<?php
/**
 * This file handles the customizer fields for the top bar.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

Aureon_Customize_Field::add_title(
	'aureon_top_bar_colors_title',
	array(
		'section' => 'aureon_colors_section',
		'title' => __( 'Top Bar', 'aureon' ),
		'choices' => array(
			'toggleId' => 'top-bar-colors',
		),
	)
);

Aureon_Customize_Field::add_field(
	'aureon_settings[top_bar_background_color]',
	'Aureon_Customize_Color_Control',
	array(
		'default' => $color_defaults['top_bar_background_color'],
		'transport' => 'postMessage',
		'sanitize_callback' => 'aureon_sanitize_rgba_color',
	),
	array(
		'label' => __( 'Background', 'aureon' ),
		'section' => 'aureon_colors_section',
		'settings' => 'aureon_settings[top_bar_background_color]',
		'active_callback' => 'aureon_is_top_bar_active',
		'choices' => array(
			'alpha' => true,
			'toggleId' => 'top-bar-colors',
		),
		'output' => array(
			array(
				'element'  => '.top-bar',
				'property' => 'background-color',
			),
		),
	)
);

Aureon_Customize_Field::add_field(
	'aureon_settings[top_bar_text_color]',
	'Aureon_Customize_Color_Control',
	array(
		'default' => $color_defaults['top_bar_text_color'],
		'transport' => 'postMessage',
		'sanitize_callback' => 'aureon_sanitize_rgba_color',
	),
	array(
		'label' => __( 'Text', 'aureon' ),
		'section' => 'aureon_colors_section',
		'active_callback' => 'aureon_is_top_bar_active',
		'choices' => array(
			'toggleId' => 'top-bar-colors',
		),
		'output' => array(
			array(
				'element'  => '.top-bar',
				'property' => 'color',
			),
		),
	)
);

Aureon_Customize_Field::add_wrapper(
	'aureon_top_bar_link_wrapper',
	array(
		'section' => 'aureon_colors_section',
		'choices' => array(
			'type' => 'color',
			'toggleId' => 'top-bar-colors',
			'items' => array(
				'top_bar_link_color',
				'top_bar_link_color_hover',
			),
		),
	)
);

Aureon_Customize_Field::add_field(
	'aureon_settings[top_bar_link_color]',
	'Aureon_Customize_Color_Control',
	array(
		'default' => $color_defaults['top_bar_link_color'],
		'transport' => 'postMessage',
		'sanitize_callback' => 'aureon_sanitize_rgba_color',
	),
	array(
		'label' => __( 'Link', 'aureon' ),
		'section' => 'aureon_colors_section',
		'active_callback' => 'aureon_is_top_bar_active',
		'choices' => array(
			'wrapper' => 'top_bar_link_color',
			'tooltip' => __( 'Choose Initial Color', 'aureon' ),
			'toggleId' => 'top-bar-colors',
		),
		'output' => array(
			array(
				'element'  => '.top-bar a',
				'property' => 'color',
			),
		),
	)
);

Aureon_Customize_Field::add_field(
	'aureon_settings[top_bar_link_color_hover]',
	'Aureon_Customize_Color_Control',
	array(
		'default' => $color_defaults['top_bar_link_color_hover'],
		'transport' => 'postMessage',
		'sanitize_callback' => 'aureon_sanitize_rgba_color',
	),
	array(
		'label' => __( 'Link Hover', 'aureon' ),
		'section' => 'aureon_colors_section',
		'active_callback' => 'aureon_is_top_bar_active',
		'choices' => array(
			'wrapper' => 'top_bar_link_color_hover',
			'tooltip' => __( 'Choose Hover Color', 'aureon' ),
			'toggleId' => 'top-bar-colors',
			'hideLabel' => true,
		),
		'output' => array(
			array(
				'element'  => '.top-bar a:hover',
				'property' => 'color',
			),
		),
	)
);
