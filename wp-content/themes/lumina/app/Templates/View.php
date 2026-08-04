<?php
/**
 * View — static facade for template authors.
 *
 * Phase 6 (Template System): the composition seam templates call instead of
 * reaching for the container: `View::partial('content-single', $args)` and
 * `View::section('loop')`. Resolves services lazily from the booted App
 * facade, so it works identically in WordPress templates and WP-free CLI
 * smoke contexts (the Kernel is launched in both).
 *
 * @package Lumina\Core\Templates
 * @since 0.6.0
 */

declare( strict_types=1 );

namespace Lumina\Core\Templates;

use Lumina\Core\Core\App;
use Lumina\Core\Render\RenderException;

/**
 * Static partial/section composition helpers.
 */
final class View {

	/**
	 * Render a partial with the configured fallback chain.
	 *
	 * @param string               $name     Partial name.
	 * @param array<string, mixed> $args     Partial data.
	 * @param string|null          $fallback Fallback partial name.
	 * @return string
	 * @throws RenderException When the partial chain cannot be resolved.
	 */
	public static function partial( string $name, array $args = array(), ?string $fallback = 'index' ): string {
		/**
		 * The resolved partial loader.
		 *
		 * @var PartialLoader
		 */
		$loader = App::instance()->make( 'templates.partials' );

		return $loader->partial( $name, $args, $fallback );
	}

	/**
	 * Render every section registered against a region.
	 *
	 * @param string $region Region name.
	 * @return string
	 */
	public static function section( string $region ): string {
		/**
		 * The resolved sections registry.
		 *
		 * @var Sections
		 */
		$sections = App::instance()->make( 'templates.sections' );

		return $sections->render( $region );
	}

	/**
	 * Render a full template composition from registry components.
	 *
	 * @param string               $slug Template slug (maps.php key).
	 * @param array<string, mixed> $data Request data (posts, products, …).
	 * @return string
	 */
	public static function compose( string $slug, array $data = array() ): string {
		/**
		 * The resolved template composer.
		 *
		 * @var Composer
		 */
		$composer = App::instance()->make( 'templates.composer' );

		return $composer->compose( $slug, $data );
	}
}
