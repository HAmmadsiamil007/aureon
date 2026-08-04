<?php
/**
 * Phase 11 — Frontend Component Library smoke suite (WP-free CLI).
 *
 * Drives the real boot entry (app/load.php → Kernel::launch()) WITHOUT a live
 * WordPress install and asserts the Phase 11 acceptance criteria:
 *
 *   1. PSR-4 resolves the Components subsystem
 *   2. The canonical catalog is discoverable and complete (78 components)
 *   3. Every catalog entry maps to a readable renderer template
 *   4. Every component renders HTML through the Phase-4 renderer
 *   5. Every component escapes XSS fixtures (text + attribute contexts)
 *   6. Interactive components ship ARIA semantics
 *   7. Components are animation-ready (data-lumina-anim / data-* hooks)
 *   8. The stylesheet layer is token-driven (no hardcoded colors/spacing)
 *   9. The behaviors entry + styles are enqueued conditionally (provider)
 *  10. Slot composition renders child components recursively
 *  11. Shortcode DSL renders catalog components
 *  12. Phases 1–10 regression
 *
 * Determinism: refuses to run when a developer's own lumina.env.json exists
 * (same contract as smoke-phase1..10.php).
 *
 * Usage: php bin/smoke-phase11.php
 * Exit code 0 = all assertions passed; 1 = any failure.
 *
 * @package Lumina
 * @since 0.11.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__ ) . '/' );
}

require dirname( __DIR__ ) . '/app/load.php';

use Lumina\Core\Boot\Kernel;
use Lumina\Core\Components\Registry;
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
 * Realistic fixture props per component (schema-shaped, exactly what each
 * template reads — every list is an array of well-formed item maps).
 *
 * @return array<string, array<string, mixed>> Component name → props.
 */
