<?php
/**
 * Lumen product card — editorial tile (M10 proof pack).
 *
 * Key:    'card/product' (override)
 * Props:  id, name, tagline, price, image, alt, url, badge,
 *         add_to_cart_url, product_type, rating, reviews, behavior, layout,
 *         price_plain, old_price_plain (same schema as engine card/product).
 * Contract: keeps .product-card, .product-image, .product-badge,
 *           .product-info, .product-name, .product-tagline,
 *           .product-price-row, .product-price, .price-old,
 *           .add-to-cart-btn[data-product-id][data-product-type] and
 *           .product-action-btn aria-labels — AJAX cart, wishlist and
 *           quick-view JS operate unchanged. Wishlist/quick-view actions
 *           omitted (Lumen editorial choice, MOVE-free).
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$componentData = isset( $componentData ) ? (array) $componentData : array();

$id       = isset( $componentData['id'] ) ? (int) $componentData['id'] : 0;
$name     = isset( $componentData['name'] ) ? $componentData['name'] : '';
$tagline  = isset( $componentData['tagline'] ) ? $componentData['tagline'] : '';
$price    = isset( $componentData['price'] ) ? $componentData['price'] : '';
$image    = isset( $componentData['image'] ) ? $componentData['image'] : '';
$alt      = isset( $componentData['alt'] ) ? $componentData['alt'] : $name;
$url      = isset( $componentData['url'] ) ? $componentData['url'] : '#';
$badge    = isset( $componentData['badge'] ) ? $componentData['badge'] : '';
$add_to_cart_url = isset( $componentData['add_to_cart_url'] ) ? $componentData['add_to_cart_url'] : '';
$product_type    = isset( $componentData['product_type'] ) ? $componentData['product_type'] : 'simple';
$layout   = isset( $componentData['layout'] ) ? $componentData['layout'] : 'home';

if ( ! $name ) {
	return;
}

if ( $add_to_cart_url ) {
	$aether_cta_url = $add_to_cart_url;
} elseif ( $id ) {
	$aether_cta_url = add_query_arg( 'add-to-cart', $id, $url );
} else {
	$aether_cta_url = $url;
}

$aether_badge_class = '';
if ( 'New' === $badge ) {
	$aether_badge_class = ' badge-new';
} elseif ( 'Limited' === $badge ) {
	$aether_badge_class = ' badge-limited';
} elseif ( 'Sale' === $badge ) {
	$aether_badge_class = ' badge-sale';
}

$price_display = isset( $componentData['price_plain'] ) ? $componentData['price_plain'] : $price;
$old_display   = isset( $componentData['old_price_plain'] ) ? $componentData['old_price_plain'] : '';
?>
<div class="product-card" <?php echo $id ? 'data-product-id="' . esc_attr( $id ) . '"' : ''; ?>>
	<div class="product-image">
		<?php if ( $badge ) : ?>
			<span class="product-badge<?php echo esc_attr( $aether_badge_class ); ?>"><?php echo esc_html( $badge ); ?></span>
		<?php endif; ?>
		<?php if ( $image ) : ?>
			<img loading="lazy" src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $alt ); ?>">
		<?php endif; ?>
	</div>
	<div class="product-info">
		<h3 class="product-name"><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $name ); ?></a></h3>
		<?php if ( $tagline ) : ?>
			<p class="product-tagline"><?php echo esc_html( $tagline ); ?></p>
		<?php endif; ?>
		<div class="product-price-row">
			<span class="product-price"><?php if ( $old_display ) : ?><span class="price-old"><?php echo esc_html( $old_display ); ?></span> <?php endif; ?><?php echo esc_html( $price_display ); ?></span>
			<a href="<?php echo esc_url( $aether_cta_url ); ?>" class="btn btn-sm btn-primary add-to-cart-btn" <?php echo $id ? 'data-product-id="' . esc_attr( $id ) . '"' : ''; ?> data-product-type="<?php echo esc_attr( $product_type ); ?>">Add to Cart</a>
		</div>
	</div>
</div>