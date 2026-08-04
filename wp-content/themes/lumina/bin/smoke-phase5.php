<?php
/**
 * Phase 5 — Component Registry smoke suite (WP-free CLI).
 *
 * Drives the real boot entry (app/load.php → Kernel::launch()) WITHOUT a live
 * WordPress install and asserts the Phase 5 acceptance criteria:
 *
 *   1. PSR-4 resolves the Components subsystem classes
 *   2. App::make('components.registry') resolves via the container
 *   3. JSON discovery seeds 'card' + 'button' from components.json
 *   4. register()/get()/all()/versions() lifecycle
 *   5. render() produces HTML via the Phase-4 renderer
 *   6. Variant presets merge under explicit props
 *   7. Slots materialize child components into trusted HTML
 *   8. provides_slot() reflects declared slots
 *   9. resolveDependencies() passes on a valid graph
 *  10. Dependency cycles throw ComponentCycleException
 *  11. Missing dependencies throw ComponentException
 *  12. `[lumina:button]` shortcode renders identically to a direct call
 *  13. Unknown components/shortcodes fail gracefully
 *  14. Definition schema validation rejects bad names
 *  15. Phases 1–4 regression
 *
 * Determinism: refuses to run when a developer's own lumina.env.json exists
 * (same contract as smoke-phase1/2/3/4.php).
 *
 * Usage: php bin/smoke-phase5.php
 * Exit code 0 = all assertions passed; 1 = any failure.
 *
 * @package Lumina
 * @since 0.5.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__ ) . '/' );
}

require dirname( __DIR__ ) . '/app/load.php';

use Lumina\Core\Boot\Kernel;
use Lumina\Core\Components\ComponentCycleException;
use Lumina\Core\Components\ComponentDefinition;
use Lumina\Core\Components\ComponentException;
use Lumina\Core\Components\ComponentNotFoundException;
use Lumina\Core\Components\ComponentsServiceProvider;
use Lumina\Core\Components\CycleDetector;
use Lumina\Core\Components\DefinitionCompiler;
use Lumina\Core\Components\Loader;
use Lumina\Core\Components\Registry;
use Lumina\Core\Components\Resolver;
use Lumina\Core\Core\App;

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
 * A registry wired to a no-op renderer for destructive dependency tests.
 *
 * @return Registry
 */
function fresh_registry(): Registry {
	return new Registry( static fn( string $view, array $props = array() ): string => $view );
}

echo "== Lumina Core Phase 5 smoke suite (Component Registry) ==\n\n";

if ( file_exists( dirname( __DIR__ ) . '/lumina.env.json' ) ) {
	echo "[SKIP] lumina.env.json present — default-state assertions are not meaningful.\n";
	echo "       Remove it (or run in a clean checkout) for the full assertion run.\n";
	exit( 0 );
}

// 1. PSR-4 resolution.
check( 'PSR-4 resolves Registry', class_exists( Registry::class ) );
check( 'PSR-4 resolves ComponentDefinition', class_exists( ComponentDefinition::class ) );
check( 'PSR-4 resolves Loader', class_exists( Loader::class ) );
check( 'PSR-4 resolves DefinitionCompiler', class_exists( DefinitionCompiler::class ) );
check( 'PSR-4 resolves CycleDetector', class_exists( CycleDetector::class ) );
check( 'PSR-4 resolves Resolver', class_exists( Resolver::class ) );
check( 'PSR-4 resolves ComponentsServiceProvider', class_exists( ComponentsServiceProvider::class ) );

Kernel::launch();
$app = App::instance();

// 2. Container wiring.
check( 'App::make("components.registry") resolves', $app->make( 'components.registry' ) instanceof Registry );

/** @var Registry $registry */
$registry = $app->make( 'components.registry' );

// 3. JSON discovery.
check( 'discovered component "card"', $registry->get( 'card' ) instanceof ComponentDefinition );
check( 'discovered component "button"', $registry->get( 'button' ) instanceof ComponentDefinition );
check( 'component card declares its renderer', 'components/card' === $registry->get( 'card' )->renderer() );

// 4. Registration lifecycle (self-contained registry so the assertions
// never depend on the shipped catalog's version numbers — the catalog is
// a Phase 11 artifact and evolves independently).
$version_registry = fresh_registry();
$version_registry->register( 'card', 'components/card', array( 'version' => 1, 'slug' => 'card' ) );
$version_before = $version_registry->versions( 'card' );
$version_registry->register(
	'card',
	'components/card',
	array(
		'version'  => 2,
		'slug'     => 'card',
		'variants' => array(
			'default' => array( 'accent' => false ),
			'accent'  => array( 'accent' => true ),
		),
		'slots'    => array( 'actions' ),
		'deps'     => array( 'button' ),
	)
);
check( 're-registration publishes a new version', $version_registry->versions( 'card' ) === array( 1, 2 ) );
check( 'versions() is monotonic across publishes', $version_before === array( 1 ) );

$registry->register( 'demo', 'components/button', array( 'slug' => 'demo' ) );
check( 'register() exposes the definition via get()', $registry->get( 'demo' ) instanceof ComponentDefinition );
check( 'all() includes registered components', isset( $registry->all()['demo'] ) );

// 5. Rendering via the Phase-4 renderer.
$button_html = $registry->render( 'button', array( 'label' => 'Go' ) );
check( 'render("button") produces HTML', str_contains( $button_html, 'lumina-btn' ) && str_contains( $button_html, 'Go' ) );

