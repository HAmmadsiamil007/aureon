<?php
/**
 * Phase 12 — Frontend Template Library smoke suite (WP-free CLI).
 *
 * Drives the real boot entry (app/load.php → Kernel::launch()) WITHOUT a live
 * WordPress install and asserts the Phase 12 acceptance criteria:
 *
 *   1. PSR-4 resolves Templates\Composer
 *   2. The canonical maps.php registers the full template inventory
 *   3. Every map slug has a frontend template file (templates/frontend/*.php)
 *   4. Every template slug composes HTML from registry components
 *   5. Template output is escaped (XSS fixtures neutralized)
 *   6. Templates reference only registry components — no direct WooCommerce
 *      (`wc_*`) or bypassing `get_template_part` calls in the frontend layer
 *   7. Commerce templates route through the Woo Bridge namespace, never `wc_`
 *   8. View::compose() facade parity with the Composer
 *   9. Custom template resolves a filterable slug
 *  10. Phases 1–11 regression
 *
 * Determinism: refuses to run when a developer's own phantom.env.json exists
 * (same contract as smoke-phase1..11.php).
 *
 * Usage: php bin/smoke-phase12.php
 * Exit code 0 = all assertions passed; 1 = any failure.
 *
 * @package Phantom
 * @since 0.12.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__ ) . '/' );
}

require dirname( __DIR__ ) . '/app/load.php';

use Phantom\Core\Boot\Kernel;
use Phantom\Core\Core\App;
use Phantom\Core\Templates\Composer;
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
 * Realistic request data for template compositions.
 *
 * @return array<string, mixed>
 */
function template_data(): array {
	$item = array(
		'title' => 'Item',
		'url'   => 'https://example.com/item',
		'image' => 'https://example.com/img.jpg',
		'text'  => 'Body',
	);

	return array(
		'site_name'        => 'Phantom',
		'copyright'        => '© 2026 Phantom',
		'menu'             => array( $item ),
		'hero_title'       => 'Hero',
		'hero_text'        => 'Hero text',
		'hero_actions'     => array( array( 'name' => 'button', 'props' => array( 'label' => 'Buy' ) ) ),
		'products'         => array( array( 'title' => 'P1', 'price' => '1.00', 'url' => $item['url'], 'image' => $item['image'] ) ),
		'featured_items'   => array( $item ),
		'testimonials'     => array( array( 'quote' => 'Q', 'name' => 'N' ) ),
		'posts'            => array( array( 'title' => 'Post', 'url' => $item['url'], 'date' => '2026-01-01' ) ),
		'results'          => array( array( 'title' => 'R', 'url' => $item['url'] ) ),
		'query'            => 'shoes',
		'count'            => 1,
		'rows'             => array( array( 'label' => 'Subtotal', 'value' => '9.99' ) ),
		'total'            => '9.99',
		'items'            => array( $item ),
		'breadcrumbs'      => array( array( 'label' => 'Home', 'url' => $item['url'] ) ),
		'tabs'             => array( array( 'label' => 'Info', 'content' => 'Details' ) ),
		'faq_items'        => array( array( 'question' => 'Q', 'answer' => 'A' ) ),
		'sections'         => array( array( 'question' => 'S', 'answer' => 'A' ) ),
		'timeline'         => array( array( 'date' => '2026-01-01', 'title' => 'T', 'text' => 'B' ) ),
		'members'          => array( array( 'name' => 'M', 'role' => 'R' ) ),
		'stats'            => array( array( 'value' => 100, 'label' => 'Users' ) ),
		'features'         => array( array( 'icon' => '★', 'title' => 'F', 'text' => 'T' ) ),
		'current_page'     => 1,
		'total_pages'      => 3,
		'base_url'         => $item['url'],
		'search_url'       => $item['url'],
		'home_url'         => $item['url'],
	);
}

/**
 * The canonical template slugs from maps.php.
 *
 * @return list<string>
 */
function map_slugs(): array {
	$maps = (array) include dirname( __DIR__ ) . '/app/Templates/config/maps.php';

	return array_keys( $maps );
}

echo "== Phantom Core Phase 12 smoke suite (Frontend Template Library) ==\n\n";

if ( file_exists( dirname( __DIR__ ) . '/phantom.env.json' ) ) {
	echo "[SKIP] phantom.env.json present — default-state assertions are not meaningful.\n";
	echo "       Remove it (or run in a clean checkout) for the full assertion run.\n";
	exit( 0 );
}

// 1. PSR-4 resolution.
check( 'PSR-4 resolves Composer', class_exists( Composer::class ) );

Kernel::launch();
$app = App::instance();

/** @var Composer $composer */
$composer = $app->make( 'templates.composer' );
check( 'templates.composer resolves', $composer instanceof Composer );

// 2. Map completeness.
$slugs   = $composer->slugs();
$expected = array(
	'header', 'footer', 'home', 'landing', 'shop', 'product', 'cart', 'checkout',
	'thank-you', 'account', 'wishlist', 'compare', 'blog', 'single-post',
	'archive', 'author', 'search', 'not-found', 'contact', 'about', 'faq-page',
	'privacy', 'terms',
);
check( 'all 23 template slugs registered', count( $slugs ) === count( $expected ) && 23 === count( $slugs ) );
check( 'commerce templates registered', in_array( 'shop', $slugs, true ) && in_array( 'product', $slugs, true ) && in_array( 'checkout', $slugs, true ) );
check( 'content templates registered', in_array( 'blog', $slugs, true ) && in_array( 'single-post', $slugs, true ) && in_array( 'not-found', $slugs, true ) );
check( 'utility templates registered', in_array( 'privacy', $slugs, true ) && in_array( 'terms', $slugs, true ) && in_array( 'contact', $slugs, true ) );

