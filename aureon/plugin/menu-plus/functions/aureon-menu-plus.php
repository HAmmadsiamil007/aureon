<?php
/**
 * This file handles the Menu Plus module functionality.
 *
 * @package Aureon Studio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

if ( ! function_exists( 'aureon_menu_plus_setup' ) ) {
	add_action( 'after_setup_theme', 'aureon_menu_plus_setup', 50 );
	/**
	 * Register the slide-out menu
	 */
	function aureon_menu_plus_setup() {
		register_nav_menus(
			array(
				'slideout' => __( 'Off Canvas Menu', 'aureon-studio' ),
			)
		);
	}
}

if ( ! function_exists( 'aureon_menu_plus_get_defaults' ) ) {
	/**
	 * Set default options
	 */
	function aureon_menu_plus_get_defaults() {
		return apply_filters(
			'aureon_menu_plus_option_defaults',
			array(
				'mobile_menu_label' => __( 'Menu', 'aureon-studio' ),
				'sticky_menu' => 'false',
				'sticky_menu_effect' => 'fade',
				'sticky_menu_logo' => '', // Deprecated since 1.8.
				'sticky_menu_logo_position' => 'sticky-menu', // Deprecated since 1.8.
				'mobile_header' => 'disable',
				'mobile_menu_breakpoint' => '768',
				'mobile_header_logo' => '',
				'mobile_header_sticky' => 'disable',
				'mobile_header_branding' => 'logo',
				'slideout_menu' => 'false',
				'off_canvas_desktop_toggle_label' => '',
				'slideout_menu_side' => 'left',
				'slideout_menu_style' => 'slide',
				'slideout_close_button' => 'outside',
				'auto_hide_sticky' => false,
				'mobile_header_auto_hide_sticky' => false,
				'sticky_navigation_logo' => '',
				'navigation_as_header' => false,
			)
		);
	}
}

add_filter( 'aureon_color_option_defaults', 'aureon_menu_plus_color_defaults' );
/**
 * Set the Menu Plus color defaults
 *
 * @since 1.6
 * @param array $defaults Existing defaults.
 */
function aureon_menu_plus_color_defaults( $defaults ) {
	$defaults['slideout_background_color'] = '';
	$defaults['slideout_text_color'] = '';
	$defaults['slideout_background_hover_color'] = '';
	$defaults['slideout_text_hover_color'] = '';
	$defaults['slideout_background_current_color'] = '';
	$defaults['slideout_text_current_color'] = '';
	$defaults['slideout_submenu_background_color'] = '';
	$defaults['slideout_submenu_text_color'] = '';
	$defaults['slideout_submenu_background_hover_color'] = '';
	$defaults['slideout_submenu_text_hover_color'] = '';
	$defaults['slideout_submenu_background_current_color'] = '';
	$defaults['slideout_submenu_text_current_color'] = '';

	return $defaults;
}

add_filter( 'aureon_font_option_defaults', 'aureon_menu_plus_typography_defaults' );
/**
 * Set the Menu Plus typography option defaults.
 *
 * @since 1.6
 * @param array $defaults Existing defaults.
 */
function aureon_menu_plus_typography_defaults( $defaults ) {
	$defaults['slideout_font_weight'] = 'normal';
	$defaults['slideout_font_transform'] = 'none';
	$defaults['slideout_font_size'] = '';
	$defaults['slideout_mobile_font_size'] = '';

	return $defaults;
}

