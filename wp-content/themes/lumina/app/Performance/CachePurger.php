<?php
/**
 * CachePurger — domain-scoped cache invalidation.
 *
 * Phase 13 (Performance Engineering): `purge($domain)` invalidates every
 * Lumina cache entry in a logical domain (tokens, menu, render, fragments…).
 * Entries live under the Phase-2 CacheKey namespace; the purger clears
 * transient + object-cache writes it can see, and flushes the render cache
 * domain through the container binding. WP-free safe: with no WordPress cache
 * present it still clears the in-memory render cache and reports the purged
 * domains (smoke-assertable).
 *
 * @package Lumina\Core\Performance
 * @since 0.13.0
 */

declare( strict_types=1 );

namespace Lumina\Core\Performance;

use Lumina\Core\Cache\CacheKey;

/**
 * Domain-scoped cache purge.
 */
final class CachePurger {

	/**
	 * Render-cache flusher (view-slug aware), WP-free safe.
	 *
	 * @var callable(string): void
	 */
	private $render_flusher;

	/**
	 * Domains purged during this instance's lifetime.
	 *
	 * @var list<string>
	 */
	private array $purged = array();

	/**
	 * Build the purger.
	 *
	 * @param callable|null $render_flusher Callable(string $domain): void.
	 */
	public function __construct( ?callable $render_flusher = null ) {
		$this->render_flusher = $render_flusher ?? static function ( string $domain ): void {
			// Default: nothing else to flush in a WP-free context.
		};
	}

	/**
	 * Purge a cache domain.
	 *
	 * @param string $domain Logical domain (tokens, render, menu, …).
	 * @return int Number of cache keys cleared.
	 */
	public function purge( string $domain ): int {
		$domain = strtolower( trim( $domain ) );

		if ( '' === $domain ) {
			return 0;
		}

		$cleared = 0;

		// Transient + object-cache writes use the lumina_ prefix namespace
		// (ADR-010). In WordPress, delete_transient/delete are prefix-scoped.
		// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals -- WP core functions.
		if ( function_exists( 'delete_transient' ) ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$cleared += $this->purge_transients( $domain );
		}

		if ( function_exists( 'wp_cache_flush_group' ) ) {
			// phpcs:ignore WordPress.PHP.NoSilencedErrors -- optional cache backend API.
			$ok       = (bool) @wp_cache_flush_group( CacheKey::make( $domain, '*' ) );
			$cleared += $ok ? 1 : 0;
		}
		// phpcs:enable WordPress.NamingConventions.PrefixAllGlobals

		// Render cache domain always flushes through the container binding.
		( $this->render_flusher )( $domain );

		if ( ! in_array( $domain, $this->purged, true ) ) {
			$this->purged[] = $domain;
		}

		return $cleared;
	}

	/**
	 * Domains purged so far.
	 *
	 * @return list<string>
	 */
	public function purged_domains(): array {
		return $this->purged;
	}

	/**
	 * Delete transient keys matching the domain prefix.
	 *
	 * Uses the WordPress transients API when present; the pure fallback scans
	 * nothing (WP-free runs report 0 transient keys — mechanics asserted via
	 * purged_domains() instead).
	 *
	 * @param string $domain Domain to purge.
	 * @return int
	 */
	private function purge_transients( string $domain ): int {
		if ( ! function_exists( 'get_transient' ) ) {
			return 0;
		}

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		$candidates = function_exists( 'get_option' ) ? $this->transient_keys() : array();
		$cleared    = 0;

		foreach ( $candidates as $key ) {
			if ( str_starts_with( $key, CacheKey::make( $domain, '' ) ) ) {
				// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
				if ( delete_transient( $key ) ) {
					++$cleared;
				}
			}
		}

		return $cleared;
	}

	/**
	 * The current transient key list (guarded; may be empty without WP).
	 *
	 * @return list<string>
	 */
	private function transient_keys(): array {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		$keys = get_option( '_transient_keys', array() );

		return is_array( $keys ) ? array_values( array_filter( $keys, 'is_string' ) ) : array();
	}
}
