<?php
/**
 * ComponentDefinition — immutable metadata for one registered component.
 *
 * Phase 5 (Component Registry): a component is a named, versioned, presentational
 * unit with a renderer view, a data (prop) schema, variants, slots,
 * dependencies, and a shortcode slug. Definitions are value objects — they are
 * built once (PHP registration or JSON discovery via DefinitionCompiler) and
 * never mutated, so the Registry can hand them out safely.
 *
 * @package Phantom\Core\Components
 * @since 0.5.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Components;

/**
 * Immutable component metadata bag.
 */
final class ComponentDefinition {

	/**
	 * Component name (registry key, e.g. 'card').
	 *
	 * @var string
	 */
	private string $name;

	/**
	 * Renderer view slug (resolved by the Phase-4 TemplateResolver).
	 *
	 * @var string
	 */
	private string $renderer;

	/**
	 * Shortcode slug (e.g. 'button' for the `[phantom:button]` DSL).
	 *
	 * @var string
	 */
	private string $slug;

	/**
	 * Data (prop) schema: prop name → shape map.
	 *
	 * @var array<string, array<string, mixed>>
	 */
	private array $data;

	/**
	 * Variant map: variant name → prop overrides.
	 *
	 * @var array<string, array<string, mixed>>
	 */
	private array $variants;

	/**
	 * Declared slot names (rendered child content passed as trusted HTML).
	 *
	 * @var list<string>
	 */
	private array $slots;

	/**
	 * Dependency component names (must be registered before resolveDependencies()).
	 *
	 * @var list<string>
	 */
	private array $deps;

	/**
	 * Published component version (int, ≥ 1).
	 *
	 * @var int
	 */
	private int $version;

	/**
	 * Build an immutable definition.
	 *
	 * @param string               $name     Component name.
	 * @param string               $renderer Renderer view slug.
	 * @param string               $slug     Shortcode slug.
	 * @param array<string, mixed> $data     Prop schema.
	 * @param array<string, mixed> $variants Variant prop overrides.
	 * @param array<int, string>   $slots    Declared slot names.
	 * @param array<int, string>   $deps     Dependency component names.
	 * @param int                  $version  Published version (≥ 1).
	 */
	public function __construct(
		string $name,
		string $renderer,
		string $slug,
		array $data = array(),
		array $variants = array(),
		array $slots = array(),
		array $deps = array(),
		int $version = 1
	) {
		$this->name     = $name;
		$this->renderer = $renderer;
		$this->slug     = $slug;
		$this->data     = $data;
		$this->variants = $variants;
		$this->slots    = array_values( $slots );
		$this->deps     = array_values( $deps );
		$this->version  = max( 1, $version );
	}

	/**
	 * Component name.
	 *
	 * @return string
	 */
	public function name(): string {
		return $this->name;
	}

	/**
	 * Renderer view slug.
	 *
	 * @return string
	 */
	public function renderer(): string {
		return $this->renderer;
	}

	/**
	 * Shortcode slug.
	 *
	 * @return string
	 */
	public function slug(): string {
		return $this->slug;
	}

	/**
	 * Prop schema.
	 *
	 * @return array<string, mixed>
	 */
	public function data(): array {
		return $this->data;
	}

	/**
	 * Variant prop overrides.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public function variants(): array {
		return $this->variants;
	}

	/**
	 * Declared slot names.
	 *
	 * @return list<string>
	 */
	public function slots(): array {
		return $this->slots;
	}

	/**
	 * Dependency component names.
	 *
	 * @return list<string>
	 */
	public function deps(): array {
		return $this->deps;
	}

	/**
	 * Published version.
	 *
	 * @return int
	 */
	public function version(): int {
		return $this->version;
	}

	/**
	 * Whether a named variant exists.
	 *
	 * @param string $name Variant name.
	 * @return bool
	 */
	public function has_variant( string $name ): bool {
		return isset( $this->variants[ $name ] );
	}
}
