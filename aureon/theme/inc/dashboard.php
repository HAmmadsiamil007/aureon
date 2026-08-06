<?php
/**
 * Builds our admin page.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! function_exists( 'aureon_create_menu' ) ) {
	add_action( 'admin_menu', 'aureon_create_menu' );
	/**
	 * Adds our "Aureon" dashboard menu item
	 *
	 * @since 0.1
	 */
	function aureon_create_menu() {
		$aureon_page = add_theme_page( esc_html__( 'Aureon', 'aureon' ), esc_html__( 'Aureon', 'aureon' ), apply_filters( 'aureon_dashboard_page_capability', 'edit_theme_options' ), 'aureon-options', 'aureon_settings_page' );
		add_action( "admin_print_styles-$aureon_page", 'aureon_options_styles' );
	}
}

if ( ! function_exists( 'aureon_options_styles' ) ) {
	/**
	 * Adds any necessary scripts to the Aureon dashboard page
	 *
	 * @since 0.1
	 */
	function aureon_options_styles() {
		wp_enqueue_style( 'aureon-options', get_template_directory_uri() . '/assets/css/admin/style.css', array(), AUREON_VERSION );
	}
}

if ( ! function_exists( 'aureon_settings_page' ) ) {
	/**
	 * Builds the content of our Aureon dashboard page
	 *
	 * @since 0.1
	 */
	function aureon_settings_page() {
		?>
		<div class="wrap">
			<div class="metabox-holder">
				<div class="aureon-masthead clearfix">
					<div class="aureon-container">
						<div class="aureon-title">
							<a href="<?php echo aureon_get_premium_url( 'https://aureonstudio.com' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in function. ?>" target="_blank">Aureon</a> <span class="aureon-version"><?php echo esc_html( AUREON_VERSION ); ?></span>
						</div>
						<div class="aureon-masthead-links">
							<?php if ( ! defined( 'AUREON_STUDIO_VERSION' ) ) : ?>
								<a style="font-weight: bold;" href="<?php echo aureon_get_premium_url( 'https://aureonstudio.com/premium/' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in function. ?>" target="_blank"><?php esc_html_e( 'Premium', 'aureon' ); ?></a>
							<?php endif; ?>
							<a href="<?php echo esc_url( 'https://aureonstudio.com/support' ); ?>" target="_blank"><?php esc_html_e( 'Support', 'aureon' ); ?></a>
							<a href="<?php echo esc_url( 'https://docs.aureonstudio.com' ); ?>" target="_blank"><?php esc_html_e( 'Documentation', 'aureon' ); ?></a>
						</div>
					</div>
				</div>

				<?php
				/**
				 * aureon_dashboard_after_header hook.
				 *
				 * @since 2.0
				 */
				do_action( 'aureon_dashboard_after_header' );
				?>

				<div class="aureon-container">
					<div class="postbox-container clearfix" style="float: none;">
						<div class="grid-container grid-parent">

							<?php
							/**
							 * aureon_dashboard_inside_container hook.
							 *
							 * @since 2.0
							 */
							do_action( 'aureon_dashboard_inside_container' );
							?>

							<div class="form-metabox grid-70" style="padding-left: 0;">
								<h2 style="height:0;margin:0;"><!-- admin notices below this element --></h2>
								<form method="post" action="options.php">
									<?php settings_fields( 'aureon-settings-group' ); ?>
									<?php do_settings_sections( 'aureon-settings-group' ); ?>
									<div class="customize-button hide-on-desktop">
										<?php
										printf(
											'<a id="aureon_customize_button" class="button button-primary" href="%1$s">%2$s</a>',
											esc_url( admin_url( 'customize.php' ) ),
											esc_html__( 'Customize', 'aureon' )
										);
										?>
									</div>

									<?php
									/**
									 * aureon_inside_options_form hook.
									 *
									 * @since 0.1
									 */
									do_action( 'aureon_inside_options_form' );
									?>
								</form>

								<?php
								$modules = array(
									'Backgrounds' => array(
										'url' => aureon_get_premium_url( 'https://aureonstudio.com/premium/#backgrounds', false ),
									),
									'Blog' => array(
										'url' => aureon_get_premium_url( 'https://aureonstudio.com/premium/#blog', false ),
									),
									'Colors' => array(
										'url' => aureon_get_premium_url( 'https://aureonstudio.com/premium/#colors', false ),
									),
									'Copyright' => array(
										'url' => aureon_get_premium_url( 'https://aureonstudio.com/premium/#copyright', false ),
									),
									'Disable Elements' => array(
										'url' => aureon_get_premium_url( 'https://aureonstudio.com/premium/#disable-elements', false ),
									),
									'Elements' => array(
										'url' => aureon_get_premium_url( 'https://aureonstudio.com/premium/#elements', false ),
									),
									'Import / Export' => array(
										'url' => aureon_get_premium_url( 'https://aureonstudio.com/premium/#import-export', false ),
									),
									'Menu Plus' => array(
										'url' => aureon_get_premium_url( 'https://aureonstudio.com/premium/#menu-plus', false ),
									),
									'Secondary Nav' => array(
										'url' => aureon_get_premium_url( 'https://aureonstudio.com/premium/#secondary-nav', false ),
									),
									'Sections' => array(
										'url' => aureon_get_premium_url( 'https://aureonstudio.com/premium/#sections', false ),
									),
									'Spacing' => array(
										'url' => aureon_get_premium_url( 'https://aureonstudio.com/premium/#spacing', false ),
									),
									'Typography' => array(
										'url' => aureon_get_premium_url( 'https://aureonstudio.com/premium/#typography', false ),
									),
									'WooCommerce' => array(
										'url' => aureon_get_premium_url( 'https://aureonstudio.com/premium/#woocommerce', false ),
									),
								);

								if ( ! defined( 'AUREON_STUDIO_VERSION' ) ) :
									?>
									<div class="postbox aureon-metabox">
										<h3 class="hndle"><?php esc_html_e( 'Premium Modules', 'aureon' ); ?></h3>
										<div class="inside" style="margin:0;padding:0;">
											<div class="premium-addons">
												<?php
												foreach ( $modules as $module => $info ) {
													?>
													<div class="add-on activated aureon-clear addon-container grid-parent">
														<div class="addon-name column-addon-name" style="">
															<a href="<?php echo esc_url( $info['url'] ); ?>" target="_blank"><?php echo esc_html( $module ); ?></a>
														</div>
														<div class="addon-action addon-addon-action" style="text-align:right;">
															<a href="<?php echo esc_url( $info['url'] ); ?>" target="_blank"><?php esc_html_e( 'Learn more', 'aureon' ); ?></a>
														</div>
													</div>
													<div class="aureon-clear"></div>
												<?php } ?>
											</div>
										</div>
									</div>
									<?php
								endif;

								/**
								 * aureon_options_items hook.
								 *
								 * @since 0.1
								 */
								do_action( 'aureon_options_items' );

								$typography_section = 'customize.php?autofocus[section]=font_section';
								$colors_section = 'customize.php?autofocus[section]=body_section';

								if ( function_exists( 'aureon_is_module_active' ) ) {
									if ( aureon_is_module_active( 'aureon_package_typography', 'AUREON_TYPOGRAPHY' ) ) {
										$typography_section = 'customize.php?autofocus[panel]=aureon_typography_panel';
									}

									if ( aureon_is_module_active( 'aureon_package_colors', 'AUREON_COLORS' ) ) {
										$colors_section = 'customize.php?autofocus[panel]=aureon_colors_panel';
									}
								}

								$quick_settings = array(
									'logo' => array(
										'title' => __( 'Upload Logo', 'aureon' ),
										'icon' => 'dashicons-format-image',
										'url' => admin_url( 'customize.php?autofocus[control]=custom_logo' ),
									),
									'typography' => array(
										'title' => __( 'Customize Fonts', 'aureon' ),
										'icon' => 'dashicons-editor-textcolor',
										'url' => admin_url( $typography_section ),
									),
									'colors' => array(
										'title' => __( 'Customize Colors', 'aureon' ),
										'icon' => 'dashicons-admin-customizer',
										'url' => admin_url( $colors_section ),
									),
									'layout' => array(
										'title' => __( 'Layout Options', 'aureon' ),
										'icon' => 'dashicons-layout',
										'url' => admin_url( 'customize.php?autofocus[panel]=aureon_layout_panel' ),
									),
									'all' => array(
										'title' => __( 'All Options', 'aureon' ),
										'icon' => 'dashicons-admin-generic',
										'url' => admin_url( 'customize.php' ),
									),
								);
								?>
							</div>

							<div class="aureon-right-sidebar grid-30" style="padding-right: 0;">
								<div class="postbox aureon-metabox start-customizing">
									<h3 class="hndle"><?php esc_html_e( 'Start Customizing', 'aureon' ); ?></h3>
									<div class="inside">
										<ul>
											<?php
											foreach ( $quick_settings as $key => $data ) {
												printf(
													'<li><span class="dashicons %1$s"></span> <a href="%2$s">%3$s</a></li>',
													esc_attr( $data['icon'] ),
													esc_url( $data['url'] ),
													esc_html( $data['title'] )
												);
											}
											?>
										</ul>

										<p><?php esc_html_e( 'Want to learn more about the theme? Check out our extensive documentation.', 'aureon' ); ?></p>
										<a href="https://docs.aureonstudio.com"><?php esc_html_e( 'Visit documentation &rarr;', 'aureon' ); ?></a>
									</div>
								</div>

								<?php
								/**
								 * aureon_admin_right_panel hook.
								 *
								 * @since 0.1
								 */
								do_action( 'aureon_admin_right_panel' );
								?>

								<div class="postbox aureon-metabox" id="gen-delete">
									<h3 class="hndle"><?php esc_html_e( 'Reset Settings', 'aureon' ); ?></h3>
									<div class="inside">
										<p><?php esc_html_e( 'Deleting your settings can not be undone.', 'aureon' ); ?></p>
										<form method="post">
											<p><input type="hidden" name="aureon_reset_customizer" value="aureon_reset_customizer_settings" /></p>
											<p>
												<?php
												$warning = 'return confirm("' . esc_html__( 'Warning: This will delete your settings.', 'aureon' ) . '")';
												wp_nonce_field( 'aureon_reset_customizer_nonce', 'aureon_reset_customizer_nonce' );

												submit_button(
													esc_attr__( 'Reset', 'aureon' ),
													'button-primary',
													'submit',
													false,
													array(
														'onclick' => esc_js( $warning ),
													)
												);
												?>
											</p>

										</form>
										<?php
										/**
										 * aureon_delete_settings_form hook.
										 *
										 * @since 0.1
										 */
										do_action( 'aureon_delete_settings_form' );
										?>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="aureon-options-footer">
						<span>
							<?php
							printf(
								/* translators: %s: Heart icon */
								_x( 'Made with %s by Aureon Studio', 'made with love', 'aureon' ),
								'<span style="color:#D04848" class="dashicons dashicons-heart"></span>'
							);
							?>
						</span>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}

if ( ! function_exists( 'aureon_reset_customizer_settings' ) ) {
	add_action( 'admin_init', 'aureon_reset_customizer_settings' );
	/**
	 * Reset customizer settings
	 *
	 * @since 0.1
	 */
	function aureon_reset_customizer_settings() {
		if ( empty( $_POST['aureon_reset_customizer'] ) || 'aureon_reset_customizer_settings' !== $_POST['aureon_reset_customizer'] ) {
			return;
		}

		$nonce = isset( $_POST['aureon_reset_customizer_nonce'] ) ? sanitize_key( $_POST['aureon_reset_customizer_nonce'] ) : '';

		if ( ! wp_verify_nonce( $nonce, 'aureon_reset_customizer_nonce' ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		delete_option( 'aureon_settings' );
		delete_option( 'aureon_dynamic_css_output' );
		delete_option( 'aureon_dynamic_css_cached_version' );
		remove_theme_mod( 'font_body_variants' );
		remove_theme_mod( 'font_body_category' );

		wp_safe_redirect( admin_url( 'themes.php?page=aureon-options&status=reset' ) );
		exit;
	}
}

if ( ! function_exists( 'aureon_admin_errors' ) ) {
	add_action( 'admin_notices', 'aureon_admin_errors' );
	/**
	 * Add our admin notices
	 *
	 * @since 0.1
	 */
	function aureon_admin_errors() {
		$screen = get_current_screen();

		if ( 'appearance_page_aureon-options' !== $screen->base ) {
			return;
		}

		if ( isset( $_GET['settings-updated'] ) && 'true' === $_GET['settings-updated'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Only checking. False positive.
			add_settings_error( 'aureon-notices', 'true', esc_html__( 'Settings saved.', 'aureon' ), 'updated' );
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Only checking. False positive.
		if ( isset( $_GET['status'] ) && 'imported' === $_GET['status'] ) {
			add_settings_error( 'aureon-notices', 'imported', esc_html__( 'Import successful.', 'aureon' ), 'updated' );
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Only checking. False positive.
		if ( isset( $_GET['status'] ) && 'reset' === $_GET['status'] ) {
			add_settings_error( 'aureon-notices', 'reset', esc_html__( 'Settings removed.', 'aureon' ), 'updated' );
		}

		settings_errors( 'aureon-notices' );
	}
}
