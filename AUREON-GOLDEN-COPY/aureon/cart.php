<?php
/**
 * WooCommerce Cart Template (AETHER).
 *
 * Pure section composition — real cart data flows from WC through the
 * cart adapter.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

if ( function_exists( 'aether_render_section' ) ) :

	aether_render_section( 'cart' );

	if ( aureon_get_option( 'aether_section_newsletter', true ) ) {
		aether_render_section( 'newsletter' );
	}

endif;

get_footer();
