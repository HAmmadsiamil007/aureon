<?php
/**
 * Rest API functions
 *
 * @package Aureon Studio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Aureon_Pro_Rest
 */
class Aureon_Pro_Rest extends WP_REST_Controller {
	/**
	 * Instance.
	 *
	 * @access private
	 * @var object Instance
	 */
	private static $instance;

	/**
	 * Namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'aureon-pro/v';

	/**
	 * Version.
	 *
	 * @var string
	 */
	protected $version = '1';

	/**
	 * Initiator.
	 *
	 * @return object initialized object of class.
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Aureon_Pro_Rest constructor.
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Register rest routes.
	 */
	public function register_routes() {
		$namespace = $this->namespace . $this->version;

		register_rest_route(
			$namespace,
			'/modules/',
			array(
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => array( $this, 'update_module' ),
				'permission_callback' => array( $this, 'update_settings_permission' ),
			)
		);

		register_rest_route(
			$namespace,
			'/export/',
			array(
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => array( $this, 'export' ),
				'permission_callback' => array( $this, 'update_settings_permission' ),
			)
		);

		register_rest_route(
			$namespace,
			'/import/',
			array(
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => array( $this, 'import' ),
				'permission_callback' => array( $this, 'update_settings_permission' ),
			)
		);

		register_rest_route(
			$namespace,
			'/reset/',
			array(
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => array( $this, 'reset' ),
				'permission_callback' => array( $this, 'update_settings_permission' ),
			)
		);
	}

