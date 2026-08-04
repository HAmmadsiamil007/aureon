<?php
/**
 * Deprecated admin functions.
 *
 * @package Aureon Studio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

/**
 * Checks to see if we're in the Site dashboard.
 *
 * @since 1.6
 * @deprecated 2.0.0
 *
 * @return bool
 */
function aureon_is_sites_dashboard() {
	$screen = get_current_screen();

	if ( ! is_object( $screen ) ) {
		return false;
	}

	if ( 'appearance_page_aureon-site-library' === $screen->id ) {
		return true;
	}

	return false;
}

/**
 * Add the Sites tab to our Dashboard tabs.
 *
 * @since 1.6
 * @deprecated 2.0.0
 *
 * @param array $tabs Existing tabs.
 * @return array New tabs.
 */
function aureon_sites_dashboard_tab( $tabs ) {
	$tabs['Sites'] = array(
		'name' => __( 'Site Library', 'aureon-studio' ),
		'url' => admin_url( 'themes.php?page=aureon-site-library' ),
		'class' => aureon_is_sites_dashboard() ? 'active' : '',
	);

	return $tabs;
}

/**
 * Register our Site Library page.
 *
 * @since 1.7
 * @deprecated 2.0.0
 */
function aureon_site_library_register() {
	add_submenu_page(
		'themes.php',
		__( 'Site Library', 'aureon-studio' ),
		__( 'Site Library', 'aureon-studio' ),
		'manage_options',
		'aureon-site-library',
		'aureon_sites_container'
	);
}

/**
 * Set our current menu item as the Aureon Dashboard.
 *
 * @since 1.7
 * @deprecated 2.0.0
 */
function aureon_site_library_fix_menu() {
	global $parent_file, $submenu_file, $post_type;

	if ( aureon_is_sites_dashboard() ) {
		$parent_file = 'themes.php'; // phpcs:ignore -- Override necessary.
		$submenu_file = 'aureon-options'; // phpcs:ignore -- Override necessary.
	}

	remove_submenu_page( 'themes.php', 'aureon-site-library' );
}

/**
 * Add our scripts for the site library.
 *
 * @since 1.8
 * @deprecated 2.0.0
 */
function aureon_sites_do_enqueue_scripts() {
	if ( ! aureon_is_sites_dashboard() ) {
		return;
	}

	$backup_data = get_option( '_aureon_site_library_backup', array() );

	wp_enqueue_script(
		'aureon-sites-admin',
		AUREON_SITES_URL . 'assets/js/admin.js',
		array( 'jquery', 'wp-util', 'updates', 'aureon-sites-blazy' ),
		AUREON_STUDIO_VERSION,
		true
	);

	wp_enqueue_script(
		'aureon-sites-download',
		AUREON_SITES_URL . 'assets/js/download.js',
		array( 'jquery', 'aureon-sites-admin' ),
		AUREON_STUDIO_VERSION,
		true
	);

	wp_enqueue_script(
		'aureon-sites-blazy',
		AUREON_SITES_URL . 'assets/js/blazy.min.js',
		array(),
		AUREON_STUDIO_VERSION,
		true
	);

	wp_localize_script(
		'aureon-sites-admin',
		'aureon_sites_params',
		array(
			'ajaxurl'                => admin_url( 'admin-ajax.php' ),
			'nonce'                  => wp_create_nonce( 'aureon_sites_nonce' ),
			'importing_options'      => __( 'Importing options', 'aureon-studio' ),
			'backing_up_options'     => __( 'Backing up options', 'aureon-studio' ),
			'checking_demo_content'  => __( 'Checking demo content', 'aureon-studio' ),
			'downloading_content'    => __( 'Downloading content', 'aureon-studio' ),
			'importing_content'      => __( 'Importing content', 'aureon-studio' ),
			'importing_site_options' => __( 'Importing site options', 'aureon-studio' ),
			'importing_widgets'      => __( 'Importing widgets', 'aureon-studio' ),
			'activating_plugins'     => __( 'Activating plugins', 'aureon-studio' ),
			'installing_plugins'     => __( 'Installing plugins', 'aureon-studio' ),
			'automatic_plugins'      => __( 'Automatic', 'aureon-studio' ),
			'manual_plugins'         => __( 'Manual', 'aureon-studio' ),
			'home_url'               => home_url(),
			'restoreThemeOptions'    => __( 'Restoring theme options', 'aureon-studio' ),
			'restoreSiteOptions'     => __( 'Restoring site options', 'aureon-studio' ),
			'restoreContent'         => __( 'Removing imported content', 'aureon-studio' ),
			'restorePlugins'         => __( 'Deactivating imported plugins', 'aureon-studio' ),
			'restoreWidgets'         => __( 'Restoring widgets', 'aureon-studio' ),
			'restoreCSS'             => __( 'Restoring CSS', 'aureon-studio' ),
			'cleanUp'                => __( 'Cleaning up', 'aureon-studio' ),
			'hasContentBackup'       => ! empty( $backup_data['content'] ),
			'confirmRemoval'         => __( 'This process makes changes to your database. If you have existing data, be sure to create a backup as a precaution.', 'aureon-studio' ),
		)
	);

	wp_enqueue_style(
		'aureon-sites-admin',
		AUREON_SITES_URL . 'assets/css/admin.css',
		array(),
		AUREON_STUDIO_VERSION
	);

	wp_enqueue_style(
		'aureon-premium-dashboard',
		plugin_dir_url( dirname( __FILE__ ) ) . 'inc/assets/dashboard.css',
		array(),
		AUREON_STUDIO_VERSION
	);
}

