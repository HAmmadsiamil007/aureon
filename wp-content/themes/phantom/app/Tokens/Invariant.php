<?php
/**
 * Invariant — token set validation + contrast computation.
 *
 * Phase 3 (Design Token Engine): enforces the token governance rules from the
 * plan — (a) every token name matches the safe pattern (no selector/property
 * injection), (b) every 'extends' fallback exists in the set, and (c) the
 * fg/bg contrast pair of a preset meets WCAG AA (4.5:1) for normal text.
 * The returned violations are surfaced by the smoke suite and CI.
 *
 * @package Phantom\Core\Tokens
 * @since 0.3.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Tokens;

/**
 * Validates token sets and computes contrast ratios.
 */
final class Invariant {

	/**
	 * WCAG AA normal-text contrast floor.
	 */
	public const CONTRAST_AA = 4.5;

	/**
	 * Validate a flat token map.
	 *
	 * @param array<string, mixed> $flat Flat token map.
	 * @return string[] Violation messages (empty = valid).
	 */
	public function validate( array $flat ): array {
		$violations = array();

		foreach ( $flat as $name => $value ) {
			if ( ! ( new TokenSource() )->is_valid_name( $name ) ) {
				$violations[] = "Invalid token name: {$name}";
			}

			if ( is_array( $value ) && array_key_exists( 'extends', $value ) ) {
				$target = (string) ( $value['extends'] ?? '' );

				if ( ! array_key_exists( $target, $flat ) ) {
					$violations[] = "Token {$name} extends unknown {$target}";
				}
			}
		}

		return $violations;
	}

	/**
	 * Compute the WCAG contrast ratio between two hex colors.
	 *
	 * @param string $hex_a Hex color (#rgb or #rrggbb).
	 * @param string $hex_b Hex color (#rgb or #rrggbb).
	 * @return float Contrast ratio (1..21).
	 */
	public function contrast( string $hex_a, string $hex_b ): float {
		$lum_a = $this->luminance( $hex_a );
		$lum_b = $this->luminance( $hex_b );

		$light = max( $lum_a, $lum_b );
		$dark  = min( $lum_a, $lum_b );

		return ( $light + 0.05 ) / ( $dark + 0.05 );
	}

	/**
	 * Validate the contrast of a color pair from a resolved token map.
	 *
	 * @param array<string, mixed> $resolved Resolved flat token map.
	 * @return bool
	 */
	public function contrast_passes( array $resolved ): bool {
		$fg = (string) ( $resolved['color.fg'] ?? '#000000' );
		$bg = (string) ( $resolved['color.bg'] ?? '#ffffff' );

		return $this->contrast( $fg, $bg ) >= self::CONTRAST_AA;
	}

	/**
	 * Compute WCAG relative luminance for a hex color.
	 *
	 * @param string $hex Hex color.
	 * @return float
	 */
	private function luminance( string $hex ): float {
		$hex = ltrim( $hex, '#' );

		if ( 3 === strlen( $hex ) ) {
			$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
		}

		$r = hexdec( substr( $hex, 0, 2 ) ) / 255;
		$g = hexdec( substr( $hex, 2, 2 ) ) / 255;
		$b = hexdec( substr( $hex, 4, 2 ) ) / 255;

		$linear = static function ( float $channel ): float {
			return $channel <= 0.03928
				? $channel / 12.92
				: ( ( $channel + 0.055 ) / 1.055 ) ** 2.4;
		};

		return 0.2126 * $linear( $r ) + 0.7152 * $linear( $g ) + 0.0722 * $linear( $b );
	}
}
