<?php
/**
 * RenderServiceProvider — wire the Render Engine into the container.
 *
 * Phase 4 (Render Engine): binds the render subsystem services so Components
 * (Phase 5) and Templates (Phase 6) resolve rendering via the container/App
 * facade. Registered in Config\config.php 'providers' and booted by the
 * Kernel's providers step (ADR-014).
 *
 * @package Lumina\Core\Render
 * @since 0.4.0
 */

declare( strict_types=1 );

namespace Lumina\Core\Render;

use Lumina\Core\Container\Container;
use Lumina\Core\Providers\ServiceProviderInterface;

/**
 * Registers render engine services.
 */
final class RenderServiceProvider implements ServiceProviderInterface {

	/**
	 * {@inheritDoc}
	 *
	 * @param Container $container Service container.
	 */
	public function register( Container $container ): void {
		$container->singleton( 'render.engine', static fn(): TemplateEngineInterface => new PhpTemplateEngine() );

		$container->singleton(
			'render.resolver',
			static fn(): TemplateResolver => new TemplateResolver(
				dirname( __DIR__, 2 ) . '/templates'
			)
		);

		$container->singleton(
			'render.cache',
			static fn( Container $c ): RenderCache => new RenderCache(
				$c->has( 'cache.transient' ) ? $c->get( 'cache.transient' ) : null
			)
		);

		$container->singleton(
			'render.layout',
			static fn( Container $c ): Layout => new Layout(
				static fn( string $view, array $args = array() ): string =>
					$c->get( 'render.renderer' )->render( $view, $args )
			)
		);

		$container->singleton(
			'render.renderer',
			static fn( Container $c ): Renderer => new Renderer(
				$c->get( 'render.engine' ),
				$c->get( 'render.resolver' ),
				$c->get( 'render.cache' )
			)
		);
	}

	/**
	 * {@inheritDoc}
	 *
	 * @param Container $container Service container.
	 */
	public function boot( Container $container ): void {
		// Rendering is on-demand; no WordPress hooks are required in Phase 4.
	}
}
