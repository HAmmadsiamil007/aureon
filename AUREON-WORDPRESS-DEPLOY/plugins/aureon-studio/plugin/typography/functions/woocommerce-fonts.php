<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'aureon_typography_wc_customizer' ) ) {
	add_action( 'customize_register', 'aureon_typography_wc_customizer', 100 );
	/**
	 * Adds our WooCommerce color options
	 */
	function aureon_typography_wc_customizer( $wp_customize ) {
		// Bail if we don't have our defaults function
		if ( ! function_exists( 'aureon_get_default_fonts' ) ) {
			return;
		}

		// Get our custom controls
		require_once AUREON_LIBRARY_DIRECTORY . 'customizer-helpers.php';

		// Get our defaults
		$defaults = aureon_get_default_fonts();

		// Register our custom control types
		if ( method_exists( $wp_customize,'register_control_type' ) ) {
			$wp_customize->register_control_type( 'Aureon_Pro_Range_Slider_Control' );
			$wp_customize->register_control_type( 'Aureon_Pro_Typography_Customize_Control' );
			$wp_customize->register_control_type( 'Aureon_Section_Shortcut_Control' );
		}

		// Bail if WooCommerce isn't activated
		if ( ! $wp_customize->get_section( 'aureon_woocommerce_typography' ) ) {
			return;
		}

		$wp_customize->add_control(
			new Aureon_Section_Shortcut_Control(
				$wp_customize,
				'aureon_woocommerce_typography_shortcuts',
				array(
					'section' => 'aureon_woocommerce_typography',
					'element' => __( 'WooCommerce', 'aureon-studio' ),
					'shortcuts' => array(
						'layout' => 'aureon_woocommerce_layout',
						'colors' => 'aureon_woocommerce_colors',
					),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
					'priority' => 0,
				)
			)
		);

		// WooCommerce
		$wp_customize->add_setting(
			'aureon_settings[wc_product_title_font_weight]',
			array(
				'default' => $defaults['wc_product_title_font_weight'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_key',
				'transport' => 'postMessage'
			)
		);

		// Text transform
		$wp_customize->add_setting(
			'aureon_settings[wc_product_title_font_transform]',
			array(
				'default' => $defaults['wc_product_title_font_transform'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_key',
				'transport' => 'postMessage'
			)
		);

		$wp_customize->add_control(
			new Aureon_Pro_Typography_Customize_Control(
				$wp_customize,
				'google_wc_product_title_control',
				array(
					'label' => __( 'Shop Product Titles', 'aureon-studio' ),
					'section' => 'aureon_woocommerce_typography',
					'settings' => array(
						'weight' => 'aureon_settings[wc_product_title_font_weight]',
						'transform' => 'aureon_settings[wc_product_title_font_transform]',
					),
				)
			)
		);

		// Font size
		$wp_customize->add_setting(
			'aureon_settings[wc_product_title_font_size]',
			array(
				'default' => $defaults['wc_product_title_font_size'],
				'type' => 'option',
				'sanitize_callback' => 'absint',
				'transport' => 'postMessage'
			)
		);

		$wp_customize->add_setting(
			'aureon_settings[mobile_wc_product_title_font_size]',
			array(
				'default' => $defaults['mobile_wc_product_title_font_size'],
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_empty_absint',
				'transport' => 'postMessage'
			)
		);

		$wp_customize->add_control(
			new Aureon_Pro_Range_Slider_Control(
				$wp_customize,
				'aureon_settings[wc_product_title_font_size]',
				array(
					'description' => __( 'Font size', 'aureon-studio' ),
					'section' => 'aureon_woocommerce_typography',
					'priority' => 240,
					'settings' => array(
						'desktop' => 'aureon_settings[wc_product_title_font_size]',
						'mobile' => 'aureon_settings[mobile_wc_product_title_font_size]',
					),
					'choices' => array(
						'desktop' => array(
							'min' => 10,
							'max' => 80,
							'step' => 1,
							'edit' => true,
							'unit' => 'px',
						),
						'mobile' => array(
							'min' => 10,
							'max' => 80,
							'step' => 1,
							'edit' => true,
							'unit' => 'px',
						),
					),
				)
			)
		);

		// Font size
		$wp_customize->add_setting(
			'aureon_settings[wc_related_product_title_font_size]',
			array(
				'default' => $defaults['wc_related_product_title_font_size'],
				'type' => 'option',
				'sanitize_callback' => 'absint',
				'transport' => 'postMessage'
			)
		);

		$wp_customize->add_control(
			new Aureon_Pro_Range_Slider_Control(
				$wp_customize,
				'aureon_settings[wc_related_product_title_font_size]',
				array(
					'description' => __( 'Related/upsell title font size', 'aureon-studio' ),
					'section' => 'aureon_woocommerce_typography',
					'priority' => 240,
					'settings' => array(
						'desktop' => 'aureon_settings[wc_related_product_title_font_size]',
						'mobile' => 'aureon_settings[mobile_wc_product_title_font_size]',
					),
					'choices' => array(
						'desktop' => array(
							'min' => 10,
							'max' => 80,
							'step' => 1,
							'edit' => true,
							'unit' => 'px',
						),
					),
				)
			)
		);
	}
}
