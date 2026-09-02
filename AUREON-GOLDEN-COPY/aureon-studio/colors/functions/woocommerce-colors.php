<?php
/**
 * This file handles the Customizer options for the WooCommerce module.
 *
 * @package Aureon Studio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

if ( ! function_exists( 'aureon_colors_wc_customizer' ) ) {
	add_action( 'customize_register', 'aureon_colors_wc_customizer', 100 );
	/**
	 * Adds our WooCommerce color options
	 *
	 * @param object $wp_customize The Customizer object.
	 */
	function aureon_colors_wc_customizer( $wp_customize ) {
		// Bail if WooCommerce isn't activated.
		if ( ! $wp_customize->get_section( 'aureon_woocommerce_colors' ) ) {
			return;
		}

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
			$wp_customize->register_control_type( 'Aureon_Title_Customize_Control' );
			$wp_customize->register_control_type( 'Aureon_Information_Customize_Control' );
			$wp_customize->register_control_type( 'Aureon_Section_Shortcut_Control' );
		}

		// Get our palettes.
		$palettes = aureon_get_default_color_palettes();

		$wp_customize->add_control(
			new Aureon_Section_Shortcut_Control(
				$wp_customize,
				'aureon_woocommerce_color_shortcuts',
				array(
					'section' => 'aureon_woocommerce_colors',
					'element' => __( 'WooCommerce', 'aureon-studio' ),
					'shortcuts' => array(
						'layout' => 'aureon_woocommerce_layout',
						'typography' => 'aureon_woocommerce_typography',
					),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
					'priority' => 0,
				)
			)
		);

		$wp_customize->add_control(
			new Aureon_Title_Customize_Control(
				$wp_customize,
				'aureon_woocommerce_button_title',
				array(
					'section' => 'aureon_woocommerce_colors',
					'type' => 'aureon-customizer-title',
					'title' => __( 'Buttons', 'aureon-studio' ),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
				)
			)
		);

		$wp_customize->add_control(
			new Aureon_Information_Customize_Control(
				$wp_customize,
				'aureon_woocommerce_primary_button_message',
				array(
					'section' => 'aureon_woocommerce_colors',
					'label' => __( 'Primary Button Colors', 'aureon-studio' ),
					'description' => __( 'Primary button colors can be set <a href="#">here</a>.', 'aureon-studio' ),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
				)
			)
		);

		$wp_customize->add_setting(
			'aureon_settings[wc_alt_button_background]',
			array(
				'default'     => $defaults['wc_alt_button_background'],
				'type'        => 'option',
				'capability'  => 'edit_theme_options',
				'transport'   => 'postMessage',
				'sanitize_callback' => 'aureon_premium_sanitize_rgba',
			)
		);

		$wp_customize->add_control(
			new Aureon_Alpha_Color_Customize_Control(
				$wp_customize,
				'aureon_settings[wc_alt_button_background]',
				array(
					'label'     => __( 'Alt Button Background', 'aureon-studio' ),
					'section'   => 'aureon_woocommerce_colors',
					'settings'  => 'aureon_settings[wc_alt_button_background]',
					'palette'   => $palettes,
				)
			)
		);

		$wp_customize->add_setting(
			'aureon_settings[wc_alt_button_background_hover]',
			array(
				'default'     => $defaults['wc_alt_button_background_hover'],
				'type'        => 'option',
				'capability'  => 'edit_theme_options',
				'transport'   => 'postMessage',
				'sanitize_callback' => 'aureon_premium_sanitize_rgba',
			)
		);

		$wp_customize->add_control(
			new Aureon_Alpha_Color_Customize_Control(
				$wp_customize,
				'aureon_settings[wc_alt_button_background_hover]',
				array(
					'label'     => __( 'Alt Button Background Hover', 'aureon-studio' ),
					'section'   => 'aureon_woocommerce_colors',
					'settings'  => 'aureon_settings[wc_alt_button_background_hover]',
					'palette'   => $palettes,
				)
			)
		);

		$wp_customize->add_setting(
			'aureon_settings[wc_alt_button_text]',
			array(
				'default' => $defaults['wc_alt_button_text'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'aureon_premium_sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'aureon_settings[wc_alt_button_text]',
				array(
					'label' => __( 'Alt Button Text', 'aureon-studio' ),
					'section' => 'aureon_woocommerce_colors',
					'settings' => 'aureon_settings[wc_alt_button_text]',
				)
			)
		);

		$wp_customize->add_setting(
			'aureon_settings[wc_alt_button_text_hover]',
			array(
				'default' => $defaults['wc_alt_button_text_hover'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'aureon_premium_sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'aureon_settings[wc_alt_button_text_hover]',
				array(
					'label' => __( 'Alt Button Text Hover', 'aureon-studio' ),
					'section' => 'aureon_woocommerce_colors',
					'settings' => 'aureon_settings[wc_alt_button_text_hover]',
				)
			)
		);

		$wp_customize->add_control(
			new Aureon_Title_Customize_Control(
				$wp_customize,
				'aureon_woocommerce_product_title',
				array(
					'section' => 'aureon_woocommerce_colors',
					'type' => 'aureon-customizer-title',
					'title' => __( 'Products', 'aureon-studio' ),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
				)
			)
		);

		$wp_customize->add_setting(
			'aureon_settings[wc_product_title_color]',
			array(
				'default' => $defaults['wc_product_title_color'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'aureon_premium_sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'aureon_settings[wc_product_title_color]',
				array(
					'label' => __( 'Product Title', 'aureon-studio' ),
					'section' => 'aureon_woocommerce_colors',
					'settings' => 'aureon_settings[wc_product_title_color]',
				)
			)
		);

		$wp_customize->add_setting(
			'aureon_settings[wc_product_title_color_hover]',
			array(
				'default' => $defaults['wc_product_title_color_hover'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'aureon_premium_sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'aureon_settings[wc_product_title_color_hover]',
				array(
					'label' => __( 'Product Title Hover', 'aureon-studio' ),
					'section' => 'aureon_woocommerce_colors',
					'settings' => 'aureon_settings[wc_product_title_color_hover]',
				)
			)
		);

		$wp_customize->add_setting(
			'aureon_settings[wc_rating_stars]',
			array(
				'default'     => $defaults['wc_rating_stars'],
				'type'        => 'option',
				'capability'  => 'edit_theme_options',
				'transport'   => '',
				'sanitize_callback' => 'aureon_premium_sanitize_rgba',
			)
		);

		$wp_customize->add_control(
			new Aureon_Alpha_Color_Customize_Control(
				$wp_customize,
				'aureon_settings[wc_rating_stars]',
				array(
					'label'     => __( 'Star Ratings', 'aureon-studio' ),
					'section'   => 'aureon_woocommerce_colors',
					'settings'  => 'aureon_settings[wc_rating_stars]',
					'palette'   => $palettes,
				)
			)
		);

		$wp_customize->add_setting(
			'aureon_settings[wc_sale_sticker_background]',
			array(
				'default'     => $defaults['wc_sale_sticker_background'],
				'type'        => 'option',
				'capability'  => 'edit_theme_options',
				'transport'   => 'postMessage',
				'sanitize_callback' => 'aureon_premium_sanitize_rgba',
			)
		);

		$wp_customize->add_control(
			new Aureon_Alpha_Color_Customize_Control(
				$wp_customize,
				'aureon_settings[wc_sale_sticker_background]',
				array(
					'label'     => __( 'Sale Sticker Background', 'aureon-studio' ),
					'section'   => 'aureon_woocommerce_colors',
					'settings'  => 'aureon_settings[wc_sale_sticker_background]',
					'palette'   => $palettes,
				)
			)
		);

		$wp_customize->add_setting(
			'aureon_settings[wc_sale_sticker_text]',
			array(
				'default' => $defaults['wc_sale_sticker_text'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'aureon_premium_sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'aureon_settings[wc_sale_sticker_text]',
				array(
					'label' => __( 'Sale Sticker Text', 'aureon-studio' ),
					'section' => 'aureon_woocommerce_colors',
					'settings' => 'aureon_settings[wc_sale_sticker_text]',
				)
			)
		);

		$wp_customize->add_setting(
			'aureon_settings[wc_price_color]',
			array(
				'default' => $defaults['wc_price_color'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'aureon_premium_sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'aureon_settings[wc_price_color]',
				array(
					'label' => __( 'Price', 'aureon-studio' ),
					'section' => 'aureon_woocommerce_colors',
					'settings' => 'aureon_settings[wc_price_color]',
				)
			)
		);

		$wp_customize->add_control(
			new Aureon_Title_Customize_Control(
				$wp_customize,
				'aureon_woocommerce_panel_cart_title',
				array(
					'section' => 'aureon_woocommerce_colors',
					'type' => 'aureon-customizer-title',
					'title' => __( 'Sticky Panel Cart', 'aureon-studio' ),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
				)
			)
		);

		$wp_customize->add_setting(
			'aureon_settings[wc_panel_cart_background_color]',
			array(
				'default'     => $defaults['wc_panel_cart_background_color'],
				'type'        => 'option',
				'capability'  => 'edit_theme_options',
				'transport'   => 'postMessage',
				'sanitize_callback' => 'aureon_premium_sanitize_rgba',
			)
		);

		$wp_customize->add_control(
			new Aureon_Alpha_Color_Customize_Control(
				$wp_customize,
				'aureon_settings[wc_panel_cart_background_color]',
				array(
					'label'     => __( 'Background Color', 'aureon-studio' ),
					'section'   => 'aureon_woocommerce_colors',
					'settings'  => 'aureon_settings[wc_panel_cart_background_color]',
					'palette'   => $palettes,
				)
			)
		);

		$wp_customize->add_setting(
			'aureon_settings[wc_panel_cart_text_color]',
			array(
				'default' => $defaults['wc_panel_cart_text_color'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'aureon_premium_sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'aureon_settings[wc_panel_cart_text_color]',
				array(
					'label' => __( 'Text Color', 'aureon-studio' ),
					'section' => 'aureon_woocommerce_colors',
					'settings' => 'aureon_settings[wc_panel_cart_text_color]',
				)
			)
		);

		$wp_customize->add_setting(
			'aureon_settings[wc_panel_cart_button_background]',
			array(
				'default' => $defaults['wc_panel_cart_button_background'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'aureon_premium_sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'aureon_settings[wc_panel_cart_button_background]',
				array(
					'label' => __( 'Button Background', 'aureon-studio' ),
					'section' => 'aureon_woocommerce_colors',
					'settings' => 'aureon_settings[wc_panel_cart_button_background]',
				)
			)
		);

		$wp_customize->add_setting(
			'aureon_settings[wc_panel_cart_button_background_hover]',
			array(
				'default' => $defaults['wc_panel_cart_button_background_hover'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'aureon_premium_sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'aureon_settings[wc_panel_cart_button_background_hover]',
				array(
					'label' => __( 'Button Background Hover', 'aureon-studio' ),
					'section' => 'aureon_woocommerce_colors',
					'settings' => 'aureon_settings[wc_panel_cart_button_background_hover]',
				)
			)
		);

		$wp_customize->add_setting(
			'aureon_settings[wc_panel_cart_button_text]',
			array(
				'default' => $defaults['wc_panel_cart_button_text'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'aureon_premium_sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'aureon_settings[wc_panel_cart_button_text]',
				array(
					'label' => __( 'Button Text', 'aureon-studio' ),
					'section' => 'aureon_woocommerce_colors',
					'settings' => 'aureon_settings[wc_panel_cart_button_text]',
				)
			)
		);

		$wp_customize->add_setting(
			'aureon_settings[wc_panel_cart_button_text_hover]',
			array(
				'default' => $defaults['wc_panel_cart_button_text_hover'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'aureon_premium_sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'aureon_settings[wc_panel_cart_button_text_hover]',
				array(
					'label' => __( 'Button Text Hover', 'aureon-studio' ),
					'section' => 'aureon_woocommerce_colors',
					'settings' => 'aureon_settings[wc_panel_cart_button_text_hover]',
				)
			)
		);

		$wp_customize->add_control(
			new Aureon_Title_Customize_Control(
				$wp_customize,
				'aureon_woocommerce_mini_cart_title',
				array(
					'section' => 'aureon_woocommerce_colors',
					'type' => 'aureon-customizer-title',
					'title' => __( 'Menu Mini Cart', 'aureon-studio' ),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
				)
			)
		);

		$wp_customize->add_setting(
			'aureon_settings[wc_mini_cart_background_color]',
			array(
				'default'     => $defaults['wc_mini_cart_background_color'],
				'type'        => 'option',
				'capability'  => 'edit_theme_options',
				'transport'   => 'postMessage',
				'sanitize_callback' => 'aureon_premium_sanitize_rgba',
			)
		);

		$wp_customize->add_control(
			new Aureon_Alpha_Color_Customize_Control(
				$wp_customize,
				'aureon_settings[wc_mini_cart_background_color]',
				array(
					'label'     => __( 'Cart Background Color', 'aureon-studio' ),
					'section'   => 'aureon_woocommerce_colors',
					'settings'  => 'aureon_settings[wc_mini_cart_background_color]',
					'palette'   => $palettes,
				)
			)
		);

		$wp_customize->add_setting(
			'aureon_settings[wc_mini_cart_text_color]',
			array(
				'default' => $defaults['wc_mini_cart_text_color'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'aureon_premium_sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'aureon_settings[wc_mini_cart_text_color]',
				array(
					'label' => __( 'Cart Text Color', 'aureon-studio' ),
					'section' => 'aureon_woocommerce_colors',
					'settings' => 'aureon_settings[wc_mini_cart_text_color]',
				)
			)
		);

		$wp_customize->add_setting(
			'aureon_settings[wc_mini_cart_button_background]',
			array(
				'default' => $defaults['wc_mini_cart_button_background'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'aureon_premium_sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'aureon_settings[wc_mini_cart_button_background]',
				array(
					'label' => __( 'Button Background', 'aureon-studio' ),
					'section' => 'aureon_woocommerce_colors',
					'settings' => 'aureon_settings[wc_mini_cart_button_background]',
				)
			)
		);

		$wp_customize->add_setting(
			'aureon_settings[wc_mini_cart_button_background_hover]',
			array(
				'default' => $defaults['wc_mini_cart_button_background_hover'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'aureon_premium_sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'aureon_settings[wc_mini_cart_button_background_hover]',
				array(
					'label' => __( 'Button Background Hover', 'aureon-studio' ),
					'section' => 'aureon_woocommerce_colors',
					'settings' => 'aureon_settings[wc_mini_cart_button_background_hover]',
				)
			)
		);

		$wp_customize->add_setting(
			'aureon_settings[wc_mini_cart_button_text]',
			array(
				'default' => $defaults['wc_mini_cart_button_text'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'aureon_premium_sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'aureon_settings[wc_mini_cart_button_text]',
				array(
					'label' => __( 'Button Text', 'aureon-studio' ),
					'section' => 'aureon_woocommerce_colors',
					'settings' => 'aureon_settings[wc_mini_cart_button_text]',
				)
			)
		);

		$wp_customize->add_setting(
			'aureon_settings[wc_mini_cart_button_text_hover]',
			array(
				'default' => $defaults['wc_mini_cart_button_text_hover'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'aureon_premium_sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'aureon_settings[wc_mini_cart_button_text_hover]',
				array(
					'label' => __( 'Button Text Hover', 'aureon-studio' ),
					'section' => 'aureon_woocommerce_colors',
					'settings' => 'aureon_settings[wc_mini_cart_button_text_hover]',
				)
			)
		);

		$wp_customize->add_control(
			new Aureon_Title_Customize_Control(
				$wp_customize,
				'aureon_woocommerce_price_slider_title',
				array(
					'section' => 'aureon_woocommerce_colors',
					'type' => 'aureon-customizer-title',
					'title' => __( 'Price Slider Widget', 'aureon-studio' ),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
				)
			)
		);

		$wp_customize->add_setting(
			'aureon_settings[wc_price_slider_background_color]',
			array(
				'default' => $defaults['wc_price_slider_background_color'],
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'aureon_settings[wc_price_slider_background_color]',
				array(
					'label' => __( 'Slider Background Color', 'aureon-studio' ),
					'section' => 'aureon_woocommerce_colors',
					'settings' => 'aureon_settings[wc_price_slider_background_color]',
				)
			)
		);

		$wp_customize->add_setting(
			'aureon_settings[wc_price_slider_bar_color]',
			array(
				'default' => $defaults['wc_price_slider_bar_color'],
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'aureon_settings[wc_price_slider_bar_color]',
				array(
					'label' => __( 'Slider Bar Color', 'aureon-studio' ),
					'section' => 'aureon_woocommerce_colors',
					'settings' => 'aureon_settings[wc_price_slider_bar_color]',
				)
			)
		);

		$wp_customize->add_control(
			new Aureon_Title_Customize_Control(
				$wp_customize,
				'aureon_woocommerce_product_tabs_title',
				array(
					'section' => 'aureon_woocommerce_colors',
					'type' => 'aureon-customizer-title',
					'title' => __( 'Product Tabs', 'aureon-studio' ),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
				)
			)
		);

		$wp_customize->add_setting(
			'aureon_settings[wc_product_tab]',
			array(
				'default' => $defaults['wc_product_tab'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'aureon_premium_sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'aureon_settings[wc_product_tab]',
				array(
					'label' => __( 'Product Tab Text', 'aureon-studio' ),
					'section' => 'aureon_woocommerce_colors',
					'settings' => 'aureon_settings[wc_product_tab]',
				)
			)
		);

		$wp_customize->add_setting(
			'aureon_settings[wc_product_tab_highlight]',
			array(
				'default' => $defaults['wc_product_tab_highlight'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'aureon_premium_sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'aureon_settings[wc_product_tab_highlight]',
				array(
					'label' => __( 'Product Tab Active', 'aureon-studio' ),
					'section' => 'aureon_woocommerce_colors',
					'settings' => 'aureon_settings[wc_product_tab_highlight]',
				)
			)
		);

		$wp_customize->add_control(
			new Aureon_Title_Customize_Control(
				$wp_customize,
				'aureon_woocommerce_messages_title',
				array(
					'section' => 'aureon_woocommerce_colors',
					'type' => 'aureon-customizer-title',
					'title' => __( 'Messages', 'aureon-studio' ),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
				)
			)
		);

		$wp_customize->add_setting(
			'aureon_settings[wc_success_message_background]',
			array(
				'default'     => $defaults['wc_success_message_background'],
				'type'        => 'option',
				'capability'  => 'edit_theme_options',
				'transport'   => 'postMessage',
				'sanitize_callback' => 'aureon_premium_sanitize_rgba',
			)
		);

		$wp_customize->add_control(
			new Aureon_Alpha_Color_Customize_Control(
				$wp_customize,
				'aureon_settings[wc_success_message_background]',
				array(
					'label'     => __( 'Success Message Background', 'aureon-studio' ),
					'section'   => 'aureon_woocommerce_colors',
					'settings'  => 'aureon_settings[wc_success_message_background]',
					'palette'   => $palettes,
				)
			)
		);

		$wp_customize->add_setting(
			'aureon_settings[wc_success_message_text]',
			array(
				'default' => $defaults['wc_success_message_text'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'aureon_premium_sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'aureon_settings[wc_success_message_text]',
				array(
					'label' => __( 'Success Message Text', 'aureon-studio' ),
					'section' => 'aureon_woocommerce_colors',
					'settings' => 'aureon_settings[wc_success_message_text]',
				)
			)
		);

		$wp_customize->add_setting(
			'aureon_settings[wc_info_message_background]',
			array(
				'default'     => $defaults['wc_info_message_background'],
				'type'        => 'option',
				'capability'  => 'edit_theme_options',
				'transport'   => 'postMessage',
				'sanitize_callback' => 'aureon_premium_sanitize_rgba',
			)
		);

		$wp_customize->add_control(
			new Aureon_Alpha_Color_Customize_Control(
				$wp_customize,
				'aureon_settings[wc_info_message_background]',
				array(
					'label'     => __( 'Info Message Background', 'aureon-studio' ),
					'section'   => 'aureon_woocommerce_colors',
					'settings'  => 'aureon_settings[wc_info_message_background]',
					'palette'   => $palettes,
				)
			)
		);

		$wp_customize->add_setting(
			'aureon_settings[wc_info_message_text]',
			array(
				'default' => $defaults['wc_info_message_text'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'aureon_premium_sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'aureon_settings[wc_info_message_text]',
				array(
					'label' => __( 'Info Message Text', 'aureon-studio' ),
					'section' => 'aureon_woocommerce_colors',
					'settings' => 'aureon_settings[wc_info_message_text]',
				)
			)
		);

		$wp_customize->add_setting(
			'aureon_settings[wc_error_message_background]',
			array(
				'default'     => $defaults['wc_error_message_background'],
				'type'        => 'option',
				'capability'  => 'edit_theme_options',
				'transport'   => 'postMessage',
				'sanitize_callback' => 'aureon_premium_sanitize_rgba',
			)
		);

		$wp_customize->add_control(
			new Aureon_Alpha_Color_Customize_Control(
				$wp_customize,
				'aureon_settings[wc_error_message_background]',
				array(
					'label'     => __( 'Error Message Background', 'aureon-studio' ),
					'section'   => 'aureon_woocommerce_colors',
					'settings'  => 'aureon_settings[wc_error_message_background]',
					'palette'   => $palettes,
				)
			)
		);

		$wp_customize->add_setting(
			'aureon_settings[wc_error_message_text]',
			array(
				'default' => $defaults['wc_error_message_text'],
				'type' => 'option',
				'capability' => 'edit_theme_options',
				'sanitize_callback' => 'aureon_premium_sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'aureon_settings[wc_error_message_text]',
				array(
					'label' => __( 'Error Message Text', 'aureon-studio' ),
					'section' => 'aureon_woocommerce_colors',
					'settings' => 'aureon_settings[wc_error_message_text]',
				)
			)
		);

	}
}
