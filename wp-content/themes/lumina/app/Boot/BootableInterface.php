<?php
/**
 * Bootable component contract.
 *
 * Phase 1 (Bootstrap): any component that participates in the framework boot
 * lifecycle implements register() + boot(). The Kernel is the first consumer;
 * service providers (Phase 2) follow the same contract.
 *
 * @package Lumina\Core\Boot
 * @since 0.1.0
 */

declare( strict_types=1 );

namespace Lumina\Core\Boot;

/**
 * Contract for bootable framework components.
 */
interface BootableInterface {

	/**
	 * Register the component's dependencies/services.
	 *
	 * @return void
	 */
	public function register(): void;

	/**
	 * Boot the component after registration.
	 *
	 * @return void
	 */
	public function boot(): void;
}
