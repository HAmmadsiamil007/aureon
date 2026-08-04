<?php
/**
 * Sections — layout sections / hook regions for the Lumina theme.
 *
 * Original implementation of the premium sections feature category. Exposes
 * named regions (before-header, after-header, before-footer, after-footer)
 * that render through Lumina's public hooks. Content is stored per region in
 * a namespaced option and escaped with wp_kses_post at output.
 *
 * @package Lumina\Companion\Modules
 */

declare( strict_types=1 );

namespace Lumina\Companion\Modules;

/**
 * Sections module.
 */
final class Sections implements ModuleInterface {

	use ModuleTrait;

	/**
	 * Canonical region slugs and the hook each renders on.
	 *
	 * @var array<string, string>
	 */
	private const REGIONS = array(
		'before-header' => 'lumina_before_header',
		'after-header'  => 'lumina_after_header',
		'before-footer' => 'lumina_before_footer',
		'after-footer'  => 'lumina_after_footer',
	);

	/**
	 * {@inheritDoc}
	 */
	public function slug(): string {
		return 'sections';
	}

	/**
	 * {@inheritDoc}
	 */
	public function label(): string {
		return 'Sections';
	}

	/**
	 * Default settings (flat per-region).
	 *
	 * @return array<string, mixed>
	 */
	protected function defaults(): array {
		return array(
			'before-header' => '',
			'after-header'  => '',
			'before-footer' => '',
			'after-footer'  => '',
		);
	}

	/**
	 * Register region render hooks (guarded).
	 *
	 * @return void
	 */
	public function register(): void {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		if ( ! function_exists( 'add_action' ) ) {
			return;
		}

		foreach ( self::REGIONS as $hook ) {
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
			add_action( $hook, array( $this, 'render_region' ), 10 );
		}
	}

	/**
	 * Render the region for the current hook (guarded).
	 *
	 * @return void
	 */
	public function render_region(): void {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		$hook   = function_exists( 'current_filter' ) ? (string) current_filter() : '';
		$region = array_search( $hook, self::REGIONS, true );

		if ( false === $region ) {
			return;
		}

		$content = $this->setting( $region, '' );

		if ( is_string( $content ) && '' !== $content ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_kses_post applied here.
			echo wp_kses_post( $content );
		}
	}

	/**
	 * Customizer section for region content.
	 *
	 * @param mixed $customizer WP_Customize_Manager.
	 * @return void
	 */
	public function customizer( $customizer ): void {
		$i = 10;

		foreach ( array_keys( self::REGIONS ) as $region ) {
			$this->add_setting(
				$customizer,
				$region,
				array( 'sanitize_callback' => 'wp_kses_post' ),
				array(
					'label'    => 'Content: ' . $region,
					'type'     => 'textarea',
					'priority' => $i,
				)
			);

			$i += 10;
		}
	}
}
