<?php
/**
 * This file handles the Customizer options for the Off-Canvas Panel.
 *
 * @package Aureon Studio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

add_action( 'customize_preview_init', 'aureon_menu_plus_live_preview_scripts', 20 );
/**
 * Add live preview JS to the Customizer.
 */
function aureon_menu_plus_live_preview_scripts() {
	wp_enqueue_script( 'aureon-menu-plus-colors-customizer' );
}

add_action( 'customize_register', 'aureon_slideout_navigation_color_controls', 150 );
/**
 * Adds our Slideout Nav color options
 *
 * @since 1.6
 * @param object $wp_customize The Customizer object.
 */
function aureon_slideout_navigation_color_controls( $wp_customize ) {
	// Bail if Secondary Nav isn't activated.
	if ( ! $wp_customize->get_section( 'menu_plus_slideout_menu' ) ) {
		return;
	}

	// Bail if we don't have our color defaults.
	if ( ! function_exists( 'aureon_get_color_defaults' ) ) {
		return;
	}

	// Add our controls.
	require_once AUREON_LIBRARY_DIRECTORY . 'customizer-helpers.php';

	// Get our defaults.
	$defaults = aureon_get_color_defaults();

	// Add control types so controls can be built using JS.
	if ( method_exists( $wp_customize, 'register_control_type' ) ) {
		$wp_customize->register_control_type( 'Aureon_Alpha_Color_Customize_Control' );
		$wp_customize->register_control_type( 'Aureon_Section_Shortcut_Control' );
	}

	// Get our palettes.
	$palettes = aureon_get_default_color_palettes();

	// Add Secondary Navigation section.
	$wp_customize->add_section(
		'slideout_color_section',
		array(
			'title' => __( 'Off Canvas Panel', 'aureon-studio' ),
			'capability' => 'edit_theme_options',
			'priority' => 73,
			'panel' => 'aureon_colors_panel',
		)
	);

	$wp_customize->add_control(
		new Aureon_Section_Shortcut_Control(
			$wp_customize,
			'aureon_off_canvas_color_shortcuts',
			array(
				'section' => 'slideout_color_section',
				'element' => __( 'Off Canvas Panel', 'aureon-studio' ),
				'shortcuts' => array(
					'layout' => 'menu_plus_slideout_menu',
					'typography' => 'aureon_slideout_typography',
				),
				'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
				'priority' => 1,
			)
		)
	);

	$wp_customize->add_control(
		new Aureon_Title_Customize_Control(
			$wp_customize,
			'aureon_slideout_navigation_items',
			array(
				'section'  => 'slideout_color_section',
				'type'     => 'aureon-customizer-title',
				'title'    => __( 'Parent Menu Items', 'aureon-studio' ),
				'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
			)
		)
	);

	// Background.
	$wp_customize->add_setting(
		'aureon_settings[slideout_background_color]',
		array(
			'default' => $defaults['slideout_background_color'],
			'type' => 'option',
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'aureon_premium_sanitize_rgba',
			'transport' => 'postMessage',
		)
	);

	$wp_customize->add_control(
		new Aureon_Alpha_Color_Customize_Control(
			$wp_customize,
			'aureon_settings[slideout_background_color]',
			array(
				'label' => __( 'Background', 'aureon-studio' ),
				'section' => 'slideout_color_section',
				'settings' => 'aureon_settings[slideout_background_color]',
				'palette'   => $palettes,
			)
		)
	);

	// Text.
	$wp_customize->add_setting(
		'aureon_settings[slideout_text_color]',
		array(
			'default' => $defaults['slideout_text_color'],
			'type' => 'option',
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'aureon_premium_sanitize_hex_color',
			'transport' => 'postMessage',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'aureon_settings[slideout_text_color]',
			array(
				'label' => __( 'Text', 'aureon-studio' ),
				'section' => 'slideout_color_section',
				'settings' => 'aureon_settings[slideout_text_color]',
			)
		)
	);

	// Background hover.
	$wp_customize->add_setting(
		'aureon_settings[slideout_background_hover_color]',
		array(
			'default' => $defaults['slideout_background_hover_color'],
			'type' => 'option',
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'aureon_premium_sanitize_rgba',
			'transport' => 'postMessage',
		)
	);

	$wp_customize->add_control(
		new Aureon_Alpha_Color_Customize_Control(
			$wp_customize,
			'aureon_settings[slideout_background_hover_color]',
			array(
				'label' => __( 'Background Hover', 'aureon-studio' ),
				'section' => 'slideout_color_section',
				'settings' => 'aureon_settings[slideout_background_hover_color]',
				'palette'   => $palettes,
			)
		)
	);

	// Text hover.
	$wp_customize->add_setting(
		'aureon_settings[slideout_text_hover_color]',
		array(
			'default' => $defaults['slideout_text_hover_color'],
			'type' => 'option',
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'aureon_premium_sanitize_hex_color',
			'transport' => 'postMessage',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'aureon_settings[slideout_text_hover_color]',
			array(
				'label' => __( 'Text Hover', 'aureon-studio' ),
				'section' => 'slideout_color_section',
				'settings' => 'aureon_settings[slideout_text_hover_color]',
			)
		)
	);

	// Background current.
	$wp_customize->add_setting(
		'aureon_settings[slideout_background_current_color]',
		array(
			'default' => $defaults['slideout_background_current_color'],
			'type' => 'option',
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'aureon_premium_sanitize_rgba',
			'transport' => 'postMessage',
		)
	);

	$wp_customize->add_control(
		new Aureon_Alpha_Color_Customize_Control(
			$wp_customize,
			'aureon_settings[slideout_background_current_color]',
			array(
				'label' => __( 'Background Current', 'aureon-studio' ),
				'section' => 'slideout_color_section',
				'settings' => 'aureon_settings[slideout_background_current_color]',
				'palette'   => $palettes,
			)
		)
	);

	// Text current.
	$wp_customize->add_setting(
		'aureon_settings[slideout_text_current_color]',
		array(
			'default' => $defaults['slideout_text_current_color'],
			'type' => 'option',
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'aureon_premium_sanitize_hex_color',
			'transport' => 'postMessage',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'aureon_settings[slideout_text_current_color]',
			array(
				'label' => __( 'Text Current', 'aureon-studio' ),
				'section' => 'slideout_color_section',
				'settings' => 'aureon_settings[slideout_text_current_color]',
			)
		)
	);

	$wp_customize->add_control(
		new Aureon_Title_Customize_Control(
			$wp_customize,
			'aureon_slideout_navigation_sub_menu_items',
			array(
				'section'  => 'slideout_color_section',
				'type'     => 'aureon-customizer-title',
				'title'    => __( 'Sub-Menu Items', 'aureon-studio' ),
				'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
			)
		)
	);

	// Background.
	$wp_customize->add_setting(
		'aureon_settings[slideout_submenu_background_color]',
		array(
			'default' => $defaults['slideout_submenu_background_color'],
			'type' => 'option',
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'aureon_premium_sanitize_rgba',
			'transport' => 'postMessage',
		)
	);

	$wp_customize->add_control(
		new Aureon_Alpha_Color_Customize_Control(
			$wp_customize,
			'aureon_settings[slideout_submenu_background_color]',
			array(
				'label' => __( 'Background', 'aureon-studio' ),
				'section' => 'slideout_color_section',
				'settings' => 'aureon_settings[slideout_submenu_background_color]',
				'palette'   => $palettes,
			)
		)
	);

	// Text.
	$wp_customize->add_setting(
		'aureon_settings[slideout_submenu_text_color]',
		array(
			'default' => $defaults['slideout_submenu_text_color'],
			'type' => 'option',
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'aureon_premium_sanitize_hex_color',
			'transport' => 'postMessage',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'aureon_settings[slideout_submenu_text_color]',
			array(
				'label' => __( 'Text', 'aureon-studio' ),
				'section' => 'slideout_color_section',
				'settings' => 'aureon_settings[slideout_submenu_text_color]',
			)
		)
	);

	// Background hover.
	$wp_customize->add_setting(
		'aureon_settings[slideout_submenu_background_hover_color]',
		array(
			'default' => $defaults['slideout_submenu_background_hover_color'],
			'type' => 'option',
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'aureon_premium_sanitize_rgba',
			'transport' => 'postMessage',
		)
	);

	$wp_customize->add_control(
		new Aureon_Alpha_Color_Customize_Control(
			$wp_customize,
			'aureon_settings[slideout_submenu_background_hover_color]',
			array(
				'label' => __( 'Background Hover', 'aureon-studio' ),
				'section' => 'slideout_color_section',
				'settings' => 'aureon_settings[slideout_submenu_background_hover_color]',
				'palette'   => $palettes,
			)
		)
	);

	// Text hover.
	$wp_customize->add_setting(
		'aureon_settings[slideout_submenu_text_hover_color]',
		array(
			'default' => $defaults['slideout_submenu_text_hover_color'],
			'type' => 'option',
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'aureon_premium_sanitize_hex_color',
			'transport' => 'postMessage',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'aureon_settings[slideout_submenu_text_hover_color]',
			array(
				'label' => __( 'Text Hover', 'aureon-studio' ),
				'section' => 'slideout_color_section',
				'settings' => 'aureon_settings[slideout_submenu_text_hover_color]',
			)
		)
	);

	// Background current.
	$wp_customize->add_setting(
		'aureon_settings[slideout_submenu_background_current_color]',
		array(
			'default' => $defaults['slideout_submenu_background_current_color'],
			'type' => 'option',
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'aureon_premium_sanitize_rgba',
			'transport' => 'postMessage',
		)
	);

	$wp_customize->add_control(
		new Aureon_Alpha_Color_Customize_Control(
			$wp_customize,
			'aureon_settings[slideout_submenu_background_current_color]',
			array(
				'label' => __( 'Background Current', 'aureon-studio' ),
				'section' => 'slideout_color_section',
				'settings' => 'aureon_settings[slideout_submenu_background_current_color]',
				'palette'   => $palettes,
			)
		)
	);

	// Text current.
	$wp_customize->add_setting(
		'aureon_settings[slideout_submenu_text_current_color]',
		array(
			'default' => $defaults['slideout_submenu_text_current_color'],
			'type' => 'option',
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'aureon_premium_sanitize_hex_color',
			'transport' => 'postMessage',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'aureon_settings[slideout_submenu_text_current_color]',
			array(
				'label' => __( 'Text Current', 'aureon-studio' ),
				'section' => 'slideout_color_section',
				'settings' => 'aureon_settings[slideout_submenu_text_current_color]',
			)
		)
	);
}