$card_html = $registry->render(
	'card',
	array(
		'title'   => 'Hello Card',
		'excerpt' => 'Body',
		'link'    => 'https://example.com/c',
	)
);
check( 'render("card") produces HTML', str_contains( $card_html, 'lumina-card' ) && str_contains( $card_html, 'Hello Card' ) );
check( 'rendered card escapes the link attribute', str_contains( $card_html, 'href="https://example.com/c"' ) );

$xss = $registry->render( 'button', array( 'label' => '<script>alert(1)</script>' ) );
check( 'component output escapes XSS fixtures', ! str_contains( $xss, '<script>alert(1)</script>' ) && str_contains( $xss, '&lt;script&gt;' ) );

// 6. Variants.
$ghost = $registry->render( 'button', array( 'label' => 'G', 'variant' => 'ghost' ) );
check( 'variant preset class applies', str_contains( $ghost, 'lumina-btn--ghost' ) );

$explicit = $registry->render( 'button', array( 'label' => 'G', 'variant' => 'ghost', 'class' => 'custom' ) );
check( 'explicit props win over variant presets', str_contains( $explicit, 'custom' ) && ! str_contains( $explicit, 'lumina-btn--ghost' ) );

// 7. Slots.
$with_slot = $registry->render(
	'card',
	array(
		'title'   => 'Slotted',
		'actions' => array(
			array( 'name' => 'button', 'props' => array( 'label' => 'Buy', 'variant' => 'primary' ) ),
		),
	)
);
check( 'slot children render recursively', str_contains( $with_slot, 'Buy' ) && str_contains( $with_slot, 'lumina-card__actions' ) );

// 8. provides_slot().
check( 'provides_slot("card") true', $registry->provides_slot( 'card' ) );
check( 'provides_slot("button") false', ! $registry->provides_slot( 'button' ) );

// 9. Dependency validation on the valid JSON graph.
$deps_ok = true;
try {
	$registry->resolve_dependencies();
} catch ( ComponentException $e ) {
	$deps_ok = false;
}
check( 'resolveDependencies() passes on valid graph', $deps_ok );

// 10. Cycle detection.
$cycle_registry = fresh_registry();
$cycle_registry->register( 'a', 'a-view', array( 'deps' => array( 'b' ) ) );
$cycle_registry->register( 'b', 'b-view', array( 'deps' => array( 'a' ) ) );
$cycle_thrown = false;
try {
	$cycle_registry->resolve_dependencies();
} catch ( ComponentCycleException $e ) {
	$cycle_thrown = str_contains( $e->getMessage(), 'a -> b -> a' );
}
check( 'dependency cycle throws ComponentCycleException', $cycle_thrown );

// 11. Missing dependency.
$missing_registry = fresh_registry();
$missing_registry->register( 'c', 'c-view', array( 'deps' => array( 'ghost-component' ) ) );
$missing_thrown = false;
try {
	$missing_registry->resolve_dependencies();
} catch ( ComponentException $e ) {
	$missing_thrown = $e instanceof ComponentException && ! ( $e instanceof ComponentCycleException );
}
check( 'missing dependency throws ComponentException', $missing_thrown );

// 12. Shortcode DSL parity.
$direct = $registry->render( 'button', array( 'label' => 'Short', 'variant' => 'outline' ) );
$short  = $registry->render_shortcode( 'lumina:button', array( 'label' => 'Short', 'variant' => 'outline' ) );
check( 'shortcode [lumina:button] renders identically to a direct call', $direct === $short );
check( 'shortcodes() maps the DSL tag', isset( $registry->shortcodes()['lumina:button'] ) );
$demo_short = $registry->render_shortcode( 'lumina:demo', array( 'label' => 'x', 'size' => 5 ) );
check( 'shortcode attrs coerce numeric sizes', str_contains( $demo_short, 'data-lumina-size="5"' ) );

// 13. Graceful failures.
$unknown_render_thrown = false;
try {
	$registry->render( 'ghost-component' );
} catch ( ComponentNotFoundException $e ) {
	$unknown_render_thrown = true;
}
check( 'unknown component render throws ComponentNotFoundException', $unknown_render_thrown );
check( 'unknown shortcode renders empty string', '' === $registry->render_shortcode( 'lumina:ghost' ) );

// 14. Schema validation.
$invalid_name_thrown = false;
try {
	fresh_registry()->register( 'Bad Name!', 'x-view' );
} catch ( ComponentException $e ) {
	$invalid_name_thrown = true;
}
check( 'invalid component name rejected', $invalid_name_thrown );

$empty_renderer_thrown = false;
try {
	fresh_registry()->register( 'ok-name', '   ' );
} catch ( ComponentException $e ) {
	$empty_renderer_thrown = true;
}
check( 'empty renderer view rejected', $empty_renderer_thrown );

// 15. Phases 1–4 regression.
check( 'Phase 1 regression: env resolvable', 'production' === $app->make( 'env' ) );
check( 'Phase 2 regression: container is Container', $app->make( 'container' ) instanceof \Lumina\Core\Container\Container );
check( 'Phase 4 regression: renderer resolves', $app->make( 'render.renderer' ) instanceof \Lumina\Core\Render\Renderer );
check( 'Phase 4 regression: render("card") still works', str_contains( $app->make( 'render.renderer' )->render( 'card', array( 'title' => 'R' ) ), 'R' ) );

echo "\n== Results: {$passes} passed, {$fails} failed ==\n";

exit( 0 === $fails ? 0 : 1 );
