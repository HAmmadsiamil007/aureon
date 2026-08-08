<?php
/**
 * Sticky bar — sticky mobile product bar with price + add-to-cart.
 *
 * Key:    'product/sticky-bar'
 * Source: product-detail.html `.pd-sticky`
 *
 * Props:  (all keys optional; escaped at render; missing key → default listed)
 * - `string $image            Thumb image. Default ''.`
 * - `string $name             Product title. Default ''.`
 * - `string $price            Formatted price. Default ''.`
 * - `array $sizes            Size schema. Default [].`
 * - `string $add_to_cart_url  Add-to-cart endpoint. Default '#'.`
 *
 * Slots:  none
 * Variants: none
 * Tokens:  no hardcoded presentation values — theme uses `--aureon-*` custom props only.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$componentData = isset( $componentData ) ? (array) $componentData : array();

$image    = isset( $componentData['image'] ) ? $componentData['image'] : '';
$name     = isset( $componentData['name'] ) ? $componentData['name'] : '';
$price    = isset( $componentData['price'] ) ? $componentData['price'] : '';
$sizes    = isset( $componentData['sizes'] ) ? (array) $componentData['sizes'] : array();
$add_url  = isset( $componentData['add_to_cart_url'] ) ? $componentData['add_to_cart_url'] : '#';

if ( ! $name ) {
	return;
}
?>
<div class="pd-sticky-bar" id="pdStickyBar">
	<div class="container">
		<div class="pd-sticky-inner">
			<div class="pd-sticky-product">
				<?php if ( $image ) : ?>
					<img loading="lazy" src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $name ); ?>" data-phantom-alt="sticky_product">
				<?php endif; ?>
				<div>
					<span class="pd-sticky-name" data-phantom="sticky_product_name"><?php echo esc_html( $name ); ?></span>
					<span class="pd-sticky-price" data-phantom="sticky_product_price"><?php echo esc_html( $price ); ?></span>
				</div>
			</div>
			<div class="pd-sticky-actions">
				<?php if ( ! empty( $sizes ) ) : ?>
					<div class="pd-sticky-sizes">
						<select class="pd-sticky-size-select" id="stickySizeSelect" aria-label="Select size">
							<option value=""><?php esc_html_e( 'Size', 'aureon' ); ?></option>
							<?php foreach ( $sizes as $size ) : ?>
								<option><?php echo esc_html( $size ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				<?php endif; ?>
				<a class="btn btn-primary pd-sticky-add" data-magnetic="0.12" href="<?php echo esc_url( $add_url ); ?>"><?php esc_html_e( 'Add to Cart', 'aureon' ); ?></a>
			</div>
		</div>
	</div>
</div>
