<?php
/**
 * Ferm Living announcement bar — rotating USP carousel.
 *
 * Key:    'shell/announcement' (override)
 * Source: fermliving.com announcement structure
 * Props:  items (array of {text}).
 * Contract: keeps .announcement-bar, .announcement-marquee, .announcement-track,
 *           .announcement-item — platform announcement JS operates unchanged.
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

$announcement_enabled = (bool) aureon_get_option( 'aether_announcement_enabled', true );
if ( ! $announcement_enabled ) {
	return;
}

$speed = (int) aureon_get_option( 'ferm_announcement_speed', 4000 );
?>
<div class="announcement-bar" id="announcementBar" role="region" aria-label="Site announcements" data-announcement-bar data-speed="<?php echo esc_attr( $speed ); ?>" data-total="<?php echo esc_attr( count( $texts ) ); ?>">
	<div class="announcement-marquee">
		<div class="announcement-track">
			<?php
			for ( $pass = 0; $pass < 2; $pass++ ) :
				foreach ( $texts as $text ) :
					?>
					<span class="announcement-item" data-announcement-item>
						<span class="star">&#10022;</span>
						<?php echo esc_html( $text ); ?>
					</span>
					<?php
				endforeach;
			endfor;
			?>
		</div>
	</div>
	<?php if ( count( $texts ) > 1 ) : ?>
	<div class="announcement-counter" data-announcement-counter>
		<span class="announcement-counter-current">1</span>/<span class="announcement-counter-total"><?php echo esc_html( count( $texts ) ); ?></span>
	</div>
	<?php endif; ?>
</div>
