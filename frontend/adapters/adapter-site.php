<?php
/**
 * Site adapter — maps site-wide data (logo, name, nav) to component data.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Site basics (brand, logo, url) — consumed by preloader/header/footer.
 *
 * @return array
 */
function aether_adapter_site() {
	return array(
		'name'    => get_bloginfo( 'name' ),
		'brand'   => get_bloginfo( 'name' ),
		'tagline' => get_bloginfo( 'description' ),
		'logo'    => get_theme_mod( 'custom_logo', '' ),
		'url'     => home_url( '/' ),
	);
}

/**
 * Footer data — brand, socials, link columns, newsletter, legal, payments.
 *
 * @return array
 */
function aether_adapter_footer() {
	$site = aether_adapter_site();

	// Default column structure — the current premium design. Empty URLs in a
	// saved setting resolve against this map so output always stays valid.
	$default_columns = array(
		array(
			'heading' => 'Shop',
			'links'   => array(
				array( 'label' => 'Men', 'url' => home_url( '/shop/' ) ),
				array( 'label' => 'Women', 'url' => home_url( '/shop/' ) ),
				array( 'label' => 'Kids', 'url' => home_url( '/shop/' ) ),
				array( 'label' => 'New Arrivals', 'url' => home_url( '/shop/' ) ),
				array( 'label' => 'Bestsellers', 'url' => home_url( '/shop/' ) ),
			),
		),
		array(
			'heading' => 'Support',
			'links'   => array(
				array( 'label' => 'FAQ', 'url' => home_url( '/faq/' ) ),
				array( 'label' => 'Contact Us', 'url' => home_url( '/contact/' ) ),
				array( 'label' => 'Shipping Info', 'url' => home_url( '/faq/#shipping' ) ),
				array( 'label' => 'Returns & Exchanges', 'url' => home_url( '/faq/#returns' ) ),
				array( 'label' => 'Size Guide', 'url' => home_url( '/shop/' ) ),
			),
		),
		array(
			'heading' => 'Company',
			'links'   => array(
				array( 'label' => 'About Us', 'url' => home_url( '/about/' ) ),
				array( 'label' => 'Blog', 'url' => home_url( '/blog/' ) ),
				array( 'label' => 'Careers', 'url' => home_url( '/about/' ) ),
				array( 'label' => 'Press', 'url' => home_url( '/about/' ) ),
			),
		),
	);

	// Footer columns from settings (aether_footer_columns) — defaults equal the
	// current design, so the initial render is pixel-identical.
	$columns = aureon_get_option( 'aether_footer_columns', array() );

	if ( is_string( $columns ) && '' !== trim( $columns ) ) {
		$columns = json_decode( $columns, true );
	}

	if ( empty( $columns ) || ! is_array( $columns ) ) {
		$columns = $default_columns;
	} else {
		// Resolve empty URLs against the default map (label → url).
		$default_urls = array();
		foreach ( $default_columns as $col ) {
			foreach ( (array) $col['links'] as $link ) {
				if ( ! empty( $link['label'] ) ) {
					$default_urls[ strtolower( $link['label'] ) ] = $link['url'];
				}
			}
		}

		foreach ( $columns as $ci => $col ) {
			if ( empty( $col['links'] ) || ! is_array( $col['links'] ) ) {
				continue;
			}
			foreach ( $col['links'] as $li => $link ) {
				if ( empty( $link['url'] ) && ! empty( $link['label'] ) && ! empty( $default_urls[ strtolower( $link['label'] ) ] ) ) {
					$columns[ $ci ]['links'][ $li ]['url'] = $default_urls[ strtolower( $link['label'] ) ];
				}
			}
		}
	}

	return array(
		'brand'       => $site['brand'],
		'brand_url'   => $site['url'],
		'tagline'     => $site['tagline'],
		'socials'     => aether_adapter_socials(),
		'columns'     => $columns,
		'newsletter'  => array(
			'heading' => 'Stay in the Loop',
			'text'    => 'Get exclusive drops, early access, and AETHER news.',
		),
		'copyright'   => sprintf(
			'&copy; %s %s. All Rights Reserved.',
			date_i18n( 'Y' ),
			$site['name']
		),
		'legal'       => array(
			array( 'label' => 'Privacy', 'url' => home_url( '/privacy-policy/' ) ),
			array( 'label' => 'Terms', 'url' => home_url( '/term-of-use/' ) ),
			array( 'label' => 'Cookies', 'url' => home_url( '/cookie-policy/' ) ),
		),
		'payments'    => array( 'fa-cc-visa', 'fa-cc-mastercard', 'fa-cc-amex', 'fa-cc-paypal', 'fa-apple-pay' ),
	);
}
