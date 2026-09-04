<?php
/**
 * This file handles the customizer fields for the slideout navigation colors.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

if ( ! function_exists( 'aureon_register_slideout_nav_colors' ) ) {
	add_action( 'aureon_customize_after_primary_navigation', 'aureon_register_slideout_nav_colors', 1000 );
	/**
	 * Register the slideout navigation color fields.
	 */
	function aureon_register_slideout_nav_colors() {
		if ( ! class_exists( 'Aureon_Customize_Field' ) ) {
			return;
		}

		$color_defaults = aureon_get_color_defaults();

		$menu_hover_selectors = '.slideout-navigation.main-navigation .main-nav ul li:not([class*="current-menu-"]):hover > a, .slideout-navigation.main-navigation .main-nav ul li:not([class*="current-menu-"]):focus > a, .slideout-navigation.main-navigation .main-nav ul li.sfHover:not([class*="current-menu-"]) > a, .slideout-navigation.main-navigation .menu-bar-item:hover > a, .slideout-navigation.main-navigation .menu-bar-item.sfHover > a';
		$menu_current_selectors = '.slideout-navigation.main-navigation .main-nav ul li[class*="current-menu-"] > a';
		$text_selectors = '.slideout-navigation.main-navigation .main-nav ul li a, .slideout-navigation.main-navigation .menu-toggle, .slideout-navigation.main-navigation button.menu-toggle:hover, .slideout-navigation.main-navigation button.menu-toggle:focus, .slideout-navigation.main-navigation .mobile-bar-items a, .slideout-navigation.main-navigation .mobile-bar-items a:hover, .slideout-navigation.main-navigation .mobile-bar-items a:focus, .slideout-navigation.main-navigation .menu-bar-items';
		$submenu_hover_selectors = '.slideout-navigation.main-navigation .main-nav ul ul li:not([class*="current-menu-"]):hover > a, .slideout-navigation.main-navigation .main-nav ul ul li:not([class*="current-menu-"]):focus > a, .slideout-navigation.main-navigation .main-nav ul ul li.sfHover:not([class*="current-menu-"]) > a';
		$submenu_current_selectors = '.slideout-navigation.main-navigation .main-nav ul ul li[class*="current-menu-"] > a';

		Aureon_Customize_Field::add_title(
			'aureon_slideout_navigation_colors_title',
			array(
				'section' => 'aureon_colors_section',
				'title' => __( 'Off Canvas Panel', 'aureon-studio' ),
				'choices' => array(
					'toggleId' => 'slideout-navigation-colors',
				),
				'active_callback' => function() {
					$settings = wp_parse_args(
						get_option( 'aureon_menu_plus_settings', array() ),
						aureon_menu_plus_get_defaults()
					);

					if ( 'false' !== $settings['slideout_menu'] ) {
						return true;
					}

					return false;
				},
			)
		);

		// Navigation background group.
		Aureon_Customize_Field::add_color_field_group(
			'slideout_navigation_background',
			'aureon_colors_section',
			'slideout-navigation-colors',
			array(
				'aureon_settings[slideout_background_color]' => array(
					'default_value' => $color_defaults['slideout_background_color'],
					'label' => __( 'Navigation Background', 'aureon-studio' ),
					'tooltip' => __( 'Choose Initial Color', 'aureon-studio' ),
					'element' => '.slideout-navigation.main-navigation',
					'property' => 'background-color',
					'hide_label' => false,
				),
				'aureon_settings[slideout_background_hover_color]' => array(
					'default_value' => $color_defaults['slideout_background_hover_color'],
					'label' => __( 'Navigation Background Hover', 'aureon-studio' ),
					'tooltip' => __( 'Choose Hover Color', 'aureon-studio' ),
					'element' => $menu_hover_selectors,
					'property' => 'background-color',
					'hide_label' => true,
				),
				'aureon_settings[slideout_background_current_color]' => array(
					'default_value' => $color_defaults['slideout_background_current_color'],
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
			'slideout_navigation_text',
			'aureon_colors_section',
			'slideout-navigation-colors',
			array(
				'aureon_settings[slideout_text_color]' => array(
					'default_value' => $color_defaults['slideout_text_color'],
					'label' => __( 'Navigation Text', 'aureon-studio' ),
					'tooltip' => __( 'Choose Initial Color', 'aureon-studio' ),
					'element' => $text_selectors,
					'property' => 'color',
					'hide_label' => false,
				),
				'aureon_settings[slideout_text_hover_color]' => array(
					'default_value' => $color_defaults['slideout_text_hover_color'],
					'label' => __( 'Navigation Text Hover', 'aureon-studio' ),
					'tooltip' => __( 'Choose Hover Color', 'aureon-studio' ),
					'element' => $menu_hover_selectors,
					'property' => 'color',
					'hide_label' => true,
				),
				'aureon_settings[slideout_text_current_color]' => array(
					'default_value' => $color_defaults['slideout_text_current_color'],
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
			'slideout_navigation_submenu_background',
			'aureon_colors_section',
			'slideout-navigation-colors',
			array(
				'aureon_settings[slideout_submenu_background_color]' => array(
					'default_value' => $color_defaults['slideout_submenu_background_color'],
					'label' => __( 'Sub-Menu Background', 'aureon-studio' ),
					'tooltip' => __( 'Choose Initial Color', 'aureon-studio' ),
					'element' => '.slideout-navigation.main-navigation ul ul',
					'property' => 'background-color',
					'hide_label' => false,
				),
				'aureon_settings[slideout_submenu_background_hover_color]' => array(
					'default_value' => $color_defaults['slideout_submenu_background_hover_color'],
					'label' => __( 'Sub-Menu Background Hover', 'aureon-studio' ),
					'tooltip' => __( 'Choose Hover Color', 'aureon-studio' ),
					'element' => $submenu_hover_selectors,
					'property' => 'background-color',
					'hide_label' => true,
				),
				'aureon_settings[slideout_submenu_background_current_color]' => array(
					'default_value' => $color_defaults['slideout_submenu_background_current_color'],
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
			'slideout_navigation_submenu_text',
			'aureon_colors_section',
			'slideout-navigation-colors',
			array(
				'aureon_settings[slideout_submenu_text_color]' => array(
					'default_value' => $color_defaults['slideout_submenu_text_color'],
					'label' => __( 'Sub-Menu Text', 'aureon-studio' ),
					'tooltip' => __( 'Choose Initial Color', 'aureon-studio' ),
					'element' => '.slideout-navigation.main-navigation .main-nav ul ul li a',
					'property' => 'color',
					'hide_label' => false,
				),
				'aureon_settings[slideout_submenu_text_hover_color]' => array(
					'default_value' => $color_defaults['slideout_submenu_text_hover_color'],
					'label' => __( 'Sub-Menu Text Hover', 'aureon-studio' ),
					'tooltip' => __( 'Choose Hover Color', 'aureon-studio' ),
					'element' => $submenu_hover_selectors,
					'property' => 'color',
					'hide_label' => true,
				),
				'aureon_settings[slideout_submenu_text_current_color]' => array(
					'default_value' => $color_defaults['slideout_submenu_text_current_color'],
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
