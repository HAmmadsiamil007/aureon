<?php
/**
 * AssetsServiceProvider — wire the Asset Pipeline into the container.
 *
 * Phase 7 (Asset Pipeline): binds `assets.manifest`, `assets.dev_server`,
 * `assets.loader`, `assets.entries`, and `assets.deps`. The manifest path and
 * dist base URL derive from the theme directory (WP-aware when present, else
 * plain paths for WP-free CLI). On boot, `wp_enqueue_scripts` enqueues the
 * configured `assets.enqueue` sources (default empty — no runtime surprise).
 *
 * @package Phantom\Core\Assets
 * @since 0.7.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Assets;

use Phantom\Core\Assets\Pipeline\DepsResolver;
use Phantom\Core\Assets\Pipeline\Entries;
use Phantom\Core\Container\Container;
use Phantom\Core\Providers\ServiceProviderInterface;

/**
 * Registers asset pipeline services.
 */
final class AssetsServiceProvider implements ServiceProviderInterface {

	/**
	 * {@inheritDoc}
	 *
	 * @param Container $container Service container.
	 */
	public function register( Container $container ): void {
		$container->singleton(
			'assets.manifest',
			static fn(): ManifestReader => new ManifestReader(
				dirname( __DIR__, 2 ) . '/assets/dist/manifest.json'
			)
		);

		$container->singleton( 'assets.dev_server', static fn(): DevServer => DevServer::from_env() );

		$container->singleton(
			'assets.loader',
			static fn( Container $c ): AssetLoader => new AssetLoader(
				$c->get( 'assets.manifest' ),
				$c->get( 'assets.dev_server' ),
				// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
				function_exists( 'get_stylesheet_directory_uri' )
					? get_stylesheet_directory_uri() . '/assets/dist'
					: ''
			)
		);

		$container->singleton(
			'assets.entries',
			static fn( Container $c ): Entries => new Entries( $c->get( 'assets.manifest' ) )
		);

		$container->singleton(
			'assets.deps',
			static fn( Container $c ): DepsResolver => new DepsResolver( $c->get( 'assets.manifest' ) )
		);
	}

	/**
	 * {@inheritDoc}
	 *
	 * @param Container $container Service container.
	 */
	public function boot( Container $container ): void {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		if ( ! function_exists( 'add_action' ) || ! $container->has( 'assets.loader' ) ) {
			return;
		}

		$enqueue = $container->get( 'config' )->get( 'assets.enqueue', array() );

		if ( ! is_array( $enqueue ) || array() === $enqueue ) {
			return;
		}

		add_action(
			'wp_enqueue_scripts',
			static function () use ( $container, $enqueue ): void {
				/**
				 * The resolved asset loader.
				 *
				 * @var AssetLoader
				 */
				$loader = $container->get( 'assets.loader' );

				foreach ( $enqueue as $type => $sources ) {
					if ( 'css' === $type && is_array( $sources ) ) {
						foreach ( $sources as $src ) {
							if ( is_string( $src ) ) {
								$loader->css( $src );
							}
						}
					}

					if ( 'js' === $type && is_array( $sources ) ) {
						foreach ( $sources as $src ) {
							if ( is_string( $src ) ) {
								$loader->js( $src );
							}
						}
					}
				}
			},
			10
		);
	}
}
