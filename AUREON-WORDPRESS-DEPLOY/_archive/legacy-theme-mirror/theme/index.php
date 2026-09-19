<?php
/**
 * The main template file (AETHER fallback).
 *
 * Composed: page hero + blog grid + newsletter. Used when no other
 * template matches.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

if ( function_exists( 'aether_render_component' ) ) :

	aether_render_component( 'hero/page-title', array(
		'label'    => is_search() ? __( 'Search', 'aureon' ) : __( 'Journal', 'aureon' ),
		'title'    => is_search() ? sprintf( __( 'Results for "%s"', 'aureon' ), get_search_query() ) : get_the_archive_title(),
		'subtitle' => __( 'Insights on technology, performance, and the future of footwear', 'aureon' ),
		'behavior' => array( 'motion-text' => 'words' ),
	) );

endif;

if ( function_exists( 'aether_render_section' ) ) :

	aether_render_section( 'blog-grid' );

	if ( aureon_get_option( 'aether_section_newsletter', true ) ) {
		aether_render_section( 'newsletter' );
	}

endif;

get_footer();