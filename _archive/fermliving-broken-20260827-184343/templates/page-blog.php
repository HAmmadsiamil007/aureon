<?php
/**
 * Ferm Living Blog Listing - Exact DOM from frozen source
 */
if ( ! defined( "ABSPATH" ) ) { exit; }
get_header();
?>
<main class="content" id="main-content">
  <div class="limit mx-auto px-4 tab_l:px-6 py-8">
    <header class="mb-12">
      <h1 class="font-primary text-3xl tab_l:text-4xl font-medium leading-[1.15]">Stories</h1>
      <p class="mt-4 text-black/60">From the Ferm Living Journal</p>
    </header>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8" data-component="blogGrid">
      <?php $query = new WP_Query( [ "post_type" => "post", "posts_per_page" => 9, "paged" => get_query_var( "paged" ) ] ); if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post(); ?>
      <article class="blog-card group">
        <a href="<?php the_permalink(); ?>" class="absolute inset-0 block no-underline z-10" aria-label="<?php the_title_attribute(); ?>"></a>
        <div class="relative aspect-[4/5] overflow-hidden mb-4">
          <?php if ( has_post_thumbnail() ) : the_post_thumbnail( "medium_large", [ "class" => "absolute left-0 top-0 h-full w-full object-cover transition-transform duration-500 group-hover:scale-105", "loading" => "lazy" ] ); endif; ?>
        </div>
        <div class="text-left">
          <time class="text-sm text-black/60 mb-1 block"><?php the_date(); ?></time>
          <h3 class="font-primary text-xl font-medium leading-[1.15]"><?php the_title(); ?></h3>
        </div>
      </article>
      <?php endwhile; endif; wp_reset_postdata(); ?>
    </div>
    <?php if ( $query->max_num_pages > 1 ) : ?>
    <nav class="mt-12 flex justify-center gap-2" data-component="pagination">
      <?php echo paginate_links( [ "prev_text" => "<svg viewBox=\"0 0 24 24\"><path d=\"M15 18L9 12L15 6\"/></svg>", "next_text" => "<svg viewBox=\"0 0 24 24\"><path d=\"M9 18L15 12L9 6\"/></svg>", "type" => "list" ] ); ?>
    </nav>
    <?php endif; ?>
  </div>
</main>
<?php get_footer(); ?>