	/**
	 * Get edit options permissions.
	 *
	 * @return bool
	 */
	public function update_settings_permission() {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Update modules.
	 *
	 * @param WP_REST_Request $request request object.
	 *
	 * @return mixed
	 */
	public function update_module( WP_REST_Request $request ) {
		$module_key = $request->get_param( 'key' );
		$action = $request->get_param( 'action' );
		$current_setting = get_option( $module_key, false );
		$modules = Aureon_Pro_Dashboard::get_modules();
		$safe_module_keys = array();

		foreach ( $modules as $key => $data ) {
			$safe_module_keys[] = $data['key'];
		}

		if ( ! in_array( $module_key, $safe_module_keys ) ) {
			return $this->failed( 'Bad module key.' );
		}

		$message = '';

		if ( 'activate' === $action ) {
			update_option( $module_key, 'activated' );
			$message = __( 'Module activated.', 'aureon-studio' );
		}

		if ( 'deactivate' === $action ) {
			update_option( $module_key, 'deactivated' );
			$message = __( 'Module deactivated.', 'aureon-studio' );
		}

		return $this->success( $message );
	}

	/**
	 * Export settings.
	 *
	 * @param WP_REST_Request $request request object.
	 *
	 * @return mixed
	 */
	public function export( WP_REST_Request $request ) {
		$exportable_modules = $request->get_param( 'items' );

		if ( ! $exportable_modules ) {
			$exportable_modules = Aureon_Pro_Dashboard::get_exportable_modules();
		}

		$export_type = $request->get_param( 'type' );

		if ( 'all' === $export_type ) {
			$data = array(
				'modules' => array(),
				'mods' => array(),
				'options' => array(),
			);

			$module_settings = array();

			foreach ( $exportable_modules as $exported_module_key => $exported_module_data ) {
				if ( isset( $exported_module_data['settings'] ) ) {
					$module_settings[] = $exported_module_data['settings'];
				}
			}

			$modules = Aureon_Pro_Dashboard::get_modules();

			// Export module status of the exported options.
			foreach ( $modules as $module_key => $module_data ) {
				if ( isset( $module_data['settings'] ) && in_array( $module_data['settings'], $module_settings ) ) {
					$data['modules'][ $module_key ] = $module_data['key'];
				}
			}

			$theme_mods = Aureon_Pro_Dashboard::get_theme_mods();

			foreach ( $theme_mods as $theme_mod ) {
				if ( 'aureon_copyright' === $theme_mod ) {
					if ( in_array( 'copyright', $module_settings ) ) {
						$data['mods'][ $theme_mod ] = get_theme_mod( $theme_mod );
					}
				} else {
					if ( in_array( 'aureon_settings', $module_settings ) ) {
						$data['mods'][ $theme_mod ] = get_theme_mod( $theme_mod );
					}
				}
			}

			$settings = Aureon_Pro_Dashboard::get_setting_keys();

			foreach ( $settings as $setting ) {
				if ( in_array( $setting, $module_settings ) ) {
					$data['options'][ $setting ] = get_option( $setting );
				}
			}
		}

		if ( 'global-colors' === $export_type ) {
			$data['global-colors'] = aureon_get_option( 'global_colors' );
		}

		if ( 'typography' === $export_type ) {
			$data['font-manager'] = aureon_get_option( 'font_manager' );
			$data['typography'] = aureon_get_option( 'typography' );
		}

		$data = apply_filters( 'aureon_export_data', $data, $export_type );

		return $this->success( $data );
	}

	/**
	 * Import settings.
	 *
	 * @param WP_REST_Request $request request object.
	 *
	 * @return mixed
	 */
	public function import( WP_REST_Request $request ) {
		$settings = $request->get_param( 'import' );

		if ( empty( $settings ) ) {
			$this->failed( __( 'No settings to import.', 'aureon-studio' ) );
		}

		if ( ! empty( $settings['typography'] ) ) {
			$existing_settings = get_option( 'aureon_settings', array() );
			$existing_settings['typography'] = $settings['typography'];

			if ( ! empty( $settings['font-manager'] ) ) {
				$existing_settings['font_manager'] = $settings['font-manager'];
			}

			update_option( 'aureon_settings', $existing_settings );
		} elseif ( ! empty( $settings['global-colors'] ) ) {
			$existing_settings = get_option( 'aureon_settings', array() );
			$existing_settings['global_colors'] = $settings['global-colors'];

			update_option( 'aureon_settings', $existing_settings );
		} else {
			$modules = Aureon_Pro_Dashboard::get_modules();

			foreach ( (array) $settings['modules'] as $key => $val ) {
				if ( isset( $modules[ $key ] ) && in_array( $val, $modules[ $key ] ) ) {
					update_option( $val, 'activated' );
				}
			}

			foreach ( (array) $settings['mods'] as $key => $val ) {
				if ( in_array( $key, Aureon_Pro_Dashboard::get_theme_mods() ) ) {
					set_theme_mod( $key, $val );
				}
			}

			foreach ( (array) $settings['options'] as $key => $val ) {
				if ( in_array( $key, Aureon_Pro_Dashboard::get_setting_keys() ) ) {
					update_option( $key, $val );
				}
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

		return $this->success( __( 'Settings imported.', 'aureon-studio' ) );
	}

	/**
	 * Reset settings.
	 *
	 * @param WP_REST_Request $request request object.
	 *
	 * @return mixed
	 */
	public function reset( WP_REST_Request $request ) {
		$reset_items = $request->get_param( 'items' );

		if ( ! $reset_items ) {
			$reset_items = Aureon_Pro_Dashboard::get_exportable_modules();
		}

		$module_settings = array();

		foreach ( $reset_items as $reset_module_key => $reset_module_data ) {
			if ( isset( $reset_module_data['settings'] ) ) {
				$module_settings[] = $reset_module_data['settings'];
			}
		}

		$theme_mods = Aureon_Pro_Dashboard::get_theme_mods();

		foreach ( $theme_mods as $theme_mod ) {
			if ( 'aureon_copyright' === $theme_mod ) {
				if ( in_array( 'copyright', $module_settings ) ) {
					remove_theme_mod( $theme_mod );
				}
			} else {
				if ( in_array( 'aureon_settings', $module_settings ) ) {
					remove_theme_mod( $theme_mod );
				}
			}
		}

		$settings = Aureon_Pro_Dashboard::get_setting_keys();

		foreach ( $settings as $setting ) {
			if ( in_array( $setting, $module_settings ) ) {
				delete_option( $setting );
			}
		}

		// Delete our dynamic CSS option.
		delete_option( 'aureon_dynamic_css_output' );
		delete_option( 'aureon_dynamic_css_cached_version' );

		// Reset our dynamic CSS file updated time so it regenerates.
		$dynamic_css_data = get_option( 'aureon_dynamic_css_data', array() );

		if ( isset( $dynamic_css_data['updated_time'] ) ) {
			unset( $dynamic_css_data['updated_time'] );
		}

		update_option( 'aureon_dynamic_css_data', $dynamic_css_data );

		// Delete any Aureon Site CSS in Additional CSS.
		$additional_css = wp_get_custom_css_post();

		if ( ! empty( $additional_css ) ) {
			$additional_css->post_content = preg_replace( '#(/\\* Aureon Site CSS \\*/).*?(/\\* End Aureon Site CSS \\*/)#s', '', $additional_css->post_content );
			wp_update_custom_css_post( $additional_css->post_content );
		}

		return $this->success( __( 'Settings reset.', 'aureon-studio' ) );
	}

	/**
	 * Success rest.
	 *
	 * @param mixed $response response data.
	 * @return mixed
	 */
	public function success( $response ) {
		return new WP_REST_Response(
			array(
				'success'  => true,
				'response' => $response,
			),
			200
		);
	}

	/**
	 * Failed rest.
	 *
	 * @param mixed $response response data.
	 * @return mixed
	 */
	public function failed( $response ) {
		return new WP_REST_Response(
			array(
				'success'  => false,
				'response' => $response,
			),
			200
		);
	}

	/**
	 * Error rest.
	 *
	 * @param mixed $code     error code.
	 * @param mixed $response response data.
	 * @return mixed
	 */
	public function error( $code, $response ) {
		return new WP_REST_Response(
			array(
				'error'      => true,
				'success'    => false,
				'error_code' => $code,
				'response'   => $response,
			),
			401
		);
	}
}

Aureon_Pro_Rest::get_instance();
