<?php
/**
 * Auth adapter — login/register form data for the auth sections.
 *
 * @package Aureon
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function aether_adapter_auth( $args = array() ) {
    $my_account_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/my-account/' );
    $register_off   = 'yes' !== get_option( 'woocommerce_enable_myaccount_registration', 'no' );

    $lost_url = function_exists( 'wc_lostpassword_url' ) ? wc_lostpassword_url() : wp_lostpassword_url();

    return array(
        'brand'           => get_bloginfo( 'name' ),
        'forgot'          => array(
            'action' => $lost_url,
            'nonce'  => function_exists( 'wp_create_nonce' ) ? wp_create_nonce( 'lost_password' ) : '',
        ),
        'login'           => array(
            'action'           => $my_account_url,
            'nonce'            => wp_create_nonce( 'woocommerce-login' ),
            'lost_url'         => $lost_url,
            'forgot_modal'     => true,
            'register_enabled' => ! $register_off,
            'register_url'     => $my_account_url,
        ),
        'register'        => array(
            'action'       => $my_account_url,
            'nonce'        => wp_create_nonce( 'woocommerce-register' ),
            'login_url'    => $my_account_url,
            'show_strength' => true,
        ),
        'redirect'        => function_exists( 'wc_get_account_endpoint_url' ) ? wc_get_account_endpoint_url( 'dashboard' ) : $my_account_url,
        'show_register'   => ! $register_off,
    );
}