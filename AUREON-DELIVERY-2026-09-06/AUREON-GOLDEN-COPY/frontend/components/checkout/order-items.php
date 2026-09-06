<?php
/**
 * Order summary — checkout order summary panel.
 *
 * Key:    'checkout/order-items'
 * Source: checkout.html `.order-summary`
 *
 * Props:  (all keys optional; escaped at render; missing key → default listed)
 * - `array $items      Order line schema. Default [].`
 * - `string $subtotal   Formatted subtotal. Default ''.`
 * - `string $shipping   Formatted shipping. Default ''.`
 * - `string $tax        Formatted tax. Default ''.`
 * - `string $total      Formatted total. Default ''.`
 *
 * Slots:  none
 * Variants: none
 * Tokens:  inline layout styles (padding/display) + `var(--chrome)` label color remain from design — move to `.order-summary-*` utilities in M3.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$componentData = isset( $componentData ) ? (array) $componentData : array();

$items    = isset( $componentData['items'] ) ? (array) $componentData['items'] : array();
$subtotal = isset( $componentData['subtotal'] ) ? $componentData['subtotal'] : '';
$shipping = isset( $componentData['shipping'] ) ? $componentData['shipping'] : '';
$tax      = isset( $componentData['tax'] ) ? $componentData['tax'] : '';
$total    = isset( $componentData['total'] ) ? $componentData['total'] : '';
?>
<div class="order-summary">
	<h3><?php esc_html_e( 'Order Summary', 'aureon' ); ?></h3>
	<?php foreach ( $items as $item ) : ?>
		<div class="order-item" data-phantom="order_item">
			<img
				loading="lazy"
				src="<?php echo esc_url( isset( $item['image'] ) ? $item['image'] : '' ); ?>"
				alt="<?php echo esc_attr( isset( $item['alt'] ) ? $item['alt'] : '' ); ?>"
				class="order-item-img"
			>
			<div class="order-item-info">
				<div class="order-item-name" data-phantom="order_product_name"><?php echo esc_html( isset( $item['name'] ) ? $item['name'] : '' ); ?></div>
				<div class="order-item-variant" data-phantom="order_product_variant">
					<?php
					$variant = isset( $item['variant'] ) ? $item['variant'] : '';
					$qty     = isset( $item['qty'] ) ? (int) $item['qty'] : 1;
					if ( $variant && 'One size' !== $variant ) {
						echo esc_html( $variant . ' / ' );
					}
					/* translators: %d: quantity. */
					echo esc_html( sprintf( __( 'Qty: %d', 'aureon' ), $qty ) );
					?>
				</div>
			</div>
			<div class="order-item-price" data-phantom="order_product_price"><?php echo esc_html( isset( $item['total'] ) ? $item['total'] : ( isset( $item['price'] ) ? $item['price'] : '' ) ); ?></div>
		</div>
	<?php endforeach; ?>
	<div style="padding:16px 0;border-bottom:1px solid var(--line);">
		<div class="summary-row" style="display:flex;justify-content:space-between;padding:8px 0;font-size:0.9rem;"><span style="color:var(--chrome);"><?php esc_html_e( 'Subtotal', 'aureon' ); ?></span><span><?php echo esc_html( $subtotal ); ?></span></div>
		<div class="summary-row" style="display:flex;justify-content:space-between;padding:8px 0;font-size:0.9rem;"><span style="color:var(--chrome);"><?php esc_html_e( 'Shipping', 'aureon' ); ?></span><span><?php echo esc_html( $shipping ); ?></span></div>
		<div class="summary-row" style="display:flex;justify-content:space-between;padding:8px 0;font-size:0.9rem;"><span style="color:var(--chrome);"><?php esc_html_e( 'Tax', 'aureon' ); ?></span><span><?php echo esc_html( $tax ); ?></span></div>
	</div>
	<div class="checkout-total">
		<span class="label"><?php esc_html_e( 'Total', 'aureon' ); ?></span>
		<span class="value"><?php echo esc_html( $total ); ?></span>
	</div>
	<button type="submit" class="place-order-btn" id="placeOrderBtn" data-magnetic="0.12"><?php esc_html_e( 'Place Order', 'aureon' ); ?></button>
	<div class="secure-badge"><i class="fas fa-lock"></i> <?php esc_html_e( 'Secured with 256-bit encryption', 'aureon' ); ?></div>
</div>
