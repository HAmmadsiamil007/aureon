<?php
/**
 * Category card — shop category tile: image, name, count.
 *
 * Key:    'card/category'
 * Source: shop.html `.card-category`
 *
 * Props:  (all keys optional; escaped at render; missing key → default listed)
 * - `string $name      Category name. Default ''.`
 * - `int $count     Item count. Default 0.`
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

$class = 'category-card';
if ( 'large' === $modifier ) {
	$class .= ' category-card--large';
} elseif ( 'accent' === $modifier ) {
	$class .= ' category-card--accent';
}
?>
<a href="<?php echo esc_url( $url ); ?>" class="<?php echo esc_attr( $class ); ?>" data-tilt data-reveal-item>
	<div class="category-card-bg">
		<?php if ( $image ) : ?>
			<img loading="lazy" src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $alt ); ?>">
		<?php endif; ?>
		<div class="category-card-overlay"></div>
	</div>
	<div class="category-card-content">
		<?php if ( '' !== $count ) : ?>
			<span class="category-count"><?php echo esc_html( $count ); ?></span>
		<?php endif; ?>
		<h3 class="category-name"><?php echo esc_html( $name ); ?></h3>
		<span class="category-cta">Shop <?php echo esc_html( $name ); ?> <i class="fas fa-arrow-right"></i></span>
	</div>
</a>
