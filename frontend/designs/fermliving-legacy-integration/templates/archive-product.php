<?php
/**
 * Ferm Living WooCommerce Archive Product Template
 *
 * Overrides WC's default archive-product.php with Ferm Living's frozen-source DOM.
 * Loaded via template_include filter when Ferm Living is active.
 *
 * @package Aureon\Designs\FermLiving
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header( 'shop' );

/**
 * Hook:woocommerce_before_main_content.
 */
do_action( 'woocommerce_before_main_content' );

$ferm_page_data = array();
if ( function_exists( 'ferm_get_archive_page_data' ) ) {
	$ferm_page_data = ferm_get_archive_page_data();
}

$ferm_sort_options = array(
	'menu_order' => 'Default',
	'popularity' => 'Featured',
	'price'      => 'Price Low-High',
	'price-desc' => 'Price High-Low',
	'date'       => 'Newest',
	'sales'      => 'Best Selling',
);

$ferm_current_sort = isset( $_GET['orderby'] ) ? sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) : 'menu_order';
?>
<main id="primary" class="ferm-archive-main">
	<div class="ferm-archive-container">

		<?php /* Collection header */ ?>
		<div class="ferm-collection-header">
			<h1><?php echo esc_html( woocommerce_page_title() ); ?></h1>
			<?php if ( ! empty( $ferm_page_data['description'] ) ) : ?>
				<p class="ferm-collection-count"><?php echo esc_html( $ferm_page_data['description'] ); ?></p>
			<?php endif; ?>
		</div>

		<?php /* Toolbar — Sort + Filter */ ?>
		<div class="ferm-toolbar" role="toolbar" aria-label="Product filters and sort">
			<div class="ferm-toolbar-filters" role="group" aria-label="Category filters">
				<a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>"
				   class="ferm-toolbar-filter-btn is-active"
				   data-filter-url="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>"
				   role="button">All</a>
				<?php
				$ferm_categories = array();
				if ( function_exists( 'wc_get_product_categories' ) ) {
					$ferm_categories = get_terms( array(
						'taxonomy'   => 'product_cat',
						'hide_empty' => true,
						'parent'     => 0,
					) );
				}
				if ( ! is_wp_error( $ferm_categories ) && ! empty( $ferm_categories ) ) :
					foreach ( $ferm_categories as $cat ) :
						if ( 'uncategorized' === $cat->slug ) {
							continue;
						}
						$cat_url = get_term_link( $cat );
						if ( is_wp_error( $cat_url ) ) {
							continue;
						}
						?>
						<a href="<?php echo esc_url( $cat_url ); ?>"
						   class="ferm-toolbar-filter-btn"
						   data-filter-url="<?php echo esc_url( $cat_url ); ?>"
						   role="button"><?php echo esc_html( $cat->name ); ?></a>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>

			<div class="ferm-toolbar-sort">
				<select id="ferm-sort-select"
						data-sort-by
						aria-label="Sort products"
						onchange="window.location.href=this.value">
					<?php foreach ( $ferm_sort_options as $key => $label ) :
						$sort_url = add_query_arg( 'orderby', $key, wc_get_page_permalink( 'shop' ) );
						?>
						<option value="<?php echo esc_url( $sort_url ); ?>"
							<?php selected( $ferm_current_sort, $key ); ?>><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>

		<?php /* Product grid */ ?>
		<div class="ferm-product-grid"
			 data-component="productGrid"
			 data-page="<?php echo esc_attr( get_query_var( 'paged', 1 ) ); ?>"
				data-total-pages="<?php echo esc_attr( wc_get_loop_prop( 'max_num_pages', 1 ) ); ?>"
			 data-archive-url="<?php echo esc_url( woocommerce_get_loop_url() ); ?>">

			<?php
			if ( woocommerce_product_loop() ) :
				do_action( 'woocommerce_before_shop_loop' );

				woocommerce_product_loop_start();

				if ( wc_get_loop_prop( 'total' ) ) :
					while ( have_posts() ) :
						the_post();
						wc_get_template_part( 'content', 'product' );
					endwhile;
				else :
					?>
					<div class="ferm-no-products">
						<p><?php esc_html_e( 'No products were found matching your selection.', 'aureon' ); ?></p>
					</div>
					<?php
				endif;

				woocommerce_product_loop_end();

				do_action( 'woocommerce_after_shop_loop' );
			else :
				?>
				<div class="ferm-no-products">
					<p><?php esc_html_e( 'No products found.', 'aureon' ); ?></p>
				</div>
				<?php
			endif;
			?>
		</div>

		<?php /* Pagination */ ?>
		<?php
		$ferm_max_pages = wc_get_loop_prop( 'max_num_pages', 1 );
		if ( $ferm_max_pages > 1 ) :
			$ferm_current_page = max( 1, get_query_var( 'paged', 1 ) );
			$ferm_base_url    = woocommerce_get_loop_url();
			?>
			<nav class="ferm-pagination" aria-label="Product pages">
				<?php
				/* Previous */
				if ( $ferm_current_page > 1 ) :
					$prev_url = add_query_arg( 'paged', $ferm_current_page - 1, $ferm_base_url );
					?>
					<a href="<?php echo esc_url( $prev_url ); ?>" aria-label="Previous page">&laquo;</a>
				<?php endif; ?>

				<?php
				/* Page numbers */
				for ( $i = 1; $i <= $ferm_max_pages; $i++ ) :
					if ( $i === $ferm_current_page ) :
						?>
						<span class="current" aria-current="page"><?php echo esc_html( $i ); ?></span>
					<?php elseif ( abs( $i - $ferm_current_page ) <= 2 || 1 === $i || $ferm_max_pages === $i ) :
						$page_url = add_query_arg( 'paged', $i, $ferm_base_url );
						?>
						<a href="<?php echo esc_url( $page_url ); ?>"><?php echo esc_html( $i ); ?></a>
					<?php elseif ( abs( $i - $ferm_current_page ) === 3 ) :
						?>
						<span class="dots">...</span>
					<?php endif;
				endfor; ?>

				<?php
				/* Next */
				if ( $ferm_current_page < $ferm_max_pages ) :
					$next_url = add_query_arg( 'paged', $ferm_current_page + 1, $ferm_base_url );
					?>
					<a href="<?php echo esc_url( $next_url ); ?>" aria-label="Next page">&raquo;</a>
				<?php endif; ?>
			</nav>
		<?php endif; ?>

	</div>
</main>

<?php
do_action( 'woocommerce_after_main_content' );
do_action( 'woocommerce_sidebar' );

get_footer( 'shop' );
