<?php
/**
 * This file handles the customizer fields for the secondary navigation.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

if ( ! function_exists( 'aureon_register_secondary_navigation_colors' ) ) {
	add_action('aureon_customize_after_primary_navigation', 'aureon_register_secondary_navigation_colors', 1000);

	/**
	 * Register the secondary navigation color fields.
	 */
	function aureon_register_secondary_navigation_colors()
	{
		if ( ! class_exists('Aureon_Customize_Field') ) {
			return;
		}

		$secondary_color_defaults = aureon_secondary_nav_get_defaults();

		$menu_hover_selectors = '.secondary-navigation .main-nav ul li:not([class*="current-menu-"]):hover > a, .secondary-navigation .main-nav ul li:not([class*="current-menu-"]):focus > a, .secondary-navigation .main-nav ul li.sfHover:not([class*="current-menu-"]) > a, .secondary-navigation .menu-bar-item:hover > a, .secondary-navigation .menu-bar-item.sfHover > a';
		$menu_current_selectors = '.secondary-navigation .main-nav ul li[class*="current-menu-"] > a';
		$submenu_hover_selectors = '.secondary-navigation .main-nav ul ul li:not([class*="current-menu-"]):hover > a, .secondary-navigation .main-nav ul ul li:not([class*="current-menu-"]):focus > a, .secondary-navigation .main-nav ul ul li.sfHover:not([class*="current-menu-"]) > a';
		$submenu_current_selectors = '.secondary-navigation .main-nav ul ul li[class*="current-menu-"] > a';

		Aureon_Customize_Field::add_title(
			'aureon_secondary_navigation_colors_title',
			array(
				'section' => 'aureon_colors_section',
				'title' => __( 'Secondary Navigation', 'aureon-studio' ),
				'choices' => array(
					'toggleId' => 'secondary-navigation-colors',
				),
			)
		);

		// Navigation background group.
		Aureon_Customize_Field::add_color_field_group(
			'secondary_navigation_background',
			'aureon_colors_section',
			'secondary-navigation-colors',
			array(
				'aureon_secondary_nav_settings[navigation_background_color]' => array(
					'default_value' => $secondary_color_defaults['navigation_background_color'],
					'label' => __( 'Navigation Background', 'aureon-studio' ),
					'tooltip' => __( 'Choose Initial Color', 'aureon-studio' ),
					'element' => '.secondary-navigation',
					'property' => 'background-color',
					'hide_label' => false,
				),
				'aureon_secondary_nav_settings[navigation_background_hover_color]' => array(
					'default_value' => $secondary_color_defaults['navigation_background_hover_color'],
					'label' => __( 'Navigation Background Hover', 'aureon-studio' ),
					'tooltip' => __( 'Choose Hover Color', 'aureon-studio' ),
					'element' => $menu_hover_selectors,
					'property' => 'background-color',
					'hide_label' => true,
				),
				'aureon_secondary_nav_settings[navigation_background_current_color]' => array(
					'default_value' => $secondary_color_defaults['navigation_background_current_color'],
					'label' => __( 'Navigation Background Current', 'aureon-studio' ),
					'tooltip' => __( 'Choose Current Color', 'aureon-studio' ),
					'element' => $menu_current_selectors,
					'property' => 'background-color',
					'hide_label' => true,
				),
			)
		);

		// Navigation text group.
		Aureon_Customize_Field::add_color_field_group(
			'secondary_navigation_text',
			'aureon_colors_section',
			'secondary-navigation-colors',
			array(
				'aureon_secondary_nav_settings[navigation_text_color]' => array(
					'default_value' => $secondary_color_defaults['navigation_text_color'],
					'label' => __( 'Navigation Text', 'aureon-studio' ),
					'tooltip' => __( 'Choose Initial Color', 'aureon-studio' ),
					'element' => '.secondary-navigation .main-nav ul li a, .secondary-navigation .menu-toggle, .secondary-navigation button.menu-toggle:hover, .secondary-navigation button.menu-toggle:focus, .secondary-navigation .mobile-bar-items a, .secondary-navigation .mobile-bar-items a:hover, .secondary-navigation .mobile-bar-items a:focus, .secondary-navigation .menu-bar-items',
					'property' => 'color',
					'hide_label' => false,
				),
				'aureon_secondary_nav_settings[navigation_text_hover_color]' => array(
					'default_value' => $secondary_color_defaults['navigation_text_hover_color'],
					'label' => __( 'Navigation Text Hover', 'aureon-studio' ),
					'tooltip' => __( 'Choose Hover Color', 'aureon-studio' ),
					'element' => $menu_hover_selectors,
					'property' => 'color',
					'hide_label' => true,
				),
				'aureon_secondary_nav_settings[navigation_text_current_color]' => array(
					'default_value' => $secondary_color_defaults['navigation_text_current_color'],
					'label' => __( 'Navigation Text Current', 'aureon-studio' ),
					'tooltip' => __( 'Choose Current Color', 'aureon-studio' ),
					'element' => $menu_current_selectors,
					'property' => 'color',
					'hide_label' => true,
				),
			)
		);

		// Sub-Menu background group.
		Aureon_Customize_Field::add_color_field_group(
			'secondary_navigation_submenu_background',
			'aureon_colors_section',
			'secondary-navigation-colors',
			array(
				'aureon_secondary_nav_settings[subnavigation_background_color]' => array(
					'default_value' => $secondary_color_defaults['subnavigation_background_color'],
					'label' => __( 'Sub-Menu Background', 'aureon-studio' ),
					'tooltip' => __( 'Choose Initial Color', 'aureon-studio' ),
					'element' => '.secondary-navigation ul ul',
					'property' => 'background-color',
					'hide_label' => false,
				),
				'aureon_secondary_nav_settings[subnavigation_background_hover_color]' => array(
					'default_value' => $secondary_color_defaults['subnavigation_background_hover_color'],
					'label' => __( 'Sub-Menu Background Hover', 'aureon-studio' ),
					'tooltip' => __( 'Choose Hover Color', 'aureon-studio' ),
					'element' => $submenu_hover_selectors,
					'property' => 'background-color',
					'hide_label' => true,
				),
				'aureon_secondary_nav_settings[subnavigation_background_current_color]' => array(
					'default_value' => $secondary_color_defaults['subnavigation_background_current_color'],
					'label' => __( 'Sub-Menu Background Current', 'aureon-studio' ),
					'tooltip' => __( 'Choose Current Color', 'aureon-studio' ),
					'element' => $submenu_current_selectors,
					'property' => 'background-color',
					'hide_label' => true,
				),
			)
		);

		// Sub-Menu text group.
		Aureon_Customize_Field::add_color_field_group(
			'secondary_navigation_submenu_text',
			'aureon_colors_section',
			'secondary-navigation-colors',
			array(
				'aureon_secondary_nav_settings[subnavigation_text_color]' => array(
					'default_value' => $secondary_color_defaults['subnavigation_text_color'],
					'label' => __( 'Sub-Menu Text', 'aureon-studio' ),
					'tooltip' => __( 'Choose Initial Color', 'aureon-studio' ),
					'element' => '.secondary-navigation .main-nav ul ul li a',
					'property' => 'color',
					'hide_label' => false,
				),
				'aureon_secondary_nav_settings[subnavigation_text_hover_color]' => array(
					'default_value' => $secondary_color_defaults['subnavigation_text_hover_color'],
					'label' => __( 'Sub-Menu Text Hover', 'aureon-studio' ),
					'tooltip' => __( 'Choose Hover Color', 'aureon-studio' ),
					'element' => $submenu_hover_selectors,
					'property' => 'color',
					'hide_label' => true,
				),
				'aureon_secondary_nav_settings[subnavigation_text_current_color]' => array(
					'default_value' => $secondary_color_defaults['subnavigation_text_current_color'],
					'label' => __( 'Sub-Menu Text Current', 'aureon-studio' ),
					'tooltip' => __( 'Choose Current Color', 'aureon-studio' ),
					'element' => $submenu_current_selectors,
					'property' => 'color',
					'hide_label' => true,
				),
			)
		);
	}
}
