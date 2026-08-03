<?php
/**
 * ConfigLoader — loads the immutable configuration array.
 *
 * Phase 1 (Bootstrap): merges the PHP defaults (app/Config/config.php) with
 * per-environment overrides from `phantom.env.json` (ADR-011). The result is a
 * plain immutable array consumed by the Kernel and App::get().
 *
 * Security: only a single, whitelisted JSON file is read; `../` traversal in
 * the env path is rejected; values are never executed.
 *
 * @package Phantom\Core\Config
 * @since 0.1.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Config;

/**
 * Loads and merges Phantom configuration.
 */
final class ConfigLoader {

	/**
	 * Theme root path (parent of app/).
	 *
	 * @var string
	 */
	private string $base_dir;

	/**
	 * Default config file (relative to base dir).
	 *
	 * @var string
	 */
	private const DEFAULTS_FILE = 'app/Config/config.php';

	/**
	 * Environment override file (relative to base dir).
	 *
	 * @var string
	 */
	private const ENV_FILE = 'phantom.env.json';

	/**
	 * Constructor.
	 *
	 * @param string $base_dir Theme root directory (parent of app/).
	 */
	public function __construct( string $base_dir ) {
		$this->base_dir = rtrim( $base_dir, '/\\' );
	}

	/**
	 * Load and merge the full configuration array.
	 *
	 * @return array<string, mixed>
	 */
	public function load(): array {
		$defaults  = $this->load_defaults();
		$overrides = $this->load_env_overrides();

		return $this->merge( $defaults, $overrides );
	}

	/**
	 * Read the PHP defaults file.
	 *
	 * @return array<string, mixed>
	 */
	private function load_defaults(): array {
		$file = $this->base_dir . '/' . self::DEFAULTS_FILE;

		if ( ! is_readable( $file ) ) {
			return array();
		}

		$config = require $file;

		return is_array( $config ) ? $config : array();
	}

	/**
	 * Read and decode the optional phantom.env.json overrides.
	 *
	 * @return array<string, mixed>
	 */
	private function load_env_overrides(): array {
		$path = $this->base_dir . '/' . self::ENV_FILE;

		// Reject traversal and hidden files outright.
		if ( ! $this->is_safe_path( $path ) || ! is_readable( $path ) ) {
			return array();
		}

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$json = file_get_contents( $path );

		if ( false === $json ) {
			return array();
		}

		$decoded = json_decode( $json, true );

		return is_array( $decoded ) ? $decoded : array();
	}

	/**
	 * Deep-merge config arrays (overrides win).
	 *
	 * @param array<string, mixed> $defaults  Base config.
	 * @param array<string, mixed> $overrides Overrides.
	 * @return array<string, mixed>
	 */
	private function merge( array $defaults, array $overrides ): array {
		foreach ( $overrides as $key => $value ) {
			if ( isset( $defaults[ $key ] ) && is_array( $defaults[ $key ] ) && is_array( $value ) ) {
				$defaults[ $key ] = $this->merge( $defaults[ $key ], $value );
			} else {
				$defaults[ $key ] = $value;
			}
		}

		return $defaults;
	}

	/**
	 * Ensure a resolved path stays inside the base directory (no ../ escape).
	 *
	 * @param string $path Absolute path candidate.
	 * @return bool
	 */
	private function is_safe_path( string $path ): bool {
		$real_base = realpath( $this->base_dir );
		$real_path = realpath( $path );

		if ( false === $real_base || false === $real_path ) {
			return false;
		}

		$prefix = $real_base . DIRECTORY_SEPARATOR;

		return str_starts_with( $real_path, $prefix ) || $real_path === $real_base;
	}
}
