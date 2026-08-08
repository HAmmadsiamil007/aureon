<?php
/**
 * AETHER Newsletter — Database Storage & Admin Management.
 *
 * Stores subscribers in a dedicated DB table with active/unsubscribed status,
 * IP logging, rate limiting, an admin management page and CSV export.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ─── Database Table ────────────────────────────────────────────
/**
 * Create the newsletter subscribers table.
 *
 * Themes cannot rely on register_activation_hook (plugin-only), so the table
 * is created lazily on the first admin request after the table is missing.
 */
function aether_newsletter_create_table() {
	global $wpdb;

	$table_name      = $wpdb->prefix . 'aether_newsletter_subscribers';
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE {$table_name} (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		email varchar(255) NOT NULL,
		status varchar(20) NOT NULL DEFAULT 'active',
		ip_address varchar(45) DEFAULT NULL,
		subscribed_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		unsubscribed_at datetime DEFAULT NULL,
		PRIMARY KEY (id),
		UNIQUE KEY email (email),
		KEY status (status),
		KEY subscribed_at (subscribed_at)
	) {$charset_collate};";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( $sql );
}

add_action( 'admin_init', 'aether_newsletter_maybe_create_table', 5 );
/**
 * Create the subscribers table on first admin load if missing.
 */
function aether_newsletter_maybe_create_table() {
	global $wpdb;

	$table = aether_newsletter_table();
	if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) ) === $table ) {
		return;
	}

	aether_newsletter_create_table();
}

/**
 * Get the subscribers table name.
 *
 * @return string Full table name with prefix.
 */
function aether_newsletter_table() {
	global $wpdb;
	return $wpdb->prefix . 'aether_newsletter_subscribers';
}

// ─── Subscribe Handler ─────────────────────────────────────────
/**
 * Store a subscriber in the database.
 *
 * @param string $email      Subscriber email address.
 * @param string $ip_address Optional IP address.
 * @return array|WP_Error Result array or WP_Error on failure.
 */
function aether_newsletter_subscribe( $email, $ip_address = '' ) {
	global $wpdb;

	$email = sanitize_email( $email );
	if ( ! is_email( $email ) ) {
		return new WP_Error( 'invalid_email', __( 'Please enter a valid email address.', 'aureon' ) );
	}

	$table = aether_newsletter_table();

	// Guard against a missing table (e.g. fresh install before admin init).
	if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) ) !== $table ) {
		aether_newsletter_create_table();
	}

	$existing = $wpdb->get_var(
		$wpdb->prepare( "SELECT status FROM {$table} WHERE email = %s", $email ) // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	);

	if ( 'active' === $existing ) {
		return array(
			'success' => true,
			'message' => __( 'Welcome back! You\'re already subscribed.', 'aureon' ),
			'action'  => 'already_subscribed',
		);
	}

	if ( 'unsubscribed' === $existing ) {
		// Re-subscribe.
		$wpdb->update(
			$table, // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			array(
				'status'          => 'active',
				'unsubscribed_at' => null,
				'subscribed_at'   => current_time( 'mysql' ),
				'ip_address'      => $ip_address,
			),
			array( 'email' => $email ),
			array( '%s', '%s', '%s', '%s' ),
			array( '%s' )
		);

		return array(
			'success' => true,
			'message' => __( 'Welcome back! You\'ve been re-subscribed.', 'aureon' ),
			'action'  => 'resubscribed',
		);
	}

	// New subscriber.
	$result = $wpdb->insert(
		$table, // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		array(
			'email'         => $email,
			'status'        => 'active',
			'ip_address'    => $ip_address,
			'subscribed_at' => current_time( 'mysql' ),
		),
		array( '%s', '%s', '%s', '%s' )
	);

	if ( false === $result ) {
		return new WP_Error( 'db_error', __( 'Failed to subscribe. Please try again.', 'aureon' ) );
	}

	/**
	 * Fires after a successful newsletter subscription.
	 *
	 * @param string $email The subscriber email address.
	 * @param int    $id    The subscriber database ID.
	 */
	do_action( 'aether_newsletter_subscribed', $email, (int) $wpdb->insert_id );

	return array(
		'success' => true,
		'message' => __( 'Welcome to the void. Check your inbox.', 'aureon' ),
		'action'  => 'subscribed',
	);
}

