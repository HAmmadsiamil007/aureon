<?php
/**
 * Ferm Living Newsletter Section - Exact DOM from frozen source
 */
if ( ! defined( "ABSPATH" ) ) { exit; }
$newsletter = $data["newsletter"] ?? [];
$heading = $newsletter["heading"] ?? "Ferm Living news";
$text = $newsletter["text"] ?? "Get exclusive drops, early access, and Ferm Living news.";
?>
<section class="section py-16 tab_l:py-24 bg-canvas" data-component="newsletter">
  <div class="limit mx-auto px-4 tab_l:px-6">
    <div class="grid-12">
      <div class="col-span-12 max-w-2xl mx-auto text-center">
        <h2 class="font-primary text-3xl tab_l:text-4xl font-medium leading-[1.15] mb-4"><?php echo esc_html( $heading ); ?></h2>
        <p class="text-black/60 mb-8"><?php echo esc_html( $text ); ?></p>
        <form class="flex flex-col gap-4 md:flex-row md:items-center justify-center" action="#" method="post" data-klaviyo-form="UDJeJw">
          <input type="email" name="email" placeholder="Enter your email" required class="flex-1 h-12 px-4 border border-black/20 text-black placeholder-black/40 focus:outline-none focus:border-black transition-colors">
          <button type="submit" class="font-secondary box-border flex h-12 w-fit cursor-pointer items-center justify-center border border-solid border-black px-[14px] py-0 text-sm font-medium no-underline transition-all duration-300 ease-in-out hover:text-cream bg-transparent text-black hover:bg-black">Sign Up</button>
        </form>
      </div>
    </div>
  </div>
</section>
