<?php
/**
 * Registry — the central Component Registry.
 *
 * Phase 5 (Component Registry): registration, discovery, versioning,
 * dependency validation, variant/slot resolution, rendering, and the
 * `[lumina:{slug}]` shortcode DSL. Components are pure presentational units:
 * they receive data through props (via Data adapters upstream), never touch
 * WordPress globals, and return strings only.
 *
 * Public contract (plan §Phase 5):
 *   register($name, $renderer, $meta)  Registry::get($name): ?ComponentDefinition
 *   versions($name): array             resolveDependencies(): void
 *   render($name, $props): string      providesSlot($name): bool
 *
 * @package Lumina\Core\Components
 * @since 0.5.0
 */

declare( strict_types=1 );

namespace Lumina\Core\Components;

/**
 * Component registry and render gateway.
 */
class Registry {

	/**
	 * Shortcode DSL prefix: components render as `[lumina:{slug}]` (plan §Phase 5).
	 *
	 * @var string
	 */
	public const SHORTCODE_PREFIX = 'lumina:';

	/**
	 * Renders a view slug with props into an HTML string.
	 *
	 * @var callable(string, array<string, mixed>): string
	 */
	private $renderer;

	/**
	 * Component definitions by name.
	 *
	 * @var array<string, ComponentDefinition>
	 */
	private array $definitions = array();

	/**
	 * Published version history by component name.
	 *
	 * @var array<string, list<int>>
	 */
	private array $versions = array();

	/**
	 * Shortcode tag → component name map (the public `[lumina:{slug}]` DSL).
	 *
	 * @var array<string, string>
	 */
	private array $shortcodes = array();

	/**
	 * Definition compiler.
	 *
	 * @var DefinitionCompiler
	 */
	private DefinitionCompiler $compiler;

	/**
	 * Variant/slot resolver.
	 *
	 * @var Resolver
	 */
	private Resolver $resolver;

	/**
	 * Dependency cycle detector.
	 *
	 * @var CycleDetector
	 */
	private CycleDetector $cycles;

	/**
	 * Build the registry.
	 *
	 * @param callable                $renderer Renderer callable.
	 * @param DefinitionCompiler|null $compiler Definition compiler.
	 * @param Resolver|null           $resolver Variant/slot resolver.
	 * @param CycleDetector|null      $cycles   Cycle detector.
	 */
	public function __construct(
		callable $renderer,
		?DefinitionCompiler $compiler = null,
		?Resolver $resolver = null,
		?CycleDetector $cycles = null
	) {
		$this->renderer = $renderer;
		$this->compiler = $compiler ?? new DefinitionCompiler();
		$this->resolver = $resolver ?? new Resolver();
		$this->cycles   = $cycles ?? new CycleDetector();
	}

	/**
	 * Register (or re-register) a component definition.
	 *
	 * Re-registration replaces the definition and publishes a new version.
	 * The shortcode tag is (re)bound to the latest definition.
	 *
	 * @param string               $name     Component name.
	 * @param string               $renderer Renderer view slug.
	 * @param array<string, mixed> $meta     Definition meta.
	 * @return ComponentDefinition
	 * @throws ComponentException When the definition is invalid.
	 */
	public function register( string $name, string $renderer, array $meta = array() ): ComponentDefinition {
		$definition = $this->compiler->compile( $name, $renderer, $meta );

		$this->definitions[ $name ] = $definition;

		if ( ! isset( $this->versions[ $name ] ) ) {
			$this->versions[ $name ] = array();
		}

		if ( ! in_array( $definition->version(), $this->versions[ $name ], true ) ) {
			$this->versions[ $name ][] = $definition->version();
		}

		$this->shortcodes[ self::SHORTCODE_PREFIX . $definition->slug() ] = $name;

		return $definition;
	}

	/**
	 * Get a registered definition.
	 *
	 * @param string $name Component name.
	 * @return ComponentDefinition|null
	 */
	public function get( string $name ): ?ComponentDefinition {
		return $this->definitions[ $name ] ?? null;
	}

	/**
	 * Whether a component is registered.
	 *
	 * @param string $name Component name.
	 * @return bool
	 */
	public function has( string $name ): bool {
		return isset( $this->definitions[ $name ] );
	}

	/**
	 * All registered definitions, keyed by name.
	 *
	 * @return array<string, ComponentDefinition>
	 */
	public function all(): array {
		return $this->definitions;
	}

	/**
	 * Published version history for a component.
	 *
	 * @param string $name Component name.
	 * @return list<int> Published versions (empty when unregistered).
	 */
	public function versions( string $name ): array {
		return $this->versions[ $name ] ?? array();
	}

	/**
	 * Whether a component declares any slots.
	 *
	 * @param string $name Component name.
	 * @return bool
	 */
	public function provides_slot( string $name ): bool {
		$definition = $this->get( $name );

		return null !== $definition && array() !== $definition->slots();
	}

