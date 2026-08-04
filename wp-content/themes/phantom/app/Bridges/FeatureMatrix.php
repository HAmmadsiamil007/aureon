<?php
/**
 * FeatureMatrix — the supported plugin capability matrix.
 *
 * Phase 8 (Plugin Bridges): structured source of truth for which plugins the
 * framework bridges and which capabilities each exposes. Reads
 * `app/Bridges/config/plugins.php` (typed, parseable) — `docs/plugins.md`
 * mirrors it for humans. Consumers use it to enumerate/verify the surface
 * without instantiating bridges.
 *
 * @package Phantom\Core\Bridges
 * @since 0.8.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Bridges;

/**
 * Plugin matrix accessor.
 */
class FeatureMatrix {

	/**
	 * Matrix path.
	 *
	 * @var string
	 */
	private string $path;

	/**
	 * Memoized matrix.
	 *
	 * @var array<string, array<string, mixed>>|null
	 */
	private ?array $memo = null;

	/**
	 * Build the matrix accessor.
	 *
	 * @param string $path Absolute path to config/plugins.php.
	 */
	public function __construct( string $path ) {
		$this->path = $path;
	}

	/**
	 * The full matrix (slug → definition).
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public function load(): array {
		if ( null !== $this->memo ) {
			return $this->memo;
		}

		$this->memo = array();

		if ( is_readable( $this->path ) ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_include -- static config include.
			$definitions = require $this->path;

			if ( is_array( $definitions ) ) {
				$matrix = array();

				foreach ( $definitions as $slug => $definition ) {
					if ( is_string( $slug ) && is_array( $definition ) ) {
						$matrix[ $slug ] = $definition;
					}
				}

				$this->memo = $matrix;
			}
		}

		return $this->memo;
	}

	/**
	 * Whether a slug is in the matrix.
	 *
	 * @param string $slug Bridge slug.
	 * @return bool
	 */
	public function has( string $slug ): bool {
		return isset( $this->load()[ $slug ] );
	}

	/**
	 * All matrix slugs.
	 *
	 * @return list<string>
	 */
	public function slugs(): array {
		return array_keys( $this->load() );
	}

	/**
	 * A matrix definition.
	 *
	 * @param string $slug Bridge slug.
	 * @return array<string, mixed>|null
	 */
	public function definition( string $slug ): ?array {
		$matrix = $this->load();

		return $matrix[ $slug ] ?? null;
	}
}
