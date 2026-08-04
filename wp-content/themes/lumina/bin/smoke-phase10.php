<?php
/**
 * Phase 10 smoke suite — Animation Engine.
 *
 * WP-free. Boots the container, resolves the animation engine, and asserts
 * the canonical surface: PSR-4; container wiring; preset registry
 * (register/get/has/all/render_attribute allowlist); canonical reveal
 * preset; reduced-motion gate + CSS guard; performance budgets (JS budget,
 * observer cap); Lenis intent; Three mounts; scroll triggers; boot config
 * serialization; Phases 1–9 regression.
 *
 * Usage: php bin/smoke-phase10.php
 *
 * @package Lumina\Core\Smoke
 * @since 0.10.0
 */

declare( strict_types=1 );

use Lumina\Core\Animation\AnimationRegistry;
use Lumina\Core\Animation\Breaking;
use Lumina\Core\Animation\Engine;
use Lumina\Core\Animation\Lenis;
use Lumina\Core\Animation\Preset;
use Lumina\Core\Animation\ReducedMotion;
use Lumina\Core\Animation\Scroll\Trigger;
use Lumina\Core\Animation\Three;
use Lumina\Core\Boot\Kernel;
use Lumina\Core\Core\App;

require __DIR__ . '/../vendor/autoload.php';

$failures = 0;
$total    = 0;

/**
 * Record a check result.
 *
 * @param string $name    Check name.
 * @param bool   $ok      Whether the check passed.
 * @param string $details Optional details on failure.
 */
function check( string $name, bool $ok, string $details = '' ): void {
	global $failures, $total;

	++$total;

	if ( ! $ok ) {
		++$failures;
		echo 'FAIL  ' . $name . ( '' !== $details ? ' — ' . $details : '' ) . PHP_EOL;
	}
}

Kernel::launch();
$app = App::instance();

// 1. Feature flag present and enabled.
$features = $app->make( 'config' )->get( 'features', array() );
check( 'animation feature is enabled', true === ( $features['animation'] ?? false ) );

// 2. PSR-4 + container wiring.
check( 'PSR-4 resolves Preset', class_exists( Preset::class ) );
check( 'PSR-4 resolves AnimationRegistry', class_exists( AnimationRegistry::class ) );
check( 'PSR-4 resolves ReducedMotion', class_exists( ReducedMotion::class ) );
check( 'PSR-4 resolves Breaking', class_exists( Breaking::class ) );
check( 'PSR-4 resolves Lenis', class_exists( Lenis::class ) );
check( 'PSR-4 resolves Three', class_exists( Three::class ) );
check( 'PSR-4 resolves Trigger', class_exists( Trigger::class ) );
check( 'PSR-4 resolves Engine', class_exists( Engine::class ) );
check( 'animation.engine resolves', $app->make( 'animation.engine' ) instanceof Engine );
check( 'animation.registry resolves', $app->make( 'animation.registry' ) instanceof AnimationRegistry );
check( 'animation.reduced_motion resolves', $app->make( 'animation.reduced_motion' ) instanceof ReducedMotion );
check( 'animation.breaking resolves', $app->make( 'animation.breaking' ) instanceof Breaking );
check( 'animation.lenis resolves', $app->make( 'animation.lenis' ) instanceof Lenis );
check( 'animation.three resolves', $app->make( 'animation.three' ) instanceof Three );
check( 'animation.trigger resolves', $app->make( 'animation.trigger' ) instanceof Trigger );

// 3. Canonical reveal preset registered by the provider.
$engine = $app->make( 'animation.engine' );
check( 'engine active with reveal preset', true === $engine->is_active() );
check( 'reveal preset registered', $engine->registry()->has( 'reveal' ) );

$reveal = $engine->registry()->get( 'reveal' );
check( 'reveal is a Preset', $reveal instanceof Preset );
check( 'reveal name', 'reveal' === $reveal->name() );
check( 'reveal type', 'reveal' === $reveal->type() );
check( 'reveal targets data-lumina-anim=reveal', '[data-lumina-anim="reveal"]' === $reveal->target() );
check( 'reveal has tween options', array() !== $reveal->options() );
check( 'reveal has scroll options', array() !== $reveal->scroll() );
check( 'reveal is decorative (reduced-motion safe)', true === $reveal->decorative() );

// 4. Registry behaviors.
$registry = $app->make( 'animation.registry' );
check( 'registry non-empty', false === $registry->is_empty() );
check( 'render_attribute allowlists known name', 'reveal' === $registry->render_attribute( 'reveal' ) );
check( 'render_attribute rejects unknown name', '' === $registry->render_attribute( 'nope' ) );

