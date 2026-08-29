<?php
/**
 * Blog Listing Template — Ferm Living
 *
 * Displays the blog/stories listing with tag filters and article card grid.
 * Maps frozen source: blogs/stories.html
 *
 * @package Aureon\Designs\FermLiving
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header( 'shop' );

$page_data = apply_filters( 'aether_adapter_blog_data', array() );

$blog_title    = isset( $page_data['title'] ) ? $page_data['title'] : 'Stories';
$blog_intro    = isset( $page_data['intro'] ) ? $page_data['intro'] : '';
$current_tag   = isset( $_GET['tag'] ) ? sanitize_text_field( wp_unslash( $_GET['tag'] ) ) : '';

// Get blog categories/tags for filtering
$categories = get_categories( array(
	'orderby'    => 'name',
	'order'      => 'ASC',
	'hide_empty' => true,
) );

// Get blog posts
$args = array(
	'post_type'      => 'post',
	'posts_per_page' => 12,
	'post_status'    => 'publish',
);

if ( $current_tag ) {
	$args['tag'] = $current_tag;
}

$posts_query = new WP_Query( $args );
?>

<main class="content" id="main-content">
	<div class="headspace">

		<div class="section-title-column-text limit mb-8 mt-8 block tab_p:grid-12 tab_p:mb-20 tab_p:mt-14">
			<div class="relative col-end-[span_4]">
				<h1 class="sticky top-[100px] mb-4 mt-0 hyphens-auto break-words font-primary text-2xl text-[32px] font-medium leading-[1.15] tab_p:mb-0 tab_p:text-[80px]">
					<?php echo esc_html( $blog_title ); ?>
				</h1>
			</div>
			<div class="title-column-text__right col-start-7 col-end-[span_6] flex flex-col gap-8 tab_p:gap-10 [&_a]:underline [&_h1]:my-8 [&_strong]:font-medium">
				<div>
					<?php if ( $blog_intro ) : ?>
						<?php echo wp_kses_post( $blog_intro ); ?>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<section
			data-section-type="blog"
			data-component="blog"
		>
			<div class="blog limit">

				<?php if ( ! empty( $categories ) ) : ?>
				<div class="blog__filter pb-3.5 tab_l:pb-[18px]">
					<ul class="blog__tags">
						<li class="blog__tag text-sm text-sm-medium <?php echo ! $current_tag ? 'underline' : ''; ?>">
							<a href="<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) ); ?>">
								All stories
							</a>
						</li>
						<?php foreach ( $categories as $cat ) : ?>
						<li class="blog__tag text-sm text-sm-medium <?php echo $current_tag === $cat->slug ? 'underline' : ''; ?>">
							<a href="<?php echo esc_url( add_query_arg( 'tag', $cat->slug, get_permalink( get_option( 'page_for_posts' ) ) ) ); ?>">
								<?php echo esc_html( $cat->name ); ?>
							</a>
						</li>
						<?php endforeach; ?>
					</ul>
				</div>
				<?php endif; ?>

				<?php if ( $posts_query->have_posts() ) : ?>
				<div class="blog__posts">
					<?php while ( $posts_query->have_posts() ) :
						$posts_query->the_post();
						$categories       = get_the_category();
						$primary_category = ! empty( $categories ) ? $categories[0]->name : '';
						$excerpt          = get_the_excerpt();
					?>
					<a
						class="blog-thumb col-span-6 block"
						href="<?php the_permalink(); ?>"
					>
						<div class="blog-thumb__wrapper">
							<?php if ( $primary_category ) : ?>
							<p class="mb-4 text-[11px] uppercase leading-[1.25]">
								<?php echo esc_html( $primary_category ); ?>
							</p>
							<?php endif; ?>

							<div
								class="group relative w-full h-full aspect-[1500/2100]"
								data-component="media"
							>
								<?php if ( has_post_thumbnail() ) : ?>
								<img
									class="absolute left-0 top-0 h-full w-full object-cover"
									src="<?php echo esc_url( get_the_post_thumbnail_url( get_the_ID(), 'large' ) ); ?>"
									alt="<?php the_title_attribute(); ?>"
									loading="lazy"
								>
								<?php endif; ?>
							</div>

							<div class="blog-thumb__content pt-4 tab_l:pb-6">
								<h2 class="mb-0 mt-0 font-primary text-2xl leading-[24px] tab_l:text-[32px] tab_l:leading-[1.25]">
									<?php the_title(); ?>
								</h2>
								<?php if ( $excerpt ) : ?>
								<p class="mt-6 hidden max-w-[500px] text-sm tab_l:block tab_l:text-[13px] tab_l:leading-[18px]">
									<?php echo esc_html( wp_trim_words( $excerpt, 20 ) ); ?>
								</p>
								<?php endif; ?>
								<span class="mt-3 block underline text-black text-sm text-left">
									Read more
								</span>
							</div>
						</div>
					</a>
					<?php endwhile; ?>
				</div>
				<?php else : ?>
				<div class="blog__posts">
					<p class="col-span-12 text-center text-sm">No stories found.</p>
				</div>
				<?php endif; ?>

				<?php wp_reset_postdata(); ?>

			</div>
		</section>

	</div>
</main>

<?php get_footer( 'shop' ); ?>
