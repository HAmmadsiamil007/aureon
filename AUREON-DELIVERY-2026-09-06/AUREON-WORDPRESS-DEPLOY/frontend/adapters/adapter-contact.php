<?php
/**
 * Contact adapter — contact form fields + destination + info cards.
 *
 * @package Aureon
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function aether_adapter_contact() {
    $socials = aether_adapter_socials();

    // Editable contact copy — defaults equal the current premium design.
    $address_lines = (array) aureon_get_option( 'aether_contact_address', array(
        __( '123 Innovation Drive', 'aureon' ),
        __( 'San Francisco, CA 94102', 'aureon' ),
    ) );
    $hours = (string) aureon_get_option( 'aether_contact_hours', __( 'Mon—Fri 9am—6pm PST', 'aureon' ) );

    if ( is_string( $address_lines ) && '' !== trim( $address_lines ) ) {
        $decoded = json_decode( $address_lines, true );
        if ( is_array( $decoded ) ) {
            $address_lines = $decoded;
        } else {
            $address_lines = array( $address_lines );
        }
    }

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
                'lines' => array_map( 'sanitize_text_field', $address_lines ),
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
                'lines' => array( sanitize_text_field( $hours ) ),
            ),
        ),
        'socials' => $socials,
    );
}