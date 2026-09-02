<?php
/**
 * This file handles the customizer fields for the Body.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

Aureon_Customize_Field::add_title(
	'aureon_forms_colors_title',
	array(
		'section' => 'aureon_colors_section',
		'title' => __( 'Forms', 'aureon' ),
		'choices' => array(
			'toggleId' => 'form-colors',
		),
	)
);

Aureon_Customize_Field::add_wrapper(
	'aureon_forms_background_wrapper',
	array(
		'section' => 'aureon_colors_section',
		'choices' => array(
			'type' => 'color',
			'toggleId' => 'form-colors',
			'items' => array(
				'form_background_color',
				'form_background_color_focus',
			),
		),
	)
);

$forms_selector = 'input[type="text"], input[type="email"], input[type="url"], input[type="password"], input[type="search"], input[type="number"], input[type="tel"], textarea, select';
$forms_focus_selector = 'input[type="text"]:focus, input[type="email"]:focus, input[type="url"]:focus, input[type="password"]:focus, input[type="search"]:focus, input[type="number"]:focus, input[type="tel"]:focus, textarea:focus, select:focus';

Aureon_Customize_Field::add_field(
	'aureon_settings[form_background_color]',
	'Aureon_Customize_Color_Control',
	array(
		'default' => $color_defaults['form_background_color'],
		'sanitize_callback' => 'aureon_sanitize_rgba_color',
		'transport' => 'postMessage',
	),
	array(
		'label' => __( 'Background', 'aureon' ),
		'section' => 'aureon_colors_section',
		'choices' => array(
			'alpha' => true,
			'toggleId' => 'form-colors',
			'wrapper' => 'form_background_color',
			'tooltip' => __( 'Choose Initial Color', 'aureon' ),
		),
		'output' => array(
			array(
				'element'  => $forms_selector,
				'property' => 'background-color',
			),
		),
	)
);

Aureon_Customize_Field::add_field(
	'aureon_settings[form_background_color_focus]',
	'Aureon_Customize_Color_Control',
	array(
		'default' => $color_defaults['form_background_color_focus'],
		'sanitize_callback' => 'aureon_sanitize_rgba_color',
		'transport' => 'postMessage',
	),
	array(
		'label' => __( 'Background Focus', 'aureon' ),
		'section' => 'aureon_colors_section',
		'choices' => array(
			'alpha' => true,
			'toggleId' => 'form-colors',
			'wrapper' => 'form_background_color_focus',
			'tooltip' => __( 'Choose Focus Color', 'aureon' ),
			'hideLabel' => true,
		),
		'output' => array(
			array(
				'element'  => $forms_focus_selector,
				'property' => 'background-color',
			),
		),
	)
);

Aureon_Customize_Field::add_wrapper(
	'aureon_forms_text_wrapper',
	array(
		'section' => 'aureon_colors_section',
		'choices' => array(
			'type' => 'color',
			'toggleId' => 'form-colors',
			'items' => array(
				'form_text_color',
				'form_text_color_focus',
			),
		),
	)
);

Aureon_Customize_Field::add_field(
	'aureon_settings[form_text_color]',
	'Aureon_Customize_Color_Control',
	array(
		'default' => $color_defaults['form_text_color'],
		'sanitize_callback' => 'aureon_sanitize_hex_color',
		'transport' => 'postMessage',
	),
	array(
		'label' => __( 'Text', 'aureon' ),
		'section' => 'aureon_colors_section',
		'choices' => array(
			'toggleId' => 'form-colors',
			'wrapper' => 'form_text_color',
			'tooltip' => __( 'Choose Initial Color', 'aureon' ),
		),
		'output' => array(
			array(
				'element'  => $forms_selector,
				'property' => 'color',
			),
		),
	)
);

Aureon_Customize_Field::add_field(
	'aureon_settings[form_text_color_focus]',
	'Aureon_Customize_Color_Control',
	array(
		'default' => $color_defaults['form_text_color_focus'],
		'sanitize_callback' => 'aureon_sanitize_hex_color',
		'transport' => 'postMessage',
	),
	array(
		'label' => __( 'Text Focus', 'aureon' ),
		'section' => 'aureon_colors_section',
		'choices' => array(
			'toggleId' => 'form-colors',
			'wrapper' => 'form_text_color_focus',
			'tooltip' => __( 'Choose Focus Color', 'aureon' ),
			'hideLabel' => true,
		),
		'output' => array(
			array(
				'element'  => $forms_focus_selector,
				'property' => 'color',
			),
		),
	)
);

Aureon_Customize_Field::add_wrapper(
	'aureon_forms_border_wrapper',
	array(
		'section' => 'aureon_colors_section',
		'choices' => array(
			'type' => 'color',
			'toggleId' => 'form-colors',
			'items' => array(
				'form_border_color',
				'form_border_color_focus',
			),
		),
	)
);

Aureon_Customize_Field::add_field(
	'aureon_settings[form_border_color]',
	'Aureon_Customize_Color_Control',
	array(
		'default' => $color_defaults['form_border_color'],
		'sanitize_callback' => 'aureon_sanitize_rgba_color',
		'transport' => 'postMessage',
	),
	array(
		'label' => __( 'Border', 'aureon' ),
		'section' => 'aureon_colors_section',
		'choices' => array(
			'alpha' => true,
			'toggleId' => 'form-colors',
			'wrapper' => 'form_border_color',
			'tooltip' => __( 'Choose Initial Color', 'aureon' ),
		),
		'output' => array(
			array(
				'element'  => $forms_selector,
				'property' => 'border-color',
			),
		),
	)
);

Aureon_Customize_Field::add_field(
	'aureon_settings[form_border_color_focus]',
	'Aureon_Customize_Color_Control',
	array(
		'default' => $color_defaults['form_border_color_focus'],
		'sanitize_callback' => 'aureon_sanitize_rgba_color',
		'transport' => 'postMessage',
	),
	array(
		'label' => __( 'Border Focus', 'aureon' ),
		'section' => 'aureon_colors_section',
		'choices' => array(
			'alpha' => true,
			'toggleId' => 'form-colors',
			'wrapper' => 'form_border_color_focus',
			'tooltip' => __( 'Choose Focus Color', 'aureon' ),
			'hideLabel' => true,
		),
		'output' => array(
			array(
				'element'  => $forms_focus_selector,
				'property' => 'border-color',
			),
		),
	)
);
