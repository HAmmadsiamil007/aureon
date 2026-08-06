<?php
/**
 * The Template for displaying single products (product detail).
 *
 * AETHER-styled WooCommerce single product template.
 *
 * @package Aureon
 * @version 8.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header( 'shop' );

while ( have_posts() ) :
	the_post();
	global $product;

	$aether      = get_template_directory_uri() . '/assets/aether';
	$shop_url    = wc_get_page_permalink( 'shop' );
	$gallery_ids = $product->get_gallery_image_ids();
	$main_image  = wp_get_attachment_url( $product->get_image_id() );
	$parent_id   = $product->get_id();
	$categories  = wc_get_product_category_list( $parent_id, ', ', '<span class="pd-category">', '</span>' );

	// Related products.
	$related_ids = wc_get_related_products( $parent_id, 4 );
	$related     = wc_get_products( array( 'include' => $related_ids, 'limit' => 4, 'return' => 'objects' ) );
?>

<section class="product-detail" id="productDetail">
	<div class="container">
		<!-- Breadcrumb -->
		<nav class="pd-breadcrumb" aria-label="Breadcrumb">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a>
			<i class="fas fa-chevron-right"></i>
			<a href="<?php echo esc_url( $shop_url ); ?>">Shop</a>
			<i class="fas fa-chevron-right"></i>
			<span><?php echo esc_html( $product->get_name() ); ?></span>
		</nav>

		<div class="pd-layout">
			<!-- Gallery -->
			<div class="pd-gallery">
				<div class="swiper pd-gallery-main">
					<div class="swiper-wrapper">
						<?php
						$all_images = array_merge( array( $product->get_image_id() ), $gallery_ids );
						foreach ( $all_images as $img_id ) :
							$img_url = wp_get_attachment_url( $img_id );
							if ( ! $img_url ) {
								continue;
							}
							?>
							<div class="swiper-slide">
								<img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $product->get_name() ); ?>" loading="lazy">
							</div>
						<?php endforeach; ?>
					</div>
				</div>
				<div class="swiper pd-gallery-thumbs-swiper">
					<div class="swiper-wrapper">
						<?php foreach ( $all_images as $img_id ) :
							$thumb_url = wp_get_attachment_image_url( $img_id, 'thumbnail' );
							if ( ! $thumb_url ) {
								continue;
							}
							?>
							<div class="swiper-slide">
								<img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php echo esc_attr( $product->get_name() ); ?>" loading="lazy">
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>

			<!-- Info -->
			<div class="pd-info">
				<?php echo wp_kses_post( $categories ); ?>
				<h1 class="pd-title"><?php echo esc_html( $product->get_name() ); ?></h1>

				<?php if ( $product->get_rating_count() > 0 ) : ?>
					<div class="pd-rating">
						<div class="pd-stars">
							<?php for ( $i = 1; $i <= 5; $i++ ) : ?>
								<i class="<?php echo $i <= round( $product->get_average_rating() ) ? 'fas' : 'far'; ?> fa-star"></i>
							<?php endfor; ?>
						</div>
						<span class="pd-rating-count">(<?php echo esc_html( $product->get_rating_count() ); ?> reviews)</span>
					</div>
				<?php endif; ?>

				<div class="pd-price">
					<?php echo wp_kses_post( $product->get_price_html() ); ?>
				</div>

				<div class="pd-short-desc">
					<?php echo wp_kses_post( $product->get_short_description() ); ?>
				</div>

				<!-- Color Selection -->
				<?php
				$attributes = $product->get_attributes();
				if ( isset( $attributes['pa_color'] ) || isset( $attributes['color'] ) ) :
					$color_attr = isset( $attributes['pa_color'] ) ? $attributes['pa_color'] : $attributes['color'];
					$colors     = $color_attr->get_terms();
					if ( ! empty( $colors ) ) :
					?>
					<div class="pd-option-group">
						<label class="pd-option-label">Color: <span id="pdColorName"><?php echo esc_html( $colors[0]->name ); ?></span></label>
						<div class="pd-color-options">
							<?php foreach ( $colors as $color ) : ?>
								<button class="pd-color-btn <?php echo 0 === $color ? 'active' : ''; ?>" data-color="<?php echo esc_attr( $color->name ); ?>" aria-label="<?php echo esc_attr( $color->name ); ?>"></button>
							<?php endforeach; ?>
						</div>
					</div>
					<?php
					endif;
				endif;
				?>

				<!-- Size Selection -->
				<?php
				if ( isset( $attributes['pa_size'] ) || isset( $attributes['size'] ) ) :
					$size_attr = isset( $attributes['pa_size'] ) ? $attributes['pa_size'] : $attributes['size'];
					$sizes     = $size_attr->get_terms();
					if ( ! empty( $sizes ) ) :
					?>
					<div class="pd-option-group">
						<label class="pd-option-label">Size: <span id="pdSizeName"><?php echo esc_html( $sizes[0]->name ); ?></span></label>
						<div class="pd-size-options">
							<?php foreach ( $sizes as $size ) : ?>
								<button class="pd-size-btn <?php echo 0 === $size ? 'active' : ''; ?>"><?php echo esc_html( $size->name ); ?></button>
							<?php endforeach; ?>
						</div>
						<a href="#" class="pd-size-guide-link" id="openSizeGuide">Size Guide</a>
					</div>
					<?php
					endif;
				endif;
				?>

				<!-- Add to Cart -->
				<div class="pd-add-to-cart">
					<?php woocommerce_template_single_add_to_cart(); ?>
				</div>

				<!-- Features -->
				<div class="pd-features">
					<div class="pd-feature">
						<i class="fas fa-truck"></i>
						<span>Free Shipping Over $200</span>
					</div>
					<div class="pd-feature">
						<i class="fas fa-undo"></i>
						<span>30-Day Free Returns</span>
					</div>
					<div class="pd-feature">
						<i class="fas fa-shield-alt"></i>
						<span>2-Year Warranty</span>
					</div>
				</div>

				<!-- Accordion: Description -->
				<div class="pd-accordion">
					<div class="pd-accordion-item active">
						<button class="pd-accordion-header">
							<span>Description</span>
							<i class="fas fa-minus"></i>
						</button>
						<div class="pd-accordion-body">
							<?php echo wp_kses_post( $product->get_description() ); ?>
						</div>
					</div>
					<div class="pd-accordion-item">
						<button class="pd-accordion-header">
							<span>Shipping & Returns</span>
							<i class="fas fa-plus"></i>
						</button>
						<div class="pd-accordion-body">
							<p>Free standard shipping on orders over $200. Express shipping available at checkout. 30-day no-questions-asked returns.</p>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Related Products -->
		<?php if ( ! empty( $related ) ) : ?>
		<div class="pd-related">
			<div class="section-header">
				<span class="section-label" data-motion-text="words">You May Also Like</span>
				<h2 class="section-title" data-motion-text="words">Related Products</h2>
			</div>
			<div class="swiper pd-related-swiper">
				<div class="swiper-wrapper">
					<?php foreach ( $related as $rel_product ) : ?>
						<div class="swiper-slide">
							<a href="<?php echo esc_url( $rel_product->get_permalink() ); ?>" class="product-card" data-tilt>
								<div class="product-image" data-image-zoom>
									<?php echo wp_kses_post( $rel_product->get_image( 'woocommerce_thumbnail' ) ); ?>
								</div>
								<div class="product-info">
									<h3 class="product-name"><?php echo esc_html( $rel_product->get_name() ); ?></h3>
									<span class="product-price"><?php echo wp_kses_post( $rel_product->get_price_html() ); ?></span>
								</div>
							</a>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
		<?php endif; ?>
	</div>
</section>

<?php
endwhile;

get_footer( 'shop' );