$custom = new Preset( 'counter', 'counter', '.stat', array( 'duration' => 1.2 ), array(), true );
$registry->register( $custom );
check( 'register adds preset', $registry->has( 'counter' ) );
check( 'get returns registered preset', $custom === $registry->get( 'counter' ) );
check( 'all returns 2 presets', 2 === count( $registry->all() ) );

// 5. Preset immutability + serialization.
$serialized = $custom->to_array();
check( 'preset to_array has name', 'counter' === $serialized['name'] );
check( 'preset to_array has type', 'counter' === $serialized['type'] );
check( 'preset to_array has options', is_array( $serialized['options'] ) );

// 6. ReducedMotion.
$reduced = $app->make( 'animation.reduced_motion' );
check( 'reduced motion enforced by default (motion.reduced token)', true === $reduced->enforced() );
check( 'css_guard is non-empty', '' !== $reduced->css_guard() );
check( 'css_guard targets prefers-reduced-motion', str_contains( $reduced->css_guard(), 'prefers-reduced-motion' ) );
check( 'css_guard zeroes animations', str_contains( $reduced->css_guard(), 'animation:none' ) );
check( 'reduced to_array has enforced flag', true === $reduced->to_array()['enforced'] );

// 7. Breaking budgets.
$breaking = $app->make( 'animation.breaking' );
check( 'JS budget is 120KB', 120 * 1024 === Breaking::JS_BUDGET );
check( 'within_budget accepts small payload', true === $breaking->within_budget( 4096 ) );
check( 'within_budget rejects over budget', false === $breaking->within_budget( 121 * 1024 ) );
check( 'observer cap is 40', 40 === Breaking::OBSERVER_CAP );
check( 'within_observer_cap accepts 40', true === $breaking->within_observer_cap( 40 ) );
check( 'within_observer_cap rejects 41', false === $breaking->within_observer_cap( 41 ) );
check( 'budgets serialized', is_array( $breaking->to_array() ) && isset( $breaking->to_array()['observer_cap'] ) );

// 8. Lenis intent.
$lenis = $app->make( 'animation.lenis' );
check( 'lenis disabled by default', false === $lenis->is_enabled() );
$lenis->enable();
check( 'lenis enable() sets intent', true === $lenis->is_enabled() );
$lenis->disable();
check( 'lenis disable() clears intent', false === $lenis->is_enabled() );
check( 'lenis serialized', false === $lenis->to_array()['enabled'] );

// 9. Three mounts.
$three = $app->make( 'animation.three' );
check( 'three empty by default', true === $three->is_empty() );
$three->with_canvas( '.lumina-hero-canvas', array( 'clear_color' => '#000000' ) );
check( 'three with_canvas registers mount', array() !== $three->mounts() );
check( 'three mount selector recorded', isset( $three->mounts()['.lumina-hero-canvas'] ) );
check( 'three serialized mounts', is_array( $three->to_array() ) );

// 10. Scroll triggers.
$trigger = $app->make( 'animation.trigger' );
check( 'trigger empty by default', true === $trigger->is_empty() );
$trigger->on( '[data-lumina-trigger]', array( 'start' => 'top 80%' ) );
check( 'trigger on() registers', array() !== $trigger->all() );
check( 'trigger serialized', isset( $trigger->to_array()['[data-lumina-trigger]'] ) );

// 11. Engine boot config serialization (JSON-safe).
$config = $engine->boot_config();
check( 'boot_config has presets', isset( $config['presets'] ) && is_array( $config['presets'] ) );
check( 'boot_config has reveal preset', isset( $config['presets']['reveal'] ) );
check( 'boot_config has reduced_motion', isset( $config['reduced_motion'] ) );
check( 'boot_config has budgets', isset( $config['budgets'] ) );
check( 'boot_config has lenis', isset( $config['lenis'] ) );
check( 'boot_config has three', isset( $config['three'] ) );
check( 'boot_config has triggers', isset( $config['triggers'] ) );

$json = json_encode( $config );
check( 'boot_config is JSON-encodable', false !== $json && is_string( $json ) );

// 12. Engine is_active reflects registered work.
check( 'engine active after additions', true === $engine->is_active() );

echo 'Results: ' . ( $total - $failures ) . '/' . $total . ' checks passed.' . PHP_EOL;

if ( 0 !== $failures ) {
	echo 'PHASE 10 SMOKE: ' . $failures . ' FAILURE(S).' . PHP_EOL;
	exit( 1 );
}

echo 'PHASE 10 SMOKE: PASS' . PHP_EOL;
