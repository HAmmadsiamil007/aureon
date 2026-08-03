<?php
/**
 * DynamicRegistry — lazy factory-backed registry.
 *
 * Phase 2 (Framework Infrastructure): entries registered via register() are
 * factories resolved once on first get() (then cached for the process);
 * entries set() directly are returned as-is. This powers registries where
 * construction is expensive or depends on late-bound dependencies.
 *
 * @package Phantom\Core\Registry
 * @since 0.2.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Registry;

/**
 * Registry with lazy factory resolution.
 */
final class DynamicRegistry implements RegistryInterface {

	/**
	 * Raw (eager) entries.
	 *
	 * @var array<string, mixed>
	 */
	private array $entries = array();

	/**
	 * Factories keyed by entry key.
	 *
	 * @var array<string, callable>
	 */
	private array $factories = array();

	/**
	 * Resolved factory results (cached).
	 *
	 * @var array<string, mixed>
	 */
	private array $resolved = array();

	/**
	 * Store an eager entry.
	 *
	 * @param string $key   Entry key.
	 * @param mixed  $value Entry value.
	 * @return void
	 */
	public function set( string $key, mixed $value ): void {
		$this->entries[ $key ] = $value;
		unset( $this->factories[ $key ], $this->resolved[ $key ] );
	}

	/**
	 * Register a lazy factory (resolved once on first get()).
	 *
	 * @param string   $key     Entry key.
	 * @param callable $factory Factory returning the entry value.
	 * @return void
	 */
	public function register( string $key, callable $factory ): void {
		$this->factories[ $key ] = $factory;
		unset( $this->entries[ $key ], $this->resolved[ $key ] );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @param string $key Entry key.
	 */
	public function get( string $key ): mixed {
		if ( array_key_exists( $key, $this->entries ) ) {
			return $this->entries[ $key ];
		}

		if ( ! isset( $this->factories[ $key ] ) ) {
			return null;
		}

		if ( ! array_key_exists( $key, $this->resolved ) ) {
			$this->resolved[ $key ] = call_user_func( $this->factories[ $key ] );
		}

		return $this->resolved[ $key ];
	}

	/**
	 * {@inheritDoc}
	 *
	 * @param string $key Entry key.
	 */
	public function has( string $key ): bool {
		return array_key_exists( $key, $this->entries ) || isset( $this->factories[ $key ] );
	}

	/**
	 * {@inheritDoc}
	 */
	public function all(): array {
		$all = $this->entries;

		foreach ( array_keys( $this->factories ) as $key ) {
			$all[ $key ] = $this->get( $key );
		}

		return $all;
	}
}
