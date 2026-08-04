<?php
/**
 * WpmlBridge — WPML capability adapter.
 *
 * Phase 8 (Plugin Bridges): locale/languages/is_translated through the public
 * WPML API, capability-guarded; safe defaults when absent.
 *
 * @package Phantom\Core\Bridges\Wpml
 * @since 0.8.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Bridges\Wpml;

use Phantom\Core\Bridges\Bridge;

/**
 * WPML adapter.
 */
final class WpmlBridge extends Bridge {

	/**
	 * {@inheritDoc}
	 */
	public function slug(): string {
		return 'wpml';
	}

	/**
	 * {@inheritDoc}
	 */
	public function name(): string {
		return 'WPML';
	}

	/**
	 * {@inheritDoc}
	 */
	public function is_active(): bool {
		return $this->guard( 'SitePress' ) || defined( 'ICL_SITEPRESS_VERSION' );
	}

	/**
	 * {@inheritDoc}
	 */
	public function version(): string {
		return $this->constant_version( 'ICL_SITEPRESS_VERSION' );
	}

	/**
	 * {@inheritDoc}
	 */
	public function capabilities(): array {
		return array( 'locale', 'languages', 'is_translated' );
	}

	/**
	 * The current site locale (WP-guarded).
	 *
	 * @return string
	 */
	public function locale(): string {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		if ( ! $this->is_active() || ! function_exists( 'get_locale' ) ) {
			return '';
		}

		return (string) get_locale();
	}

	/**
	 * The active languages list.
	 *
	 * @return list<array<string, mixed>>
	 */
	public function languages(): array {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WPML core function.
		if ( ! $this->is_active() || ! function_exists( 'icl_get_languages' ) ) {
			return array();
		}

		$languages = icl_get_languages( 'skip_missing=0' );

		return is_array( $languages ) ? array_values( $languages ) : array();
	}

	/**
	 * Whether a post id has translations.
	 *
	 * @param int    $id   Post id.
	 * @param string $type Post type.
	 * @return bool
	 */
	public function is_translated( int $id, string $type = 'post' ): bool {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WPML core function.
		if ( ! $this->is_active() || ! function_exists( 'wpml_get_language_information' ) ) {
			return false;
		}

		$info = wpml_get_language_information( null, $id );

		return is_array( $info ) && ! empty( $info['translations'] );
	}
}
