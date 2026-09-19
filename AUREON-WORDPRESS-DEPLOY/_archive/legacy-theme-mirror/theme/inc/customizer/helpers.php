<?php
/**
 * Helper functions for the Customizer.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! function_exists( 'aureon_is_posts_page' ) ) {
	/**
	 * Check to see if we're on a posts page
	 *
	 * @since 1.3.39
	 */
	function aureon_is_posts_page() {
		return ( is_home() || is_archive() || is_tax() ) ? true : false;
	}
}

if ( ! function_exists( 'aureon_is_footer_bar_active' ) ) {
	/**
	 * Check to see if we're using our footer bar widget
	 *
	 * @since 1.3.42
	 */
	function aureon_is_footer_bar_active() {
		return ( is_active_sidebar( 'footer-bar' ) ) ? true : false;
	}
}

if ( ! function_exists( 'aureon_is_top_bar_active' ) ) {
	/**
	 * Check to see if the top bar is active
	 *
	 * @since 1.3.45
	 */
	function aureon_is_top_bar_active() {
		$top_bar = is_active_sidebar( 'top-bar' ) ? true : false;
		return apply_filters( 'aureon_is_top_bar_active', $top_bar );
	}
}

if ( ! function_exists( 'aureon_customize_partial_blogname' ) ) {
	/**
	 * Render the site title for the selective refresh partial.
	 *
	 * @since 1.3.41
	 */
	function aureon_customize_partial_blogname() {
		bloginfo( 'name' );
	}
}

if ( ! function_exists( 'aureon_customize_partial_blogdescription' ) ) {
	/**
	 * Render the site tagline for the selective refresh partial.
	 *
	 * @since 1.3.41
	 */
	function aureon_customize_partial_blogdescription() {
		bloginfo( 'description' );
	}
}

if ( ! function_exists( 'aureon_enqueue_color_palettes' ) ) {
	add_action( 'customize_controls_enqueue_scripts', 'aureon_enqueue_color_palettes' );
	/**
	 * Add our custom color palettes to the color pickers in the Customizer.
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

if ( ! function_exists( 'aureon_sanitize_integer' ) ) {
	/**
	 * Sanitize integers.
	 *
	 * @since 1.0.8
	 * @param string $input The value to check.
	 */
	function aureon_sanitize_integer( $input ) {
		return absint( $input );
	}
}

/**
 * Sanitize the shop per-page count, clamped to the control's UI range (1–48).
 *
 * @since 1.1.0
 * @param string $input The value to check.
 * @return int
 */
function aureon_sanitize_shop_per_page( $input ) {
	$value = (int) $input;

	return min( 48, max( 1, $value ) );
}

/**
 * Sanitize section padding — accepts 1–4 CSS lengths (e.g. "100px 0",
 * "40px 24px 40px 24px") or an empty string (inherit). Anything else is
 * rejected to keep raw CSS out of the emitted `:root` tokens (F4-4).
 *
 * @since 1.1.0
 * @param string $input The value to check.
 * @return string
 */
function aureon_sanitize_section_padding( $input ) {
	$input = trim( (string) $input );

	if ( '' === $input ) {
		return '';
	}

	$parts = preg_split( '/\s+/', $input );

	if ( count( $parts ) > 4 ) {
		return '';
	}

	foreach ( $parts as $part ) {
		if ( ! preg_match( '/^\d+(\.\d+)?(px|rem|em|vh|vw|%)?$/', $part ) ) {
			return '';
		}
	}

	return implode( ' ', $parts );
}

if ( ! function_exists( 'aureon_sanitize_decimal_integer' ) ) {
	/**
	 * Sanitize integers that can use decimals.
	 *
	 * @since 1.3.41
	 * @param string $input The value to check.
	 */
	function aureon_sanitize_decimal_integer( $input ) {
		return abs( floatval( $input ) );
	}
}

/**
 * Sanitize integers that can use decimals.
 *
 * @since 3.1.0
 * @param string $input The value to check.
 */
function aureon_sanitize_empty_decimal_integer( $input ) {
	if ( '' === $input ) {
		return '';
	}

	return abs( floatval( $input ) );
}

/**
 * Sanitize integers that can use negative decimals.
 *
 * @since 3.1.0
 * @param string $input The value to check.
 */
