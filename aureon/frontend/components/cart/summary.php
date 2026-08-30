<?php
/**
 * Cart summary — totals panel: subtotal, shipping, total, checkout CTA.
 *
 * Key:    'cart/summary'
 * Source: checkout.html `.cart-summary`
 *
 * Props:  (all keys optional; escaped at render; missing key → default listed)
 * - `string $subtotal     Formatted subtotal. Default ''.`
 * - `string $shipping     Formatted shipping. Default ''.`
 * - `string $total        Formatted total. Default ''.`
 * - `string $checkout_url  Checkout CTA href. Default '#'.`
 * - `string $shop_url     Continue-shopping link. Default ''.`
 *
 * Slots:  none
 * Variants: none
 * Tokens:  no hardcoded presentation values — theme uses `--aureon-*` custom props only.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$componentData = isset( $componentData ) ? (array) $componentData : array();

$subtotal     = isset( $componentData['subtotal'] ) ? $componentData['subtotal'] : '';
$shipping     = isset( $componentData['shipping'] ) ? $componentData['shipping'] : '';
$total        = isset( $componentData['total'] ) ? $componentData['total'] : '';
$checkout_url = isset( $componentData['checkout_url'] ) ? $componentData['checkout_url'] : '#';
$shop_url     = isset( $componentData['shop_url'] ) ? $componentData['shop_url'] : '';
?>
<div class="cart-summary shopping-cart-info">
	<h3><?php esc_html_e( 'Order Summary', 'aureon' ); ?></h3>
	<div class="summary-row">
		<span class="label"><?php esc_html_e( 'Subtotal', 'aureon' ); ?></span>
		<span class="value"><?php echo esc_html( $subtotal ); ?></span>
	</div>
	<div class="summary-row">
		<span class="label"><?php esc_html_e( 'Shipping', 'aureon' ); ?></span>
		<span class="value" style="color: var(--success);"><?php echo esc_html( $shipping ); ?></span>
	</div>
	<div class="summary-row total">
		<span class="label"><?php esc_html_e( 'Total', 'aureon' ); ?></span>
		<span class="value"><?php echo esc_html( $total ); ?></span>
	</div>
	<a href="<?php echo esc_url( $checkout_url ); ?>" class="checkout-btn"><?php esc_html_e( 'Proceed to Checkout', 'aureon' ); ?></a>
	<?php if ( $shop_url ) : ?>
		<a href="<?php echo esc_url( $shop_url ); ?>" class="continue-shopping" style="margin-top: 15px;"><i class="fas fa-arrow-left"></i><?php esc_html_e( 'Continue Shopping', 'aureon' ); ?></a>
	<?php endif; ?>
</div>
