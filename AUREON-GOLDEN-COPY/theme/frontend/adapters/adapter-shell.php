<?php
/**
 * Shell adapter — sitewide shell data (announcement, header, mobile, footer).
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Resolve the wishlist page URL (falls back to the shop archive).
 *
 * @return string
 */
function aether_wishlist_url() {
	$wishlist = get_page_by_path( 'wishlist' );
	if ( $wishlist instanceof WP_Post ) {
		return get_permalink( $wishlist );
	}
	return function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
}

/**
 * Announcement bar data.
 *
 * @return array
 */
function aether_adapter_announcement() {
	// Resolve from settings — defaults are the premium marquee strings
	// registered in tokens.php, so default output is unchanged.
	$items = aureon_get_option( 'aether_announcement_items', array() );

	// Repeater may be stored as JSON (Customizer) or PHP array (tokens default).
	if ( is_string( $items ) && '' !== trim( $items ) ) {
		$items = json_decode( $items, true );
	}

	// Single-text control (aether_announcement_text) as a one-item marquee.
	if ( empty( $items ) || ! is_array( $items ) ) {
		$text  = aureon_get_option( 'aether_announcement_text', '' );
		$items = $text ? array( array( 'text' => $text ) ) : array();
	}

	$normalized = array();

	foreach ( (array) $items as $item ) {
		if ( is_array( $item ) && ! empty( $item['text'] ) ) {
			$normalized[] = array( 'text' => sanitize_text_field( $item['text'] ) );
		} elseif ( is_string( $item ) && '' !== trim( $item ) ) {
			$normalized[] = array( 'text' => sanitize_text_field( $item ) );
		}
	}

	return array(
		'items' => $normalized,
	);
}

/**
 * Desktop header data.
 *
 * @return array
 */
function aether_adapter_header() {
	$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
	$cart_url = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/' );
	$account  = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : wp_login_url();

	return array(
		'brand'     => get_bloginfo( 'name' ),
		'brand_url' => home_url( '/' ),
		'menu'      => aether_adapter_menu( 'primary' ),
		'icons'     => array(
			'search'   => $shop_url,
			'wishlist' => aether_wishlist_url(),
			'cart'     => $cart_url,
			'account'  => $account,
		),
		'cart_count' => function_exists( 'WC' ) && WC()->cart ? (int) WC()->cart->get_cart_contents_count() : 0,
	);
}

/**
 * Mobile chrome data (mobile header + slide-out menu).
 *
 * @return array
 */
function aether_adapter_mobile() {
	$menu = aether_adapter_menu( 'primary' );

	// Secondary groups: account + contact links (mirror the source template).
	$account_items = array(
		array(
			'label' => __( 'Account', 'aureon' ),
			'url'   => function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : wp_login_url(),
		),
		array(
			'label' => __( 'Wishlist', 'aureon' ),
			'url'   => aether_wishlist_url(),
		),
		array(
			'label' => __( 'Cart', 'aureon' ),
			'url'   => function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/' ),
		),
	);

	$contact_items = array();

	$announcement_items = aether_adapter_announcement();
	$announcement_texts = array_map(
		function ( $item ) {
			return isset( $item['text'] ) ? $item['text'] : '';
		},
		$announcement_items['items']
	);

	return array(
		'announcement' => $announcement_texts,
		'brand'        => get_bloginfo( 'name' ),
		'brand_url'    => home_url( '/' ),
		'groups'       => array(
			array(
				'heading' => '',
				'items'   => $menu,
			),
			array(
				'heading' => '',
				'items'   => $account_items,
			),
		),
		'cta'          => array(
			'label' => __( 'Shop Now', 'aureon' ),
			'url'   => function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' ),
		),
		'socials'      => aether_adapter_socials(),
	);
}
