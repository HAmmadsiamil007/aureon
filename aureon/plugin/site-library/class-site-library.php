<?php
/**
 * This file handles the Site Library.
 *
 * @since 1.6.0
 *
 * @package Aureon Studio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

/**
 * Do our Site Library.
 */
class Aureon_Site_Library {
	/**
	 * Instance.
	 *
	 * @access private
	 * @var object Instance
	 * @since 1.6
	 */
	private static $instance;

	/**
	 * Initiator.
	 *
	 * @since 1.6
	 * @return object initialized object of class.
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Get it going.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'aureon_export_items', array( $this, 'add_export_checkbox' ) );
		add_filter( 'aureon_export_data', array( $this, 'do_site_export' ) );
		add_filter( 'aureon_dashboard_tabs', array( $this, 'add_dashboard_tab' ) );
		add_filter( 'aureon_dashboard_screens', array( $this, 'add_dashboard_screen' ) );
		add_action( 'admin_head', array( $this, 'fix_menu' ) );
	}

	/**
	 * Add our menu item.
	 */
	public function add_menu() {
		add_submenu_page(
			'themes.php',
			__( 'Site Library', 'aureon-studio' ),
			__( 'Site Library', 'aureon-studio' ),
			'manage_options',
			'aureon-library',
			array( $this, 'library_page' )
		);
	}

	/**
	 * Set our current menu item as the Aureon Dashboard.
	 */
	public function fix_menu() {
		global $parent_file, $submenu_file, $post_type;

		$screen = get_current_screen();

		if ( 'appearance_page_aureon-library' === $screen->id ) {
			$parent_file = 'themes.php'; // phpcs:ignore -- Override necessary.
			$submenu_file = 'aureon-options'; // phpcs:ignore -- Override necessary.
		}

		remove_submenu_page( 'themes.php', 'aureon-library' );
	}

	/**
	 * Add our scripts.
	 */
	public function enqueue_scripts() {
		$screen = get_current_screen();

		if ( 'appearance_page_aureon-library' === $screen->id ) {
			wp_enqueue_script(
				'aureon-pro-site-library',
				AUREON_STUDIO_DIR_URL . 'dist/site-library.js',
				array( 'wp-api', 'wp-i18n', 'wp-components', 'wp-element', 'wp-api-fetch', 'wp-util', 'wp-html-entities', 'updates' ),
				AUREON_STUDIO_VERSION,
				true
			);

			if ( function_exists( 'wp_set_script_translations' ) ) {
				wp_set_script_translations( 'aureon-pro-site-library', 'aureon-studio', AUREON_STUDIO_DIR_PATH . 'langs' );
			}

			if ( function_exists( 'wp_get_upload_dir' ) ) {
				$uploads_url = wp_get_upload_dir();
			} else {
				$uploads_url = wp_upload_dir( null, false );
			}

			wp_localize_script(
				'aureon-pro-site-library',
				'aureonStudioSiteLibrary',
				array(
					'homeUrl' => esc_url( home_url() ),
					'hasBackup' => ! empty( get_option( '_aureon_site_library_backup', array() ) ),
					'aureonStudioVersion' => AUREON_STUDIO_VERSION,
					'aureonVersion' => aureon_premium_get_theme_version(),
					'generateblocksVersion' => defined( 'GENERATEBLOCKS_VERSION' ) ? GENERATEBLOCKS_VERSION : '',
					'isGenerateBlocksProInstalled' => file_exists( WP_PLUGIN_DIR . '/generateblocks-pro/plugin.php' ),
					'elementorReplaceUrls' => esc_url( admin_url( 'admin.php?page=elementor-tools#tab-replace_url' ) ),
					'uploadsUrl' => $uploads_url['baseurl'],
					'isDebugEnabled' => defined( 'WP_DEBUG' ) && true === WP_DEBUG,
				)
			);

			wp_enqueue_style(
				'aureon-pro-site-library',
				AUREON_STUDIO_DIR_URL . 'dist/site-library.css',
				array( 'wp-components' ),
				AUREON_STUDIO_VERSION
			);

			if ( ! class_exists( 'Aureon_Dashboard' ) ) {
				wp_enqueue_style(
					'aureon-premium-dashboard',
					AUREON_STUDIO_DIR_URL . 'inc/legacy/assets/dashboard.css',
					array(),
					AUREON_STUDIO_VERSION
				);
			}
		}
	}

	/**
	 * Add our page.
	 */
	public function library_page() {
		if ( ! class_exists( 'Aureon_Dashboard' ) ) :
			?>
			<div class="site-library-header">
				<div class="site-library-container">
					<div class="library-title">
						<?php _e( 'Aureon Site Library', 'aureon-studio' ); ?>
					</div>

					<div class="library-links">
						<a href="https://aureonstudio.com/support" target="_blank"><?php _e( 'Support', 'aureon-studio' ); ?></a>
						<a href="https://docs.aureonstudio.com" target="_blank"><?php _e( 'Documentation', 'aureon-studio' ); ?></a>
					</div>
				</div>
			</div>
			<?php
		endif;

		do_action( 'aureon_before_site_library' );
		?>

		<div id="aureon-studio-site-library"></div>
		<?php
	}

