<?php
/**
 * AccountAdapter — WooCommerce account data shaping.
 *
 * Phase 9 (WooCommerce Bridge): exposes account navigation endpoints and the
 * current customer through the public WC API only. Guarded; inert defaults
 * when WC is absent or nobody is logged in.
 *
 * @package Phantom\Core\Woo\Data
 * @since 0.9.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Woo\Data;

/**
 * Account data adapter.
 */
class AccountAdapter {

	/**
	 * Account navigation endpoints (key → URL).
	 *
	 * @return array<string, string>
	 */
	public function nav(): array {
		if ( ! function_exists( 'wc_get_account_menu_items' ) ) {
			return array();
		}

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WC core function.
		$items = wc_get_account_menu_items();

		if ( ! is_array( $items ) ) {
			return array();
		}

		$nav = array();

		foreach ( $items as $key => $label ) {
			$nav[ (string) $key ] = (string) $label;
		}

		return $nav;
	}

	/**
	 * Account pages (id → URL) for the known endpoints.
	 *
	 * @return array<string, string>
	 */
	public function pages(): array {
		if ( ! function_exists( 'wc_get_page_permalink' ) ) {
			return array();
		}

		$pages = array();

		foreach ( array( 'myaccount', 'shop', 'cart', 'checkout', 'terms' ) as $page ) {
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WC core function.
			$url = wc_get_page_permalink( $page );

			if ( is_string( $url ) && '' !== $url ) {
				$pages[ $page ] = $url;
			}
		}

		return $pages;
	}

	/**
	 * Current customer summary (empty when logged out / WC absent).
	 *
	 * @return array<string, mixed>
	 */
	public function current_user(): array {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		if ( ! function_exists( 'WC' ) || ! is_user_logged_in() ) {
			return array();
		}

		$customer = WC()->customer; // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WC core API.

		if ( ! $customer instanceof \WC_Customer ) {
			return array();
		}

		$user = wp_get_current_user(); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.

		return array(
			'id'              => (int) $customer->get_id(),
			'email'           => (string) $customer->get_email(),
			'first_name'      => (string) $customer->get_first_name(),
			'last_name'       => (string) $customer->get_last_name(),
			'display_name'    => (string) $user->display_name,
			'billing_country' => (string) $customer->get_billing_country(),
		);
	}
}
