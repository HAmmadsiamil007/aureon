<?php
/**
 * Category card — shop category tile: image, name, count.
 *
 * Key:    'card/category'
 * Source: shop.html `.card-category`
 *
 * Props:  (all keys optional; escaped at render; missing key → default listed)
 * - `string $name      Category name. Default ''.`
 * - `string $count     Item count label. Default ''.`
 * - `string $image     Image URL. Default ''.`
 * - `string $alt       Image alt text. Default $name.`
 * - `string $url       Category link. Default '#'.`
 * - `string $modifier  CSS modifier. Default ''.`
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

$name     = isset( $componentData['name'] ) ? $componentData['name'] : '';
$count    = isset( $componentData['count'] ) ? $componentData['count'] : '';
$image    = isset( $componentData['image'] ) ? $componentData['image'] : '';
$alt      = isset( $componentData['alt'] ) ? $componentData['alt'] : $name;
$url      = isset( $componentData['url'] ) ? $componentData['url'] : '#';
$modifier = isset( $componentData['modifier'] ) ? $componentData['modifier'] : '';

if ( ! $name ) {
	return;
}

// Build CSS classes.
$class = 'category-card';
if ( 'large' === $modifier ) {
	$class .= ' category-card--large';
} elseif ( 'accent' === $modifier ) {
	$class .= ' category-card--accent';
}

// Descriptive link text for screen readers.
$screen_reader_text = sprintf( __( 'Shop %s', 'aureon' ), $name );
?>
<a href="<?php echo esc_url( $url ); ?>" class="<?php echo esc_attr( $class ); ?>" data-tilt data-reveal-item aria-label="<?php echo esc_attr( $screen_reader_text ); ?>">
	<div class="category-card-bg">
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
		<div class="category-card-overlay"></div>
	</div>
	<div class="category-card-content">
		<?php if ( '' !== $count ) : ?>
			<span class="category-count"><?php echo esc_html( $count ); ?></span>
		<?php endif; ?>
		<h3 class="category-name"><?php echo esc_html( $name ); ?></h3>
		<span class="category-cta">
			<span class="category-cta-text"><?php echo esc_html( $screen_reader_text ); ?></span>
			<i class="fas fa-arrow-right" aria-hidden="true"></i>
		</span>
	</div>
</a>
