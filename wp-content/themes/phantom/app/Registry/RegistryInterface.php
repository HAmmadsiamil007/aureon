<?php
/**
 * RegistryInterface — read contract for keyed registries.
 *
 * Phase 2 (Framework Infrastructure): the minimum surface every registry
 * implementation exposes. Writers vary by implementation (ArrayRegistry::set(),
 * DynamicRegistry::register()); readers are uniform so consumers can depend on
 * the interface and swap backends (plan §Phase 2).
 *
 * @package Phantom\Core\Registry
 * @since 0.2.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Registry;

/**
 * Read contract for keyed registries.
 */
interface RegistryInterface {

	/**
	 * Resolve a registered entry.
	 *
	 * @param string $key Entry key.
	 * @return mixed
	 */
	public function get( string $key ): mixed;

	/**
	 * Whether an entry is registered.
	 *
	 * @param string $key Entry key.
	 * @return bool
	 */
	public function has( string $key ): bool;

	/**
	 * All entries.
	 *
	 * @return array<string, mixed>
	 */
	public function all(): array;
}
