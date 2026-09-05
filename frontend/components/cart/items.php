<?php
/**
 * Cart items — line-item table with quantity steppers and remove links.
 *
 * Key:    'cart/items'
 * Source: cart.html `.cart-items`
 *
 * Props:  (all keys optional; escaped at render; missing key → default listed)
 * - `array $items     Line item schema (name/image/price/qty/variant/remove). Default [].`
 * - `string $cart_url   Cart submit URL. Default '#'.`
 * - `string $shop_url   Continue-shopping link. Default ''.`
 *
 * Slots:  none
 * Variants: none
 * Tokens:  no hardcoded colors; two inline layout styles (padding-top, link color inherit) — consolidate into `.cart-item-*` utilities in M3.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$componentData = isset( $componentData ) ? (array) $componentData : array();

$items    = isset( $componentData['items'] ) ? (array) $componentData['items'] : array();
$cart_url = isset( $componentData['cart_url'] ) ? $componentData['cart_url'] : '#';
$shop_url = isset( $componentData['shop_url'] ) ? $componentData['shop_url'] : '';
?>
<form class="woocommerce-cart-form" action="<?php echo esc_url( $cart_url ); ?>" method="post">
	<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
	<div class="cart-table-header">
		<span><?php esc_html_e( 'Product', 'aureon' ); ?></span>
		<span><?php esc_html_e( 'Price', 'aureon' ); ?></span>
		<span><?php esc_html_e( 'Quantity', 'aureon' ); ?></span>
		<span><?php esc_html_e( 'Total', 'aureon' ); ?></span>
		<span></span>
	</div>

	<?php foreach ( $items as $item ) : ?>
		<div class="cart-item" data-phantom="cart_item">
			<div class="cart-item-info">
				<img
					loading="lazy"
					src="<?php echo esc_url( isset( $item['image'] ) ? $item['image'] : '' ); ?>"
					alt="<?php echo esc_attr( isset( $item['alt'] ) ? $item['alt'] : '' ); ?>"
					class="cart-item-img"
				>
				<div>
					<div class="cart-item-name" data-phantom="cart_product_name">
						<a href="<?php echo esc_url( isset( $item['product_url'] ) ? $item['product_url'] : '#' ); ?>" style="color:inherit;text-decoration:none;"><?php echo esc_html( isset( $item['name'] ) ? $item['name'] : '' ); ?></a>
					</div>
					<div class="cart-item-variant" data-phantom="cart_product_variant"><?php echo esc_html( isset( $item['variant'] ) ? $item['variant'] : '' ); ?></div>
				</div>
			</div>
			<div class="cart-item-price" data-phantom="cart_product_price"><?php echo esc_html( isset( $item['price'] ) ? $item['price'] : '' ); ?></div>
			<div class="cart-item-qty">
				<button type="button" class="qty-btn aether-qty-btn" data-dir="-1" aria-label="<?php esc_attr_e( 'Decrease quantity', 'aureon' ); ?>">&minus;</button>
				<input type="number" class="qty-value" name="cart[<?php echo esc_attr( isset( $item['key'] ) ? $item['key'] : '' ); ?>][qty]" value="<?php echo esc_attr( isset( $item['qty'] ) ? (int) $item['qty'] : 1 ); ?>" min="1" step="1" aria-label="<?php esc_attr_e( 'Quantity', 'aureon' ); ?>">
				<button type="button" class="qty-btn aether-qty-btn" data-dir="1" aria-label="<?php esc_attr_e( 'Increase quantity', 'aureon' ); ?>">+</button>
			</div>
			<div class="cart-item-total" data-phantom="cart_product_total"><?php echo esc_html( isset( $item['total'] ) ? $item['total'] : '' ); ?></div>
			<a class="cart-item-remove" href="<?php echo esc_url( isset( $item['remove_url'] ) ? $item['remove_url'] : '#' ); ?>" aria-label="<?php esc_attr_e( 'Remove item', 'aureon' ); ?>"><i class="fas fa-times"></i></a>
		</div>
	<?php endforeach; ?>

	<div style="padding-top: 30px;">
		<?php if ( $shop_url ) : ?>
			<a href="<?php echo esc_url( $shop_url ); ?>" class="continue-shopping"><i class="fas fa-arrow-left"></i><?php esc_html_e( 'Continue Shopping', 'aureon' ); ?></a>
		<?php endif; ?>
	</div>
</form>
