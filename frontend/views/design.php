<?php
/**
 * Design pack resolution — the presentation-layer envelope.
 *
 * The engine tree (frontend/) IS the default 'luxury' design. Alternative
 * client designs live in frontend/designs/<slug>/ and resolve pack-first:
 * a file that exists in the active pack shadows the base file; everything
 * else falls back to the engine defaults. A pack may:
 *
 *   - shadow section templates (sections/section-*.php)
 *   - shadow component templates (components/**)
 *   - register extra sections (its sections/*.php self-register on boot)
 *   - supply token defaults (tokens.php -> aureon_option_defaults, priority 20)
 *   - ship assets/ (enqueued by the bridge; pack descriptor is M6+)
 *
 * Packs never touch adapters, the manifest, or other kernel files.
 * See docs/frontend-platform/MASTER_FRONTEND_REPLACEMENT_PLAN.md §3–§10.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Active design slug.
 *
 * Resolution order: AETHER_DESIGN constant > 'aether_active_design' option
 * > 'luxury' (the engine tree itself).
 *
 * @return string
 */
function aether_active_design() {
	static $design = null;

	if ( null !== $design ) {
		return $design;
	}

	if ( defined( 'AETHER_DESIGN' ) && AETHER_DESIGN ) {
		$design = sanitize_key( (string) AETHER_DESIGN );
	} else {
		$stored = get_option( 'aether_active_design', '' );
		$design = sanitize_key( (string) $stored );
	}

	// Resolve the default BEFORE caching: a truthy slug is cached as-is,
	// an empty one must fall back to 'luxury' on every call — caching the
	// raw value would make later calls in the same request return ''.
	$design = $design ? $design : 'luxury';

	return $design;
}

/**
 * Directory of the active design pack (trailing slash), or '' when the
 * engine tree itself is the active design ('luxury').
 *
 * @return string
 */
function aether_active_design_dir() {
	$design = aether_active_design();

	if ( 'luxury' === $design ) {
		return '';
	}

	$dir = AETHER_FRONTEND_DIR . 'designs/' . $design . '/';

	return is_dir( $dir ) ? $dir : '';
}

/**
 * URL of the active design pack (trailing slash), or '' when the
 * engine tree itself is the active design ('luxury').
 *
 * @return string
 */
function aether_pack_url() {
	$design = aether_active_design();

	if ( 'luxury' === $design ) {
		return '';
	}

	return trailingslashit( content_url() ) . 'frontend/designs/' . $design . '/';
}

/**
 * Resolve a relative engine path pack-first.
 *
 * @param string $relative_path Path relative to AETHER_FRONTEND_DIR (e.g. 'sections/section-hero.php').
 * @return string Absolute path to the pack file when present, else the base file.
 */
function aether_resolve_design_path( $relative_path ) {
	$relative_path = ltrim( (string) $relative_path, '/' );

	$design_dir = aether_active_design_dir();
	if ( $design_dir && file_exists( $design_dir . $relative_path ) ) {
		return $design_dir . $relative_path;
	}

	return AETHER_FRONTEND_DIR . $relative_path;
}

/**
 * Design pack token defaults.
 *
 * A pack may ship tokens.php returning an array of option defaults. They
 * merge onto the settings bucket at priority 20 — after the engine defaults
 * (10). Saved Customizer values always win because defaults only apply when
 * an option is unset.
 *
 * @return array
 */
function aether_design_defaults() {
	$defaults = array();

	$design_dir = aether_active_design_dir();
	if ( $design_dir && file_exists( $design_dir . 'tokens.php' ) ) {
		$included = include $design_dir . 'tokens.php';
		if ( is_array( $included ) ) {
			$defaults = $included;
		}
	}

	return (array) apply_filters( 'aether_design_defaults', $defaults );
}

add_filter( 'aureon_option_defaults', 'aether_apply_design_defaults', 20 );
/**
 * Merge design pack defaults onto the settings bucket defaults.
 *
 * @param array $defaults Engine defaults.
 * @return array Merged defaults.
 */
function aether_apply_design_defaults( $defaults ) {
	return array_merge( $defaults, aether_design_defaults() );
}

add_filter( 'body_class', 'aether_design_body_class' );
/**
 * Tag the frontend body with the active design (design-<slug>).
 *
 * Gives pack CSS a stable scoping hook and lets pack JS feature-detect.
 *
 * @param array $classes Body classes.
 * @return array
 */
function aether_design_body_class( $classes ) {
	$classes[] = 'design-' . aether_active_design();

	return $classes;
}

/**
 * Whether the active design uses complete-page mode.
 *
 * Complete-page designs serve frozen HTML directly and must NOT load
 * AUREON platform presentation assets (Bootstrap, Swiper, GSAP, etc.).
 * The flag is set in the design pack's manifest.json.
 *
 * @return bool
 */
function aether_is_complete_page_design() {
	$manifest = aether_design_manifest();
	return ! empty( $manifest['complete_page'] );
}

/**
 * Active design pack manifest (machine-readable mapping, M6).
 *
 * Reads manifest.json from the active pack directory. Malformed or missing
 * manifests degrade to an empty array — never a fatal error. Keys are
 * whitelisted by aether_sanitize_design_manifest().
 *
 * Schema: docs/frontend-platform/MAPPING_MANIFEST_SCHEMA.md
 *
 * @return array
 */
function aether_design_manifest() {
	static $manifest = null;

	if ( null !== $manifest ) {
		return $manifest;
	}

	$manifest = array();

	$design_dir = aether_active_design_dir();
	if ( ! $design_dir ) {
		return $manifest;
	}

	$manifest_file = $design_dir . 'manifest.json';
	if ( ! file_exists( $manifest_file ) ) {
		return $manifest;
	}

	$json = json_decode( (string) file_get_contents( $manifest_file ), true );
	if ( is_array( $json ) ) {
		$manifest = aether_sanitize_design_manifest( $json );
	}

	return $manifest;
}

/**
 * Whitelist and normalize manifest keys.
 *
 * @param array $m Raw manifest array.
 * @return array Sanitized manifest.
 */
function aether_sanitize_design_manifest( $m ) {
	$allowed = array( 'id', 'label', 'version', 'assets', 'components', 'sections', 'mappings', 'data', 'integrations', 'js', 'customizer', 'pages', 'complete_page' );

	$m = array_intersect_key( (array) $m, array_flip( $allowed ) );

	if ( isset( $m['assets'] ) && is_array( $m['assets'] ) ) {
		foreach ( array( 'css', 'js' ) as $kind ) {
			$m['assets'][ $kind ] = isset( $m['assets'][ $kind ] ) && is_array( $m['assets'][ $kind ] ) ? array_values( $m['assets'][ $kind ] ) : array();
		}
	}

	return $m;
}