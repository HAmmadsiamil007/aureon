<?php
/**
 * Plugin Name: Aureon — WC Session Fix
 * Description: Initializes the WooCommerce session early and guards
 *              session-dependent checkout calls so REST API, Customizer
 *              and front-end requests never hit
 *              "order_awaiting_payment on null" PHP warnings.
 * Version:     1.0.0
 * Author:      Aureon Studio
 * License:     GPL-2.0-or-later
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

/**
 * Ensure the WC session object exists before anything reads it.
 *
 * WC only initializes the session lazily; on REST / Customizer / CLI
 * requests WC()->session can still be null when checkout code calls
 * ->get()/->set() on it. Initialize defensively on every entry point.
 */
function aureon_wc_session_early_init() {
	if ( ! function_exists( 'WC' ) ) {
		return;
	}

	$woocommerce = WC();

	if ( ! $woocommerce instanceof WooCommerce ) {
		return;
	}

	if ( null === $woocommerce->session ) {
		$woocommerce->initialize_session();
	}
}
add_action( 'init', 'aureon_wc_session_early_init', 0 );
add_action( 'rest_api_init', 'aureon_wc_session_early_init', 0 );
add_action( 'customize_preview_init', 'aureon_wc_session_early_init', 0 );
add_action( 'wp_ajax_woocommerce_add_to_cart', 'aureon_wc_session_early_init', 0 );
add_action( 'wp_ajax_nopriv_woocommerce_add_to_cart', 'aureon_wc_session_early_init', 0 );

/**
 * Safety net around wc_clear_cart_after_payment().
 *
 * The function itself cannot be overridden, but we can pre-empt its only
 * failure mode: calling wc_empty_cart() after the session was destroyed.
 * Re-initializing the session here keeps order_awaiting_payment lookups
 * from hitting null.
 */
add_action( 'woocommerce_checkout_order_processed', 'aureon_wc_session_guard_after_payment', 1 );
function aureon_wc_session_guard_after_payment() {
	if ( ! function_exists( 'WC' ) ) {
		return;
	}

	$woocommerce = WC();

	if ( $woocommerce instanceof WooCommerce && null === $woocommerce->session ) {
		$woocommerce->initialize_session();
	}
}
