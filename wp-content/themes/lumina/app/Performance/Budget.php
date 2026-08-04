<?php
/**
 * Budget — immutable Core Web Vitals + asset budget definition.
 *
 * Phase 13 (Performance Engineering): a single value object holding the
 * enforceable budgets (plan §Phase 13, defaults overridable via
 * `performance.budgets` config). Every limit is exposed with a stable key so
 * BudgetLogger and the CI gate evaluate the same numbers.
 *
 * @package Lumina\Core\Performance
 * @since 0.13.0
 */

declare( strict_types=1 );

namespace Lumina\Core\Performance;

/**
 * Immutable performance budget values.
 */
final class Budget {

	/**
	 * Largest Contentful Paint budget in seconds.
	 *
	 * @var float
	 */
	private float $lcp;

	/**
	 * Cumulative Layout Shift budget (unitless).
	 *
	 * @var float
	 */
	private float $cls;

	/**
	 * Interaction to Next Paint budget in milliseconds.
	 *
	 * @var float
	 */
	private float $inp;

	/**
	 * Core JS payload budget in kilobytes.
	 *
	 * @var int
	 */
	private int $js_kb;

	/**
	 * Core CSS payload budget in kilobytes.
	 *
	 * @var int
	 */
	private int $css_kb;

	/**
	 * Uncached full-page server time budget in milliseconds.
	 *
	 * @var int
	 */
	private int $server_ms;

	/**
	 * Database query budget per request.
	 *
	 * @var int
	 */
	private int $queries;

	/**
	 * Build a budget from raw values (config or defaults).
	 *
	 * @param array<string, mixed> $values Budget values.
	 */
	public function __construct( array $values = array() ) {
		$this->lcp       = (float) ( $values['lcp'] ?? 2.0 );
		$this->cls       = (float) ( $values['cls'] ?? 0.05 );
		$this->inp       = (float) ( $values['inp'] ?? 150.0 );
		$this->js_kb     = (int) ( $values['js_kb'] ?? 120 );
		$this->css_kb    = (int) ( $values['css_kb'] ?? 50 );
		$this->server_ms = (int) ( $values['server_ms'] ?? 300 );
		$this->queries   = (int) ( $values['queries'] ?? 8 );
	}

	/**
	 * Largest Contentful Paint budget (seconds).
	 *
	 * @return float
	 */
	public function lcp(): float {
		return $this->lcp;
	}

	/**
	 * Cumulative Layout Shift budget.
	 *
	 * @return float
	 */
	public function cls(): float {
		return $this->cls;
	}

	/**
	 * Interaction to Next Paint budget (milliseconds).
	 *
	 * @return float
	 */
	public function inp(): float {
		return $this->inp;
	}

	/**
	 * Core JS budget (kilobytes).
	 *
	 * @return int
	 */
	public function js_kb(): int {
		return $this->js_kb;
	}

	/**
	 * Core CSS budget (kilobytes).
	 *
	 * @return int
	 */
	public function css_kb(): int {
		return $this->css_kb;
	}

	/**
	 * Uncached server-time budget (milliseconds).
	 *
	 * @return int
	 */
	public function server_ms(): int {
		return $this->server_ms;
	}

	/**
	 * Query budget per request.
	 *
	 * @return int
	 */
	public function queries(): int {
		return $this->queries;
	}

	/**
	 * Full budget map (stable keys for reporting).
	 *
	 * @return array<string, float|int>
	 */
	public function to_array(): array {
		return array(
			'lcp'       => $this->lcp,
			'cls'       => $this->cls,
			'inp'       => $this->inp,
			'js_kb'     => $this->js_kb,
			'css_kb'    => $this->css_kb,
			'server_ms' => $this->server_ms,
			'queries'   => $this->queries,
		);
	}
}
