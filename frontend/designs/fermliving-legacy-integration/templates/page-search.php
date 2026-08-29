<?php
/**
 * Ferm Living Search Page Template
 *
 * Overrides search results. Renders frozen source DOM structure
 * with WooCommerce product search results.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$search_query = get_search_query();
$paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;

/* Build product search args */
$args = array(
	'post_type'      => 'product',
	's'              => $search_query,
	'posts_per_page' => 12,
	'paged'          => $paged,
	'post_status'    => 'publish',
);

if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
	$args['lang'] = ICL_LANGUAGE_CODE;
}

$search_results = new WP_Query( $args );
$total_results = $search_results->found_posts;
?>

<main class="content" id="main-content">
	<section class="headspace">
		<div class="ferm-search" data-ferm-search>
			<div class="limit">

				<h1 class="ferm-search__heading">
					<?php
					if ( $search_query ) {
						printf(
							/* translators: %s: search query */
							esc_html__( 'Search: "%s"', 'aureon' ),
							esc_html( $search_query )
						);
					} else {
						esc_html_e( 'Search', 'aureon' );
					}
					?>
				</h1>

				<!-- Search input -->
				<div class="ferm-search__input-wrap">
					<input type="search" class="ferm-search__input" value="<?php echo esc_attr( $search_query ); ?>" placeholder="<?php esc_attr_e( 'Search Ferm Living...', 'aureon' ); ?>" data-ferm-search-input>
					<svg class="ferm-search__input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
						<circle cx="11" cy="11" r="8"></circle>
						<path d="m21 21-4.3-4.3"></path>
					</svg>
				</div>

				<!-- Popular suggestions -->
				<?php if ( ! $search_query ) : ?>
					<div class="ferm-search__suggestions">
						<?php
						$suggestions = array( 'Furniture', 'Lighting', 'Accessories', 'Kids', 'Kitchen' );
						foreach ( $suggestions as $suggestion ) :
							$term = get_term_by( 'name', $suggestion, 'product_cat' );
							$url = $term ? get_term_link( $term ) : wc_get_page_permalink( 'shop' );
						?>
							<a href="<?php echo esc_url( $url ); ?>" class="ferm-search__suggestion">
								<?php echo esc_html( $suggestion ); ?>
							</a>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<?php if ( $search_query ) : ?>

					<!-- Results count -->
					<p class="ferm-search__results-count">
						<?php
						printf(
							/* translators: %d: number of results */
							_n( '%d result found', '%d results found', $total_results, 'aureon' ),
							$total_results
						);
						?>
					</p>

					<?php if ( $search_results->have_posts() ) : ?>

						<div class="ferm-search__results">
							<?php while ( $search_results->have_posts() ) : $search_results->the_post();
								global $product;
								if ( ! $product || ! $product->is_visible() ) {
									continue;
								}

								$image_url = wp_get_attachment_image_url( $product->get_image_id(), 'woocommerce_catalog' );
								$permalink = get_permalink( $product->get_id() );
							?>
								<a href="<?php echo esc_url( $permalink ); ?>" class="ferm-search__card">
									<?php if ( $image_url ) : ?>
										<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $product->get_name() ); ?>" class="ferm-search__card-image" loading="lazy">
									<?php else : ?>
										<div class="ferm-search__card-image"></div>
									<?php endif; ?>
									<p class="ferm-search__card-name"><?php echo esc_html( $product->get_name() ); ?></p>
									<p class="ferm-search__card-price"><?php echo wp_kses_post( $product->get_price_html() ); ?></p>
								</a>
							<?php endwhile; wp_reset_postdata(); ?>
						</div>

						<!-- Pagination -->
						<?php if ( $search_results->max_num_pages > 1 ) : ?>
							<nav class="ferm-pagination" aria-label="<?php esc_attr_e( 'Search results navigation', 'aureon' ); ?>" style="margin-top: 48px; text-align: center;">
								<?php
								echo paginate_links( array(
									'total'     => $search_results->max_num_pages,
									'current'   => $paged,
									'prev_text' => '&larr;',
									'next_text' => '&rarr;',
									'type'      => 'list',
								) );
								?>
							</nav>
						<?php endif; ?>

					<?php else : ?>

						<div class="ferm-search__empty">
							<h2 class="ferm-search__empty-title">
								<?php esc_html_e( 'No results found', 'aureon' ); ?>
							</h2>
							<p class="ferm-search__empty-text">
								<?php esc_html_e( 'Try searching for something else, or browse our shop.', 'aureon' ); ?>
							</p>
							<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="ferm-404__btn ferm-404__btn--primary" style="margin-top: 16px;">
								<?php esc_html_e( 'Browse shop', 'aureon' ); ?>
							</a>
						</div>

					<?php endif; ?>

				<?php endif; ?>

			</div>
		</div>
	</section>
</main>

<?php
wp_localize_script( 'ferm-commerce', 'fermSearchData', array(
	'ajax_url' => admin_url( 'admin-ajax.php' ),
	'nonce'    => wp_create_nonce( 'ferm_search_nonce' ),
	'shop_url' => wc_get_page_permalink( 'shop' ),
) );

get_footer();
