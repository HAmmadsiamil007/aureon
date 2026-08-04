<?php
/**
 * Phase 4 — Render Engine smoke suite (WP-free CLI).
 *
 * Drives the real boot entry (app/load.php → Kernel::launch()) WITHOUT a live
 * WordPress install and asserts the Phase 4 acceptance criteria:
 *
 *   1. PSR-4 resolves the Render subsystem classes
 *   2. App::make('render.renderer') resolves via the container
 *   3. Renderer::render('card', …) produces valid HTML
 *   4. Escaping is applied end-to-end (XSS fixture survives escaped)
 *   5. 'card.twig' view slugs normalize to the native PHP template
 *   6. TemplateResolver priority: override → base → wp-{name} → null
 *   7. Unknown views throw RenderException; unresolvable → graceful null
 *   8. Layout push/flush ordering + callable blocks
 *   9. ViewModel get/has/with immutability; ViewContext escaping helpers
 *  10. RenderCache hit/miss + renderer cache round-trip (in-memory store)
 *  11. Data adapters: Post, Term, User, Site, Settings, Tax, WpQuery, Menu
 *  12. Phases 1–3 regression: boot, container, config, tokens
 *
 * Determinism: refuses to run when a developer's own lumina.env.json exists
 * (same contract as smoke-phase1/2/3.php).
 *
 * Usage: php bin/smoke-phase4.php
 * Exit code 0 = all assertions passed; 1 = any failure.
 *
 * @package Lumina
 * @since 0.4.0
 */

declare( strict_types=1 );

// Simulate a WordPress bootstrap boundary so app/load.php's guard passes.
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__ ) . '/' );
}

require dirname( __DIR__ ) . '/app/load.php';

use Lumina\Core\Boot\Kernel;
use Lumina\Core\Cache\CacheInterface;
use Lumina\Core\Container\Container;
use Lumina\Core\Core\App;
use Lumina\Core\Data\MenuAdapter;
use Lumina\Core\Data\PostAdapter;
use Lumina\Core\Data\SettingsAdapter;
use Lumina\Core\Data\SiteAdapter;
use Lumina\Core\Data\TaxAdapter;
use Lumina\Core\Data\TermAdapter;
use Lumina\Core\Data\UserAdapter;
use Lumina\Core\Data\WpQueryAdapter;
use Lumina\Core\Render\Layout;
use Lumina\Core\Render\PhpTemplateEngine;
use Lumina\Core\Render\RenderCache;
use Lumina\Core\Render\RenderException;
use Lumina\Core\Render\Renderer;
use Lumina\Core\Render\TemplateResolver;
use Lumina\Core\Render\ViewModel;
use Lumina\Core\Render\ViewContext;

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
 * In-memory cache store implementing CacheInterface for the render-cache tests.
 */
class InMemoryCache implements CacheInterface {

	/**
	 * Stored entries.
	 *
	 * @var array<string, mixed>
	 */
	public array $store = array();

	/**
	 * Total get() calls.
	 *
	 * @var int
	 */
	public int $gets = 0;

	/**
	 * Successful get() hits.
	 *
	 * @var int
	 */
	public int $hits = 0;

	/**
	 * {@inheritDoc}
	 */
	public function get( string $key, mixed $fallback = null ): mixed {
		++$this->gets;

		if ( array_key_exists( $key, $this->store ) ) {
			++$this->hits;

			return $this->store[ $key ];
		}

		return $fallback;
	}

	/**
	 * {@inheritDoc}
	 */
	public function set( string $key, mixed $value, int $ttl = 0 ): bool {
		$this->store[ $key ] = $value;

		return true;
	}

	/**
	 * {@inheritDoc}
	 */
	public function delete( string $key ): bool {
		unset( $this->store[ $key ] );

		return true;
	}

	/**
	 * {@inheritDoc}
	 */
	public function flush(): bool {
		$this->store = array();

		return true;
	}
}

echo "== Lumina Core Phase 4 smoke suite (Render Engine) ==\n\n";

