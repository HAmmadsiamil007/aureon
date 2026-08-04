<?php
/**
 * SkipLink — accessible skip-to-content link.
 *
 * Phase 14 (Accessibility Engineering): renders the first-focusable skip link
 * (`<a class="screen-reader-text" href="#main">`) that lets keyboard users
 * jump past navigation. WP-free safe — pure markup builder (plan §Phase 14:
 * `A11y\SkipLink`).
 *
 * @package Phantom\Core\A11y
 * @since 0.14.0
 */

declare( strict_types=1 );

namespace Phantom\Core\A11y;

/**
 * Skip-link markup builder.
 */
final class SkipLink {

	/**
	 * Render the skip link.
	 *
	 * @param string $target Target id (default 'main').
	 * @param string $label  Accessible label.
	 * @return string
	 */
	public static function render( string $target = 'main', string $label = 'Skip to content' ): string {
		$target = '' !== $target ? '#' . ltrim( $target, '#' ) : '#main';
		$label  = '' !== $label ? $label : 'Skip to content';

		return sprintf(
			'<a class="screen-reader-text phantom-skip-link" href="%1$s">%2$s</a>',
			self::attr( $target ),
			self::text( $label )
		);
	}

	/**
	 * Escape for an attribute context (WP-first, PHP fallback).
	 *
	 * @param string $value Raw value.
	 * @return string
	 */
	private static function attr( string $value ): string {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		return function_exists( 'esc_attr' )
			? esc_attr( $value )
			: htmlspecialchars( $value, ENT_QUOTES, 'UTF-8' );
	}

	/**
	 * Escape for a text context (WP-first, PHP fallback).
	 *
	 * @param string $value Raw value.
	 * @return string
	 */
	private static function text( string $value ): string {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		return function_exists( 'esc_html' )
			? esc_html( $value )
			: htmlspecialchars( $value, ENT_QUOTES, 'UTF-8' );
	}
}
