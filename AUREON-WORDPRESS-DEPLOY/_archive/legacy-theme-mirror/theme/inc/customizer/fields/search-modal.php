<?php
/**
 * This file handles the customizer fields for the Search Modal.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

Aureon_Customize_Field::add_title(
	'aureon_search_modal_colors_title',
	array(
		'section' => 'aureon_colors_section',
		'title' => __( 'Search Modal', 'aureon' ),
		'choices' => array(
			'toggleId' => 'search-modal-colors',
		),
		'active_callback' => function() {
			if ( aureon_get_option( 'nav_search_modal' ) ) {
				return true;
			}

			return false;
		},
	)
);

Aureon_Customize_Field::add_field(
	'aureon_settings[search_modal_bg_color]',
	'Aureon_Customize_Color_Control',
	array(
		'default' => $color_defaults['search_modal_bg_color'],
		'sanitize_callback' => 'aureon_sanitize_rgba_color',
		'transport' => 'postMessage',
	),
	array(
		'label' => __( 'Field Background', 'aureon' ),
		'section' => 'aureon_colors_section',
		'choices' => array(
			'toggleId' => 'search-modal-colors',
		),
		'output' => array(
			array(
				'element'  => ':root',
				'property' => '--aureon-search-modal-bg-color',
			),
		),
	)
);

Aureon_Customize_Field::add_field(
	'aureon_settings[search_modal_text_color]',
	'Aureon_Customize_Color_Control',
	array(
		'default' => $color_defaults['search_modal_text_color'],
		'sanitize_callback' => 'aureon_sanitize_rgba_color',
		'transport' => 'postMessage',
	),
	array(
		'label' => __( 'Field Text', 'aureon' ),
		'section' => 'aureon_colors_section',
		'choices' => array(
			'toggleId' => 'search-modal-colors',
		),
		'output' => array(
			array(
				'element'  => ':root',
				'property' => '--aureon-search-modal-text-color',
			),
		),
	)
);

Aureon_Customize_Field::add_field(
	'aureon_settings[search_modal_overlay_bg_color]',
	'Aureon_Customize_Color_Control',
	array(
		'default' => $color_defaults['search_modal_overlay_bg_color'],
		'sanitize_callback' => 'aureon_sanitize_rgba_color',
		'transport' => 'postMessage',
	),
	array(
		'label' => __( 'Overlay Background', 'aureon' ),
		'section' => 'aureon_colors_section',
		'choices' => array(
			'toggleId' => 'search-modal-colors',
		),
		'output' => array(
			array(
				'element'  => ':root',
				'property' => '--aureon-search-modal-overlay-bg-color',
			),
		),
	)
);
