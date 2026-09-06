<?php
/**
 * Ferm Living category card — full-bleed image with title overlay.
 *
 * Key:    'card/category' (override)
 * Source: fermliving.com category tile structure
 * Props:  same schema as engine card/category.
 * Contract: keeps .category-card, .category-card-bg, .category-card-content,
 *           .category-card-overlay — platform tilt/reveal JS operates unchanged.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$componentData = isset( $componentData ) ? (array) $componentData : array();

$name     = isset( $componentData['name'] ) ? $componentData['name'] : '';
$count    = isset( $componentData['count'] ) ? $componentData['count'] : '';
$image    = isset( $componentData['image'] ) ? $componentData['image'] : '';
$alt      = isset( $componentData['alt'] ) ? $componentData['alt'] : $name;
$url      = isset( $componentData['url'] ) ? $componentData['url'] : '#';
$modifier = isset( $componentData['modifier'] ) ? $componentData['modifier'] : '';

if ( ! $name ) {
	return;
}

$screen_reader_text = sprintf( __( 'Shop %s', 'aureon' ), $name );
?>
<a href="<?php echo esc_url( $url ); ?>" class="category-card" data-reveal-item aria-label="<?php echo esc_attr( $screen_reader_text ); ?>">
	<div class="category-card-bg">
		<?php if ( $image ) : ?>
			<img loading="lazy"
				 src="<?php echo esc_url( $image ); ?>"
				 alt="<?php echo esc_attr( $alt ); ?>"
				 width="800"
				 height="421"
				 decoding="async">
		<?php endif; ?>
	</div>
	<div class="category-card-content">
		<h3><?php echo esc_html( $name ); ?></h3>
	</div>
</a>
