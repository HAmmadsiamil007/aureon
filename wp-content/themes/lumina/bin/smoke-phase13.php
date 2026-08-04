<?php
/**
 * Phase 13 — Performance Engineering smoke suite (WP-free CLI).
 *
 * Drives the real boot entry (app/load.php → Kernel::launch()) WITHOUT a live
 * WordPress install and asserts the Phase 13 acceptance criteria:
 *
 *   1. PSR-4 resolves the Performance subsystem classes
 *   2. Container wiring (performance.budget/logger/guard/lazy/purger)
 *   3. Budget defaults match the plan §Phase 13 numbers
 *   4. BudgetLogger::check() flags over-budget metrics and passes in-budget
 *   5. QueryGuard: inactive by default; limit()/register() mechanics in debug
 *   6. Lazy builders produce CLS-safe image/iframe attributes
 *   7. CachePurger::purge() records domains + is WP-free safe
 *   8. Lazy::prefers_reduced_motion() seam
 *   9. Phases 1–12 regression
 *
 * Determinism: refuses to run when a developer's own lumina.env.json exists.
 *
 * Usage: php bin/smoke-phase13.php
 * Exit code 0 = all assertions passed; 1 = any failure.
 *
 * @package Lumina
 * @since 0.13.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__ ) . '/' );
}

require dirname( __DIR__ ) . '/app/load.php';

use Lumina\Core\Boot\Kernel;
use Lumina\Core\Core\App;
use Lumina\Core\Performance\Budget;
use Lumina\Core\Performance\BudgetLogger;
use Lumina\Core\Performance\CachePurger;
use Lumina\Core\Performance\Lazy;
use Lumina\Core\Performance\QueryGuard;

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

echo "== Lumina Core Phase 13 smoke suite (Performance Engineering) ==\n\n";

if ( file_exists( dirname( __DIR__ ) . '/lumina.env.json' ) ) {
	echo "[SKIP] lumina.env.json present — default-state assertions are not meaningful.\n";
	echo "       Remove it (or run in a clean checkout) for the full assertion run.\n";
	exit( 0 );
}

// 1. PSR-4 resolution.
check( 'PSR-4 resolves Budget', class_exists( Budget::class ) );
check( 'PSR-4 resolves BudgetLogger', class_exists( BudgetLogger::class ) );
check( 'PSR-4 resolves QueryGuard', class_exists( QueryGuard::class ) );
check( 'PSR-4 resolves Lazy', class_exists( Lazy::class ) );
check( 'PSR-4 resolves CachePurger', class_exists( CachePurger::class ) );

Kernel::launch();
$app = App::instance();

// 2. Container wiring.
check( 'performance.budget resolves', $app->make( 'performance.budget' ) instanceof Budget );
check( 'performance.logger resolves', $app->make( 'performance.logger' ) instanceof BudgetLogger );
check( 'performance.guard resolves', $app->make( 'performance.guard' ) instanceof QueryGuard );
check( 'performance.lazy resolves', $app->make( 'performance.lazy' ) instanceof Lazy );
check( 'performance.purger resolves', $app->make( 'performance.purger' ) instanceof CachePurger );

/** @var Budget $budget */
$budget = $app->make( 'performance.budget' );

// 3. Budget defaults (plan §Phase 13).
check( 'LCP budget 2.0s', abs( $budget->lcp() - 2.0 ) < 0.001 );
check( 'CLS budget 0.05', abs( $budget->cls() - 0.05 ) < 0.001 );
check( 'INP budget 150ms', abs( $budget->inp() - 150.0 ) < 0.001 );
check( 'JS budget 120KB', 120 === $budget->js_kb() );
check( 'CSS budget 50KB', 50 === $budget->css_kb() );
check( 'server budget 300ms', 300 === $budget->server_ms() );
check( 'query budget 8', 8 === $budget->queries() );

// 4. BudgetLogger behavior.
/** @var BudgetLogger $logger */
$logger = $app->make( 'performance.logger' );

$clean = $logger->check(
	array(
		'lcp'       => 1.2,
		'cls'       => 0.01,
		'inp'       => 90.0,
		'js_kb'     => 80,
		'css_kb'    => 30,
		'server_ms' => 200,
		'queries'   => 5,
	)
);
check( 'in-budget metrics yield no violations', array() === $clean );

