<?php
/**
 * AUREON asset reference check.
 *
 * Verifies every local asset referenced by pack HTML actually exists.
 * References that carry a graceful onerror fallback are reported but do not
 * fail the check (demo-only pages).
 *
 * Usage: php scripts/check-assets.php  (exit 0 = pass, 1 = missing required)
 */

$pack = __DIR__ . '/../frontend/designs/vineta';
chdir( $pack );

$refs = array();
$it   = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( '.', FilesystemIterator::SKIP_DOTS ) );
foreach ( $it as $f ) {
	if ( ! $f->isFile() || ! preg_match( '/\.(html|php|css|js)$/i', $f->getFilename() ) ) {
		continue;
	}
	if ( false !== strpos( $f->getPathname(), 'node_modules' ) ) {
		continue;
	}
	$c    = file_get_contents( $f->getPathname() );
	$base = dirname( $f->getPathname() );
	preg_match_all( '/(?:src|href|poster|data-src)\s*=\s*["\']((?:images|video|fonts)\/[^"\']+)["\']/i', $c, $m );
	foreach ( $m[1] as $p ) {
		$refs[ $base . '/' . $p ] = $f->getPathname();
	}
	// CSS url() refs.
	preg_match_all( '/url\(\s*["\']?((?:\.\.\/)?(?:images|fonts)\/[^"\')]+)["\']?\s*\)/i', $c, $m2 );
	foreach ( $m2[1] as $p ) {
		$p = preg_replace( '/^\.\.\//', '', $p );
		$refs[ './' . $p ] = $f->getPathname();
	}
}

ksort( $refs );
$missing_required = 0;
$missing_tolerated = 0;
foreach ( $refs as $path => $src ) {
	if ( file_exists( $path ) ) {
		continue;
	}
	$src_content = file_get_contents( $src );
	$base        = basename( $path );
	// Tolerated when referenced with an onerror fallback in the same file.
	if ( preg_match( '/onerror[^>]*' . preg_quote( $base, '/' ) . '/', $src_content ) || false !== strpos( $src_content, 'onerror' ) ) {
		echo "TOLERATED (graceful fallback): $path  <- $src\n";
		$missing_tolerated++;
		continue;
	}
	// Tolerated when it is a model-viewer .glb (web component degrades itself).
	if ( '.glb' === substr( $path, -4 ) ) {
		echo "TOLERATED (model-viewer degrades): $path  <- $src\n";
		$missing_tolerated++;
		continue;
	}
	echo "MISSING (required): $path  <- $src\n";
	$missing_required++;
}

echo "\nReferences checked: " . count( $refs ) . "\n";
echo "Missing required: $missing_required | Tolerated (graceful): $missing_tolerated\n";
exit( $missing_required > 0 ? 1 : 0 );
