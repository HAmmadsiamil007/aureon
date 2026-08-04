<?php
/**
 * Phase 16.5 — Lumina release packaging.
 *
 * Builds the two distributable artifacts:
 *
 *   1. Release/lumina-1.0.0.zip            — standalone Lumina theme
 *   2. Release/lumina-companion-1.0.0.zip  — Lumina Companion plugin
 *
 * Design rules (ADR-004 / Phase 16 freeze):
 *
 *   * Only runtime files ship. Dev tooling is excluded: node_modules/,
 *     assets-src/, tools/, docs/, tests/, e2e/, vendor/ (dev-only deps),
 *     bin/ (smoke suites + integrity gate), .git/, .phpstan/, .psalm/,
 *     *.lock files, Vite/ESLint/PHPCS/PHPStan/Psalm configs.
 *   * The theme ships WITHOUT vendor/: app/load.php now registers a
 *     self-contained PSR-4 fallback autoloader, so a fresh install runs
 *     with zero composer dependency (Phase 16.5).
 *   * Built assets ship: assets/dist/ (hashed CSS/JS + .vite/manifest.json).
 *   * Distribution files ship: readme.txt, license.txt, composer.json
 *     (metadata), theme.json, style.css, and every shell template.
 *   * ZIP top-level folder matches the install slug: lumina/ and
 *     lumina-companion/ (WordPress installs by folder name).
 *
 * The script self-verifies each archive before writing it (ZipArchive open
 * test) and prints a manifest summary. Exit 0 = success, 1 = any failure.
 *
 * Usage: php bin/package-lumina.php
 *
 * @package Lumina
 * @since 1.0.0
 */

declare( strict_types=1 );

$lumina_theme_dir  = dirname( __DIR__ );
$lumina_plugin_dir = dirname( dirname( $lumina_theme_dir ) ) . '/plugins/lumina-companion';
$lumina_release    = dirname( dirname( dirname( dirname( __DIR__ ) ) ) ) . '/Release';
$lumina_version    = '1.0.0';

// ---------------------------------------------------------------------------
// Helpers.
// ---------------------------------------------------------------------------

/**
 * Recursively collect files for a package, skipping excluded fragments.
 *
 * Paths are normalized to forward slashes on every platform so exclusion
 * fragments ('/vendor/') match regardless of the OS directory separator.
 *
 * @param string $dir      Directory to walk.
 * @param array  $excludes Path fragments (relative, prefixed '/') to skip.
 * @param string $base     Base path used to make paths relative.
 * @return array<string,string> Relative path (with leading '/') => absolute path.
 */
function lumina_package_files( string $dir, array $excludes, string $base ): array {
	$files = array();
	$it    = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS )
	);

	foreach ( $it as $entry ) {
		if ( ! $entry->isFile() ) {
			continue;
		}

		$abs   = $entry->getPathname();
		$rel   = '/' . ltrim( substr( $abs, strlen( $base ) ), '/\\' );
		$rel   = str_replace( '\\', '/', $rel ); // Normalize Windows separators.
		$skip  = false;

		foreach ( $excludes as $frag ) {
			if ( str_starts_with( $rel, $frag ) ) {
				$skip = true;
				break;
			}
		}

		if ( ! $skip ) {
			$files[ $rel ] = $abs;
		}
	}

	ksort( $files );
	return $files;
}

/**
 * Build a ZIP archive from a staged file map.
 *
 * @param string               $zip_path Destination ZIP path.
 * @param array<string,string> $files    Relative path => absolute source path.
 * @param string               $slug     Top-level folder inside the archive.
 * @return int Number of files archived.
 */
