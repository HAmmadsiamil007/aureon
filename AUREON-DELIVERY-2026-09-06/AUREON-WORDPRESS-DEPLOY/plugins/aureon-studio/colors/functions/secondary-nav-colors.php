<?php
/**
 * This file handles the Customizer options for the Secondary Nav module.
 *
 * @package Aureon Studio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

if ( ! function_exists( 'aureon_colors_secondary_nav_customizer' ) ) {
	add_action( 'customize_register', 'aureon_colors_secondary_nav_customizer', 1000 );
	/**
	 * Adds our Secondary Nav color options
	 *
	 * These options are in their own function so we can hook it in late to
	 * make sure Secondary Nav is activated.
	 *
	 * 1000 priority is there to make sure Secondary Nav is registered (999)
	 * as we check to see if the layout control exists.
	 *
	 * Secondary Nav now uses 100 as a priority.
	 *
	 * @param object $wp_customize The Customizer object.
	 */
	function aureon_colors_secondary_nav_customizer( $wp_customize ) {
		// Bail if Secondary Nav isn't activated.
		if ( ! $wp_customize->get_section( 'secondary_nav_section' ) ) {
			return;
		}

		// Bail if we don't have our color defaults.
		if ( ! function_exists( 'aureon_secondary_nav_get_defaults' ) ) {
			return;
		}

		// Add our controls.
		require_once AUREON_LIBRARY_DIRECTORY . 'customizer-helpers.php';

		// Get our defaults.
		$defaults = aureon_secondary_nav_get_defaults();

		// Add control types so controls can be built using JS.
		if ( method_exists( $wp_customize, 'register_control_type' ) ) {
			$wp_customize->register_control_type( 'Aureon_Alpha_Color_Customize_Control' );
			$wp_customize->register_control_type( 'Aureon_Title_Customize_Control' );
			$wp_customize->register_control_type( 'Aureon_Section_Shortcut_Control' );
		}

		// Get our palettes.
		$palettes = aureon_get_default_color_palettes();

		// Add Secondary Navigation section.
		$wp_customize->add_section(
			'secondary_navigation_color_section',
			array(
				'title' => __( 'Secondary Navigation', 'aureon-studio' ),
				'capability' => 'edit_theme_options',
				'priority' => 71,
				'panel' => 'aureon_colors_panel',
			)
		);

		$wp_customize->add_control(
			new Aureon_Section_Shortcut_Control(
				$wp_customize,
				'aureon_secondary_navigation_color_shortcuts',
				array(
					'section' => 'secondary_navigation_color_section',
					'element' => __( 'Secondary Navigation', 'aureon-studio' ),
					'shortcuts' => array(
						'layout' => 'secondary_nav_section',
						'typography' => 'secondary_font_section',
						'backgrounds' => 'secondary_bg_images_section',
					),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
					'priority' => 1,
				)
			)
		);

		$wp_customize->add_control(
			new Aureon_Title_Customize_Control(
				$wp_customize,
				'aureon_secondary_navigation_items',
				array(
					'section'  => 'secondary_navigation_color_section',
					'type'     => 'aureon-customizer-title',
					'title'    => __( 'Parent Items', 'aureon-studio' ),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
				)
			)
		);

		// Background.
		$wp_customize->add_setting(
			'aureon_secondary_nav_settings[navigation_background_color]',
			array(
				'default' => $defaults['navigation_background_color'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'aureon_premium_sanitize_rgba',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new Aureon_Alpha_Color_Customize_Control(
				$wp_customize,
				'secondary_navigation_background_color',
				array(
					'label' => __( 'Background', 'aureon-studio' ),
					'section' => 'secondary_navigation_color_section',
					'settings' => 'aureon_secondary_nav_settings[navigation_background_color]',
					'palette'   => $palettes,
				)
			)
		);

		// Text.
		$wp_customize->add_setting(
			'aureon_secondary_nav_settings[navigation_text_color]',
			array(
				'default' => $defaults['navigation_text_color'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'aureon_premium_sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'secondary_navigation_text_color',
				array(
					'label' => __( 'Text', 'aureon-studio' ),
					'section' => 'secondary_navigation_color_section',
					'settings' => 'aureon_secondary_nav_settings[navigation_text_color]',
				)
			)
		);

		// Background hover.
		$wp_customize->add_setting(
			'aureon_secondary_nav_settings[navigation_background_hover_color]',
			array(
				'default' => $defaults['navigation_background_hover_color'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'aureon_premium_sanitize_rgba',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new Aureon_Alpha_Color_Customize_Control(
				$wp_customize,
				'secondary_navigation_background_hover_color',
				array(
					'label' => __( 'Background Hover', 'aureon-studio' ),
					'section' => 'secondary_navigation_color_section',
					'settings' => 'aureon_secondary_nav_settings[navigation_background_hover_color]',
					'palette'   => $palettes,
				)
			)
		);

		// Text hover.
		$wp_customize->add_setting(
			'aureon_secondary_nav_settings[navigation_text_hover_color]',
			array(
				'default' => $defaults['navigation_text_hover_color'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'aureon_premium_sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'secondary_navigation_text_hover_color',
				array(
					'label' => __( 'Text Hover', 'aureon-studio' ),
					'section' => 'secondary_navigation_color_section',
					'settings' => 'aureon_secondary_nav_settings[navigation_text_hover_color]',
				)
			)
		);

		// Background current.
		$wp_customize->add_setting(
			'aureon_secondary_nav_settings[navigation_background_current_color]',
			array(
				'default' => $defaults['navigation_background_current_color'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'aureon_premium_sanitize_rgba',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new Aureon_Alpha_Color_Customize_Control(
				$wp_customize,
				'secondary_navigation_background_current_color',
				array(
					'label' => __( 'Background Current', 'aureon-studio' ),
					'section' => 'secondary_navigation_color_section',
					'settings' => 'aureon_secondary_nav_settings[navigation_background_current_color]',
					'palette'   => $palettes,
				)
			)
		);

		// Text current.
		$wp_customize->add_setting(
			'aureon_secondary_nav_settings[navigation_text_current_color]',
			array(
				'default' => $defaults['navigation_text_current_color'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'aureon_premium_sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'secondary_navigation_text_current_color',
				array(
					'label' => __( 'Text Current', 'aureon-studio' ),
					'section' => 'secondary_navigation_color_section',
					'settings' => 'aureon_secondary_nav_settings[navigation_text_current_color]',
				)
			)
		);

		$wp_customize->add_control(
			new Aureon_Title_Customize_Control(
				$wp_customize,
				'aureon_secondary_navigation_sub_menu_items',
				array(
					'section'  => 'secondary_navigation_color_section',
					'type'     => 'aureon-customizer-title',
					'title'    => __( 'Sub-Menu Items', 'aureon-studio' ),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
				)
			)
		);

		// Background.
		$wp_customize->add_setting(
			'aureon_secondary_nav_settings[subnavigation_background_color]',
			array(
				'default' => $defaults['subnavigation_background_color'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'aureon_premium_sanitize_rgba',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new Aureon_Alpha_Color_Customize_Control(
				$wp_customize,
				'secondary_subnavigation_background_color',
				array(
					'label' => __( 'Background', 'aureon-studio' ),
					'section' => 'secondary_navigation_color_section',
					'settings' => 'aureon_secondary_nav_settings[subnavigation_background_color]',
					'palette'   => $palettes,
				)
			)
		);

		// Text.
		$wp_customize->add_setting(
			'aureon_secondary_nav_settings[subnavigation_text_color]',
			array(
				'default' => $defaults['subnavigation_text_color'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'aureon_premium_sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'secondary_subnavigation_text_color',
				array(
					'label' => __( 'Text', 'aureon-studio' ),
					'section' => 'secondary_navigation_color_section',
					'settings' => 'aureon_secondary_nav_settings[subnavigation_text_color]',
				)
			)
		);

		// Background hover.
		$wp_customize->add_setting(
			'aureon_secondary_nav_settings[subnavigation_background_hover_color]',
			array(
				'default' => $defaults['subnavigation_background_hover_color'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'aureon_premium_sanitize_rgba',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new Aureon_Alpha_Color_Customize_Control(
				$wp_customize,
				'secondary_subnavigation_background_hover_color',
				array(
					'label' => __( 'Background Hover', 'aureon-studio' ),
					'section' => 'secondary_navigation_color_section',
					'settings' => 'aureon_secondary_nav_settings[subnavigation_background_hover_color]',
					'palette'   => $palettes,
				)
			)
		);

		// Text hover.
		$wp_customize->add_setting(
			'aureon_secondary_nav_settings[subnavigation_text_hover_color]',
			array(
				'default' => $defaults['subnavigation_text_hover_color'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'aureon_premium_sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'secondary_subnavigation_text_hover_color',
				array(
					'label' => __( 'Text Hover', 'aureon-studio' ),
					'section' => 'secondary_navigation_color_section',
					'settings' => 'aureon_secondary_nav_settings[subnavigation_text_hover_color]',
				)
			)
		);

		// Background current.
		$wp_customize->add_setting(
			'aureon_secondary_nav_settings[subnavigation_background_current_color]',
			array(
				'default' => $defaults['subnavigation_background_current_color'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'aureon_premium_sanitize_rgba',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new Aureon_Alpha_Color_Customize_Control(
				$wp_customize,
				'secondary_subnavigation_background_current_color',
				array(
					'label' => __( 'Background Current', 'aureon-studio' ),
					'section' => 'secondary_navigation_color_section',
					'settings' => 'aureon_secondary_nav_settings[subnavigation_background_current_color]',
					'palette'   => $palettes,
				)
			)
		);

		// Text current.
		$wp_customize->add_setting(
			'aureon_secondary_nav_settings[subnavigation_text_current_color]',
			array(
				'default' => $defaults['subnavigation_text_current_color'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'aureon_premium_sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'secondary_subnavigation_text_current_color',
				array(
					'label' => __( 'Text Current', 'aureon-studio' ),
					'section' => 'secondary_navigation_color_section',
					'settings' => 'aureon_secondary_nav_settings[subnavigation_text_current_color]',
				)
			)
		);
	}
}
