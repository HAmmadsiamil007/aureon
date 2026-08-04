<?php
/**
 * Hooks — custom hook / code-snippet injection for the Lumina theme.
 *
 * Original implementation of the premium hooks feature category. Lets users
 * inject HTML snippets at named Lumina hooks. Content is sanitized with
 * wp_kses_post (script/style tags are stripped — safe-by-default), matching
 * the theme's plugin-safe posture.
 *
 * @package Lumina\Companion\Modules
 */

declare( strict_types=1 );

namespace Lumina\Companion\Modules;

/**
 * Hooks module.
 */
final class Hooks implements ModuleInterface {

	use ModuleTrait;

	/**
	 * Canonical injection points → Lumina hooks.
	 *
	 * @var array<string, string>
	 */
	private const POINTS = array(
		'wp_head'       => 'wp_head',
		'wp_body_open'  => 'wp_body_open',
		'wp_footer'     => 'wp_footer',
		'before_header' => 'lumina_before_header',
		'after_header'  => 'lumina_after_header',
		'before_footer' => 'lumina_before_footer',
		'after_footer'  => 'lumina_after_footer',
	);

	/**
	 * {@inheritDoc}
	 */
	public function slug(): string {
		return 'hooks';
	}

	/**
	 * {@inheritDoc}
	 */
	public function label(): string {
		return 'Hooks';
	}

	/**
	 * Default settings (flat per hook point).
	 *
	 * @return array<string, mixed>
	 */
	protected function defaults(): array {
		return array(
			'wp_head'       => '',
			'wp_body_open'  => '',
			'wp_footer'     => '',
			'before_header' => '',
			'after_header'  => '',
			'before_footer' => '',
			'after_footer'  => '',
		);
	}

	/**
	 * Register hook renderers (guarded).
	 *
	 * @return void
	 */
	public function register(): void {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		if ( ! function_exists( 'add_action' ) ) {
			return;
		}

		foreach ( self::POINTS as $point => $hook ) {
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
			add_action( $hook, array( $this, 'render_snippet' ), 20 );
		}
	}

	/**
	 * Render the snippet for the current hook (guarded).
	 *
	 * @return void
	 */
	public function render_snippet(): void {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		$hook  = function_exists( 'current_filter' ) ? (string) current_filter() : '';
		$point = array_search( $hook, self::POINTS, true );

		if ( false === $point ) {
			return;
		}

		$content = $this->setting( $point, '' );

		if ( ! is_string( $content ) || '' === $content ) {
			return;
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_kses_post applied here.
		echo wp_kses_post( $content );
	}

	/**
	 * Customizer settings.
	 *
	 * @param mixed $customizer WP_Customize_Manager.
	 * @return void
	 */
	public function customizer( $customizer ): void {
		$labels = array(
			'wp_head'       => 'HTML in <head>',
			'wp_body_open'  => 'HTML after body open',
			'wp_footer'     => 'HTML before </body>',
			'before_header' => 'HTML before header',
			'after_header'  => 'HTML after header',
			'before_footer' => 'HTML before footer',
			'after_footer'  => 'HTML after footer',
		);

		$i = 10;

		foreach ( $labels as $key => $label ) {
			$this->add_setting(
				$customizer,
				$key,
				array( 'sanitize_callback' => 'wp_kses_post' ),
				array(
					'label'    => $label,
					'type'     => 'textarea',
					'priority' => $i,
				)
			);

			$i += 10;
		}
	}
}
