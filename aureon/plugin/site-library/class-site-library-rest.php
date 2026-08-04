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
 * Class Aureon_Site_Library_Rest
 */
class Aureon_Site_Library_Rest extends WP_REST_Controller {
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
	protected $namespace = 'aureon-site-library/v';

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
	 * GenerateBlocks_Rest constructor.
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
		add_action( 'init', array( 'Aureon_Site_Library_Helper', 'woocommerce_no_new_pages' ), 4 );
	}

	/**
	 * Register rest routes.
	 */
	public function register_routes() {
		$namespace = $this->namespace . $this->version;

		// Get Templates.
		register_rest_route(
			$namespace,
			'/get_sites/',
			array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_sites' ),
				'permission_callback' => array( $this, 'get_sites_permission' ),
			)
		);

		// Get Templates.
		register_rest_route(
			$namespace,
			'/get_site_data/',
			array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => array( $this, 'get_site_data' ),
				'permission_callback' => array( $this, 'update_settings_permission' ),
			)
		);

		// Get Templates.
		register_rest_route(
			$namespace,
			'/import_theme_options/',
			array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => array( $this, 'import_options' ),
				'permission_callback' => array( $this, 'update_settings_permission' ),
			)
		);

		// Get Templates.
		register_rest_route(
			$namespace,
			'/activate_plugins/',
			array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => array( $this, 'activate_plugins' ),
				'permission_callback' => array( $this, 'update_settings_permission' ),
			)
		);

		// Get Templates.
		register_rest_route(
			$namespace,
			'/import_content/',
			array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => array( $this, 'import_content' ),
				'permission_callback' => array( $this, 'update_settings_permission' ),
			)
		);

		// Get Templates.
		register_rest_route(
			$namespace,
			'/import_site_options/',
			array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => array( $this, 'import_site_options' ),
				'permission_callback' => array( $this, 'update_settings_permission' ),
			)
		);

		// Get Templates.
		register_rest_route(
			$namespace,
			'/import_widgets/',
			array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => array( $this, 'import_widgets' ),
				'permission_callback' => array( $this, 'update_settings_permission' ),
			)
		);

		// Get Templates.
		register_rest_route(
			$namespace,
			'/restore_theme_options/',
			array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => array( $this, 'restore_theme_options' ),
				'permission_callback' => array( $this, 'update_settings_permission' ),
			)
		);

		// Get Templates.
		register_rest_route(
			$namespace,
			'/restore_content/',
			array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => array( $this, 'restore_content' ),
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
	 * Get sites permissions.
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return bool
	 */
	public function get_sites_permission( WP_REST_Request $request ) {
		// Allow admin users.
		if ( current_user_can( 'manage_options' ) ) {
			return true;
		}

		// Allow public access if enabled via filter.
		return apply_filters( 'aureon_allow_public_site_library', false, $request );
	}

	/**
	 * Verify nonce for destructive operations.
	 *
	 * @param WP_REST_Request $request The request object.
	 * @return bool True if nonce is valid, false otherwise.
	 */
	private function verify_nonce( WP_REST_Request $request ) {
		$nonce = $request->get_header( 'X-WP-Nonce' );

		if ( ! $nonce ) {
			return false;
		}

		return wp_verify_nonce( $nonce, 'wp_rest' );
	}


	/**
	 * Export a group of assets.
	 *
	 * @param WP_REST_Request $request  request object.
	 *
	 * @return mixed
	 */
	public function get_sites( WP_REST_Request $request ) {
		// Check if this is a force refresh request (requires manage_options capability).
		$force_refresh = $request->get_param( 'forceRefresh' );

		if ( $force_refresh && ! current_user_can( 'manage_options' ) ) {
			$force_refresh = false;
		}

		$sites = get_option( 'aureon_sites', array() );

		$time_now = strtotime( 'now' );
		$sites_expire = get_option( 'aureon_sites_expiration', sanitize_text_field( $time_now ) );

		if ( $force_refresh || empty( $sites ) || $sites_expire < $time_now ) {
			$sites = array();

			$url = 'https://example.com/invalid';

			if ( defined( 'GENERATEBLOCKS_VERSION' ) ) {
				if ( ! function_exists( 'generateblocks_use_v1_blocks' ) || generateblocks_use_v1_blocks() ) {
					$url = 'https://example.com/invalid';
				}
			}

			$data = wp_safe_remote_get( $url );
		}

		// Site options.
		if ( ! empty( $backup_data['site_options'] ) ) {
			foreach ( $backup_data['site_options'] as $key => $val ) {
				if ( in_array( $key, (array) Aureon_Site_Library_Helper::disallowed_options() ) ) {
					Aureon_Site_Library_Helper::log( 'Disallowed option: ' . $key );
					continue;
				}

				if ( 'nav_menu_locations' === $key || 'custom_logo' === $key ) {
					set_theme_mod( $key, $val );
				} else {
					if ( ! $val && ! is_numeric( $val ) ) {
						delete_option( $key );
					} else {
						update_option( $key, $val );
					}
				}
			}
		}

		// Widgets.
		if ( ! empty( $backup_data['widgets'] ) ) {
			update_option( 'sidebars_widgets', $backup_data['widgets'] );
		}

		// CSS.
		$current_css = wp_get_custom_css_post();

		if ( isset( $current_css->post_content ) ) {
			// Remove existing library CSS.
			$current_css->post_content = preg_replace( '#(/\\* Aureon Site CSS \\*/).*?(/\\* End Aureon Site CSS \\*/)#s', '', $current_css->post_content );
		}

		wp_update_custom_css_post( $current_css->post_content );

		// Clean up.
		delete_option( 'aureon_dynamic_css_output' );
		delete_option( 'aureon_dynamic_css_cached_version' );
		delete_option( '_aureon_site_library_backup' );

		return $this->success( __( 'Content restored.', 'aureon-studio' ) );
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

Aureon_Site_Library_Rest::get_instance();
