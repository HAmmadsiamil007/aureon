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

// Product URL pattern detection for complete-page designs.
// When a product URL hits 404, serve the generic Ferm product template
// instead of the AETHER 404 page. This ensures /product/[slug]/ always
// renders a product presentation, even for demo/missing products.
$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? wp_parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ) : '';
if ( preg_match( '#/product/([^/]+)/?$#', $request_uri, $m ) ) {
	if ( function_exists( 'aether_is_complete_page_design' ) && aether_is_complete_page_design() ) {
		// Override the 404 status — this is a product presentation, not an error.
		status_header( 200 );
		nocache_headers();
		require_once __DIR__ . '/ferm-page.php';
		exit;
	}
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