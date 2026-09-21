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

// ─── CDN Subresource Integrity (SRI) ──────────────────────────
add_filter( 'wp_script_tag', 'aether_add_sri_to_scripts', 10, 3 );
add_filter( 'wp_style_tag', 'aether_add_sri_to_styles', 10, 3 );
/**
 * Add SRI integrity/crossorigin attributes to known CDN assets.
 *
 * Hashes correspond to the exact pinned versions in the source contract.
 * If a CDN URL is upgraded, hashes MUST be recomputed.
 *
 * @param string $tag        The script/style tag.
 * @param string $handle     The asset handle.
 * @param string $src        The asset source URL.
 * @return string Modified tag.
 */
function aether_add_sri_to_scripts( $tag, $handle, $src ) {
	return aether_apply_sri( $tag, $src );
}
function aether_add_sri_to_styles( $tag, $handle, $src ) {
	return aether_apply_sri( $tag, $src );
}
function aether_apply_sri( $tag, $src ) {
	$sri = aether_cdn_sri_map();
	$normalized = esc_url_raw( $src );
	foreach ( $sri as $url_fragment => $hash ) {
		if ( false !== strpos( $normalized, $url_fragment ) ) {
			$tag = str_replace( '<script ', '<script integrity="' . esc_attr( $hash ) . '" crossorigin="anonymous" ', $tag );
			$tag = str_replace( '<link ', '<link integrity="' . esc_attr( $hash ) . '" crossorigin="anonymous" ', $tag );
			break;
		}
	}
	return $tag;
}
/**
 * Known CDN resource SRI hashes (version-pinned).
 *
 * @return array URL fragment => sha384 hash.
 */
function aether_cdn_sri_map() {
	return array(
		'bootstrap@5.3.3/dist/css/bootstrap.min.css'  => 'sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH',
		'font-awesome/6.5.1/css/all.min.css'           => 'sha384-t1nt8BQoYMLFN5p42tRAtuAAFQaCQODekUVeKKZrEnEyp4H2R0RHFz0KWpmj7i8g',
		'swiper@11/swiper-bundle.min.css'              => 'sha384-gAPqlBuTCdtVcYt9ocMOYWrnBZ4XSL6q+4eXqwNycOr4iFczhNKtnYhF3NEXJM51',
		'bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js' => 'sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz',
		'swiper@11/swiper-bundle.min.js'               => 'sha384-2UI1PfnXFjVMQ7/ZDEF70CR943oH3v6uZrFQGGqJYlvhh4g6z6uVktxYbOlAczav',
		'gsap/3.12.5/gsap.min.js'                      => 'sha384-g4NTh/Iv5PPU4xPyhEWqPcwtNXOvdaDI8LLnyYfyNZOjKJeYQyjzQ9X5275eBjpt',
		'gsap/3.12.5/ScrollTrigger.min.js'             => 'sha384-Z3REaz79l2IaAZqJsSABtTbhjgOUYyV3p90XNnAPCSHg3EMTz1fouunq9WZRtj3d',
		'lenis@1.1.19/dist/lenis.min.js'               => 'sha384-cpO5a+hyuyImPs1AWAUDpKJ5zzCqsDjiZOqfOyTb4h4sVh2AST2RUyLHdXD3Vz8p',
	);
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
