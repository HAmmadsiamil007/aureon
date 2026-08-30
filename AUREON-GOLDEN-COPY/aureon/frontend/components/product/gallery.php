<?php
/**
 * Product gallery — image stack with zoom and thumbnails.
 *
 * Key:    'product/gallery'
 * Source: product-detail.html `.pd-gallery`
 *
 * Props:  (all keys optional; escaped at render; missing key → default listed)
 * - `array $images  Image schema (src/zoom). Default [].`
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

$images = isset( $componentData['images'] ) ? (array) $componentData['images'] : array();

if ( empty( $images ) ) {
	return;
}
?>
<div class="pd-gallery">
	<div class="pd-gallery-main" data-image-zoom>
		<div class="swiper pd-gallery-swiper">
			<div class="swiper-wrapper">
				<?php foreach ( $images as $i => $image ) : ?>
					<div class="swiper-slide">
						<img loading="lazy" src="<?php echo esc_url( $image['src'] ); ?>" alt="<?php echo esc_attr( isset( $image['alt'] ) ? $image['alt'] : '' ); ?>" data-phantom-alt="product_gallery_<?php echo (int) $i + 1; ?>">
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<button class="pd-gallery-zoom" aria-label="Zoom image"><i class="fas fa-expand"></i></button>
	</div>
	<div class="pd-gallery-thumbs">
		<div class="swiper pd-gallery-thumbs-swiper">
			<div class="swiper-wrapper">
				<?php foreach ( $images as $i => $image ) : ?>
					<div class="swiper-slide<?php echo 0 === $i ? ' pd-thumb-active' : ''; ?>">
						<img loading="lazy" src="<?php echo esc_url( $image['src'] ); ?>" alt="Thumbnail <?php echo (int) $i + 1; ?>" data-phantom-alt="product_thumb_<?php echo (int) $i + 1; ?>">
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</div>
