<?php
/**
 * AETHER Front Page Template.
 *
 * Renders the AETHER homepage with:
 * - Header (preloader, fog, navigation)
 * - Hero slider (Swiper)
 * - Category selector
 * - Bestsellers product grid
 * - Reviews carousel
 * - FAQ accordion
 * - Footer (newsletter, links, social)
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// AETHER components are rendered via action hooks in inc/aether-hooks.php.
// get_header() triggers wp_head() (enqueue) + Aureon hooks where AETHER components attach.
get_header();

$dir_uri = get_template_directory_uri();
$aether  = $dir_uri . '/assets/aether';
$wc_active = class_exists( 'WooCommerce' );
$shop_url  = $wc_active ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
?>

<!-- Hero Slider -->
<section class="hero-slider" id="heroSlider" data-phantom-bg="hero" data-mouse-parallax data-parallax-section>
	<div class="hero-fog" aria-hidden="true">
		<div id="hl_01" class="hf-fog">
			<div class="hf-img"></div>
			<div class="hf-img"></div>
		</div>
		<div id="hl_02" class="hf-fog">
			<div class="hf-img"></div>
			<div class="hf-img"></div>
		</div>
		<div id="hl_03" class="hf-fog">
			<div class="hf-img"></div>
			<div class="hf-img"></div>
		</div>
	</div>
	<div class="swiper hero-swiper">
		<div class="swiper-wrapper">
			<!-- Slide 1 -->
			<div class="swiper-slide hero-slide">
				<div class="hero-slide-bg">
					<img loading="eager" src="<?php echo esc_url( $aether ); ?>/images/Luxury_running_sneaker_on_pedestal_202607222032.jpeg" alt="AETHER Void Runner" data-phantom-alt="hero_slide_1" data-mouse-depth="0.06" data-parallax data-parallax-speed="0.15">
					<div class="hero-slide-overlay"></div>
				</div>
				<div class="container hero-slide-content">
					<div class="hero-slide-text">
						<h1 class="hero-headline" data-swiper-parallax="-200" data-phantom="hero_headline" data-mouse-depth="0.02">AETHER<br><span class="hero-headline-accent">Void Runner</span></h1>
						<p class="hero-subline" data-swiper-parallax="-300" data-phantom="hero_subline">Born from the silence between stars. Carbon-fiber exoskeleton meets adaptive cushioning that learns your stride.</p>
						<div class="hero-cta-group" data-swiper-parallax="-400" data-mouse-depth="0.03">
							<a href="<?php echo esc_url( $shop_url ); ?>" class="btn btn-primary btn-lg" data-magnetic="0.12">Shop Now</a>
							<a href="<?php echo esc_url( home_url( '/about/' ) ); ?>" class="btn btn-outline btn-lg" data-magnetic="0.12">Explore Tech</a>
						</div>
					</div>
				</div>
			</div>
			<!-- Slide 2 -->
			<div class="swiper-slide hero-slide">
				<div class="hero-slide-bg">
					<img loading="eager" src="<?php echo esc_url( $aether ); ?>/images/Luxury_running_sneaker_on_pedestal_202607222032.jpeg" alt="AETHER Cloud Stride" data-phantom-alt="hero_slide_2" data-mouse-depth="0.06" data-parallax data-parallax-speed="0.15">
					<div class="hero-slide-overlay"></div>
				</div>
				<div class="container hero-slide-content">
					<div class="hero-slide-text">
						<h1 class="hero-headline" data-swiper-parallax="-200" data-phantom="hero_headline" data-mouse-depth="0.02">Cloud<br><span class="hero-headline-accent">Stride</span></h1>
						<p class="hero-subline" data-swiper-parallax="-300" data-phantom="hero_subline">Float above the pavement. Zero-gravity foam compounds deliver infinite energy return with every step.</p>
						<div class="hero-cta-group" data-swiper-parallax="-400" data-mouse-depth="0.03">
							<a href="<?php echo esc_url( $shop_url ); ?>" class="btn btn-primary btn-lg" data-magnetic="0.12">Shop Collection</a>
							<a href="<?php echo esc_url( home_url( '/about/' ) ); ?>" class="btn btn-outline btn-lg" data-magnetic="0.12">Our Story</a>
						</div>
					</div>
				</div>
			</div>
			<!-- Slide 3 -->
			<div class="swiper-slide hero-slide">
				<div class="hero-slide-bg">
					<img loading="eager" src="<?php echo esc_url( $aether ); ?>/images/Luxury_running_sneaker_on_pedestal_202607222032.jpeg" alt="AETHER Midnight Edition" data-phantom-alt="hero_slide_3" data-mouse-depth="0.06" data-parallax data-parallax-speed="0.15">
					<div class="hero-slide-overlay"></div>
				</div>
				<div class="container hero-slide-content">
					<div class="hero-slide-text">
						<h1 class="hero-headline" data-swiper-parallax="-200" data-phantom="hero_headline" data-mouse-depth="0.02">Midnight<br><span class="hero-headline-accent">Edition</span></h1>
						<p class="hero-subline" data-swiper-parallax="-300" data-phantom="hero_subline">Darkness refined. A stealth-black silhouette with phosphorescent accents that ignite after sunset.</p>
						<div class="hero-cta-group" data-swiper-parallax="-400" data-mouse-depth="0.03">
							<a href="<?php echo esc_url( $shop_url ); ?>" class="btn btn-primary btn-lg" data-magnetic="0.12">Get Yours</a>
							<a href="<?php echo esc_url( home_url( '/about/' ) ); ?>" class="btn btn-outline btn-lg" data-magnetic="0.12">View Details</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- Navigation -->
		<div class="hero-slider-nav">
			<button class="hero-nav-btn hero-nav-prev" data-magnetic="0.12" aria-label="Previous slide"><i class="fas fa-arrow-left"></i></button>
			<div class="hero-slide-counter"><span class="hero-current-slide">01</span> / <span class="hero-total-slides">03</span></div>
			<button class="hero-nav-btn hero-nav-next" data-magnetic="0.12" aria-label="Next slide"><i class="fas fa-arrow-right"></i></button>
		</div>
		<div class="hero-slider-progress"></div>
	</div>
	<!-- Hero Particles -->
	<div id="hero-particles" class="hero-particles"></div>
	<div class="scroll-indicator">
		<div class="mouse">
			<div class="wheel"></div>
		</div>
		<p>Scroll to explore</p>
	</div>
</section>

<!-- Category Selector -->
<section class="categories" id="categories">
	<div class="container">
		<div class="section-header">
			<span class="section-label" data-phantom="section_label" data-motion-text="words">Shop by Category</span>
			<h2 class="section-title" data-phantom="section_title" data-motion-text="words">Find Your Fit</h2>
		</div>
		<div class="category-grid" data-reveal-group>
			<?php
			// Display WooCommerce product categories.
			$product_categories = get_terms(
				array(
					'taxonomy'   => 'product_cat',
					'hide_empty' => true,
					'number'     => 4,
					'orderby'    => 'count',
					'order'      => 'DESC',
				)
			);

			if ( ! is_wp_error( $product_categories ) && ! empty( $product_categories ) ) {
				$index = 0;
				foreach ( $product_categories as $category ) {
					// Skip the "Uncategorized" category.
					if ( 'uncategorized' === $category->slug ) {
						continue;
					}

					$term_link = get_term_link( $category );
					$thumb_id  = get_term_meta( $category->term_id, 'thumbnail_id', true );
					$image_url = $thumb_id ? wp_get_attachment_url( $thumb_id ) : $aether . '/images/Luxury_running_sneaker_on_pedestal_202607222032.jpeg';
					$class     = 0 === $index ? 'category-card category-card--large' : 'category-card';
					?>
					<a href="<?php echo esc_url( $term_link ); ?>" class="<?php echo esc_attr( $class ); ?>" data-tilt data-reveal-item>
						<div class="category-card-bg">
							<img loading="lazy" src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $category->name ); ?>">
							<div class="category-card-overlay"></div>
						</div>
						<div class="category-card-content">
							<span class="category-count"><?php echo esc_html( $category->count ); ?> Products</span>
							<h3 class="category-name"><?php echo esc_html( $category->name ); ?></h3>
							<span class="category-cta">Shop <?php echo esc_html( $category->name ); ?> <i class="fas fa-arrow-right"></i></span>
						</div>
					</a>
					<?php
					$index++;
				}
			} else {
				// Fallback categories if no WooCommerce categories exist.
				$fallback_cats = array(
					array( 'name' => 'Men', 'count' => '24', 'image' => 'category_men' ),
					array( 'name' => 'Women', 'count' => '18', 'image' => 'category_women' ),
					array( 'name' => 'Kids', 'count' => '12', 'image' => 'category_kids' ),
					array( 'name' => 'New Arrivals', 'count' => 'Just Dropped', 'image' => 'category_new' ),
				);

				foreach ( $fallback_cats as $i => $cat ) {
					$class = 0 === $i ? 'category-card category-card--large' : 'category-card';
					?>
					<a href="<?php echo esc_url( $shop_url ); ?>" class="<?php echo esc_attr( $class ); ?>" data-tilt data-reveal-item>
						<div class="category-card-bg">
							<img loading="lazy" src="<?php echo esc_url( $aether ); ?>/images/Luxury_running_sneaker_on_pedestal_202607222032.jpeg" alt="<?php echo esc_attr( $cat['name'] ); ?>" data-phantom-alt="<?php echo esc_attr( $cat['image'] ); ?>">
							<div class="category-card-overlay"></div>
						</div>
						<div class="category-card-content">
							<span class="category-count" data-phantom="category_count"><?php echo esc_html( $cat['count'] ); ?></span>
							<h3 class="category-name"><?php echo esc_html( $cat['name'] ); ?></h3>
							<span class="category-cta">Shop <?php echo esc_html( $cat['name'] ); ?> <i class="fas fa-arrow-right"></i></span>
						</div>
					</a>
					<?php
				}
			}
			?>
		</div>
	</div>
</section>

<!-- Bestsellers -->
<section class="bestsellers" id="bestsellers">
	<div class="container">
		<div class="section-header">
			<span class="section-label" data-phantom="section_label" data-motion-text="words">Bestsellers</span>
			<h2 class="section-title" data-phantom="section_title" data-motion-text="words">Most Loved</h2>
			<p class="section-subtitle" data-phantom="section_subtitle" data-motion-text="lines">The shoes everyone's talking about. Tried, tested, and obsessed over.</p>
		</div>
		<div class="products-grid" data-phantom-products="featured" data-reveal-group>
			<?php
			// Query featured/ bestselling products.
			$products = array();

			if ( $wc_active ) {
				$products = wc_get_products(
					array(
						'limit'    => 4,
						'orderby'  => 'popularity',
						'order'    => 'DESC',
						'status'   => 'publish',
						'return'   => 'objects',
					)
				);
			}

			if ( ! empty( $products ) ) {
				foreach ( $products as $product ) {
					$badge_class = '';
					$badge_text  = '';

					if ( $product->is_on_sale() ) {
						$badge_class = 'badge-sale';
						$badge_text  = 'Sale';
					} elseif ( $product->is_featured() ) {
						$badge_class = '';
						$badge_text  = 'Bestseller';
					}

					$rating_html = '';
					if ( $product->get_rating_count() > 0 ) {
						$rating_html = '<div class="product-rating">';
						$rating      = round( $product->get_average_rating() );
						for ( $i = 1; $i <= 5; $i++ ) {
							if ( $i <= $rating ) {
								$rating_html .= '<i class="fas fa-star"></i>';
							} else {
								$rating_html .= '<i class="far fa-star"></i>';
							}
						}
						$rating_html .= '<span>(' . esc_html( $product->get_rating_count() ) . ')</span></div>';
					}
					?>
					<div class="product-card" data-tilt data-reveal-item>
						<div class="product-image" data-image-zoom>
							<?php echo wp_kses_post( $product->get_image( 'woocommerce_thumbnail' ) ); ?>
							<?php if ( $badge_text ) : ?>
								<span class="product-badge <?php echo esc_attr( $badge_class ); ?>"><?php echo esc_html( $badge_text ); ?></span>
							<?php endif; ?>
							<div class="product-actions">
								<button class="product-action-btn" aria-label="Add to wishlist"><i class="fas fa-heart"></i></button>
								<button class="product-action-btn" aria-label="Quick view"><i class="fas fa-eye"></i></button>
							</div>
						</div>
						<div class="product-info">
							<?php echo wp_kses_post( $rating_html ); ?>
							<h3 class="product-name"><?php echo esc_html( $product->get_name() ); ?></h3>
							<p class="product-tagline"><?php echo esc_html( wp_trim_words( $product->get_short_description(), 6 ) ); ?></p>
							<div class="product-price-row">
								<span class="product-price"><?php echo wp_kses_post( $product->get_price_html() ); ?></span>
								<a href="<?php echo esc_url( $product->add_to_cart_url() ); ?>" class="btn btn-sm btn-primary" data-magnetic="0.12">Add to Cart</a>
							</div>
						</div>
					</div>
					<?php
				}
			} else {
				// Fallback products if no WooCommerce products exist.
				$fallback_products = array(
					array( 'name' => 'AETHER Void Runner', 'price' => '$449', 'tagline' => 'Carbon fiber · 40mm cushion', 'badge' => 'Bestseller', 'badge_class' => '' ),
					array( 'name' => 'AETHER Cloud Stride', 'price' => '$99', 'tagline' => 'Ultra-light · Zero gravity', 'badge' => 'New', 'badge_class' => 'badge-new' ),
					array( 'name' => 'AETHER Midnight', 'price' => '$479', 'tagline' => 'Stealth black · Reflective', 'badge' => 'Limited', 'badge_class' => 'badge-limited' ),
					array( 'name' => 'AETHER Aero Sprint', 'price' => '$69', 'tagline' => 'Race-ready · Featherweight', 'badge' => '', 'badge_class' => '' ),
				);

				foreach ( $fallback_products as $product ) :
					?>
					<div class="product-card" data-tilt data-reveal-item>
						<div class="product-image" data-image-zoom>
							<img loading="lazy" src="<?php echo esc_url( $aether ); ?>/images/Luxury_running_sneaker_on_pedestal_202607222032.jpeg" alt="<?php echo esc_attr( $product['name'] ); ?>">
							<?php if ( $product['badge'] ) : ?>
								<span class="product-badge <?php echo esc_attr( $product['badge_class'] ); ?>"><?php echo esc_html( $product['badge'] ); ?></span>
							<?php endif; ?>
							<div class="product-actions">
								<button class="product-action-btn" aria-label="Add to wishlist"><i class="fas fa-heart"></i></button>
								<button class="product-action-btn" aria-label="Quick view"><i class="fas fa-eye"></i></button>
							</div>
						</div>
						<div class="product-info">
							<div class="product-rating">
								<i class="fas fa-star"></i>
								<i class="fas fa-star"></i>
								<i class="fas fa-star"></i>
								<i class="fas fa-star"></i>
								<i class="fas fa-star"></i>
								<span>(128)</span>
							</div>
							<h3 class="product-name"><?php echo esc_html( $product['name'] ); ?></h3>
							<p class="product-tagline"><?php echo esc_html( $product['tagline'] ); ?></p>
							<div class="product-price-row">
								<span class="product-price"><?php echo esc_html( $product['price'] ); ?></span>
								<a href="<?php echo esc_url( $shop_url ); ?>" class="btn btn-sm btn-primary" data-magnetic="0.12">Add to Cart</a>
							</div>
						</div>
					</div>
					<?php
				endforeach;
			}
			?>
		</div>
		<div class="section-cta">
			<a href="<?php echo esc_url( $shop_url ); ?>" class="btn btn-outline btn-lg" data-magnetic="0.12">View All Products <i class="fas fa-arrow-right"></i></a>
		</div>
	</div>
</section>

<!-- Reviews -->
<section class="reviews" id="reviews">
	<div class="container">
		<div class="section-header">
			<span class="section-label" data-phantom="section_label" data-motion-text="words">Reviews</span>
			<h2 class="section-title" data-phantom="section_title" data-motion-text="words">What Athletes Say</h2>
			<div class="reviews-summary">
				<div class="reviews-score">
					<span class="score-number" data-phantom="reviews_score">4.9</span>
					<div class="score-stars">
						<i class="fas fa-star"></i>
						<i class="fas fa-star"></i>
						<i class="fas fa-star"></i>
						<i class="fas fa-star"></i>
						<i class="fas fa-star"></i>
					</div>
					<span class="score-count">Based on 312 reviews</span>
				</div>
			</div>
		</div>
		<div class="swiper reviews-swiper">
			<div class="swiper-wrapper">
				<?php
				// Display WooCommerce reviews if available.
				if ( class_exists( 'WooCommerce' ) ) {
					$reviews = get_comments(
						array(
							'type'       => 'review',
							'status'     => 'approve',
							'number'     => 4,
							'meta_query' => array(
								array(
									'key'     => 'rating',
									'compare' => 'EXISTS',
								),
							),
						)
					);

					if ( ! empty( $reviews ) ) {
						foreach ( $reviews as $review ) {
							$product = wc_get_product( $review->comment_post_ID );
							$rating  = (int) get_comment_meta( $review->comment_ID, 'rating', true );
							$initials = mb_strtoupper( mb_substr( $review->comment_author, 0, 1 ) );
							?>
							<div class="swiper-slide">
								<div class="review-card" data-tilt>
									<div class="review-header">
										<div class="review-avatar"><?php echo esc_html( $initials ); ?></div>
										<div class="review-meta">
											<strong class="review-author"><?php echo esc_html( $review->comment_author ); ?></strong>
											<span class="review-role"><?php echo esc_html( $product ? $product->get_name() : 'Customer' ); ?></span>
										</div>
										<div class="review-verified"><i class="fas fa-check-circle"></i> Verified</div>
									</div>
									<div class="review-stars">
										<?php for ( $i = 1; $i <= 5; $i++ ) : ?>
											<i class="<?php echo $i <= $rating ? 'fas' : 'far'; ?> fa-star"></i>
										<?php endfor; ?>
									</div>
									<h4 class="review-title"><?php echo esc_html( get_the_title( $review->comment_post_ID ) ); ?></h4>
									<p class="review-text">"<?php echo esc_html( $review->comment_content ); ?>"</p>
									<span class="review-date"><?php echo esc_html( human_time_diff( strtotime( $review->comment_date_gmt ), current_time( 'timestamp' ) ) ); ?> ago</span>
								</div>
							</div>
							<?php
						}
					}
				}

				// Fallback reviews if no WooCommerce reviews exist.
				if ( ! class_exists( 'WooCommerce' ) || empty( $reviews ) ) {
					$fallback_reviews = array(
						array( 'initials' => 'MC', 'name' => 'Marcus Chen', 'role' => 'Professional Runner', 'title' => 'Game changer for marathon training', 'text' => 'The carbon fiber plate gives insane energy return. I shaved 3 minutes off my half marathon time within the first month.' ),
						array( 'initials' => 'SK', 'name' => 'Sarah Kim', 'role' => 'Sneaker Collector', 'title' => 'Premium feel from box to street', 'text' => 'The design is unlike anything I have seen. The unboxing experience alone is worth it.' ),
						array( 'initials' => 'JW', 'name' => 'James Wright', 'role' => 'Marathon Coach', 'title' => 'My athletes love these', 'text' => 'I have put all my competitive runners in AETHER. The performance difference is measurable.' ),
						array( 'initials' => 'AL', 'name' => 'Aisha Lawson', 'role' => 'Fitness Enthusiast', 'title' => 'Best running shoe I have owned', 'text' => 'From daily jogs to race day, these handle everything. The breathability is incredible.' ),
					);

					foreach ( $fallback_reviews as $review ) :
						?>
						<div class="swiper-slide">
							<div class="review-card" data-tilt>
								<div class="review-header">
									<div class="review-avatar"><?php echo esc_html( $review['initials'] ); ?></div>
									<div class="review-meta">
										<strong class="review-author"><?php echo esc_html( $review['name'] ); ?></strong>
										<span class="review-role"><?php echo esc_html( $review['role'] ); ?></span>
									</div>
									<div class="review-verified"><i class="fas fa-check-circle"></i> Verified</div>
								</div>
								<div class="review-stars">
									<i class="fas fa-star"></i>
									<i class="fas fa-star"></i>
									<i class="fas fa-star"></i>
									<i class="fas fa-star"></i>
									<i class="fas fa-star"></i>
								</div>
								<h4 class="review-title"><?php echo esc_html( $review['title'] ); ?></h4>
								<p class="review-text">"<?php echo esc_html( $review['text'] ); ?>"</p>
								<span class="review-date">2 weeks ago</span>
							</div>
						</div>
						<?php
					endforeach;
				}
				?>
			</div>
			<div class="reviews-pagination"></div>
		</div>
	</div>
</section>

<!-- FAQ -->
<section class="faq-section" id="faq">
	<div class="container">
		<div class="section-header">
			<span class="section-label" data-phantom="section_label" data-motion-text="words">FAQ</span>
			<h2 class="section-title" data-phantom="section_title" data-motion-text="words">Got Questions?</h2>
			<p class="section-subtitle" data-phantom="section_subtitle" data-motion-text="lines">Everything you need to know about us.</p>
		</div>
		<div class="faq-grid">
			<div class="faq-column">
				<div class="faq-item active">
					<button class="faq-question" aria-expanded="true">
						<span>What makes our products different?</span>
						<i class="fas fa-minus"></i>
					</button>
					<div class="faq-answer">
						<p>We combine carbon fiber plate technology with our proprietary cushioning system. Every shoe is engineered with aerospace-grade materials, delivering 40% more energy return than traditional running shoes while weighing only 280g.</p>
					</div>
				</div>
				<div class="faq-item">
					<button class="faq-question" aria-expanded="false">
						<span>What is the return policy?</span>
						<i class="fas fa-plus"></i>
					</button>
					<div class="faq-answer">
						<p>We offer a 30-day no-questions-asked return policy. If you are not satisfied, return them in original condition for a full refund.</p>
					</div>
				</div>
				<div class="faq-item">
					<button class="faq-question" aria-expanded="false">
						<span>How do I find my size?</span>
						<i class="fas fa-plus"></i>
					</button>
					<div class="faq-answer">
						<p>Each product page includes our interactive size guide. We recommend measuring your foot length and comparing it to our chart.</p>
					</div>
				</div>
			</div>
			<div class="faq-column">
				<div class="faq-item">
					<button class="faq-question" aria-expanded="false">
						<span>How long does shipping take?</span>
						<i class="fas fa-plus"></i>
					</button>
					<div class="faq-answer">
						<p>Standard shipping is free on orders over $200 and takes 3-5 business days. Express shipping is available for a fee.</p>
					</div>
				</div>
				<div class="faq-item">
					<button class="faq-question" aria-expanded="false">
						<span>Are your products sustainable?</span>
						<i class="fas fa-plus"></i>
					</button>
					<div class="faq-answer">
						<p>Sustainability is core to us. Our upper mesh uses 75% recycled materials, our packaging is 100% recyclable, and we offset 100% of carbon emissions from shipping.</p>
					</div>
				</div>
				<div class="faq-item">
					<button class="faq-question" aria-expanded="false">
						<span>Do you offer warranty?</span>
						<i class="fas fa-plus"></i>
					</button>
					<div class="faq-answer">
						<p>Every product comes with a 2-year performance warranty. If the cushioning, sole, or upper fails under normal use within 24 months, we will replace them free of charge.</p>
					</div>
				</div>
			</div>
		</div>
		<div class="faq-cta">
			<p>Still have questions?</p>
			<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="btn btn-outline" data-magnetic="0.12">Contact Us</a>
		</div>
	</div>
</section>

<?php
// AETHER footer is rendered via action hooks in inc/aether-hooks.php.
get_footer();
?>
