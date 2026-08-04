<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_enqueue_scripts', 'aureon_premium_dashboard_scripts' );
/**
 * Enqueue scripts and styles for the GP Dashboard area.
 *
 * @since 1.6
 */
function aureon_premium_dashboard_scripts() {
	$screen = get_current_screen();

	if ( 'appearance_page_aureon-options' !== $screen->base ) {
		return;
	}

	wp_enqueue_style( 'aureon-premium-dashboard', plugin_dir_url( __FILE__ ) . 'assets/dashboard.css', array(), AUREON_STUDIO_VERSION );
	wp_enqueue_script( 'aureon-premium-dashboard', plugin_dir_url( __FILE__ ) . 'assets/dashboard.js', array( 'jquery' ), AUREON_STUDIO_VERSION, true );

	wp_localize_script(
		'aureon-premium-dashboard',
		'dashboard',
		array(
			'deprecated_module' => esc_attr__( 'This module has been deprecated. Deactivating it will remove it from this list.', 'aureon-studio' ),
		)
	);
}

if ( ! function_exists( 'aureon_premium_notices' ) ) {
	add_action( 'admin_notices', 'aureon_premium_notices' );
	/*
	* Set up errors and messages
	*/
	function aureon_premium_notices() {
		if ( isset( $_GET['aureon-message'] ) && 'addon_deactivated' == $_GET['aureon-message'] ) {
			 add_settings_error( 'aureon-premium-notices', 'addon_deactivated', __( 'Module deactivated.', 'aureon-studio' ), 'updated' );
		}

		if ( isset( $_GET['aureon-message'] ) && 'addon_activated' == $_GET['aureon-message'] ) {
			 add_settings_error( 'aureon-premium-notices', 'addon_activated', __( 'Module activated.', 'aureon-studio' ), 'updated' );
		}

		settings_errors( 'aureon-premium-notices' );
	}
}

if ( ! function_exists( 'aureon_license_errors' ) ) {
	add_action( 'admin_notices', 'aureon_license_errors' );
	/*
	* Set up errors and messages
	*/
	function aureon_license_errors() {
		if ( isset( $_GET['aureon-message'] ) && 'deactivation_passed' == $_GET['aureon-message'] ) {
			add_settings_error( 'aureon-license-notices', 'deactivation_passed', __( 'License deactivated.', 'aureon-studio' ), 'updated' );
		}

		if ( isset( $_GET['aureon-message'] ) && 'license_activated' == $_GET['aureon-message'] ) {
			add_settings_error( 'aureon-license-notices', 'license_activated', __( 'License activated.', 'aureon-studio' ), 'updated' );
		}

		if ( isset( $_GET['sl_activation'] ) && ! empty( $_GET['message'] ) ) {

			switch ( $_GET['sl_activation'] ) {

				case 'false':
					$message = urldecode( $_GET['message'] );
					add_settings_error( 'aureon-license-notices', 'license_failed', esc_html( $message ), 'error' );
				break;

				case 'true':
				default:
				break;

			}
		}

		settings_errors( 'aureon-license-notices' );
	}
}

