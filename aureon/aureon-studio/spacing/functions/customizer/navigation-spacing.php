<?php
/**
 * This file handles the navigation spacing Customizer options.
 *
 * @package Aureon Studio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

// Add our old navigation section.
$wp_customize->add_section(
	'aureon_spacing_navigation',
	array(
		'title' => __( 'Primary Navigation', 'aureon-studio' ),
		'capability' => 'edit_theme_options',
		'priority' => 15,
		'panel' => 'aureon_spacing_panel',
	)
);

// If our new Layout section doesn't exist, use the old navigation section.
$navigation_section = ( $wp_customize->get_panel( 'aureon_layout_panel' ) ) ? 'aureon_layout_navigation' : 'aureon_spacing_navigation';

// Menu item width.
$wp_customize->add_setting(
	'aureon_spacing_settings[menu_item]',
	array(
		'default' => $defaults['menu_item'],
		'type' => 'option',
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'absint',
		'transport' => 'postMessage',
	)
);

$wp_customize->add_setting(
	'aureon_spacing_settings[mobile_menu_item]',
	array(
		'default' => $defaults['mobile_menu_item'],
		'type' => 'option',
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'aureon_premium_sanitize_empty_absint',
		'transport' => 'postMessage',
	)
);

$wp_customize->add_control(
	new Aureon_Pro_Range_Slider_Control(
		$wp_customize,
		'aureon_spacing_settings[menu_item]',
		array(
			'label' => __( 'Menu Item Width', 'aureon-studio' ),
			'section' => $navigation_section,
			'settings' => array(
				'desktop' => 'aureon_spacing_settings[menu_item]',
				'mobile' => 'aureon_spacing_settings[mobile_menu_item]',
			),
			'choices' => array(
				'desktop' => array(
					'min' => 0,
					'max' => 100,
					'step' => 1,
					'edit' => true,
					'unit' => 'px',
				),
				'mobile' => array(
					'min' => 0,
					'max' => 100,
					'step' => 1,
					'edit' => true,
					'unit' => 'px',
				),
			),
			'priority' => 220,
		)
	)
);

// Menu item height.
$wp_customize->add_setting(
	'aureon_spacing_settings[menu_item_height]',
	array(
		'default' => $defaults['menu_item_height'],
		'type' => 'option',
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'absint',
		'transport' => 'postMessage',
	)
);

$wp_customize->add_setting(
	'aureon_spacing_settings[mobile_menu_item_height]',
	array(
		'default' => $defaults['mobile_menu_item_height'],
		'type' => 'option',
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'aureon_premium_sanitize_empty_absint',
		'transport' => 'postMessage',
	)
);

$wp_customize->add_control(
	new Aureon_Pro_Range_Slider_Control(
		$wp_customize,
		'aureon_spacing_settings[menu_item_height]',
		array(
			'label' => __( 'Menu Item Height', 'aureon-studio' ),
			'section' => $navigation_section,
			'settings' => array(
				'desktop' => 'aureon_spacing_settings[menu_item_height]',
				'mobile' => 'aureon_spacing_settings[mobile_menu_item_height]',
			),
			'choices' => array(
				'desktop' => array(
					'min' => 20,
					'max' => 150,
					'step' => 1,
					'edit' => true,
					'unit' => 'px',
				),
				'mobile' => array(
					'min' => 20,
					'max' => 150,
					'step' => 1,
					'edit' => true,
					'unit' => 'px',
				),
			),
			'priority' => 240,
		)
	)
);

// Sub-menu item height.
$wp_customize->add_setting(
	'aureon_spacing_settings[sub_menu_item_height]',
	array(
		'default' => $defaults['sub_menu_item_height'],
		'type' => 'option',
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'absint',
		'transport' => 'postMessage',
	)
);

$wp_customize->add_control(
	new Aureon_Pro_Range_Slider_Control(
		$wp_customize,
		'aureon_spacing_settings[sub_menu_item_height]',
		array(
			'label' => __( 'Sub-Menu Item Height', 'aureon-studio' ),
			'section' => $navigation_section,
			'settings' => array(
				'desktop' => 'aureon_spacing_settings[sub_menu_item_height]',
			),
			'choices' => array(
				'desktop' => array(
					'min' => 0,
					'max' => 50,
					'step' => 1,
					'edit' => true,
					'unit' => 'px',
				),
			),
			'priority' => 260,
		)
	)
);

if ( isset( $defaults['sub_menu_width'] ) ) {
	$wp_customize->add_setting(
		'aureon_spacing_settings[sub_menu_width]',
		array(
			'default' => $defaults['sub_menu_width'],
			'type' => 'option',
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'absint',
			'transport' => 'postMessage',
		)
	);

	$wp_customize->add_control(
		new Aureon_Pro_Range_Slider_Control(
			$wp_customize,
			'aureon_spacing_settings[sub_menu_width]',
			array(
				'label' => __( 'Sub-Menu Width', 'aureon-studio' ),
				'section' => $navigation_section,
				'settings' => array(
					'desktop' => 'aureon_spacing_settings[sub_menu_width]',
				),
				'choices' => array(
					'desktop' => array(
						'min' => 100,
						'max' => 500,
						'step' => 1,
						'edit' => true,
						'unit' => 'px',
					),
				),
				'priority' => 265,
			)
		)
	);
}

// Sticky menu height.
$wp_customize->add_setting(
	'aureon_spacing_settings[sticky_menu_item_height]',
	array(
		'default' => $defaults['sticky_menu_item_height'],
		'type' => 'option',
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'aureon_premium_sanitize_empty_absint',
		'transport' => 'postMessage',
	)
);

$wp_customize->add_control(
	new Aureon_Pro_Range_Slider_Control(
		$wp_customize,
		'aureon_spacing_settings[sticky_menu_item_height]',
		array(
			'label' => __( 'Menu Item Height', 'aureon-studio' ),
			'section' => 'menu_plus_sticky_menu',
			'settings' => array(
				'desktop' => 'aureon_spacing_settings[sticky_menu_item_height]',
			),
			'choices' => array(
				'desktop' => array(
					'min' => 20,
					'max' => 150,
					'step' => 1,
					'edit' => true,
					'unit' => 'px',
				),
			),
			'priority' => 150,
			'active_callback' => 'aureon_sticky_navigation_activated',
		)
	)
);

// Off canvas menu height.
$wp_customize->add_setting(
	'aureon_spacing_settings[off_canvas_menu_item_height]',
	array(
		'default' => $defaults['off_canvas_menu_item_height'],
		'type' => 'option',
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'aureon_premium_sanitize_empty_absint',
		'transport' => 'postMessage',
	)
);

$wp_customize->add_control(
	new Aureon_Pro_Range_Slider_Control(
		$wp_customize,
		'aureon_spacing_settings[off_canvas_menu_item_height]',
		array(
			'label' => __( 'Menu Item Height', 'aureon-studio' ),
			'section' => 'menu_plus_slideout_menu',
			'settings' => array(
				'desktop' => 'aureon_spacing_settings[off_canvas_menu_item_height]',
			),
			'choices' => array(
				'desktop' => array(
					'min' => 20,
					'max' => 150,
					'step' => 1,
					'edit' => true,
					'unit' => 'px',
				),
			),
			'priority' => 200,
			'active_callback' => 'aureon_slideout_navigation_activated',
		)
	)
);
