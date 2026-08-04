<?php
/**
 * Phase 7 — Asset Pipeline smoke suite (WP-free CLI).
 *
 * Drives the real boot entry (app/load.php → Kernel::launch()) WITHOUT a live
 * WordPress install and asserts the Phase 7 acceptance criteria:
 *
 *   1. PSR-4 resolves the Assets subsystem classes
 *   2. Container wiring (manifest, dev server, loader, entries, deps)
 *   3. ManifestReader parses a Vite manifest fixture (file/css/imports/isEntry)
 *   4. DevServer: env-driven port/host/active; URL building
 *   5. AssetLoader: hashed prod URL via manifest; raw fallback; dev URL
 *   6. BuildFingerprint: stable per build, 'dev' without a build
 *   7. Image::build_srcset pure builder; srcset() WP-free → empty map
 *   8. Markup: defer_all + preload_critical_css (escaped)
 *   9. Pipeline: Entries (isEntry only) + DepsResolver transitive closure
 *  10. Phases 1–6 regression
 *
 * The manifest fixture is built in a temp dir; the real dist build is
 * verified separately by `npm run build` + `php bin/build-tokens.php`.
 *
 * Usage: php bin/smoke-phase7.php
 * Exit code 0 = all assertions passed; 1 = any failure.
 *
 * @package Phantom
 * @since 0.7.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__ ) . '/' );
}

require dirname( __DIR__ ) . '/app/load.php';

use Phantom\Core\Assets\AssetLoader;
use Phantom\Core\Assets\AssetsServiceProvider;
use Phantom\Core\Assets\BuildFingerprint;
use Phantom\Core\Assets\DevServer;
use Phantom\Core\Assets\Image;
use Phantom\Core\Assets\ManifestReader;
use Phantom\Core\Assets\Markup;
use Phantom\Core\Assets\Pipeline\DepsResolver;
use Phantom\Core\Assets\Pipeline\Entries;
use Phantom\Core\Boot\Kernel;
use Phantom\Core\Core\App;

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
 * Write a Vite-format manifest fixture and return its absolute path.
 *
 * @return string Manifest path.
 */
function make_manifest_fixture(): string {
	$dir  = sys_get_temp_dir() . '/phantom-manifest-' . getmypid();
	@mkdir( $dir, 0777, true );
	$path = $dir . '/manifest.json';

	$manifest = array(
		'assets-src/ts/main.ts' => array(
			'file'    => 'assets/main-abc123.js',
			'src'     => 'assets-src/ts/main.ts',
			'isEntry' => true,
			'imports' => array( 'assets-src/ts/chunk.js' ),
		),
		'assets-src/ts/chunk.js' => array(
			'file'    => 'assets/chunk-def456.js',
			'src'     => 'assets-src/ts/chunk.js',
			'isEntry' => false,
			'imports' => array(),
			'css'     => array( 'assets/chunk-styles-789.css' ),
		),
		'assets-src/scss/main.scss' => array(
			'file'    => 'assets/styles-0abc.css',
			'src'     => 'assets-src/scss/main.scss',
			'isEntry' => true,
			'css'     => array( 'assets/styles-0abc.css' ),
			'imports' => array(),
		),
	);

	// phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode -- WP-free fixture encode.
	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents -- WP-free fixture.
	@file_put_contents( $path, (string) json_encode( $manifest ) );

	return $path;
}

/**
 * Remove the manifest fixture.
 *
 * @param string $path Manifest path.
 * @return void
 */
function drop_manifest_fixture( string $path ): void {
	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_unlink -- WP-free fixture cleanup.
	@unlink( $path );
	@rmdir( dirname( $path ) );
}

echo "== Phantom Core Phase 7 smoke suite (Asset Pipeline) ==\n\n";

if ( file_exists( dirname( __DIR__ ) . '/phantom.env.json' ) ) {
	echo "[SKIP] phantom.env.json present — default-state assertions are not meaningful.\n";
	echo "       Remove it (or run in a clean checkout) for the full assertion run.\n";
	exit( 0 );
}

$fixture = make_manifest_fixture();

