<?php
/**
 * This file handles the customizer fields for the sidebar widgets.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

Aureon_Customize_Field::add_title(
	'aureon_sidebar_widgets_colors_title',
	array(
		'section' => 'aureon_colors_section',
		'title' => __( 'Sidebar Widgets', 'aureon' ),
		'choices' => array(
			'toggleId' => 'sidebar-widget-colors',
		),
	)
);

Aureon_Customize_Field::add_field(
	'aureon_settings[sidebar_widget_background_color]',
	'Aureon_Customize_Color_Control',
	array(
		'default' => $color_defaults['sidebar_widget_background_color'],
		'sanitize_callback' => 'aureon_sanitize_rgba_color',
		'transport' => 'postMessage',
	),
	array(
		'label' => __( 'Background', 'aureon' ),
		'section' => 'aureon_colors_section',
		'choices' => array(
			'alpha' => true,
			'toggleId' => 'sidebar-widget-colors',
			'wrapper' => 'sidebar_widget_background_color',
			'tooltip' => __( 'Choose Initial Color', 'aureon' ),
		),
		'output' => array(
			array(
				'element'  => '.sidebar .widget',
				'property' => 'background-color',
			),
		),
	)
);

Aureon_Customize_Field::add_field(
	'aureon_settings[sidebar_widget_text_color]',
	'Aureon_Customize_Color_Control',
	array(
		'default' => $color_defaults['sidebar_widget_text_color'],
		'sanitize_callback' => 'aureon_sanitize_hex_color',
		'transport' => 'postMessage',
	),
	array(
		'label' => __( 'Text', 'aureon' ),
		'section' => 'aureon_colors_section',
		'choices' => array(
			'toggleId' => 'sidebar-widget-colors',
			'wrapper' => 'sidebar_widget_text_color',
			'tooltip' => __( 'Choose Initial Color', 'aureon' ),
		),
		'output' => array(
			array(
				'element'  => '.sidebar .widget',
				'property' => 'color',
			),
		),
	)
);

Aureon_Customize_Field::add_wrapper(
	'aureon_sidebar_widget_colors_wrapper',
	array(
		'section' => 'aureon_colors_section',
		'choices' => array(
			'type' => 'color',
			'toggleId' => 'sidebar-widget-colors',
			'items' => array(
				'sidebar_widget_link_color',
				'sidebar_widget_link_hover_color',
			),
		),
	)
);

Aureon_Customize_Field::add_field(
	'aureon_settings[sidebar_widget_link_color]',
	'Aureon_Customize_Color_Control',
	array(
		'default' => $color_defaults['sidebar_widget_link_color'],
		'sanitize_callback' => 'aureon_sanitize_hex_color',
		'transport' => 'postMessage',
	),
	array(
		'label' => __( 'Link', 'aureon' ),
		'section' => 'aureon_colors_section',
		'choices' => array(
			'toggleId' => 'sidebar-widget-colors',
			'wrapper' => 'sidebar_widget_link_color',
			'tooltip' => __( 'Choose Initial Color', 'aureon' ),
		),
		'output' => array(
			array(
				'element'  => '.sidebar .widget a',
				'property' => 'color',
			),
		),
	)
);

Aureon_Customize_Field::add_field(
	'aureon_settings[sidebar_widget_link_hover_color]',
	'Aureon_Customize_Color_Control',
	array(
		'default' => $color_defaults['sidebar_widget_link_hover_color'],
		'sanitize_callback' => 'aureon_sanitize_hex_color',
		'transport' => 'postMessage',
	),
	array(
		'label' => __( 'Link Hover', 'aureon' ),
		'section' => 'aureon_colors_section',
		'choices' => array(
			'toggleId' => 'sidebar-widget-colors',
			'wrapper' => 'sidebar_widget_link_hover_color',
			'tooltip' => __( 'Choose Hover Color', 'aureon' ),
			'hideLabel' => true,
		),
		'output' => array(
			array(
				'element'  => '.sidebar .widget a:hover',
				'property' => 'color',
			),
		),
	)
);

Aureon_Customize_Field::add_field(
	'aureon_settings[sidebar_widget_title_color]',
	'Aureon_Customize_Color_Control',
	array(
		'default' => $color_defaults['sidebar_widget_title_color'],
		'sanitize_callback' => 'aureon_sanitize_hex_color',
		'transport' => 'postMessage',
	),
	array(
		'label' => __( 'Widget Title', 'aureon' ),
		'section' => 'aureon_colors_section',
		'choices' => array(
			'toggleId' => 'sidebar-widget-colors',
		),
		'output' => array(
			array(
				'element'  => '.sidebar .widget .widget-title',
				'property' => 'color',
			),
		),
	)
);
