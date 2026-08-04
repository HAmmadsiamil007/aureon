<?php
/**
 * Kernel — the Lumina Core bootstrap orchestrator.
 *
 * Phase 1–2: launched once from app/load.php on `plugins_loaded` (priority 5).
 * It runs the ordered boot sequence:
 *
 *   config → container → env → flags → logger → errorHandler
 *   → hooks → events → registry → cache → factory → providers
 *
 * then raises `lumina_core:ready`. Every step result is published into the
 * App service registry + container, making the framework runtime reachable via
 * App::instance()->make('...') (ADR-013/014).
 *
 * Lifecycle events (ADR-006):
 *   lumina_core:booting      — before any step
 *   lumina_core:booted       — after all steps succeeded
 *   lumina_core:ready        — framework fully booted (only on full success)
 *   lumina_core:boot_error   — on failure (with Throwable + step id)
 *
 * The Kernel never throws on the WordPress surface; boot failures are logged
 * and converted to events.
 *
 * @package Lumina\Core\Boot
 * @since 0.1.0
 */

declare( strict_types=1 );

namespace Lumina\Core\Boot;

use Lumina\Core\Cache\ObjectCache;
use Lumina\Core\Cache\TransientCache;
use Lumina\Core\Config\ConfigLoader;
use Lumina\Core\Config\Repository;
use Lumina\Core\Container\Container;
use Lumina\Core\Core\App;
use Lumina\Core\Events\Dispatcher;
use Lumina\Core\Factory\SimpleFactory;
use Lumina\Core\Hooks\HookManager;
use Lumina\Core\Hooks\WpBridge;
use Lumina\Core\Providers\ServiceProviderInterface;
use Lumina\Core\Registry\ArrayRegistry;
use Lumina\Core\Registry\DynamicRegistry;
use Lumina\Core\Support\Debug\Log;
use Lumina\Core\Support\Debug\Loggers;
use Lumina\Core\Support\Env;
use Lumina\Core\Support\ErrorHandler;
use Lumina\Core\Support\FeatureFlags;

/**
 * Boot orchestrator (singleton).
 */
final class Kernel implements BootableInterface {

	/**
	 * Singleton instance.
	 *
	 * @var Kernel|null
	 */
	private static ?Kernel $instance = null;

	/**
	 * Whether bootstrap already ran.
	 *
	 * @var bool
	 */
	private bool $booted = false;

	/**
	 * Private constructor — use Kernel::launch().
	 */
	private function __construct() {}

	/**
	 * The single entry point invoked by app/load.php.
	 *
	 * @return void
	 */
	public static function launch(): void {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		self::$instance->boot();
	}

