<?php
/**
 * Phase 6 — Template System smoke suite (WP-free CLI).
 *
 * Drives the real boot entry (app/load.php → Kernel::launch()) WITHOUT a live
 * WordPress install and asserts the Phase 6 acceptance criteria:
 *
 *   1. PSR-4 resolves the Templates subsystem classes
 *   2. Container wiring (resolver, partials, sections, bridge)
 *   3. 'single' resolves to the child templates/single.php
 *   4. WP template hierarchy order (single-{type}-{slug} → … → index)
 *   5. Override tiers: override → base → wp-{name} → parent → null
 *   6. Parent-theme fallback tier (fixture dir)
 *   7. PartialLoader: content-single renders; missing partial → index fallback;
 *      no fallback → RenderException
 *   8. Sections: register/render order, has, clear; view-slug + callable
 *   9. View facade: View::partial() + View::section() after boot
 *  10. ThemeTemplatesBridge::locate() + guarded register()
 *  11. Phases 1–5 regression
 *
 * Determinism: refuses to run when a developer's own phantom.env.json exists.
 *
 * Usage: php bin/smoke-phase6.php
 * Exit code 0 = all assertions passed; 1 = any failure.
 *
 * @package Phantom
 * @since 0.6.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__ ) . '/' );
}

require dirname( __DIR__ ) . '/app/load.php';

use Phantom\Core\Boot\Kernel;
use Phantom\Core\Core\App;
use Phantom\Core\Render\RenderException;
use Phantom\Core\Templates\PartialLoader;
use Phantom\Core\Templates\Sections;
use Phantom\Core\Templates\TemplateResolver;
use Phantom\Core\Templates\TemplatesServiceProvider;
use Phantom\Core\Templates\ThemeTemplatesBridge;
use Phantom\Core\Templates\View;

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

/**
 * Build a parent-theme fixture directory and return its path.
 *
 * @return string Absolute fixture dir (created on disk).
 */
function make_parent_fixture(): string {
	$dir = sys_get_temp_dir() . '/phantom-smoke-parent-' . getmypid();
	@mkdir( $dir, 0777, true );

	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents -- WP-free fixture.
	@file_put_contents( $dir . '/index.php', "<?php\n// Parent-theme fallback fixture.\n" );

	return $dir;
}

/**
 * Remove the parent-theme fixture directory.
 *
 * @param string $dir Fixture dir.
 * @return void
 */
function drop_parent_fixture( string $dir ): void {
	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_unlink -- WP-free fixture cleanup.
	@unlink( $dir . '/index.php' );
	@rmdir( $dir );
}

echo "== Phantom Core Phase 6 smoke suite (Template System) ==\n\n";

if ( file_exists( dirname( __DIR__ ) . '/phantom.env.json' ) ) {
	echo "[SKIP] phantom.env.json present — default-state assertions are not meaningful.\n";
	echo "       Remove it (or run in a clean checkout) for the full assertion run.\n";
	exit( 0 );
}

$templates_dir = dirname( __DIR__ ) . '/templates';
$parent_dir    = make_parent_fixture();

// 1. PSR-4 resolution.
check( 'PSR-4 resolves TemplateResolver', class_exists( TemplateResolver::class ) );
check( 'PSR-4 resolves PartialLoader', class_exists( PartialLoader::class ) );
check( 'PSR-4 resolves Sections', class_exists( Sections::class ) );
check( 'PSR-4 resolves ThemeTemplatesBridge', class_exists( ThemeTemplatesBridge::class ) );
check( 'PSR-4 resolves TemplatesServiceProvider', class_exists( TemplatesServiceProvider::class ) );

Kernel::launch();
$app = App::instance();

// 2. Container wiring.
check( 'templates.resolver resolves', $app->make( 'templates.resolver' ) instanceof TemplateResolver );
check( 'templates.partials resolves', $app->make( 'templates.partials' ) instanceof PartialLoader );
check( 'templates.sections resolves', $app->make( 'templates.sections' ) instanceof Sections );
check( 'templates.bridge resolves', $app->make( 'templates.bridge' ) instanceof ThemeTemplatesBridge );

/** @var TemplateResolver $resolver */
$resolver = $app->make( 'templates.resolver' );

// 3. Acceptance: 'single' resolves to the child template.
$single = $resolver->resolve( 'single' );
check( "'single' resolves to child templates/single.php", is_string( $single ) && str_ends_with( $single, 'single.php' ), (string) $single );

// 4. WP hierarchy order.
$hierarchy_plain = $resolver->hierarchy( 'single' );
check( 'hierarchy(single) base order', array( 'single', 'singular', 'index' ) === $hierarchy_plain, implode( ',', $hierarchy_plain ) );

$hierarchy_ctx = $resolver->hierarchy( 'single', array( 'post_type' => 'post', 'slug' => 'hello-world' ) );
check(
	'hierarchy(single) prefixes most-specific first',
	array( 'single-post-hello-world', 'single-post', 'single', 'singular', 'index' ) === $hierarchy_ctx,
	implode( ',', $hierarchy_ctx )
);

