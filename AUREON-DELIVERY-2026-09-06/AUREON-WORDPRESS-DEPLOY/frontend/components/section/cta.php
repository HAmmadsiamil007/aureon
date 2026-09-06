<?php
/**
 * CTA banner — promotional call-to-action band.
 *
 * Key:    'section/cta'
 * Source: index.html `.cta`
 *
 * Props:  (all keys optional; escaped at render; missing key → default listed)
 * - `string $label  CTA label. Default ''.`
 * - `string $url    CTA href. Default '#'.`
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

$label = isset( $componentData['label'] ) ? $componentData['label'] : '';
$url   = isset( $componentData['url'] ) ? $componentData['url'] : '#';

if ( ! $label ) {
	return;
}
?>
<div class="section-cta">
	<a href="<?php echo esc_url( $url ); ?>" class="btn btn-outline btn-lg" data-magnetic="0.12"><?php echo esc_html( $label ); ?> <i class="fas fa-arrow-right"></i></a>
</div>