if ( ! function_exists( 'aureon_super_package_addons' ) ) {
	add_action( 'aureon_options_items', 'aureon_super_package_addons', 5 );
	/**
	 * Build the area that allows us to activate and deactivate modules.
	 *
	 * @since 0.1
	 */
	function aureon_super_package_addons() {
		$addons = array(
			'Backgrounds' => 'aureon_package_backgrounds',
			'Blog' => 'aureon_package_blog',
			'Colors' => 'aureon_package_colors',
			'Copyright' => 'aureon_package_copyright',
			'Disable Elements' => 'aureon_package_disable_elements',
			'Elements' => 'aureon_package_elements',
			'Hooks' => 'aureon_package_hooks',
			'Menu Plus' => 'aureon_package_menu_plus',
			'Page Header' => 'aureon_package_page_header',
			'Secondary Nav' => 'aureon_package_secondary_nav',
			'Sections' => 'aureon_package_sections',
			'Spacing' => 'aureon_package_spacing',
			'Typography' => 'aureon_package_typography',
			'WooCommerce' => 'aureon_package_woocommerce',
		);

		if ( version_compare( PHP_VERSION, '5.4', '>=' ) && ! defined( 'AUREON_DISABLE_SITE_LIBRARY' ) ) {
			$addons['Site Library'] = 'aureon_package_site_library';
		}

		if ( function_exists( 'aureon_is_using_dynamic_typography' ) && aureon_is_using_dynamic_typography() ) {
			unset( $addons['Typography'] );
		}

		if ( version_compare( aureon_premium_get_theme_version(), '3.1.0-alpha.1', '>=' ) ) {
			unset( $addons['Colors'] );
		}

		ksort( $addons );

		$addon_count = 0;
		foreach ( $addons as $k => $v ) {
			if ( 'activated' == get_option( $v ) )
				$addon_count++;
		}

		$key = get_option( 'aureon_studio_license_key_status', 'deactivated' );
		$version = ( defined( 'AUREON_STUDIO_VERSION' ) ) ? AUREON_STUDIO_VERSION  : '';

		?>
		<div class="postbox aureon-metabox aureon-admin-block" id="modules">
			<h3 class="hndle"><?php _e('Aureon Studio','aureon-studio'); ?> <?php echo $version; ?></h3>
			<div class="inside" style="margin:0;padding:0;">
				<div class="premium-addons">
					<form method="post">
						<div class="add-on aureon-clear addon-container grid-parent" style="background:#EFEFEF;border-left:5px solid #DDD;padding-left:10px !important;">
							<div class="addon-name column-addon-name">
								<input type="checkbox" id="aureon-select-all" />
								<select name="aureon_mass_activate" class="mass-activate-select">
									<option value=""><?php _e( 'Bulk Actions', 'aureon-studio' ) ;?></option>
									<option value="activate-selected"><?php _e( 'Activate','aureon-studio' ) ;?></option>
									<option value="deactivate-selected"><?php _e( 'Deactivate','aureon-studio' ) ;?></option>
								</select>
								<?php wp_nonce_field( 'aureon_studio_bulk_action_nonce', 'aureon_studio_bulk_action_nonce' ); ?>
								<input type="submit" name="aureon_multi_activate" class="button mass-activate-button" value="<?php _e( 'Apply','aureon-studio' ); ?>" />
							</div>
						</div>
						<?php

						$deprecated_modules = apply_filters(
							'aureon_premium_deprecated_modules',
							array(
								'Page Header',
								'Hooks',
								'Sections',
							)
						);

						foreach ( $addons as $k => $v ) :

							$key = get_option( $v );

							if( $key == 'activated' ) { ?>
								<div class="add-on activated aureon-clear addon-container grid-parent">
									<div class="addon-name column-addon-name" style="">
										<input type="checkbox" class="addon-checkbox" name="aureon_addon_checkbox[]" value="<?php echo $v; ?>" />
										<?php echo $k;?>
									</div>
									<div class="addon-action addon-addon-action" style="text-align:right;">
										<?php wp_nonce_field( $v . '_deactivate_nonce', $v . '_deactivate_nonce' ); ?>
										<input type="submit" name="<?php echo $v;?>_deactivate_package" value="<?php _e( 'Deactivate', 'aureon-studio' );?>"/>
									</div>
								</div>
							<?php } else {
								// Don't output deprecated modules.
								if ( in_array( $k, $deprecated_modules ) ) {
									continue;
								}
								?>
								<div class="add-on aureon-clear addon-container grid-parent">

									<div class="addon-name column-addon-name">
										<input <?php if ( 'WooCommerce' == $k && ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) { echo 'disabled'; } ?> type="checkbox" class="addon-checkbox" name="aureon_addon_checkbox[]" value="<?php echo $v; ?>" />
										<?php echo $k;?>
									</div>

									<div class="addon-action addon-addon-action" style="text-align:right;">
										<?php if ( 'WooCommerce' == $k && ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) { ?>
											<?php _e( 'WooCommerce not activated.','aureon-studio' ); ?>
										<?php } else { ?>
											<?php wp_nonce_field( $v . '_activate_nonce', $v . '_activate_nonce' ); ?>
											<input type="submit" name="<?php echo $v;?>_activate_package" value="<?php _e( 'Activate', 'aureon-studio' );?>"/>
										<?php } ?>
									</div>

								</div>
							<?php }
							echo '<div class="aureon-clear"></div>';
						endforeach;
						?>
					</form>
				</div>
			</div>
		</div>
		<?php
	}
}

