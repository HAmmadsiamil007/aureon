<?php
/**
 * DisableElements — per-page / global element disable controls.
 *
 * Original implementation of the premium disable-elements feature category.
 * Lets users hide the site header, footer, and page header per post/page
 * (via post meta) or globally (via options). The Lumina shell reads the
 * flags through the composition pipeline and skips the regions.
 *
 * @package Lumina\Companion\Modules
 */

declare( strict_types=1 );

namespace Lumina\Companion\Modules;

/**
 * DisableElements module.
 */
final class DisableElements implements ModuleInterface {

	use ModuleTrait;

	/**
	 * {@inheritDoc}
	 */
	public function slug(): string {
		return 'disable-elements';
	}

	/**
	 * {@inheritDoc}
	 */
	public function label(): string {
		return 'Disable Elements';
	}

	/**
	 * Default settings.
	 *
	 * @return array<string, mixed>
	 */
	protected function defaults(): array {
		return array(
			'header'     => false,
			'footer'     => false,
			'page_title' => false,
		);
	}

	/**
	 * Register WP hooks (guarded).
	 *
	 * Customizer settings are driven by the plugin's module loop (Plugin::
	 * register_customizer), so no standalone action binding is needed here.
	 *
	 * @return void
	 */
	public function register(): void {
	}

	/**
	 * Customizer settings (global defaults).
	 *
	 * @param mixed $customizer WP_Customize_Manager.
	 * @return void
	 */
	public function customizer( $customizer ): void {
		$fields = array(
			'header'     => 'Disable site header globally',
			'footer'     => 'Disable site footer globally',
			'page_title' => 'Disable page title globally',
		);

		foreach ( $fields as $key => $label ) {
			$this->add_setting(
				$customizer,
				$key,
				array( 'sanitize_callback' => 'rest_sanitize_boolean' ),
				array(
					'label' => $label,
					'type'  => 'checkbox',
				)
			);
		}
	}

	/**
	 * Per-post meta: should an element be disabled for the current post?
	 *
	 * @param string $element Element key (header|footer|page_title).
	 * @return bool
	 */
	private function per_post_disabled( string $element ): bool {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		if ( ! function_exists( 'get_post_meta' ) || ! function_exists( 'get_the_ID' ) ) {
			return false;
		}

		$id = (int) get_the_ID();

		if ( $id <= 0 ) {
			return false;
		}

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		$meta = get_post_meta( $id, 'lumina_disable_' . $element, true );

		return (bool) $meta;
	}

	/**
	 * Inject disable flags into the template data pipeline.
	 *
	 * @param array<string, mixed> $data Template data.
	 * @param string               $slug Template slug.
	 * @return array<string, mixed>
	 */
	public function template_data( array $data, string $slug ): array {
		$settings = $this->settings();

		$data['disable'] = array(
			'header'     => (bool) ( $settings['header'] ?? false ) || $this->per_post_disabled( 'header' ),
			'footer'     => (bool) ( $settings['footer'] ?? false ) || $this->per_post_disabled( 'footer' ),
			'page_title' => (bool) ( $settings['page_title'] ?? false ) || $this->per_post_disabled( 'page_title' ),
		);

		return $data;
	}
}
