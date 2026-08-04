<?php
/**
 * Layout — region-based layout composition buffer.
 *
 * Phase 4 (Render Engine): templates compose pages from named regions
 * (header, main, sidebar, footer, loop, …). Components/templates push
 * renderables into regions and flush them in insertion order:
 *
 *   Layout::push('main', 'partials.card', $args);
 *   echo Layout::flush('main');
 *
 * A block is either a view slug (resolved + rendered via the Renderer) or a
 * callable returning a string. The Layout never touches WordPress globals; it
 * only needs the renderer it was constructed with (plan §Phase 4).
 *
 * @package Phantom\Core\Render
 * @since 0.4.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Render;

/**
 * Ordered, region-based render buffer.
 */
class Layout {

	/**
	 * Renderer used to turn block slugs into HTML.
	 *
	 * @var callable(string, array<string, mixed>): string
	 */
	private $renderer;

	/**
	 * Region buffers: region → list of blocks.
	 *
	 * @var array<string, list<array{0: string|callable, 1: array<string, mixed>}>>
	 */
	private array $regions = array();

	/**
	 * Build the layout buffer.
	 *
	 * @param callable $renderer Renderer callable.
	 */
	public function __construct( callable $renderer ) {
		$this->renderer = $renderer;
	}

	/**
	 * Append a block to a region.
	 *
	 * @param string               $region Region name.
	 * @param string|callable      $block  View slug or callable returning HTML.
	 * @param array<string, mixed> $args   Arguments merged into the block data.
	 * @return void
	 */
	public function push( string $region, string|callable $block, array $args = array() ): void {
		$region = $this->normalize_region( $region );

		if ( ! isset( $this->regions[ $region ] ) ) {
			$this->regions[ $region ] = array();
		}

		$this->regions[ $region ][] = array( $block, $args );
	}

	/**
	 * Whether a region has any queued blocks.
	 *
	 * @param string $region Region name.
	 * @return bool
	 */
	public function has( string $region ): bool {
		return ! empty( $this->regions[ $this->normalize_region( $region ) ] );
	}

	/**
	 * Render and clear a region.
	 *
	 * @param string $region Region name.
	 * @return string
	 */
	public function flush( string $region ): string {
		$region = $this->normalize_region( $region );
		$blocks = $this->regions[ $region ] ?? array();
		unset( $this->regions[ $region ] );

		$output = '';

		foreach ( $blocks as $block ) {
			$output .= $this->render_block( $block[0], $block[1] );
		}

		return $output;
	}

	/**
	 * Render a region without clearing it (repeatable sections).
	 *
	 * @param string $region Region name.
	 * @return string
	 */
	public function render_region( string $region ): string {
		$region = $this->normalize_region( $region );
		$blocks = $this->regions[ $region ] ?? array();
		$output = '';

		foreach ( $blocks as $block ) {
			$output .= $this->render_block( $block[0], $block[1] );
		}

		return $output;
	}

	/**
	 * Render a queued block.
	 *
	 * @param string|callable      $block Block definition.
	 * @param array<string, mixed> $args  Block arguments.
	 * @return string
	 */
	private function render_block( string|callable $block, array $args ): string {
		if ( is_callable( $block ) ) {
			$result = $block( $args );

			return is_string( $result ) ? $result : '';
		}

		return ( $this->renderer )( $block, $args );
	}

	/**
	 * Normalize a region name (trim, lowercase).
	 *
	 * @param string $region Raw region name.
	 * @return string
	 */
	private function normalize_region( string $region ): string {
		return strtolower( trim( $region ) );
	}
}
