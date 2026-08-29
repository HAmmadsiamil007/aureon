<?php
/**
 * Ferm Living Hero Section - Exact DOM from frozen source
 */
if ( ! defined( "ABSPATH" ) ) { exit; }
$slides = $data["slides"] ?? [];
if ( empty( $slides ) ) { return; }
?>
<section class="section slider-with-images !py-6 tab_p:!py-4 tab_l:!py-6" data-component="heroSlider">
  <div data-component="emblaSlider" class="embla">
    <div class="embla__container flex">
      <?php foreach ( $slides as $index => $slide ) : $img_mobile = $slide["image_mobile"] ?? ""; $img_desktop = $slide["image_desktop"] ?? ""; $headline = $slide["headline"] ?? ""; $cta_text = $slide["cta_text"] ?? ""; $cta_url = $slide["cta_url"] ?? "#"; ?>
      <div class="embla__slide flex-shrink-0 w-full relative">
        <div class="relative h-screen w-full px-0 pt-0 first:pt-0 pt-0" data-hero-critical>
          <div class="relative h-full w-full">
            <div class="h-full">
              <div class="grid grid-cols-1 grid-rows-2 md:grid-cols-2 md:grid-rows-1 h-full">
                <div class="relative flex items-center justify-center h-full">
                  <a href="<?php echo esc_url( $cta_url ); ?>" class="absolute inset-0 block h-full w-full no-underline z-10" aria-label="<?php echo esc_attr( $headline ); ?>"></a>
                  <?php if ( $img_mobile ) : ?>
                  <img src="<?php echo esc_url( $img_mobile ); ?>" alt="<?php echo esc_attr( $headline ); ?>" loading="lazy" class="absolute left-0 top-0 hidden h-full w-full object-cover md:block w-full h-full object-cover">
                  <?php endif; ?>
                  <?php if ( $img_desktop ) : ?>
                  <img src="<?php echo esc_url( $img_desktop ); ?>" alt="<?php echo esc_attr( $headline ); ?>" loading="lazy" class="absolute left-0 top-0 h-full w-full object-cover md:hidden w-full h-full object-cover">
                  <?php endif; ?>
                  <div class="absolute z-[1] w-full p-4 tab_l:p-6 self-end pointer-events-none">
                    <div class="hidden tab_p:block font-primary [&_*]:tab_l:text-[48px] text-cream [&_*]:leading-[1.15] [&_a]:underline text-left"><p><?php echo esc_html( $headline ); ?></p></div>
                    <div class="block tab_p:hidden font-primary [&_*]:text-[32px] text-cream [&_*]:leading-[1.15] [&_a]:underline text-left"><p><?php echo esc_html( $headline ); ?></p></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="embla__controls justify-items-end pb-5 grid-cols-1">
      <div class="embla__buttons">
        <button class="embla__button embla__button--prev !h-4 !w-4" type="button" aria-label="Previous slide"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
        <button class="embla__button embla__button--next !h-4 !w-4" type="button" aria-label="Next slide"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
      </div>
    </div>
  </div>
</section>
