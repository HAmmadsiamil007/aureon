<?php
/**
 * HealthCheck — plugin presence + version-floor checks.
 *
 * Phase 8 (Plugin Bridges): answers "is this plugin active?" and "what
 * version?" through the public WordPress API (`is_plugin_active()`,
 * `get_plugin_data()`) when present, falling back to inert answers in WP-free
 * contexts. `passes()` applies a minimum-version floor; every call is
 * capability-guarded and never throws (plan §Phase 8 acceptance).
 *
 * @package Phantom\Core\Bridges
 * @since 0.8.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Bridges;

/**
 * Plugin health checks.
 */
class HealthCheck {

	/**
	 * Whether a plugin is active (by plugin file path).
	 *
	 * @param string $plugin_file Plugin file path relative to wp-content/plugins.
	 * @return bool
	 */
	public function active( string $plugin_file ): bool {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		if ( function_exists( 'is_plugin_active' ) ) {
			return is_plugin_active( $plugin_file );
		}

		return false;
	}

	/**
	 * The installed plugin version ('' when unavailable).
	 *
	 * @param string $plugin_file Plugin file path.
	 * @return string
	 */
	public function version( string $plugin_file ): string {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core functions.
		if ( ! function_exists( 'get_plugin_data' ) || ! function_exists( 'get_plugins' ) ) {
			return '';
		}

		$plugins = get_plugins();

		if ( isset( $plugins[ $plugin_file ] ) && isset( $plugins[ $plugin_file ]['Version'] ) ) {
			return (string) $plugins[ $plugin_file ]['Version'];
		}

		return '';
	}

	/**
	 * Whether the active plugin meets a minimum version floor.
	 *
	 * An inactive plugin never passes; an unknown version fails unless the
	 * floor is empty.
	 *
	 * @param string $plugin_file  Plugin file path.
	 * @param string $min_version  Minimum version ('' = any active version).
	 * @return bool
	 */
	public function passes( string $plugin_file, string $min_version ): bool {
		if ( ! $this->active( $plugin_file ) ) {
			return false;
		}

		if ( '' === $min_version ) {
			return true;
		}

		$installed = $this->version( $plugin_file );

		return '' !== $installed && version_compare( $installed, $min_version, '>=' );
	}
}
