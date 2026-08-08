<?php
/**
 * Contact adapter — contact form fields + destination + info cards.
 *
 * @package Aureon
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function aether_adapter_contact() {
    $socials = aether_adapter_socials();

    return array(
        'fields' => array(
            array( 'name' => 'aether_name', 'label' => __( 'Name', 'aureon' ), 'type' => 'text', 'required' => true, 'placeholder' => __( 'Your full name', 'aureon' ) ),
            array( 'name' => 'aether_email', 'label' => __( 'Email', 'aureon' ), 'type' => 'email', 'required' => true, 'placeholder' => __( 'you@example.com', 'aureon' ) ),
            array(
                'name'     => 'aether_subject',
                'label'    => __( 'Subject', 'aureon' ),
                'type'     => 'select',
                'required' => true,
                'options'  => array(
                    'general'   => __( 'General Inquiry', 'aureon' ),
                    'order'     => __( 'Order Support', 'aureon' ),
                    'returns'   => __( 'Returns', 'aureon' ),
                    'wholesale' => __( 'Wholesale', 'aureon' ),
                ),
            ),
            array( 'name' => 'aether_message', 'label' => __( 'Message', 'aureon' ), 'type' => 'textarea', 'required' => true, 'placeholder' => __( 'How can we help you?', 'aureon' ) ),
        ),
        'action' => admin_url( 'admin-ajax.php' ),
        'nonce'  => wp_create_nonce( 'aether_contact' ),
        'info'   => array(
            array(
                'icon'  => 'fa-location-dot',
                'title' => __( 'Address', 'aureon' ),
                'lines' => array(
                    __( '123 Innovation Drive', 'aureon' ),
                    __( 'San Francisco, CA 94102', 'aureon' ),
                ),
            ),
            array(
                'icon'  => 'fa-envelope',
                'title' => __( 'Email', 'aureon' ),
                'lines' => array( get_option( 'admin_email', 'support@example.com' ) ),
                'href'  => 'mailto:' . get_option( 'admin_email' ),
            ),
            array(
                'icon'  => 'fa-clock',
                'title' => __( 'Hours', 'aureon' ),
                'lines' => array( __( 'Mon—Fri 9am—6pm PST', 'aureon' ) ),
            ),
        ),
        'socials' => $socials,
    );
}