if ( file_exists( dirname( __DIR__ ) . '/lumina.env.json' ) ) {
	echo "[SKIP] lumina.env.json present — default-state assertions are not meaningful.\n";
	echo "       Remove it (or run in a clean checkout) for the full assertion run.\n";
	exit( 0 );
}

$templates_dir = dirname( __DIR__ ) . '/templates';

// 1. PSR-4 resolution.
check( 'PSR-4 resolves Renderer', class_exists( Renderer::class ) );
check( 'PSR-4 resolves Layout', class_exists( Layout::class ) );
check( 'PSR-4 resolves TemplateResolver', class_exists( TemplateResolver::class ) );
check( 'PSR-4 resolves PostAdapter', class_exists( PostAdapter::class ) );
check( 'PSR-4 resolves WpQueryAdapter', class_exists( WpQueryAdapter::class ) );

Kernel::launch();
$app = App::instance();

// 2. Container wiring.
check( 'App::make("render.renderer") resolves', $app->make( 'render.renderer' ) instanceof Renderer );
check( 'App::make("render.layout") resolves', $app->make( 'render.layout' ) instanceof Layout );
check( 'App::make("render.resolver") resolves', $app->make( 'render.resolver' ) instanceof TemplateResolver );
check( 'App::make("render.cache") resolves', $app->make( 'render.cache' ) instanceof RenderCache );

// 3 + 4. Render + escaping end-to-end.
$renderer   = $app->make( 'render.renderer' );
$card_html  = $renderer->render( 'card', array( 'title' => 'Hello Lumina', 'link' => 'https://example.com/p', 'excerpt' => 'A card.' ) );
check( 'render("card") produces HTML', str_contains( $card_html, 'lumina-card' ) && str_contains( $card_html, 'Hello Lumina' ) );
check( 'rendered card escapes the link attribute', str_contains( $card_html, 'href="https://example.com/p"' ) );

$xss_html = $renderer->render( 'card', array( 'title' => '<script>alert(1)</script>' ) );
check( 'XSS script tag is escaped in output', ! str_contains( $xss_html, '<script>alert(1)</script>' ) );
check( 'XSS script tag appears entity-encoded', str_contains( $xss_html, '&lt;script&gt;' ) );

// 5. .twig slug normalization.
$twig_html = $renderer->render( 'card.twig', array( 'title' => 'Twig Slug' ) );
check( "'card.twig' renders via the native engine", str_contains( $twig_html, 'Twig Slug' ) );

// 6. TemplateResolver priority + normalization.
$resolver = new TemplateResolver( $templates_dir );
$resolved = $resolver->resolve( 'card' );
check( 'resolve("card") → base tier', is_string( $resolved ) && str_ends_with( $resolved, 'card.php' ), (string) $resolved );
check( 'resolve("card.twig") normalizes to card.php', str_ends_with( (string) $resolver->resolve( 'card.twig' ), 'card.php' ) );

$candidates = $resolver->candidates( 'card', array( 'override' => 'theme-x' ) );
check( 'override tier is most specific', str_ends_with( $candidates[0], 'theme-x/card.php' ) );
check( 'base tier follows override', str_ends_with( $candidates[1], 'card.php' ) );
check( 'wp-{name} tier exists', str_ends_with( $candidates[2], 'wp-card.php' ) );

$resolved_override = ( new TemplateResolver( $templates_dir ) )->resolve( 'card', array( 'override' => 'does-not-exist' ) );
check( 'missing override falls back to base tier', is_string( $resolved_override ) && str_ends_with( $resolved_override, 'card.php' ) );
check( 'unknown view resolves to null', null === $resolver->resolve( 'nope.never' ) );

// 7. RenderException on unresolvable view.
$render_exception = false;
try {
	$renderer->render( 'nope.never' );
} catch ( RenderException $e ) {
	$render_exception = true;
}
check( 'render("nope.never") throws RenderException', $render_exception );

