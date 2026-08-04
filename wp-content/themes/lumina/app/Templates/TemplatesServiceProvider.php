<?php
/**
 * TemplatesServiceProvider — wire the Template System into the container.
 *
 * Phase 6 (Template System): binds `templates.resolver` (hierarchy-aware),
 * `templates.partials`, `templates.sections`, and the `ThemeTemplatesBridge`.
 * The theme templates dir is the theme's `templates/`. On boot the bridge
 * registers `template_include` when WP is present.
 *
 * Phase 16 (Safe Rebranding): the parent-theme fallback was removed — Lumina
 * is standalone and ships its own WP hierarchy files.
 *
 * @package Lumina\Core\Templates
 * @since 0.6.0
 */

declare( strict_types=1 );

namespace Lumina\Core\Templates;

use Lumina\Core\Container\Container;
use Lumina\Core\Render\TemplateResolver as RenderTemplateResolver;
use Lumina\Core\Render\ViewModel;
use Lumina\Core\Providers\ServiceProviderInterface;

/**
 * Registers template system services.
 */
final class TemplatesServiceProvider implements ServiceProviderInterface {

	/**
	 * {@inheritDoc}
	 *
	 * @param Container $container Service container.
	 */
	public function register( Container $container ): void {
		$container->singleton(
			'templates.resolver',
			static fn(): TemplateResolver => new TemplateResolver(
				dirname( __DIR__, 2 ) . '/templates'
			)
		);

		$container->singleton(
			'templates.partials',
			static fn( Container $c ): PartialLoader => new PartialLoader(
				new RenderTemplateResolver( dirname( __DIR__, 2 ) . '/templates/partials' ),
				static fn( string $file, array $args = array() ): string =>
					$c->get( 'render.engine' )->render( $file, new ViewModel( $args ) )
			)
		);

		$container->singleton(
			'templates.sections',
			static fn( Container $c ): Sections => new Sections(
				static fn( string $view, array $args = array() ): string =>
					$c->get( 'render.renderer' )->render( $view, $args )
			)
		);

		$container->singleton(
			'templates.bridge',
			static fn( Container $c ): ThemeTemplatesBridge => new ThemeTemplatesBridge(
				$c->get( 'templates.resolver' )
			)
		);

		$container->singleton(
			'templates.composer',
			static fn( Container $c ): Composer => new Composer(
				static fn( string $name, array $props = array() ): string =>
					$c->get( 'components.registry' )->render( $name, $props ),
				(array) include __DIR__ . '/config/maps.php'
			)
		);
	}

	/**
	 * {@inheritDoc}
	 *
	 * @param Container $container Service container.
	 */
	public function boot( Container $container ): void {
		if ( ! $container->has( 'templates.bridge' ) ) {
			return;
		}

		/**
		 * The resolved template bridge.
		 *
		 * @var ThemeTemplatesBridge
		 */
		$bridge = $container->get( 'templates.bridge' );
		$bridge->register();
	}
}
