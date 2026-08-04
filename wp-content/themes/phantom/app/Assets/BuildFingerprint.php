<?php
/**
 * BuildFingerprint — deterministic build identity for cache busting.
 *
 * Phase 7 (Asset Pipeline): the fingerprint is the md5 of the manifest file
 * contents (hashed filenames change per build, so the manifest hash is a
 * stable per-build token). Falls back to the literal `dev` when no build is
 * present so URLs stay deterministic in dev/WP-free contexts.
 *
 * @package Phantom\Core\Assets
 * @since 0.7.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Assets;

/**
 * Build identity token.
 */
class BuildFingerprint {

	/**
	 * Manifest reader.
	 *
	 * @var ManifestReader
	 */
	private ManifestReader $manifest;

	/**
	 * Memoized token.
	 *
	 * @var string|null
	 */
	private ?string $memo = null;

	/**
	 * Build the fingerprint.
	 *
	 * @param ManifestReader $manifest Manifest reader.
	 */
	public function __construct( ManifestReader $manifest ) {
		$this->manifest = $manifest;
	}

	/**
	 * The build token.
	 *
	 * @return string
	 */
	public function token(): string {
		if ( null !== $this->memo ) {
			return $this->memo;
		}

		$map   = $this->manifest->load();
		$token = 'dev';

		if ( array() !== $map ) {
			// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize -- internal cache-key seed, never user data.
			$token = md5( serialize( $map ) );
		}

		$this->memo = $token;

		return $token;
	}
}
