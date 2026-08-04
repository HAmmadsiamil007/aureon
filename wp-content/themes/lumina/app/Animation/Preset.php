<?php
/**
 * Preset — immutable animation preset definition.
 *
 * Phase 10 (Animation Engine): a named, declarative animation behavior. A
 * preset carries the target selector, the behavior name (reveal/counter/
 * stagger/timeline), GSAP tween options (from/to, duration, ease, stagger),
 * optional scroll-trigger options, and whether it is decorative-only (so it
 * can be safely disabled under prefers-reduced-motion). Presets are
 * allowlisted by name — no user string is ever turned into a function
 * (ADR-020 security: no eval).
 *
 * @package Lumina\Core\Animation
 * @since 0.10.0
 */

declare( strict_types=1 );

namespace Lumina\Core\Animation;

/**
 * Immutable animation preset.
 */
final class Preset {

	/**
	 * Preset name (allowlisted identity).
	 *
	 * @var string
	 */
	private string $name;

	/**
	 * Behavior type: 'reveal' | 'counter' | 'stagger' | 'timeline'.
	 *
	 * @var string
	 */
	private string $type;

	/**
	 * Target selector(s).
	 *
	 * @var string
	 */
	private string $target;

	/**
	 * GSAP tween options.
	 *
	 * @var array<string, mixed>
	 */
	private array $options;

	/**
	 * Scroll-trigger options ('' = scroll-driven, 'once' = play once).
	 *
	 * @var array<string, mixed>
	 */
	private array $scroll;

	/**
	 * Whether the animation is decorative (safe to disable for reduced motion).
	 *
	 * @var bool
	 */
	private bool $decorative;

	/**
	 * Build a preset.
	 *
	 * @param string               $name       Preset name.
	 * @param string               $type       Behavior type.
	 * @param string               $target     Target selector.
	 * @param array<string, mixed> $options    GSAP tween options.
	 * @param array<string, mixed> $scroll     Scroll-trigger options.
	 * @param bool                 $decorative Decorative-only (reduced-motion safe).
	 */
	public function __construct(
		string $name,
		string $type,
		string $target,
		array $options = array(),
		array $scroll = array(),
		bool $decorative = true
	) {
		$this->name       = $name;
		$this->type       = $type;
		$this->target     = $target;
		$this->options    = $options;
		$this->scroll     = $scroll;
		$this->decorative = $decorative;
	}

	/**
	 * Preset name.
	 *
	 * @return string
	 */
	public function name(): string {
		return $this->name;
	}

	/**
	 * Behavior type.
	 *
	 * @return string
	 */
	public function type(): string {
		return $this->type;
	}

	/**
	 * Target selector.
	 *
	 * @return string
	 */
	public function target(): string {
		return $this->target;
	}

	/**
	 * GSAP tween options.
	 *
	 * @return array<string, mixed>
	 */
	public function options(): array {
		return $this->options;
	}

	/**
	 * Scroll-trigger options.
	 *
	 * @return array<string, mixed>
	 */
	public function scroll(): array {
		return $this->scroll;
	}

	/**
	 * Whether the preset is decorative-only.
	 *
	 * @return bool
	 */
	public function decorative(): bool {
		return $this->decorative;
	}

	/**
	 * Serialize for the JS runtime (JSON-safe).
	 *
	 * @return array<string, mixed>
	 */
	public function to_array(): array {
		return array(
			'name'       => $this->name,
			'type'       => $this->type,
			'target'     => $this->target,
			'options'    => $this->options,
			'scroll'     => $this->scroll,
			'decorative' => $this->decorative,
		);
	}
}
