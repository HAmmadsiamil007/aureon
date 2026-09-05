<?php
/**
 * This file handles the import/export functionality.
 *
 * @package Aureon Studio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

/**
 * Import/export class.
 */
class Aureon_Import_Export {
	/**
	 * Instance.
	 *
	 * @access private
	 * @var object Instance
	 * @since 1.7
	 */
	private static $instance;

	/**
	 * Initiator.
	 *
	 * @since 1.7
	 * @return object initialized object of class.
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Add necessary actions.
	 *
	 * @since 1.7
	 */
	public function __construct() {
		add_action( 'aureon_admin_right_panel', array( $this, 'build_html' ), 15 );
		add_action( 'admin_init', array( $this, 'export' ) );
		add_action( 'admin_init', array( $this, 'import' ) );
	}

	/**
	 * Build our export and import HTML.
	 *
	 * @since 1.7
	 */
	public static function build_html() {
		?>
		<div class="postbox aureon-metabox" id="aureon-ie">
			<h3 class="hndle"><?php esc_html_e( 'Import/Export', 'aureon-studio' ); ?></h3>
			<div class="inside">
				<form method="post">
					<h3 style="font-size: 15px;"><?php esc_html_e( 'Export', 'aureon-studio' ); ?></h3>
					<span class="show-advanced"><?php _e( 'Advanced', 'aureon-studio' ); ?></span>
					<div class="export-choices advanced-choices">
						<label>
							<input type="checkbox" name="module_group[]" value="aureon_settings" checked />
							<?php _ex( 'Core', 'Module name', 'aureon-studio' ); ?>
						</label>

						<?php if ( aureon_is_module_active( 'aureon_package_backgrounds', 'AUREON_BACKGROUNDS' ) ) : ?>
							<label>
								<input type="checkbox" name="module_group[]" value="aureon_background_settings" checked />
								<?php _ex( 'Backgrounds', 'Module name', 'aureon-studio' ); ?>
							</label>
						<?php endif; ?>

						<?php if ( aureon_is_module_active( 'aureon_package_blog', 'AUREON_BLOG' ) ) : ?>
							<label>
								<input type="checkbox" name="module_group[]" value="aureon_blog_settings" checked />
								<?php _ex( 'Blog', 'Module name', 'aureon-studio' ); ?>
							</label>
						<?php endif; ?>

						<?php if ( aureon_is_module_active( 'aureon_package_hooks', 'AUREON_HOOKS' ) ) : ?>
							<label>
								<input type="checkbox" name="module_group[]" value="aureon_hooks" checked />
								<?php _ex( 'Hooks', 'Module name', 'aureon-studio' ); ?>
							</label>
						<?php endif; ?>

						<?php if ( aureon_is_module_active( 'aureon_package_page_header', 'AUREON_PAGE_HEADER' ) ) : ?>
							<label>
								<input type="checkbox" name="module_group[]" value="aureon_page_header_settings" checked />
								<?php _ex( 'Page Header', 'Module name', 'aureon-studio' ); ?>
							</label>
						<?php endif; ?>

						<?php if ( aureon_is_module_active( 'aureon_package_secondary_nav', 'AUREON_SECONDARY_NAV' ) ) : ?>
							<label>
								<input type="checkbox" name="module_group[]" value="aureon_secondary_nav_settings" checked />
								<?php _ex( 'Secondary Navigation', 'Module name', 'aureon-studio' ); ?>
							</label>
						<?php endif; ?>

						<?php if ( aureon_is_module_active( 'aureon_package_spacing', 'AUREON_SPACING' ) ) : ?>
							<label>
								<input type="checkbox" name="module_group[]" value="aureon_spacing_settings" checked />
								<?php _ex( 'Spacing', 'Module name', 'aureon-studio' ); ?>
							</label>
						<?php endif; ?>

						<?php if ( aureon_is_module_active( 'aureon_package_menu_plus', 'AUREON_MENU_PLUS' ) ) : ?>
							<label>
								<input type="checkbox" name="module_group[]" value="aureon_menu_plus_settings" checked />
								<?php _ex( 'Menu Plus', 'Module name', 'aureon-studio' ); ?>
							</label>
						<?php endif; ?>

						<?php if ( aureon_is_module_active( 'aureon_package_woocommerce', 'AUREON_WOOCOMMERCE' ) ) : ?>
							<label>
								<input type="checkbox" name="module_group[]" value="aureon_woocommerce_settings" checked />
								<?php _ex( 'WooCommerce', 'Module name', 'aureon-studio' ); ?>
							</label>
						<?php endif; ?>

						<?php if ( aureon_is_module_active( 'aureon_package_copyright', 'AUREON_COPYRIGHT' ) ) : ?>
							<label>
								<input type="checkbox" name="module_group[]" value="copyright" checked />
								<?php _ex( 'Copyright', 'Module name', 'aureon-studio' ); ?>
							</label>
						<?php endif; ?>

						<?php do_action( 'aureon_export_items' ); ?>
					</div>
					<p><input type="hidden" name="aureon_action" value="export_settings" /></p>
					<p style="margin-bottom:0">
						<?php wp_nonce_field( 'aureon_export_nonce', 'aureon_export_nonce' ); ?>
						<?php submit_button( __( 'Export', 'aureon-studio' ), 'button-primary', 'submit', false, array( 'id' => '' ) ); ?>
					</p>
				</form>

				<h3 style="font-size: 15px;margin-top: 30px;"><?php esc_html_e( 'Import', 'aureon-studio' ); ?></h3>
				<form method="post" enctype="multipart/form-data">
					<p>
						<input type="file" name="import_file"/>
					</p>
					<p style="margin-bottom:0">
						<input type="hidden" name="aureon_action" value="import_settings" />
						<?php wp_nonce_field( 'aureon_import_nonce', 'aureon_import_nonce' ); ?>
						<?php submit_button( __( 'Import', 'aureon-studio' ), 'button-primary', 'submit', false, array( 'id' => '' ) ); ?>
					</p>
				</form>
			</div>
		</div>
		<?php
	}