// 1. PSR-4 resolution.
check( 'PSR-4 resolves AssetLoader', class_exists( AssetLoader::class ) );
check( 'PSR-4 resolves ManifestReader', class_exists( ManifestReader::class ) );
check( 'PSR-4 resolves DevServer', class_exists( DevServer::class ) );
check( 'PSR-4 resolves BuildFingerprint', class_exists( BuildFingerprint::class ) );
check( 'PSR-4 resolves Image', class_exists( Image::class ) );
check( 'PSR-4 resolves Markup', class_exists( Markup::class ) );
check( 'PSR-4 resolves Entries', class_exists( Entries::class ) );
check( 'PSR-4 resolves DepsResolver', class_exists( DepsResolver::class ) );

Kernel::launch();
$app = App::instance();

// 2. Container wiring.
check( 'assets.manifest resolves', $app->make( 'assets.manifest' ) instanceof ManifestReader );
check( 'assets.dev_server resolves', $app->make( 'assets.dev_server' ) instanceof DevServer );
check( 'assets.loader resolves', $app->make( 'assets.loader' ) instanceof AssetLoader );
check( 'assets.entries resolves', $app->make( 'assets.entries' ) instanceof Entries );
check( 'assets.deps resolves', $app->make( 'assets.deps' ) instanceof DepsResolver );

// 3. ManifestReader.
$reader = new ManifestReader( $fixture );
$map    = $reader->load();
check( 'manifest loads 3 entries', 3 === count( $map ), (string) count( $map ) );
check( 'manifest entry lookup by exact src', $reader->has( 'assets-src/ts/main.ts' ) );
check( 'manifest suffix-tolerant lookup', $reader->has( 'ts/main.ts' ) );
check( 'manifest file() returns hashed name', 'assets/main-abc123.js' === $reader->file( 'assets-src/ts/main.ts' ) );
check( 'manifest css() returns own + imported css', array( 'assets/styles-0abc.css' ) === $reader->css( 'assets-src/scss/main.scss' ) );
check( 'unknown src returns null file', null === $reader->file( 'nope.ts' ) );

// 4. DevServer.
$server = new DevServer( 'localhost', 5173, true );
check( 'dev server active flag', $server->is_active() );
check( 'dev server URL format', 'http://localhost:5173/assets-src/ts/main.ts' === $server->url( '/assets-src/ts/main.ts' ) );
check( 'dev server strips leading slash', 'http://localhost:5173/x.js' === $server->url( 'x.js' ) );

$inactive = new DevServer();
check( 'dev server inactive by default', ! $inactive->is_active() );

// 5. AssetLoader.
$loader = new AssetLoader( $reader, $inactive, 'https://cdn.test/assets' );
$prod   = $loader->asset_url( 'assets-src/ts/main.ts' );
check( 'loader returns hashed prod URL', 'https://cdn.test/assets/assets/main-abc123.js' === $prod, $prod );

$fallback = $loader->resolve( 'missing/entry.ts' );
check( 'loader falls back to raw source', 'https://cdn.test/assets/missing/entry.ts' === $fallback, $fallback );

$dev_loader = new AssetLoader( $reader, $server, 'https://cdn.test/assets' );
$dev_url    = $dev_loader->resolve( 'assets-src/ts/main.ts' );
check( 'loader uses dev server when active', str_starts_with( $dev_url, 'http://localhost:5173/' ), $dev_url );

$no_base = new AssetLoader( $reader, $inactive, '' );
check( 'loader with empty base yields root-relative URL', '/assets/main-abc123.js' === $no_base->resolve( 'assets-src/ts/main.ts' ), $no_base->resolve( 'assets-src/ts/main.ts' ) );

// Vite 6 `.vite/manifest.json` fallback probe.
$vite6_dir = sys_get_temp_dir() . '/phantom-vite6-' . getmypid();
@mkdir( $vite6_dir . '/.vite', 0777, true );
// phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode -- WP-free fixture encode.
// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents -- WP-free fixture.
@file_put_contents(
	$vite6_dir . '/.vite/manifest.json',
	(string) json_encode( array( 'main.ts' => array( 'file' => 'main-hash.js', 'isEntry' => true ) ) )
);
$vite6_reader = new ManifestReader( $vite6_dir . '/manifest.json' );
check( 'manifest probes .vite/ subdirectory (Vite 6)', 'main-hash.js' === $vite6_reader->file( 'main.ts' ) );
// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_unlink -- WP-free fixture cleanup.
@unlink( $vite6_dir . '/.vite/manifest.json' );
@rmdir( $vite6_dir . '/.vite' );
@rmdir( $vite6_dir );

