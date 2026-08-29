<?php
/**
 * Ferm Living Search Page - Exact DOM from frozen source
 */
if ( ! defined( "ABSPATH" ) ) { exit; }
get_header();
$search_data = apply_filters( "aether_adapter_search_data", [] );
$search_query = get_search_query();
?>
<main class="content" id="main-content">
  <div class="limit mx-auto px-4 tab_l:px-6 py-8">
    <header class="mb-12">
      <h1 class="font-primary text-3xl tab_l:text-4xl font-medium leading-[1.15]">Search Results</h1>
      <p class="mt-4 text-black/60">Showing results for: <strong><?php echo esc_html( $search_query ); ?></strong></p>
    </header>
    <form role="search" method="get" class="mb-8" data-component="searchOverlay">
      <div class="relative">
        <input type="search" name="s" value="<?php echo esc_attr( $search_query ); ?>" placeholder="<?php echo esc_attr( $search_data["placeholder"] ?? "Search Ferm Living..." ); ?>" class="w-full h-12 px-4 border border-black/20 text-black placeholder-black/40 focus:outline-none focus:border-black" aria-label="Search">
        <button type="submit" class="absolute right-4 top-1/2 -translate-y-1/2 text-black/60 hover:text-black"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.25"><circle cx="11" cy="11" r="7"/><path d="M21 21L16.65 16.65"/></svg></button>
      </div>
    </form>
    <?php if ( have_posts() ) : ?>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8" data-component="searchResults">
      <?php while ( have_posts() ) : the_post(); if ( get_post_type() === "product" ) : include aether_active_design_dir() . "components/cards/product.php"; else : ?>
      <article class="search-result-card">
        <a href="<?php the_permalink(); ?>" class="block no-underline">
          <h3 class="font-medium mb-1"><?php the_title(); ?></h3>
          <p class="text-sm text-black/60"><?php the_excerpt(); ?></p>
        </a>
      </article>
      <?php endif; endwhile; ?>
    </div>
    <nav class="mt-12 flex justify-center gap-2" data-component="pagination"><?php echo paginate_links(); ?></nav>
    <?php else : ?>
    <div class="text-center py-16"><p class="text-black/60">No results found for <strong><?php echo esc_html( $search_query ); ?></strong></p></div>
    <?php endif; ?>
  </div>
</main>
<?php get_footer(); ?>
