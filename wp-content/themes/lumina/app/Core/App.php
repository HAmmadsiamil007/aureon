<?php
/**
 * App — the public static facade for Lumina Core.
 *
 * Phase 1–2: exposes the framework's runtime entry point after boot:
 *   - instance()  — the singleton App
 *   - make()      — resolve a service (container-backed since Phase 2)
 *   - get()       — config shorthand (dot notation via Config\Repository)
 *   - env()       — current environment (production|staging|development|local)
 *   - is_debug()  — debug mode flag
 *   - log()       — structured logging through the Log facade
 *
 * The Phase-1 id → value registry remains as the pre-container fallback; since
 * Phase 2 the Container\Container (PSR-11-style) backs make(), and get() routes
 * through the immutable Config\Repository. This public API is stable across
 * both phases (ADR-013/014).
 *
 * @package Lumina\Core\Core
 * @since 0.1.0
 */

declare( strict_types=1 );

namespace Lumina\Core\Core;

use Lumina\Core\Config\Repository;
use Lumina\Core\Container\Container;
use Lumina\Core\Support\Debug\Log;
use Lumina\Core\Support\Env;

/**
 * Static entry point for Lumina Core.
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
	 * Kept as the pre-container fallback; superseded by $container in Phase 2
	 * (ADR-013/014) without breaking consumers of make().
	 *
	 * @var array<string, mixed>
	 */
	private array $services = array();

	/**
	 * Phase-2 service container (set by the Kernel during boot).
	 *
	 * @var Container|null
	 */
	private ?Container $container = null;

	/**
	 * Immutable config repository (set by the Kernel during boot).
	 *
	 * @var Repository|null
	 */
	private ?Repository $config_repository = null;

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
	 * Register services into the registry (and the container when present).
	 *
	 * Called by the Kernel during bootstrap.
	 *
	 * @param array<string, mixed> $services Map of service id → value.
	 * @return void
	 *
	 * @internal Framework bootstrap only.
	 */
	public function provide( array $services ): void {
		$this->services = array_merge( $this->services, $services );

		if ( null !== $this->container ) {
			foreach ( $services as $id => $value ) {
				$this->container->set( (string) $id, $value );
			}
		}
	}

	/**
	 * Attach the Phase-2 container (called by the Kernel).
	 *
	 * @param Container $container Service container.
	 * @return void
	 *
	 * @internal Framework bootstrap only.
	 */
	public function set_container( Container $container ): void {
		$this->container = $container;
	}

	/**
	 * Attach the config repository (called by the Kernel).
	 *
	 * @param Repository $repository Immutable config repository.
	 * @return void
	 *
	 * @internal Framework bootstrap only.
	 */
	public function set_config_repository( Repository $repository ): void {
		$this->config_repository = $repository;
	}

	/**
	 * Resolve a registered service by id.
	 *
	 * Phase 2: resolution delegates to the container when attached; the Phase-1
	 * registry remains the fallback. Unknown ids resolve to null (Phase-1
	 * contract) rather than throwing.
	 *
	 * @param string $service_id Service id.
	 * @return mixed The service value, or null when unknown.
	 */
	public function make( string $service_id ): mixed {
		if ( null !== $this->container && $this->container->has( $service_id ) ) {
			return $this->container->get( $service_id );
		}

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
	 * Config shorthand (dot notation supported via the repository).
	 *
	 * @param string $id       Config key, e.g. "log.level" (dot notation).
	 * @param mixed  $fallback Fallback when the key is absent.
	 * @return mixed
	 */
	public function get( string $id, mixed $fallback = null ): mixed {
		if ( null !== $this->config_repository ) {
			return $this->config_repository->get( $id, $fallback );
		}

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
