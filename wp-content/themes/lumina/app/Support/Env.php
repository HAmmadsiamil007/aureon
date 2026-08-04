<?php
/**
 * Env — environment detection.
 *
 * Phase 1 (Bootstrap): wraps `wp_get_environment_type()` (WordPress 5.5+) with
 * a config-level override (ADR-011: lumina.env.json `environment.override`).
 * Returns one of: local, development, staging, production.
 *
 * @package Lumina\Core\Support
 * @since 0.1.0
 */

declare( strict_types=1 );

namespace Lumina\Core\Support;

/**
 * Environment detection helpers.
 */
final class Env {

	/**
	 * Allowed environment values (mirrors wp_get_environment_type()).
	 *
	 * @var string[]
	 */
	private const VALID_ENVIRONMENTS = array(
		'local',
		'development',
		'staging',
		'production',
	);

	/**
	 * Detect the current environment.
	 *
	 * Resolution order:
	 *   1. config['environment']['override'] (lumina.env.json)
	 *   2. wp_get_environment_type() when WordPress is loaded
	 *   3. 'production' (safe fallback)
	 *
	 * The override is validated against the allowed set; unknown values fall
	 * through to the next source so a typo can never produce a bogus env.
	 *
	 * @param array<string, mixed> $config Loaded config array.
	 * @return string local|development|staging|production
	 */
	public static function detect( array $config = array() ): string {
		$override = $config['environment']['override'] ?? null;

		if ( is_string( $override ) && in_array( $override, self::VALID_ENVIRONMENTS, true ) ) {
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
