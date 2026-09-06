<?php
/**
 * Register form — registration form with strength meter.
 *
 * Key:    'forms/register'
 * Source: join-now.html `.register-form`
 *
 * Props:  (all keys optional; escaped at render; missing key → default listed)
 * - `string $action         Form action URL. Default ''.`
 * - `string $nonce          WP nonce. Default ''.`
 * - `string $login_url       Login link. Default ''.`
 * - `bool $show_strength  Show strength meter. Default false.`
 * - `string $brand          Brand wordmark. Default ''.`
 * - `string $title          Form title. Default 'Join the Void'.`
 * - `string $subtitle       Subtitle. Default 'Create an account for faster checkout and exclusive drops.'.`
 *
 * Slots:  'auth/password-strength'
 * Variants: none
 * Tokens:  no hardcoded presentation values — theme uses `--aureon-*` custom props only.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$componentData = isset( $componentData ) ? (array) $componentData : array();

$action   = isset( $componentData['action'] ) ? $componentData['action'] : '';
$nonce    = isset( $componentData['nonce'] ) ? $componentData['nonce'] : '';
$login_url = isset( $componentData['login_url'] ) ? $componentData['login_url'] : '';
$show_strength = ! empty( $componentData['show_strength'] );
?>
<div class="auth-card" data-tilt>
	<div class="auth-logo"><?php echo esc_html( isset( $componentData['brand'] ) ? $componentData['brand'] : '' ); ?></div>
	<h1 class="auth-title"><?php echo esc_html( isset( $componentData['title'] ) ? $componentData['title'] : __( 'Join the Void', 'aureon' ) ); ?></h1>
	<p class="auth-subtitle"><?php echo esc_html( isset( $componentData['subtitle'] ) ? $componentData['subtitle'] : __( 'Create an account for faster checkout and exclusive drops.', 'aureon' ) ); ?></p>

	<form method="post" class="woocommerce-form-register" novalidate>
		<div class="form-group">
			<label class="form-label" for="reg_email"><?php esc_html_e( 'Email', 'aureon' ); ?> <span style="color:var(--gold);">*</span></label>
			<input type="email" class="woocommerce-Input form-input" name="email" id="reg_email" value="<?php echo esc_attr( ! empty( $_POST['email'] ) ? wp_unslash( $_POST['email'] ) : '' ); // phpcs:ignore ?> " autocomplete="email" required>
		</div>
		<div class="form-group">
			<label class="form-label" for="reg_password"><?php esc_html_e( 'Password', 'aureon' ); ?> <span style="color:var(--gold);">*</span></label>
			<div class="password-wrapper">
				<input type="password" class="woocommerce-Input form-input" name="password" id="reg_password" placeholder="<?php esc_attr_e( 'Create a password', 'aureon' ); ?>" autocomplete="new-password" required>
			</div>
			<?php if ( $show_strength && function_exists( 'aether_render_component' ) ) : ?>
				<?php aether_render_component( 'auth/password-strength', array( 'target' => 'reg_password' ) ); ?>
			<?php endif; ?>
		</div>
		<?php if ( $nonce ) : ?>
			<input type="hidden" name="woocommerce-register-nonce" value="<?php echo esc_attr( $nonce ); ?>">
		<?php endif; ?>

		<?php do_action( 'woocommerce_register_form' ); ?>
		<button type="submit" class="btn-signin" name="register" value="Register" data-magnetic="0.12"><?php esc_html_e( 'Create Account', 'aureon' ); ?></button>
	</form>

	<?php if ( $login_url ) : ?>
		<div class="auth-divider"><span><?php esc_html_e( 'or', 'aureon' ); ?></span></div>
		<div class="auth-footer">
			<?php esc_html_e( 'Already have an account?', 'aureon' ); ?>
			<a href="<?php echo esc_url( $login_url ); ?>"><?php esc_html_e( 'Sign In', 'aureon' ); ?></a>
		</div>
	<?php endif; ?>
</div>