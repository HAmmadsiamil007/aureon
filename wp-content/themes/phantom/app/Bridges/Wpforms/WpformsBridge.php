<?php
/**
 * WpformsBridge — WPForms capability adapter.
 *
 * Phase 8 (Plugin Bridges): form embedding through the public WPForms API,
 * capability-guarded; safe defaults when absent.
 *
 * @package Phantom\Core\Bridges\Wpforms
 * @since 0.8.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Bridges\Wpforms;

use Phantom\Core\Bridges\Bridge;

/**
 * WPForms adapter.
 */
final class WpformsBridge extends Bridge {

	/**
	 * {@inheritDoc}
	 */
	public function slug(): string {
		return 'wpforms';
	}

	/**
	 * {@inheritDoc}
	 */
	public function name(): string {
		return 'WPForms';
	}

	/**
	 * {@inheritDoc}
	 */
	public function is_active(): bool {
		return $this->guard( 'WPForms\\WPForms' ) || function_exists( 'wpforms' );
	}

	/**
	 * {@inheritDoc}
	 */
	public function version(): string {
		return $this->constant_version( 'WPFORMS_VERSION' );
	}

	/**
	 * {@inheritDoc}
	 */
	public function capabilities(): array {
		return array( 'embed' );
	}

	/**
	 * Render a form by id.
	 *
	 * @param int $id Form id.
	 * @return string
	 */
	public function embed( int $id ): string {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		if ( $id <= 0 || ! function_exists( 'wpforms_display' ) ) {
			return '';
		}

		ob_start();
		wpforms_display( $id );
		$html = (string) ob_get_clean();

		return $html;
	}
}
