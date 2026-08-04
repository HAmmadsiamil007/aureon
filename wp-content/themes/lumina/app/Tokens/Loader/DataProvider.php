<?php
/**
 * DataProvider — load token + preset definition files.
 *
 * Phase 3 (Design Token Engine): reads the canonical defaults and theme presets
 * from the theme's app/Tokens/config/ directory. Loads once per process and
 * caches the arrays so repeated repository access is zero-I/O.
 *
 * @package Lumina\Core\Tokens\Loader
 * @since 0.3.0
 */

declare( strict_types=1 );

namespace Lumina\Core\Tokens\Loader;

/**
 * Loads token/preset definition files.
 */
final class DataProvider {

	/**
	 * Theme root directory (base for config paths).
	 *
	 * @var string
	 */
	private string $base_dir;

	/**
	 * Loaded default definitions.
	 *
	 * @var array<string, mixed>|null
	 */
	private ?array $tokens = null;

	/**
	 * Loaded presets.
	 *
	 * @var array<string, mixed>|null
	 */
	private ?array $presets = null;

	/**
	 * Constructor.
	 *
	 * @param string $base_dir Theme root (defaults to the lumina theme dir).
	 */
	public function __construct( string $base_dir = '' ) {
		$this->base_dir = '' !== $base_dir
			? rtrim( $base_dir, '/\\' )
			: dirname( __DIR__, 3 );
	}

	/**
	 * Canonical default token definitions.
	 *
	 * @return array<string, mixed>
	 */
	public function tokens(): array {
		if ( null === $this->tokens ) {
			$this->tokens = $this->load( '/app/Tokens/config/tokens.php' );
		}

		return $this->tokens;
	}

	/**
	 * Theme presets.
	 *
	 * @return array<string, mixed>
	 */
	public function presets(): array {
		if ( null === $this->presets ) {
			$this->presets = $this->load( '/app/Tokens/config/presets.php' );
		}

		return $this->presets;
	}

	/**
	 * Load a PHP definition file (returns [] when absent).
	 *
	 * @param string $relative Relative path from the theme root.
	 * @return array<string, mixed>
	 */
	private function load( string $relative ): array {
		$file = $this->base_dir . $relative;

		if ( ! is_file( $file ) ) {
			return array();
		}

		$loaded = require $file;

		return is_array( $loaded ) ? $loaded : array();
	}
}
