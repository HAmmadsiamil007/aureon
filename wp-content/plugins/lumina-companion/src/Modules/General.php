<?php
/**
 * General — global layout and site settings for the Lumina theme.
 *
 * Original implementation of the premium general feature category. Central
 * container/layout/site controls consumed by the composition pipeline:
 * container width, content/sidebar layout, site tagline visibility, and
 * scroll-to-top toggle.
 *
 * @package Lumina\Companion\Modules
 */

declare( strict_types=1 );

namespace Lumina\Companion\Modules;

/**
 * General module.
 */
final class General implements ModuleInterface {

	use ModuleTrait;

	/**
	 * {@inheritDoc}
	 */
	public function slug(): string {
		return 'general';
	}

	/**
	 * {@inheritDoc}
	 */
	public function label(): string {
		return 'General';
	}

	/**
	 * Default settings.
	 *
	 * @return array<string, mixed>
	 */
	protected function defaults(): array {
		return array(
			'layout'           => 'full-width',
			'container'        => 'wide',
			'show_tagline'     => true,
			'show_back_to_top' => true,
			'content_width'    => '1280',
			'sidebar'          => 'right',
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
		add_action( 'wp_head', array( $this, 'print_css' ), 26 );
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		add_filter( 'body_class', array( $this, 'body_class' ), 20 );
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
			'layout',
			array( 'sanitize_callback' => 'sanitize_key' ),
			array(
				'label'   => 'Site layout',
				'type'    => 'select',
				'choices' => array(
					'full-width' => 'Full width',
					'boxed'      => 'Boxed',
				),
			)
		);
		$this->add_setting(
			$customizer,
			'container',
			array( 'sanitize_callback' => 'sanitize_key' ),
			array(
				'label'   => 'Container width',
				'type'    => 'select',
				'choices' => array(
					'wide'      => 'Wide',
					'contained' => 'Contained',
				),
			)
		);
		$this->add_setting(
			$customizer,
			'sidebar',
			array( 'sanitize_callback' => 'sanitize_key' ),
			array(
				'label'   => 'Sidebar layout',
				'type'    => 'select',
				'choices' => array(
					'none'  => 'No sidebar',
					'right' => 'Right sidebar',
					'left'  => 'Left sidebar',
				),
			)
		);
		$this->add_setting(
			$customizer,
			'show_tagline',
			array( 'sanitize_callback' => 'rest_sanitize_boolean' ),
			array(
				'label' => 'Show tagline',
				'type'  => 'checkbox',
			)
		);
		$this->add_setting(
			$customizer,
			'show_back_to_top',
			array( 'sanitize_callback' => 'rest_sanitize_boolean' ),
			array(
				'label' => 'Show back-to-top',
				'type'  => 'checkbox',
			)
		);
	}

	/**
	 * Token-driven layout CSS.
	 *
	 * @return string
	 */
	public function css(): string {
		$s = $this->settings();

		$rules   = array();
		$rules[] = '--lumina-container:' . (string) $s['content_width'] . 'px';

		if ( 'boxed' === $s['layout'] ) {
			$rules[] = '--lumina-layout:boxed';
		}

		$css = ':root{' . implode( ';', $rules ) . ';}';

		if ( 'none' === $s['sidebar'] ) {
			$css .= '.lumina-sidebar{display:none;}';
		} elseif ( 'left' === $s['sidebar'] ) {
			$css .= '.lumina-content-wrap{flex-direction:row-reverse;}';
		}

		return $css;
	}

	/**
	 * Body classes (guarded).
	 *
	 * @param array<int, string> $classes Existing classes.
	 * @return array<int, string>
	 */
	public function body_class( array $classes ): array {
		$s = $this->settings();

		$classes[] = 'lumina-layout-' . (string) $s['layout'];
		$classes[] = 'lumina-container-' . (string) $s['container'];
		$classes[] = 'lumina-sidebar-' . (string) $s['sidebar'];

		if ( ! (bool) $s['show_back_to_top'] ) {
			$classes[] = 'lumina-no-back-to-top';
		}

		return $classes;
	}

	/**
	 * Print CSS in <head> (guarded).
	 *
	 * @return void
	 */
	public function print_css(): void {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- generated from sanitized values.
		echo '<style id="lumina-general-css">' . $this->css() . '</style>';
	}

	/**
	 * Inject layout settings into shell compositions.
	 *
	 * @param array<string, mixed> $data Template data.
	 * @param string               $slug Template slug.
	 * @return array<string, mixed>
	 */
	public function template_data( array $data, string $slug ): array {
		if ( in_array( $slug, array( 'header', 'footer', 'home', 'landing' ), true ) ) {
			$data['general'] = array(
				'show_tagline'     => (bool) $this->setting( 'show_tagline', true ),
				'show_back_to_top' => (bool) $this->setting( 'show_back_to_top', true ),
				'layout'           => (string) $this->setting( 'layout', 'full-width' ),
			);
		}

		return $data;
	}
}
