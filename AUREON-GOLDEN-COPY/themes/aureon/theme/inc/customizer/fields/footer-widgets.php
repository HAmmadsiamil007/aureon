<?php
/**
 * This file handles the customizer fields for the footer widgets.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

Aureon_Customize_Field::add_title(
	'aureon_footer_widgets_colors_title',
	array(
		'section' => 'aureon_colors_section',
		'title' => __( 'Footer Widgets', 'aureon' ),
		'choices' => array(
			'toggleId' => 'footer-widget-colors',
		),
	)
);

Aureon_Customize_Field::add_field(
	'aureon_settings[footer_widget_background_color]',
	'Aureon_Customize_Color_Control',
	array(
		'default' => $color_defaults['footer_widget_background_color'],
		'sanitize_callback' => 'aureon_sanitize_rgba_color',
		'transport' => 'postMessage',
	),
	array(
		'label' => __( 'Background', 'aureon' ),
		'section' => 'aureon_colors_section',
		'choices' => array(
			'alpha' => true,
			'toggleId' => 'footer-widget-colors',
			'wrapper' => 'footer_widget_background_color',
			'tooltip' => __( 'Choose Initial Color', 'aureon' ),
		),
		'output' => array(
			array(
				'element'  => '.footer-widgets',
				'property' => 'background-color',
			),
		),
	)
);

Aureon_Customize_Field::add_field(
	'aureon_settings[footer_widget_text_color]',
	'Aureon_Customize_Color_Control',
	array(
		'default' => $color_defaults['footer_widget_text_color'],
		'sanitize_callback' => 'aureon_sanitize_hex_color',
		'transport' => 'postMessage',
	),
	array(
		'label' => __( 'Text', 'aureon' ),
		'section' => 'aureon_colors_section',
		'choices' => array(
			'toggleId' => 'footer-widget-colors',
			'wrapper' => 'footer_widget_text_color',
			'tooltip' => __( 'Choose Initial Color', 'aureon' ),
		),
		'output' => array(
			array(
				'element'  => '.footer-widgets',
				'property' => 'color',
			),
		),
	)
);

Aureon_Customize_Field::add_wrapper(
	'aureon_footer_widget_colors_wrapper',
	array(
		'section' => 'aureon_colors_section',
		'choices' => array(
			'type' => 'color',
			'toggleId' => 'footer-widget-colors',
			'items' => array(
				'footer_widget_link_color',
				'footer_widget_link_hover_color',
			),
		),
	)
);

Aureon_Customize_Field::add_field(
	'aureon_settings[footer_widget_link_color]',
	'Aureon_Customize_Color_Control',
	array(
		'default' => $color_defaults['footer_widget_link_color'],
		'sanitize_callback' => 'aureon_sanitize_hex_color',
		'transport' => 'postMessage',
	),
	array(
		'label' => __( 'Link', 'aureon' ),
		'section' => 'aureon_colors_section',
		'choices' => array(
			'toggleId' => 'footer-widget-colors',
			'wrapper' => 'footer_widget_link_color',
			'tooltip' => __( 'Choose Initial Color', 'aureon' ),
		),
		'output' => array(
			array(
				'element'  => '.footer-widgets a',
				'property' => 'color',
			),
		),
	)
);

Aureon_Customize_Field::add_field(
	'aureon_settings[footer_widget_link_hover_color]',
	'Aureon_Customize_Color_Control',
	array(
		'default' => $color_defaults['footer_widget_link_hover_color'],
		'sanitize_callback' => 'aureon_sanitize_hex_color',
		'transport' => 'postMessage',
	),
	array(
		'label' => __( 'Link Hover', 'aureon' ),
		'section' => 'aureon_colors_section',
		'choices' => array(
			'toggleId' => 'footer-widget-colors',
			'wrapper' => 'footer_widget_link_hover_color',
			'tooltip' => __( 'Choose Hover Color', 'aureon' ),
			'hideLabel' => true,
		),
		'output' => array(
			array(
				'element'  => '.footer-widgets a:hover',
				'property' => 'color',
			),
		),
	)
);

Aureon_Customize_Field::add_field(
	'aureon_settings[footer_widget_title_color]',
	'Aureon_Customize_Color_Control',
	array(
		'default' => $color_defaults['footer_widget_title_color'],
		'sanitize_callback' => 'aureon_sanitize_hex_color',
		'transport' => 'postMessage',
	),
	array(
		'label' => __( 'Widget Title', 'aureon' ),
		'section' => 'aureon_colors_section',
		'choices' => array(
			'toggleId' => 'footer-widget-colors',
		),
		'output' => array(
			array(
				'element'  => '.footer-widgets .widget-title',
				'property' => 'color',
			),
		),
	)
);
