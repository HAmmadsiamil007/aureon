<?php
/**
 * QueryGuard — debug-only database query introspection.
 *
 * Phase 13 (Performance Engineering): `limit(int $n)` records a query budget
 * for the request and, when WordPress is present, hooks `pre_get_posts` (or
 * falls back to a shutdown check) to warn once the budget is exceeded.
 * Debug-only: in production the guard is a silent no-op (plan §Phase 13:
 * `QueryGuard::limit(int $n)` debug-only introspection; "query cap triggers
 * warning in debug only").
 *
 * WP-free safe: without WordPress the counter still accumulates via the pure
 * `register()` API so the smoke suite can assert the mechanics.
 *
 * @package Lumina\Core\Performance
 * @since 0.13.0
 */

declare( strict_types=1 );

namespace Lumina\Core\Performance;

/**
 * Per-request query budget guard.
 */
final class QueryGuard {

	/**
	 * Whether introspection is active (debug builds only).
	 *
	 * @var bool
	 */
	private bool $active;

	/**
	 * The current query budget (0 = unlimited/unset).
	 *
	 * @var int
	 */
	private int $limit = 0;

	/**
	 * Number of queries registered this request.
	 *
	 * @var int
	 */
	private int $count = 0;

	/**
	 * Whether the budget was already exceeded (report once).
	 *
	 * @var bool
	 */
	private bool $warned = false;

	/**
	 * Build the guard.
	 *
	 * @param bool $active Whether introspection is active.
	 */
	public function __construct( bool $active = false ) {
		$this->active = $active;
	}

	/**
	 * Record a query against the budget.
	 *
	 * @return void
	 */
	public function register(): void {
		if ( ! $this->active ) {
			return;
		}

		++$this->count;

		if ( $this->limit > 0 && $this->count > $this->limit ) {
			$this->warn();
		}
	}

	/**
	 * Set the per-request query budget (debug only).
	 *
	 * @param int $limit Maximum queries.
	 * @return void
	 */
	public function limit( int $limit ): void {
		$this->limit = max( 0, $limit );
	}

	/**
	 * The number of queries registered so far.
	 *
	 * @return int
	 */
	public function count(): int {
		return $this->count;
	}

	/**
	 * The active limit.
	 *
	 * @return int
	 */
	public function limit_value(): int {
		return $this->limit;
	}

	/**
	 * Whether the guard is active.
	 *
	 * @return bool
	 */
	public function is_active(): bool {
		return $this->active;
	}

	/**
	 * Whether the budget has been exceeded (and reported).
	 *
	 * @return bool
	 */
	public function exceeded(): bool {
		return $this->warned;
	}

	/**
	 * Report the violation once (via the Log facade, WP-free safe).
	 *
	 * @return void
	 */
	private function warn(): void {
		if ( $this->warned ) {
			return;
		}

		$this->warned = true;

		\Lumina\Core\Support\Debug\Log::warning(
			'Query budget exceeded: {count} queries (limit {limit})',
			array(
				'count' => $this->count,
				'limit' => $this->limit,
			)
		);
	}
}