// ─── Unsubscribe Handler ───────────────────────────────────────
/**
 * Mark a subscriber as unsubscribed.
 *
 * @param string $email Subscriber email address.
 * @return array Result array.
 */
function aether_newsletter_unsubscribe( $email ) {
	global $wpdb;

	$email = sanitize_email( $email );
	$table = aether_newsletter_table();
	$result = $wpdb->update(
		$table, // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		array(
			'status'          => 'unsubscribed',
			'unsubscribed_at' => current_time( 'mysql' ),
		),
		array( 'email' => $email, 'status' => 'active' ),
		array( '%s', '%s' ),
		array( '%s', '%s' )
	);

	if ( $result ) {
		return array(
			'success' => true,
			'message' => __( 'You have been unsubscribed.', 'aureon' ),
		);
	}

	return array(
		'success' => false,
		'message' => __( 'Email not found or already unsubscribed.', 'aureon' ),
	);
}

// ─── AJAX Subscribe ────────────────────────────────────────────
add_action( 'wp_ajax_aether_newsletter_subscribe', 'aether_ajax_newsletter_subscribe' );
add_action( 'wp_ajax_nopriv_aether_newsletter_subscribe', 'aether_ajax_newsletter_subscribe' );
/**
 * AJAX handler for newsletter subscription (DB-backed).
 */
function aether_ajax_newsletter_subscribe() {
	check_ajax_referer( 'aether_nonce', 'nonce' );

	$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$ip    = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';

	if ( ! is_email( $email ) ) {
		wp_send_json_error( array( 'message' => __( 'Please enter a valid email address.', 'aureon' ) ) );
	}

	// Rate limit: one subscribe per IP per minute.
	$ip_key = 'aether_newsletter_rate_' . md5( $ip );
	if ( get_transient( $ip_key ) ) {
		wp_send_json_error( array( 'message' => __( 'Please wait before subscribing again.', 'aureon' ) ) );
	}

	$result = aether_newsletter_subscribe( $email, $ip );

	if ( is_wp_error( $result ) ) {
		wp_send_json_error( array( 'message' => $result->get_error_message() ) );
	}

	set_transient( $ip_key, true, MINUTE_IN_SECONDS );

	wp_send_json_success( array( 'message' => $result['message'], 'action' => $result['action'] ) );
}

// ─── Admin Page ────────────────────────────────────────────────
add_action( 'admin_menu', 'aether_newsletter_admin_menu' );
/**
 * Register the newsletter admin page under Appearance.
 */
function aether_newsletter_admin_menu() {
	add_submenu_page(
		'themes.php',
		__( 'Newsletter Subscribers', 'aureon' ),
		__( 'Newsletter', 'aureon' ),
		'manage_options',
		'aether-newsletter',
		'aether_newsletter_admin_page'
	);
}

/**
 * Render the newsletter subscribers admin page.
 */
