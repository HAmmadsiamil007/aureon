<?php
/**
 * Coming soon adapter — countdown + notify form + socials data.
 *
 * @package Aureon
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function aether_adapter_coming_soon( $args = array() ) {
	return array(
		'brand'    => get_bloginfo( 'name' ),
		'title'    => __( 'Something is Coming', 'aureon' ),
		'subtitle' => __( 'The next evolution in performance footwear drops soon.', 'aureon' ),
		'target'   => gmdate( 'Y-m-d', strtotime( '+14 days' ) ),
		'socials'  => aether_adapter_socials(),
	);
}