function aureon_sanitize_empty_negative_decimal_integer( $input ) {
	if ( '' === $input ) {
		return '';
	}

	return floatval( $input );
}

/**
 * Sanitize a positive number, but allow an empty value.
 *
 * @since 2.2
 * @param string $input The value to check.
 */
function aureon_sanitize_empty_absint( $input ) {
	// phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison -- Intentially loose.
	if ( '' == $input ) {
		return '';
	}

	return absint( $input );
}

if ( ! function_exists( 'aureon_sanitize_checkbox' ) ) {
	/**
	 * Sanitize checkbox values.
	 *
	 * @since 1.0.8
	 * @param string $checked The value to check.
	 */
	function aureon_sanitize_checkbox( $checked ) {
		// phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison -- Intentially loose.
		return ( ( isset( $checked ) && true == $checked ) ? true : false );
	}
}

if ( ! function_exists( 'aureon_sanitize_blog_excerpt' ) ) {
	/**
	 * Sanitize blog excerpt.
	 * Needed because Aureon Studio calls the control ID which is different from the settings ID.
	 *
	 * @since 1.0.8
	 * @param string $input The value to check.
	 */
	function aureon_sanitize_blog_excerpt( $input ) {
		$valid = array(
			'full',
			'excerpt',
		);

		if ( in_array( $input, $valid ) ) {
			return $input;
		} else {
			return 'full';
		}
	}
}

if ( ! function_exists( 'aureon_sanitize_hex_color' ) ) {
	/**
	 * Sanitize colors.
	 * Allow blank value.
	 *
	 * @since 1.2.9.6
	 * @param string $color The color to check.
	 */
	function aureon_sanitize_hex_color( $color ) {
		// phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison -- Intentially loose.
		if ( '' === $color ) {
			return '';
		}

		// 3, 4, 6 or 8 hex digits, or the empty string.
		if ( preg_match( '~^#([A-Fa-f0-9]{3,4}|[A-Fa-f0-9]{6}|[A-Fa-f0-9]{8})$~', $color ) ) {
			return $color;
		}

		// Sanitize CSS variables.
		if ( strpos( $color, 'var(' ) !== false ) {
			return sanitize_text_field( $color );
		}

		// Sanitize rgb() values.
		if ( strpos( $color, 'rgb(' ) !== false ) {
			$color = str_replace( ' ', '', $color );

			sscanf( $color, 'rgb(%d,%d,%d)', $red, $green, $blue );
			return 'rgb(' . $red . ',' . $green . ',' . $blue . ')';
		}

		// Sanitize rgba() values.
		if ( strpos( $color, 'rgba' ) !== false ) {
			$color = str_replace( ' ', '', $color );
			sscanf( $color, 'rgba(%d,%d,%d,%f)', $red, $green, $blue, $alpha );

			return 'rgba(' . $red . ',' . $green . ',' . $blue . ',' . $alpha . ')';
		}

		return '';
	}
}

/**
 * Sanitize RGBA colors.
 *
 * @since 2.2
 * @param string $color The color to check.
 */
function aureon_sanitize_rgba_color( $color ) {
	if ( '' === $color ) {
		return '';
	}

	if ( false === strpos( $color, 'rgba' ) ) {
		return aureon_sanitize_hex_color( $color );
	}

	$color = str_replace( ' ', '', $color );
	sscanf( $color, 'rgba(%d,%d,%d,%f)', $red, $green, $blue, $alpha );

	return 'rgba(' . $red . ',' . $green . ',' . $blue . ',' . $alpha . ')';
}

/**
 * Migrate a pre-v1.2 hero slide item onto the current schema contract.
 *
 * Legacy shape (title/subtitle/cta/url/label) is mapped to the schema keys
 * (headline/subline/primary_cta/secondary_cta/image_alt) before whitelisting,
 * so existing customization survives the first v1.2 save. Explicit new-shape
 * values always win.
 *
 * @since 1.2.0
 * @param array $item Raw repeater item.
 * @return array Migrated item.
 */
