<?php
/**
 * Bridge — abstract base for plugin capability adapters.
 *
 * Phase 8 (Plugin Bridges): implements the shared contract — `supports()`
 * over the declared capability list and guarded feature detection helpers.
 * Concrete bridges declare slug/name/is_active/version/capabilities and the
 * capability methods; every vendor call goes through `guard()` first so
 * absent plugins are never touched (ADR-007).
 *
 * @package Lumina\Core\Bridges
 * @since 0.8.0
 */

declare( strict_types=1 );

namespace Lumina\Core\Bridges;

/**
 * Base capability adapter.
 */
abstract class Bridge implements BridgeInterface {

	/**
	 * {@inheritDoc}
	 *
	 * @param string $capability Capability name.
	 * @return bool
	 */
	public function supports( string $capability ): bool {
		return in_array( $capability, $this->capabilities(), true );
	}

	/**
	 * Guarded feature check: true when the class and/or function exists.
	 *
	 * @param string $class_name    Vendor class name (may be '').
	 * @param string $function_name Vendor function name (may be '').
	 * @return bool
	 */
	protected function guard( string $class_name = '', string $function_name = '' ): bool {
		return ( '' !== $class_name && class_exists( $class_name ) )
			|| ( '' !== $function_name && function_exists( $function_name ) );
	}

	/**
	 * Read a vendor version constant when defined.
	 *
	 * @param string $constant Constant name.
	 * @return string
	 */
	protected function constant_version( string $constant ): string {
		return defined( $constant ) ? (string) constant( $constant ) : '';
	}
}
