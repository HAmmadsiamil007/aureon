<?php
/**
 * ServiceProviderInterface — framework service provider lifecycle.
 *
 * Phase 2 (Framework Infrastructure): subsystems register their dependencies
 * and runtime hooks through providers (plan §Phase 2 "[Provider bootstrap]
 * providers register bindings → Container"). The Kernel resolves the configured
 * provider list at boot, calls register() on each, then boot() on each — so
 * providers can consume other providers' bindings during boot (ADR-014).
 *
 * @package Phantom\Core\Providers
 * @since 0.2.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Providers;

use Phantom\Core\Container\Container;

/**
 * Contract for framework service providers.
 */
interface ServiceProviderInterface {

	/**
	 * Register bindings into the container.
	 *
	 * Must be side-effect free beyond container registration.
	 *
	 * @param Container $container Service container.
	 * @return void
	 */
	public function register( Container $container ): void;

	/**
	 * Boot after all providers have registered.
	 *
	 * Safe place to bind hooks/events that depend on other providers.
	 *
	 * @param Container $container Service container.
	 * @return void
	 */
	public function boot( Container $container ): void;
}
