<?php
/**
 * Options adapter — maps Customizer settings to component data.
 *
 * @package Aureon
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function aether_adapter_options( $keys = array() ) {
    $data = array();
    foreach ( $keys as $key => $default ) {
        $data[ $key ] = aureon_get_option( $key, $default );
    }
    return $data;
}
