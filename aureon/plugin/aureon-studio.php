<?php
/**
 * Plugin Name: Aureon Studio
 * Plugin URI: https://aureonstudio.com
 * Description: The entire collection of Aureon premium modules.
 * Version: 1.0.0
 * Requires at least: 6.1
 * Requires PHP: 7.2
 * Author: Aureon Studio
 * Author URI: https://aureonstudio.com
 * License: GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: aureon-studio
 *
 * @package Aureon Studio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'AUREON_STUDIO_VERSION', '3.0.0' );
define( 'AUREON_STUDIO_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'AUREON_STUDIO_DIR_URL', plugin_dir_url( __FILE__ ) );
define( 'AUREON_LIBRARY_DIRECTORY', plugin_dir_path( __FILE__ ) . 'library/' );
define( 'AUREON_LIBRARY_DIRECTORY_URL', plugin_dir_url( __FILE__ ) . 'library/' );

require_once AUREON_STUDIO_DIR_PATH . 'inc/functions.php';
require_once AUREON_STUDIO_DIR_PATH . 'inc/deprecated.php';
require_once AUREON_STUDIO_DIR_PATH . 'inc/class-rest.php';
require_once AUREON_STUDIO_DIR_PATH . 'inc/class-singleton.php';

if ( ! function_exists( 'aureon_is_module_active' ) ) {
	/**
	 * Checks if a module is active.
	 *
	 * @param string $module The option name to check.
	 * @param string $constant The constant to check for.
	 **/
	function aureon_is_module_active( $module, $constant ) {
		// If we don't have the module or constant, bail.
		if ( ! $module && ! $constant ) {
			return false;
		}

		// If our module is active, return true.
		if ( 'activated' === get_option( $module ) || defined( $constant ) ) {
			return true;
		}

		// Not active? Return false.
		return false;
	}
}

if ( aureon_is_module_active( 'aureon_package_backgrounds', 'AUREON_BACKGROUNDS' ) ) {
	require_once AUREON_STUDIO_DIR_PATH . 'backgrounds/aureon-backgrounds.php';
}

if ( aureon_is_module_active( 'aureon_package_blog', 'AUREON_BLOG' ) ) {
	require_once AUREON_STUDIO_DIR_PATH . 'blog/aureon-blog.php';
}

if ( aureon_is_module_active( 'aureon_package_copyright', 'AUREON_COPYRIGHT' ) ) {
	require_once AUREON_STUDIO_DIR_PATH . 'copyright/aureon-copyright.php';
}

if ( aureon_is_module_active( 'aureon_package_disable_elements', 'AUREON_DISABLE_ELEMENTS' ) ) {
	require_once AUREON_STUDIO_DIR_PATH . 'disable-elements/aureon-disable-elements.php';
}

if ( aureon_is_module_active( 'aureon_package_elements', 'AUREON_ELEMENTS' ) ) {
	require_once AUREON_STUDIO_DIR_PATH . 'elements/elements.php';
	require_once AUREON_STUDIO_DIR_PATH . 'inc/class-register-dynamic-tags.php';
	require_once AUREON_STUDIO_DIR_PATH . 'inc/class-adjacent-posts.php';
}

if ( aureon_is_module_active( 'aureon_package_secondary_nav', 'AUREON_SECONDARY_NAV' ) ) {
	require_once AUREON_STUDIO_DIR_PATH . 'secondary-nav/aureon-secondary-nav.php';
}

if ( aureon_is_module_active( 'aureon_package_spacing', 'AUREON_SPACING' ) ) {
	require_once AUREON_STUDIO_DIR_PATH . 'spacing/aureon-spacing.php';
}

if ( aureon_is_module_active( 'aureon_package_menu_plus', 'AUREON_MENU_PLUS' ) ) {
	require_once AUREON_STUDIO_DIR_PATH . 'menu-plus/aureon-menu-plus.php';
}

if ( aureon_is_module_active( 'aureon_package_woocommerce', 'AUREON_WOOCOMMERCE' ) ) {
	include_once ABSPATH . 'wp-admin/includes/plugin.php';

	if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
		require_once AUREON_STUDIO_DIR_PATH . 'woocommerce/woocommerce.php';
	}
}

// Deprecated modules.
if ( aureon_is_module_active( 'aureon_package_hooks', 'AUREON_HOOKS' ) ) {
	require_once AUREON_STUDIO_DIR_PATH . 'hooks/aureon-hooks.php';
}

if ( aureon_is_module_active( 'aureon_package_page_header', 'AUREON_PAGE_HEADER' ) ) {
	require_once AUREON_STUDIO_DIR_PATH . 'page-header/aureon-page-header.php';
}

