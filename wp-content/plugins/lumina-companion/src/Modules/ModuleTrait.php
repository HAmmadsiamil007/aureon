<?php
/**
 * ModuleTrait — shared helpers for Lumina Companion modules.
 *
 * Provides namespaced option storage (get_option guarded for WP-free runs),
 * Customizer registration helpers, and default merging.
 *
 * @package Lumina\Companion\Modules
 */

declare( strict_types=1 );

namespace Lumina\Companion\Modules;

/**
 * Shared module behavior.
 */
trait ModuleTrait {

	/**
	 * Default settings for this module.
	 *
	 * @return array<string, mixed>
	 */
	protected function defaults(): array {
		return array();
	}

	/**
	 * Option name for this module (prefixed, namespaced).
	 *
	 * @return string
	 */
	protected function option_name(): string {
		return 'lumina_companion_' . $this->slug();
	}

	/**
	 * Read the merged settings for this module.
	 *
	 * @return array<string, mixed>
	 */
	protected function settings(): array {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		$stored = function_exists( 'get_option' ) ? get_option( $this->option_name(), array() ) : array();

		if ( ! is_array( $stored ) ) {
			$stored = array();
		}

		return array_merge( $this->defaults(), $stored );
	}

	/**
	 * Read a single setting.
	 *
	 * @param string $key      Setting key.
	 * @param mixed  $fallback Fallback.
	 * @return mixed
	 */
	protected function setting( string $key, mixed $fallback = null ): mixed {
		$settings = $this->settings();

		return $settings[ $key ] ?? $fallback;
	}

	/**
	 * Sanitize a text value (WP-free guarded fallback).
	 *
	 * @param mixed $value Raw value.
	 * @return string
	 */
	protected function sanitize_text( mixed $value ): string {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		if ( function_exists( 'sanitize_text_field' ) ) {
			return sanitize_text_field( (string) $value );
		}

		// WP-free fallback: strip tags + control chars (sanitize_text_field parity).
		$clean = self::strip_tags_fallback( (string) $value );

		return trim( preg_replace( '/[\r\n\t\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $clean ) ?? '' );
	}

	/**
	 * WP-free strip-tags fallback (sanitize_text_field parity subset).
	 *
	 * @param string $value Raw value.
	 * @return string
	 */
	private static function strip_tags_fallback( string $value ): string {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.strip_tags_strip_tags -- WP-free fallback only.
		return strip_tags( $value );
	}

	/**
	 * Register a Customizer setting + control pair (guarded, original).
	 *
	 * @param mixed  $customizer WP_Customize_Manager.
	 * @param string $key        Setting key.
	 * @param array  $args       Setting args (type, default, sanitize_callback).
	 * @param array  $control    Control args (label, type, choices…).
	 * @return void
	 */
	protected function add_setting( $customizer, string $key, array $args, array $control ): void {
		// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals -- WP core API in guarded calls.
		if ( ! is_object( $customizer ) || ! method_exists( $customizer, 'add_setting' ) || ! method_exists( $customizer, 'add_control' ) ) {
			return;
		}

		$option_name = $this->option_name();
		$default     = $this->defaults()[ $key ] ?? '';

		$customizer->add_setting(
			$option_name . '[' . $key . ']',
			array_merge(
				array(
					'type'              => 'option',
					'default'           => $default,
					'sanitize_callback' => 'sanitize_text_field',
				),
				$args
			)
		);

		$customizer->add_control(
			$option_name . '[' . $key . ']',
			array_merge(
				array(
					'section' => 'lumina_companion',
				),
				$control
			)
		);
		// phpcs:enable WordPress.NamingConventions.PrefixAllGlobals
	}
}