if ( ! function_exists( 'aureon_menu_plus_customize_register' ) ) {
	add_action( 'customize_register', 'aureon_menu_plus_customize_register', 100 );
	/**
	 * Initiate Customizer controls.
	 *
	 * @param object $wp_customize The Customizer object.
	 */
	function aureon_menu_plus_customize_register( $wp_customize ) {
		if ( version_compare( PHP_VERSION, '5.6', '<' ) ) {
			return;
		}

		$defaults = aureon_menu_plus_get_defaults();

		$settings = wp_parse_args(
			get_option( 'aureon_menu_plus_settings', array() ),
			aureon_menu_plus_get_defaults()
		);

		require_once AUREON_LIBRARY_DIRECTORY . 'customizer-helpers.php';

		if ( method_exists( $wp_customize, 'register_control_type' ) ) {
			$wp_customize->register_control_type( 'Aureon_Action_Button_Control' );
			$wp_customize->register_control_type( 'Aureon_Section_Shortcut_Control' );
			$wp_customize->register_control_type( 'Aureon_Pro_Range_Slider_Control' );
			$wp_customize->register_control_type( 'Aureon_Information_Customize_Control' );
		}

		// Add our old Menu Plus panel.
		// This panel shouldn't display anymore but is left for back compat.
		if ( class_exists( 'WP_Customize_Panel' ) ) {
			if ( ! $wp_customize->get_panel( 'aureon_menu_plus' ) ) {
				$wp_customize->add_panel(
					'aureon_menu_plus',
					array(
						'priority'       => 50,
						'capability'     => 'edit_theme_options',
						'theme_supports' => '',
						'title'          => esc_html__( 'Menu Plus', 'aureon-studio' ),
						'description'    => '',
					)
				);
			}
		}

		// Add our options to the Layout panel if it exists.
		// The layout panel is in the free theme, so we have the fallback in case people haven't updated.
		if ( $wp_customize->get_panel( 'aureon_layout_panel' ) ) {
			$panel = 'aureon_layout_panel';
			$navigation_section = 'aureon_layout_navigation';
			$header_section = 'aureon_layout_header';
			$sticky_menu_section = 'aureon_layout_navigation';
		} else {
			$panel = 'aureon_menu_plus';
			$navigation_section = 'menu_plus_section';
			$header_section = 'menu_plus_mobile_header';
			$sticky_menu_section = 'menu_plus_sticky_menu';
		}

		// Add Menu Plus section.
		// This section shouldn't display anymore for the above reasons.
		$wp_customize->add_section(
			'menu_plus_section',
			array(
				'title' => esc_html__( 'General Settings', 'aureon-studio' ),
				'capability' => 'edit_theme_options',
				'panel' => 'aureon_menu_plus',
			)
		);

		$wp_customize->add_setting(
			'aureon_menu_plus_settings[mobile_menu_label]',
			array(
				'default' => $defaults['mobile_menu_label'],
				'type' => 'option',
				'sanitize_callback' => 'wp_kses_post',
			)
		);

		$wp_customize->add_control(
			'mobile_menu_label_control',
			array(
				'label' => esc_html__( 'Mobile Menu Label', 'aureon-studio' ),
				'section' => $navigation_section,
				'settings' => 'aureon_menu_plus_settings[mobile_menu_label]',
			)
		);

		$wp_customize->add_setting(
			'aureon_menu_plus_settings[mobile_menu_breakpoint]',
			array(
				'default' => $defaults['mobile_menu_breakpoint'],
				'type' => 'option',
				'sanitize_callback' => 'absint',
			)
		);

		if ( defined( 'AUREON_VERSION' ) && version_compare( AUREON_VERSION, '2.3-alpha.1', '>=' ) ) {
			$wp_customize->add_control(
				new Aureon_Pro_Range_Slider_Control(
					$wp_customize,
					'aureon_menu_plus_settings[mobile_menu_breakpoint]',
					array(
						'label' => esc_html__( 'Mobile Menu Breakpoint', 'aureon-studio' ),
						'section' => $navigation_section,
						'settings' => array(
							'desktop' => 'aureon_menu_plus_settings[mobile_menu_breakpoint]',
						),
						'choices' => array(
							'desktop' => array(
								'min' => 0,
								'max' => 2000,
								'step' => 5,
								'edit' => true,
								'unit' => 'px',
							),
						),
					)
				)
			);
		}

		$wp_customize->add_section(
			'menu_plus_sticky_menu',
			array(
				'title' => esc_html__( 'Sticky Navigation', 'aureon-studio' ),
				'capability' => 'edit_theme_options',
				'panel' => $panel,
				'priority' => 33,
			)
		);

		$wp_customize->add_setting(
			'aureon_menu_plus_settings[sticky_menu]',
			array(
				'default' => $defaults['sticky_menu'],
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_choices',
			)
		);

		$wp_customize->add_control(
			'aureon_menu_plus_settings[sticky_menu]',
			array(
				'type' => 'select',
				'label' => esc_html__( 'Sticky Navigation', 'aureon-studio' ),
				'section' => 'menu_plus_sticky_menu',
				'choices' => array(
					'mobile' => esc_html__( 'Mobile only', 'aureon-studio' ),
					'desktop' => esc_html__( 'Desktop only', 'aureon-studio' ),
					'true' => esc_html__( 'On', 'aureon-studio' ),
					'false' => esc_html__( 'Off', 'aureon-studio' ),
				),
				'settings' => 'aureon_menu_plus_settings[sticky_menu]',
				'priority' => 105,
			)
		);

		$wp_customize->add_setting(
			'aureon_menu_plus_settings[sticky_menu_effect]',
			array(
				'default' => $defaults['sticky_menu_effect'],
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_choices',
			)
		);

		$wp_customize->add_control(
			'aureon_menu_plus_settings[sticky_menu_effect]',
			array(
				'type' => 'select',
				'label' => esc_html__( 'Transition', 'aureon-studio' ),
				'section' => 'menu_plus_sticky_menu',
				'choices' => array(
					'fade' => esc_html__( 'Fade', 'aureon-studio' ),
					'slide' => esc_html__( 'Slide', 'aureon-studio' ),
					'none' => esc_html__( 'None', 'aureon-studio' ),
				),
				'settings' => 'aureon_menu_plus_settings[sticky_menu_effect]',
				'active_callback' => 'aureon_sticky_navigation_activated',
				'priority' => 110,
			)
		);

		$wp_customize->add_setting(
			'aureon_menu_plus_settings[auto_hide_sticky]',
			array(
				'default' => $defaults['auto_hide_sticky'],
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_checkbox',
			)
		);

		$wp_customize->add_control(
			'aureon_menu_plus_settings[auto_hide_sticky]',
			array(
				'type' => 'checkbox',
				'label' => esc_html__( 'Hide when scrolling down', 'aureon-studio' ),
				'section' => 'menu_plus_sticky_menu',
				'settings' => 'aureon_menu_plus_settings[auto_hide_sticky]',
				'priority' => 120,
				'active_callback' => 'aureon_sticky_navigation_activated',
			)
		);

		if ( '' == $settings['sticky_menu_logo'] ) { // phpcs:ignore -- Non-script on purpose.
			$wp_customize->add_setting(
				'aureon_menu_plus_settings[sticky_navigation_logo]',
				array(
					'default' => $defaults['sticky_navigation_logo'],
					'type' => 'option',
					'sanitize_callback' => function( $input ) {
						if ( is_numeric( $input ) ) {
							return absint( $input );
						}

						return esc_url_raw( $input );
					},
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Image_Control(
					$wp_customize,
					'aureon_menu_plus_settings[sticky_navigation_logo]',
					array(
						'label' => esc_html__( 'Sticky Navigation Logo', 'aureon-studio' ),
						'section' => 'menu_plus_sticky_menu',
						'settings' => 'aureon_menu_plus_settings[sticky_navigation_logo]',
						'priority' => 125,
						'active_callback' => function() {
							if ( ! function_exists( 'aureon_menu_plus_get_defaults' ) ) {
								return false;
							}

							$settings = wp_parse_args(
								get_option( 'aureon_menu_plus_settings', array() ),
								aureon_menu_plus_get_defaults()
							);

							return (
								'' !== $settings['sticky_navigation_logo'] &&
								! is_numeric( $settings['sticky_navigation_logo'] ) &&
								'false' !== $settings['sticky_menu']
							);
						},
					)
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Media_Control(
					$wp_customize,
					'sticky_navigation_logo',
					array(
						'mime_type' => 'image',
						'label' => esc_html__( 'Sticky Navigation Logo', 'aureon-studio' ),
						'section' => 'menu_plus_sticky_menu',
						'settings' => 'aureon_menu_plus_settings[sticky_navigation_logo]',
						'priority' => 125,
						'active_callback' => function() {
							if ( ! function_exists( 'aureon_menu_plus_get_defaults' ) ) {
								return false;
							}

							$settings = wp_parse_args(
								get_option( 'aureon_menu_plus_settings', array() ),
								aureon_menu_plus_get_defaults()
							);

							return (
								'false' !== $settings['sticky_menu'] &&
								(
									'' === $settings['sticky_navigation_logo'] ||
									is_numeric( $settings['sticky_navigation_logo'] )
								)
							);
						},
					)
				)
			);
		}

		// Deprecated as of 1.8.
		if ( '' !== $settings['sticky_menu_logo'] ) { // phpcs:ignore -- Non-strict on purpose.
			$wp_customize->add_setting(
				'aureon_menu_plus_settings[sticky_menu_logo]',
				array(
					'default' => $defaults['sticky_menu_logo'],
					'type' => 'option',
					'sanitize_callback' => 'esc_url_raw',
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Image_Control(
					$wp_customize,
					'aureon_menu_plus_settings[sticky_menu_logo]',
					array(
						'label' => esc_html__( 'Navigation Logo', 'aureon-studio' ),
						'section' => $sticky_menu_section,
						'settings' => 'aureon_menu_plus_settings[sticky_menu_logo]',
						'priority' => 115,
					)
				)
			);

			$wp_customize->add_setting(
				'aureon_menu_plus_settings[sticky_menu_logo_position]',
				array(
					'default' => $defaults['sticky_menu_logo_position'],
					'type' => 'option',
					'sanitize_callback' => 'aureon_premium_sanitize_choices',
				)
			);

			$wp_customize->add_control(
				'aureon_menu_plus_settings[sticky_menu_logo_position]',
				array(
					'type' => 'select',
					'label' => esc_html__( 'Navigation Logo Placement', 'aureon-studio' ),
					'section' => $sticky_menu_section,
					'choices' => array(
						'sticky-menu' => esc_html__( 'Sticky', 'aureon-studio' ),
						'menu' => esc_html__( 'Sticky + Static', 'aureon-studio' ),
						'regular-menu' => esc_html__( 'Static', 'aureon-studio' ),
					),
					'settings' => 'aureon_menu_plus_settings[sticky_menu_logo_position]',
					'priority' => 120,
					'active_callback' => 'aureon_navigation_logo_activated',
				)
			);
		}

		// Mobile Header section.
		// No longer displays.
		$wp_customize->add_section(
			'menu_plus_mobile_header',
			array(
				'title' => esc_html__( 'Mobile Header', 'aureon-studio' ),
				'capability' => 'edit_theme_options',
				'panel' => $panel,
				'priority' => 11,
			)
		);

		if ( '' == $settings['sticky_menu_logo'] ) { // phpcs:ignore -- Non-strict on purpose.
			$wp_customize->add_setting(
				'aureon_menu_plus_settings[navigation_as_header]',
				array(
					'default' => $defaults['navigation_as_header'],
					'type' => 'option',
					'sanitize_callback' => 'aureon_premium_sanitize_checkbox',
				)
			);

			$wp_customize->add_control(
				'aureon_menu_plus_settings[navigation_as_header]',
				array(
					'type' => 'checkbox',
					'label' => esc_html__( 'Use Navigation as Header', 'aureon-studio' ),
					'section' => $header_section,
					'settings' => 'aureon_menu_plus_settings[navigation_as_header]',
				)
			);
		}

		$wp_customize->add_setting(
			'aureon_menu_plus_settings[mobile_header]',
			array(
				'default' => $defaults['mobile_header'],
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_choices',
			)
		);

		$wp_customize->add_control(
			'aureon_menu_plus_settings[mobile_header]',
			array(
				'type' => 'select',
				'label' => esc_html__( 'Mobile Header', 'aureon-studio' ),
				'section' => $header_section,
				'choices' => array(
					'disable' => esc_html__( 'Off', 'aureon-studio' ),
					'enable' => esc_html__( 'On', 'aureon-studio' ),
				),
				'settings' => 'aureon_menu_plus_settings[mobile_header]',
			)
		);

		if ( defined( 'AUREON_VERSION' ) && version_compare( AUREON_VERSION, '2.3-alpha.1', '<' ) ) {
			$wp_customize->add_control(
				new Aureon_Pro_Range_Slider_Control(
					$wp_customize,
					'aureon_menu_plus_settings[mobile_menu_breakpoint]',
					array(
						'label' => esc_html__( 'Breakpoint', 'aureon-studio' ),
						'section' => $header_section,
						'settings' => array(
							'desktop' => 'aureon_menu_plus_settings[mobile_menu_breakpoint]',
						),
						'choices' => array(
							'desktop' => array(
								'min' => 768,
								'max' => 2000,
								'step' => 5,
								'edit' => true,
								'unit' => 'px',
							),
						),
						'active_callback' => 'aureon_mobile_header_activated',
					)
				)
			);
		}

		$wp_customize->add_setting(
			'aureon_menu_plus_settings[mobile_header_branding]',
			array(
				'default' => $defaults['mobile_header_branding'],
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_choices',
			)
		);

		$wp_customize->add_control(
			'aureon_menu_plus_settings[mobile_header_branding]',
			array(
				'type' => 'select',
				'label' => esc_html__( 'Branding Type', 'aureon-studio' ),
				'section' => $header_section,
				'choices' => array(
					'logo' => esc_html__( 'Logo', 'aureon-studio' ),
					'title' => esc_html__( 'Site Title', 'aureon-studio' ),
				),
				'settings' => 'aureon_menu_plus_settings[mobile_header_branding]',
				'active_callback' => 'aureon_mobile_header_activated',
			)
		);

		$wp_customize->add_setting(
			'aureon_menu_plus_settings[mobile_header_logo]',
			array(
				'default' => $defaults['mobile_header_logo'],
				'type' => 'option',
				'sanitize_callback' => function( $input ) {
					if ( is_numeric( $input ) ) {
						return absint( $input );
					}

					return esc_url_raw( $input );
				},
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'aureon_menu_plus_settings[mobile_header_logo]',
				array(
					'label' => esc_html__( 'Logo', 'aureon-studio' ),
					'section' => $header_section,
					'settings' => 'aureon_menu_plus_settings[mobile_header_logo]',
					'active_callback' => function() {
						if ( ! function_exists( 'aureon_menu_plus_get_defaults' ) ) {
							return false;
						}

						$settings = wp_parse_args(
							get_option( 'aureon_menu_plus_settings', array() ),
							aureon_menu_plus_get_defaults()
						);

						return (
							'' !== $settings['mobile_header_logo'] &&
							! is_numeric( $settings['mobile_header_logo'] ) &&
							'enable' === $settings['mobile_header'] &&
							'logo' === $settings['mobile_header_branding']
						);
					},
				)
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Media_Control(
				$wp_customize,
				'mobile_header_logo',
				array(
					'mime_type' => 'image',
					'label' => esc_html__( 'Logo', 'aureon-studio' ),
					'section' => $header_section,
					'settings' => 'aureon_menu_plus_settings[mobile_header_logo]',
					'active_callback' => function() {
						if ( ! function_exists( 'aureon_menu_plus_get_defaults' ) ) {
							return false;
						}

						$settings = wp_parse_args(
							get_option( 'aureon_menu_plus_settings', array() ),
							aureon_menu_plus_get_defaults()
						);

						return (
							'enable' === $settings['mobile_header'] &&
							'logo' === $settings['mobile_header_branding'] &&
							(
								'' === $settings['mobile_header_logo'] ||
								is_numeric( $settings['mobile_header_logo'] )
							)
						);
					},
				)
			)
		);

		$wp_customize->add_setting(
			'aureon_menu_plus_settings[mobile_header_sticky]',
			array(
				'default' => $defaults['mobile_header_sticky'],
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_choices',
			)
		);

		$wp_customize->add_control(
			'aureon_menu_plus_settings[mobile_header_sticky]',
			array(
				'type' => 'select',
				'label' => esc_html__( 'Sticky', 'aureon-studio' ),
				'section' => $header_section,
				'choices' => array(
					'enable' => esc_html__( 'On', 'aureon-studio' ),
					'disable' => esc_html__( 'Off', 'aureon-studio' ),
				),
				'settings' => 'aureon_menu_plus_settings[mobile_header_sticky]',
				'active_callback' => 'aureon_mobile_header_activated',
			)
		);

		$wp_customize->add_setting(
			'aureon_menu_plus_settings[mobile_header_auto_hide_sticky]',
			array(
				'default' => $defaults['mobile_header_auto_hide_sticky'],
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_checkbox',
			)
		);

		$wp_customize->add_control(
			'aureon_menu_plus_settings[mobile_header_auto_hide_sticky]',
			array(
				'type' => 'checkbox',
				'label' => esc_html__( 'Hide when scrolling down', 'aureon-studio' ),
				'section' => $header_section,
				'settings' => 'aureon_menu_plus_settings[mobile_header_auto_hide_sticky]',
				'active_callback' => 'aureon_mobile_header_sticky_activated',
			)
		);

		$wp_customize->add_section(
			'menu_plus_slideout_menu',
			array(
				'title' => esc_html__( 'Off Canvas Panel', 'aureon-studio' ),
				'capability' => 'edit_theme_options',
				'panel' => $panel,
				'priority' => 34,
			)
		);

		$wp_customize->add_control(
			new Aureon_Section_Shortcut_Control(
				$wp_customize,
				'aureon_off_canvas_layout_shortcuts',
				array(
					'section' => 'menu_plus_slideout_menu',
					'element' => __( 'Off Canvas Panel', 'aureon-studio' ),
					'shortcuts' => array(
						'colors' => 'slideout_color_section',
						'typography' => 'aureon_slideout_typography',
					),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
					'priority' => 1,
				)
			)
		);

		$wp_customize->add_setting(
			'aureon_menu_plus_settings[slideout_menu]',
			array(
				'default' => $defaults['slideout_menu'],
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_choices',
			)
		);

		$wp_customize->add_control(
			'aureon_menu_plus_settings[slideout_menu]',
			array(
				'type' => 'select',
				'label' => esc_html__( 'Off Canvas Panel', 'aureon-studio' ),
				'section' => 'menu_plus_slideout_menu',
				'choices' => array(
					'mobile' => esc_html__( 'Mobile only', 'aureon-studio' ),
					'desktop' => esc_html__( 'Desktop only', 'aureon-studio' ),
					'both' => esc_html__( 'On', 'aureon-studio' ),
					'false' => esc_html__( 'Off', 'aureon-studio' ),
				),
				'settings' => 'aureon_menu_plus_settings[slideout_menu]',
			)
		);

		$wp_customize->add_setting(
			'aureon_menu_plus_settings[off_canvas_desktop_toggle_label]',
			array(
				'default' => $defaults['off_canvas_desktop_toggle_label'],
				'type' => 'option',
				'sanitize_callback' => 'wp_kses_post',
			)
		);

		$wp_customize->add_control(
			'aureon_menu_plus_settings[off_canvas_desktop_toggle_label]',
			array(
				'label' => esc_html__( 'Desktop Toggle Label', 'aureon-studio' ),
				'section' => 'menu_plus_slideout_menu',
				'settings' => 'aureon_menu_plus_settings[off_canvas_desktop_toggle_label]',
				'active_callback' => 'aureon_slideout_navigation_activated',
			)
		);

		$wp_customize->add_setting(
			'aureon_menu_plus_settings[slideout_menu_style]',
			array(
				'default' => $defaults['slideout_menu_style'],
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_choices',
			)
		);

		$wp_customize->add_control(
			'aureon_menu_plus_settings[slideout_menu_style]',
			array(
				'type' => 'select',
				'label' => esc_html__( 'Style', 'aureon-studio' ),
				'section' => 'menu_plus_slideout_menu',
				'choices' => array(
					'slide' => esc_html__( 'Slide', 'aureon-studio' ),
					'overlay' => esc_html__( 'Overlay', 'aureon-studio' ),
				),
				'settings' => 'aureon_menu_plus_settings[slideout_menu_style]',
				'active_callback' => 'aureon_slideout_navigation_activated',
			)
		);

		$wp_customize->add_setting(
			'aureon_menu_plus_settings[slideout_menu_side]',
			array(
				'default' => $defaults['slideout_menu_side'],
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_choices',
			)
		);

		$wp_customize->add_control(
			'aureon_menu_plus_settings[slideout_menu_side]',
			array(
				'type' => 'select',
				'label' => esc_html__( 'Side', 'aureon-studio' ),
				'section' => 'menu_plus_slideout_menu',
				'choices' => array(
					'left' => esc_html__( 'Left', 'aureon-studio' ),
					'right' => esc_html__( 'Right', 'aureon-studio' ),
				),
				'settings' => 'aureon_menu_plus_settings[slideout_menu_side]',
				'active_callback' => 'aureon_is_slideout_navigation_active_callback',
			)
		);

		$wp_customize->add_control(
			new Aureon_Action_Button_Control(
				$wp_customize,
				'aureon_set_slideout_overlay_option',
				array(
					'section' => 'menu_plus_slideout_menu',
					'data_type' => 'overlay_design',
					'label' => __( 'Set Overlay Defaults', 'aureon-studio' ),
					'description' => esc_html__( 'Clicking the above button will design your overlay by changing some of your off canvas color and typography options.', 'aureon-studio' ),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
					'active_callback' => 'aureon_is_overlay_navigation_active_callback',
				)
			)
		);

		$wp_customize->add_setting(
			'aureon_menu_plus_settings[slideout_close_button]',
			array(
				'default' => $defaults['slideout_close_button'],
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_choices',
			)
		);

		$wp_customize->add_control(
			'aureon_menu_plus_settings[slideout_close_button]',
			array(
				'type' => 'select',
				'label' => esc_html__( 'Close Button', 'aureon-studio' ),
				'section' => 'menu_plus_slideout_menu',
				'choices' => array(
					'outside' => esc_html__( 'Outside', 'aureon-studio' ),
					'inside' => esc_html__( 'Inside', 'aureon-studio' ),
				),
				'settings' => 'aureon_menu_plus_settings[slideout_close_button]',
				'active_callback' => 'aureon_is_slideout_navigation_active_callback',
			)
		);
	}
}

if ( ! function_exists( 'aureon_menu_plus_enqueue_css' ) ) {
	add_action( 'wp_enqueue_scripts', 'aureon_menu_plus_enqueue_css', 100 );
	/**
	 * Enqueue scripts
	 */
	function aureon_menu_plus_enqueue_css() {
		$settings = wp_parse_args(
			get_option( 'aureon_menu_plus_settings', array() ),
			aureon_menu_plus_get_defaults()
		);

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		$using_flex = false;

		if ( function_exists( 'aureon_is_using_flexbox' ) && aureon_is_using_flexbox() ) {
			$using_flex = true;
		}

		if ( 'false' !== $settings['sticky_menu'] && ! $using_flex ) {
			wp_enqueue_style( 'aureon-sticky', plugin_dir_url( __FILE__ ) . "css/sticky{$suffix}.css", array(), AUREON_MENU_PLUS_VERSION );
		}

		if ( 'false' !== $settings['slideout_menu'] ) {
			wp_enqueue_style( 'aureon-offside', plugin_dir_url( __FILE__ ) . "css/offside{$suffix}.css", array(), AUREON_MENU_PLUS_VERSION );
			wp_add_inline_style( 'aureon-offside', aureon_do_off_canvas_css() );

			if ( class_exists( 'Aureon_Typography' ) ) {
				wp_add_inline_style( 'aureon-offside', Aureon_Typography::get_css( 'off-canvas-panel' ) );
			}

			$font_icons = true;

			if ( function_exists( 'aureon_get_option' ) ) {
				if ( 'font' !== aureon_get_option( 'icons' ) ) {
					$font_icons = false;
				}
			}

			if ( $font_icons ) {
				wp_enqueue_style( 'aureon-studio-icons' );
			}
		}

		if ( '' !== $settings['sticky_menu_logo'] ) {
			wp_enqueue_style( 'aureon-menu-logo', plugin_dir_url( __FILE__ ) . "css/menu-logo{$suffix}.css", array(), AUREON_MENU_PLUS_VERSION );
			wp_add_inline_style( 'aureon-menu-logo', aureon_do_mobile_navigation_logo_css() );
		}

		if ( $settings['navigation_as_header'] || $settings['sticky_navigation_logo'] || 'enable' === $settings['mobile_header'] ) {
			if ( function_exists( 'aureon_is_using_flexbox' ) && aureon_is_using_flexbox() ) {
				wp_enqueue_style( 'aureon-navigation-branding', plugin_dir_url( __FILE__ ) . "css/navigation-branding-flex{$suffix}.css", array(), AUREON_MENU_PLUS_VERSION );
			} else {
				wp_enqueue_style( 'aureon-navigation-branding', plugin_dir_url( __FILE__ ) . "css/navigation-branding{$suffix}.css", array(), AUREON_MENU_PLUS_VERSION );
			}

			wp_add_inline_style( 'aureon-navigation-branding', aureon_do_nav_branding_css() );
		}

		if ( 'inline' === aureon_get_css_print_method() ) {
			wp_add_inline_style( 'aureon-style', aureon_menu_plus_inline_css() );
		}
	}
}

add_filter( 'aureon_external_dynamic_css_output', 'aureon_menu_plus_add_to_external_stylesheet' );
/**
 * Add CSS to the external stylesheet.
 *
 * @since 1.11.0
 * @param string $css Existing CSS.
 */
function aureon_menu_plus_add_to_external_stylesheet( $css ) {
	if ( 'inline' === aureon_get_css_print_method() ) {
		return $css;
	}

	$css .= aureon_menu_plus_inline_css();

	return $css;
}

if ( ! function_exists( 'aureon_menu_plus_enqueue_js' ) ) {
	add_action( 'wp_enqueue_scripts', 'aureon_menu_plus_enqueue_js', 0 );
	/**
	 * Enqueue scripts
	 */
	function aureon_menu_plus_enqueue_js() {
		$settings = wp_parse_args(
			get_option( 'aureon_menu_plus_settings', array() ),
			aureon_menu_plus_get_defaults()
		);

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		if ( ( 'false' !== $settings['sticky_menu'] ) || ( 'enable' === $settings['mobile_header'] && 'enable' === $settings['mobile_header_sticky'] ) ) {
			wp_enqueue_script( 'aureon-sticky', plugin_dir_url( __FILE__ ) . "js/sticky{$suffix}.js", array( 'jquery-core' ), AUREON_MENU_PLUS_VERSION, true );
		}

		if ( 'false' !== $settings['slideout_menu'] ) {
			wp_enqueue_script( 'aureon-offside', plugin_dir_url( __FILE__ ) . "js/offside{$suffix}.js", array(), AUREON_MENU_PLUS_VERSION, true );

			wp_localize_script(
				'aureon-offside',
				'offSide',
				array(
					'side' => $settings['slideout_menu_side'],
				)
			);
		}
	}
}

if ( ! function_exists( 'aureon_menu_plus_mobile_header_js' ) ) {
	add_action( 'wp_enqueue_scripts', 'aureon_menu_plus_mobile_header_js', 15 );
	/**
	 * Enqueue scripts
	 */
	function aureon_menu_plus_mobile_header_js() {
		if ( function_exists( 'wp_add_inline_script' ) ) {
			$settings = wp_parse_args(
				get_option( 'aureon_menu_plus_settings', array() ),
				aureon_menu_plus_get_defaults()
			);

			if ( 'enable' === $settings['mobile_header'] && ( 'desktop' === $settings['slideout_menu'] || 'false' === $settings['slideout_menu'] ) ) {
				wp_add_inline_script(
					'aureon-navigation',
					"jQuery( document ).ready( function($) {
						$( '#mobile-header .menu-toggle' ).on( 'click', function( e ) {
							e.preventDefault();
							$( this ).closest( '#mobile-header' ).toggleClass( 'toggled' );
							$( this ).closest( '#mobile-header' ).attr( 'aria-expanded', $( this ).closest( '#mobile-header' ).attr( 'aria-expanded' ) === 'true' ? 'false' : 'true' );
							$( this ).toggleClass( 'toggled' );
							$( this ).children( 'i' ).toggleClass( 'fa-bars' ).toggleClass( 'fa-close' );
							$( this ).attr( 'aria-expanded', $( this ).attr( 'aria-expanded' ) === 'false' ? 'true' : 'false' );
						});
					});"
				);
			}
		}
	}
}

if ( ! function_exists( 'aureon_menu_plus_inline_css' ) ) {
	/**
	 * Enqueue inline CSS
	 */
	function aureon_menu_plus_inline_css() {
		if ( ! function_exists( 'aureon_get_defaults' ) ) {
			return;
		}

		$aureon_settings = wp_parse_args(
			get_option( 'aureon_settings', array() ),
			aureon_get_defaults()
		);

		$aureon_menu_plus_settings = wp_parse_args(
			get_option( 'aureon_menu_plus_settings', array() ),
			aureon_menu_plus_get_defaults()
		);

		if ( function_exists( 'aureon_spacing_get_defaults' ) ) {
			$spacing_settings = wp_parse_args(
				get_option( 'aureon_spacing_settings', array() ),
				aureon_spacing_get_defaults()
			);
			$menu_height = $spacing_settings['menu_item_height'];
		} else {
			$menu_height = 60;
		}

		$return = '';

		if ( '' !== $aureon_menu_plus_settings['sticky_menu_logo'] ) {
			$return .= '.main-navigation .navigation-logo img {height:' . absint( $menu_height ) . 'px;}';
			$return .= '@media (max-width: ' . ( absint( $aureon_settings['container_width'] + 10 ) ) . 'px) {.main-navigation .navigation-logo.site-logo {margin-left:0;}body.sticky-menu-logo.nav-float-left .main-navigation .site-logo.navigation-logo {margin-right:0;}}';
		}

		if ( 'false' !== $aureon_menu_plus_settings['sticky_menu'] ) {
			if ( function_exists( 'aureon_is_using_flexbox' ) && aureon_is_using_flexbox() ) {
				$return .= '.main-navigation .main-nav ul li a,.menu-toggle,.main-navigation .menu-bar-item > a{transition: line-height 300ms ease}';
			} else {
				$return .= '.main-navigation .main-nav ul li a,.menu-toggle,.main-navigation .mobile-bar-items a{transition: line-height 300ms ease}';
			}

			if ( class_exists( 'FLBuilderModel' ) ) {
				$return .= '.fl-builder-edit .navigation-stick {z-index: 10 !important;}';
			}
		}

		if ( function_exists( 'aureon_get_color_defaults' ) ) {
			$color_defaults = wp_parse_args(
				get_option( 'aureon_settings', array() ),
				aureon_get_color_defaults()
			);

			if ( 'true' === $aureon_menu_plus_settings['sticky_menu'] || 'mobile' === $aureon_menu_plus_settings['sticky_menu'] || 'enable' === $aureon_menu_plus_settings['mobile_header_sticky'] ) {
				$return .= '.main-navigation.toggled .main-nav > ul{background-color: ' . $color_defaults['navigation_background_color'] . '}';
			}
		}

		if ( function_exists( 'aureon_is_using_flexbox' ) && aureon_is_using_flexbox() ) {
			if ( 'true' === $aureon_menu_plus_settings['sticky_menu'] || 'desktop' === $aureon_menu_plus_settings['sticky_menu'] || 'mobile' === $aureon_menu_plus_settings['sticky_menu'] || 'enable' === $aureon_menu_plus_settings['mobile_header_sticky'] ) {
				$return .= '.sticky-enabled .gen-sidebar-nav.is_stuck .main-navigation {margin-bottom: 0px;}';
				$return .= '.sticky-enabled .gen-sidebar-nav.is_stuck {z-index: 500;}';
				$return .= '.sticky-enabled .main-navigation.is_stuck {box-shadow: 0 2px 2px -2px rgba(0, 0, 0, .2);}';
				$return .= '.navigation-stick:not(.gen-sidebar-nav) {left: 0;right: 0;width: 100% !important;}';

				if ( function_exists( 'aureon_get_option' ) && aureon_get_option( 'smooth_scroll' ) ) {
					$return .= '.both-sticky-menu .main-navigation:not(#mobile-header).toggled .main-nav > ul,.mobile-sticky-menu .main-navigation:not(#mobile-header).toggled .main-nav > ul,.mobile-header-sticky #mobile-header.toggled .main-nav > ul {position: absolute;left: 0;right: 0;z-index: 999;}';
				}

				if ( function_exists( 'aureon_has_inline_mobile_toggle' ) && aureon_has_inline_mobile_toggle() && 'enable' !== $aureon_menu_plus_settings['mobile_header'] ) {
					$return .= '@media ' . aureon_premium_get_media_query( 'mobile-menu' ) . '{#sticky-placeholder{height:0;overflow:hidden;}.has-inline-mobile-toggle #site-navigation.toggled{margin-top:0;}.has-inline-mobile-menu #site-navigation.toggled .main-nav > ul{top:1.5em;}}';
				}

				$return .= '.nav-float-right .navigation-stick {width: 100% !important;left: 0;}';
				$return .= '.nav-float-right .navigation-stick .navigation-branding {margin-right: auto;}';
				$return .= '.main-navigation.has-sticky-branding:not(.grid-container) .inside-navigation:not(.grid-container) .navigation-branding{margin-left: 10px;}';

				if ( ! $aureon_menu_plus_settings['navigation_as_header'] ) {
					$header_left = 40;
					$header_right = 40;

					if ( function_exists( 'aureon_spacing_get_defaults' ) ) {
						$spacing_settings = wp_parse_args(
							get_option( 'aureon_spacing_settings', array() ),
							aureon_spacing_get_defaults()
						);

						$header_left = $spacing_settings['header_left'];
						$header_right = $spacing_settings['header_right'];
					}

					if ( function_exists( 'aureon_is_using_flexbox' ) && aureon_is_using_flexbox() ) {
						if ( function_exists( 'aureon_get_option' ) && 'text' === aureon_get_option( 'container_alignment' ) ) {
							$return .= '.main-navigation.navigation-stick.has-sticky-branding .inside-navigation.grid-container{padding-left:' . $header_left . 'px;padding-right:' . $header_right . 'px;}';

							$return .= '@media ' . aureon_premium_get_media_query( 'mobile' ) . '{.main-navigation.navigation-stick.has-sticky-branding .inside-navigation.grid-container{padding-left:0;padding-right:0;}}';
						}
					}
				}
			}
		}

		return $return;
	}
}

if ( ! function_exists( 'aureon_menu_plus_mobile_header' ) ) {
	add_action( 'aureon_after_header', 'aureon_menu_plus_mobile_header', 5 );
	add_action( 'aureon_inside_mobile_header', 'aureon_navigation_search', 1 );
	add_action( 'aureon_inside_mobile_header', 'aureon_mobile_menu_search_icon' );
	/**
	 * Build the mobile header.
	 */
	function aureon_menu_plus_mobile_header() {
		$settings = wp_parse_args(
			get_option( 'aureon_menu_plus_settings', array() ),
			aureon_menu_plus_get_defaults()
		);

		if ( 'disable' === $settings['mobile_header'] ) {
			return;
		}

		$attributes = array(
			'id' => 'mobile-header',
		);

		if ( 'false' !== $settings['mobile_header_auto_hide_sticky'] && $settings['mobile_header_auto_hide_sticky'] ) {
			$hide_sticky = ' data-auto-hide-sticky="true"';
			$attributes['data-auto-hide-sticky'] = true;
		} else {
			$hide_sticky = '';
		}

		$microdata = 'itemtype="https://schema.org/SiteNavigationElement" itemscope';

		if ( function_exists( 'aureon_get_microdata' ) ) {
			$microdata = aureon_get_microdata( 'navigation' );
		}

		if ( function_exists( 'aureon_get_schema_type' ) ) {
			if ( 'microdata' === aureon_get_schema_type() ) {
				$attributes['itemtype'] = 'https://schema.org/SiteNavigationElement';
				$attributes['itemscope'] = true;
			}
		}

		$classes = array(
			'main-navigation',
			'mobile-header-navigation',
		);

		if ( ( 'logo' === $settings['mobile_header_branding'] && '' !== $settings['mobile_header_logo'] ) || 'title' === $settings['mobile_header_branding'] ) {
			$classes[] = 'has-branding';
		}

		if ( 'enable' === $settings['mobile_header_sticky'] ) {
			if ( ( 'logo' === $settings['mobile_header_branding'] && '' !== $settings['mobile_header_logo'] ) || 'title' === $settings['mobile_header_branding'] ) {
				$classes[] = 'has-sticky-branding';
			}
		}

		if ( function_exists( 'aureon_wc_get_setting' ) && aureon_wc_get_setting( 'cart_menu_item' ) ) {
			$classes[] = 'wc-menu-cart-activated';
		}

		if ( function_exists( 'aureon_is_using_flexbox' ) && aureon_is_using_flexbox() ) {
			if ( function_exists( 'aureon_has_menu_bar_items' ) && aureon_has_menu_bar_items() ) {
				$classes[] = 'has-menu-bar-items';
			}
		}

		$classes = implode( ' ', $classes );
		$attributes['class'] = $classes;

		$mobile_header_attributes = sprintf(
			'id="mobile-header"%1$s class="%2$s" %3$s"',
			$hide_sticky,
			esc_attr( $classes ),
			$microdata
		);

		if ( function_exists( 'aureon_get_attr' ) ) {
			$mobile_header_attributes = aureon_get_attr(
				'mobile-header',
				$attributes
			);
		}
		?>
		<nav <?php echo $mobile_header_attributes; // phpcs:ignore -- No escaping needed. ?>>
			<div class="inside-navigation grid-container grid-parent">
				<?php
				do_action( 'aureon_inside_mobile_header' );

				$disable_mobile_header_menu = apply_filters( 'aureon_disable_mobile_header_menu', false );

				if ( ! $disable_mobile_header_menu ) :
					if ( function_exists( 'aureon_get_attr' ) ) {
						$menu_toggle_attributes = aureon_get_attr(
							'mobile-header-menu-toggle',
							array(
								'class' => 'menu-toggle',
								'aria-controls' => 'mobile-menu',
								'aria-expanded' => 'false',
							)
						);
					} else {
						$menu_toggle_attributes = 'class="menu-toggle" aria-controls="mobile-menu" aria-expanded="false"';
					}
					?>
					<button <?php echo $menu_toggle_attributes; // phpcs:ignore -- No escaping needed. ?>>
						<?php
						do_action( 'aureon_inside_mobile_header_menu' );

						if ( function_exists( 'aureon_do_svg_icon' ) ) {
							aureon_do_svg_icon( 'menu-bars', true );
						}

						$mobile_menu_label = apply_filters( 'aureon_mobile_menu_label', __( 'Menu', 'aureon-studio' ) );

						if ( $mobile_menu_label ) {
							printf(
								'<span class="mobile-menu">%s</span>',
								$mobile_menu_label // phpcs:ignore -- HTML allowed.
							);
						} else {
							printf(
								'<span class="screen-reader-text">%s</span>',
								__( 'Menu', 'aureon-studio' )
							);
						}
						?>
					</button>
					<?php
					/**
					 * aureon_after_mobile_header_menu_button hook
					 *
					 * @since 1.11.0
					 */
					do_action( 'aureon_after_mobile_header_menu_button' );
				endif;

				wp_nav_menu(
					array(
						'theme_location' => apply_filters( 'aureon_mobile_header_theme_location', 'primary' ),
						'container' => 'div',
						'container_class' => 'main-nav',
						'container_id' => 'mobile-menu',
						'menu_class' => '',
						'fallback_cb' => 'aureon_menu_fallback',
						'items_wrap' => '<ul id="%1$s" class="%2$s ' . join( ' ', aureon_get_menu_class() ) . '">%3$s</ul>',
					)
				);

				/**
				 * aureon_after_primary_menu hook.
				 *
				 * @since 1.11.0
				 */
				do_action( 'aureon_after_primary_menu' );
		?>
			</div><!-- .inside-navigation -->
		</nav><!-- #site-navigation -->
		<?php
	}
}

if ( ! function_exists( 'aureon_slideout_navigation' ) ) {
	add_action( 'wp_footer', 'aureon_slideout_navigation', 0 );
	/**
	 * Build the navigation.
	 *
	 * @since 0.1
	 */
	function aureon_slideout_navigation() {
		$settings = wp_parse_args(
			get_option( 'aureon_menu_plus_settings', array() ),
			aureon_menu_plus_get_defaults()
		);

		if ( 'false' === $settings['slideout_menu'] ) {
			return;
		}

		$microdata = 'itemtype="https://schema.org/SiteNavigationElement" itemscope';

		if ( function_exists( 'aureon_get_microdata' ) ) {
			$microdata = aureon_get_microdata( 'navigation' );
		}

		$overlay = '';
		if ( 'overlay' === $settings['slideout_menu_style'] ) {
			$overlay = ' do-overlay';
		}

		?>
		<nav id="aureon-slideout-menu" class="main-navigation slideout-navigation<?php echo esc_attr( $overlay ); ?>" <?php echo $microdata; // phpcs:ignore -- No escaping needed. ?>>
			<div class="inside-navigation grid-container grid-parent">
				<?php
				do_action( 'aureon_inside_slideout_navigation' );

				wp_nav_menu(
					array(
						'theme_location' => 'slideout',
						'container' => 'div',
						'container_class' => 'main-nav',
						'menu_class' => '',
						'fallback_cb' => false,
						'items_wrap' => '<ul id="%1$s" class="%2$s slideout-menu">%3$s</ul>',
					)
				);

				do_action( 'aureon_after_slideout_navigation' );
				?>
			</div><!-- .inside-navigation -->
		</nav><!-- #site-navigation -->

		<?php
		if ( 'slide' === $settings['slideout_menu_style'] ) :
			$svg_icon = '';

			if ( function_exists( 'aureon_get_svg_icon' ) ) {
				$svg_icon = aureon_get_svg_icon( 'pro-close' );
			}
			?>
			<div class="slideout-overlay">
				<?php if ( 'outside' === $settings['slideout_close_button'] && 'slide' === $settings['slideout_menu_style'] ) : ?>
					<button class="slideout-exit <?php echo $svg_icon ? 'has-svg-icon' : ''; ?>">
						<?php echo $svg_icon; // phpcs:ignore -- Escaping not needed. ?>
						<span class="screen-reader-text"><?php esc_attr_e( 'Close', 'aureon-studio' ); ?></span>
					</button>
				<?php endif; ?>
			</div>
			<?php
		endif;
	}
}

add_action( 'aureon_after_slideout_navigation', 'aureon_slideout_menu_widget' );
/**
 * Add Off-Canvas panel widget to the panel.
 */
function aureon_slideout_menu_widget() {
	if ( is_active_sidebar( 'slide-out-widget' ) ) {
		dynamic_sidebar( 'slide-out-widget' );
	}
}

add_action( 'widgets_init', 'aureon_slideout_navigation_widget', 99 );
/**
 * Register widgetized area and update sidebar with default widgets
 */
function aureon_slideout_navigation_widget() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Off Canvas Panel', 'aureon-studio' ),
			'id'            => 'slide-out-widget',
			'before_widget' => '<aside id="%1$s" class="slideout-widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => apply_filters( 'aureon_start_widget_title', '<h2 class="widget-title">' ),
			'after_title'   => apply_filters( 'aureon_end_widget_title', '</h2>' ),
		)
	);
}

