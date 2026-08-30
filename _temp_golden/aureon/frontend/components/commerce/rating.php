<?php
/**
 * Star rating — read-only 0-5 star display used across product/social cards.
 *
 * Key:    'commerce/rating'
 * Source: engine-native (reused across product/social components)
 *
 * Props:  (all keys optional; escaped at render; missing key → default listed)
 * - `float $stars  Score 0-5. Default 0.`
 * - `int $max    Star count. Default 5.`
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

$stars = isset( $componentData['stars'] ) ? (float) $componentData['stars'] : 0;
$max   = isset( $componentData['max'] ) ? (int) $componentData['max'] : 5;

for ( $i = 1; $i <= $max; $i++ ) {
	if ( $stars >= $i - 0.25 ) {
		echo '<i class="fas fa-star"></i>';
	} elseif ( $stars >= $i - 0.75 ) {
		echo '<i class="fas fa-star-half-alt"></i>';
	} else {
		echo '<i class="far fa-star"></i>';
	}
}
