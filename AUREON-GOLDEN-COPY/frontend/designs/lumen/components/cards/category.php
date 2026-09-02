<?php
/**
 * Lumen category card — editorial tile (M10 proof pack).
 *
 * Key:    'card/category' (override)
 * Props:  name, count, image, alt, url, modifier (same schema as engine).
 * Contract: keeps .category-card, .category-image, .category-count,
 *           .category-name, aria-label — style + interaction hooks intact.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$componentData = isset( $componentData ) ? (array) $componentData : array();

$name     = isset( $componentData['name'] ) ? $componentData['name'] : '';
$count    = isset( $componentData['count'] ) ? $componentData['count'] : '';
$image    = isset( $componentData['image'] ) ? $componentData['image'] : '';
$alt      = isset( $componentData['alt'] ) ? $componentData['alt'] : $name;
$url      = isset( $componentData['url'] ) ? $componentData['url'] : '#';

if ( ! $name ) {
	return;
}

$screen_reader_text = sprintf( __( 'Shop %s', 'aureon' ), $name );
?>
<a href="<?php echo esc_url( $url ); ?>" class="category-card" aria-label="<?php echo esc_attr( $screen_reader_text ); ?>">
	<div class="category-image">
		<?php if ( $image ) : ?>
			<img
				loading="lazy"
				src="<?php echo esc_url( $image ); ?>"
				alt="<?php echo esc_attr( $alt ); ?>"
				width="400"
				height="300"
				decoding="async"
			>
		<?php endif; ?>
	</div>
	<div class="category-info">
		<?php if ( '' !== $count ) : ?>
			<span class="category-count"><?php echo esc_html( $count ); ?></span>
		<?php endif; ?>
		<h3 class="category-name"><?php echo esc_html( $name ); ?></h3>
	</div>
</a>