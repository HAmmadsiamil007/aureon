<?php
/**
 * Lenis — smooth-scroll facade.
 *
 * Phase 10 (Animation Engine): declarative Lenis smooth-scroll control
 * (opt-in, feature-flagged). The PHP side records enable/disable intent and
 * serializes config for the JS runtime; the runtime constructs the Lenis
 * instance (dynamic import) only when enabled and reduced motion is off.
 * Disabled under prefers-reduced-motion (plan §Phase 10).
 *
 * @package Phantom\Core\Animation
 * @since 0.10.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Animation;

/**
 * Smooth-scroll intent holder.
 */
class Lenis {

	/**
	 * Whether smooth scroll is enabled.
	 *
	 * @var bool
	 */
	private bool $enabled = false;

	/**
	 * Runtime options (duration, easing, wheel multiplier, etc.).
	 *
	 * @var array<string, mixed>
	 */
	private array $options;

	/**
	 * Build the facade.
	 *
	 * @param array<string, mixed> $options Lenis runtime options.
	 */
	public function __construct( array $options = array() ) {
		$this->options = $options;
	}

	/**
	 * Enable smooth scroll.
	 *
	 * @return void
	 */
	public function enable(): void {
		$this->enabled = true;
	}

	/**
	 * Disable smooth scroll.
	 *
	 * @return void
	 */
	public function disable(): void {
		$this->enabled = false;
	}

	/**
	 * Whether smooth scroll is enabled.
	 *
	 * @return bool
	 */
	public function is_enabled(): bool {
		return $this->enabled;
	}

	/**
	 * Runtime options.
	 *
	 * @return array<string, mixed>
	 */
	public function options(): array {
		return $this->options;
	}

	/**
	 * Serialized runtime config.
	 *
	 * @return array<string, mixed>
	 */
	public function to_array(): array {
		return array(
			'enabled' => $this->enabled,
			'options' => $this->options,
		);
	}
}
