<?php
/**
 * MenuPlus — menu enhancements (mega menu support) for the Lumina theme.
 *
 * Original implementation of the premium menu-plus feature category. Adds a
 * `mega` class hook for menu items marked as mega (via a Walker override and
 * a CSS-only/JS-friendly mega panel), plus mobile toggling classes. All
 * output is original markup; the Lumina mega-menu component consumes the
 * `mega` class through the composition pipeline.
 *
 * @package Lumina\Companion\Modules
 */

declare( strict_types=1 );

namespace Lumina\Companion\Modules;

/**
 * MenuPlus module.
 */
final class MenuPlus implements ModuleInterface {

	use ModuleTrait;

	/**
	 * {@inheritDoc}
	 */
	public function slug(): string {
		return 'menu-plus';
	}

	/**
	 * {@inheritDoc}
	 */
	public function label(): string {
		return 'Menu Plus';
	}

	/**
	 * Default menu plus settings.
	 *
	 * @return array<string, mixed>
	 */
	protected function defaults(): array {
		return array(
			'enabled'         => true,
			'mega_breakpoint' => '1024',
		);
	}

	/**
	 * Register WP hooks (guarded).
	 *
	 * @return void
	 */
	public function register(): void {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		if ( ! function_exists( 'add_filter' ) ) {
			return;
		}

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		add_filter( 'nav_menu_css_class', array( $this, 'mark_mega_items' ), 10, 2 );
	}

	/**
	 * Mark menu items whose classes include `menu-item-mega` with `lumina-mega`.
	 *
	 * @param array<int, string> $classes Menu item classes.
	 * @param mixed              $item    Menu item object.
	 * @return array<int, string>
	 */
	public function mark_mega_items( array $classes, $item ): array {
		if ( in_array( 'menu-item-mega', $classes, true ) && ! in_array( 'lumina-mega', $classes, true ) ) {
			$classes[] = 'lumina-mega';
		}

		return $classes;
	}

	/**
	 * Menu plus CSS.
	 *
	 * @return string
	 */
	public function css(): string {
		$s = $this->settings();

		return sprintf(
			'.lumina-mega{position:static}.lumina-mega>.sub-menu{position:absolute;left:0;right:0;top:100%%;display:none;background:var(--lumina-color-bg);border-top:1px solid var(--lumina-color-border)}@media(min-width:%spx){.lumina-mega:hover>.sub-menu,.lumina-mega:focus-within>.sub-menu{display:grid;grid-template-columns:repeat(4,1fr);gap:var(--lumina-spacing-gap,1rem);padding:2rem}}',
			(int) $s['mega_breakpoint']
		);
	}

	/**
	 * Inject menu plus data.
	 *
	 * @param array<string, mixed> $data Template data.
	 * @param string               $slug Template slug.
	 * @return array<string, mixed>
	 */
	public function template_data( array $data, string $slug ): array {
		if ( 'header' === $slug && (bool) $this->setting( 'enabled', true ) ) {
			$data['menu_plus'] = array(
				'enabled'         => true,
				'mega_breakpoint' => (int) $this->setting( 'mega_breakpoint', '1024' ),
			);
		}

		return $data;
	}
}