function aureon_repeater_migrate_legacy( $item ) {
	if ( empty( $item['headline'] ) && ! empty( $item['title'] ) ) {
		$item['headline'] = $item['title'];
	}
	if ( empty( $item['subline'] ) && ! empty( $item['subtitle'] ) ) {
		$item['subline'] = $item['subtitle'];
	}
	if ( empty( $item['image_alt'] ) && ! empty( $item['label'] ) ) {
		$item['image_alt'] = $item['label'];
	}
	if ( empty( $item['primary_cta'] ) && ( ! empty( $item['cta'] ) || isset( $item['url'] ) ) ) {
		$item['primary_cta'] = array(
			'label' => isset( $item['cta'] ) ? $item['cta'] : '',
			'url'   => isset( $item['url'] ) ? $item['url'] : '',
		);
	}
	return $item;
}

/**
 * Sanitize a schema-driven repeater value.
 *
 * Whitelists keys from the consumer-registered schema (see the
 * 'aether_repeater_schemas' filter), sanitizes each field by its declared
 * type, drops unknown/malformed entries, preserves stable slide IDs, and
 * reindexes the collection. Customizer sanitization is the trust boundary;
 * callers still escape at output.
 *
 * @since 1.2.0
 * @param mixed  $input      The submitted value (JSON string or array).
 * @param string $schema_key The registered schema key this setting belongs to.
 * @return array Sanitized, reindexed collection.
 */
function aureon_sanitize_repeater( $input, $schema_key = '' ) {
	if ( '' === $schema_key ) {
		return array();
	}

	$schemas = apply_filters( 'aether_repeater_schemas', array() );

	if ( ! isset( $schemas[ $schema_key ] ) || empty( $schemas[ $schema_key ]['fields'] ) || ! is_array( $schemas[ $schema_key ]['fields'] ) ) {
		return array();
	}

	if ( is_string( $input ) ) {
		$input = json_decode( $input, true );
	}

	if ( ! is_array( $input ) ) {
		return array();
	}

	$schema  = $schemas[ $schema_key ];
	$allowed = array( 'id' => 'id' );

	foreach ( $schema['fields'] as $field ) {
		if ( isset( $field['key'] ) ) {
			$allowed[ $field['key'] ] = isset( $field['type'] ) ? $field['type'] : 'text';
		}
	}

	$output = array();

	foreach ( $input as $item ) {
		if ( ! is_array( $item ) ) {
			continue;
		}

		// Migrate pre-v1.2 (legacy) keys onto the schema contract so an
		// existing user's hero survives their first v1.2 save. New keys win
		// when both shapes are present.
		$item = aureon_repeater_migrate_legacy( $item );

		$clean = array();

		foreach ( $allowed as $key => $type ) {
			if ( ! isset( $item[ $key ] ) ) {
				continue;
			}

			switch ( $type ) {
				case 'id':
					if ( preg_match( '|^slide_[a-f0-9]{8}$|', $item[ $key ] ) ) {
						$clean['id'] = $item[ $key ];
					}
					break;

				case 'url':
				case 'image':
					$value = trim( (string) $item[ $key ] );
					// Relative frontend asset paths (frontend/…) stay relative —
					// the frontend resolves them via content_url(). esc_url_raw()
					// would mangle them into http://frontend/… hosts.
					if ( '' !== $value && 0 === strpos( $value, 'frontend/' ) ) {
						$clean[ $key ] = $value;
					} elseif ( '' !== $value ) {
						$value = esc_url_raw( $value );
						if ( '' !== $value ) {
							$clean[ $key ] = $value;
						}
					}
					break;

				case 'checkbox':
					$clean[ $key ] = (bool) aureon_sanitize_checkbox( $item[ $key ] );
					break;

				case 'color':
					$color = aureon_sanitize_rgba_color( $item[ $key ] );
					if ( '' !== $color ) {
						$clean[ $key ] = $color;
					}
					break;

				case 'cta':
					$cta = $item[ $key ];
					if ( ! is_array( $cta ) || ( ! isset( $cta['label'] ) && ! isset( $cta['url'] ) ) ) {
						break;
					}
					if ( isset( $cta['label'] ) ) {
						$clean[ $key ]['label'] = sanitize_text_field( $cta['label'] );
					}
					if ( isset( $cta['url'] ) && '' !== esc_url_raw( $cta['url'] ) ) {
						$clean[ $key ]['url'] = esc_url_raw( $cta['url'] );
					} elseif ( isset( $cta['label'] ) && empty( $cta['url'] ) ) {
						$clean[ $key ]['url'] = '';
					}
					break;

				case 'textarea':
				default:
					$value = 'textarea' === $type ? sanitize_textarea_field( $item[ $key ] ) : sanitize_text_field( $item[ $key ] );
					if ( '' !== $value ) {
						$clean[ $key ] = $value;
					}
					break;
			}
		}

		// Preserve stable IDs; backfill for rows without a valid one.
		if ( empty( $clean['id'] ) ) {
			$clean['id'] = 'slide_' . substr( wp_hash( wp_json_encode( $clean ) . microtime(), 'nonce' ), 0, 8 );
		}

		$output[] = $clean;
	}

	return $output;
}

