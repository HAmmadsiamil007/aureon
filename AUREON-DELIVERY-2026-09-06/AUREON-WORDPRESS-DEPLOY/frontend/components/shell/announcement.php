<?php
/**
 * Announcement bar — premium marquee with continuous scroll.
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
	exit;
}

$componentData = isset( $componentData ) ? (array) $componentData : array();
$items         = isset( $componentData['items'] ) ? (array) $componentData['items'] : array();

if ( empty( $items ) ) {
	return;
}

// Flatten items to text strings.
$texts = array();
foreach ( $items as $item ) {
	$text = isset( $item['text'] ) ? $item['text'] : '';
	if ( '' !== $text ) {
		$texts[] = $text;
	}
}

if ( empty( $texts ) ) {
	return;
}
?>
<div class="announcement-bar" id="announcementBar" role="region" aria-label="Site announcements">
	<div class="announcement-marquee">
		<div class="announcement-track">
			<?php
			// Render items twice for seamless infinite loop.
			for ( $pass = 0; $pass < 2; $pass++ ) :
				foreach ( $texts as $index => $text ) :
					?>
					<span class="announcement-item">
						<span class="star">&#10022;</span>
						<?php echo esc_html( $text ); ?>
					</span>
					<?php
				endforeach;
			endfor;
			?>
		</div>
	</div>
</div>