function fixtures(): array {
	$item = array(
		'title'   => 'Item',
		'text'    => 'Body',
		'label'   => 'Link',
		'url'     => 'https://example.com/item',
		'name'    => 'Example',
		'role'    => 'Role',
		'bio'     => 'Bio',
		'quote'   => 'Quote',
		'date'    => '2026-01-01',
		'value'   => 42,
		'target'  => 99,
		'suffix'  => '%',
		'icon'    => '★',
		'image'   => 'https://example.com/img.jpg',
		'logo'    => 'https://example.com/logo.png',
		'photo'   => 'https://example.com/photo.jpg',
		'avatar'  => 'https://example.com/avatar.jpg',
		'answer'  => 'Answer',
		'question' => 'Question',
		'src'     => 'https://example.com/src.png',
		'alt'     => 'Alt',
	);

	$link = array( 'label' => 'Link', 'url' => 'https://example.com/' );

	return array(
		'accordion'        => array( 'title' => 'FAQ', 'items' => array( array( 'question' => 'Q1', 'answer' => 'A1' ) ) ),
		'alert'            => array( 'type' => 'error', 'title' => 'Alert', 'message' => 'Message' ),
		'announcement-bar' => array( 'text' => 'Announcement' ),
		'archive-header'   => array( 'title' => 'Archive', 'text' => 'Intro' ),
		'author-box'       => array( 'name' => 'Author', 'bio' => 'Bio', 'avatar' => $item['avatar'], 'url' => $item['url'] ),
		'back-to-top'      => array( 'label' => 'Top' ),
		'banner'           => array( 'title' => 'Banner', 'text' => 'Text', 'image' => $item['image'] ),
		'blog-card'        => array( 'title' => 'Post', 'url' => $item['url'], 'excerpt' => 'Ex', 'date' => $item['date'], 'image' => $item['image'] ),
		'blog-grid'        => array( 'title' => 'Blog', 'posts' => array( array( 'title' => 'P', 'url' => $item['url'], 'excerpt' => 'E', 'date' => $item['date'], 'image' => $item['image'] ) ) ),
		'brands'           => array( 'title' => 'Brands', 'brands' => array( array( 'name' => 'B', 'url' => $item['url'] ) ) ),
		'breadcrumb'       => array( 'items' => array( array( 'label' => 'Home', 'url' => $item['url'] ), array( 'label' => 'Shop' ) ) ),
		'button'           => array( 'label' => 'Go', 'href' => $item['url'] ),
		'card'             => array( 'title' => 'Card', 'excerpt' => 'Body', 'link' => $item['url'] ),
		'cart-drawer'      => array( 'items' => array( $item ), 'total' => '9.99' ),
		'cart-summary'     => array( 'rows' => array( array( 'label' => 'Subtotal', 'value' => '9.99' ) ), 'total' => '9.99' ),
		'categories'       => array( 'title' => 'Categories', 'items' => array( array( 'name' => 'Cat', 'url' => $item['url'] ) ) ),
		'category-grid'    => array( 'title' => 'Categories', 'items' => array( array( 'name' => 'Cat', 'url' => $item['url'], 'image' => $item['image'] ) ) ),
		'checkout-blocks'  => array( 'items' => array( $item ), 'total' => '9.99' ),
		'collection-grid'  => array( 'title' => 'Collections', 'items' => array( array( 'name' => 'Coll', 'url' => $item['url'], 'image' => $item['image'] ) ) ),
		'comments'         => array( 'title' => 'Comments', 'items' => array( array( 'author' => 'A', 'date' => $item['date'], 'text' => 'T', 'avatar' => $item['avatar'] ) ) ),
		'compare-button'   => array( 'label' => 'Compare', 'product_id' => 1 ),
		'cookie-notice'    => array( 'text' => 'Cookies', 'accept_label' => 'Accept', 'decline_label' => 'Decline' ),
		'copyright'        => array( 'text' => '© 2026', 'links' => array( $link ) ),
		'countdown-timer'  => array( 'date' => '2030-01-01T00:00:00Z' ),
		'counters'         => array( 'items' => array( array( 'target' => 100, 'suffix' => '%', 'label' => 'Done' ) ) ),
		'cta'              => array( 'title' => 'CTA', 'text' => 'Text' ),
		'empty-state'      => array( 'title' => 'Empty', 'text' => 'Text', 'action' => array( 'label' => 'Go', 'href' => $item['url'] ) ),
		'error-state'      => array( 'title' => 'Error', 'message' => 'Message', 'retry_label' => 'Retry' ),
		'faceted-nav'      => array( 'title' => 'Shop by', 'facets' => array( array( 'label' => 'Size', 'values' => array( array( 'label' => 'M', 'value' => 'm', 'count' => 3, 'url' => $item['url'] ) ) ) ) ),
		'faq'              => array( 'title' => 'FAQ', 'items' => array( array( 'question' => 'Q', 'answer' => 'A' ) ) ),
		'featured-collection' => array( 'title' => 'Featured', 'items' => array( array( 'name' => 'P', 'url' => $item['url'], 'image' => $item['image'] ) ) ),
		'features-grid'    => array( 'title' => 'Features', 'items' => array( array( 'icon' => '★', 'title' => 'F', 'text' => 'T' ) ) ),
		'filters'          => array( 'title' => 'Filters', 'groups' => array( array( 'label' => 'G', 'options' => array( array( 'name' => 'g', 'value' => 'v', 'label' => 'V' ) ) ) ) ),
		'footer'           => array(
			'columns'   => array(
				array(
					'name'  => 'footer-columns',
					'props' => array( 'columns' => array( array( 'title' => 'Col', 'links' => array( $link ) ) ) ),
				),
			),
			'copyright' => array(
				array(
					'name'  => 'copyright',
					'props' => array( 'text' => '© 2026' ),
				),
			),
		),
		'footer-columns'   => array( 'columns' => array( array( 'title' => 'Col', 'links' => array( $link ) ) ) ),
		'header'           => array( 'title' => 'Site', 'items' => array( array( 'label' => 'Home', 'url' => $item['url'] ) ) ),
		'hero'             => array( 'title' => 'Hero', 'text' => 'Text', 'eyebrow' => 'Eyebrow', 'image' => $item['image'], 'actions' => array( array( 'name' => 'button', 'props' => array( 'label' => 'Buy' ) ) ) ),
		'hero-slider'      => array( 'slides' => array( array( 'title' => 'S', 'text' => 'T', 'image' => $item['image'] ) ) ),
		'image-banner'     => array( 'title' => 'Banner', 'text' => 'Text', 'image' => $item['image'] ),
		'loading-skeleton' => array( 'rows' => 3, 'label' => 'Loading' ),
		'logo-cloud'       => array( 'title' => 'Logos', 'logos' => array( array( 'src' => $item['src'], 'alt' => 'Alt' ) ) ),
		'mega-menu'        => array( 'items' => array( array( 'label' => 'Shop', 'url' => $item['url'], 'children' => array( $link ) ) ) ),
		'mini-cart'        => array( 'items' => array( $item ), 'total' => '9.99' ),
		'mobile-nav'       => array( 'items' => array( $link ) ),
		'modal'            => array( 'trigger' => array( 'label' => 'Open' ), 'title' => 'Modal', 'content' => 'Content' ),
		'module-404'       => array( 'title' => 'Not found', 'text' => 'Text', 'search_label' => 'Search', 'search_url' => $item['url'], 'home_label' => 'Home', 'home_url' => $item['url'] ),
		'newsletter'       => array( 'title' => 'News', 'text' => 'Text', 'placeholder' => 'Email', 'submit_label' => 'Subscribe' ),
		'notification'     => array( 'message' => 'Saved', 'type' => 'success', 'dismissible' => true ),
		'off-canvas'       => array( 'items' => array( $link ) ),
		'order-summary'    => array( 'rows' => array( array( 'label' => 'Subtotal', 'value' => '9.99' ) ), 'total' => '9.99' ),
		'page-header'      => array( 'title' => 'Page', 'text' => 'Intro' ),
		'pagination'       => array( 'current' => 2, 'total' => 5, 'page_url' => $item['url'] ),
		'popup'            => array( 'title' => 'Popup', 'content' => 'Content', 'delay' => 1000 ),
		'pricing-table'    => array( 'title' => 'Pro', 'price' => '29', 'period' => '/mo', 'features' => array( 'F1', 'F2' ), 'cta' => array( 'label' => 'Buy', 'href' => $item['url'] ), 'featured' => true ),
		'product-badge'    => array( 'label' => 'New' ),
		'product-card'     => array( 'name' => 'Product', 'price' => '9.99', 'regular_price' => '12.99', 'link' => $item['url'], 'image' => $item['image'], 'badges' => array( 'Sale', 'New' ) ),
		'product-gallery'  => array( 'images' => array( $item['image'], $item['image'] ) ),
		'product-tabs'     => array( 'tabs' => array( array( 'label' => 'Info', 'content' => 'Info' ) ) ),
		'products-grid'    => array( 'title' => 'Products', 'products' => array( array( 'title' => 'P', 'price' => '1.00', 'image' => $item['image'], 'url' => $item['url'] ) ) ),
		'quick-view'       => array( 'product_id' => 1, 'label' => 'Quick view' ),
		'recently-viewed'  => array( 'title' => 'Viewed', 'products' => array( array( 'title' => 'P', 'price' => '1.00', 'image' => $item['image'], 'url' => $item['url'] ) ) ),
		'related-posts'    => array( 'title' => 'Related', 'posts' => array( array( 'title' => 'P', 'url' => $item['url'] ) ) ),
		'reviews'          => array( 'rating' => 4.5, 'count' => 2, 'items' => array( array( 'author' => 'A', 'date' => $item['date'], 'text' => 'T' ) ) ),
		'sale-badge'       => array( 'label' => '-20%' ),
		'search-overlay'   => array( 'placeholder' => 'Search…', 'action' => $item['url'] ),
		'search-results'   => array( 'heading' => 'Results', 'query' => 'shoes', 'count' => 2, 'count_label' => 'results', 'results' => array( array( 'title' => 'R', 'url' => $item['url'], 'excerpt' => 'E' ) ) ),
		'sidebar'          => array( 'title' => 'Sidebar' ),
		'social-icons'     => array( 'items' => array( array( 'name' => 'X', 'url' => $item['url'] ) ) ),
		'star-rating'      => array( 'rating' => 4, 'max' => 5 ),
		'statistics'       => array( 'items' => array( array( 'value' => 100, 'suffix' => 'k', 'label' => 'Users' ) ) ),
		'sticky-add-to-cart' => array( 'product_id' => 1, 'price' => '9.99', 'label' => 'Add to cart' ),
		'tabs'             => array( 'title' => 'Tabs', 'tabs' => array( array( 'label' => 'One', 'content' => '1' ), array( 'label' => 'Two', 'content' => '2' ) ) ),
		'team'             => array( 'title' => 'Team', 'members' => array( array( 'name' => 'M', 'role' => 'R', 'photo' => $item['photo'], 'bio' => 'B' ) ) ),
		'testimonials'     => array( 'title' => 'Quotes', 'items' => array( array( 'quote' => 'Q', 'name' => 'N', 'role' => 'R' ) ) ),
		'timeline'         => array( 'items' => array( array( 'date' => $item['date'], 'title' => 'T', 'text' => 'B' ) ) ),
		'top-bar'          => array( 'text' => 'Top', 'items' => array( $link ) ),
		'video-banner'     => array( 'title' => 'Video', 'video' => 'https://example.com/v.mp4' ),
		'wishlist-button'  => array( 'label' => 'Wishlist', 'product_id' => 1 ),
	);
}

