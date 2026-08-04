<?php
/**
 * HookPreservation — WooCommerce official hook registry + audit.
 *
 * Phase 9 (WooCommerce Bridge): the canonical list of official WooCommerce
 * template hooks (plan §Phase 9 hook table). Phantom never removes a WC hook;
 * where Phantom output replaces a template, the hooks are re-emitted
 * (guarded by `has_action`/`do_action` presence). `audit()` reports which
 * hooks still have registered callbacks (WP-free: empty audit).
 *
 * @package Phantom\Core\Woo\Hooks
 * @since 0.9.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Woo\Hooks;

/**
 * WooCommerce hook preservation.
 */
class HookPreservation {

	/**
	 * Canonical WooCommerce template hooks (plan §Phase 9 table).
	 *
	 * `woocommerce_account_*` is a wildcard prefix matched separately.
	 *
	 * @var list<string>
	 */
	private const HOOKS = array(
		'woocommerce_before_main_content',
		'woocommerce_after_main_content',
		'woocommerce_before_shop_loop',
		'woocommerce_before_shop_loop_item',
		'woocommerce_shop_loop_item_title',
		'woocommerce_after_shop_loop_item',
		'woocommerce_after_shop_loop_item_title',
		'woocommerce_after_shop_loop',
		'woocommerce_pagination',
		'woocommerce_before_single_product_summary',
		'woocommerce_single_product_summary',
		'woocommerce_after_single_product_summary',
		'woocommerce_before_add_to_cart_form',
		'woocommerce_before_add_to_cart_button',
		'woocommerce_after_add_to_cart_button',
		'woocommerce_after_add_to_cart_form',
		'woocommerce_after_add_to_cart_quantity',
		'woocommerce_before_quantity_input_field',
		'woocommerce_after_quantity_input_field',
		'woocommerce_meta',
		'woocommerce_share',
		'woocommerce_single_product_image_thumbnail_html',
		'woocommerce_after_single_product',
		'woocommerce_before_checkout_form',
		'woocommerce_checkout_before_customer_details',
		'woocommerce_checkout_after_customer_details',
		'woocommerce_checkout_billing',
		'woocommerce_checkout_shipping',
		'woocommerce_checkout_order_review',
		'woocommerce_after_checkout_form',
	);

	/**
	 * The full canonical hook list.
	 *
	 * @return list<string>
	 */
	public function hooks(): array {
		return self::HOOKS;
	}

	/**
	 * Whether the wildcard account-hook prefix is part of the surface.
	 *
	 * @return bool
	 */
	public function supports_account_wildcard(): bool {
		return true;
	}

	/**
	 * The account-hook wildcard prefix.
	 *
	 * @return string
	 */
	public function account_wildcard(): string {
		return 'woocommerce_account_';
	}

	/**
	 * Audit: which canonical hooks still carry registered callbacks.
	 *
	 * WP-free contexts return an empty map (nothing registered yet).
	 *
	 * @return array<string, bool>
	 */
	public function audit(): array {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		if ( ! function_exists( 'has_action' ) ) {
			return array();
		}

		$audit = array();

		foreach ( self::HOOKS as $hook ) {
			$audit[ $hook ] = has_action( $hook ) || has_filter( $hook );
		}

		return $audit;
	}

	/**
	 * Re-emit a hook when it has registered callbacks (guarded).
	 *
	 * Used where Phantom output replaces a WooCommerce template: the official
	 * hook is fired so third-party callbacks keep running (plan §Phase 9).
	 *
	 * @param string $hook Hook name.
	 * @return void
	 */
	public function re_emit( string $hook ): void {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core functions.
		if ( ! function_exists( 'has_action' ) || ! function_exists( 'do_action' ) ) {
			return;
		}

		if ( in_array( $hook, self::HOOKS, true ) && ( has_action( $hook ) || has_filter( $hook ) ) ) {
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- re-emitting official WooCommerce hooks (plan §Phase 9).
			do_action( $hook );
		}
	}
}
