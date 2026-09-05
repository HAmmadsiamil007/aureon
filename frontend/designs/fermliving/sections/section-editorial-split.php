<?php
/**
 * Ferm Living editorial split section — text + image band.
 *
 * Used on homepage for "Bestsellers for Kids" and similar editorial blocks.
 * Renders a 50/50 split: image on one side, text + CTA on the other.
 *
 * Key:    'ferm-editorial-split' (pack section)
 * Source: fermliving.com homepage text-image sections
 * Props:  title, text, image, cta_label, cta_url, reverse (layout flip).
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

aether_register_section( 'ferm-editorial-split', array(
	'template' => 'sections/section-editorial-split.php',
	'adapter'  => 'adapter-options.php',
	'behavior' => array(),
) );

if ( ! isset( $sectionData ) || ! is_array( $sectionData ) ) {
	return;
}

$title     = isset( $sectionData['title'] ) ? $sectionData['title'] : '';
$text      = isset( $sectionData['text'] ) ? $sectionData['text'] : '';
$image     = isset( $sectionData['image'] ) ? $sectionData['image'] : '';
$image_alt = isset( $sectionData['image_alt'] ) ? $sectionData['image_alt'] : $title;
$cta_label = isset( $sectionData['cta_label'] ) ? $sectionData['cta_label'] : '';
$cta_url   = isset( $sectionData['cta_url'] ) ? $sectionData['cta_url'] : '';
$reverse   = ! empty( $sectionData['reverse'] );

if ( empty( $title ) && empty( $image ) ) {
	return;
}
?>
<section class="ferm-editorial-split">
	<div class="container">
		<div class="ferm-editorial-split-inner<?php echo $reverse ? ' ferm-editorial-split--reverse' : ''; ?>">

			<?php /* Image side */ ?>
			<div class="ferm-editorial-split-image">
				<?php if ( $image ) : ?>
					<img loading="lazy"
						 src="<?php echo esc_url( $image ); ?>"
						 alt="<?php echo esc_attr( $image_alt ); ?>"
						 width="684"
						 height="800"
						 decoding="async">
				<?php endif; ?>
			</div>

			<?php /* Text side */ ?>
			<div class="ferm-editorial-split-text">
				<?php if ( $title ) : ?>
					<h2 class="ferm-editorial-split-title"><?php echo esc_html( $title ); ?></h2>
				<?php endif; ?>

				<?php if ( $text ) : ?>
					<div class="ferm-editorial-split-body">
						<?php echo wp_kses_post( $text ); ?>
					</div>
				<?php endif; ?>

				<?php if ( $cta_label && $cta_url ) : ?>
					<a href="<?php echo esc_url( $cta_url ); ?>" class="btn ferm-editorial-split-cta">
						<?php echo esc_html( $cta_label ); ?>
					</a>
				<?php endif; ?>
			</div>

		</div>
	</div>
</section>
