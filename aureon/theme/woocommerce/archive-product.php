<?php
/**
 * The Template for displaying product archives (shop page).
 *
 * AETHER-styled WooCommerce shop template.
 *
 * @package Aureon
 * @version 8.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header( 'shop' );

$shop_page_id = wc_get_page_id( 'shop' );
$shop_url     = get_permalink( $shop_page_id );
$aether       = get_template_directory_uri() . '/assets/aether';
?>

<section class="shop-page" id="shopPage">
	<div class="container">
		<!-- Shop Header -->
		<div class="shop-header">
			<div class="section-header">
				<span class="section-label" data-motion-text="words">Shop</span>
				<h1 class="section-title" data-motion-text="words">
					<?php
					if ( is_product_category() ) {
						single_term_title();
					} else {
						echo esc_html( 'All Products' );
					}
					?>
				</h1>
			</div>

			<?php if ( is_product_category() && term_description() ) : ?>
				<div class="shop-description">
					<?php echo wp_kses_post( term_description() ); ?>
				</div>
			<?php endif; ?>
		</div>

		<!-- Shop Toolbar -->
		<div class="shop-toolbar">
			<div class="shop-results">
				<?php
				woocommerce_result_count();
				woocommerce_catalog_ordering();
				?>
			</div>
			<div class="shop-filters-toggle">
				<button class="btn btn-outline btn-sm filter-toggle-btn" id="filterToggle">
					<i class="fas fa-sliders-h"></i> Filters
				</button>
				<div class="shop-view-toggle">
					<button class="view-btn active" data-view="grid" aria-label="Grid view"><i class="fas fa-th"></i></button>
					<button class="view-btn" data-view="list" aria-label="List view"><i class="fas fa-list"></i></button>
				</div>
			</div>
		</div>

		<!-- Product Grid -->
		<div class="shop-layout">
			<!-- Sidebar Filters -->
			<aside class="shop-sidebar" id="shopSidebar">
				<div class="filter-group">
					<h4 class="filter-heading">Categories</h4>
					<?php
					$product_categories = get_terms(
						array(
							'taxonomy'   => 'product_cat',
							'hide_empty' => true,
							'orderby'    => 'count',
							'order'      => 'DESC',
						)
					);

					if ( ! is_wp_error( $product_categories ) ) {
						foreach ( $product_categories as $cat ) {
							if ( 'uncategorized' === $cat->slug ) {
								continue;
							}
							?>
							<a href="<?php echo esc_url( get_term_link( $cat ) ); ?>" class="filter-link <?php echo is_product_category( $cat->slug ) ? 'active' : ''; ?>">
								<?php echo esc_html( $cat->name ); ?>
								<span class="filter-count">(<?php echo esc_html( $cat->count ); ?>)</span>
							</a>
							<?php
						}
					}
					?>
				</div>
				<div class="filter-group">
					<h4 class="filter-heading">Price</h4>
					<a href="<?php echo esc_url( add_query_arg( array( 'orderby' => 'price', 'order' => 'ASC' ), $shop_url ) ); ?>" class="filter-link">Low to High</a>
					<a href="<?php echo esc_url( add_query_arg( array( 'orderby' => 'price', 'order' => 'DESC' ), $shop_url ) ); ?>" class="filter-link">High to Low</a>
				</div>
			</aside>

			<!-- Products -->
			<div class="shop-main">
				<?php if ( wc_get_loop_prop( 'total' ) ) : ?>
					<div class="products-grid" data-reveal-group>
						<?php
						while ( have_posts() ) :
							the_post();
							wc_get_template_part( 'content', 'product' );
						endwhile;
						?>
					</div>

					<!-- Pagination -->
					<div class="shop-pagination">
						<?php
						woocommerce_pagination();
						?>
					</div>
				<?php else : ?>
					<div class="shop-empty">
						<i class="fas fa-box-open"></i>
						<h3>No products found</h3>
						<p>Sorry, no products match your criteria.</p>
						<a href="<?php echo esc_url( $shop_url ); ?>" class="btn btn-primary">View All Products</a>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>

<?php
get_footer( 'shop' );
