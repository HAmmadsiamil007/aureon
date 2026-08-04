<?php
/**
 * Breaking — animation performance gates.
 *
 * Phase 10 (Animation Engine): declarative budgets the animation runtime
 * honours (plan §Phase 10):
 *   - JS payload budget (uncompressed 3p libs are lazy-loaded),
 *   - IntersectionObserver count cap (e.g. 40 observers),
 *   - will-change policy (only while animating — no layout thrash).
 * The PHP side exposes the constants for docs/tests; the TS module enforces
 * them at runtime.
 *
 * @package Lumina\Core\Animation
 * @since 0.10.0
 */

declare( strict_types=1 );

namespace Lumina\Core\Animation;

/**
 * Animation performance budgets.
 */
class Breaking {

	/**
	 * Max JS payload for animation runtime (bytes, uncompressed).
	 *
	 * @var int
	 */
	public const JS_BUDGET = 120 * 1024;

	/**
	 * Max concurrently-observed elements via IntersectionObserver.
	 *
	 * @var int
	 */
	public const OBSERVER_CAP = 40;

	/**
	 * Whether a payload size is within budget.
	 *
	 * @param int $bytes Uncompressed bytes.
	 * @return bool
	 */
	public function within_budget( int $bytes ): bool {
		return $bytes <= self::JS_BUDGET;
	}

	/**
	 * Whether an observer count is within the cap.
	 *
	 * @param int $count Current observer count.
	 * @return bool
	 */
	public function within_observer_cap( int $count ): bool {
		return $count <= self::OBSERVER_CAP;
	}

	/**
	 * Serialized budgets for the JS runtime.
	 *
	 * @return array<string, int>
	 */
	public function to_array(): array {
		return array(
			'js_budget'    => self::JS_BUDGET,
			'observer_cap' => self::OBSERVER_CAP,
		);
	}
}
