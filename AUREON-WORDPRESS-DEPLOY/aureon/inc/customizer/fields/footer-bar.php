<?php
/**
 * This file handles the customizer fields for the footer bar.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

Aureon_Customize_Field::add_title(
	'aureon_footer_bar_colors_title',
	array(
		'section' => 'aureon_colors_section',
		'title' => __( 'Footer Bar', 'aureon' ),
		'choices' => array(
			'toggleId' => 'footer-bar-colors',
		),
	)
);

Aureon_Customize_Field::add_field(
	'aureon_settings[footer_background_color]',
	'Aureon_Customize_Color_Control',
	array(
		'default' => $color_defaults['footer_background_color'],
		'sanitize_callback' => 'aureon_sanitize_rgba_color',
		'transport' => 'postMessage',
	),
	array(
		'label' => __( 'Background', 'aureon' ),
		'section' => 'aureon_colors_section',
		'choices' => array(
			'alpha' => true,
			'toggleId' => 'footer-bar-colors',
			'wrapper' => 'footer_background_color',
			'tooltip' => __( 'Choose Initial Color', 'aureon' ),
		),
		'output' => array(
			array(
				'element'  => '.site-info',
				'property' => 'background-color',
			),
		),
	)
);

Aureon_Customize_Field::add_field(
	'aureon_settings[footer_text_color]',
	'Aureon_Customize_Color_Control',
	array(
		'default' => $color_defaults['footer_text_color'],
		'sanitize_callback' => 'aureon_sanitize_hex_color',
		'transport' => 'postMessage',
	),
	array(
		'label' => __( 'Text', 'aureon' ),
		'section' => 'aureon_colors_section',
		'choices' => array(
			'toggleId' => 'footer-bar-colors',
			'wrapper' => 'footer_text_color',
			'tooltip' => __( 'Choose Initial Color', 'aureon' ),
		),
		'output' => array(
			array(
				'element'  => '.site-info',
				'property' => 'color',
			),
		),
	)
);

Aureon_Customize_Field::add_wrapper(
	'aureon_footer_bar_colors_wrapper',
	array(
		'section' => 'aureon_colors_section',
		'choices' => array(
			'type' => 'color',
			'toggleId' => 'footer-bar-colors',
			'items' => array(
				'footer_link_color',
				'footer_link_hover_color',
			),
		),
	)
);

Aureon_Customize_Field::add_field(
	'aureon_settings[footer_link_color]',
	'Aureon_Customize_Color_Control',
	array(
		'default' => $color_defaults['footer_link_color'],
		'sanitize_callback' => 'aureon_sanitize_hex_color',
		'transport' => 'postMessage',
	),
	array(
		'label' => __( 'Link', 'aureon' ),
		'section' => 'aureon_colors_section',
		'choices' => array(
			'toggleId' => 'footer-bar-colors',
			'wrapper' => 'footer_link_color',
			'tooltip' => __( 'Choose Initial Color', 'aureon' ),
		),
		'output' => array(
			array(
				'element'  => '.site-info a',
				'property' => 'color',
			),
		),
	)
);

Aureon_Customize_Field::add_field(
	'aureon_settings[footer_link_hover_color]',
	'Aureon_Customize_Color_Control',
	array(
		'default' => $color_defaults['footer_link_hover_color'],
		'sanitize_callback' => 'aureon_sanitize_hex_color',
		'transport' => 'postMessage',
	),
	array(
		'label' => __( 'Link Hover', 'aureon' ),
		'section' => 'aureon_colors_section',
		'choices' => array(
			'toggleId' => 'footer-bar-colors',
			'wrapper' => 'footer_link_hover_color',
			'tooltip' => __( 'Choose Hover Color', 'aureon' ),
			'hideLabel' => true,
		),
		'output' => array(
			array(
				'element'  => '.site-info a:hover',
				'property' => 'color',
			),
		),
	)
);