if ( ! function_exists( 'aureon_multi_activate' ) ) {
	add_action( 'admin_init', 'aureon_multi_activate' );

	function aureon_multi_activate() {
		// Deactivate selected
		if ( isset( $_POST['aureon_multi_activate'] ) ) {

			// If we didn't click the button, bail.
			if ( ! check_admin_referer( 'aureon_studio_bulk_action_nonce', 'aureon_studio_bulk_action_nonce' ) ) {
				return;
			}

			// If we're not an administrator, bail.
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			$name = ( isset( $_POST['aureon_addon_checkbox'] ) ) ? $_POST['aureon_addon_checkbox'] : '';
			$option = ( isset( $_POST['aureon_addon_checkbox'] ) ) ? $_POST['aureon_mass_activate'] : '';
			$autoload = null;

			if ( isset( $_POST['aureon_addon_checkbox'] ) ) {

				if ( 'deactivate-selected' == $option ) {
					foreach ( $name as $id ) {
						if ( 'activated' == get_option( $id ) ) {
							if ( 'aureon_package_site_library' === $id ) {
								$autoload = false;
							}

							update_option( $id, '', $autoload );
						}
					}
				}

				if ( 'activate-selected' == $option ) {
					foreach ( $name as $id ) {
						if ( 'activated' !== get_option( $id ) ) {
							if ( 'aureon_package_site_library' === $id ) {
								$autoload = false;
							}

							update_option( $id, 'activated', $autoload );
						}
					}
				}

				wp_safe_redirect( admin_url( 'themes.php?page=aureon-options' ) );
				exit;
			} else {
				wp_safe_redirect( admin_url( 'themes.php?page=aureon-options' ) );
				exit;
			}
		}
	}
}

/***********************************************
* Activate the add-on
***********************************************/
if ( ! function_exists( 'aureon_activate_super_package_addons' ) ) {
	add_action( 'admin_init', 'aureon_activate_super_package_addons' );

	function aureon_activate_super_package_addons() {
		$addons = array(
			'Typography' => 'aureon_package_typography',
			'Colors' => 'aureon_package_colors',
			'Backgrounds' => 'aureon_package_backgrounds',
			'Page Header' => 'aureon_package_page_header',
			'Sections' => 'aureon_package_sections',
			'Copyright' => 'aureon_package_copyright',
			'Disable Elements' => 'aureon_package_disable_elements',
			'Elements' => 'aureon_package_elements',
			'Blog' => 'aureon_package_blog',
			'Hooks' => 'aureon_package_hooks',
			'Spacing' => 'aureon_package_spacing',
			'Secondary Nav' => 'aureon_package_secondary_nav',
			'Menu Plus' => 'aureon_package_menu_plus',
			'WooCommerce' => 'aureon_package_woocommerce',
		);

		if ( version_compare( PHP_VERSION, '5.4', '>=' ) && ! defined( 'AUREON_DISABLE_SITE_LIBRARY' ) ) {
			$addons['Site Library'] = 'aureon_package_site_library';
		}

		foreach( $addons as $k => $v ) :

			if ( isset( $_POST[$v . '_activate_package'] ) ) {

				// If we didn't click the button, bail.
				if ( ! check_admin_referer( $v . '_activate_nonce', $v . '_activate_nonce' ) ) {
					return;
				}

				// If we're not an administrator, bail.
				if ( ! current_user_can( 'manage_options' ) ) {
					return;
				}

				$autoload = null;

				if ( 'aureon_package_site_library' === $v ) {
					$autoload = false;
				}

				update_option( $v, 'activated', $autoload );
				wp_safe_redirect( admin_url( 'themes.php?page=aureon-options&aureon-message=addon_activated' ) );
				exit;
			}

		endforeach;
	}
}

