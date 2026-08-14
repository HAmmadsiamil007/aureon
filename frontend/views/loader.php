<?php
/**
 * Frontend engine loader — wires renderer, registry, adapters, sections.
 *
 * Loaded once by the theme. All frontend assets resolve relative to this
 * file so the engine is self-contained and path-safe.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Path to the frontend engine root (…/frontend).
 */
if ( ! defined( 'AETHER_FRONTEND_DIR' ) ) {
	define( 'AETHER_FRONTEND_DIR', trailingslashit( dirname( __DIR__ ) ) );
}

/**
 * Boot the frontend engine: includes, section registry, and entry points.
 *
 * Hooked to after_setup_theme so theme internals (get_theme_mod, options)
 * are available to adapters at render time.
 */
function aether_frontend_boot() {
	require_once AETHER_FRONTEND_DIR . 'tokens/tokens.php';
	require_once AETHER_FRONTEND_DIR . 'views/design.php';
	require_once AETHER_FRONTEND_DIR . 'views/registry.php';
	require_once AETHER_FRONTEND_DIR . 'views/renderer.php';
	require_once AETHER_FRONTEND_DIR . 'views/viewmodel.php';
	require_once AETHER_FRONTEND_DIR . 'views/composer.php';

	// Adapters — the only layer allowed to touch WP/WC.
	$adapter_files = glob( AETHER_FRONTEND_DIR . 'adapters/*.php' );
	if ( is_array( $adapter_files ) ) {
		foreach ( $adapter_files as $adapter_file ) {
			require_once $adapter_file;
		}
	}

	// Sections — self-register via aether_register_section().
	$section_files = glob( AETHER_FRONTEND_DIR . 'sections/*.php' );
	if ( is_array( $section_files ) ) {
		foreach ( $section_files as $section_file ) {
			require_once $section_file;
		}
	}

	// Pack sections — active design's extra/override sections self-register too.
	$design_dir = aether_active_design_dir();
	if ( $design_dir ) {
		$pack_sections = glob( $design_dir . 'sections/*.php' );
		if ( is_array( $pack_sections ) ) {
			foreach ( $pack_sections as $pack_section ) {
				require_once $pack_section;
			}
		}
	}
}
