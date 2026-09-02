<?php
/**
 * WooCommerce Order Received (Thank You) — AETHER.
 *
 * Pure section composition — the confirmation block reads the real
 * WC_Order through adapter-order and renders the order/confirmation
 * component, then the newsletter section.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

if ( function_exists( 'aether_render_section' ) ) :
	aether_render_section( 'order-confirmation' );

	if ( function_exists( 'aureon_get_option' ) && aureon_get_option( 'aether_section_newsletter', true ) ) {
		aether_render_section( 'newsletter' );
	}
endif;

get_footer();