function lumina_build_zip( string $zip_path, array $files, string $slug ): int {
	$zip = new ZipArchive();

	if ( true !== $zip->open( $zip_path, ZipArchive::CREATE | ZipArchive::OVERWRITE ) ) {
		fwrite( STDERR, "[package] FAILED to create: {$zip_path}\n" );
		exit( 1 );
	}

	foreach ( $files as $rel => $abs ) {
		if ( ! is_readable( $abs ) ) {
			fwrite( STDERR, "[package] FAILED — source file missing: {$abs}\n" );
			exit( 1 );
		}

		if ( ! $zip->addFile( $abs, $slug . $rel ) ) {
			fwrite( STDERR, "[package] FAILED to add file: {$abs}\n" );
			exit( 1 );
		}
	}

	if ( ! $zip->close() ) {
		fwrite( STDERR, "[package] FAILED to finalize: {$zip_path}\n" );
		exit( 1 );
	}

	// Self-verify: reopen and count.
	$check = new ZipArchive();

	if ( true !== $check->open( $zip_path ) ) {
		fwrite( STDERR, "[package] FAILED self-verify (reopen): {$zip_path}\n" );
		exit( 1 );
	}

	$count = $check->numFiles;
	$check->close();

	return $count;
}

// ---------------------------------------------------------------------------
// 1. Theme package.
// ---------------------------------------------------------------------------

$lumina_theme_excludes = array(
	'/.git',
	'/.phpstan/',
	'/.psalm/',
	'/.phpunit.cache/',
	'/assets-src/',
	'/bin/',
	'/docs/',
	'/e2e/',
	'/node_modules/',
	'/tests/',
	'/tools/',
	'/vendor/',
	'/composer.lock',
	'/package-lock.json',
	'/package.json',
	'/eslint.config.js',
	'/phpstan.neon',
	'/psalm.xml',
	'/tsconfig.json',
	'/vite.config.js',
	'/.editorconfig',
	'/.gitattributes',
	'/.gitignore',
	'/.phpcs.xml',
	'/.prettierignore',
	'/.prettierrc.json',
	'/lumina.env.json.example',
);

$lumina_theme_files = lumina_package_files( $lumina_theme_dir, $lumina_theme_excludes, $lumina_theme_dir );

// Distribution files are added explicitly (guaranteed present + versioned).
$lumina_theme_files['/readme.txt']    = $lumina_theme_dir . '/readme.txt';
$lumina_theme_files['/license.txt']   = dirname( dirname( dirname( $lumina_theme_dir ) ) ) . '/Release/license-gplv2.txt';

$lumina_theme_zip  = $lumina_release . '/lumina-' . $lumina_version . '.zip';
$lumina_theme_n    = lumina_build_zip( $lumina_theme_zip, $lumina_theme_files, 'lumina' );
$lumina_theme_size = round( filesize( $lumina_theme_zip ) / 1024 );

echo "[package] theme  : {$lumina_theme_zip} ({$lumina_theme_n} files, {$lumina_theme_size} KiB)\n";

// ---------------------------------------------------------------------------
// 2. Plugin package.
// ---------------------------------------------------------------------------

$lumina_plugin_excludes = array(
	'/.git',
	'/.phpstan/',
	'/bin/',
	'/.gitignore',
	'/phpcs.xml',
	'/phpstan.neon',
);

$lumina_plugin_files = lumina_package_files( $lumina_plugin_dir, $lumina_plugin_excludes, $lumina_plugin_dir );

$lumina_plugin_files['/license.txt'] = dirname( dirname( dirname( $lumina_theme_dir ) ) ) . '/Release/license-gplv2.txt';

$lumina_plugin_zip  = $lumina_release . '/lumina-companion-' . $lumina_version . '.zip';
$lumina_plugin_n    = lumina_build_zip( $lumina_plugin_zip, $lumina_plugin_files, 'lumina-companion' );
$lumina_plugin_size = round( filesize( $lumina_plugin_zip ) / 1024 );

echo "[package] plugin : {$lumina_plugin_zip} ({$lumina_plugin_n} files, {$lumina_plugin_size} KiB)\n";

// ---------------------------------------------------------------------------
// 3. Summary + exit.
// ---------------------------------------------------------------------------

echo "[package] OK — Lumina {$lumina_version} release packages built.\n";
echo "[package] theme  manifest: {$lumina_theme_n} files, plugin manifest: {$lumina_plugin_n} files.\n";
