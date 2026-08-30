<?php
/**
 * Account orders — order history table for the /my-account/orders/ endpoint.
 *
 * Key:    'account/orders'
 * Source: account.html `.account-orders` (WC order data model)
 *
 * Props:  (all keys optional; escaped at render; missing key → default listed)
 * - `array $orders     Order rows (number/date/status/total/url). Default [].`
 * - `string $shop_url   Empty-state CTA. Default ''.`
 * - `string $empty_text Empty-state message. Default ''.`
 * - `string $logout_url Sign-out link. Default ''.`
 *
 * Slots:  none
 * Variants: none
 * Tokens:  no hardcoded presentation values; one inline layout style (text-align/margin) on the empty-state CTA — consolidate into `.account-orders-empty` in M3.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$componentData = isset( $componentData ) ? (array) $componentData : array();

$orders     = isset( $componentData['orders'] ) ? (array) $componentData['orders'] : array();
$shop_url   = isset( $componentData['shop_url'] ) ? $componentData['shop_url'] : '';
$empty_text = isset( $componentData['empty_text'] ) ? $componentData['empty_text'] : '';
$logout_url = isset( $componentData['logout_url'] ) ? $componentData['logout_url'] : '';
?>
<div class="account-section" data-phantom-account="true">
	<div class="account-orders">
		<?php if ( empty( $orders ) ) : ?>
			<div class="account-orders-empty">
				<i class="fas fa-box-open"></i>
				<h3 class="account-orders-empty-title"><?php esc_html_e( 'No orders yet', 'aureon' ); ?></h3>
				<p class="account-orders-empty-text"><?php echo esc_html( $empty_text ); ?></p>
				<?php if ( $shop_url ) : ?>
					<a class="btn-signin account-orders-cta" href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'Start Shopping', 'aureon' ); ?></a>
				<?php endif; ?>
			</div>
		<?php else : ?>
			<div class="account-orders-table" role="table" aria-label="<?php esc_attr_e( 'Your orders', 'aureon' ); ?>">
				<div class="account-orders-row account-orders-head" role="row">
					<span class="account-orders-cell" role="columnheader"><?php esc_html_e( 'Order', 'aureon' ); ?></span>
					<span class="account-orders-cell" role="columnheader"><?php esc_html_e( 'Date', 'aureon' ); ?></span>
					<span class="account-orders-cell" role="columnheader"><?php esc_html_e( 'Status', 'aureon' ); ?></span>
					<span class="account-orders-cell" role="columnheader"><?php esc_html_e( 'Total', 'aureon' ); ?></span>
					<span class="account-orders-cell" role="columnheader"><?php esc_html_e( 'Actions', 'aureon' ); ?></span>
				</div>
				<?php foreach ( $orders as $order ) : ?>
					<div class="account-orders-row" role="row">
						<span class="account-orders-cell account-orders-no" role="cell">
							<a href="<?php echo esc_url( isset( $order['view_url'] ) ? $order['view_url'] : '#' ); ?>">
								<?php echo esc_html( isset( $order['number'] ) ? $order['number'] : '' ); ?>
							</a>
						</span>
						<span class="account-orders-cell" role="cell"><?php echo esc_html( isset( $order['date'] ) ? $order['date'] : '' ); ?></span>
						<span class="account-orders-cell" role="cell">
							<span class="order-status status-<?php echo esc_attr( isset( $order['status_slug'] ) ? sanitize_key( $order['status_slug'] ) : 'any' ); ?>">
								<?php echo esc_html( isset( $order['status'] ) ? $order['status'] : '' ); ?>
							</span>
						</span>
						<span class="account-orders-cell account-orders-total" role="cell"><?php echo esc_html( isset( $order['total'] ) ? $order['total'] : '' ); ?></span>
						<span class="account-orders-cell" role="cell">
							<a class="account-orders-view" href="<?php echo esc_url( isset( $order['view_url'] ) ? $order['view_url'] : '#' ); ?>">
								<?php esc_html_e( 'View', 'aureon' ); ?>
							</a>
						</span>
					</div>
				<?php endforeach; ?>
			</div>
			<?php if ( $logout_url ) : ?>
				<div style="text-align:center;margin-top:40px;">
					<a href="<?php echo esc_url( $logout_url ); ?>" class="btn-logout"><?php esc_html_e( 'Sign Out', 'aureon' ); ?></a>
				</div>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</div>