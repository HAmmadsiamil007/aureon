<?php
/**
 * Blog card — blog post card: image, category, title, excerpt.
 *
 * Key:    'card/blog'
 * Source: blog.html `.card-blog`
 *
 * Props:  (all keys optional; escaped at render; missing key → default listed)
 * - `string $title     Post title. Default ''.`
 * - `string $excerpt   Excerpt. Default ''.`
 * - `string $date      Date label. Default ''.`
 * - `string $category  Category label. Default ''.`
 * - `string $image     Image URL. Default ''.`
 * - `string $alt       Image alt text. Default $title.`
 * - `string $url       Post link. Default '#'.`
 * - `array $behavior  Behavior whitelist. Default [].`
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

$title    = isset( $componentData['title'] ) ? $componentData['title'] : '';
$excerpt  = isset( $componentData['excerpt'] ) ? $componentData['excerpt'] : '';
$date     = isset( $componentData['date'] ) ? $componentData['date'] : '';
$category = isset( $componentData['category'] ) ? $componentData['category'] : '';
$image    = isset( $componentData['image'] ) ? $componentData['image'] : '';
$alt      = isset( $componentData['alt'] ) ? $componentData['alt'] : $title;
$url      = isset( $componentData['url'] ) ? $componentData['url'] : '#';
$behavior = isset( $componentData['behavior'] ) ? (array) $componentData['behavior'] : array();

if ( ! $title ) {
	return;
}
?>
<a href="<?php echo esc_url( $url ); ?>" class="blog-card" data-phantom="blog_post" <?php echo aether_behavior_attrs( $behavior ); ?>>
	<div class="blog-card-image" data-image-zoom>
		<?php if ( $image ) : ?>
			<img loading="lazy" src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $alt ); ?>">
		<?php endif; ?>
		<?php if ( $category ) : ?>
			<span class="blog-category" data-phantom="blog_category"><?php echo esc_html( $category ); ?></span>
		<?php endif; ?>
	</div>
	<div class="blog-card-content">
		<?php if ( $date ) : ?>
			<span class="blog-date" data-phantom="blog_date"><?php echo esc_html( $date ); ?></span>
		<?php endif; ?>
		<h3 class="blog-card-title" data-phantom="blog_title"><?php echo esc_html( $title ); ?></h3>
		<?php if ( $excerpt ) : ?>
			<p class="blog-card-excerpt" data-phantom="blog_excerpt"><?php echo esc_html( $excerpt ); ?></p>
		<?php endif; ?>
		<span class="blog-read-more"><?php esc_html_e( 'Read More', 'aureon' ); ?> <i class="fas fa-arrow-right"></i></span>
	</div>
</a>
