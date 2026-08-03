<?php
/**
 * Phase 3 — Design Token Engine smoke suite (WP-free CLI).
 *
 * Drives the real boot entry (app/load.php → Kernel::launch()) WITHOUT a live
 * WordPress install and asserts the Phase 3 acceptance criteria:
 *
 *   1. tokens('color') returns a validated subset (default layer)
 *   2. TokenRepository::token('color.accent') returns a hex value
 *   3. CssRenderer emits a valid :root block (default preset)
 *   4. A [data-phantom-theme="dark"] block is emitted with an alternate palette
 *   5. Preset switch flips the :root-level semantic tokens
 *   6. space.4 === '0.25rem' (canonical spacing scale)
 *   7. Component tokens resolve through the extends inheritance graph
 *   8. Invariant validation passes (no invalid names, no missing fallbacks)
 *   9. WCAG AA contrast (4.5:1) holds for the default + dark presets
 *  10. Unknown token names throw UnknownToken; resolve() returns null
 *  11. Phases 1 + 2 regression: boot + container + config still resolve
 *
 * Determinism: refuses to run when a developer's own phantom.env.json exists
 * (same contract as smoke-phase1/2.php).
 *
 * Usage: php bin/smoke-phase3.php
 * Exit code 0 = all assertions passed (or skipped); 1 = any failure.
 *
 * @package Phantom
 * @since 0.3.0
 */

declare( strict_types=1 );

// Simulate a WordPress bootstrap boundary so app/load.php's guard passes.
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__ ) . '/' );
}

require dirname( __DIR__ ) . '/app/load.php';

use Phantom\Core\Boot\Kernel;
use Phantom\Core\Container\Container;
use Phantom\Core\Core\App;
use Phantom\Core\Tokens\Invariant;
use Phantom\Core\Tokens\TokenRepository;
use Phantom\Core\Tokens\UnknownToken;

$passes = 0;
$fails  = 0;

/**
 * Record and print an assertion result.
 *
 * @param string $label  Assertion label.
 * @param bool   $ok     Passed?
 * @param string $detail Optional evidence.
 * @return void
 */
function check( string $label, bool $ok, string $detail = '' ): void {
	global $passes, $fails;

	if ( $ok ) {
		++$passes;
		echo "[PASS] {$label}\n";
	} else {
		++$fails;
		echo "[FAIL] {$label}" . ( '' !== $detail ? " — {$detail}" : '' ) . "\n";
	}
}

echo "== Phantom Core Phase 3 smoke suite (Design Token Engine) ==\n\n";

// Same contract as Phases 1–2: default-state assertions require a clean env.
if ( file_exists( dirname( __DIR__ ) . '/phantom.env.json' ) ) {
	echo "[SKIP] phantom.env.json present — default-state assertions are not meaningful.\n";
	echo "       Remove it (or run in a clean checkout) for the full assertion run.\n";
	exit( 0 );
}

// PSR-4 + boot.
check( 'PSR-4 resolves TokenRepository', class_exists( TokenRepository::class ) );
check( 'PSR-4 resolves Invariant', class_exists( Invariant::class ) );

Kernel::launch();
$app = App::instance();

check( 'App::make("tokens.repository") resolves', $app->make( 'tokens.repository' ) instanceof TokenRepository );

$tokens = $app->make( 'tokens.repository' );

// 1. tokens('color') — validated subset.
$color = $tokens->tokens( 'color' );
check( 'tokens("color") returns a map', is_array( $color ) && count( $color ) >= 5, (string) count( $color ) );
check( 'tokens("color") contains color.bg', array_key_exists( 'color.bg', $color ) );
check( 'tokens("color") contains only color.*', array() === array_diff( array_keys( $color ), array_filter( array_keys( $color ), static fn( string $k ): bool => str_starts_with( $k, 'color.' ) ) ) );

// 2. token('color.accent') returns hex.
$accent = $tokens->token( 'color.accent' );
check( 'token("color.accent") returns hex', is_string( $accent ) && 1 === preg_match( '/^#[0-9a-f]{6}$/i', $accent ), (string) $accent );

