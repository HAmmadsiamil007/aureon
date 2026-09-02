<?php
/**
 * Hero slider — auto-advancing hero with slides, dots and behavior flags.
 *
 * Key:    'hero/slider'
 * Source: index.html `.hero-slider`
 *
 * Props:  (all keys optional; escaped at render; missing key → default listed)
 * - `array $slides    Slide schema (see hero/slide). Default [].`
 * - `array $behavior  Behavior whitelist (autoplay/speed/loop). Default [].`
 *
 * Slots:  'hero/slide'
 * Variants: none
 * Tokens:  no hardcoded presentation values — theme uses `--aureon-*` custom props only.
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
<section class="hero-slider" id="heroSlider" data-phantom-bg="hero" data-mouse-parallax <?php echo aether_behavior_attrs( $behavior ); ?>>
	<div class="hero-fog" aria-hidden="true">
		<div id="hl_01" class="hf-fog">
			<div class="hf-img"></div>
			<div class="hf-img"></div>
		</div>
		<div id="hl_02" class="hf-fog">
			<div class="hf-img"></div>
			<div class="hf-img"></div>
		</div>
		<div id="hl_03" class="hf-fog">
			<div class="hf-img"></div>
			<div class="hf-img"></div>
		</div>
	</div>
	<div class="swiper hero-swiper">
		<div class="swiper-wrapper">
			<?php foreach ( $slides as $i => $slide ) : ?>
				<?php aether_render_component( 'hero/slide', $slide ); ?>
			<?php endforeach; ?>
		</div>
		<div class="hero-slider-nav">
			<button class="hero-nav-btn hero-nav-prev" data-magnetic="0.12" aria-label="Previous slide"><i class="fas fa-arrow-left"></i></button>
			<div class="hero-slide-counter"><span class="hero-current-slide">01</span> / <span class="hero-total-slides"><?php echo esc_html( $current ); ?></span></div>
			<button class="hero-nav-btn hero-nav-next" data-magnetic="0.12" aria-label="Next slide"><i class="fas fa-arrow-right"></i></button>
		</div>
		<div class="hero-slider-progress"></div>
	</div>

	<div id="hero-particles" class="hero-particles"></div>

	<div class="scroll-indicator">
		<div class="mouse">
			<div class="wheel"></div>
		</div>
		<p>Scroll to explore</p>
	</div>
</section>
