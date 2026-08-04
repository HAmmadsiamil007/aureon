<?php
/**
 * Definition — immutable container binding.
 *
 * Phase 2 (Framework Infrastructure): a binding pairs a service id with a
 * factory callable and a resolution scope (transient|singleton). Bindings are
 * immutable once registered; the Container resolves them lazily on first
 * get(). Factories receive the resolving Container so nested dependencies are
 * injected explicitly (composition over inheritance, ADR-014).
 *
 * @package Lumina\Core\Container
 * @since 0.2.0
 */

declare( strict_types=1 );

namespace Lumina\Core\Container;

/**
 * Immutable service binding.
 */
final class Definition {

	/**
	 * A new instance is built on every get().
	 *
	 * @var string
	 */
	public const SCOPE_TRANSIENT = 'transient';

	/**
	 * One instance is built and reused for the process lifetime.
	 *
	 * @var string
	 */
	public const SCOPE_SINGLETON = 'singleton';

	/**
	 * Service id.
	 *
	 * @var string
	 */
	private string $id;

	/**
	 * Factory callable (receives the Container).
	 *
	 * @var callable
	 */
	private $factory;

	/**
	 * Resolution scope (self::SCOPE_*).
	 *
	 * @var string
	 */
	private string $scope;

	/**
	 * Constructor.
	 *
	 * @param string   $id      Service id.
	 * @param callable $factory Factory callable (receives the Container).
	 * @param string   $scope   self::SCOPE_TRANSIENT|self::SCOPE_SINGLETON.
	 */
	public function __construct( string $id, callable $factory, string $scope = self::SCOPE_TRANSIENT ) {
		$this->id      = $id;
		$this->factory = $factory;
		$this->scope   = $scope;
	}

	/**
	 * The service id.
	 *
	 * @return string
	 */
	public function id(): string {
		return $this->id;
	}

	/**
	 * Build the service value by invoking the factory.
	 *
	 * @param Container $container Resolving container.
	 * @return mixed
	 */
	public function build( Container $container ): mixed {
		return call_user_func( $this->factory, $container );
	}

	/**
	 * Whether the binding resolves once per process (singleton scope).
	 *
	 * @return bool
	 */
	public function is_singleton(): bool {
		return self::SCOPE_SINGLETON === $this->scope;
	}
}
