<?php
/**
 * AETHER Performance Optimizations.
 *
 * Resource hints (DNS prefetch / preconnect), critical font & CSS preload,
 * query-string cleanup for third-party assets, WooCommerce script
 * optimization, and lightweight HTML compression.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_head', 'aether_resource_hints', 1 );
/**
 * Output DNS prefetch and preconnect resource hints for CDN origins.
 */
function aether_resource_hints() {
	if ( is_admin() || is_customize_preview() ) {
		return;
	}
	?>
	<link rel="dns-prefetch" href="//cdn.jsdelivr.net">
	<link rel="dns-prefetch" href="//cdnjs.cloudflare.com">
	<link rel="dns-prefetch" href="//unpkg.com">
	<link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
	<link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
	<?php
}

add_action( 'wp_head', 'aether_preload_assets', 2 );
/**
 * Preload critical fonts, the main AETHER stylesheet and the first hero slide.
 */
function aether_preload_assets() {
	if ( is_admin() || is_customize_preview() ) {
		return;
	}

	// Critical font stylesheet (no crossorigin: must match the enqueued
	// stylesheet's fetch mode, otherwise Chrome ORB-blocks the preload).
	// Cabinet Grotesk + Satoshi are self-hosted (Fontshare, local @font-face).
	$fonts_url = trailingslashit( content_url() ) . 'frontend/assets/css/fonts.css';
	if ( file_exists( WP_CONTENT_DIR . '/frontend/assets/css/fonts.css' ) ) {
		$fonts_url = add_query_arg( 'ver', filemtime( WP_CONTENT_DIR . '/frontend/assets/css/fonts.css' ), $fonts_url );
	}
	echo '<link rel="preload" href="' . esc_url( $fonts_url ) . '" as="style">' . "\n";

	// First visible hero slide image on the front page. Uses the adapter so
	// hidden slides and unresolved raw paths never leak into a preload.
	// Skip for complete-page designs that ship their own
	// HTML templates and don't use the PHP shell hero.
	if ( is_front_page() && function_exists( 'aether_viewmodel_resolve_image' ) && function_exists( 'aether_active_design' ) ) {
		if ( ! aether_is_complete_page_design() ) {
			$hero = function_exists( 'aether_adapter_hero' ) ? aether_adapter_hero() : array();
			$slides = isset( $hero['slides'] ) ? (array) $hero['slides'] : array();

			foreach ( $slides as $slide ) {
				$image = isset( $slide['image'] ) ? $slide['image'] : '';
				if ( '' !== $image ) {
					printf( '<link rel="preload" href="%s" as="image">', esc_url( $image ) );
					echo "\n";
					break;
				}
			}
		}
	}
}

add_filter( 'style_loader_src', 'aether_remove_query_strings', 10, 2 );
add_filter( 'script_loader_src', 'aether_remove_query_strings', 10, 2 );
/**
 * Remove query strings from third-party CDN resources.
 *
 * AETHER's own local assets keep their filemtime version strings; the
 * aether-* handles on CDN origins get ?ver= stripped since the CDN files
 * never change with the theme.
 *
 * @param string $src    The resource URL.
 * @param string $handle The resource handle.
 * @return string Modified URL.
 */
function aether_remove_query_strings( $src, $handle ) {
	if ( is_admin() ) {
		return $src;
	}

	$content_url = trailingslashit( content_url() );

	// Keep version strings for local assets.
	if ( 0 === strpos( $src, $content_url ) ) {
		return $src;
	}

	if ( false !== strpos( $src, '?ver=' ) ) {
		$src = remove_query_arg( 'ver', $src );
	}
	return $src;
}

add_action( 'wp', 'aether_optimize_woocommerce' );
/**
 * Disable WooCommerce frontend assets on non-WC pages.
 */
function aether_optimize_woocommerce() {
	if ( ! class_exists( 'WooCommerce' ) || is_admin() ) {
		return;
	}

	if ( ! is_woocommerce() && ! is_cart() && ! is_checkout() && ! is_account_page() ) {
		remove_action( 'wp_enqueue_scripts', array( 'WC_Frontend_Scripts', 'enqueue_scripts' ), 10 );
	}
}

// ─── HTML Output Compression ───────────────────────────────────
if ( ! is_admin() && ! is_customize_preview() ) {
	add_action( 'template_redirect', 'aether_start_output_buffer' );
}

/**
 * Start output buffering for HTML compression.
 */
function aether_start_output_buffer() {
	if ( ! is_admin() && ! is_customize_preview() ) {
		ob_start( 'aether_compress_html' );
	}
}

/**
 * Compress HTML output: strip non-conditional comments, collapse
 * inter-tag whitespace. Never touches admin-ajax or REST responses.
 *
 * @param string $html The raw HTML output.
 * @return string Compressed HTML.
 */
function aether_compress_html( $html ) {
	if ( empty( $html ) || strlen( $html ) < 500 ) {
		return $html;
	}

	if ( defined( 'DOING_AJAX' ) || defined( 'REST_REQUEST' ) ) {
		return $html;
	}

	// Remove HTML comments, preserving conditionals and JSON-LD scripts.
	$html = preg_replace( '/<!--(?!\[if )(?<!\[endif\])(?!<script type="application\/ld\+json).*?-->/s', '', $html );

	// Collapse whitespace between tags (never inside <pre>/<script>).
	$html = preg_replace( '/>\s+</', '><', $html );

	return $html;
}
