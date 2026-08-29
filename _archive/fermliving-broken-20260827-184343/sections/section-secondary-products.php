<?php
/**
 * Ferm Living Secondary Products Section - Exact DOM from frozen source
 */
if ( ! defined( "ABSPATH" ) ) { exit; }
$products = $data["secondary_products"]["items"] ?? [];
if ( empty( $products ) ) { return; }
?>
<section class="section py-16 tab_l:py-24" data-component="secondaryProducts">
  <div class="limit mx-auto px-4 tab_l:px-6">
    <div class="grid-12">
      <div class="col-span-12 mb-8 tab_l:mb-12">
        <h2 class="font-primary text-3xl tab_l:text-4xl font-medium leading-[1.15] text-black">You May Also Like</h2>
      </div>
      <div class="grid grid-cols-2 gap-4 md:grid-cols-4" data-component="productCardGrid">
        <?php foreach ( $products as $product ) : include aether_active_design_dir() . "components/cards/product.php"; endforeach; ?>
      </div>
    </div>
  </div>
</section>
