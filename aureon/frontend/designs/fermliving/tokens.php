<?php
/**
 * Ferm Living Design Pack — Token Defaults.
 *
 * Provides pack-specific option defaults that merge onto the engine
 * bucket at priority 20. Saved Customizer values always win.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	// Ferm Living uses its own CSS — disable AETHER motion for complete-page mode.
	'aether_motion_enabled'  => false,
	'aether_motion_reveal'   => false,
	'aether_motion_tilt'     => false,
	'aether_motion_parallax' => false,
	'aether_motion_text'     => false,

	// Shell toggles — Ferm has its own shell.
	'aether_preloader_enabled'   => false,
	'aether_fog_enabled'         => false,
	'aether_announcement_enabled' => true,

	// Ferm Living announcement text.
	'aether_announcement_text' => 'Free shipping on orders over €150',
	'aether_announcement_url'  => '',

	// Shop per page — match Ferm's default.
	'aether_shop_per_page' => 24,

	// Section visibility — Ferm homepage is frozen HTML, not AETHER sections.
	'aether_section_hero'        => false,
	'aether_section_categories'  => false,
	'aether_section_bestsellers' => false,
	'aether_section_reviews'     => false,
	'aether_section_faq'         => false,
	'aether_section_newsletter'  => false,

	// Footer data.
	'aether_footer_usp_items' => array(
		array( 'icon' => '', 'text' => 'Free shipping on orders over €150' ),
		array( 'icon' => '', 'text' => '30-day return policy' ),
		array( 'icon' => '', 'text' => 'Secure checkout' ),
	),
	'aether_newsletter_heading' => 'Ferm Living news',
	'aether_newsletter_text'    => 'Get exclusive drops, early access, and Ferm Living news.',
);