/***********************************************
* Deactivate the plugin
***********************************************/
if ( ! function_exists( 'aureon_deactivate_super_package_addons' ) ) {
	add_action( 'admin_init', 'aureon_deactivate_super_package_addons' );

	function aureon_deactivate_super_package_addons() {
		$addons = array(
			'Typography' => 'aureon_package_typography',
			'Colors' => 'aureon_package_colors',
			'Backgrounds' => 'aureon_package_backgrounds',
			'Page Header' => 'aureon_package_page_header',
			'Sections' => 'aureon_package_sections',
			'Copyright' => 'aureon_package_copyright',
			'Disable Elements' => 'aureon_package_disable_elements',
			'Elements' => 'aureon_package_elements',
			'Blog' => 'aureon_package_blog',
			'Hooks' => 'aureon_package_hooks',
			'Spacing' => 'aureon_package_spacing',
			'Secondary Nav' => 'aureon_package_secondary_nav',
			'Menu Plus' => 'aureon_package_menu_plus',
			'WooCommerce' => 'aureon_package_woocommerce',
		);

		if ( version_compare( PHP_VERSION, '5.4', '>=' ) && ! defined( 'AUREON_DISABLE_SITE_LIBRARY' ) ) {
			$addons['Site Library'] = 'aureon_package_site_library';
		}

		foreach( $addons as $k => $v ) :

			if ( isset( $_POST[$v . '_deactivate_package'] ) ) {

				// If we didn't click the button, bail.
				if ( ! check_admin_referer( $v . '_deactivate_nonce', $v . '_deactivate_nonce' ) ) {
					return;
				}

				// If we're not an administrator, bail.
				if ( ! current_user_can( 'manage_options' ) ) {
					return;
				}

				$autoload = null;

				if ( 'aureon_package_site_library' === $v ) {
					$autoload = false;
				}

				update_option( $v, 'deactivated', $autoload );
				wp_safe_redirect( admin_url('themes.php?page=aureon-options&aureon-message=addon_deactivated' ) );
				exit;
			}

		endforeach;
	}
}

if ( ! function_exists( 'aureon_premium_body_class' ) ) {
	add_filter( 'admin_body_class', 'aureon_premium_body_class' );
	/**
	 * Add a class or many to the body in the dashboard
	 */
	function aureon_premium_body_class( $classes ) {
	    return "$classes aureon_studio";
	}
}

if ( ! function_exists( 'aureon_activation_area' ) ) {
	add_action( 'aureon_admin_right_panel', 'aureon_activation_area' );

	function aureon_activation_area() {
		// License key UI removed — no phone-home validation.
	}
}

add_action( 'admin_init', 'aureon_premium_process_license_key', 5 );
/**
 * Process our saved license key.
 *
 * @since 1.6
 */
