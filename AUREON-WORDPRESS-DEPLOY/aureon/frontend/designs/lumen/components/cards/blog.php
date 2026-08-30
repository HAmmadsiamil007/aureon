<?php
/**
 * Lumen blog card — editorial tile (M10 proof pack).
 *
 * Key:    'card/blog' (override)
 * Props:  title, excerpt, date, category, image, alt, url, read_more,
 *         behavior (same schema as engine card/blog).
 * Contract: keeps .blog-card, .blog-image, .blog-meta/category/date hooks,
 *           .blog-title, .blog-excerpt, .blog-read-more — card styling +
 *           navigation intact; motion-text parallax is a luxury choice.
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
$read_more = isset( $componentData['read_more'] ) && '' !== $componentData['read_more'] ? $componentData['read_more'] : __( 'Read More', 'aureon' );

if ( ! $title ) {
	return;
}
?>
<a href="<?php echo esc_url( $url ); ?>" class="blog-card">
	<div class="blog-image">
		<?php if ( $image ) : ?>
			<img loading="lazy" src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $alt ); ?>">
		<?php endif; ?>
	</div>
	<div class="blog-info">
		<div class="blog-meta">
			<?php if ( $date ) : ?><span class="blog-date"><?php echo esc_html( $date ); ?></span><?php endif; ?>
			<?php if ( $category ) : ?><span class="blog-category"><?php echo esc_html( $category ); ?></span><?php endif; ?>
		</div>
		<h3 class="blog-title"><?php echo esc_html( $title ); ?></h3>
		<?php if ( $excerpt ) : ?>
			<p class="blog-excerpt"><?php echo esc_html( $excerpt ); ?></p>
		<?php endif; ?>
		<span class="blog-read-more"><?php echo esc_html( $read_more ); ?> <i class="fas fa-arrow-right"></i></span>
	</div>
</a>