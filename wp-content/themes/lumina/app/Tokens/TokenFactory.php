<?php
/**
 * TokenFactory — convert resolved tokens to a CSS custom-property map.
 *
 * Phase 3 (Design Token Engine): turns the resolved flat token map into
 * CSS variable assignments: token 'color.bg' with scope 'lumina' →
 * '--lumina-color-bg'. Scope defaults to the project prefix (ADR-002).
 *
 * @package Lumina\Core\Tokens
 * @since 0.3.0
 */

declare( strict_types=1 );

namespace Lumina\Core\Tokens;

/**
 * Converts resolved tokens to CSS variable names/values.
 */
final class TokenFactory {

	/**
	 * Build the CSS variable map for a resolved token set.
	 *
	 * @param array<string, mixed> $resolved Resolved flat token map.
	 * @param string               $scope    CSS variable scope prefix.
	 * @return array<string, string> CSS var name => value.
	 */
	public function to_css_map( array $resolved, string $scope = 'lumina' ): array {
		$map = array();

		foreach ( $resolved as $name => $value ) {
			if ( null === $value ) {
				continue; // Unresolvable references are omitted.
			}

			$map[ $this->var_name( $name, $scope ) ] = (string) $value;
		}

		return $map;
	}

	/**
	 * Build a CSS custom-property name from a token name.
	 *
	 * @param string $token Token name (dots become hyphens).
	 * @param string $scope CSS scope prefix.
	 * @return string
	 */
	public function var_name( string $token, string $scope = 'lumina' ): string {
		return '--' . $scope . '-' . str_replace( '.', '-', $token );
	}
}
