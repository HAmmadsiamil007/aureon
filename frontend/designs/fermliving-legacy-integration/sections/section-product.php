<?php
/**
 * Ferm Living — Product Section (Thin Bridge)
 *
 * Renders the FROZEN Ferm Living product DOM verbatim.
 * Data flows: WC → adapter → FermPageData JSON → frozen DOM → frozen JS.
 *
 * This file OVERRIDES the engine's section-product.php when the
 * fermliving design pack is active. It does NOT use aether_render_component()
 * for product sub-components — it outputs the exact DOM from the frozen source.
 *
 * @package Aureon\Designs\FermLiving
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

aether_register_section( 'product', array(
	'template' => 'sections/section-product.php',
	'adapter'  => 'adapter-product.php',
	'behavior' => array( 'parallax-section' => true ),
) );

if ( ! isset( $sectionData ) || ! is_array( $sectionData ) ) {
	return; // Registration only — render happens via aether_render_section().
}

/* If no adapter data, bail — don't render empty product page */
if ( empty( $sectionData ) ) {
	return;
}

/* ── Build FermPageData ──────────────────────────────────────── */
$ferm_data = ferm_build_product_page_data( $sectionData );

/* ── Output FermPageData as JSON (before DOM) ────────────────── */
echo "\n<script>window.FermPageData = " . wp_json_encode( $ferm_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . ";</script>\n";

/* ── Frozen Ferm Product DOM ──────────────────────────────────── */
$is_mto    = ! empty( $ferm_data['is_mto'] );
$cta_state = $is_mto ? 'mto' : 'default';
$cta_text  = $is_mto ? 'Order' : 'Add to Cart';
$badge     = isset( $ferm_data['badge'] ) ? $ferm_data['badge'] : '';
?>
<div class='headspace'>
  <section
    data-section-id='product'
    data-section-type='product'
    data-component='productPage'
  >
    <button
      data-back-button
      class='absolute z-10 ml-4 mt-6 inline-flex cursor-pointer items-center border-b border-transparent pb-[5px] text-sm font-medium text-black transition-colors duration-200 ease-in-out'
      role='button'
    >
      <span class='flex h-4 items-center'>
        <svg width='13' height='8' viewBox='0 0 13 8' fill='none' xmlns='http://www.w3.org/2000/svg' class='rotate-180 w-[29px] h-2.5 mr-1.5'>
          <path d="M0 4H12M12 4C11 3.83333 8 2 8 0M12 4C11 4.16667 8 6 8 8" stroke="currentColor"/>
        </svg>
      </span>
      <span class='text-sm leading-4'>Back</span>
    </button>

    <div class='md:min-h-full-screen mb-12 grid gap-6 tab_p:mb-24 md:[grid-template-columns:repeat(12,minmax(0,1fr))]'>

      <?php /* ── LEFT: Gallery ─────────────────────────────────── */ ?>
      <div class='relative col-span-6'>
        <?php if ( $badge ) : ?>
        <div class='absolute right-0 top-1 z-10 mr-1.5 flex flex-row flex-wrap items-center gap-1.5 p-3.5'>
          <div class='bg-cream flex items-center justify-center px-1.5 py-[9px] transition-all duration-300 text-xxs leading-[1] uppercase text-black group-hover:bg-[#f7f5ef26] group-hover:text-cream'><?php echo esc_html( $badge ); ?></div>
        </div>
        <?php endif; ?>

        <div
          data-component='productImages'
          data-product-id='<?php echo esc_attr( $ferm_data['id'] ); ?>'
          class='embla w-full overflow-hidden md:overflow-visible'
          data-variant-images=''
        >
          <div class='embla__container flex md:flex-col'>
            <?php foreach ( $ferm_data['gallery'] as $i => $img ) :
              $src    = esc_url( $img['src'] );
              $alt    = esc_attr( $img['alt'] );
              $first  = 0 === $i;
              $hidden = $first ? '' : 'hidden';
            ?>
            <div
              class='embla__slide basis-full w-full h-auto max-h-[calc(60vh-116px)] aspect-[0.75] md:max-h-full-screen object-contain flex flex-shrink-0<?php echo $first ? '' : ''; ?>'
              data-media
              <?php echo $first ? 'data-featured-image-container' : ''; ?>
            >
              <img
                src='<?php echo $src; ?>'
                alt='<?php echo $alt; ?>'
                width='1000'
                height='750'
                class='w-full h-auto max-h-[calc(60vh-116px)] aspect-[0.75] md:max-h-full-screen object-contain flex flex-shrink-0 <?php echo $hidden; ?>'
                <?php echo $first ? '' : 'loading="lazy"'; ?>
                style='aspect-ratio: 1.3333333333333333'
              >
            </div>
            <?php endforeach; ?>
          </div>

          <?php /* Desktop pagination bullets */ ?>
          <?php if ( count( $ferm_data['gallery'] ) > 1 ) : ?>
          <div class='absolute left-0 top-0 ml-6 h-[calc(100%-50vh)] min-h-screen w-5'>
            <div
              data-desktop-pagination
              class='sticky left-0 top-[50vh] z-10 hidden flex-col items-center justify-between gap-1 md:flex'
            >
              <?php foreach ( $ferm_data['gallery'] as $i => $img ) : ?>
              <div data-bullet class='h-1 w-1 shrink-0 cursor-pointer rounded-full bg-black <?php echo 0 === $i ? 'opacity-40' : 'opacity-40'; ?>'></div>
              <?php endforeach; ?>
            </div>
          </div>
          <?php endif; ?>
        </div>
      </div>

      <?php /* ── RIGHT: Product Info ───────────────────────────── */ ?>
      <div
        data-product-info
        data-component='variantInfo'
        data-product-id='<?php echo esc_attr( $ferm_data['id'] ); ?>'
        class='relative col-span-6 md:max-w-[448px] px-4 md:px-0'
      >
        <div class='sticky right-0 top-0 md:mt-[26px]'>

          <?php /* Tagline */ ?>
          <?php if ( ! empty( $ferm_data['tagline'] ) ) : ?>
          <div class='mb-2 w-fit text-xxs uppercase text-black/5'>
            <span class='text-black'><?php echo esc_html( $ferm_data['tagline'] ); ?></span>
          </div>
          <?php endif; ?>

          <?php /* Title */ ?>
          <div class='flex w-full justify-between'>
            <h1 class='m-0 font-secondary text-2xl font-normal leading-10'><?php echo esc_html( $ferm_data['title'] ); ?></h1>
          </div>

          <?php /* Price */ ?>
          <div>
            <div class='mt-2 flex'>
              <div
                data-component='price'
                data-product-id='<?php echo esc_attr( $ferm_data['id'] ); ?>'
                class='text-sm font-medium text-black'
              >
                <?php echo wp_kses_post( $ferm_data['price_html'] ); ?>
              </div>
            </div>
          </div>

          <?php /* Add to Cart widget */ ?>
          <div
            class='md:w-unset flex w-full max-w-full flex-col items-start justify-end text-sm mt-6 product-ctas'
            data-component='addToCart'
            data-cart-template=''
            data-loading-text='Adding ...'
            data-product-title='<?php echo esc_attr( $ferm_data['title'] ); ?>'
            data-product-price='<?php echo esc_attr( $ferm_data['price_cents'] ); ?>'
            data-shop-currency='<?php echo esc_attr( $ferm_data['currency'] ); ?>'
            data-variant-id='<?php echo esc_attr( $ferm_data['variant_id'] ); ?>'
            data-is-mto-product='<?php echo $is_mto ? 'true' : 'false'; ?>'
            data-has-mto-tag='<?php echo $is_mto ? 'true' : 'false'; ?>'
            data-do-not-open-drawer=''
            data-cta-state='<?php echo esc_attr( $cta_state ); ?>'
          >

            <?php /* Color swatches */ ?>
            <?php if ( ! empty( $ferm_data['colors'] ) ) : ?>
            <div class='mb-2 flex w-full items-start justify-between gap-3'>
              <div class='flex items-center mb-2.5 gap-0.5'>
                <div class='flex items-center flex-wrap gap-x-0.5 gap-y-2 max-w-[10.875rem] md:max-w-[13.625rem]'>
                  <?php foreach ( $ferm_data['colors'] as $ci => $color ) :
                    $is_current = ( $ci === 0 );
                    $outline    = $is_current ? 'border-transparent outline outline-1 border-2 outline-black' : 'border border-black-05';
                    $href       = ! empty( $color['url'] ) ? esc_url( $color['url'] ) : '#';
                  ?>
                  <a
                    href='<?php echo $href; ?>'
                    class='relative rotate-45 cursor-pointer overflow-hidden rounded-full p-0 h-5 w-5 <?php echo esc_attr( $outline ); ?>'
                    data-color-handle='<?php echo esc_attr( $color['handle'] ?? '' ); ?>'
                    title='<?php echo esc_attr( $color['name'] ); ?>'
                    data-hex='<?php echo esc_attr( $color['hex'] ); ?>'
                    style='order: <?php echo (int) ( $ci + 1 ); ?>'
                  >
                    <div class='absolute h-full w-full' style='background-color: <?php echo esc_attr( $color['hex'] ); ?>'></div>
                  </a>
                  <?php endforeach; ?>
                </div>
              </div>
              <div class='text-right text-sm'><?php echo esc_html( $ferm_data['color_name'] ); ?></div>
            </div>
            <?php endif; ?>

            <?php /* Quantity stepper */ ?>
            <div class='mb-2 mt-2 inline-flex h-12 w-full items-center justify-between border border-black/5 px-5 text-[13px] tracking-[0.52px]' data-quantity-container>
              <button type='button' class='appearance-none border-0 bg-transparent cursor-pointer text-[25px] font-normal flex items-center justify-center self-stretch px-2.5 -mx-2.5' data-decrease-quantity aria-label='Decrease quantity' title='Decrease quantity'>–</button>
              <input
                class='w-16 border-none bg-transparent text-center text-sm outline-none [-moz-appearance:textfield]'
                data-quantity
                type='number'
                value='1'
                aria-label='Quantity'
              >
              <button type='button' class='appearance-none border-0 bg-transparent cursor-pointer text-[25px] font-normal flex items-center justify-center self-stretch px-2.5 -mx-2.5' data-increase-quantity aria-label='Increase quantity' title='Increase quantity'>+</button>
            </div>

            <?php /* CTA buttons */ ?>
            <div class="inline-flex w-full flex-row items-center justify-between">
              <button
                class='font-secondary box-border flex h-12 w-fit max-w-full cursor-pointer items-center justify-center border border-solid border-black px-[14px] py-0 text-sm font-medium no-underline transition-all duration-300 ease-in-out text-cream bg-black hover:bg-transparent hover:text-black disabled:cursor-not-allowed disabled:opacity-40 w-full hidden'
                data-button-notify-me
                data-variant-id='<?php echo esc_attr( $ferm_data['variant_id'] ); ?>'
              >Notify me</button>

              <button
                class='font-secondary box-border flex h-12 w-fit max-w-full cursor-pointer items-center justify-center border border-solid border-black px-[14px] py-0 text-sm font-medium no-underline transition-all duration-300 ease-in-out hover:text-cream bg-transparent text-black hover:bg-black disabled:cursor-not-allowed disabled:opacity-40 w-full hidden'
                disabled
                data-button-sold-out
              >Sold Out</button>

              <button
                class='font-secondary box-border flex h-12 w-fit max-w-full cursor-pointer items-center justify-center border border-solid border-black px-[14px] py-0 text-sm font-medium no-underline transition-all duration-300 ease-in-out text-cream bg-black hover:bg-transparent hover:text-black disabled:cursor-not-allowed disabled:opacity-40 w-full'
                aria-label='<?php echo esc_attr( $cta_text . ' - ' . $ferm_data['title'] ); ?>'
                data-button-add-to-cart
                data-add-text="Add to Cart"
                data-mto-text="Order"
                data-select-text="Select size"
                data-prefix=""
              ><?php echo esc_html( $cta_text ); ?></button>

              <?php /* Wishlist button (stub — Swym disabled) */ ?>
              <div class='swym-wishlist-button-wrapper' data-component='swymWishlistButton'>
                <button
                  type='button'
                  data-wishlist-button
                  data-product-id='<?php echo esc_attr( $ferm_data['id'] ); ?>'
                  data-variant-id='<?php echo esc_attr( $ferm_data['variant_id'] ); ?>'
                  class='flex items-center justify-center w-10 h-10 transition-all duration-200 group relative h-12 w-12 flex-shrink-0 flex items-center justify-center border border-black/5'
                  aria-label='Add to wishlist'
                >
                  <div data-unfilled-icon class='w-6 h-6 transition-opacity group-hover:scale-110 transition-transform duration-200'>
                    <svg class='' width='24' height='24' viewBox='0 0 24 24' fill='none' xmlns='http://www.w3.org/2000/svg'>
                      <path d="M11.226 6.87717L11.961 7.79592C11.981 7.82094 12.019 7.82094 12.039 7.79592L12.774 6.87717C13.4699 6.00733 14.5235 5.50098 15.6374 5.50098C16.6099 5.50098 17.5426 5.88731 18.2302 6.57498L18.3281 6.67285C19.0785 7.42319 19.5 8.44087 19.5 9.50201V9.86553C19.5 10.3899 19.4155 10.9108 19.2496 11.4083L19.0534 11.9971C18.7287 12.9712 18.2342 13.8801 17.5928 14.6818L17.4072 14.9138C16.9289 15.5118 16.3858 16.0549 15.7878 16.5332L15.6832 16.6169C14.7951 17.3274 13.8071 17.9031 12.7511 18.3255C12.2689 18.5184 11.7311 18.5184 11.2489 18.3255C10.1929 17.9031 9.20492 17.3274 8.31681 16.6169L8.21216 16.5332C7.61423 16.0549 7.07112 15.5118 6.59277 14.9138L6.40719 14.6818C5.76579 13.8801 5.27132 12.9712 4.94663 11.9971L4.75036 11.4083C4.58454 10.9108 4.5 10.3899 4.5 9.86553V9.50201C4.5 8.44087 4.92154 7.42319 5.67187 6.67285L5.76975 6.57498C6.45742 5.88731 7.3901 5.50098 8.36261 5.50098C9.47655 5.50098 10.5301 6.00733 11.226 6.87717Z" stroke="currentColor" stroke-width="1.25"/>
                    </svg>
                  </div>
                  <div data-filled-icon class='hidden w-6 h-6 transition-opacity group-hover:scale-110 transition-transform duration-200'>
                    <svg class='' width='24' height='24' viewBox='0 0 24 24' fill='currentColor' xmlns='http://www.w3.org/2000/svg'>
                      <path d="M11.226 6.87717L11.961 7.79592C11.981 7.82094 12.019 7.82094 12.039 7.79592L12.774 6.87717C13.4699 6.00733 14.5235 5.50098 15.6374 5.50098C16.6099 5.50098 17.5426 5.88731 18.2302 6.57498L18.3281 6.67285C19.0785 7.42319 19.5 8.44087 19.5 9.50201V9.86553C19.5 10.3899 19.4155 10.9108 19.2496 11.4083L19.0534 11.9971C18.7287 12.9712 18.2342 13.8801 17.5928 14.6818L17.4072 14.9138C16.9289 15.5118 16.3858 16.0549 15.7878 16.5332L15.6832 16.6169C14.7951 17.3274 13.8071 17.9031 12.7511 18.3255C12.2689 18.5184 11.7311 18.5184 11.2489 18.3255C10.1929 17.9031 9.20492 17.3274 8.31681 16.6169L8.21216 16.5332C7.61423 16.0549 7.07112 15.5118 6.59277 14.9138L6.40719 14.6818C5.76579 13.8801 5.27132 12.9712 4.94663 11.9971L4.75036 11.4083C4.58454 10.9108 4.5 10.3899 4.5 9.86553V9.50201C4.5 8.44087 4.92154 7.42319 5.67187 6.67285L5.76975 6.57498C6.45742 5.88731 7.3901 5.50098 8.36261 5.50098C9.47655 5.50098 10.5301 6.00733 11.226 6.87717Z" stroke="currentColor" stroke-width="1.25"/>
                    </svg>
                  </div>
                </button>
              </div>
            </div>
          </div>

          <?php /* Stock / delivery info */ ?>
          <div class='gap-6 py-3 text-xxs text-black/80' data-component='stockInfo'>
            <div
              data-mto-delivery-date
              data-string='Estimated delivery'
              data-week-string='Week'
              data-region='DEFAULT'
              data-delivery-time='<?php echo esc_attr( $ferm_data['delivery_time'] ); ?>'
              data-delivery-time-us='<?php echo esc_attr( $ferm_data['delivery_time_us'] ); ?>'
              class='date'
            ></div>
          </div>

          <?php /* Sample link */ ?>
          <?php if ( ! empty( $ferm_data['sample_url'] ) ) : ?>
          <div class='flex items-start justify-between gap-6 py-5'>
            <a data-sample-link class='cursor-pointer text-sm font-medium underline' href='<?php echo esc_url( $ferm_data['sample_url'] ); ?>'>In doubt? Buy a sample here</a>
          </div>
          <?php endif; ?>

          <?php /* USPs */ ?>
          <?php if ( ! empty( $ferm_data['usps'] ) ) : ?>
          <ul class="py-4 grid grid-cols-2 gap-x-4 gap-y-2 border-t border-light-grey" data-product-usps>
            <?php foreach ( $ferm_data['usps'] as $usp ) : ?>
            <li class="flex items-center gap-2 text-xs sm:text-sm font-medium">
              <svg class='shrink-0 w-3' viewBox="0 0 11 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0.431641 2.59573L4.46325 6.44077L10.4316 0.440765" stroke="#383838" stroke-width="1.25"/>
              </svg>
              <?php echo esc_html( $usp ); ?>
            </li>
            <?php endforeach; ?>
          </ul>
          <?php endif; ?>

          <?php /* Accordion */ ?>
          <div data-component='accordion' class='mx-auto mb-[22px] w-full'>

            <?php /* Description */ ?>
            <div data-accordion-item data-start-open='true' class='border-t border-black/5 py-2.5'>
              <button data-accordion-button class='m-0 flex h-[25.5px] w-full items-center justify-between text-sm font-medium' aria-expanded='true'>
                <span>Description</span>
                <span data-expand-icon class='text-[25px] hidden'> + </span>
                <span data-collapse-icon class='text-[25.5px]'> – </span>
              </button>
              <div data-accordion-content aria-hidden='false' class='grid-height-transition grid'>
                <div class='text-sm leading-tight'>
                  <div data-inner-content class='mt-2 [&>p]:mb-3 [&_a]:underline line-clamp-3'>
                    <?php echo wp_kses_post( $ferm_data['description'] ); ?>
                  </div>
                  <button data-read-more-button data-read-less-text='- Read less' data-read-more-text='+ Read more' class='mb-[14px] hidden pt-5 text-xxs font-medium uppercase underline'>+ Read more</button>
                </div>
              </div>
            </div>

            <?php /* Details */ ?>
            <div data-accordion-item class='border-t border-black/5 py-2.5'>
              <button data-accordion-button class='m-0 flex h-[25.5px] w-full items-center justify-between text-sm font-medium' aria-expanded='false'>
                <span>Details</span>
                <span data-expand-icon class='text-[25px]'> + </span>
                <span data-collapse-icon class='text-[25px] hidden'> – </span>
              </button>
              <div data-accordion-content aria-hidden='true' class='grid-height-transition grid'>
                <div class='text-sm leading-tight'>
                  <div data-inner-content class='mt-2 [&>p]:mb-3 [&_a]:underline'>
                    <ul class='mb-4 flex flex-col gap-2 [&>li]:text-sm'>
                      <?php if ( ! empty( $ferm_data['sku'] ) ) : ?>
                      <li><span class='font-medium'>Item no.:</span> <span data-sku><?php echo esc_html( $ferm_data['sku'] ); ?></span></li>
                      <?php endif; ?>
                      <?php if ( ! empty( $ferm_data['color_name'] ) ) : ?>
                      <li><span class='font-medium'>Color:</span> <span><?php echo esc_html( $ferm_data['color_name'] ); ?></span></li>
                      <?php endif; ?>
                      <?php if ( ! empty( $ferm_data['dimensions'] ) ) : ?>
                      <li><span class='font-medium'>Size:</span> <span data-dimensions data-is-us='false'><?php echo esc_html( $ferm_data['dimensions'] ); ?></span></li>
                      <?php endif; ?>
                      <?php if ( ! empty( $ferm_data['seat_height'] ) ) : ?>
                      <li><span class='font-medium'>Seat height:</span> <span data-seat-height data-is-us='false'><?php echo esc_html( $ferm_data['seat_height'] ); ?></span></li>
                      <?php endif; ?>
                      <?php if ( ! empty( $ferm_data['backrest_height'] ) ) : ?>
                      <li><span class='font-medium'>Backrest height:</span> <span data-backrest-height data-is-us='false'><?php echo esc_html( $ferm_data['backrest_height'] ); ?></span></li>
                      <?php endif; ?>
                      <?php if ( ! empty( $ferm_data['weight'] ) ) : ?>
                      <li><span class='font-medium'>Weight:</span> <span data-weight data-is-us='false'><?php echo esc_html( $ferm_data['weight'] ); ?></span></li>
                      <?php endif; ?>
                      <?php if ( ! empty( $ferm_data['material'] ) ) : ?>
                      <li><span class='font-medium'>Material:</span> <span data-material><?php echo esc_html( $ferm_data['material'] ); ?></span></li>
                      <?php endif; ?>
                      <?php if ( ! empty( $ferm_data['care'] ) ) : ?>
                      <li><span class='font-medium'>Care instructions:</span> <span><?php echo esc_html( $ferm_data['care'] ); ?></span></li>
                      <?php endif; ?>
                    </ul>
                  </div>
                  <button data-read-more-button data-read-less-text='- Read less' data-read-more-text='+ Read more' class='mb-[14px] hidden pt-5 text-xxs font-medium uppercase underline'>+ Read more</button>
                </div>
              </div>
            </div>

            <?php /* Customisation */ ?>
            <div data-accordion-item class='border-t border-black/5 py-2.5'>
              <button data-accordion-button class='m-0 flex h-[25.5px] w-full items-center justify-between text-sm font-medium' aria-expanded='false'>
                <span>Customisation</span>
                <span data-expand-icon class='text-[25px]'> + </span>
                <span data-collapse-icon class='text-[25px] hidden'> – </span>
              </button>
              <div data-accordion-content aria-hidden='true' class='grid-height-transition grid'>
                <div class='text-sm leading-tight'>
                  <div data-inner-content class='mt-2 format-richtext [&>*+*]:mb-3 line-clamp-3'>
                    <p>If you would like to customise one of our Made to Order furniture pieces, you have a few different options:<strong><br/><br/>Online: </strong>When you have found the perfect upholstery for your Made to Order piece, contact us via our contact form, and we will place the order for you.<br/><br/><strong>Need styling assistance?</strong> Book a Styling Session with our dedicated team of design consultants. Our sessions are always complimentary and non-binding for members and can occur online or at our Copenhagen Boutique.</p>
                  </div>
                  <button data-read-more-button data-read-less-text='- Read less' data-read-more-text='+ Read more' class='mb-[14px] hidden pt-5 text-xxs font-medium uppercase underline'>+ Read more</button>
                </div>
              </div>
            </div>

            <?php /* Delivery & Return */ ?>
            <div data-accordion-item class='border-t border-black/5 py-2.5'>
              <button data-accordion-button class='m-0 flex h-[25.5px] w-full items-center justify-between text-sm font-medium' aria-expanded='false'>
                <span>Delivery &amp; Return</span>
                <span data-expand-icon class='text-[25px]'> + </span>
                <span data-collapse-icon class='text-[25px] hidden'> – </span>
              </button>
              <div data-accordion-content aria-hidden='true' class='grid-height-transition grid'>
                <div class='text-sm leading-tight'>
                  <div data-inner-content class='mt-2 format-richtext [&>*+*]:mb-3 line-clamp-3'>
                    <p><strong>Please note: </strong>All freight prices are calculated by the volume of your chosen product(s). The exact price for your order will be calculated at check-out.<br/><br/>For more information on estimated delivery time and shipping costs, please see our shipping terms.</p>
                  </div>
                  <button data-read-more-button data-read-less-text='- Read less' data-read-more-text='+ Read more' class='mb-[14px] hidden pt-5 text-xxs font-medium uppercase underline'>+ Read more</button>
                </div>
              </div>
            </div>

          </div>

        </div>
      </div>

    </div>

    <?php /* ── Full-width Product Details ──────────────────────── */ ?>
    <div class='md:limit bg-cream'>
      <h4 class='mb-4 px-4 md:px-0'>Product details</h4>
      <div class='relative grid grid-cols-2 gap-x-3 bg-beige px-4 pb-20 pt-6 md:p-20'>
        <div class='top-40 col-span-2 pr-4 md:sticky md:col-span-1 self-start'>
          <div class='grid grid-cols-[1fr_2fr] gap-x-6 gap-y-2 text-sm'>
            <?php if ( ! empty( $ferm_data['sku'] ) ) : ?>
            <p class='col-span-1 font-medium'>Item no.:</p>
            <p class='col-span-1' data-sku><?php echo esc_html( $ferm_data['sku'] ); ?></p>
            <?php endif; ?>
            <?php if ( ! empty( $ferm_data['color_name'] ) ) : ?>
            <p class='col-span-1 font-medium'>Color:</p>
            <p class='col-span-1'><?php echo esc_html( $ferm_data['color_name'] ); ?></p>
            <?php endif; ?>
            <?php if ( ! empty( $ferm_data['dimensions'] ) ) : ?>
            <p class='col-span-1 font-medium'>Size:</p>
            <p class='col-span-1' data-dimensions data-is-us='false'><?php echo esc_html( $ferm_data['dimensions'] ); ?></p>
            <?php endif; ?>
            <?php if ( ! empty( $ferm_data['weight'] ) ) : ?>
            <p class='col-span-1 font-medium'>Weight:</p>
            <p class='col-span-1' data-weight data-is-us='false'><?php echo esc_html( $ferm_data['weight'] ); ?></p>
            <?php endif; ?>
            <?php if ( ! empty( $ferm_data['seat_height'] ) ) : ?>
            <p class='col-span-1 font-medium'>Seat height:</p>
            <p class='col-span-1' data-seat-height data-is-us='false'><?php echo esc_html( $ferm_data['seat_height'] ); ?></p>
            <?php endif; ?>
            <?php if ( ! empty( $ferm_data['backrest_height'] ) ) : ?>
            <p class='col-span-1 font-medium'>Backrest height:</p>
            <p class='col-span-1' data-backrest-height data-is-us='false'><?php echo esc_html( $ferm_data['backrest_height'] ); ?></p>
            <?php endif; ?>
          </div>
        </div>
        <?php if ( ! empty( $ferm_data['dimension_drawing'] ) ) : ?>
        <div class='col-span-2 overflow-hidden md:col-span-1'>
          <img
            data-dimension-drawing
            src='<?php echo esc_url( $ferm_data['dimension_drawing'] ); ?>'
            alt='Dimension drawing for <?php echo esc_attr( $ferm_data['title'] ); ?>'
            width='1200'
            height='auto'
            class='w-full md:min-w-[600px]'
          >
        </div>
        <?php endif; ?>
      </div>
    </div>

    <?php /* ── Sticky Add-to-Cart Bar ───────────────────────────── */ ?>
    <div
      data-sticky-atc
      data-product-id='<?php echo esc_attr( $ferm_data['id'] ); ?>'
      data-product-title='<?php echo esc_attr( $ferm_data['title'] ); ?>'
      class='invisible fixed bottom-0 left-0 z-[11] w-full translate-y-full border-t border-black/5 bg-cream pb-[env(safe-area-inset-bottom)]'
    >
      <div class='limit flex flex-col gap-1 py-2.5 md:flex-row md:items-center md:gap-6'>
        <div class='flex min-w-0 items-baseline gap-1.5'>
          <span class='line-clamp-1 text-sm font-medium'><?php echo esc_html( $ferm_data['title'] ); ?></span>
          <span data-sticky-atc-variant class='shrink-0 text-sm text-black/60'></span>
        </div>
        <div class='flex items-baseline justify-between gap-2.5 md:ml-auto md:flex-col-reverse md:items-end md:justify-center md:gap-0'>
          <div class='flex items-baseline gap-2.5'>
            <span data-component='price' data-product-id='<?php echo esc_attr( $ferm_data['id'] ); ?>' class='text-sm text-black'><?php echo wp_kses_post( $ferm_data['price_html'] ); ?></span>
            <span data-sticky-atc-compare class='text-sm text-black line-through hidden'></span>
          </div>
          <span data-sticky-atc-stock class='hidden shrink-0 text-xxs text-black/60'></span>
        </div>
        <div class='mt-2 w-full shrink-0 md:mt-0 md:w-[320px]'>
          <button
            class='font-secondary box-border flex h-12 w-fit max-w-full cursor-pointer items-center justify-center border border-solid border-black px-[14px] py-0 text-sm font-medium no-underline transition-all duration-300 ease-in-out text-cream bg-black hover:bg-transparent hover:text-black disabled:cursor-not-allowed disabled:opacity-40 w-full'
            aria-label='Add to Cart - <?php echo esc_attr( $ferm_data['title'] ); ?>'
            data-sticky-atc-button
          >Add to Cart</button>
        </div>
      </div>
    </div>

  </section>
</div>