if ( ! function_exists( 'aureon_sanitize_choices' ) ) {
	/**
	 * Sanitize choices.
	 *
	 * @since 1.3.24
	 * @param string $input The value to check.
	 * @param object $setting The setting object.
	 */
	function aureon_sanitize_choices( $input, $setting ) {
		// Ensure input is a slug.
		$input = sanitize_key( $input );

		// Get list of choices from the control.
		// associated with the setting.
		$choices = $setting->manager->get_control( $setting->id )->choices;

		// If the input is a valid key, return it.
		// otherwise, return the default.
		return ( array_key_exists( $input, $choices ) ? $input : $setting->default );
	}
}

/**
 * Sanitize our Google Font variants
 *
 * @since 2.0
 * @param string $input The value to check.
 */
function aureon_sanitize_variants( $input ) {
	if ( is_array( $input ) ) {
		$input = implode( ',', $input );
	}
	return sanitize_text_field( $input );
}

add_action( 'customize_controls_enqueue_scripts', 'aureon_do_control_inline_scripts', 100 );
/**
 * Add misc inline scripts to our controls.
 *
 * We don't want to add these to the controls themselves, as they will be repeated
 * each time the control is initialized.
 *
 * @since 2.0
 */
function aureon_do_control_inline_scripts() {
	wp_localize_script(
		'aureon-typography-customizer',
		'aureon_customize',
		array(
			'nonce' => wp_create_nonce( 'aureon_customize_nonce' ),
		)
	);

	$number_of_fonts = apply_filters( 'aureon_number_of_fonts', 200 );

	wp_localize_script(
		'aureon-typography-customizer',
		'aureonTypography',
		array(
			'googleFonts' => apply_filters( 'aureon_typography_customize_list', aureon_get_all_google_fonts( $number_of_fonts ) ),
		)
	);

	wp_localize_script( 'aureon-typography-customizer', 'typography_defaults', aureon_typography_default_fonts() );

	wp_enqueue_script( 'aureon-customizer-controls', trailingslashit( get_template_directory_uri() ) . 'inc/customizer/controls/js/customizer-controls.js', array( 'customize-controls', 'jquery' ), AUREON_VERSION, true );
	wp_localize_script( 'aureon-customizer-controls', 'aureon_defaults', aureon_get_defaults() );
	wp_localize_script( 'aureon-customizer-controls', 'aureon_color_defaults', aureon_get_color_defaults() );
	wp_localize_script( 'aureon-customizer-controls', 'aureon_typography_defaults', aureon_get_default_fonts() );
	wp_localize_script( 'aureon-customizer-controls', 'aureon_spacing_defaults', aureon_spacing_get_defaults() );

	wp_localize_script(
		'aureon-customizer-controls',
		'aureonCustomizeControls',
		array(
			'mappedTypographyData' => array(
				'typography' => Aureon_Typography_Migration::get_mapped_typography_data(),
				'fonts' => Aureon_Typography_Migration::get_mapped_font_data(),
			),
		)
	);

	wp_enqueue_script(
		'aureon-customizer-controls-react',
		trailingslashit( get_template_directory_uri() ) . 'assets/dist/customizer.js',
		// We're including wp-color-picker for localized strings, nothing more.
		array( 'lodash', 'react', 'react-dom', 'wp-components', 'wp-element', 'wp-hooks', 'wp-i18n', 'wp-polyfill', 'jquery', 'customize-base', 'customize-controls', 'wp-color-picker' ),
		AUREON_VERSION,
		true
	);

	if ( function_exists( 'wp_set_script_translations' ) ) {
		wp_set_script_translations( 'aureon-customizer-controls-react', 'aureon' );
	}

	$color_palette = get_theme_support( 'editor-color-palette' );
	$colors = array();

	if ( is_array( $color_palette ) ) {
		foreach ( $color_palette as $key => $value ) {
			foreach ( $value as $color ) {
				$colors[] = array(
					'name' => $color['name'],
					'color' => $color['color'],
				);
			}
		}
	}

	wp_localize_script(
		'aureon-customizer-controls-react',
		'aureonCustomizerControls',
		array(
			'palette' => $colors,
			'showGoogleFonts' => apply_filters( 'aureon_font_manager_show_google_fonts', true ),
			'colorPickerShouldShift' => function_exists( 'did_filter' ),
			'aureonFontLibrary' => class_exists( 'Aureon_Pro_Font_Library' )
				? Aureon_Pro_Font_Library::get_fonts()
				: array(),
			'aureonFontLibraryURI' => class_exists( 'Aureon_Pro_Font_Library' )
				? Aureon_Pro_Font_Library::get_font_library_uri()
				: '',
		)
	);

	wp_enqueue_style(
		'aureon-customizer-controls-react',
		trailingslashit( get_template_directory_uri() ) . 'assets/dist/style-customizer.css',
		array( 'wp-components' ),
		AUREON_VERSION
	);

	$global_colors = aureon_get_global_colors();
	$global_colors_css = ':root {';

	if ( ! empty( $global_colors ) ) {
		foreach ( (array) $global_colors as $key => $data ) {
			$global_colors_css .= '--' . $data['slug'] . ':' . $data['color'] . ';';
		}
	}

	$global_colors_css .= '}';

	wp_add_inline_style( 'aureon-customizer-controls-react', $global_colors_css );
}

