<?php
/**
 * Product info — buy box: title, price, rating, sizes, quantity, add-to-cart.
 *
 * Key:    'product/info'
 * Source: product-detail.html `.pd-info`
 *
 * Props:  (all keys optional; escaped at render; missing key → default listed)
 * - `string $badge        Badge text. Default ''.`
 * - `int $id             WC product ID (AJAX add-to-cart wiring). Default 0.`
 * - `string $product_type WC product type: 'simple'|'variable'|... Default 'simple'.`
 * - `string $title        Product title. Default ''.`
 * - `string $price        Formatted price. Default ''.`
 * - `string $price_plain  Raw numeric price. Default ''.`
 * - `string $old_price_plain  Raw numeric old price. Default ''.`
 * - `float $rating       Star score 0-5. Default 0.`
 * - `string $rating_text  Rating label (e.g. 4.9 / 312). Default ''.`
 * - `string $description  Short description. Default ''.`
 * - `array $colors       Color schema (name/hex). Default [].`
 * - `array $sizes        Size schema (label/available). Default [].`
 * - `int $quantity     Qty stepper value. Default 1.`
 * - `string $add_to_cart_url  Add-to-cart endpoint. Default '#'.`
 * - `string $add_to_cart_label  Add-to-cart button label. Default 'Add to Cart'.`
 * - `array $trust         Trust badge schema. Default [].`
 *
 * Slots:  'commerce/rating'
 * Variants: none
 * Tokens:  no hardcoded presentation values — theme uses `--aureon-*` custom props only.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$componentData = isset( $componentData ) ? (array) $componentData : array();

$badge        = isset( $componentData['badge'] ) ? $componentData['badge'] : '';
$id           = isset( $componentData['id'] ) ? (int) $componentData['id'] : 0;
$product_type = isset( $componentData['product_type'] ) ? $componentData['product_type'] : 'simple';
$title        = isset( $componentData['title'] ) ? $componentData['title'] : '';
$price        = isset( $componentData['price'] ) ? $componentData['price'] : '';
$price_plain  = isset( $componentData['price_plain'] ) ? $componentData['price_plain'] : '';
$old_price    = isset( $componentData['old_price_plain'] ) ? $componentData['old_price_plain'] : '';
$rating       = isset( $componentData['rating'] ) ? (float) $componentData['rating'] : 0;
$rating_text  = isset( $componentData['rating_text'] ) ? $componentData['rating_text'] : '';
$description  = isset( $componentData['description'] ) ? $componentData['description'] : '';
$colors       = isset( $componentData['colors'] ) ? (array) $componentData['colors'] : array();
$sizes        = isset( $componentData['sizes'] ) ? (array) $componentData['sizes'] : array();
$quantity     = isset( $componentData['quantity'] ) ? (int) $componentData['quantity'] : 1;
$add_url      = isset( $componentData['add_to_cart_url'] ) ? $componentData['add_to_cart_url'] : '#';
$add_label    = isset( $componentData['add_to_cart_label'] ) ? $componentData['add_to_cart_label'] : __( 'Add to Cart', 'aureon' );
$trust        = isset( $componentData['trust'] ) ? (array) $componentData['trust'] : array();

if ( ! $title ) {
	return;
}
?>
<div class="pd-info">
	<div class="pd-info-inner">
		<?php if ( $badge ) : ?>
			<span class="pd-badge" data-phantom="product_badge"><?php echo esc_html( $badge ); ?></span>
		<?php endif; ?>
		<h1 class="pd-title" data-phantom="page_title"><?php echo esc_html( $title ); ?></h1>
		<p class="pd-price" data-phantom="product_price">
			<?php if ( $old_price ) : ?><span class="price-old"><?php echo esc_html( $old_price ); ?></span> <?php endif; ?>
			<?php echo $price ? $price : esc_html( $price_plain ); ?>
		</p>
		<?php if ( $rating > 0 ) : ?>
			<div class="pd-rating">
				<div class="pd-stars"><?php aether_render_component( 'commerce/rating', array( 'stars' => $rating ) ); ?></div>
				<?php if ( $rating_text ) : ?>
					<span class="pd-rating-text"><?php echo esc_html( $rating_text ); ?></span>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		<?php if ( $description ) : ?>
			<p class="pd-description" data-phantom="product_description"><?php echo esc_html( $description ); ?></p>
		<?php endif; ?>

		<?php if ( ! empty( $colors ) ) : ?>
			<div class="pd-option-group">
				<div class="pd-option-header">
					<label class="pd-option-label"><?php esc_html_e( 'Color — ', 'aureon' ); ?><span id="pdColorName"><?php echo esc_html( $colors[0]['name'] ); ?></span></label>
				</div>
				<div class="pd-color-options">
					<?php foreach ( $colors as $i => $color ) : ?>
						<button class="pd-color-btn<?php echo 0 === $i ? ' active' : ''; ?>" style="background-color: <?php echo esc_attr( $color['hex'] ); ?>;" aria-label="<?php echo esc_attr( $color['name'] ); ?>" data-color="<?php echo esc_attr( $color['name'] ); ?>"></button>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $sizes ) ) : ?>
			<div class="pd-option-group">
				<div class="pd-option-header">
					<label class="pd-option-label"><?php esc_html_e( 'Size — ', 'aureon' ); ?><span id="pdSizeName"><?php esc_html_e( 'Select', 'aureon' ); ?></span></label>
					<button class="pd-size-guide-link" id="openSizeGuide"><?php esc_html_e( 'Size Guide', 'aureon' ); ?></button>
				</div>
				<div class="pd-size-grid">
					<?php foreach ( $sizes as $size ) : ?>
						<button class="pd-size-btn"><?php echo esc_html( $size ); ?></button>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>

		<div class="pd-option-group">
			<label class="pd-option-label"><?php esc_html_e( 'Quantity', 'aureon' ); ?></label>
			<div class="pd-qty">
				<button class="pd-qty-btn" id="qtyMinus" aria-label="Decrease quantity">-</button>
				<span class="pd-qty-value" id="qtyValue"><?php echo (int) $quantity; ?></span>
				<button class="pd-qty-btn" id="qtyPlus" aria-label="Increase quantity">+</button>
			</div>
		</div>

		<div class="pd-actions">
			<a class="btn btn-primary pd-add-to-cart add-to-cart-btn" data-magnetic="0.12" href="<?php echo esc_url( $add_url ); ?>" <?php echo $id ? 'data-product-id="' . esc_attr( $id ) . '"' : ''; ?> data-product-type="<?php echo esc_attr( $product_type ); ?>">
				<i class="fas fa-shopping-bag"></i>
				<?php echo esc_html( $add_label ); ?>
			</a>
			<?php if ( $id ) : ?>
				<button class="pd-wishlist-btn product-action-btn" aria-label="Add to wishlist" data-product-id="<?php echo esc_attr( $id ); ?>">
					<i class="far fa-heart"></i>
				</button>
			<?php else : ?>
				<button class="pd-wishlist-btn" aria-label="Add to wishlist">
					<i class="far fa-heart"></i>
				</button>
			<?php endif; ?>
		</div>

		<?php if ( ! empty( $trust ) ) : ?>
			<div class="pd-trust">
				<?php foreach ( $trust as $item ) : ?>
					<div class="pd-trust-item">
						<i class="fas <?php echo esc_attr( $item['icon'] ); ?>"></i>
						<span><?php echo esc_html( $item['label'] ); ?></span>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</div>
