<?php
/**
 * YoastBridge — Yoast SEO capability adapter.
 *
 * Phase 8 (Plugin Bridges): meta title/description/canonical through Yoast's
 * post-meta storage, capability-guarded; safe defaults when absent.
 *
 * @package Lumina\Core\Bridges\Yoast
 * @since 0.8.0
 */

declare( strict_types=1 );

namespace Lumina\Core\Bridges\Yoast;

use Lumina\Core\Bridges\Bridge;

/**
 * Yoast adapter.
 */
final class YoastBridge extends Bridge {

	/**
	 * {@inheritDoc}
	 */
	public function slug(): string {
		return 'yoast';
	}

	/**
	 * {@inheritDoc}
	 */
	public function name(): string {
		return 'Yoast SEO';
	}

	/**
	 * {@inheritDoc}
	 */
	public function is_active(): bool {
		return $this->guard( 'WPSEO_Options' ) || defined( 'WPSEO_VERSION' );
	}

	/**
	 * {@inheritDoc}
	 */
	public function version(): string {
		return $this->constant_version( 'WPSEO_VERSION' );
	}

	/**
	 * {@inheritDoc}
	 */
	public function capabilities(): array {
		return array( 'meta_title', 'meta_description', 'canonical' );
	}

	/**
	 * Yoast meta title for a post id.
	 *
	 * @param int $id Post id.
	 * @return string
	 */
	public function meta_title( int $id = 0 ): string {
		return $this->post_meta( $id, '_yoast_wpseo_title' );
	}

	/**
	 * Yoast meta description for a post id.
	 *
	 * @param int $id Post id.
	 * @return string
	 */
	public function meta_description( int $id = 0 ): string {
		return $this->post_meta( $id, '_yoast_wpseo_metadesc' );
	}

	/**
	 * Yoast canonical for a post id.
	 *
	 * @param int $id Post id.
	 * @return string
	 */
	public function canonical( int $id = 0 ): string {
		return $this->post_meta( $id, '_yoast_wpseo_canonical' );
	}

	/**
	 * Read a Yoast post-meta key (WP-guarded).
	 *
	 * @param int    $id  Post id.
	 * @param string $key Meta key.
	 * @return string
	 */
	private function post_meta( int $id, string $key ): string {
		if ( $id <= 0 || ! $this->is_active() ) {
			return '';
		}

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		if ( ! function_exists( 'get_post_meta' ) ) {
			return '';
		}

		$value = get_post_meta( $id, $key, true );

		return is_string( $value ) ? $value : '';
	}
}
