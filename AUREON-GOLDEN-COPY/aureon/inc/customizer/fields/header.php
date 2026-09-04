<?php
/**
 * This file handles the customizer fields for the header.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

Aureon_Customize_Field::add_title(
	'aureon_header_colors_title',
	array(
		'section' => 'aureon_colors_section',
		'title' => __( 'Header', 'aureon' ),
		'choices' => array(
			'toggleId' => 'header-colors',
		),
	)
);

Aureon_Customize_Field::add_field(
	'aureon_settings[header_background_color]',
	'Aureon_Customize_Color_Control',
	array(
		'default' => $color_defaults['header_background_color'],
		'transport' => 'postMessage',
		'sanitize_callback' => 'aureon_sanitize_rgba_color',
	),
	array(
		'label' => __( 'Background', 'aureon' ),
		'section' => 'aureon_colors_section',
		'choices' => array(
			'alpha' => true,
			'toggleId' => 'header-colors',
		),
		'output' => array(
			array(
				'element'  => '.site-header',
				'property' => 'background-color',
			),
		),
	)
);

Aureon_Customize_Field::add_field(
	'aureon_settings[header_text_color]',
	'Aureon_Customize_Color_Control',
	array(
		'default' => $color_defaults['header_text_color'],
		'transport' => 'postMessage',
		'sanitize_callback' => 'aureon_sanitize_rgba_color',
	),
	array(
		'label' => __( 'Text', 'aureon' ),
		'section' => 'aureon_colors_section',
		'choices' => array(
			'toggleId' => 'header-colors',
		),
		'output' => array(
			array(
				'element'  => '.site-header',
				'property' => 'color',
			),
		),
	)
);

Aureon_Customize_Field::add_wrapper(
	'aureon_header_link_wrapper',
	array(
		'section' => 'aureon_colors_section',
		'choices' => array(
			'type' => 'color',
			'toggleId' => 'header-colors',
			'items' => array(
				'header_link_color',
				'header_link_hover_color',
			),
		),
	)
);

Aureon_Customize_Field::add_field(
	'aureon_settings[header_link_color]',
	'Aureon_Customize_Color_Control',
	array(
		'default' => $color_defaults['header_link_color'],
		'transport' => 'postMessage',
		'sanitize_callback' => 'aureon_sanitize_rgba_color',
	),
	array(
		'label' => __( 'Link', 'aureon' ),
		'section' => 'aureon_colors_section',
		'choices' => array(
			'toggleId' => 'header-colors',
			'wrapper' => 'header_link_color',
			'tooltip' => __( 'Choose Initial Color', 'aureon' ),
		),
		'output' => array(
			array(
				'element'  => '.site-header a:not([rel="home"])',
				'property' => 'color',
			),
		),
	)
);

Aureon_Customize_Field::add_field(
	'aureon_settings[header_link_hover_color]',
	'Aureon_Customize_Color_Control',
	array(
		'default' => $color_defaults['header_link_hover_color'],
		'transport' => 'postMessage',
		'sanitize_callback' => 'aureon_sanitize_rgba_color',
	),
	array(
		'label' => __( 'Link Hover', 'aureon' ),
		'section' => 'aureon_colors_section',
		'choices' => array(
			'toggleId' => 'header-colors',
			'wrapper' => 'header_link_hover_color',
			'tooltip' => __( 'Choose Hover Color', 'aureon' ),
			'hideLabel' => true,
		),
		'output' => array(
			array(
				'element'  => '.site-header a:not([rel="home"]):hover',
				'property' => 'color',
			),
		),
	)
);

Aureon_Customize_Field::add_field(
	'aureon_settings[site_title_color]',
	'Aureon_Customize_Color_Control',
	array(
		'default' => $color_defaults['site_title_color'],
		'transport' => 'postMessage',
		'sanitize_callback' => 'aureon_sanitize_rgba_color',
	),
	array(
		'label' => __( 'Site Title', 'aureon' ),
		'section' => 'aureon_colors_section',
		'choices' => array(
			'toggleId' => 'header-colors',
		),
		'output' => array(
			array(
				'element'  => '.main-title a, .main-title a:hover',
				'property' => 'color',
			),
		),
	)
);

Aureon_Customize_Field::add_field(
	'aureon_settings[site_tagline_color]',
	'Aureon_Customize_Color_Control',
	array(
		'default' => $color_defaults['site_tagline_color'],
		'transport' => 'postMessage',
		'sanitize_callback' => 'aureon_sanitize_rgba_color',
	),
	array(
		'label' => __( 'Tagline', 'aureon' ),
		'section' => 'aureon_colors_section',
		'choices' => array(
			'toggleId' => 'header-colors',
		),
		'output' => array(
			array(
				'element'  => '.site-description',
				'property' => 'color',
			),
		),
	)
);
