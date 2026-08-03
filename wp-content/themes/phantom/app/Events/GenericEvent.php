<?php
/**
 * GenericEvent — a simple named event carrying arbitrary params.
 *
 * Phase 2 (Framework Infrastructure): used by Dispatcher::map() to forward a
 * WordPress action's arguments into the domain bus as a phantom_core:wp_*
 * event. Subsystems may dispatch richer event classes; this type covers the
 * generic bridge case without extra machinery.
 *
 * @package Phantom\Core\Events
 * @since 0.2.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Events;

/**
 * Generic named event with keyed params.
 */
final class GenericEvent implements EventInterface {

	/**
	 * Event name (routing key).
	 *
	 * @var string
	 */
	private string $name;

	/**
	 * Event payload.
	 *
	 * @var array<string, mixed>
	 */
	private array $params;

	/**
	 * Constructor.
	 *
	 * @param string               $name   Event name.
	 * @param array<string, mixed> $params Keyed payload.
	 */
	public function __construct( string $name, array $params = array() ) {
		$this->name   = $name;
		$this->params = $params;
	}

	/**
	 * The event name (routing key).
	 *
	 * @return string
	 */
	public function name(): string {
		return $this->name;
	}

	/**
	 * All payload params.
	 *
	 * @return array<string, mixed>
	 */
	public function params(): array {
		return $this->params;
	}

	/**
	 * A single payload param with fallback.
	 *
	 * @param string $key      Param key.
	 * @param mixed  $fallback Fallback when absent.
	 * @return mixed
	 */
	public function param( string $key, mixed $fallback = null ): mixed {
		return $this->params[ $key ] ?? $fallback;
	}
}
