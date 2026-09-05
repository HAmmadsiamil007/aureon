<?php
/**
 * This file handles most of our Colors functionality.
 *
 * @package Aureon Studio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

// Add necessary files.
require_once trailingslashit( dirname( __FILE__ ) ) . 'secondary-nav-colors.php';
require_once trailingslashit( dirname( __FILE__ ) ) . 'woocommerce-colors.php';
require_once trailingslashit( dirname( __FILE__ ) ) . 'slideout-nav-colors.php';

if ( ! function_exists( 'aureon_colors_customize_register' ) ) {
	add_action( 'customize_register', 'aureon_colors_customize_register', 5 );
	/**
	 * Add our Customizer options.
	 *
	 * @since 0.1
	 * @param object $wp_customize The Customizer object.
	 */
	function aureon_colors_customize_register( $wp_customize ) {
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
			$wp_customize->register_control_type( 'Aureon_Title_Customize_Control' );
			$wp_customize->register_control_type( 'Aureon_Section_Shortcut_Control' );
		}

		// Get our palettes.
		$palettes = aureon_get_default_color_palettes();

		// Add our Colors panel.
		if ( class_exists( 'WP_Customize_Panel' ) ) {
			$wp_customize->add_panel(
				'aureon_colors_panel',
				array(
					'priority'       => 30,
					'theme_supports' => '',
					'title'          => __( 'Colors', 'aureon-studio' ),
					'description'    => '',
				)
			);
		}

		$wp_customize->add_control(
			new Aureon_Section_Shortcut_Control(
				$wp_customize,
				'aureon_body_color_shortcuts',
				array(
					'section' => 'body_section',
					'element' => __( 'Body', 'aureon-studio' ),
					'shortcuts' => array(
						'typography' => 'font_section',
						'backgrounds' => 'aureon_backgrounds_body',
					),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
				)
			)
		);

		// Add Top Bar Colors section.
		if ( isset( $defaults['top_bar_background_color'] ) && function_exists( 'aureon_is_top_bar_active' ) ) {
			$wp_customize->add_section(
				'aureon_top_bar_colors',
				array(
					'title' => __( 'Top Bar', 'aureon-studio' ),
					'priority' => 40,
					'panel' => 'aureon_colors_panel',
				)
			);

			$wp_customize->add_setting(
				'aureon_settings[top_bar_background_color]',
				array(
					'default'     => $defaults['top_bar_background_color'],
					'type'        => 'option',
					'transport'   => 'postMessage',
					'sanitize_callback' => 'aureon_premium_sanitize_rgba',
				)
			);

			$wp_customize->add_control(
				new Aureon_Alpha_Color_Customize_Control(
					$wp_customize,
					'aureon_settings[top_bar_background_color]',
					array(
						'label'     => __( 'Background', 'aureon-studio' ),
						'section'   => 'aureon_top_bar_colors',
						'settings'  => 'aureon_settings[top_bar_background_color]',
						'palette'   => $palettes,
						'show_opacity'  => true,
						'priority' => 1,
						'active_callback' => 'aureon_is_top_bar_active',
					)
				)
			);

			// Add color settings.
			$top_bar_colors = array();
			$top_bar_colors[] = array(
				'slug' => 'top_bar_text_color',
				'default' => $defaults['top_bar_text_color'],
				'label' => __( 'Text', 'aureon-studio' ),
				'priority' => 2,
			);
			$top_bar_colors[] = array(
				'slug' => 'top_bar_link_color',
				'default' => $defaults['top_bar_link_color'],
				'label' => __( 'Link', 'aureon-studio' ),
				'priority' => 3,
			);
			$top_bar_colors[] = array(
				'slug' => 'top_bar_link_color_hover',
				'default' => $defaults['top_bar_link_color_hover'],
				'label' => __( 'Link Hover', 'aureon-studio' ),
				'priority' => 4,
			);

			foreach ( $top_bar_colors as $color ) {
				$wp_customize->add_setting(
					'aureon_settings[' . $color['slug'] . ']',
					array(
						'default' => $color['default'],
						'type' => 'option',
						'sanitize_callback' => 'aureon_premium_sanitize_hex_color',
						'transport' => 'postMessage',
					)
				);

				$wp_customize->add_control(
					new WP_Customize_Color_Control(
						$wp_customize,
						$color['slug'],
						array(
							'label' => $color['label'],
							'section' => 'aureon_top_bar_colors',
							'settings' => 'aureon_settings[' . $color['slug'] . ']',
							'priority' => $color['priority'],
							'palette'   => $palettes,
							'active_callback' => 'aureon_is_top_bar_active',
						)
					)
				);
			}
		}

		// Add Header Colors section.
		$wp_customize->add_section(
			'header_color_section',
			array(
				'title' => __( 'Header', 'aureon-studio' ),
				'priority' => 50,
				'panel' => 'aureon_colors_panel',
			)
		);

		$wp_customize->add_control(
			new Aureon_Section_Shortcut_Control(
				$wp_customize,
				'aureon_header_color_shortcuts',
				array(
					'section' => 'header_color_section',
					'element' => __( 'Header', 'aureon-studio' ),
					'shortcuts' => array(
						'layout' => 'aureon_layout_header',
						'typography' => 'font_header_section',
						'backgrounds' => 'aureon_backgrounds_header',
					),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
					'priority' => 1,
				)
			)
		);

		$wp_customize->add_setting(
			'aureon_settings[header_background_color]',
			array(
				'default'     => $defaults['header_background_color'],
				'type'        => 'option',
				'transport'   => 'postMessage',
				'sanitize_callback' => 'aureon_premium_sanitize_rgba',
			)
		);

		$wp_customize->add_control(
			new Aureon_Alpha_Color_Customize_Control(
				$wp_customize,
				'aureon_settings[header_background_color]',
				array(
					'label'     => __( 'Background', 'aureon-studio' ),
					'section'   => 'header_color_section',
					'settings'  => 'aureon_settings[header_background_color]',
					'palette'   => $palettes,
					'show_opacity'  => true,
					'priority' => 1,
				)
			)
		);

		// Add color settings.
		$header_colors = array();
		$header_colors[] = array(
			'slug' => 'header_text_color',
			'default' => $defaults['header_text_color'],
			'label' => __( 'Text', 'aureon-studio' ),
			'priority' => 2,
		);
		$header_colors[] = array(
			'slug' => 'header_link_color',
			'default' => $defaults['header_link_color'],
			'label' => __( 'Link', 'aureon-studio' ),
			'priority' => 3,
		);
		$header_colors[] = array(
			'slug' => 'header_link_hover_color',
			'default' => $defaults['header_link_hover_color'],
			'label' => __( 'Link Hover', 'aureon-studio' ),
			'priority' => 4,
		);
		$header_colors[] = array(
			'slug' => 'site_title_color',
			'default' => $defaults['site_title_color'],
			'label' => __( 'Site Title', 'aureon-studio' ),
			'priority' => 5,
		);
		$header_colors[] = array(
			'slug' => 'site_tagline_color',
			'default' => $defaults['site_tagline_color'],
			'label' => __( 'Tagline', 'aureon-studio' ),
			'priority' => 6,
		);

		foreach ( $header_colors as $color ) {
			$wp_customize->add_setting(
				'aureon_settings[' . $color['slug'] . ']',
				array(
					'default' => $color['default'],
					'type' => 'option',
					'sanitize_callback' => 'aureon_premium_sanitize_hex_color',
					'transport' => 'postMessage',
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					$color['slug'],
					array(
						'label' => $color['label'],
						'section' => 'header_color_section',
						'settings' => 'aureon_settings[' . $color['slug'] . ']',
						'priority' => $color['priority'],
						'palette'   => $palettes,
					)
				)
			);
		}

		// Add Navigation section.
		$wp_customize->add_section(
			'navigation_color_section',
			array(
				'title' => __( 'Primary Navigation', 'aureon-studio' ),
				'priority' => 60,
				'panel' => 'aureon_colors_panel',
			)
		);

		$wp_customize->add_control(
			new Aureon_Section_Shortcut_Control(
				$wp_customize,
				'aureon_primary_navigation_color_shortcuts',
				array(
					'section' => 'navigation_color_section',
					'element' => __( 'Primary Navigation', 'aureon-studio' ),
					'shortcuts' => array(
						'layout' => 'aureon_layout_navigation',
						'typography' => 'font_navigation_section',
						'backgrounds' => 'aureon_backgrounds_navigation',
					),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
					'priority' => 0,
				)
			)
		);

		$wp_customize->add_control(
			new Aureon_Title_Customize_Control(
				$wp_customize,
				'aureon_primary_navigation_parent_items',
				array(
					'section'  => 'navigation_color_section',
					'type'     => 'aureon-customizer-title',
					'title'    => __( 'Parent Items', 'aureon-studio' ),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
					'priority' => 1,
				)
			)
		);

		$wp_customize->add_setting(
			'aureon_settings[navigation_background_color]',
			array(
				'default'     => $defaults['navigation_background_color'],
				'type'        => 'option',
				'transport'   => 'postMessage',
				'sanitize_callback' => 'aureon_premium_sanitize_rgba',
			)
		);

		$wp_customize->add_control(
			new Aureon_Alpha_Color_Customize_Control(
				$wp_customize,
				'aureon_settings[navigation_background_color]',
				array(
					'label'     => __( 'Background', 'aureon-studio' ),
					'section'   => 'navigation_color_section',
					'settings'  => 'aureon_settings[navigation_background_color]',
					'palette'   => $palettes,
					'priority' => 1,
				)
			)
		);

		$wp_customize->add_setting(
			'aureon_settings[navigation_background_hover_color]',
			array(
				'default'     => $defaults['navigation_background_hover_color'],
				'type'        => 'option',
				'transport'   => 'postMessage',
				'sanitize_callback' => 'aureon_premium_sanitize_rgba',
			)
		);

		$wp_customize->add_control(
			new Aureon_Alpha_Color_Customize_Control(
				$wp_customize,
				'aureon_settings[navigation_background_hover_color]',
				array(
					'label'     => __( 'Background Hover', 'aureon-studio' ),
					'section'   => 'navigation_color_section',
					'settings'  => 'aureon_settings[navigation_background_hover_color]',
					'palette'   => $palettes,
					'priority' => 3,
				)
			)
		);

		$wp_customize->add_setting(
			'aureon_settings[navigation_background_current_color]',
			array(
				'default'     => $defaults['navigation_background_current_color'],
				'type'        => 'option',
				'transport'   => 'postMessage',
				'sanitize_callback' => 'aureon_premium_sanitize_rgba',
			)
		);

		$wp_customize->add_control(
			new Aureon_Alpha_Color_Customize_Control(
				$wp_customize,
				'aureon_settings[navigation_background_current_color]',
				array(
					'label'     => __( 'Background Current', 'aureon-studio' ),
					'section'   => 'navigation_color_section',
					'settings'  => 'aureon_settings[navigation_background_current_color]',
					'palette'   => $palettes,
					'priority' => 5,
				)
			)
		);

		// Add color settings.
		$navigation_colors = array();
		$navigation_colors[] = array(
			'slug' => 'navigation_text_color',
			'default' => $defaults['navigation_text_color'],
			'label' => __( 'Text', 'aureon-studio' ),
			'priority' => 2,
		);
		$navigation_colors[] = array(
			'slug' => 'navigation_text_hover_color',
			'default' => $defaults['navigation_text_hover_color'],
			'label' => __( 'Text Hover', 'aureon-studio' ),
			'priority' => 4,
		);
		$navigation_colors[] = array(
			'slug' => 'navigation_text_current_color',
			'default' => $defaults['navigation_text_current_color'],
			'label' => __( 'Text Current', 'aureon-studio' ),
			'priority' => 6,
		);

		foreach ( $navigation_colors as $color ) {
			$wp_customize->add_setting(
				'aureon_settings[' . $color['slug'] . ']',
				array(
					'default' => $color['default'],
					'type' => 'option',
					'sanitize_callback' => 'aureon_premium_sanitize_hex_color',
					'transport' => 'postMessage',
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					$color['slug'],
					array(
						'label' => $color['label'],
						'section' => 'navigation_color_section',
						'settings' => 'aureon_settings[' . $color['slug'] . ']',
						'priority' => $color['priority'],
					)
				)
			);
		}

		$wp_customize->add_control(
			new Aureon_Title_Customize_Control(
				$wp_customize,
				'aureon_primary_navigation_sub_menu_items',
				array(
					'section'  => 'navigation_color_section',
					'type'     => 'aureon-customizer-title',
					'title'    => __( 'Sub-Menu Items', 'aureon-studio' ),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
					'priority' => 7,
				)
			)
		);

		$wp_customize->add_setting(
			'aureon_settings[subnavigation_background_color]',
			array(
				'default'     => $defaults['subnavigation_background_color'],
				'type'        => 'option',
				'transport'   => 'postMessage',
				'sanitize_callback' => 'aureon_premium_sanitize_rgba',
			)
		);

		$wp_customize->add_control(
			new Aureon_Alpha_Color_Customize_Control(
				$wp_customize,
				'aureon_settings[subnavigation_background_color]',
				array(
					'label'     => __( 'Background', 'aureon-studio' ),
					'section'   => 'navigation_color_section',
					'settings'  => 'aureon_settings[subnavigation_background_color]',
					'palette'   => $palettes,
					'priority' => 8,
				)
			)
		);

		$wp_customize->add_setting(
			'aureon_settings[subnavigation_background_hover_color]',
			array(
				'default'     => $defaults['subnavigation_background_hover_color'],
				'type'        => 'option',
				'transport'   => 'postMessage',
				'sanitize_callback' => 'aureon_premium_sanitize_rgba',
			)
		);

		$wp_customize->add_control(
			new Aureon_Alpha_Color_Customize_Control(
				$wp_customize,
				'aureon_settings[subnavigation_background_hover_color]',
				array(
					'label'     => __( 'Background Hover', 'aureon-studio' ),
					'section'   => 'navigation_color_section',
					'settings'  => 'aureon_settings[subnavigation_background_hover_color]',
					'palette'   => $palettes,
					'priority' => 10,
				)
			)
		);

		$wp_customize->add_setting(
			'aureon_settings[subnavigation_background_current_color]',
			array(
				'default'     => $defaults['subnavigation_background_current_color'],
				'type'        => 'option',
				'transport'   => 'postMessage',
				'sanitize_callback' => 'aureon_premium_sanitize_rgba',
			)
		);

		$wp_customize->add_control(
			new Aureon_Alpha_Color_Customize_Control(
				$wp_customize,
				'aureon_settings[subnavigation_background_current_color]',
				array(
					'label'     => __( 'Background Current', 'aureon-studio' ),
					'section'   => 'navigation_color_section',
					'settings'  => 'aureon_settings[subnavigation_background_current_color]',
					'palette'   => $palettes,
					'priority' => 12,
				)
			)
		);

		// Add color settings.
		$subnavigation_colors = array();
		$subnavigation_colors[] = array(
			'slug' => 'subnavigation_text_color',
			'default' => $defaults['subnavigation_text_color'],
			'label' => __( 'Text', 'aureon-studio' ),
			'priority' => 9,
		);
		$subnavigation_colors[] = array(
			'slug' => 'subnavigation_text_hover_color',
			'default' => $defaults['subnavigation_text_hover_color'],
			'label' => __( 'Text Hover', 'aureon-studio' ),
			'priority' => 11,
		);
		$subnavigation_colors[] = array(
			'slug' => 'subnavigation_text_current_color',
			'default' => $defaults['subnavigation_text_current_color'],
			'label' => __( 'Text Current', 'aureon-studio' ),
			'priority' => 13,
		);

		foreach ( $subnavigation_colors as $color ) {
			$wp_customize->add_setting(
				'aureon_settings[' . $color['slug'] . ']',
				array(
					'default' => $color['default'],
					'type' => 'option',
					'sanitize_callback' => 'aureon_premium_sanitize_hex_color',
					'transport' => 'postMessage',
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					$color['slug'],
					array(
						'label' => $color['label'],
						'section' => 'navigation_color_section',
						'settings' => 'aureon_settings[' . $color['slug'] . ']',
						'priority' => $color['priority'],
					)
				)
			);
		}

		if ( isset( $defaults['navigation_search_background_color'] ) ) {
			$wp_customize->add_control(
				new Aureon_Title_Customize_Control(
					$wp_customize,
					'aureon_primary_navigation_search',
					array(
						'section'  => 'navigation_color_section',
						'type'     => 'aureon-customizer-title',
						'title'    => __( 'Navigation Search', 'aureon-studio' ),
						'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
						'priority' => 15,
					)
				)
			);

			$wp_customize->add_setting(
				'aureon_settings[navigation_search_background_color]',
				array(
					'default'     => $defaults['navigation_search_background_color'],
					'type'        => 'option',
					'transport'   => 'postMessage',
					'sanitize_callback' => 'aureon_premium_sanitize_rgba',
				)
			);

			$wp_customize->add_control(
				new Aureon_Alpha_Color_Customize_Control(
					$wp_customize,
					'aureon_settings[navigation_search_background_color]',
					array(
						'label'     => __( 'Background', 'aureon-studio' ),
						'section'   => 'navigation_color_section',
						'settings'  => 'aureon_settings[navigation_search_background_color]',
						'palette'   => $palettes,
						'priority' => 16,
					)
				)
			);

			$wp_customize->add_setting(
				'aureon_settings[navigation_search_text_color]',
				array(
					'default' => $defaults['navigation_search_text_color'],
					'type' => 'option',
					'sanitize_callback' => 'aureon_premium_sanitize_hex_color',
					'transport' => 'postMessage',
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'aureon_settings[navigation_search_text_color]',
					array(
						'label' => __( 'Text', 'aureon-studio' ),
						'section' => 'navigation_color_section',
						'settings' => 'aureon_settings[navigation_search_text_color]',
						'priority' => 17,
					)
				)
			);
		}

		$wp_customize->add_section(
			'buttons_color_section',
			array(
				'title' => __( 'Buttons', 'aureon-studio' ),
				'priority' => 75,
				'panel' => 'aureon_colors_panel',
			)
		);

		$wp_customize->add_control(
			new Aureon_Section_Shortcut_Control(
				$wp_customize,
				'aureon_buttons_color_shortcuts',
				array(
					'section' => 'buttons_color_section',
					'element' => __( 'Button', 'aureon-studio' ),
					'shortcuts' => array(
						'typography' => 'font_buttons_section',
					),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
					'priority' => 1,
				)
			)
		);

		$wp_customize->add_setting(
			'aureon_settings[form_button_background_color]',
			array(
				'default'     => $defaults['form_button_background_color'],
				'type'        => 'option',
				'transport'   => 'postMessage',
				'sanitize_callback' => 'aureon_premium_sanitize_rgba',
			)
		);

		$wp_customize->add_control(
			new Aureon_Alpha_Color_Customize_Control(
				$wp_customize,
				'aureon_settings[form_button_background_color]',
				array(
					'label'     => __( 'Background', 'aureon-studio' ),
					'section'   => 'buttons_color_section',
					'settings'  => 'aureon_settings[form_button_background_color]',
					'palette'   => $palettes,
				)
			)
		);

		$wp_customize->add_setting(
			'aureon_settings[form_button_text_color]',
			array(
				'default' => $defaults['form_button_text_color'],
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'form_button_text_color',
				array(
					'label' => __( 'Text', 'aureon-studio' ),
					'section' => 'buttons_color_section',
					'settings' => 'aureon_settings[form_button_text_color]',
				)
			)
		);

		$wp_customize->add_setting(
			'aureon_settings[form_button_background_color_hover]',
			array(
				'default'     => $defaults['form_button_background_color_hover'],
				'type'        => 'option',
				'transport'   => 'postMessage',
				'sanitize_callback' => 'aureon_premium_sanitize_rgba',
			)
		);

		$wp_customize->add_control(
			new Aureon_Alpha_Color_Customize_Control(
				$wp_customize,
				'aureon_settings[form_button_background_color_hover]',
				array(
					'label'     => __( 'Background Hover', 'aureon-studio' ),
					'section'   => 'buttons_color_section',
					'settings'  => 'aureon_settings[form_button_background_color_hover]',
					'palette'   => $palettes,
				)
			)
		);

		$wp_customize->add_setting(
			'aureon_settings[form_button_text_color_hover]',
			array(
				'default' => $defaults['form_button_text_color_hover'],
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_hex_color',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'form_button_text_color_hover',
				array(
					'label' => __( 'Text Hover', 'aureon-studio' ),
					'section' => 'buttons_color_section',
					'settings' => 'aureon_settings[form_button_text_color_hover]',
				)
			)
		);

		// Add Content Colors section.
		$wp_customize->add_section(
			'content_color_section',
			array(
				'title' => __( 'Content', 'aureon-studio' ),
				'priority' => 80,
				'panel' => 'aureon_colors_panel',
			)
		);

		$wp_customize->add_control(
			new Aureon_Section_Shortcut_Control(
				$wp_customize,
				'aureon_content_color_shortcuts',
				array(
					'section' => 'content_color_section',
					'element' => __( 'Content', 'aureon-studio' ),
					'shortcuts' => array(
						'layout' => 'aureon_layout_container',
						'typography' => 'font_content_section',
						'backgrounds' => 'aureon_backgrounds_content',
					),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
					'priority' => 1,
				)
			)
		);

		$wp_customize->add_setting(
			'aureon_settings[content_background_color]',
			array(
				'default'     => $defaults['content_background_color'],
				'type'        => 'option',
				'transport'   => 'postMessage',
				'sanitize_callback' => 'aureon_premium_sanitize_rgba',
			)
		);

		$wp_customize->add_control(
			new Aureon_Alpha_Color_Customize_Control(
				$wp_customize,
				'aureon_settings[content_background_color]',
				array(
					'label'     => __( 'Background', 'aureon-studio' ),
					'section'   => 'content_color_section',
					'settings'  => 'aureon_settings[content_background_color]',
					'palette'   => $palettes,
					'priority' => 1,
				)
			)
		);

		// Add color settings.
		$content_colors = array();
		$content_colors[] = array(
			'slug' => 'content_text_color',
			'default' => $defaults['content_text_color'],
			'label' => __( 'Text', 'aureon-studio' ),
			'priority' => 2,
		);
		$content_colors[] = array(
			'slug' => 'content_link_color',
			'default' => $defaults['content_link_color'],
			'label' => __( 'Link', 'aureon-studio' ),
			'priority' => 3,
		);
		$content_colors[] = array(
			'slug' => 'content_link_hover_color',
			'default' => $defaults['content_link_hover_color'],
			'label' => __( 'Link Hover', 'aureon-studio' ),
			'priority' => 4,
		);
		$content_colors[] = array(
			'slug' => 'content_title_color',
			'default' => $defaults['content_title_color'],
			'label' => __( 'Content Title', 'aureon-studio' ),
			'priority' => 5,
		);
		$content_colors[] = array(
			'slug' => 'blog_post_title_color',
			'default' => $defaults['blog_post_title_color'],
			'label' => __( 'Archive Content Title', 'aureon-studio' ),
			'priority' => 6,
		);
		$content_colors[] = array(
			'slug' => 'blog_post_title_hover_color',
			'default' => $defaults['blog_post_title_hover_color'],
			'label' => __( 'Archive Content Title Hover', 'aureon-studio' ),
			'priority' => 7,
		);
		$content_colors[] = array(
			'slug' => 'entry_meta_text_color',
			'default' => $defaults['entry_meta_text_color'],
			'label' => __( 'Entry Meta Text', 'aureon-studio' ),
			'priority' => 8,
		);
		$content_colors[] = array(
			'slug' => 'entry_meta_link_color',
			'default' => $defaults['entry_meta_link_color'],
			'label' => __( 'Entry Meta Links', 'aureon-studio' ),
			'priority' => 9,
		);
		$content_colors[] = array(
			'slug' => 'entry_meta_link_color_hover',
			'default' => $defaults['entry_meta_link_color_hover'],
			'label' => __( 'Entry Meta Links Hover', 'aureon-studio' ),
			'priority' => 10,
		);
		$content_colors[] = array(
			'slug' => 'h1_color',
			'default' => $defaults['h1_color'],
			'label' => __( 'Heading 1 (H1) Color', 'aureon-studio' ),
			'priority' => 11,
		);
		$content_colors[] = array(
			'slug' => 'h2_color',
			'default' => $defaults['h2_color'],
			'label' => __( 'Heading 2 (H2) Color', 'aureon-studio' ),
			'priority' => 12,
		);
		$content_colors[] = array(
			'slug' => 'h3_color',
			'default' => $defaults['h3_color'],
			'label' => __( 'Heading 3 (H3) Color', 'aureon-studio' ),
			'priority' => 13,
		);

		if ( isset( $defaults['h4_color'] ) ) {
			$content_colors[] = array(
				'slug' => 'h4_color',
				'default' => $defaults['h4_color'],
				'label' => __( 'Heading 4 (H4) Color', 'aureon-studio' ),
				'priority' => 14,
			);
		}

		if ( isset( $defaults['h5_color'] ) ) {
			$content_colors[] = array(
				'slug' => 'h5_color',
				'default' => $defaults['h5_color'],
				'label' => __( 'Heading 5 (H5) Color', 'aureon-studio' ),
				'priority' => 15,
			);
		}

		foreach ( $content_colors as $color ) {
			$wp_customize->add_setting(
				'aureon_settings[' . $color['slug'] . ']',
				array(
					'default' => $color['default'],
					'type' => 'option',
					'sanitize_callback' => 'aureon_premium_sanitize_hex_color',
					'transport' => 'postMessage',
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					$color['slug'],
					array(
						'label' => $color['label'],
						'section' => 'content_color_section',
						'settings' => 'aureon_settings[' . $color['slug'] . ']',
						'priority' => $color['priority'],
					)
				)
			);
		}

		// Add Sidebar Widget colors.
		$wp_customize->add_section(
			'sidebar_widget_color_section',
			array(
				'title' => __( 'Sidebar Widgets', 'aureon-studio' ),
				'priority' => 90,
				'panel' => 'aureon_colors_panel',
			)
		);

		$wp_customize->add_control(
			new Aureon_Section_Shortcut_Control(
				$wp_customize,
				'aureon_sidebar_color_shortcuts',
				array(
					'section' => 'sidebar_widget_color_section',
					'element' => __( 'Sidebar', 'aureon-studio' ),
					'shortcuts' => array(
						'layout' => 'aureon_layout_sidebars',
						'typography' => 'font_widget_section',
						'backgrounds' => 'aureon_backgrounds_sidebars',
					),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
					'priority' => 1,
				)
			)
		);

		$wp_customize->add_setting(
			'aureon_settings[sidebar_widget_background_color]',
			array(
				'default'     => $defaults['sidebar_widget_background_color'],
				'type'        => 'option',
				'transport'   => 'postMessage',
				'sanitize_callback' => 'aureon_premium_sanitize_rgba',
			)
		);

		$wp_customize->add_control(
			new Aureon_Alpha_Color_Customize_Control(
				$wp_customize,
				'aureon_settings[sidebar_widget_background_color]',
				array(
					'label'     => __( 'Background', 'aureon-studio' ),
					'section'   => 'sidebar_widget_color_section',
					'settings'  => 'aureon_settings[sidebar_widget_background_color]',
					'palette'   => $palettes,
					'priority' => 1,
				)
			)
		);

		// Add color settings.
		$sidebar_widget_colors = array();
		$sidebar_widget_colors[] = array(
			'slug' => 'sidebar_widget_text_color',
			'default' => $defaults['sidebar_widget_text_color'],
			'label' => __( 'Text', 'aureon-studio' ),
			'priority' => 2,
		);
		$sidebar_widget_colors[] = array(
			'slug' => 'sidebar_widget_link_color',
			'default' => $defaults['sidebar_widget_link_color'],
			'label' => __( 'Link', 'aureon-studio' ),
			'priority' => 3,
		);
		$sidebar_widget_colors[] = array(
			'slug' => 'sidebar_widget_link_hover_color',
			'default' => $defaults['sidebar_widget_link_hover_color'],
			'label' => __( 'Link Hover', 'aureon-studio' ),
			'priority' => 4,
		);
		$sidebar_widget_colors[] = array(
			'slug' => 'sidebar_widget_title_color',
			'default' => $defaults['sidebar_widget_title_color'],
			'label' => __( 'Widget Title', 'aureon-studio' ),
			'priority' => 5,
		);

		foreach ( $sidebar_widget_colors as $color ) {
			$wp_customize->add_setting(
				'aureon_settings[' . $color['slug'] . ']',
				array(
					'default' => $color['default'],
					'type' => 'option',
					'sanitize_callback' => 'aureon_premium_sanitize_hex_color',
					'transport' => 'postMessage',
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					$color['slug'],
					array(
						'label' => $color['label'],
						'section' => 'sidebar_widget_color_section',
						'settings' => 'aureon_settings[' . $color['slug'] . ']',
						'priority' => $color['priority'],
					)
				)
			);
		}

		// Add Form colors.
		$wp_customize->add_section(
			'form_color_section',
			array(
				'title' => __( 'Forms', 'aureon-studio' ),
				'priority' => 130,
				'panel' => 'aureon_colors_panel',
			)
		);

		$wp_customize->add_setting(
			'aureon_settings[form_background_color]',
			array(
				'default'     => $defaults['form_background_color'],
				'type'        => 'option',
				'transport'   => 'postMessage',
				'sanitize_callback' => 'aureon_premium_sanitize_rgba',
			)
		);

		$wp_customize->add_control(
			new Aureon_Alpha_Color_Customize_Control(
				$wp_customize,
				'aureon_settings[form_background_color]',
				array(
					'label'     => __( 'Form Background', 'aureon-studio' ),
					'section'   => 'form_color_section',
					'settings'  => 'aureon_settings[form_background_color]',
					'palette'   => $palettes,
					'priority' => 1,
				)
			)
		);

		$wp_customize->add_setting(
			'aureon_settings[form_background_color_focus]',
			array(
				'default'     => $defaults['form_background_color_focus'],
				'type'        => 'option',
				'transport'   => 'postMessage',
				'sanitize_callback' => 'aureon_premium_sanitize_rgba',
			)
		);

		$wp_customize->add_control(
			new Aureon_Alpha_Color_Customize_Control(
				$wp_customize,
				'aureon_settings[form_background_color_focus]',
				array(
					'label'     => __( 'Form Background Focus', 'aureon-studio' ),
					'section'   => 'form_color_section',
					'settings'  => 'aureon_settings[form_background_color_focus]',
					'palette'   => $palettes,
					'priority' => 3,
				)
			)
		);

		$wp_customize->add_setting(
			'aureon_settings[form_border_color]',
			array(
				'default'     => $defaults['form_border_color'],
				'type'        => 'option',
				'transport'   => 'postMessage',
				'sanitize_callback' => 'aureon_premium_sanitize_rgba',
			)
		);

		$wp_customize->add_control(
			new Aureon_Alpha_Color_Customize_Control(
				$wp_customize,
				'aureon_settings[form_border_color]',
				array(
					'label'     => __( 'Form Border', 'aureon-studio' ),
					'section'   => 'form_color_section',
					'settings'  => 'aureon_settings[form_border_color]',
					'palette'   => $palettes,
					'priority' => 5,
				)
			)
		);

		$wp_customize->add_setting(
			'aureon_settings[form_border_color_focus]',
			array(
				'default'     => $defaults['form_border_color_focus'],
				'type'        => 'option',
				'transport'   => 'postMessage',
				'sanitize_callback' => 'aureon_premium_sanitize_rgba',
			)
		);

		$wp_customize->add_control(
			new Aureon_Alpha_Color_Customize_Control(
				$wp_customize,
				'aureon_settings[form_border_color_focus]',
				array(
					'label'     => __( 'Form Border Focus', 'aureon-studio' ),
					'section'   => 'form_color_section',
					'settings'  => 'aureon_settings[form_border_color_focus]',
					'palette'   => $palettes,
					'priority' => 6,
				)
			)
		);

		// Add color settings.
		$form_colors = array();
		$form_colors[] = array(
			'slug' => 'form_text_color',
			'default' => $defaults['form_text_color'],
			'label' => __( 'Form Text', 'aureon-studio' ),
			'priority' => 2,
		);
		$form_colors[] = array(
			'slug' => 'form_text_color_focus',
			'default' => $defaults['form_text_color_focus'],
			'label' => __( 'Form Text Focus', 'aureon-studio' ),
			'priority' => 4,
		);

		foreach ( $form_colors as $color ) {
			$wp_customize->add_setting(
				'aureon_settings[' . $color['slug'] . ']',
				array(
					'default' => $color['default'],
					'type' => 'option',
					'sanitize_callback' => 'aureon_premium_sanitize_hex_color',
					'transport' => 'postMessage',
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					$color['slug'],
					array(
						'label' => $color['label'],
						'section' => 'form_color_section',
						'settings' => 'aureon_settings[' . $color['slug'] . ']',
						'priority' => $color['priority'],
					)
				)
			);
		}

		// Add Footer colors.
		$wp_customize->add_section(
			'footer_color_section',
			array(
				'title' => __( 'Footer', 'aureon-studio' ),
				'priority' => 150,
				'panel' => 'aureon_colors_panel',
			)
		);

		$wp_customize->add_control(
			new Aureon_Section_Shortcut_Control(
				$wp_customize,
				'aureon_footer_color_shortcuts',
				array(
					'section' => 'footer_color_section',
					'element' => __( 'Footer', 'aureon-studio' ),
					'shortcuts' => array(
						'layout' => 'aureon_layout_footer',
						'typography' => 'font_footer_section',
						'backgrounds' => 'aureon_backgrounds_footer',
					),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
					'priority' => 1,
				)
			)
		);

		$wp_customize->add_control(
			new Aureon_Title_Customize_Control(
				$wp_customize,
				'aureon_footer_widgets_title',
				array(
					'section' => 'footer_color_section',
					'type' => 'aureon-customizer-title',
					'title' => __( 'Footer Widgets', 'aureon-studio' ),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
				)
			)
		);

		$wp_customize->add_setting(
			'aureon_settings[footer_widget_background_color]',
			array(
				'default'     => $defaults['footer_widget_background_color'],
				'type'        => 'option',
				'transport'   => 'postMessage',
				'sanitize_callback' => 'aureon_premium_sanitize_rgba',
			)
		);

		$wp_customize->add_control(
			new Aureon_Alpha_Color_Customize_Control(
				$wp_customize,
				'aureon_settings[footer_widget_background_color]',
				array(
					'label'     => __( 'Background', 'aureon-studio' ),
					'section'   => 'footer_color_section',
					'settings'  => 'aureon_settings[footer_widget_background_color]',
					'palette'   => $palettes,
				)
			)
		);

		// Add color settings.
		$footer_widget_colors = array();
		$footer_widget_colors[] = array(
			'slug' => 'footer_widget_text_color',
			'default' => $defaults['footer_widget_text_color'],
			'label' => __( 'Text', 'aureon-studio' ),
		);
		$footer_widget_colors[] = array(
			'slug' => 'footer_widget_link_color',
			'default' => $defaults['footer_widget_link_color'],
			'label' => __( 'Link', 'aureon-studio' ),
		);
		$footer_widget_colors[] = array(
			'slug' => 'footer_widget_link_hover_color',
			'default' => $defaults['footer_widget_link_hover_color'],
			'label' => __( 'Link Hover', 'aureon-studio' ),
		);
		$footer_widget_colors[] = array(
			'slug' => 'footer_widget_title_color',
			'default' => $defaults['footer_widget_title_color'],
			'label' => __( 'Widget Title', 'aureon-studio' ),
		);

		foreach ( $footer_widget_colors as $color ) {
			$wp_customize->add_setting(
				'aureon_settings[' . $color['slug'] . ']',
				array(
					'default' => $color['default'],
					'type' => 'option',
					'sanitize_callback' => 'aureon_premium_sanitize_hex_color',
					'transport' => 'postMessage',
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					$color['slug'],
					array(
						'label' => $color['label'],
						'section' => 'footer_color_section',
						'settings' => 'aureon_settings[' . $color['slug'] . ']',
					)
				)
			);
		}

		$wp_customize->add_control(
			new Aureon_Title_Customize_Control(
				$wp_customize,
				'aureon_footer_title',
				array(
					'section' => 'footer_color_section',
					'type' => 'aureon-customizer-title',
					'title' => __( 'Footer Bar', 'aureon-studio' ),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
				)
			)
		);

		$wp_customize->add_setting(
			'aureon_settings[footer_background_color]',
			array(
				'default'     => $defaults['footer_background_color'],
				'type'        => 'option',
				'transport'   => 'postMessage',
				'sanitize_callback' => 'aureon_premium_sanitize_rgba',
			)
		);

		$wp_customize->add_control(
			new Aureon_Alpha_Color_Customize_Control(
				$wp_customize,
				'aureon_settings[footer_background_color]',
				array(
					'label'     => __( 'Background', 'aureon-studio' ),
					'section'   => 'footer_color_section',
					'settings'  => 'aureon_settings[footer_background_color]',
					'palette'   => $palettes,
				)
			)
		);

		// Add color settings.
		$footer_colors = array();
		$footer_colors[] = array(
			'slug' => 'footer_text_color',
			'default' => $defaults['footer_text_color'],
			'label' => __( 'Text', 'aureon-studio' ),
		);
		$footer_colors[] = array(
			'slug' => 'footer_link_color',
			'default' => $defaults['footer_link_color'],
			'label' => __( 'Link', 'aureon-studio' ),
		);
		$footer_colors[] = array(
			'slug' => 'footer_link_hover_color',
			'default' => $defaults['footer_link_hover_color'],
			'label' => __( 'Link Hover', 'aureon-studio' ),
		);

		foreach ( $footer_colors as $color ) {
			$wp_customize->add_setting(
				'aureon_settings[' . $color['slug'] . ']',
				array(
					'default' => $color['default'],
					'type' => 'option',
					'sanitize_callback' => 'aureon_premium_sanitize_hex_color',
					'transport' => 'postMessage',
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					$color['slug'],
					array(
						'label' => $color['label'],
						'section' => 'footer_color_section',
						'settings' => 'aureon_settings[' . $color['slug'] . ']',
					)
				)
			);
		}

		if ( isset( $defaults['back_to_top_background_color'] ) ) {
			$wp_customize->add_control(
				new Aureon_Title_Customize_Control(
					$wp_customize,
					'aureon_back_to_top_title',
					array(
						'section' => 'footer_color_section',
						'type' => 'aureon-customizer-title',
						'title' => __( 'Back to Top Button', 'aureon-studio' ),
						'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
					)
				)
			);

			$wp_customize->add_setting(
				'aureon_settings[back_to_top_background_color]',
				array(
					'default' => $defaults['back_to_top_background_color'],
					'type' => 'option',
					'sanitize_callback' => 'aureon_premium_sanitize_rgba',
					'transport' => 'postMessage',
				)
			);

			$wp_customize->add_control(
				new Aureon_Alpha_Color_Customize_Control(
					$wp_customize,
					'aureon_settings[back_to_top_background_color]',
					array(
						'label' => __( 'Background', 'aureon-studio' ),
						'section' => 'footer_color_section',
						'settings' => 'aureon_settings[back_to_top_background_color]',
						'palette' => $palettes,
					)
				)
			);

			$wp_customize->add_setting(
				'aureon_settings[back_to_top_text_color]',
				array(
					'default' => $defaults['back_to_top_text_color'],
					'type' => 'option',
					'sanitize_callback' => 'aureon_premium_sanitize_rgba',
					'transport' => 'postMessage',
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'aureon_settings[back_to_top_text_color]',
					array(
						'label' => __( 'Text', 'aureon-studio' ),
						'section' => 'footer_color_section',
						'settings' => 'aureon_settings[back_to_top_text_color]',
					)
				)
			);

			$wp_customize->add_setting(
				'aureon_settings[back_to_top_background_color_hover]',
				array(
					'default' => $defaults['back_to_top_background_color_hover'],
					'type' => 'option',
					'sanitize_callback' => 'aureon_premium_sanitize_rgba',
					'transport' => 'postMessage',
				)
			);

			$wp_customize->add_control(
				new Aureon_Alpha_Color_Customize_Control(
					$wp_customize,
					'aureon_settings[back_to_top_background_color_hover]',
					array(
						'label'     => __( 'Background Hover', 'aureon-studio' ),
						'section'   => 'footer_color_section',
						'settings'  => 'aureon_settings[back_to_top_background_color_hover]',
						'palette'   => $palettes,
					)
				)
			);

			$wp_customize->add_setting(
				'aureon_settings[back_to_top_text_color_hover]',
				array(
					'default' => $defaults['back_to_top_text_color_hover'],
					'type' => 'option',
					'sanitize_callback' => 'aureon_premium_sanitize_rgba',
					'transport' => 'postMessage',
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'aureon_settings[back_to_top_text_color_hover]',
					array(
						'label' => __( 'Text Hover', 'aureon-studio' ),
						'section' => 'footer_color_section',
						'settings' => 'aureon_settings[back_to_top_text_color_hover]',
					)
				)
			);
		}
	}
}