if ( ! function_exists( 'aureon_slideout_body_classes' ) ) {
	add_filter( 'body_class', 'aureon_slideout_body_classes' );
	/**
	 * Adds custom classes to body
	 *
	 * @since 0.1
	 * @param array $classes Existing classes.
	 */
	function aureon_slideout_body_classes( $classes ) {
		$settings = wp_parse_args(
			get_option( 'aureon_menu_plus_settings', array() ),
			aureon_menu_plus_get_defaults()
		);

		if ( 'false' !== $settings['slideout_menu'] ) {
			$classes[] = 'slideout-enabled';
		}

		if ( 'mobile' === $settings['slideout_menu'] ) {
			$classes[] = 'slideout-mobile';
		}

		if ( 'desktop' === $settings['slideout_menu'] ) {
			$classes[] = 'slideout-desktop';
		}

		if ( 'both' === $settings['slideout_menu'] ) {
			$classes[] = 'slideout-both';
		}

		if ( 'slide' === $settings['sticky_menu_effect'] ) {
			$classes[] = 'sticky-menu-slide';
		}

		if ( 'fade' === $settings['sticky_menu_effect'] ) {
			$classes[] = 'sticky-menu-fade';
		}

		if ( 'none' === $settings['sticky_menu_effect'] ) {
			$classes[] = 'sticky-menu-no-transition';
		}

		if ( 'false' !== $settings['sticky_menu'] ) {
			$classes[] = 'sticky-enabled';
		}

		if ( '' !== $settings['sticky_menu_logo'] ) {

			if ( 'sticky-menu' === $settings['sticky_menu_logo_position'] ) {
				$classes[] = 'sticky-menu-logo';
			} elseif ( 'menu' === $settings['sticky_menu_logo_position'] ) {
				$classes[] = 'menu-logo';
			} elseif ( 'regular-menu' === $settings['sticky_menu_logo_position'] ) {
				$classes[] = 'regular-menu-logo';
			}

			$classes[] = 'menu-logo-enabled';

		}

		if ( 'mobile' === $settings['sticky_menu'] ) {
			$classes[] = 'mobile-sticky-menu';
		}

		if ( 'desktop' === $settings['sticky_menu'] ) {
			$classes[] = 'desktop-sticky-menu';
		}

		if ( 'true' === $settings['sticky_menu'] ) {
			$classes[] = 'both-sticky-menu';
		}

		if ( 'enable' === $settings['mobile_header'] ) {
			$classes[] = 'mobile-header';
		}

		if ( '' !== $settings['mobile_header_logo'] && 'enable' === $settings['mobile_header'] ) {
			$classes[] = 'mobile-header-logo';
		}

		if ( 'enable' === $settings['mobile_header_sticky'] && 'enable' === $settings['mobile_header'] ) {
			$classes[] = 'mobile-header-sticky';
		}

		return $classes;

	}
}

