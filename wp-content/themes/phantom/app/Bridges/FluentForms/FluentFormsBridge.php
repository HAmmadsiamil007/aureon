<?php
/**
 * FluentFormsBridge — Fluent Forms capability adapter.
 *
 * Phase 8 (Plugin Bridges): form embedding via the public shortcode surface,
 * capability-guarded; safe defaults when absent.
 *
 * @package Phantom\Core\Bridges\FluentForms
 * @since 0.8.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Bridges\FluentForms;

use Phantom\Core\Bridges\Bridge;

/**
 * Fluent Forms adapter.
 */
final class FluentFormsBridge extends Bridge {

	/**
	 * {@inheritDoc}
	 */
	public function slug(): string {
		return 'fluentforms';
	}

	/**
	 * {@inheritDoc}
	 */
	public function name(): string {
		return 'Fluent Forms';
	}

	/**
	 * {@inheritDoc}
	 */
	public function is_active(): bool {
		return $this->guard( 'FluentForm\\App\\Services\\Form\\FormService' )
			|| defined( 'FLUENTFORM_VERSION' );
	}

	/**
	 * {@inheritDoc}
	 */
	public function version(): string {
		return $this->constant_version( 'FLUENTFORM_VERSION' );
	}

	/**
	 * {@inheritDoc}
	 */
	public function capabilities(): array {
		return array( 'embed' );
	}

	/**
	 * Render a form by id through the shortcode surface.
	 *
	 * @param int $id Form id.
	 * @return string
	 */
	public function embed( int $id ): string {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		if ( $id <= 0 || ! function_exists( 'do_shortcode' ) ) {
			return '';
		}

		$html = do_shortcode( '[fluentform id="' . (string) $id . '"]' );

		return (string) $html;
	}
}
