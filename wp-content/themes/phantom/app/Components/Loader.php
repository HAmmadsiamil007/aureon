<?php
/**
 * Loader — JSON component discovery.
 *
 * Phase 5 (Component Registry): reads `components.json` files (one per
 * instance, per the plan) and returns the raw definition arrays they contain.
 * Files are read once per process (memoized); a missing or unreadable file
 * yields no definitions rather than failing, so optional per-instance files
 * cannot break the registry.
 *
 * @package Phantom\Core\Components
 * @since 0.5.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Components;

/**
 * Loads component definitions from JSON files.
 */
final class Loader {

	/**
	 * Memoized raw definitions per file path.
	 *
	 * @var array<string, list<array<string, mixed>>>
	 */
	private array $memo = array();

	/**
	 * Load definitions from one JSON file.
	 *
	 * @param string $path Absolute path to a components.json file.
	 * @return list<array<string, mixed>> Raw definitions (empty on miss).
	 */
	public function load_file( string $path ): array {
		if ( isset( $this->memo[ $path ] ) ) {
			return $this->memo[ $path ];
		}

		$definitions = array();

		if ( is_readable( $path ) ) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- WP-free loader; static config read.
			$contents = file_get_contents( $path );
			// phpcs:ignore WordPress.WP.AlternativeFunctions.json_decode_json_decode -- WP-free loader; json_decode() is the only decoder here.
			$decoded = is_string( $contents ) ? json_decode( $contents, true ) : null;

			if ( is_array( $decoded ) && isset( $decoded['components'] ) && is_array( $decoded['components'] ) ) {
				$definitions = array_values( $decoded['components'] );
			}
		}

		$this->memo[ $path ] = $definitions;

		return $definitions;
	}

	/**
	 * Load and merge definitions across several files.
	 *
	 * Later files win on duplicate component names (PHP registration still
	 * wins over all JSON — see Registry::register()).
	 *
	 * @param array<int, string> $paths Absolute file paths.
	 * @return list<array<string, mixed>> Merged raw definitions.
	 */
	public function load( array $paths ): array {
		$merged = array();

		foreach ( $paths as $path ) {
			foreach ( $this->load_file( $path ) as $definition ) {
				if ( isset( $definition['name'] ) && is_string( $definition['name'] ) ) {
					$merged[ $definition['name'] ] = $definition;
				}
			}
		}

		return array_values( $merged );
	}
}
