<?php
/**
 * BridgesServiceProvider — wire the Plugin Bridge layer into the container.
 *
 * Phase 8 (Plugin Bridges): binds `bridges.registry` (lazy slug → factory
 * map), `bridges.manager` (public facade), `bridges.matrix` (capability
 * matrix from config/plugins.php) and `bridges.health` (presence/version
 * checks). The canonical 12 bridges are registered as one-line factories so
 * no bridge class is ever instantiated until requested — absent plugins
 * simply resolve to inactive adapters and Lumina never throws (ADR-007).
 *
 * @package Lumina\Core\Bridges
 * @since 0.8.0
 */

declare( strict_types=1 );

namespace Lumina\Core\Bridges;

use Lumina\Core\Container\Container;
use Lumina\Core\Providers\ServiceProviderInterface;

/**
 * Registers bridge services.
 */
final class BridgesServiceProvider implements ServiceProviderInterface {

	/**
	 * Canonical bridge factories: slug → class-string.
	 *
	 * @var array<string, class-string<BridgeInterface>>
	 */
	private const FACTORIES = array(
		'acf'         => Acf\AcfBridge::class,
		'rankmath'    => RankMath\RankMathBridge::class,
		'yoast'       => Yoast\YoastBridge::class,
		'wpml'        => Wpml\WpmlBridge::class,
		'polylang'    => Polylang\PolylangBridge::class,
		'fluentforms' => FluentForms\FluentFormsBridge::class,
		'gravity'     => Gravity\GravityBridge::class,
		'wpforms'     => Wpforms\WpformsBridge::class,
		'buddypress'  => Buddypress\BuddyBridge::class,
		'bbpress'     => Bbpress\BbpressBridge::class,
		'learndash'   => Learndash\LearndashBridge::class,
		'tec'         => Tec\TecBridge::class,
	);

	/**
	 * {@inheritDoc}
	 *
	 * @param Container $container Service container.
	 */
	public function register( Container $container ): void {
		$container->singleton(
			'bridges.registry',
			static function (): Registry {
				$registry = new Registry();

				foreach ( self::FACTORIES as $slug => $class ) {
					$registry->register(
						$slug,
						static fn(): BridgeInterface => new $class()
					);
				}

				return $registry;
			}
		);

		$container->singleton(
			'bridges.manager',
			static function ( Container $c ): BridgeManager {
				return new BridgeManager( $c->get( 'bridges.registry' ) );
			}
		);

		$container->singleton(
			'bridges.matrix',
			static function (): FeatureMatrix {
				return new FeatureMatrix( __DIR__ . '/config/plugins.php' );
			}
		);

		$container->singleton(
			'bridges.health',
			static function (): HealthCheck {
				return new HealthCheck();
			}
		);
	}

	/**
	 * {@inheritDoc}
	 *
	 * @param Container $container Service container.
	 */
	public function boot( Container $container ): void {
		// Bridges are passive capability adapters — nothing to boot.
	}
}
