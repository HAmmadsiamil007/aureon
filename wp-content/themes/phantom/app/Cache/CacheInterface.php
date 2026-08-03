<?php
/**
 * CacheInterface — cache read/write contract.
 *
 * Phase 2 (Framework Infrastructure): a small cache contract implemented by
 * the WP-backed adapters (ObjectCache, TransientCache). Subsystems depend on
 * this interface so the backing store can be swapped per environment
 * (ADR-010: WP Transients + object-cache abstraction, tagged keys).
 *
 * @package Phantom\Core\Cache
 * @since 0.2.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Cache;

/**
 * Cache store contract.
 */
interface CacheInterface {

	/**
	 * Read a cached value.
	 *
	 * @param string $key      Cache key.
	 * @param mixed  $fallback Fallback when absent/expired.
	 * @return mixed
	 */
	public function get( string $key, mixed $fallback = null ): mixed;

	/**
	 * Store a value.
	 *
	 * @param string $key   Cache key.
	 * @param mixed  $value Value.
	 * @param int    $ttl   Time to live in seconds (0 = never expires).
	 * @return bool
	 */
	public function set( string $key, mixed $value, int $ttl = 0 ): bool;

	/**
	 * Delete a cached value.
	 *
	 * @param string $key Cache key.
	 * @return bool
	 */
	public function delete( string $key ): bool;

	/**
	 * Flush the whole store.
	 *
	 * @return bool
	 */
	public function flush(): bool;
}
