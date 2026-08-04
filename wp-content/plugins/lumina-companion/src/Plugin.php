<?php
/**
 * Plugin — Lumina Companion main class.
 *
 * Original companion plugin for the Lumina theme. Provides premium feature
 * categories (spacing, typography, page header, secondary nav, menu plus,
 * sections, WooCommerce styling) as 100% original code. Every module
 * registers only when the Lumina theme is active — the plugin degrades to a
 * no-op on any other theme.
 *
 * @package Lumina\Companion
 */

declare( strict_types=1 );

namespace Lumina\Companion;

use Lumina\Companion\Modules\SiteLibrary;
use Lumina\Companion\Modules\MenuPlus;
use Lumina\Companion\Modules\PageHeader;
use Lumina\Companion\Modules\SecondaryNav;
use Lumina\Companion\Modules\Sections;
use Lumina\Companion\Modules\Spacing;
use Lumina\Companion\Modules\Typography;
use Lumina\Companion\Modules\WooCommerce;

/**
 * Singleton application root.
 */
final class Plugin {

	/**
	 * Singleton instance.
	 *
	 * @var Plugin|null
	 */
	private static ?Plugin $instance = null;

	/**
	 * Module instances (slug => module).
	 *
	 * @var array<string, object>
	 */
	private array $modules = array();

	/**
	 * Whether the Lumina theme is active.
	 *
	 * @var bool|null
	 */
	private ?bool $theme_active = null;

	/**
	 * Private constructor — singleton.
	 */
	private function __construct() {
	}

	/**
	 * Singleton accessor.
	 *
	 * @return Plugin
	 */
	public static function instance(): Plugin {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Boot the plugin: register modules and wire WordPress hooks.
	 *
	 * @return void
	 */
	public function boot(): void {
		// PHPCS: no prefixable WP functions used on purpose — this file is
		// loaded before WP's plugin API in CLI/smoke contexts.
		$this->register_modules();

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		if ( function_exists( 'add_action' ) ) {
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
			add_action( 'customize_register', array( $this, 'register_customizer' ), 20 );
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 30 );
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
			add_action( 'wp_body_open', array( $this, 'render_page_header_region' ), 15 );
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
			add_filter( 'lumina_template_data', array( $this, 'inject_template_data' ), 10, 2 );
		}
	}

	/**
	 * Whether the Lumina theme is the active theme.
	 *
	 * @return bool
	 */
	public function is_theme_active(): bool {
		if ( null !== $this->theme_active ) {
			return $this->theme_active;
		}

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		$template = function_exists( 'get_template' ) ? (string) get_template() : '';

		// WP-free / CLI: theme cannot be detected — assume inactive.
		$this->theme_active = '' !== $template && 'lumina' === $template;

		return $this->theme_active;
	}

	/**
	 * Register all companion modules.
	 *
	 * @return void
	 */
	private function register_modules(): void {
		$modules = array(
			'spacing'       => new Spacing(),
			'typography'    => new Typography(),
			'page-header'   => new PageHeader(),
			'secondary-nav' => new SecondaryNav(),
			'menu-plus'     => new MenuPlus(),
			'sections'      => new Sections(),
			'site-library'  => new SiteLibrary(),
			'woocommerce'   => new WooCommerce(),
		);

		foreach ( $modules as $slug => $module ) {
			$this->modules[ $slug ] = $module;

			if ( method_exists( $module, 'register' ) ) {
				$module->register();
			}
		}
	}

	/**
	 * Customizer callback — expose module settings.
	 *
	 * @param mixed $customizer WP_Customize_Manager.
	 * @return void
	 */
	public function register_customizer( $customizer ): void {
		if ( ! $this->is_theme_active() ) {
			return;
		}

		foreach ( $this->modules as $module ) {
			if ( method_exists( $module, 'customizer' ) ) {
				$module->customizer( $customizer );
			}
		}
	}

	/**
	 * Frontend styles — enqueue the companion stylesheet (original CSS).
	 *
	 * @return void
	 */
	public function enqueue_styles(): void {
		if ( ! $this->is_theme_active() ) {
			return;
		}

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		if ( ! function_exists( 'wp_enqueue_style' ) ) {
			return;
		}

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals -- WP core function.
		wp_enqueue_style(
			'lumina-companion',
			LUMINA_COMPANION_URL . 'assets/lumina-companion.css',
			array(),
			LUMINA_COMPANION_VERSION
		);
	}

	/**
	 * Page header region — render the page header component above the content.
	 *
	 * @return void
	 */
	public function render_page_header_region(): void {
		if ( ! $this->is_theme_active() ) {
			return;
		}

		if ( ! function_exists( 'apply_filters' ) || ! class_exists( \Lumina\Core\Templates\View::class ) ) {
			return;
		}

		$data = apply_filters( 'lumina_template_data', array(), 'page-header' );
		$html = \Lumina\Core\Templates\View::compose( 'page-header', $data );

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- composition HTML escaped at the leaf.
		echo $html;
	}

	/**
	 * Inject companion settings into the template data pipeline.
	 *
	 * @param array<string, mixed> $data Template data.
	 * @param string               $slug Template slug.
	 * @return array<string, mixed>
	 */
	public function inject_template_data( array $data, string $slug ): array {
		foreach ( $this->modules as $module ) {
			if ( method_exists( $module, 'template_data' ) ) {
				$data = $module->template_data( $data, $slug );
			}
		}

		return $data;
	}

	/**
	 * Registered module slugs.
	 *
	 * @return list<string>
	 */
	public function module_slugs(): array {
		return array_keys( $this->modules );
	}

	/**
	 * A registered module by slug (null when missing).
	 *
	 * @param string $slug Module slug.
	 * @return object|null
	 */
	public function module( string $slug ): ?object {
		return $this->modules[ $slug ] ?? null;
	}

	/**
	 * Collect inline CSS from every module (token-driven, original).
	 *
	 * @return string
	 */
	public function inline_css(): string {
		$css = '';

		foreach ( $this->modules as $module ) {
			if ( method_exists( $module, 'css' ) ) {
				$css .= (string) $module->css();
			}
		}

		return $css;
	}
}