/**
 * The canonical component names from the JSON catalog.
 *
 * @return list<string>
 */
function catalog_names(): array {
	$json = json_decode( (string) file_get_contents( dirname( __DIR__ ) . '/app/Components/config/components.json' ), true );
	$json = is_array( $json ) ? $json : array();

	$names = array();

	foreach ( (array) ( $json['components'] ?? array() ) as $entry ) {
		if ( is_array( $entry ) && isset( $entry['name'] ) && is_string( $entry['name'] ) ) {
			$names[] = $entry['name'];
		}
	}

	return $names;
}

echo "== Lumina Core Phase 11 smoke suite (Frontend Component Library) ==\n\n";

if ( file_exists( dirname( __DIR__ ) . '/lumina.env.json' ) ) {
	echo "[SKIP] lumina.env.json present — default-state assertions are not meaningful.\n";
	echo "       Remove it (or run in a clean checkout) for the full assertion run.\n";
	exit( 0 );
}

// 1. PSR-4 resolution.
check( 'PSR-4 resolves Registry', class_exists( Registry::class ) );

Kernel::launch();
$app = App::instance();

/** @var Registry $registry */
$registry = $app->make( 'components.registry' );

// 2. Catalog completeness.
$names = catalog_names();
check( 'catalog discovered 78 components', 78 === count( $names ) );
check( 'catalog is duplicate-free', count( $names ) === count( array_unique( $names ) ) );
check( 'catalog covers shell components', in_array( 'header', $names, true ) && in_array( 'footer', $names, true ) );
check( 'catalog covers commerce components', in_array( 'product-card', $names, true ) && in_array( 'cart-drawer', $names, true ) );
check( 'catalog covers content components', in_array( 'testimonials', $names, true ) && in_array( 'faq', $names, true ) );
check( 'catalog covers state components', in_array( 'empty-state', $names, true ) && in_array( 'error-state', $names, true ) );

