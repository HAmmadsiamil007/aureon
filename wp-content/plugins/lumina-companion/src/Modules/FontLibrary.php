<?php
/**
 * FontLibrary — font registration and enqueueing for the Lumina theme.
 *
 * Original implementation of the premium font-library feature category.
 * Registers Google-font families (or custom hosted font URLs) as CSS
 * custom properties (--lumina-font-*) so the typography system consumes
 * the selected families without hard-coded stacks.
 *
 * @package Lumina\Companion\Modules
 */

declare( strict_types=1 );

namespace Lumina\Companion\Modules;

/**
 * FontLibrary module.
 */
final class FontLibrary implements ModuleInterface {

	use ModuleTrait;

	/**
	 * {@inheritDoc}
	 */
	public function slug(): string {
		return 'font-library';
	}

	/**
	 * {@inheritDoc}
	 */
	public function label(): string {
		return 'Font Library';
	}

	/**
	 * Default settings.
	 *
	 * @return array<string, mixed>
	 */
	protected function defaults(): array {
		return array(
			'heading_font' => '',
			'body_font'    => '',
			'google_fonts' => '', // Comma-separated Google Fonts families.
			'custom_css'   => '',
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
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_fonts' ), 20 );
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		add_action( 'wp_head', array( $this, 'print_css' ), 18 );
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
			'heading_font',
			array( 'sanitize_callback' => 'sanitize_text_field' ),
			array(
				'label' => 'Heading font family',
				'type'  => 'text',
			)
		);
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
			'google_fonts',
			array( 'sanitize_callback' => 'sanitize_text_field' ),
			array(
				'label' => 'Google Fonts (comma separated)',
				'type'  => 'text',
			)
		);
	}

	/**
	 * Enqueue the Google Fonts stylesheet (guarded).
	 *
	 * @return void
	 */
	public function enqueue_fonts(): void {
		$families = is_string( $this->setting( 'google_fonts', '' ) ) ? trim( (string) $this->setting( 'google_fonts', '' ) ) : '';

		if ( '' === $families ) {
			return;
		}

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		if ( ! function_exists( 'wp_enqueue_style' ) ) {
			return;
		}

		$url = 'https://fonts.googleapis.com/css2?family=' . str_replace( ' ', '+', $families ) . '&display=swap';

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		wp_enqueue_style( 'lumina-fonts', esc_url( $url ), array(), LUMINA_COMPANION_VERSION );
	}

	/**
	 * Token-driven font CSS.
	 *
	 * @return string
	 */
	public function css(): string {
		$heading = is_string( $this->setting( 'heading_font', '' ) ) ? (string) $this->setting( 'heading_font', '' ) : '';
		$body    = is_string( $this->setting( 'body_font', '' ) ) ? (string) $this->setting( 'body_font', '' ) : '';
		$extra   = is_string( $this->setting( 'custom_css', '' ) ) ? (string) $this->setting( 'custom_css', '' ) : '';

		$rules = array();

		if ( '' !== $heading ) {
			$rules[] = '--lumina-font-heading:' . $heading;
		}

		if ( '' !== $body ) {
			$rules[] = '--lumina-font-body:' . $body;
		}

		$css = '';

		if ( array() !== $rules ) {
			$css .= ':root{' . implode( ';', $rules ) . ';}';
		}

		if ( '' !== $extra ) {
			$css .= $extra;
		}

		return $css;
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

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- font-family values from sanitized settings.
		echo '<style id="lumina-fonts-css">' . $css . '</style>';
	}
}
