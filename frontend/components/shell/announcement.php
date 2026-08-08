<?php
/**
 * Announcement bar — marquee of rotating promo messages.
 *
 * Key:    'shell/announcement'
 * Source: engine-native (global chrome — all 21 source pages)
 *
 * Props:  (all keys optional; escaped at render; missing key → default listed)
 * - `array $items  Message strings. Default [].`
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
$items         = isset( $componentData['items'] ) ? (array) $componentData['items'] : array();

if ( empty( $items ) ) {
	return;
}
?>
<div class="announcement-bar" id="announcementBar">
	<div class="announcement-content">
		<?php foreach ( $items as $index => $item ) : ?>
			<?php if ( $index > 0 ) : ?>
				<span class="separator">|</span>
			<?php endif; ?>
			<span>
				<?php if ( ! empty( $item['icon'] ) ) : ?>
					<i class="<?php echo esc_attr( $item['icon'] ); ?>" aria-hidden="true"></i>
				<?php endif; ?>
				<?php echo esc_html( isset( $item['text'] ) ? $item['text'] : '' ); ?>
			</span>
		<?php endforeach; ?>
	</div>
</div>
