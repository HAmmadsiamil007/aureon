<?php
/**
 * AETHER AJAX Handlers — wishlist toggle/count, quick view + contact.
 *
 * Newsletter subscription lives in aether-newsletter.php (DB-backed).
 * All handlers verify a nonce (shared aether_nonce; contact uses its own
 * aether_contact nonce, matching the contact form's hidden field).
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ─── Wishlist Toggle ─────────────────────────────────────────
add_action( 'wp_ajax_aether_wishlist_toggle', 'aether_wishlist_toggle' );
add_action( 'wp_ajax_nopriv_aether_wishlist_toggle', 'aether_wishlist_toggle' );
/**
 * Toggle a product in the user's wishlist.
 */
function aether_wishlist_toggle() {
	check_ajax_referer( 'aether_nonce', 'nonce' );

	if ( ! is_user_logged_in() ) {
		wp_send_json_error( array( 'message' => __( 'Please log in to use the wishlist.', 'aureon' ), 'redirect' => function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : wp_login_url() ) );
	}

	$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;

	if ( ! $product_id ) {
		wp_send_json_error( array( 'message' => __( 'Invalid product.', 'aureon' ) ) );
	}

	$user_id  = get_current_user_id();
	$wishlist = get_user_meta( $user_id, 'aether_wishlist', true );
	$wishlist = is_array( $wishlist ) ? $wishlist : array();

	if ( in_array( $product_id, $wishlist, true ) ) {
		$wishlist = array_diff( $wishlist, array( $product_id ) );
		$action   = 'removed';
	} else {
		$wishlist[] = $product_id;
		$action     = 'added';
	}

	update_user_meta( $user_id, 'aether_wishlist', array_values( $wishlist ) );

	wp_send_json_success(
		array(
			'action' => $action,
			'count'  => count( $wishlist ),
		)
	);
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
 * Get product data for the quick view modal.
 */
function aether_quick_view() {
	check_ajax_referer( 'aether_nonce', 'nonce' );

	$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
	$product    = function_exists( 'wc_get_product' ) ? wc_get_product( $product_id ) : false;

	if ( ! $product ) {
		wp_send_json_error( array( 'message' => __( 'Product not found.', 'aureon' ) ) );
	}

	$image_id  = $product->get_image_id();
	$image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'woocommerce_large_image' ) : wc_placeholder_img_src();

	wp_send_json_success(
		array(
			'id'         => $product->get_id(),
			'name'       => $product->get_name(),
			'image'      => $image_url,
			'price'      => wp_strip_all_tags( wc_price( (float) $product->get_price() ) ),
			'short_desc' => wp_strip_all_tags( $product->get_short_description() ),
			'url'        => $product->get_permalink(),
			'rating'     => $product->get_average_rating(),
		)
	);
}

// ─── Contact Submit ──────────────────────────────────────────
add_action( 'wp_ajax_aether_contact_submit', 'aether_contact_submit' );
add_action( 'wp_ajax_nopriv_aether_contact_submit', 'aether_contact_submit' );
/**
 * Handle the AETHER contact form — validated + rate-limited, emailed to
 * the site admin (or the configured recipient).
 */
function aether_contact_submit() {
	check_ajax_referer( 'aether_contact', 'aether_contact_nonce' );

	$name    = isset( $_POST['aether_name'] ) ? sanitize_text_field( wp_unslash( $_POST['aether_name'] ) ) : '';
	$email   = isset( $_POST['aether_email'] ) ? sanitize_email( wp_unslash( $_POST['aether_email'] ) ) : '';
	$subject = isset( $_POST['aether_subject'] ) ? sanitize_text_field( wp_unslash( $_POST['aether_subject'] ) ) : 'general';
	$message = isset( $_POST['aether_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['aether_message'] ) ) : '';

	$subject_labels = array(
		'general'   => __( 'General Inquiry', 'aureon' ),
		'order'     => __( 'Order Support', 'aureon' ),
		'returns'   => __( 'Returns', 'aureon' ),
		'wholesale' => __( 'Wholesale', 'aureon' ),
	);

	if ( '' === $name || '' === $message ) {
		wp_send_json_error( array( 'message' => __( 'Please fill in every required field.', 'aureon' ) ) );
	}

	if ( ! is_email( $email ) ) {
		wp_send_json_error( array( 'message' => __( 'Please enter a valid email address.', 'aureon' ) ) );
	}

	// Rate limit: one submission per IP per minute.
	$ip_key = 'aether_contact_rate_' . md5( isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '0.0.0.0' );
	if ( get_transient( $ip_key ) ) {
		wp_send_json_error( array( 'message' => __( 'Please wait before sending another message.', 'aureon' ) ) );
	}

	$recipient = apply_filters( 'aether_contact_recipient', (string) aureon_get_option( 'aether_contact_recipient', get_option( 'admin_email' ) ) );
	if ( ! is_email( $recipient ) ) {
		$recipient = get_option( 'admin_email' );
	}

	$subject_label = isset( $subject_labels[ $subject ] ) ? $subject_labels[ $subject ] : $subject;

	$mail_body  = sprintf( __( 'Name: %s', 'aureon' ), $name ) . "\n";
	$mail_body .= sprintf( __( 'Email: %s', 'aureon' ), $email ) . "\n";
	$mail_body .= sprintf( __( 'Subject: %s', 'aureon' ), $subject_label ) . "\n\n";
	$mail_body .= $message . "\n";

	$headers = array( 'Reply-To: ' . $name . ' <' . $email . '>' );

	$sent = wp_mail(
		$recipient,
		sprintf( '[%s] %s', get_bloginfo( 'name' ), $subject_label ),
		$mail_body,
		$headers
	);

	if ( ! $sent ) {
		wp_send_json_error( array( 'message' => __( 'The message could not be sent. Please try again.', 'aureon' ) ) );
	}

	set_transient( $ip_key, true, MINUTE_IN_SECONDS );

	wp_send_json_success(
		array(
			'message' => __( 'Thank you — your message has been sent. We reply within one business day.', 'aureon' ),
		)
	);
}