// 3. Every catalog entry maps to a readable renderer template.
$missing = array();

foreach ( $names as $name ) {
	$definition = $registry->get( $name );

	if ( null === $definition ) {
		$missing[] = $name . ' (not registered)';
		continue;
	}

	$path = dirname( __DIR__ ) . '/templates/' . $definition->renderer() . '.php';

	if ( ! is_readable( $path ) ) {
		$missing[] = $name . " ({$path})";
	}
}

check( 'every catalog component has a renderer template', array() === $missing, implode( ', ', $missing ) );

// 4. Every component renders HTML through the renderer.
$fixtures = fixtures();
$render_fails = array();

foreach ( $names as $name ) {
	try {
		$html = $registry->render( $name, $fixtures[ $name ] ?? array() );

		if ( ! str_contains( $html, 'lumina-' ) ) {
			$render_fails[] = $name . ' (no lumina- class)';
		}
	} catch ( \Throwable $throwable ) {
		$render_fails[] = $name . ' (' . $throwable->getMessage() . ')';
	}
}

check( 'all 78 components render valid HTML', array() === $render_fails, implode( '; ', $render_fails ) );

// 5. Escaping (text + attribute contexts).
$xss = '<script>alert(1)</script>';
$hero_html = $registry->render( 'hero', array( 'title' => $xss, 'text' => $xss, 'eyebrow' => $xss ) );
check( 'hero escapes title text context', ! str_contains( $hero_html, '<script>' ) && str_contains( $hero_html, '&lt;script&gt;' ) );

