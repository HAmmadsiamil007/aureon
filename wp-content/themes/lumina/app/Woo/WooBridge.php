<?php
/**
 * WooBridge — WooCommerce capability bridge.
 *
 * Phase 9 (WooCommerce Bridge): full WooCommerce isolation on the Phase 8
 * bridge contract. Enabled when `class_exists( 'WooCommerce' )`; exposes
 * product/cart/checkout/account/order data through public WC APIs only, plus
 * hook preservation and HPOS-safe order reads. Blocks markup is never
 * replaced — legacy template overrides are opt-in via `woo.enable` config
 * (default false), per plan §Phase 9.
 *
 * @package Lumina\Core\Woo
 * @since 0.9.0
 */

declare( strict_types=1 );

namespace Lumina\Core\Woo;

use Lumina\Core\Bridges\Bridge;
use Lumina\Core\Woo\Data\AccountAdapter;
use Lumina\Core\Woo\Data\CartAdapter;
use Lumina\Core\Woo\Data\CheckoutAdapter;
use Lumina\Core\Woo\Data\OrderAdapter;
use Lumina\Core\Woo\Data\ProductAdapter;
use Lumina\Core\Woo\Hooks\HookPreservation;

/**
 * WooCommerce adapter facade.
 */
final class WooBridge extends Bridge {

	/**
	 * Product adapter.
	 *
	 * @var ProductAdapter
	 */
	private ProductAdapter $products;

	/**
	 * Cart adapter.
	 *
	 * @var CartAdapter
	 */
	private CartAdapter $cart;

	/**
	 * Checkout adapter.
	 *
	 * @var CheckoutAdapter
	 */
	private CheckoutAdapter $checkout;

	/**
	 * Account adapter.
	 *
	 * @var AccountAdapter
	 */
	private AccountAdapter $account;

	/**
	 * Order adapter (HPOS-safe).
	 *
	 * @var OrderAdapter
	 */
	private OrderAdapter $orders;

	/**
	 * Hook preservation registry.
	 *
	 * @var HookPreservation
	 */
	private HookPreservation $hooks;

	/**
	 * Build the bridge.
	 *
	 * @param ProductAdapter   $products Product adapter.
	 * @param CartAdapter      $cart     Cart adapter.
	 * @param CheckoutAdapter  $checkout Checkout adapter.
	 * @param AccountAdapter   $account  Account adapter.
	 * @param OrderAdapter     $orders   Order adapter.
	 * @param HookPreservation $hooks    Hook preservation.
	 */
	public function __construct(
		ProductAdapter $products,
		CartAdapter $cart,
		CheckoutAdapter $checkout,
		AccountAdapter $account,
		OrderAdapter $orders,
		HookPreservation $hooks
	) {
		$this->products = $products;
		$this->cart     = $cart;
		$this->checkout = $checkout;
		$this->account  = $account;
		$this->orders   = $orders;
		$this->hooks    = $hooks;
	}

	/**
	 * {@inheritDoc}
	 */
	public function slug(): string {
		return 'woocommerce';
	}

	/**
	 * {@inheritDoc}
	 */
	public function name(): string {
		return 'WooCommerce';
	}

	/**
	 * {@inheritDoc}
	 */
	public function is_active(): bool {
		return class_exists( '\WooCommerce' );
	}

	/**
	 * {@inheritDoc}
	 */
	public function version(): string {
		return $this->constant_version( 'WC_VERSION' );
	}

	/**
	 * {@inheritDoc}
	 */
	public function capabilities(): array {
		return array(
			'product',
			'cart',
			'checkout',
			'account',
			'order',
			'hooks',
			'hpos',
			'blocks_safe',
		);
	}

	/**
	 * Normalized product snapshot.
	 *
	 * @param int $id Product id.
	 * @return array<string, mixed>
	 */
	public function product( int $id ): array {
		return $this->products->from_id( $id );
	}

	/**
	 * Normalized cart snapshot.
	 *
	 * @return array<string, mixed>
	 */
	public function cart(): array {
		return $this->cart->snapshot();
	}

	/**
	 * Checkout fields schema + url.
	 *
	 * @return array<string, mixed>
	 */
	public function checkout(): array {
		return $this->checkout->fields_schema();
	}

	/**
	 * Account navigation + pages + current user.
	 *
	 * @return array<string, mixed>
	 */
	public function account(): array {
		return array(
			'nav'          => $this->account->nav(),
			'pages'        => $this->account->pages(),
			'current_user' => $this->account->current_user(),
		);
	}

	/**
	 * Normalized order snapshot (HPOS-safe), null when unavailable.
	 *
	 * @param int $id Order id.
	 * @return array<string, mixed>|null
	 */
	public function order( int $id ): ?array {
		return $this->orders->by_id( $id );
	}

	/**
	 * The canonical WooCommerce hook registry.
	 *
	 * @return HookPreservation
	 */
	public function hooks(): HookPreservation {
		return $this->hooks;
	}

	/**
	 * Whether legacy template override is enabled (default false — Blocks-safe).
	 *
	 * @return bool
	 */
	public function use_legacy_templates(): bool {
		return false;
	}
}
