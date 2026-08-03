<?php
/**
 * ArrayRegistry — simple in-memory keyed registry.
 *
 * Phase 2 (Framework Infrastructure): stores entries in a plain array. Values
 * are returned as-is (no lazy resolution) — the registry equivalent of a
 * container set(). Use DynamicRegistry when entries are factories.
 *
 * @package Phantom\Core\Registry
 * @since 0.2.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Registry;

/**
 * In-memory array registry.
 */
final class ArrayRegistry implements RegistryInterface {

	/**
	 * Entries keyed by entry key.
	 *
	 * @var array<string, mixed>
	 */
	private array $entries = array();

	/**
	 * Store an entry.
	 *
	 * @param string $key   Entry key.
	 * @param mixed  $value Entry value.
	 * @return void
	 */
	public function set( string $key, mixed $value ): void {
		$this->entries[ $key ] = $value;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @param string $key Entry key.
	 */
	public function get( string $key ): mixed {
		return $this->entries[ $key ] ?? null;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @param string $key Entry key.
	 */
	public function has( string $key ): bool {
		return array_key_exists( $key, $this->entries );
	}

	/**
	 * {@inheritDoc}
	 */
	public function all(): array {
		return $this->entries;
	}
}