	/**
	 * Validate the whole dependency graph.
	 *
	 * Throws on cycles (ComponentCycleException) or missing dependencies
	 * (ComponentException). Cheap and idempotent — safe to call per request.
	 *
	 * @return void
	 * @throws ComponentCycleException When a dependency cycle exists.
	 * @throws ComponentException      When a dependency is unregistered.
	 */
	public function resolve_dependencies(): void {
		$graph = array();

		foreach ( $this->definitions as $name => $definition ) {
			$graph[ $name ] = $definition->deps();
		}

		foreach ( $this->cycles->find( $graph ) as $cycle ) {
			throw new ComponentCycleException(
				// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped -- developer-facing message, not HTML.
				sprintf( 'Component dependency cycle detected: %s.', implode( ' -> ', $cycle ) )
			);
		}

		foreach ( $this->definitions as $name => $definition ) {
			foreach ( $definition->deps() as $dep ) {
				if ( ! isset( $this->definitions[ $dep ] ) ) {
					throw new ComponentException(
						// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped -- developer-facing message, not HTML.
						sprintf( 'Component "%s" depends on unregistered component "%s".', $name, $dep )
					);
				}
			}
		}
	}

	/**
	 * Render a component to an HTML string.
	 *
	 * Variants are applied (explicit props win), declared slots are
	 * materialized from child-component lists into trusted HTML, then the
	 * renderer view runs with the final props.
	 *
	 * @param string               $name  Component name.
	 * @param array<string, mixed> $props Render props.
	 * @return string
	 * @throws ComponentNotFoundException When the component is unregistered.
	 */
	public function render( string $name, array $props = array() ): string {
		$definition = $this->get( $name );

		if ( null === $definition ) {
			throw new ComponentNotFoundException(
				// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped -- developer-facing message, not HTML.
				sprintf( 'Unknown component: %s', $name )
			);
		}

		$props = $this->resolver->variant( $definition, $props );

		foreach ( $definition->slots() as $slot ) {
			if ( isset( $props[ $slot ] ) && $this->resolver->has_slot_content( $props[ $slot ] ) ) {
				$props[ $slot ] = $this->render_slot( (array) $props[ $slot ] );
			}
		}

		$html = ( $this->renderer )( $definition->renderer(), $props );

		// The renderer callable is declared string-returning; the cast also
		// absorbs a defensive non-string result without breaking the contract.
		return (string) $html;
	}

	/**
	 * Render slot content: child component lists or pre-rendered HTML strings.
	 *
	 * @param array<int, mixed> $content Slot content.
	 * @return string
	 */
	private function render_slot( array $content ): string {
		$html = '';

		foreach ( $content as $item ) {
			if ( is_array( $item ) && isset( $item['name'] ) && is_string( $item['name'] ) ) {
				$child_props = isset( $item['props'] ) && is_array( $item['props'] ) ? $item['props'] : array();
				$html       .= $this->render( $item['name'], $child_props );
			} elseif ( is_string( $item ) ) {
				$html .= $item;
			}
		}

		return $html;
	}

	/**
	 * All shortcode tags, keyed by tag (slug → component name).
	 *
	 * @return array<string, string>
	 */
	public function shortcodes(): array {
		return $this->shortcodes;
	}

	/**
	 * Render through the public `[lumina:{slug}]` shortcode DSL.
	 *
	 * Unknown tags render empty (never throw on the WordPress surface).
	 * Shortcode attributes arrive as strings; numeric-looking values are cast
	 * so templates can trust prop types.
	 *
	 * @param string               $tag    Shortcode tag (e.g. 'lumina:button').
	 * @param array<string, mixed> $attrs Shortcode attributes.
	 * @return string
	 */
	public function render_shortcode( string $tag, array $attrs = array() ): string {
		if ( ! isset( $this->shortcodes[ $tag ] ) ) {
			return '';
		}

		$props = array();

		foreach ( $attrs as $key => $value ) {
			if ( ! is_string( $key ) ) {
				continue;
			}

			$props[ $key ] = $this->coerce_attr( $value );
		}

		return $this->render( $this->shortcodes[ $tag ], $props );
	}

	/**
	 * Coerce a shortcode attribute string to a scalar prop.
	 *
	 * @param mixed $value Raw attribute value.
	 * @return mixed
	 */
	private function coerce_attr( mixed $value ): mixed {
		if ( ! is_string( $value ) ) {
			return $value;
		}

		if ( 'true' === $value ) {
			return true;
		}

		if ( 'false' === $value ) {
			return false;
		}

		if ( is_numeric( $value ) ) {
			return str_contains( $value, '.' ) ? (float) $value : (int) $value;
		}

		return $value;
	}
}
