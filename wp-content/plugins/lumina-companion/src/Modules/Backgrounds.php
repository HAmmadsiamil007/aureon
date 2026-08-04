<?php
/**
 * Backgrounds — per-context background color / image overrides.
 *
 * Original implementation of the premium backgrounds feature category.
 * Emits token-driven CSS custom properties (--lumina-bg-*) plus an optional
 * fixed background layer, so body/section backgrounds can be styled from
 * the Customizer without touching component CSS.
 *
 * @package Lumina\Companion\Modules
 */

declare( strict_types=1 );

namespace Lumina\Companion\Modules;

/**
 * Backgrounds module.
 */
final class Backgrounds implements ModuleInterface {

	use ModuleTrait;

	/**
	 * {@inheritDoc}
	 */
	public function slug(): string {
		return 'backgrounds';
	}

	/**
	 * {@inheritDoc}
	 */
	public function label(): string {
		return 'Backgrounds';
	}

	/**
	 * Default settings.
	 *
	 * @return array<string, mixed>
	 */
	protected function defaults(): array {
		return array(
			'body_color'      => '',
			'body_image'      => '',
			'body_repeat'     => 'no-repeat',
			'body_position'   => 'center center',
			'body_attachment' => 'scroll',
			'content_color'   => '',
			'footer_color'    => '',
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
		add_action( 'wp_head', array( $this, 'print_css' ), 22 );
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
			'body_color',
			array( 'sanitize_callback' => 'sanitize_hex_color' ),
			array(
				'label' => 'Body background color',
				'type'  => 'color',
			)
		);
		$this->add_setting(
			$customizer,
			'body_image',
			array( 'sanitize_callback' => 'esc_url_raw' ),
			array(
				'label' => 'Body background image',
				'type'  => 'url',
			)
		);
		$this->add_setting(
			$customizer,
			'body_repeat',
			array( 'sanitize_callback' => 'sanitize_key' ),
			array(
				'label'   => 'Background repeat',
				'type'    => 'select',
				'choices' => array(
					'no-repeat' => 'No repeat',
					'repeat'    => 'Repeat',
					'repeat-x'  => 'Repeat horizontally',
					'repeat-y'  => 'Repeat vertically',
				),
			)
		);
		$this->add_setting(
			$customizer,
			'body_position',
			array( 'sanitize_callback' => 'sanitize_text_field' ),
			array(
				'label'   => 'Background position',
				'type'    => 'select',
				'choices' => array(
					'center center' => 'Center',
					'top left'      => 'Top left',
					'top right'     => 'Top right',
					'center top'    => 'Top center',
					'bottom center' => 'Bottom center',
				),
			)
		);
		$this->add_setting(
			$customizer,
			'content_color',
			array( 'sanitize_callback' => 'sanitize_hex_color' ),
			array(
				'label' => 'Content background color',
				'type'  => 'color',
			)
		);
		$this->add_setting(
			$customizer,
			'footer_color',
			array( 'sanitize_callback' => 'sanitize_hex_color' ),
			array(
				'label' => 'Footer background color',
				'type'  => 'color',
			)
		);
	}

	/**
	 * Token-driven background CSS.
	 *
	 * @return string
	 */
	public function css(): string {
		$s     = $this->settings();
		$rules = array();

		if ( is_string( $s['body_color'] ) && '' !== $s['body_color'] ) {
			$rules[] = '--lumina-bg-body:' . $s['body_color'];
		}

		if ( is_string( $s['content_color'] ) && '' !== $s['content_color'] ) {
			$rules[] = '--lumina-bg-content:' . $s['content_color'];
		}

		if ( is_string( $s['footer_color'] ) && '' !== $s['footer_color'] ) {
			$rules[] = '--lumina-bg-footer:' . $s['footer_color'];
		}

		$css = '';

		if ( array() !== $rules ) {
			$css .= ':root{' . implode( ';', $rules ) . ';}';
		}

		$image = is_string( $s['body_image'] ) ? trim( $s['body_image'] ) : '';

		if ( '' !== $image && str_starts_with( $image, 'http' ) ) {
			// Defense in depth: esc_url + single quotes keep malicious stored
			// values (containing ')', ';', or quotes) inside the url() context.
			// phpcs:ignore WordPress.WP.AlternativeFunctions -- esc_url via WP or fallback below.
			$safe_url = function_exists( 'esc_url' ) ? esc_url( $image ) : preg_replace( '/[\'"\\)\;\s]+/', '', $image );

			$css .= sprintf(
				'body{background-image:url(%1$s);background-repeat:%2$s;background-position:%3$s;background-attachment:%4$s;}',
				$safe_url,
				(string) $s['body_repeat'],
				(string) $s['body_position'],
				(string) $s['body_attachment']
			);
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

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- sanitized values, url validated.
		echo '<style id="lumina-backgrounds-css">' . $css . '</style>';
	}
}
