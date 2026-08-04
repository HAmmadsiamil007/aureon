<?php
/**
 * ComponentsServiceProvider — wire the Component Registry into the container.
 *
 * Phase 5 (Component Registry): binds `components.registry` (singleton),
 * seeding it from JSON discovery (canonical `app/Components/config/components.json`
 * plus any per-instance paths from config `components.json_paths`). On boot it
 * registers the `[lumina:{slug}]` shortcodes through WordPress when present,
 * and records them in the registry otherwise (WP-free CLI parity).
 *
 * @package Lumina\Core\Components
 * @since 0.5.0
 */

declare( strict_types=1 );

namespace Lumina\Core\Components;

use Lumina\Core\Container\Container;
use Lumina\Core\Providers\ServiceProviderInterface;

/**
 * Registers component registry services.
 */
final class ComponentsServiceProvider implements ServiceProviderInterface {

	/**
	 * {@inheritDoc}
	 *
	 * @param Container $container Service container.
	 */
	public function register( Container $container ): void {
		$container->singleton(
			'components.registry',
			static function ( Container $c ): Registry {
				$registry = new Registry(
					static fn( string $view, array $props = array() ): string =>
						$c->get( 'render.renderer' )->render( $view, $props )
				);

				$paths = $c->get( 'config' )->get( 'components.json_paths', array() );

				if ( ! is_array( $paths ) || array() === $paths ) {
					$paths = array( __DIR__ . '/config/components.json' );
				}

				$loader = new Loader();

				foreach ( $loader->load( $paths ) as $raw ) {
					$name = is_string( $raw['name'] ?? null ) ? $raw['name'] : '';

					if ( '' === $name ) {
						continue;
					}

					$renderer = is_string( $raw['renderer'] ?? null ) ? $raw['renderer'] : '';

					if ( '' === $renderer ) {
						continue;
					}

					$registry->register( $name, $renderer, $raw );
				}

				return $registry;
			}
		);
	}

	/**
	 * {@inheritDoc}
	 *
	 * @param Container $container Service container.
	 */
	public function boot( Container $container ): void {
		if ( ! $container->has( 'components.registry' ) ) {
			return;
		}

		/**
		 * The resolved registry instance.
		 *
		 * @var Registry
		 */
		$registry = $container->get( 'components.registry' );

		if ( array() !== $registry->all() ) {
			$this->enqueue_assets( $container );
		}

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		if ( ! function_exists( 'add_shortcode' ) ) {
			return;
		}

		foreach ( $registry->shortcodes() as $tag => $name ) {
			add_shortcode(
				$tag,
				static fn( $atts ): string => $registry->render_shortcode( $tag, (array) $atts )
			);
		}
	}

	/**
	 * Enqueue the component library assets when components are registered.
	 *
	 * The behaviors entry and the token-driven stylesheet ship only when the
	 * registry has components. Note: the canonical catalog always seeds the
	 * registry for this theme, so the enqueue fires whenever the library is
	 * present; sites that override `components.json_paths` with an empty
	 * list get true zero-cost behavior.
	 *
	 * @param Container $container Service container.
	 * @return void
	 */
	private function enqueue_assets( Container $container ): void {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		if ( ! function_exists( 'add_action' ) || ! $container->has( 'assets.loader' ) ) {
			return;
		}

		add_action(
			'wp_enqueue_scripts',
			static function () use ( $container ): void {
				/**
				 * The resolved asset loader.
				 *
				 * @var \Lumina\Core\Assets\AssetLoader
				 */
				$loader = $container->get( 'assets.loader' );

				$loader->js( 'assets-src/ts/components.ts' );
				$loader->css( 'assets-src/scss/main.scss' );
			},
			10
		);
	}
}
