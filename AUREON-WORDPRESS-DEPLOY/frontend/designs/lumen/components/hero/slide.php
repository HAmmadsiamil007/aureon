<?php
/**
 * Lumen hero slide — editorial panel (M10 proof pack).
 *
 * Key:    'hero/slide' (override)
 * Props:  headline, accent, subline, image, mobile_image, alt, badge,
 *         overlay, buttons (same schema as engine hero/slide).
 * Contract: keeps .hero-slide, .hero-eyebrow, .hero-headline,
 *           .hero-headline-accent, .hero-subline, .hero-cta-group,
 *           .btn-primary/.btn-outline — copy rendered for SEO/SSR.
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
$buttons  = isset( $componentData['buttons'] ) ? (array) $componentData['buttons'] : array();
?>
<div class="swiper-slide hero-slide">
	<div class="container hero-slide-content">
		<div class="hero-slide-text">
			<?php if ( $badge ) : ?>
				<span class="hero-eyebrow"><?php echo esc_html( $badge ); ?></span>
			<?php endif; ?>
			<h1 class="hero-headline"><?php echo esc_html( $headline ); ?><?php if ( $accent ) : ?><br><span class="hero-headline-accent"><?php echo esc_html( $accent ); ?></span><?php endif; ?></h1>
			<?php if ( $subline ) : ?>
				<p class="hero-subline"><?php echo esc_html( $subline ); ?></p>
			<?php endif; ?>
			<?php if ( ! empty( $buttons ) ) : ?>
				<div class="hero-cta-group">
					<?php foreach ( $buttons as $button ) : ?>
						<?php
						$label = isset( $button['label'] ) ? $button['label'] : '';
						$url   = isset( $button['url'] ) ? $button['url'] : '#';
						$style = isset( $button['style'] ) ? $button['style'] : 'primary';
						?>
						<a href="<?php echo esc_url( $url ); ?>" class="lumen-hero-btn <?php echo 'outline' === $style ? 'lumen-hero-btn-outline' : 'lumen-hero-btn-primary'; ?>">
							<?php echo esc_html( $label ); ?>
						</a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
	<?php if ( $image ) : ?>
		<div class="hero-slide-media">
			<?php if ( $mobile ) : ?>
				<picture>
					<source media="(max-width: 767px)" srcset="<?php echo esc_url( $mobile ); ?>">
					<img loading="eager" src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $alt ); ?>">
				</picture>
			<?php else : ?>
				<img loading="eager" src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $alt ); ?>">
			<?php endif; ?>
		</div>
	<?php endif; ?>
</div>