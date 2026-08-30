<?php
/**
 * Cart / checkout adapter — real WooCommerce cart data for the AETHER
 * cart page and checkout order summary.
 *
 * @package Aureon
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resolve a product image for cart rows (featured image, else demo token).
 *
 * @param int $product_id Product ID.
 * @return string Image URL.
 */
function aether_cart_item_image( $product_id ) {
	$src = get_the_post_thumbnail_url( $product_id, 'thumbnail' );
	if ( ! $src ) {
		$src = aether_viewmodel_resolve_image( 'aether_cart_item_image' );
	}
	return $src;
}

/**
 * Cart page data — items, totals and actions from WC()->cart.
 *
 * @param array $args Context args.
 * @return array
 */
function aether_adapter_cart( $args = array() ) {
	$context = isset( $args['context'] ) ? $args['context'] : 'cart';

	$shop_url     = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
	$checkout_url = function_exists( 'wc_get_checkout_url' ) ? wc_get_checkout_url() : home_url( '/' );
	$cart_url     = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/' );

	$data = array(
		'context'      => $context,
		'cart_url'     => $cart_url,
		'shop_url'     => $shop_url,
		'checkout_url' => $checkout_url,
		'is_empty'     => true,
		'items'        => array(),
		'subtotal'     => '$0',
		'shipping'     => 'Free',
		'tax'          => '$0',
		'total'        => '$0',
	);

	$crumbs = array(
		array( 'label' => __( 'Home', 'aureon' ), 'url' => home_url( '/' ) ),
	);
	if ( 'checkout' === $context ) {
		$data['title'] = __( 'Checkout', 'aureon' );
		$crumbs[]      = array( 'label' => __( 'Cart', 'aureon' ), 'url' => $cart_url );
		$crumbs[]      = array( 'label' => __( 'Checkout', 'aureon' ), 'url' => '' );
	} else {
		$data['title'] = __( 'Your Cart', 'aureon' );
		$crumbs[]      = array( 'label' => __( 'Cart', 'aureon' ), 'url' => '' );
	}
	$data['crumbs'] = $crumbs;

	if ( ! function_exists( 'WC' ) || ! WC()->cart || WC()->cart->is_empty() ) {
		return $data;
	}

	$cart = WC()->cart;
	$data['is_empty'] = false;

	foreach ( $cart->get_cart() as $key => $cart_item ) {
		$product = $cart_item['data'];

		if ( ! $product || ! $product->exists() ) {
			continue;
		}

		$name = $product->get_name();

		// Variant text from variation attributes (e.g. "Size 10 / Obsidian").
		$variant = '';
		if ( ! empty( $cart_item['variation'] ) ) {
			$parts = array();
			foreach ( $cart_item['variation'] as $attr => $value ) {
				if ( '' === $value ) {
					continue;
				}
				$tax = function_exists( 'wc_attribute_taxonomy_name' ) ? wc_attribute_taxonomy_name( 'pa_color' ) : 'pa_color';
				if ( $attr === $tax ) {
					$parts[] = ucfirst( $value );
				} else {
					$parts[] = ucwords( str_replace( '_', ' ', $value ) );
				}
			}
			$variant = implode( ' / ', $parts );
		}
		if ( '' === $variant ) {
			$variant = __( 'One size', 'aureon' );
		}

		$qty   = (int) $cart_item['quantity'];
		$price = wp_strip_all_tags( wc_price( (float) $product->get_price() ) );

		$data['items'][] = array(
			'key'         => $key,
			'name'        => $name,
			'variant'     => $variant,
			'image'       => aether_cart_item_image( $product->get_id() ),
			'alt'         => $name,
			'price'       => $price,
			'qty'         => $qty,
			'total'       => wp_strip_all_tags( wc_price( $product->get_price() * $qty ) ),
			'remove_url'  => function_exists( 'wc_get_cart_remove_url' ) ? wc_get_cart_remove_url( $key ) : $cart_url,
			'product_url' => $product->get_permalink(),
		);
	}

	// Totals.
	$data['subtotal'] = wp_strip_all_tags( $cart->get_cart_subtotal() );
	$data['shipping'] = (float) $cart->get_shipping_total() <= 0 ? 'Free' : wp_strip_all_tags( wc_price( (float) $cart->get_shipping_total() ) );
	$data['tax']      = wp_strip_all_tags( wc_price( (float) $cart->get_total_tax() ) );
	$data['total']    = wp_strip_all_tags( wc_price( (float) $cart->get_total( 'edit' ) ) );

	return $data;
}
