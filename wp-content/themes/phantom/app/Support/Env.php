<?php
/**
 * Env — environment detection.
 *
 * Phase 1 (Bootstrap): wraps `wp_get_environment_type()` (WordPress 5.5+) with
 * a config-level override (ADR-011: phantom.env.json `environment.override`).
 * Returns one of: local, development, staging, production.
 *
 * @package Phantom\Core\Support
 * @since 0.1.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Support;

/**
 * Environment detection helpers.
 */
final class Env {

	/**
	 * Detect the current environment.
	 *
	 * Resolution order:
	 *   1. config['environment']['override'] (phantom.env.json)
	 *   2. wp_get_environment_type() when WordPress is loaded
	 *   3. 'production' (safe fallback)
	 *
	 * @param array<string, mixed> $config Loaded config array.
	 * @return string local|development|staging|production
	 */
	public static function detect( array $config = array() ): string {
		$override = $config['environment']['override'] ?? null;

		if ( is_string( $override ) && '' !== $override ) {
			return $override;
		}

		if ( function_exists( 'wp_get_environment_type' ) ) {
			$wp_env = wp_get_environment_type();

			if ( is_string( $wp_env ) && '' !== $wp_env ) {
				return $wp_env;
			}
		}

		return 'production';
	}

	/**
	 * Whether debug mode is enabled.
	 *
	 * Config['debug'] wins when present; otherwise falls back to the
	 * WordPress WP_DEBUG constant (defined in wp-config.php).
	 *
	 * @param array<string, mixed> $config Loaded config array.
	 * @return bool
	 */
	public static function is_debug( array $config = array() ): bool {
		if ( array_key_exists( 'debug', $config ) && is_bool( $config['debug'] ) ) {
			return $config['debug'];
		}

		// constant() is used (not a bare WP_DEBUG reference) so static analysis
		// stays clean when WP is not loaded (wp-config.php constants are not
		// part of the WP stubs).
		return defined( 'WP_DEBUG' ) && true === constant( 'WP_DEBUG' );
	}
}
