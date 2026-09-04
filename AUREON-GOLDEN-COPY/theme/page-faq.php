<?php
/**
 * Template Name: AETHER FAQ
 * Template Post Type: page
 *
 * AETHER FAQ page — pure section composition.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

if ( function_exists( 'aether_render_component' ) ) :

	aether_render_component( 'hero/page-title', array(
		'label'    => __( 'Support', 'aureon' ),
		'title'    => __( 'Got Questions?', 'aureon' ),
		'subtitle' => __( 'Everything you need to know about AETHER.', 'aureon' ),
		'behavior' => array( 'motion-text' => 'words' ),
	) );

endif;

if ( function_exists( 'aether_render_section' ) ) :

	if ( aureon_get_option( 'aether_section_faq', true ) ) {
		aether_render_section( 'faq', array(
			'cta_url' => home_url( '/contact/' ),
		) );
	}

	if ( aureon_get_option( 'aether_section_newsletter', true ) ) {
		aether_render_section( 'newsletter' );
	}

endif;

get_footer();