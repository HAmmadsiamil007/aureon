<?php
/**
 * This file handles the customizer fields for the AETHER Frontend framework.
 *
 * Exposes the frontend engine's section visibility toggles, motion toggles,
 * shell toggles, newsletter toggle, and layout options. Every setting uses the
 * AETHER defaults registered in frontend/tokens/tokens.php via the
 * aether_frontend_defaults filter, so nothing is hardcoded here.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

// Section that hosts all AETHER frontend toggles.
if ( ! $wp_customize->get_section( 'aureon_aether_section' ) ) {
	$wp_customize->add_section(
		'aureon_aether_section',
		array(
			'title'    => __( 'AETHER Frontend', 'aureon' ),
			'priority' => 120,
		)
	);
}

Aureon_Customize_Field::add_title(
	'aureon_aether_sections_title',
	array(
		'section' => 'aureon_aether_section',
		'title'   => __( 'Section Visibility', 'aureon' ),
	)
);

// Front page sections.
$frontend_section_options = array(
	'aether_section_hero'        => __( 'Hero', 'aureon' ),
	'aether_section_categories'  => __( 'Categories', 'aureon' ),
	'aether_section_bestsellers' => __( 'Bestsellers', 'aureon' ),
	'aether_section_reviews'     => __( 'Reviews', 'aureon' ),
	'aether_section_faq'         => __( 'FAQ', 'aureon' ),
	'aether_section_newsletter'  => __( 'Newsletter', 'aureon' ),
	// Static page sections.
	'aether_section_mission'     => __( 'Mission (About)', 'aureon' ),
	'aether_section_features'    => __( 'Features (About)', 'aureon' ),
	'aether_section_story'       => __( 'Story (About)', 'aureon' ),
	'aether_section_stats'       => __( 'Stats (About)', 'aureon' ),
	'aether_section_team'        => __( 'Team', 'aureon' ),
	'aether_section_values'      => __( 'Values (Team)', 'aureon' ),
	'aether_section_contact'     => __( 'Contact Form', 'aureon' ),
	'aether_section_auth'        => __( 'Login / Register', 'aureon' ),
	'aether_section_wishlist'    => __( 'Wishlist', 'aureon' ),
	'aether_section_coming_soon' => __( 'Coming Soon', 'aureon' ),
);

foreach ( $frontend_section_options as $option_key => $label ) {
	if ( ! isset( $defaults[ $option_key ] ) ) {
		continue;
	}

	Aureon_Customize_Field::add_field(
		'aureon_settings[' . $option_key . ']',
		'',
		array(
			'default'           => $defaults[ $option_key ],
			'sanitize_callback' => 'aureon_sanitize_checkbox',
			'transport'         => 'refresh',
		),
		array(
			'type'    => 'checkbox',
			'label'   => $label,
			'section' => 'aureon_aether_section',
		)
	);
}

Aureon_Customize_Field::add_title(
	'aureon_aether_shell_title',
	array(
		'section' => 'aureon_aether_section',
		'title'   => __( 'Shell & Motion', 'aureon' ),
	)
);

// Shell toggles.
$frontend_shell_options = array(
	'aether_preloader_enabled'    => __( 'Preloader', 'aureon' ),
	'aether_fog_enabled'          => __( 'Fog overlay', 'aureon' ),
	'aether_announcement_enabled' => __( 'Announcement bar', 'aureon' ),
);

foreach ( $frontend_shell_options as $option_key => $label ) {
	if ( ! isset( $defaults[ $option_key ] ) ) {
		continue;
	}

	Aureon_Customize_Field::add_field(
		'aureon_settings[' . $option_key . ']',
		'',
		array(
			'default'           => $defaults[ $option_key ],
			'sanitize_callback' => 'aureon_sanitize_checkbox',
			'transport'         => 'refresh',
		),
		array(
			'type'    => 'checkbox',
			'label'   => $label,
			'section' => 'aureon_aether_section',
		)
	);
}

// Motion toggles.
$frontend_motion_options = array(
	'aether_motion_enabled'  => __( 'Motion system', 'aureon' ),
	'aether_motion_reveal'   => __( 'Scroll reveal', 'aureon' ),
	'aether_motion_tilt'     => __( 'Card tilt', 'aureon' ),
	'aether_motion_parallax' => __( 'Parallax', 'aureon' ),
	'aether_motion_text'     => __( 'Text motion', 'aureon' ),
);

foreach ( $frontend_motion_options as $option_key => $label ) {
	if ( ! isset( $defaults[ $option_key ] ) ) {
		continue;
	}

	Aureon_Customize_Field::add_field(
		'aureon_settings[' . $option_key . ']',
		'',
		array(
			'default'           => $defaults[ $option_key ],
			'sanitize_callback' => 'aureon_sanitize_checkbox',
			'transport'         => 'refresh',
		),
		array(
			'type'    => 'checkbox',
			'label'   => $label,
			'section' => 'aureon_aether_section',
		)
	);
}

Aureon_Customize_Field::add_title(
	'aureon_aether_content_title',
	array(
		'section' => 'aureon_aether_section',
		'title'   => __( 'Announcement & Commerce', 'aureon' ),
	)
);

Aureon_Customize_Field::add_field(
	'aureon_settings[aether_announcement_text]',
	'',
	array(
		'default'           => isset( $defaults['aether_announcement_text'] ) ? $defaults['aether_announcement_text'] : '',
		'sanitize_callback' => 'sanitize_text_field',
		'transport'         => 'refresh',
	),
	array(
		'type'              => 'text',
		'label'             => __( 'Announcement text', 'aureon' ),
		'section'           => 'aureon_aether_section',
		'active_callback'   => function() {
			return (bool) aureon_get_option( 'aether_announcement_enabled', true );
		},
	)
);

Aureon_Customize_Field::add_field(
	'aureon_settings[aether_announcement_url]',
	'',
	array(
		'default'           => isset( $defaults['aether_announcement_url'] ) ? $defaults['aether_announcement_url'] : '',
		'sanitize_callback' => 'esc_url_raw',
		'transport'         => 'refresh',
	),
	array(
		'type'    => 'url',
		'label'   => __( 'Announcement link (optional)', 'aureon' ),
		'section' => 'aureon_aether_section',
	)
);

Aureon_Customize_Field::add_field(
	'aureon_settings[aether_shop_per_page]',
	'',
	array(
		'default'           => isset( $defaults['aether_shop_per_page'] ) ? $defaults['aether_shop_per_page'] : 9,
		'sanitize_callback' => 'absint',
		'transport'         => 'refresh',
	),
	array(
		'type'              => 'number',
		'label'             => __( 'Products per shop page', 'aureon' ),
		'section'           => 'aureon_aether_section',
		'input_attrs'       => array(
			'min'  => 1,
			'max'  => 48,
			'step' => 1,
		),
	)
);