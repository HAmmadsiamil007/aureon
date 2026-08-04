<?php
/**
 * WpBridge — thin adapter to the WordPress action/filter API.
 *
 * Phase 2 (Framework Infrastructure): the single place Lumina Core touches
 * add_action/add_filter/apply_filters/do_action (plan §Phase 2 "WpBridge — thin
 * adapter"). Every call is guarded with function_exists so the same code is
 * safe in WP-free CLI contexts (smoke suites, CI) without a live install.
 *
 * @package Lumina\Core\Hooks
 * @since 0.2.0
 */

declare( strict_types=1 );

namespace Lumina\Core\Hooks;

/**
 * Guarded WordPress hook adapter.
 */
final class WpBridge {

	/**
	 * Register an action handler when WordPress is loaded.
	 *
	 * @param string   $hook     WordPress action name.
	 * @param callable $callback Handler.
	 * @param int      $priority Execution order (lower runs first).
	 * @param int      $args     Number of accepted args.
	 * @return void
	 */
	public function add_action( string $hook, callable $callback, int $priority = 10, int $args = 1 ): void {
		if ( function_exists( 'add_action' ) ) {
			add_action( $hook, $callback, $priority, $args );
		}
	}

	/**
	 * Register a filter handler when WordPress is loaded.
	 *
	 * @param string   $hook     WordPress filter name.
	 * @param callable $callback Handler.
	 * @param int      $priority Execution order (lower runs first).
	 * @return void
	 */
	public function add_filter( string $hook, callable $callback, int $priority = 10 ): void {
		if ( function_exists( 'add_filter' ) ) {
			add_filter( $hook, $callback, $priority );
		}
	}

	/**
	 * Apply a WordPress filter chain when loaded; otherwise pass the value through.
	 *
	 * @param string $hook  WordPress filter name.
	 * @param mixed  $value Value to filter.
	 * @param mixed  ...$args Additional filter args.
	 * @return mixed
	 */
	public function apply_filters( string $hook, mixed $value, mixed ...$args ): mixed {
		if ( function_exists( 'apply_filters' ) ) {
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- dynamic filter name; callers pass lumina_* hooks only.
			return apply_filters( $hook, $value, ...$args );
		}

		return $value;
	}

	/**
	 * Fire a WordPress action when loaded; otherwise no-op.
	 *
	 * @param string $hook WordPress action name.
	 * @param mixed  ...$args Action args.
	 * @return void
	 */
	public function do_action( string $hook, mixed ...$args ): void {
		if ( function_exists( 'do_action' ) ) {
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- dynamic action name; callers pass lumina_* hooks only.
			do_action( $hook, ...$args );
		}
	}
}
