<?php
/**
 * Phase 14 — Accessibility Engineering smoke suite (WP-free CLI).
 *
 * Drives the real boot entry (app/load.php → Kernel::launch()) WITHOUT a live
 * WordPress install and asserts the Phase 14 acceptance criteria:
 *
 *   1. PSR-4 resolves the A11y subsystem classes
 *   2. Container wiring (a11y.checker/skip_link/dialog)
 *   3. Checker::run() passes clean, WCAG-oriented HTML
 *   4. Checker::run() flags real violations (headings, landmarks, images,
 *      forms, interactive names, focus, dialogs)
 *   5. SkipLink renders a first-focusable, escaped skip link
 *   6. DialogManager contract: required attributes + validation
 *   7. Feature/config wiring (features.accessibility)
 *   8. Phases 1–13 regression
 *
 * Determinism: refuses to run when a developer's own lumina.env.json exists.
 *
 * Usage: php bin/smoke-phase14.php
 * Exit code 0 = all assertions passed; 1 = any failure.
 *
 * @package Lumina
 * @since 0.14.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__ ) . '/' );
}

require dirname( __DIR__ ) . '/app/load.php';

use Lumina\Core\Boot\Kernel;
use Lumina\Core\Core\App;
use Lumina\Core\A11y\Checker;
use Lumina\Core\A11y\DialogManager;
use Lumina\Core\A11y\SkipLink;

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

echo "== Lumina Core Phase 14 smoke suite (Accessibility Engineering) ==\n\n";

if ( file_exists( dirname( __DIR__ ) . '/lumina.env.json' ) ) {
	echo "[SKIP] lumina.env.json present — default-state assertions are not meaningful.\n";
	echo "       Remove it (or run in a clean checkout) for the full assertion run.\n";
	exit( 0 );
}

// 1. PSR-4 resolution.
check( 'PSR-4 resolves Checker', class_exists( Checker::class ) );
check( 'PSR-4 resolves SkipLink', class_exists( SkipLink::class ) );
check( 'PSR-4 resolves DialogManager', class_exists( DialogManager::class ) );

Kernel::launch();
$app = App::instance();

// 2. Container wiring.
check( 'a11y.checker resolves', $app->make( 'a11y.checker' ) instanceof Checker );
check( 'a11y.skip_link resolves', $app->make( 'a11y.skip_link' ) instanceof SkipLink );
check( 'a11y.dialog resolves', $app->make( 'a11y.dialog' ) instanceof DialogManager );

/** @var Checker $checker */
$checker = $app->make( 'a11y.checker' );

// 3. Checker passes clean, WCAG-oriented HTML.
$clean = '<!doctype html><html><head><title>t</title></head><body>'
	. '<header><a class="screen-reader-text" href="#main">Skip</a></header>'
	. '<nav aria-label="Main"><a href="/">Home</a></nav>'
	. '<main id="main"><h1>Title</h1><p>Body.</p>'
	. '<img src="a.jpg" alt="A photo" width="800" height="600">'
	. '<form><label for="q">Search</label><input id="q" name="q" type="text">'
	. '<button type="submit">Go</button></form>'
	. '<a href="/more">Read more</a>'
	. '</main><footer>© 2026</footer></body></html>';

$clean_result = $checker->run( $clean );
check( 'clean HTML passes the audit', $clean_result['pass'], implode( '; ', $clean_result['findings'] ) );

// 4a. Headings: multiple h1 + skipped levels.
$multi_h1 = $checker->run( '<h1>One</h1><h1>Two</h1><h2>Deep</h2>' );
check( 'multiple h1 flagged', ! $multi_h1['pass'] && in_array( 'multiple h1 elements (2)', $multi_h1['findings'], true ) );

$skip_level = $checker->run( '<h1>One</h1><h3>Jump</h3>' );
check(
	'heading level skip flagged',
	! $skip_level['pass'] && in_array( 'heading level skipped from h1 to h3', $skip_level['findings'], true )
);

// 4a2. Missing h1 flagged.
$no_h1 = $checker->run( '<h2>Sub</h2><h3>Subsub</h3>' );
check( 'missing h1 flagged', in_array( 'no h1 element', $no_h1['findings'], true ) );

// 4b. Landmarks.
$no_landmarks = $checker->run( '<h1>Only</h1><p>Text.</p>' );
check( 'missing main landmark flagged', in_array( 'missing main landmark', $no_landmarks['findings'], true ) );
check( 'missing nav/aside flagged', in_array( 'no nav or aside landmark', $no_landmarks['findings'], true ) );

// 4c. Images without alt.
$no_alt = $checker->run( '<img src="x.png" width="1" height="1">' );
check( 'img without alt flagged', in_array( 'img without alt attribute', $no_alt['findings'], true ) );

// 4d. Forms: control without label.
$no_label = $checker->run( '<input type="text" name="q">' );
check( 'form control without label flagged', in_array( 'form control without label or id', $no_label['findings'], true ) );

// 4d2. Forms: id present but not referenced by a label.
$unreferenced = $checker->run( '<input id="q" name="q" type="text">' );
check( 'form control id not label-referenced flagged', in_array( 'form control id not referenced by a label', $unreferenced['findings'], true ) );

// 4d3. Forms: label for= association passes.
$associated = $checker->run( '<label for="q">Search</label><input id="q" name="q" type="text">' );
check( 'label for= association passes', ! in_array( 'form control id not referenced by a label', $associated['findings'], true ) );