if ( ! function_exists( 'aureon_get_color_setting' ) ) {
	/**
	 * Wrapper function to get our settings
	 *
	 * @since 1.3.42
	 * @param string $setting The setting to check.
	 */
	function aureon_get_color_setting( $setting ) {

		// Bail if we don't have our color defaults.
		if ( ! function_exists( 'aureon_get_color_defaults' ) ) {
			return;
		}

		if ( function_exists( 'aureon_get_defaults' ) ) {
			$defaults = array_merge( aureon_get_defaults(), aureon_get_color_defaults() );
		} else {
			$defaults = aureon_get_color_defaults();
		}

		$aureon_settings = wp_parse_args(
			get_option( 'aureon_settings', array() ),
			$defaults
		);

		return $aureon_settings[ $setting ];
	}
}

if ( ! function_exists( 'aureon_colors_rgba_to_hex' ) ) {
	/**
	 * Convert RGBA to hex if necessary
	 *
	 * @since 1.3.42
	 * @param string $rgba The string to convert to hex.
	 */
	function aureon_colors_rgba_to_hex( $rgba ) {
		// If it's not rgba, return it.
		if ( false === strpos( $rgba, 'rgba' ) ) {
			return $rgba;
		}

		return substr( $rgba, 0, strrpos( $rgba, ',' ) ) . ')';
	}
}

