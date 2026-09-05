<?php
/**
 * WooCommerce Single Product Template (AETHER).
 *
 * Pure section composition — product data flows from WC through the
 * product adapter; related products through adapter-wc-products.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

if ( function_exists( 'aether_render_section' ) ) :

	aether_render_section( 'product' );
	aether_render_section( 'related', array( 'related_to' => get_queried_object_id(), 'posts_per_page' => 4 ) );

	if ( aureon_get_option( 'aether_section_newsletter', true ) ) {
		aether_render_section( 'newsletter' );
	}

endif;

get_footer();
