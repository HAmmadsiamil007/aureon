<?php
/**
 * Ferm Living preloader — brand wordmark splash during initial paint.
 *
 * Key:    'shell/preloader' (override)
 * Source: fermliving.com preloader structure
 * Props:  brand (string).
 * Contract: keeps #preloader, .preloader-inner, .preloader-logo,
 *           .preloader-bar, .preloader-progress — platform animation JS
 *           operates unchanged.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$componentData = isset( $componentData ) ? (array) $componentData : array();

// Prefer adapter brand (WP site name), then token override, then fallback.
$brand = isset( $componentData['brand'] ) ? $componentData['brand'] : '';
if ( empty( $brand ) ) {
	$brand = aureon_get_option( 'aether_brand_name', 'Ferm Living' );
}
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
