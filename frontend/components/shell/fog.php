<?php
/**
 * Fog — global 3-layer cinematic CSS fog backdrop.
 *
 * Key:    'shell/fog'
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
<div id="fog-system" aria-hidden="true">
	<div id="foglayer_01" class="fog">
		<div class="image01"></div>
		<div class="image02"></div>
	</div>
	<div id="foglayer_02" class="fog">
		<div class="image01"></div>
		<div class="image02"></div>
	</div>
	<div id="foglayer_03" class="fog">
		<div class="image01"></div>
		<div class="image02"></div>
	</div>
</div>
