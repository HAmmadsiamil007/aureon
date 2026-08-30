<?php
/**
 * My Account adapter — current user data for the AETHER account dashboard.
 *
 * @package Aureon
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Account dashboard data (logged-in users only).
 *
 * @return array
 */
function aether_adapter_account() {
	$user = wp_get_current_user();

	if ( ! $user || ! $user->exists() ) {
		return array();
	}

	$display = $user->display_name ? $user->display_name : ( $user->first_name ? $user->first_name : __( 'Customer', 'aureon' ) );
	$email   = $user->user_email ? $user->user_email : '';

	$orders_count = 0;
	if ( function_exists( 'wc_get_customer_order_count' ) ) {
		$orders_count = (int) wc_get_customer_order_count( $user->ID );
	}

	$address_count = 0;
	if ( get_user_meta( $user->ID, 'billing_address_1', true ) ) {
		$address_count++;
	}
	if ( get_user_meta( $user->ID, 'shipping_address_1', true ) ) {
		$address_count++;
	}

	$member_since = $user->user_registered ? gmdate( 'Y', strtotime( $user->user_registered ) ) : '';

	$endpoints = array(
		'dashboard'   => __( 'Dashboard', 'aureon' ),
		'orders'      => __( 'Orders', 'aureon' ),
		'edit-address' => __( 'Addresses', 'aureon' ),
		'edit-account' => __( 'Account Details', 'aureon' ),
	);

	$menu = array();
	foreach ( $endpoints as $endpoint => $label ) {
		$url = function_exists( 'wc_get_account_endpoint_url' ) ? wc_get_account_endpoint_url( $endpoint ) : '';
		$menu[] = array(
			'label' => $label,
			'url'   => $url,
			'icon'  => 'fas fa-' . ( 'dashboard' === $endpoint ? 'grip' : ( 'orders' === $endpoint ? 'box-open' : ( 'edit-address' === $endpoint ? 'map-marker-alt' : 'user-cog' ) ) ),
		);
	}

	$menu[] = array(
		'label' => __( 'Logout', 'aureon' ),
		'url'   => function_exists( 'wc_get_account_endpoint_url' ) ? wc_get_account_endpoint_url( 'customer-logout' ) : wp_logout_url(),
		'icon'  => 'fas fa-sign-out-alt',
	);

	return array(
		'name'           => $display,
		'email'          => $email,
		'initial'        => mb_substr( $display, 0, 1 ),
		'stats'          => array(
			array(
				'number' => number_format_i18n( $orders_count ),
				'label'  => _n( 'Order', 'Orders', $orders_count, 'aureon' ),
			),
			array(
				'number' => number_format_i18n( $address_count ),
				'label'  => _n( 'Address', 'Addresses', $address_count, 'aureon' ),
			),
			array(
				'number' => $member_since,
				'label'  => __( 'Member Since', 'aureon' ),
			),
		),
		'menu'           => $menu,
		'logout_label'   => __( 'Logout', 'aureon' ),
		'dashboard_url'  => function_exists( 'wc_get_account_endpoint_url' ) ? wc_get_account_endpoint_url( 'dashboard' ) : '',
		'shop_url'       => function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' ),
	);
}

/**
 * Orders list data (logged-in users only).
 *
 * Builds the normalized rows for the account/orders component from the
 * current customer's WooCommerce orders (newest first).
 *
 * @return array
 */
function aether_adapter_account_orders() {
	$user = wp_get_current_user();

	if ( ! $user || ! $user->exists() ) {
		return array( 'orders' => array() );
	}

	$orders = array();

	if ( function_exists( 'wc_get_orders' ) ) {
		$customer_orders = wc_get_orders(
			array(
				'customer' => $user->ID,
				'limit'    => 20,
				'orderby'  => 'date',
				'order'    => 'DESC',
			)
		);

		foreach ( $customer_orders as $order ) {
			$orders[] = array(
				'number'      => sprintf(
					/* translators: %s order number */
					__( '#%s', 'aureon' ),
					$order->get_order_number()
				),
				'date'        => $order->get_date_created() ? wc_format_datetime( $order->get_date_created(), 'M j, Y' ) : '',
				'status'      => wc_get_order_status_name( $order->get_status() ),
				'status_slug' => $order->get_status(),
				'total'       => $order->get_formatted_order_total(),
				'view_url'    => $order->get_view_order_url(),
			);
		}
	}

	return array(
		'orders'     => $orders,
		'empty_text' => __( 'When you place your first order it will show up here.', 'aureon' ),
		'shop_url'   => function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' ),
		'logout_url' => function_exists( 'wc_get_account_endpoint_url' ) ? wc_get_account_endpoint_url( 'customer-logout' ) : wp_logout_url(),
	);
}
