<?php
/**
 * The home template — blog index (AETHER).
 *
 * Pure section composition: page hero + blog grid + newsletter.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

if ( function_exists( 'aether_render_component' ) ) :

	aether_render_component( 'hero/page-title', array(
		'label'    => __( 'Journal', 'aureon' ),
		'title'    => __( 'The AETHER Dispatch', 'aureon' ),
		'subtitle' => __( 'Insights on technology, performance, and the future of footwear', 'aureon' ),
		'behavior' => array( 'motion-text' => 'words' ),
	) );

endif;

if ( function_exists( 'aether_render_section' ) ) :

	aether_render_section( 'blog-grid', array(
		'label'    => __( 'Journal', 'aureon' ),
		'title'    => __( 'Latest From the Void', 'aureon' ),
		'subtitle' => '',
	) );

	if ( aureon_get_option( 'aether_section_newsletter', true ) ) {
		aether_render_section( 'newsletter' );
	}

endif;

get_footer();