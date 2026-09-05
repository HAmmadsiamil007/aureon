<?php
/**
 * Ferm Living product gallery — Embla-style image carousel with dots.
 *
 * Key:    'product/gallery' (override)
 * Source: fermliving.com product page gallery
 * Props:  images (src, alt).
 * Contract: keeps .product-gallery, data-image-zoom — platform gallery JS unchanged.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$componentData = isset( $componentData ) ? (array) $componentData : array();

$images = isset( $componentData['images'] ) ? (array) $componentData['images'] : array();

if ( empty( $images ) ) {
	return;
}

$total = count( $images );
?>
<div class="product-gallery" data-component="productGallery">
	<?php /* Main carousel viewport */ ?>
	<div class="product-gallery-viewport">
		<div class="product-gallery-track">
			<?php foreach ( $images as $i => $image ) :
				$src = isset( $image['src'] ) ? $image['src'] : '';
				$alt = isset( $image['alt'] ) ? $image['alt'] : '';
				if ( empty( $src ) ) {
					continue;
				}
				?>
				<div class="product-gallery-slide<?php echo 0 === $i ? ' is-active' : ''; ?>"
					 data-gallery-slide="<?php echo (int) $i; ?>"
					 role="group"
					 aria-roledescription="Slide"
					 aria-label="Image <?php echo (int) ( $i + 1 ); ?> of <?php echo (int) $total; ?>">
					<img loading="<?php echo 0 === $i ? 'eager' : 'lazy'; ?>"
						 src="<?php echo esc_url( $src ); ?>"
						 alt="<?php echo esc_attr( $alt ); ?>"
						 width="800"
						 height="1067"
						 decoding="async"
						 data-image-zoom>
				</div>
			<?php endforeach; ?>
		</div>
	</div>

	<?php /* Dots navigation */ ?>
	<?php if ( $total > 1 ) : ?>
		<div class="product-gallery-dots" role="tablist" aria-label="Product images">
			<?php foreach ( $images as $i => $image ) : ?>
				<button type="button"
						class="product-gallery-dot<?php echo 0 === $i ? ' is-active' : ''; ?>"
						role="tab"
						aria-selected="<?php echo 0 === $i ? 'true' : 'false'; ?>"
						aria-label="Go to image <?php echo (int) ( $i + 1 ); ?>"
						data-gallery-dot="<?php echo (int) $i; ?>"></button>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<?php /* Mobile dot indicators (full-width bar) */ ?>
	<?php if ( $total > 1 ) : ?>
		<div class="product-gallery-mobile-dots">
			<?php foreach ( $images as $i => $image ) : ?>
				<button type="button"
						class="product-gallery-mobile-dot<?php echo 0 === $i ? ' is-active' : ''; ?>"
						aria-label="Go to image <?php echo (int) ( $i + 1 ); ?>"
						data-gallery-dot="<?php echo (int) $i; ?>"></button>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</div>
