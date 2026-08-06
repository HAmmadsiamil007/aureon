<?php
/**
 * AETHER Design Token Output.
 *
 * Outputs CSS custom properties (:root variables) dynamically from
 * Customizer settings, overriding the hardcoded defaults in style.css.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_enqueue_scripts', 'aether_enqueue_tokens', 98 );
/**
 * Enqueue inline CSS that overrides :root custom properties.
 *
 * Priority 98 ensures this loads after core Aureon assets (50) but
 * before AETHER style.css (99), so the dynamic tokens take effect.
 */
function aether_enqueue_tokens() {
	if ( is_customize_preview() || is_admin() ) {
		return;
	}

	$css = aether_generate_tokens_css();

	if ( empty( $css ) ) {
		return;
	}

	wp_register_style( 'aether-tokens', false, array(), null, true );
	wp_enqueue_style( 'aether-tokens' );
	wp_add_inline_style( 'aether-tokens', $css );
}

/**
 * Generate CSS custom properties from Customizer settings.
 *
 * Only outputs non-default values to keep the inline CSS minimal.
 * Falls back to the hardcoded defaults in style.css for anything
 * not explicitly set by the user.
 *
 * @return string CSS custom property block.
 */
function aether_generate_tokens_css() {
	$tokens = array();

	// ─── Colors ──────────────────────────────────────────────
	$void    = get_theme_mod( 'aether_color_void', '#09090B' );
	$surface = get_theme_mod( 'aether_color_surface', '#141416' );
	$accent  = get_theme_mod( 'aether_color_accent', '#C8956C' );
	$text    = get_theme_mod( 'aether_color_text', '#A8B5C0' );

	$tokens[] = '--void: ' . sanitize_hex_color( $void ) . ';';
	$tokens[] = '--surface: ' . sanitize_hex_color( $surface ) . ';';
	$tokens[] = '--gold: ' . sanitize_hex_color( $accent ) . ';';
	$tokens[] = '--chrome: ' . sanitize_hex_color( $text ) . ';';

	// ─── Typography ──────────────────────────────────────────
	$heading = get_theme_mod( 'aether_font_heading', "'Cabinet Grotesk', sans-serif" );
	$body    = get_theme_mod( 'aether_font_body', "'Satoshi', sans-serif" );

	$tokens[] = '--font-heading: ' . esc_attr( $heading ) . ';';
	$tokens[] = '--font-body: ' . esc_attr( $body ) . ';';

	// ─── Spacing ─────────────────────────────────────────────
	$container = get_theme_mod( 'aether_container_width', '1200' );

	$tokens[] = '--container-max: ' . absint( $container ) . 'px;';

	if ( empty( $tokens ) ) {
		return '';
	}

	return ':root {' . implode( "\n\t", $tokens ) . '}';
}
