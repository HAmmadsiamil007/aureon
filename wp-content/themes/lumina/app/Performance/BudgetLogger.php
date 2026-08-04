<?php
/**
 * BudgetLogger — evaluates measured metrics against the Budget and reports.
 *
 * Phase 13 (Performance Engineering): `check()` accepts a metrics map
 * (LCP/CLS/INP/JS KB/CSS KB/server ms/queries) and returns the list of
 * violations against the budget. When WordPress logging is available it also
 * dispatches a warning-level log per violation; failures are never thrown —
 * the gate is observability-first (plan §Phase 13: `BudgetLogger::check()`).
 *
 * WP-free safe: logging falls back to a no-op when no logger is present.
 *
 * @package Lumina\Core\Performance
 * @since 0.13.0
 */

declare( strict_types=1 );

namespace Lumina\Core\Performance;

use Lumina\Core\Support\Debug\Log;

/**
 * Budget evaluation + violation reporting.
 */
final class BudgetLogger {

	/**
	 * The budget to evaluate against.
	 *
	 * @var Budget
	 */
	private Budget $budget;

	/**
	 * Build the logger.
	 *
	 * @param Budget $budget Budget definition.
	 */
	public function __construct( Budget $budget ) {
		$this->budget = $budget;
	}

	/**
	 * Evaluate a metrics map against the budget.
	 *
	 * @param array<string, mixed> $metrics Measured values.
	 * @return list<string> Violation messages (empty = within budget).
	 */
	public function check( array $metrics ): array {
		$violations = array();

		if ( isset( $metrics['lcp'] ) && (float) $metrics['lcp'] > $this->budget->lcp() ) {
			$violations[] = sprintf(
				'LCP %.2fs exceeds budget %.2fs',
				(float) $metrics['lcp'],
				$this->budget->lcp()
			);
		}

		if ( isset( $metrics['cls'] ) && (float) $metrics['cls'] > $this->budget->cls() ) {
			$violations[] = sprintf(
				'CLS %.3f exceeds budget %.3f',
				(float) $metrics['cls'],
				$this->budget->cls()
			);
		}

		if ( isset( $metrics['inp'] ) && (float) $metrics['inp'] > $this->budget->inp() ) {
			$violations[] = sprintf(
				'INP %.0fms exceeds budget %.0fms',
				(float) $metrics['inp'],
				$this->budget->inp()
			);
		}

		if ( isset( $metrics['js_kb'] ) && (int) $metrics['js_kb'] > $this->budget->js_kb() ) {
			$violations[] = sprintf(
				'JS %dKB exceeds budget %dKB',
				(int) $metrics['js_kb'],
				$this->budget->js_kb()
			);
		}

		if ( isset( $metrics['css_kb'] ) && (int) $metrics['css_kb'] > $this->budget->css_kb() ) {
			$violations[] = sprintf(
				'CSS %dKB exceeds budget %dKB',
				(int) $metrics['css_kb'],
				$this->budget->css_kb()
			);
		}

		if ( isset( $metrics['server_ms'] ) && (int) $metrics['server_ms'] > $this->budget->server_ms() ) {
			$violations[] = sprintf(
				'server time %dms exceeds budget %dms',
				(int) $metrics['server_ms'],
				$this->budget->server_ms()
			);
		}

		if ( isset( $metrics['queries'] ) && (int) $metrics['queries'] > $this->budget->queries() ) {
			$violations[] = sprintf(
				'%d queries exceed budget %d',
				(int) $metrics['queries'],
				$this->budget->queries()
			);
		}

		foreach ( $violations as $violation ) {
			Log::warning( 'Performance budget violation: {violation}', array( 'violation' => $violation ) );
		}

		return $violations;
	}

	/**
	 * The active budget.
	 *
	 * @return Budget
	 */
	public function budget(): Budget {
		return $this->budget;
	}
}