$missing_file_exception = false;
try {
	( new PhpTemplateEngine() )->render( $templates_dir . '/does-not-exist.php', new ViewModel() );
} catch ( RenderException $e ) {
	$missing_file_exception = true;
}
check( 'engine throws on unreadable template', $missing_file_exception );

// 8. Layout composition.
$layout = new Layout(
	static fn( string $view, array $args = array() ): string => $view . ':' . json_encode( $args )
);
$layout->push( 'main', 'card', array( 'id' => 1 ) );
$layout->push( 'main', static fn( array $args ): string => '<x>' . (string) ( $args['k'] ?? '' ) . '</x>', array( 'k' => 'v' ) );
check( 'layout has region after push', $layout->has( 'main' ) );
check( 'layout flush renders in insertion order', 'card:{"id":1}<x>v</x>' === $layout->flush( 'main' ) );
check( 'layout region cleared after flush', ! $layout->has( 'main' ) );

$layout->push( 'loop', 'card', array() );
check( 'render_region keeps the buffer', str_contains( $layout->render_region( 'loop' ), 'card' ) && $layout->has( 'loop' ) );

// 9. ViewModel + ViewContext.
$vm = new ViewModel( array( 'a' => 1, 'nested' => array( 'b' => 2 ) ) );
check( 'viewmodel get() reads keys', 1 === $vm->get( 'a' ) );
check( 'viewmodel dot-notation read', 2 === $vm->get( 'nested.b' ) );
check( 'viewmodel get() default fallback', 'd' === $vm->get( 'missing', 'd' ) );
check( 'viewmodel has()', $vm->has( 'a' ) && ! $vm->has( 'missing' ) );
$vm2 = $vm->with( 'a', 99 );
check( 'viewmodel with() returns new instance', 99 === $vm2->get( 'a' ) && 1 === $vm->get( 'a' ) );

$ctx = new ViewContext( new ViewModel( array( 'raw' => '<b>&"x"' ) ) );
check( 'viewcontext e() escapes text', '&lt;b&gt;&amp;&quot;x&quot;' === $ctx->e( '<b>&"x"' ) );
check( 'viewcontext attr() escapes quotes', str_contains( $ctx->attr( 'a"b' ), '&quot;' ) );
check( 'viewcontext url() escapes ampersands', 'https://x.test/?a=1&amp;b=2' === $ctx->url( 'https://x.test/?a=1&b=2' ) );
check( 'viewcontext html() falls back to escaping (WP-free)', '&lt;em&gt;x&lt;/em&gt;' === $ctx->html( '<em>x</em>' ) );

// 10. RenderCache + renderer cache round-trip.
$store    = new InMemoryCache();
$cache    = new RenderCache( $store );
$renderer = new Renderer( new PhpTemplateEngine(), new TemplateResolver( $templates_dir ), $cache );

check( 'render cache enabled with a store (WP-free)', $cache->enabled() );
check( 'cache miss on first read', null === $cache->get( 'card', array( 'title' => 'x' ) ) );

$cached_a = $renderer->render( 'card', array( 'title' => 'Cached' ) );
check( 'cached render produces output', str_contains( $cached_a, 'Cached' ) );
$hits_after_first = $store->hits;

$cached_b = $renderer->render( 'card', array( 'title' => 'Cached' ) );
check( 'second identical render hits the cache', $store->hits === $hits_after_first + 1 );
check( 'cached render output is identical', $cached_a === $cached_b );

$cached_c = $renderer->render( 'card', array( 'title' => 'Different' ) );
check( 'different data produces a distinct cache entry', str_contains( $cached_c, 'Different' ) && str_contains( $cached_c, 'Cached' ) !== true && $store->hits === $hits_after_first + 1 );

$no_cache = new Renderer( new PhpTemplateEngine(), new TemplateResolver( $templates_dir ) );
check( 'renderer works with no cache attached', str_contains( $no_cache->render( 'card', array( 'title' => 'Nc' ) ), 'Nc' ) );

