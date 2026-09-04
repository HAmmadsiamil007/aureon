<?php
/**
 * Where old Customizer functions retire.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! function_exists( 'aureon_sanitize_typography' ) ) {
	/**
	 * Sanitize typography dropdown.
	 *
	 * @since 1.1.10
	 * @deprecated 1.3.45
	 * @param string $input The value to check.
	 */
	function aureon_sanitize_typography( $input ) {
		// Grab all of our fonts.
		$fonts = aureon_get_all_google_fonts();

		// Loop through all of them and grab their names.
		$font_names = array();
		foreach ( $fonts as $k => $fam ) {
			$font_names[] = $fam['name'];
		}

		// Get all non-Google font names.
		$not_google = aureon_typography_default_fonts();

		// Merge them both into one array.
		$valid = array_merge( $font_names, $not_google );

		// Sanitize.
		if ( in_array( $input, $valid ) ) {
			return $input;
		} else {
			return 'Open Sans';
		}
	}
}

if ( ! function_exists( 'aureon_sanitize_font_weight' ) ) {
	/**
	 * Sanitize font weight.
	 *
	 * @since 1.1.10
	 * @deprecated 1.3.40
	 * @param string $input The value to check.
	 */
	function aureon_sanitize_font_weight( $input ) {

		$valid = array(
			'normal',
			'bold',
			'100',
			'200',
			'300',
			'400',
			'500',
			'600',
			'700',
			'800',
			'900',
		);

		if ( in_array( $input, $valid ) ) {
			return $input;
		} else {
			return 'normal';
		}
	}
}

if ( ! function_exists( 'aureon_sanitize_text_transform' ) ) {
	/**
	 * Sanitize text transform.
	 *
	 * @since 1.1.10
	 * @deprecated 1.3.40
	 * @param string $input The value to check.
	 */
	function aureon_sanitize_text_transform( $input ) {

		$valid = array(
			'none',
			'capitalize',
			'uppercase',
			'lowercase',
		);

		if ( in_array( $input, $valid ) ) {
			return $input;
		} else {
			return 'none';
		}
	}
}

if ( ! function_exists( 'aureon_typography_customize_preview_css' ) ) {
	/**
	 * Hide the hidden input control
	 *
	 * @since 1.3.40
	 */
	function aureon_typography_customize_preview_css() {
		?>
		<style>
			.customize-control-aureon-hidden-input {display:none !important;}
		</style>
		<?php
	}
}

if ( ! function_exists( 'aureon_hidden_navigation' ) && function_exists( 'is_customize_preview' ) ) {
	/**
	 * Adds a hidden navigation if no navigation is set
	 * This allows us to use postMessage to position the navigation when it doesn't exist
	 *
	 * @since 1.3.40
	 */
	function aureon_hidden_navigation() {
		if ( is_customize_preview() && function_exists( 'aureon_navigation_position' ) ) {
			?>
			<div style="display:none;">
				<?php aureon_navigation_position(); ?>
			</div>
			<?php
		}
	}
}