if ( ! function_exists( 'aureon_menu_plus_slidebar_icon' ) ) {
	add_filter( 'wp_nav_menu_items', 'aureon_menu_plus_slidebar_icon', 10, 2 );
	/**
	 * Add slidebar icon to primary menu if set
	 *
	 * @since 0.1
	 * @param string $nav Existing menu items.
	 * @param object $args Nav args.
	 */
	function aureon_menu_plus_slidebar_icon( $nav, $args ) {
		$settings = wp_parse_args(
			get_option( 'aureon_menu_plus_settings', array() ),
			aureon_menu_plus_get_defaults()
		);

		if ( function_exists( 'aureon_is_using_flexbox' ) && aureon_is_using_flexbox() ) {
			return $nav;
		}

		// If the search icon isn't enabled, return the regular nav.
		if ( 'desktop' !== $settings['slideout_menu'] && 'both' !== $settings['slideout_menu'] ) {
			return $nav;
		}

		// If our primary menu is set, add the search icon.
		if ( 'primary' === $args->theme_location ) {
			$svg_icon = '';

			if ( function_exists( 'aureon_get_svg_icon' ) ) {
				$svg_icon = aureon_get_svg_icon( 'pro-menu-bars' );
			}

			$icon = apply_filters(
				'aureon_off_canvas_toggle_output',
				sprintf(
					'<li class="slideout-toggle menu-item-align-right %2$s"><a href="#" role="button">%1$s%3$s</a></li>',
					$svg_icon,
					$svg_icon ? 'has-svg-icon' : '',
					'' !== $settings['off_canvas_desktop_toggle_label'] ? '<span class="off-canvas-toggle-label">' . wp_kses_post( $settings['off_canvas_desktop_toggle_label'] ) . '</span>' : ''
				)
			);

			return $nav . $icon;
		}

		return $nav;
	}
}

