<?php
/**
 * This file handles the smooth scroll functionality.
 *
 * @package Aureon Studio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

add_action( 'wp_enqueue_scripts', 'aureon_smooth_scroll_scripts' );
/**
 * Add the smooth scroll script if enabled.
 *
 * @since 1.6
 */
function aureon_smooth_scroll_scripts() {
	if ( ! function_exists( 'aureon_get_defaults' ) ) {
		return;
	}

	$settings = wp_parse_args(
		get_option( 'aureon_settings', array() ),
		aureon_get_defaults()
	);

	if ( ! $settings['smooth_scroll'] ) {
		return;
	}

	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

	wp_enqueue_script( 'aureon-smooth-scroll', plugin_dir_url( __FILE__ ) . "js/smooth-scroll{$suffix}.js", array(), AUREON_STUDIO_VERSION, true );

	wp_localize_script(
		'aureon-smooth-scroll',
		'aureonSmoothScroll',
		array(
			'elements' => apply_filters(
				'aureon_smooth_scroll_elements',
				array(
					'.smooth-scroll',
					'li.smooth-scroll a',
				)
			),
			'duration' => apply_filters( 'aureon_smooth_scroll_duration', 800 ),
			'offset' => apply_filters( 'aureon_smooth_scroll_offset', '' ),
		)
	);
}

add_filter( 'aureon_option_defaults', 'aureon_smooth_scroll_default' );
/**
 * Add the smooth scroll option to our defaults.
 *
 * @since 1.6
 *
 * @param array $defaults Existing defaults.
 * @return array New defaults.
 */
function aureon_smooth_scroll_default( $defaults ) {
	$defaults['smooth_scroll'] = false;

	return $defaults;
}

add_action( 'customize_register', 'aureon_smooth_scroll_customizer', 99 );
/**
 * Add our smooth scroll option to the Customizer.
 *
 * @since 1.6
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function aureon_smooth_scroll_customizer( $wp_customize ) {
	if ( ! function_exists( 'aureon_get_defaults' ) ) {
		return;
	}

	$defaults = aureon_get_defaults();

	require_once AUREON_LIBRARY_DIRECTORY . 'customizer-helpers.php';

	$wp_customize->add_setting(
		'aureon_settings[smooth_scroll]',
		array(
			'default' => $defaults['smooth_scroll'],
			'type' => 'option',
			'sanitize_callback' => 'aureon_premium_sanitize_checkbox',
		)
	);

	$wp_customize->add_control(
		'aureon_settings[smooth_scroll]',
		array(
			'type' => 'checkbox',
			'label' => __( 'Smooth scroll', 'aureon-studio' ),
			'description' => __( 'Initiate smooth scroll on anchor links using the <code>smooth-scroll</code> class.', 'aureon-studio' ),
			'section' => 'aureon_general_section',
		)
	);
}
