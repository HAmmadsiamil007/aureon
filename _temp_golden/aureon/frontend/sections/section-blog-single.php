<?php
/**
 * Blog single section — article hero + meta + body + author bio.
 *
 * Source: single-blog.html .blog-hero + .blog-article
 *
 * @package Aureon
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

aether_register_section( 'blog-single', array(
	'template' => 'sections/section-blog-single.php',
	'adapter'  => 'adapter-article.php',
	'behavior' => array( 'reveal' => true ),
) );

if ( ! isset( $sectionData ) || ! is_array( $sectionData ) ) {
	return; // Registration only — render happens via aether_render_section().
}

if ( empty( $sectionData['title'] ) ) {
	return;
}

$behavior = isset( $sectionData['behavior'] ) ? aether_viewmodel_behavior( $sectionData['behavior'] ) : array();

aether_render_component( 'content/article-hero', array(
	'category' => isset( $sectionData['category'] ) ? $sectionData['category'] : '',
	'title'    => $sectionData['title'],
	'image'    => isset( $sectionData['image'] ) ? $sectionData['image'] : '',
	'alt'      => isset( $sectionData['alt'] ) ? $sectionData['alt'] : $sectionData['title'],
	'behavior' => $behavior,
) );
?>
<section class="blog-article" data-phantom-bg="hero">
	<div class="container">
		<?php
		aether_render_component( 'content/article-meta', array(
			'author'    => isset( $sectionData['author'] ) ? $sectionData['author'] : '',
			'date'      => isset( $sectionData['date'] ) ? $sectionData['date'] : '',
			'read_time' => isset( $sectionData['read_time'] ) ? $sectionData['read_time'] : '',
		) );

		aether_render_component( 'content/article-body', array(
			'content' => isset( $sectionData['content'] ) ? $sectionData['content'] : '',
		) );

		if ( ! empty( $sectionData['author'] ) ) {
			aether_render_component( 'content/author-bio', array(
				'author'      => $sectionData['author'],
				'description' => isset( $sectionData['author_bio'] ) ? $sectionData['author_bio'] : '',
				'avatar'      => isset( $sectionData['avatar'] ) ? $sectionData['avatar'] : '',
			) );
		}
		?>
	</div>
</section>