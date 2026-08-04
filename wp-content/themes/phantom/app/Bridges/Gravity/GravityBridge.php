<?php
/**
 * GravityBridge — Gravity Forms capability adapter.
 *
 * Phase 8 (Plugin Bridges): form embedding + asset enqueueing through the
 * public Gravity Forms API, capability-guarded; safe defaults when absent.
 *
 * @package Phantom\Core\Bridges\Gravity
 * @since 0.8.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Bridges\Gravity;

use Phantom\Core\Bridges\Bridge;

/**
 * Gravity Forms adapter.
 */
final class GravityBridge extends Bridge {

	/**
	 * {@inheritDoc}
	 */
	public function slug(): string {
		return 'gravity';
	}

	/**
	 * {@inheritDoc}
	 */
	public function name(): string {
		return 'Gravity Forms';
	}

	/**
	 * {@inheritDoc}
	 */
	public function is_active(): bool {
		return $this->guard( 'GFForms' ) || defined( 'GF_VERSION' );
	}

	/**
	 * {@inheritDoc}
	 */
	public function version(): string {
		return $this->constant_version( 'GF_VERSION' );
	}

	/**
	 * {@inheritDoc}
	 */
	public function capabilities(): array {
		return array( 'embed', 'enqueue_assets' );
	}

	/**
	 * Render a form by id.
	 *
	 * @param int $id Form id.
	 * @return string
	 */
	public function embed( int $id ): string {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- Gravity Forms core function.
		if ( $id <= 0 || ! function_exists( 'gravity_form' ) ) {
			return '';
		}

		ob_start();
		gravity_form( $id, false, false, false, '', true, 1 );
		$html = (string) ob_get_clean();

		return $html;
	}

	/**
	 * Enqueue a form's assets (guarded).
	 *
	 * @param int $id Form id.
	 * @return void
	 */
	public function enqueue_assets( int $id ): void {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- Gravity Forms core function.
		if ( $id <= 0 || ! function_exists( 'gf_do_action' ) ) {
			return;
		}

		gf_do_action( array( 'gform_enqueue_scripts', (string) $id ), $id, false );
	}
}
