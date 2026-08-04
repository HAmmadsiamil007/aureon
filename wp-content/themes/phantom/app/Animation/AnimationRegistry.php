<?php
/**
 * AnimationRegistry — named animation behavior registry.
 *
 * Phase 10 (Animation Engine): register/render named animation presets.
 * Presets are allowlisted by name (ADR-020: no user string → function); the
 * registry only ever emits data attributes and a serialized preset map for
 * the JS runtime. Empty registry → the animation entry is never enqueued
 * (performance: zero cost when unused).
 *
 * @package Phantom\Core\Animation
 * @since 0.10.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Animation;

/**
 * Named animation preset registry.
 */
class AnimationRegistry {

	/**
	 * Registered presets by name.
	 *
	 * @var array<string, Preset>
	 */
	private array $presets = array();

	/**
	 * Register a preset (replaces any same-named preset).
	 *
	 * @param Preset $preset Preset to register.
	 * @return void
	 */
	public function register( Preset $preset ): void {
		$this->presets[ $preset->name() ] = $preset;
	}

	/**
	 * Resolve a preset by name.
	 *
	 * @param string $name Preset name.
	 * @return Preset|null
	 */
	public function get( string $name ): ?Preset {
		return $this->presets[ $name ] ?? null;
	}

	/**
	 * Whether a preset is registered.
	 *
	 * @param string $name Preset name.
	 * @return bool
	 */
	public function has( string $name ): bool {
		return isset( $this->presets[ $name ] );
	}

	/**
	 * All registered presets.
	 *
	 * @return list<Preset>
	 */
	public function all(): array {
		return array_values( $this->presets );
	}

	/**
	 * Whether the registry is non-empty (drives enqueue decisions).
	 *
	 * @return bool
	 */
	public function is_empty(): bool {
		return array() === $this->presets;
	}

	/**
	 * Serialized preset map for the JS runtime.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public function to_array(): array {
		$map = array();

		foreach ( $this->presets as $name => $preset ) {
			$map[ $name ] = $preset->to_array();
		}

		return $map;
	}

	/**
	 * Render the `data-phantom-anim` attribute value for a preset name.
	 *
	 * Returns '' when the name is not registered (allowlist guard).
	 *
	 * @param string $name Preset name.
	 * @return string
	 */
	public function render_attribute( string $name ): string {
		return $this->has( $name ) ? $name : '';
	}
}
