<?php
/**
 * Lazy — lazy-loading attribute builders.
 *
 * Phase 13 (Performance Engineering): small, pure builders for the lazy
 * loading strategy — image attributes (`loading="lazy"`, `decoding="async"`,
 * explicit width/height to prevent CLS), iframe laziness, and an
 * IntersectionObserver hook attribute for below-fold behavior. All helpers
 * are pure string builders (WP-free) so templates can use them without
 * touching WordPress globals.
 *
 * @package Phantom\Core\Performance
 * @since 0.13.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Performance;

/**
 * Lazy-load attribute helpers.
 */
final class Lazy {

	/**
	 * Image attributes preventing CLS + deferring decode.
	 *
	 * @param int $width  Natural width (0 = omit).
	 * @param int $height Natural height (0 = omit).
	 * @return string Attribute string (leading space omitted).
	 */
	public static function image_attrs( int $width = 0, int $height = 0 ): string {
		$parts = array(
			'loading="lazy"',
			'decoding="async"',
			'fetchpriority="auto"',
		);

		if ( $width > 0 && $height > 0 ) {
			$parts[] = 'width="' . (string) $width . '"';
			$parts[] = 'height="' . (string) $height . '"';
		}

		return implode( ' ', $parts );
	}

	/**
	 * Iframe lazy attributes.
	 *
	 * @param string $title Accessible iframe title.
	 * @return string
	 */
	public static function iframe_attrs( string $title = '' ): string {
		$parts = array(
			'loading="lazy"',
			'allowfullscreen',
		);

		if ( '' !== $title ) {
			$parts[] = 'title="' . self::attr( $title ) . '"';
		}

		return implode( ' ', $parts );
	}

	/**
	 * Whether the environment prefers reduced motion.
	 *
	 * @return bool
	 */
	public static function prefers_reduced_motion(): bool {
		// In a CLI context there is no media query; callers (TS runtime) gate
		// on the real matchMedia. This helper documents the intent and is the
		// seam tests assert against.
		return false;
	}

	/**
	 * Escape a value for an HTML attribute (WP-free parity with ViewContext).
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
}
