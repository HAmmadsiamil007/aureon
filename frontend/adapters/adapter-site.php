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

	$columns = array(
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
				array( 'label' => 'Size Guide', 'url' => home_url( '/product-detail/#size-guide' ) ),
			),
		),
		array(
			'heading' => 'Company',
			'links'   => array(
				array( 'label' => 'About Us', 'url' => home_url( '/about/' ) ),
				array( 'label' => 'Blog', 'url' => home_url( '/blog/' ) ),
				array( 'label' => 'Careers', 'url' => home_url( '/about/#careers' ) ),
				array( 'label' => 'Press', 'url' => home_url( '/about/#press' ) ),
			),
		),
	);

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