add_action( 'wp', 'aureon_add_menu_plus_menu_bar_items' );
/**
 * Conditionally add our menu bar items.
 *
 * @since 1.11.0
 */
function aureon_add_menu_plus_menu_bar_items() {
	if ( function_exists( 'aureon_is_using_flexbox' ) && aureon_is_using_flexbox() ) {
		$settings = wp_parse_args(
			get_option( 'aureon_menu_plus_settings', array() ),
			aureon_menu_plus_get_defaults()
		);

		if ( 'desktop' !== $settings['slideout_menu'] && 'both' !== $settings['slideout_menu'] ) {
			return;
		}

		add_action( 'aureon_menu_bar_items', 'aureon_do_off_canvas_toggle_button', 15 );
	}
}

/**
 * Add the off-canvas toggle button to the menu.
 *
 * @since 1.11.0
 */
function aureon_do_off_canvas_toggle_button() {
	if ( function_exists( 'aureon_is_using_flexbox' ) && aureon_is_using_flexbox() ) {
		$settings = wp_parse_args(
			get_option( 'aureon_menu_plus_settings', array() ),
			aureon_menu_plus_get_defaults()
		);

		if ( 'desktop' !== $settings['slideout_menu'] && 'both' !== $settings['slideout_menu'] ) {
			return;
		}

		$svg_icon = '';

		if ( function_exists( 'aureon_get_svg_icon' ) ) {
			$svg_icon = aureon_get_svg_icon( 'pro-menu-bars' );
		}

		$icon = apply_filters(
			'aureon_off_canvas_toggle_output',
			sprintf(
				'<span class="menu-bar-item slideout-toggle hide-on-mobile %2$s"><a href="#" role="button"%4$s>%1$s%3$s</a></span>',
				$svg_icon,
				$svg_icon ? 'has-svg-icon' : '',
				'' !== $settings['off_canvas_desktop_toggle_label'] ? '<span class="off-canvas-toggle-label">' . wp_kses_post( $settings['off_canvas_desktop_toggle_label'] ) . '</span>' : '',
				'' === $settings['off_canvas_desktop_toggle_label']
					? sprintf(
						' aria-label="%s"',
						apply_filters( 'aureon_off_canvas_button_aria_label', __( 'Open Off-Canvas Panel', 'aureon-studio' ) )
					) : ''
			)
		);

		echo $icon; // phpcs:ignore -- No escaping needed.
	}
}

if ( ! function_exists( 'aureon_sticky_navigation_classes' ) ) {
	add_filter( 'aureon_navigation_class', 'aureon_sticky_navigation_classes' );
	/**
	 * Adds custom classes to the navigation.
	 *
	 * @since 0.1
	 * @param array $classes Existing classes.
	 */
	function aureon_sticky_navigation_classes( $classes ) {

		$settings = wp_parse_args(
			get_option( 'aureon_menu_plus_settings', array() ),
			aureon_menu_plus_get_defaults()
		);

		if ( 'false' !== $settings['sticky_menu'] && $settings['auto_hide_sticky'] ) {
			$classes[] = 'auto-hide-sticky';
		}

		if ( function_exists( 'aureon_get_option' ) ) {
			if ( $settings['navigation_as_header'] && ( get_theme_mod( 'custom_logo' ) || ! aureon_get_option( 'hide_title' ) ) ) {
				$classes[] = 'has-branding';
			}
		}

		if ( $settings['sticky_navigation_logo'] ) {
			$classes[] = 'has-sticky-branding';
		}

		return $classes;

	}
}

if ( ! function_exists( 'aureon_menu_plus_label' ) ) {
	add_filter( 'aureon_mobile_menu_label', 'aureon_menu_plus_label' );
	/**
	 * Add mobile menu label
	 *
	 * @since 0.1
	 */
	function aureon_menu_plus_label() {
		$settings = wp_parse_args(
			get_option( 'aureon_menu_plus_settings', array() ),
			aureon_menu_plus_get_defaults()
		);

		return wp_kses_post( $settings['mobile_menu_label'] );
	}
}

if ( ! function_exists( 'aureon_menu_plus_sticky_logo' ) ) {
	add_action( 'aureon_inside_navigation', 'aureon_menu_plus_sticky_logo' );
	/**
	 * Add logo to sticky menu
	 *
	 * @since 0.1
	 */
	function aureon_menu_plus_sticky_logo() {
		$settings = wp_parse_args(
			get_option( 'aureon_menu_plus_settings', array() ),
			aureon_menu_plus_get_defaults()
		);

		if ( '' == $settings['sticky_menu_logo'] ) { // phpcs:ignore -- Non-strict on purpose.
			return;
		}

		// phpcs:ignore -- Escaping not needed.
		echo apply_filters(
			'aureon_navigation_logo_output',
			sprintf(
				'<div class="site-logo sticky-logo navigation-logo">
					<a href="%1$s" title="%2$s" rel="home">
						<img src="%3$s" alt="%4$s" class="is-logo-image" />
					</a>
				</div>',
				esc_url( apply_filters( 'aureon_logo_href', home_url( '/' ) ) ),
				esc_attr( apply_filters( 'aureon_logo_title', get_bloginfo( 'name', 'display' ) ) ),
				esc_url( apply_filters( 'aureon_navigation_logo', $settings['sticky_menu_logo'] ) ),
				esc_attr( apply_filters( 'aureon_logo_title', get_bloginfo( 'name', 'display' ) ) )
			)
		);
	}
}

