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
 * @package Phantom\Core\Woo
 * @since 0.9.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Woo;

use Phantom\Core\Container\Container;
use Phantom\Core\Providers\ServiceProviderInterface;
use Phantom\Core\Woo\Data\AccountAdapter;
use Phantom\Core\Woo\Data\CartAdapter;
use Phantom\Core\Woo\Data\CheckoutAdapter;
use Phantom\Core\Woo\Data\OrderAdapter;
use Phantom\Core\Woo\Data\ProductAdapter;
use Phantom\Core\Woo\Hooks\HookPreservation;

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
