<?php
/**
 * Empty state — graceful "nothing here yet" block for sections that have
 * no data (blog grid, related products, search results).
 *
 * Key:    'utility/empty-state'
 *
 * Props:  (all keys optional; escaped at render; missing key → default listed)
 * - `string $title         Title. Default 'Nothing here yet'.`
 * - `string $description   Description. Default 'Check back soon — new content is on the way.'.`
 * - `string $icon          FontAwesome icon class. Default 'fa-inbox'.`
 * - `string $action_label  CTA label. Default ''.`
 * - `string $action_url    CTA URL. Default ''.`
 * - `array  $behavior      Behavior whitelist. Default [].`
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

$title       = isset( $componentData['title'] ) ? $componentData['title'] : __( 'Nothing here yet', 'aureon' );
$description = isset( $componentData['description'] ) ? $componentData['description'] : __( 'Check back soon — new content is on the way.', 'aureon' );
$icon        = isset( $componentData['icon'] ) ? $componentData['icon'] : 'fa-inbox';
$action_label = isset( $componentData['action_label'] ) ? $componentData['action_label'] : '';
$action_url   = isset( $componentData['action_url'] ) ? $componentData['action_url'] : '';
$behavior     = isset( $componentData['behavior'] ) ? (array) $componentData['behavior'] : array();
?>
<div class="empty-state" <?php echo aether_behavior_attrs( $behavior ); ?>>
	<i class="fas <?php echo esc_attr( $icon ); ?> empty-state-icon" aria-hidden="true"></i>
	<h3 class="empty-state-title" data-phantom="empty_state_title"><?php echo esc_html( $title ); ?></h3>
	<?php if ( $description ) : ?>
		<p class="empty-state-description"><?php echo esc_html( $description ); ?></p>
	<?php endif; ?>
	<?php if ( $action_label && $action_url ) : ?>
		<a href="<?php echo esc_url( $action_url ); ?>" class="btn btn-outline" data-magnetic="0.12"><?php echo esc_html( $action_label ); ?></a>
	<?php endif; ?>
</div>