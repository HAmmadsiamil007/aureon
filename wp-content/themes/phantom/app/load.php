<?php
/**
 * Phantom Core — bootstrap entry point.
 *
 * Phase 1 (Bootstrap): this file is the single entry for the framework. It
 * registers the Composer PSR-4 autoloader (ADR-009) and binds the Kernel to
 * `plugins_loaded` at priority 5 — before GP Premium and third-party plugins —
 * and never earlier/later than that (ADR-013).
 *
 * Required once from functions.php when the file exists. Safe to require in
 * WP-free CLI contexts (the WP hook binding is guarded).
 *
 * @package Phantom
 * @since 0.1.0
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

if ( defined( 'PHANTOM_CORE_LOADED' ) ) {
	return;
}

define( 'PHANTOM_CORE_LOADED', true );

$phantom_autoload = dirname( __DIR__ ) . '/vendor/autoload.php';

if ( is_readable( $phantom_autoload ) ) {
	require_once $phantom_autoload;
}

// Bind the Kernel to the plugin-loaded phase at priority 5 (ADR-013).
if ( function_exists( 'add_action' ) ) {
	add_action( 'plugins_loaded', array( \Phantom\Core\Boot\Kernel::class, 'launch' ), 5 );
}
