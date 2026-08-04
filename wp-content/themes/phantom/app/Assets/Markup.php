<?php
/**
 * Markup — output helpers for loading strategy.
 *
 * Phase 7 (Asset Pipeline): `defer_all()` returns the small script that
 * defers same-origin scripts (the runtime loading policy), and
 * `preload_critical_css()` returns a `preload` link tag for critical CSS.
 * Both are pure string builders — templates echo them where appropriate.
 *
 * @package Phantom\Core\Assets
 * @since 0.7.0
 */

declare( strict_types=1 );

namespace Phantom\Core\Assets;

/**
 * Static loading-policy markup builders.
 */
final class Markup {

	/**
	 * Script that defers all same-origin scripts at runtime.
	 *
	 * @return string
	 */
	public static function defer_all(): string {
		return '<script>(function(){var s=document.scripts;'
			. 'for(var i=0;i<s.length;i++){'
			. 'if(s[i].src&&s[i].src.indexOf(location.origin)===0&&!s[i].defer&&!s[i].async){s[i].defer=true;}}'
			. '})();</script>';
	}

	/**
	 * Preload link tag for critical CSS.
	 *
	 * @param string $href Stylesheet URL.
	 * @return string
	 */
	public static function preload_critical_css( string $href ): string {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		$escaped = function_exists( 'esc_attr' )
			? esc_attr( $href )
			: htmlspecialchars( $href, ENT_QUOTES, 'UTF-8' );

		return '<link rel="preload" as="style" href="' . $escaped . '">';
	}
}