if ( aureon_is_module_active( 'aureon_package_sections', 'AUREON_SECTIONS' ) ) {
	require_once AUREON_STUDIO_DIR_PATH . 'sections/aureon-sections.php';
}

add_action( 'after_setup_theme', 'aureon_premium_load_modules' );
/**
 * Load our modules after the theme has initiated.
 *
 * @since 2.1.0
 */
function aureon_premium_load_modules() {
	$is_using_dynamic_typography = function_exists( 'aureon_is_using_dynamic_typography' ) && aureon_is_using_dynamic_typography();

	if ( ! $is_using_dynamic_typography && aureon_is_module_active( 'aureon_package_typography', 'AUREON_TYPOGRAPHY' ) ) {
		require_once AUREON_STUDIO_DIR_PATH . 'typography/aureon-fonts.php';
	}

	if ( version_compare( aureon_premium_get_theme_version(), '3.1.0-alpha.1', '<' ) && aureon_is_module_active( 'aureon_package_colors', 'AUREON_COLORS' ) ) {
		require_once AUREON_STUDIO_DIR_PATH . 'colors/aureon-colors.php';
	}

	load_plugin_textdomain( 'aureon-studio', false, 'aureon-studio/langs/' );
}

// General functionality.
require_once AUREON_STUDIO_DIR_PATH . 'general/class-external-file-css.php';
require_once AUREON_STUDIO_DIR_PATH . 'general/smooth-scroll.php';
require_once AUREON_STUDIO_DIR_PATH . 'general/icons.php';
require_once AUREON_STUDIO_DIR_PATH . 'general/enqueue-scripts.php';

// Load our Dashboard functions once the theme has loaded.
require_once AUREON_STUDIO_DIR_PATH . 'inc/class-dashboard.php';

if ( aureon_is_module_active( 'aureon_package_site_library', 'AUREON_SITE_LIBRARY' ) && version_compare( PHP_VERSION, '5.4', '>=' ) && ! defined( 'AUREON_DISABLE_SITE_LIBRARY' ) ) {
	require_once AUREON_STUDIO_DIR_PATH . 'site-library/class-site-library-rest.php';
	require_once AUREON_STUDIO_DIR_PATH . 'site-library/class-site-library-helper.php';
}

if ( is_admin() ) {
	require_once AUREON_STUDIO_DIR_PATH . 'inc/deprecated-admin.php';

	if ( aureon_is_module_active( 'aureon_package_site_library', 'AUREON_SITE_LIBRARY' ) && version_compare( PHP_VERSION, '5.4', '>=' ) && ! defined( 'AUREON_DISABLE_SITE_LIBRARY' ) ) {
		require_once AUREON_STUDIO_DIR_PATH . 'site-library/class-site-library.php';
	}
}

if ( aureon_is_module_active( 'aureon_package_font_library', 'AUREON_FONT_LIBRARY' ) ) {
	require_once AUREON_STUDIO_DIR_PATH . 'font-library/class-font-library.php';
	require_once AUREON_STUDIO_DIR_PATH . 'font-library/class-font-library-rest.php';
	require_once AUREON_STUDIO_DIR_PATH . 'font-library/class-font-library-optimize.php';
	require_once AUREON_STUDIO_DIR_PATH . 'font-library/class-font-library-cpt.php';
}

if ( ! function_exists( 'aureon_premium_updater' ) ) {
	add_action( 'admin_init', 'aureon_premium_updater', 0 );
	/**
	 * Set up the updater
	 **/
	function aureon_premium_updater() {
		if ( ! class_exists( 'Aureon_Premium_Plugin_Updater' ) ) {
			include AUREON_STUDIO_DIR_PATH . 'library/class-plugin-updater.php';
		}

		$license_key = get_option( 'aureon_studio_license_key' );

		$edd_updater = new Aureon_Premium_Plugin_Updater(
			'https://aureonstudio.com',
			__FILE__,
			array(
				'version'   => AUREON_STUDIO_VERSION,
				'license'   => trim( $license_key ),
				'item_name' => 'Aureon Studio',
				'author'    => 'Aureon Studio',
				'url'       => home_url(),
				'beta'      => apply_filters( 'aureon_premium_beta_tester', false ),
			)
		);
	}
}

add_filter( 'edd_sl_plugin_updater_api_params', 'aureon_premium_set_updater_api_params', 10, 3 );
/**
 * Add the Aureon version to our updater params.
 *
 * @param array  $api_params  The array of data sent in the request.
 * @param array  $api_data    The array of data set up in the class constructor.
 * @param string $plugin_file The full path and filename of the file.
 */
