<?php
/**
 * AUREON Snippet Allowlist — version-controlled PHP snippets.
 *
 * THE ONLY place where executable PHP for hooks/elements may live.
 * Each entry must be code-reviewed in version control. Nothing from the
 * database or the admin UI is ever executed.
 *
 * To add a snippet: define a function here and register it below.
 * To use in a hook/element: set the content to a single line `snippet:<id>`.
 *
 * Migrated legacy semantics:
 *   - Previously `<?php ... ?>` content was eval()'d (arbitrary execution).
 *   - The common legacy patterns (shortcode output, dynamic year, WP footer
 *     scripts) are preserved as named snippets below.
 *
 * @package Aureon Studio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Current year (common footer/copyright usage).
Aureon_Snippet_Registry::register(
	'current_year',
	function () {
		echo esc_html( gmdate( 'Y' ) );
	}
);

// WordPress wp_footer scripts passthrough (analytics hooks etc.).
Aureon_Snippet_Registry::register(
	'wp_footer_scripts',
	function () {
		wp_footer();
	}
);

// WordPress body open hook.
Aureon_Snippet_Registry::register(
	'wp_body_open',
	function () {
		if ( function_exists( 'wp_body_open' ) ) {
			wp_body_open();
		}
	}
);
