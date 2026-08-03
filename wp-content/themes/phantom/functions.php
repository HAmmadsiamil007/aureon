<?php
/**
 * Phantom — thin theme loader.
 *
 * Phase 0 (Project Foundation): registers the Composer autoloader (PSR-4,
 * `Phantom\Core\` → `app/`, ADR-009) and loads the Phantom Core kernel entry
 * point. The kernel bootstrap (`app/load.php`) ships in Phase 1; until then
 * this loader is inert and safe to activate against an empty `app/` tree.
 *
 * GeneratePress, GP Premium, and WordPress Core are never modified.
 *
 * @package Phantom
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

$phantom_autoload = get_stylesheet_directory() . '/vendor/autoload.php';

if ( is_readable( $phantom_autoload ) ) {
	require_once $phantom_autoload;
}

$phantom_load = get_stylesheet_directory() . '/app/load.php';

if ( is_readable( $phantom_load ) ) {
	require_once $phantom_load;
}
