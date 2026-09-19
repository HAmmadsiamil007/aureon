<?php
/**
 * AUREON Core Freeze gate — boundary enforcement (Phase D).
 *
 * The AUREON core (WordPress/WooCommerce integration, runtime, plugin,
 * engine infrastructure and shared contracts) is FROZEN. Client visual work
 * happens exclusively inside frontend/designs/<pack>/.
 *
 * This script checksums every frozen file and compares it against
 * docs/core-freeze-manifest.json. Any drift — even one byte — fails the gate.
 *
 * Usage:
 *   php scripts/check-core-freeze.php             # verify against manifest
 *   php scripts/check-core-freeze.php --generate  # (re)build the manifest
 *
 * Exit codes: 0 = boundary intact, 1 = frozen file modified, 2 = missing manifest.
 *
 * @package Aureon
 */

$root      = dirname( __DIR__ );
$manifest  = $root . '/docs/core-freeze-manifest.json';
$generate  = in_array( '--generate', $argv, true );

/**
 * Frozen roots: every PHP/JSON file under these directories is checksummed.
 * Design packs (frontend/designs/*) are deliberately NOT here — they are the
 * editable layer. QA/demo dirs are excluded from the scan entirely.
 */
$frozenRoots = array(
	'themes/aureon',
	'plugins/aureon-studio',
	'frontend/views',
	'frontend/tokens',
	'frontend/adapters',
);

$exclude = array(
	'@(?:^|/)\.git(/|$)@',
	'@(?:^|/)_archive(/|$)@',
	'@(?:^|/)demo(/|$)@',
	'@\.map$@',
);

$collect = function ( $relRoot ) use ( $root, $exclude ) {
	$files = array();
	$dir   = $root . '/' . $relRoot;
	if ( ! is_dir( $dir ) ) {
		return $files;
	}
	$rii = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS ) );
	foreach ( $rii as $f ) {
		if ( ! $f->isFile() ) {
			continue;
		}
		$rel = str_replace( '\\', '/', str_replace( $root . '/', '', $f->getPathname() ) );
		foreach ( $exclude as $rx ) {
			if ( preg_match( $rx, $rel ) ) {
				continue 2;
			}
		}
		if ( 'php' !== $f->getExtension() && 'json' !== $f->getExtension() ) {
			continue;
		}
		$files[ $rel ] = hash_file( 'sha256', $f->getPathname() );
	}
	return $files;
};

$current = array();
foreach ( $frozenRoots as $fr ) {
	$current += $collect( $fr );
}

if ( $generate ) {
	@mkdir( dirname( $manifest ), 0777, true );
	file_put_contents(
		$manifest,
		wp_json_encode_safe( array(
			'generated' => gmdate( 'c' ),
			'note'      => 'AUREON CORE FROZEN manifest. Regenerate ONLY after a reviewed, regression-tested core change.',
			'files'     => $current,
		) )
	);
	echo 'Manifest generated: ' . count( $current ) . " frozen files checksummed.\n";
	exit( 0 );
}

if ( ! file_exists( $manifest ) ) {
	echo "ERROR: docs/core-freeze-manifest.json missing. Run `php scripts/check-core-freeze.php --generate` once the baseline is approved.\n";
	exit( 2 );
}

$saved = json_decode( (string) file_get_contents( $manifest ), true );
if ( ! is_array( $saved ) || empty( $saved['files'] ) ) {
	echo "ERROR: manifest unreadable/corrupt.\n";
	exit( 2 );
}

$savedFiles = $saved['files'];
$modified   = array();
$missing    = array();
$added      = array();

foreach ( $savedFiles as $rel => $hash ) {
	if ( ! file_exists( $root . '/' . $rel ) ) {
		$missing[] = $rel;
		continue;
	}
	if ( hash_file( 'sha256', $root . '/' . $rel ) !== $hash ) {
		$modified[] = $rel;
	}
}
foreach ( array_keys( $current ) as $rel ) {
	if ( ! isset( $savedFiles[ $rel ] ) ) {
		$added[] = $rel;
	}
}

$fail = false;
if ( $modified ) {
	$fail = true;
	echo 'MODIFIED frozen files (' . count( $modified ) . "):\n";
	foreach ( $modified as $rel ) {
		echo "  M  $rel\n";
	}
}
if ( $missing ) {
	$fail = true;
	echo 'MISSING frozen files (' . count( $missing ) . "):\n";
	foreach ( $missing as $rel ) {
		echo "  D  $rel\n";
	}
}
if ( $added ) {
	echo 'New files inside frozen roots (' . count( $added ) . ") — regenerate manifest if intentional:\n";
	foreach ( $added as $rel ) {
		echo "  A  $rel\n";
	}
}

if ( $fail ) {
	echo "FAIL: AUREON core boundary violated. Revert the change or run a reviewed re-freeze.\n";
	exit( 1 );
}

echo 'PASS: core boundary intact — ' . count( $savedFiles ) . " frozen files unchanged.\n";
exit( 0 );

/**
 * JSON encode helper (wp_json_encode is unavailable in CLI outside WP).
 *
 * @param mixed $data Data to encode.
 * @return string
 */
function wp_json_encode_safe( $data ) {
	if ( function_exists( 'wp_json_encode' ) ) {
		return wp_json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
	}
	return json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
}
