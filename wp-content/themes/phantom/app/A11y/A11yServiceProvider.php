<?php
/**
 * A11yServiceProvider — wire the Accessibility subsystem into the container.
 *
 * Phase 14 (Accessibility Engineering): binds `a11y.checker`, `a11y.skip_link`,
 * and `a11y.dialog`. On boot, when WordPress is present, the skip link is
 * emitted at the top of the body via `wp_body_open`. All services are lazy
 * and WP-free safe (the checker is a pure string analyzer).
 *
 * @package Phantom\Core\A11y
 * @since 0.14.0
 */

declare( strict_types=1 );

namespace Phantom\Core\A11y;

use Phantom\Core\Container\Container;
use Phantom\Core\Providers\ServiceProviderInterface;

/**
 * Registers accessibility services.
 */
final class A11yServiceProvider implements ServiceProviderInterface {

	/**
	 * {@inheritDoc}
	 *
	 * @param Container $container Service container.
	 */
	public function register( Container $container ): void {
		$container->set( 'a11y.checker', new Checker() );
		$container->set( 'a11y.dialog', new DialogManager() );

		$container->singleton(
			'a11y.skip_link',
			static fn(): SkipLink => new SkipLink()
		);
	}

	/**
	 * {@inheritDoc}
	 *
	 * @param Container $container Service container.
	 */
	public function boot( Container $container ): void {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core functions.
		if ( ! function_exists( 'add_action' ) || ! function_exists( 'wp_body_open' ) ) {
			return;
		}

		// Feature-gated, matching the other subsystems (config.features.accessibility).
		if ( ! $container->has( 'config' ) || ! $container->get( 'config' )->get( 'features.accessibility', false ) ) {
			return;
		}

		add_action(
			'wp_body_open',
			static function (): void {
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from sanitized parameters in SkipLink.
				echo SkipLink::render();
			},
			1
		);
	}
}
