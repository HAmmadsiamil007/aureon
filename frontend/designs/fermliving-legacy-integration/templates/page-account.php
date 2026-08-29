<?php
/**
 * Ferm Living Account Page Template
 *
 * Overrides WooCommerce account. Renders frozen source DOM structure
 * with WooCommerce customer data and tab-based navigation.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$customer = wp_get_current_user();
$dashboard = is_account_page() && ! isset( $_GET['view-order'] ) && ! isset( $_GET['edit-account'] ) && ! isset( $_GET['edit-address'] ) && ! isset( $_GET['customer-logout'] );
?>

<main class="content" id="main-content">
	<section class="headspace">
		<div class="ferm-account" data-ferm-account>
			<div class="limit">

				<?php wc_print_notices(); ?>

				<h1 class="ferm-account__heading">My Account</h1>

				<?php if ( $customer && $customer->exists() ) : ?>
					<p class="ferm-account__welcome">
						<?php
						printf(
							/* translators: %s: customer display name */
							esc_html__( 'Hello %s', 'aureon' ),
							'<strong>' . esc_html( $customer->display_name ) . '</strong>'
						);
						?>
					</p>
				<?php endif; ?>

				<!-- Tabs navigation -->
				<nav class="ferm-account__tabs" data-ferm-account-tabs>
					<button type="button" class="ferm-account__tab<?php echo ( is_account_page() && ! isset( $_GET['view-order'] ) && ! isset( $_GET['edit-account'] ) && ! isset( $_GET['edit-address'] ) ) ? ' is-active' : ''; ?>" data-tab="dashboard">
						<?php esc_html_e( 'Dashboard', 'aureon' ); ?>
					</button>
					<button type="button" class="ferm-account__tab<?php echo isset( $_GET['view-order'] ) || ( is_account_page() && 'orders' === ( $_GET['action'] ?? '' ) ) ? ' is-active' : ''; ?>" data-tab="orders">
						<?php esc_html_e( 'Orders', 'aureon' ); ?>
					</button>
					<button type="button" class="ferm-account__tab<?php echo ( $_GET['action'] ?? '' ) === 'edit-address' ? ' is-active' : ''; ?>" data-tab="address">
						<?php esc_html_e( 'Addresses', 'aureon' ); ?>
					</button>
					<button type="button" class="ferm-account__tab<?php echo ( $_GET['action'] ?? '' ) === 'edit-account' ? ' is-active' : ''; ?>" data-tab="account">
						<?php esc_html_e( 'Account details', 'aureon' ); ?>
					</button>
					<?php if ( class_exists( 'WC_Gift_Cards' ) ) : ?>
						<button type="button" class="ferm-account__tab" data-tab="giftcards">
							<?php esc_html_e( 'Gift cards', 'aureon' ); ?>
						</button>
					<?php endif; ?>
				</nav>

				<!-- Dashboard panel -->
				<div class="ferm-account__panel<?php echo $dashboard ? ' is-active' : ''; ?>" data-ferm-account-panel="dashboard">
					<p>
						<?php
						printf(
							/* translators: %s: customer first name */
							esc_html__( 'Hello %s. From your account dashboard you can view your recent orders, manage your shipping and billing addresses, and edit your account details.', 'aureon' ),
							esc_html( $customer->first_name )
						);
						?>
					</p>

					<?php
					/* Recent orders */
					$recent_orders = wc_get_orders( array(
						'customer_id' => $customer->get_id(),
						'limit'       => 5,
						'orderby'     => 'date',
						'order'       => 'DESC',
					) );

					if ( ! empty( $recent_orders ) ) :
					?>
						<h3 style="font-family: var(--aureon-font-heading); font-size: 18px; font-weight: 400; margin: 32px 0 16px;">
							<?php esc_html_e( 'Recent orders', 'aureon' ); ?>
						</h3>
						<table class="ferm-account__orders-table">
							<thead>
								<tr>
									<th><?php esc_html_e( 'Order', 'aureon' ); ?></th>
									<th><?php esc_html_e( 'Date', 'aureon' ); ?></th>
									<th><?php esc_html_e( 'Status', 'aureon' ); ?></th>
									<th><?php esc_html_e( 'Total', 'aureon' ); ?></th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $recent_orders as $order ) : ?>
									<tr>
										<td>#<?php echo esc_html( $order->get_order_number() ); ?></td>
										<td><?php echo esc_html( wc_format_datetime( $order->get_date_created(), 'F j, Y' ) ); ?></td>
										<td><?php echo wp_kses_post( wc_get_order_status_name( $order->get_status() ) ); ?></td>
										<td><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></td>
										<td><a href="<?php echo esc_url( $order->get_view_order_url() ); ?>"><?php esc_html_e( 'View', 'aureon' ); ?></a></td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					<?php endif; ?>
				</div>

				<!-- Orders panel -->
				<div class="ferm-account__panel<?php echo ( $_GET['action'] ?? '' ) === 'orders' ? ' is-active' : ''; ?>" data-ferm-account-panel="orders">
					<?php
					$all_orders = wc_get_orders( array(
						'customer_id' => $customer->get_id(),
						'limit'       => 20,
						'orderby'     => 'date',
						'order'       => 'DESC',
					) );

					if ( ! empty( $all_orders ) ) :
					?>
						<table class="ferm-account__orders-table">
							<thead>
								<tr>
									<th><?php esc_html_e( 'Order', 'aureon' ); ?></th>
									<th><?php esc_html_e( 'Date', 'aureon' ); ?></th>
									<th><?php esc_html_e( 'Status', 'aureon' ); ?></th>
									<th><?php esc_html_e( 'Total', 'aureon' ); ?></th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $all_orders as $order ) : ?>
									<tr>
										<td>#<?php echo esc_html( $order->get_order_number() ); ?></td>
										<td><?php echo esc_html( wc_format_datetime( $order->get_date_created(), 'F j, Y' ) ); ?></td>
										<td><?php echo wp_kses_post( wc_get_order_status_name( $order->get_status() ) ); ?></td>
										<td><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></td>
										<td><a href="<?php echo esc_url( $order->get_view_order_url() ); ?>"><?php esc_html_e( 'View', 'aureon' ); ?></a></td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					<?php else : ?>
						<p><?php esc_html_e( 'No orders have been made yet.', 'aureon' ); ?></p>
					<?php endif; ?>
				</div>

				<!-- Addresses panel -->
				<div class="ferm-account__panel<?php echo ( $_GET['action'] ?? '' ) === 'edit-address' ? ' is-active' : ''; ?>" data-ferm-account-panel="address">
					<div class="ferm-account__addresses">
						<div class="ferm-account__address-card">
							<h4><?php esc_html_e( 'Billing address', 'aureon' ); ?></h4>
							<p>
								<?php
								$billing = $customer->get_billing();
								$lines = array_filter( array(
									$billing['first_name'] . ' ' . $billing['last_name'],
									$billing['address_1'],
									$billing['address_2'],
									$billing['city'] . ' ' . $billing['postcode'],
									$billing['state'],
									$billing['country'],
									$billing['email'],
									$billing['phone'],
								) );
								echo nl2br( esc_html( implode( "\n", $lines ) ) );
								?>
							</p>
							<div class="ferm-account__address-actions">
								<a href="<?php echo esc_url( wc_get_account_endpoint_url( 'edit-address' ) . '?address=billing' ); ?>"><?php esc_html_e( 'Edit', 'aureon' ); ?></a>
							</div>
						</div>

						<div class="ferm-account__address-card">
							<h4><?php esc_html_e( 'Shipping address', 'aureon' ); ?></h4>
							<p>
								<?php
								$shipping = $customer->get_shipping();
								if ( $shipping['first_name'] || $shipping['address_1'] ) {
									$lines = array_filter( array(
										$shipping['first_name'] . ' ' . $shipping['last_name'],
										$shipping['address_1'],
										$shipping['address_2'],
										$shipping['city'] . ' ' . $shipping['postcode'],
										$shipping['state'],
										$shipping['country'],
									) );
									echo nl2br( esc_html( implode( "\n", $lines ) ) );
								} else {
									esc_html_e( 'You have not set up this type of address yet.', 'aureon' );
								}
								?>
							</p>
							<div class="ferm-account__address-actions">
								<a href="<?php echo esc_url( wc_get_account_endpoint_url( 'edit-address' ) . '?address=shipping' ); ?>"><?php esc_html_e( 'Edit', 'aureon' ); ?></a>
							</div>
						</div>
					</div>
				</div>

				<!-- Account details panel -->
				<div class="ferm-account__panel<?php echo ( $_GET['action'] ?? '' ) === 'edit-account' ? ' is-active' : ''; ?>" data-ferm-account-panel="account">
					<form class="ferm-login__form" method="post" action="<?php echo esc_url( wc_get_account_endpoint_url( 'edit-account' ) ); ?>">
						<div class="ferm-checkout__field">
							<label for="account_first_name"><?php esc_html_e( 'First name', 'aureon' ); ?></label>
							<input type="text" id="account_first_name" name="account_first_name" value="<?php echo esc_attr( $customer->first_name ); ?>">
						</div>
						<div class="ferm-checkout__field">
							<label for="account_last_name"><?php esc_html_e( 'Last name', 'aureon' ); ?></label>
							<input type="text" id="account_last_name" name="account_last_name" value="<?php echo esc_attr( $customer->last_name ); ?>">
						</div>
						<div class="ferm-checkout__field">
							<label for="account_email"><?php esc_html_e( 'Email address', 'aureon' ); ?></label>
							<input type="email" id="account_email" name="account_email" value="<?php echo esc_attr( $customer->user_email ); ?>">
						</div>
						<div class="ferm-checkout__field">
							<label for="password_current"><?php esc_html_e( 'Current password', 'aureon' ); ?></label>
							<input type="password" id="password_current" name="password_current" autocomplete="current-password">
						</div>
						<div class="ferm-checkout__field">
							<label for="password_1"><?php esc_html_e( 'New password', 'aureon' ); ?></label>
							<input type="password" id="password_1" name="password_1" autocomplete="new-password">
						</div>
						<div class="ferm-checkout__field">
							<label for="password_2"><?php esc_html_e( 'Confirm new password', 'aureon' ); ?></label>
							<input type="password" id="password_2" name="password_2" autocomplete="new-password">
						</div>
						<?php wp_nonce_field( 'save_account_details', 'save-account-details-nonce' ); ?>
						<button type="submit" class="ferm-account__btn ferm-account__btn--primary" name="save_account_details" value="1">
							<?php esc_html_e( 'Save changes', 'aureon' ); ?>
						</button>
					</form>
				</div>

				<?php if ( class_exists( 'WC_Gift_Cards' ) ) : ?>
					<!-- Gift cards panel -->
					<div class="ferm-account__panel" data-ferm-account-panel="giftcards">
						<?php
						/* Gift card balance check form */
						?>
						<p><?php esc_html_e( 'Check your gift card balance or redeem a gift card code.', 'aureon' ); ?></p>
						<form class="ferm-login__form" method="post" style="max-width: 400px; margin-top: 24px;">
							<div class="ferm-checkout__field">
								<label for="gift_card_code"><?php esc_html_e( 'Gift card code', 'aureon' ); ?></label>
								<input type="text" id="gift_card_code" name="gift_card_code" placeholder="<?php esc_attr_e( 'Enter code', 'aureon' ); ?>">
							</div>
							<button type="submit" class="ferm-account__btn" name="check_gift_card" value="1">
								<?php esc_html_e( 'Check balance', 'aureon' ); ?>
							</button>
						</form>
					</div>
				<?php endif; ?>

				<!-- Logout link -->
				<div style="margin-top: 48px; padding-top: 24px; border-top: 1px solid rgba(0, 0, 0, 0.05);">
					<a href="<?php echo esc_url( wc_get_account_endpoint_url( 'customer-logout' ) ); ?>" style="font-size: 13px; color: var(--aureon-color-muted); text-decoration: underline;">
						<?php esc_html_e( 'Logout', 'aureon' ); ?>
					</a>
				</div>

			</div>
		</div>
	</section>
</main>

<?php
wp_localize_script( 'ferm-commerce', 'fermAccountData', array(
	'ajax_url' => admin_url( 'admin-ajax.php' ),
	'nonce'    => wp_create_nonce( 'ferm_account_nonce' ),
) );

get_footer();
