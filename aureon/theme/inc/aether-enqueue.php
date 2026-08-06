<?php
/**
 * AETHER Frontend Asset Enqueue.
 *
 * Loads AETHER CSS, JS, fonts, and CDN libraries.
 * Only activates on front-end pages (not admin/customizer).
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'aether_enqueue_assets' ) ) {
	add_action( 'wp_enqueue_scripts', 'aether_enqueue_assets', 99 );
	/**
	 * Enqueue all AETHER frontend assets.
	 *
	 * Priority 99 ensures this loads after Aureon core assets,
	 * allowing selective overrides.
	 */
	function aether_enqueue_assets() {
		// Skip if in customizer (Aureon core handles that).
		if ( is_customize_preview() ) {
			return;
		}

		$dir_uri = get_template_directory_uri();
		$aether  = $dir_uri . '/assets/aether';
		$version = AUREON_VERSION . '.' . filemtime( get_template_directory() . '/inc/aether-enqueue.php' );

		// ─── Dequeue conflicting Aureon core CSS ──
		// Only dequeue on front page where AETHER owns the full page markup.
		// On other pages, AETHER header/footer loads but body uses Aureon templates.
		if ( is_front_page() ) {
			wp_dequeue_style( 'aureon-style' );
			wp_dequeue_style( 'aureon-style-grid' );
			wp_dequeue_style( 'aureon-mobile-style' );
			wp_dequeue_style( 'aureon-rtl' );
			wp_dequeue_style( 'aureon-comments' );
			wp_dequeue_style( 'aureon-widget-areas' );
		}
		// Always replace FA 4.7 with FA 6.
		wp_dequeue_style( 'font-awesome' );

		// ─── CDN Libraries ──
		// Bootstrap CSS (CDN — local copy missing from integration).
		wp_enqueue_style(
			'aether-bootstrap',
			'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
			array(),
			'5.3.3',
			'all'
		);

		// Swiper CSS.
		wp_enqueue_style(
			'aether-swiper',
			'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css',
			array(),
			'11.0.0',
			'all'
		);

		// Font Awesome 6.
		wp_enqueue_style(
			'aether-font-awesome',
			'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
			array(),
			'6.5.1',
			'all'
		);

		// Google Fonts: Cabinet Grotesk + Satoshi.
		wp_enqueue_style(
			'aether-google-fonts',
			'https://fonts.googleapis.com/css2?family=Cabinet+Grotesk:wght@400;500;700;800&family=Satoshi:wght@400;500;700&display=swap',
			array(),
			null,
			'all'
		);

		// ─── AETHER Core CSS ──
		wp_enqueue_style(
			'aether-style',
			$aether . '/css/style.css',
			array( 'aether-bootstrap', 'aether-swiper', 'aether-font-awesome', 'aether-google-fonts' ),
			$version,
			'all'
		);

		wp_enqueue_style(
			'aether-motion',
			$aether . '/css/motion.css',
			array( 'aether-style' ),
			$version,
			'all'
		);

		wp_enqueue_style(
			'aether-responsive',
			$aether . '/css/responsive.css',
			array( 'aether-style' ),
			$version,
			'all'
		);

		wp_enqueue_style(
			'aether-a11y',
			$aether . '/css/a11y.css',
			array( 'aether-style' ),
			$version,
			'all'
		);

		// Vendor CSS.
		wp_enqueue_style(
			'aether-vendor-animate',
			$aether . '/css/vendor/animate.css',
			array(),
			$version,
			'all'
		);

		wp_enqueue_style(
			'aether-vendor-owl',
			$aether . '/css/vendor/owl.carousel.min.css',
			array(),
			$version,
			'all'
		);

		wp_enqueue_style(
			'aether-vendor-owl-theme',
			$aether . '/css/vendor/owl.theme.default.min.css',
			array( 'aether-vendor-owl' ),
			$version,
			'all'
		);

		// AETHER pages (blog, about, contact, coming soon, 404, WooCommerce overrides).
		wp_enqueue_style(
			'aether-pages',
			$aether . '/css/pages.css',
			array( 'aether-style' ),
			$version,
			'all'
		);

		// ─── CDN JS Libraries ──
		// Swiper JS.
		wp_enqueue_script(
			'aether-swiper-js',
			'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js',
			array(),
			'11.0.0',
			true
		);

		// Bootstrap JS.
		wp_enqueue_script(
			'aether-bootstrap-js',
			'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js',
			array(),
			'5.3.3',
			true
		);

		// ─── GSAP + Lenis — use vendor copies (loaded by vendor-enqueue.php) ──
		// AETHER script handles require via wp_register_script dependencies.
		// GSAP is enqueued as 'aureon-gsap' by vendor-enqueue.php.
		// Register aliases so AETHER scripts can depend on them.
		if ( ! wp_script_is( 'aureon-gsap', 'registered' ) ) {
			// Fallback: load from CDN if vendor-enqueue didn't run.
			wp_enqueue_script( 'aether-gsap', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js', array(), '3.12.5', true );
			wp_enqueue_script( 'aether-scroll-trigger', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js', array( 'aether-gsap' ), '3.12.5', true );
		} else {
			// Alias vendor handles for AETHER dependencies.
			wp_register_script( 'aether-gsap', false, array( 'aureon-gsap' ), null, true );
			wp_enqueue_script( 'aether-gsap' );
			wp_register_script( 'aether-scroll-trigger', false, array( 'aureon-gsap-ScrollTrigger' ), null, true );
			wp_enqueue_script( 'aether-scroll-trigger' );
		}

		if ( ! wp_script_is( 'aureon-lenis', 'registered' ) ) {
			wp_enqueue_script( 'aether-lenis', 'https://unpkg.com/lenis@1.1.18/dist/lenis.min.js', array(), '1.1.18', true );
		} else {
			wp_register_script( 'aether-lenis', false, array( 'aureon-lenis' ), null, true );
			wp_enqueue_script( 'aether-lenis' );
		}

		// ─── jQuery — use WordPress core jQuery ──
		// Vendor scripts (owl, loadmore, etc.) depend on 'jquery' handle.
		if ( ! wp_script_is( 'jquery', 'enqueued' ) ) {
			wp_enqueue_script( 'jquery' );
		}

		// Vendor scripts use $ directly — define $ as jQuery alias for vendor scope.
		wp_add_inline_script( 'jquery', 'if(typeof $==="undefined"&&typeof jQuery!=="undefined"){var $=jQuery;}', 'after' );

		// ─── AETHER Core JS ──
		wp_enqueue_script(
			'aether-phantom-bridge',
			$aether . '/js/phantom-bridge.js',
			array(),
			$version,
			true
		);

		wp_enqueue_script(
			'aether-phantom-data',
			$aether . '/js/phantom-data.js',
			array( 'aether-phantom-bridge' ),
			$version,
			true
		);

		wp_enqueue_script(
			'aether-phantom-dark-mode',
			$aether . '/js/phantom-dark-mode.js',
			array(),
			$version,
			true
		);

		wp_enqueue_script(
			'aether-lenis-scroll',
			$aether . '/js/lenis-scroll.js',
			array( 'aether-lenis' ),
			$version,
			true
		);

		wp_enqueue_script(
			'aether-animations',
			$aether . '/js/animations.js',
			array( 'aether-gsap', 'aether-scroll-trigger' ),
			$version,
			true
		);

		wp_enqueue_script(
			'aether-effects',
			$aether . '/js/effects.js',
			array(),
			$version,
			true
		);

		wp_enqueue_script(
			'aether-main',
			$aether . '/js/main.js',
			array( 'aether-swiper-js', 'aether-gsap', 'aether-lenis' ),
			$version,
			true
		);

		// ─── Vendor JS (individual, loaded in footer) ──
		$vendor_scripts = array(
			'aether-vendor-back-to-top'      => 'back-to-top-button.js',
			'aether-vendor-contact-form'     => 'contact-form.js',
			'aether-vendor-counter'          => 'counter.js',
			'aether-vendor-country-dropdown' => 'country_dropdown.js',
			'aether-vendor-filter-button'    => 'filter-button.js',
			'aether-vendor-loadmore'         => 'loadmore.js',
			'aether-vendor-owl'              => 'owl.carousel.js',
			'aether-vendor-product-quantity' => 'product-quantity.js',
			'aether-vendor-quantity'         => 'quantity.js',
			'aether-vendor-remove-product'   => 'remove-product.js',
			'aether-vendor-search'           => 'search.js',
			'aether-vendor-video-popup'      => 'video-popup.js',
			'aether-vendor-video-section'    => 'video-section.js',
			'aether-vendor-wow'              => 'wow.js',
			'aether-vendor-jquery-validate'  => 'jquery.validate.js',
		);

		foreach ( $vendor_scripts as $handle => $file ) {
			wp_enqueue_script(
				$handle,
				$aether . '/js/vendor/' . $file,
				array( 'jquery' ),
				$version,
				true
			);
		}

		// ─── Localize phantom-data for WP REST API ──
		// Build WC-aware URLs for JS navigation.
		$wc_active = class_exists( 'WooCommerce' );
		$urls      = array(
			'home'       => esc_url( home_url( '/' ) ),
			'shop'       => $wc_active ? esc_url( wc_get_page_permalink( 'shop' ) ) : esc_url( home_url( '/shop/' ) ),
			'cart'       => $wc_active ? esc_url( wc_get_cart_url() ) : esc_url( home_url( '/cart/' ) ),
			'checkout'   => $wc_active ? esc_url( wc_get_checkout_url() ) : esc_url( home_url( '/checkout/' ) ),
			'myaccount'  => $wc_active ? esc_url( wc_get_page_permalink( 'myaccount' ) ) : esc_url( home_url( '/my-account/' ) ),
			'search'     => esc_url( home_url( '/' ) ),
		);

		wp_localize_script(
			'aether-phantom-data',
			'phantomData',
			array(
				'rest_url'    => esc_url_raw( rest_url() ),
				'nonce'       => wp_create_nonce( 'wp_rest' ),
				'plugin_url'  => esc_url( $dir_uri . '/assets/aether' ),
				'site_name'   => get_bloginfo( 'name' ),
				'is_logged_in' => is_user_logged_in(),
				'settings'    => aether_get_phantom_settings(),
				'urls'        => $urls,
			)
		);

		// ─── Disable Aureon core scripts that conflict ──
		wp_dequeue_script( 'aureon-menu' );
		wp_dequeue_script( 'aureon-dropdown-click' );
		wp_dequeue_script( 'aureon-navigation-search' );
		wp_dequeue_script( 'aureon-back-to-top' );
	}
}

if ( ! function_exists( 'aether_get_phantom_settings' ) ) {
	/**
	 * Build the settings array that phantom-data.js expects.
	 *
	 * Maps Customizer values to the data-phantom-* attribute keys.
	 *
	 * @return array Key-value pairs for phantom-data injection.
	 */
	function aether_get_phantom_settings() {
		$settings = array();

		// Site identity.
		$settings['site_name']   = get_bloginfo( 'name' );
		$settings['site_tagline'] = get_bloginfo( 'description' );

		// Hero slides (Customizer values or defaults).
		$dir_uri = get_template_directory_uri();
		$aether  = $dir_uri . '/assets/aether';
		$fallback_image = $aether . '/images/Luxury_running_sneaker_on_pedestal_202607222032.jpeg';

		$settings['hero_slide_1_headline'] = get_theme_mod( 'aether_hero_slide_1_headline', 'AETHER' );
		$settings['hero_slide_1_subline']  = get_theme_mod( 'aether_hero_slide_1_subline', 'Born from the silence between stars.' );
		$settings['hero_slide_1_image']    = get_theme_mod( 'aether_hero_slide_1_image', '' ) ?: $fallback_image;

		$settings['hero_slide_2_headline'] = get_theme_mod( 'aether_hero_slide_2_headline', 'Cloud Stride' );
		$settings['hero_slide_2_subline']  = get_theme_mod( 'aether_hero_slide_2_subline', 'Float above the pavement.' );
		$settings['hero_slide_2_image']    = get_theme_mod( 'aether_hero_slide_2_image', '' ) ?: $fallback_image;

		$settings['hero_slide_3_headline'] = get_theme_mod( 'aether_hero_slide_3_headline', 'Midnight Edition' );
		$settings['hero_slide_3_subline']  = get_theme_mod( 'aether_hero_slide_3_subline', 'Darkness refined.' );
		$settings['hero_slide_3_image']    = get_theme_mod( 'aether_hero_slide_3_image', '' ) ?: $fallback_image;

		// Section labels.
		$settings['section_label_categories']    = get_theme_mod( 'aether_section_label_categories', 'Shop by Category' );
		$settings['section_title_categories']    = get_theme_mod( 'aether_section_title_categories', 'Find Your Fit' );
		$settings['section_label_bestsellers']   = get_theme_mod( 'aether_section_label_bestsellers', 'Bestsellers' );
		$settings['section_title_bestsellers']   = get_theme_mod( 'aether_section_title_bestsellers', 'Most Loved' );
		$settings['section_subtitle_bestsellers'] = get_theme_mod( 'aether_section_subtitle_bestsellers', 'The shoes everyone\'s talking about.' );
		$settings['section_label_reviews']       = get_theme_mod( 'aether_section_label_reviews', 'Reviews' );
		$settings['section_title_reviews']       = get_theme_mod( 'aether_section_title_reviews', 'What Athletes Say' );
		$settings['section_label_faq']           = get_theme_mod( 'aether_section_label_faq', 'FAQ' );
		$settings['section_title_faq']           = get_theme_mod( 'aether_section_title_faq', 'Got Questions?' );
		$settings['section_subtitle_faq']        = get_theme_mod( 'aether_section_subtitle_faq', 'Everything you need to know about us.' );

		// Announcement bar.
		$settings['announcement_text']   = get_theme_mod( 'aether_announcement_text', 'Free Shipping On Orders Over $200' );
		$settings['announcement_enable'] = get_theme_mod( 'aether_announcement_enable', true );

		return $settings;
	}
}
