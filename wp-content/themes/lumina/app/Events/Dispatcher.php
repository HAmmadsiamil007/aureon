<?php
/**
 * Dispatcher — in-process domain event dispatcher.
 *
 * Phase 2 (Framework Infrastructure): routes EventInterface objects to
 * listeners in registration order (plan §Phase 2 acceptance). Listeners that
 * receive a StoppableEventInterface may halt the chain. Dispatcher::map()
 * bridges a WordPress action to the domain bus as a lumina_core:wp_* event
 * via the injected HookManager — the WP surface stays thin (ADR-006).
 *
 * @package Lumina\Core\Events
 * @since 0.2.0
 */

declare( strict_types=1 );

namespace Lumina\Core\Events;

use Lumina\Core\Hooks\HookManager;

/**
 * In-process event dispatcher.
 */
final class Dispatcher {

	/**
	 * Listeners keyed by event name.
	 *
	 * @var array<string, array<int, callable>>
	 */
	private array $listeners = array();

	/**
	 * Hook manager used to bridge WP actions (optional).
	 *
	 * @var HookManager|null
	 */
	private ?HookManager $hooks;

	/**
	 * Constructor.
	 *
	 * @param HookManager|null $hooks Hook manager for WP-action bridging.
	 */
	public function __construct( ?HookManager $hooks = null ) {
		$this->hooks = $hooks;
	}

	/**
	 * Register a listener for an event name.
	 *
	 * Listeners run in registration order for a single dispatch.
	 *
	 * @param string   $event    Event name (routing key).
	 * @param callable $listener Listener receiving the event object.
	 * @return void
	 */
	public function listen( string $event, callable $listener ): void {
		$this->listeners[ $event ][] = $listener;
	}

	/**
	 * Dispatch an event to its listeners.
	 *
	 * @param object $event Event object (EventInterface preferred).
	 * @return object The dispatched event (for inspection/fluent use).
	 */
	public function dispatch( object $event ): object {
		$name = $event instanceof EventInterface ? $event->name() : get_class( $event );

		foreach ( $this->listeners[ $name ] ?? array() as $listener ) {
			call_user_func( $listener, $event );

			if ( $event instanceof StoppableEventInterface && $event->is_propagation_stopped() ) {
				break;
			}
		}

		return $event;
	}

	/**
	 * Bridge a WordPress action to a domain event.
	 *
	 * When the WP action fires, a GenericEvent is dispatched with the action
	 * arguments under the 'args' key. Guarded: without the HookManager (WP-free
	 * CLI) the mapping is a no-op.
	 *
	 * @param string      $wp_hook WordPress action name.
	 * @param string|null $event   Optional domain event name; defaults to
	 *                             lumina_core:wp_{$wp_hook}.
	 * @return void
	 */
	public function map( string $wp_hook, ?string $event = null ): void {
		if ( null === $this->hooks ) {
			return;
		}

		$event_name = $event ?? 'lumina_core:wp_' . $wp_hook;

		$this->hooks->add_action(
			$wp_hook,
			function ( mixed ...$args ) use ( $event_name ): void {
				$this->dispatch(
					new GenericEvent(
						$event_name,
						array( 'args' => array_values( $args ) )
					)
				);
			}
		);
	}
}
