<?php
/**
 * This file handles the customizer fields for the back to top button.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

Aureon_Customize_Field::add_title(
	'aureon_back_to_top_colors_title',
	array(
		'section' => 'aureon_colors_section',
		'title' => __( 'Back to Top', 'aureon' ),
		'choices' => array(
			'toggleId' => 'back-to-top-colors',
		),
		'active_callback' => function() {
			if ( aureon_get_option( 'back_to_top' ) ) {
				return true;
			}

			return false;
		},
	)
);

Aureon_Customize_Field::add_wrapper(
	'aureon_back_to_top_background_wrapper',
	array(
		'section' => 'aureon_colors_section',
		'choices' => array(
			'type' => 'color',
			'toggleId' => 'back-to-top-colors',
			'items' => array(
				'back_to_top_background_color',
				'back_to_top_background_color_hover',
			),
		),
	)
);

Aureon_Customize_Field::add_field(
	'aureon_settings[back_to_top_background_color]',
	'Aureon_Customize_Color_Control',
	array(
		'default' => $color_defaults['back_to_top_background_color'],
		'sanitize_callback' => 'aureon_sanitize_rgba_color',
		'transport' => 'postMessage',
	),
	array(
		'label' => __( 'Background', 'aureon' ),
		'section' => 'aureon_colors_section',
		'choices' => array(
			'alpha' => true,
			'toggleId' => 'back-to-top-colors',
			'wrapper' => 'back_to_top_background_color',
			'tooltip' => __( 'Choose Initial Color', 'aureon' ),
		),
		'output' => array(
			array(
				'element'  => 'a.aureon-back-to-top',
				'property' => 'background-color',
			),
		),
	)
);

Aureon_Customize_Field::add_field(
	'aureon_settings[back_to_top_background_color_hover]',
	'Aureon_Customize_Color_Control',
	array(
		'default' => $color_defaults['back_to_top_background_color_hover'],
		'sanitize_callback' => 'aureon_sanitize_rgba_color',
		'transport' => 'postMessage',
	),
	array(
		'label' => __( 'Background Hover', 'aureon' ),
		'section' => 'aureon_colors_section',
		'choices' => array(
			'alpha' => true,
			'toggleId' => 'back-to-top-colors',
			'wrapper' => 'back_to_top_background_color_hover',
			'tooltip' => __( 'Choose Hover Color', 'aureon' ),
			'hideLabel' => true,
		),
		'output' => array(
			array(
				'element'  => 'a.aureon-back-to-top:hover, a.aureon-back-to-top:focus',
				'property' => 'background-color',
			),
		),
	)
);

Aureon_Customize_Field::add_wrapper(
	'aureon_back_to_top_text_wrapper',
	array(
		'section' => 'aureon_colors_section',
		'choices' => array(
			'type' => 'color',
			'toggleId' => 'back-to-top-colors',
			'items' => array(
				'back_to_top_text_color',
				'back_to_top_text_color_hover',
			),
		),
	)
);

Aureon_Customize_Field::add_field(
	'aureon_settings[back_to_top_text_color]',
	'Aureon_Customize_Color_Control',
	array(
		'default' => $color_defaults['back_to_top_text_color'],
		'sanitize_callback' => 'aureon_sanitize_hex_color',
		'transport' => 'postMessage',
	),
	array(
		'label' => __( 'Text', 'aureon' ),
		'section' => 'aureon_colors_section',
		'choices' => array(
			'toggleId' => 'button-colors',
			'wrapper' => 'back_to_top_text_color',
			'tooltip' => __( 'Choose Initial Color', 'aureon' ),
		),
		'output' => array(
			array(
				'element'  => 'a.aureon-back-to-top',
				'property' => 'color',
			),
		),
	)
);

Aureon_Customize_Field::add_field(
	'aureon_settings[back_to_top_text_color_hover]',
	'Aureon_Customize_Color_Control',
	array(
		'default' => $color_defaults['back_to_top_text_color_hover'],
		'sanitize_callback' => 'aureon_sanitize_hex_color',
		'transport' => 'postMessage',
	),
	array(
		'label' => __( 'Text Hover', 'aureon' ),
		'section' => 'aureon_colors_section',
		'choices' => array(
			'toggleId' => 'back-to-top-colors',
			'wrapper' => 'back_to_top_text_color_hover',
			'tooltip' => __( 'Choose Hover Color', 'aureon' ),
			'hideLabel' => true,
		),
		'output' => array(
			array(
				'element'  => 'a.aureon-back-to-top:hover, a.aureon-back-to-top:focus',
				'property' => 'color',
			),
		),
	)
);
