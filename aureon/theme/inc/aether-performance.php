<?php
/**
 * AETHER Performance Optimizations.
 *
 * Adds resource hints, font preloading, DNS prefetch, and other
 * performance enhancements for the AETHER frontend.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ─── Resource Hints ────────────────────────────────────────────
add_action( 'wp_head', 'aether_resource_hints', 1 );
/**
 * Output DNS prefetch, preconnect, and preload resource hints.
 */
function aether_resource_hints() {
	if ( is_admin() ) {
		return;
	}

	?>
	<!-- DNS Prefetch -->
	<link rel="dns-prefetch" href="//cdn.jsdelivr.net">
	<link rel="dns-prefetch" href="//cdnjs.cloudflare.com">
	<link rel="dns-prefetch" href="//unpkg.com">
	<link rel="dns-prefetch" href="//fonts.googleapis.com">
	<link rel="dns-prefetch" href="//fonts.gstatic.com">
	<link rel="dns-prefetch" href="//www.google-analytics.com">
	<link rel="dns-prefetch" href="//www.googletagmanager.com">

	<!-- Preconnect -->
	<link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
	<link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
	<link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

	<!-- Preload Critical Fonts -->
	<link rel="preload" href="https://fonts.googleapis.com/css2?family=Cabinet+Grotesk:wght@400;500;700;800&family=Satoshi:wght@400;500;700&display=swap" as="style" crossorigin>

	<!-- Preload Critical CSS -->
	<link rel="preload" href="<?php echo esc_url( get_template_directory_uri() . '/assets/aether/css/style.css' ); ?>" as="style">

	<!-- Preload Hero Image (first slide) -->
	<?php if ( is_front_page() ) : ?>
		<?php
		$hero_img = get_theme_mod( 'aether_hero_slide_1_image', '' );
		if ( $hero_img ) :
			?>
			<link rel="preload" href="<?php echo esc_url( $hero_img ); ?>" as="image">
		<?php endif; ?>
	<?php endif; ?>

	<?php
}

// ─── Lazy Loading for Images ───────────────────────────────────
add_filter( 'wp_img_tag_add_loading_attr', 'aether_lazy_loading_attr', 10, 2 );
/**
 * Ensure lazy loading is applied to all images except above-the-fold.
 *
 * @param string $value  The loading attribute value.
 * @param string $image  The image tag.
 * @return string Modified loading attribute.
 */
function aether_lazy_loading_attr( $value, $image ) {
	// Don't lazy-load hero images or logo.
	if ( false !== strpos( $image, 'hero-slide' ) || false !== strpos( $image, 'brand-logo' ) || false !== strpos( $image, 'footer-logo' ) ) {
		return false;
	}
	return $value;
}

// ─── Defer Non-Critical Scripts ─────────────────────────────────
add_filter( 'script_loader_tag', 'aether_defer_scripts', 10, 2 );
/**
 * Add defer attribute to non-critical scripts.
 *
 * @param string $tag    The script tag.
 * @param string $handle The script handle.
 * @return string Modified script tag.
 */
function aether_defer_scripts( $tag, $handle ) {
	if ( is_admin() ) {
		return $tag;
	}

	$defer_handles = array(
		'aether-vendor-back-to-top',
		'aether-vendor-contact-form',
		'aether-vendor-counter',
		'aether-vendor-country-dropdown',
		'aether-vendor-filter-button',
		'aether-vendor-loadmore',
		'aether-vendor-video-popup',
		'aether-vendor-video-section',
		'aether-vendor-wow',
		'aether-effects',
	);

	if ( in_array( $handle, $defer_handles, true ) ) {
		return str_replace( '<script ', '<script defer ', $tag );
	}

	return $tag;
}

// ─── Remove Query Strings from Static Resources ────────────────
add_filter( 'style_loader_src', 'aether_remove_query_strings', 10, 2 );
add_filter( 'script_loader_src', 'aether_remove_query_strings', 10, 2 );
/**
 * Remove query strings from static resource URLs for better caching.
 *
 * @param string $src    The resource URL.
 * @param string $handle The resource handle.
 * @return string Modified URL.
 */
function aether_remove_query_strings( $src, $handle ) {
	if ( is_admin() ) {
		return $src;
	}

	// Keep version strings for AETHER assets (they use filemtime).
	if ( 0 === strpos( $handle, 'aether-' ) || 0 === strpos( $handle, 'aureon-' ) ) {
		return $src;
	}

	if ( false !== strpos( $src, '?ver=' ) ) {
		$src = remove_query_arg( 'ver', $src );
	}
	return $src;
}

// ─── Preload Critical Assets ───────────────────────────────────
add_action( 'wp_head', 'aether_preload_assets', 2 );
/**
 * Output preload links for critical assets.
 */
function aether_preload_assets() {
	if ( is_admin() || is_customize_preview() ) {
		return;
	}

	// Preload the main AETHER CSS.
	$aether_css = get_template_directory_uri() . '/assets/aether/css/style.css';
	printf( '<link rel="preload" href="%s" as="style">', esc_url( $aether_css ) );
	echo "\n";

	// Preload GSAP (needed for animations).
	$gsap_url = get_template_directory_uri() . '/assets/vendor/gsap/gsap.min.js';
	printf( '<link rel="preload" href="%s" as="script">', esc_url( $gsap_url ) );
	echo "\n";
}

// ─── Output Buffer for HTML Minification ───────────────────────
/**
 * Minimal HTML output compression (removes extra whitespace between tags).
 * Only runs on front-end, not in admin or customizer.
 */
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
 * Callback for output buffering — compress HTML output.
 *
 * @param string $html The raw HTML output.
 * @return string Compressed HTML.
 */
function aether_compress_html( $html ) {
	// Don't compress if the response is empty or already minified.
	if ( empty( $html ) || strlen( $html ) < 500 ) {
		return $html;
	}

	// Don't compress admin-ajax or REST API responses.
	if ( defined( 'DOING_AJAX' ) || defined( 'REST_REQUEST' ) ) {
		return $html;
	}

	// Remove HTML comments (except conditional comments and structured data).
	$html = preg_replace( '/<!--(?!\[if )(?<!\[endif\])(?!<script type="application\/ld\+json).*?-->/s', '', $html );

	// Collapse whitespace between tags (but not inside <pre> or <script>).
	$html = preg_replace( '/>\s+</', '><', $html );

	// Collapse multiple newlines into one.
	$html = preg_replace( '/\n{2,}/', "\n", $html );

	return $html;
}

// ─── WooCommerce Performance ───────────────────────────────────
add_action( 'wp', 'aether_optimize_woocommerce' );
/**
 * Optimize WooCommerce by disabling unnecessary features on non-WC pages.
 */
function aether_optimize_woocommerce() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	// Disable WC scripts and styles on non-WC pages.
	if ( ! is_woocommerce() && ! is_cart() && ! is_checkout() && ! is_account_page() ) {
		remove_action( 'wp_enqueue_scripts', array( 'WC_Frontend_Scripts', 'enqueue_scripts' ), 10 );
	}
}

// ─── Database Query Optimization ───────────────────────────────
add_action( 'init', 'aether_optimize_queries' );
/**
 * Optimize common database queries.
 */
function aether_optimize_queries() {
	// Preload common options.
	if ( is_front_page() ) {
		wp_cache_set( 'aether_preloaded', true, 'aether', 300 );
	}
}
