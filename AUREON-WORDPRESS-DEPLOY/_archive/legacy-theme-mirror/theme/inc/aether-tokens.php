<?php
/**
 * AETHER Design Token Output.
 *
 * Outputs CSS custom properties (:root variables) dynamically from
 * the Aureon option bucket (aureon_get_option), overriding the
 * hardcoded defaults in style.css.
 *
 * WS-1 (AETHER × Aureon integration plan):
 * - Emits the FULL token set: 12 colors, 2 font stacks (bridged to the
 *   dynamic Typography Manager), and 9 layout tokens (container, section
 *   padding, announcement/header heights, grid gap, radii).
 * - Customizer-aware: tokens render in the Customizer preview iframe
 *   (the old is_customize_preview() bail hid every dynamic token).
 * - Color resolution precedence: explicit `aether_color_*` option →
 *   customized `global_colors` palette (React Color Manager) → AETHER
 *   default. Fonts: explicit `aether_font_*` → Typography Manager entry
 *   (body / all-headings) → classic font option → default.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

add_action( 'wp_enqueue_scripts', 'aether_enqueue_tokens', 98 );

/**
 * Enqueue inline CSS that overrides :root custom properties.
 *
 * Priority 98 ensures this loads after the engine styles (20) but is
 * printed after them, so the dynamic tokens take effect.
 *
 * NOTE: is_customize_preview() is intentionally NOT bailed — the
 * Customizer preview iframe is a normal front-end render and must see
 * live tokens so every control round-trips (G10).
 */
function aether_enqueue_tokens() {
	if ( is_admin() ) {
		return;
	}

	// Complete-page designs use their own CSS custom properties.
	// AUREON :root tokens must not leak into the complete-page document.
	if ( aether_is_complete_page_design() ) {
		return;
	}

	$css = aether_generate_tokens_css();

	if ( empty( $css ) ) {
		return;
	}

	wp_register_style( 'aether-tokens', false, array( 'aether-style' ), null, true );
	wp_enqueue_style( 'aether-tokens' );
	wp_add_inline_style( 'aether-tokens', $css );
}

/**
 * Generate CSS custom properties from the Aureon option bucket.
 *
 * Every value flows through a sanitizer and falls back to the same
 * default that style.css :root declares, so removing this block never
 * breaks the design.
 *
 * @return string CSS custom property block.
 */