// 4e. Interactive names.
$no_name = $checker->run( '<button></button>' );
check( 'button without accessible name flagged', in_array( 'button without accessible name', $no_name['findings'], true ) );

// 4e2. Image-only link names itself via img alt.
$image_link = $checker->run( '<a href="/p"><img src="p.jpg" alt="Product photo"></a>' );
check( 'image-only link with alt not flagged', ! in_array( 'a without accessible name', $image_link['findings'], true ) );

// 4f. Focus: positive tabindex.
$tabindex = $checker->run( '<a href="#" tabindex="5">X</a>' );
check( 'positive tabindex flagged', in_array( 'positive tabindex found', $tabindex['findings'], true ) );

// 4g. Dialog without tabindex="-1".
$bad_dialog = $checker->run( '<div role="dialog" aria-modal="true"></div>' );
check( 'dialog without tabindex="-1" flagged', in_array( 'dialog without tabindex="-1"', $bad_dialog['findings'], true ) );

$good_dialog = $checker->run( '<div role="dialog" aria-modal="true" tabindex="-1" aria-labelledby="t"></div>' );
check( 'dialog with tabindex="-1" not flagged', ! in_array( 'dialog without tabindex="-1"', $good_dialog['findings'], true ) );

// 4g2. Attribute order must not matter (tabindex before role).
$reordered_dialog = $checker->run( '<div tabindex="-1" role="dialog" aria-modal="true" aria-labelledby="t"></div>' );
check( 'dialog compliant regardless of attribute order', ! in_array( 'dialog without tabindex="-1"', $reordered_dialog['findings'], true ) );

// 4g3. Positive tabindex is flagged regardless of role.
$plain_dialog = $checker->run( '<div role="dialog" aria-modal="true" tabindex="1"></div>' );
check( 'positive tabindex still flagged', in_array( 'positive tabindex found', $plain_dialog['findings'], true ) );

// 5. SkipLink.
$skip = SkipLink::render();
check( 'skip link renders', str_contains( $skip, 'class="screen-reader-text lumina-skip-link"' ) );
check( 'skip link targets #main', str_contains( $skip, 'href="#main"' ) );
check( 'skip link label escaped', str_contains( $skip, '>Skip to content</a>' ) );
check( 'skip link escapes target', ( static function (): bool {
	$rendered = SkipLink::render( 'main" onclick="alert(1)' );

	return ! str_contains( $rendered, 'onclick="alert(1)"' ) && str_contains( $rendered, '#main&quot;' );
} )() );
check( 'skip link escapes label', ( static function (): bool {
	$rendered = SkipLink::render( 'main', '<script>alert(1)</script>' );

	return ! str_contains( $rendered, '<script>' ) && str_contains( $rendered, '&lt;script&gt;' );
} )() );
check( 'skip link default target normalized', str_contains( SkipLink::render( '#main' ), 'href="#main"' ) );

// 6. DialogManager.
/** @var DialogManager $dialog */
$dialog = $app->make( 'a11y.dialog' );
check( 'dialog required attributes include role/aria-modal/tabindex', ( static function () use ( $dialog ): bool {
	$attrs = $dialog->required_attributes();

	return in_array( 'role="dialog"', $attrs, true )
		&& in_array( 'aria-modal="true"', $attrs, true )
		&& in_array( 'tabindex="-1"', $attrs, true );
} )() );

$compliant_dialog = '<div role="dialog" aria-modal="true" tabindex="-1" aria-labelledby="dialog-title"></div>';
check( 'compliant dialog validates clean', array() === $dialog->validate( $compliant_dialog ) );

$incomplete_dialog = '<div role="dialog"></div>';
$missing = $dialog->validate( $incomplete_dialog );
check( 'incomplete dialog reports missing aria-modal', in_array( 'aria-modal="true"', $missing, true ) );
check( 'incomplete dialog reports missing tabindex', in_array( 'tabindex="-1"', $missing, true ) );
check( 'incomplete dialog reports missing labelledby', in_array( 'aria-labelledby', $missing, true ) );

// 7. Feature/config wiring.
check( 'config exposes features.accessibility', true === $app->make( 'config' )->get( 'features.accessibility' ) );

// 8. Phases 1–13 regression.
check( 'Phase 1 regression: env resolvable', 'production' === $app->make( 'env' ) );
check( 'Phase 2 regression: container is Container', $app->make( 'container' ) instanceof \Lumina\Core\Container\Container );
check( 'Phase 4 regression: renderer resolves', $app->make( 'render.renderer' ) instanceof \Lumina\Core\Render\Renderer );
check( 'Phase 5 regression: registry resolves', $app->make( 'components.registry' ) instanceof \Lumina\Core\Components\Registry );
check( 'Phase 6 regression: composer resolves', $app->make( 'templates.composer' ) instanceof \Lumina\Core\Templates\Composer );
check( 'Phase 10 regression: animation engine resolves', $app->make( 'animation.engine' ) instanceof \Lumina\Core\Animation\Engine );
check( 'Phase 13 regression: budget resolves', $app->make( 'performance.budget' ) instanceof \Lumina\Core\Performance\Budget );

echo "\n== Results: {$passes} passed, {$fails} failed ==\n";

exit( 0 === $fails ? 0 : 1 );
