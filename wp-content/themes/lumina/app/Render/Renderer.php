<?php
/**
 * Renderer — orchestrates the render lifecycle.
 *
 * Phase 4 (Render Engine): the full pipeline for one view:
 *
 *   resolve template (TemplateResolver)
 *     → normalize data into a ViewModel
 *       → TemplateEngine::render(template, viewmodel) → HTML string
 *         → optional RenderCache read/write around the whole pipeline
 *
 * The renderer never executes `eval`, never renders user input directly, and
 * never lets a render failure escape unhandled — callers may catch
 * RenderException and fall back to a default block (plan §Phase 4: "never
 * die"). It is bound in the container as `render.renderer` (singleton).
 *
 * @package Lumina\Core\Render
 * @since 0.4.0
 */

declare( strict_types=1 );

namespace Lumina\Core\Render;

/**
 * Render pipeline orchestrator.
 */
class Renderer implements RendererInterface {

	/**
	 * Template engine.
	 *
	 * @var TemplateEngineInterface
	 */
	private TemplateEngineInterface $engine;

	/**
	 * Template resolver.
	 *
	 * @var TemplateResolver
	 */
	private TemplateResolver $resolver;

	/**
	 * Optional render cache.
	 *
	 * @var RenderCache|null
	 */
	private ?RenderCache $cache;

	/**
	 * Build the renderer.
	 *
	 * @param TemplateEngineInterface $engine   Template engine.
	 * @param TemplateResolver        $resolver Template resolver.
	 * @param RenderCache|null        $cache    Optional render cache.
	 */
	public function __construct(
		TemplateEngineInterface $engine,
		TemplateResolver $resolver,
		?RenderCache $cache = null
	) {
		$this->engine   = $engine;
		$this->resolver = $resolver;
		$this->cache    = $cache;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @param string               $view View slug (dots separate directories).
	 * @param array<string, mixed> $data Render data.
	 * @return string
	 * @throws RenderException When the view cannot be resolved or rendered.
	 */
	public function render( string $view, array $data = array() ): string {
		if ( null !== $this->cache && $this->cache->enabled() ) {
			$hit = $this->cache->get( $view, $data );

			if ( null !== $hit ) {
				return $hit;
			}
		}

		$template = $this->resolver->resolve( $view, $this->resolver_context() );

		if ( null === $template ) {
			throw new RenderException(
				// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped -- developer-facing message, not HTML.
				sprintf( 'View not resolvable: %s', $view )
			);
		}

		$html = $this->engine->render( $template, new ViewModel( $data ) );

		if ( null !== $this->cache && $this->cache->enabled() ) {
			$this->cache->put( $view, $data, $html );
		}

		return $html;
	}

	/**
	 * The resolver context for this request.
	 *
	 * Filterable via the `render.resolver_context` config key so child themes
	 * can activate the `templates/{$override}/{$slug}.php` tier. WP-free by
	 * default.
	 *
	 * @return array<string, mixed>
	 */
	private function resolver_context(): array {
		$context = array();

		if ( function_exists( 'apply_filters' ) ) {
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
			$context = (array) apply_filters( 'lumina_render_resolver_context', $context );
		}

		return $context;
	}
}
