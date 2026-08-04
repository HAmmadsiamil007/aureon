<?php
/**
 * Phase 16 — Lumina theme + Companion plugin integration smoke (WP-free CLI).
 *
 * Loads BOTH the Lumina theme kernel and the companion plugin, then asserts
 * the integration contract (STEP 8):
 *
 *   1. Theme kernel boots WP-free (Kernel::launch + App::instance)
 *   2. Plugin loads after the theme without fatal errors
 *   3. Plugin registers its 8 modules; theme container still resolves
 *   4. Theme shell exposes the 4 public region hooks the plugin fills
 *      (lumina_before_header, lumina_after_header, lumina_before_footer,
 *      lumina_after_footer) in header.php / footer.php
 *   5. Plugin template-data injection feeds the theme composition pipeline
 *      (lumina_template_data filter key exists in theme templates)
 *   6. Theme CSS vars (--lumina-*) and plugin CSS vars coexist without
 *      collision; no --phantom- anywhere
 *
 * Usage: php bin/smoke-phase16-integration.php
 * Exit code 0 = all assertions passed; 1 = any failure.
 *
 * @package Lumina
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__, 3 ) . '/themes/lumina/' );
}

$theme_dir = dirname( __DIR__, 3 ) . '/themes/lumina';

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

echo "== Lumina + Companion integration smoke suite (Phase 16, WP-free) ==\n\n";

// 1. Theme kernel boots.
require $theme_dir . '/app/load.php';
\Lumina\Core\Boot\Kernel::launch();
$app = \Lumina\Core\Core\App::instance();
check( 'theme kernel boots WP-free', $app instanceof \Lumina\Core\Core\App );
check( 'theme renderer resolves', $app->make( 'render.renderer' ) instanceof \Lumina\Core\Render\Renderer );

// 2. Plugin loads after the theme.
require dirname( __DIR__ ) . '/lumina-companion.php';
check( 'plugin loads after theme without fatal', class_exists( \Lumina\Companion\Plugin::class ) );

$plugin = \Lumina\Companion\Plugin::instance();
$plugin->boot();
check( 'plugin boots alongside theme', true );

// 3. Plugin modules + theme container coexist.
$slugs = $plugin->module_slugs();
check( 'plugin registers 8 modules', 8 === count( $slugs ), implode( ',', $slugs ) );
check( 'theme container still resolves templates', $app->make( 'templates.resolver' ) instanceof \Lumina\Core\Templates\TemplateResolver );
check( 'theme container still resolves components', $app->make( 'components.registry' ) instanceof \Lumina\Core\Components\Registry );

// 4. Theme shell exposes the public region hooks.
$header = (string) file_get_contents( $theme_dir . '/header.php' );
$footer = (string) file_get_contents( $theme_dir . '/footer.php' );

check( 'header.php fires lumina_before_header', false !== strpos( $header, "do_action( 'lumina_before_header' )" ) );
check( 'header.php fires lumina_after_header', false !== strpos( $header, "do_action( 'lumina_after_header' )" ) );
check( 'footer.php fires lumina_before_footer', false !== strpos( $footer, "do_action( 'lumina_before_footer' )" ) );
check( 'footer.php fires lumina_after_footer', false !== strpos( $footer, "do_action( 'lumina_after_footer' )" ) );

// 5. Theme composition pipeline consumes lumina_template_data.
check(
	'header composition reads lumina_template_data',
	false !== strpos( $header, "apply_filters( 'lumina_template_data'" )
);

// 6. CSS var coexistence + zero phantom.
$theme_tokens = (string) file_get_contents( $theme_dir . '/assets-src/scss/_tokens.scss' );
$plugin_css   = $plugin->inline_css();
check( 'theme tokens carry --lumina-*', false !== strpos( $theme_tokens, '--lumina-color-bg' ) );
check( 'plugin CSS carries --lumina-*', false !== strpos( $plugin_css, '--lumina-spacing-container' ) );
check( 'no --phantom- in theme tokens', false === strpos( $theme_tokens, '--phantom-' ) );
check( 'no --phantom- in plugin CSS', false === strpos( $plugin_css, '--phantom-' ) );

echo "\n== Results: {$passes} passed, {$fails} failed ==\n";

exit( 0 === $fails ? 0 : 1 );
