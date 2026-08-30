<?php
/**
 * Related products — "You May Also Like" grid.
 *
 * Key:    'product/related'
 * Source: product-detail.html `.pd-related`
 *
 * Props:  (all keys optional; escaped at render; missing key → default listed)
 * - `string $title  Section title. Default 'You May Also Like'.`
 * - `array $items  Product card schemas. Default [].`
 *
 * Slots:  'cards/product'
 * Variants: none
 * Tokens:  no hardcoded presentation values — theme uses `--aureon-*` custom props only.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$componentData = isset( $componentData ) ? (array) $componentData : array();

$title = isset( $componentData['title'] ) ? $componentData['title'] : __( 'You May Also Like', 'aureon' );
$items = isset( $componentData['items'] ) ? (array) $componentData['items'] : array();

if ( empty( $items ) ) {
	return;
}
?>
<section class="pd-related">
	<div class="container">
		<h2 class="pd-section-title"><?php echo esc_html( $title ); ?></h2>
		<div class="pd-gold-line"></div>
		<div class="swiper pd-related-swiper">
			<div class="swiper-wrapper">
				<?php foreach ( $items as $product ) : ?>
					<div class="swiper-slide">
						<?php aether_render_component( 'card/product', $product ); ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>
