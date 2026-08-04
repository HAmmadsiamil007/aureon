<?php
/**
 * ReducedMotion — prefers-reduced-motion gating.
 *
 * Phase 10 (Animation Engine): server-side decision helpers for the
 * `motion.reduced` design token (Phase 3) and the CSS guard emitted alongside
 * the animation entry. The user's actual media query is resolved in JS; this
 * class provides the CSS guard markup and the runtime flag so the JS module
 * can take the zero-cost path (no listeners) when reduced motion is active.
 *
 * @package Phantom\Core\Animation
 * @since 0.10.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Animation;

/**
 * Reduced-motion gating helpers.
 */
class ReducedMotion {

	/**
	 * Whether reduced motion is enforced by configuration (token), not by the
	 * user's media query. When true, the JS runtime disables animation even
	 * before consulting matchMedia.
	 *
	 * @var bool
	 */
	private bool $enforced;

	/**
	 * Build the helper.
	 *
	 * @param bool $enforced Config-enforced reduced motion (motion.reduced).
	 */
	public function __construct( bool $enforced = true ) {
		$this->enforced = $enforced;
	}

	/**
	 * Whether reduced motion is enforced by configuration.
	 *
	 * @return bool
	 */
	public function enforced(): bool {
		return $this->enforced;
	}

	/**
	 * Inline CSS guard zeroing animation durations under reduced motion.
	 *
	 * Uses the design-token duration (0) and disables transforms/transitions
	 * so reduced-motion users see static content (plan §Phase 10 acceptance).
	 *
	 * @return string
	 */
	public function css_guard(): string {
		return '@media (prefers-reduced-motion: reduce){'
			. '*,[data-phantom-anim]{animation:none!important;transition:none!important;'
			. 'scroll-behavior:auto!important}}';
	}

	/**
	 * Runtime flag serialized for the JS module.
	 *
	 * @return array<string, bool>
	 */
	public function to_array(): array {
		return array(
			'enforced' => $this->enforced,
		);
	}
}