function aureon_premium_process_license_key() {
	// Has our button been clicked?
	if ( isset( $_POST[ 'aureon_studio_license_key' ] ) ) {

		// Get out if we didn't click the button
		if ( ! check_admin_referer( 'aureon_license_key_aureon_studio_nonce', 'aureon_license_key_aureon_studio_nonce' ) ) {
			return;
		}

		// If we're not an administrator, bail.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Set our beta testing option if it's checked.
		if ( ! empty( $_POST['aureon_studio_beta_testing'] ) ) {
			update_option( 'aureon_studio_beta_testing', true, false );
		} else {
			delete_option( 'aureon_studio_beta_testing' );
		}

		// Grab the value being saved
		$new = sanitize_key( $_POST['aureon_license_key_aureon_studio'] );

		// Get the previously saved value
		$old = get_option( 'aureon_studio_license_key' );

		// Still here? Update our option with the new license key
		update_option( 'aureon_studio_license_key', $new );

		// If we have a value, run activation.
		if ( '' !== $new ) {
			$api_params = array(
				'edd_action' => 'activate_license',
				'license' => $new,
				'item_name' => urlencode( 'Aureon Studio' ),
				'url' => home_url()
			);
		}

		// If we don't have a value (it's been cleared), run deactivation.
		if ( '' == $new && 'valid' == get_option( 'aureon_studio_license_key_status' ) ) {
			$api_params = array(
				'edd_action' => 'deactivate_license',
				'license' => $old,
				'item_name' => urlencode( 'Aureon Studio' ),
				'url' => home_url()
			);
		}

		// Nothing? Get out of here.
		if ( ! isset( $api_params ) ) {
			wp_safe_redirect( admin_url( 'themes.php?page=aureon-options' ) );
			exit;
		}

		// Phone home.
		$license_response = wp_remote_post( 'https://example.com', array(
			'timeout'   => 60,
			'sslverify' => false,
			'body'      => $api_params
		) );

		// Make sure the response came back okay.
		if ( is_wp_error( $license_response ) || 200 !== wp_remote_retrieve_response_code( $license_response ) ) {
			if ( is_object( $license_response ) ) {
				$message = $license_response->get_error_message();
			} elseif ( is_array( $license_response ) && isset( $license_response['response']['message'] ) ) {
				if ( 'Forbidden' === $license_response['response']['message'] ) {
					$message = __( '403 Forbidden. Your server is not able to communicate with aureonstudio.com in order to activate your license key.', 'aureon-studio' );
				} else {
					$message = $license_response['response']['message'];
				}
			}
		} else {

			// Still here? Decode our response.
			$license_data = json_decode( wp_remote_retrieve_body( $license_response ) );

			if ( false === $license_data->success ) {

				switch ( $license_data->error ) {

				case 'expired' :

					$message = sprintf(
						__( 'Your license key expired on %s.', 'aureon-studio' ),
						date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
					);
					break;

				case 'revoked' :

					$message = __( 'Your license key has been disabled.', 'aureon-studio' );
					break;

				case 'missing' :

					$message = __( 'Invalid license.', 'aureon-studio' );
					break;

				case 'invalid' :
				case 'site_inactive' :

					$message = __( 'Your license is not active for this URL.', 'aureon-studio' );
					break;

				case 'item_name_mismatch' :

					$message = __( 'This appears to be an invalid license key for Aureon Studio.', 'aureon-studio' );
					break;

				case 'no_activations_left':

					$message = __( 'Your license key has reached its activation limit.', 'aureon-studio' );
					break;

				default :

					$message = __( 'An error occurred, please try again.', 'aureon-studio' );
					break;
				}

			}

		}

		// Check if anything passed on a message constituting a failure
		if ( ! empty( $message ) ) {
			delete_option( 'aureon_studio_license_key_status' );
			$base_url = admin_url( 'themes.php?page=aureon-options' );
			$redirect = add_query_arg( array( 'sl_activation' => 'false', 'message' => urlencode( $message ) ), esc_url( $base_url ) );
			wp_redirect( $redirect );
			exit();
		}

		// Update our license key status
		update_option( 'aureon_studio_license_key_status', $license_data->license );

		if ( 'valid' == $license_data->license ) {
			// Validated, go tell them
			wp_safe_redirect( admin_url( 'themes.php?page=aureon-options&aureon-message=license_activated' ) );
			exit;
		} elseif ( 'deactivated' == $license_data->license ) {
			// Deactivated, go tell them
			wp_safe_redirect( admin_url( 'themes.php?page=aureon-options&aureon-message=deactivation_passed' ) );
			exit;
		} else {
			// Failed, go tell them
			wp_safe_redirect( admin_url( 'themes.php?page=aureon-options&aureon-message=license_failed' ) );
			exit;
		}
	}
}

if ( ! function_exists( 'aureon_license_missing' ) ) {
	add_action( 'in_plugin_update_message-aureon-studio/aureon-studio.php', 'aureon_license_missing', 10, 2 );
	/**
	 * Add a message to the plugin update area if no license key is set
	 */
	function aureon_license_missing() {
		$license = get_option( 'aureon_studio_license_key_status' );

		if ( 'valid' !== $license ) {
			echo '&nbsp;<strong><a href="' . esc_url( admin_url('themes.php?page=aureon-options' ) ) . '">' . __( 'Enter valid license key for automatic updates.', 'aureon-studio' ) . '</a></strong>';
		}
	}
}

add_filter( 'aureon_premium_beta_tester', 'aureon_premium_beta_tester' );
/**
 * Enable beta testing if our option is set.
 *
 * @since 1.6
 */
function aureon_premium_beta_tester( $value ) {
	if ( get_option( 'aureon_studio_beta_testing', false ) ) {
		return true;
	}

	return $value;
}
