<?php
/**
 * Container — PSR-11-style dependency injection container.
 *
 * Phase 2 (Framework Infrastructure): the service container that supersedes
 * the Phase-1 App registry (ADR-013). It supports raw values (set()), explicit
 * closure bindings (register()/singleton()), and class-string bindings that are
 * auto-wired "where safe" via constructor reflection. Resolutions are lazy —
 * a service is only built on first get(). Cycles are detected and reported as
 * CircularDependencyException. Unknown ids throw NotFoundException (PSR-11).
 *
 * The App facade delegates make() here while keeping its public API stable.
 *
 * @package Phantom\Core\Container
 * @since 0.2.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Container;

/**
 * PSR-11-style service container.
 */
final class Container {

	/**
	 * Immutable bindings by service id.
	 *
	 * @var array<string, Definition>
	 */
	private array $definitions = array();

	/**
	 * Resolved singleton instances.
	 *
	 * @var array<string, mixed>
	 */
	private array $instances = array();

	/**
	 * Raw values stored via set().
	 *
	 * @var array<string, mixed>
	 */
	private array $values = array();

	/**
	 * In-flight resolution stack (cycle detection).
	 *
	 * @var string[]
	 */
	private array $resolving = array();

	/**
	 * Store an already-resolved value (effective singleton).
	 *
	 * @param string $id    Service id.
	 * @param mixed  $value Resolved value.
	 * @return $this
	 */
	public function set( string $id, mixed $value ): self {
		$this->values[ $id ] = $value;

		// A raw value replaces any prior binding or cached singleton.
		unset( $this->definitions[ $id ], $this->instances[ $id ] );

		return $this;
	}

	/**
	 * Register a binding.
	 *
	 * @param string                $id      Service id.
	 * @param callable|class-string $factory Factory closure (receives the Container)
	 *                                       or an autoloadable class name.
	 * @param array<string, mixed>  $options Optional 'scope' (Definition::SCOPE_*).
	 * @return $this
	 */
	public function register( string $id, callable|string $factory, array $options = array() ): self {
		$scope = isset( $options['scope'] ) && Definition::SCOPE_SINGLETON === $options['scope']
			? Definition::SCOPE_SINGLETON
			: Definition::SCOPE_TRANSIENT;

		$factory = is_string( $factory ) ? $this->class_factory( $factory ) : $factory;

		$this->definitions[ $id ] = new Definition( $id, $factory, $scope );
		unset( $this->values[ $id ], $this->instances[ $id ] );

		return $this;
	}

	/**
	 * Register a singleton binding.
	 *
	 * @param string   $id      Service id.
	 * @param callable $factory Factory closure (receives the Container).
	 * @return $this
	 */
	public function singleton( string $id, callable $factory ): self {
		return $this->register(
			$id,
			$factory,
			array( 'scope' => Definition::SCOPE_SINGLETON )
		);
	}

	/**
	 * Whether a service id is bound.
	 *
	 * @param string $id Service id.
	 * @return bool
	 */
	public function has( string $id ): bool {
		return isset( $this->values[ $id ] ) || isset( $this->definitions[ $id ] );
	}

	/**
	 * Resolve a service, building it lazily on first access.
	 *
	 * @param string $id Service id.
	 * @return mixed
	 * @throws NotFoundException          When the id is unregistered.
	 * @throws CircularDependencyException When resolution enters a cycle.
	 */
	public function get( string $id ): mixed {
		if ( array_key_exists( $id, $this->values ) ) {
			return $this->values[ $id ];
		}

		if ( ! isset( $this->definitions[ $id ] ) ) {
			throw new NotFoundException(
				// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped -- exception messages are developer-facing, not HTML.
				'Service "' . $id . '" is not registered.'
			);
		}

		if ( in_array( $id, $this->resolving, true ) ) {
			throw new CircularDependencyException(
				// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped -- exception messages are developer-facing, not HTML.
				'Circular dependency detected while resolving "' . $id . '".'
			);
		}

		$definition = $this->definitions[ $id ];

		if ( $definition->is_singleton() && array_key_exists( $id, $this->instances ) ) {
			return $this->instances[ $id ];
		}

		$this->resolving[] = $id;

		try {
			$value = $definition->build( $this );
		} finally {
			array_pop( $this->resolving );
		}

		if ( $definition->is_singleton() ) {
			$this->instances[ $id ] = $value;
		}

		return $value;
	}

	/**
	 * Build a factory for a class-string binding.
	 *
	 * @param class-string $class_name Autoloadable class name.
	 * @return callable
	 */
	private function class_factory( string $class_name ): callable {
		return static function ( Container $container ) use ( $class_name ): mixed {
			return $container->resolve_class( $class_name );
		};
	}

	/**
	 * Instantiate a class, auto-wiring constructor parameters from the container.
	 *
	 * Reflection is used only for class-string bindings; explicit closure
	 * bindings stay zero-reflection in production (ADR-014). Each constructor
	 * parameter resolves from a matching class binding when present, falls back
	 * to its declared default, and otherwise aborts with NotFoundException.
	 *
	 * @param class-string $class_name Class name.
	 * @return object
	 * @throws NotFoundException When the class or a parameter cannot resolve.
	 */
	private function resolve_class( string $class_name ): object {
		if ( ! class_exists( $class_name ) ) {
			throw new NotFoundException(
				// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped -- exception messages are developer-facing, not HTML.
				'Class "' . $class_name . '" is not autoloadable.'
			);
		}

		$reflection = new \ReflectionClass( $class_name );
		$ctor       = $reflection->getConstructor();

		if ( null === $ctor ) {
			return new $class_name();
		}

		$args = array();

		foreach ( $ctor->getParameters() as $param ) {
			$type = $param->getType();

			if ( $type instanceof \ReflectionNamedType && ! $type->isBuiltin() ) {
				$name = $type->getName();

				if ( $this->has( $name ) ) {
					$args[] = $this->get( $name );
					continue;
				}
			}

			if ( $param->isDefaultValueAvailable() ) {
				$args[] = $param->getDefaultValue();
				continue;               }

				$message = 'Cannot auto-wire "' . $class_name
					. '": constructor parameter $' . $param->getName() . ' is unresolvable.';

				// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped -- exception messages are developer-facing, not HTML.
				throw new NotFoundException( $message );
		}

		return $reflection->newInstanceArgs( $args );
	}
}
