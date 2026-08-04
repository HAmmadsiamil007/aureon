<?php
/**
 * Trigger — declarative scroll triggers.
 *
 * Phase 10 (Animation Engine): registers named scroll triggers
 * (`on(element, callback, opts)`) that the JS runtime binds via
 * IntersectionObserver → GSAP ScrollTrigger. The PHP side stores the
 * declarative map; the runtime creates ScrollTrigger instances for elements
 * that exist, guarded by the observer cap (Breaking::OBSERVER_CAP).
 *
 * @package Phantom\Core\Animation\Scroll
 * @since 0.10.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Animation\Scroll;

/**
 * Scroll trigger registry.
 */
class Trigger {

	/**
	 * Registered triggers: selector → options.
	 *
	 * @var array<string, array<string, mixed>>
	 */
	private array $triggers = array();

	/**
	 * Register a scroll trigger.
	 *
	 * @param string               $selector Target element selector.
	 * @param array<string, mixed> $options  ScrollTrigger options.
	 * @return void
	 */
	public function on( string $selector, array $options = array() ): void {
		$this->triggers[ $selector ] = $options;
	}

	/**
	 * All registered triggers.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public function all(): array {
		return $this->triggers;
	}

	/**
	 * Whether any trigger is registered.
	 *
	 * @return bool
	 */
	public function is_empty(): bool {
		return array() === $this->triggers;
	}

	/**
	 * Serialized triggers for the JS runtime.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public function to_array(): array {
		return $this->triggers;
	}
}
