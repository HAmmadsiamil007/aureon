<?php
/**
 * Kernel — the Phantom Core bootstrap orchestrator.
 *
 * Phase 1 (Bootstrap): launched once from app/load.php on `plugins_loaded`
 * (priority 5). It runs the ordered boot sequence:
 *
 *   config → env → flags → logger → errorHandler
 *
 * then raises `phantom_core:ready`. Every step result is published into the
 * App service registry, making the framework runtime reachable via
 * App::instance()->make('...').
 *
 * Lifecycle events (ADR-006):
 *   phantom_core:booting      — before any step
 *   phantom_core:booted       — after all steps succeeded
 *   phantom_core:ready        — framework fully booted
 *   phantom_core:boot_error   — on failure (with Throwable + step id)
 *
 * The Kernel never throws on the WordPress surface; boot failures are logged
 * and converted to events.
 *
 * @package Phantom\Core\Boot
 * @since 0.1.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Boot;

use Phantom\Core\Config\ConfigLoader;
use Phantom\Core\Core\App;
use Phantom\Core\Support\Debug\Log;
use Phantom\Core\Support\Debug\Loggers;
use Phantom\Core\Support\Env;
use Phantom\Core\Support\ErrorHandler;
use Phantom\Core\Support\FeatureFlags;

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
	 * Register the Phase-1 boot steps.
	 *
	 * @return void
	 */
	public function register(): void {
		$app = App::instance();

		// Config must load first — every later step reads from it.
		$config_step = static function (): array {
			$loader = new ConfigLoader( dirname( __DIR__, 2 ) );
			$config = $loader->load();

			App::instance()->set_config( $config );

			return array( 'config' => $config );
		};

		$env_step = static function ( array $context ): array {
			$env = Env::detect( $context['config'] ?? array() );
			$app = App::instance();
			$app->provide( array( 'env' => $env ) );

			return array( 'env' => $env );
		};

		$flags_step = static function ( array $context ): array {
			$config = $context['config'] ?? array();
			$flags  = new FeatureFlags( (array) ( $config['features'] ?? array() ) );
			$app    = App::instance();
			$app->provide( array( 'flags' => $flags ) );

			return array( 'flags' => $flags );
		};

		$logger_step = static function ( array $context ): array {
			$config  = $context['config'] ?? array();
			$loggers = new Loggers( (array) ( $config['log'] ?? array() ) );
			$app     = App::instance();

			Log::set_writer( $loggers );
			$app->provide( array( 'logger' => $loggers ) );

			return array( 'logger' => $loggers );
		};

		$error_step = static function ( array $context ): array {
			$config = $context['config'] ?? array();
			$errors = new ErrorHandler();

			if ( ! empty( $config['error_handler']['register'] ) ) {
				$errors->register();
			}

			$app = App::instance();
			$app->provide( array( 'errorHandler' => $errors ) );

			return array( 'errorHandler' => $errors );
		};

		$this->sequencer()
			->add( 'config', $config_step, 10 )
			->add( 'env', $env_step, 20 )
			->add( 'flags', $flags_step, 30 )
			->add( 'logger', $logger_step, 40 )
			->add( 'errorHandler', $error_step, 50 );
	}

	/**
	 * Boot the kernel: run the sequence and raise lifecycle events.
	 *
	 * `phantom_core:ready` is only raised when every boot step succeeded; on a
	 * partial failure the sequence stops, `phantom_core:boot_error` fires, and
	 * no ready event is emitted — so listeners never see a false "ready".
	 *
	 * @return void
	 */
	public function boot(): void {
		if ( $this->booted ) {
			return;
		}

		$this->booted = true;

		$this->raise( 'phantom_core:booting' );
		$this->register();
		$this->sequencer()->run();

		if ( $this->sequencer()->has_failed() ) {
			return;
		}

		$this->raise( 'phantom_core:booted' );
		$this->raise( 'phantom_core:ready' );
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
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- dynamic domain-event hook (ADR-006); every caller passes a phantom_core:* hook.
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
