<?php
/**
 * RendererInterface — the public Render Engine contract.
 *
 * Phase 4 (Render Engine): the facade every later subsystem (Components,
 * Templates, shortcodes) uses to produce HTML strings from view names and
 * data. Rendering is side-effect free and returns strings only.
 *
 * @package Phantom\Core\Render
 * @since 0.4.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Render;

/**
 * Renders a view to an HTML string.
 */
interface RendererInterface {

	/**
	 * Render a view with data.
	 *
	 * @param string               $view View slug (dots separate directories).
	 * @param array<string, mixed> $data Render data.
	 * @return string
	 *
	 * @throws RenderException When the view cannot be resolved or rendered.
	 */
	public function render( string $view, array $data = array() ): string;
}