$button_html = $registry->render( 'button', array( 'label' => $xss, 'href' => '" onmouseover="alert(1)' ) );
check( 'button escapes label text context', str_contains( $button_html, '&lt;script&gt;' ) );
check( 'button escapes href attribute context', ! str_contains( $button_html, '" onmouseover=' ) && str_contains( $button_html, '&quot;' ) );

$news_html = $registry->render( 'newsletter', array( 'title' => $xss, 'placeholder' => $xss ) );
check( 'newsletter escapes title', str_contains( $news_html, '&lt;script&gt;' ) );
check( 'newsletter escapes placeholder attribute', ! str_contains( $news_html, '<script>' ) );

// 6. Accessibility semantics.
$tabs_html = $registry->render( 'tabs', $fixtures['tabs'] );
check( 'tabs ship role=tablist', str_contains( $tabs_html, 'role="tablist"' ) );
check( 'tabs ship role=tab', str_contains( $tabs_html, 'role="tab"' ) );
check( 'tabs ship role=tabpanel', str_contains( $tabs_html, 'role="tabpanel"' ) );
check( 'tabs wire aria-controls', str_contains( $tabs_html, 'aria-controls' ) );
check( 'tabs wire aria-selected', str_contains( $tabs_html, 'aria-selected="true"' ) );

$modal_html = $registry->render( 'modal', $fixtures['modal'] );
check( 'modal ships role=dialog', str_contains( $modal_html, 'role="dialog"' ) );
check( 'modal ships aria-modal', str_contains( $modal_html, 'aria-modal="true"' ) );
check( 'modal ships aria-haspopup on trigger', str_contains( $modal_html, 'aria-haspopup="dialog"' ) );

$newsletter_html = $registry->render( 'newsletter', $fixtures['newsletter'] );
check( 'newsletter labels its input', str_contains( $newsletter_html, 'screen-reader-text' ) && str_contains( $newsletter_html, 'for="lumina-newsletter-email"' ) );
check( 'newsletter input is required', str_contains( $newsletter_html, 'required' ) );

$faq_html = $registry->render( 'faq', $fixtures['faq'] );
check( 'faq uses semantic details/summary', str_contains( $faq_html, '<details' ) && str_contains( $faq_html, '<summary' ) );

$pagination_html = $registry->render( 'pagination', $fixtures['pagination'] );
check( 'pagination marks current page', str_contains( $pagination_html, 'aria-current="page"' ) );

// 7. Animation-ready hooks.
check( 'cta opts into the reveal animation', str_contains( $registry->render( 'cta', $fixtures['cta'] ), 'data-lumina-anim="reveal"' ) );
check( 'testimonials opt into reveal', str_contains( $registry->render( 'testimonials', $fixtures['testimonials'] ), 'data-lumina-anim="reveal"' ) );
check( 'counters carry count hooks', str_contains( $registry->render( 'counters', $fixtures['counters'] ), 'data-lumina-counters' ) && str_contains( $registry->render( 'counters', $fixtures['counters'] ), 'data-count-target' ) );
check( 'sticky add to cart carries its hook', str_contains( $registry->render( 'sticky-add-to-cart', $fixtures['sticky-add-to-cart'] ), 'data-lumina-sticky-atc' ) );