$empty_reader = new ManifestReader( $fixture . '.missing' );
$empty_loader = new AssetLoader( $empty_reader, $inactive, 'https://x.test' );
check( 'missing manifest → raw fallback', 'https://x.test/src/x.js' === $empty_loader->resolve( 'src/x.js' ) );

// 6. BuildFingerprint.
$fingerprint = new BuildFingerprint( $reader );
$token       = $fingerprint->token();
check( 'fingerprint token is 32-hex', 32 === strlen( $token ) && ctype_xdigit( $token ) );
check( 'fingerprint is memoized', $token === $fingerprint->token() );
$dev_fingerprint = new BuildFingerprint( $empty_reader );
check( 'no build → dev token', 'dev' === $dev_fingerprint->token() );

// 7. Image.
$srcset = Image::build_srcset(
	array(
		array( 'url' => 'a.png', 'width' => 300 ),
		array( 'url' => 'b.png', 'width' => 600 ),
		array( 'url' => 'skip.png', 'width' => 0 ),
	)
);
check( 'build_srcset pure builder', 'a.png 300w, b.png 600w' === $srcset, $srcset );
check( 'srcset() is WP-free safe (empty map)', array() === Image::srcset( 1, array( 300, 300 ) ) );

// 8. Markup.
check( 'defer_all returns a script', str_contains( Markup::defer_all(), '<script>' ) );
$preload = Markup::preload_critical_css( 'https://x.test/a.css' );
check( 'preload_critical_css returns link tag', str_contains( $preload, 'rel="preload"' ) && str_contains( $preload, 'https://x.test/a.css' ) );
$escaped = Markup::preload_critical_css( 'https://x.test/?a=1&b=2' );
check( 'preload href is attribute-escaped', str_contains( $escaped, '&amp;' ) );

// 9. Pipeline.
$entries = new Entries( $reader );
check( 'entries() only includes isEntry', 2 === count( $entries->entries() ) && $entries->has( 'assets-src/ts/main.ts' ) );
check( 'entries() excludes non-entries', ! $entries->has( 'assets-src/ts/chunk.js' ) );

$deps = new DepsResolver( $reader );
check( 'deps direct imports', array( 'assets-src/ts/chunk.js' ) === $deps->imports( 'assets-src/ts/main.ts' ) );
check( 'deps transitive closure', array( 'assets-src/ts/chunk.js' ) === $deps->resolve( 'assets-src/ts/main.ts' ) );
check( 'deps unknown src → empty', array() === $deps->resolve( 'nope.ts' ) );

// Cycle safety: a→b→a closure terminates.
$cycle_manifest = array(
	'a.ts' => array( 'file' => 'a.js', 'src' => 'a.ts', 'imports' => array( 'b.ts' ) ),
	'b.ts' => array( 'file' => 'b.js', 'src' => 'b.ts', 'imports' => array( 'a.ts' ) ),
);
$cycle_path = sys_get_temp_dir() . '/phantom-cycle-' . getmypid() . '.json';
// phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode -- WP-free fixture encode.
// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents -- WP-free fixture.
@file_put_contents( $cycle_path, (string) json_encode( $cycle_manifest ) );
$cycle_reader = new ManifestReader( $cycle_path );
$cycle_deps   = new DepsResolver( $cycle_reader );
$closure      = $cycle_deps->resolve( 'a.ts' );
check( 'deps resolver is cycle-safe', array( 'b.ts' ) === $closure, implode( ',', $closure ) );
// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_unlink -- WP-free fixture cleanup.
@unlink( $cycle_path );

// 10. Phases 1–6 regression.
check( 'Phase 1 regression: env resolvable', 'production' === $app->make( 'env' ) );
check( 'Phase 4 regression: renderer resolves', $app->make( 'render.renderer' ) instanceof \Phantom\Core\Render\Renderer );
check( 'Phase 5 regression: components registry resolves', $app->make( 'components.registry' ) instanceof \Phantom\Core\Components\Registry );
check( 'Phase 6 regression: templates.resolver resolves', $app->make( 'templates.resolver' ) instanceof \Phantom\Core\Templates\TemplateResolver );
check( 'Phase 6 regression: resolve("single") works', null !== $app->make( 'templates.resolver' )->resolve( 'single' ) );

drop_manifest_fixture( $fixture );

echo "\n== Results: {$passes} passed, {$fails} failed ==\n";

exit( 0 === $fails ? 0 : 1 );
