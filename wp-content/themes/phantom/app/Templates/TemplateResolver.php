<?php
/**
 * TemplateResolver — WP-template-hierarchy-aware path resolution.
 *
 * Phase 6 (Template System): resolves template names through the plan's
 * override table (most specific wins):
 *
 *   1. templates/{$override}/{$slug}.php
 *   2. templates/{$slug}.php            (child theme)
 *   3. templates/wp-{$slug}.php
 *   4. {$parent_dir}/{$slug}.php        (parent theme — GeneratePress, untouched)
 *   5. null (caller falls back / defers to WordPress)
 *
 * It also encodes the documented WordPress template hierarchy (public,
 * stable knowledge) so template *types* resolve to the most specific file a
 * child ships: `single-{post_type}-{slug}`, `single-{post_type}`, `single`,
 * `singular`, `index`. The resolver never touches WordPress globals; the
 * parent dir is injected (provider resolves it via `get_template_directory()`
 * when WordPress is present).
 *
 * @package Phantom\Core\Templates
 * @since 0.6.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Templates;

/**
 * Resolves template names and WP hierarchy types to file paths.
 */
class TemplateResolver {

	/**
	 * Child theme templates directory.
	 *
	 * @var string
	 */
	private string $child_dir;

	/**
	 * Parent theme directory (null when WP-free / unknown).
	 *
	 * @var string|null
	 */
	private ?string $parent_dir;

	/**
	 * Canonical WP template hierarchy (type → ordered candidate names).
	 * Documented, stable WordPress behavior.
	 */
	private const HIERARCHY = array(
		'index'      => array( 'index' ),
		'home'       => array( 'home', 'index' ),
		'front'      => array( 'front-page', 'home', 'index' ),
		'single'     => array( 'single', 'singular', 'index' ),
		'page'       => array( 'page', 'singular', 'index' ),
		'archive'    => array( 'archive', 'index' ),
		'category'   => array( 'category', 'archive', 'index' ),
		'tag'        => array( 'tag', 'archive', 'index' ),
		'author'     => array( 'author', 'archive', 'index' ),
		'date'       => array( 'date', 'archive', 'index' ),
		'search'     => array( 'search', 'index' ),
		'404'        => array( '404', 'index' ),
		'attachment' => array( 'attachment', 'single', 'singular', 'index' ),
	);

	/**
	 * Build the resolver.
	 *
	 * @param string      $child_dir  Child theme templates directory.
	 * @param string|null $parent_dir Parent theme directory (optional).
	 */
	public function __construct( string $child_dir, ?string $parent_dir = null ) {
		$this->child_dir  = rtrim( $child_dir, '/\\' );
		$this->parent_dir = null !== $parent_dir ? rtrim( $parent_dir, '/\\' ) : null;
	}

	/**
	 * Resolve a single template name to the most specific existing file.
	 *
	 * @param string               $name    Template name (dots separate dirs).
	 * @param array<string, mixed> $context Context (['override' => string|null]).
	 * @return string|null Absolute path, or null when nothing matches.
	 */
	public function path( string $name, array $context = array() ): ?string {
		$name = $this->normalize( $name );
		$base = $this->candidates( $name, $context );

		foreach ( $base as $file ) {
			if ( is_readable( $file ) ) {
				return $file;
			}
		}

		if ( null !== $this->parent_dir ) {
			$parent = $this->parent_dir . '/' . $name . '.php';

			if ( is_readable( $parent ) ) {
				return $parent;
			}
		}

		return null;
	}

	/**
	 * Resolve a WP template *type* through its documented hierarchy.
	 *
	 * Context may carry `post_type`, `slug`, `id`, `taxonomy`, `term`,
	 * `author`, `date` to build the most-specific-first candidate names.
	 *
	 * @param string               $type    Template type (single, page, …).
	 * @param array<string, mixed> $context Type-specific context.
	 * @return string|null Absolute path, or null when the whole chain misses.
	 */
	public function resolve( string $type, array $context = array() ): ?string {
		foreach ( $this->hierarchy( $type, $context ) as $name ) {
			$file = $this->path( $name, $context );

			if ( null !== $file ) {
				return $file;
			}
		}

		return null;
	}

