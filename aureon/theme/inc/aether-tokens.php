<?php
/**
 * AETHER Design Token Output.
 *
 * Outputs CSS custom properties (:root variables) dynamically from
 * the Aureon option bucket (aureon_get_option), overriding the
 * hardcoded defaults in style.css.
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
 */
function aether_enqueue_tokens() {
	if ( is_customize_preview() || is_admin() ) {
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
 * Falls back to the hardcoded defaults in style.css for anything not
 * explicitly set. Font stacks are quoted when they contain spaces.
 *
 * @return string CSS custom property block.
 */
function aether_generate_tokens_css() {
	if ( ! function_exists( 'aureon_get_option' ) ) {
		return '';
	}

	$tokens = array();

	// ─── Colors ──────────────────────────────────────────────
	$void    = aureon_get_option( 'aether_color_bg', '#09090B' );
	$surface = aureon_get_option( 'aether_color_surface', '#141416' );
	$accent  = aureon_get_option( 'aether_color_accent', '#C8956C' );
	$muted   = aureon_get_option( 'aether_color_muted', '#A8B5C0' );

	$tokens[] = '--void: ' . sanitize_hex_color( $void ) . ';';
	$tokens[] = '--surface: ' . sanitize_hex_color( $surface ) . ';';
	$tokens[] = '--gold: ' . sanitize_hex_color( $accent ) . ';';
	$tokens[] = '--chrome: ' . sanitize_hex_color( $muted ) . ';';

	// ─── Typography ──────────────────────────────────────────
	$heading = aureon_get_option( 'aether_font_heading', 'Cabinet Grotesk' );
	$body    = aureon_get_option( 'aether_font_body', 'Satoshi' );

	$tokens[] = '--font-heading: ' . aether_token_css_value( aether_token_font_stack( $heading ) ) . ';';
	$tokens[] = '--font-body: ' . aether_token_css_value( aether_token_font_stack( $body ) ) . ';';

	// ─── Layout ──────────────────────────────────────────────
	$container = aureon_get_option( 'aether_container_max', '1200px' );

	if ( is_numeric( $container ) ) {
		$container .= 'px';
	}

	$tokens[] = '--container-max: ' . aether_token_css_value( $container ) . ';';

	return ':root {' . implode( "\n\t", $tokens ) . '}';
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
