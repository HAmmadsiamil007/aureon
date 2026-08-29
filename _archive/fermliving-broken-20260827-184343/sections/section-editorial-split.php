<?php
/**
 * Ferm Living Editorial Split Section - Exact DOM from frozen source
 */
if ( ! defined( "ABSPATH" ) ) { exit; }
$editorial = $data["editorial"] ?? [];
if ( empty( $editorial ) ) { return; }
?>
<section class="section py-16 tab_l:py-24" data-component="editorialSplit">
  <div class="limit mx-auto px-4 tab_l:px-6">
    <div class="grid-12">
      <?php foreach ( $editorial as $item ) : $image = $item["image"] ?? ""; $alt = $item["alt"] ?? ""; $headline = $item["headline"] ?? ""; $body = $item["body"] ?? ""; $cta_text = $item["cta_text"] ?? ""; $cta_url = $item["cta_url"] ?? "#"; $reverse = ! empty( $item["reverse"] ); ?>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mb-16 <?php echo $reverse ? "md:flex-row-reverse" : ""; ?>" data-component="mediaText">
        <div class="relative aspect-[4/5] overflow-hidden">
          <?php if ( $image ) : ?>
          <img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $alt ); ?>" loading="lazy" class="absolute left-0 top-0 h-full w-full object-cover">
          <?php endif; ?>
        </div>
        <div class="flex flex-col justify-center p-4 md:p-10">
          <div class="max-w-[400px] mx-auto md:mx-0">
            <h2 class="font-primary text-3xl tab_l:text-4xl font-medium leading-[1.15] mb-4"><?php echo esc_html( $headline ); ?></h2>
            <?php if ( $body ) : ?><div class="prose prose-sm mb-6"><?php echo wp_kses_post( $body ); ?></div><?php endif; ?>
            <?php if ( $cta_text && $cta_url ) : ?><a href="<?php echo esc_url( $cta_url ); ?>" class="font-secondary box-border inline-flex h-12 w-fit cursor-pointer items-center justify-center border border-solid border-black px-[14px] py-0 text-sm font-medium no-underline transition-all duration-300 ease-in-out hover:text-cream bg-transparent text-black hover:bg-black"><?php echo esc_html( $cta_text ); ?></a><?php endif; ?>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
