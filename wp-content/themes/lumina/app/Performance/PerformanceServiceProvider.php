<?php
/**
 * PerformanceServiceProvider — wire the Performance subsystem into the container.
 *
 * Phase 13 (Performance Engineering): binds `performance.budget` (from
 * `performance.budgets` config), `performance.logger`, `performance.guard`
 * (debug-only per `performance.query_guard`), `performance.lazy`, and
 * `performance.purger`. On boot, the query guard is wired to WordPress query
 * events when present and active. All services are lazy and WP-free safe.
 *
 * @package Lumina\Core\Performance
 * @since 0.13.0
 */

declare( strict_types=1 );

namespace Lumina\Core\Performance;

use Lumina\Core\Container\Container;
use Lumina\Core\Providers\ServiceProviderInterface;

/**
 * Registers performance services.
 */
final class PerformanceServiceProvider implements ServiceProviderInterface {

	/**
	 * {@inheritDoc}
	 *
	 * @param Container $container Service container.
	 */
	public function register( Container $container ): void {
		$container->singleton(
			'performance.budget',
			static function ( Container $c ): Budget {
				$values = $c->get( 'config' )->get( 'performance.budgets', array() );

				return new Budget( is_array( $values ) ? $values : array() );
			}
		);

		$container->singleton(
			'performance.logger',
			static fn( Container $c ): BudgetLogger => new BudgetLogger( $c->get( 'performance.budget' ) )
		);

		$container->singleton(
			'performance.guard',
			static function ( Container $c ): QueryGuard {
				$active = (bool) $c->get( 'config' )->get( 'performance.query_guard', false );

				return new QueryGuard( $active );
			}
		);

		$container->set( 'performance.lazy', new Lazy() );

		$container->singleton(
			'performance.purger',
			static function (): CachePurger {
				$flusher = static function ( string $domain ): void {
					// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
					if ( function_exists( 'do_action' ) ) {
						// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
						do_action( 'lumina_core:cache_purged', $domain );
					}
				};

				return new CachePurger( $flusher );
			}
		);
	}

	/**
	 * {@inheritDoc}
	 *
	 * @param Container $container Service container.
	 */
	public function boot( Container $container ): void {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		if ( ! function_exists( 'add_action' ) || ! $container->has( 'performance.guard' ) ) {
			return;
		}

		/**
		 * The query guard.
		 *
		 * @var QueryGuard
		 */
		$guard = $container->get( 'performance.guard' );

		if ( ! $guard->is_active() ) {
			return;
		}

		add_action(
			'pre_get_posts',
			static function () use ( $guard ): void {
				$guard->register();
			},
			10
		);
	}
}