// 6. space.4 === '0.25rem' (canonical spacing).
check( 'space.4 resolves to 0.25rem', '0.25rem' === $tokens->token( 'space.4' ), (string) $tokens->token( 'space.4' ) );

// 3. CssRenderer emits a valid :root block.
$css = $tokens->css();
check( 'css() emits :root block', str_contains( $css, ':root {' ) );
check( 'css() contains --phantom-color-bg', str_contains( $css, '--phantom-color-bg:' ) );
check( 'css() contains --phantom-space-4', str_contains( $css, '--phantom-space-4: 0.25rem' ) );

// 4. Dark preset block emitted.
check( 'css() emits [data-phantom-theme="dark"]', str_contains( $css, '[data-phantom-theme="dark"] {' ) );

// 5. Preset switch flips the semantic palette.
$dark_block_start = strpos( $css, '[data-phantom-theme="dark"]' );
$dark_block       = false !== $dark_block_start ? substr( $css, $dark_block_start ) : '';
$default_block    = substr( $css, 0, false !== $dark_block_start ? $dark_block_start : strlen( $css ) );
check( 'dark preset flips color.bg', str_contains( $dark_block, '--phantom-color-bg:' ) && ! str_contains( $dark_block, '--phantom-color-bg: #ffffff' ) );
check( 'default :root keeps light bg', str_contains( $default_block, '--phantom-color-bg: #ffffff' ) );

// 7. Component tokens resolve through the inheritance graph.
check( 'component.button.bg resolves to accent hex', str_contains( (string) $tokens->token( 'component.button.bg' ), '#' ) );
check( 'component.card.shadow resolves to a shadow value', str_contains( (string) $tokens->token( 'component.card.shadow' ), 'rgba' ) );

// 8. Invariant validation passes.
check( 'validate() has zero violations', array() === $tokens->validate(), implode( '; ', $tokens->validate() ) );

// 9. WCAG AA contrast for default + dark.
check( 'default preset fg/bg contrast >= 4.5:1', $tokens->contrast_passes() );

// Resolve the dark preset through the real pipeline and assert its fg/bg pair
// also meets WCAG AA (plan testing strategy: "Invariant test ensures dark
// preset AA").
$dark_resolved = ( new \Phantom\Core\Tokens\Resolver() )->resolve_all(
	( new \Phantom\Core\Tokens\Preced() )->collect(
		( new \Phantom\Core\Tokens\TokenSource() )->parse(
			( new \Phantom\Core\Tokens\Loader\DataProvider() )->tokens()
		),
		( new \Phantom\Core\Tokens\TokenSource() )->parse(
			(array) ( ( new \Phantom\Core\Tokens\Loader\DataProvider() )->presets()['dark'] ?? array() )
		)
	)
);
$invariant = new Invariant();
check( 'dark preset fg/bg contrast >= 4.5:1', $invariant->contrast_passes( $dark_resolved ) );
check( 'Invariant contrast() ratio in 1..21', $invariant->contrast( (string) $dark_resolved['color.fg'], (string) $dark_resolved['color.bg'] ) >= 1.0 && $invariant->contrast( (string) $dark_resolved['color.fg'], (string) $dark_resolved['color.bg'] ) <= 21.0 );

// 10. UnknownToken behavior.
$unknown_thrown = false;
try {
	$tokens->token( 'nope.never' );
} catch ( UnknownToken $e ) {
	$unknown_thrown = true;
}
check( 'token("nope.never") throws UnknownToken', $unknown_thrown );
check( 'resolve("nope.never") returns null', null === $tokens->resolve( 'nope.never' ) );

// 11. Phases 1 + 2 regression.
check( 'Phase 1 regression: env resolvable', 'production' === $app->make( 'env' ), (string) $app->make( 'env' ) );
check( 'Phase 2 regression: config repository resolvable', $app->make( 'config' ) instanceof \Phantom\Core\Config\Repository );
check( 'Phase 2 regression: container is Container', $app->make( 'container' ) instanceof Container );

echo "\n== Results: {$passes} passed, {$fails} failed ==\n";

exit( 0 === $fails ? 0 : 1 );
