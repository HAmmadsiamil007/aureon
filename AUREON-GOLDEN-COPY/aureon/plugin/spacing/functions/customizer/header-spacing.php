<?php
/**
 * This file handles the header spacing Customizer options.
 *
 * @package Aureon Studio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

// Add our old header section.
$wp_customize->add_section(
	'aureon_spacing_header',
	array(
		'title' => __( 'Header', 'aureon-studio' ),
		'capability' => 'edit_theme_options',
		'priority' => 5,
		'panel' => 'aureon_spacing_panel',
	)
);

// If we don't have a layout panel, use our old spacing section.
$header_section = ( $wp_customize->get_panel( 'aureon_layout_panel' ) ) ? 'aureon_layout_header' : 'aureon_spacing_header';

// Header top.
$wp_customize->add_setting(
	'aureon_spacing_settings[header_top]',
	array(
		'default' => $defaults['header_top'],
		'type' => 'option',
		'sanitize_callback' => 'absint',
		'transport' => 'postMessage',
	)
);

// Header right.
$wp_customize->add_setting(
	'aureon_spacing_settings[header_right]',
	array(
		'default' => $defaults['header_right'],
		'type' => 'option',
		'sanitize_callback' => 'absint',
		'transport' => 'postMessage',
	)
);

// Header bottom.
$wp_customize->add_setting(
	'aureon_spacing_settings[header_bottom]',
	array(
		'default' => $defaults['header_bottom'],
		'type' => 'option',
		'sanitize_callback' => 'absint',
		'transport' => 'postMessage',
	)
);

// Header left.
$wp_customize->add_setting(
	'aureon_spacing_settings[header_left]',
	array(
		'default' => $defaults['header_left'],
		'type' => 'option',
		'sanitize_callback' => 'absint',
		'transport' => 'postMessage',
	)
);

$wp_customize->add_setting(
	'aureon_spacing_settings[mobile_header_top]',
	array(
		'default' => $defaults['mobile_header_top'],
		'type' => 'option',
		'sanitize_callback' => 'absint',
		'transport' => 'postMessage',
	)
);

// Header right.
$wp_customize->add_setting(
	'aureon_spacing_settings[mobile_header_right]',
	array(
		'default' => $defaults['mobile_header_right'],
		'type' => 'option',
		'sanitize_callback' => 'absint',
		'transport' => 'postMessage',
	)
);

// Header bottom.
$wp_customize->add_setting(
	'aureon_spacing_settings[mobile_header_bottom]',
	array(
		'default' => $defaults['mobile_header_bottom'],
		'type' => 'option',
		'sanitize_callback' => 'absint',
		'transport' => 'postMessage',
	)
);

// Header left.
$wp_customize->add_setting(
	'aureon_spacing_settings[mobile_header_left]',
	array(
		'default' => $defaults['mobile_header_left'],
		'type' => 'option',
		'sanitize_callback' => 'absint',
		'transport' => 'postMessage',
	)
);

// Do something with our header controls.
$wp_customize->add_control(
	new Aureon_Spacing_Control(
		$wp_customize,
		'header_spacing',
		array(
			'type' => 'aureon-spacing',
			'label'       => esc_html__( 'Header Padding', 'aureon-studio' ),
			'section'     => $header_section,
			'settings'    => array(
				'desktop_top'    => 'aureon_spacing_settings[header_top]',
				'desktop_right'  => 'aureon_spacing_settings[header_right]',
				'desktop_bottom' => 'aureon_spacing_settings[header_bottom]',
				'desktop_left'   => 'aureon_spacing_settings[header_left]',
				'mobile_top'     => 'aureon_spacing_settings[mobile_header_top]',
				'mobile_right'   => 'aureon_spacing_settings[mobile_header_right]',
				'mobile_bottom'  => 'aureon_spacing_settings[mobile_header_bottom]',
				'mobile_left'    => 'aureon_spacing_settings[mobile_header_left]',
			),
			'element' => 'header',
		)
	)
);
