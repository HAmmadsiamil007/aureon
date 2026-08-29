<?php
/**
 * Ferm Living Footer - Exact DOM from frozen source
 */
if ( ! defined( "ABSPATH" ) ) { exit; }
$footer = $data["footer"] ?? [];
$usp_items = $footer["usp_items"] ?? [];
$newsletter = $footer["newsletter"] ?? [ "heading" => "Ferm Living news", "text" => "" ];
$columns = $footer["columns"] ?? [];
$legal = $footer["legal"] ?? [];
$payments = $footer["payments"] ?? [];
$socials = $footer["socials"] ?? [];
?>
<div id="shopify-section-footer" class="shopify-section"><footer class="section-footer mt-20 bg-cream tab_p:mt-32">
  <div class="bg-canvas py-20">
    <div class="limit w-full">
      <div class="grid-12">
        <?php foreach ( $usp_items as $item ) : $url = $item["url"] ?? "#"; $title = $item["title"] ?? ""; $desc = $item["description"] ?? ""; ?>
        <div class="col-start-auto col-end-[span_12] flex max-w-[300px] flex-col tab_p:col-end-[span_6] tab_p:max-w-none tab_l:col-end-[span_3] tab_l:mb-0 [&:not(:last-child)]:mb-8 tab_l:[&:not(:last-child)]:mb-0 tab_p:[&:nth-child(1)]:mb-10 tab_l:[&:nth-child(1)]:mb-0 tab_p:[&:nth-child(2)]:mb-10 tab_l:[&:nth-child(2)]:mb-0">
          <?php if ( $url && $url !== "#" ) : ?><a href="<?php echo esc_url( $url ); ?>" class="footer__top__item"><?php endif; ?>
            <div class="mb-3 font-primary text-2xl font-medium text-black tab_p:mb-2"><?php echo esc_html( $title ); ?></div>
            <div class="text-sm"><?php echo esc_html( $desc ); ?></div>
          <?php if ( $url && $url !== "#" ) : ?></a><?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <div class="flex w-full flex-row justify-between bg-cream pb-[120px] pt-16 mobile:pt-14 tab_l:flex-col">
    <div class="limit w-full">
      <div class="grid-12">
        <div class="col-start-auto col-end-[span_12] flex w-full flex-col tab_p:col-end-[span_6] tab_l:col-end-[span_3] [&:not(:last-child)]:mb-8 [&:nth-child(1)]:!mb-16 tab_p:[&:nth-child(1)]:mb-10 tab_p:[&:nth-child(2)]:mb-10">
          <div role="region" aria-label="Newsletter signup" class="klaviyo-form-UDJeJw klaviyo-form-footer"></div>
        </div>
        <?php foreach ( $columns as $col ) : $title = $col["title"] ?? ""; $links = $col["links"] ?? []; ?>
        <ul class="col-start-auto col-end-[span_6] m-0 flex w-full list-none flex-col p-0 tab_l:col-end-[span_3] [&:not(:last-child)]:mb-8 [&:nth-child(1)]:col-end-[span_12] [&:nth-child(1)]:!mb-16 tab_p:[&:nth-child(1)]:col-end-[span_3] tab_p:[&:nth-child(1)]:mb-10 tab_p:[&:nth-child(2)]:mb-10">
          <li class="text-lg font-medium"><div class="mb-4"><?php echo esc_html( $title ); ?></div>
            <ul class="m-0 list-none p-0 leading-[13px]">
              <?php foreach ( $links as $link ) : $url = $link["url"] ?? "#"; $label = $link["label"] ?? ""; ?>
              <li class="[&:not(:last-child)]:mb-2"><a class="animation-underline text-[13px] font-normal text-black no-underline" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $label ); ?></a></li>
              <?php endforeach; ?>
            </ul>
          </li>
        </ul>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="footer__col--bottom col-end-[span_12] flex w-full flex-row flex-wrap gap-x-4 gap-y-1 tab_p:col-end-[span_6] tab_p:gap-x-6 items-center min-w-0">
      <?php foreach ( $legal as $item ) : $url = $item["url"] ?? "#"; $label = $item["label"] ?? ""; ?>
      <a class="text-[13px] font-normal text-[#000000bf] no-underline [&:visited]:text-[#000000bf]" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $label ); ?></a>
      <?php endforeach; ?>
    </div>
    <div class="col-start-auto col-end-[span_12] flex w-full items-center tab_p:col-end-[span_3] min-w-0">
      <address class="text-[13px] not-italic">Ferm Living ApS CVR No. 30070186</address>
    </div>
    <div class="col-end-[span_12] tab_p:col-end-[span_3]"><span><?php if ( $payments ) : ?><img src="<?php echo esc_url( $payments[0]["image"] ?? "" ); ?>" alt="Payment icons" loading="lazy" width="359" height="32" class="max-w-[280px] h-auto tab_p:ml-auto tab_p:max-w-full"><?php endif; ?></span></div>
  </div>
</footer></div>
