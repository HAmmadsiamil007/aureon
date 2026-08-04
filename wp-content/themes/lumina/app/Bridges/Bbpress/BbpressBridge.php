<?php
/**
 * BbpressBridge — bbPress capability adapter.
 *
 * Phase 8 (Plugin Bridges): topic/reply detection + forum URL through the
 * public bbPress API, capability-guarded; safe defaults when absent.
 *
 * @package Lumina\Core\Bridges\Bbpress
 * @since 0.8.0
 */

declare( strict_types=1 );

namespace Lumina\Core\Bridges\Bbpress;

use Lumina\Core\Bridges\Bridge;

/**
 * BbPress adapter.
 */
final class BbpressBridge extends Bridge {

	/**
	 * {@inheritDoc}
	 */
	public function slug(): string {
		return 'bbpress';
	}

	/**
	 * {@inheritDoc}
	 */
	public function name(): string {
		return 'bbPress';
	}

	/**
	 * {@inheritDoc}
	 */
	public function is_active(): bool {
		return $this->guard( 'bbPress' ) || function_exists( 'bbpress' );
	}

	/**
	 * {@inheritDoc}
	 */
	public function version(): string {
		return $this->constant_version( 'BBPRESS_VERSION' );
	}

	/**
	 * {@inheritDoc}
	 */
	public function capabilities(): array {
		return array( 'is_topic', 'is_reply', 'forum_url' );
	}

	/**
	 * Whether the current query is a topic.
	 *
	 * @return bool
	 */
	public function is_topic(): bool {
		if ( ! $this->is_active() || ! function_exists( 'bbp_is_topic' ) ) {
			return false;
		}

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- bbPress core function.
		return bbp_is_topic();
	}

	/**
	 * Whether the current query is a reply.
	 *
	 * @return bool
	 */
	public function is_reply(): bool {
		if ( ! $this->is_active() || ! function_exists( 'bbp_is_reply' ) ) {
			return false;
		}

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- bbPress core function.
		return bbp_is_reply();
	}

	/**
	 * Forum URL for a forum id.
	 *
	 * @param int $forum_id Forum id.
	 * @return string
	 */
	public function forum_url( int $forum_id ): string {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- bbPress core function.
		if ( $forum_id <= 0 || ! function_exists( 'bbp_get_forum_url' ) ) {
			return '';
		}

		$url = bbp_get_forum_url( $forum_id );

		return is_string( $url ) ? $url : '';
	}
}
