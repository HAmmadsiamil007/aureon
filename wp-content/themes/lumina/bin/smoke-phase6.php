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
 *   5. Override tiers: override → base → wp-{name} → null (no parent tier —
 *      Phase 16 standalone: Lumina ships its own WP hierarchy files)
 *   6. Parent fallback removed: no parent-dir constructor arg, unknown
 *      templates resolve to null
 *   7. PartialLoader: content-single renders; missing partial → index fallback;
 *      no fallback → RenderException
 *   8. Sections: register/render order, has, clear; view-slug + callable
 *   9. View facade: View::partial() + View::section() after boot
 *  10. ThemeTemplatesBridge::locate() + guarded register()
 *  11. Standalone shell: theme ships root header/footer/index + no Template:
 *      header in style.css
 *  12. Phases 1–5 regression
 *
 * Determinism: refuses to run when a developer's own lumina.env.json exists.
 *
 * Usage: php bin/smoke-phase6.php
 * Exit code 0 = all assertions passed; 1 = any failure.
 *
 * @package Lumina
 * @since 0.6.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__ ) . '/' );
}

require dirname( __DIR__ ) . '/app/load.php';

use Lumina\Core\Boot\Kernel;
use Lumina\Core\Core\App;
use Lumina\Core\Render\RenderException;
use Lumina\Core\Templates\PartialLoader;
use Lumina\Core\Templates\Sections;
use Lumina\Core\Templates\TemplateResolver;
use Lumina\Core\Templates\TemplatesServiceProvider;
use Lumina\Core\Templates\ThemeTemplatesBridge;
use Lumina\Core\Templates\View;

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

echo "== Lumina Core Phase 6 smoke suite (Template System) ==\n\n";

if ( file_exists( dirname( __DIR__ ) . '/lumina.env.json' ) ) {
	echo "[SKIP] lumina.env.json present — default-state assertions are not meaningful.\n";
	echo "       Remove it (or run in a clean checkout) for the full assertion run.\n";
	exit( 0 );
}

$templates_dir = dirname( __DIR__ ) . '/templates';

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

// 6. Parent fallback removed (Phase 16 standalone).
$tier_notes = $resolver->path( 'does-not-exist-anywhere' );
check( 'no parent tier: unresolvable template is null (constructor takes 1 arg)', null === $tier_notes, is_string( $tier_notes ) ? $tier_notes : 'null' );

check( 'unknown template resolves to null', null === $resolver->resolve( 'ghost.template' ) );

// 7. PartialLoader.
/** @var PartialLoader $partials */
$partials    = $app->make( 'templates.partials' );
$partial_html = $partials->partial( 'content-single', array( 'title' => 'Hello Single', 'excerpt' => 'Body' ) );
check( 'partial("content-single") renders', str_contains( $partial_html, 'lumina-entry' ) && str_contains( $partial_html, 'Hello Single' ) );

$fallback_html = $partials->partial( 'nope.never' );
check( 'missing partial falls back to index', str_contains( $fallback_html, 'data-lumina-partial="index"' ) );

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
check( 'sections render view slugs + callables in order', str_contains( $loop_html, 'lumina-card' ) && str_contains( $loop_html, '<p class="extra">tail</p>' ) );
check( 'sections order preserved (view first, callable last)', strpos( $loop_html, 'lumina-card' ) < strpos( $loop_html, 'extra' ) );

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

// 11. Standalone shell (Phase 16): root hierarchy files ship with the theme
// and the parent dependency is gone from style.css.
$theme_root = dirname( __DIR__ );
check( 'standalone: header.php ships', is_readable( $theme_root . '/header.php' ) );
check( 'standalone: footer.php ships', is_readable( $theme_root . '/footer.php' ) );
check( 'standalone: index.php ships', is_readable( $theme_root . '/index.php' ) );
check( 'standalone: 404.php ships', is_readable( $theme_root . '/404.php' ) );
check( 'standalone: search.php ships', is_readable( $theme_root . '/search.php' ) );

$style_header = (string) @file_get_contents( $theme_root . '/style.css' );
check( 'standalone: no Template: header in style.css', false === stripos( $style_header, 'Template:' ) );

// 12. Phases 1–5 regression.
check( 'Phase 1 regression: env resolvable', 'production' === $app->make( 'env' ) );
check( 'Phase 4 regression: renderer resolves', $app->make( 'render.renderer' ) instanceof \Lumina\Core\Render\Renderer );
check( 'Phase 5 regression: components registry resolves', $app->make( 'components.registry' ) instanceof \Lumina\Core\Components\Registry );
check( 'Phase 5 regression: render("card") works', str_contains( $app->make( 'components.registry' )->render( 'card', array( 'title' => 'C6' ) ), 'C6' ) );

echo "\n== Results: {$passes} passed, {$fails} failed ==\n";

exit( 0 === $fails ? 0 : 1 );
