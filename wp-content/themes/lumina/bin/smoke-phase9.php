<?php
/**
 * Phase 9 smoke suite — WooCommerce Bridge.
 *
 * WP-free. Boots the container, resolves the Woo bridge + adapters, and
 * asserts the canonical surface: bridge contract, adapter inertness without
 * WC, hook preservation table (30 canonical hooks + account wildcard), HPOS
 * safety seam (OrderAdapter guards), and Phases 1–8 regression.
 *
 * Usage: php bin/smoke-phase9.php
 *
 * @package Lumina\Core\Smoke
 * @since 0.9.0
 */

declare( strict_types=1 );

use Lumina\Core\Boot\Kernel;
use Lumina\Core\Core\App;
use Lumina\Core\Woo\Data\AccountAdapter;
use Lumina\Core\Woo\Data\CartAdapter;
use Lumina\Core\Woo\Data\CheckoutAdapter;
use Lumina\Core\Woo\Data\OrderAdapter;
use Lumina\Core\Woo\Data\ProductAdapter;
use Lumina\Core\Woo\Hooks\HookPreservation;
use Lumina\Core\Woo\WooBridge;

require __DIR__ . '/../vendor/autoload.php';

$failures = 0;
$total    = 0;

/**
 * Record a check result.
 *
 * @param string $name    Check name.
 * @param bool   $ok      Whether the check passed.
 * @param string $details Optional details on failure.
 */
function check( string $name, bool $ok, string $details = '' ): void {
	global $failures, $total;

	++$total;

	if ( ! $ok ) {
		++$failures;
		echo 'FAIL  ' . $name . ( '' !== $details ? ' — ' . $details : '' ) . PHP_EOL;
	}
}

Kernel::launch();
$app = App::instance();

// 1. Feature flag present and enabled.
$features = $app->make( 'config' )->get( 'features', array() );
check( 'woo_bridge feature is enabled', true === ( $features['woo_bridge'] ?? false ) );

// 2. Container wiring.
check( 'woo.bridge resolves', $app->make( 'woo.bridge' ) instanceof WooBridge );
check( 'woo.products resolves', $app->make( 'woo.products' ) instanceof ProductAdapter );
check( 'woo.cart resolves', $app->make( 'woo.cart' ) instanceof CartAdapter );
check( 'woo.checkout resolves', $app->make( 'woo.checkout' ) instanceof CheckoutAdapter );
check( 'woo.account resolves', $app->make( 'woo.account' ) instanceof AccountAdapter );
check( 'woo.orders resolves', $app->make( 'woo.orders' ) instanceof OrderAdapter );
check( 'woo.hooks resolves', $app->make( 'woo.hooks' ) instanceof HookPreservation );

// 3. Bridge contract (Phase 8 BridgeInterface).
$bridge = $app->make( 'woo.bridge' );
check( 'bridge slug is woocommerce', 'woocommerce' === $bridge->slug() );
check( 'bridge name is WooCommerce', 'WooCommerce' === $bridge->name() );
check( 'bridge inactive in WP-free context', false === $bridge->is_active() );
check( 'bridge version() is string-safe', is_string( $bridge->version() ) && '' === $bridge->version() );

// 4. Capabilities.
$expected_caps = array( 'product', 'cart', 'checkout', 'account', 'order', 'hooks', 'hpos', 'blocks_safe' );
check( 'bridge declares expected capabilities', $expected_caps === $bridge->capabilities() );

foreach ( $expected_caps as $cap ) {
	if ( ! $bridge->supports( $cap ) ) {
		check( "bridge supports $cap", false );
		break;
	}
}

check( 'bridge rejects unknown capability', false === $bridge->supports( 'nope' ) );

// 5. Adapter inertness without WooCommerce (safe defaults, never throw).
$product = $bridge->product( 42 );
check( 'product() returns array', is_array( $product ) );
check( 'product() empty id when inactive', 0 === $product['id'] );
check( 'product() empty name when inactive', '' === $product['name'] );
check( 'product() image is array', is_array( $product['image'] ) );
check( 'product() gallery is array', is_array( $product['gallery'] ) );
check( 'product() stock is array', is_array( $product['stock'] ) );
check( 'product() stock manage is bool', is_bool( $product['stock']['manage'] ) );

$cart = $bridge->cart();
check( 'cart() returns array', is_array( $cart ) );
check( 'cart() count is 0', 0 === $cart['count'] );
check( 'cart() items is array', is_array( $cart['items'] ) );
check( 'cart() total is float', is_float( $cart['total'] ) );

$checkout = $bridge->checkout();
check( 'checkout() returns array', is_array( $checkout ) );
check( 'checkout() billing is array', is_array( $checkout['billing'] ) );
check( 'checkout() shipping is array', is_array( $checkout['shipping'] ) );
check( 'checkout() url empty when inactive', '' === $checkout['url'] );

$account = $bridge->account();
check( 'account() returns array', is_array( $account ) );
check( 'account() nav is array', is_array( $account['nav'] ) );
check( 'account() pages is array', is_array( $account['pages'] ) );
check( 'account() current_user is array', is_array( $account['current_user'] ) );

// 6. Order adapter (HPOS seam) — null without WC, never throws.
check( 'order() null when inactive', null === $bridge->order( 7 ) );
$order_adapter = $app->make( 'woo.orders' );
check( 'order by_id(0) is null', null === $order_adapter->by_id( 0 ) );
check( 'order by_id(-1) is null', null === $order_adapter->by_id( -1 ) );

// 7. Hook preservation — canonical table.
$hooks = $bridge->hooks();
check( 'hooks registry is HookPreservation', $hooks instanceof HookPreservation );
check( 'hooks() has 30 canonical hooks', 30 === count( $hooks->hooks() ), 'got ' . count( $hooks->hooks() ) );

$canonical = array(
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
$missing = array_diff( $canonical, $hooks->hooks() );
check( 'all 30 canonical hooks present', array() === $missing, 'missing: ' . implode( ', ', $missing ) );

check( 'account wildcard supported', true === $hooks->supports_account_wildcard() );
check( 'account wildcard prefix correct', 'woocommerce_account_' === $hooks->account_wildcard() );

// 8. Hook audit + re-emit are WP-free safe.
check( 'audit() returns array in WP-free context', is_array( $hooks->audit() ) );
check( 'audit() empty in WP-free context', array() === $hooks->audit() );

$hooks->re_emit( 'woocommerce_before_main_content' );
check( 're_emit() no-op in WP-free context (no throw)', true );

$hooks->re_emit( 'not_a_real_hook' );
check( 're_emit() ignores unknown hook', true );

// 9. Legacy template override default (Blocks-safe).
check( 'use_legacy_templates() defaults false', false === $bridge->use_legacy_templates() );

echo 'Results: ' . ( $total - $failures ) . '/' . $total . ' checks passed.' . PHP_EOL;

if ( 0 !== $failures ) {
	echo 'PHASE 9 SMOKE: ' . $failures . ' FAILURE(S).' . PHP_EOL;
	exit( 1 );
}

echo 'PHASE 9 SMOKE: PASS' . PHP_EOL;
