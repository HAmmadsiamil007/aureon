<?php
/**
 * SecondaryNav — secondary navigation for the Lumina theme.
 *
 * Original implementation of the premium secondary-nav feature category.
 * Registers a secondary menu location and renders a slim secondary bar
 * through a public wp_nav_menu() call (original markup).
 *
 * @package Lumina\Companion\Modules
 */

declare( strict_types=1 );

namespace Lumina\Companion\Modules;

/**
 * SecondaryNav module.
 */
final class SecondaryNav implements ModuleInterface {

	use ModuleTrait;

	/**
	 * {@inheritDoc}
	 */
	public function slug(): string {
		return 'secondary-nav';
	}

	/**
	 * {@inheritDoc}
	 */
	public function label(): string {
		return 'Secondary Navigation';
	}

	/**
	 * Default secondary nav settings.
	 *
	 * @return array<string, mixed>
	 */
	protected function defaults(): array {
		return array(
			'enabled' => false,
			'label'   => 'Secondary',
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
		add_action( 'after_setup_theme', array( $this, 'register_menu_location' ), 20 );
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		add_action( 'lumina_after_header', array( $this, 'render_bar' ), 10 );
	}

	/**
	 * Register the `secondary` menu location (guarded).
	 *
	 * @return void
	 */
	public function register_menu_location(): void {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		if ( ! function_exists( 'register_nav_menus' ) ) {
			return;
		}

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		register_nav_menus(
			array(
				'secondary' => 'Secondary menu (Lumina Companion)',
			)
		);
	}

	/**
	 * Render the secondary bar (guarded; original markup).
	 *
	 * @return void
	 */
	public function render_bar(): void {
		$s = $this->settings();

		if ( ! (bool) $s['enabled'] ) {
			return;
		}

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		if ( ! function_exists( 'has_nav_menu' ) || ! has_nav_menu( 'secondary' ) ) {
			return;
		}

		echo '<div class="lumina-secondary-nav" role="navigation" aria-label="' . esc_attr( (string) $s['label'] ) . '">';
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		// `container => false` is documented wp_nav_menu behavior; the
		// phpstan-wordpress stub types container as string (see phpstan.neon).
		wp_nav_menu(
			array(
				'theme_location' => 'secondary',
				'container'      => false,
				'menu_class'     => 'lumina-secondary-nav__list',
				'depth'          => 1,
				'fallback_cb'    => '__return_false',
			)
		);
		echo '</div>';
	}

	/**
	 * Secondary nav CSS.
	 *
	 * @return string
	 */
	public function css(): string {
		return '.lumina-secondary-nav{background:var(--lumina-color-bg);border-bottom:1px solid var(--lumina-color-border)}.lumina-secondary-nav__list{display:flex;gap:var(--lumina-spacing-gap,1rem);list-style:none;margin:0;padding:.5rem var(--lumina-spacing-gutter,1rem)}.lumina-secondary-nav__list a{color:var(--lumina-color-fg);text-decoration:none}.lumina-secondary-nav__list a:focus-visible{outline:2px solid var(--lumina-color-accent);outline-offset:2px}';
	}

	/**
	 * Inject secondary nav data.
	 *
	 * @param array<string, mixed> $data Template data.
	 * @param string               $slug Template slug.
	 * @return array<string, mixed>
	 */
	public function template_data( array $data, string $slug ): array {
		$s = $this->settings();

		if ( 'header' === $slug && (bool) $s['enabled'] ) {
			$data['secondary_nav'] = array(
				'enabled' => true,
				'label'   => (string) $s['label'],
			);
		}

		return $data;
	}
}