if ( ! function_exists( 'aureon_menu_plus_mobile_header_logo' ) ) {
	add_action( 'aureon_inside_mobile_header', 'aureon_menu_plus_mobile_header_logo', 5 );
	/**
	 * Add logo to mobile header
	 *
	 * @since 0.1
	 */
	function aureon_menu_plus_mobile_header_logo() {
		$settings = wp_parse_args(
			get_option( 'aureon_menu_plus_settings', array() ),
			aureon_menu_plus_get_defaults()
		);

		if ( 'logo' === $settings['mobile_header_branding'] && '' !== $settings['mobile_header_logo'] ) {
			if ( is_numeric( $settings['mobile_header_logo'] ) ) {
				$image = $settings['mobile_header_logo'];
				$image_url = wp_get_attachment_image_url( $image, 'full' );
			} else {
				$image_url = $settings['mobile_header_logo'];
				$image = attachment_url_to_postid( $image_url );
			}

			$image_width = '';
			$image_height = '';

			if ( $image ) {
				$image = wp_get_attachment_metadata( $image );

				if ( ! empty( $image['width'] ) ) {
					$image_width = $image['width'];
				}

				if ( ! empty( $image['height'] ) ) {
					$image_height = $image['height'];
				}
			}

			// phpcs:ignore -- Escaping not needed.
			echo apply_filters(
				'aureon_mobile_header_logo_output',
				sprintf(
					'<div class="site-logo mobile-header-logo">
						<a href="%1$s" title="%2$s" rel="home">
							<img src="%3$s" alt="%4$s" class="is-logo-image" width="%5$s" height="%6$s" />
						</a>
					</div>',
					esc_url( apply_filters( 'aureon_logo_href', home_url( '/' ) ) ),
					esc_attr( apply_filters( 'aureon_logo_title', get_bloginfo( 'name', 'display' ) ) ),
					esc_url( apply_filters( 'aureon_mobile_header_logo', $image_url ) ),
					esc_attr( apply_filters( 'aureon_logo_title', get_bloginfo( 'name', 'display' ) ) ),
					! empty( $image_width ) ? absint( $image_width ) : '',
					! empty( $image_height ) ? absint( $image_height ) : ''
				),
				$image_url,
				$image
			);
		}

		if ( 'title' === $settings['mobile_header_branding'] ) {
			echo '<div class="navigation-branding">';

				do_action( 'aureon_inside_mobile_header_branding' );

				// phpcs:ignore -- Escaping not needed.
				echo apply_filters(
					'aureon_site_title_output',
					sprintf(
						'<%1$s class="main-title" itemprop="headline">
							<a href="%2$s" rel="home">
								%3$s
							</a>
						</%1$s>',
						( is_front_page() && is_home() ) ? 'h1' : 'p',
						esc_url( apply_filters( 'aureon_site_title_href', home_url( '/' ) ) ),
						get_bloginfo( 'name' )
					)
				);

			echo '</div>';
		}
	}
}

/**
 * Build our off canvas CSS.
 *
 * @since 1.8
 */
function aureon_do_off_canvas_css() {
	if ( ! function_exists( 'aureon_get_color_defaults' ) || ! function_exists( 'aureon_get_defaults' ) || ! function_exists( 'aureon_get_default_fonts' ) ) {
		return;
	}

	$defaults = array_merge( aureon_get_color_defaults(), aureon_get_defaults(), aureon_get_default_fonts() );

	$settings = wp_parse_args(
		get_option( 'aureon_settings', array() ),
		$defaults
	);

	$menu_plus_settings = wp_parse_args(
		get_option( 'aureon_menu_plus_settings', array() ),
		aureon_menu_plus_get_defaults()
	);

	// Check if we're using our legacy typography system.
	$using_dynamic_typography = function_exists( 'aureon_is_using_dynamic_typography' ) && aureon_is_using_dynamic_typography();

	require_once AUREON_LIBRARY_DIRECTORY . 'class-make-css.php';
	$css = new Aureon_Pro_CSS();

	$css->set_selector( ':root' );
	$css->add_property( '--aureon-slideout-width', apply_filters( 'aureon_slideout_width', '265px' ) );

	$css->set_selector( '.slideout-navigation.main-navigation' );
	$css->add_property( 'background-color', esc_attr( $settings['slideout_background_color'] ) );

	$css->set_selector( '.slideout-navigation.main-navigation .main-nav ul li a' );
	$css->add_property( 'color', esc_attr( $settings['slideout_text_color'] ) );

	if ( ! $using_dynamic_typography ) {
		$css->add_property( 'font-weight', esc_attr( $settings['slideout_font_weight'] ) );
		$css->add_property( 'text-transform', esc_attr( $settings['slideout_font_transform'] ) );

		if ( '' !== $settings['slideout_font_size'] ) {
			$css->add_property( 'font-size', absint( $settings['slideout_font_size'] ), false, 'px' );
		}
	}

	$css->set_selector( '.slideout-navigation.main-navigation ul ul' );
	$css->add_property( 'background-color', esc_attr( $settings['slideout_submenu_background_color'] ) );

	$css->set_selector( '.slideout-navigation.main-navigation .main-nav ul ul li a' );
	$css->add_property( 'color', esc_attr( $settings['slideout_submenu_text_color'] ) );

	if ( ! $using_dynamic_typography ) {
		$css->set_selector( '.slideout-navigation.main-navigation.do-overlay .main-nav ul ul li a' );
		$css->add_property( 'font-size', '1em' );

		if ( '' !== $settings['slideout_font_size'] ) {
			$css->add_property( 'font-size', absint( $settings['slideout_font_size'] - 1 ), false, 'px' );
		}

		if ( '' !== $settings['slideout_mobile_font_size'] ) {
			$css->start_media_query( aureon_premium_get_media_query( 'mobile' ) );
				$css->set_selector( '.slideout-navigation.main-navigation .main-nav ul li a' );
				$css->add_property( 'font-size', absint( $settings['slideout_mobile_font_size'] ), false, 'px' );

				$css->set_selector( '.slideout-navigation.main-navigation .main-nav ul ul li a' );
				$css->add_property( 'font-size', absint( $settings['slideout_mobile_font_size'] - 1 ), false, 'px' );
			$css->stop_media_query();
		}
	}

	$css->set_selector( '.slideout-navigation.main-navigation .main-nav ul li:not([class*="current-menu-"]):hover > a, .slideout-navigation.main-navigation .main-nav ul li:not([class*="current-menu-"]):focus > a, .slideout-navigation.main-navigation .main-nav ul li.sfHover:not([class*="current-menu-"]) > a' );
	$css->add_property( 'color', esc_attr( $settings['slideout_text_hover_color'] ) );
	$css->add_property( 'background-color', esc_attr( $settings['slideout_background_hover_color'] ) );

	$css->set_selector( '.slideout-navigation.main-navigation .main-nav ul ul li:not([class*="current-menu-"]):hover > a, .slideout-navigation.main-navigation .main-nav ul ul li:not([class*="current-menu-"]):focus > a, .slideout-navigation.main-navigation .main-nav ul ul li.sfHover:not([class*="current-menu-"]) > a' );
	$css->add_property( 'color', esc_attr( $settings['slideout_submenu_text_hover_color'] ) );
	$css->add_property( 'background-color', esc_attr( $settings['slideout_submenu_background_hover_color'] ) );

	$css->set_selector( '.slideout-navigation.main-navigation .main-nav ul li[class*="current-menu-"] > a' );
	$css->add_property( 'color', esc_attr( $settings['slideout_text_current_color'] ) );
	$css->add_property( 'background-color', esc_attr( $settings['slideout_background_current_color'] ) );

	$css->set_selector( '.slideout-navigation.main-navigation .main-nav ul ul li[class*="current-menu-"] > a' );
	$css->add_property( 'color', esc_attr( $settings['slideout_submenu_text_current_color'] ) );
	$css->add_property( 'background-color', esc_attr( $settings['slideout_submenu_background_current_color'] ) );

	$css->set_selector( '.slideout-navigation, .slideout-navigation a' );

	if ( $settings['slideout_text_color'] ) {
		$css->add_property( 'color', esc_attr( $settings['slideout_text_color'] ) );
	} else {
		$css->add_property( 'color', esc_attr( $settings['navigation_text_color'] ) );
	}

	$css->set_selector( '.slideout-navigation button.slideout-exit' );

	if ( $settings['slideout_text_color'] ) {
		$css->add_property( 'color', esc_attr( $settings['slideout_text_color'] ) );
	} else {
		$css->add_property( 'color', esc_attr( $settings['navigation_text_color'] ) );
	}

	if ( function_exists( 'aureon_spacing_get_defaults' ) ) {
		$spacing_settings = wp_parse_args(
			get_option( 'aureon_spacing_settings', array() ),
			aureon_spacing_get_defaults()
		);

		$css->add_property( 'padding-left', absint( $spacing_settings['menu_item'] ), false, 'px' );
		$css->add_property( 'padding-right', absint( $spacing_settings['menu_item'] ), false, 'px' );

		if ( ! empty( $settings['mobile_menu_item'] ) ) {
			$css->start_media_query( aureon_premium_get_media_query( 'mobile' ) );
				$css->set_selector( '.slideout-navigation button.slideout-exit' );

				$css->add_property( 'padding-left', absint( $spacing_settings['mobile_menu_item'] ), false, 'px' );
				$css->add_property( 'padding-right', absint( $spacing_settings['mobile_menu_item'] ), false, 'px' );
			$css->stop_media_query();
		}
	}

	if ( function_exists( 'aureon_get_option' ) && function_exists( 'aureon_get_defaults' ) ) {
		$theme_defaults = aureon_get_defaults();

		if ( isset( $theme_defaults['icons'] ) ) {
			if ( 'svg' === aureon_get_option( 'icons' ) ) {
				$css->set_selector( '.slide-opened nav.toggled .menu-toggle:before' );
				$css->add_property( 'display', 'none' );
			}

			if ( 'font' === aureon_get_option( 'icons' ) ) {
				$css->set_selector( '.slideout-navigation .dropdown-menu-toggle:before' );
				$css->add_property( 'content', '"\f107"' );

				$css->set_selector( '.slideout-navigation .sfHover > a .dropdown-menu-toggle:before' );
				$css->add_property( 'content', '"\f106"' );
			}
		}
	}

	$css->start_media_query( aureon_premium_get_media_query( 'mobile-menu' ) );
		$css->set_selector( '.menu-bar-item.slideout-toggle' );
		$css->add_property( 'display', 'none' );
	$css->stop_media_query();

	return $css->css_output();
}

/**
 * Write dynamic CSS for our navigation branding.
 *
 * @since 1.8
 */
