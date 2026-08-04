<?php
/**
 * Colors — per-element color overrides for the Lumina theme.
 *
 * Original implementation of the premium colors feature category. Emits
 * token-driven CSS custom properties (--lumina-color-*) so every Lumina
 * component recolors through the existing token system — no hard-coded
 * hex values in component CSS.
 *
 * @package Lumina\Companion\Modules
 */

declare( strict_types=1 );

namespace Lumina\Companion\Modules;

/**
 * Colors module.
 */
final class Colors implements ModuleInterface {

	use ModuleTrait;

	/**
	 * {@inheritDoc}
	 */
	public function slug(): string {
		return 'colors';
	}

	/**
	 * {@inheritDoc}
	 */
	public function label(): string {
		return 'Colors';
	}

	/**
	 * Default per-element colors (token values; '' = inherit theme token).
	 *
	 * @return array<string, mixed>
	 */
	protected function defaults(): array {
		return array(
			'background'   => '',
			'text'         => '',
			'heading'      => '',
			'link'         => '',
			'link_hover'   => '',
			'button_bg'    => '',
			'button_text'  => '',
			'button_hover' => '',
			'border'       => '',
			'header_bg'    => '',
			'footer_bg'    => '',
			'selection_bg' => '',
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
		add_action( 'wp_head', array( $this, 'print_css' ), 20 );
	}

	/**
	 * Customizer settings.
	 *
	 * @param mixed $customizer WP_Customize_Manager.
	 * @return void
	 */
	public function customizer( $customizer ): void {
		$fields = array(
			'background'   => 'Background',
			'text'         => 'Text',
			'heading'      => 'Headings',
			'link'         => 'Links',
			'link_hover'   => 'Links (hover)',
			'button_bg'    => 'Button background',
			'button_text'  => 'Button text',
			'button_hover' => 'Button hover',
			'border'       => 'Borders',
			'header_bg'    => 'Header background',
			'footer_bg'    => 'Footer background',
			'selection_bg' => 'Selection background',
		);

		foreach ( $fields as $key => $label ) {
			$this->add_setting(
				$customizer,
				$key,
				array( 'sanitize_callback' => 'sanitize_hex_color' ),
				array(
					'label' => $label,
					'type'  => 'color',
				)
			);
		}
	}

	/**
	 * Token-driven CSS custom properties (overrides only when set).
	 *
	 * @return string
	 */
	public function css(): string {
		$s     = $this->settings();
		$rules = array();

		$map = array(
			'background'   => '--lumina-color-bg',
			'text'         => '--lumina-color-text',
			'heading'      => '--lumina-color-heading',
			'link'         => '--lumina-color-link',
			'link_hover'   => '--lumina-color-link-hover',
			'button_bg'    => '--lumina-color-button-bg',
			'button_text'  => '--lumina-color-button-text',
			'button_hover' => '--lumina-color-button-hover',
			'border'       => '--lumina-color-border',
			'header_bg'    => '--lumina-color-header-bg',
			'footer_bg'    => '--lumina-color-footer-bg',
			'selection_bg' => '--lumina-color-selection-bg',
		);

		foreach ( $map as $key => $var ) {
			$value = $s[ $key ] ?? '';

			if ( is_string( $value ) && '' !== $value && $this->is_hex( $value ) ) {
				$rules[] = $var . ':' . $value;
			}
		}

		if ( array() === $rules ) {
			return '';
		}

		return ':root{' . implode( ';', $rules ) . ';}';
	}

	/**
	 * Whether a value looks like a hex color.
	 *
	 * @param string $value Raw value.
	 * @return bool
	 */
	private function is_hex( string $value ): bool {
		return 1 === preg_match( '/^#[0-9a-fA-F]{3,8}$/', $value );
	}

	/**
	 * Print CSS in <head> (guarded).
	 *
	 * @return void
	 */
	public function print_css(): void {
		$css = $this->css();

		if ( '' === $css ) {
			return;
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- generated from sanitize_hex_color values.
		echo '<style id="lumina-colors-css">' . $css . '</style>';
	}
}
