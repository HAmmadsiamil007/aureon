<?php
/**
 * PageHeader — page header module for the Lumina theme.
 *
 * Original implementation of the premium page-header feature category.
 * Renders a configurable page header region (title, subtitle, breadcrumb
 * slot) above the content via the Lumina composition pipeline. Data flows
 * through the `lumina_template_data` filter — never direct globals.
 *
 * @package Lumina\Companion\Modules
 */

declare( strict_types=1 );

namespace Lumina\Companion\Modules;

/**
 * PageHeader module.
 */
final class PageHeader implements ModuleInterface {

	use ModuleTrait;

	/**
	 * {@inheritDoc}
	 */
	public function slug(): string {
		return 'page-header';
	}

	/**
	 * {@inheritDoc}
	 */
	public function label(): string {
		return 'Page Header';
	}

	/**
	 * Default page header settings.
	 *
	 * @return array<string, mixed>
	 */
	protected function defaults(): array {
		return array(
			'enabled'          => true,
			'show_breadcrumbs' => true,
			'align'            => 'left',
			'background'       => '',
			'padding'          => '48',
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
			'enabled',
			array( 'sanitize_callback' => 'rest_sanitize_boolean' ),
			array(
				'label' => 'Enable page header',
				'type'  => 'checkbox',
			)
		);
		$this->add_setting(
			$customizer,
			'show_breadcrumbs',
			array( 'sanitize_callback' => 'rest_sanitize_boolean' ),
			array(
				'label' => 'Show breadcrumbs',
				'type'  => 'checkbox',
			)
		);
		$this->add_setting(
			$customizer,
			'align',
			array(
				'sanitize_callback' => static fn( mixed $v ): string => in_array( $v, array( 'left', 'center', 'right' ), true ) ? $v : 'left',
			),
			array(
				'label'   => 'Alignment',
				'type'    => 'select',
				'choices' => array(
					'left'   => 'Left',
					'center' => 'Center',
					'right'  => 'Right',
				),
			)
		);
		$this->add_setting(
			$customizer,
			'padding',
			array( 'sanitize_callback' => 'absint' ),
			array(
				'label' => 'Padding (px)',
				'type'  => 'number',
			)
		);
	}

	/**
	 * Page header CSS.
	 *
	 * @return string
	 */
	public function css(): string {
		$s = $this->settings();

		$align = in_array( (string) $s['align'], array( 'left', 'center', 'right' ), true )
			? (string) $s['align']
			: 'left';

		$background = $this->sanitize_text( $s['background'] );
		$bg_css     = '' !== $background ? 'background-color:' . $background . ';' : '';

		return sprintf(
			'.lumina-page-header{text-align:%s;padding:%spx 0;%s}',
			$align,
			(int) $s['padding'],
			$bg_css
		);
	}

	/**
	 * Print CSS in <head> (guarded).
	 *
	 * @return void
	 */
	public function print_css(): void {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- generated from sanitized values.
		echo '<style id="lumina-page-header-css">' . $this->css() . '</style>';
	}

	/**
	 * The current page title (WP context) with a safe WP-free fallback.
	 *
	 * @return string
	 */
	private function the_title(): string {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		if ( function_exists( 'wp_get_document_title' ) ) {
			return wp_get_document_title();
		}

		return '';
	}

	/**
	 * Inject page header data into the composition pipeline.
	 *
	 * @param array<string, mixed> $data Template data.
	 * @param string               $slug Template slug.
	 * @return array<string, mixed>
	 */
	public function template_data( array $data, string $slug ): array {
		$s = $this->settings();

		if ( ! (bool) $s['enabled'] ) {
			return $data;
		}

		$data['page_header'] = array(
			'enabled'          => true,
			'show_breadcrumbs' => (bool) $s['show_breadcrumbs'],
			'align'            => (string) $s['align'],
			'padding'          => (int) $s['padding'],
			'title'            => (string) ( $data['title'] ?? $this->the_title() ),
			'text'             => (string) ( $data['text'] ?? '' ),
		);

		return $data;
	}
}
