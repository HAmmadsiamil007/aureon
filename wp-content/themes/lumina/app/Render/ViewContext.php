<?php
/**
 * ViewContext — what a template/component actually receives.
 *
 * Phase 4 (Render Engine): wraps a ViewModel and exposes context-safe escaping
 * helpers (`e`, `attr`, `url`, `html`) that delegate to WordPress escaping
 * functions when available and fall back to PHP-native equivalents in WP-free
 * CLI/smoke contexts. Every field a component prints must pass through one of
 * these helpers (plan §Phase 4: "escaping helpers esc_html designated per
 * field").
 *
 * @package Lumina\Core\Render
 * @since 0.4.0
 */

declare( strict_types=1 );

namespace Lumina\Core\Render;

/**
 * Escaping-aware view context.
 */
class ViewContext {

	/**
	 * The wrapped view model.
	 *
	 * @var ViewModel
	 */
	private ViewModel $view;

	/**
	 * Wrap a view model.
	 *
	 * @param ViewModel $view View model.
	 */
	public function __construct( ViewModel $view ) {
		$this->view = $view;
	}

	/**
	 * Read a property through the underlying view model.
	 *
	 * @param string $key      Dot-notation key.
	 * @param mixed  $fallback Fallback value.
	 * @return mixed
	 */
	public function prop( string $key, mixed $fallback = null ): mixed {
		return $this->view->get( $key, $fallback );
	}

	/**
	 * The full data map (for advanced templates).
	 *
	 * @return array<string, mixed>
	 */
	public function all(): array {
		return $this->view->all();
	}

	/**
	 * Escape for HTML text context.
	 *
	 * @param mixed $value Value to escape.
	 * @return string
	 */
	public function e( mixed $value ): string {
		if ( function_exists( 'esc_html' ) ) {
			return esc_html( (string) $value );
		}

		return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' );
	}

	/**
	 * Escape for HTML attribute context.
	 *
	 * @param mixed $value Value to escape.
	 * @return string
	 */
	public function attr( mixed $value ): string {
		if ( function_exists( 'esc_attr' ) ) {
			return esc_attr( (string) $value );
		}

		return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' );
	}

	/**
	 * Escape a URL for href/src context.
	 *
	 * @param mixed $value Value to escape.
	 * @return string
	 */
	public function url( mixed $value ): string {
		if ( function_exists( 'esc_url' ) ) {
			return esc_url( (string) $value );
		}

		return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' );
	}

	/**
	 * Allow trusted HTML (rich text). Falls back to full escaping in WP-free
	 * contexts so no raw markup can ever reach output without a WordPress
	 * sanitizer.
	 *
	 * @param mixed $value Value to sanitize.
	 * @return string
	 */
	public function html( mixed $value ): string {
		if ( function_exists( 'wp_kses_post' ) ) {
			return wp_kses_post( (string) $value );
		}

		return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' );
	}
}