// 11. Data adapters.
$post_adapter = new PostAdapter();
$post_vm      = $post_adapter->adapt( array( 'ID' => 7, 'post_title' => 'Hello', 'post_type' => 'post' ) );
check( 'PostAdapter normalizes arrays', 7 === $post_vm->get( 'id' ) && 'Hello' === $post_vm->get( 'title' ) );
check( 'PostAdapter normalizes stdClass', 'World' === $post_adapter->adapt( (object) array( 'ID' => 8, 'post_title' => 'World' ) )->get( 'title' ) );
check( 'PostAdapter supports int sources', $post_adapter->supports( 5 ) );

$term_vm = ( new TermAdapter() )->adapt( array( 'term_id' => 3, 'name' => 'News', 'slug' => 'news' ) );
check( 'TermAdapter normalizes terms', 3 === $term_vm->get( 'id' ) && 'News' === $term_vm->get( 'name' ) );

$user_vm = ( new UserAdapter() )->adapt( array( 'ID' => 1, 'display_name' => 'Admin', 'roles' => array( 'administrator' ) ) );
check( 'UserAdapter normalizes users', 'Admin' === $user_vm->get( 'name' ) && array( 'administrator' ) === $user_vm->get( 'roles' ) );

$site_vm = ( new SiteAdapter() )->adapt( array( 'name' => 'Test Site', 'url' => 'https://t.test' ) );
check( 'SiteAdapter merges overrides', 'Test Site' === $site_vm->get( 'name' ) && 'https://t.test' === $site_vm->get( 'url' ) );
check( 'SiteAdapter WP-free defaults', 'Lumina' === ( new SiteAdapter() )->adapt( null )->get( 'name' ) );

$settings_vm = ( new SettingsAdapter() )->adapt( array( 'blogname' => 'T' ) );
check( 'SettingsAdapter maps allow-listed keys', 'T' === $settings_vm->get( 'site_name' ) );
check( 'SettingsAdapter unknown keys empty', '' === $settings_vm->get( 'date_format' ) );

$tax_vm = ( new TaxAdapter() )->adapt( array( 'terms' => array( array( 'term_id' => 1, 'name' => 'A' ) ) ), array( 'taxonomy' => 'category' ) );
check( 'TaxAdapter normalizes term lists', 'category' === $tax_vm->get( 'taxonomy' ) && 1 === count( $tax_vm->get( 'terms', array() ) ) );

$query_vm = ( new WpQueryAdapter() )->adapt(
	array(
		'posts'         => array(
			array( 'ID' => 1, 'post_title' => 'P1' ),
			array( 'ID' => 2, 'post_title' => 'P2' ),
		),
		'max_num_pages' => 2,
	)
);
$query_posts = $query_vm->get( 'posts', array() );
check( 'WpQueryAdapter normalizes loops', 2 === $query_vm->get( 'post_count' ) && 'P1' === $query_posts[0]['title'] );
check( 'WpQueryAdapter exposes pagination', 2 === $query_vm->get( 'max_num_pages' ) );

$menu_vm = ( new MenuAdapter() )->adapt( array( array( 'id' => 1, 'title' => 'Home', 'url' => '/' ) ) );
$menu_items = $menu_vm->get( 'items', array() );
check( 'MenuAdapter normalizes menus', 1 === count( $menu_items ) && 'Home' === $menu_items[0]['title'] );

// 12. Phases 1–3 regression.
check( 'Phase 1 regression: env resolvable', 'production' === $app->make( 'env' ), (string) $app->make( 'env' ) );
check( 'Phase 2 regression: config repository resolvable', $app->make( 'config' ) instanceof \Lumina\Core\Config\Repository );
check( 'Phase 2 regression: container is Container', $app->make( 'container' ) instanceof Container );
check( 'Phase 3 regression: tokens repository resolves', $app->make( 'tokens.repository' ) instanceof \Lumina\Core\Tokens\TokenRepository );
check( 'Phase 3 regression: space.4 is 0.25rem', '0.25rem' === $app->make( 'tokens.repository' )->token( 'space.4' ) );

echo "\n== Results: {$passes} passed, {$fails} failed ==\n";

exit( 0 === $fails ? 0 : 1 );
