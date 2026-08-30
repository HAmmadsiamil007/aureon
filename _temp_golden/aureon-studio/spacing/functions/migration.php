<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'aureon_spacing_update_footer_padding' ) ) {
	add_action( 'admin_init', 'aureon_spacing_update_footer_padding' );
	/**
	 * If our footer widget area has the old default 0 for left and right, set it to 40
	 * @since 1.3.42
	 * December 19, 2016
	 */
	function aureon_spacing_update_footer_padding() {
		// Bail if Aureon isn't activated
		if ( ! function_exists( 'aureon_spacing_get_defaults' ) ) {
			return;
		}

		// Get our migration settings
		$settings = get_option( 'aureon_migration_settings', array() );

		// If we've already ran this function, bail
		if ( isset( $settings[ 'footer_padding_updated' ] ) && 'true' == $settings[ 'footer_padding_updated' ] ) {
			return;
		}

		// Get our spacing settings
		$spacing_settings = wp_parse_args(
			get_option( 'aureon_spacing_settings', array() ),
			aureon_spacing_get_defaults()
		);

		// If we don't have a footer widget separator, we don't need to do this
		if ( ! isset( $spacing_settings[ 'footer_widget_separator' ] ) ) {
			return;
		}

		// We're still here, update our left and right footer widget area padding if they're set to 0
		if ( '0' == $spacing_settings[ 'footer_widget_container_right' ] && '0' == $spacing_settings[ 'footer_widget_container_left' ] ) {
			$new_settings[ 'footer_widget_container_right' ] = '40';
			$new_settings[ 'footer_widget_container_left' ] = '40';
			$update_settings = wp_parse_args( $new_settings, $spacing_settings );
			update_option( 'aureon_spacing_settings', $update_settings );
		}

		// Update our migration option so we don't need to run this again
		$updated[ 'footer_padding_updated' ] = 'true';
		$migration_settings = wp_parse_args( $updated, $settings );
		update_option( 'aureon_migration_settings', $migration_settings );
	}
}

if ( ! function_exists( 'aureon_spacing_update_mobile_content_padding' ) ) {
	add_action( 'admin_init', 'aureon_spacing_update_mobile_content_padding' );
	/**
	 * Mobile content padding used to be one option.
	 * Now we use 4, so we need to grab our mobile content padding option and set it to all 4 areas.
	 * @since 1.3.45
	 * Febuary 20, 2017
	 */
	function aureon_spacing_update_mobile_content_padding() {
		// Bail if Aureon isn't activated
		if ( ! function_exists( 'aureon_spacing_get_defaults' ) ) {
			return;
		}

		// Get our migration settings
		$settings = get_option( 'aureon_migration_settings', array() );

		// If we've already ran this function, bail
		if ( isset( $settings[ 'mobile_content_padding_updated' ] ) && 'true' == $settings[ 'mobile_content_padding_updated' ] ) {
			return;
		}

		// Get our spacing settings
		$spacing_settings = wp_parse_args(
			get_option( 'aureon_spacing_settings', array() ),
			aureon_spacing_get_defaults()
		);

		// If we don't have the new mobile content padding options, bail
		if ( ! isset( $spacing_settings[ 'mobile_content_top' ] ) ) {
			return;
		}

		// We're still here, update our mobile content padding options if they aren't set to 30 (default)
		if ( isset( $spacing_settings[ 'mobile_content_padding' ] ) && '30' !== $spacing_settings[ 'mobile_content_padding' ] ) {
			$new_settings[ 'mobile_content_top' ] = $spacing_settings[ 'mobile_content_padding' ];
			$new_settings[ 'mobile_content_right' ] = $spacing_settings[ 'mobile_content_padding' ];
			$new_settings[ 'mobile_content_bottom' ] = $spacing_settings[ 'mobile_content_padding' ];
			$new_settings[ 'mobile_content_left' ] = $spacing_settings[ 'mobile_content_padding' ];
			$update_settings = wp_parse_args( $new_settings, $spacing_settings );
			update_option( 'aureon_spacing_settings', $update_settings );
		}

		// Update our migration option so we don't need to run this again
		$updated[ 'mobile_content_padding_updated' ] = 'true';
		$migration_settings = wp_parse_args( $updated, $settings );
		update_option( 'aureon_migration_settings', $migration_settings );
	}
}
