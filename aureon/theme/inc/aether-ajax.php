<?php
/**
 * AETHER AJAX Handlers.
 *
 * Handles newsletter subscription and other AJAX endpoints.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ─── Newsletter AJAX Subscription ────────────────────────────
add_action( 'wp_ajax_aether_newsletter_subscribe', 'aether_newsletter_subscribe' );
add_action( 'wp_ajax_nopriv_aether_newsletter_subscribe', 'aether_newsletter_subscribe' );

/**
 * Handle newsletter form submission via AJAX.
 */
function aether_newsletter_subscribe() {
	check_ajax_referer( 'aether_nonce', 'nonce' );

	$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';

	if ( ! is_email( $email ) ) {
		wp_send_json_error( array( 'message' => 'Please enter a valid email address.' ) );
	}

	// Check for duplicates.
	$existing = get_transient( 'aether_newsletter_' . md5( $email ) );
	if ( $existing ) {
		wp_send_json_success( array( 'message' => 'Welcome back! You\'re already subscribed.' ) );
	}

	// Store subscription (transient as simple persistence; replace with proper DB/ESP integration).
	set_transient( 'aether_newsletter_' . md5( $email ), array(
		'email'     => $email,
		'timestamp' => current_time( 'timestamp' ),
	), YEAR_IN_SECONDS );

	// Store in option for admin visibility.
	$subscribers = get_option( 'aether_newsletter_subscribers', array() );
	if ( ! in_array( $email, $subscribers, true ) ) {
		$subscribers[] = $email;
		update_option( 'aether_newsletter_subscribers', $subscribers );
	}

	/**
	 * Fires after a successful newsletter subscription.
	 *
	 * @param string $email The subscriber email address.
	 */
	do_action( 'aether_newsletter_subscribed', $email );

	wp_send_json_success( array( 'message' => 'Welcome to the void. Check your inbox.' ) );
}

// ─── Wishlist Toggle ─────────────────────────────────────────
add_action( 'wp_ajax_aether_wishlist_toggle', 'aether_wishlist_toggle' );

/**
 * Toggle a product in the user's wishlist.
 */
function aether_wishlist_toggle() {
	check_ajax_referer( 'aether_nonce', 'nonce' );

	if ( ! is_user_logged_in() ) {
		wp_send_json_error( array( 'message' => 'Please log in to use the wishlist.' ) );
	}

	$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;

	if ( ! $product_id ) {
		wp_send_json_error( array( 'message' => 'Invalid product.' ) );
	}

	$user_id    = get_current_user_id();
	$wishlist   = get_user_meta( $user_id, 'aether_wishlist', true );
	$wishlist   = is_array( $wishlist ) ? $wishlist : array();

	if ( in_array( $product_id, $wishlist, true ) ) {
		$wishlist = array_diff( $wishlist, array( $product_id ) );
		$action   = 'removed';
	} else {
		$wishlist[] = $product_id;
		$action     = 'added';
	}

	update_user_meta( $user_id, 'aether_wishlist', array_values( $wishlist ) );

	wp_send_json_success( array(
		'action' => $action,
		'count'  => count( $wishlist ),
	) );
}

// ─── Wishlist Count ──────────────────────────────────────────
add_action( 'wp_ajax_aether_wishlist_count', 'aether_wishlist_count' );
add_action( 'wp_ajax_nopriv_aether_wishlist_count', 'aether_wishlist_count' );

/**
 * Get the wishlist item count.
 */
function aether_wishlist_count() {
	if ( ! is_user_logged_in() ) {
		wp_send_json_success( array( 'count' => 0 ) );
	}

	$user_id  = get_current_user_id();
	$wishlist = get_user_meta( $user_id, 'aether_wishlist', true );
	$count    = is_array( $wishlist ) ? count( $wishlist ) : 0;

	wp_send_json_success( array( 'count' => $count ) );
}

// ─── Quick View Product ──────────────────────────────────────
add_action( 'wp_ajax_aether_quick_view', 'aether_quick_view' );
add_action( 'wp_ajax_nopriv_aether_quick_view', 'aether_quick_view' );

/**
 * Get product data for quick view modal.
 */
function aether_quick_view() {
	check_ajax_referer( 'aether_nonce', 'nonce' );

	$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
	$product    = wc_get_product( $product_id );

	if ( ! $product ) {
		wp_send_json_error( array( 'message' => 'Product not found.' ) );
	}

	$image_id  = $product->get_image_id();
	$image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'woocommerce_large_image' ) : wc_placeholder_img_src();

	$data = array(
		'id'         => $product->get_id(),
		'name'       => $product->get_name(),
		'image'      => $image_url,
		'price'      => $product->get_price_html(),
		'short_desc' => $product->get_short_description(),
		'url'        => $product->get_permalink(),
		'rating'     => $product->get_average_rating(),
	);

	wp_send_json_success( $data );
}
