<?php
/**
 * Newsletter form — email capture with submit and success state.
 *
 * Key:    'forms/newsletter'
 * Source: index.html `.newsletter-form`
 *
 * Props:  (all keys optional; escaped at render; missing key → default listed)
 * - `string $placeholder   Placeholder text. Default 'Enter your email'.`
 * - `string $button_text  Submit label. Default 'Subscribe'.`
 * - `string $note          Lock-in note. Default 'No spam. Unsubscribe anytime.'.`
 * - `string $success_text Success message. Default 'Welcome to the void. Check your inbox.'.`
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
?>
<form class="newsletter-form" id="newsletterForm">
	<div class="newsletter-input-wrap">
		<input type="email" placeholder="<?php echo esc_attr( isset( $componentData['placeholder'] ) ? $componentData['placeholder'] : __( 'Enter your email', 'aureon' ) ); ?>" required class="newsletter-input" id="newsletterEmail" aria-label="<?php esc_attr_e( 'Email address', 'aureon' ); ?>">
		<button type="submit" class="newsletter-btn">
			<span class="newsletter-btn-text"><?php echo esc_html( isset( $componentData['button_text'] ) ? $componentData['button_text'] : __( 'Subscribe', 'aureon' ) ); ?></span>
			<i class="fas fa-arrow-right newsletter-btn-icon"></i>
		</button>
	</div>
	<p class="newsletter-note"><i class="fas fa-lock"></i> <?php echo esc_html( isset( $componentData['note'] ) ? $componentData['note'] : __( 'No spam. Unsubscribe anytime.', 'aureon' ) ); ?></p>
</form>
<div class="newsletter-success" id="newsletterSuccess">
	<i class="fas fa-check-circle"></i>
	<p><?php echo esc_html( isset( $componentData['success_text'] ) ? $componentData['success_text'] : __( 'Welcome to the void. Check your inbox.', 'aureon' ) ); ?></p>
</div>