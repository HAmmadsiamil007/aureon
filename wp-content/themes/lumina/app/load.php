<?php
/**
 * Lumina Core — bootstrap entry point.
 *
 * Phase 1 (Bootstrap): this file is the single entry for the framework. It
 * registers the Composer PSR-4 autoloader (ADR-009) and binds the Kernel to
 * `plugins_loaded` at priority 5 — before third-party plugins —
 * and never earlier/later than that (ADR-013).
 *
 * Required once from functions.php when the file exists. Safe to require in
 * WP-free CLI contexts (the WP hook binding is guarded).
 *
 * @package Lumina
 * @since 0.1.0
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

if ( defined( 'LUMINA_CORE_LOADED' ) ) {
	return;
}

define( 'LUMINA_CORE_LOADED', true );

$lumina_autoload = dirname( __DIR__ ) . '/vendor/autoload.php';

if ( is_readable( $lumina_autoload ) ) {
	require_once $lumina_autoload;
}

// Self-contained PSR-4 fallback autoloader (ADR-009, Phase 16.5).
// Distributions do NOT ship vendor/ (dev tooling only). When Composer's
// autoloader is absent this registers a minimal `Lumina\Core\` → `app/`
// mapping so the theme runs standalone on a fresh install. Registered only
// when the Kernel is not already loadable.
if ( ! class_exists( \Lumina\Core\Boot\Kernel::class, false ) ) {
	spl_autoload_register(
		static function ( string $lumina_class ): void {
			$lumina_prefix = 'Lumina\\Core\\';

			if ( ! str_starts_with( $lumina_class, $lumina_prefix ) ) {
				return;
			}

			$lumina_relative = substr( $lumina_class, strlen( $lumina_prefix ) );
			$lumina_file     = __DIR__ . '/' . str_replace( '\\', '/', $lumina_relative ) . '.php';

			if ( is_readable( $lumina_file ) ) {
				require_once $lumina_file;
			}
		}
	);
}

// Bind the Kernel to the plugin-loaded phase at priority 5 (ADR-013).
if ( function_exists( 'add_action' ) ) {
	add_action( 'plugins_loaded', array( \Lumina\Core\Boot\Kernel::class, 'launch' ), 5 );
}
