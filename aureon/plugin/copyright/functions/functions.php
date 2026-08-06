<?php
/**
 * This file handles the Copyright functionality.
 *
 * @package Aureon Studio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

if ( ! function_exists( 'aureon_copyright_customize_register' ) ) {
	add_action( 'customize_register', 'aureon_copyright_customize_register' );
	/**
	 * Add our copyright options to the Customizer.
	 *
	 * @param object $wp_customize The Customizer object.
	 */
	function aureon_copyright_customize_register( $wp_customize ) {
		// Get our custom control.
		require_once AUREON_LIBRARY_DIRECTORY . 'customizer-helpers.php';

		// Register our custom control.
		if ( method_exists( $wp_customize, 'register_control_type' ) ) {
			$wp_customize->register_control_type( 'Aureon_Copyright_Customize_Control' );
		}

		$wp_customize->add_setting(
			'aureon_copyright',
			array(
				'default' => '',
				'type' => 'theme_mod',
				'sanitize_callback' => 'wp_kses_post',
				'transport' => 'postMessage',
			)
		);

		$wp_customize->add_control(
			new Aureon_Copyright_Customize_Control(
				$wp_customize,
				'aureon_copyright',
				array(
					'label'      => __( 'Copyright', 'aureon-studio' ),
					'section'    => 'aureon_layout_footer',
					'settings'   => 'aureon_copyright',
					'priority' => 500,
				)
			)
		);

		// Initiate selective refresh.
		if ( isset( $wp_customize->selective_refresh ) ) {
			$wp_customize->selective_refresh->add_partial(
				'aureon_copyright',
				array(
					'selector' => '.copyright-bar',
					'settings' => array( 'aureon_copyright' ),
					'render_callback' => 'aureon_copyright_selective_refresh',
				)
			);
		}
	}
}

if ( ! function_exists( 'aureon_copyright_selective_refresh' ) ) {
	/**
	 * Return our copyright on selective refresh
	 */
	function aureon_copyright_selective_refresh() {
		$options = array(
			'%current_year%',
			'%copy%',
		);

		$replace = array(
			date( 'Y' ), // phpcs:ignore -- prefer date().
			'&copy;',
		);

		$new_copyright = get_theme_mod( 'aureon_copyright' );
		$new_copyright = str_replace( $options, $replace, get_theme_mod( 'aureon_copyright' ) );

		return do_shortcode( $new_copyright );
	}
}

if ( ! function_exists( 'aureon_copyright_remove_default' ) ) {
	add_action( 'wp', 'aureon_copyright_remove_default' );
	/**
	 * Remove the default copyright.
	 *
	 * @since 0.1
	 * @deprecated 1.3.42
	 */
	function aureon_copyright_remove_default() {
		// As of 1.3.42, we no longer need to do this.
		// We use a nice little filter instead.
		if ( ! function_exists( 'aureon_add_login_attribution' ) ) {
			return;
		}

		if ( get_theme_mod( 'aureon_copyright' ) && '' !== get_theme_mod( 'aureon_copyright' ) ) {
			remove_action( 'aureon_credits', 'aureon_add_footer_info' );
			remove_action( 'aureon_copyright_line', 'aureon_add_login_attribution' );
		}
	}
}

if ( ! function_exists( 'aureon_copyright_add_custom' ) ) {
	add_action( 'aureon_credits', 'aureon_copyright_add_custom' );
	/**
	 * Add the custom copyright.
	 *
	 * @since 0.1
	 * @deprecated 1.3.42
	 */
	function aureon_copyright_add_custom() {
		// As of 1.3.42, we no longer need to do this.
		// We use a nice little filter instead.
		if ( ! function_exists( 'aureon_add_login_attribution' ) ) {
			return;
		}

		$options = array(
			'%current_year%',
			'%copy%',
		);

		$replace = array(
			date( 'Y' ), // phpcs:ignore -- prefer date().
			'&copy;',
		);

		$new_copyright = get_theme_mod( 'aureon_copyright' );
		$new_copyright = str_replace( $options, $replace, get_theme_mod( 'aureon_copyright' ) );

		if ( get_theme_mod( 'aureon_copyright' ) && '' !== get_theme_mod( 'aureon_copyright' ) ) {
			echo do_shortcode( $new_copyright );
		}
	}
}

if ( ! function_exists( 'aureon_apply_custom_copyright' ) ) {
	add_filter( 'aureon_copyright', 'aureon_apply_custom_copyright' );
	/**
	 * Add the custom copyright
	 *
	 * @since 1.2.92
	 * @param string $copyright The copyright value.
	 */
	function aureon_apply_custom_copyright( $copyright ) {
		// This will only work if Aureon >= 1.3.42 and the below function doesn't exist.
		if ( function_exists( 'aureon_add_login_attribution' ) ) {
			return;
		}

		$options = array(
			'%current_year%',
			'%copy%',
		);

		$replace = array(
			date( 'Y' ), // phpcs:ignore -- prefer date().
			'&copy;',
		);

		$new_copyright = get_theme_mod( 'aureon_copyright' );
		$new_copyright = str_replace( $options, $replace, get_theme_mod( 'aureon_copyright' ) );

		if ( get_theme_mod( 'aureon_copyright' ) && '' !== get_theme_mod( 'aureon_copyright' ) ) {
			return do_shortcode( $new_copyright );
		}

		return $copyright;

	}
}

if ( ! function_exists( 'aureon_copyright_customizer_live_preview' ) ) {
	add_action( 'customize_preview_init', 'aureon_copyright_customizer_live_preview' );
	/**
	 * Add our live preview
	 */
	function aureon_copyright_customizer_live_preview() {
		wp_enqueue_script(
			'aureon-copyright-customizer',
			plugin_dir_url( __FILE__ ) . 'js/customizer.js',
			array( 'jquery', 'customize-preview' ),
			AUREON_COPYRIGHT_VERSION,
			true
		);
	}
}

if ( ! function_exists( 'aureon_update_copyright' ) ) {
	add_action( 'admin_init', 'aureon_update_copyright' );
	/**
	 * Our copyright use to have it's own setting
	 * If we have the old setting, move it into our theme_mod
	 */
	function aureon_update_copyright() {
		// If we already have a custom logo, bail.
		if ( get_theme_mod( 'aureon_copyright' ) ) {
			return;
		}

		// Get the old logo value.
		$old_value = get_option( 'gen_custom_copyright' );

		// If there's no old value, bail.
		if ( empty( $old_value ) ) {
			return;
		}

		// Now let's update the new logo setting with our ID.
		set_theme_mod( 'aureon_copyright', $old_value );

		// Got our custom logo? Time to delete the old value.
		if ( get_theme_mod( 'aureon_copyright' ) ) {
			delete_option( 'gen_custom_copyright' );
		}
	}
}
