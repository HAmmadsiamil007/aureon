<?php
/**
 * Ferm Living room grid section — category image cards with product links.
 *
 * Used on homepage for "The Bedroom", "The Office", "The Living Room" etc.
 * Renders a grid of room/category cards, each with an image, title, and optional
 * product category links below.
 *
 * Key:    'ferm-room-grid' (pack section)
 * Source: fermliving.com homepage room sections
 * Props:  items[] (title, image, url, links[]).
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

aether_register_section( 'ferm-room-grid', array(
	'template' => 'sections/section-room-grid.php',
	'adapter'  => 'adapter-options.php',
	'behavior' => array(),
) );

if ( ! isset( $sectionData ) || ! is_array( $sectionData ) ) {
	return;
}

$items = isset( $sectionData['items'] ) ? (array) $sectionData['items'] : array();

if ( empty( $items ) ) {
	return;
}
?>
<section class="ferm-room-grid">
	<div class="container">
		<div class="ferm-room-grid-inner">
			<?php foreach ( $items as $item ) :
				$item_title = isset( $item['title'] ) ? $item['title'] : '';
				$item_image = isset( $item['image'] ) ? $item['image'] : '';
				$item_url   = isset( $item['url'] ) ? $item['url'] : '#';
				$item_links = isset( $item['links'] ) ? (array) $item['links'] : array();
				?>
				<div class="ferm-room-card">
					<?php /* Room image */ ?>
					<div class="ferm-room-card-image">
						<a href="<?php echo esc_url( $item_url ); ?>" class="ferm-room-card-link" aria-label="<?php echo esc_attr( $item_title ); ?>">
							<?php if ( $item_image ) : ?>
								<img loading="lazy"
									 src="<?php echo esc_url( $item_image ); ?>"
									 alt="<?php echo esc_attr( $item_title ); ?>"
									 width="480"
									 height="600"
									 decoding="async">
							<?php endif; ?>
						</a>
						<?php /* Title overlay */ ?>
						<div class="ferm-room-card-title">
							<p><?php echo esc_html( $item_title ); ?></p>
						</div>
					</div>

					<?php /* Product category links below the card */ ?>
					<?php if ( ! empty( $item_links ) ) : ?>
						<div class="ferm-room-card-links">
							<?php foreach ( $item_links as $link ) :
								$link_label = isset( $link['label'] ) ? $link['label'] : '';
								$link_url   = isset( $link['url'] ) ? $link['url'] : '#';
								if ( empty( $link_label ) ) {
									continue;
								}
								?>
								<a href="<?php echo esc_url( $link_url ); ?>" class="ferm-room-card-link-item">
									<strong><?php echo esc_html( $link_label ); ?></strong>
								</a>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
