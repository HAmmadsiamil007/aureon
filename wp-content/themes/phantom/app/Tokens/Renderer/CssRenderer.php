<?php
/**
 * CssRenderer — emit CSS custom-property blocks for token sets.
 *
 * Phase 3 (Design Token Engine): renders `:root` for the base (default) token
 * set plus a `[data-phantom-theme="<preset>"]` block per non-default preset
 * (e.g. dark). Values are raw CSS-safe scalars from the resolved token map;
 * names were validated by TokenSource/Invariant before reaching here.
 *
 * @package Phantom\Core\Tokens\Renderer
 * @since 0.3.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Tokens\Renderer;

use Phantom\Core\Tokens\TokenFactory;

/**
 * Renders CSS custom-property declarations.
 */
final class CssRenderer {

	/**
	 * Render token sets as CSS custom-property blocks.
	 *
	 * @param array<string, mixed> $default_map Resolved default token map.
	 * @param array<string, mixed> $presets     Map of preset slug => resolved map.
	 * @param string               $scope       CSS variable scope prefix.
	 * @return string CSS text (no trailing newline).
	 */
	public function render( array $default_map, array $presets = array(), string $scope = 'phantom' ): string {
		$factory  = new TokenFactory();
		$blocks   = array();
		$blocks[] = $this->block( ':root', $factory->to_css_map( $default_map, $scope ) );

		foreach ( $presets as $slug => $tokens ) {
			$blocks[] = $this->block(
				sprintf( '[data-phantom-theme="%s"]', $this->sanitize_attr( (string) $slug ) ),
				$factory->to_css_map( $tokens, $scope )
			);
		}

		return implode( "\n\n", $blocks );
	}

	/**
	 * Sanitize an attribute value without requiring WordPress (WP-free smoke).
	 *
	 * Allows the token-name-safe charset: lowercase letters, digits, hyphens.
	 *
	 * @param string $value Raw value.
	 * @return string
	 */
	private function sanitize_attr( string $value ): string {
		return (string) preg_replace( '/[^a-z0-9\-]/', '', $value );
	}

	/**
	 * Render a single selector + declarations block.
	 *
	 * @param string                $selector CSS selector.
	 * @param array<string, string> $vars     CSS var => value.
	 * @return string
	 */
	private function block( string $selector, array $vars ): string {
		if ( array() === $vars ) {
			return $selector . ' {}';
		}

		$lines = array( $selector . ' {' );

		foreach ( $vars as $name => $value ) {
			$lines[] = "\t" . $name . ': ' . $value . ';';
		}

		$lines[] = '}';

		return implode( "\n", $lines );
	}
}
