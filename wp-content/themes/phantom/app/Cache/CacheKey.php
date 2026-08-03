<?php
/**
 * CacheKey — namespaced cache key builder.
 *
 * Phase 2 (Framework Infrastructure): builds collision-safe, versioned keys of
 * the form phantom_{source}_{version}_{key} (ADR-010 tagged keys). All segments
 * are sanitized to [a-z0-9_] so keys survive any backend (transients, memcached).
 *
 * @package Phantom\Core\Cache
 * @since 0.2.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Cache;

/**
 * Namespaced cache key builder.
 */
final class CacheKey {

	/**
	 * Build a namespaced key.
	 *
	 * @param string      $source  Logical source namespace (e.g. tokens, menu).
	 * @param string      $key     Entry key.
	 * @param string|null $version Optional version segment for invalidation.
	 * @return string
	 */
	public static function make( string $source, string $key, ?string $version = null ): string {
		$parts = array( 'phantom', self::sanitize( $source ), self::sanitize( $key ) );

		if ( null !== $version && '' !== $version ) {
			array_splice( $parts, 2, 0, array( self::sanitize( $version ) ) );
		}

		return implode( '_', $parts );
	}

	/**
	 * Sanitize a segment to [a-z0-9_] (lowercased).
	 *
	 * @param string $segment Raw segment.
	 * @return string
	 */
	private static function sanitize( string $segment ): string {
		$segment = strtolower( $segment );
		$segment = preg_replace( '/[^a-z0-9_]+/', '_', $segment );
		$segment = trim( (string) $segment, '_' );

		return '' !== $segment ? $segment : 'none';
	}
}
