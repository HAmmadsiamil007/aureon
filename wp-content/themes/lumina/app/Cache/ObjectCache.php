<?php
/**
 * ObjectCache — wp_cache_* object-cache adapter.
 *
 * Phase 2 (Framework Infrastructure): thin, guarded wrapper around the WP
 * object cache (wp_cache_get/set/delete, wp_cache_flush). Without WordPress
 * loaded, reads return the default and writes report failure (no fatal), which
 * keeps CLI smoke suites safe. ADR-010: object-cache abstraction.
 *
 * @package Lumina\Core\Cache
 * @since 0.2.0
 */

declare( strict_types=1 );

namespace Lumina\Core\Cache;

/**
 * WordPress object-cache adapter.
 */
final class ObjectCache implements CacheInterface {

	/**
	 * Cache group used for all Lumina entries.
	 *
	 * @var string
	 */
	private string $group;

	/**
	 * Constructor.
	 *
	 * @param string $group Cache group (defaults to 'lumina').
	 */
	public function __construct( string $group = 'lumina' ) {
		$this->group = $group;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @param string $key      Cache key.
	 * @param mixed  $fallback Fallback when absent.
	 */
	public function get( string $key, mixed $fallback = null ): mixed {
		if ( ! function_exists( 'wp_cache_get' ) ) {
			return $fallback;
		}

		$value = wp_cache_get( $key, $this->group );

		return false === $value ? $fallback : $value;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @param string $key   Cache key.
	 * @param mixed  $value Value.
	 * @param int    $ttl   TTL seconds (0 = never expires).
	 */
	public function set( string $key, mixed $value, int $ttl = 0 ): bool {
		if ( ! function_exists( 'wp_cache_set' ) ) {
			return false;
		}

		return wp_cache_set( $key, $value, $this->group, $ttl );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @param string $key Cache key.
	 */
	public function delete( string $key ): bool {
		if ( ! function_exists( 'wp_cache_delete' ) ) {
			return false;
		}

		return wp_cache_delete( $key, $this->group );
	}

	/**
	 * {@inheritDoc}
	 */
	public function flush(): bool {
		if ( ! function_exists( 'wp_cache_flush' ) ) {
			return false;
		}

		return wp_cache_flush();
	}
}
