<?php
/**
 * This file handles all of the Secondary Navigation functionality.
 *
 * @package Aureon Studio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

// Add necessary files.
require plugin_dir_path( __FILE__ ) . 'css.php';

if ( ! function_exists( 'aureon_secondary_nav_setup' ) ) {
	add_action( 'after_setup_theme', 'aureon_secondary_nav_setup', 50 );
	/**
	 * Register our secondary navigation
	 *
	 * @since 0.1
	 */
	function aureon_secondary_nav_setup() {
		register_nav_menus(
			array(
				'secondary' => __( 'Secondary Menu', 'aureon-studio' ),
			)
		);
	}
}

if ( ! function_exists( 'aureon_secondary_nav_enqueue_scripts' ) ) {
	add_action( 'wp_enqueue_scripts', 'aureon_secondary_nav_enqueue_scripts', 100 );
	/**
	 * Add our necessary scripts.
	 *
	 * @since 0.1
	 */
	function aureon_secondary_nav_enqueue_scripts() {
		// Bail if no Secondary menu is set.
		if ( ! has_nav_menu( 'secondary' ) ) {
			return;
		}

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		if ( function_exists( 'aureon_is_using_flexbox' ) && aureon_is_using_flexbox() ) {
			wp_enqueue_style( 'aureon-secondary-nav', plugin_dir_url( __FILE__ ) . "css/main{$suffix}.css", array(), AUREON_SECONDARY_NAV_VERSION );
			wp_enqueue_style( 'aureon-secondary-nav-mobile', plugin_dir_url( __FILE__ ) . "css/main-mobile{$suffix}.css", array(), AUREON_SECONDARY_NAV_VERSION, 'all' );
		} else {
			wp_enqueue_style( 'aureon-secondary-nav', plugin_dir_url( __FILE__ ) . "css/style{$suffix}.css", array(), AUREON_SECONDARY_NAV_VERSION );
			wp_enqueue_style( 'aureon-secondary-nav-mobile', plugin_dir_url( __FILE__ ) . "css/style-mobile{$suffix}.css", array(), AUREON_SECONDARY_NAV_VERSION, 'all' );
		}

		if ( ! defined( 'AUREON_DISABLE_MOBILE' ) ) {
			wp_add_inline_script(
				'aureon-navigation',
				"jQuery( document ).ready( function($) {
					$( '.secondary-navigation .menu-toggle' ).on( 'click', function( e ) {
						e.preventDefault();
						$( this ).closest( '.secondary-navigation' ).toggleClass( 'toggled' );
						$( this ).closest( '.secondary-navigation' ).attr( 'aria-expanded', $( this ).closest( '.secondary-navigation' ).attr( 'aria-expanded' ) === 'true' ? 'false' : 'true' );
						$( this ).toggleClass( 'toggled' );
						$( this ).children( 'i' ).toggleClass( 'fa-bars' ).toggleClass( 'fa-close' );
						$( this ).attr( 'aria-expanded', $( this ).attr( 'aria-expanded' ) === 'false' ? 'true' : 'false' );
					});
				});"
			);
		}
	}
}

if ( ! function_exists( 'aureon_secondary_nav_enqueue_customizer_scripts' ) ) {
	add_action( 'customize_preview_init', 'aureon_secondary_nav_enqueue_customizer_scripts' );
	/**
	 * Add our Customizer preview JS.
	 *
	 * @since 0.1
	 */
	function aureon_secondary_nav_enqueue_customizer_scripts() {
		wp_enqueue_script( 'aureon-secondary-nav-customizer', plugin_dir_url( __FILE__ ) . 'js/customizer.js', array( 'jquery', 'customize-preview' ), AUREON_SECONDARY_NAV_VERSION, true );

		wp_localize_script(
			'aureon-secondary-nav-customizer',
			'generateSecondaryNav',
			array(
				'isFlex' => function_exists( 'aureon_is_using_flexbox' ) && aureon_is_using_flexbox(),
			)
		);
	}
}

if ( ! function_exists( 'aureon_secondary_nav_get_defaults' ) ) {
	/**
	 * Set default options.
	 *
	 * @since 0.1
	 * @param bool $filter Whether to filter the defaults or not.
	 */
	function aureon_secondary_nav_get_defaults( $filter = true ) {
		$aureon_defaults = array(
			'secondary_nav_mobile_label' => 'Menu',
			'secondary_nav_layout_setting' => 'secondary-fluid-nav',
			'secondary_nav_inner_width' => 'contained',
			'secondary_nav_position_setting' => 'secondary-nav-above-header',
			'secondary_nav_alignment' => 'right',
			'secondary_nav_dropdown_direction' => 'right',
			'navigation_background_color' => '#636363',
			'navigation_text_color' => '#ffffff',
			'navigation_background_hover_color' => '#303030',
			'navigation_text_hover_color' => '#ffffff',
			'navigation_background_current_color' => '#ffffff',
			'navigation_text_current_color' => '#222222',
			'subnavigation_background_color' => '#303030',
			'subnavigation_text_color' => '#ffffff',
			'subnavigation_background_hover_color' => '#474747',
			'subnavigation_text_hover_color' => '#ffffff',
			'subnavigation_background_current_color' => '#474747',
			'subnavigation_text_current_color' => '#ffffff',
			'secondary_menu_item' => '20',
			'secondary_menu_item_height' => '40',
			'secondary_sub_menu_item_height' => '10',
			'font_secondary_navigation' => 'inherit',
			'font_secondary_navigation_variants' => '',
			'font_secondary_navigation_category' => '',
			'secondary_navigation_font_weight' => 'normal',
			'secondary_navigation_font_transform' => 'none',
			'secondary_navigation_font_size' => '13',
			'nav_image' => '',
			'nav_repeat' => '',
			'nav_item_image' => '',
			'nav_item_repeat' => '',
			'nav_item_hover_image' => '',
			'nav_item_hover_repeat' => '',
			'nav_item_current_image' => '',
			'nav_item_current_repeat' => '',
			'sub_nav_image' => '',
			'sub_nav_repeat' => '',
			'sub_nav_item_image' => '',
			'sub_nav_item_repeat' => '',
			'sub_nav_item_hover_image' => '',
			'sub_nav_item_hover_repeat' => '',
			'sub_nav_item_current_image' => '',
			'sub_nav_item_current_repeat' => '',
			'merge_top_bar' => false,
		);

		if ( $filter ) {
			return apply_filters( 'aureon_secondary_nav_option_defaults', $aureon_defaults );
		}

		return $aureon_defaults;
	}
}

