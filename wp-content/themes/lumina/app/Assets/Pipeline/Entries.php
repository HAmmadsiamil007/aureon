<?php
/**
 * Entries — index of manifest entries (handle → output files).
 *
 * Phase 7 (Asset Pipeline): exposes the entry points the manifest marks with
 * `isEntry: true` (plan: "Entries (maps handle → output)"). Consumers use it
 * to discover what a build actually shipped before enqueuing.
 *
 * @package Lumina\Core\Assets\Pipeline
 * @since 0.7.0
 */

declare( strict_types=1 );

namespace Lumina\Core\Assets\Pipeline;

use Lumina\Core\Assets\ManifestReader;

/**
 * Build entry index.
 */
class Entries {

	/**
	 * Manifest reader.
	 *
	 * @var ManifestReader
	 */
	private ManifestReader $manifest;

	/**
	 * Build the index.
	 *
	 * @param ManifestReader $manifest Manifest reader.
	 */
	public function __construct( ManifestReader $manifest ) {
		$this->manifest = $manifest;
	}

	/**
	 * Entry info for a source.
	 *
	 * @param string $src Input source.
	 * @return array<string, mixed>|null
	 */
	public function entry( string $src ): ?array {
		$info = $this->manifest->get( $src );

		if ( null === $info ) {
			return null;
		}

		return ( isset( $info['isEntry'] ) && true === $info['isEntry'] ) ? $info : null;
	}

	/**
	 * Whether a source is a build entry.
	 *
	 * @param string $src Input source.
	 * @return bool
	 */
	public function has( string $src ): bool {
		return null !== $this->entry( $src );
	}

	/**
	 * All build entries (src → info).
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public function entries(): array {
		$entries = array();

		foreach ( $this->manifest->load() as $src => $info ) {
			if ( isset( $info['isEntry'] ) && true === $info['isEntry'] ) {
				$entries[ $src ] = $info;
			}
		}

		return $entries;
	}
}
