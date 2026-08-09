<?php
/**
 * Preloader — full-screen brand splash shown during initial paint.
 *
 * Key:    'shell/preloader'
 * Source: engine-native (global chrome — all 21 source pages)
 *
 * Props:  (all keys optional; escaped at render; missing key → default listed)
 * - `string $brand  Wordmark text. Default 'AETHER'.`
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

$brand = isset( $componentData['brand'] ) ? $componentData['brand'] : 'AETHER';
?>
<div id="preloader" aria-hidden="true">
	<noscript><style>#preloader{display:none!important}</style></noscript>
	<div class="preloader-inner">
		<div class="preloader-logo"><?php echo esc_html( $brand ); ?></div>
		<div class="preloader-bar">
			<div class="preloader-progress"></div>
		</div>
	</div>
</div>