if ( ! function_exists( 'aureon_secondary_nav_customize_register' ) ) {
	add_action( 'customize_register', 'aureon_secondary_nav_customize_register', 100 );
	/**
	 * Register our options.
	 *
	 * @since 0.1
	 * @param object $wp_customize The Customizer object.
	 */
	function aureon_secondary_nav_customize_register( $wp_customize ) {
		$defaults = aureon_secondary_nav_get_defaults();

		require_once AUREON_LIBRARY_DIRECTORY . 'customizer-helpers.php';

		if ( method_exists( $wp_customize, 'register_control_type' ) ) {
			$wp_customize->register_control_type( 'Aureon_Section_Shortcut_Control' );
		}

		// Use the Layout panel in the free theme if it exists.
		if ( $wp_customize->get_panel( 'aureon_layout_panel' ) ) {
			$layout_panel = 'aureon_layout_panel';
		} else {
			$layout_panel = 'secondary_navigation_panel';
		}

		// Add our secondary navigation panel.
		// This shouldn't be used anymore if the theme is up to date.
		if ( class_exists( 'WP_Customize_Panel' ) ) {
			$wp_customize->add_panel(
				'secondary_navigation_panel',
				array(
					'priority'       => 100,
					'capability'     => 'edit_theme_options',
					'theme_supports' => '',
					'title'          => __( 'Secondary Navigation', 'aureon-studio' ),
					'description'    => '',
				)
			);
		}

		$wp_customize->add_section(
			'secondary_nav_section',
			array(
				'title' => __( 'Secondary Navigation', 'aureon-studio' ),
				'capability' => 'edit_theme_options',
				'priority' => 31,
				'panel' => $layout_panel,
			)
		);

		$wp_customize->add_control(
			new Aureon_Section_Shortcut_Control(
				$wp_customize,
				'aureon_secondary_navigation_layout_shortcuts',
				array(
					'section' => 'secondary_nav_section',
					'element' => __( 'Secondary Navigation', 'aureon-studio' ),
					'shortcuts' => array(
						'colors' => 'secondary_navigation_color_section',
						'typography' => 'secondary_font_section',
						'backgrounds' => 'secondary_bg_images_section',
					),
					'settings' => ( isset( $wp_customize->selective_refresh ) ) ? array() : 'blogname',
					'priority' => 1,
				)
			)
		);

		$wp_customize->add_setting(
			'aureon_secondary_nav_settings[secondary_nav_mobile_label]',
			array(
				'default' => $defaults['secondary_nav_mobile_label'],
				'type' => 'option',
				'sanitize_callback' => 'wp_kses_post',
			)
		);

		$wp_customize->add_control(
			'secondary_nav_mobile_label_control',
			array(
				'label' => __( 'Mobile Menu Label', 'aureon-studio' ),
				'section' => 'secondary_nav_section',
				'settings' => 'aureon_secondary_nav_settings[secondary_nav_mobile_label]',
				'priority' => 10,
			)
		);

		$wp_customize->add_setting(
			'aureon_secondary_nav_settings[secondary_nav_layout_setting]',
			array(
				'default' => $defaults['secondary_nav_layout_setting'],
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_choices',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			'aureon_secondary_nav_settings[secondary_nav_layout_setting]',
			array(
				'type' => 'select',
				'label' => __( 'Navigation Width', 'aureon-studio' ),
				'section' => 'secondary_nav_section',
				'choices' => array(
					'secondary-fluid-nav' => _x( 'Full', 'Width', 'aureon-studio' ),
					'secondary-contained-nav' => _x( 'Contained', 'Width', 'aureon-studio' ),
				),
				'settings' => 'aureon_secondary_nav_settings[secondary_nav_layout_setting]',
				'priority' => 15,
			)
		);

		$wp_customize->add_setting(
			'aureon_secondary_nav_settings[secondary_nav_inner_width]',
			array(
				'default' => $defaults['secondary_nav_inner_width'],
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_choices',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			'aureon_secondary_nav_settings[secondary_nav_inner_width]',
			array(
				'type' => 'select',
				'label' => __( 'Inner Navigation Width', 'aureon-studio' ),
				'section' => 'secondary_nav_section',
				'choices' => array(
					'full-width' => _x( 'Full', 'Width', 'aureon-studio' ),
					'contained' => _x( 'Contained', 'Width', 'aureon-studio' ),
				),
				'settings' => 'aureon_secondary_nav_settings[secondary_nav_inner_width]',
				'priority' => 15,
			)
		);

		$wp_customize->add_setting(
			'aureon_secondary_nav_settings[secondary_nav_alignment]',
			array(
				'default' => $defaults['secondary_nav_alignment'],
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_choices',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			'aureon_secondary_nav_settings[secondary_nav_alignment]',
			array(
				'type' => 'select',
				'label' => __( 'Navigation Alignment', 'aureon-studio' ),
				'section' => 'secondary_nav_section',
				'choices' => array(
					'left' => __( 'Left', 'aureon-studio' ),
					'center' => __( 'Center', 'aureon-studio' ),
					'right' => __( 'Right', 'aureon-studio' ),
				),
				'settings' => 'aureon_secondary_nav_settings[secondary_nav_alignment]',
				'priority' => 20,
			)
		);

		$wp_customize->add_setting(
			'aureon_secondary_nav_settings[secondary_nav_position_setting]',
			array(
				'default' => $defaults['secondary_nav_position_setting'],
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_choices',
			)
		);

		$wp_customize->add_control(
			'aureon_secondary_nav_settings[secondary_nav_position_setting]',
			array(
				'type' => 'select',
				'label' => __( 'Navigation Location', 'aureon-studio' ),
				'section' => 'secondary_nav_section',
				'choices' => array(
					'secondary-nav-below-header' => __( 'Below Header', 'aureon-studio' ),
					'secondary-nav-above-header' => __( 'Above Header', 'aureon-studio' ),
					'secondary-nav-float-right' => __( 'Float Right', 'aureon-studio' ),
					'secondary-nav-float-left' => __( 'Float Left', 'aureon-studio' ),
					'secondary-nav-left-sidebar' => __( 'Left Sidebar', 'aureon-studio' ),
					'secondary-nav-right-sidebar' => __( 'Right Sidebar', 'aureon-studio' ),
					'' => __( 'No Navigation', 'aureon-studio' ),
				),
				'settings' => 'aureon_secondary_nav_settings[secondary_nav_position_setting]',
				'priority' => 30,
			)
		);

		$wp_customize->add_setting(
			'aureon_secondary_nav_settings[secondary_nav_dropdown_direction]',
			array(
				'default' => $defaults['secondary_nav_dropdown_direction'],
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_choices',
			)
		);

		$wp_customize->add_control(
			'aureon_secondary_nav_settings[secondary_nav_dropdown_direction]',
			array(
				'type' => 'select',
				'label' => __( 'Dropdown Direction', 'aureon-studio' ),
				'section' => 'secondary_nav_section',
				'choices' => array(
					'right' => __( 'Right', 'aureon-studio' ),
					'left' => __( 'Left', 'aureon-studio' ),
				),
				'settings' => 'aureon_secondary_nav_settings[secondary_nav_dropdown_direction]',
				'priority' => 35,
			)
		);

		$wp_customize->add_setting(
			'aureon_secondary_nav_settings[merge_top_bar]',
			array(
				'default' => $defaults['merge_top_bar'],
				'type' => 'option',
				'sanitize_callback' => 'aureon_premium_sanitize_checkbox',
			)
		);

		$wp_customize->add_control(
			'aureon_secondary_nav_settings[merge_top_bar]',
			array(
				'type' => 'checkbox',
				'label' => __( 'Merge with Secondary Navigation', 'aureon-studio' ),
				'section' => 'aureon_top_bar',
				'settings' => 'aureon_secondary_nav_settings[merge_top_bar]',
				'priority' => 100,
				'active_callback' => 'aureon_secondary_nav_show_merge_top_bar',
			)
		);
	}
}

if ( ! function_exists( 'aureon_display_secondary_google_fonts' ) ) {
	add_filter( 'aureon_typography_google_fonts', 'aureon_display_secondary_google_fonts', 50 );
	/**
	 * Add Google Fonts to wp_head if needed.
	 *
	 * @since 0.1
	 * @param array $google_fonts Existing fonts.
	 */
	function aureon_display_secondary_google_fonts( $google_fonts ) {
		if ( ! has_nav_menu( 'secondary' ) ) {
			return $google_fonts;
		}

		$aureon_secondary_nav_settings = wp_parse_args(
			get_option( 'aureon_secondary_nav_settings', array() ),
			aureon_secondary_nav_get_defaults()
		);

		if ( function_exists( 'aureon_typography_default_fonts' ) ) {
			$not_google = str_replace( ' ', '+', aureon_typography_default_fonts() );
		} else {
			$not_google = array(
				'inherit',
				'Arial,+Helvetica,+sans-serif',
				'Century+Gothic',
				'Comic+Sans+MS',
				'Courier+New',
				'Georgia,+Times+New+Roman,+Times,+serif',
				'Helvetica',
				'Impact',
				'Lucida+Console',
				'Lucida+Sans+Unicode',
				'Palatino+Linotype',
				'Tahoma,+Geneva,+sans-serif',
				'Trebuchet+MS,+Helvetica,+sans-serif',
				'Verdana,+Geneva,+sans-serif',
			);
		}

		$secondary_google_fonts = array();

		if ( function_exists( 'aureon_get_google_font_variants' ) ) {

			// If our value is still using the old format, fix it.
			if ( strpos( $aureon_secondary_nav_settings['font_secondary_navigation'], ':' ) !== false ) {
				$aureon_secondary_nav_settings['font_secondary_navigation'] = current( explode( ':', $aureon_secondary_nav_settings['font_secondary_navigation'] ) );
			}

			// Grab the variants using the plain name.
			$variants = aureon_get_google_font_variants( $aureon_secondary_nav_settings['font_secondary_navigation'], 'font_secondary_navigation', aureon_secondary_nav_get_defaults() );

		} else {
			$variants = '';
		}

		// Replace the spaces in the names with a plus.
		$value = str_replace( ' ', '+', $aureon_secondary_nav_settings['font_secondary_navigation'] );

		// If we have variants, add them to our value.
		$value = ! empty( $variants ) ? $value . ':' . $variants : $value;

		// Add our value to the array.
		$secondary_google_fonts[] = $value;

		// Ignore any non-Google fonts.
		$secondary_google_fonts = array_diff( $secondary_google_fonts, $not_google );

		// Separate each different font with a bar.
		$secondary_google_fonts = implode( '|', $secondary_google_fonts );

		if ( ! empty( $secondary_google_fonts ) ) {
			$print_secondary_fonts = '|' . $secondary_google_fonts;
		} else {
			$print_secondary_fonts = '';
		}

		// Remove any duplicates.
		$return = $google_fonts . $print_secondary_fonts;
		$return = implode( '|', array_unique( explode( '|', $return ) ) );
		return $return;

	}
}

if ( ! function_exists( 'aureon_add_secondary_navigation_after_header' ) ) {
	add_action( 'aureon_after_header', 'aureon_add_secondary_navigation_after_header', 7 );
	/**
	 * Add the navigation after the header.
	 *
	 * @since 0.1
	 */
	function aureon_add_secondary_navigation_after_header() {
		$aureon_settings = wp_parse_args(
			get_option( 'aureon_secondary_nav_settings', array() ),
			aureon_secondary_nav_get_defaults()
		);

		if ( 'secondary-nav-below-header' === $aureon_settings['secondary_nav_position_setting'] ) {
			aureon_secondary_navigation_position();
		}

	}
}

if ( ! function_exists( 'aureon_add_secondary_navigation_before_header' ) ) {
	add_action( 'aureon_before_header', 'aureon_add_secondary_navigation_before_header', 7 );
	/**
	 * Add the navigation before the header.
	 *
	 * @since 0.1
	 */
	function aureon_add_secondary_navigation_before_header() {
		$aureon_settings = wp_parse_args(
			get_option( 'aureon_secondary_nav_settings', array() ),
			aureon_secondary_nav_get_defaults()
		);

		if ( 'secondary-nav-above-header' === $aureon_settings['secondary_nav_position_setting'] ) {
			aureon_secondary_navigation_position();
		}

	}
}

if ( ! function_exists( 'aureon_add_secondary_navigation_float_right' ) ) {
	add_action( 'aureon_before_header_content', 'aureon_add_secondary_navigation_float_right', 7 );
	/**
	 * Add the navigation inside the header so it can float right.
	 *
	 * @since 0.1
	 */
	function aureon_add_secondary_navigation_float_right() {
		if ( function_exists( 'aureon_is_using_flexbox' ) && aureon_is_using_flexbox() ) {
			return;
		}

		$aureon_settings = wp_parse_args(
			get_option( 'aureon_secondary_nav_settings', array() ),
			aureon_secondary_nav_get_defaults()
		);

		if ( 'secondary-nav-float-right' === $aureon_settings['secondary_nav_position_setting'] || 'secondary-nav-float-left' === $aureon_settings['secondary_nav_position_setting'] ) {
			aureon_secondary_navigation_position();
		}

	}
}

add_action( 'aureon_after_header_content', 'aureon_do_secondary_navigation_float_right', 7 );
/**
 * Add the navigation inside the header so it can float right.
 *
 * @since 1.11.0
 */
function aureon_do_secondary_navigation_float_right() {
	if ( ! function_exists( 'aureon_is_using_flexbox' ) ) {
		return;
	}

	if ( ! aureon_is_using_flexbox() ) {
		return;
	}

	$aureon_settings = wp_parse_args(
		get_option( 'aureon_secondary_nav_settings', array() ),
		aureon_secondary_nav_get_defaults()
	);

	if ( 'secondary-nav-float-right' === $aureon_settings['secondary_nav_position_setting'] || 'secondary-nav-float-left' === $aureon_settings['secondary_nav_position_setting'] ) {
		aureon_secondary_navigation_position();
	}

}

add_action( 'aureon_before_navigation', 'aureon_do_multi_navigation_wrapper_open', 11 );
/**
 * Open our wrapper that puts both navigations inside one element.
 *
 * @since 1.11.0
 */
function aureon_do_multi_navigation_wrapper_open() {
	if ( ! function_exists( 'aureon_is_using_flexbox' ) ) {
		return;
	}

	if ( ! aureon_is_using_flexbox() ) {
		return;
	}

	if ( ! function_exists( 'aureon_get_option' ) ) {
		return;
	}

	if ( ! has_nav_menu( 'secondary' ) ) {
		return;
	}

	if ( function_exists( 'aureon_menu_plus_get_defaults' ) ) {
		$menu_settings = wp_parse_args(
			get_option( 'aureon_menu_plus_settings', array() ),
			aureon_menu_plus_get_defaults()
		);

		if ( $menu_settings['navigation_as_header'] ) {
			return;
		}
	}

	$aureon_settings = wp_parse_args(
		get_option( 'aureon_secondary_nav_settings', array() ),
		aureon_secondary_nav_get_defaults()
	);

	if (
		( 'secondary-nav-float-right' === $aureon_settings['secondary_nav_position_setting'] && 'nav-float-right' === aureon_get_option( 'nav_position_setting' ) ) ||
		( 'secondary-nav-float-left' === $aureon_settings['secondary_nav_position_setting'] && 'nav-float-left' === aureon_get_option( 'nav_position_setting' ) )
	) {
		echo '<div class="multi-navigation-wrapper">';
	}
}

add_action( 'aureon_after_secondary_navigation', 'aureon_do_multi_navigation_wrapper_close', 7 );
/**
 * Close our wrapper that puts both navigations inside one element.
 *
 * @since 1.11.0
 */
function aureon_do_multi_navigation_wrapper_close() {
	if ( ! function_exists( 'aureon_is_using_flexbox' ) ) {
		return;
	}

	if ( ! aureon_is_using_flexbox() ) {
		return;
	}

	if ( ! function_exists( 'aureon_get_option' ) ) {
		return;
	}

	$aureon_settings = wp_parse_args(
		get_option( 'aureon_secondary_nav_settings', array() ),
		aureon_secondary_nav_get_defaults()
	);

	if (
		( 'secondary-nav-float-right' === $aureon_settings['secondary_nav_position_setting'] && 'nav-float-right' === aureon_get_option( 'nav_position_setting' ) ) ||
		( 'secondary-nav-float-left' === $aureon_settings['secondary_nav_position_setting'] && 'nav-float-left' === aureon_get_option( 'nav_position_setting' ) )
	) {
		echo '</div>';
	}
}

if ( ! function_exists( 'aureon_add_secondary_navigation_before_right_sidebar' ) ) {
	add_action( 'aureon_before_right_sidebar_content', 'aureon_add_secondary_navigation_before_right_sidebar', 7 );
	/**
	 * Add the navigation into the right sidebar.
	 *
	 * @since 0.1
	 */
	function aureon_add_secondary_navigation_before_right_sidebar() {
		$aureon_settings = wp_parse_args(
			get_option( 'aureon_secondary_nav_settings', array() ),
			aureon_secondary_nav_get_defaults()
		);

		if ( 'secondary-nav-right-sidebar' === $aureon_settings['secondary_nav_position_setting'] ) {
			echo '<div class="gen-sidebar-secondary-nav">';
				aureon_secondary_navigation_position();
			echo '</div><!-- .gen-sidebar-secondary-nav -->';
		}

	}
}

if ( ! function_exists( 'aureon_add_secondary_navigation_before_left_sidebar' ) ) {
	add_action( 'aureon_before_left_sidebar_content', 'aureon_add_secondary_navigation_before_left_sidebar', 7 );
	/**
	 * Add the navigation into the left sidebar.
	 *
	 * @since 0.1
	 */
	function aureon_add_secondary_navigation_before_left_sidebar() {
		$aureon_settings = wp_parse_args(
			get_option( 'aureon_secondary_nav_settings', array() ),
			aureon_secondary_nav_get_defaults()
		);

		if ( 'secondary-nav-left-sidebar' === $aureon_settings['secondary_nav_position_setting'] ) {
			echo '<div class="gen-sidebar-secondary-nav">';
				aureon_secondary_navigation_position();
			echo '</div><!-- .gen-sidebar-secondary-nav -->';
		}

	}
}

if ( ! function_exists( 'aureon_secondary_navigation_position' ) ) {
	/**
	 * Build our secondary navigation.
	 * Would like to change this function name.
	 *
	 * @since 0.1
	 */
	function aureon_secondary_navigation_position() {
		$aureon_settings = wp_parse_args(
			get_option( 'aureon_secondary_nav_settings', array() ),
			aureon_secondary_nav_get_defaults()
		);
		if ( has_nav_menu( 'secondary' ) ) :
			do_action( 'aureon_before_secondary_navigation' );

			$classes    = aureon_get_secondary_navigation_class();
			$classes    = implode( ' ', $classes );
			$attributes = [
				'id'         => 'secondary-navigation',
				'aria-label' => esc_attr__( 'Secondary', 'aureon-studio' ),
				'class'      => $classes,
			];
			$microdata  = '';

			if ( function_exists( 'aureon_get_schema_type' ) && 'microdata' === aureon_get_schema_type() ) {
				$attributes['itemtype']  = 'https://schema.org/SiteNavigationElement';
				$attributes['itemscope'] = true;
				$microdata               = ' itemtype="https://schema.org/SiteNavigationElement" itemscope="itemscope"';
			}

			$secondary_nav_attributes = sprintf(
				'id="secondary-navigation" aria-label="%1$s" class="%2$s"%3$s"',
				esc_attr__( 'Secondary', 'aureon-studio' ),
				esc_attr( $classes ),
				$microdata
			);

			if ( function_exists( 'aureon_get_attr' ) ) {
				$secondary_nav_attributes = aureon_get_attr(
					'secondary-navigation',
					$attributes
				);
			}
			?>
			<nav <?php echo $secondary_nav_attributes; // phpcs:ignore -- No escaping needed. ?>>
				<div <?php aureon_inside_secondary_navigation_class(); ?>>
					<?php do_action( 'aureon_inside_secondary_navigation' ); ?>
					<button class="menu-toggle secondary-menu-toggle">
						<?php
						do_action( 'aureon_inside_secondary_mobile_menu' );

						if ( function_exists( 'aureon_do_svg_icon' ) ) {
							aureon_do_svg_icon( 'menu-bars', true );
						}

						$mobile_menu_label = $aureon_settings['secondary_nav_mobile_label'];

						if ( $mobile_menu_label ) {
							printf(
								'<span class="mobile-menu">%s</span>',
								$mobile_menu_label // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML allowed in filter.
							);
						} else {
							printf(
								'<span class="screen-reader-text">%s</span>',
								esc_html__( 'Menu', 'aureon-studio' )
							);
						}
						?>
					</button>
					<?php
					/**
					 * aureon_after_mobile_menu_button hook
					 *
					 * @since 1.11.0
					 */
					do_action( 'aureon_after_secondary_mobile_menu_button' );

					wp_nav_menu(
						array(
							'theme_location' => 'secondary',
							'container' => 'div',
							'container_class' => 'main-nav',
							'menu_class' => '',
							'fallback_cb' => 'aureon_secondary_menu_fallback',
							'items_wrap' => '<ul id="%1$s" class="%2$s ' . join( ' ', aureon_get_secondary_menu_class() ) . '">%3$s</ul>',
						)
					);

					/**
					 * aureon_after_secondary_menu hook.
					 *
					 * @since 1.11.0
					 */
					do_action( 'aureon_after_secondary_menu' );
					?>
				</div><!-- .inside-navigation -->
			</nav><!-- #secondary-navigation -->
			<?php
			do_action( 'aureon_after_secondary_navigation' );
		endif;
	}
}

if ( ! function_exists( 'aureon_secondary_menu_fallback' ) ) {
	/**
	 * Menu fallback.
	 *
	 * @param array $args Menu args.
	 * @since 1.1.4
	 */
	function aureon_secondary_menu_fallback( $args ) {
		?>
		<div class="main-nav">
			<ul <?php aureon_secondary_menu_class(); ?>>
				<?php wp_list_pages( 'sort_column=menu_order&title_li=' ); ?>
			</ul>
		</div><!-- .main-nav -->
		<?php
	}
}

add_action( 'aureon_after_secondary_menu', 'aureon_do_secondary_menu_bar_item_container' );
/**
 * Add a container for menu bar items.
 *
 * @since 1.11.0
 */
function aureon_do_secondary_menu_bar_item_container() {
	if ( function_exists( 'aureon_is_using_flexbox' ) && aureon_is_using_flexbox() ) {
		if ( aureon_secondary_nav_has_menu_bar_items() ) {
			echo '<div class="secondary-menu-bar-items">';
				do_action( 'aureon_secondary_menu_bar_items' );
			echo '</div>';
		}
	}
}

if ( ! function_exists( 'aureon_secondary_nav_body_classes' ) ) {
	add_filter( 'body_class', 'aureon_secondary_nav_body_classes' );
	/**
	 * Adds custom classes to the array of body classes.
	 *
	 * @since 0.1
	 * @param array $classes Existing body classes.
	 */
	function aureon_secondary_nav_body_classes( $classes ) {
		if ( ! has_nav_menu( 'secondary' ) ) {
			return $classes;
		}

		$aureon_settings = wp_parse_args(
			get_option( 'aureon_secondary_nav_settings', array() ),
			aureon_secondary_nav_get_defaults()
		);

		$classes[] = ( $aureon_settings['secondary_nav_position_setting'] ) ? $aureon_settings['secondary_nav_position_setting'] : 'secondary-nav-below-header';

		if ( 'left' === $aureon_settings['secondary_nav_alignment'] ) {
			$classes[] = 'secondary-nav-aligned-left';
		} elseif ( 'center' === $aureon_settings['secondary_nav_alignment'] ) {
			$classes[] = 'secondary-nav-aligned-center';
		} elseif ( 'right' === $aureon_settings['secondary_nav_alignment'] ) {
			$classes[] = 'secondary-nav-aligned-right';
		} else {
			$classes[] = 'secondary-nav-aligned-left';
		}

		return $classes;
	}
}

if ( ! function_exists( 'aureon_secondary_menu_classes' ) ) {
	add_filter( 'aureon_secondary_menu_class', 'aureon_secondary_menu_classes' );
	/**
	 * Adds custom classes to the menu.
	 *
	 * @since 0.1
	 * @param array $classes Existing classes.
	 */
	function aureon_secondary_menu_classes( $classes ) {

		$classes[] = 'secondary-menu';
		$classes[] = 'sf-menu';

		return $classes;

	}
}

if ( ! function_exists( 'aureon_secondary_navigation_classes' ) ) {
	add_filter( 'aureon_secondary_navigation_class', 'aureon_secondary_navigation_classes' );
	/**
	 * Adds custom classes to the navigation.
	 *
	 * @since 0.1
	 * @param array $classes Existing classes.
	 */
	function aureon_secondary_navigation_classes( $classes ) {
		$classes[] = 'secondary-navigation';

		$aureon_settings = wp_parse_args(
			get_option( 'aureon_secondary_nav_settings', array() ),
			aureon_secondary_nav_get_defaults()
		);

		$nav_layout = $aureon_settings['secondary_nav_layout_setting'];

		if ( 'secondary-contained-nav' === $nav_layout ) {
			if ( function_exists( 'aureon_is_using_flexbox' ) && aureon_is_using_flexbox() ) {
				$navigation_location = $aureon_settings['secondary_nav_position_setting'];

				if ( 'secondary-nav-float-right' !== $navigation_location && 'secondary-nav-float-left' !== $navigation_location ) {
					$classes[] = 'grid-container';
				}
			} else {
				$classes[] = 'grid-container';
				$classes[] = 'grid-parent';
			}
		}

		if ( 'left' === $aureon_settings['secondary_nav_dropdown_direction'] ) {
			$layout = $aureon_settings['secondary_nav_position_setting'];

			switch ( $layout ) {
				case 'secondary-nav-below-header':
				case 'secondary-nav-above-header':
				case 'secondary-nav-float-right':
				case 'secondary-nav-float-left':
					$classes[] = 'sub-menu-left';
					break;
			}
		}

		if ( $aureon_settings['merge_top_bar'] && is_active_sidebar( 'top-bar' ) ) {
			$classes[] = 'has-top-bar';
		}

		if ( aureon_secondary_nav_has_menu_bar_items() ) {
			$classes[] = 'has-menu-bar-items';
		}

		return $classes;

	}
}

if ( ! function_exists( 'aureon_inside_secondary_navigation_classes' ) ) {
	add_filter( 'aureon_inside_secondary_navigation_class', 'aureon_inside_secondary_navigation_classes' );
	/**
	 * Adds custom classes to the inner navigation
	 *
	 * @since 1.3.41
	 * @param array $classes Existing classes.
	 */
	function aureon_inside_secondary_navigation_classes( $classes ) {
		$classes[] = 'inside-navigation';

		$aureon_settings = wp_parse_args(
			get_option( 'aureon_secondary_nav_settings', array() ),
			aureon_secondary_nav_get_defaults()
		);

		$inner_nav_width = $aureon_settings['secondary_nav_inner_width'];

		if ( 'full-width' !== $inner_nav_width ) {
			$classes[] = 'grid-container';
			$classes[] = 'grid-parent';
		}

		return $classes;

	}
}

if ( ! function_exists( 'aureon_secondary_nav_css' ) ) {
	/**
	 * Generate the CSS in the <head> section using the Theme Customizer.
	 *
	 * @since 0.1
	 */
	function aureon_secondary_nav_css() {

		$aureon_settings = wp_parse_args(
			get_option( 'aureon_secondary_nav_settings', array() ),
			aureon_secondary_nav_get_defaults()
		);

		if ( function_exists( 'aureon_spacing_get_defaults' ) ) {
			$spacing_settings = wp_parse_args(
				get_option( 'aureon_spacing_settings', array() ),
				aureon_spacing_get_defaults()
			);
			$separator = $spacing_settings['separator'];
		} else {
			$separator = 20;
		}

		// Check if we're using our legacy typography system.
		$using_dynamic_typography = function_exists( 'aureon_is_using_dynamic_typography' ) && aureon_is_using_dynamic_typography();
		$secondary_nav_family = '';

		if ( ! $using_dynamic_typography ) {
			if ( function_exists( 'aureon_get_font_family_css' ) ) {
				$secondary_nav_family = aureon_get_font_family_css( 'font_secondary_navigation', 'aureon_secondary_nav_settings', aureon_secondary_nav_get_defaults() );
			} else {
				$secondary_nav_family = current( explode( ':', $aureon_settings['font_secondary_navigation'] ) );
			}

			if ( '""' === $secondary_nav_family ) {
				$secondary_nav_family = 'inherit';
			}
		}

		// Get our untouched defaults.
		$og_defaults = aureon_secondary_nav_get_defaults( false );

		$css = new Aureon_Secondary_Nav_CSS();

		$css->set_selector( '.secondary-navigation' );
		$css->add_property( 'background-color', esc_attr( $aureon_settings['navigation_background_color'] ) );
		$css->add_property( 'background-image', ! empty( $aureon_settings['nav_image'] ) ? 'url(' . esc_url( $aureon_settings['nav_image'] ) . ')' : '' );
		$css->add_property( 'background-repeat', esc_attr( $aureon_settings['nav_repeat'] ) );

		if ( function_exists( 'aureon_is_using_flexbox' ) && aureon_is_using_flexbox() ) {
			if ( function_exists( 'aureon_spacing_get_defaults' ) && function_exists( 'aureon_get_option' ) && 'text' === aureon_get_option( 'container_alignment' ) ) {
				$spacing_settings = wp_parse_args(
					get_option( 'aureon_spacing_settings', array() ),
					aureon_spacing_get_defaults()
				);

				$navigation_left_padding = absint( $spacing_settings['header_left'] ) - absint( $aureon_settings['secondary_menu_item'] );
				$navigation_right_padding = absint( $spacing_settings['header_right'] ) - absint( $aureon_settings['secondary_menu_item'] );

				$css->set_selector( '.secondary-nav-below-header .secondary-navigation .inside-navigation.grid-container, .secondary-nav-above-header .secondary-navigation .inside-navigation.grid-container' );
				$css->add_property( 'padding', aureon_padding_css( 0, $navigation_right_padding, 0, $navigation_left_padding ) );
			}
		}

		if ( 'secondary-nav-above-header' === $aureon_settings['secondary_nav_position_setting'] && has_nav_menu( 'secondary' ) && is_active_sidebar( 'top-bar' ) ) {
			$css->set_selector( '.secondary-navigation .top-bar' );
			$css->add_property( 'color', esc_attr( $aureon_settings['navigation_text_color'] ) );
			$css->add_property( 'line-height', absint( $aureon_settings['secondary_menu_item_height'] ), false, 'px' );

			if ( ! $using_dynamic_typography ) {
				$css->add_property( 'font-family', $secondary_nav_family );
				$css->add_property( 'font-weight', esc_attr( $aureon_settings['secondary_navigation_font_weight'] ) );
				$css->add_property( 'text-transform', esc_attr( $aureon_settings['secondary_navigation_font_transform'] ) );
				$css->add_property( 'font-size', absint( $aureon_settings['secondary_navigation_font_size'] ), false, 'px' );
			}

			$css->set_selector( '.secondary-navigation .top-bar a' );
			$css->add_property( 'color', esc_attr( $aureon_settings['navigation_text_color'] ) );

			$css->set_selector( '.secondary-navigation .top-bar a:hover, .secondary-navigation .top-bar a:focus' );
			$css->add_property( 'color', esc_attr( $aureon_settings['navigation_background_hover_color'] ) );
		}

		// Navigation text.
		$css->set_selector( '.secondary-navigation .main-nav ul li a,.secondary-navigation .menu-toggle,.secondary-menu-bar-items .menu-bar-item > a' );
		$css->add_property( 'color', esc_attr( $aureon_settings['navigation_text_color'] ) );

		if ( ! $using_dynamic_typography ) {
			$css->add_property( 'font-family', ( 'inherit' !== $secondary_nav_family ) ? $secondary_nav_family : null );
			$css->add_property( 'font-weight', esc_attr( $aureon_settings['secondary_navigation_font_weight'] ), $og_defaults['secondary_navigation_font_weight'] );
			$css->add_property( 'text-transform', esc_attr( $aureon_settings['secondary_navigation_font_transform'] ), $og_defaults['secondary_navigation_font_transform'] );
			$css->add_property( 'font-size', absint( $aureon_settings['secondary_navigation_font_size'] ), $og_defaults['secondary_navigation_font_size'], 'px' );
		}

		$css->add_property( 'padding-left', absint( $aureon_settings['secondary_menu_item'] ), $og_defaults['secondary_menu_item'], 'px' );
		$css->add_property( 'padding-right', absint( $aureon_settings['secondary_menu_item'] ), $og_defaults['secondary_menu_item'], 'px' );
		$css->add_property( 'line-height', absint( $aureon_settings['secondary_menu_item_height'] ), $og_defaults['secondary_menu_item_height'], 'px' );
		$css->add_property( 'background-image', ! empty( $aureon_settings['nav_item_image'] ) ? 'url(' . esc_url( $aureon_settings['nav_item_image'] ) . ')' : '' );
		$css->add_property( 'background-repeat', esc_attr( $aureon_settings['nav_item_repeat'] ) );

		$css->set_selector( '.secondary-navigation .secondary-menu-bar-items' );
		$css->add_property( 'color', $aureon_settings['navigation_text_color'] );

		if ( ! $using_dynamic_typography ) {
			$css->add_property( 'font-size', absint( $aureon_settings['secondary_navigation_font_size'] ), $og_defaults['secondary_navigation_font_size'], 'px' );
		}

		// Mobile menu text on hover.
		$css->set_selector( 'button.secondary-menu-toggle:hover,button.secondary-menu-toggle:focus' );
		$css->add_property( 'color', esc_attr( $aureon_settings['navigation_text_color'] ) );

		// Widget area navigation.
		$css->set_selector( '.widget-area .secondary-navigation' );
		$css->add_property( 'margin-bottom', absint( $separator ), false, 'px' );

		// Sub-navigation background.
		$css->set_selector( '.secondary-navigation ul ul' );
		$css->add_property( 'background-color', esc_attr( $aureon_settings['subnavigation_background_color'] ) );
		$css->add_property( 'top', 'auto' ); // Added for compatibility purposes on 22/12/2016.

		// Sub-navigation text.
		$css->set_selector( '.secondary-navigation .main-nav ul ul li a' );
		$css->add_property( 'color', esc_attr( $aureon_settings['subnavigation_text_color'] ) );

		if ( ! $using_dynamic_typography ) {
			$css->add_property( 'font-size', absint( $aureon_settings['secondary_navigation_font_size'] - 1 ), absint( $og_defaults['secondary_navigation_font_size'] - 1 ), 'px' );
		}

		$css->add_property( 'padding-left', absint( $aureon_settings['secondary_menu_item'] ), $og_defaults['secondary_menu_item'], 'px' );
		$css->add_property( 'padding-right', absint( $aureon_settings['secondary_menu_item'] ), $og_defaults['secondary_menu_item'], 'px' );
		$css->add_property( 'padding-top', absint( $aureon_settings['secondary_sub_menu_item_height'] ), $og_defaults['secondary_sub_menu_item_height'], 'px' );
		$css->add_property( 'padding-bottom', absint( $aureon_settings['secondary_sub_menu_item_height'] ), $og_defaults['secondary_sub_menu_item_height'], 'px' );
		$css->add_property( 'background-image', ! empty( $aureon_settings['sub_nav_item_image'] ) ? 'url(' . esc_url( $aureon_settings['sub_nav_item_image'] ) . ')' : '' );
		$css->add_property( 'background-repeat', esc_attr( $aureon_settings['sub_nav_item_repeat'] ) );

		// Menu item padding on RTL.
		if ( is_rtl() ) {
			$css->set_selector( 'nav.secondary-navigation .main-nav ul li.menu-item-has-children > a' );
			$css->add_property( 'padding-right', absint( $aureon_settings['secondary_menu_item'] ), $og_defaults['secondary_menu_item'], 'px' );
		}

		// Dropdown arrow.
		$css->set_selector( '.secondary-navigation .menu-item-has-children ul .dropdown-menu-toggle' );
		$css->add_property( 'padding-top', absint( $aureon_settings['secondary_sub_menu_item_height'] ), $og_defaults['secondary_sub_menu_item_height'], 'px' );
		$css->add_property( 'padding-bottom', absint( $aureon_settings['secondary_sub_menu_item_height'] ), $og_defaults['secondary_sub_menu_item_height'], 'px' );
		$css->add_property( 'margin-top', '-' . absint( $aureon_settings['secondary_sub_menu_item_height'] ), '-' . absint( $og_defaults['secondary_sub_menu_item_height'] ), 'px' );

		// Dropdown arrow.
		$css->set_selector( '.secondary-navigation .menu-item-has-children .dropdown-menu-toggle' );
		$css->add_property( 'padding-right', absint( $aureon_settings['secondary_menu_item'] ), $og_defaults['secondary_menu_item'], 'px' );

		// Sub-navigation dropdown arrow.
		$css->set_selector( '.secondary-navigation .menu-item-has-children ul .dropdown-menu-toggle' );
		$css->add_property( 'padding-top', absint( $aureon_settings['secondary_sub_menu_item_height'] ), $og_defaults['secondary_sub_menu_item_height'], 'px' );
		$css->add_property( 'padding-bottom', absint( $aureon_settings['secondary_sub_menu_item_height'] ), $og_defaults['secondary_sub_menu_item_height'], 'px' );
		$css->add_property( 'margin-top', '-' . absint( $aureon_settings['secondary_sub_menu_item_height'] ), '-' . absint( $og_defaults['secondary_sub_menu_item_height'] ), 'px' );

		// Navigation background/text on hover.
		$css->set_selector( '.secondary-navigation .main-nav ul li:not([class*="current-menu-"]):hover > a, .secondary-navigation .main-nav ul li:not([class*="current-menu-"]):focus > a, .secondary-navigation .main-nav ul li.sfHover:not([class*="current-menu-"]) > a, .secondary-menu-bar-items .menu-bar-item:hover > a' );
		$css->add_property( 'color', esc_attr( $aureon_settings['navigation_text_hover_color'] ) );
		$css->add_property( 'background-color', esc_attr( $aureon_settings['navigation_background_hover_color'] ) );
		$css->add_property( 'background-image', ! empty( $aureon_settings['nav_item_hover_image'] ) ? 'url(' . esc_url( $aureon_settings['nav_item_hover_image'] ) . ')' : '' );
		$css->add_property( 'background-repeat', esc_attr( $aureon_settings['nav_item_hover_repeat'] ) );

		// Sub-Navigation background/text on hover.
		$css->set_selector( '.secondary-navigation .main-nav ul ul li:not([class*="current-menu-"]):hover > a,.secondary-navigation .main-nav ul ul li:not([class*="current-menu-"]):focus > a,.secondary-navigation .main-nav ul ul li.sfHover:not([class*="current-menu-"]) > a' );
		$css->add_property( 'color', esc_attr( $aureon_settings['subnavigation_text_hover_color'] ) );
		$css->add_property( 'background-color', esc_attr( $aureon_settings['subnavigation_background_hover_color'] ) );
		$css->add_property( 'background-image', ! empty( $aureon_settings['sub_nav_item_hover_image'] ) ? 'url(' . esc_url( $aureon_settings['sub_nav_item_hover_image'] ) . ')' : '' );
		$css->add_property( 'background-repeat', esc_attr( $aureon_settings['sub_nav_item_hover_repeat'] ) );

		// Navigation background / text current + hover.
		$css->set_selector( '.secondary-navigation .main-nav ul li[class*="current-menu-"] > a' );
		$css->add_property( 'color', esc_attr( $aureon_settings['navigation_text_current_color'] ) );
		$css->add_property( 'background-color', esc_attr( $aureon_settings['navigation_background_current_color'] ) );
		$css->add_property( 'background-image', ! empty( $aureon_settings['nav_item_current_image'] ) ? 'url(' . esc_url( $aureon_settings['nav_item_current_image'] ) . ')' : '' );
		$css->add_property( 'background-repeat', esc_attr( $aureon_settings['nav_item_current_repeat'] ) );

		// Sub-Navigation background / text current + hover.
		$css->set_selector( '.secondary-navigation .main-nav ul ul li[class*="current-menu-"] > a' );
		$css->add_property( 'color', esc_attr( $aureon_settings['subnavigation_text_current_color'] ) );
		$css->add_property( 'background-color', esc_attr( $aureon_settings['subnavigation_background_current_color'] ) );
		$css->add_property( 'background-image', ! empty( $aureon_settings['sub_nav_item_current_image'] ) ? 'url(' . esc_url( $aureon_settings['sub_nav_item_current_image'] ) . ')' : '' );
		$css->add_property( 'background-repeat', esc_attr( $aureon_settings['sub_nav_item_current_repeat'] ) );

		// RTL menu item padding.
		if ( is_rtl() ) {
			$css->set_selector( '.secondary-navigation .main-nav ul li.menu-item-has-children > a' );
			$css->add_property( 'padding-right', absint( $aureon_settings['secondary_menu_item'] ), false, 'px' );
		}

		if ( function_exists( 'aureon_get_option' ) && function_exists( 'aureon_get_defaults' ) ) {
			$theme_defaults = aureon_get_defaults();

			if ( isset( $theme_defaults['icons'] ) ) {
				if ( 'svg' === aureon_get_option( 'icons' ) ) {
					$css->set_selector( '.secondary-navigation.toggled .dropdown-menu-toggle:before' );
					$css->add_property( 'display', 'none' );
				}
			}
		}

		$mobile_css = '@media ' . aureon_premium_get_media_query( 'mobile-menu' ) . ' {.secondary-menu-bar-items .menu-bar-item:hover > a{background: none;color: ' . $aureon_settings['navigation_text_color'] . ';}}';

		// Return our dynamic CSS.
		return $css->css_output() . $mobile_css;
	}
}

if ( ! function_exists( 'aureon_secondary_color_scripts' ) ) {
	add_action( 'wp_enqueue_scripts', 'aureon_secondary_color_scripts', 110 );
	/**
	 * Enqueue scripts and styles
	 */
	function aureon_secondary_color_scripts() {
		// Bail if no Secondary menu is set.
		if ( ! has_nav_menu( 'secondary' ) ) {
			return;
		}

		wp_add_inline_style( 'aureon-secondary-nav', aureon_secondary_nav_css() );

		if ( class_exists( 'Aureon_Typography' ) ) {
			wp_add_inline_style( 'aureon-secondary-nav', Aureon_Typography::get_css( 'secondary-nav' ) );
		}
	}
}

if ( ! function_exists( 'aureon_secondary_navigation_class' ) ) {
	/**
	 * Display the classes for the secondary navigation.
	 *
	 * @since 0.1
	 * @param string|array $class One or more classes to add to the class list.
	 */
	function aureon_secondary_navigation_class( $class = '' ) {
		// Separates classes with a single space, collates classes for post DIV.
		echo 'class="' . join( ' ', aureon_get_secondary_navigation_class( $class ) ) . '"'; // phpcs:ignore -- Escaped in aureon_get_secondary_navigation_class.
	}
}

if ( ! function_exists( 'aureon_get_secondary_navigation_class' ) ) {
	/**
	 * Retrieve the classes for the secondary navigation.
	 *
	 * @since 0.1
	 * @param string|array $class One or more classes to add to the class list.
	 * @return array Array of classes.
	 */
	function aureon_get_secondary_navigation_class( $class = '' ) {
		$classes = array();

		if ( ! empty( $class ) ) {
			if ( ! is_array( $class ) ) {
				$class = preg_split( '#\s+#', $class );
			}

			$classes = array_merge( $classes, $class );
		}

		$classes = array_map( 'esc_attr', $classes );

		return apply_filters( 'aureon_secondary_navigation_class', $classes, $class );
	}
}

if ( ! function_exists( 'aureon_secondary_menu_class' ) ) {
	/**
	 * Display the classes for the secondary navigation.
	 *
	 * @since 0.1
	 * @param string|array $class One or more classes to add to the class list.
	 */
	function aureon_secondary_menu_class( $class = '' ) {
		// Separates classes with a single space, collates classes for post DIV.
		echo 'class="' . join( ' ', aureon_get_secondary_menu_class( $class ) ) . '"'; // phpcs:ignore -- Escaped in aureon_get_secondary_menu_class.
	}
}

if ( ! function_exists( 'aureon_get_secondary_menu_class' ) ) {
	/**
	 * Retrieve the classes for the secondary navigation.
	 *
	 * @since 0.1
	 * @param string|array $class One or more classes to add to the class list.
	 * @return array Array of classes.
	 */
	function aureon_get_secondary_menu_class( $class = '' ) {
		$classes = array();

		if ( ! empty( $class ) ) {
			if ( ! is_array( $class ) ) {
				$class = preg_split( '#\s+#', $class );
			}

			$classes = array_merge( $classes, $class );
		}

		$classes = array_map( 'esc_attr', $classes );

		return apply_filters( 'aureon_secondary_menu_class', $classes, $class );
	}
}

if ( ! function_exists( 'aureon_inside_secondary_navigation_class' ) ) {
	/**
	 * Display the classes for the inner navigation.
	 *
	 * @since 0.1
	 * @param string|array $class One or more classes to add to the class list.
	 */
	function aureon_inside_secondary_navigation_class( $class = '' ) {
		$classes = array();

		if ( ! empty( $class ) ) {
			if ( ! is_array( $class ) ) {
				$class = preg_split( '#\s+#', $class );
			}

			$classes = array_merge( $classes, $class );
		}

		$classes = array_map( 'esc_attr', $classes );

		$return = apply_filters( 'aureon_inside_secondary_navigation_class', $classes, $class );

		// Separates classes with a single space, collates classes for post DIV.
		echo 'class="' . join( ' ', $return ) . '"'; // phpcs:ignore -- Escaped above.
	}
}

if ( ! function_exists( 'aureon_secondary_nav_remove_top_bar' ) ) {
	add_action( 'wp', 'aureon_secondary_nav_remove_top_bar' );
	/**
	 * Remove the top bar and add it to the Secondary Navigation if it's set
	 */
	function aureon_secondary_nav_remove_top_bar() {
		$aureon_settings = wp_parse_args(
			get_option( 'aureon_secondary_nav_settings', array() ),
			aureon_secondary_nav_get_defaults()
		);

		if ( $aureon_settings['merge_top_bar'] && 'secondary-nav-above-header' === $aureon_settings['secondary_nav_position_setting'] && has_nav_menu( 'secondary' ) && is_active_sidebar( 'top-bar' ) ) {
			remove_action( 'aureon_before_header', 'aureon_top_bar', 5 );
			add_action( 'aureon_inside_secondary_navigation', 'aureon_secondary_nav_top_bar_widget', 5 );
			add_filter( 'aureon_is_top_bar_active', '__return_false' );
		}
	}
}

if ( ! function_exists( 'aureon_secondary_nav_top_bar_widget' ) ) {
	/**
	 * Build the top bar widget area
	 * This is placed into the secondary navigation if set
	 */
	function aureon_secondary_nav_top_bar_widget() {
		if ( ! is_active_sidebar( 'top-bar' ) ) {
			return;
		}
		?>
		<div class="top-bar">
			<div class="inside-top-bar">
				<?php dynamic_sidebar( 'top-bar' ); ?>
			</div>
		</div>
		<?php
	}
}

/**
 * Check if we have any menu bar items.
 */
function aureon_secondary_nav_has_menu_bar_items() {
	return has_action( 'aureon_secondary_menu_bar_items' );
}

add_filter( 'aureon_has_active_menu', 'aureon_secondary_nav_set_active_menu' );
/**
 * Tell GP about our active menus.
 *
 * @since 2.1.0
 * @param boolean $has_active_menu Whether we have an active menu.
 */
function aureon_secondary_nav_set_active_menu( $has_active_menu ) {
	if ( has_nav_menu( 'secondary' ) ) {
		return true;
	}

	return $has_active_menu;
}

add_filter( 'aureon_typography_css_selector', 'aureon_secondary_nav_typography_selectors' );
/**
 * Add the Secondary Nav typography CSS selectors.
 *
 * @since 2.1.0
 * @param string $selector The selector we're targeting.
 */
function aureon_secondary_nav_typography_selectors( $selector ) {
	switch ( $selector ) {
		case 'secondary-nav-menu-items':
			$selector = '.secondary-navigation .main-nav ul li a, .secondary-navigation .menu-toggle, .secondary-navigation .menu-bar-items';
			break;

		case 'secondary-nav-sub-menu-items':
			$selector = '.secondary-navigation .main-nav ul ul li a';
			break;

		case 'secondary-nav-menu-toggle':
			$selector = '.secondary-navigation .menu-toggle';
			break;
	}

	return $selector;
}
