<?php
/**
 * Vineta Design Pack — Token Defaults
 *
 * Provides default Customizer values for the Vineta design.
 * These merge onto the settings bucket at priority 20 (after engine defaults).
 * Saved Customizer values always win.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	// Site identity.
	'aether_site_heading' => '',

	// Announcement bar. The engine-level single-text default is old demo copy
	// ("Free shipping on orders over $150 — Step into the void."); Vineta ships
	// an empty default so clients start clean and set their own text in the
	// Customizer. Items are driven by aether_announcement_items (also empty).
	'aether_announcement_items' => array(),
	'aether_announcement_text'  => '',

	// Hero.
	'aether_hero_slides' => array(),

	// Categories.
	'aether_category_items' => array(),

	// Newsletter.
	'aether_newsletter_heading' => 'Subscribe Newsletter',
	'aether_newsletter_text'    => 'Register to read the latest news, offers and events about our company. We promise not spam your inbox.',
	'aether_newsletter_subtitle' => '',

	// Footer.
	'aether_footer_columns' => array(),
	'aether_footer_usp_items' => array(),
	'aether_footer_payments' => array(),

	// Social.
	'aether_social_items' => array(),

	// Search.
	'aether_search_placeholder' => 'Search products...',

	// Colors (Vineta defaults).
	'aether_color_bg'           => '#ffffff',
	'aether_color_surface'      => '#f8f8f8',
	'aether_color_text'         => '#1a1a1a',
	'aether_color_muted'        => '#777777',
	'aether_color_accent'       => '#1a1a1a',
	'aether_color_accent_hover' => '#333333',
	'aether_color_border'       => '#e5e5e5',

	// Fonts (Vineta uses system fonts via CSS).
	'aether_font_heading' => '',
	'aether_font_body'    => '',

	// Demo mode.
	'aether_demo_mode' => 'auto',
);
