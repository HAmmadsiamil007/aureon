<?php
/**
 * App — the public static facade for Phantom Core.
 *
 * Phase 1 (Bootstrap): exposes the framework's runtime entry point after boot:
 *   - instance()  — the singleton App
 *   - make()      — resolve a registered service from the Phase-1 registry
 *   - get()       — config shorthand
 *   - env()       — current environment (production|staging|development|local)
 *   - is_debug()  — debug mode flag
 *   - log()       — structured logging through the Log facade
 *
 * The Phase-1 service registry is a simple id → value map populated by the
 * Kernel during bootstrap. The full dependency-injection container
 * (Container\Container, PSR-11-style) lands in Phase 2 and will back make()
 * without changing this public API.
 *
 * @package Phantom\Core\Core
 * @since 0.1.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Core;

use Phantom\Core\Support\Debug\Log;
use Phantom\Core\Support\Env;

/**
 * Static entry point for Phantom Core.
 */
final class App {

	/**
	 * The singleton instance.
	 *
	 * @var App|null
	 */
	private static ?App $instance = null;

	/**
	 * Phase-1 service registry (id → value).
	 *
	 * @var array<string, mixed>
	 */
	private array $services = array();

	/**
	 * Loaded configuration array.
	 *
	 * @var array<string, mixed>
	 */
	private array $config = array();

	/**
	 * Private constructor — use App::instance().
	 */
	private function __construct() {}

	/**
	 * Return the singleton App instance.
	 *
	 * Creating the instance has no side effects (no boot, no config load) so it
	 * is safe to call at class-load time.
	 *
	 * @return App
	 */
	public static function instance(): App {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Register services into the Phase-1 registry.
	 *
	 * Called by the Kernel during bootstrap. Superseded by the Phase-2
	 * container without breaking this API.
	 *
	 * @param array<string, mixed> $services Map of service id → value.
	 * @return void
	 *
	 * @internal Framework bootstrap only.
	 */
	public function provide( array $services ): void {
		$this->services = array_merge( $this->services, $services );
	}

	/**
	 * Resolve a registered service by id.
	 *
	 * @param string $service_id Service id.
	 * @return mixed The service value, or null when unknown.
	 */
	public function make( string $service_id ): mixed {
		return $this->services[ $service_id ] ?? null;
	}

	/**
	 * Set the configuration array (called by the Kernel after load).
	 *
	 * @param array<string, mixed> $config Immutable config array.
	 * @return void
	 *
	 * @internal Framework bootstrap only.
	 */
	public function set_config( array $config ): void {
		$this->config = $config;
	}

	/**
	 * Config shorthand.
	 *
	 * @param string $id       Config key (dot notation unsupported pre-Phase 2).
	 * @param mixed  $fallback Fallback when the key is absent.
	 * @return mixed
	 */
	public function get( string $id, mixed $fallback = null ): mixed {
		return $this->config[ $id ] ?? $fallback;
	}

	/**
	 * Current environment type.
	 *
	 * @return string One of production|staging|development|local.
	 */
	public function env(): string {
		return Env::detect( $this->config );
	}

	/**
	 * Whether debug mode is enabled.
	 *
	 * @return bool
	 */
	public function is_debug(): bool {
		return Env::is_debug( $this->config );
	}

	/**
	 * Structured log through the Log facade.
	 *
	 * @param string               $level   PSR-3 level (debug|info|notice|warning|error|critical|alert|emergency).
	 * @param string               $message Message with optional {placeholders}.
	 * @param array<string, mixed> $context Context values (secrets auto-redacted).
	 * @return void
	 */
	public function log( string $level, string $message, array $context = array() ): void {
		Log::log( $level, $message, $context );
	}
}
