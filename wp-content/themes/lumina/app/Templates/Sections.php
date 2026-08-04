<?php
/**
 * Sections — dynamic section registry for region-based composition.
 *
 * Phase 6 (Template System): plugins and themes register renderables against
 * named regions (`register('loop', …)`); templates render a region with
 * `render('loop')` so third-party sections emerge in order without template
 * edits (plan §Phase 6 "Dynamic sections"). A renderable is a view slug
 * (rendered through the Phase-4 renderer) or a callable returning a string.
 *
 * @package Lumina\Core\Templates
 * @since 0.6.0
 */

declare( strict_types=1 );

namespace Lumina\Core\Templates;

/**
 * Ordered renderables per region.
 */
class Sections {

	/**
	 * Renders a view slug with args into an HTML string.
	 *
	 * @var callable(string, array<string, mixed>): string
	 */
	private $renderer;

	/**
	 * Region → list of renderables.
	 *
	 * @var array<string, list<string|callable>>
	 */
	private array $regions = array();

	/**
	 * Build the sections registry.
	 *
	 * @param callable $renderer Renderer callable (view slug → HTML).
	 */
	public function __construct( callable $renderer ) {
		$this->renderer = $renderer;
	}

	/**
	 * Register a renderable against a region (appended in call order).
	 *
	 * @param string          $region     Region name.
	 * @param string|callable $renderable View slug or callable returning HTML.
	 * @return void
	 */
	public function register( string $region, string|callable $renderable ): void {
		$region = $this->normalize( $region );

		if ( ! isset( $this->regions[ $region ] ) ) {
			$this->regions[ $region ] = array();
		}

		$this->regions[ $region ][] = $renderable;
	}

	/**
	 * Whether a region has registered renderables.
	 *
	 * @param string $region Region name.
	 * @return bool
	 */
	public function has( string $region ): bool {
		return ! empty( $this->regions[ $this->normalize( $region ) ] );
	}

	/**
	 * Render every renderable registered against a region, in order.
	 *
	 * @param string $region Region name.
	 * @return string
	 */
	public function render( string $region ): string {
		$region      = $this->normalize( $region );
		$renderables = $this->regions[ $region ] ?? array();
		$output      = '';

		foreach ( $renderables as $renderable ) {
			if ( is_string( $renderable ) ) {
				$html = ( $this->renderer )( $renderable, array() );

				if ( is_string( $html ) ) {
					$output .= $html;
				}
			} elseif ( is_callable( $renderable ) ) {
				$html = $renderable( array() );

				if ( is_string( $html ) ) {
					$output .= $html;
				}
			}
		}

		return $output;
	}

	/**
	 * Clear all renderables from a region (idempotent).
	 *
	 * @param string $region Region name.
	 * @return void
	 */
	public function clear( string $region ): void {
		unset( $this->regions[ $this->normalize( $region ) ] );
	}

	/**
	 * Normalize a region name.
	 *
	 * @param string $region Raw region name.
	 * @return string
	 */
	private function normalize( string $region ): string {
		return strtolower( trim( $region ) );
	}
}
