<?php
/**
 * Lumina — thin theme loader.
 *
 * Phase 0 (Project Foundation): registers the Composer autoloader (PSR-4,
 * `Lumina\Core\` → `app/`, ADR-009) and loads the Lumina kernel entry
 * point. The kernel bootstrap (`app/load.php`) ships in Phase 1; until then
 * this loader is inert and safe to activate against an empty `app/` tree.
 *
 * WordPress Core is never modified.
 *
 * @package Lumina
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

$lumina_autoload = get_stylesheet_directory() . '/vendor/autoload.php';

if ( is_readable( $lumina_autoload ) ) {
	require_once $lumina_autoload;
}

$lumina_load = get_stylesheet_directory() . '/app/load.php';

if ( is_readable( $lumina_load ) ) {
	require_once $lumina_load;
}
