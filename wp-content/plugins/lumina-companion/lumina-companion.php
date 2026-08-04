<?php
/**
 * Plugin Name:       Lumina Companion
 * Plugin URI:        https://github.com/luminatheme/lumina-companion
 * Description:       Original companion plugin for the Lumina theme — spacing, typography, page header, secondary navigation, menu plus, sections, and WooCommerce styling. 100% original code; works only with the Lumina theme.
 * Version:           1.0.0
 * Requires at least: 6.5
 * Requires PHP:      8.2
 * Author:            Lumina Studio
 * Author URI:        https://github.com/luminatheme
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       lumina-companion
 *
 * Lumina Companion is an original, independent implementation. It does not
 * include, copy, or derive from any third-party commercial product.
 *
 * @package Lumina\Companion
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

define( 'LUMINA_COMPANION_VERSION', '1.0.0' );
define( 'LUMINA_COMPANION_FILE', __FILE__ );

// WP-free guard: plugin_dir_path()/plugin_dir_url() exist only inside
// WordPress. Default to filesystem/URL derivations so the WP-free smoke
// suite can load the plugin in isolation (CLI/CI).
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals -- WP core functions.
define(
	'LUMINA_COMPANION_DIR',
	function_exists( 'plugin_dir_path' )
		? plugin_dir_path( __FILE__ )
		: rtrim( __DIR__, '/\\' ) . '/'
);
define(
	'LUMINA_COMPANION_URL',
	function_exists( 'plugin_dir_url' ) ? plugin_dir_url( __FILE__ ) : ''
);
// phpcs:enable WordPress.NamingConventions.PrefixAllGlobals

/**
 * Register the PSR-4 autoloader for Lumina\Companion\ → src/.
 *
 * Falls back to a small spl_autoload implementation so the plugin works even
 * when Composer dependencies have not been installed (no runtime deps).
 *
 * @return void
 */
function lumina_companion_autoload(): void {
	$prefix = 'Lumina\\Companion\\';
	$base   = LUMINA_COMPANION_DIR . 'src/';

	spl_autoload_register(
		static function ( string $class_name ) use ( $prefix, $base ): void {
			if ( 0 !== strpos( $class_name, $prefix ) ) {
				return;
			}

			$relative = substr( $class_name, strlen( $prefix ) );
			$file     = $base . str_replace( '\\', '/', $relative ) . '.php';

			if ( is_readable( $file ) ) {
				// phpcs:ignore WordPress.PHP.IncludeThemes -- plugin autoload.
				require_once $file;
			}
		}
	);
}

lumina_companion_autoload();

/**
 * Boot the companion plugin (guarded: only when the Lumina theme is active).
 *
 * @return void
 */
function lumina_companion_boot(): void {
	if ( ! class_exists( \Lumina\Companion\Plugin::class ) ) {
		return;
	}

	\Lumina\Companion\Plugin::instance()->boot();
}

// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
if ( function_exists( 'add_action' ) ) {
	add_action( 'plugins_loaded', 'lumina_companion_boot', 20 );
}
