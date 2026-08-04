<?php
/**
 * TemplateEngineInterface — the renderer's pluggable engine contract.
 *
 * Phase 4 (Render Engine): abstracts the template syntax so the engine choice
 * stays a configuration decision (`render.engine`, ADR-009 keeps the child
 * theme free of runtime PHP dependencies). The default implementation is the
 * native PHP template engine; a Twig engine can be dropped in later behind the
 * same interface without touching the Renderer.
 *
 * @package Lumina\Core\Render
 * @since 0.4.0
 */

declare( strict_types=1 );

namespace Lumina\Core\Render;

/**
 * Renders a resolved template file against a view model.
 */
interface TemplateEngineInterface {

	/**
	 * Render a template file to a string.
	 *
	 * @param string    $template Absolute path to the template file.
	 * @param ViewModel $view     View model.
	 * @return string
	 *
	 * @throws RenderException When the template cannot be rendered.
	 */
	public function render( string $template, ViewModel $view ): string;

	/**
	 * Whether this engine can render a given template file.
	 *
	 * @param string $template Absolute path to the template file.
	 * @return bool
	 */
	public function supports( string $template ): bool;
}
