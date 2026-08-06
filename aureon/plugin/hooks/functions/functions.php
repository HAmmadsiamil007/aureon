<?php
/**
 * This file handles the legacy hook system.
 *
 * @package Aureon Studio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

// Add any necessary files.
require plugin_dir_path( __FILE__ ) . 'hooks.php';

if ( ! function_exists( 'aureon_hooks_get_hooks' ) ) {
	/**
	 * Get our list of hooks.
	 */
	function aureon_hooks_get_hooks() {
		$hooks = array(
			'aureon_wp_head_php',
			'aureon_wp_head',
			'aureon_before_header_php',
			'aureon_before_header',
			'aureon_before_header_content_php',
			'aureon_before_header_content',
			'aureon_after_header_content_php',
			'aureon_after_header_content',
			'aureon_after_header_php',
			'aureon_after_header',
			'aureon_before_main_content_php',
			'aureon_before_main_content',
			'aureon_before_content_php',
			'aureon_before_content',
			'aureon_after_entry_header_php',
			'aureon_after_entry_header',
			'aureon_after_content_php',
			'aureon_after_content',
			'aureon_before_right_sidebar_content_php',
			'aureon_before_right_sidebar_content',
			'aureon_after_right_sidebar_content_php',
			'aureon_after_right_sidebar_content',
			'aureon_before_left_sidebar_content_php',
			'aureon_before_left_sidebar_content',
			'aureon_after_left_sidebar_content_php',
			'aureon_after_left_sidebar_content',
			'aureon_before_footer_php',
			'aureon_before_footer',
			'aureon_after_footer_widgets_php',
			'aureon_after_footer_widgets',
			'aureon_before_footer_content_php',
			'aureon_before_footer_content',
			'aureon_after_footer_content_php',
			'aureon_after_footer_content',
			'aureon_wp_footer_php',
			'aureon_wp_footer',
		);

		return $hooks;
	}
}

if ( ! function_exists( 'aureon_hooks_php_check' ) ) {
	add_action( 'admin_notices', 'aureon_hooks_php_check' );
	/**
	 * Checks if DISALLOW_FILE_EDIT is defined.
	 * If it is, tell the user to disallow PHP execution in Aureon Hooks.
	 *
	 * @since 1.3.1
	 */
	function aureon_hooks_php_check() {
		if ( defined( 'DISALLOW_FILE_EDIT' ) && DISALLOW_FILE_EDIT && ! defined( 'AUREON_HOOKS_DISALLOW_PHP' ) && current_user_can( 'manage_options' ) ) {
			printf(
				'<div class="notice notice-error">
					<p>%1$s <a href="https://docs.aureonstudio.com/article/disallow-php-execution/" target="_blank">%2$s</a></p>
				</div>',
				esc_html__( 'DISALLOW_FILE_EDIT is defined. You should also disallow PHP execution in Aureon Hooks.', 'aureon-studio' ),
				esc_html__( 'Learn how', 'aureon-studio' )
			);
		}
	}
}

if ( ! function_exists( 'aureon_hooks_setup' ) ) {
	function aureon_hooks_setup() {
		// Just to verify that we're activated.
	}
}

