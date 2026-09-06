<?php
/**
 * Coming soon adapter — countdown + notify form + socials data.
 *
 * @package Aureon
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function aether_adapter_coming_soon( $args = array() ) {
	$target = (string) aureon_get_option( 'aether_coming_soon_date', '' );
	if ( '' === $target ) {
		// Persist a fixed target on first render so the countdown does not
		// reset every request (F3-2). Editable via the aureon_settings bucket.
		$target     = gmdate( 'Y-m-d', strtotime( '+14 days' ) );
		$settings   = get_option( 'aureon_settings', array() );
		if ( is_array( $settings ) ) {
			$settings['aether_coming_soon_date'] = $target;
			update_option( 'aureon_settings', $settings );
		}
	}

	return array(
		'brand'    => get_bloginfo( 'name' ),
		'title'    => __( 'Something is Coming', 'aureon' ),
		'subtitle' => __( 'The next evolution in performance footwear drops soon.', 'aureon' ),
		'target'   => $target,
		'socials'  => aether_adapter_socials(),
	);
}