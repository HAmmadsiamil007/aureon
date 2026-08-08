<?php
/**
 * Hero slide — single hero panel: headline, image, CTA buttons.
 *
 * Key:    'hero/slide'
 * Source: index.html `.hero-slide`
 *
 * Props:  (all keys optional; escaped at render; missing key → default listed)
 * - `string $headline  Display headline. Default ''.`
 * - `string $accent    Accent word. Default ''.`
 * - `string $subline   Supporting line. Default ''.`
 * - `string $image     Background image URL. Default ''.`
 * - `string $alt       Image alt text. Default ''.`
 * - `array $buttons    CTA button schema (label/url/style). Default [].`
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

$headline = isset( $componentData['headline'] ) ? $componentData['headline'] : '';
$accent   = isset( $componentData['accent'] ) ? $componentData['accent'] : '';
$subline  = isset( $componentData['subline'] ) ? $componentData['subline'] : '';
$image    = isset( $componentData['image'] ) ? $componentData['image'] : '';
$alt      = isset( $componentData['alt'] ) ? $componentData['alt'] : '';
$buttons  = isset( $componentData['buttons'] ) ? (array) $componentData['buttons'] : array();
?>
<div class="swiper-slide hero-slide">
	<div class="hero-slide-bg">
		<?php if ( $image ) : ?>
			<img loading="eager" src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $alt ); ?>" data-mouse-depth="0.06" data-parallax data-parallax-speed="0.15">
		<?php endif; ?>
		<div class="hero-slide-overlay"></div>
	</div>
	<div class="container hero-slide-content">
		<div class="hero-slide-text">
			<h1 class="hero-headline" data-swiper-parallax="-200" data-mouse-depth="0.02"><?php echo esc_html( $headline ); ?><?php if ( $accent ) : ?><br><span class="hero-headline-accent"><?php echo esc_html( $accent ); ?></span><?php endif; ?></h1>
			<?php if ( $subline ) : ?>
				<p class="hero-subline" data-swiper-parallax="-300"><?php echo esc_html( $subline ); ?></p>
			<?php endif; ?>
			<?php if ( ! empty( $buttons ) ) : ?>
				<div class="hero-cta-group" data-swiper-parallax="-400" data-mouse-depth="0.03">
					<?php foreach ( $buttons as $button ) : ?>
						<?php
						$label = isset( $button['label'] ) ? $button['label'] : '';
						$url   = isset( $button['url'] ) ? $button['url'] : '#';
						$style = isset( $button['style'] ) ? $button['style'] : 'primary';
						?>
						<a href="<?php echo esc_url( $url ); ?>" class="btn <?php echo 'outline' === $style ? 'btn-outline' : 'btn-primary'; ?> btn-lg" data-magnetic="0.12"><?php echo esc_html( $label ); ?></a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>
