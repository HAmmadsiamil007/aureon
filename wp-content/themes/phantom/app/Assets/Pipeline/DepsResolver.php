<?php
/**
 * DepsResolver — resolves the transitive import graph of a manifest entry.
 *
 * Phase 7 (Asset Pipeline): Vite records `imports` per entry. This resolver
 * walks them (cycle-safe, deduped, deterministic order) so the loader can
 * preload/modulepreload the full dependency closure of any entry.
 *
 * @package Phantom\Core\Assets\Pipeline
 * @since 0.7.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Assets\Pipeline;

use Phantom\Core\Assets\ManifestReader;

/**
 * Import-graph resolver.
 */
class DepsResolver {

	/**
	 * Manifest reader.
	 *
	 * @var ManifestReader
	 */
	private ManifestReader $manifest;

	/**
	 * Build the resolver.
	 *
	 * @param ManifestReader $manifest Manifest reader.
	 */
	public function __construct( ManifestReader $manifest ) {
		$this->manifest = $manifest;
	}

	/**
	 * Direct imports of an entry.
	 *
	 * @param string $src Input source.
	 * @return list<string>
	 */
	public function imports( string $src ): array {
		$info = $this->manifest->get( $src );

		return null === $info ? array() : $this->manifest->imports( $info );
	}

	/**
	 * Transitive import closure (deduped, breadth-first, cycle-safe).
	 *
	 * @param string $src Input source.
	 * @return list<string>
	 */
	public function resolve( string $src ): array {
		$resolved = array();
		$queue    = $this->imports( $src );
		$seen     = array( $src => true );

		while ( array() !== $queue ) {
			$current = array_shift( $queue );

			if ( isset( $seen[ $current ] ) ) {
				continue;
			}

			$seen[ $current ] = true;
			$resolved[]       = $current;

			$info = $this->manifest->get( $current );

			if ( null !== $info ) {
				foreach ( $this->manifest->imports( $info ) as $import ) {
					if ( ! isset( $seen[ $import ] ) ) {
						$queue[] = $import;
					}
				}
			}
		}

		return $resolved;
	}
}
