<?php
/**
 * Typography — font family/size/weight controls for the Lumina theme.
 *
 * Original implementation of the premium typography feature category. Emits
 * token-driven CSS custom properties (--lumina-typography-*) consumed by the
 * Lumina token engine and component styles.
 *
 * @package Lumina\Companion\Modules
 */

declare( strict_types=1 );

namespace Lumina\Companion\Modules;

/**
 * Typography module.
 */
final class Typography implements ModuleInterface {

	use ModuleTrait;

	/**
	 * {@inheritDoc}
	 */
	public function slug(): string {
		return 'typography';
	}

	/**
	 * {@inheritDoc}
	 */
	public function label(): string {
		return 'Typography';
	}

	/**
	 * Default typography settings.
	 *
	 * @return array<string, mixed>
	 */
	protected function defaults(): array {
		return array(
			'body_font'      => '"Inter", system-ui, sans-serif',
			'heading_font'   => '"Inter", system-ui, sans-serif',
			'body_size'      => '16',
			'heading_weight' => '700',
			'line_height'    => '1.6',
		);
	}

	/**
	 * Register WP hooks (guarded).
	 *
	 * @return void
	 */
	public function register(): void {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		if ( ! function_exists( 'add_action' ) ) {
			return;
		}

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		add_action( 'wp_head', array( $this, 'print_css' ), 25 );
	}

	/**
	 * Customizer settings.
	 *
	 * @param mixed $customizer WP_Customize_Manager.
	 * @return void
	 */
	public function customizer( $customizer ): void {
		$this->add_setting(
			$customizer,
			'body_font',
			array( 'sanitize_callback' => 'sanitize_text_field' ),
			array(
				'label' => 'Body font family',
				'type'  => 'text',
			)
		);
		$this->add_setting(
			$customizer,
			'heading_font',
			array( 'sanitize_callback' => 'sanitize_text_field' ),
			array(
				'label' => 'Heading font family',
				'type'  => 'text',
			)
		);
		$this->add_setting(
			$customizer,
			'body_size',
			array( 'sanitize_callback' => 'absint' ),
			array(
				'label' => 'Body font size (px)',
				'type'  => 'number',
			)
		);
		$this->add_setting(
			$customizer,
			'heading_weight',
			array( 'sanitize_callback' => 'absint' ),
			array(
				'label' => 'Heading font weight',
				'type'  => 'number',
			)
		);
		$this->add_setting(
			$customizer,
			'line_height',
			array( 'sanitize_callback' => 'sanitize_text_field' ),
			array(
				'label' => 'Line height',
				'type'  => 'text',
			)
		);
	}

	/**
	 * Token-driven CSS custom properties.
	 *
	 * @return string
	 */
	public function css(): string {
		$s = $this->settings();

		// Font families may contain quotes — strip for safe inline CSS.
		$body    = $this->sanitize_text( $s['body_font'] );
		$heading = $this->sanitize_text( $s['heading_font'] );

		return sprintf(
			':root{--lumina-typography-font-sans:%s;--lumina-typography-font-heading:%s;--lumina-typography-type-size-base:%srem;--lumina-typography-heading-weight:%s;--lumina-typography-line-height:%s;}',
			$body,
			$heading,
			( (int) $s['body_size'] ) / 16,
			(int) $s['heading_weight'],
			$this->sanitize_text( $s['line_height'] )
		);
	}

	/**
	 * Print CSS in <head> (guarded).
	 *
	 * @return void
	 */
	public function print_css(): void {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- generated from sanitized values.
		echo '<style id="lumina-typography-css">' . $this->css() . '</style>';
	}
}
