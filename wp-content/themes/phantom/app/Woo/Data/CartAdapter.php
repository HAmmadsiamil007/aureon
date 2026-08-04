<?php
/**
 * CartAdapter — WooCommerce cart data shaping.
 *
 * Phase 9 (WooCommerce Bridge): normalizes the WC cart (items, totals,
 * count, currency) into a Phantom-safe array through the public WC API only.
 * Guarded; inert defaults when WC is absent or the cart is unavailable.
 *
 * @package Phantom\Core\Woo\Data
 * @since 0.9.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Woo\Data;

/**
 * Cart data adapter.
 */
class CartAdapter {

	/**
	 * Cart snapshot.
	 *
	 * @return array<string, mixed>
	 */
	public function snapshot(): array {
		if ( ! function_exists( 'WC' ) || ! function_exists( 'wc_get_cart_url' ) ) {
			return $this->empty();
		}

		$cart = WC()->cart; // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WC core API.

		if ( ! $cart instanceof \WC_Cart ) {
			return $this->empty();
		}

		$items = array();

		foreach ( $cart->get_cart() as $key => $item ) {
			if ( ! is_array( $item ) || ! isset( $item['data'] ) || ! $item['data'] instanceof \WC_Product ) {
				continue;
			}

			$items[] = array(
				'key'        => (string) $key,
				'product_id' => (int) $item['data']->get_id(),
				'name'       => (string) $item['data']->get_name(),
				'quantity'   => (int) ( $item['quantity'] ?? 0 ),
				'line_total' => (float) ( $item['line_total'] ?? 0.0 ),
				'line_tax'   => (float) ( $item['line_tax'] ?? 0.0 ),
				'price'      => (string) $item['data']->get_price(),
			);
		}

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WC core function.
		$currency = function_exists( 'get_woocommerce_currency' )
			? (string) get_woocommerce_currency()
			: '';

		return array(
			'count'        => (int) $cart->get_cart_contents_count(),
			'items'        => $items,
			'subtotal'     => (float) $cart->get_subtotal(),
			'total'        => (float) $cart->get_total( 'edit' ),
			'currency'     => $currency,
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WC core functions.
			'cart_url'     => (string) wc_get_cart_url(),
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WC core functions.
			'checkout_url' => (string) wc_get_checkout_url(),
		);
	}

	/**
	 * Empty snapshot (WC absent).
	 *
	 * @return array<string, mixed>
	 */
	public function empty(): array {
		return array(
			'count'        => 0,
			'items'        => array(),
			'subtotal'     => 0.0,
			'total'        => 0.0,
			'currency'     => '',
			'cart_url'     => '',
			'checkout_url' => '',
		);
	}
}
