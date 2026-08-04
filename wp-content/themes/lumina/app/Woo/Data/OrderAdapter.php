<?php
/**
 * OrderAdapter — WooCommerce order data shaping (HPOS-safe).
 *
 * Phase 9 (WooCommerce Bridge): reads orders exclusively through the public
 * `wc_get_order()` API, which transparently supports both legacy CPT storage
 * and High-Performance Order Storage (HPOS) — no storage internals are ever
 * touched (ADR-004). Guarded; null when WC is absent or the order is missing.
 *
 * @package Lumina\Core\Woo\Data
 * @since 0.9.0
 */

declare( strict_types=1 );

namespace Lumina\Core\Woo\Data;

/**
 * Order data adapter.
 */
class OrderAdapter {

	/**
	 * Normalized order snapshot by order id (null when unavailable).
	 *
	 * @param int $id Order id.
	 * @return array<string, mixed>|null
	 */
	public function by_id( int $id ): ?array {
		if ( $id <= 0 || ! class_exists( '\WC_Order' ) || ! function_exists( 'wc_get_order' ) ) {
			return null;
		}

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WC core function (HPOS-safe).
		$order = wc_get_order( $id );

		if ( ! $order instanceof \WC_Order ) {
			return null;
		}       $created = $order->get_date_created();

		return array(
			'id'             => $order->get_id(),
			'number'         => (string) $order->get_order_number(),
			'status'         => (string) $order->get_status(),
			'total'          => (float) $order->get_total(),
			'currency'       => (string) $order->get_currency(),
			'date'           => $created instanceof \WC_DateTime ? (string) $created->date( 'c' ) : '',
			'email'          => (string) $order->get_billing_email(),
			'billing'        => array(
				'first_name' => (string) $order->get_billing_first_name(),
				'last_name'  => (string) $order->get_billing_last_name(),
				'country'    => (string) $order->get_billing_country(),
				'city'       => (string) $order->get_billing_city(),
			),
			'shipping'       => array(
				'first_name' => (string) $order->get_shipping_first_name(),
				'last_name'  => (string) $order->get_shipping_last_name(),
				'country'    => (string) $order->get_shipping_country(),
				'city'       => (string) $order->get_shipping_city(),
			),
			'items'          => $this->items( $order ),
			'payment_method' => (string) $order->get_payment_method_title(),
		);
	}

	/**
	 * Order line items summary.
	 *
	 * @param \WC_Order $order Order object.
	 * @return list<array<string, mixed>>
	 */
	private function items( \WC_Order $order ): array {
		$items = array();

		foreach ( $order->get_items() as $item ) {
			if ( ! $item instanceof \WC_Order_Item_Product ) {
				continue;
			}

			$items[] = array(
				'product_id' => (int) $item->get_product_id(),
				'name'       => (string) $item->get_name(),
				'quantity'   => (int) $item->get_quantity(),
				'total'      => (float) $item->get_total(),
			);
		}

		return $items;
	}
}
