<?php
/**
 * TecBridge — The Events Calendar capability adapter.
 *
 * Phase 8 (Plugin Bridges): events list + ticket count through the public
 * TEC API, capability-guarded; safe defaults when absent.
 *
 * @package Lumina\Core\Bridges\Tec
 * @since 0.8.0
 */

declare( strict_types=1 );

namespace Lumina\Core\Bridges\Tec;

use Lumina\Core\Bridges\Bridge;

/**
 * The Events Calendar adapter.
 */
final class TecBridge extends Bridge {

	/**
	 * {@inheritDoc}
	 */
	public function slug(): string {
		return 'tec';
	}

	/**
	 * {@inheritDoc}
	 */
	public function name(): string {
		return 'The Events Calendar';
	}

	/**
	 * {@inheritDoc}
	 */
	public function is_active(): bool {
		return $this->guard( 'Tribe__Events__Main' ) || defined( 'TRIBE_EVENTS_FILE' );
	}

	/**
	 * {@inheritDoc}
	 */
	public function version(): string {
		return $this->constant_version( 'Tribe__Events__Main::VERSION' );
	}

	/**
	 * {@inheritDoc}
	 */
	public function capabilities(): array {
		return array( 'events', 'ticket_count' );
	}

	/**
	 * A list of upcoming events.
	 *
	 * @param int $limit Max events (default 5).
	 * @return list<int>
	 */
	public function events( int $limit = 5 ): array {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- TEC core function.
		if ( ! function_exists( 'tribe_get_events' ) ) {
			return array();
		}

		$events = tribe_get_events(
			array(
				'posts_per_page' => max( 1, $limit ),
				'start_date'     => 'now',
			)
		);

		$ids = array();

		if ( is_array( $events ) ) {
			foreach ( $events as $event ) {
				if ( $event instanceof \WP_Post ) {
					$ids[] = (int) $event->ID;
				}
			}
		}

		return $ids;
	}

	/**
	 * Ticket count for an event id (0 when tickets are absent).
	 *
	 * @param int $event_id Event id.
	 * @return int
	 */
	public function ticket_count( int $event_id ): int {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- TEC core function.
		if ( $event_id <= 0 || ! function_exists( 'tribe_events_has_tickets' ) ) {
			return 0;
		}

		return tribe_events_has_tickets( $event_id ) ? 1 : 0;
	}
}