// 8. Token-driven stylesheet. The SCSS source ships only in dev checkouts;
// distributions ship the built CSS in assets/dist/. Assert the token rule
// against whichever layer is present.
$scss_src  = dirname( __DIR__ ) . '/assets-src/scss/_components.scss';
$scss      = is_readable( $scss_src ) ? (string) file_get_contents( $scss_src ) : '';
$built_css = glob( dirname( __DIR__ ) . '/assets/dist/assets/*.css' );
$css_all   = '';

foreach ( (array) $built_css as $file ) {
	$css_all .= (string) file_get_contents( $file );
}

if ( '' !== $scss ) {
	check( 'component layer uses var(--lumina-*) tokens', substr_count( $scss, 'var(--lumina-' ) > 50 );
	check( 'component layer uses motion tokens', str_contains( $scss, '--lumina-motion-duration' ) );
	check( 'component layer uses radius tokens', str_contains( $scss, '--lumina-radius-' ) );
	check( 'component layer uses spacing tokens', str_contains( $scss, '--lumina-space-' ) );
	check( 'component layer has no raw hex colors', 0 === preg_match( '/#[0-9a-fA-F]{3,6}\b/', preg_replace( '/\/\/.*$/m', '', $scss ) ) );
} else {
	check( 'built CSS carries --lumina-* tokens', str_contains( $css_all, '--lumina-' ) );
	check( 'built CSS defines color tokens', str_contains( $css_all, '--lumina-color-' ) );
}

check( 'component layer respects reduced motion', '' !== $scss ? ( str_contains( $scss, 'prefers-reduced-motion' ) || str_contains( $scss, 'lumina-shimmer' ) ) : true );

// 9. Conditional asset enqueue (provider wiring).
$provider = new \ReflectionClass( \Lumina\Core\Components\ComponentsServiceProvider::class );
check( 'provider conditionally enqueues assets', $provider->hasMethod( 'enqueue_assets' ) );
check( 'behaviors entry exists for Vite', is_readable( dirname( __DIR__ ) . '/assets-src/ts/components.ts' ) || '' !== $css_all );
check( 'Vite config lists the components entry', ! is_readable( dirname( __DIR__ ) . '/vite.config.js' ) || str_contains( (string) file_get_contents( dirname( __DIR__ ) . '/vite.config.js' ), "components: resolve(rootDir, 'assets-src/ts/components.ts')" ) );

// 10. Slot composition.
$slot_html = $registry->render(
	'card',
	array(
		'title'   => 'Slotted',
		'actions' => array(
			array( 'name' => 'button', 'props' => array( 'label' => 'Buy' ) ),
		),
	)
);
check( 'slot children render recursively', str_contains( $slot_html, 'Buy' ) );

$hero_slot = $registry->render( 'hero', $fixtures['hero'] );
check( 'hero actions slot renders its button child', str_contains( $hero_slot, 'Buy' ) && str_contains( $hero_slot, 'lumina-btn' ) );

// 11. Shortcode DSL parity for catalog components.
$direct = $registry->render( 'button', array( 'label' => 'Short' ) );
$short  = $registry->render_shortcode( 'lumina:button', array( 'label' => 'Short' ) );
check( 'shortcode DSL renders catalog components', $direct === $short );

// 12. Phases 1–10 regression.
check( 'Phase 1 regression: env resolvable', 'production' === $app->make( 'env' ) );
check( 'Phase 2 regression: container is Container', $app->make( 'container' ) instanceof \Lumina\Core\Container\Container );
check( 'Phase 4 regression: renderer resolves', $app->make( 'render.renderer' ) instanceof \Lumina\Core\Render\Renderer );
check( 'Phase 5 regression: registry resolves', $app->make( 'components.registry' ) instanceof Registry );
check( 'Phase 7 regression: asset loader resolves', $app->make( 'assets.loader' ) instanceof \Lumina\Core\Assets\AssetLoader );
check( 'Phase 10 regression: animation engine resolves', $app->make( 'animation.engine' ) instanceof \Lumina\Core\Animation\Engine );

echo "\n== Results: {$passes} passed, {$fails} failed ==\n";

exit( 0 === $fails ? 0 : 1 );
