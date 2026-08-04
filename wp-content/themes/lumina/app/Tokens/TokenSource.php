<?php
/**
 * TokenSource — parse token definitions and select presets.
 *
 * Phase 3 (Design Token Engine): turns raw definition arrays (defaults file +
 * preset files) into the canonical flat map used by the resolver. Flattening
 * converts nested groups (color.bg, component.button.bg) into dot-names.
 *
 * @package Lumina\Core\Tokens
 * @since 0.3.0
 */

declare( strict_types=1 );

namespace Lumina\Core\Tokens;

/**
 * Parses token definition arrays into flat token maps.
 */
final class TokenSource {

	/**
	 * Token name validation pattern (plan §Phase 3 security): lowercase letters,
	 * digits, and hyphens per dot-segment — prevents selector/property
	 * injection when names become CSS custom-property suffixes. A leading digit
	 * is permitted so canonical numeric-scale tokens (space.4, type.size.2xl)
	 * validate; the CSS var prefix (--lumina-) guarantees the full property
	 * name still starts with a letter. No dots, quotes, braces, or uppercase
	 * ever reach a selector/declaration.
	 */
	public const NAME_PATTERN = '/^[a-z0-9][a-z0-9\-]{0,63}$/';

	/**
	 * Flatten a nested definition array into dot-named leaf tokens.
	 *
	 * Structured entries (arrays with an 'extends' key) are preserved as
	 * unresolved references; scalars are leaf values.
	 *
	 * @param array<string, mixed> $definition Nested definition (group => tree).
	 * @param string               $prefix    Accumulated dot-prefix.
	 * @return array<string, mixed> Flat map: 'color.bg' => '#fff', etc.
	 */
	public function parse( array $definition, string $prefix = '' ): array {
		$flat = array();

		foreach ( $definition as $key => $value ) {
			$name = '' === $prefix ? (string) $key : $prefix . '.' . $key;

			if ( is_array( $value ) && ! array_key_exists( 'extends', $value ) ) {
				$flat = array_merge( $flat, $this->parse( $value, $name ) );
				continue;
			}

			$flat[ $name ] = $value;
		}

		return $flat;
	}

	/**
	 * Validate a token name against the canonical pattern.
	 *
	 * @param string $name Dot-separated token name.
	 * @return bool
	 */
	public function is_valid_name( string $name ): bool {
		foreach ( explode( '.', $name ) as $segment ) {
			if ( 1 !== preg_match( self::NAME_PATTERN, $segment ) ) {
				return false;
			}
		}

		return true;
	}
}
