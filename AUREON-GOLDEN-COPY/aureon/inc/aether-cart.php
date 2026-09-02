<?php
/**
 * AETHER cart integration — header cart count fragment.
 *
 * Mirrors the shell/header cart anchor markup so AJAX add-to-cart responses
 * (and WC cart-fragments consumers) can refresh the header count without a
 * page reload. The engine's add-to-cart flow posts to `?wc-ajax=add_to_cart`
 * and reads the `a.aether-cart-count` fragment from the JSON response.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Header cart anchor markup (mirrors shell/header.php).
 *
 * @return string
 */
function aether_cart_count_markup() {
	$count = 0;
	if ( function_exists( 'WC' ) && WC()->cart ) {
		$count = (int) WC()->cart->get_cart_contents_count();
	}
	$cart_url = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/' );

	return sprintf(
		'<a href="%1$s" class="header-icon" aria-label="Cart"><i class="fas fa-shopping-bag"></i><span class="cart-count">%2$d</span></a>',
		esc_url( $cart_url ),
		$count
	);
}

/**
 * Expose the header cart count as a cart-fragments fragment.
 *
 * Key matches the anchor in shell/header.php; the engine's JS reads it after
 * a wc-ajax add_to_cart response.
 *
 * @param array $fragments Existing fragments.
 * @return array
 */
function aether_cart_count_fragment( $fragments ) {
	if ( ! is_array( $fragments ) ) {
		$fragments = array();
	}
	$fragments['a.aether-cart-count'] = aether_cart_count_markup();

	// Complete-page designs: provide a fragment matching the design's header cart HTML.
	if ( function_exists( 'aether_is_complete_page_design' ) && aether_is_complete_page_design() ) {
		$count = 0;
		if ( function_exists( 'WC' ) && WC()->cart ) {
			$count = (int) WC()->cart->get_cart_contents_count();
		}
		$fragments['.cart-count-bubble span'] = '<span class="cart-count-bubble">' . $count . '</span>';
	}

	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'aether_cart_count_fragment' );