<?php
/**
 * AUREON Snippet Registry — safe replacement for arbitrary PHP execution.
 *
 * Replaces eval()-based hook/element PHP execution with an explicit,
 * version-controlled allowlist of named PHP snippets.
 *
 * Contract:
 *   stored hook config  ->  "snippet:<id>" reference  ->  registered callable
 *   ->  controlled execution  ->  buffered output
 *
 * Security model:
 *   - PHP code lives ONLY in inc/snippet-registry.php (code-reviewed, not
 *     editable through the WP admin or the database).
 *   - Hook/element content that declares `snippet:<id>` executes the
 *     registered callable for that exact id — nothing else ever executes.
 *   - Any other content (including stored PHP strings) is echoed verbatim,
 *     never evaluated. A missing/invalid id therefore degrades visibly
 *     instead of silently dropping output.
 *
 * @package Aureon Studio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

final class Aureon_Snippet_Registry {

	/**
	 * Registered snippets: id => callable.
	 *
	 * @var array
	 */
	private static $snippets = array();

	/**
	 * Whether the allowlist definitions file has been loaded.
	 *
	 * @var bool
	 */
	private static $bootstrapped = false;

	/**
	 * Register a named snippet callable.
	 *
	 * @param string   $id       Stable snippet identifier ([a-z0-9_-]).
	 * @param callable $callback Output-producing callable. May echo or return.
	 * @return bool True when registered.
	 */
	public static function register( $id, $callback ) {
		$id = self::sanitize_id( $id );
		if ( '' === $id || ! is_callable( $callback ) ) {
			return false;
		}
		self::$snippets[ $id ] = $callback;
		return true;
	}

	/**
	 * Whether a snippet id is registered.
	 *
	 * @param string $id Snippet identifier.
	 * @return bool
	 */
	public static function is_registered( $id ) {
		return isset( self::$snippets[ self::sanitize_id( $id ) ] );
	}

	/**
	 * Extract a snippet reference from stored content.
	 *
	 * Accepted form (case-insensitive, trimmed): `snippet:<id>` as its own
	 * line (optionally with surrounding whitespace/newlines).
	 *
	 * @param string $content Stored hook/element content.
	 * @return string Snippet id, or '' when content is not a snippet reference.
	 */
	public static function extract_id( $content ) {
		if ( ! is_string( $content ) ) {
			return '';
		}
		if ( preg_match( '/(?:^|[\r\n])\s*snippet:\s*([a-z0-9_\-]+)\s*(?:$|[\r\n])/i', $content, $m ) ) {
			return self::sanitize_id( $m[1] );
		}
		return '';
	}

	/**
	 * Execute a registered snippet with output buffering.
	 *
	 * @param string $id Snippet identifier.
	 * @return string|false Rendered output, or false when id is unknown.
	 */
	public static function execute( $id ) {
		$id = self::sanitize_id( $id );
		if ( '' === $id || ! isset( self::$snippets[ $id ] ) ) {
			return false;
		}
		ob_start();
		call_user_func( self::$snippets[ $id ] );
		return ob_get_clean();
	}

	/**
	 * Load the version-controlled allowlist definitions.
	 *
	 * @return void
	 */
	public static function bootstrap() {
		if ( self::$bootstrapped ) {
			return;
		}
		self::$bootstrapped = true;

		$file = AUREON_STUDIO_DIR_PATH . 'inc/snippet-registry.php';
		if ( file_exists( $file ) ) {
			require_once $file;
		}
	}

	/**
	 * Sanitize a snippet identifier.
	 *
	 * @param string $id Raw identifier.
	 * @return string
	 */
	private static function sanitize_id( $id ) {
		return preg_replace( '/[^a-z0-9_\-]/', '', strtolower( (string) $id ) );
	}
}

Aureon_Snippet_Registry::bootstrap();
