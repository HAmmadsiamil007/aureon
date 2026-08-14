<?php
/**
 * Stage 2 — Frontend engine integration (shell).
 *
 * Boots the frontend engine, registers the primary nav location, suppresses
 * theme presentation output the engine now owns, and enqueues AETHER shell
 * assets (CDN per source contract + local styles/scripts).
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require_once get_template_directory() . '/../../frontend/views/loader.php';

require_once get_template_directory() . '/inc/aether-tokens.php';
require_once get_template_directory() . '/inc/aether-security.php';
require_once get_template_directory() . '/inc/aether-seo.php';
require_once get_template_directory() . '/inc/aether-newsletter.php';
require_once get_template_directory() . '/inc/aether-ajax.php';
require_once get_template_directory() . '/inc/aether-cart.php';
require_once get_template_directory() . '/inc/aether-analytics.php';
require_once get_template_directory() . '/inc/aether-performance.php';

/**
 * Register the primary navigation location.
 */
function aureon_aether_register_nav_menus() {
	register_nav_menus(
		array(
			'primary' => __( 'AETHER Primary Menu', 'aureon' ),
			'footer'  => __( 'AETHER Footer Menu', 'aureon' ),
		)
	);
}
add_action( 'after_setup_theme', 'aureon_aether_register_nav_menus', 20 );

/**
 * Boot the frontend engine after the theme is set up.
 */
function aureon_aether_frontend_boot() {
	aether_frontend_boot();
}
add_action( 'after_setup_theme', 'aureon_aether_frontend_boot', 30 );

/**
 * Suppress theme presentation output the engine now owns.
 *
 * Runs late (priority 1000) after the theme's own enqueue callbacks so
 * every theme layout style/script is removed before output.
 */
function aureon_aether_suppress_theme_output() {
	// Theme layout styles.
	// NOTE: 'aureon-google-fonts' is deliberately NOT suppressed — the
	// dynamic Typography Manager (Font Manager) enqueues it, and AETHER
	// bridges those families into --font-heading / --font-body tokens.
	// 'aureon-fonts' (the legacy non-dynamic handle) stays suppressed.
	$theme_styles = array(
		'aureon-comments',
		'aureon-widget-areas',
		'aureon-style',
		'aureon-style-grid',
		'aureon-mobile-style',
		'aureon-font-icons',
		'font-awesome', // Theme's own FA 4.7 — AETHER loads FA 6.5.1.
		'aureon-rtl',
		'aureon-fonts',
		'aureon-child',
	);

	foreach ( $theme_styles as $handle ) {
		wp_dequeue_style( $handle );
		wp_deregister_style( $handle );
	}

	// Theme layout scripts.
	$theme_scripts = array(
		'aureon-menu',
		'aureon-dropdown-click',
		'aureon-modal',
		'aureon-navigation-search',
		'aureon-back-to-top',
	);

	foreach ( $theme_scripts as $handle ) {
		wp_dequeue_script( $handle );
		wp_deregister_script( $handle );
	}

	// Theme wp_footer hookups that would duplicate AETHER chrome.
	remove_action( 'wp_footer', 'aureon_do_a11y_scripts' );
	remove_action( 'wp_footer', 'aureon_do_search_modal' );
	remove_action( 'wp_footer', 'aureon_clone_sidebar_navigation' );

	// Theme header/footer construction is bypassed by template replacement,
	// but kill the remaining after-header callback defensively.
	remove_action( 'aureon_after_header', 'aureon_featured_page_header', 10 );
}
add_action( 'wp_enqueue_scripts', 'aureon_aether_suppress_theme_output', 1000 );

/**
 * Enqueue AETHER shell assets (source contract order).
 */
