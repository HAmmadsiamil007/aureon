<?php
/**
 * Hero slide — single hero panel: headline, image, CTA buttons.
 *
 * Key:    'hero/slide'
 * Source: index.html `.hero-slide`
 *
 * Props:  (all keys optional; escaped at render; missing key → default listed)
 * - `string $headline      Display headline. Default ''.`
 * - `string $accent        Accent word. Default ''.`
 * - `string $subline       Supporting line. Default ''.`
 * - `string $image         Background image URL. Default ''.`
 * - `string $mobile_image  Mobile-only background image (max-width: 767px). Default ''.`
 * - `string $alt           Image alt text. Default ''.`
 * - `string $badge         Optional eyebrow/badge above the headline. Default ''.`
 * - `string $overlay       Optional overlay color (hex/rgba). Default '' (= CSS default).`
 * - `array  $buttons       CTA button schema (label/url/style). Default [].`
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
$mobile   = isset( $componentData['mobile_image'] ) ? $componentData['mobile_image'] : '';
$alt      = isset( $componentData['alt'] ) ? $componentData['alt'] : '';
$badge    = isset( $componentData['badge'] ) ? $componentData['badge'] : '';
$overlay  = isset( $componentData['overlay'] ) ? $componentData['overlay'] : '';
$buttons  = isset( $componentData['buttons'] ) ? (array) $componentData['buttons'] : array();
?>
<div class="swiper-slide hero-slide">
	<div class="hero-slide-bg">
		<?php if ( $image ) : ?>
			<?php if ( $mobile ) : ?>
				<picture>
					<source media="(max-width: 767px)" srcset="<?php echo esc_url( $mobile ); ?>">
					<img loading="eager" src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $alt ); ?>" data-mouse-depth="0.06" data-parallax data-parallax-speed="0.15">
				</picture>
			<?php else : ?>
				<img loading="eager" src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $alt ); ?>" data-mouse-depth="0.06" data-parallax data-parallax-speed="0.15">
			<?php endif; ?>
		<?php endif; ?>
		<div class="hero-slide-overlay"<?php echo $overlay ? ' style="background:' . esc_attr( $overlay ) . '"' : ''; ?>></div>
	</div>
	<div class="container hero-slide-content">
		<div class="hero-slide-text">
<?php if ( $badge ) : ?>
				<span class="hero-eyebrow" data-swiper-parallax="-150" data-mouse-depth="0.01"><?php echo esc_html( $badge ); ?></span>
			<?php endif; ?>
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
						<a href="<?php echo esc_url( $url ); ?>" class="btn <?php echo 'outline' === $style ? 'btn-outline' : 'btn-primary'; ?> btn-lg" data-magnetic="0.12">
							<?php if ( 'outline' === $style ) : ?><i class="fas fa-play" style="margin-right: 8px; font-size: 0.7rem;"></i><?php endif; ?>
							<?php echo esc_html( $label ); ?>
						</a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>
