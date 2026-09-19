<?php
/**
 * Ferm Living room grid — WC categories as full-bleed image tiles.
 *
 * Shadows engine section id 'categories'. Same adapter contract:
 * adapter-wc-categories.php -> items[] { name, count, image, alt, url,
 * modifier } + has_more/all_categories_url.
 *
 * Source: fermliving.com homepage room tiles — edge-to-edge image cards with
 * the room name overlaid bottom-left in cream.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

aether_register_section( 'categories', array(
	'template' => 'sections/section-categories.php',
	'adapter'  => 'adapter-wc-categories.php',
	'adapter_args' => array(
		'aether_categories_label'    => '',
		'aether_categories_title'    => '',
		'aether_categories_subtitle' => '',
	),
	'behavior' => array(),
) );

if ( ! isset( $sectionData ) || ! is_array( $sectionData ) ) {
	return; // Registration only — render happens via aether_render_section().
}

$items = isset( $sectionData['items'] ) ? (array) $sectionData['items'] : array();

if ( empty( $items ) ) {
	return;
}
?>
<section class="ferm-room-grid ferm-room-grid--categories" id="categories" aria-label="<?php esc_attr_e( 'Shop by room', 'aureon' ); ?>">
	<div class="ferm-room-grid-inner" data-carousel>
		<?php foreach ( $items as $item ) :
			$item_title = isset( $item['name'] ) ? $item['name'] : '';
			$item_image = isset( $item['image'] ) ? $item['image'] : '';
			$item_alt   = isset( $item['alt'] ) ? $item['alt'] : $item_title;
			$item_url   = isset( $item['url'] ) ? $item['url'] : '#';
			?>
			<div class="ferm-room-card">
				<div class="ferm-room-card-image">
					<a href="<?php echo esc_url( $item_url ); ?>" class="ferm-room-card-link" aria-label="<?php echo esc_attr( sprintf( __( 'Shop %s', 'aureon' ), $item_title ) ); ?>">
						<?php if ( $item_image ) : ?>
							<img loading="lazy"
								 src="<?php echo esc_url( $item_image ); ?>"
								 alt="<?php echo esc_attr( $item_alt ); ?>"
								 width="480"
								 height="600"
								 decoding="async">
						<?php endif; ?>
					</a>
					<div class="ferm-room-card-title">
						<p><?php echo esc_html( $item_title ); ?></p>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
	<?php if ( count( $items ) > 5 ) : ?>
	<div class="ferm-carousel-nav">
		<button type="button" class="ferm-carousel-arrow ferm-carousel-prev" data-carousel-prev aria-label="Previous">
			<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M15 18l-6-6 6-6"/></svg>
		</button>
		<button type="button" class="ferm-carousel-arrow ferm-carousel-next" data-carousel-next aria-label="Next">
			<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 18l6-6-6-6"/></svg>
		</button>
	</div>
	<?php endif; ?>
</section>
