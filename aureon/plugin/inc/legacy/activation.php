<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_enqueue_scripts', 'aureon_premium_dashboard_scripts' );
/**
 * Enqueue scripts and styles for the Aureon Dashboard area.
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
							update_option( $id, '', $autoload );
						}
					}
				}

				if ( 'activate-selected' == $option ) {
					foreach ( $name as $id ) {
						if ( 'activated' !== get_option( $id ) ) {
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

				update_option( $v, 'activated' );
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

				update_option( $v, 'deactivated' );
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