	/**
	 * Add the Sites tab to our Dashboard tabs.
	 *
	 * @param array $tabs Existing tabs.
	 * @return array New tabs.
	 */
	public function add_dashboard_tab( $tabs ) {
		$screen = get_current_screen();

		$tabs['Sites'] = array(
			'name' => __( 'Site Library', 'aureon-studio' ),
			'url' => admin_url( 'themes.php?page=aureon-library' ),
			'class' => 'appearance_page_aureon-library' === $screen->id ? 'active' : '',
			'id' => 'aureon-site-library-tab',
		);

		return $tabs;
	}

	/**
	 * Tell Aureon this is an admin page.
	 *
	 * @param array $screens Existing screens.
	 */
	public function add_dashboard_screen( $screens ) {
		$screens[] = 'appearance_page_aureon-library';

		return $screens;
	}

	/**
	 * Add our Aureon Site export checkbox to the Export module.
	 */
	public function add_export_checkbox() {
		if ( ! apply_filters( 'aureon_show_aureon_site_export_option', false ) ) {
			return;
		}
		?>
		<hr style="margin:10px 0;border-bottom:0;" />

		<label>
			<input type="checkbox" name="module_group[]" value="aureon-site" />
			<?php _ex( 'Aureon Site', 'Module name', 'aureon-studio' ); ?>
		</label>
		<?php
	}

	/**
	 * Add to our export .json file.
	 *
	 * @param array $data The current data being exported.
	 * @return array Existing and extended data.
	 */
	public function do_site_export( $data ) {
		// Bail if we haven't chosen to export the Site.
		if ( ! in_array( 'aureon-site', $_POST['module_group'] ) ) { // phpcs:ignore -- No processing happening here.
			return $data;
		}

		// Modules.
		$modules = Aureon_Site_Library_Helper::premium_modules();

		$data['modules'] = array();
		foreach ( $modules as $name => $key ) {
			if ( 'activated' === get_option( $key ) ) {
				$data['modules'][ $name ] = $key;
			}
		}

		// Site options.
		$data['site_options']['nav_menu_locations'] = get_theme_mod( 'nav_menu_locations' );
		$data['site_options']['custom_logo']        = wp_get_attachment_url( get_theme_mod( 'custom_logo' ) );
		$data['site_options']['show_on_front']      = get_option( 'show_on_front' );
		$data['site_options']['page_on_front']      = get_option( 'page_on_front' );
		$data['site_options']['page_for_posts']     = get_option( 'page_for_posts' );

		// Elements.
		$data['site_options']['element_locations'] = $this->get_elements_locations();
		$data['site_options']['element_exclusions'] = $this->get_elements_exclusions();

		// Custom CSS.
		if ( function_exists( 'wp_get_custom_css_post' ) ) {
			$data['custom_css'] = wp_get_custom_css_post()->post_content;
		}

		// WooCommerce.
		if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			$data['site_options']['woocommerce_shop_page_id']             = get_option( 'woocommerce_shop_page_id' );
			$data['site_options']['woocommerce_cart_page_id']             = get_option( 'woocommerce_cart_page_id' );
			$data['site_options']['woocommerce_checkout_page_id']         = get_option( 'woocommerce_checkout_page_id' );
			$data['site_options']['woocommerce_myaccount_page_id']        = get_option( 'woocommerce_myaccount_page_id' );
			$data['site_options']['woocommerce_single_image_width']       = get_option( 'woocommerce_single_image_width' );
			$data['site_options']['woocommerce_thumbnail_image_width']    = get_option( 'woocommerce_thumbnail_image_width' );
			$data['site_options']['woocommerce_thumbnail_cropping']       = get_option( 'woocommerce_thumbnail_cropping' );
			$data['site_options']['woocommerce_shop_page_display']        = get_option( 'woocommerce_shop_page_display' );
			$data['site_options']['woocommerce_category_archive_display'] = get_option( 'woocommerce_category_archive_display' );
			$data['site_options']['woocommerce_default_catalog_orderby']  = get_option( 'woocommerce_default_catalog_orderby' );
		}

		// Elementor.
		if ( is_plugin_active( 'elementor/elementor.php' ) ) {
			$data['site_options']['elementor_container_width']             = get_option( 'elementor_container_width' );
			$data['site_options']['elementor_cpt_support']                 = get_option( 'elementor_cpt_support' );
			$data['site_options']['elementor_css_print_method']            = get_option( 'elementor_css_print_method' );
			$data['site_options']['elementor_default_generic_fonts']       = get_option( 'elementor_default_generic_fonts' );
			$data['site_options']['elementor_disable_color_schemes']       = get_option( 'elementor_disable_color_schemes' );
			$data['site_options']['elementor_disable_typography_schemes']  = get_option( 'elementor_disable_typography_schemes' );
			$data['site_options']['elementor_editor_break_lines']          = get_option( 'elementor_editor_break_lines' );
			$data['site_options']['elementor_exclude_user_roles']          = get_option( 'elementor_exclude_user_roles' );
			$data['site_options']['elementor_global_image_lightbox']       = get_option( 'elementor_global_image_lightbox' );
			$data['site_options']['elementor_page_title_selector']         = get_option( 'elementor_page_title_selector' );
			$data['site_options']['elementor_scheme_color']                = get_option( 'elementor_scheme_color' );
			$data['site_options']['elementor_scheme_color-picker']         = get_option( 'elementor_scheme_color-picker' );
			$data['site_options']['elementor_scheme_typography']           = get_option( 'elementor_scheme_typography' );
			$data['site_options']['elementor_space_between_widgets']       = get_option( 'elementor_space_between_widgets' );
			$data['site_options']['elementor_stretched_section_container'] = get_option( 'elementor_stretched_section_container' );
			$data['site_options']['elementor_load_fa4_shim']               = get_option( 'elementor_load_fa4_shim' );
			$data['site_options']['elementor_active_kit']                  = get_option( 'elementor_active_kit' );
		}