$hierarchy_page = $resolver->hierarchy( 'page', array( 'slug' => 'about', 'id' => 12 ) );
check( 'hierarchy(page) includes id + slug prefixes', array( 'page-about', 'page-12', 'page', 'singular', 'index' ) === $hierarchy_page, implode( ',', $hierarchy_page ) );

check( 'unknown type falls back to index chain', array( 'index' ) === $resolver->hierarchy( 'nope' ) );

// 5. Override tiers + candidates.
$candidates = $resolver->candidates( 'single', array( 'override' => 'theme-x' ) );
check( 'override tier is most specific', str_ends_with( $candidates[0], 'theme-x/single.php' ) );
check( 'base tier follows override', str_ends_with( $candidates[1], 'single.php' ) );
check( 'wp-{name} tier present', str_ends_with( $candidates[2], 'wp-single.php' ) );

$override_miss = $resolver->path( 'single', array( 'override' => 'does-not-exist' ) );
check( 'missing override falls back to base tier', is_string( $override_miss ) && str_ends_with( $override_miss, 'single.php' ) );

// 6. Parent-theme fallback tier.
$parent_resolver = new TemplateResolver( $templates_dir, $parent_dir );
$parent_index    = $parent_resolver->path( 'index' );
check( 'parent tier resolves files missing from the child', is_string( $parent_index ) && str_contains( $parent_index, $parent_dir ), (string) $parent_index );

check( 'unknown template resolves to null', null === $resolver->resolve( 'ghost.template' ) );

// 7. PartialLoader.
/** @var PartialLoader $partials */
$partials    = $app->make( 'templates.partials' );
$partial_html = $partials->partial( 'content-single', array( 'title' => 'Hello Single', 'excerpt' => 'Body' ) );
check( 'partial("content-single") renders', str_contains( $partial_html, 'phantom-entry' ) && str_contains( $partial_html, 'Hello Single' ) );

$fallback_html = $partials->partial( 'nope.never' );
check( 'missing partial falls back to index', str_contains( $fallback_html, 'data-phantom-partial="index"' ) );

$thrown = false;
try {
	$partials->partial( 'nope.never', array(), null );
} catch ( RenderException $e ) {
	$thrown = true;
}
check( 'no-fallback miss throws RenderException', $thrown );

// 8. Sections.
/** @var Sections $sections */
$sections = $app->make( 'templates.sections' );
$sections->register( 'loop', 'card' );
$sections->register( 'loop', static fn( array $args ): string => '<p class="extra">tail</p>' );

check( 'sections has() after register', $sections->has( 'loop' ) );

$loop_html = $sections->render( 'loop' );
check( 'sections render view slugs + callables in order', str_contains( $loop_html, 'phantom-card' ) && str_contains( $loop_html, '<p class="extra">tail</p>' ) );
check( 'sections order preserved (view first, callable last)', strpos( $loop_html, 'phantom-card' ) < strpos( $loop_html, 'extra' ) );

$sections->clear( 'loop' );
check( 'sections clear() empties the region', ! $sections->has( 'loop' ) && '' === $sections->render( 'loop' ) );

// 9. View facade.
$view_partial = View::partial( 'content-single', array( 'title' => 'Facade' ) );
check( 'View::partial() renders via the facade', str_contains( $view_partial, 'Facade' ) );

View::section( 'after-main' ); // no-op when empty — must not throw.
$app->make( 'templates.sections' )->register( 'after-main', static fn( array $args ): string => '<section id="after">ok</section>' );
check( 'View::section() renders registered sections', str_contains( View::section( 'after-main' ), 'after' ) );

// 10. Bridge.
/** @var ThemeTemplatesBridge $bridge */
$bridge = $app->make( 'templates.bridge' );
$bridge->register(); // guarded no-op in WP-free context — must not throw.

$located_single = $bridge->locate( 'single' );
check( 'bridge locate("single") returns child template', is_string( $located_single ) && str_ends_with( $located_single, 'single.php' ) );

// 11. Phases 1–5 regression.
check( 'Phase 1 regression: env resolvable', 'production' === $app->make( 'env' ) );
check( 'Phase 4 regression: renderer resolves', $app->make( 'render.renderer' ) instanceof \Phantom\Core\Render\Renderer );
check( 'Phase 5 regression: components registry resolves', $app->make( 'components.registry' ) instanceof \Phantom\Core\Components\Registry );
check( 'Phase 5 regression: render("card") works', str_contains( $app->make( 'components.registry' )->render( 'card', array( 'title' => 'C6' ) ), 'C6' ) );

drop_parent_fixture( $parent_dir );

echo "\n== Results: {$passes} passed, {$fails} failed ==\n";

exit( 0 === $fails ? 0 : 1 );
