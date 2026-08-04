<?php
/**
 * Composer — template → component composition engine.
 *
 * Phase 12 (Frontend Template Library): the composition contract between a
 * template slug and the Component Registry. Each slug maps to an ordered
 * region → component sequence (canonical `app/Templates/config/maps.php`,
 * overridable per template). `compose()` renders every entry through the
 * Registry — a template never contains duplicated markup, hardcoded business
 * logic, or direct WordPress/WooCommerce calls (plan §Phase 12 acceptance:
 * "each references only registry components; Woo pages use WooBridge only").
 *
 * Props may be static arrays or callables `fn(array $data): array` resolved
 * lazily with the request data — so templates stay data-driven and WP-free
 * CLI-verifiable (smoke-phase12 drives compose() without WordPress).
 *
 * @package Lumina\Core\Templates
 * @since 0.12.0
 */

declare( strict_types=1 );

namespace Lumina\Core\Templates;

/**
 * Renders template compositions from registry components.
 */
final class Composer {

	/**
	 * Renders a component through the registry.
	 *
	 * @var callable(string, array<string, mixed>): string
	 */
	private $render;

	/**
	 * Template slug → region → component sequence map.
	 *
	 * @var array<string, array<string, list<array<string, mixed>>>>
	 */
	private array $maps;

	/**
	 * Build the composer.
	 *
	 * @param callable $render Component renderer callable.
	 * @param array    $maps   Template composition maps.
	 */
	public function __construct( callable $render, array $maps ) {
		$this->render = $render;
		$this->maps   = $maps;
	}

	/**
	 * Whether a template slug has a composition map.
	 *
	 * @param string $slug Template slug.
	 * @return bool
	 */
	public function has( string $slug ): bool {
		return isset( $this->maps[ $slug ] );
	}

	/**
	 * Registered template slugs.
	 *
	 * @return list<string>
	 */
	public function slugs(): array {
		return array_keys( $this->maps );
	}

	/**
	 * Render a full template composition.
	 *
	 * @param string               $slug Template slug.
	 * @param array<string, mixed> $data Request data (posts, products, …).
	 * @return string
	 */
	public function compose( string $slug, array $data = array() ): string {
		$output = '';

		foreach ( $this->regions( $slug, $data ) as $region_html ) {
			$output .= $region_html;
		}

		return $output;
	}

	/**
	 * Render each region of a template composition.
	 *
	 * @param string               $slug Template slug.
	 * @param array<string, mixed> $data Request data.
	 * @return array<string, string> Region name → rendered HTML.
	 */
	public function regions( string $slug, array $data = array() ): array {
		$regions = $this->maps[ $slug ] ?? array();
		$html    = array();

		foreach ( $regions as $region => $entries ) {
			if ( ! is_array( $entries ) ) {
				continue;
			}

			$html[ $region ] = '';

			foreach ( $entries as $entry ) {
				$html[ $region ] .= $this->render_entry( $entry, $data );
			}
		}

		return $html;
	}

	/**
	 * Render a single map entry (component + resolved props).
	 *
	 * @param array<string, mixed> $entry Map entry.
	 * @param array<string, mixed> $data  Request data.
	 * @return string
	 */
	private function render_entry( array $entry, array $data ): string {
		$component = isset( $entry['component'] ) && is_string( $entry['component'] )
			? $entry['component']
			: '';

		if ( '' === $component ) {
			return '';
		}

		$props = $entry['props'] ?? array();

		// Arrays are props as-is (checked first so an array that happens to
		// look callable-shaped is never invoked); callables resolve lazily.
		if ( is_array( $props ) ) {
			$props = $props;
		} elseif ( is_callable( $props ) ) {
			$resolved = $props( $data );
			$props    = is_array( $resolved ) ? $resolved : array();
		} else {
			$props = array();
		}

		return (string) ( $this->render )( $component, $props );
	}
}
