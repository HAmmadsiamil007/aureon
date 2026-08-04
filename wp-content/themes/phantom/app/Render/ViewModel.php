<?php
/**
 * ViewModel — the plain, immutable data bag delivered to templates.
 *
 * Phase 4 (Render Engine): the contract between data adapters and renderers.
 * Adapters normalize vendor/WP data into ViewModels; templates read them via
 * get()/all(); ViewContext decorates a ViewModel with escaping helpers for
 * template authors. ViewModels are intentionally free of WordPress globals so
 * the render path stays testable without a live install (plan §Phase 4).
 *
 * @package Phantom\Core\Render
 * @since 0.4.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Render;

/**
 * Immutable data bag (constructed from an array; values are copied on write).
 */
class ViewModel {

	/**
	 * Backing data.
	 *
	 * @var array<string, mixed>
	 */
	private array $data;

	/**
	 * Build the bag from a data map.
	 *
	 * @param array<string, mixed> $data Normalized key/value pairs.
	 */
	public function __construct( array $data = array() ) {
		$this->data = $data;
	}

	/**
	 * Whether a key exists.
	 *
	 * @param string $key Dot-notation key.
	 * @return bool
	 */
	public function has( string $key ): bool {
		return null !== $this->read( $key );
	}

	/**
	 * Read a key (dot notation), or a fallback when absent.
	 *
	 * @param string $key      Dot-notation key.
	 * @param mixed  $fallback Fallback value.
	 * @return mixed
	 */
	public function get( string $key, mixed $fallback = null ): mixed {
		$value = $this->read( $key );

		return null === $value ? $fallback : $value;
	}

	/**
	 * Return the full data map.
	 *
	 * @return array<string, mixed>
	 */
	public function all(): array {
		return $this->data;
	}

	/**
	 * Produce a new ViewModel with a key overridden (immutability).
	 *
	 * @param string $key   Dot-notation key.
	 * @param mixed  $value Replacement value.
	 * @return ViewModel
	 */
	public function with( string $key, mixed $value ): ViewModel {
		$copy = $this->data;
		$this->write( $copy, $key, $value );

		return new self( $copy );
	}

	/**
	 * Read a dot-notation key without returning "explicit null" confusion.
	 *
	 * @param string $key Key to read.
	 * @return mixed|null Null when absent.
	 */
	private function read( string $key ): mixed {
		$segments = explode( '.', $key );
		$cursor   = $this->data;

		foreach ( $segments as $segment ) {
			if ( ! is_array( $cursor ) || ! array_key_exists( $segment, $cursor ) ) {
				return null;
			}

			$cursor = $cursor[ $segment ];
		}

		return $cursor;
	}

	/**
	 * Write a dot-notation key into a reference array.
	 *
	 * @param array<string, mixed> $data  Target array (by reference).
	 * @param string               $key   Dot-notation key.
	 * @param mixed                $value Value to set.
	 * @return void
	 */
	private function write( array &$data, string $key, mixed $value ): void {
		$segments = explode( '.', $key );
		$last     = array_pop( $segments );
		$cursor   = &$data;

		foreach ( $segments as $segment ) {
			if ( ! isset( $cursor[ $segment ] ) || ! is_array( $cursor[ $segment ] ) ) {
				$cursor[ $segment ] = array();
			}

			$cursor = &$cursor[ $segment ];
		}

		$cursor[ (string) $last ] = $value;
	}
}
