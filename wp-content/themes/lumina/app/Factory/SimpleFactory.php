<?php
/**
 * SimpleFactory — container-backed object factory.
 *
 * Phase 2 (Framework Infrastructure): wraps a callable map (abstract → factory)
 * and falls back to the container for unknown abstracts, so subsystems can
 * build objects through one indirection without reaching into the container.
 *
 * @package Lumina\Core\Factory
 * @since 0.2.0
 */

declare( strict_types=1 );

namespace Lumina\Core\Factory;

use Lumina\Core\Container\Container;
use Lumina\Core\Container\NotFoundException;

/**
 * Container-backed factory.
 */
final class SimpleFactory implements FactoryInterface {

	/**
	 * Backing container.
	 *
	 * @var Container
	 */
	private Container $container;

	/**
	 * Abstract → factory map.
	 *
	 * @var array<string, callable>
	 */
	private array $factories = array();

	/**
	 * Constructor.
	 *
	 * @param Container $container Backing container.
	 */
	public function __construct( Container $container ) {
		$this->container = $container;
	}

	/**
	 * Register a factory for an abstract.
	 *
	 * @param string   $abstract_id Abstract name.
	 * @param callable $factory     Factory receiving the container.
	 * @return void
	 */
	public function register( string $abstract_id, callable $factory ): void {
		$this->factories[ $abstract_id ] = $factory;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @param string               $abstract_id Abstract name.
	 * @param array<string, mixed> $args        Construction args.
	 * @return mixed
	 * @throws NotFoundException When nothing can build the abstract.
	 */
	public function make( string $abstract_id, array $args = array() ): mixed {
		if ( isset( $this->factories[ $abstract_id ] ) ) {
			return call_user_func( $this->factories[ $abstract_id ], $this->container, $args );
		}

		if ( $this->container->has( $abstract_id ) ) {
			return $this->container->get( $abstract_id );
		}

		// Last resort: auto-wire an autoloadable class.
		if ( class_exists( $abstract_id ) ) {
			return $this->container->register( $abstract_id, $abstract_id )->get( $abstract_id );
		}

		throw new NotFoundException(
			// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped -- exception messages are developer-facing, not HTML.
			'SimpleFactory cannot build "' . $abstract_id . '".'
		);
	}
}
