<?php
/**
 * Login form — login form with password reset link and nonce.
 *
 * Key:    'forms/login'
 * Source: login.html `.login-form`
 *
 * Props:  (all keys optional; escaped at render; missing key → default listed)
 * - `string $action            Form action URL. Default ''.`
 * - `string $nonce             WP nonce. Default ''.`
 * - `string $lost_url          Lost-password URL. Default ''.`
 * - `bool $forgot_modal      Show modal instead of link. Default false.`
 * - `bool $register_enabled  Show register link. Default false.`
 * - `string $register_url      Registration link. Default ''.`
 * - `string $brand            Brand wordmark. Default ''.`
 * - `string $title             Form title. Default 'Welcome Back'.`
 * - `string $subtitle         Subtitle. Default 'Sign in to access your account'.`
 *
 * Slots:  none
 * Variants: none
 * Tokens:  no hardcoded presentation values — theme uses `--aureon-*` custom props only.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$componentData = isset( $componentData ) ? (array) $componentData : array();

$action      = isset( $componentData['action'] ) ? $componentData['action'] : '';
$nonce       = isset( $componentData['nonce'] ) ? $componentData['nonce'] : '';
$lost_url    = isset( $componentData['lost_url'] ) ? $componentData['lost_url'] : '';
$forgot_modal = ! empty( $componentData['forgot_modal'] );
$register_enabled = ! empty( $componentData['register_enabled'] );
$register_url     = isset( $componentData['register_url'] ) ? $componentData['register_url'] : '';
?>
<div class="auth-card" data-tilt>
	<div class="auth-logo"><?php echo esc_html( isset( $componentData['brand'] ) ? $componentData['brand'] : '' ); ?></div>
	<h1 class="auth-title"><?php echo esc_html( isset( $componentData['title'] ) ? $componentData['title'] : __( 'Welcome Back', 'aureon' ) ); ?></h1>
	<p class="auth-subtitle"><?php echo esc_html( isset( $componentData['subtitle'] ) ? $componentData['subtitle'] : __( 'Sign in to access your account', 'aureon' ) ); ?></p>

	<form class="woocommerce-form-login" method="post" novalidate>
		<div class="form-group">
			<label class="form-label" for="username"><?php esc_html_e( 'Email', 'aureon' ); ?> <span style="color:var(--gold);">*</span></label>
			<input type="text" class="woocommerce-Input form-input" name="username" id="username" value="<?php echo esc_attr( ! empty( $_POST['username'] ) ? wp_unslash( $_POST['username'] ) : '' ); // phpcs:ignore ?> " autocomplete="username" required>
		</div>
		<div class="form-group">
			<label class="form-label" for="password"><?php esc_html_e( 'Password', 'aureon' ); ?> <span style="color:var(--gold);">*</span></label>
			<div class="password-wrapper">
				<input class="woocommerce-Input form-input" type="password" name="password" id="password" placeholder="<?php esc_attr_e( 'Enter your password', 'aureon' ); ?>" autocomplete="current-password" required>
			</div>
		</div>
		<div class="form-row">
			<div class="form-check">
				<input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever">
				<label for="rememberme"><?php esc_html_e( 'Remember me', 'aureon' ); ?></label>
			</div>
			<?php if ( $forgot_modal ) : ?>
				<button type="button" class="forgot-link" data-forgot-toggle aria-controls="forgotModal"><?php esc_html_e( 'Forgot Password?', 'aureon' ); ?></button>
			<?php elseif ( $lost_url ) : ?>
				<a class="forgot-link" href="<?php echo esc_url( $lost_url ); ?>"><?php esc_html_e( 'Lost your password?', 'aureon' ); ?></a>
			<?php endif; ?>
		</div>
		<?php if ( $nonce ) : ?>
			<input type="hidden" name="woocommerce-login-nonce" value="<?php echo esc_attr( $nonce ); ?>">
		<?php endif; ?>

		<?php do_action( 'woocommerce_login_form' ); ?>
		<button type="submit" class="btn-signin" name="login" value="Login" data-magnetic="0.12"><?php esc_html_e( 'Sign In', 'aureon' ); ?></button>
	</form>

	<?php if ( $register_enabled && $register_url ) : ?>
		<div class="auth-divider"><span><?php esc_html_e( 'or', 'aureon' ); ?></span></div>
		<div class="auth-footer">
			<?php esc_html_e( "Don't have an account?", 'aureon' ); ?>
			<a href="<?php echo esc_url( $register_url ); ?>"><?php esc_html_e( 'Join the Void', 'aureon' ); ?></a>
		</div>
	<?php endif; ?>
</div>