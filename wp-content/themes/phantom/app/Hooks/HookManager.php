<?php
/**
 * HookManager — framework hook registration with WP-free operation.
 *
 * Phase 2 (Framework Infrastructure): subsystems register WP actions/filters
 * through this manager instead of calling add_action/add_filter directly
 * (ADR-006: documented WP-boundary only). When WordPress is loaded, calls
 * delegate to the WpBridge; in WP-free contexts (CLI smoke, CI) registrations
 * are kept in internal registries so do_action/apply still behave predictably.
 *
 * Registration is idempotent per (hook, callback) pair — the same callback is
 * never double-registered (plan §Phase 2: "HookManager does not double fire").
 *
 * @package Phantom\Core\Hooks
 * @since 0.2.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Hooks;

/**
 * Framework hook registration facade.
 */
final class HookManager {

	/**
	 * WordPress adapter.
	 *
	 * @var WpBridge
	 */
	private WpBridge $bridge;

	/**
	 * WP-free action registry: hook => list of [priority, callback].
	 *
	 * @var array<string, array<int, array{priority:int, callback:callable}>>
	 */
	private array $actions = array();

	/**
	 * WP-free filter registry: hook => list of [priority, callback].
	 *
	 * @var array<string, array<int, array{priority:int, callback:callable}>>
	 */
	private array $filters = array();

	/**
	 * Constructor.
	 *
	 * @param WpBridge|null $bridge WordPress adapter (auto-created when null).
	 */
	public function __construct( ?WpBridge $bridge = null ) {
		$this->bridge = $bridge ?? new WpBridge();
	}

	/**
	 * Register an action handler.
	 *
	 * @param string   $hook     WordPress action name.
	 * @param callable $callback Handler.
	 * @param int      $priority Execution order (lower runs first).
	 * @param int      $args     Number of accepted args.
	 * @return void
	 */
	public function add_action( string $hook, callable $callback, int $priority = 10, int $args = 1 ): void {
		if ( $this->has_callback( $this->actions, $hook, $callback ) ) {
			return;
		}

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- hook name is a variable; callers pass phantom_* hooks only.
		$this->bridge->add_action( $hook, $callback, $priority, $args );
		$this->actions[ $hook ][] = array(
			'priority' => $priority,
			'callback' => $callback,
		);
	}

	/**
	 * Register a filter handler.
	 *
	 * @param string   $hook     WordPress filter name.
	 * @param callable $callback Handler.
	 * @param int      $priority Execution order (lower runs first).
	 * @return void
	 */
	public function add_filter( string $hook, callable $callback, int $priority = 10 ): void {
		if ( $this->has_callback( $this->filters, $hook, $callback ) ) {
			return;
		}

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- hook name is a variable; callers pass phantom_* hooks only.
		$this->bridge->add_filter( $hook, $callback, $priority );
		$this->filters[ $hook ][] = array(
			'priority' => $priority,
			'callback' => $callback,
		);
	}

	/**
	 * Apply the filter chain to a value.
	 *
	 * Delegates to apply_filters when WordPress is loaded; otherwise runs the
	 * internal registrations in priority order (idempotent pass-through when
	 * nothing is registered).
	 *
	 * @param string $hook  Filter name.
	 * @param mixed  $value Value to filter.
	 * @param mixed  ...$args Extra args passed to handlers.
	 * @return mixed
	 */
	public function apply( string $hook, mixed $value, mixed ...$args ): mixed {
		if ( function_exists( 'apply_filters' ) ) {
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- dynamic filter name; callers pass phantom_* hooks only.
			return apply_filters( $hook, $value, ...$args );
		}

		$handlers = $this->filters[ $hook ] ?? array();
		usort(
			$handlers,
			static function ( array $a, array $b ): int {
				return $a['priority'] <=> $b['priority'];
			}
		);

		foreach ( $handlers as $handler ) {
			$value = call_user_func( $handler['callback'], $value, ...$args );
		}

		return $value;
	}

	/**
	 * Fire the action chain (WP-free mode only; WordPress owns do_action).
	 *
	 * @param string $hook Action name.
	 * @param mixed  ...$args Args passed to handlers.
	 * @return void
	 */
	public function do_action( string $hook, mixed ...$args ): void {
		if ( function_exists( 'do_action' ) ) {
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- dynamic action name; callers pass phantom_* hooks only.
			do_action( $hook, ...$args );

			return;
		}

		$handlers = $this->actions[ $hook ] ?? array();
		usort(
			$handlers,
			static function ( array $a, array $b ): int {
				return $a['priority'] <=> $b['priority'];
			}
		);

		foreach ( $handlers as $handler ) {
			call_user_func( $handler['callback'], ...$args );
		}
	}

	/**
	 * Whether a callback is already registered for a hook in a registry.
	 *
	 * @param array<string, array<int, array{priority:int, callback:callable}>> $registry Registry to scan.
	 * @param string                                                            $hook     Hook name.
	 * @param callable                                                          $callback Callback to match.
	 * @return bool
	 */
	private function has_callback( array $registry, string $hook, callable $callback ): bool {
		foreach ( $registry[ $hook ] ?? array() as $entry ) {
			if ( $entry['callback'] === $callback ) {
				return true;
			}
		}

		return false;
	}
}
