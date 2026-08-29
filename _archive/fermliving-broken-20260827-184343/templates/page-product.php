<?php
/**
 * Ferm Living Product Page - Exact DOM from frozen source
 */
if ( ! defined( "ABSPATH" ) ) { exit; }
global $product;
$product_id = $product->get_id();
$product_data = apply_filters( "aether_adapter_product_data", [] );
$ferm_data = ferm_build_product_page_data( $product_data );
?>
<main class="content" id="main-content">
  <div class="limit mx-auto px-4 tab_l:px-6 py-8">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-12 md:gap-16">
      <div class="product-gallery-wrapper">
        <?php include aether_active_design_dir() . "components/product/gallery.php"; ?>
      </div>
      <div class="product-info-wrapper">
        <?php include aether_active_design_dir() . "components/product/info.php"; ?>
      </div>
    </div>
    <div class="mt-16" data-component="productAccordion">
      <?php if ( $product_data["specs"] ?? [] ) : ?>
      <div class="border-t border-black/10">
        <?php foreach ( $product_data["specs"] as $spec ) : $title = $spec["title"] ?? ""; $body = $spec["body"] ?? ""; ?>
        <details class="group border-b border-black/10">
          <summary class="flex items-center justify-between py-4 cursor-pointer list-none">
            <span class="font-medium"><?php echo esc_html( $title ); ?></span>
            <svg class="h-5 w-5 transition-transform group-open:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
          </summary>
          <div class="prose prose-sm pb-6"><?php echo wp_kses_post( $body ); ?></div>
        </details>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
    <?php if ( $ferm_data["cross_sell"] ?? [] ) : ?>
    <div class="mt-16" data-component="crossSell">
      <h2 class="font-primary text-3xl font-medium leading-[1.15] mb-8">You May Also Like</h2>
      <div class="grid grid-cols-2 gap-4 md:grid-cols-4" data-component="productCardGrid">
        <?php foreach ( $ferm_data["cross_sell"] as $product ) : include aether_active_design_dir() . "components/cards/product.php"; endforeach; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>
</main>
<script>window.FermPageData = <?php echo wp_json_encode( $ferm_data ); ?>;</script>