if ( ! class_exists( 'Aureon_Hooks_Settings' ) ) {
	class Aureon_Hooks_Settings {
	    private $dir;
		private $file;
		private $assets_dir;
		private $assets_url;
		private $settings_base;
		private $settings;

		public function __construct( $file ) {
			$this->file = $file;
			$this->dir = dirname( $this->file );
			$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
			$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $this->file ) ) );
			$this->settings_base = '';

			// Initialise settings
			add_action( 'admin_init', array( $this, 'init' ) );

			// Register plugin settings
			add_action( 'admin_init' , array( $this, 'register_settings' ) );

			// Add settings page to menu
			add_action( 'admin_menu' , array( $this, 'add_menu_item' ) );

			// Add settings link to plugins page
			add_filter( 'plugin_action_links_' . plugin_basename( $this->file ) , array( $this, 'add_settings_link' ) );
		}

		/**
		 * Initialise settings
		 * @return void
		 */
		public function init() {
			$this->settings = $this->settings_fields();
		}

		/**
		 * Add settings page to admin menu
		 * @return void
		 */
		public function add_menu_item() {
			$page = add_theme_page( __( 'Aureon Hooks', 'aureon-studio' ) , __( 'Aureon Hooks', 'aureon-studio' ) , apply_filters( 'aureon_hooks_capability','manage_options' ) , 'aureon_hooks_settings' ,  array( $this, 'settings_page' ) );
			add_action( 'admin_print_styles-' . $page, array( $this, 'settings_assets' ) );
		}

		/**
		 * Load settings JS & CSS
		 * @return void
		 */
		public function settings_assets() {
			wp_enqueue_script( 'aureon-cookie', $this->assets_url . 'js/jquery.cookie.js', array( 'jquery' ), AUREON_HOOKS_VERSION );
			wp_enqueue_script( 'aureon-hooks', $this->assets_url . 'js/admin.js', array( 'jquery', 'aureon-cookie' ), AUREON_HOOKS_VERSION );
			wp_enqueue_style( 'aureon-hooks', $this->assets_url . 'css/hooks.css' );
		}

		/**
		 * Add settings link to plugin list table
		 * @param  array $links Existing links
		 * @return array 		Modified links
		 */
		public function add_settings_link( $links ) {
			$settings_link = '<a href="options-general.php?page=aureon_hooks_settings">' . __( 'Aureon Hooks', 'aureon-studio' ) . '</a>';
	  		array_push( $links, $settings_link );
	  		return $links;
		}

		/**
		 * Build settings fields
		 * @return array Fields to be displayed on settings page
		 */
		private function settings_fields() {

			$settings['standard'] = array(
				'title'					=> '',
				'description'			=> '',
				'fields'				=> array(
					array(
						"name" => __( 'wp_head', 'aureon-studio' ),
						"id" => 'aureon_wp_head',
						"type" => 'textarea'
					),

					array(
						"name" => __( 'Before Header', 'aureon-studio' ),
						"id" => 'aureon_before_header',
						"type" => 'textarea'
					),

					array(
						"name" => __( 'Before Header Content', 'aureon-studio' ),
						"id" => 'aureon_before_header_content',
						"type" => 'textarea'
					),

					array(
						"name" => __( 'After Header Content', 'aureon-studio' ),
						"id" => 'aureon_after_header_content',
						"type" => 'textarea'
					),

					array(
						"name" => __( 'After Header', 'aureon-studio' ),
						"id" => 'aureon_after_header',
						"type" => 'textarea'
					),

					array(
						"name" => __( 'Inside Content Container', 'aureon-studio' ),
						"id" => 'aureon_before_main_content',
						"type" => 'textarea'
					),

					array(
						"name" => __( 'Before Content', 'aureon-studio' ),
						"id" => 'aureon_before_content',
						"type" => 'textarea'
					),

					array(
						"name" => __( 'After Entry Title', 'aureon-studio' ),
						"id" => 'aureon_after_entry_header',
						"type" => 'textarea'
					),

					array(
						"name" => __( 'After Content', 'aureon-studio' ),
						"id" => 'aureon_after_content',
						"type" => 'textarea'
					),

					array(
						"name" => __( 'Before Right Sidebar Content', 'aureon-studio' ),
						"id" => 'aureon_before_right_sidebar_content',
						"type" => 'textarea'
					),

					array(
						"name" => __( 'After Right Sidebar Content', 'aureon-studio' ),
						"id" => 'aureon_after_right_sidebar_content',
						"type" => 'textarea'
					),

					array(
						"name" => __( 'Before Left Sidebar Content', 'aureon-studio' ),
						"id" => 'aureon_before_left_sidebar_content',
						"type" => 'textarea'
					),

					array(
						"name" => __( 'After Left Sidebar Content', 'aureon-studio' ),
						"id" => 'aureon_after_left_sidebar_content',
						"type" => 'textarea'
					),

					array(
						"name" => __( 'Before Footer', 'aureon-studio' ),
						"id" => 'aureon_before_footer',
						"type" => 'textarea'
					),

					array(
						"name" => __( 'After Footer Widgets', 'aureon-studio' ),
						"id" => 'aureon_after_footer_widgets',
						"type" => 'textarea'
					),

					array(
						"name" => __( 'Before Footer Content', 'aureon-studio' ),
						"id" => 'aureon_before_footer_content',
						"type" => 'textarea'
					),

					array(
						"name" => __( 'After Footer Content', 'aureon-studio' ),
						"id" => 'aureon_after_footer_content',
						"type" => 'textarea'
					),

					array(
						"name" => __( 'wp_footer', 'aureon-studio' ),
						"id" => 'aureon_wp_footer',
						"type" => 'textarea'
					)
				)
			);

			$settings = apply_filters( 'aureon_hooks_settings_fields', $settings );

			return $settings;
		}

		/**
		 * Register plugin settings
		 * @return void
		 */
		public function register_settings() {
			if ( is_array( $this->settings ) ) {
				foreach( $this->settings as $section => $data ) {

					// Add section to page
					add_settings_section( $section, $data['title'], array( $this, 'settings_section' ), 'aureon_hooks_settings' );

					foreach( $data['fields'] as $field ) {

						// Sanitizing isn't possible, as hooks allow any HTML, JS or PHP to be added.
						// Allowing PHP can be a security issue if you have admin users who you don't trust.
						// In that case, you can disable the ability to add PHP in hooks like this: define( 'AUREON_HOOKS_DISALLOW_PHP', true );
						$validation = '';
						if( isset( $field['callback'] ) ) {
							$validation = $field['callback'];
						}

						// Register field
						$option_name = $this->settings_base . $field['id'];
						register_setting( 'aureon_hooks_settings', 'aureon_hooks', $validation );

						// Add field to page
						add_settings_field( 'aureon_hooks[' . $field['id'] . ']', $field['name'], array( $this, 'display_field' ), 'aureon_hooks_settings', $section, array( 'field' => $field ) );
					}
				}
			}
		}

		public function settings_section( $section ) {
			$html = '';
			echo $html;
		}

		/**
		 * Generate HTML for displaying fields
		 * @param  array $args Field data
		 * @return void
		 */
		public function display_field( $args ) {

			$field = $args['field'];

			$html = '';

			$option_name = $this->settings_base . $field['id'];
			$option = get_option( 'aureon_hooks' );

			$data = '';
			if( isset( $option[$option_name] ) ) {
				$data = $option[$option_name];
			} elseif( isset( $field['default'] ) ) {
				$data = $field['default'];
			}


			switch( $field['type'] ) {

				case 'textarea':
					$checked = '';
					$checked2 = '';
					if( isset( $option[$field['id'] . '_php'] ) && 'true' == $option[$field['id'] . '_php'] ){
						$checked = 'checked="checked"';
					}
					if( isset( $option[$field['id'] . '_disable'] ) && 'true' == $option[$field['id'] . '_disable'] ){
						$checked2 = 'checked="checked"';
					}
					$html .= '<textarea autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" id="aureon_hooks[' . esc_attr( $field['id'] ) . ']" name="aureon_hooks[' . esc_attr( $field['id'] ) . ']" style="width:100%;height:200px;" cols="" rows="">' . esc_textarea( $data ) . '</textarea>';

					if ( ! defined( 'AUREON_HOOKS_DISALLOW_PHP' ) ) {
						$html .= '<div class="execute"><input type="checkbox" name="aureon_hooks[' . esc_attr( $field['id'] ) . '_php]" id="aureon_hooks[' . esc_attr( $field['id'] ) . '_php]" value="true" ' . $checked . ' /> <label for="aureon_hooks[' . esc_attr( $field['id'] ) . '_php]">' . __( 'Execute PHP', 'aureon-studio' ) . '</label></div>';
					}
					$html .= '<div class="disable"><input type="checkbox" name="aureon_hooks[' . esc_attr( $field['id'] ) . '_disable]" id="aureon_hooks[' . esc_attr( $field['id'] ) . '_disable]" value="true" ' . $checked2 . ' /> <label for="aureon_hooks[' . esc_attr( $field['id'] ) . '_disable]" class="disable">' . __( 'Disable Hook', 'aureon-studio' ) . '</label></div>';
				break;

				case 'checkbox':


				break;

			}

			echo $html;
		}

		/**
		 * Validate individual settings field
		 * @param  string $data Inputted value
		 * @return string       Validated value
		 */
		public function validate_field( $data ) {
			if ( $data && strlen( $data ) > 0 && $data != '' ) {
				$data = urlencode( strtolower( str_replace( ' ' , '-' , $data ) ) );
			}
			return $data;
		}

		/**
		 * Load settings page content
		 * @return void
		 */
		public function settings_page() {

			// Build page HTML
			$html = '<div class="wrap" id="aureon_hooks_settings">';
				$html .= '<div id="poststuff">';
				$html .= '<div class="metabox-holder columns-2" id="post-body">';
				$html .= '<form method="post" action="options.php" enctype="multipart/form-data">';
					$html .= '<div id="post-body-content">';
						// Get settings fields
						ob_start();
							settings_fields( 'aureon_hooks_settings' );
							do_settings_sections( 'aureon_hooks_settings' );
						$html .= ob_get_clean();
					$html .= '</div>';

					$html .= '<div id="postbox-container-1">';
						$html .= '<div class="postbox sticky-scroll-box">';
							$html .= '<h3 class="hndle">' . __( 'Aureon Hooks', 'aureon-studio' ) . '</h3>';
							$html .= '<div class="inside">';
								$html .= '<p>' . __( 'Use these fields to insert anything you like throughout Aureon. Shortcodes are allowed, and you can even use PHP if you check the Execute PHP checkboxes.', 'aureon-studio' ) . '</p>';
								$html .= '<select id="hook-dropdown" style="margin-top:20px;">';
									$html .= '<option value="all">' . __( 'Show all', 'aureon-studio' ) . '</option>';
									if( is_array( $this->settings ) ) {
										foreach( $this->settings as $section => $data ) {
											$count = 0;
											foreach( $data['fields'] as $field ) {
												$html .= '<option id="' . $count++ . '">' . $field['name'] . '</option>';
											}
										}
									}
								$html .= '</select>';
								$html .= '<p style="padding:0;margin:13px 0 0 0;" class="submit">';
									$html .= '<input name="Submit" type="submit" class="button-primary" value="' . esc_attr( __( 'Save Hooks', 'aureon-studio' ) ) . '" />';
								$html .= '</p>';
							$html .= '</div>';
						$html .= '</div>';
					$html .= '</div>';
				$html .= '</form>';
				$html .= '</div>';
				$html .= '<br class="clear" />';
			$html .= '</div>';
			$html .= '</div>';

			echo $html;
		}

	}
	$settings = new Aureon_Hooks_Settings( __FILE__ );
}

