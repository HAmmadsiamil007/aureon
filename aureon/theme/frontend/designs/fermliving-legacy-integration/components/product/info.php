<?php
/**
 * Ferm Living product info — title, price, color swatches, size, quantity, add-to-cart.
 *
 * Key:    'product/info' (override)
 * Source: fermliving.com product page info section
 * Props:  same schema as engine product/info + swatches array.
 * Contract: keeps .pd-info, .pd-title, .pd-price, data-product-id,
 *           data-product-type, [data-button-add-to-cart] — AJAX cart JS unchanged.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$componentData = isset( $componentData ) ? (array) $componentData : array();

$id           = isset( $componentData['id'] ) ? (int) $componentData['id'] : 0;
$product_type = isset( $componentData['product_type'] ) ? $componentData['product_type'] : 'simple';
$title        = isset( $componentData['title'] ) ? $componentData['title'] : '';
$price        = isset( $componentData['price'] ) ? $componentData['price'] : '';
$price_plain  = isset( $componentData['price_plain'] ) ? $componentData['price_plain'] : '';
$old_price    = isset( $componentData['old_price_plain'] ) ? $componentData['old_price_plain'] : '';
$badge        = isset( $componentData['badge'] ) ? $componentData['badge'] : '';
$description  = isset( $componentData['description'] ) ? $componentData['description'] : '';
$swatches     = isset( $componentData['swatches'] ) ? (array) $componentData['swatches'] : array();
$sizes        = isset( $componentData['sizes'] ) ? (array) $componentData['sizes'] : array();
$quantity     = isset( $componentData['quantity'] ) ? (int) $componentData['quantity'] : 1;
$add_url      = isset( $componentData['add_to_cart_url'] ) ? $componentData['add_to_cart_url'] : '#';
$add_label    = isset( $componentData['add_to_cart_label'] ) ? $componentData['add_to_cart_label'] : __( 'Add to Cart', 'aureon' );
$certified    = isset( $componentData['certified'] ) ? $componentData['certified'] : '';
$sku          = isset( $componentData['sku'] ) ? $componentData['sku'] : '';

if ( ! $title ) {
	return;
}
?>
<div class="product-info" data-phantom-product>
	<div class="product-info-inner">

		<?php /* Badge */ ?>
		<?php if ( $badge ) : ?>
			<div class="product-badge"><?php echo esc_html( $badge ); ?></div>
		<?php endif; ?>

		<?php /* Title */ ?>
		<h1 class="product-title" data-phantom="page_title"><?php echo esc_html( $title ); ?></h1>

		<?php /* Price */ ?>
		<div class="product-price" data-phantom="product_price">
			<?php if ( $old_price ) : ?>
				<span class="product-price-old"><?php echo esc_html( $old_price ); ?></span>
			<?php endif; ?>
			<span class="product-price-current"><?php echo $price ? wp_kses_post( $price ) : esc_html( $price_plain ); ?></span>
		</div>

		<?php /* Certified badge */ ?>
		<?php if ( $certified ) : ?>
			<div class="product-certified">
				<span class="product-certified-badge">Certified</span>
				<span class="product-certified-reason"><?php echo esc_html( $certified ); ?></span>
			</div>
		<?php endif; ?>

		<?php /* Color swatches */ ?>
		<?php if ( ! empty( $swatches ) ) : ?>
			<div class="product-swatches" role="radiogroup" aria-label="Color">
				<?php foreach ( $swatches as $swatch ) :
					$swatch_color = isset( $swatch['color'] ) ? $swatch['color'] : '';
					$swatch_url   = isset( $swatch['url'] ) ? $swatch['url'] : '#';
					$swatch_label = isset( $swatch['label'] ) ? $swatch['label'] : '';
					$swatch_active = ! empty( $swatch['active'] );
					if ( empty( $swatch_color ) ) {
						continue;
					}
					?>
					<a href="<?php echo esc_url( $swatch_url ); ?>"
					   class="product-swatch<?php echo $swatch_active ? ' is-active' : ''; ?>"
					   title="<?php echo esc_attr( $swatch_label ); ?>"
					   aria-label="<?php echo esc_attr( $swatch_label ); ?>"
					   role="radio"
					   aria-checked="<?php echo $swatch_active ? 'true' : 'false'; ?>">
						<span class="product-swatch-inner" style="background-color: <?php echo esc_attr( $swatch_color ); ?>"></span>
					</a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php /* Size selector */ ?>
		<?php if ( ! empty( $sizes ) ) : ?>
			<div class="product-sizes" role="radiogroup" aria-label="Size">
				<?php foreach ( $sizes as $size ) :
					$size_label    = isset( $size['label'] ) ? $size['label'] : '';
					$size_available = isset( $size['available'] ) ? (bool) $size['available'] : true;
					$size_active    = ! empty( $size['active'] );
					if ( empty( $size_label ) ) {
						continue;
					}
					?>
					<button type="button"
							class="product-size<?php echo $size_active ? ' is-active' : ''; ?><?php echo ! $size_available ? ' is-unavailable' : ''; ?>"
							<?php echo ! $size_available ? 'disabled' : ''; ?>
							aria-label="<?php echo esc_attr( $size_label ); ?><?php echo ! $size_available ? ' (out of stock)' : ''; ?>"
							role="radio"
							aria-checked="<?php echo $size_active ? 'true' : 'false'; ?>">
						<?php echo esc_html( $size_label ); ?>
					</button>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php /* Quantity + Add to Cart */ ?>
		<div class="product-purchase">
			<div class="product-quantity" data-component="quantityStepper">
				<button type="button"
						class="product-quantity-btn"
						data-decrease-quantity
						aria-label="Decrease quantity">-</button>
				<input type="number"
					   class="product-quantity-input"
					   name="quantity"
					   value="<?php echo esc_attr( $quantity ); ?>"
					   min="1"
					   step="1"
					   aria-label="Quantity"
					   data-quantity>
				<button type="button"
						class="product-quantity-btn"
						data-increase-quantity
						aria-label="Increase quantity">+</button>
			</div>

			<a href="<?php echo esc_url( $add_url ); ?>"
			   class="btn product-add-to-cart"
			   <?php echo $id ? 'data-product-id="' . esc_attr( $id ) . '"' : ''; ?>
			   data-product-type="<?php echo esc_attr( $product_type ); ?>"
			   data-button-add-to-cart>
				<?php echo esc_html( $add_label ); ?>
			</a>
		</div>

		<?php /* Description (accordion-ready) */ ?>
		<?php if ( $description ) : ?>
			<div class="product-description" data-phantom="product_description">
				<?php echo wp_kses_post( $description ); ?>
			</div>
		<?php endif; ?>

		<?php /* SKU */ ?>
		<?php if ( $sku ) : ?>
			<div class="product-sku">
				<span class="product-sku-label">SKU:</span>
				<span class="product-sku-value"><?php echo esc_html( $sku ); ?></span>
			</div>
		<?php endif; ?>

	</div>
</div>
