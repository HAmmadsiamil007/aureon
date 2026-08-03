<?php
/**
 * Repository — immutable configuration repository.
 *
 * Phase 2 (Framework Infrastructure): wraps the Phase-1 immutable config array
 * with dot-notation access (log.level, features.phantom_core), all() for bulk
 * reads, and an explicit mutable flag for programmatic override. The Kernel
 * registers one instance at boot; App::get() routes through it, giving later
 * subsystems a single cached read path (plan §Phase 2 acceptance).
 *
 * @package Phantom\Core\Config
 * @since 0.2.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Config;

/**
 * Dot-notation configuration repository.
 */
final class Repository {

	/**
	 * Configuration data.
	 *
	 * @var array<string, mixed>
	 */
	private array $data;

	/**
	 * Whether set() is permitted (default: immutable).
	 *
	 * @var bool
	 */
	private bool $mutable;

	/**
	 * Constructor.
	 *
	 * @param array<string, mixed> $config   Configuration data.
	 * @param bool                 $mutable  Permit set() writes.
	 */
	public function __construct( array $config = array(), bool $mutable = false ) {
		$this->data    = $config;
		$this->mutable = $mutable;
	}

	/**
	 * Read a key (dot notation supported).
	 *
	 * @param string $key      Key, e.g. "log.level" or "features.phantom_core".
	 * @param mixed  $fallback Fallback when the key is absent.
	 * @return mixed
	 */
	public function get( string $key, mixed $fallback = null ): mixed {
		$current = $this->data;

		foreach ( explode( '.', $key ) as $segment ) {
			if ( is_array( $current ) && array_key_exists( $segment, $current ) ) {
				$current = $current[ $segment ];
				continue;
			}

			return $fallback;
		}

		return $current;
	}

	/**
	 * Write a key (dot notation supported); immutable unless constructed mutable.
	 *
	 * Implemented as an immutable rebuild (no by-reference property writes) so
	 * static analysis can fully trace the data flow.
	 *
	 * @param string $key   Key, e.g. "log.level".
	 * @param mixed  $value Value to store.
	 * @return void
	 * @throws \LogicException When the repository is immutable.
	 */
	public function set( string $key, mixed $value ): void {
		if ( ! $this->mutable ) {
			throw new \LogicException(
				'Config\\Repository is immutable; overrides belong in phantom.env.json.'
			);
		}

		$this->data = self::set_at( $this->data, explode( '.', $key ), $value );
	}

	/**
	 * Rebuild a config array with a nested key set (pure, recursive).
	 *
	 * @param array<string, mixed> $data     Config array to rebuild.
	 * @param string[]             $segments Remaining path segments.
	 * @param mixed                $value    Value to store at the leaf.
	 * @return array<string, mixed>
	 */
	private static function set_at( array $data, array $segments, mixed $value ): array {
		$segment = array_shift( $segments );

		if ( array() === $segments ) {
			$data[ $segment ] = $value;

			return $data;
		}

		$data[ $segment ] = self::set_at(
			(array) ( $data[ $segment ] ?? array() ),
			$segments,
			$value
		);

		return $data;
	}

	/**
	 * The full configuration array.
	 *
	 * @return array<string, mixed>
	 */
	public function all(): array {
		return $this->data;
	}
}
