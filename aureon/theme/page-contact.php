<?php
/**
 * Template Name: AETHER Contact
 * Template Post Type: page
 *
 * AETHER Contact page — pure section composition.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

if ( function_exists( 'aether_render_component' ) ) :

	aether_render_component( 'hero/page-title', array(
		'label'    => __( 'Contact', 'aureon' ),
		'title'    => __( 'Get in Touch', 'aureon' ),
		'subtitle' => __( 'Questions about an order, sizing, or the collection? Send a signal.', 'aureon' ),
		'behavior' => array( 'motion-text' => 'words' ),
	) );

endif;

if ( function_exists( 'aether_render_section' ) ) :

	if ( aureon_get_option( 'aether_section_contact', true ) ) {
		aether_render_section( 'contact' );
	}

	if ( aureon_get_option( 'aether_section_newsletter', true ) ) {
		aether_render_section( 'newsletter' );
	}

endif;

get_footer();