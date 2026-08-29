<?php
/**
 * Ferm Living 404 Page - Exact DOM from frozen source
 */
if ( ! defined( "ABSPATH" ) ) { exit; }
get_header();
?>
<main class="content" id="main-content">
  <div class="limit mx-auto px-4 tab_l:px-6 py-16">
    <div class="text-center max-w-md mx-auto">
      <h1 class="font-primary text-6xl tab_l:text-8xl font-medium leading-[1.1] mb-4">404</h1>
      <h2 class="font-primary text-2xl tab_l:text-3xl font-medium leading-[1.15] mb-6">Page Not Found</h2>
      <p class="text-black/60 mb-8">Sorry, we couldn\'t find the page you\'re looking for.</p>
      <a href="<?php echo esc_url( home_url( "/" ) ); ?>" class="font-secondary box-border inline-flex h-12 w-fit cursor-pointer items-center justify-center border border-solid border-black px-[14px] py-0 text-sm font-medium no-underline transition-all duration-300 ease-in-out hover:text-cream bg-transparent text-black hover:bg-black">Back to Home</a>
    </div>
  </div>
</main>
<?php get_footer(); ?>
