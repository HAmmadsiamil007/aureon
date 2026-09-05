<?php
/**
 * AETHER Security Headers.
 *
 * Adds hardened HTTP security headers on front-end pages.
 * All headers are filterable for child themes and plugins.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'send_headers', 'aether_send_security_headers', 1 );
/**
 * Send security headers early so they are guaranteed to be sent.
 */
function aether_send_security_headers() {
	if ( is_admin() ) {
		return;
	}

	// Skip if headers already sent (standalone templates output HTML early).
	if ( headers_sent() ) {
		return;
	}

	// X-Content-Type-Options — prevent MIME sniffing.
	header( 'X-Content-Type-Options: nosniff' );

	// X-Frame-Options — allow the Customizer preview iframe; block elsewhere.
	if ( ! is_customize_preview() ) {
		header( 'X-Frame-Options: SAMEORIGIN' );
	}

	// Referrer-Policy — keep referrers on the same origin only.
	header( 'Referrer-Policy: strict-origin-when-cross-origin' );

	// Permissions-Policy — deny sensitive browser APIs by default.
	$permissions = array(
		'camera'          => '()',
		'microphone'      => '()',
		'geolocation'     => '()',
		'interest-cohort' => '()',
	);
	$permissions = apply_filters( 'aether_permissions_policy', $permissions );
	$header_value = array();
	foreach ( $permissions as $feature => $value ) {
		$header_value[] = $feature . '=' . $value;
	}
	header( 'Permissions-Policy: ' . implode( ', ', $header_value ) );

	// Content-Security-Policy (report-only by default; enforce via constant).
	aether_send_csp_header();
}

/**
 * Build and send the Content-Security-Policy header.
 *
 * Nonce-based for inline scripts; known CDNs are allowlisted. Report-only by
 * default — define AETHER_CSP_STRICT = true in wp-config.php to enforce.
 */
function aether_send_csp_header() {
	$nonce = aether_get_csp_nonce();

	$directives = array(
		'default-src'     => "'self'",
		'script-src'      => "'self' 'nonce-{$nonce}' 'strict-dynamic' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://unpkg.com https://www.google-analytics.com https://www.googletagmanager.com",
		'style-src'       => "'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://unpkg.com",
		'img-src'         => "'self' data: https: blob:",
		'font-src'        => "'self' data: https://cdnjs.cloudflare.com",
		'connect-src'     => "'self' https://www.google-analytics.com https://analytics.google.com",
		'frame-src'       => "'self' https://www.youtube.com https://player.vimeo.com",
		'object-src'      => "'none'",
		'base-uri'        => "'self'",
		'form-action'     => "'self'",
		'frame-ancestors' => "'self'",
	);

	$directives = apply_filters( 'aether_csp_directives', $directives );

	$header = array();
	foreach ( $directives as $directive => $value ) {
		$header[] = $directive . ' ' . $value;
	}

	$csp_value = implode( '; ', $header );

	if ( defined( 'AETHER_CSP_STRICT' ) && AETHER_CSP_STRICT ) {
		header( "Content-Security-Policy: {$csp_value}" );
	} else {
		header( "Content-Security-Policy-Report-Only: {$csp_value}" );
	}
}

/**
 * Get or generate the CSP nonce for the current request.
 *
 * Regenerated on every page load.
 *
 * @return string Base64-encoded nonce.
 */
function aether_get_csp_nonce() {
	static $nonce = null;
	if ( null === $nonce ) {
		$nonce = base64_encode( function_exists( 'random_bytes' ) ? random_bytes( 16 ) : wp_generate_password( 16, false ) );
	}
	return $nonce;
}

add_action( 'wp_body_open', 'aether_print_csp_nonce_script', 1 );
/**
 * Expose the nonce to JavaScript via a global (used by inline handlers).
 */
function aether_print_csp_nonce_script() {
	if ( is_admin() ) {
		return;
	}
	$nonce = aether_get_csp_nonce();
	printf( '<script nonce="%s">window.aetherCSPNonce="%s";</script>', esc_attr( $nonce ), esc_js( $nonce ) );
}

add_filter( 'script_loader_tag', 'aether_add_nonce_to_scripts', 10, 2 );
/**
 * Add the CSP nonce to every AETHER-enqueued script tag.
 *
 * @param string $tag    The script tag.
 * @param string $handle The script handle.
 * @return string Modified script tag.
 */
function aether_add_nonce_to_scripts( $tag, $handle ) {
	if ( is_admin() ) {
		return $tag;
	}

	if ( 0 !== strpos( $handle, 'aether-' ) && 0 !== strpos( $handle, 'aureon-' ) ) {
		return $tag;
	}

	$nonce = aether_get_csp_nonce();
	return str_replace( '<script ', '<script nonce="' . esc_attr( $nonce ) . '" ', $tag );
}

add_action( 'init', 'aether_remove_x_powered_by', 1 );
/**
 * Remove the X-Powered-By header if present.
 */
function aether_remove_x_powered_by() {
	if ( ! headers_sent() ) {
		header_remove( 'X-Powered-By' );
	}
}

add_action( 'send_headers', 'aether_add_hsts_header', 2 );
/**
 * Add Strict-Transport-Security for HTTPS sites.
 */
function aether_add_hsts_header() {
	if ( is_admin() || ! is_ssl() ) {
		return;
	}

	header( 'Strict-Transport-Security: max-age=31536000; includeSubDomains; preload' );
}
