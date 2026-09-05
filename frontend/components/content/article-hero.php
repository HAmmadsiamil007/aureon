<?php
/**
 * Article hero — single-article header: category, title, image.
 *
 * Key:    'content/article-hero'
 * Source: single-blog.html `.article-hero`
 *
 * Props:  (all keys optional; escaped at render; missing key → default listed)
 * - `string $category  Category label. Default ''.`
 * - `string $title     Article title. Default ''.`
 * - `string $image     Cover image URL. Default ''.`
 * - `string $alt       Image alt text. Default $title.`
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

$category = isset( $componentData['category'] ) ? $componentData['category'] : '';
$title    = isset( $componentData['title'] ) ? $componentData['title'] : '';
$image    = isset( $componentData['image'] ) ? $componentData['image'] : '';
$alt      = isset( $componentData['alt'] ) ? $componentData['alt'] : $title;
$behavior = isset( $componentData['behavior'] ) ? (array) $componentData['behavior'] : array();
?>
<section class="blog-hero" data-parallax-section data-phantom-bg="hero" <?php echo aether_behavior_attrs( $behavior ); ?>>
	<div class="hero-fog" aria-hidden="true">
		<div id="hl_01" class="hf-fog">
			<div class="hf-img"></div>
			<div class="hf-img"></div>
		</div>
		<div id="hl_02" class="hf-fog">
			<div class="hf-img"></div>
			<div class="hf-img"></div>
		</div>
		<div id="hl_03" class="hf-fog">
			<div class="hf-img"></div>
			<div class="hf-img"></div>
		</div>
	</div>
	<?php if ( $image ) : ?>
		<img loading="lazy" src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $alt ); ?>" class="blog-hero-image" data-phantom-alt="blog_hero">
	<?php endif; ?>
	<div class="blog-hero-overlay">
		<div class="container">
			<?php if ( $category ) : ?>
				<span class="blog-category" data-phantom="blog_category"><?php echo esc_html( $category ); ?></span>
			<?php endif; ?>
			<h1 class="blog-hero-title" data-phantom="page_title"><?php echo esc_html( $title ); ?></h1>
		</div>
	</div>
</section>