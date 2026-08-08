<?php
/**
 * Order confirmation section — thank-you page composition.
 *
 * Renders the order/confirmation component fed by adapter-order.
 *
 * @package Aureon
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

aether_register_section( 'order-confirmation', array(
	'template' => 'sections/section-order-confirmation.php',
	'adapter'  => 'adapter-order.php',
	'behavior' => array( 'reveal-item' => true ),
) );

if ( ! isset( $sectionData ) || ! is_array( $sectionData ) ) {
	return; // Registration only — render happens via aether_render_section().
}

$behavior = isset( $sectionData['behavior'] ) ? aether_viewmodel_behavior( $sectionData['behavior'] ) : array();
aether_render_component( 'order/confirmation', array(
	'title'         => isset( $sectionData['title'] ) ? $sectionData['title'] : '',
	'subtitle'      => isset( $sectionData['subtitle'] ) ? $sectionData['subtitle'] : '',
	'order_number'  => isset( $sectionData['order_number'] ) ? $sectionData['order_number'] : '',
	'email_note'    => isset( $sectionData['email_note'] ) ? $sectionData['email_note'] : '',
	'delivery_note' => isset( $sectionData['delivery_note'] ) ? $sectionData['delivery_note'] : '',
	'shop_url'      => isset( $sectionData['shop_url'] ) ? $sectionData['shop_url'] : '',
	'track_url'     => isset( $sectionData['track_url'] ) ? $sectionData['track_url'] : '',
	'behavior'      => $behavior,
) );