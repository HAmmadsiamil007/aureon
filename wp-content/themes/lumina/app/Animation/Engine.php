<?php
/**
 * Engine — animation runtime controller.
 *
 * Phase 10 (Animation Engine): aggregates the registry, reduced-motion gate,
 * performance budgets, Lenis intent, Three mounts and scroll triggers into a
 * single serialized boot payload for the JS runtime. Emits
 * `lumina_core:animation:ready` (domain event, ADR-006) when configured and
 * WordPress events are available; otherwise stays passive (WP-free CLI).
 *
 * @package Lumina\Core\Animation
 * @since 0.10.0
 */

declare( strict_types=1 );

namespace Lumina\Core\Animation;

use Lumina\Core\Animation\Scroll\Trigger;

/**
 * Animation runtime controller.
 */
class Engine {

	/**
	 * Animation preset registry.
	 *
	 * @var AnimationRegistry
	 */
	private AnimationRegistry $registry;

	/**
	 * Reduced-motion gate.
	 *
	 * @var ReducedMotion
	 */
	private ReducedMotion $reduced_motion;

	/**
	 * Performance gates.
	 *
	 * @var Breaking
	 */
	private Breaking $breaking;

	/**
	 * Smooth-scroll intent.
	 *
	 * @var Lenis
	 */
	private Lenis $lenis;

	/**
	 * Three.js scene declarations.
	 *
	 * @var Three
	 */
	private Three $three;

	/**
	 * Scroll triggers.
	 *
	 * @var Trigger
	 */
	private Trigger $trigger;

	/**
	 * Build the engine.
	 *
	 * @param AnimationRegistry $registry      Preset registry.
	 * @param ReducedMotion     $reduced_motion Reduced-motion gate.
	 * @param Breaking          $breaking      Performance gates.
	 * @param Lenis             $lenis         Smooth-scroll intent.
	 * @param Three             $three         Three.js declarations.
	 * @param Trigger           $trigger       Scroll triggers.
	 */
	public function __construct(
		AnimationRegistry $registry,
		ReducedMotion $reduced_motion,
		Breaking $breaking,
		Lenis $lenis,
		Three $three,
		Trigger $trigger
	) {
		$this->registry       = $registry;
		$this->reduced_motion = $reduced_motion;
		$this->breaking       = $breaking;
		$this->lenis          = $lenis;
		$this->three          = $three;
		$this->trigger        = $trigger;
	}

	/**
	 * The preset registry.
	 *
	 * @return AnimationRegistry
	 */
	public function registry(): AnimationRegistry {
		return $this->registry;
	}

	/**
	 * Whether any animation work is configured (drives enqueue decision).
	 *
	 * @return bool
	 */
	public function is_active(): bool {
		return ! $this->registry->is_empty()
			|| $this->lenis->is_enabled()
			|| ! $this->three->is_empty()
			|| ! $this->trigger->is_empty();
	}

	/**
	 * The serialized boot payload for the JS runtime.
	 *
	 * @return array<string, mixed>
	 */
	public function boot_config(): array {
		return array(
			'presets'        => $this->registry->to_array(),
			'reduced_motion' => $this->reduced_motion->to_array(),
			'budgets'        => $this->breaking->to_array(),
			'lenis'          => $this->lenis->to_array(),
			'three'          => $this->three->to_array(),
			'triggers'       => $this->trigger->to_array(),
		);
	}

	/**
	 * The inline reduced-motion CSS guard (zero-JS fallback).
	 *
	 * @return string
	 */
	public function reduced_motion_guard(): string {
		return $this->reduced_motion->css_guard();
	}

	/**
	 * Announce readiness through the domain-event bus when available.
	 *
	 * @return void
	 */
	public function ready(): void {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		if ( ! function_exists( 'do_action' ) || ! $this->is_active() ) {
			return;
		}

		do_action( 'lumina_core:animation:ready' );
	}
}
