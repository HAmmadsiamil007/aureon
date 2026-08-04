<?php
/**
 * PolylangBridge — Polylang capability adapter.
 *
 * Phase 8 (Plugin Bridges): locale/languages/is_translated through the public
 * Polylang API, capability-guarded; safe defaults when absent.
 *
 * @package Phantom\Core\Bridges\Polylang
 * @since 0.8.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Bridges\Polylang;

use Phantom\Core\Bridges\Bridge;

/**
 * Polylang adapter.
 */
final class PolylangBridge extends Bridge {

	/**
	 * {@inheritDoc}
	 */
	public function slug(): string {
		return 'polylang';
	}

	/**
	 * {@inheritDoc}
	 */
	public function name(): string {
		return 'Polylang';
	}

	/**
	 * {@inheritDoc}
	 */
	public function is_active(): bool {
		return $this->guard( 'Polylang' ) || defined( 'POLYLANG_VERSION' );
	}

	/**
	 * {@inheritDoc}
	 */
	public function version(): string {
		return $this->constant_version( 'POLYLANG_VERSION' );
	}

	/**
	 * {@inheritDoc}
	 */
	public function capabilities(): array {
		return array( 'locale', 'languages', 'is_translated' );
	}

	/**
	 * The current language slug (or locale when Polylang is absent).
	 *
	 * @return string
	 */
	public function locale(): string {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- Polylang core function.
		if ( function_exists( 'pll_current_language' ) ) {
			$language = pll_current_language( 'slug' );

			return is_string( $language ) ? $language : '';
		}

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		return function_exists( 'get_locale' ) ? (string) get_locale() : '';
	}

	/**
	 * The active languages list.
	 *
	 * @return list<string>
	 */
	public function languages(): array {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- Polylang core function.
		if ( ! function_exists( 'pll_languages_list' ) ) {
			return array();
		}

		$languages = pll_languages_list();

		return is_array( $languages ) ? array_values( array_map( 'strval', $languages ) ) : array();
	}

	/**
	 * Whether a post id is translated.
	 *
	 * @param int $id Post id.
	 * @return bool
	 */
	public function is_translated( int $id ): bool {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- Polylang core function.
		if ( ! function_exists( 'pll_get_post_language' ) ) {
			return false;
		}

		return false !== pll_get_post_language( $id );
	}
}
