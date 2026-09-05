<?php
/**
 * The template for displaying a single post (AETHER).
 *
 * Composed: blog-single (hero + meta + body + author) + related + newsletter.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$post_id = get_the_ID();

if ( function_exists( 'aether_render_section' ) ) :

	aether_render_section( 'blog-single', array( 'post_id' => $post_id ) );

	$category = '';
	$cats     = get_the_category( $post_id );
	if ( ! empty( $cats ) ) {
		$category = $cats[0]->slug;
	}

	aether_render_section( 'blog-grid', array(
		'label'           => __( 'Continue Reading', 'aureon' ),
		'title'           => __( 'Related Posts', 'aureon' ),
		'posts_per_page'  => 3,
		'category_name'   => $category,
		'post__not_in'    => array( $post_id ),
		'show_pagination' => false,
	) );

	if ( aureon_get_option( 'aether_section_newsletter', true ) ) {
		aether_render_section( 'newsletter' );
	}

endif;

get_footer();