<?php
/**
 * CheckoutAdapter — WooCommerce checkout data shaping.
 *
 * Phase 9 (WooCommerce Bridge): exposes the checkout fields schema and the
 * current order id through the public WC API only. Guarded; inert defaults
 * when WC is absent.
 *
 * @package Phantom\Core\Woo\Data
 * @since 0.9.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Woo\Data;

/**
 * Checkout data adapter.
 */
class CheckoutAdapter {

	/**
	 * Checkout fields schema (billing + shipping + account).
	 *
	 * @return array<string, mixed>
	 */
	public function fields_schema(): array {
		if ( ! function_exists( 'WC' ) || ! function_exists( 'wc_get_checkout_url' ) ) {
			return $this->empty();
		}

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WC core API.
		$checkout = WC()->checkout();

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WC core function.
		$checkout_url = wc_get_checkout_url();

		if ( ! $checkout instanceof \WC_Checkout ) {
			return array(
				'url'      => (string) $checkout_url,
				'billing'  => array(),
				'shipping' => array(),
				'account'  => array(),
			);
		}

		return array(
			'url'      => (string) $checkout_url,
			'billing'  => $this->fields( $checkout, 'billing' ),
			'shipping' => $this->fields( $checkout, 'shipping' ),
			'account'  => $this->fields( $checkout, 'account' ),
		);
	}

	/**
	 * Current checkout order id (0 when none).
	 *
	 * @return int
	 */
	public function order_id(): int {
		if ( ! function_exists( 'WC' ) ) {
			return 0;
		}

		$session = WC()->session; // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WC core API.

		if ( ! $session instanceof \WC_Session ) {
			return 0;
		}

		$order_id = $session->get( 'order_awaiting_payment' );

		return is_numeric( $order_id ) ? (int) $order_id : 0;
	}

	/**
	 * Normalize a checkout field group.
	 *
	 * @param \WC_Checkout $checkout Checkout object.
	 * @param string       $group    Field group key.
	 * @return array<string, array<string, mixed>>
	 */
	private function fields( \WC_Checkout $checkout, string $group ): array {
		$fields = $checkout->get_checkout_fields( $group );

		if ( ! is_array( $fields ) ) {
			return array();
		}

		$normalized = array();

		foreach ( $fields as $key => $field ) {
			if ( ! is_array( $field ) ) {
				continue;
			}

			$normalized[ (string) $key ] = array(
				'label'    => isset( $field['label'] ) ? (string) $field['label'] : '',
				'type'     => isset( $field['type'] ) ? (string) $field['type'] : 'text',
				'required' => ! empty( $field['required'] ),
				'priority' => isset( $field['priority'] ) ? (int) $field['priority'] : 0,
			);
		}

		return $normalized;
	}

	/**
	 * Empty schema (WC absent).
	 *
	 * @return array<string, mixed>
	 */
	public function empty(): array {
		return array(
			'url'      => '',
			'billing'  => array(),
			'shipping' => array(),
			'account'  => array(),
		);
	}
}
