<?php
/**
 * TransientCache — WP Transients adapter.
 *
 * Phase 2 (Framework Infrastructure): thin, guarded wrapper around
 * get_transient/set_transient/delete_transient (ADR-010). Transients persist
 * to the database/object cache and expire by design. Without WordPress loaded,
 * reads return the default and writes report failure (no fatal), keeping CLI
 * smoke suites safe.
 *
 * @package Phantom\Core\Cache
 * @since 0.2.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Cache;

/**
 * WordPress Transients adapter.
 */
final class TransientCache implements CacheInterface {

	/**
	 * Optional key prefix (defaults to 'phantom').
	 *
	 * @var string
	 */
	private string $prefix;

	/**
	 * Keys written by this instance (for flush()).
	 *
	 * @var string[]
	 */
	private array $tracked = array();

	/**
	 * Constructor.
	 *
	 * @param string $prefix Transient name prefix.
	 */
	public function __construct( string $prefix = 'phantom' ) {
		$this->prefix = $prefix;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @param string $key      Cache key.
	 * @param mixed  $fallback Fallback when absent.
	 */
	public function get( string $key, mixed $fallback = null ): mixed {
		if ( ! function_exists( 'get_transient' ) ) {
			return $fallback;
		}

		$value = get_transient( $this->name( $key ) );

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
		if ( ! function_exists( 'set_transient' ) ) {
			return false;
		}

		$ok = set_transient( $this->name( $key ), $value, $ttl );

		if ( $ok ) {
			$this->tracked[] = $key;
		}

		return $ok;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @param string $key Cache key.
	 */
	public function delete( string $key ): bool {
		if ( ! function_exists( 'delete_transient' ) ) {
			return false;
		}

		return delete_transient( $this->name( $key ) );
	}

	/**
	 * {@inheritDoc}
	 *
	 * Transients have no global enumeration primitive, so flush() deletes every
	 * key this instance wrote during the current process. Backing caches are
	 * purged naturally via their own TTL expiry.
	 */
	public function flush(): bool {
		if ( ! function_exists( 'delete_transient' ) ) {
			return false;
		}

		$cleared = true;

		foreach ( $this->tracked as $key ) {
			$cleared = delete_transient( $this->name( $key ) ) && $cleared;
		}

		$this->tracked = array();

		return $cleared;
	}

	/**
	 * Transient name (prefixed; WP caps names at 172 chars).
	 *
	 * @param string $key Cache key.
	 * @return string
	 */
	private function name( string $key ): string {
		return $this->prefix . '_' . $key;
	}
}
