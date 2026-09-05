<?php
/**
 * Design pack asset pipeline (M7).
 *
 * For non-luxury designs, this function replaces the bridge's Luxury-only
 * enqueue: platform CDNs + platform contract JS (motion watchdog, AJAX
 * contract, countdown) load for every design; the pack's own CSS/JS come
 * from its manifest.json (`assets.css` / `assets.js`). Luxury assets
 * (style.css, motion.css, ... main.js-dependent design css) are NOT
 * enqueued, so the two design systems never coexist — isolation by
 * construction, verified by tests/specs/design-isolation.spec.js.
 *
 * Asset entry formats (relative to the pack dir unless base:true):
 *   "css/style.css"
 *   { "file": "js/lumen.js", "deps": ["aether-main"], "base": false }
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Enqueue platform + pack assets for the active (non-luxury) design.
 */
function aether_design_enqueue_assets() {
	$design = aether_active_design();
	$dir    = aether_active_design_dir();

	if ( ! $dir ) {
		return; // Luxury handled by the theme bridge (aureon_aether_enqueue_assets).
	}

	$base_uri = trailingslashit( content_url() ) . 'frontend/assets';
	$base_dir = trailingslashit( WP_CONTENT_DIR ) . 'frontend/assets';
	$pack_uri = trailingslashit( content_url() ) . 'frontend/designs/' . $design;
	$pack_dir = trailingslashit( WP_CONTENT_DIR ) . 'frontend/designs/' . $design;

	// --- Platform CDNs (same handles as the bridge, so manifest deps resolve) ---
	wp_enqueue_style(
		'aether-bootstrap',
		'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
		array(),
		'5.3.3'
	);
	wp_enqueue_style(
		'aether-fontawesome',
		'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
		array(),
		'6.5.1'
	);
	wp_enqueue_style(
		'aether-swiper',
		'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css',
		array(),
		'11'
	);
	wp_enqueue_script(
		'aether-bootstrap-js',
		'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js',
		array(),
		'5.3.3',
		true
	);
	wp_enqueue_script( 'aether-swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', array(), '11', true );
	wp_enqueue_script( 'aether-gsap', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js', array(), '3.12.5', true );
	wp_enqueue_script( 'aether-scrolltrigger', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js', array( 'aether-gsap' ), '3.12.5', true );

	// --- Platform contract JS (base files, same handles as the bridge) ---
	wp_enqueue_script( 'aether-animations', $base_uri . '/js/animations.js', array( 'aether-bootstrap-js', 'aether-gsap' ), filemtime( $base_dir . '/js/animations.js' ), true );
	wp_enqueue_script( 'aether-main', $base_uri . '/js/main.js', array( 'aether-animations' ), filemtime( $base_dir . '/js/main.js' ), true );
	wp_enqueue_script( 'aether-countdown', $base_uri . '/js/countdown.js', array(), filemtime( $base_dir . '/js/countdown.js' ), true );

	// AJAX + REST context (identical payload to the bridge's localize).
	wp_localize_script(
		'aether-main',
		'aetherAjax',
		array(
			'ajaxUrl'        => admin_url( 'admin-ajax.php' ),
			'nonce'          => wp_create_nonce( 'aether_nonce' ),
			'restUrl'        => esc_url_raw( rest_url( 'aether/v1/' ) ),
			'isUserLoggedIn' => is_user_logged_in(),
			'shopUrl'        => function_exists( 'wc_get_page_permalink' ) && wc_get_page_permalink( 'shop' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' ),
			'searchUrl'      => home_url( '/?s=' ),
			'wcAjaxUrl'      => function_exists( 'WC' ) ? add_query_arg( 'wc-ajax', 'add_to_cart', home_url( '/' ) ) : '',
		)
	);

	// --- Pack assets (manifest.json) ---
	$manifest = aether_design_manifest();

	foreach ( (array) ( isset( $manifest['assets']['css'] ) ? $manifest['assets']['css'] : array() ) as $entry ) {
		aether_enqueue_pack_asset( $entry, 'css', $base_uri, $base_dir, $pack_uri, $pack_dir );
	}

	foreach ( (array) ( isset( $manifest['assets']['js'] ) ? $manifest['assets']['js'] : array() ) as $entry ) {
		aether_enqueue_pack_asset( $entry, 'js', $base_uri, $base_dir, $pack_uri, $pack_dir );
	}
}
add_action( 'wp_enqueue_scripts', 'aether_design_enqueue_assets', 20 );

/**
 * Enqueue one manifest asset entry.
 *
 * @param string|array $entry    Manifest entry (string file or {file,deps,base}).
 * @param string       $kind     'css'|'js'.
 * @param string       $base_uri Base assets URL.
 * @param string       $base_dir Base assets dir.
 * @param string       $pack_uri Pack URL.
 * @param string       $pack_dir Pack dir.
 */
function aether_enqueue_pack_asset( $entry, $kind, $base_uri, $base_dir, $pack_uri, $pack_dir ) {
	if ( is_string( $entry ) ) {
		$entry = array( 'file' => $entry );
	}

	if ( ! is_array( $entry ) || empty( $entry['file'] ) || ! is_string( $entry['file'] ) ) {
		return;
	}

	$file    = ltrim( $entry['file'], '/' );
	$from_base = ! empty( $entry['base'] );
	$src_uri = $from_base ? trailingslashit( $base_uri ) . $file : trailingslashit( $pack_uri ) . $file;
	$src_dir = $from_base ? trailingslashit( $base_dir ) . $file : trailingslashit( $pack_dir ) . $file;
	$deps    = isset( $entry['deps'] ) && is_array( $entry['deps'] ) ? array_map( 'sanitize_key', $entry['deps'] ) : array();

	if ( ! file_exists( $src_dir ) ) {
		return;
	}

	$version = filemtime( $src_dir );
	$handle  = 'aether-pack-' . $kind . '-' . sanitize_key( preg_replace( '/\.[a-z0-9]+$/i', '', basename( $file ) ) );

	if ( 'css' === $kind ) {
		wp_enqueue_style( $handle, $src_uri, $deps, $version );
	} else {
		wp_enqueue_script( $handle, $src_uri, $deps, $version, true );
	}
}