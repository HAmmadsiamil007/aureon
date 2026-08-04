<?php
/**
 * EventInterface — domain event contract.
 *
 * Phase 2 (Framework Infrastructure): every domain event exposes a stable
 * name (ADR-006 double-colon namespace, e.g. lumina_core:ready) so the
 * Dispatcher can route it to registered listeners. The name is the single
 * routing key; payload lives on the event object.
 *
 * @package Lumina\Core\Events
 * @since 0.2.0
 */

declare( strict_types=1 );

namespace Lumina\Core\Events;

/**
 * Contract for dispatcher-routable domain events.
 */
interface EventInterface {

	/**
	 * The event name (routing key).
	 *
	 * @return string
	 */
	public function name(): string;
}