/**
 * Add a body class while in the Site Library.
 *
 * @since 1.8
 * @deprecated 2.0.0
 *
 * @param array $classes Current body classes.
 * @return array Existing and our new body classes
 */
function aureon_sites_do_admin_body_classes( $classes ) {
	if ( aureon_is_sites_dashboard() ) {
		$classes .= ' aureon-sites';
	}

	return $classes;
}

/**
 * Add an opening wrapper element for our Dashboard tabs and page builder links.
 *
 * @since 1.8
 */
function aureon_sites_add_tabs_wrapper_open() {
	echo '<div class="site-library-tabs-wrapper">';
}

/**
 * Adds our Site dashboard container.
 *
 * @since 1.6
 * @deprecated 2.0.0
 */
function aureon_sites_container() {
	?>
	<div class="aureon-site-library">
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
		<div class="site-library-container">
			<?php
			do_action( 'aureon_inside_site_library_container' );

			$site_data = get_transient( 'aureon_sites' );
			$page_builders = array();

			foreach ( (array) $site_data as $data ) {
				if ( isset( $data['page_builder'][0] ) ) {
					$page_builder = $data['page_builder'][0];
					$page_builder_id = str_replace( ' ', '-', strtolower( $page_builder ) );

					if ( 'no-page-builder' !== $page_builder_id ) {
						$page_builders[ $page_builder_id ] = $page_builder;
					}
				}
			}

			echo '<div class="library-filters">';

			if ( ! empty( $page_builders ) ) :
				?>
				<div class="page-builder-filter">
					<label for="page-builder" class="page-builder-label"><?php _e( 'Page Builder:', 'aureon-studio' ); ?></label>
					<div class="filter-select">
						<select id="page-builder" class="page-builder-group" data-filter-group="page-builder" data-page-builder=".no-page-builder">
							<option value="no-page-builder"><?php _e( 'None', 'aureon-studio' ); ?></option>
							<?php
							foreach ( $page_builders as $id => $name ) {
								printf(
									'<option value="%1$s">%2$s</option>',
									esc_attr( $id ),
									esc_html( $name )
								);
							}
							?>
						</select>
					</div>
				</div>
				<?php
			endif;
			?>

			</div>

			</div> <!-- .site-library-tabs-wrapper -->
			<?php // The opening wrapper for this is in aureon_sites_add_tabs_wrapper_open(). ?>

			<?php
			$backup_data = get_option( '_aureon_site_library_backup', array() );
			$show_remove_site = false;

			if ( ! empty( $backup_data ) ) {
				$show_remove_site = true;
			}
			?>

			<div class="remove-site" style="<?php echo ! $show_remove_site ? 'display: none' : ''; ?>">
				<h2><?php _e( 'Existing Site Import Detected', 'aureon-studio' ); ?></h2>

				<div class="remove-site-content">
					<p><?php _e( 'It is highly recommended that you remove the last site you imported before importing a new one.', 'aureon-studio' ); ?></p>
					<p><?php _e( 'This process restores your previous options, widgets and active plugins. It will also remove your imported content and CSS.', 'aureon-studio' ); ?></p>
				</div>

				<div class="remove-site-actions">
					<button class="do-remove-site button-primary"><?php _e( 'Remove Imported Site', 'aureon-studio' ); ?></button>
					<a class="skip-remove-site" href="#"><?php _e( 'Skip', 'aureon-studio' ); ?></a>

					<div class="loading" style="display: none;">
						<span class="remove-site-message"></span>
						<?php Aureon_Sites_Helper::loading_icon(); ?>
					</div>
				</div>
			</div>

			<div class="aureon-sites aureon-admin-block <?php echo $show_remove_site ? 'remove-site-needed' : ''; ?>" id="sites" data-page-builder=".no-page-builder">
				<?php do_action( 'aureon_inside_sites_container' ); ?>
			</div>

			<?php
			printf(
				'<div class="refresh-sites">
					<a data-nonce="%1$s" class="button" href="#">%2$s</a>
					<a class="button button-primary" href="%3$s" style="display: none;">%4$s</a>
				</div>',
				esc_html( wp_create_nonce( 'refresh_sites_nonce' ) ),
				__( 'Refresh Sites', 'aureon-studio' ),
				esc_url( admin_url( 'themes.php?page=aureon-site-library' ) ),
				__( 'Reload Page', 'aureon-studio' )
			);
			?>
		</div>
	</div>
	<?php
}

