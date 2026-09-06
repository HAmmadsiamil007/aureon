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
	'aureon_body_colors_title',
	array(
		'section' => 'aureon_colors_section',
		'title' => __( 'Body', 'aureon' ),
		'choices' => array(
			'toggleId' => 'base-colors',
		),
	)
);

Aureon_Customize_Field::add_field(
	'aureon_settings[background_color]',
	'Aureon_Customize_Color_Control',
	array(
		'default' => $defaults['background_color'],
		'sanitize_callback' => 'aureon_sanitize_hex_color',
		'transport' => 'postMessage',
	),
	array(
		'label' => __( 'Background', 'aureon' ),
		'section' => 'aureon_colors_section',
		'choices' => array(
			'toggleId' => 'base-colors',
		),
		'output' => array(
			array(
				'element'  => 'body',
				'property' => 'background-color',
			),
		),
	)
);

Aureon_Customize_Field::add_field(
	'aureon_settings[text_color]',
	'Aureon_Customize_Color_Control',
	array(
		'default' => $defaults['text_color'],
		'sanitize_callback' => 'aureon_sanitize_hex_color',
		'transport' => 'postMessage',
	),
	array(
		'label' => __( 'Text', 'aureon' ),
		'section' => 'aureon_colors_section',
		'choices' => array(
			'toggleId' => 'base-colors',
		),
		'output' => array(
			array(
				'element'  => 'body',
				'property' => 'color',
			),
		),
	)
);

Aureon_Customize_Field::add_wrapper(
	'aureon_body_link_wrapper',
	array(
		'section' => 'aureon_colors_section',
		'choices' => array(
			'type' => 'color',
			'toggleId' => 'base-colors',
			'items' => array(
				'link_color',
				'link_color_hover',
				'link_color_visited',
			),
		),
	)
);

Aureon_Customize_Field::add_field(
	'aureon_settings[link_color]',
	'Aureon_Customize_Color_Control',
	array(
		'default' => $defaults['link_color'],
		'sanitize_callback' => 'aureon_sanitize_hex_color',
		'transport' => 'postMessage',
	),
	array(
		'label' => __( 'Link', 'aureon' ),
		'section' => 'aureon_colors_section',
		'choices' => array(
			'wrapper' => 'link_color',
			'tooltip' => __( 'Choose Initial Color', 'aureon' ),
			'toggleId' => 'base-colors',
		),
		'output' => array(
			array(
				'element'  => 'a, a:visited',
				'property' => 'color',
			),
		),
	)
);

Aureon_Customize_Field::add_field(
	'aureon_settings[link_color_hover]',
	'Aureon_Customize_Color_Control',
	array(
		'default' => $defaults['link_color_hover'],
		'sanitize_callback' => 'aureon_sanitize_hex_color',
		'transport' => 'postMessage',
	),
	array(
		'label' => __( 'Link Hover', 'aureon' ),
		'section' => 'aureon_colors_section',
		'choices' => array(
			'wrapper' => 'link_color_hover',
			'tooltip' => __( 'Choose Hover Color', 'aureon' ),
			'toggleId' => 'base-colors',
			'hideLabel' => true,
		),
		'output' => array(
			array(
				'element'  => 'a:hover',
				'property' => 'color',
			),
		),
	)
);

if ( '' !== aureon_get_option( 'link_color_visited' ) ) {
	Aureon_Customize_Field::add_field(
		'aureon_settings[link_color_visited]',
		'Aureon_Customize_Color_Control',
		array(
			'default' => $defaults['link_color_visited'],
			'sanitize_callback' => 'aureon_sanitize_hex_color',
			'transport' => 'refresh',
		),
		array(
			'label' => __( 'Link Color Visited', 'aureon' ),
			'section' => 'aureon_colors_section',
			'choices' => array(
				'wrapper' => 'link_color_visited',
				'tooltip' => __( 'Choose Visited Color', 'aureon' ),
				'toggleId' => 'base-colors',
				'hideLabel' => true,
			),
		)
	);
}
