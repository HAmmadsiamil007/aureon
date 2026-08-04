<?php
/**
 * BuddyBridge — BuddyPress capability adapter.
 *
 * Phase 8 (Plugin Bridges): avatar/profile URL through the public BuddyPress
 * API, capability-guarded; safe defaults when absent.
 *
 * @package Lumina\Core\Bridges\Buddypress
 * @since 0.8.0
 */

declare( strict_types=1 );

namespace Lumina\Core\Bridges\Buddypress;

use Lumina\Core\Bridges\Bridge;

/**
 * BuddyPress adapter.
 */
final class BuddyBridge extends Bridge {

	/**
	 * {@inheritDoc}
	 */
	public function slug(): string {
		return 'buddypress';
	}

	/**
	 * {@inheritDoc}
	 */
	public function name(): string {
		return 'BuddyPress';
	}

	/**
	 * {@inheritDoc}
	 */
	public function is_active(): bool {
		return $this->guard( 'BuddyPress' ) || defined( 'BP_VERSION' );
	}

	/**
	 * {@inheritDoc}
	 */
	public function version(): string {
		return $this->constant_version( 'BP_VERSION' );
	}

	/**
	 * {@inheritDoc}
	 */
	public function capabilities(): array {
		return array( 'avatar', 'profile_url' );
	}

	/**
	 * Avatar URL for a user id.
	 *
	 * @param int $user_id User id.
	 * @return string
	 */
	public function avatar( int $user_id ): string {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- BuddyPress core function.
		if ( $user_id <= 0 || ! function_exists( 'bp_core_fetch_avatar' ) ) {
			return '';
		}

		$html = bp_core_fetch_avatar(
			array(
				'item_id' => $user_id,
				'html'    => false,
			)
		);

		return is_string( $html ) ? $html : '';
	}

	/**
	 * Profile URL for a user id.
	 *
	 * @param int $user_id User id.
	 * @return string
	 */
	public function profile_url( int $user_id ): string {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- BuddyPress core function.
		if ( $user_id <= 0 || ! function_exists( 'bp_core_get_user_domain' ) ) {
			return '';
		}

		$url = bp_core_get_user_domain( $user_id );

		return is_string( $url ) ? $url : '';
	}
}
