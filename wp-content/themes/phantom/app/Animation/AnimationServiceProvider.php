<?php
/**
 * AnimationServiceProvider — wire the Animation Engine into the container.
 *
 * Phase 10 (Animation Engine): binds the preset registry, reduced-motion
 * gate, performance gates, Lenis/Three/Trigger facades and the `animation.engine`
 * controller. All services are lazy; the animation entry is only enqueued
 * when the engine reports active (non-empty registry / lenis / three /
 * triggers). A canonical `reveal` preset is registered for acceptance
 * coverage (plan §Phase 10).
 *
 * @package Phantom\Core\Animation
 * @since 0.10.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Animation;

use Phantom\Core\Animation\Scroll\Trigger;
use Phantom\Core\Container\Container;
use Phantom\Core\Providers\ServiceProviderInterface;

/**
 * Registers animation services.
 */
final class AnimationServiceProvider implements ServiceProviderInterface {

	/**
	 * {@inheritDoc}
	 *
	 * @param Container $container Service container.
	 */
	public function register( Container $container ): void {
		$container->set( 'animation.registry', new AnimationRegistry() );
		$container->set( 'animation.reduced_motion', new ReducedMotion( true ) );
		$container->set( 'animation.breaking', new Breaking() );
		$container->set( 'animation.lenis', new Lenis() );
		$container->set( 'animation.three', new Three() );
		$container->set( 'animation.trigger', new Trigger() );

		$container->singleton(
			'animation.engine',
			static function ( Container $c ): Engine {
				$engine = new Engine(
					$c->get( 'animation.registry' ),
					$c->get( 'animation.reduced_motion' ),
					$c->get( 'animation.breaking' ),
					$c->get( 'animation.lenis' ),
					$c->get( 'animation.three' ),
					$c->get( 'animation.trigger' )
				);

				// Canonical reveal preset (plan §Phase 10 acceptance):
				// data-phantom-anim="reveal" lifts into GSAP on scroll.
				$engine->registry()->register(
					new Preset(
						'reveal',
						'reveal',
						'[data-phantom-anim="reveal"]',
						array(
							'opacity'  => 0,
							'y'        => 24,
							'duration' => 0.6,
							'ease'     => 'power2.out',
						),
						array(
							'start' => 'top 85%',
							'once'  => true,
						),
						true
					)
				);

				return $engine;
			}
		);
	}

	/**
	 * {@inheritDoc}
	 *
	 * @param Container $container Service container.
	 */
	public function boot( Container $container ): void {
		if ( ! $container->has( 'animation.engine' ) ) {
			return;
		}

		/**
		 * The animation engine.
		 *
		 * @var Engine
		 */
		$engine = $container->get( 'animation.engine' );
		$engine->ready();

		// Enqueue the code-split animation entry only when the engine is active
		// (plan §Phase 10: "enqueue phantom-animation only when registry
		// non-empty"). GSAP/Lenis/Three are dynamic imports inside it.
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core functions.
		if ( ! function_exists( 'add_action' ) || ! function_exists( 'wp_add_inline_style' ) ) {
			return;
		}

		if ( ! $engine->is_active() || ! $container->has( 'assets.loader' ) ) {
			return;
		}

		add_action(
			'wp_enqueue_scripts',
			static function () use ( $container, $engine ): void {
				/**
				 * The resolved asset loader.
				 *
			 * @var \Phantom\Core\Assets\AssetLoader
				 */
				$loader = $container->get( 'assets.loader' );

				// Reduced-motion CSS guard needs a registered style handle to
				// attach inline CSS to (zero-JS fallback). Versioned with the
				// framework version so browser caches refresh on release.
				// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core functions.
				wp_register_style( 'phantom-animation', false, array(), \Phantom\Core\Core\Version::VERSION );
				wp_enqueue_style( 'phantom-animation' );
				// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
				wp_add_inline_style( 'phantom-animation', $engine->reduced_motion_guard() );

				// Deliver the boot config to the runtime. The handle mirrors
				// AssetLoader::js(): 'phantom-' . sanitize_key( $src ).
				$loader->js( 'assets-src/ts/animation.ts' );

				// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
				wp_localize_script(
					'phantom-' . sanitize_key( 'assets-src/ts/animation.ts' ),
					'phantomAnimation',
					$engine->boot_config()
				);
			},
			10
		);
	}
}
