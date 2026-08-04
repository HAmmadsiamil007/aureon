<?php
/**
 * RankMathBridge — Rank Math SEO capability adapter.
 *
 * Phase 8 (Plugin Bridges): meta title/description/robots through the public
 * Rank Math API, capability-guarded; safe defaults when absent.
 *
 * @package Phantom\Core\Bridges\RankMath
 * @since 0.8.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Bridges\RankMath;

use Phantom\Core\Bridges\Bridge;

/**
 * Rank Math adapter.
 */
final class RankMathBridge extends Bridge {

	/**
	 * {@inheritDoc}
	 */
	public function slug(): string {
		return 'rankmath';
	}

	/**
	 * {@inheritDoc}
	 */
	public function name(): string {
		return 'Rank Math SEO';
	}

	/**
	 * {@inheritDoc}
	 */
	public function is_active(): bool {
		return $this->guard( 'RankMath\\RankMath' )
			|| defined( 'RANK_MATH_VERSION' );
	}

	/**
	 * {@inheritDoc}
	 */
	public function version(): string {
		return $this->constant_version( 'RANK_MATH_VERSION' );
	}

	/**
	 * {@inheritDoc}
	 */
	public function capabilities(): array {
		return array( 'meta_title', 'meta_description', 'robots' );
	}

	/**
	 * SEO meta title for a post id.
	 *
	 * @param int $id Post id.
	 * @return string
	 */
	public function meta_title( int $id = 0 ): string {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- Rank Math core function.
		if ( ! function_exists( 'rank_math_get_the_title' ) ) {
			return '';
		}

		$title = rank_math_get_the_title( $id );

		return is_string( $title ) ? $title : '';
	}

	/**
	 * SEO meta description for a post id.
	 *
	 * @param int $id Post id.
	 * @return string
	 */
	public function meta_description( int $id = 0 ): string {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- Rank Math core function.
		if ( ! function_exists( 'rank_math_get_the_description' ) ) {
			return '';
		}

		$description = rank_math_get_the_description( $id );

		return is_string( $description ) ? $description : '';
	}

	/**
	 * Robots directives for a post id.
	 *
	 * @param int $id Post id.
	 * @return array<string, bool>
	 */
	public function robots( int $id = 0 ): array {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- Rank Math core function.
		if ( ! function_exists( 'rank_math_get_robots' ) ) {
			return array(
				'index'  => true,
				'follow' => true,
			);
		}

		$robots = rank_math_get_robots( $id );

		return is_array( $robots ) ? $robots : array(
			'index'  => true,
			'follow' => true,
		);
	}
}