	/**
	 * Register the boot steps.
	 *
	 * @return void
	 */
	public function register(): void {
		$app = App::instance();

		// Config must load first — every later step reads from it.
		$config_step = static function (): array {
			$loader = new ConfigLoader( dirname( __DIR__, 2 ) );
			$config = $loader->load();
			$repo   = new Repository( $config );

			App::instance()->set_config( $config );
			App::instance()->set_config_repository( $repo );

			return array(
				'config'     => $config,
				'repository' => $repo,
			);
		};

		$container_step = static function ( array $context ): array {
			$container = new Container();

			if ( isset( $context['repository'] ) && $context['repository'] instanceof Repository ) {
				$container->set( 'config', $context['repository'] );
			}

			App::instance()->set_container( $container );
			App::instance()->provide( array( 'container' => $container ) );

			return array( 'container' => $container );
		};

		$env_step = static function ( array $context ): array {
			$env = Env::detect( $context['config'] ?? array() );

			App::instance()->provide( array( 'env' => $env ) );

			return array( 'env' => $env );
		};

		$flags_step = static function ( array $context ): array {
			$config = $context['config'] ?? array();
			$flags  = new FeatureFlags( (array) ( $config['features'] ?? array() ) );

			App::instance()->provide( array( 'flags' => $flags ) );

			return array( 'flags' => $flags );
		};

		$logger_step = static function ( array $context ): array {
			$config  = $context['config'] ?? array();
			$loggers = new Loggers( (array) ( $config['log'] ?? array() ) );

			Log::set_writer( $loggers );
			App::instance()->provide( array( 'logger' => $loggers ) );

			return array( 'logger' => $loggers );
		};

		$error_step = static function ( array $context ): array {
			$config = $context['config'] ?? array();
			$errors = new ErrorHandler();

			if ( ! empty( $config['error_handler']['register'] ) ) {
				$errors->register();
			}

			App::instance()->provide( array( 'errorHandler' => $errors ) );

			return array( 'errorHandler' => $errors );
		};

		$hooks_step = static function (): array {
			$hooks = new HookManager( new WpBridge() );

			App::instance()->provide( array( 'hooks' => $hooks ) );

			return array( 'hooks' => $hooks );
		};

		$events_step = static function ( array $context ): array {
			$hooks      = $context['hooks'] ?? null;
			$dispatcher = new Dispatcher( $hooks instanceof HookManager ? $hooks : null );

			App::instance()->provide( array( 'events' => $dispatcher ) );

			return array( 'events' => $dispatcher );
		};

		$registry_step = static function (): array {
			$array_registry   = new ArrayRegistry();
			$dynamic_registry = new DynamicRegistry();

			App::instance()->provide(
				array(
					'registry.array'   => $array_registry,
					'registry.dynamic' => $dynamic_registry,
				)
			);

			return array(
				'registry.array'   => $array_registry,
				'registry.dynamic' => $dynamic_registry,
			);
		};

		$cache_step = static function (): array {
			$object_cache    = new ObjectCache();
			$transient_cache = new TransientCache();

			App::instance()->provide(
				array(
					'cache.object'    => $object_cache,
					'cache.transient' => $transient_cache,
				)
			);

			return array(
				'cache.object'    => $object_cache,
				'cache.transient' => $transient_cache,
			);
		};

		$factory_step = static function ( array $context ): array {
			$container = $context['container'] ?? null;
			$factory   = new SimpleFactory( $container instanceof Container ? $container : new Container() );

			App::instance()->provide( array( 'factory' => $factory ) );

			return array( 'factory' => $factory );
		};

		$providers_step = static function ( array $context ): array {
			$config    = $context['config'] ?? array();
			$container = $context['container'] ?? null;

			if ( ! $container instanceof Container ) {
				return array( 'providers' => array() );
			}

			$registered = array();

			foreach ( (array) ( $config['providers'] ?? array() ) as $provider_class ) {
				$is_provider = is_string( $provider_class )
					&& is_subclass_of( $provider_class, ServiceProviderInterface::class );

				if ( ! $is_provider ) {
					throw new \InvalidArgumentException(
						// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped -- exception messages are developer-facing, not HTML.
						'Provider "' . (string) $provider_class . '" must implement '
							. ServiceProviderInterface::class . '.'
					);
				}

				$provider = new $provider_class();
				$provider->register( $container );
				$registered[] = $provider;
			}

			foreach ( $registered as $provider ) {
				$provider->boot( $container );
			}

			return array( 'providers' => $registered );
		};

		$this->sequencer()
			->add( 'config', $config_step, 10 )
			->add( 'container', $container_step, 20 )
			->add( 'env', $env_step, 30 )
			->add( 'flags', $flags_step, 40 )
			->add( 'logger', $logger_step, 50 )
			->add( 'errorHandler', $error_step, 60 )
			->add( 'hooks', $hooks_step, 70 )
			->add( 'events', $events_step, 80 )
			->add( 'registry', $registry_step, 90 )
			->add( 'cache', $cache_step, 100 )
			->add( 'factory', $factory_step, 110 )
			->add( 'providers', $providers_step, 120 );
	}

	/**
	 * Boot the kernel: run the sequence and raise lifecycle events.
	 *
	 * `lumina_core:ready` is only raised when every boot step succeeded; on a
	 * partial failure the sequence stops, `lumina_core:boot_error` fires, and
	 * no ready event is emitted — so listeners never see a false "ready".
	 *
	 * @return void
	 */
	public function boot(): void {
		if ( $this->booted ) {
			return;
		}

		$this->booted = true;

		$this->raise( 'lumina_core:booting' );
		$this->register();
		$this->sequencer()->run();

		if ( $this->sequencer()->has_failed() ) {
			return;
		}

		$this->raise( 'lumina_core:booted' );
		$this->raise( 'lumina_core:ready' );
	}

	/**
	 * Raise a domain event (no-op when WP is not loaded, e.g. CLI smoke runs).
	 *
	 * @param string $hook Hook name.
	 * @param mixed  ...$args Event args.
	 * @return void
	 */
	private function raise( string $hook, mixed ...$args ): void {
		if ( function_exists( 'do_action' ) ) {
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- dynamic domain-event hook (ADR-006); every caller passes a lumina_core:* hook.
			do_action( $hook, ...$args );
		}
	}

	/**
	 * The boot Sequencer (lazily created, shared across register/boot).
	 *
	 * @return Sequencer
	 */
	private function sequencer(): Sequencer {
		static $sequencer = null;

		if ( null === $sequencer ) {
			$sequencer = new Sequencer();
		}

		return $sequencer;
	}
}
