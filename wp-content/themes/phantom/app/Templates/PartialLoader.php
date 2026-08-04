<?php
/**
 * PartialLoader — renders template partials with a safe fallback chain.
 *
 * Phase 6 (Template System): partials live under `templates/partials/`.
 * `partial('content-single', $args)` resolves `content-single` through the
 * Render engine resolver (override → base → wp-{name}); when nothing matches,
 * the fallback partial (`index` by default) is used; when even the fallback
 * is missing, a RenderException is thrown so callers can catch and degrade —
 * the partial path never silently outputs nothing on a real miss (plan
 * §Phase 6 "missing partial throws-aware fallback to index").
 *
 * @package Phantom\Core\Templates
 * @since 0.6.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Templates;

use Phantom\Core\Render\RenderException;
use Phantom\Core\Render\TemplateResolver;

/**
 * Partial rendering with fallback.
 */
class PartialLoader {

	/**
	 * Resolver scoped to the partials directory (override → base → wp-{name}).
	 *
	 * @var TemplateResolver
	 */
	private TemplateResolver $resolver;

	/**
	 * Renders a resolved partial file path with args into an HTML string.
	 *
	 * @var callable(string, array<string, mixed>): string
	 */
	private $renderer;

	/**
	 * Build the partial loader.
	 *
	 * @param TemplateResolver $resolver Partial resolver (scoped to partials dir).
	 * @param callable         $renderer Renderer callable (file path + args → HTML).
	 */
	public function __construct(
		TemplateResolver $resolver,
		callable $renderer
	) {
		$this->resolver = $resolver;
		$this->renderer = $renderer;
	}

	/**
	 * Render a partial, falling back to `$fallback` when it cannot be resolved.
	 *
	 * @param string               $name     Partial name (dots separate dirs).
	 * @param array<string, mixed> $args     Partial data.
	 * @param string|null          $fallback Fallback partial name (default 'index').
	 * @return string
	 * @throws RenderException When the partial and its fallback both miss.
	 */
	public function partial( string $name, array $args = array(), ?string $fallback = 'index' ): string {
		$file = $this->resolver->resolve( $name, array() );

		if ( null === $file && null !== $fallback ) {
			$file = $this->resolver->resolve( $fallback, array() );
		}

		if ( null === $file ) {
			throw new RenderException(
				// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped -- developer-facing message, not HTML.
				sprintf( 'Partial not resolvable: %s', $name )
			);
		}

		$html = ( $this->renderer )( $file, $args );

		// The renderer callable is declared string-returning; the cast absorbs
		// a defensive non-string result without breaking the contract.
		return (string) $html;
	}
}
