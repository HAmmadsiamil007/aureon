<?php
/**
 * FactoryInterface — object construction contract.
 *
 * Phase 2 (Framework Infrastructure): a uniform way for subsystems to build
 * objects without hard-coding `new`. Implementations may back onto the
 * container, a callable map, or any custom strategy.
 *
 * @package Phantom\Core\Factory
 * @since 0.2.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Factory;

/**
 * Contract for object factories.
 */
interface FactoryInterface {

	/**
	 * Build an object/instance for an abstract name.
	 *
	 * @param string               $abstract_id Abstract name (class, key, or id).
	 * @param array<string, mixed> $args        Optional construction args.
	 * @return mixed
	 */
	public function make( string $abstract_id, array $args = array() ): mixed;
}