if ( ! function_exists( 'aureon_customizer_live_preview' ) ) {
	add_action( 'customize_preview_init', 'aureon_customizer_live_preview', 100 );
	/**
	 * Add our live preview scripts
	 *
	 * @since 0.1
	 */
	function aureon_customizer_live_preview() {
		$spacing_settings = wp_parse_args(
			get_option( 'aureon_spacing_settings', array() ),
			aureon_spacing_get_defaults()
		);

		wp_enqueue_script( 'aureon-themecustomizer', trailingslashit( get_template_directory_uri() ) . 'inc/customizer/controls/js/customizer-live-preview.js', array( 'customize-preview' ), AUREON_VERSION, true );

		wp_localize_script(
			'aureon-themecustomizer',
			'aureon_live_preview',
			array(
				'mobile' => aureon_get_media_query( 'mobile' ),
				'tablet' => aureon_get_media_query( 'tablet_only' ),
				'desktop' => aureon_get_media_query( 'desktop' ),
				'contentLeft' => absint( $spacing_settings['content_left'] ),
				'contentRight' => absint( $spacing_settings['content_right'] ),
				'isFlex' => aureon_is_using_flexbox(),
				'isRTL' => is_rtl(),
			)
		);

		wp_enqueue_script(
			'aureon-postMessage',
			trailingslashit( get_template_directory_uri() ) . 'inc/customizer/controls/js/postMessage.js',
			array( 'jquery', 'customize-preview', 'wp-hooks' ),
			AUREON_VERSION,
			true
		);

		global $aureon_customize_fields;
		wp_localize_script( 'aureon-postMessage', 'aureonPostMessageFields', $aureon_customize_fields );
	}
}

/**
 * Check to see if we have a logo or not.
 *
 * Used as an active callback. Calling has_custom_logo creates a PHP notice for
 * multisite users.
 *
 * @since 2.0.1
 */
function aureon_has_custom_logo_callback() {
	if ( get_theme_mod( 'custom_logo' ) ) {
		return true;
	}

	return false;
}

/**
 * Save our preset layout controls. These should always save to be "current".
 *
 * @since 2.2
 */
function aureon_sanitize_preset_layout() {
	return 'current';
}

/**
 * Display options if we're using the Floats structure.
 */
function aureon_is_using_floats_callback() {
	return 'floats' === aureon_get_option( 'structure' );
}

/**
 * Callback to determine whether to show the inline logo option.
 */
function aureon_show_inline_logo_callback() {
	return 'floats' === aureon_get_option( 'structure' ) && aureon_has_logo_site_branding();
}