function aureon_aether_enqueue_assets() {
	$uri = trailingslashit( content_url() ) . 'frontend/assets';
	$dir = trailingslashit( WP_CONTENT_DIR ) . 'frontend/assets';

	// --- CSS (source contract order) ---
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
	wp_enqueue_style( 'aether-style', $uri . '/css/style.css', array(), filemtime( $dir . '/css/style.css' ) );
	wp_enqueue_style( 'aether-motion', $uri . '/css/motion.css', array( 'aether-style' ), filemtime( $dir . '/css/motion.css' ) );
	wp_enqueue_style( 'aether-responsive', $uri . '/css/responsive.css', array( 'aether-style' ), filemtime( $dir . '/css/responsive.css' ) );
	wp_enqueue_style( 'aether-a11y', $uri . '/css/a11y.css', array( 'aether-style' ), filemtime( $dir . '/css/a11y.css' ) );
	wp_enqueue_style( 'aether-pages', $uri . '/css/pages.css', array( 'aether-style' ), filemtime( $dir . '/css/pages.css' ) );

	// --- JS (source contract order) ---
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
	wp_enqueue_script( 'aether-lenis', 'https://unpkg.com/lenis@1.1.19/dist/lenis.min.js', array(), '1.1.19', true );

	wp_enqueue_script( 'aether-lenis-scroll', $uri . '/js/lenis-scroll.js', array( 'aether-lenis' ), filemtime( $dir . '/js/lenis-scroll.js' ), true );
	wp_enqueue_script( 'aether-animations', $uri . '/js/animations.js', array( 'aether-bootstrap-js', 'aether-gsap' ), filemtime( $dir . '/js/animations.js' ), true );
	wp_enqueue_script( 'aether-main', $uri . '/js/main.js', array( 'aether-animations' ), filemtime( $dir . '/js/main.js' ), true );
	wp_enqueue_script( 'aether-phantom-bridge', $uri . '/js/phantom-bridge.js', array( 'aether-main' ), filemtime( $dir . '/js/phantom-bridge.js' ), true );
	wp_enqueue_script( 'aether-countdown', $uri . '/js/countdown.js', array(), filemtime( $dir . '/js/countdown.js' ), true );

	// AJAX + REST context for the engine scripts (shared aether_nonce).
	wp_localize_script(
		'aether-main',
		'aetherAjax',
		array(
			'ajaxUrl'       => admin_url( 'admin-ajax.php' ),
			'nonce'         => wp_create_nonce( 'aether_nonce' ),
			'restUrl'       => esc_url_raw( rest_url( 'aether/v1/' ) ),
			'isUserLoggedIn'=> is_user_logged_in(),
			'shopUrl'       => function_exists( 'wc_get_page_permalink' ) && wc_get_page_permalink( 'shop' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' ),
			'searchUrl'     => home_url( '/?s=' ),
			'wcAjaxUrl'     => function_exists( 'WC' ) ? add_query_arg( 'wc-ajax', 'add_to_cart', home_url( '/' ) ) : '',
		)
	);

	// --- Fonts (source contract) ---
	// Cabinet Grotesk + Satoshi are Fontshare families (not on Google Fonts),
	// self-hosted locally from the Fontshare CDN. See assets/css/fonts.css.
	wp_enqueue_style( 'aether-fonts', $uri . '/css/fonts.css', array( 'aether-style' ), filemtime( $dir . '/css/fonts.css' ) );

	// --- Favicons ---
	add_action( 'wp_head', 'aureon_aether_favicons', 1 );
}
add_action( 'wp_enqueue_scripts', 'aureon_aether_enqueue_assets', 20 );

/**
 * Route WooCommerce cart / checkout / account pages to AETHER templates.
 *
 * WC 11's template loader only handles product/shop archives; these pages
 * otherwise render through the theme's default page template. The route is
 * guarded so order-pay / order-received endpoints keep WC's stock flow.
 *
 * @param string $template The resolved template path.
 * @return string
 */
function aureon_aether_wc_page_templates( $template ) {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return $template;
	}

	if ( is_cart() ) {
		return get_template_directory() . '/cart.php';
	}

	if ( is_wc_endpoint_url( 'order-received' ) ) {
		return get_template_directory() . '/woocommerce/checkout/thankyou.php';
	}

	if ( is_checkout() && ! is_wc_endpoint_url( 'order-pay' ) ) {
		return get_template_directory() . '/checkout/form-checkout.php';
	}

	if ( is_account_page() ) {
		return get_template_directory() . '/myaccount/my-account.php';
	}

	return $template;
}
add_filter( 'template_include', 'aureon_aether_wc_page_templates', 99 );

/**
 * Favicon + theme-color + Open Graph head output.
 */
function aureon_aether_favicons() {
	$uri = trailingslashit( content_url() ) . 'frontend/assets';
	?>
	<link rel="icon" type="image/x-icon" href="<?php echo esc_url( $uri ); ?>/images/favicon/favicon.ico">
	<link rel="icon" type="image/png" sizes="32x32" href="<?php echo esc_url( $uri ); ?>/images/favicon/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="<?php echo esc_url( $uri ); ?>/images/favicon/favicon-16x16.png">
	<link rel="apple-touch-icon" sizes="180x180" href="<?php echo esc_url( $uri ); ?>/images/favicon/apple-icon-180x180.png">
	<meta name="msapplication-TileColor" content="#09090B">
	<meta name="theme-color" content="#09090B">
	<?php
}