/**
 * Refresh our list of sites.
 *
 * @deprecated 2.0.0
 */
function aureon_sites_do_refresh_list() {
	check_ajax_referer( 'refresh_sites_nonce', '_nonce' );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( __( 'Security check failed.', 'aureon-studio' ) );
	}

	delete_transient( 'aureon_sites' );
	aureon_get_sites_from_library();

	wp_send_json_success();
}

/**
 * Get our page header meta slugs.
 *
 * @since 1.6
 * @deprecated 2.0.0
 *
 * @return array
 */
function aureon_sites_export_page_headers() {
	$args = array(
		'post_type' => get_post_types( array( 'public' => true ) ),
		'showposts' => -1,
		'meta_query' => array(
			array(
				'key' => '_aureon-select-page-header',
				'compare' => 'EXISTS',
			),
		),
	);

	$posts = get_posts( $args );
	$new_values = array();

	foreach ( $posts as $post ) {
		$page_header_id = get_post_meta( $post->ID, '_aureon-select-page-header', true );

		if ( $page_header_id ) {
			$new_values[ $post->ID ] = $page_header_id;
		}
	}

	return $new_values;
}

/**
 * Get our Element display locations.
 *
 * @since 1.7
 * @deprecated 2.0.0
 *
 * @return array
 */
