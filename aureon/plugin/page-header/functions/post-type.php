<?php
defined( 'WPINC' ) or die;

add_action( 'init', 'aureon_page_header_post_type', 15 );
/**
 * Create our Page Header post type.
 *
 * @since 1.4
 */
function aureon_page_header_post_type() {
	$labels = array(
		'name'                  => _x( 'Page Headers', 'Post Type General Name', 'aureon-studio' ),
		'singular_name'         => _x( 'Page Header', 'Post Type Singular Name', 'aureon-studio' ),
		'menu_name'             => __( 'Page Headers', 'aureon-studio' ),
		'name_admin_bar'        => __( 'Page Header', 'aureon-studio' ),
		'archives'              => __( 'Page Header Archives', 'aureon-studio' ),
		'parent_item_colon'     => __( 'Parent Page Header:', 'aureon-studio' ),
		'all_items'             => __( 'All Page Headers', 'aureon-studio' ),
		'add_new_item'          => __( 'Add New Page Header', 'aureon-studio' ),
		'new_item'              => __( 'New Page Header', 'aureon-studio' ),
		'edit_item'             => __( 'Edit Page Header', 'aureon-studio' ),
		'update_item'           => __( 'Update Page Header', 'aureon-studio' ),
		'view_item'             => __( 'View Page Header', 'aureon-studio' ),
		'search_items'          => __( 'Search Page Header', 'aureon-studio' ),
		'insert_into_item'      => __( 'Insert into Page Header', 'aureon-studio' ),
		'uploaded_to_this_item' => __( 'Uploaded to this Page Header', 'aureon-studio' ),
	);
	$args = array(
		'label'                 => __( 'Page Header', 'aureon-studio' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'thumbnail' ),
		'hierarchical'          => false,
		'public'                => false,
		'show_ui'               => true,
		'show_in_menu'          => function_exists( 'aureon_premium_do_elements' ) ? false : true,
		'menu_position'         => 100,
		'menu_icon'				=> 'dashicons-welcome-widgets-menus',
		'show_in_admin_bar'     => false,
		'show_in_nav_menus'     => false,
		'can_export'            => true,
		'has_archive'           => false,
		'exclude_from_search'   => true,
		'publicly_queryable'    => false,
		'capability_type'       => 'page',
	);
	register_post_type( 'aureon_page_header', $args );

	$post_types = get_post_types( array( 'public' => true ) );
	$term_args = array(
		'sanitize_callback' => 'int',
		'type' => 'string',
		'description' => '',
		'single' => true,
		'show_in_rest' => true,
	);
	foreach ( $post_types as $type ) {
		register_meta( $type, 'aureon_page_header', $term_args );
	}

	$taxonomies = get_taxonomies( array( 'public' => true ) );
	if ( $taxonomies ) {
		foreach ( $taxonomies  as $taxonomy ) {
			add_action( $taxonomy . '_add_form_fields', 'aureon_page_header_tax_new_ph_field' );
			add_action( $taxonomy . '_edit_form_fields', 'aureon_page_header_tax_edit_ph_field' );
			add_action( 'edit_' . $taxonomy,   'aureon_page_header_tax_save_ph' );
			add_action( 'create_' . $taxonomy, 'aureon_page_header_tax_save_ph' );
		}
	}
}

add_action( 'admin_menu', 'aureon_old_page_header_options_page' );
/**
 * Add our submenu page to the Appearance tab.
 *
 * @since 1.7
 */
function aureon_old_page_header_options_page() {
	if ( ! function_exists( 'aureon_premium_do_elements' ) ) {
		return;
	}

	add_submenu_page(
		'themes.php',
		esc_html__( 'Page Headers', 'aureon-studio' ),
		esc_html__( 'Page Headers', 'aureon-studio' ),
		'manage_options',
		'edit.php?post_type=aureon_page_header'
	);
}

add_action( 'admin_head', 'aureon_old_page_header_fix_menu' );
/**
 * Set our current menu in the admin while in the old Page Header pages.
 *
 * @since 1.7
 */
function aureon_old_page_header_fix_menu() {
	if ( ! function_exists( 'aureon_premium_do_elements' ) ) {
		return;
	}

	global $parent_file, $submenu_file, $post_type;

	$screen = get_current_screen();

	if ( 'aureon_page_header' === $post_type || 'appearance_page_page-header-global-locations' === $screen->base ) {
		$parent_file = 'themes.php';
		$submenu_file = 'edit.php?post_type=aureon_elements';
	}

	remove_submenu_page( 'themes.php', 'edit.php?post_type=aureon_page_header' );
	remove_submenu_page( 'themes.php', 'page-header-global-locations' );
}

