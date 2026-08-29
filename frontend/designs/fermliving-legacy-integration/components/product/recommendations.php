<?php
/**
 * Ferm Living product recommendations — "You May Also Like" carousel.
 *
 * Key:    'product/recommendations' (override)
 * Source: fermliving.com product page related products
 * Props:  items (image, name, price, url, tagline), title.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$componentData = isset( $componentData ) ? (array) $componentData : array();

$title = isset( $componentData['title'] ) ? $componentData['title'] : __( 'You May Also Like', 'aureon' );
$items = isset( $componentData['items'] ) ? (array) $componentData['items'] : array();

if ( empty( $items ) ) {
	return;
}
?>
<section class="pd-related">
	<div class="container">
		<h2 class="pd-section-title"><?php echo esc_html( $title ); ?></h2>
		<div class="pd-gold-line"></div>
		<div class="swiper pd-related-swiper">
			<div class="swiper-wrapper">
				<?php foreach ( $items as $item ) :
					$image    = isset( $item['image'] ) ? $item['image'] : '';
					$name     = isset( $item['name'] ) ? $item['name'] : '';
					$price    = isset( $item['price'] ) ? $item['price'] : '';
					$url      = isset( $item['url'] ) ? $item['url'] : '#';
					$tagline  = isset( $item['tagline'] ) ? $item['tagline'] : '';
					$alt      = isset( $item['alt'] ) ? $item['alt'] : $name;
					if ( empty( $name ) ) {
						continue;
					}
					?>
					<div class="swiper-slide">
						<a href="<?php echo esc_url( $url ); ?>" class="product-card" data-tilt>
							<div class="product-image" data-image-zoom>
								<?php if ( $image ) : ?>
									<img loading="lazy"
										 src="<?php echo esc_url( $image ); ?>"
										 alt="<?php echo esc_attr( $alt ); ?>"
										 width="400"
										 height="533"
										 decoding="async">
								<?php endif; ?>
							</div>
							<div class="product-info">
								<h3 class="product-name"><?php echo esc_html( $name ); ?></h3>
								<?php if ( $tagline ) : ?>
									<p class="product-tagline"><?php echo esc_html( $tagline ); ?></p>
								<?php endif; ?>
								<p class="product-price"><?php echo esc_html( $price ); ?></p>
							</div>
						</a>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>