function aether_generate_tokens_css() {
	if ( ! function_exists( 'aureon_get_option' ) ) {
		return '';
	}

	// Read the raw option bucket ONCE and pass it down — every resolver
	// needs to distinguish "explicitly saved" from "default", and this
	// avoids N+1 get_option() calls on every page render.
	$saved  = get_option( 'aureon_settings', array() );
	$tokens = array();

	// ─── Colors ──────────────────────────────────────────────
	// CSS var => [ option key, palette slug fallbacks, default ].
	$colors = array(
		'--void'        => array( 'aether_color_bg', array( 'base', 'base-2' ), '#09090B' ),
		'--surface'     => array( 'aether_color_surface', array( 'base-2', 'base-3' ), '#141416' ),
		'--surface-2'   => array( 'aether_color_surface_2', array( 'base-3' ), '#1a1a1d' ),
		'--surface-3'   => array( 'aether_color_surface_3', array(), '#232327' ),
		'--text'        => array( 'aether_color_text', array( 'contrast' ), '#FFFFFF' ),
		'--muted'       => array( 'aether_color_muted', array( 'contrast-2' ), '#A8B5C0' ),
		'--chrome'      => array( 'aether_color_muted', array( 'contrast-2' ), '#A8B5C0' ), // Alias kept for existing CSS.
		'--gold'        => array( 'aether_color_accent', array( 'accent' ), '#C8956C' ),
		'--gold-alt'    => array( 'aether_color_accent_hover', array(), '#D4A574' ),
		'--line'        => array( 'aether_color_border', array(), '#1A1A1A' ),
		'--error'       => array( 'aether_color_error', array(), '#CC4444' ),
		'--success'     => array( 'aether_color_success', array(), '#4CAF50' ),
	);

	foreach ( $colors as $var => $def ) {
		$tokens[] = $var . ': ' . aether_resolve_color( $def[0], $def[1], $def[2], $saved ) . ';';
	}

	// ─── WooCommerce color bridge (M2) ─────────────────────────────
	// Streams the WC Customizer palette into dedicated `--aether-wc-*`
	// tokens consumed by the engine's shop surfaces (rating stars, price,
	// badges). Bridged ONLY when the merchant explicitly set the palette —
	// untouched WC options stay on the AETHER defaults (gold), so the
	// engine accent survives out-of-the-box installs unchanged.
	if ( class_exists( 'WooCommerce' ) ) {
		$wc_primary   = get_option( 'woocommerce_primary_color' );
		$wc_highlight = get_option( 'woocommerce_highlight_color' );
		$wc_subtext   = get_option( 'woocommerce_subtext_color' );

		$wc_bridge = array();
		if ( is_string( $wc_primary ) && '' !== $wc_primary ) {
			$wc_bridge['--aether-wc-primary'] = aether_sanitize_color( $wc_primary, '#C8956C' );
		}
		if ( is_string( $wc_highlight ) && '' !== $wc_highlight ) {
			$wc_bridge['--aether-wc-highlight'] = aether_sanitize_color( $wc_highlight, '#CC4444' );
		}
		if ( is_string( $wc_subtext ) && '' !== $wc_subtext ) {
			$wc_bridge['--aether-wc-subtext'] = aether_sanitize_color( $wc_subtext, '#A8B5C0' );
		}

		// Price follows primary unless WC stores an explicit price color.
		if ( isset( $wc_bridge['--aether-wc-primary'] ) ) {
			$wc_price = get_option( 'woocommerce_price_color' );
			$wc_bridge['--aether-wc-price'] = ( is_string( $wc_price ) && '' !== $wc_price )
				? aether_sanitize_color( $wc_price, $wc_bridge['--aether-wc-primary'] )
				: $wc_bridge['--aether-wc-primary'];
		}

		foreach ( $wc_bridge as $var => $value ) {
			$tokens[] = $var . ': ' . $value . ';';
		}
	}

	// ─── Typography (bridged to the dynamic Typography Manager) ──
	$heading = aether_font_for( 'heading', $saved );
	$body    = aether_font_for( 'body', $saved );

	$tokens[] = '--font-heading: ' . aether_token_css_value( aether_token_font_stack( $heading ) ) . ';';
	$tokens[] = '--font-body: ' . aether_token_css_value( aether_token_font_stack( $body ) ) . ';';

	// ─── Layout ──────────────────────────────────────────────
	$layout = array(
		'--container-max'       => array( 'aether_container_max', '1200px' ),
		'--section-padding'     => array( 'aether_section_padding', '100px 0' ),
		'--announcement-height' => array( 'aether_announcement_height', '40px' ),
		'--header-height'       => array( 'aether_header_height', '80px' ),
		'--grid-gap'            => array( 'aether_grid_gap', '24px' ),
		'--radius-sm'           => array( 'aether_radius_sm', '8px' ),
		'--radius-md'           => array( 'aether_radius_md', '12px' ),
		'--radius-lg'           => array( 'aether_radius_lg', '24px' ),
		'--radius-pill'         => array( 'aether_radius_pill', '999px' ),
	);

	foreach ( $layout as $var => $def ) {
		$value = aureon_get_option( $def[0] );

		if ( null === $value || '' === $value ) {
			$value = $def[1];
		}

		// Normalize bare numbers to px (matching the option pattern).
		if ( is_numeric( $value ) ) {
			$value .= 'px';
		}

		$tokens[] = $var . ': ' . aether_token_css_value( $value ) . ';';
	}

	return ':root {' . implode( "\n\t", $tokens ) . '}';
}

/**
 * Resolve a color token.
 *
 * Precedence:
 * 1. Explicit `aether_color_*` option saved in the DB.
 * 2. A customized `global_colors` palette (React Color Manager), mapped
 *    by palette slug — only when the palette differs from the theme
 *    defaults, so the dark AETHER design can't be clobbered by the
 *    default light palette.
 * 3. The AETHER default (registered in frontend/tokens/tokens.php).
 *
 * @param string $option_key    Option key (e.g. 'aether_color_bg').
 * @param array  $palette_slugs Palette slugs that may map to this token.
 * @param string $default       Fallback color.
 * @param array  $saved         Raw aureon_settings bucket (read once by the caller).
 * @return string Safe CSS color value.
 */
function aether_resolve_color( $option_key, $palette_slugs, $default, $saved ) {
	if ( is_array( $saved ) && isset( $saved[ $option_key ] ) && '' !== $saved[ $option_key ] && null !== $saved[ $option_key ] ) {
		return aether_sanitize_color( $saved[ $option_key ], $default );
	}

	$palette = aether_get_custom_palette( $saved );

	if ( ! empty( $palette ) ) {
		foreach ( (array) $palette_slugs as $slug ) {
			if ( isset( $palette[ $slug ] ) ) {
				return aether_sanitize_color( $palette[ $slug ], $default );
			}
		}
	}

	return $default;
}

/**
 * Get the palette from the React Color Manager — only when the user
 * actually customized it (saved value differs from the theme defaults).
 *
 * @param array $saved Raw aureon_settings bucket (read once by the caller).
 * @return array Slug => color map, empty when not customized.
 */