$bad = $logger->check(
	array(
		'lcp'       => 3.1,
		'cls'       => 0.2,
		'inp'       => 320.0,
		'js_kb'     => 210,
		'css_kb'    => 90,
		'server_ms' => 700,
		'queries'   => 40,
	)
);
check( 'over-budget metrics yield violations', count( $bad ) >= 7 );
check( 'violation names the metric + budget', str_contains( $bad[0] ?? '', 'LCP' ) && str_contains( $bad[0] ?? '', '2.00' ) );

// 5. QueryGuard.
/** @var QueryGuard $guard */
$guard = $app->make( 'performance.guard' );
check( 'query guard inactive by default', ! $guard->is_active() );
check( 'inactive guard registers silently', ( static function (): bool {
	$g = new QueryGuard( false );
	$g->limit( 3 );
	$g->register();
	$g->register();
	$g->register();
	$g->register();

	return 0 === $g->count() && ! $g->exceeded();
} )() );

check( 'active guard counts + warns over budget', ( static function (): bool {
	$g = new QueryGuard( true );
	$g->limit( 2 );
	$g->register();
	$g->register();
	$g->register();
	$g->register();

	return 4 === $g->count() && $g->exceeded() && 2 === $g->limit_value();
} )() );

// 6. Lazy builders.
check( 'image attrs lazy + decode async', str_contains( Lazy::image_attrs(), 'loading="lazy"' ) && str_contains( Lazy::image_attrs(), 'decoding="async"' ) );
check( 'image attrs include dimensions for CLS', str_contains( Lazy::image_attrs( 800, 600 ), 'width="800"' ) && str_contains( Lazy::image_attrs( 800, 600 ), 'height="600"' ) );
check( 'iframe attrs lazy + allowfullscreen', str_contains( Lazy::iframe_attrs( 'Video' ), 'loading="lazy"' ) && str_contains( Lazy::iframe_attrs(), 'allowfullscreen' ) );
check( 'iframe attrs carry title', str_contains( Lazy::iframe_attrs( 'Demo' ), 'title="Demo"' ) );
check( 'reduced-motion seam present', false === Lazy::prefers_reduced_motion() );

// 7. CachePurger.
$purger = new CachePurger( static function ( string $domain ): void {
	// no-op WP-free.
} );
check( 'purge() records the domain', ( static function () use ( $purger ): bool {
	$purger->purge( 'tokens' );

	return array( 'tokens' ) === $purger->purged_domains();
} )() );
check( 'purge() dedupes domains', ( static function () use ( $purger ): bool {
	$purger->purge( 'tokens' );
	$purger->purge( 'render' );

	return array( 'tokens', 'render' ) === $purger->purged_domains();
} )() );
check( 'purge() rejects empty domains', 0 === $purger->purge( '   ' ) );

// 8. Config-driven guard flag exists.
check( 'config exposes performance.query_guard', null !== $app->make( 'config' )->get( 'performance.query_guard' ) );
check( 'config exposes performance.budgets', is_array( $app->make( 'config' )->get( 'performance.budgets' ) ) );

// 9. Phases 1–12 regression.
check( 'Phase 1 regression: env resolvable', 'production' === $app->make( 'env' ) );
check( 'Phase 2 regression: container is Container', $app->make( 'container' ) instanceof \Lumina\Core\Container\Container );
check( 'Phase 4 regression: renderer resolves', $app->make( 'render.renderer' ) instanceof \Lumina\Core\Render\Renderer );
check( 'Phase 5 regression: registry resolves', $app->make( 'components.registry' ) instanceof \Lumina\Core\Components\Registry );
check( 'Phase 6 regression: composer resolves', $app->make( 'templates.composer' ) instanceof \Lumina\Core\Templates\Composer );
check( 'Phase 7 regression: asset loader resolves', $app->make( 'assets.loader' ) instanceof \Lumina\Core\Assets\AssetLoader );
check( 'Phase 10 regression: animation engine resolves', $app->make( 'animation.engine' ) instanceof \Lumina\Core\Animation\Engine );
check( 'Phase 11 regression: hero renders', str_contains( $app->make( 'components.registry' )->render( 'hero', array( 'title' => 'R' ) ), 'lumina-hero' ) );

echo "\n== Results: {$passes} passed, {$fails} failed ==\n";

exit( 0 === $fails ? 0 : 1 );
