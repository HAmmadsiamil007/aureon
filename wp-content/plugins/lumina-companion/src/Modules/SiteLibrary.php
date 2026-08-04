<?php
/**
 * SiteLibrary — site/template library scaffold for the Lumina theme.
 *
 * Original implementation of the premium site-library feature category.
 * Provides a guarded registry of site presets (import manifests) and a REST
 * endpoint that returns the available presets. No third-party content is
 * bundled; presets are user-supplied JSON manifests placed in the theme.
 *
 * @package Lumina\Companion\Modules
 */

declare( strict_types=1 );

namespace Lumina\Companion\Modules;

/**
 * SiteLibrary module.
 */
final class SiteLibrary implements ModuleInterface {

	use ModuleTrait;

	/**
	 * {@inheritDoc}
	 */
	public function slug(): string {
		return 'site-library';
	}

	/**
	 * {@inheritDoc}
	 */
	public function label(): string {
		return 'Site Library';
	}

	/**
	 * Default settings.
	 *
	 * @return array<string, mixed>
	 */
	protected function defaults(): array {
		return array(
			'enabled'    => true,
			'endpoint'   => 'lumina/v1/site-library',
			'preset_dir' => '',
		);
	}

	/**
	 * Register the REST route (guarded).
	 *
	 * @return void
	 */
	public function register(): void {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		if ( ! function_exists( 'add_action' ) ) {
			return;
		}

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		add_action( 'rest_api_init', array( $this, 'register_route' ) );
	}

	/**
	 * Register the presets list endpoint.
	 *
	 * @return void
	 */
	public function register_route(): void {
		// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals -- WP core API in guarded calls.
		if ( ! function_exists( 'register_rest_route' ) ) {
			return;
		}

		$endpoint = (string) $this->setting( 'endpoint', 'lumina/v1/site-library' );

		register_rest_route(
			'lumina/v1',
			'site-library',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'route_presets' ),
				'permission_callback' => static fn(): bool => current_user_can( 'edit_theme_options' ),
			)
		);
		// phpcs:enable WordPress.NamingConventions.PrefixAllGlobals
	}

	/**
	 * REST callback: list available presets.
	 *
	 * @return array<string, mixed>
	 */
	public function route_presets(): array {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		$dir = function_exists( 'get_stylesheet_directory' )
			? get_stylesheet_directory() . '/site-library'
			: '';

		$presets = array();

		if ( '' !== $dir && is_dir( $dir ) ) {
			$found = glob( $dir . '/*.json' );

			foreach ( is_array( $found ) ? $found : array() as $file ) {
				$presets[] = array(
					'slug'  => basename( $file, '.json' ),
					'label' => basename( $file, '.json' ),
				);
			}
		}

		return array(
			'presets' => $presets,
		);
	}

	/**
	 * Inject site library data.
	 *
	 * @param array<string, mixed> $data Template data.
	 * @param string               $slug Template slug.
	 * @return array<string, mixed>
	 */
	public function template_data( array $data, string $slug ): array {
		$data['site_library'] = array(
			'enabled'  => (bool) $this->setting( 'enabled', true ),
			'endpoint' => (string) $this->setting( 'endpoint', 'lumina/v1/site-library' ),
		);

		return $data;
	}
}
