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

// ──────────────────────────────────────────────────────────────────
// Design — Colors (G2/G3): AETHER-native color controls seeded from
// the theme palette. Empty value = inherit the theme palette (or the
// AETHER default) — see aether_resolve_color() in inc/aether-tokens.php.
// ──────────────────────────────────────────────────────────────────
Aureon_Customize_Field::add_title(
	'aureon_aether_design_colors_title',
	array(
		'section' => 'aureon_aether_section',
		'title'   => __( 'Design — Colors', 'aureon' ),
	)
);

$aether_color_controls = array(
	'aether_color_bg'           => __( 'Background', 'aureon' ),
	'aether_color_surface'      => __( 'Surface', 'aureon' ),
	'aether_color_surface_2'    => __( 'Surface 2', 'aureon' ),
	'aether_color_surface_3'    => __( 'Surface 3', 'aureon' ),
	'aether_color_text'         => __( 'Text', 'aureon' ),
	'aether_color_muted'        => __( 'Muted', 'aureon' ),
	'aether_color_accent'       => __( 'Accent', 'aureon' ),
	'aether_color_accent_hover' => __( 'Accent hover', 'aureon' ),
	'aether_color_border'       => __( 'Border', 'aureon' ),
	'aether_color_error'        => __( 'Error', 'aureon' ),
	'aether_color_success'      => __( 'Success', 'aureon' ),
);

foreach ( $aether_color_controls as $option_key => $label ) {
	if ( ! isset( $defaults[ $option_key ] ) ) {
		continue;
	}

	Aureon_Customize_Field::add_field(
		'aureon_settings[' . $option_key . ']',
		'Aureon_Customize_Color_Control',
		array(
			'default'           => '',
			'sanitize_callback' => 'aureon_sanitize_hex_color',
			'transport'         => 'refresh',
		),
		array(
			'label'       => $label,
			'section'     => 'aureon_aether_section',
			'settings'    => 'aureon_settings[' . $option_key . ']',
			'description' => __( 'Leave empty to inherit the theme palette.', 'aureon' ),
			'choices'     => array(
				'alpha' => true,
			),
		)
	);
}

// ──────────────────────────────────────────────────────────────────
// Design — Layout (G4): token-driven sizes exposed as range sliders.
// Values are emitted by inc/aether-tokens.php as :root CSS variables.
// ──────────────────────────────────────────────────────────────────
Aureon_Customize_Field::add_title(
	'aureon_aether_design_layout_title',
	array(
		'section' => 'aureon_aether_section',
		'title'   => __( 'Design — Layout', 'aureon' ),
	)
);

$layout_sliders = array(
	'aether_container_max'       => array(
		'label'   => __( 'Container max width', 'aureon' ),
		'min'     => 960,
		'max'     => 1920,
		'step'    => 10,
		'unit'    => 'px',
		'default' => '1200',
	),
	'aether_announcement_height' => array(
		'label'   => __( 'Announcement bar height', 'aureon' ),
		'min'     => 32,
		'max'     => 80,
		'step'    => 2,
		'unit'    => 'px',
		'default' => '40',
	),
	'aether_header_height'       => array(
		'label'   => __( 'Header height', 'aureon' ),
		'min'     => 60,
		'max'     => 120,
		'step'    => 2,
		'unit'    => 'px',
		'default' => '80',
	),
	'aether_grid_gap'            => array(
		'label'   => __( 'Grid gap', 'aureon' ),
		'min'     => 8,
		'max'     => 48,
		'step'    => 2,
		'unit'    => 'px',
		'default' => '24',
	),
	'aether_radius_sm'           => array(
		'label'   => __( 'Radius — small', 'aureon' ),
		'min'     => 0,
		'max'     => 20,
		'step'    => 1,
		'unit'    => 'px',
		'default' => '8',
	),
	'aether_radius_md'           => array(
		'label'   => __( 'Radius — medium', 'aureon' ),
		'min'     => 0,
		'max'     => 40,
		'step'    => 1,
		'unit'    => 'px',
		'default' => '12',
	),
	'aether_radius_lg'           => array(
		'label'   => __( 'Radius — large', 'aureon' ),
		'min'     => 0,
		'max'     => 60,
		'step'    => 1,
		'unit'    => 'px',
		'default' => '24',
	),
);

foreach ( $layout_sliders as $option_key => $cfg ) {
	$id = 'aureon_settings[' . $option_key . ']';

	$wp_customize->add_setting(
		$id,
		array(
			'default'           => $cfg['default'],
			'type'              => 'option',
			'sanitize_callback' => 'aureon_sanitize_integer',
			'transport'         => 'refresh',
		)
	);

	$wp_customize->add_control(
		new Aureon_Range_Slider_Control(
			$wp_customize,
			$id,
			array(
				'type'     => 'aureon-range-slider',
				'label'    => $cfg['label'],
				'section'  => 'aureon_aether_section',
				'settings' => array(
					'desktop' => $id,
				),
				'choices'  => array(
					'desktop' => array(
						'min'  => $cfg['min'],
						'max'  => $cfg['max'],
						'step' => $cfg['step'],
						'edit' => true,
						'unit' => $cfg['unit'],
					),
				),
			)
		)
	);
}

Aureon_Customize_Field::add_field(
	'aureon_settings[aether_section_padding]',
	'',
	array(
		'default'           => isset( $defaults['aether_section_padding'] ) ? $defaults['aether_section_padding'] : '100px 0',
		'sanitize_callback' => 'sanitize_text_field',
		'transport'         => 'refresh',
	),
	array(
		'type'        => 'text',
		'label'       => __( 'Section padding (e.g. 100px 0)', 'aureon' ),
		'section'     => 'aureon_aether_section',
		'description' => __( 'Vertical rhythm for front-page sections.', 'aureon' ),
	)
);