<?php
/**
 * Wishlist card — saved wishlist item: image, title, price.
 *
 * Key:    'card/wishlist'
 * Source: wishlist.html `.card-wishlist`
 *
 * Props:  (all keys optional; escaped at render; missing key → default listed)
 * - `int $id          Product ID (wishlist-remove wiring). Default 0.`
 * - `string $title     Item title. Default ''.`
 * - `string $price     Formatted price. Default ''.`
 * - `string $image     Image URL. Default ''.`
 * - `string $alt       Image alt text. Default $title.`
 * - `string $url       Item link. Default '#'.`
 * - `array $behavior  Behavior whitelist. Default [].`
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

$id       = isset( $componentData['id'] ) ? (int) $componentData['id'] : 0;
$title    = isset( $componentData['title'] ) ? $componentData['title'] : '';
$price    = isset( $componentData['price'] ) ? $componentData['price'] : '';
$image    = isset( $componentData['image'] ) ? $componentData['image'] : '';
$alt      = isset( $componentData['alt'] ) ? $componentData['alt'] : $title;
$url      = isset( $componentData['url'] ) ? $componentData['url'] : '#';
$behavior = isset( $componentData['behavior'] ) ? (array) $componentData['behavior'] : array();

if ( ! $title ) {
	return;
}
?>
<div class="wishlist-card" data-phantom="wishlist_item" <?php echo $id ? 'data-product-id="' . esc_attr( $id ) . '"' : ''; ?> <?php echo aether_behavior_attrs( $behavior ); ?>>
	<div class="product-image" data-image-zoom>
		<?php if ( $image ) : ?>
			<a href="<?php echo esc_url( $url ); ?>"><img loading="lazy" src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $alt ); ?>"></a>
		<?php endif; ?>
		<button class="wishlist-remove" type="button" aria-label="<?php esc_attr_e( 'Remove from wishlist', 'aureon' ); ?>"><i class="fas fa-times"></i></button>
	</div>
	<div class="product-info">
		<h3 class="product-name"><?php echo esc_html( $title ); ?></h3>
		<?php if ( $price ) : ?>
			<p class="product-price"><?php echo esc_html( $price ); ?></p>
		<?php endif; ?>
		<a href="<?php echo esc_url( $url ); ?>" class="btn btn-primary btn-sm btn-full" data-magnetic="0.12"><?php esc_html_e( 'Add to Cart', 'aureon' ); ?></a>
	</div>
</div>