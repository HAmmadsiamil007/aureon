<?php
/**
 * ManifestReader — reads the Vite build manifest.
 *
 * Phase 7 (Asset Pipeline): parses `assets/dist/manifest.json` (Vite's
 * manifest format: input key → { file, src, isEntry, css, imports }) once per
 * process. A missing/unreadable manifest yields an empty map instead of
 * failing, so the loader degrades to raw URLs during development.
 *
 * @package Phantom\Core\Assets
 * @since 0.7.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Assets;

/**
 * Vite manifest parser.
 */
class ManifestReader {

	/**
	 * Manifest file path.
	 *
	 * @var string
	 */
	private string $path;

	/**
	 * Memoized manifest map.
	 *
	 * @var array<string, array<string, mixed>>|null
	 */
	private ?array $memo = null;

	/**
	 * Build the reader.
	 *
	 * @param string $path Absolute manifest path.
	 */
	public function __construct( string $path ) {
		$this->path = $path;
	}

	/**
	 * The parsed manifest map (memoized; empty on miss).
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public function load(): array {
		if ( null !== $this->memo ) {
			return $this->memo;
		}

		$this->memo = array();

		$path = $this->path;

		// Vite 6 writes the manifest under {outDir}/.vite/manifest.json;
		// earlier versions used {outDir}/manifest.json — probe both.
		if ( ! is_readable( $path ) ) {
			$alternate = dirname( $path ) . '/.vite/' . basename( $path );

			if ( is_readable( $alternate ) ) {
				$path = $alternate;
			}
		}

		if ( is_readable( $path ) ) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- WP-free loader; static build artifact read.
			$contents = file_get_contents( $path );
			// phpcs:ignore WordPress.WP.AlternativeFunctions.json_decode_json_decode -- WP-free loader; static build artifact decode.
			$decoded = is_string( $contents ) ? json_decode( $contents, true ) : null;

			if ( is_array( $decoded ) ) {
				$entries = array();

				foreach ( $decoded as $src => $info ) {
					if ( is_string( $src ) && is_array( $info ) ) {
						$entries[ $src ] = $info;
					}
				}

				$this->memo = $entries;
			}
		}

		return $this->memo;
	}

	/**
	 * Whether the manifest knows an input source (suffix match tolerated).
	 *
	 * @param string $src Input source (e.g. 'assets-src/ts/main.ts').
	 * @return bool
	 */
	public function has( string $src ): bool {
		return null !== $this->get( $src );
	}

	/**
	 * Entry info for a source (suffix match tolerated).
	 *
	 * @param string $src Input source.
	 * @return array<string, mixed>|null
	 */
	public function get( string $src ): ?array {
		$manifest = $this->load();

		if ( isset( $manifest[ $src ] ) ) {
			return $manifest[ $src ];
		}

		$suffix = '/' . ltrim( $src, '/' );

		foreach ( $manifest as $key => $info ) {
			if ( str_ends_with( $key, $suffix ) ) {
				return $info;
			}
		}

		return null;
	}

	/**
	 * The hashed output file for a source.
	 *
	 * @param string $src Input source.
	 * @return string|null
	 */
	public function file( string $src ): ?string {
		$info = $this->get( $src );

		return isset( $info['file'] ) && is_string( $info['file'] ) ? $info['file'] : null;
	}

	/**
	 * The CSS files an entry pulls in (its own plus imported entries' CSS).
	 *
	 * @param string $src Input source.
	 * @return list<string>
	 */
	public function css( string $src ): array {
		$files = array();
		$info  = $this->get( $src );

		if ( null === $info ) {
			return $files;
		}

		if ( isset( $info['css'] ) && is_array( $info['css'] ) ) {
			foreach ( $info['css'] as $css ) {
				if ( is_string( $css ) && '' !== $css ) {
					$files[] = $css;
				}
			}
		}

		foreach ( $this->imports( $info ) as $import ) {
			$import_info = $this->get( $import );

			if ( null !== $import_info && isset( $import_info['css'] ) && is_array( $import_info['css'] ) ) {
				foreach ( $import_info['css'] as $css ) {
					if ( is_string( $css ) && '' !== $css ) {
						$files[] = $css;
					}
				}
			}
		}

		return array_values( array_unique( $files ) );
	}

	/**
	 * Direct imports of an entry info array.
	 *
	 * @param array<string, mixed> $info Entry info.
	 * @return list<string>
	 */
	public function imports( array $info ): array {
		$imports = array();

		if ( isset( $info['imports'] ) && is_array( $info['imports'] ) ) {
			foreach ( $info['imports'] as $import ) {
				if ( is_string( $import ) ) {
					$imports[] = $import;
				}
			}
		}

		return $imports;
	}
}
