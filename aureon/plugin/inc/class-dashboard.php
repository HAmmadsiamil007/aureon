<?php
/**
 * Build our admin dashboard.
 *
 * @package Aureon Studio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * This class adds premium sections to our Dashboard.
 */
class Aureon_Pro_Dashboard {
	/**
	 * Class instance.
	 *
	 * @access private
	 * @var $instance Class instance.
	 */
	private static $instance;

	/**
	 * Initiator
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Get started.
	 */
	public function __construct() {
		add_action( 'after_setup_theme', array( $this, 'setup' ) );
	}

	/**
	 * Add our actions and require old Dashboard files if we need them.
	 */
	public function setup() {
		// Load our old dashboard if we're using an old version of Aureon.
		if ( ! class_exists( 'Aureon_Dashboard' ) ) {
			if ( is_admin() ) {
				require_once AUREON_STUDIO_DIR_PATH . 'inc/legacy/dashboard.php';
				require_once AUREON_STUDIO_DIR_PATH . 'inc/legacy/import-export.php';
				require_once AUREON_STUDIO_DIR_PATH . 'inc/legacy/reset.php';
				require_once AUREON_STUDIO_DIR_PATH . 'inc/legacy/activation.php';
			}

			return;
		}

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'aureon_admin_dashboard', array( $this, 'module_list' ), 8 );
		add_action( 'aureon_admin_dashboard', array( $this, 'import_export' ), 50 );
		add_action( 'aureon_admin_dashboard', array( $this, 'reset' ), 100 );
	}

	/**
	 * Get data for all of our pro modules.
	 */
	public static function get_modules() {
		$modules = array(
			'Backgrounds' => array(
				'title' => __( 'Backgrounds', 'aureon-studio' ),
				'description' => __( 'Set background images for various HTML elements.', 'aureon-studio' ),
				'key' => 'aureon_package_backgrounds',
				'settings' => 'aureon_background_settings',
				'isActive' => 'activated' === get_option( 'aureon_package_backgrounds', false ),
				'exportable' => true,
			),
			'Blog' => array(
				'title' => __( 'Blog', 'aureon-studio' ),
				'description' => __( 'Set blog options like infinite scroll, masonry layouts and more.', 'aureon-studio' ),
				'key' => 'aureon_package_blog',
				'settings' => 'aureon_blog_settings',
				'isActive' => 'activated' === get_option( 'aureon_package_blog', false ),
				'exportable' => true,
			),
			'Colors' => array(
				'title' => __( 'Colors', 'aureon-studio' ),
				'key' => 'aureon_package_colors',
				'isActive' => 'activated' === get_option( 'aureon_package_colors', false ),
			),
			'Copyright' => array(
				'title' => __( 'Copyright', 'aureon-studio' ),
				'description' => __( 'Set a custom copyright message in your footer.', 'aureon-studio' ),
				'key' => 'aureon_package_copyright',
				'settings' => 'copyright',
				'isActive' => 'activated' === get_option( 'aureon_package_copyright', false ),
				'exportable' => true,
			),
			'Disable Elements' => array(
				'title' => __( 'Disable Elements', 'aureon-studio' ),
				'description' => __( 'Disable default theme elements on specific pages or inside a Layout Element.', 'aureon-studio' ),
				'key' => 'aureon_package_disable_elements',
				'isActive' => 'activated' === get_option( 'aureon_package_disable_elements', false ),
			),
			'Elements' => array(
				'title' => __( 'Elements', 'aureon-studio' ),
				'description' => __( 'Use our block editor theme builder, build advanced HTML hooks, and gain more Layout control.', 'aureon-studio' ),
				'key' => 'aureon_package_elements',
				'isActive' => 'activated' === get_option( 'aureon_package_elements', false ),
			),
			'Font Library' => array(
				'title' => __( 'Font Library', 'aureon-studio' ),
				'description' => __( 'Download and localize fonts from the Google Fonts library.', 'aureon-studio' ),
				'key' => 'aureon_package_font_library',
				'isActive' => 'activated' === get_option( 'aureon_package_font_library', false ),
			),
			'Hooks' => array(
				'title' => __( 'Hooks', 'aureon-studio' ),
				'description' => __( 'This module has been deprecated. Please use Elements instead.', 'aureon-studio' ),
				'key' => 'aureon_package_hooks',
				'settings' => 'aureon_hooks',
				'isActive' => 'activated' === get_option( 'aureon_package_hooks', false ),
				'exportable' => true,
			),
			'Menu Plus' => array(
				'title' => __( 'Menu Plus', 'aureon-studio' ),
				'description' => __( 'Set up a mobile header, sticky navigation or off-canvas panel.', 'aureon-studio' ),
				'key' => 'aureon_package_menu_plus',
				'settings' => 'aureon_menu_plus_settings',
				'isActive' => 'activated' === get_option( 'aureon_package_menu_plus', false ),
				'exportable' => true,
			),
			'Page Header' => array(
				'title' => __( 'Page Header', 'aureon-studio' ),
				'description' => __( 'This module has been deprecated. Please use Elements instead.', 'aureon-studio' ),
				'key' => 'aureon_package_page_header',
				'settings' => 'aureon_page_header_settings',
				'isActive' => 'activated' === get_option( 'aureon_package_page_header', false ),
				'exportable' => true,
			),
			'Secondary Nav' => array(
				'title' => __( 'Secondary Nav', 'aureon-studio' ),
				'description' => __( 'Add a fully-featured secondary navigation to your site.', 'aureon-studio' ),
				'key' => 'aureon_package_secondary_nav',
				'settings' => 'aureon_secondary_nav_settings',
				'isActive' => 'activated' === get_option( 'aureon_package_secondary_nav', false ),
				'exportable' => true,
			),
			'Sections' => array(
				'title' => __( 'Sections', 'aureon-studio' ),
				'description' => __( 'This module has been deprecated. Please consider using our GenerateBlocks plugin instead.', 'aureon-studio' ),
				'key' => 'aureon_package_sections',
				'isActive' => 'activated' === get_option( 'aureon_package_sections', false ),
			),
			'Spacing' => array(
				'title' => __( 'Spacing', 'aureon-studio' ),
				'description' => __( 'Set the padding and overall spacing of your theme elements.', 'aureon-studio' ),
				'key' => 'aureon_package_spacing',
				'settings' => 'aureon_spacing_settings',
				'isActive' => 'activated' === get_option( 'aureon_package_spacing', false ),
				'exportable' => true,
			),
			'Typography' => array(
				'title' => __( 'Typography', 'aureon-studio' ),
				'description' => __( 'This module has been deprecated. Switch to our dynamic typography system in Customize > General instead.', 'aureon-studio' ),
				'key' => 'aureon_package_typography',
				'isActive' => 'activated' === get_option( 'aureon_package_typography', false ),
			),
			'WooCommerce' => array(
				'title' => __( 'WooCommerce', 'aureon-studio' ),
				'description' => __( 'Add colors, typography, and layout options to your WooCommerce store.', 'aureon-studio' ),
				'key' => 'aureon_package_woocommerce',
				'settings' => 'aureon_woocommerce_settings',
				'isActive' => 'activated' === get_option( 'aureon_package_woocommerce', false ),
				'exportable' => true,
			),
		);

		if ( function_exists( 'aureon_is_using_dynamic_typography' ) && aureon_is_using_dynamic_typography() ) {
			unset( $modules['Typography'] );
		}

		if ( version_compare( aureon_premium_get_theme_version(), '3.1.0-alpha.1', '>=' ) ) {
			unset( $modules['Colors'] );
		}

		$deprecated_modules = apply_filters(
			'aureon_premium_deprecated_modules',
			array(
				'Page Header',
				'Hooks',
				'Sections',
			)
		);

		foreach ( $deprecated_modules as $deprecated_module ) {
			if ( isset( $modules[ $deprecated_module ] ) ) {
				$modules[ $deprecated_module ]['deprecated'] = true;
			}
		}

		ksort( $modules );

		return $modules;
	}

	/**
	 * Get modules that can have their settings exported and imported.
	 */
	public static function get_exportable_modules() {
		$modules = array(
			'Core' => array(
				'settings' => 'aureon_settings',
				'title' => __( 'Core', 'aureon-studio' ),
				'isActive' => true,
			),
		);

		foreach ( self::get_modules() as $key => $data ) {
			if ( ! empty( $data['exportable'] ) && $data['isActive'] ) {
				$modules[ $key ] = $data;
			}
		}

		return $modules;
	}

	/**
	 * Get options using theme_mods.
	 */
	public static function get_theme_mods() {
		$theme_mods = array(
			'font_body_variants',
			'font_body_category',
			'font_site_title_variants',
			'font_site_title_category',
			'font_site_tagline_variants',
			'font_site_tagline_category',
			'font_navigation_variants',
			'font_navigation_category',
			'font_secondary_navigation_variants',
			'font_secondary_navigation_category',
			'font_buttons_variants',
			'font_buttons_category',
			'font_heading_1_variants',
			'font_heading_1_category',
			'font_heading_2_variants',
			'font_heading_2_category',
			'font_heading_3_variants',
			'font_heading_3_category',
			'font_heading_4_variants',
			'font_heading_4_category',
			'font_heading_5_variants',
			'font_heading_5_category',
			'font_heading_6_variants',
			'font_heading_6_category',
			'font_widget_title_variants',
			'font_widget_title_category',
			'font_footer_variants',
			'font_footer_category',
			'aureon_copyright',
		);

		if ( function_exists( 'aureon_is_using_dynamic_typography' ) && aureon_is_using_dynamic_typography() ) {
			$theme_mods = array(
				'aureon_copyright',
			);
		}

		return $theme_mods;
	}

	/**
	 * Get our setting keys.
	 */
	public static function get_setting_keys() {
		return array(
			'aureon_settings',
			'aureon_background_settings',
			'aureon_blog_settings',
			'aureon_hooks',
			'aureon_page_header_settings',
			'aureon_secondary_nav_settings',
			'aureon_spacing_settings',
			'aureon_menu_plus_settings',
			'aureon_woocommerce_settings',
		);
	}

	/**
	 * Add our scripts to the page.
	 */
	public function enqueue_scripts() {
		if ( ! class_exists( 'Aureon_Dashboard' ) ) {
			return;
		}

		$dashboard_pages = Aureon_Dashboard::get_pages();
		$current_screen = get_current_screen();


		if ( in_array( $current_screen->id, $dashboard_pages ) ) {
			$packages_info = aureon_premium_get_enqueue_assets( 'packages' );
			wp_enqueue_style(
				'aureon-pro-packages',
				AUREON_STUDIO_DIR_URL . 'dist/packages.css',
				array(),
				$packages_info['version']
			);

			wp_enqueue_style(
				'aureon-pro-dashboard',
				AUREON_STUDIO_DIR_URL . 'dist/style-dashboard.css',
				array( 'wp-components' ),
				AUREON_STUDIO_VERSION
			);

			if ( 'appearance_page_aureon-options' === $current_screen->id ) {
				wp_enqueue_script(
					'aureon-pro-dashboard',
					AUREON_STUDIO_DIR_URL . 'dist/dashboard.js',
					array(),
					AUREON_STUDIO_VERSION,
					true
				);

				wp_set_script_translations( 'aureon-pro-dashboard', 'aureon-studio', AUREON_STUDIO_DIR_PATH . 'langs' );

				wp_localize_script(
					'aureon-pro-dashboard',
					'aureonProDashboard',
					array(
						'modules' => self::get_modules(),
						'exportableModules' => self::get_exportable_modules(),
						'fontLibraryUrl' => admin_url( 'themes.php?page=aureon-font-library' ),
						'elementsUrl' => admin_url( 'edit.php?post_type=aureon_elements' ),
						'hasWooCommerce' => class_exists( 'WooCommerce' ),
					)
				);
			}
		}
	}

	/**
	 * Add the container for our start customizing app.
	 */
	public function module_list() {
		echo '<div id="aureon-module-list"></div>';
	}

	/**
	 * Add the container for our start customizing app.
	 */
	public function import_export() {
		echo '<div id="aureon-import-export-pro"></div>';
	}

	/**
	 * Add the container for our reset app.
	 */
	public function reset() {
		echo '<div id="aureon-reset-pro"></div>';
	}
}

Aureon_Pro_Dashboard::get_instance();
