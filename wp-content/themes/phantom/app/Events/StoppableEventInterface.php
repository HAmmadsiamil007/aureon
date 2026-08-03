<?php
/**
 * StoppableEventInterface — propagation-stoppable domain events.
 *
 * Phase 2 (Framework Infrastructure): events that implement this contract can
 * halt the listener chain; the Dispatcher stops invoking remaining listeners
 * once propagation is flagged stopped (mirrors the Symfony/PSR-14 idiom).
 *
 * @package Phantom\Core\Events
 * @since 0.2.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Events;

/**
 * Contract for events whose dispatch can be short-circuited.
 */
interface StoppableEventInterface {

	/**
	 * Whether a listener stopped propagation.
	 *
	 * @return bool
	 */
	public function is_propagation_stopped(): bool;
}
