<?php
/**
 * This file handles Secondary Nav background images.
 *
 * @package Aureon Studio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

if ( ! function_exists( 'aureon_backgrounds_secondary_nav_customizer' ) ) {
	add_action( 'customize_register', 'aureon_backgrounds_secondary_nav_customizer', 1000 );
	/**
	 * Adds our Secondary Nav background image options
	 *
	 * These options are in their own function so we can hook it in late to
	 * make sure Secondary Nav is activated.
	 *
	 * 1000 priority is there to make sure Secondary Nav is registered (999)
	 * as we check to see if the layout control exists.
	 *
	 * Secondary Nav now uses 100 as a priority.
	 *
	 * @param object $wp_customize Our Customizer object.
	 */
	function aureon_backgrounds_secondary_nav_customizer( $wp_customize ) {
		if ( ! function_exists( 'aureon_secondary_nav_get_defaults' ) ) {
			return;
		}

		if ( ! $wp_customize->get_section( 'secondary_nav_section' ) ) {
			return;
		}

		$defaults = aureon_secondary_nav_get_defaults();

		if ( method_exists( $wp_customize, 'register_control_type' ) ) {
			$wp_customize->register_control_type( 'Aureon_Section_Shortcut_Control' );
		}

		require_once AUREON_LIBRARY_DIRECTORY . 'customizer-helpers.php';

		$wp_customize->add_section(
			'secondary_bg_images_section',
			array(
				'title' => __( 'Secondary Navigation', 'aureon-studio' ),
				'capability' => 'edit_theme_options',
				'description' => '',
				'panel' => 'aureon_backgrounds_panel',
				'priority' => 21,
			)
		);

		$wp_customize->add_control(
			new Aureon_Section_Shortcut_Control(
				$wp_customize,
				'aureon_secondary_navigation_background_image_shortcuts',
				array(
					'section' => 'secondary_bg_images_section',
					'element' => __( 'Secondary Navigation', 'aureon-studio' ),
					'shortcuts' => array(
						'layout' => 'secondary_nav_section',
						'colors' => 'secondary_navigation_color_section',
						'typography' => 'secondary_font_section',
					),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
					'priority' => 1,
				)
			)
		);

		$wp_customize->add_setting(
			'aureon_secondary_nav_settings[nav_image]',
			array(
				'default' => $defaults['nav_image'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'esc_url_raw',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'aureon_secondary_backgrounds-nav-image',
				array(
					'section' => 'secondary_bg_images_section',
					'settings' => 'aureon_secondary_nav_settings[nav_image]',
					'priority' => 750,
					'label' => __( 'Navigation', 'aureon-studio' ),
				)
			)
		);

		$wp_customize->add_setting(
			'aureon_secondary_nav_settings[nav_repeat]',
			array(
				'default' => $defaults['nav_repeat'],
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_choices',
			)
		);

		$wp_customize->add_control(
			'aureon_secondary_nav_settings[nav_repeat]',
			array(
				'type' => 'select',
				'section' => 'secondary_bg_images_section',
				'choices' => array(
					'' => __( 'Repeat', 'aureon-studio' ),
					'repeat-x' => __( 'Repeat x', 'aureon-studio' ),
					'repeat-y' => __( 'Repeat y', 'aureon-studio' ),
					'no-repeat' => __( 'No Repeat', 'aureon-studio' ),
				),
				'settings' => 'aureon_secondary_nav_settings[nav_repeat]',
				'priority' => 800,
			)
		);

		$wp_customize->add_setting(
			'aureon_secondary_nav_settings[nav_item_image]',
			array(
				'default' => $defaults['nav_item_image'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'esc_url_raw',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'aureon_secondary_backgrounds-nav-item-image',
				array(
					'section' => 'secondary_bg_images_section',
					'settings' => 'aureon_secondary_nav_settings[nav_item_image]',
					'priority' => 950,
					'label' => __( 'Navigation Item', 'aureon-studio' ),
				)
			)
		);

		$wp_customize->add_setting(
			'aureon_secondary_nav_settings[nav_item_repeat]',
			array(
				'default' => $defaults['nav_item_repeat'],
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_choices',
			)
		);

		$wp_customize->add_control(
			'aureon_secondary_nav_settings[nav_item_repeat]',
			array(
				'type' => 'select',
				'section' => 'secondary_bg_images_section',
				'choices' => array(
					'' => __( 'Repeat', 'aureon-studio' ),
					'repeat-x' => __( 'Repeat x', 'aureon-studio' ),
					'repeat-y' => __( 'Repeat y', 'aureon-studio' ),
					'no-repeat' => __( 'No Repeat', 'aureon-studio' ),
				),
				'settings' => 'aureon_secondary_nav_settings[nav_item_repeat]',
				'priority' => 1000,
			)
		);

		$wp_customize->add_setting(
			'aureon_secondary_nav_settings[nav_item_hover_image]',
			array(
				'default' => $defaults['nav_item_hover_image'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'esc_url_raw',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'aureon_secondary_backgrounds-nav-item-hover-image',
				array(
					'section' => 'secondary_bg_images_section',
					'settings' => 'aureon_secondary_nav_settings[nav_item_hover_image]',
					'priority' => 1150,
					'label' => __( 'Navigation Item Hover', 'aureon-studio' ),
				)
			)
		);

		$wp_customize->add_setting(
			'aureon_secondary_nav_settings[nav_item_hover_repeat]',
			array(
				'default' => $defaults['nav_item_hover_repeat'],
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_choices',
			)
		);

		$wp_customize->add_control(
			'aureon_secondary_nav_settings[nav_item_hover_repeat]',
			array(
				'type' => 'select',
				'section' => 'secondary_bg_images_section',
				'choices' => array(
					'' => __( 'Repeat', 'aureon-studio' ),
					'repeat-x' => __( 'Repeat x', 'aureon-studio' ),
					'repeat-y' => __( 'Repeat y', 'aureon-studio' ),
					'no-repeat' => __( 'No Repeat', 'aureon-studio' ),
				),
				'settings' => 'aureon_secondary_nav_settings[nav_item_hover_repeat]',
				'priority' => 1200,
			)
		);

		$wp_customize->add_setting(
			'aureon_secondary_nav_settings[nav_item_current_image]',
			array(
				'default' => $defaults['nav_item_current_image'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'esc_url_raw',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'aureon_secondary_backgrounds-nav-item-current-image',
				array(
					'section' => 'secondary_bg_images_section',
					'settings' => 'aureon_secondary_nav_settings[nav_item_current_image]',
					'priority' => 1350,
					'label' => __( 'Navigation Item Current', 'aureon-studio' ),
				)
			)
		);

		$wp_customize->add_setting(
			'aureon_secondary_nav_settings[nav_item_current_repeat]',
			array(
				'default' => $defaults['nav_item_current_repeat'],
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_choices',
			)
		);

		$wp_customize->add_control(
			'aureon_secondary_nav_settings[nav_item_current_repeat]',
			array(
				'type' => 'select',
				'section' => 'secondary_bg_images_section',
				'choices' => array(
					'' => __( 'Repeat', 'aureon-studio' ),
					'repeat-x' => __( 'Repeat x', 'aureon-studio' ),
					'repeat-y' => __( 'Repeat y', 'aureon-studio' ),
					'no-repeat' => __( 'No Repeat', 'aureon-studio' ),
				),
				'settings' => 'aureon_secondary_nav_settings[nav_item_current_repeat]',
				'priority' => 1400,
			)
		);

		$wp_customize->add_section(
			'secondary_subnav_bg_images_section',
			array(
				'title' => __( 'Secondary Sub-Navigation', 'aureon-studio' ),
				'capability' => 'edit_theme_options',
				'description' => '',
				'panel' => 'aureon_backgrounds_panel',
				'priority' => 22,
			)
		);

		$wp_customize->add_setting(
			'aureon_secondary_nav_settings[sub_nav_item_image]',
			array(
				'default' => $defaults['sub_nav_item_image'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'esc_url_raw',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'aureon_secondary_backgrounds-sub-nav-item-image',
				array(
					'section' => 'secondary_subnav_bg_images_section',
					'settings' => 'aureon_secondary_nav_settings[sub_nav_item_image]',
					'priority' => 1700,
					'label' => __( 'Sub-Navigation Item', 'aureon-studio' ),
				)
			)
		);

		$wp_customize->add_setting(
			'aureon_secondary_nav_settings[sub_nav_item_repeat]',
			array(
				'default' => $defaults['sub_nav_item_repeat'],
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_choices',
			)
		);

		$wp_customize->add_control(
			'aureon_secondary_nav_settings[sub_nav_item_repeat]',
			array(
				'type' => 'select',
				'section' => 'secondary_subnav_bg_images_section',
				'choices' => array(
					'' => __( 'Repeat', 'aureon-studio' ),
					'repeat-x' => __( 'Repeat x', 'aureon-studio' ),
					'repeat-y' => __( 'Repeat y', 'aureon-studio' ),
					'no-repeat' => __( 'No Repeat', 'aureon-studio' ),
				),
				'settings' => 'aureon_secondary_nav_settings[sub_nav_item_repeat]',
				'priority' => 1800,
			)
		);

		$wp_customize->add_setting(
			'aureon_secondary_nav_settings[sub_nav_item_hover_image]',
			array(
				'default' => $defaults['sub_nav_item_hover_image'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'esc_url_raw',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'aureon_secondary_backgrounds-sub-nav-item-hover-image',
				array(
					'section' => 'secondary_subnav_bg_images_section',
					'settings' => 'aureon_secondary_nav_settings[sub_nav_item_hover_image]',
					'priority' => 2000,
					'label' => __( 'Sub-Navigation Item Hover', 'aureon-studio' ),
				)
			)
		);

		$wp_customize->add_setting(
			'aureon_secondary_nav_settings[sub_nav_item_hover_repeat]',
			array(
				'default' => $defaults['sub_nav_item_hover_repeat'],
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_choices',
			)
		);

		$wp_customize->add_control(
			'aureon_secondary_nav_settings[sub_nav_item_hover_repeat]',
			array(
				'type' => 'select',
				'section' => 'secondary_subnav_bg_images_section',
				'choices' => array(
					'' => __( 'Repeat', 'aureon-studio' ),
					'repeat-x' => __( 'Repeat x', 'aureon-studio' ),
					'repeat-y' => __( 'Repeat y', 'aureon-studio' ),
					'no-repeat' => __( 'No Repeat', 'aureon-studio' ),
				),
				'settings' => 'aureon_secondary_nav_settings[sub_nav_item_hover_repeat]',
				'priority' => 2100,
			)
		);

		$wp_customize->add_setting(
			'aureon_secondary_nav_settings[sub_nav_item_current_image]',
			array(
				'default' => $defaults['sub_nav_item_current_image'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'esc_url_raw',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'aureon_secondary_backgrounds-sub-nav-item-current-image',
				array(
					'section' => 'secondary_subnav_bg_images_section',
					'settings' => 'aureon_secondary_nav_settings[sub_nav_item_current_image]',
					'priority' => 2300,
					'label' => __( 'Sub-Navigation Item Current', 'aureon-studio' ),
				)
			)
		);

		$wp_customize->add_setting(
			'aureon_secondary_nav_settings[sub_nav_item_current_repeat]',
			array(
				'default' => $defaults['sub_nav_item_current_repeat'],
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_choices',
			)
		);

		$wp_customize->add_control(
			'aureon_secondary_nav_settings[sub_nav_item_current_repeat]',
			array(
				'type' => 'select',
				'section' => 'secondary_subnav_bg_images_section',
				'choices' => array(
					'' => __( 'Repeat', 'aureon-studio' ),
					'repeat-x' => __( 'Repeat x', 'aureon-studio' ),
					'repeat-y' => __( 'Repeat y', 'aureon-studio' ),
					'no-repeat' => __( 'No Repeat', 'aureon-studio' ),
				),
				'settings' => 'aureon_secondary_nav_settings[sub_nav_item_current_repeat]',
				'priority' => 2400,
			)
		);
	}
}
