<?php
/**
 * Order confirmation — thank-you hero for the order-received page.
 *
 * Key:    'order/confirmation'
 * Source: thank-you.html `.order-confirmation`
 *
 * Props:  (all keys optional; escaped at render; missing key → default listed)
 * - `string $title         Panel title. Default ''.`
 * - `string $subtitle      Subtitle. Default ''.`
 * - `string $order_number  Public order number. Default ''.`
 * - `string $email_note    Email confirmation note. Default ''.`
 * - `string $delivery_note Delivery estimate note. Default ''.`
 * - `string $shop_url      Continue-shopping CTA. Default ''.`
 * - `string $track_url     Order tracking link. Default ''.`
 * - `array $behavior      Behavior whitelist. Default [].`
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

$title         = isset( $componentData['title'] ) ? $componentData['title'] : '';
$subtitle      = isset( $componentData['subtitle'] ) ? $componentData['subtitle'] : '';
$order_number  = isset( $componentData['order_number'] ) ? $componentData['order_number'] : '';
$email_note    = isset( $componentData['email_note'] ) ? $componentData['email_note'] : '';
$delivery_note = isset( $componentData['delivery_note'] ) ? $componentData['delivery_note'] : '';
$shop_url      = isset( $componentData['shop_url'] ) ? $componentData['shop_url'] : '';
$track_url     = isset( $componentData['track_url'] ) ? $componentData['track_url'] : '';
$behavior      = isset( $componentData['behavior'] ) && is_array( $componentData['behavior'] )
	? aether_viewmodel_behavior( $componentData['behavior'] ) : array();
?>
<section class="confirmation-section" data-phantom-bg="confirmation">
	<div class="container">
		<div class="confirmation-icon">
			<i class="fas fa-check" aria-hidden="true"></i>
		</div>
		<h2 <?php echo aether_behavior_attrs( $behavior ); // phpcs:ignore ?>><?php echo esc_html( $title ); ?></h2>
		<?php if ( $subtitle ) : ?>
			<p class="subtitle"><?php echo esc_html( $subtitle ); ?></p>
		<?php endif; ?>
		<?php if ( $order_number ) : ?>
			<p class="order-number"><?php echo esc_html( $order_number ); ?></p>
		<?php endif; ?>
		<?php if ( $email_note ) : ?>
			<p class="order-note"><?php echo esc_html( $email_note ); ?></p>
		<?php endif; ?>
		<?php if ( $delivery_note ) : ?>
			<p class="order-note"><?php echo esc_html( $delivery_note ); ?></p>
		<?php endif; ?>
		<div class="confirmation-buttons">
			<?php if ( $shop_url ) : ?>
				<a href="<?php echo esc_url( $shop_url ); ?>" class="btn btn-primary" data-magnetic="0.12"><?php esc_html_e( 'Continue Shopping', 'aureon' ); ?></a>
			<?php endif; ?>
			<?php if ( $track_url ) : ?>
				<a href="<?php echo esc_url( $track_url ); ?>" class="btn btn-outline" data-magnetic="0.12"><?php esc_html_e( 'View Order', 'aureon' ); ?></a>
			<?php endif; ?>
		</div>
	</div>
</section>