function aureon_sites_export_elements_location() {
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
 * @since 1.7
 * @deprecated 2.0.0
 *
 * @return array
 */
function aureon_sites_export_elements_exclusion() {
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

/**
 * List out compatible theme modules Sites can activate.
 *
 * @since 1.6
 * @deprecated 2.0.0
 *
 * @return array
 */
function aureon_get_site_premium_modules() {
	return array(
		'Backgrounds' => 'aureon_package_backgrounds',
		'Blog' => 'aureon_package_blog',
		'Colors' => 'aureon_package_colors',
		'Copyright' => 'aureon_package_copyright',
		'Elements' => 'aureon_package_elements',
		'Disable Elements' => 'aureon_package_disable_elements',
		'Hooks' => 'aureon_package_hooks',
		'Menu Plus' => 'aureon_package_menu_plus',
		'Page Header' => 'aureon_package_page_header',
		'Secondary Nav' => 'aureon_package_secondary_nav',
		'Sections' => 'aureon_package_sections',
		'Spacing' => 'aureon_package_spacing',
		'Typography' => 'aureon_package_typography',
		'WooCommerce' => 'aureon_package_woocommerce',
	);
}

/**
 * Don't allow Sites to modify these options.
 *
 * @since 1.6
 * @deprecated 2.0.0
 *
 * @return array
 */
function aureon_sites_disallowed_options() {
	return array(
		'admin_email',
		'siteurl',
		'home',
		'blog_charset',
		'blog_public',
		'current_theme',
		'stylesheet',
		'template',
		'default_role',
		'mailserver_login',
		'mailserver_pass',
		'mailserver_port',
		'mailserver_url',
		'permalink_structure',
		'rewrite_rules',
		'users_can_register',
	);
}

/**
 * Add our Aureon Site export checkbox to the Export module.
 *
 * @since 1.7
 * @deprecated 2.0.0
 */
function aureon_sites_add_export_checkbox() {
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
 * @since 1.6
 * @deprecated 2.0.0
 *
 * @param array $data The current data being exported.
 * @return array Existing and extended data.
 */
function aureon_sites_do_site_options_export( $data ) {
	// Bail if we haven't chosen to export the Site.
	if ( ! in_array( 'aureon-site', $_POST['module_group'] ) ) { // phpcs:ignore -- No processing happening here.
		return $data;
	}

	// Modules.
	$modules = aureon_get_site_premium_modules();

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

	// Page header.
	$data['site_options']['page_header_global_locations'] = get_option( 'aureon_page_header_global_locations' );
	$data['site_options']['page_headers'] = aureon_sites_export_page_headers();

	// Elements.
	$data['site_options']['element_locations'] = aureon_sites_export_elements_location();
	$data['site_options']['element_exclusions'] = aureon_sites_export_elements_exclusion();

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
 * Get our sites from the site server.
 *
 * @since 1.12.0'
 * @deprecated 2.0.0
 */
function aureon_get_sites_from_library() {
	$remote_sites = get_transient( 'aureon_sites' );
	$trusted_authors = get_transient( 'aureon_sites_trusted_providers' );

	if ( empty( $remote_sites ) ) {
		$sites = array();

		$data = wp_safe_remote_get( 'https://sites.aureonstudio.com/wp-json/wp/v2/sites?per_page=100' );

		if ( is_wp_error( $data ) ) {
			set_transient( 'aureon_sites', 'no results', 5 * MINUTE_IN_SECONDS );
			return;
		}

		$data = json_decode( wp_remote_retrieve_body( $data ), true );

		if ( ! is_array( $data ) ) {
			set_transient( 'aureon_sites', 'no results', 5 * MINUTE_IN_SECONDS );
			return;
		}

		foreach ( (array) $data as $site ) {
			$sites[ $site['name'] ] = array(
				'name'          => $site['name'],
				'directory'     => $site['directory'],
				'preview_url'   => $site['preview_url'],
				'author_name'   => $site['author_name'],
				'author_url'    => $site['author_url'],
				'description'   => $site['description'],
				'page_builder'  => $site['page_builder'],
				'min_version'   => $site['min_version'],
				'uploads_url'   => $site['uploads_url'],
				'plugins'       => $site['plugins'],
				'documentation' => $site['documentation'],
			);
		}

		$sites = apply_filters( 'aureon_add_sites', $sites );

		set_transient( 'aureon_sites', $sites, 24 * HOUR_IN_SECONDS );
	}

	if ( empty( $trusted_authors ) ) {
		$trusted_authors = wp_safe_remote_get( 'https://sites.aureonstudio.com/wp-json/sites/site' );

		if ( is_wp_error( $trusted_authors ) || empty( $trusted_authors ) ) {
			set_transient( 'aureon_sites_trusted_providers', 'no results', 5 * MINUTE_IN_SECONDS );
			return;
		}

		$trusted_authors = json_decode( wp_remote_retrieve_body( $trusted_authors ), true );

		$authors = array();
		foreach ( (array) $trusted_authors['trusted_author'] as $author ) {
			$authors[] = $author;
		}

		set_transient( 'aureon_sites_trusted_providers', $authors, 24 * HOUR_IN_SECONDS );
	}
}

/**
 * Fetch our sites and trusted authors. Stores them in their own transients.
 * We use current_screen instead of admin_init so we can check what admin page we're on.
 *
 * @since 1.6
 * @deprecated 2.0.0
 */
function aureon_sites_init() {
	$screen = get_current_screen();

	if ( 'appearance_page_aureon-options' === $screen->id || 'appearance_page_aureon-site-library' === $screen->id ) {
		aureon_get_sites_from_library();
	}
}

/**
 * Initiate our Sites once everything has loaded.
 * We use current_screen instead of admin_init so we can check what admin page we're on.
 *
 * @since 1.6
 * @deprecated 2.0.0
 */
function aureon_sites_output() {
	if ( ! class_exists( 'Aureon_Site' ) ) {
		return; // Bail if we don't have the needed class.
	}

	$sites = get_transient( 'aureon_sites' );

	if ( empty( $sites ) || ! is_array( $sites ) ) {
		add_action( 'aureon_inside_sites_container', 'aureon_sites_no_results_error' );
		return;
	}

	if ( apply_filters( 'aureon_sites_randomize', false ) ) {
		shuffle( $sites );
	}

	foreach ( $sites as $site ) {
		new Aureon_Site( $site );
	}
}

/**
 * Show an error message when no sites exist.
 *
 * @since 1.8.2
 * @deprecated 2.0.0
 */
function aureon_sites_no_results_error() {
	printf(
		'<div class="no-site-library-results">
			%1$s <a href="%3$s" target="_blank" rel="noopener noreferrer">%2$s</a>
		</div>',
		__( 'No sites found.', 'aureon-studio' ),
		__( 'Why?', 'aureon-studio' ),
		'https://docs.aureonstudio.com/article/site-library-unavailable/'
	);
}

/**
 * Build each site UI.
 *
 * @deprecated 2.0.0
 */
class Aureon_Site {
	/**
	 * Get it rockin'
	 *
	 * @param array $config The site configuration.
	 */
	public function __construct( $config = array() ) {
		// Do nothing.
	}
}
