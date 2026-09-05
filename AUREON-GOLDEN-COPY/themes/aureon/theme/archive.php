<?php
/**
 * The template for displaying archive pages (AETHER).
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
		'title'    => get_the_archive_title(),
		'subtitle' => get_the_archive_description(),
		'behavior' => array( 'motion-text' => 'words' ),
	) );

endif;

if ( function_exists( 'aether_render_section' ) ) :

	aether_render_section( 'blog-grid', array(
		'label'    => __( 'Journal', 'aureon' ),
		'title'    => '',
		'subtitle' => '',
	) );

	if ( aureon_get_option( 'aether_section_newsletter', true ) ) {
		aether_render_section( 'newsletter' );
	}

endif;

get_footer();