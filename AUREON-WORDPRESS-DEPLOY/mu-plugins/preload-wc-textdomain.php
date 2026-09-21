<?php
/**
 * Pre-load the WooCommerce textdomain before WC boots.
 *
 * WooCommerce 8.9 resolves Marketplace::init() during plugins_loaded and it
 * calls __() (via FeaturesUtil::feature_is_enabled) BEFORE the init action.
 * On WordPress 6.7+ that implicit just-in-time load raises a _doing_it_wrong
 * notice whose output breaks wp-admin redirects (headers already sent).
 * Fixed properly in WC 9.4; this mu-plugin is the shim for WC 8.9.
 *
 * If a real .mo exists for the current locale we load it (explicit early
 * loading is legal and does not warn); otherwise we seed a NOOP instance so
 * the JIT loader never runs pre-init. For en_US that is lossless because the
 * source strings are already English.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'muplugins_loaded', 'aureon_preload_wc_textdomain', 5 );
function aureon_preload_wc_textdomain() {
	if ( did_action( 'init' ) ) {
		return;
	}
	if ( isset( $GLOBALS['l10n']['woocommerce'] ) ) {
		return;
	}

	$locale = get_locale();

	// Try the real translation files first (user language pack, then bundled).
	$mo_candidates = array(
		WP_LANG_DIR . '/plugins/woocommerce-' . $locale . '.mo',
		WP_PLUGIN_DIR . '/woocommerce/i18n/languages/woocommerce-' . $locale . '.mo',
	);
	foreach ( $mo_candidates as $mofile ) {
		if ( is_readable( $mofile ) && load_textdomain( 'woocommerce', $mofile ) ) {
			return;
		}
	}

	// No .mo for this locale (e.g. en_US): seed an empty instance so
	// get_translations_for_domain() short-circuits and JIT never fires.
	if ( ! class_exists( 'NOOP_Translations' ) ) {
		return;
	}
	$GLOBALS['l10n']['woocommerce'] = new NOOP_Translations();
}
