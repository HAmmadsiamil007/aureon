<?php
/**
 * Sequencer — ordered, filterable boot steps.
 *
 * Phase 1 (Bootstrap): runs an ordered list of callbacks, threading a shared
 * context array. Any step may append results to the context; failures are
 * logged and raise `phantom_core:boot_error` (once), then the sequence stops —
 * the WP request surface is never crashed by a boot failure (ADR-013).
 *
 * The step list is filterable via `phantom_core:boot_steps` so other
 * plugins/extensions can add or reorder steps.
 *
 * @package Phantom\Core\Boot
 * @since 0.1.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Boot;

use Phantom\Core\Support\Debug\Log;

/**
 * Ordered boot step runner.
 */
final class Sequencer {

	/**
	 * Registered steps: id → [priority, callback].
	 *
	 * @var array<string, array{priority:int, callback:callable}>
	 */
	private array $steps = array();

	/**
	 * Whether the most recent run had a failing step.
	 *
	 * @var bool
	 */
	private bool $failed = false;

	/**
	 * Register a boot step.
	 *
	 * @param string   $id       Unique step id (used in events and filters).
	 * @param callable $callback fn(array $context): array — returns updated context.
	 * @param int      $priority Execution order (lower runs first).
	 * @return $this
	 */
	public function add( string $id, callable $callback, int $priority = 10 ): self {
		$this->steps[ $id ] = array(
			'priority' => $priority,
			'callback' => $callback,
		);

		return $this;
	}

	/**
	 * Whether a step is registered.
	 *
	 * @param string $id Step id.
	 * @return bool
	 */
	public function has( string $id ): bool {
		return isset( $this->steps[ $id ] );
	}

	/**
	 * Whether the last run had a failing step.
	 *
	 * Lets the Kernel gate the `phantom_core:ready` event on full success.
	 *
	 * @return bool
	 */
	public function has_failed(): bool {
		return $this->failed;
	}

	/**
	 * Run all steps in priority order, threading the shared context.
	 *
	 * @param array<string, mixed> $context Initial context.
	 * @return array<string, mixed> Final context after all (or remaining) steps.
	 */
	public function run( array $context = array() ): array {
		$steps = $this->steps;

		// Allow extensions to add/reorder steps (guarded for WP-free CLI runs).
		if ( function_exists( 'apply_filters' ) ) {
			$filtered = apply_filters( 'phantom_core:boot_steps', $steps );

			if ( is_array( $filtered ) ) {
				$steps = $filtered;
			}
		}

		uasort(
			$steps,
			static function ( array $a, array $b ): int {
				return $a['priority'] <=> $b['priority'];
			}
		);

		foreach ( $steps as $id => $step ) {
			try {
				$result = call_user_func( $step['callback'], $context );

				if ( is_array( $result ) ) {
					$context = array_merge( $context, $result );
				}
			} catch ( \Throwable $e ) {
				$this->failed = true;

				Log::error(
					'Boot step "{step}" failed: {message}',
					array(
						'step'    => $id,
						'message' => $e->getMessage(),
					)
				);

				if ( function_exists( 'do_action' ) ) {
					do_action( 'phantom_core:boot_error', $e, $id );
				}

				break;
			}
		}

		return $context;
	}
}
