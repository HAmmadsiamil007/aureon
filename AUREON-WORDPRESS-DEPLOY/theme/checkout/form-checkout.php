<?php
/**
 * WooCommerce Checkout Template (AETHER).
 *
 * Pure section composition — real checkout fields, totals and payment
 * gateways flow from WC through the cart adapter (context: checkout).
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

if ( function_exists( 'aether_render_section' ) ) :

	aether_render_section( 'checkout' );

endif;

get_footer();
