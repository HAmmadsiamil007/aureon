<?php
/**
 * This file handles resetting of options.
 *
 * @package Aureon Studio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access, please.
}

add_action( 'aureon_admin_right_panel', 'aureon_premium_reset_metabox', 25 );
/**
 * Add the reset options to the Dashboard.
 */
function aureon_premium_reset_metabox() {
	?>
	<div class="postbox aureon-metabox" id="aureon-reset">
		<h3 class="hndle"><?php esc_html_e( 'Reset Settings', 'aureon-studio' ); ?></h3>
		<div class="inside">
			<form method="post">
				<span class="show-advanced"><?php esc_html_e( 'Advanced', 'aureon-studio' ); ?></span>
				<div class="reset-choices advanced-choices">
					<label><input type="checkbox" name="module_group[]" value="aureon_settings" checked /><?php _ex( 'Core', 'Module name', 'aureon-studio' ); ?></label>

					<?php if ( aureon_is_module_active( 'aureon_package_backgrounds', 'AUREON_BACKGROUNDS' ) ) { ?>
						<label><input type="checkbox" name="module_group[]" value="aureon_background_settings" checked /><?php _ex( 'Backgrounds', 'Module name', 'aureon-studio' ); ?></label>
					<?php } ?>

					<?php if ( aureon_is_module_active( 'aureon_package_blog', 'AUREON_BLOG' ) ) { ?>
						<label><input type="checkbox" name="module_group[]" value="aureon_blog_settings" checked /><?php _ex( 'Blog', 'Module name', 'aureon-studio' ); ?></label>
					<?php } ?>

					<?php if ( aureon_is_module_active( 'aureon_package_hooks', 'AUREON_HOOKS' ) ) { ?>
						<label><input type="checkbox" name="module_group[]" value="aureon_hooks" checked /><?php _ex( 'Hooks', 'Module name', 'aureon-studio' ); ?></label>
					<?php } ?>

					<?php if ( aureon_is_module_active( 'aureon_package_page_header', 'AUREON_PAGE_HEADER' ) ) { ?>
						<label><input type="checkbox" name="module_group[]" value="aureon_page_header_settings" checked /><?php _ex( 'Page Header', 'Module name', 'aureon-studio' ); ?></label>
					<?php } ?>

					<?php if ( aureon_is_module_active( 'aureon_package_secondary_nav', 'AUREON_SECONDARY_NAV' ) ) { ?>
						<label><input type="checkbox" name="module_group[]" value="aureon_secondary_nav_settings" checked /><?php _ex( 'Secondary Navigation', 'Module name', 'aureon-studio' ); ?></label>
					<?php } ?>

					<?php if ( aureon_is_module_active( 'aureon_package_spacing', 'AUREON_SPACING' ) ) { ?>
						<label><input type="checkbox" name="module_group[]" value="aureon_spacing_settings" checked /><?php _ex( 'Spacing', 'Module name', 'aureon-studio' ); ?></label>
					<?php } ?>

					<?php if ( aureon_is_module_active( 'aureon_package_menu_plus', 'AUREON_MENU_PLUS' ) ) { ?>
						<label><input type="checkbox" name="module_group[]" value="aureon_menu_plus_settings" checked /><?php _ex( 'Menu Plus', 'Module name', 'aureon-studio' ); ?></label>
					<?php } ?>

					<?php if ( aureon_is_module_active( 'aureon_package_woocommerce', 'AUREON_WOOCOMMERCE' ) ) { ?>
						<label><input type="checkbox" name="module_group[]" value="aureon_woocommerce_settings" checked /><?php _ex( 'WooCommerce', 'Module name', 'aureon-studio' ); ?></label>
					<?php } ?>

					<?php if ( aureon_is_module_active( 'aureon_package_copyright', 'AUREON_COPYRIGHT' ) ) { ?>
						<label><input type="checkbox" name="module_group[]" value="copyright" checked /><?php _ex( 'Copyright', 'Module name', 'aureon-studio' ); ?></label>
					<?php } ?>
				</div>
				<p><input type="hidden" name="aureon_reset_action" value="reset_settings" /></p>
				<p style="margin-bottom:0">
					<?php
					$warning = 'return confirm("' . __( 'Warning: This will delete your settings and can not be undone.', 'aureon-studio' ) . '")';
					wp_nonce_field( 'aureon_reset_settings_nonce', 'aureon_reset_settings_nonce' );
					submit_button(
						__( 'Reset', 'aureon-studio' ),
						'button-primary',
						'submit',
						false,
						array(
							'onclick' => esc_js( $warning ),
							'id' => '',
						)
					);
					?>
				</p>
			</form>
		</div>
	</div>
	<?php
}

add_action( 'admin_init', 'aureon_premium_process_reset' );
/**
 * Process the reset functions.
 */
function aureon_premium_process_reset() {
	if ( empty( $_POST['aureon_reset_action'] ) || 'reset_settings' !== $_POST['aureon_reset_action'] ) {
		return;
	}

	if ( ! wp_verify_nonce( $_POST['aureon_reset_settings_nonce'], 'aureon_reset_settings_nonce' ) ) {
		return;
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$theme_mods = array(
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

	$settings = array(
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

	$data = array(
		'mods' => array(),
		'options' => array(),
	);

	foreach ( $theme_mods as $theme_mod ) {
		if ( 'aureon_copyright' === $theme_mod ) {
			if ( in_array( 'copyright', $_POST['module_group'] ) ) {
				remove_theme_mod( $theme_mod );
			}
		} else {
			if ( in_array( 'aureon_settings', $_POST['module_group'] ) ) {
				remove_theme_mod( $theme_mod );
			}
		}
	}

	foreach ( $settings as $setting ) {
		if ( in_array( $setting, $_POST['module_group'] ) ) {
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

	wp_safe_redirect( admin_url( 'themes.php?page=aureon-options&status=reset' ) );
	exit;
}

add_action( 'admin_head', 'aureon_reset_options_css', 100 );
/**
 * Add CSS to the dashboard.
 */
function aureon_reset_options_css() {
	$screen = get_current_screen();

	if ( ! is_object( $screen ) ) {
		return;
	}

	if ( 'appearance_page_aureon-options' !== $screen->base ) {
		return;
	}
	?>
	<style>
		#gen-delete {
			display: none;
		}

		.advanced-choices {
			margin-top: 10px;
			font-size: 95%;
			opacity: 0.9;
		}

		.advanced-choices:not(.show) {
			display: none;
		}

		.advanced-choices label {
			display: block;
		}

		.show-advanced {
			font-size: 13px;
			opacity: 0.8;
			cursor: pointer;
		}

		.show-advanced:after {
			content: "\f347";
			font-family: dashicons;
			padding-left: 2px;
			padding-top: 2px;
			font-size: 10px;
		}

		.show-advanced.active:after {
			content: "\f343";
		}
	</style>
	<?php
}

add_action( 'admin_footer', 'aureon_reset_options_scripts', 100 );
/**
 * Add scripts to the Dashboard.
 */
function aureon_reset_options_scripts() {
	$screen = get_current_screen();

	if ( ! is_object( $screen ) ) {
		return;
	}

	if ( 'appearance_page_aureon-options' !== $screen->base ) {
		return;
	}
	?>
	<script>
		jQuery( function( $ ) {
			$( '.show-advanced' ).on( 'click', function() {
				$( this ).toggleClass( 'active' );
				$( this ).next( '.advanced-choices' ).toggleClass( 'show' );
			} );
		} );
	</script>
	<?php
}
