<?php
/**
 * ViewModel helpers — normalize adapter output into component data.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Normalize an image field into { id, url, alt, sizes }.
 *
 * @param int|array $attachment Attachment ID or ['id','url','alt'] array.
 * @return array
 */
function aether_viewmodel_image( $attachment ) {
	$out = array(
		'id'    => 0,
		'url'   => '',
		'alt'   => '',
		'sizes' => array(),
	);

	if ( is_numeric( $attachment ) && $attachment ) {
		$out['id']  = (int) $attachment;
		$out['url'] = wp_get_attachment_image_url( $attachment, 'medium_large' );
		$out['alt'] = get_post_meta( $attachment, '_wp_attachment_image_alt', true );
		return $out;
	}

	if ( is_array( $attachment ) ) {
		$out['id']  = isset( $attachment['id'] ) ? absint( $attachment['id'] ) : 0;
		$out['url'] = isset( $attachment['url'] ) ? esc_url_raw( $attachment['url'] ) : '';
		$out['alt'] = isset( $attachment['alt'] ) ? sanitize_text_field( $attachment['alt'] ) : '';
	}

	return $out;
}

/**
 * Resolve an asset/image URL — relative frontend paths get a content URL prefix.
 *
 * @param string $src Raw source (absolute URL, protocol-relative, or relative).
 * @return string
 */
function aether_viewmodel_resolve_image( $src ) {
	$src = trim( (string) $src );

	if ( '' === $src ) {
		return '';
	}

	if ( 0 === strpos( $src, 'http://' ) || 0 === strpos( $src, 'https://' ) || 0 === strpos( $src, '//' ) || 0 === strpos( $src, 'data:' ) ) {
		return esc_url_raw( $src );
	}

	// Strip a leading slash, then resolve against the content root.
	$path = ltrim( $src, '/' );
	if ( 0 === strpos( $path, 'frontend/' ) ) {
		return esc_url_raw( content_url( '/' . $path ) );
	}

	return esc_url_raw( $src );
}

/**
 * Sanitize an overlay color: hex (#rgb/#rgba/#rrggbb/#rrggbbaa) or
 * rgb()/rgba(). Anything else resolves to '' (component default overlay).
 *
 * @param string $color Raw overlay value.
 * @return string
 */
function aether_sanitize_overlay_color( $color ) {
	$color = trim( (string) $color );

	if ( '' === $color ) {
		return '';
	}

	if ( preg_match( '~^#([A-Fa-f0-9]{3,4}|[A-Fa-f0-9]{6}|[A-Fa-f0-9]{8})$~', $color ) ) {
		return $color;
	}

	if ( preg_match( '|^rgba?\([0-9.,\s]+\)$|', $color ) ) {
		return $color;
	}

	return '';
}

/**
 * Merge component defaults with supplied data.
 *
 * @param array $data      Supplied data.
 * @param array $defaults  Default keys.
 * @return array
 */
function aether_viewmodel_merge( $data, $defaults ) {
	return wp_parse_args( (array) $data, $defaults );
}

/**
 * Build a behavior array honoring the Customizer motion toggles.
 *
 * @param array $behavior Desired behavior flags.
 * @return array Filtered behavior.
 */
function aether_viewmodel_behavior( $behavior = array() ) {
	$behavior = (array) $behavior;

	if ( ! aureon_get_option( 'aether_motion_enabled' ) ) {
		return array();
	}

	if ( ! aureon_get_option( 'aether_motion_reveal' ) ) {
		unset( $behavior['reveal'], $behavior['reveal-group'] );
	}

	if ( ! aureon_get_option( 'aether_motion_tilt' ) ) {
		unset( $behavior['tilt'] );
	}

	if ( ! aureon_get_option( 'aether_motion_parallax' ) ) {
		unset( $behavior['parallax'], $behavior['parallax-section'] );
	}

	if ( ! aureon_get_option( 'aether_motion_text' ) ) {
		unset( $behavior['motion-text'] );
	}

	return $behavior;
}
