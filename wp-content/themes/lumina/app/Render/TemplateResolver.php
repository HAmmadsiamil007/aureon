<?php
/**
 * TemplateResolver — resolves a view slug to a template file path.
 *
 * Phase 4 (Render Engine): the resolution contract between view names and the
 * template system (Phase 6 hardens the WP-hierarchy side). Priority order
 * (most-specific first, plan §Phase 6 override table):
 *
 *   1. templates/{$override}/{$slug}.php
 *   2. templates/{$slug}.php
 *   3. wp-{$slug}.php
 *   4. null (caller falls back to the parent theme / default view)
 *
 * A `.twig` suffix in a view slug is normalized to `.php` when the active
 * engine is the native PHP engine, so `render('card.twig')` resolves to
 * `templates/partials/card.php` — keeping the plan's acceptance-criteria
 * syntax while honouring ADR-009 (no runtime PHP dependencies).
 *
 * @package Lumina\Core\Render
 * @since 0.4.0
 */

declare( strict_types=1 );

namespace Lumina\Core\Render;

/**
 * Resolves view slugs to template paths within a base directory.
 */
class TemplateResolver {

	/**
	 * Base templates directory.
	 *
	 * @var string
	 */
	private string $base_path;

	/**
	 * Build the resolver.
	 *
	 * @param string $base_path Absolute base directory for templates.
	 */
	public function __construct( string $base_path ) {
		$this->base_path = rtrim( $base_path, '/\\' );
	}

	/**
	 * Resolve a view slug to an absolute template path.
	 *
	 * @param string               $slug    View slug (dots separate directories).
	 * @param array<string, mixed> $context Optional resolver context
	 *                                      (['override' => string|null]).
	 * @return string|null Absolute path, or null when nothing matches.
	 */
	public function resolve( string $slug, array $context = array() ): ?string {
		$slug  = $this->normalize_slug( $slug );
		$paths = $this->candidates( $slug, $context );

		foreach ( $paths as $path ) {
			if ( is_readable( $path ) ) {
				return $path;
			}
		}

		return null;
	}

	/**
	 * The list of candidate paths for a slug, most specific first.
	 *
	 * @param string               $slug    Normalized slug.
	 * @param array<string, mixed> $context Resolver context.
	 * @return list<string>
	 */
	public function candidates( string $slug, array $context = array() ): array {
		$override = isset( $context['override'] ) && is_string( $context['override'] )
			? trim( (string) $context['override'], '/' )
			: '';

		$relative = str_replace( '.', '/', $slug );
		$paths    = array();

		if ( '' !== $override ) {
			$paths[] = $this->base_path . '/' . $override . '/' . $relative . '.php';
		}

		$paths[] = $this->base_path . '/' . $relative . '.php';
		$paths[] = $this->base_path . '/wp-' . $relative . '.php';

		return array_values( array_unique( $paths ) );
	}

	/**
	 * Normalize a view slug: trim, lowercase, map .twig → .php.
	 *
	 * @param string $slug Raw view slug.
	 * @return string
	 */
	private function normalize_slug( string $slug ): string {
		$slug = strtolower( trim( $slug, " \t\n\r\0\x0B/." ) );

		if ( str_ends_with( $slug, '.twig' ) ) {
			$slug = substr( $slug, 0, -5 );
		}

		if ( str_ends_with( $slug, '.php' ) ) {
			$slug = substr( $slug, 0, -4 );
		}

		// Reject path traversal so slugs can never escape the template base.
		$slug = str_replace( array( '..', '\\' ), array( '', '/' ), $slug );

		return $slug;
	}
}