	/**
	 * The ordered candidate names for a template type (most specific first).
	 *
	 * @param string               $type    Template type.
	 * @param array<string, mixed> $context Type-specific context.
	 * @return list<string>
	 */
	public function hierarchy( string $type, array $context = array() ): array {
		$names = self::HIERARCHY[ $type ] ?? array( 'index' );

		return array_merge( $this->context_prefixes( $type, $context ), $names );
	}

	/**
	 * The candidate file paths for a name through the override tiers.
	 *
	 * @param string               $name    Normalized name.
	 * @param array<string, mixed> $context Context.
	 * @return list<string>
	 */
	public function candidates( string $name, array $context = array() ): array {
		$override = isset( $context['override'] ) && is_string( $context['override'] )
			? trim( (string) $context['override'], '/' )
			: '';

		$paths = array();

		if ( '' !== $override ) {
			$paths[] = $this->child_dir . '/' . $override . '/' . $name . '.php';
		}

		$paths[] = $this->child_dir . '/' . $name . '.php';
		$paths[] = $this->child_dir . '/wp-' . $name . '.php';

		return array_values( array_unique( $paths ) );
	}

	/**
	 * Context-specific hierarchy prefixes (e.g. single-{post_type}-{slug}).
	 *
	 * @param string               $type    Template type.
	 * @param array<string, mixed> $context Context.
	 * @return list<string>
	 */
	private function context_prefixes( string $type, array $context ): array {
		$prefixes = array();
		$slug     = $this->context_string( $context, 'slug' );
		$id       = $this->context_int( $context, 'id' );

		if ( 'single' === $type ) {
			$post_type = $this->context_string( $context, 'post_type' );

			if ( '' !== $post_type ) {
				if ( '' !== $slug ) {
					$prefixes[] = 'single-' . $post_type . '-' . $slug;
				}

				$prefixes[] = 'single-' . $post_type;
			} elseif ( '' !== $slug ) {
				$prefixes[] = 'single-' . $slug;
			}
		} elseif ( in_array( $type, array( 'page', 'category', 'tag', 'author' ), true ) ) {
			if ( '' !== $slug ) {
				$prefixes[] = $type . '-' . $slug;
			}

			if ( $id > 0 ) {
				$prefixes[] = $type . '-' . (string) $id;
			}
		}

		if ( 'archive' === $type ) {
			$post_type = $this->context_string( $context, 'post_type' );

			if ( '' !== $post_type ) {
				$prefixes[] = 'archive-' . $post_type;
			}
		}

		return $prefixes;
	}

	/**
	 * Read a string context value.
	 *
	 * @param array<string, mixed> $context Context.
	 * @param string               $key     Key.
	 * @return string
	 */
	private function context_string( array $context, string $key ): string {
		$value = isset( $context[ $key ] ) && is_string( $context[ $key ] ) ? $context[ $key ] : '';

		if ( '' === $value ) {
			return '';
		}

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		if ( function_exists( 'sanitize_key' ) ) {
			return sanitize_key( $value );
		}

		// WP-free fallback: the same charset as sanitize_key() — lowercase
		// alphanumerics, dash, and underscore only.
		$value = strtolower( $value );

		return (string) preg_replace( '/[^a-z0-9_\-]/', '', $value );
	}

	/**
	 * Read an int context value.
	 *
	 * @param array<string, mixed> $context Context.
	 * @param string               $key     Key.
	 * @return int
	 */
	private function context_int( array $context, string $key ): int {
		return isset( $context[ $key ] ) && is_numeric( $context[ $key ] )
			? (int) $context[ $key ]
			: 0;
	}

	/**
	 * Normalize a template name (lowercase, strip .php/.twig, block traversal).
	 *
	 * @param string $name Raw name.
	 * @return string
	 */
	private function normalize( string $name ): string {
		$name = strtolower( trim( $name, " \t\n\r\0\x0B/." ) );

		if ( str_ends_with( $name, '.php' ) ) {
			$name = substr( $name, 0, -4 );
		}

		if ( str_ends_with( $name, '.twig' ) ) {
			$name = substr( $name, 0, -5 );
		}

		return str_replace( array( '..', '\\' ), array( '', '/' ), $name );
	}
}
