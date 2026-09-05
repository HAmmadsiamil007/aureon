<?php
/**
 * This file handles the top bar spacing Customizer options.
 *
 * @package Aureon Studio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

if ( isset( $defaults['top_bar_top'] ) ) {
	$wp_customize->add_setting(
		'aureon_spacing_settings[top_bar_top]',
		array(
			'default' => $defaults['top_bar_top'],
			'type' => 'option',
			'sanitize_callback' => 'absint',
			'transport' => 'postMessage',
		)
	);

	$wp_customize->add_setting(
		'aureon_spacing_settings[top_bar_right]',
		array(
			'default' => $defaults['top_bar_right'],
			'type' => 'option',
			'sanitize_callback' => 'absint',
			'transport' => 'postMessage',
		)
	);

	$wp_customize->add_setting(
		'aureon_spacing_settings[top_bar_bottom]',
		array(
			'default' => $defaults['top_bar_bottom'],
			'type' => 'option',
			'sanitize_callback' => 'absint',
			'transport' => 'postMessage',
		)
	);

	$wp_customize->add_setting(
		'aureon_spacing_settings[top_bar_left]',
		array(
			'default' => $defaults['top_bar_left'],
			'type' => 'option',
			'sanitize_callback' => 'absint',
			'transport' => 'postMessage',
		)
	);

	$wp_customize->add_control(
		new Aureon_Spacing_Control(
			$wp_customize,
			'top_bar_spacing',
			array(
				'type'     => 'aureon-spacing',
				'label'    => esc_html__( 'Top Bar Padding', 'aureon-studio' ),
				'section'  => 'aureon_top_bar',
				'settings' => array(
					'desktop_top'    => 'aureon_spacing_settings[top_bar_top]',
					'desktop_right'  => 'aureon_spacing_settings[top_bar_right]',
					'desktop_bottom' => 'aureon_spacing_settings[top_bar_bottom]',
					'desktop_left'   => 'aureon_spacing_settings[top_bar_left]',
				),
				'element' => 'top_bar',
				'priority' => 99,
				'active_callback' => 'aureon_premium_is_top_bar_active',
			)
		)
	);
}
