<?php
/**
 * This file handles the customizer fields for the primary navigation.
 *
 * @package Aureon
 *
 * @var array $color_defaults
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

$menu_hover_selectors = '.navigation-search input[type="search"], .navigation-search input[type="search"]:active, .navigation-search input[type="search"]:focus, .main-navigation .main-nav ul li:not([class*="current-menu-"]):hover > a, .main-navigation .main-nav ul li:not([class*="current-menu-"]):focus > a, .main-navigation .main-nav ul li.sfHover:not([class*="current-menu-"]) > a, .main-navigation .menu-bar-item:hover > a, .main-navigation .menu-bar-item.sfHover > a';
$menu_current_selectors = '.main-navigation .main-nav ul li[class*="current-menu-"] > a';
$submenu_hover_selectors = '.main-navigation .main-nav ul ul li:not([class*="current-menu-"]):hover > a,.main-navigation .main-nav ul ul li:not([class*="current-menu-"]):focus > a,.main-navigation .main-nav ul ul li.sfHover:not([class*="current-menu-"]) > a';
$submenu_current_selectors = '.main-navigation .main-nav ul ul li[class*="current-menu-"] > a';

Aureon_Customize_Field::add_title(
	'aureon_primary_navigation_colors_title',
	array(
		'section' => 'aureon_colors_section',
		'title' => __( 'Primary Navigation', 'aureon' ),
		'choices' => array(
			'toggleId' => 'primary-navigation-colors',
		),
	)
);

// Navigation background group.
Aureon_Customize_Field::add_color_field_group(
	'primary_navigation_background',
	'aureon_colors_section',
	'primary-navigation-colors',
	array(
		'aureon_settings[navigation_background_color]' => array(
			'default_value' => $color_defaults['navigation_background_color'],
			'label' => __( 'Navigation Background', 'aureon' ),
			'tooltip' => __( 'Choose Initial Color', 'aureon' ),
			'element' => '.main-navigation',
			'property' => 'background-color',
			'hide_label' => false,
		),
		'aureon_settings[navigation_background_hover_color]' => array(
			'default_value' => $color_defaults['navigation_background_hover_color'],
			'label' => __( 'Navigation Background Hover', 'aureon' ),
			'tooltip' => __( 'Choose Hover Color', 'aureon' ),
			'element' => $menu_hover_selectors,
			'property' => 'background-color',
			'hide_label' => true,
		),
		'aureon_settings[navigation_background_current_color]' => array(
			'default_value' => $color_defaults['navigation_background_current_color'],
			'label' => __( 'Navigation Background Current', 'aureon' ),
			'tooltip' => __( 'Choose Current Color', 'aureon' ),
			'element' => $menu_current_selectors,
			'property' => 'background-color',
			'hide_label' => true,
		),
	)
);

// Navigation text group.
Aureon_Customize_Field::add_color_field_group(
	'primary_navigation_text',
	'aureon_colors_section',
	'primary-navigation-colors',
	array(
		'aureon_settings[navigation_text_color]' => array(
			'default_value' => $color_defaults['navigation_text_color'],
			'label' => __( 'Navigation Text', 'aureon' ),
			'tooltip' => __( 'Choose Initial Color', 'aureon' ),
			'element' => '.main-navigation .main-nav ul li a, .main-navigation .menu-toggle, .main-navigation button.menu-toggle:hover, .main-navigation button.menu-toggle:focus, .main-navigation .mobile-bar-items a, .main-navigation .mobile-bar-items a:hover, .main-navigation .mobile-bar-items a:focus, .main-navigation .menu-bar-items',
			'property' => 'color',
			'hide_label' => false,
		),
		'aureon_settings[navigation_text_hover_color]' => array(
			'default_value' => $color_defaults['navigation_text_hover_color'],
			'label' => __( 'Navigation Text Hover', 'aureon' ),
			'tooltip' => __( 'Choose Hover Color', 'aureon' ),
			'element' => $menu_hover_selectors,
			'property' => 'color',
			'hide_label' => true,
		),
		'aureon_settings[navigation_text_current_color]' => array(
			'default_value' => $color_defaults['navigation_text_current_color'],
			'label' => __( 'Navigation Text Current', 'aureon' ),
			'tooltip' => __( 'Choose Current Color', 'aureon' ),
			'element' => $menu_current_selectors,
			'property' => 'color',
			'hide_label' => true,
		),
	)
);

// Sub-Menu background group.
Aureon_Customize_Field::add_color_field_group(
	'primary_navigation_submenu_background',
	'aureon_colors_section',
	'primary-navigation-colors',
	array(
		'aureon_settings[subnavigation_background_color]' => array(
			'default_value' => $color_defaults['subnavigation_background_color'],
			'label' => __( 'Sub-Menu Background', 'aureon' ),
			'tooltip' => __( 'Choose Initial Color', 'aureon' ),
			'element' => '.main-navigation ul ul',
			'property' => 'background-color',
			'hide_label' => false,
		),
		'aureon_settings[subnavigation_background_hover_color]' => array(
			'default_value' => $color_defaults['subnavigation_background_hover_color'],
			'label' => __( 'Sub-Menu Background Hover', 'aureon' ),
			'tooltip' => __( 'Choose Hover Color', 'aureon' ),
			'element' => $submenu_hover_selectors,
			'property' => 'background-color',
			'hide_label' => true,
		),
		'aureon_settings[subnavigation_background_current_color]' => array(
			'default_value' => $color_defaults['subnavigation_background_current_color'],
			'label' => __( 'Sub-Menu Background Current', 'aureon' ),
			'tooltip' => __( 'Choose Current Color', 'aureon' ),
			'element' => $submenu_current_selectors,
			'property' => 'background-color',
			'hide_label' => true,
		),
	)
);

// Sub-Menu text group.
Aureon_Customize_Field::add_color_field_group(
	'primary_navigation_submenu_text',
	'aureon_colors_section',
	'primary-navigation-colors',
	array(
		'aureon_settings[subnavigation_text_color]' => array(
			'default_value' => $color_defaults['subnavigation_text_color'],
			'label' => __( 'Sub-Menu Text', 'aureon' ),
			'tooltip' => __( 'Choose Initial Color', 'aureon' ),
			'element' => '.main-navigation .main-nav ul ul li a',
			'property' => 'color',
			'hide_label' => false,
		),
		'aureon_settings[subnavigation_text_hover_color]' => array(
			'default_value' => $color_defaults['subnavigation_text_hover_color'],
			'label' => __( 'Sub-Menu Text Hover', 'aureon' ),
			'tooltip' => __( 'Choose Hover Color', 'aureon' ),
			'element' => $submenu_hover_selectors,
			'property' => 'color',
			'hide_label' => true,
		),
		'aureon_settings[subnavigation_text_current_color]' => array(
			'default_value' => $color_defaults['subnavigation_text_current_color'],
			'label' => __( 'Sub-Menu Text Current', 'aureon' ),
			'tooltip' => __( 'Choose Current Color', 'aureon' ),
			'element' => $submenu_current_selectors,
			'property' => 'color',
			'hide_label' => true,
		),
	)
);

Aureon_Customize_Field::add_title(
	'aureon_navigation_search_colors_title',
	array(
		'section' => 'aureon_colors_section',
		'title' => __( 'Navigation Search', 'aureon' ),
		'choices' => array(
			'toggleId' => 'primary-navigation-search-colors',
		),
		'active_callback' => function() {
			if ( 'enable' === aureon_get_option( 'nav_search' ) ) {
				return true;
			}

			return false;
		},
	)
);

Aureon_Customize_Field::add_field(
	'aureon_settings[navigation_search_background_color]',
	'Aureon_Customize_Color_Control',
	array(
		'default' => $color_defaults['navigation_search_background_color'],
		'transport' => 'refresh',
		'sanitize_callback' => 'aureon_sanitize_rgba_color',
	),
	array(
		'label' => __( 'Background', 'aureon' ),
		'section' => 'aureon_colors_section',
		'choices' => array(
			'alpha' => true,
			'toggleId' => 'primary-navigation-search-colors',
		),
	)
);

Aureon_Customize_Field::add_field(
	'aureon_settings[navigation_search_text_color]',
	'Aureon_Customize_Color_Control',
	array(
		'default' => $color_defaults['navigation_search_text_color'],
		'transport' => 'refresh',
		'sanitize_callback' => 'aureon_sanitize_rgba_color',
	),
	array(
		'label' => __( 'Text', 'aureon' ),
		'section' => 'aureon_colors_section',
		'choices' => array(
			'alpha' => true,
			'toggleId' => 'primary-navigation-search-colors',
		),
	)
);
