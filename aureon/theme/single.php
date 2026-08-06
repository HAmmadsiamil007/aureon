<?php
/**
 * The template for displaying single blog posts.
 *
 * AETHER-styled single post with related articles.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<section class="blog-single" id="blogSingle">
	<div class="container">
		<?php while ( have_posts() ) :
			the_post();
			$categories   = get_the_category();
			$cat_name     = ! empty( $categories ) ? $categories[0]->name : 'Blog';
			$cat_link     = ! empty( $categories ) ? get_category_link( $categories[0]->term_id ) : '#';
			$reading_time = max( 1, ceil( str_word_count( get_the_content() ) / 200 ) );
			?>

			<!-- Article Header -->
			<article class="post-article">
				<header class="post-header">
					<div class="post-meta-top">
						<a href="<?php echo esc_url( $cat_link ); ?>" class="blog-category-badge"><?php echo esc_html( $cat_name ); ?></a>
						<span class="post-date"><i class="far fa-calendar-alt"></i> <?php echo esc_html( get_the_date() ); ?></span>
						<span class="post-reading-time"><i class="far fa-clock"></i> <?php echo esc_html( $reading_time ); ?> min read</span>
					</div>
					<h1 class="post-title"><?php the_title(); ?></h1>
					<div class="post-author-info">
						<div class="post-author-avatar">
							<?php echo get_avatar( get_the_author_meta( 'ID' ), 48 ); ?>
						</div>
						<div class="post-author-details">
							<span class="post-author-name">By <?php the_author(); ?></span>
							<span class="post-author-role"><?php echo esc_html( get_the_author_meta( 'description' ) ? wp_trim_words( get_the_author_meta( 'description' ), 8 ) : 'Writer' ); ?></span>
						</div>
					</div>
				</header>

				<!-- Featured Image -->
				<?php if ( has_post_thumbnail() ) : ?>
					<div class="post-featured-image" data-reveal>
						<?php the_post_thumbnail( 'full', array( 'loading' => 'eager' ) ); ?>
					</div>
				<?php endif; ?>

				<!-- Post Content -->
				<div class="post-content" data-reveal>
					<?php
					the_content();

					wp_link_pages(
						array(
							'before' => '<div class="page-links">',
							'after'  => '</div>',
						)
					);
					?>
				</div>

				<!-- Post Tags -->
				<?php
				$tags = get_the_tags();
				if ( $tags ) :
				?>
					<div class="post-tags">
						<span class="post-tags-label">Tags:</span>
						<?php foreach ( $tags as $tag ) : ?>
							<a href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>" class="post-tag"><?php echo esc_html( $tag->name ); ?></a>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<!-- Share -->
				<div class="post-share">
					<span class="post-share-label">Share this article:</span>
					<div class="post-share-links">
						<a href="https://twitter.com/intent/tweet?url=<?php echo urlencode( get_the_permalink() ); ?>&text=<?php urlencode( get_the_title() ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Share on Twitter" class="post-share-btn"><i class="fab fa-twitter"></i></a>
						<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode( get_the_permalink() ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Share on Facebook" class="post-share-btn"><i class="fab fa-facebook-f"></i></a>
						<a href="https://www.linkedin.com/shareArticle?url=<?php echo urlencode( get_the_permalink() ); ?>&title=<?php urlencode( get_the_title() ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Share on LinkedIn" class="post-share-btn"><i class="fab fa-linkedin-in"></i></a>
					</div>
				</div>
			</article>

			<!-- Post Navigation -->
			<?php
			$prev_post = get_previous_post();
			$next_post = get_next_post();
			if ( $prev_post || $next_post ) :
			?>
				<div class="post-navigation">
					<?php if ( $prev_post ) : ?>
						<a href="<?php echo esc_url( get_permalink( $prev_post->ID ) ); ?>" class="post-nav-link post-nav-prev">
							<i class="fas fa-arrow-left"></i>
							<span class="post-nav-label">Previous Article</span>
							<span class="post-nav-title"><?php echo esc_html( $prev_post->post_title ); ?></span>
						</a>
					<?php endif; ?>
					<?php if ( $next_post ) : ?>
						<a href="<?php echo esc_url( get_permalink( $next_post->ID ) ); ?>" class="post-nav-link post-nav-next">
							<span class="post-nav-label">Next Article</span>
							<span class="post-nav-title"><?php echo esc_html( $next_post->post_title ); ?></span>
							<i class="fas fa-arrow-right"></i>
						</a>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<!-- Related Posts -->
			<?php
			$related_args = array(
				'post_type'      => get_post_type(),
				'posts_per_page' => 3,
				'post__not_in'   => array( get_the_ID() ),
				'orderby'        => 'rand',
			);

			if ( ! empty( $categories ) ) {
				$related_args['category__in'] = wp_list_pluck( $categories, 'term_id' );
			}

			$related_query = new WP_Query( $related_args );

			if ( $related_query->have_posts() ) :
			?>
				<div class="post-related">
					<div class="section-header">
						<span class="section-label" data-motion-text="words">Continue Reading</span>
						<h2 class="section-title" data-motion-text="words">Related Articles</h2>
					</div>
					<div class="blog-grid blog-grid-3" data-reveal-group>
						<?php
						while ( $related_query->have_posts() ) :
							$related_query->the_post();
							$rel_cats      = get_the_category();
							$rel_cat_name  = ! empty( $rel_cats ) ? $rel_cats[0]->name : 'Blog';
							$rel_excerpt   = wp_trim_words( get_the_excerpt(), 12 );
							?>
							<a href="<?php the_permalink(); ?>" class="blog-card" data-tilt data-reveal-item>
								<div class="blog-card-image" data-image-zoom>
									<?php if ( has_post_thumbnail() ) : ?>
										<?php the_post_thumbnail( 'large', array( 'loading' => 'lazy' ) ); ?>
									<?php else : ?>
										<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/aether/images/Luxury_running_sneaker_on_pedestal_202607222032.jpeg' ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" loading="lazy">
									<?php endif; ?>
									<span class="blog-category"><?php echo esc_html( $rel_cat_name ); ?></span>
								</div>
								<div class="blog-card-content">
									<span class="blog-date"><?php echo esc_html( get_the_date() ); ?></span>
									<h3 class="blog-card-title"><?php the_title(); ?></h3>
									<p class="blog-card-excerpt"><?php echo esc_html( $rel_excerpt ); ?></p>
								</div>
							</a>
						<?php endwhile; ?>
					</div>
				</div>
			<?php
			endif;
			wp_reset_postdata();
			?>

		<?php endwhile; ?>
	</div>
</section>

<?php
get_footer();