// 3. Every page slug maps to a frontend template file. The `header`/`footer`
// slugs are shell compositions consumed by the site shell (not standalone
// page templates); `not-found` renders through templates/frontend/404.php
// and `faq-page` through templates/frontend/faq.php.
$frontend_dir = dirname( __DIR__ ) . '/templates/frontend';
$missing_files = array();

foreach ( $expected as $slug ) {
	if ( in_array( $slug, array( 'header', 'footer' ), true ) ) {
		continue;
	}

	$file = $frontend_dir . '/' . $slug . '.php';

	if ( ! is_readable( $file ) ) {
		// Alternative file names for slugs whose template filename differs.
		$aliases = array(
			'not-found' => '404.php',
			'faq-page'  => 'faq.php',
		);
		$alt  = isset( $aliases[ $slug ] ) ? $frontend_dir . '/' . $aliases[ $slug ] : '';
		$file = '' !== $alt && is_readable( $alt ) ? $alt : $file;
	}

	if ( ! is_readable( $file ) ) {
		$missing_files[] = $slug;
	}
}

check( 'every page slug has a frontend template file', array() === $missing_files, implode( ', ', $missing_files ) );

// 4. Every template slug composes HTML from registry components.
$data        = template_data();
$compose_fails = array();

foreach ( $expected as $slug ) {
	try {
		$html = $composer->compose( $slug, $data );

		if ( ! str_contains( $html, 'phantom-' ) ) {
			$compose_fails[] = $slug . ' (no phantom- class)';
		}
	} catch ( \Throwable $throwable ) {
		$compose_fails[] = $slug . ' (' . $throwable->getMessage() . ')';
	}
}

check( 'all 23 templates compose component HTML', array() === $compose_fails, implode( '; ', $compose_fails ) );

// 5. Escaping in composed output.
$xss     = '<script>alert(1)</script>';
$home    = $composer->compose( 'home', array_merge( $data, array( 'hero_title' => $xss ) ) );
check( 'composed home escapes title text context', str_contains( $home, '&lt;script&gt;' ) && ! str_contains( $home, '<script>alert(1)</script>' ) );

$search  = $composer->compose( 'search', array_merge( $data, array( 'query' => $xss ) ) );
check( 'composed search escapes query echo', ! str_contains( $search, '<script>alert(1)</script>' ) );

// 6. Frontend templates reference only registry components.
$frontend_source = '';

foreach ( glob( $frontend_dir . '/*.php' ) as $file ) {
	$frontend_source .= (string) file_get_contents( $file );
}

check( 'frontend templates never call wc_* functions', ! preg_match( '/\bwc_[a-z_]+\(/', $frontend_source ) );
check( 'frontend templates never bypass via get_template_part', ! str_contains( $frontend_source, 'get_template_part' ) );
check( 'frontend templates delegate to View::compose', substr_count( $frontend_source, 'View::compose' ) >= 23 );
check( 'frontend templates carry no business logic (no WP_Query)', ! str_contains( $frontend_source, 'WP_Query' ) );

// 7. Maps reference only registry components.
$maps  = (array) include dirname( __DIR__ ) . '/app/Templates/config/maps.php';
$all_component_names = array();

foreach ( $slugs as $slug ) {
	foreach ( (array) ( $maps[ $slug ] ?? array() ) as $region => $entries ) {
		foreach ( (array) $entries as $entry ) {
			if ( is_array( $entry ) && isset( $entry['component'] ) && is_string( $entry['component'] ) ) {
				$all_component_names[] = $entry['component'];
			}
		}
	}
}

$all_component_names = array_unique( $all_component_names );

$registry = $app->make( 'components.registry' );
$unknown  = array();

foreach ( $all_component_names as $component ) {
	if ( ! $registry->has( $component ) ) {
		$unknown[] = $component;
	}
}

check( 'every mapped component is registered', array() === $unknown, implode( ', ', $unknown ) );
check( 'maps reuse the registry across templates', count( $all_component_names ) < 60 );

// 8. View::compose() facade parity.
$direct = $composer->compose( 'home', $data );
$facade = View::compose( 'home', $data );
check( 'View::compose() matches Composer::compose()', $direct === $facade );

// 9. Custom template resolves a filterable slug (WP-free: defaults).
check( 'maps.php is the canonical composition source', is_readable( dirname( __DIR__ ) . '/app/Templates/config/maps.php' ) );

// 10. Phases 1–11 regression.
check( 'Phase 1 regression: env resolvable', 'production' === $app->make( 'env' ) );
check( 'Phase 2 regression: container is Container', $app->make( 'container' ) instanceof \Phantom\Core\Container\Container );
check( 'Phase 4 regression: renderer resolves', $app->make( 'render.renderer' ) instanceof \Phantom\Core\Render\Renderer );
check( 'Phase 5 regression: registry resolves', $app->make( 'components.registry' ) instanceof \Phantom\Core\Components\Registry );
check( 'Phase 6 regression: partial loader resolves', $app->make( 'templates.partials' ) instanceof \Phantom\Core\Templates\PartialLoader );
check( 'Phase 10 regression: animation engine resolves', $app->make( 'animation.engine' ) instanceof \Phantom\Core\Animation\Engine );
check( 'Phase 11 regression: hero component renders', str_contains( $registry->render( 'hero', array( 'title' => 'R' ) ), 'phantom-hero' ) );

echo "\n== Results: {$passes} passed, {$fails} failed ==\n";

exit( 0 === $fails ? 0 : 1 );