if ( ! function_exists( 'aureon_update_hooks' ) ) {
	add_action( 'admin_init', 'aureon_update_hooks' );
	/**
	 * Moving standalone db entries to aureon_hooks db entry
	 */
	function aureon_update_hooks() {
		$aureon_hooks = get_option( 'aureon_hooks' );

		// If we've done this before, bail
		if ( ! empty( $aureon_hooks ) ) {
			return;
		}

		// One last check
		if ( 'true' == $aureon_hooks['updated'] ) {
			return;
		}

		$hooks = aureon_hooks_get_hooks();
		$aureon_new_hooks = array();

		foreach ( $hooks as $hook ) {

			$current_hook = get_option( $hook );

			if ( isset( $current_hook ) && '' !== $current_hook ) {

				$aureon_new_hooks[ $hook ] = get_option( $hook );
				$aureon_new_hooks[ 'updated' ] = 'true';
				// Let's not delete the old options yet, just in case
				//delete_option( $hook );

			}

		}

		$aureon_new_hook_settings = wp_parse_args( $aureon_new_hooks, $aureon_hooks );
		update_option( 'aureon_hooks', $aureon_new_hook_settings );

	}
}

if ( ! function_exists( 'aureon_hooks_admin_errors' ) ) {
	add_action( 'admin_notices', 'aureon_hooks_admin_errors' );
	/**
	 * Add our admin notices
	 */
	function aureon_hooks_admin_errors() {
		$screen = get_current_screen();
		if ( 'appearance_page_aureon_hooks_settings' !== $screen->base ) {
			return;
		}

		if ( isset( $_GET['settings-updated'] ) && 'true' == $_GET['settings-updated'] ) {
			 add_settings_error( 'aureon-hook-notices', 'true', __( 'Hooks saved.', 'aureon-studio' ), 'updated' );
		}

		settings_errors( 'aureon-hook-notices' );
	}
}

