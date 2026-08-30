<?php
/**
 * Order adapter — data for the order-received (thank-you) confirmation.
 *
 * Pulls the current WC_Order from the `order-received` query var so the
 * confirmation component shows the real order number, email and totals.
 * Falls back to a demo shape when no order is present (styleguide use).
 *
 * @package Aureon
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Order-received / thank-you page data.
 *
 * @param array $args Context args (none currently).
 * @return array
 */
function aether_adapter_order( $args = array() ) {
	$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );

	$data = array(
		'title'         => __( 'Order Confirmed', 'aureon' ),
		'subtitle'      => __( 'Your order has been placed successfully.', 'aureon' ),
		'order_number'  => '',
		'email_note'    => __( 'A confirmation email has been sent to your email address.', 'aureon' ),
		'delivery_note' => __( 'Estimated delivery: 5-7 business days', 'aureon' ),
		'shop_url'      => $shop_url,
		'track_url'     => '',
	);

	$order_id = get_query_var( 'order-received' );

	if ( $order_id && function_exists( 'wc_get_order' ) ) {
		$order = wc_get_order( $order_id );
		if ( $order ) {
			$data['order_number'] = sprintf(
				/* translators: %s order number */
				__( '#%s', 'aureon' ),
				$order->get_order_number()
			);
			if ( is_account_page() && function_exists( 'wc_get_page_permalink' ) ) {
				$data['track_url'] = wc_get_endpoint_url( 'orders' );
			}
		}
	}

	return $data;
}