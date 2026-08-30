<?php
/**
 * Lumen hero slider — editorial Swiper (M10 proof pack).
 *
 * Key:    'hero/slider' (override)
 * Props:  slides, behavior (same schema as engine hero/slider).
 * Contract: keeps .hero-slider, .swiper, .swiper-wrapper, .swiper-slide,
 *           .hero-nav-prev/.hero-nav-next, .hero-current-slide,
 *           .hero-total-slides — Swiper init + counter JS operate unchanged.
 *           Fog/particles/mouse-parallax are luxury design choices (REMOVE).
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$componentData = isset( $componentData ) ? (array) $componentData : array();

$slides   = isset( $componentData['slides'] ) ? (array) $componentData['slides'] : array();
$behavior = isset( $componentData['behavior'] ) ? (array) $componentData['behavior'] : array();

if ( empty( $slides ) ) {
	return;
}

$total = count( $slides );
$current = $total < 10 ? '0' . $total : (string) $total;
?>
<section class="hero-slider lumen-hero-slider" id="heroSlider" <?php echo aether_behavior_attrs( $behavior ); ?>>
	<div class="swiper hero-swiper">
		<div class="swiper-wrapper">
			<?php foreach ( $slides as $i => $slide ) : ?>
				<?php aether_render_component( 'hero/slide', $slide ); ?>
			<?php endforeach; ?>
		</div>
		<div class="hero-slider-nav">
			<button class="hero-nav-btn hero-nav-prev" aria-label="Previous slide"><i class="fas fa-arrow-left"></i></button>
			<div class="hero-slide-counter"><span class="hero-current-slide">01</span> / <span class="hero-total-slides"><?php echo esc_html( $current ); ?></span></div>
			<button class="hero-nav-btn hero-nav-next" aria-label="Next slide"><i class="fas fa-arrow-right"></i></button>
		</div>
		<div class="hero-slider-progress"></div>
	</div>
</section>