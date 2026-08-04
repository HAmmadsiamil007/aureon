<?php
/**
 * Copyright — footer copyright text and links for the Lumina theme.
 *
 * Original implementation of the premium copyright feature category.
 * Feeds the Lumina footer/copyright components through the composition
 * pipeline, with a sensible default and full output escaping.
 *
 * @package Lumina\Companion\Modules
 */

declare( strict_types=1 );

namespace Lumina\Companion\Modules;

/**
 * Copyright module.
 */
final class Copyright implements ModuleInterface {

	use ModuleTrait;

	/**
	 * {@inheritDoc}
	 */
	public function slug(): string {
		return 'copyright';
	}

	/**
	 * {@inheritDoc}
	 */
	public function label(): string {
		return 'Copyright';
	}

	/**
	 * Default settings.
	 *
	 * @return array<string, mixed>
	 */
	protected function defaults(): array {
		return array(
			'text'   => '',
			'remove' => false,
			'logo'   => '',
			'links'  => '',
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
		add_action( 'wp_head', array( $this, 'print_css' ), 28 );
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
			'text',
			array( 'sanitize_callback' => 'sanitize_text_field' ),
			array(
				'label' => 'Copyright text',
				'type'  => 'text',
			)
		);
		$this->add_setting(
			$customizer,
			'remove',
			array( 'sanitize_callback' => 'rest_sanitize_boolean' ),
			array(
				'label' => 'Remove copyright bar',
				'type'  => 'checkbox',
			)
		);
	}

	/**
	 * CSS for the copyright bar (token-driven).
	 *
	 * @return string
	 */
	public function css(): string {
		return '.lumina-copyright{text-align:center;font-size:.9em;color:var(--lumina-color-text-muted,inherit);padding:var(--lumina-space-4,1rem) var(--lumina-space-4,1rem);}';
	}

	/**
	 * Print CSS in <head> (guarded).
	 *
	 * @return void
	 */
	public function print_css(): void {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static string.
		echo '<style id="lumina-copyright-css">' . $this->css() . '</style>';
	}

	/**
	 * Inject copyright data into the footer composition.
	 *
	 * @param array<string, mixed> $data Template data.
	 * @param string               $slug Template slug.
	 * @return array<string, mixed>
	 */
	public function template_data( array $data, string $slug ): array {
		if ( 'footer' === $slug ) {
			$settings = $this->settings();
			$text     = is_string( $settings['text'] ) ? $settings['text'] : '';

			if ( '' === $text ) {
				$site = isset( $data['site_name'] ) && is_string( $data['site_name'] ) ? $data['site_name'] : 'Lumina';
				$text = '© ' . gmdate( 'Y' ) . ' ' . $site;
			}

			$data['copyright'] = array(
				'text'   => $text,
				'remove' => (bool) $settings['remove'],
			);
		}

		return $data;
	}
}
