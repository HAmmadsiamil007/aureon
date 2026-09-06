<?php
/**
 * Wishlist adapter — maps the user's saved wishlist (user meta) to
 * card/wishlist component data.
 *
 * Reads `aether_wishlist` user meta (maintained by the aether_wishlist_toggle
 * AJAX handler in aureon/theme/inc/aether-ajax.php). Logged-out visitors get
 * a login CTA; logged-in users with no saved items get a shop CTA.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Build wishlist page data.
 *
 * @return array Array with items/status/count/links.
 */
function aether_adapter_wishlist() {
	// WooCommerce must be active — wishlist items are WC products.
	if ( ! function_exists( 'wc_get_product' ) || ! function_exists( 'wc_price' ) ) {
		return array(
			'items'       => array(),
			'status'      => 'empty',
			'count'       => 0,
			'shop_url'    => home_url( '/shop/' ),
			'account_url' => wp_login_url(),
		);
	}

	$shop_url    = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
	$account_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : wp_login_url();

	if ( ! is_user_logged_in() ) {
		return array(
			'items'       => array(),
			'status'      => 'logged_out',
			'count'       => 0,
			'shop_url'    => $shop_url,
			'account_url' => $account_url,
		);
	}

	$user_id  = get_current_user_id();
	$wishlist = get_user_meta( $user_id, 'aether_wishlist', true );
	$wishlist = is_array( $wishlist ) ? array_map( 'absint', $wishlist ) : array();
	$wishlist = array_values( array_filter( $wishlist ) );

	$items = array();

	if ( ! empty( $wishlist ) ) {
		$query = new WP_Query(
			array(
				'post_type'      => 'product',
				'post_status'    => 'publish',
				'post__in'       => $wishlist,
				'posts_per_page' => -1,
				'orderby'        => 'post__in',
			)
		);

		foreach ( $query->posts as $post ) {
			$product = wc_get_product( $post );
			if ( ! $product ) {
				continue;
			}

			$items[] = array(
				'id'       => $product->get_id(),
				'title'    => $product->get_name(),
				'price'    => wp_strip_all_tags( wc_price( (float) $product->get_price() ) ),
				'image'    => get_the_post_thumbnail_url( $product->get_id(), 'medium_large' ),
				'alt'      => $product->get_name(),
				'url'      => get_permalink( $product->get_id() ),
				'behavior' => array( 'tilt' => true ),
			);
		}

		wp_reset_postdata();
	}

	return array(
		'items'       => $items,
		'status'      => empty( $items ) ? 'empty' : 'ready',
		'count'       => count( $items ),
		'shop_url'    => $shop_url,
		'account_url' => $account_url,
	);
}
