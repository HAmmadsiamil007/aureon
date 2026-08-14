<?php
/**
 * Product card — grid product card: image, name, badge, rating, price.
 *
 * Key:    'card/product'
 * Source: shop.html `.card-product`
 *
 * Props:  (all keys optional; escaped at render; missing key → default listed)
 * - `int $id            Product ID (wishlist/quick-view wiring). Default 0.`
 * - `string $name        Product name. Default ''.`
 * - `string $tagline     Tagline. Default ''.`
 * - `string $price       Formatted price. Default ''.`
 * - `string $image       Product image URL. Default ''.`
 * - `string $alt         Image alt text. Default $name.`
 * - `string $url         Product link. Default '#'.`
 * - `string $badge       Badge text (e.g. NEW/SALE). Default ''.`
 * - `string $add_to_cart_url  Add-to-cart endpoint. Default ''.`
 * - `string $product_type     WC product type: 'simple'|'variable'|... Default 'simple'.`
 * - `float $rating      Star score 0-5. Default 0.`
 * - `string $reviews     Review count label. Default ''.`
 * - `array $behavior    Behavior whitelist. Default [].`
 * - `string $layout      Card layout: 'home'|'shop'. Default 'home'.`
 * - `string $price_plain      Raw numeric price (shop layout). Default = $price.`
 * - `string $old_price_plain  Raw numeric old price (shop layout). Default ''.`
 *
 * Slots:  'commerce/rating'
 * Variants: layout = home|shop
 * Tokens:  no hardcoded presentation values — theme uses `--aureon-*` custom props only.
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
$rating   = isset( $componentData['rating'] ) ? (float) $componentData['rating'] : 0;
$reviews  = isset( $componentData['reviews'] ) ? $componentData['reviews'] : '';
$behavior = isset( $componentData['behavior'] ) ? (array) $componentData['behavior'] : array();
$layout   = isset( $componentData['layout'] ) ? $componentData['layout'] : 'home';

if ( ! $name ) {
	return;
}

// Add-to-cart target: adapter URL when provided, else ?add-to-cart on the
// product link (WC-native no-JS fallback). Never calls WP/WC here — the
// view layer stays engine-pure; adapters supply the real endpoints.
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

// Shop layout — compact card (source shop.html): badge, image, name, price row, CTA.
if ( 'shop' === $layout ) {
	$price_plain    = isset( $componentData['price_plain'] ) ? $componentData['price_plain'] : $price;
	$old_price_plain = isset( $componentData['old_price_plain'] ) ? $componentData['old_price_plain'] : '';
	?>
	<div class="product-card" data-phantom="product" data-tilt data-reveal-item <?php echo $id ? 'data-product-id="' . esc_attr( $id ) . '"' : ''; ?>>
		<div class="product-image" data-image-zoom>
			<?php if ( $badge ) : ?>
				<span class="product-badge<?php echo esc_attr( $aether_badge_class ); ?>"><?php echo esc_html( $badge ); ?></span>
			<?php endif; ?>
			<?php if ( $image ) : ?>
				<img loading="lazy" src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $alt ); ?>">
			<?php endif; ?>
		</div>
		<div class="product-info">
			<h3 class="product-name"><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $name ); ?></a></h3>
			<p class="product-price"><?php if ( $old_price_plain ) : ?><span class="price-old"><?php echo esc_html( $old_price_plain ); ?></span> <?php endif; ?><?php echo esc_html( $price_plain ); ?></p>
			<a href="<?php echo esc_url( $aether_cta_url ); ?>" class="btn btn-primary btn-sm add-to-cart-btn" data-magnetic="0.12" <?php echo $id ? 'data-product-id="' . esc_attr( $id ) . '"' : ''; ?> data-product-type="<?php echo esc_attr( $product_type ); ?>">Add to Cart</a>
		</div>
	</div>
	<?php
	return;
}
?>
<div class="product-card" data-tilt data-reveal-item <?php echo $id ? 'data-product-id="' . esc_attr( $id ) . '"' : ''; ?>>
	<div class="product-image" data-image-zoom>
		<?php if ( $image ) : ?>
			<img loading="lazy" src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $alt ); ?>">
		<?php endif; ?>
		<?php if ( $badge ) : ?>
			<span class="product-badge<?php echo 'New' === $badge ? ' badge-new' : ( 'Limited' === $badge ? ' badge-limited' : '' ); ?>"><?php echo esc_html( $badge ); ?></span>
		<?php endif; ?>
		<div class="product-actions">
			<button class="product-action-btn" aria-label="Add to wishlist"><i class="fas fa-heart"></i></button>
			<button class="product-action-btn" aria-label="Quick view"><i class="fas fa-eye"></i></button>
		</div>
	</div>
	<div class="product-info">
		<?php if ( $rating > 0 ) : ?>
			<div class="product-rating">
				<?php aether_render_component( 'commerce/rating', array( 'stars' => $rating ) ); ?>
				<?php if ( '' !== $reviews ) : ?>
					<span>(<?php echo esc_html( $reviews ); ?>)</span>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		<h3 class="product-name"><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $name ); ?></a></h3>
		<?php if ( $tagline ) : ?>
			<p class="product-tagline"><?php echo esc_html( $tagline ); ?></p>
		<?php endif; ?>
		<div class="product-price-row">
			<span class="product-price"><?php echo $price; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- may carry HTML from WC price_html. ?></span>
			<a href="<?php echo esc_url( $aether_cta_url ); ?>" class="btn btn-sm btn-primary add-to-cart-btn" data-magnetic="0.12" <?php echo $id ? 'data-product-id="' . esc_attr( $id ) . '"' : ''; ?> data-product-type="<?php echo esc_attr( $product_type ); ?>">Add to Cart</a>
		</div>
	</div>
</div>
