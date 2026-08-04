<?php
/**
 * PhpTemplateEngine — the default (zero-dependency) template engine.
 *
 * Phase 4 (Render Engine): renders native PHP template files against a
 * ViewModel. The template scope receives a ViewContext (`$view`) with escaping
 * helpers plus the raw data map (`$data`), so template authors escape every
 * field without reaching for globals. Output is captured via an output buffer
 * that is always cleaned on failure, so a broken template can never corrupt
 * the page (plan §Phase 4 error handling).
 *
 * The engine honours `render.engine` = "php"; a Twig engine may be added later
 * behind TemplateEngineInterface without touching the Renderer (ADR-009 keeps
 * the child theme free of runtime PHP dependencies).
 *
 * @package Lumina\Core\Render
 * @since 0.4.0
 */

declare( strict_types=1 );

namespace Lumina\Core\Render;

/**
 * Native PHP template engine.
 */
class PhpTemplateEngine implements TemplateEngineInterface {

	/**
	 * {@inheritDoc}
	 *
	 * @param string $template Template path.
	 * @return bool
	 */
	public function supports( string $template ): bool {
		return str_ends_with( strtolower( $template ), '.php' );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @param string    $template Template path.
	 * @param ViewModel $view     View model.
	 * @return string
	 * @throws RenderException When the template cannot be read or rendered.
	 */
	public function render( string $template, ViewModel $view ): string {
		if ( ! $this->supports( $template ) || ! is_readable( $template ) ) {
			throw new RenderException(
				// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped -- developer-facing message, not HTML.
				sprintf( 'Template not readable: %s', $template )
			);
		}

		$context = new ViewContext( $view );
		$data    = $view->all();

		ob_start();

		try {
			// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions -- include is the only sane way to execute a PHP template in an isolated scope.
			( static function ( string $__template, ViewContext $__view, array $__data ): void {
				$view = $__view; // Escaping-aware context for template authors.
				$data = $__data; // Raw data map for advanced templates.
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_include -- template inclusion is the engine's purpose.
				include $__template;
			} )( $template, $context, $data );
		} catch ( \Throwable $throwable ) {
			ob_end_clean();

			// phpcs:disable WordPress.Security.EscapeOutput -- developer-facing exception message, never rendered to a page.
			throw new RenderException(
				sprintf( 'Template render failed: %s (%s)', $template, $throwable->getMessage() ),
				0,
				$throwable
			);
			// phpcs:enable WordPress.Security.EscapeOutput
		}

		return (string) ob_get_clean();
	}
}