	/**
	 * Export our chosen options.
	 *
	 * @since 1.7
	 */
	public static function export() {
		if ( empty( $_POST['aureon_action'] ) || 'export_settings' !== $_POST['aureon_action'] ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['aureon_export_nonce'], 'aureon_export_nonce' ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$modules = self::get_modules();
		$theme_mods = self::get_theme_mods();
		$settings = self::get_settings();

		$data = array(
			'modules' => array(),
			'mods' => array(),
			'options' => array(),
		);

		foreach ( $modules as $name => $value ) {
			if ( 'activated' === get_option( $value ) ) {
				$data['modules'][ $name ] = $value;
			}
		}

		foreach ( $theme_mods as $theme_mod ) {
			if ( 'aureon_copyright' === $theme_mod ) {
				if ( in_array( 'copyright', $_POST['module_group'] ) ) {
					$data['mods'][ $theme_mod ] = get_theme_mod( $theme_mod );
				}
			} else {
				if ( in_array( 'aureon_settings', $_POST['module_group'] ) ) {
					$data['mods'][ $theme_mod ] = get_theme_mod( $theme_mod );
				}
			}
		}

		foreach ( $settings as $setting ) {
			if ( in_array( $setting, $_POST['module_group'] ) ) {
				$data['options'][ $setting ] = get_option( $setting );
			}
		}

		$data = apply_filters( 'aureon_export_data', $data );

		nocache_headers();
		header( 'Content-Type: application/json; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=aureon-settings-export-' . date( 'Ymd' ) . '.json' ); // phpcs:ignore -- Prefer date().
		header( 'Expires: 0' );

		echo wp_json_encode( $data );
		exit;
	}

	/**
	 * Import our exported file.
	 *
	 * @since 1.7
	 */
	public static function import() {
		if ( empty( $_POST['aureon_action'] ) || 'import_settings' !== $_POST['aureon_action'] ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['aureon_import_nonce'], 'aureon_import_nonce' ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$filename = $_FILES['import_file']['name'];
		$extension = end( explode( '.', $_FILES['import_file']['name'] ) );

		if ( 'json' !== $extension ) {
			wp_die( __( 'Please upload a valid .json file', 'aureon-studio' ) );
		}

		$import_file = $_FILES['import_file']['tmp_name'];

		if ( empty( $import_file ) ) {
			wp_die( __( 'Please upload a file to import', 'aureon-studio' ) );
		}

		// Retrieve the settings from the file and convert the json object to an array.
		$settings = json_decode( file_get_contents( $import_file ), true ); // phpcs:ignore -- file_get_contents() is fine here.

		foreach ( (array) $settings['modules'] as $key => $val ) {
			if ( in_array( $val, self::get_modules() ) ) {
				update_option( $val, 'activated' );
			}
		}

		foreach ( (array) $settings['mods'] as $key => $val ) {
			if ( in_array( $key, self::get_theme_mods() ) ) {
				set_theme_mod( $key, $val );
			}
		}

		foreach ( (array) $settings['options'] as $key => $val ) {
			if ( in_array( $key, self::get_settings() ) ) {
				update_option( $key, $val );
			}
		}

		// Delete existing dynamic CSS cache.
		delete_option( 'aureon_dynamic_css_output' );
		delete_option( 'aureon_dynamic_css_cached_version' );

		$dynamic_css_data = get_option( 'aureon_dynamic_css_data', array() );

		if ( isset( $dynamic_css_data['updated_time'] ) ) {
			unset( $dynamic_css_data['updated_time'] );
		}

		update_option( 'aureon_dynamic_css_data', $dynamic_css_data );

		wp_safe_redirect( admin_url( 'admin.php?page=aureon-options&status=imported' ) );
		exit;
	}

	/**
	 * List out our available modules.
	 *
	 * @since 1.7
	 */
	public static function get_modules() {
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
	 * List our our set theme mods.
	 *
	 * @since 1.7
	 */
	public static function get_theme_mods() {
		return array(
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
	}

	/**
	 * List out our available settings.
	 *
	 * @since 1.7
	 */
	public static function get_settings() {
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
}

Aureon_Import_Export::get_instance();
