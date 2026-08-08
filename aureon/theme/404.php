<?php
/**
 * The template for displaying 404 pages (AETHER).
 *
 * Composed: error hero + newsletter.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

if ( function_exists( 'aether_render_component' ) ) :

	$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );

	aether_render_component( 'error/404', array(
		'code'        => '404',
		'title'       => __( 'Lost in the Void', 'aureon' ),
		'description' => __( "The page you're looking for doesn't exist or has been moved.", 'aureon' ),
		'home_url'    => home_url( '/' ),
		'shop_url'    => $shop_url,
		'behavior'    => array( 'motion-text' => 'words' ),
	) );

	if ( aureon_get_option( 'aether_section_newsletter', true ) ) {
		if ( function_exists( 'aether_render_section' ) ) {
			aether_render_section( 'newsletter' );
		}
	}

endif;

get_footer();