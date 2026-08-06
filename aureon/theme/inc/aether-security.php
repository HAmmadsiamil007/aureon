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

	// ─── X-Content-Type-Options ─────────────────────────────────
	header( 'X-Content-Type-Options: nosniff' );

	// ─── X-Frame-Options ────────────────────────────────────────
	// Allow Customizer preview iframe; block everywhere else.
	if ( ! is_customize_preview() ) {
		header( 'X-Frame-Options: SAMEORIGIN' );
	}

	// ─── Referrer-Policy ────────────────────────────────────────
	header( 'Referrer-Policy: strict-origin-when-cross-origin' );

	// ─── Permissions-Policy ─────────────────────────────────────
	// Deny access to sensitive browser APIs by default.
	$permissions = array(
		'camera'     => '()',
		'microphone' => '()',
		'geolocation'=> '()',
		'interest-cohort' => '()',
	);
	$permissions = apply_filters( 'aether_permissions_policy', $permissions );
	$header_value = array();
	foreach ( $permissions as $feature => $value ) {
		$header_value[] = $feature . '=' . $value;
	}
	header( 'Permissions-Policy: ' . implode( ', ', $header_value ) );

	// ─── Content-Security-Policy ────────────────────────────────
	// Report-only by default; set AETHER_CSP_STRICT = true to enforce.
	aether_send_csp_header();
}

/**
 * Build and send the Content-Security-Policy header.
 *
 * Uses nonce-based approach for inline scripts. External scripts from
 * known CDNs are allowlisted. The policy is in report-only mode by
 * default — define AETHER_CSP_STRICT = true in wp-config.php to enforce.
 */
function aether_send_csp_header() {
	$nonce = aether_get_csp_nonce();

	// ─── Directives ────────────────────────────────────────────
	$directives = array(
		"default-src"     => "'self'",
		"script-src"      => "'self' 'nonce-{$nonce}' 'strict-dynamic' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://unpkg.com https://fonts.googleapis.com https://www.google-analytics.com https://www.googletagmanager.com",
		"style-src"       => "'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://unpkg.com https://fonts.googleapis.com",
		"img-src"         => "'self' data: https: blob:",
		"font-src"        => "'self' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.gstatic.com",
		"connect-src"     => "'self' https://www.google-analytics.com https://analytics.google.com",
		"frame-src"       => "'self' https://www.youtube.com https://player.vimeo.com",
		"object-src"      => "'none'",
		"base-uri"        => "'self'",
		"form-action"     => "'self'",
		"frame-ancestors" => "'self'",
	);

	$directives = apply_filters( 'aether_csp_directives', $directives );

	$header = array();
	foreach ( $directives as $directive => $value ) {
		$header[] = $directive . ' ' . $value;
	}

	$csp_value = implode( '; ', $header );

	// Report-only by default; enforce with AETHER_CSP_STRICT.
	if ( defined( 'AETHER_CSP_STRICT' ) && AETHER_CSP_STRICT ) {
		header( "Content-Security-Policy: {$csp_value}" );
	} else {
		header( "Content-Security-Policy-Report-Only: {$csp_value}" );
	}
}

/**
 * Get or generate the CSP nonce for the current request.
 *
 * The nonce is regenerated on every page load for security.
 *
 * @return string Base64-encoded nonce string.
 */
function aether_get_csp_nonce() {
	static $nonce = null;
	if ( null === $nonce ) {
		$nonce = base64_encode( wp_random_bytes( 16 ) );
	}
	return $nonce;
}

/**
 * Print the CSP nonce as a data attribute on <body> for JS access.
 *
 * @param string $tag The opening body tag.
 * @return string Modified body tag with nonce data attribute.
 */
add_filter( 'body_class', 'aether_add_csp_nonce_body_class' );
function aether_add_csp_nonce_body_class( $classes ) {
	return $classes;
}

add_action( 'wp_body_open', 'aether_print_csp_nonce_script', 1 );
/**
 * Print a tiny script that exposes the nonce to JavaScript via a global.
 */
function aether_print_csp_nonce_script() {
	if ( is_admin() ) {
		return;
	}
	$nonce = aether_get_csp_nonce();
	printf( '<script nonce="%s">window.aetherCSPNonce="%s";</script>', esc_attr( $nonce ), esc_js( $nonce ) );
}

/**
 * Add nonce to all AETHER enqueued scripts.
 *
 * This hook modifies the script tag attributes to include the CSP nonce.
 *
 * @param array $tag    The script tag.
 * @param string $handle The script handle.
 * @return string Modified script tag.
 */
add_filter( 'script_loader_tag', 'aether_add_nonce_to_scripts', 10, 2 );
function aether_add_nonce_to_scripts( $tag, $handle ) {
	if ( is_admin() ) {
		return $tag;
	}

	// Only add nonce to AETHER scripts.
	if ( 0 !== strpos( $handle, 'aether-' ) && 0 !== strpos( $handle, 'aureon-' ) ) {
		return $tag;
	}

	$nonce = aether_get_csp_nonce();
	return str_replace( '<script ', '<script nonce="' . esc_attr( $nonce ) . '" ', $tag );
}

/**
 * Remove X-Powered-By header if present.
 */
add_action( 'init', 'aether_remove_x_powered_by', 1 );
function aether_remove_x_powered_by() {
	if ( ! headers_sent() ) {
		header_remove( 'X-Powered-By' );
	}
}

/**
 * Add Strict-Transport-Security header for HTTPS sites.
 */
add_action( 'send_headers', 'aether_add_hsts_header', 2 );
function aether_add_hsts_header() {
	if ( is_admin() ) {
		return;
	}

	if ( is_ssl() ) {
		header( 'Strict-Transport-Security: max-age=31536000; includeSubDomains; preload' );
	}
}

/**
 * Generate a CSP nonce and make it available in wp_head.
 */
add_action( 'wp_head', 'aether_csp_nonce_meta', 1 );
function aether_csp_nonce_meta() {
	if ( is_admin() ) {
		return;
	}
	$nonce = aether_get_csp_nonce();
	printf( '<meta name="csp-nonce" content="%s">', esc_attr( $nonce ) );
}