function aureon_premium_set_updater_api_params( $api_params, $api_data, $plugin_file ) {
	/*
	 * Make sure $plugin_file matches your plugin's file path. You should have a constant for this
	 * or can use __FILE__ if this code goes in your plugin's main file.
	 */
	if ( __FILE__ === $plugin_file ) {
		// Dynamically retrieve the current version number.
		$api_params['aureon_version'] = defined( 'AUREON_VERSION' ) ? AUREON_VERSION : '';
	}

	return $api_params;
}

if ( ! function_exists( 'aureon_premium_setup' ) ) {
	add_action( 'after_setup_theme', 'aureon_premium_setup' );
	/**
	 * Add useful functions to Aureon Studio
	 **/
	function aureon_premium_setup() {
		// This used to be in the theme but the WP.org review team asked for it to be removed.
		// Not wanting people to have broken shortcodes in their widgets on update, I added it into premium.
		add_filter( 'widget_text', 'do_shortcode' );
	}
}

if ( ! function_exists( 'aureon_premium_theme_information' ) ) {
	add_action( 'admin_notices', 'aureon_premium_theme_information' );
	/**
	 * Checks whether there's a theme update available and lets you know.
	 * Also checks to see if Aureon is the active theme. If not, tell them.
	 *
	 * @since 1.2.95
	 **/
	function aureon_premium_theme_information() {
		$theme = wp_get_theme();

		if ( 'Aureon' === $theme->name || 'aureon' === $theme->template ) {

			// Get our information on updates.
			// @see https://developer.wordpress.org/reference/functions/wp_prepare_themes_for_js/.
			$updates = array();
			if ( current_user_can( 'update_themes' ) ) {
				$updates_transient = get_site_transient( 'update_themes' );
				if ( isset( $updates_transient->response ) ) {
					$updates = $updates_transient->response;
				}
			}

			$screen = get_current_screen();

			// If a Aureon update exists, and we're not on the themes page.
			// No need to tell people an update exists on the themes page, WP does that for us.
			if ( isset( $updates['aureon'] ) && 'themes' !== $screen->base ) {
				printf(
					'<div class="notice is-dismissible notice-info">
						<p>%1$s <a href="%2$s">%3$s</a></p>
					</div>',
					esc_html__( 'Aureon has an update available.', 'aureon-studio' ),
					esc_url( admin_url( 'themes.php' ) ),
					esc_html__( 'Update now.', 'aureon-studio' )
				);
			}
		} else {
			// Aureon isn't the active theme, let them know Aureon Studio won't work.
			printf(
				'<div class="notice is-dismissible notice-warning">
					<p>%1$s <a href="%3$s">%2$s</a></p>
				</div>',
				esc_html__( 'Aureon Studio requires Aureon to be your active theme.', 'aureon-studio' ),
				esc_html__( 'Install now.', 'aureon-studio' ),
				esc_url( admin_url( 'theme-install.php?theme=aureon' ) )
			);
		}

	}
}

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'aureon_add_configure_action_link' );
/**
 * Show a "Configure" link in the plugin action links.
 *
 * @since 1.3
 * @param array $links The existing plugin row links.
 */
function aureon_add_configure_action_link( $links ) {
	$configuration_link = '<a href="' . admin_url( 'themes.php?page=aureon-options' ) . '">' . __( 'Configure', 'aureon-studio' ) . '</a>';

	return array_merge( $links, array( $configuration_link ) );
}

add_action( 'admin_init', 'aureon_deactivate_standalone_addons' );
/**
 * Deactivate any standalone add-ons if they're active.
 *
 * @since 1.3.1
 */
function aureon_deactivate_standalone_addons() {
	$addons = array(
		'aureon-backgrounds/aureon-backgrounds.php',
		'aureon-blog/aureon-blog.php',
		'aureon-colors/aureon-colors.php',
		'aureon-copyright/aureon-copyright.php',
		'aureon-disable-elements/aureon-disable-elements.php',
		'aureon-hooks/aureon-hooks.php',
		'aureon-ie/aureon-ie.php',
		'aureon-menu-plus/aureon-menu-plus.php',
		'aureon-page-header/aureon-page-header.php',
		'aureon-secondary-nav/aureon-secondary-nav.php',
		'aureon-sections/aureon-sections.php',
		'aureon-spacing/aureon-spacing.php',
		'aureon-typography/aureon-fonts.php',
	);

	deactivate_plugins( $addons );
}
