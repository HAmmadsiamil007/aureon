<?php
/**
 * Password strength — strength meter for the register form password field.
 *
 * Key:    'auth/password-strength'
 * Source: join-now.html `.strength`
 *
 * Props:  (all keys optional; escaped at render; missing key → default listed)
 * - `string $target  Client ID of the password input to observe. Default ''.`
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

$target = isset( $componentData['target'] ) ? $componentData['target'] : '';
?>
<div class="strength-bars" data-strength-target="<?php echo esc_attr( $target ); ?>" aria-hidden="true">
	<div class="strength-bar"></div>
	<div class="strength-bar"></div>
	<div class="strength-bar"></div>
	<div class="strength-bar"></div>
</div>
<div class="strength-text" id="strengthText" aria-live="polite"></div>