function aether_get_custom_palette( $saved ) {
	if ( empty( $saved['global_colors'] ) || ! is_array( $saved['global_colors'] ) ) {
		return array();
	}

	$default_palette = function_exists( 'aureon_get_defaults' ) ? aureon_get_defaults() : array();
	$default_palette = isset( $default_palette['global_colors'] ) ? $default_palette['global_colors'] : array();

	// Identical to the default palette → not a real customization.
	if ( $saved['global_colors'] === $default_palette ) {
		return array();
	}

	$map = array();

	foreach ( $saved['global_colors'] as $entry ) {
		if ( ! empty( $entry['slug'] ) && ! empty( $entry['color'] ) ) {
			$map[ $entry['slug'] ] = $entry['color'];
		}
	}

	return $map;
}

/**
 * Resolve a font stack role to a family name.
 *
 * Precedence:
 * 1. Explicit `aether_font_heading` / `aether_font_body` override.
 * 2. Dynamic Typography Manager entries (selector `body` for body;
 *    `all-headings` for headings).
 * 3. Classic font options when dynamic typography is disabled.
 * 4. AETHER defaults (Cabinet Grotesk / Satoshi).
 *
 * @param string $role  'heading' | 'body'.
 * @param array  $saved Raw aureon_settings bucket (read once by the caller).
 * @return string Bare font family name.
 */
function aether_font_for( $role, $saved ) {
	$option = 'aether_font_' . $role;

	// 1. Explicit AETHER override.
	if ( is_array( $saved ) && isset( $saved[ $option ] ) && '' !== $saved[ $option ] ) {
		return $saved[ $option ];
	}

	// 2. Dynamic typography entries.
	if ( function_exists( 'aureon_is_using_dynamic_typography' ) && aureon_is_using_dynamic_typography() ) {
		$typography = aureon_get_option( 'typography' );
		$selectors  = ( 'body' === $role ) ? array( 'body' ) : array( 'all-headings' );

		foreach ( (array) $typography as $entry ) {
			if ( ! empty( $entry['fontFamily'] ) && in_array( $entry['selector'], $selectors, true ) ) {
				return $entry['fontFamily'];
			}
		}
	} elseif ( function_exists( 'aureon_get_option' ) ) {
		// 3. Classic fonts (legacy path when dynamic typography is off).
		$classic = ( 'body' === $role ) ? 'font_body' : 'font_heading_1';
		$family  = aureon_get_option( $classic );

		if ( $family && 'inherit' !== $family && 'System Stack' !== $family ) {
			return $family;
		}
	}

	// 4. AETHER defaults (defensive: tokens.php may not be loaded yet).
	$default = aureon_get_option( $option );

	if ( ! is_string( $default ) || '' === $default ) {
		$default = ( 'heading' === $role ) ? 'Cabinet Grotesk' : 'Satoshi';
	}

	return $default;
}

/**
 * Sanitize a color value for use inside a CSS declaration.
 *
 * Accepts hex, rgb()/rgba(), and var() references. Anything else falls
 * back to $fallback. (Core sanitize_hex_color() rejects rgba strings,
 * so this is the CSS-value-safe alternative used on the front end.)
 *
 * @param string $value    Raw color value.
 * @param string $fallback Safe fallback color.
 * @return string Safe CSS color value.
 */
function aether_sanitize_color( $value, $fallback ) {
	$value = trim( (string) $value );

	if ( '' === $value ) {
		return $fallback;
	}

	if ( preg_match( '/^#([A-Fa-f0-9]{3}){1,2}$/', $value ) ) {
		return $value;
	}

	if ( 0 === strpos( $value, 'rgb' ) || 0 === strpos( $value, 'var(' ) ) {
		$clean = aether_token_css_value( $value );

		if ( '' !== $clean ) {
			return $clean;
		}
	}

	return $fallback;
}

/**
 * Sanitize a value for use inside a CSS declaration.
 *
 * Inline CSS is printed inside a <style> tag (CSS text context), so the
 * value must not be HTML-escaped (esc_attr() would turn quotes into
 * &#039; entities and break the declaration). Only characters valid in a
 * CSS value are allowed; anything else is stripped.
 *
 * @param string $value Raw CSS value.
 * @return string Sanitized CSS value.
 */
function aether_token_css_value( $value ) {
	$value = preg_replace( '/[^a-zA-Z0-9-_.,\'"(): #%\/!@]/', '', (string) $value );
	return $value;
}

/**
 * Build a CSS font-family value from a bare font name.
 *
 * @param string $font Font name.
 * @return string CSS font stack.
 */
function aether_token_font_stack( $font ) {
	$font = trim( $font );

	if ( '' === $font ) {
		return 'sans-serif';
	}

	if ( false !== strpos( $font, ',' ) || 0 === strpos( $font, "'" ) || 0 === strpos( $font, '"' ) ) {
		return $font;
	}

	return "'" . $font . "', sans-serif";
}