add_action( 'admin_head', 'aureon_page_header_add_legacy_locations_button', 999 );
/**
 * Add legacy buttons to our new GP Elements post type.
 *
 * @since 1.7
 */
function aureon_page_header_add_legacy_locations_button() {
	if ( ! function_exists( 'aureon_premium_do_elements' ) ) {
		return;
	}

	$screen = get_current_screen();

	if ( 'aureon_page_header' === $screen->post_type ) :
		?>
		<script>
			jQuery( function( $ ) {
				$( '<a href="<?php echo admin_url(); ?>themes.php?page=page-header-global-locations" class="page-title-action legacy-button"><?php esc_html_e( "Global Locations", "aureon-studio" ); ?></a>' ).insertAfter( '.page-title-action:not(.legacy-button)' );
			} );
		</script>
		<?php
	endif;

	if ( 'aureon_elements' === $screen->post_type && 'edit' === $screen->base ) :
		?>
		<script>
			jQuery( function( $ ) {
				$( '<a href="<?php echo admin_url(); ?>edit.php?post_type=aureon_page_header" class="page-title-action legacy-button"><?php esc_html_e( "Legacy Page Headers", "aureon-studio" ); ?></a>' ).insertAfter( '.page-title-action:not(.legacy-button)' );
			} );
		</script>
		<?php
	endif;
}

/**
 * Build our taxonomy select option when adding new taxonomies.
 *
 * @since 1.4
 */
function aureon_page_header_tax_new_ph_field() {
	wp_nonce_field( basename( __FILE__ ), 'aureon_page_header_term_nonce' ); ?>
	<div class="form-field term-page-header-wrap">
		<label for="_aureon-select-page-header"><?php _e( 'Page Header', 'aureon-studio' ); ?></label>
		<select name="_aureon-select-page-header" id="_aureon-select-page-header">
			<option value=""></option>
			<?php
			$page_headers = get_posts(array(
				'posts_per_page' => -1,
				'orderby' => 'title',
				'post_type' => 'aureon_page_header',
			));

			foreach( $page_headers as $header ) {
				printf( '<option value="%1$s">%2$s</option>',
					$header->ID,
					$header->post_title
				);
			}
			?>
		</select>
	</div>
	<?php
}

/**
 * Build our taxonomy select option when editing existing taxonomies.
 *
 * @since 1.4
 *
 * @param string $term The selected term.
 */
function aureon_page_header_tax_edit_ph_field( $term ) {
?>
	<tr class="form-field form-required term-page-header-wrap">
		<th scope="row">
			<label for="_aureon-select-page-header"><?php _e( 'Page Header', 'aureon-studio' ); ?></label>
		</th>
		<td>
			<?php wp_nonce_field( basename( __FILE__ ), 'aureon_page_header_term_nonce' ); ?>
			<select name="_aureon-select-page-header" id="_aureon-select-page-header">
				<option value="" <?php selected( get_term_meta( $term->term_id, '_aureon-select-page-header', true ), ''  ); ?>></option>
				<?php
				$page_headers = get_posts(array(
					'posts_per_page' => -1,
					'orderby' => 'title',
					'post_type' => 'aureon_page_header',
				));

				foreach( $page_headers as $header ) {
					printf( '<option value="%1$s" %2$s>%3$s</option>',
						$header->ID,
						selected( get_term_meta( $term->term_id, '_aureon-select-page-header', true ), $header->ID ),
						$header->post_title
					);
				}
				?>
			</select>
		</td>
	</tr>
<?php }

/**
 * Save our selected page header inside taxonomies.
 *
 * @since 1.4
 *
 * @param int $term_id The selected term ID.
 */
function aureon_page_header_tax_save_ph( $term_id ) {
	if ( ! isset( $_POST['aureon_page_header_term_nonce'] ) || ! wp_verify_nonce( $_POST['aureon_page_header_term_nonce'], basename( __FILE__ ) ) ) {
		return;
	}

	$old = get_term_meta( $term_id, '_aureon-select-page-header', true );
	$new = isset( $_POST['_aureon-select-page-header'] ) ? sanitize_key( $_POST['_aureon-select-page-header'] ) : '';

	if ( $old && '' === $new ) {
		delete_term_meta( $term_id, '_aureon-select-page-header' );
	} else if ( $old !== $new ) {
		update_term_meta( $term_id, '_aureon-select-page-header', $new );
	}
}
