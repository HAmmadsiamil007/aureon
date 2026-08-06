<?php
/**
 * Template for displaying product cards in the shop grid.
 *
 * AETHER-styled product card.
 *
 * @package Aureon
 * @version 8.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}

$classes = apply_filters( 'woocommerce_product_loop_class', array(), $product );

// Badge logic.
$badge_class = '';
$badge_text  = '';

if ( $product->is_on_sale() ) {
	$badge_class = 'badge-sale';
	$badge_text  = 'Sale';
} elseif ( $product->is_featured() ) {
	$badge_text = 'Bestseller';
}

// Rating.
$rating_html = '';
if ( $product->get_rating_count() > 0 ) {
	$rating      = round( $product->get_average_rating() );
	$rating_html = '<div class="product-rating">';
	for ( $i = 1; $i <= 5; $i++ ) {
		$rating_html .= $i <= $rating ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>';
	}
	$rating_html .= '<span>(' . esc_html( $product->get_rating_count() ) . ')</span></div>';
}
?>
<li <?php wc_product_class( 'product-card-wrapper', $product ); ?>>
	<div class="product-card" data-tilt data-reveal-item>
		<div class="product-image" data-image-zoom>
			<a href="<?php the_permalink(); ?>">
				<?php echo wp_kses_post( $product->get_image( 'woocommerce_thumbnail' ) ); ?>
			</a>
			<?php if ( $badge_text ) : ?>
				<span class="product-badge <?php echo esc_attr( $badge_class ); ?>"><?php echo esc_html( $badge_text ); ?></span>
			<?php endif; ?>
			<div class="product-actions">
				<button class="product-action-btn add-to-wishlist" data-product-id="<?php echo esc_attr( $product->get_id() ); ?>" aria-label="Add to wishlist">
					<i class="fas fa-heart"></i>
				</button>
				<a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="product-action-btn" aria-label="Quick view">
					<i class="fas fa-eye"></i>
				</a>
			</div>
		</div>
		<div class="product-info">
			<?php echo wp_kses_post( $rating_html ); ?>
			<h3 class="product-name">
				<a href="<?php the_permalink(); ?>"><?php echo esc_html( $product->get_name() ); ?></a>
			</h3>
			<?php if ( $product->get_short_description() ) : ?>
				<p class="product-tagline"><?php echo esc_html( wp_trim_words( $product->get_short_description(), 6 ) ); ?></p>
			<?php endif; ?>
			<div class="product-price-row">
				<span class="product-price"><?php echo wp_kses_post( $product->get_price_html() ); ?></span>
				<?php if ( $product->manually_added() || $product->add_to_cart_url() ) : ?>
					<a href="<?php echo esc_url( $product->add_to_cart_url() ); ?>" class="btn btn-sm btn-primary" data-magnetic="0.12" aria-label="Add <?php echo esc_attr( $product->get_name() ); ?> to cart">
						Add to Cart
					</a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</li>
