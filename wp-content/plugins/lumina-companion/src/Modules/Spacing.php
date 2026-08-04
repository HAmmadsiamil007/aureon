<?php
/**
 * Spacing — per-element spacing controls for the Lumina theme.
 *
 * Original implementation of the premium spacing feature category. Emits
 * token-driven CSS custom properties (--lumina-spacing-*) so every Lumina
 * component scales without hard-coded pixels.
 *
 * @package Lumina\Companion\Modules
 */

declare( strict_types=1 );

namespace Lumina\Companion\Modules;

/**
 * Spacing module.
 */
final class Spacing implements ModuleInterface {

	use ModuleTrait;

	/**
	 * {@inheritDoc}
	 */
	public function slug(): string {
		return 'spacing';
	}

	/**
	 * {@inheritDoc}
	 */
	public function label(): string {
		return 'Spacing';
	}

	/**
	 * Default spacing scale (px).
	 *
	 * @return array<string, mixed>
	 */
	protected function defaults(): array {
		return array(
			'container_width'  => '1280',
			'gutter'           => '24',
			'section_vertical' => '64',
			'card_padding'     => '24',
			'element_gap'      => '16',
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
			'container_width',
			array( 'sanitize_callback' => 'absint' ),
			array(
				'label' => 'Container width (px)',
				'type'  => 'number',
			)
		);
		$this->add_setting(
			$customizer,
			'gutter',
			array( 'sanitize_callback' => 'absint' ),
			array(
				'label' => 'Gutter (px)',
				'type'  => 'number',
			)
		);
		$this->add_setting(
			$customizer,
			'section_vertical',
			array( 'sanitize_callback' => 'absint' ),
			array(
				'label' => 'Section vertical spacing (px)',
				'type'  => 'number',
			)
		);
		$this->add_setting(
			$customizer,
			'card_padding',
			array( 'sanitize_callback' => 'absint' ),
			array(
				'label' => 'Card padding (px)',
				'type'  => 'number',
			)
		);
		$this->add_setting(
			$customizer,
			'element_gap',
			array( 'sanitize_callback' => 'absint' ),
			array(
				'label' => 'Element gap (px)',
				'type'  => 'number',
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

		return sprintf(
			':root{--lumina-spacing-container:%spx;--lumina-spacing-gutter:%spx;--lumina-spacing-section:%spx;--lumina-spacing-card:%spx;--lumina-spacing-gap:%spx;}',
			(int) $s['container_width'],
			(int) $s['gutter'],
			(int) $s['section_vertical'],
			(int) $s['card_padding'],
			(int) $s['element_gap']
		);
	}

	/**
	 * Print CSS in <head> (guarded).
	 *
	 * @return void
	 */
	public function print_css(): void {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- generated from validated ints.
		echo '<style id="lumina-spacing-css">' . $this->css() . '</style>';
	}

	/**
	 * Inject template data (site shell spacing).
	 *
	 * @param array<string, mixed> $data Template data.
	 * @param string               $slug Template slug.
	 * @return array<string, mixed>
	 */
	public function template_data( array $data, string $slug ): array {
		if ( 'header' === $slug || 'footer' === $slug ) {
			$data['spacing'] = $this->settings();
		}

		return $data;
	}
}
