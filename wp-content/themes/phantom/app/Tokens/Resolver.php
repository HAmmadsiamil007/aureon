<?php
/**
 * Resolver — resolve token inheritance (extends graph).
 *
 * Phase 3 (Design Token Engine): resolves the flat token map produced by
 * TokenSource, walking 'extends' references (e.g. component.button.bg extends
 * color.accent). Cycles are detected and reported; references to unknown
 * tokens resolve to null so callers can degrade gracefully.
 *
 * @package Phantom\Core\Tokens
 * @since 0.3.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Tokens;

/**
 * Inheritance-aware token resolver.
 */
final class Resolver {

	/**
	 * Resolve every token in a flat map to its final value.
	 *
	 * @param array<string, mixed> $flat Flat token map (may contain extends refs).
	 * @return array<string, mixed> Map of name => resolved scalar|null.
	 */
	public function resolve_all( array $flat ): array {
		$resolved = array();

		foreach ( $flat as $name => $value ) {
			$resolved[ $name ] = $this->resolve( $flat, $name, array() );
		}

		return $resolved;
	}

	/**
	 * Resolve a single token value (inheritance-aware).
	 *
	 * @param array<string, mixed> $flat    Flat token map.
	 * @param string               $name    Token name.
	 * @param string[]             $chain   Names already visited (cycle guard).
	 * @return mixed Resolved scalar, or null for unknown/unresolvable refs.
	 */
	public function resolve( array $flat, string $name, array $chain = array() ): mixed {
		if ( ! array_key_exists( $name, $flat ) ) {
			return null;
		}

		$value = $flat[ $name ];

		if ( ! is_array( $value ) || ! array_key_exists( 'extends', $value ) ) {
			return $value;
		}

		$target = (string) ( $value['extends'] ?? '' );

		if ( '' === $target || in_array( $target, $chain, true ) ) {
			return null; // Cycle or empty reference.
		}

		return $this->resolve( $flat, $target, array_merge( $chain, array( $name ) ) );
	}
}