function aureon_do_nav_branding_css() {
	if ( ! function_exists( 'aureon_get_color_defaults' ) || ! function_exists( 'aureon_get_defaults' ) || ! function_exists( 'aureon_get_default_fonts' ) ) {
		return;
	}

	$defaults = array_merge( aureon_get_color_defaults(), aureon_get_defaults(), aureon_get_default_fonts() );

	$settings = wp_parse_args(
		get_option( 'aureon_settings', array() ),
		$defaults
	);

	$menu_plus_settings = wp_parse_args(
		get_option( 'aureon_menu_plus_settings', array() ),
		aureon_menu_plus_get_defaults()
	);

	require_once AUREON_LIBRARY_DIRECTORY . 'class-make-css.php';
	$css = new Aureon_Pro_CSS();

	if ( 'enable' === $menu_plus_settings['mobile_header'] ) {
		$css->start_media_query( aureon_premium_get_media_query( 'mobile-menu' ) );
		$css->set_selector( '.site-header, #site-navigation, #sticky-navigation' );
		$css->add_property( 'display', 'none !important' );
		$css->add_property( 'opacity', '0.0' );

		$css->set_selector( '#mobile-header' );
		$css->add_property( 'display', 'block !important' );
		$css->add_property( 'width', '100% !important' );

		$css->set_selector( '#mobile-header .main-nav > ul' );
		$css->add_property( 'display', 'none' );

		$css->set_selector( '#mobile-header.toggled .main-nav > ul, #mobile-header .menu-toggle, #mobile-header .mobile-bar-items' );
		$css->add_property( 'display', 'block' );

		$css->set_selector( '#mobile-header .main-nav' );
		$css->add_property( '-webkit-box-flex', '0' );
		$css->add_property( '-ms-flex', '0 0 100%' );
		$css->add_property( 'flex', '0 0 100%' );
		$css->add_property( '-webkit-box-ordinal-group', '5' );
		$css->add_property( '-ms-flex-order', '4' );
		$css->add_property( 'order', '4' );

		if ( ! $menu_plus_settings['navigation_as_header'] && 'title' === $menu_plus_settings['mobile_header_branding'] ) {
			$css->set_selector( '.navigation-branding .main-title a, .navigation-branding .main-title a:hover, .navigation-branding .main-title a:visited' );
			$css->add_property( 'color', $settings['navigation_text_color'] );
		}
		$css->stop_media_query();
	}

	$is_using_dynamic_typography = function_exists( 'aureon_is_using_dynamic_typography' ) && aureon_is_using_dynamic_typography();

	if ( ! function_exists( 'aureon_typography_premium_css' ) && ! $is_using_dynamic_typography ) {
		$css->set_selector( '.navigation-branding .main-title' );
		$css->add_property( 'font-size', '25px' );
		$css->add_property( 'font-weight', 'bold' );
	}

	$navigation_height = 60;
	$mobile_navigation_height = '';
	$content_left = 40;
	$content_right = 40;

	if ( function_exists( 'aureon_spacing_get_defaults' ) ) {
		$spacing_settings = wp_parse_args(
			get_option( 'aureon_spacing_settings', array() ),
			aureon_spacing_get_defaults()
		);

		$navigation_height = $spacing_settings['menu_item_height'];

		if ( isset( $spacing_settings['mobile_menu_item_height'] ) ) {
			$mobile_navigation_height = $spacing_settings['mobile_menu_item_height'];
		}

		$content_left = $spacing_settings['content_left'];
		$content_right = $spacing_settings['content_right'];
	}

	if ( function_exists( 'aureon_is_using_flexbox' ) && aureon_is_using_flexbox() ) {
		if ( function_exists( 'aureon_get_option' ) ) {
			if ( 'text' === aureon_get_option( 'container_alignment' ) ) {
				$css->set_selector( '.main-navigation.has-branding .inside-navigation.grid-container, .main-navigation.has-branding.grid-container .inside-navigation:not(.grid-container)' );
				$css->add_property( 'padding', aureon_padding_css( 0, $content_right, 0, $content_left ) );

				$css->set_selector( '.main-navigation.has-branding:not(.grid-container) .inside-navigation:not(.grid-container) .navigation-branding' );

				if ( ! is_rtl() ) {
					$css->add_property( 'margin-left', '10px' );
				} else {
					$css->add_property( 'margin-right', '10px' );
				}
			} else {
				$css->set_selector( '.main-navigation.has-branding.grid-container .navigation-branding, .main-navigation.has-branding:not(.grid-container) .inside-navigation:not(.grid-container) .navigation-branding' );

				if ( ! is_rtl() ) {
					$css->add_property( 'margin-left', '10px' );
				} else {
					$css->add_property( 'margin-right', '10px' );
				}
			}
		}
	}

	if ( '' !== $menu_plus_settings['sticky_navigation_logo'] ) {
		$css->set_selector( '.main-navigation .sticky-navigation-logo, .main-navigation.navigation-stick .site-logo:not(.mobile-header-logo)' );
		$css->add_property( 'display', 'none' );

		$css->set_selector( '.main-navigation.navigation-stick .sticky-navigation-logo' );
		$css->add_property( 'display', 'block' );
	}

	$css->set_selector( '.navigation-branding img, .site-logo.mobile-header-logo img' );
	$css->add_property( 'height', absint( $navigation_height ), false, 'px' );
	$css->add_property( 'width', 'auto' );

	$css->set_selector( '.navigation-branding .main-title' );
	$css->add_property( 'line-height', absint( $navigation_height ), false, 'px' );

	$do_nav_padding = true;

	if ( function_exists( 'aureon_is_using_flexbox' ) && aureon_is_using_flexbox() ) {
		if ( function_exists( 'aureon_get_option' ) && 'text' === aureon_get_option( 'container_alignment' ) ) {
			$do_nav_padding = false;
		}
	}

	if ( $do_nav_padding ) {
		$css->start_media_query( '(max-width: ' . ( $settings['container_width'] + 10 ) . 'px)' );
		$css->set_selector( '#site-navigation .navigation-branding, #sticky-navigation .navigation-branding' );
		$css->add_property( 'margin-left', '10px' );

		if ( is_rtl() ) {
			$css->set_selector( '#site-navigation .navigation-branding, #sticky-navigation .navigation-branding' );
			$css->add_property( 'margin-left', 'auto' );
			$css->add_property( 'margin-right', '10px' );
		}
		$css->stop_media_query();
	}

	$css->start_media_query( aureon_premium_get_media_query( 'mobile-menu' ) );
	if ( function_exists( 'aureon_is_using_flexbox' ) && aureon_is_using_flexbox() ) {
		$css->set_selector( '.main-navigation.has-branding.nav-align-center .menu-bar-items, .main-navigation.has-sticky-branding.navigation-stick.nav-align-center .menu-bar-items' );
		$css->add_property( 'margin-left', 'auto' );

		$css->set_selector( '.navigation-branding' );
		$css->add_property( 'margin-right', 'auto' );
		$css->add_property( 'margin-left', '10px' );

		$css->set_selector( '.navigation-branding .main-title, .mobile-header-navigation .site-logo' );
		$css->add_property( 'margin-left', '10px' );

		if ( is_rtl() ) {
			$css->set_selector( '.rtl .navigation-branding' );
			$css->add_property( 'margin-left', 'auto' );
			$css->add_property( 'margin-right', '10px' );

			$css->set_selector( '.rtl .navigation-branding .main-title, .rtl .mobile-header-navigation .site-logo' );
			$css->add_property( 'margin-right', '10px' );
			$css->add_property( 'margin-left', '0px' );

			$css->set_selector( '.rtl .main-navigation.has-branding.nav-align-center .menu-bar-items, .rtl .main-navigation.has-sticky-branding.navigation-stick.nav-align-center .menu-bar-items' );
			$css->add_property( 'margin-left', '0px' );
			$css->add_property( 'margin-right', 'auto' );
		}

		if ( function_exists( 'aureon_get_option' ) && 'text' === aureon_get_option( 'container_alignment' ) ) {
			$css->set_selector( '.main-navigation.has-branding .inside-navigation.grid-container' );
			$css->add_property( 'padding', '0px' );
		}
	} else {
		$css->set_selector( '.main-navigation:not(.slideout-navigation) .main-nav' );
		$css->add_property( '-webkit-box-flex', '0' );
		$css->add_property( '-ms-flex', '0 0 100%' );
		$css->add_property( 'flex', '0 0 100%' );

		$css->set_selector( '.main-navigation:not(.slideout-navigation) .inside-navigation' );
		$css->add_property( '-ms-flex-wrap', 'wrap' );
		$css->add_property( 'flex-wrap', 'wrap' );
		$css->add_property( 'display', '-webkit-box' );
		$css->add_property( 'display', '-ms-flexbox' );
		$css->add_property( 'display', 'flex' );

		$css->set_selector( '.nav-aligned-center .navigation-branding, .nav-aligned-left .navigation-branding' );
		$css->add_property( 'margin-right', 'auto' );

		$css->set_selector( '.nav-aligned-center  .main-navigation.has-branding:not(.slideout-navigation) .inside-navigation .main-nav,.nav-aligned-center  .main-navigation.has-sticky-branding.navigation-stick .inside-navigation .main-nav,.nav-aligned-left  .main-navigation.has-branding:not(.slideout-navigation) .inside-navigation .main-nav,.nav-aligned-left  .main-navigation.has-sticky-branding.navigation-stick .inside-navigation .main-nav' );
		$css->add_property( 'margin-right', '0px' );
	}

	if ( '' !== $mobile_navigation_height ) {
		$css->set_selector( '.navigation-branding img, .site-logo.mobile-header-logo' );
		$css->add_property( 'height', absint( $mobile_navigation_height ), false, 'px' );

		$css->set_selector( '.navigation-branding .main-title' );
		$css->add_property( 'line-height', absint( $mobile_navigation_height ), false, 'px' );
	}
	$css->stop_media_query();

	return $css->css_output();
}

/**
 * Add dynamic CSS for the legacy navigation logo option deprecated in 1.8.
 *
 * @since 1.8
 */
function aureon_do_mobile_navigation_logo_css() {
	require_once AUREON_LIBRARY_DIRECTORY . 'class-make-css.php';
	$css = new Aureon_Pro_CSS();

	$css->start_media_query( aureon_premium_get_media_query( 'mobile-menu' ) );
		// Sticky & Sticky + Static logo.
		$css->set_selector( '.sticky-menu-logo .navigation-stick:not(.mobile-header-navigation) .menu-toggle,.menu-logo .main-navigation:not(.mobile-header-navigation) .menu-toggle' );
		$css->add_property( 'display', 'inline-block' );
		$css->add_property( 'clear', 'none' );
		$css->add_property( 'width', 'auto' );
		$css->add_property( 'float', 'right' );

		$css->set_selector( '.sticky-menu-logo .navigation-stick:not(.mobile-header-navigation) .mobile-bar-items,.menu-logo .main-navigation:not(.mobile-header-navigation) .mobile-bar-items' );
		$css->add_property( 'position', 'relative' );
		$css->add_property( 'float', 'right' );

		// Static logo.
		$css->set_selector( '.regular-menu-logo .main-navigation:not(.navigation-stick):not(.mobile-header-navigation) .menu-toggle' );
		$css->add_property( 'display', 'inline-block' );
		$css->add_property( 'clear', 'none' );
		$css->add_property( 'width', 'auto' );
		$css->add_property( 'float', 'right' );

		$css->set_selector( '.regular-menu-logo .main-navigation:not(.navigation-stick):not(.mobile-header-navigation) .mobile-bar-items' );
		$css->add_property( 'position', 'relative' );
		$css->add_property( 'float', 'right' );

		$css->set_selector( 'body[class*="nav-float-"].menu-logo-enabled:not(.sticky-menu-logo) .main-navigation .main-nav' );
		$css->add_property( 'display', 'block' );

		// Navigation floating left.
		$css->set_selector( '.sticky-menu-logo.nav-float-left .navigation-stick:not(.mobile-header-navigation) .menu-toggle,.menu-logo.nav-float-left .main-navigation:not(.mobile-header-navigation) .menu-toggle,.regular-menu-logo.nav-float-left .main-navigation:not(.navigation-stick):not(.mobile-header-navigation) .menu-toggle' );
		$css->add_property( 'float', 'left' );
	$css->stop_media_query();

	return $css->css_output();
}

add_action( 'aureon_inside_slideout_navigation', 'aureon_do_slideout_menu_close_button' );
/**
 * Add a button inside the slideout nav to close it.
 *
 * @since 1.8
 */
