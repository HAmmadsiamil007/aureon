<?php
/**
 * The template for displaying search results (AETHER).
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

if ( function_exists( 'aether_render_component' ) ) :

	aether_render_component( 'hero/page-title', array(
		'label'    => __( 'Search', 'aureon' ),
		'title'    => sprintf( __( 'Results for "%s"', 'aureon' ), get_search_query() ),
		'subtitle' => '',
		'behavior' => array( 'motion-text' => 'words' ),
	) );

endif;

if ( function_exists( 'aether_render_section' ) ) :

	aether_render_section( 'blog-grid', array(
		'label'    => __( 'Search Results', 'aureon' ),
		'title'    => '',
		'subtitle' => '',
		's'        => get_search_query(),
	) );

	if ( aureon_get_option( 'aether_section_newsletter', true ) ) {
		aether_render_section( 'newsletter' );
	}

endif;

get_footer();