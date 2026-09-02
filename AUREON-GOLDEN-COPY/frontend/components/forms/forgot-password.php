<?php
/**
 * Forgot password — forgot-password modal form posting to WC lost-password.
 *
 * Key:    'forms/forgot-password'
 * Source: login.html `.forgot-form`
 *
 * Props:  (all keys optional; escaped at render; missing key → default listed)
 * - `string $action  Form action URL (wc_lostpassword_url). Default ''.`
 * - `string $nonce   Lost-password WP nonce. Default ''.`
 * - `string $title   Modal title. Default 'Reset Password'.`
 * - `string $text    Helper text. Default 'Enter your email and we'll send you a reset link.'.`
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

$action = isset( $componentData['action'] ) ? $componentData['action'] : '';
$nonce  = isset( $componentData['nonce'] ) ? $componentData['nonce'] : '';
$title  = isset( $componentData['title'] ) ? $componentData['title'] : __( 'Reset Password', 'aureon' );
$text   = isset( $componentData['text'] ) ? $componentData['text'] : __( "Enter your email and we'll send you a reset link.", 'aureon' );
?>
<div class="forgot-modal" id="forgotModal" role="dialog" aria-modal="true" aria-labelledby="forgotModalTitle" hidden>
	<div class="forgot-modal-content">
		<button type="button" class="forgot-modal-close" id="forgotModalClose" aria-label="<?php esc_attr_e( 'Close', 'aureon' ); ?>">
			<i class="fas fa-times"></i>
		</button>
		<h2 class="forgot-modal-title" id="forgotModalTitle"><?php echo esc_html( $title ); ?></h2>
		<p class="forgot-modal-text"><?php echo esc_html( $text ); ?></p>
		<form method="post" action="<?php echo esc_url( $action ); ?>" id="forgotForm" novalidate>
			<div class="form-group">
				<label class="form-label" for="forgotEmail"><?php esc_html_e( 'Email Address', 'aureon' ); ?></label>
				<input type="email" id="forgotEmail" class="form-input" name="user_login" placeholder="your@email.com" autocomplete="email" required>
			</div>
			<?php if ( $nonce ) : ?>
				<input type="hidden" name="_wpnonce" value="<?php echo esc_attr( $nonce ); ?>">
			<?php endif; ?>
			<input type="hidden" name="wc_reset_password" value="true">
			<button type="submit" class="btn-signin" id="resetBtn"><?php esc_html_e( 'Send Reset Link', 'aureon' ); ?></button>
		</form>
	</div>
</div>