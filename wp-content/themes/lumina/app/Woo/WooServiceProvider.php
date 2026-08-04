<?php
/**
 * WooServiceProvider — wire the WooCommerce Bridge into the container.
 *
 * Phase 9 (WooCommerce Bridge): binds the Woo adapters, hook preservation,
 * and the `woo.bridge` facade. The bridge is lazy — nothing is constructed
 * until requested, so sites without WooCommerce pay zero cost. Bridges are
 * passive: no hooks installed, no templates overridden, no assets enqueued
 * (legacy overrides are opt-in, default off — Blocks-safe).
 *
 * @package Lumina\Core\Woo
 * @since 0.9.0
 */

declare( strict_types=1 );

namespace Lumina\Core\Woo;

use Lumina\Core\Container\Container;
use Lumina\Core\Providers\ServiceProviderInterface;
use Lumina\Core\Woo\Data\AccountAdapter;
use Lumina\Core\Woo\Data\CartAdapter;
use Lumina\Core\Woo\Data\CheckoutAdapter;
use Lumina\Core\Woo\Data\OrderAdapter;
use Lumina\Core\Woo\Data\ProductAdapter;
use Lumina\Core\Woo\Hooks\HookPreservation;

/**
 * Registers WooCommerce bridge services.
 */
final class WooServiceProvider implements ServiceProviderInterface {

	/**
	 * {@inheritDoc}
	 *
	 * @param Container $container Service container.
	 */
	public function register( Container $container ): void {
		$container->set( 'woo.products', new ProductAdapter() );
		$container->set( 'woo.cart', new CartAdapter() );
		$container->set( 'woo.checkout', new CheckoutAdapter() );
		$container->set( 'woo.account', new AccountAdapter() );
		$container->set( 'woo.orders', new OrderAdapter() );
		$container->set( 'woo.hooks', new HookPreservation() );

		$container->singleton(
			'woo.bridge',
			static function ( Container $c ): WooBridge {
				return new WooBridge(
					$c->get( 'woo.products' ),
					$c->get( 'woo.cart' ),
					$c->get( 'woo.checkout' ),
					$c->get( 'woo.account' ),
					$c->get( 'woo.orders' ),
					$c->get( 'woo.hooks' )
				);
			}
		);
	}

	/**
	 * {@inheritDoc}
	 *
	 * @param Container $container Service container.
	 */
	public function boot( Container $container ): void {
		// Passive bridge — nothing to boot (Blocks-safe, opt-in overrides).
	}
}