		// Beaver Builder.
		if ( is_plugin_active( 'beaver-builder-lite-version/fl-builder.php' ) || is_plugin_active( 'bb-plugin/fl-builder.php' ) ) {
			$data['site_options']['_fl_builder_enabled_icons']     = get_option( '_fl_builder_enabled_icons' );
			$data['site_options']['_fl_builder_enabled_modules']   = get_option( '_fl_builder_enabled_modules' );
			$data['site_options']['_fl_builder_post_types']        = get_option( '_fl_builder_post_types' );
			$data['site_options']['_fl_builder_color_presets']     = get_option( '_fl_builder_color_presets' );
			$data['site_options']['_fl_builder_services']          = get_option( '_fl_builder_services' );
			$data['site_options']['_fl_builder_settings']          = get_option( '_fl_builder_settings' );
			$data['site_options']['_fl_builder_user_access']       = get_option( '_fl_builder_user_access' );
			$data['site_options']['_fl_builder_enabled_templates'] = get_option( '_fl_builder_enabled_templates' );
		}

		// Menu Icons.
		if ( is_plugin_active( 'menu-icons/menu-icons.php' ) ) {
			$data['site_options']['menu-icons'] = get_option( 'menu-icons' );
		}

		// Ninja Forms.
		if ( is_plugin_active( 'ninja-forms/ninja-forms.php' ) ) {
			$data['site_options']['ninja_forms_settings'] = get_option( 'ninja_forms_settings' );
		}

		// Social Warfare.
		if ( is_plugin_active( 'social-warfare/social-warfare.php' ) ) {
			$data['site_options']['socialWarfareOptions'] = get_option( 'socialWarfareOptions' );
		}

		// Elements Plus.
		if ( is_plugin_active( 'elements-plus/elements-plus.php' ) ) {
			$data['site_options']['elements_plus_settings'] = get_option( 'elements_plus_settings' );
		}

		// Ank Google Map.
		if ( is_plugin_active( 'ank-google-map/ank-google-map.php' ) ) {
			$data['site_options']['ank_google_map'] = get_option( 'ank_google_map' );
		}

		// GP Social Share.
		if ( is_plugin_active( 'aureon-social-share-svg/aureon-social-share.php' ) ) {
			$data['site_options']['aureon_social_settings'] = get_option( 'aureon_social_settings' );
		}

		// Active plugins.
		$active_plugins = get_option( 'active_plugins' );
		$all_plugins = get_plugins();

		$ignore = apply_filters(
			'aureon_sites_ignore_plugins',
			array(
				'aureon-studio/aureon-studio.php',
				'widget-importer-exporter/widget-importer-exporter.php',
			)
		);

		foreach ( $ignore as $plugin ) {
			unset( $all_plugins[ $plugin ] );
		}

		$activated_plugins = array();

		foreach ( $active_plugins as $p ) {
			if ( isset( $all_plugins[ $p ] ) ) {
				$activated_plugins[ $all_plugins[ $p ]['Name'] ] = $p;
			}
		}

		$data['plugins'] = $activated_plugins;

		return $data;

	}

	/**
	 * Get our Element display locations.
	 *
	 * @return array
	 */
	public function get_elements_locations() {
		$args = array(
			'post_type' => 'aureon_elements',
			'showposts' => -1,
		);

		$posts = get_posts( $args );
		$new_values = array();

		foreach ( $posts as $post ) {
			$display_conditions = get_post_meta( $post->ID, '_aureon_element_display_conditions', true );

			if ( $display_conditions ) {
				$new_values[ $post->ID ] = $display_conditions;
			}
		}

		return $new_values;
	}

	/**
	 * Get our Element display locations.
	 *
	 * @return array
	 */
	public function get_elements_exclusions() {
		$args = array(
			'post_type' => 'aureon_elements',
			'showposts' => -1,
		);

		$posts = get_posts( $args );
		$new_values = array();

		foreach ( $posts as $post ) {
			$display_conditions = get_post_meta( $post->ID, '_aureon_element_exclude_conditions', true );

			if ( $display_conditions ) {
				$new_values[ $post->ID ] = $display_conditions;
			}
		}

		return $new_values;
	}
}

Aureon_Site_Library::get_instance();
