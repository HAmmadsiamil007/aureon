<?php
/**
 * Registry — lazy bridge registry.
 *
 * Phase 8 (Plugin Bridges): maps slug → factory callable; bridges are built
 * exactly once on first `get()` (lazy, memoized). Factories come from the
 * service provider (the canonical 12 bridges), so registering a bridge is a
 * one-line closure — no bridge is ever constructed until requested.
 *
 * @package Lumina\Core\Bridges
 * @since 0.8.0
 */

declare( strict_types=1 );

namespace Lumina\Core\Bridges;

/**
 * Lazy slug → bridge map.
 */
class Registry {

	/**
	 * Bridge factories by slug.
	 *
	 * @var array<string, callable(): BridgeInterface>
	 */
	private array $factories = array();

	/**
	 * Resolved bridge instances by slug.
	 *
	 * @var array<string, BridgeInterface>
	 */
	private array $resolved = array();

	/**
	 * Register a bridge factory.
	 *
	 * @param string   $slug     Bridge slug.
	 * @param callable $factory  Factory returning a BridgeInterface.
	 * @return void
	 */
	public function register( string $slug, callable $factory ): void {
		$this->factories[ $slug ] = $factory;
		unset( $this->resolved[ $slug ] );
	}

	/**
	 * Whether a bridge slug is registered.
	 *
	 * @param string $slug Bridge slug.
	 * @return bool
	 */
	public function has( string $slug ): bool {
		return isset( $this->factories[ $slug ] );
	}

	/**
	 * Resolve a bridge (lazy, memoized), or null when unregistered.
	 *
	 * @param string $slug Bridge slug.
	 * @return BridgeInterface|null
	 */
	public function get( string $slug ): ?BridgeInterface {
		if ( isset( $this->resolved[ $slug ] ) ) {
			return $this->resolved[ $slug ];
		}

		if ( ! isset( $this->factories[ $slug ] ) ) {
			return null;
		}

		$bridge                  = ( $this->factories[ $slug ] )();
		$this->resolved[ $slug ] = $bridge;

		return $bridge;
	}

	/**
	 * All registered slugs.
	 *
	 * @return list<string>
	 */
	public function slugs(): array {
		return array_keys( $this->factories );
	}

	/**
	 * Whether a bridge was resolved yet (laziness test seam).
	 *
	 * @param string $slug Bridge slug.
	 * @return bool
	 */
	public function is_resolved( string $slug ): bool {
		return isset( $this->resolved[ $slug ] );
	}
}
