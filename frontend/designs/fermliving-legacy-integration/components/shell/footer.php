<?php
/**
 * Ferm Living site footer — exact frozen source DOM structure.
 *
 * Key:    'shell/footer' (override)
 * Source: fermliving.com footer structure
 * Props:  brand, brand_url, columns, newsletter, legal, payments, usp_items.
 * Contract: keeps footer#footer, .footer-newsletter-form (#footerNewsletterForm),
 *           .footer-legal, .footer-payments — platform newsletter JS operates unchanged.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$componentData = isset( $componentData ) ? (array) $componentData : array();

$brand      = isset( $componentData['brand'] ) ? $componentData['brand'] : '';
$brand_url  = isset( $componentData['brand_url'] ) ? $componentData['brand_url'] : '';
$columns    = isset( $componentData['columns'] ) ? (array) $componentData['columns'] : array();
$newsletter = isset( $componentData['newsletter'] ) ? (array) $componentData['newsletter'] : array();
$legal      = aureon_get_option( 'aether_footer_legal', isset( $componentData['legal'] ) ? (array) $componentData['legal'] : array() );
$payments   = aureon_get_option( 'aether_footer_payments', isset( $componentData['payments'] ) ? (array) $componentData['payments'] : array() );
$usp_items  = isset( $componentData['usp_items'] ) ? (array) $componentData['usp_items'] : array();
$socials    = aureon_get_option( 'aether_social_items', isset( $componentData['socials'] ) ? (array) $componentData['socials'] : array() );

$newsletter_heading = aureon_get_option( 'aether_newsletter_heading', 'Stay Updated' );
$newsletter_text    = aureon_get_option( 'aether_newsletter_text', 'Sign up for news, offers and inspiration. No spam, ever.' );
$newsletter_url     = isset( $newsletter['url'] ) ? $newsletter['url'] : '#';
?>
<footer class='section-footer mt-20 bg-cream tab_p:mt-32' id="footer" role="contentinfo" aria-label="Site footer">
  <?php /* USP Row */ ?>
  <div class='bg-canvas py-20'>
    <div class='limit w-full'>
      <div class='grid-12'>
        <?php foreach ( $usp_items as $item ) :
          $usp_title = isset( $item['title'] ) ? $item['title'] : '';
          $usp_text  = isset( $item['text'] ) ? $item['text'] : '';
          $usp_url   = isset( $item['url'] ) ? $item['url'] : '';
          ?>
          <div class='col-start-auto col-end-[span_12] flex max-w-[300px] flex-col tab_p:col-end-[span_6] tab_p:max-w-none tab_l:col-end-[span_3] tab_l:mb-0 [&:not(:last-child)]:mb-8 tab_l:[&:not(:last-child)]:mb-0 tab_p:[&:nth-child(1)]:mb-10 tab_l:[&:nth-child(1)]:mb-0 tab_p:[&:nth-child(2)]:mb-10 tab_l:[&:nth-child(2)]:mb-0'>
            <?php if ( $usp_url ) : ?>
              <a href='<?php echo esc_url( $usp_url ); ?>' class='footer__top__item'>
            <?php endif; ?>
              <div class='mb-3 font-primary text-2xl font-medium text-black tab_p:mb-2'>
                <?php echo esc_html( $usp_title ); ?>
              </div>
              <div class='text-sm'><?php echo esc_html( $usp_text ); ?></div>
            <?php if ( $usp_url ) : ?>
              </a>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <?php /* Main Footer: Newsletter + Link Columns */ ?>
  <div class='flex w-full flex-row justify-between bg-cream pb-[120px] pt-16 mobile:pt-14 tab_l:flex-col'>
    <div class='limit w-full'>
      <div class='grid-12'>
        <div class='col-start-auto col-end-[span_12] flex w-full flex-col tab_p:col-end-[span_6] tab_l:col-end-[span_3] [&:not(:last-child)]:mb-8 [&:nth-child(1)]:!mb-16 tab_p:[&:nth-child(1)]:mb-10 tab_p:[&:nth-child(2)]:mb-10'>
          <div
            role='region'
            aria-label='Newsletter signup'
            class='klaviyo-form-UDJeJw klaviyo-form-footer'
            id="footerNewsletterForm"
          >
            <?php /* Newsletter form — bridges to Klaviyo or Aureon newsletter */ ?>
            <div class='mb-4'>
              <h4 class='text-lg font-medium'><?php echo esc_html( $newsletter_heading ); ?></h4>
              <p class='text-sm'><?php echo esc_html( $newsletter_text ); ?></p>
            </div>
            <form class='footer-newsletter-form-inner' aria-label="Newsletter subscription" method="post">
              <input type="email"
                     name="email"
                     placeholder="Your email"
                     required
                     aria-label="Email address"
                     class='w-full border-b border-black bg-transparent py-2 text-sm focus:outline-none'>
              <button type="submit" aria-label="Subscribe" class='btn btn-sm mt-2'>
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.25" stroke-linecap="square" stroke-linejoin="round"/>
                </svg>
              </button>
            </form>
          </div>
        </div>

        <?php foreach ( $columns as $column ) :
          $heading = isset( $column['heading'] ) ? $column['heading'] : '';
          $links   = isset( $column['links'] ) ? (array) $column['links'] : array();
          if ( empty( $heading ) && empty( $links ) ) {
            continue;
          }
          ?>
          <ul class='col-start-auto col-end-[span_6] m-0 flex w-full list-none flex-col p-0 tab_l:col-end-[span_3] [&:not(:last-child)]:mb-8 [&:nth-child(1)]:col-end-[span_12] [&:nth-child(1)]:!mb-16 tab_p:[&:nth-child(1)]:col-end-[span_3] tab_p:[&:nth-child(1)]:mb-10 tab_p:[&:nth-child(2)]:mb-10'>
            <li class='text-lg font-medium'>
              <div class='mb-4'><?php echo esc_html( $heading ); ?></div>
              <?php if ( ! empty( $links ) ) : ?>
                <ul class='m-0 list-none p-0 leading-[13px]'>
                  <?php foreach ( $links as $link ) :
                    $link_label = isset( $link['label'] ) ? $link['label'] : '';
                    $link_url   = isset( $link['url'] ) ? $link['url'] : '#';
                    if ( empty( $link_label ) ) {
                      continue;
                    }
                    ?>
                    <li class='[&:not(:last-child)]:mb-2'>
                      <a
                        class='animation-underline text-[13px] font-normal text-black no-underline'
                        href='<?php echo esc_url( $link_url ); ?>'
                      ><?php echo esc_html( $link_label ); ?></a>
                    </li>
                  <?php endforeach; ?>
                </ul>
              <?php endif; ?>
            </li>
          </ul>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <?php /* Bottom Bar: Legal + Company Info + Payments */ ?>
  <div class='bg-cream text-[#000000bf]'>
    <div class='limit w-full'>
      <div class='grid-12 border-t border-[rgba(0,0,0,0.05)] py-8 tab_p:py-3'>
        <div class='footer__col--bottom col-end-[span_12] flex w-full flex-row flex-wrap gap-x-4 gap-y-1 tab_p:col-end-[span_6] tab_p:gap-x-6 items-center min-w-0'>
          <?php if ( ! empty( $legal ) ) : ?>
            <?php foreach ( $legal as $link ) :
              $link_label = isset( $link['label'] ) ? $link['label'] : '';
              $link_url   = isset( $link['url'] ) ? $link['url'] : '#';
              if ( empty( $link_label ) ) {
                continue;
              }
              ?>
              <a
                class='text-[13px] font-normal text-[#000000bf] no-underline [&:visited]:text-[#000000bf]'
                href='<?php echo esc_url( $link_url ); ?>'
              ><?php echo esc_html( $link_label ); ?></a>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>

        <div class='col-start-auto col-end-[span_12] flex w-full items-center tab_p:col-end-[span_3] min-w-0'>
          <address class='text-[13px] not-italic'>Ferm Living ApS CVR No. 30070186</address>
        </div>

        <div class='col-end-[span_12] tab_p:col-end-[span_3]'>
          <span>
            <?php if ( ! empty( $payments ) ) : ?>
              <img src='<?php echo esc_url( aureon_get_option( 'aether_footer_payments_image', '' ) ); ?>' alt='Payment icons' loading='lazy' width='359' height='32' class='max-w-[280px] h-auto tab_p:ml-auto tab_p:max-w-full'>
            <?php endif; ?>
          </span>
        </div>
      </div>
    </div>
  </div>
</footer>