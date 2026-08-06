<?php
/**
 * Template Name: AETHER Blog
 * Template Post Type: page
 *
 * AETHER-styled blog grid layout.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<section class="blog-page" id="blogPage">
	<div class="container">
		<div class="section-header">
			<span class="section-label" data-motion-text="words">Blog</span>
			<h1 class="section-title" data-motion-text="words">
				<?php
				if ( is_home() && ! is_front_page() ) {
					single_post_title();
				} elseif ( is_category() ) {
					single_term_title( 'Category: ' );
				} elseif ( is_tag() ) {
					single_term_title( 'Tag: ' );
				} elseif ( is_search() ) {
					echo 'Search Results';
				} else {
					echo 'Latest Articles';
				}
				?>
			</h1>
		</div>

		<?php if ( is_search() ) : ?>
			<p class="blog-search-info">Showing results for: <strong><?php echo esc_html( get_search_query() ); ?></strong></p>
		<?php endif; ?>

		<?php if ( have_posts() ) : ?>
			<div class="blog-grid" data-reveal-group>
				<?php
				while ( have_posts() ) :
					the_post();
					$categories    = get_the_category();
					$cat_name      = ! empty( $categories ) ? $categories[0]->name : 'Blog';
					$excerpt       = wp_trim_words( get_the_excerpt(), 18 );
					$reading_time  = max( 1, ceil( str_word_count( get_the_content() ) / 200 ) );
					?>
					<a href="<?php the_permalink(); ?>" class="blog-card" data-tilt data-reveal-item>
						<div class="blog-card-image" data-image-zoom>
							<?php if ( has_post_thumbnail() ) : ?>
								<?php the_post_thumbnail( 'large', array( 'loading' => 'lazy' ) ); ?>
							<?php else : ?>
								<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/aether/images/Luxury_running_sneaker_on_pedestal_202607222032.jpeg' ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" loading="lazy">
							<?php endif; ?>
							<span class="blog-category"><?php echo esc_html( $cat_name ); ?></span>
						</div>
						<div class="blog-card-content">
							<span class="blog-date"><i class="far fa-calendar-alt"></i> <?php echo esc_html( get_the_date() ); ?> <span class="blog-sep">&middot;</span> <?php echo esc_html( $reading_time ); ?> min read</span>
							<h3 class="blog-card-title"><?php the_title(); ?></h3>
							<p class="blog-card-excerpt"><?php echo esc_html( $excerpt ); ?></p>
							<span class="blog-card-link">Read More <i class="fas fa-arrow-right"></i></span>
						</div>
					</a>
				<?php endwhile; ?>
			</div>

			<!-- Pagination -->
			<div class="blog-pagination">
				<?php
				the_posts_pagination(
					array(
						'mid_size'  => 2,
						'prev_text' => '<i class="fas fa-chevron-left"></i>',
						'next_text' => '<i class="fas fa-chevron-right"></i>',
					)
				);
				?>
			</div>
		<?php else : ?>
			<div class="blog-empty">
				<i class="fas fa-pen-nib"></i>
				<h3>No articles found</h3>
				<p>Check back soon for new content.</p>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn-primary">Back to Home</a>
			</div>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer();
