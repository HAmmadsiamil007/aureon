<?php
/**
 * Skip link — keyboard accessibility shortcut to #main.
 *
 * Key:    'shell/skip-link'
 * Source: engine-native (global chrome — all 21 source pages)
 *
 * Props:  (all keys optional; escaped at render; missing key → default listed)
 * - (none — static markup)
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
?>
<a class="skip-to-content visually-hidden" href="#main"><?php esc_html_e( 'Skip to main content', 'aureon' ); ?></a>