add_action( 'admin_head', 'aureon_old_aureon_hooks_fix_menu' );
/**
 * Set our current menu in the admin while in the old Page Header pages.
 *
 * @since 1.7
 */
function aureon_old_aureon_hooks_fix_menu() {
	if ( ! function_exists( 'aureon_premium_do_elements' ) ) {
		return;
	}

	global $parent_file, $submenu_file, $post_type;

	$screen = get_current_screen();

	if ( 'appearance_page_aureon_hooks_settings' === $screen->base ) {
		$parent_file = 'themes.php';
		$submenu_file = 'edit.php?post_type=aureon_elements';
	}

	remove_submenu_page( 'themes.php', 'aureon_hooks_settings' );
}

add_action( 'admin_head', 'aureon_hooks_add_legacy_button', 999 );
/**
 * Add legacy buttons to our new Aureon Elements post type.
 *
 * @since 1.7
 */
function aureon_hooks_add_legacy_button() {
	if ( ! function_exists( 'aureon_premium_do_elements' ) ) {
		return;
	}

	$screen = get_current_screen();

	if ( 'aureon_elements' === $screen->post_type && 'edit' === $screen->base ) :
		?>
		<script>
			jQuery( function( $ ) {
				$( '<a href="<?php echo admin_url(); ?>themes.php?page=aureon_hooks_settings" class="page-title-action legacy-button"><?php esc_html_e( "Legacy Hooks", "aureon-studio" ); ?></a>' ).insertAfter( '.page-title-action:not(.legacy-button)' );
			} );
		</script>
		<?php
	endif;
}
