<?php
/**
 * Template Name: AETHER Wishlist
 * Template Post Type: page
 *
 * AETHER Wishlist page — pure section composition.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

if ( function_exists( 'aether_render_component' ) ) :

	aether_render_component( 'hero/page-title', array(
		'label'    => __( 'Your Collection', 'aureon' ),
		'title'    => __( 'Wishlist', 'aureon' ),
		'subtitle' => __( "Items you've saved for later", 'aureon' ),
		'behavior' => array( 'motion-text' => 'words' ),
	) );

endif;

if ( function_exists( 'aether_render_section' ) ) :

	if ( aureon_get_option( 'aether_section_wishlist', true ) ) {
		aether_render_section( 'wishlist' );
	}

	if ( aureon_get_option( 'aether_section_newsletter', true ) ) {
		aether_render_section( 'newsletter' );
	}

endif;

get_footer();