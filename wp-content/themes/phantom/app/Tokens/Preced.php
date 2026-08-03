<?php
/**
 * Preced — precedence collector (default → preset → override).
 *
 * Phase 3 (Design Token Engine): merges token layers in fixed precedence
 * order. Later layers win on key conflicts; 'extends' references from any
 * layer resolve against the merged map (so a preset may override the target
 * of a component alias).
 *
 * @package Phantom\Core\Tokens
 * @since 0.3.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Tokens;

/**
 * Collects token layers in precedence order.
 */
final class Preced {

	/**
	 * Merge layers with later-wins precedence.
	 *
	 * @param array<string, mixed> ...$layers Flat token maps in ascending precedence.
	 * @return array<string, mixed>
	 */
	public function collect( array ...$layers ): array {
		$merged = array();

		foreach ( $layers as $layer ) {
			foreach ( $layer as $name => $value ) {
				$merged[ $name ] = $value;
			}
		}

		return $merged;
	}
}