function aether_newsletter_admin_page() {
	global $wpdb;
	$table = aether_newsletter_table();

	if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) ) !== $table ) {
		echo '<div class="wrap">';
		echo '<h1>' . esc_html__( 'Newsletter Subscribers', 'aureon' ) . '</h1>';
		echo '<div class="notice notice-warning"><p>' . esc_html__( 'Database table not found. It will be created on the next admin load.', 'aureon' ) . '</p></div>';
		echo '</div>';
		return;
	}

	// CSV export.
	if ( isset( $_GET['aether_export_csv'] ) && check_admin_referer( 'aether_newsletter_export' ) ) {
		aether_newsletter_export_csv();
		return;
	}

	// Single delete.
	if ( isset( $_POST['aether_delete_subscriber'] ) && check_admin_referer( 'aether_newsletter_manage' ) ) {
		$del_id = absint( $_POST['subscriber_id'] );
		if ( $del_id ) {
			$wpdb->delete( $table, array( 'id' => $del_id ), array( '%d' ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			echo '<div class="notice notice-success"><p>' . esc_html__( 'Subscriber removed.', 'aureon' ) . '</p></div>';
		}
	}

	// Bulk delete.
	if ( isset( $_POST['action'] ) && 'bulk-delete' === $_POST['action'] && check_admin_referer( 'aether_newsletter_manage' ) ) {
		if ( ! empty( $_POST['subscriber'] ) && is_array( $_POST['subscriber'] ) ) {
			$ids = array_map( 'absint', $_POST['subscriber'] );
			$placeholders = implode( ',', array_fill( 0, count( $ids ), '%d' ) );
			$wpdb->query( $wpdb->prepare( "DELETE FROM {$table} WHERE id IN ({$placeholders})", $ids ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			echo '<div class="notice notice-success"><p>' . esc_html__( 'Selected subscribers removed.', 'aureon' ) . '</p></div>';
		}
	}

	// Stats.
	$total        = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table}" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$active       = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$table} WHERE status = %s", 'active' ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$unsubscribed = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$table} WHERE status = %s", 'unsubscribed' ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared

	// Pagination.
	$per_page     = 20;
	$current_page = isset( $_GET['paged'] ) ? max( 1, (int) $_GET['paged'] ) : 1;
	$offset       = ( $current_page - 1 ) * $per_page;
	$max_pages    = ceil( $total / $per_page );

	$subscribers = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT * FROM {$table} ORDER BY subscribed_at DESC LIMIT %d OFFSET %d", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$per_page,
			$offset
		)
	);
	?>
	<div class="wrap">
		<h1><?php echo esc_html__( 'Newsletter Subscribers', 'aureon' ); ?></h1>

		<div class="card">
			<h2><?php echo esc_html__( 'Statistics', 'aureon' ); ?></h2>
			<table class="widefat">
				<tr>
					<td><strong><?php echo esc_html__( 'Total Subscribers:', 'aureon' ); ?></strong></td>
					<td><?php echo esc_html( number_format_i18n( $total ) ); ?></td>
				</tr>
				<tr>
					<td><strong><?php echo esc_html__( 'Active:', 'aureon' ); ?></strong></td>
					<td><?php echo esc_html( number_format_i18n( $active ) ); ?></td>
				</tr>
				<tr>
					<td><strong><?php echo esc_html__( 'Unsubscribed:', 'aureon' ); ?></strong></td>
					<td><?php echo esc_html( number_format_i18n( $unsubscribed ) ); ?></td>
				</tr>
			</table>
		</div>

		<p>
			<a href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'aether_export_csv', '1' ), 'aether_newsletter_export' ) ); ?>" class="button button-secondary">
				<?php echo esc_html__( 'Export CSV', 'aureon' ); ?>
			</a>
		</p>

		<form method="post">
			<?php wp_nonce_field( 'aether_newsletter_manage' ); ?>

			<div class="tablenav top">
				<div class="alignleft actions bulkactions">
					<select name="action">
						<option value="bulk-delete"><?php echo esc_html__( 'Delete', 'aureon' ); ?></option>
					</select>
					<input type="submit" class="button action" value="<?php echo esc_attr__( 'Apply', 'aureon' ); ?>">
				</div>
			</div>

			<table class="wp-list-table widefat fixed striped subscribers">
				<thead>
					<tr>
						<td class="manage-column column-cb check-column" width="30">
							<input type="checkbox" id="cb-select-all-1">
						</td>
						<th><?php echo esc_html__( 'Email', 'aureon' ); ?></th>
						<th><?php echo esc_html__( 'Status', 'aureon' ); ?></th>
						<th><?php echo esc_html__( 'IP Address', 'aureon' ); ?></th>
						<th><?php echo esc_html__( 'Subscribed', 'aureon' ); ?></th>
						<th><?php echo esc_html__( 'Actions', 'aureon' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php if ( empty( $subscribers ) ) : ?>
						<tr>
							<td colspan="6"><?php echo esc_html__( 'No subscribers yet.', 'aureon' ); ?></td>
						</tr>
					<?php else : ?>
						<?php foreach ( $subscribers as $sub ) : ?>
							<tr>
								<th scope="row" class="check-column">
									<input type="checkbox" name="subscriber[]" value="<?php echo esc_attr( $sub->id ); ?>">
								</th>
								<td><?php echo esc_html( $sub->email ); ?></td>
								<td>
									<span class="status-<?php echo esc_attr( $sub->status ); ?>">
										<?php echo esc_html( ucfirst( $sub->status ) ); ?>
									</span>
								</td>
								<td><?php echo esc_html( $sub->ip_address ? $sub->ip_address : '—' ); ?></td>
								<td><?php echo esc_html( human_time_diff( strtotime( $sub->subscribed_at ) ) . ' ' . __( 'ago', 'aureon' ) ); ?></td>
								<td>
									<form method="post" style="display:inline;">
										<?php wp_nonce_field( 'aether_newsletter_manage' ); ?>
										<input type="hidden" name="subscriber_id" value="<?php echo esc_attr( $sub->id ); ?>">
										<button type="submit" name="aether_delete_subscriber" value="1" class="button-link delete" onclick="return confirm('<?php echo esc_js( __( 'Remove this subscriber?', 'aureon' ) ); ?>');">
											<?php echo esc_html__( 'Delete', 'aureon' ); ?>
										</button>
									</form>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</form>

		<?php if ( $max_pages > 1 ) : ?>
			<div class="tablenav bottom">
				<div class="tablenav-pages">
					<?php
					echo wp_kses_post(
						paginate_links(
							array(
								'base'    => add_query_arg( 'paged', '%#%' ),
								'format'  => '',
								'current' => $current_page,
								'total'   => $max_pages,
							)
						)
					);
					?>
				</div>
			</div>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Export subscribers as CSV.
 */
function aether_newsletter_export_csv() {
	global $wpdb;
	$table   = aether_newsletter_table();
	$results = $wpdb->get_results( "SELECT * FROM {$table} ORDER BY subscribed_at DESC" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared

	header( 'Content-Type: text/csv; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=aether-newsletter-subscribers-' . gmdate( 'Y-m-d' ) . '.csv' );

	$output = fopen( 'php://output', 'w' );
	fputcsv( $output, array( 'Email', 'Status', 'IP Address', 'Subscribed At', 'Unsubscribed At' ) );

	foreach ( $results as $row ) {
		fputcsv(
			$output,
			array(
				$row->email,
				$row->status,
				$row->ip_address,
				$row->subscribed_at,
				$row->unsubscribed_at,
			)
		);
	}

	fclose( $output );
	exit;
}

// ─── REST Endpoint ─────────────────────────────────────────────
add_action( 'rest_api_init', 'aether_register_newsletter_rest' );
/**
 * Register the REST newsletter endpoint.
 */
function aether_register_newsletter_rest() {
	register_rest_route(
		'aether/v1',
		'/newsletter/subscribe',
		array(
			'methods'             => 'POST',
			'callback'            => 'aether_rest_newsletter_subscribe',
			'permission_callback' => '__return_true',
			'args'                => array(
				'email' => array(
					'required'          => true,
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_email',
					'validate_callback' => 'is_email',
				),
			),
		)
	);
}

/**
 * REST callback for newsletter subscription (DB-backed).
 *
 * @param WP_REST_Request $request Full request object.
 * @return WP_REST_Response Response object.
 */
function aether_rest_newsletter_subscribe( $request ) {
	$email      = $request->get_param( 'email' );
	$ip_address = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';

	// Rate limit: one subscribe per IP per minute.
	$ip_key = 'aether_newsletter_rate_' . md5( $ip_address );
	if ( get_transient( $ip_key ) ) {
		return new WP_REST_Response(
			array(
				'code'    => 'rate_limited',
				'message' => __( 'Please wait before subscribing again.', 'aureon' ),
			),
			429
		);
	}

	$result = aether_newsletter_subscribe( $email, $ip_address );

	if ( is_wp_error( $result ) ) {
		return new WP_REST_Response(
			array(
				'code'    => $result->get_error_code(),
				'message' => $result->get_error_message(),
			),
			400
		);
	}

	set_transient( $ip_key, true, MINUTE_IN_SECONDS );

	return new WP_REST_Response(
		array(
			'success' => true,
			'message' => $result['message'],
			'action'  => $result['action'],
		),
		200
	);
}