function aureon_do_slideout_menu_close_button() {
	$settings = wp_parse_args(
		get_option( 'aureon_menu_plus_settings', array() ),
		aureon_menu_plus_get_defaults()
	);

	if ( 'inside' === $settings['slideout_close_button'] || 'overlay' === $settings['slideout_menu_style'] ) {
		$svg_icon = '';

		if ( function_exists( 'aureon_get_svg_icon' ) ) {
			$svg_icon = aureon_get_svg_icon( 'pro-close' );
		}

		// phpcs:ignore -- No escaping needed.
		echo apply_filters(
			'aureon_close_slideout_navigation_button',
			sprintf(
				'<button class="slideout-exit %3$s">%1$s <span class="screen-reader-text">%2$s</span></button>',
				$svg_icon,
				esc_html__( 'Close', 'aureon-studio' ),
				$svg_icon ? 'has-svg-icon' : ''
			)
		);
	}
}

add_action( 'wp', 'aureon_menu_plus_remove_header', 200 );
/**
 * Remove our header if we're using the navigation as a header.
 *
 * @since 1.8
 */
function aureon_menu_plus_remove_header() {
	$settings = wp_parse_args(
		get_option( 'aureon_menu_plus_settings', array() ),
		aureon_menu_plus_get_defaults()
	);

	if ( $settings['navigation_as_header'] ) {
		remove_action( 'aureon_header', 'aureon_construct_header' );
		add_filter( 'aureon_navigation_location', 'aureon_set_navigation_location_as_header' );
	}
}

/**
 * Set our navigation location if we're using our navigation as the header.
 *
 * @since 1.8
 */
function aureon_set_navigation_location_as_header() {
	return 'nav-below-header';
}

add_action( 'aureon_inside_navigation', 'aureon_do_navigation_branding' );
/**
 * Add our navigation logo if set.
 *
 * @since 1.8
 */
function aureon_do_navigation_branding() {
	$settings = wp_parse_args(
		get_option( 'aureon_menu_plus_settings', array() ),
		aureon_menu_plus_get_defaults()
	);

	if ( ! function_exists( 'aureon_get_option' ) ) {
		return;
	}

	$logo = false;
	$sticky_logo = false;
	$site_title = false;

	if ( $settings['navigation_as_header'] && get_theme_mod( 'custom_logo' ) ) {
		$logo_url = wp_get_attachment_image_src( get_theme_mod( 'custom_logo' ), 'full' );
		$logo_url = esc_url( apply_filters( 'aureon_logo', $logo_url[0] ) );
		$retina_logo_url = esc_url( apply_filters( 'aureon_retina_logo', aureon_get_option( 'retina_logo' ) ) );

		if ( $logo_url ) {
			$attr = apply_filters(
				'aureon_logo_attributes',
				array(
					'class' => 'header-image is-logo-image',
					'alt'   => esc_attr( apply_filters( 'aureon_logo_title', get_bloginfo( 'name', 'display' ) ) ),
					'src'   => $logo_url,
					'title' => esc_attr( apply_filters( 'aureon_logo_title', get_bloginfo( 'name', 'display' ) ) ),
				)
			);

			if ( '' !== $retina_logo_url ) {
				$attr['srcset'] = $logo_url . ' 1x, ' . $retina_logo_url . ' 2x';
			}

			if ( function_exists( 'the_custom_logo' ) && get_theme_mod( 'custom_logo' ) ) {
				$data = wp_get_attachment_metadata( get_theme_mod( 'custom_logo' ) );

				if ( ! empty( $data ) ) {
					if ( isset( $data['width'] ) ) {
						$attr['width'] = $data['width'];
					}

					if ( isset( $data['height'] ) ) {
						$attr['height'] = $data['height'];
					}
				}
			}

			$attr = array_map( 'esc_attr', $attr );

			$html_attr = '';
			foreach ( $attr as $name => $value ) {
				$html_attr .= " $name=" . '"' . $value . '"';
			}

			// Print our HTML.
			$logo = apply_filters(
				'aureon_logo_output',
				sprintf(
					'<div class="site-logo">
						<a href="%1$s" title="%2$s" rel="home">
							<img %3$s />
						</a>
					</div>',
					esc_url( apply_filters( 'aureon_logo_href', home_url( '/' ) ) ),
					esc_attr( apply_filters( 'aureon_logo_title', get_bloginfo( 'name', 'display' ) ) ),
					$html_attr
				),
				$logo_url,
				$html_attr
			);
		}
	}

	if ( 'false' !== $settings['sticky_menu'] && '' !== $settings['sticky_navigation_logo'] ) {
		if ( is_numeric( $settings['sticky_navigation_logo'] ) ) {
			$image = $settings['sticky_navigation_logo'];
			$image_url = wp_get_attachment_image_url( $image, 'full' );
		} else {
			$image_url = $settings['sticky_navigation_logo'];
			$image = attachment_url_to_postid( $image_url );
		}

		$image_width = '';
		$image_height = '';

		if ( $image ) {
			$image = wp_get_attachment_metadata( $image );

			if ( ! empty( $image['width'] ) ) {
				$image_width = $image['width'];
			}

			if ( ! empty( $image['height'] ) ) {
				$image_height = $image['height'];
			}
		}

		$sticky_logo = apply_filters(
			'aureon_sticky_navigation_logo_output',
			sprintf(
				'<div class="sticky-navigation-logo">
					<a href="%1$s" title="%2$s" rel="home">
						<img src="%3$s" class="is-logo-image" alt="%2$s" width="%4$s" height="%5$s" />
					</a>
				</div>',
				esc_url( apply_filters( 'aureon_logo_href', home_url( '/' ) ) ),
				esc_attr( apply_filters( 'aureon_logo_title', get_bloginfo( 'name', 'display' ) ) ),
				esc_url( $image_url ),
				! empty( $image_width ) ? absint( $image_width ) : '',
				! empty( $image_height ) ? absint( $image_height ) : ''
			),
			$image_url,
			$image
		);
	}

	if ( $settings['navigation_as_header'] && ! aureon_get_option( 'hide_title' ) ) {
		$site_title = apply_filters(
			'aureon_site_title_output',
			sprintf(
				'<%1$s class="main-title" itemprop="headline">
					<a href="%2$s" rel="home">
						%3$s
					</a>
				</%1$s>',
				( is_front_page() && is_home() ) ? 'h1' : 'p',
				esc_url( apply_filters( 'aureon_site_title_href', home_url( '/' ) ) ),
				get_bloginfo( 'name' )
			)
		);
	}

	if ( $logo || $sticky_logo || $site_title ) {
		echo '<div class="navigation-branding">';

		if ( $logo ) {
			/**
			 * aureon_before_logo hook.
			 *
			 * @since 0.1
			 */
			do_action( 'aureon_before_logo' );

			echo $logo; // phpcs:ignore -- No escaping needed.

			/**
			 * aureon_after_logo hook.
			 *
			 * @since 0.1
			 */
			do_action( 'aureon_after_logo' );
		}

		if ( $sticky_logo ) {
			echo $sticky_logo; // phpcs:ignore -- No escaping needed.
		}

		if ( $site_title ) {
			echo $site_title; // phpcs:ignore -- No escaping needed.
		}

		echo '</div>';
	}
}

add_filter( 'aureon_mobile_menu_media_query', 'aureon_set_mobile_menu_breakpoint' );
/**
 * Set the mobile menu breakpoint.
 *
 * @since 1.8
 *
 * @param string $breakpoint Current breakpoint.
 * @return string
 */
function aureon_set_mobile_menu_breakpoint( $breakpoint ) {
	$settings = wp_parse_args(
		get_option( 'aureon_menu_plus_settings', array() ),
		aureon_menu_plus_get_defaults()
	);

	// This setting shouldn't apply if the mobile header isn't on and we're using GP < 2.3.
	if ( defined( 'AUREON_VERSION' ) && version_compare( AUREON_VERSION, '2.3-alpha.1', '<' ) ) {
		if ( 'enable' !== $settings['mobile_header'] ) {
			return $breakpoint;
		}
	}

	$mobile_menu_breakpoint = $settings['mobile_menu_breakpoint'];

	if ( '' !== $mobile_menu_breakpoint ) {
		return '(max-width: ' . absint( $mobile_menu_breakpoint ) . 'px)';
	}

	return $breakpoint;
}

add_filter( 'aureon_not_mobile_menu_media_query', 'aureon_set_not_mobile_menu_breakpoint' );
/**
 * Set the breakpoint when the mobile menu doesn't apply.
 *
 * @since 1.8.3
 *
 * @param string $breakpoint Existing breakpoint.
 * @return string
 */
function aureon_set_not_mobile_menu_breakpoint( $breakpoint ) {
	$settings = wp_parse_args(
		get_option( 'aureon_menu_plus_settings', array() ),
		aureon_menu_plus_get_defaults()
	);

	// This setting shouldn't apply if the mobile header isn't on and we're using GP < 2.3.
	if ( defined( 'AUREON_VERSION' ) && version_compare( AUREON_VERSION, '2.3-alpha.1', '<' ) ) {
		if ( 'enable' !== $settings['mobile_header'] ) {
			return $breakpoint;
		}
	}

	$mobile_menu_breakpoint = $settings['mobile_menu_breakpoint'];

	if ( '' !== $mobile_menu_breakpoint && is_int( $mobile_menu_breakpoint ) ) {
		return '(min-width: ' . ( absint( $mobile_menu_breakpoint ) + 1 ) . 'px)';
	}

	return $breakpoint;
}

add_filter( 'aureon_has_active_menu', 'aureon_menu_plus_set_active_menu' );
/**
 * Tell GP about our active menus.
 *
 * @since 2.1.0
 * @param boolean $has_active_menu Whether we have an active menu.
 */
function aureon_menu_plus_set_active_menu( $has_active_menu ) {
	$settings = wp_parse_args(
		get_option( 'aureon_menu_plus_settings', array() ),
		aureon_menu_plus_get_defaults()
	);

	if ( 'disable' !== $settings['mobile_header'] || 'false' !== $settings['slideout_menu'] ) {
		return true;
	}

	return $has_active_menu;
}

add_filter( 'aureon_typography_css_selector', 'aureon_menu_plus_typography_selectors' );
/**
 * Add the Menu Plus typography CSS selectors.
 *
 * @since 2.1.0
 * @param string $selector The selector we're targeting.
 */
function aureon_menu_plus_typography_selectors( $selector ) {
	switch ( $selector ) {
		case 'off-canvas-panel-menu-items':
			$selector = '.slideout-navigation.main-navigation .main-nav ul li a';
			break;

		case 'off-canvas-panel-sub-menu-items':
			$selector = '.slideout-navigation.main-navigation .main-nav ul ul li a';
			break;
	}

	return $selector;
}

add_filter( 'aureon_parse_attr', 'aureon_set_off_canvas_toggle_attributes', 20, 2 );
/**
 * Add attributes to our menu-toggle element when using the Off Canvas panel.
 *
 * @since 2.2.0
 * @param array  $attributes The current attributes.
 * @param string $context The context in which attributes are applied.
 */
function aureon_set_off_canvas_toggle_attributes( $attributes, $context ) {
	if ( 'menu-toggle' === $context ) {
		$settings = wp_parse_args(
			get_option( 'aureon_menu_plus_settings', array() ),
			aureon_menu_plus_get_defaults()
		);

		if ( 'mobile' === $settings['slideout_menu'] || 'both' === $settings['slideout_menu'] ) {
			$attributes['aria-controls'] = 'aureon-slideout-menu';
		}
	}

	return $attributes;
}