if ( ! function_exists( 'aureon_get_default_color_palettes' ) ) {
	/**
	 * Set up our colors for the color picker palettes and filter them so you can change them
	 *
	 * @since 1.3.42
	 */
	function aureon_get_default_color_palettes() {
		$palettes = array(
			aureon_colors_rgba_to_hex( aureon_get_color_setting( 'link_color' ) ),
			aureon_colors_rgba_to_hex( aureon_get_color_setting( 'background_color' ) ),
			aureon_colors_rgba_to_hex( aureon_get_color_setting( 'navigation_background_color' ) ),
			aureon_colors_rgba_to_hex( aureon_get_color_setting( 'navigation_background_hover_color' ) ),
			'#F1C40F',
			'#1e72bd',
			'#1ABC9C',
			'#3498DB',
		);

		return apply_filters( 'aureon_default_color_palettes', $palettes );
	}
}

if ( ! function_exists( 'aureon_enqueue_color_palettes' ) ) {
	add_action( 'customize_controls_enqueue_scripts', 'aureon_enqueue_color_palettes', 1001 );
	/**
	 * Add our custom color palettes to the color pickers in the Customizer.
	 * Hooks into 1001 priority to show up after Secondary Nav.
	 *
	 * @since 1.3.42
	 */
	function aureon_enqueue_color_palettes() {
		// Old versions of WP don't get nice things.
		if ( ! function_exists( 'wp_add_inline_script' ) ) {
			return;
		}

		// Grab our palette array and turn it into JS.
		$palettes = wp_json_encode( aureon_get_default_color_palettes() );

		// Add our custom palettes.
		// json_encode takes care of escaping.
		wp_add_inline_script( 'wp-color-picker', 'jQuery.wp.wpColorPicker.prototype.options.palettes = ' . $palettes . ';' );
	}
}

if ( ! function_exists( 'aureon_colors_customizer_live_preview' ) ) {
	add_action( 'customize_preview_init', 'aureon_colors_customizer_live_preview' );
	/**
	 * Add our live preview javascript.
	 *
	 * @since 0.1
	 */
	function aureon_colors_customizer_live_preview() {
		wp_enqueue_script(
			'aureon-colors-customizer',
			trailingslashit( plugin_dir_url( __FILE__ ) ) . 'js/customizer.js',
			array( 'jquery', 'customize-preview' ),
			AUREON_COLORS_VERSION,
			true
		);

		wp_register_script(
			'aureon-wc-colors-customizer',
			trailingslashit( plugin_dir_url( __FILE__ ) ) . 'js/wc-customizer.js',
			array( 'jquery', 'customize-preview', 'aureon-colors-customizer' ),
			AUREON_COLORS_VERSION,
			true
		);

		wp_register_script(
			'aureon-menu-plus-colors-customizer',
			trailingslashit( plugin_dir_url( __FILE__ ) ) . 'js/menu-plus-customizer.js',
			array( 'jquery', 'customize-preview', 'aureon-colors-customizer' ),
			AUREON_COLORS_VERSION,
			true
		);
	}
}
