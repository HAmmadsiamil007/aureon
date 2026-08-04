<?php
/**
 * ThemeTemplatesBridge — WP-surface glue for the template hierarchy.
 *
 * Phase 6 (Template System): the only WordPress-boundary piece of the template
 * subsystem. When WordPress is present it hooks `template_include` so the
 * resolved child template wins the hierarchy; every call is capability-guarded
 * (WP functions may be absent in CLI/smoke contexts). `locate()` is the
 * WP-free seam used by tests and by templates that need a resolved path.
 *
 * The theme is fully standalone (Phase 16): it ships its own WP hierarchy
 * files; the resolver reads only the theme's own templates.
 *
 * @package Lumina\Core\Templates
 * @since 0.6.0
 */

declare( strict_types=1 );

namespace Lumina\Core\Templates;

/**
 * Hooks the WP template_include filter to the child template resolver.
 */
class ThemeTemplatesBridge {

	/**
	 * Template resolver.
	 *
	 * @var TemplateResolver
	 */
	private TemplateResolver $resolver;

	/**
	 * Build the bridge.
	 *
	 * @param TemplateResolver $resolver Template resolver.
	 */
	public function __construct( TemplateResolver $resolver ) {
		$this->resolver = $resolver;
	}

	/**
	 * Register the `template_include` filter when WordPress is present.
	 *
	 * @return void
	 */
	public function register(): void {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		if ( ! function_exists( 'add_filter' ) ) {
			return;
		}

		add_filter( 'template_include', array( $this, 'template_include' ), 10, 1 );
	}

	/**
	 * Filter callback: prefer the resolved child template over the default.
	 *
	 * @param string $template Default resolved template path from WordPress.
	 * @return string
	 */
	public function template_include( string $template ): string {
		$type = $this->current_type();

		if ( null === $type ) {
			return $template;
		}

		$resolved = $this->resolver->resolve( $type, $this->current_context() );

		return null !== $resolved ? $resolved : $template;
	}

	/**
	 * Locate a template type through the resolver (WP-free seam).
	 *
	 * @param string               $type    Template type.
	 * @param array<string, mixed> $context Type context.
	 * @return string|null
	 */
	public function locate( string $type, array $context = array() ): ?string {
		return $this->resolver->resolve( $type, $context );
	}

	/**
	 * The WP template type for the current query, or null when indeterminate.
	 *
	 * @return string|null
	 */
	private function current_type(): ?string {
		// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals -- WP core functions.
		if ( ! function_exists( 'is_home' ) || ! function_exists( 'is_singular' ) ) {
			return null;
		}

		if ( is_404() ) {
			return '404';
		}

		if ( is_search() ) {
			return 'search';
		}

		if ( is_home() ) {
			return 'home';
		}

		if ( is_front_page() ) {
			return 'front';
		}

		if ( is_page() ) {
			return 'page';
		}

		if ( is_single() ) {
			return 'single';
		}

		if ( is_category() ) {
			return 'category';
		}

		if ( is_tag() ) {
			return 'tag';
		}

		if ( is_author() ) {
			return 'author';
		}

		if ( is_date() ) {
			return 'date';
		}

		if ( is_archive() ) {
			return 'archive';
		}

		if ( is_attachment() ) {
			return 'attachment';
		}

		return 'index';
		// phpcs:enable WordPress.NamingConventions.PrefixAllGlobals
	}

	/**
	 * Minimal type context (post type / slug / id) for hierarchy prefixes.
	 *
	 * @return array<string, mixed>
	 */
	private function current_context(): array {
		$context = array();

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		if ( function_exists( 'get_post_type' ) && function_exists( 'get_the_ID' ) ) {
			$post_type = get_post_type();

			if ( is_string( $post_type ) && '' !== $post_type ) {
				$context['post_type'] = $post_type;
			}

			$id = (int) get_the_ID();

			if ( $id > 0 ) {
				$context['id'] = $id;
			}
		}

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		if ( function_exists( 'get_post_field' ) ) {
			$slug = (string) get_post_field( 'post_name' );

			if ( '' !== $slug ) {
				$context['slug'] = $slug;
			}
		}

		return $context;
	}
}
