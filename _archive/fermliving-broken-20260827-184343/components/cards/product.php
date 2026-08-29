<?php
/**
 * Ferm Living Product Card - Exact DOM from frozen source
 */
if ( ! defined( "ABSPATH" ) ) { exit; }
$product = $data["product"] ?? [];
$id      = $product["id"] ?? 0;
$name    = $product["name"] ?? "";
$price   = $product["price"] ?? "";
$image   = $product["image"] ?? "";
$alt     = $product["alt"] ?? $name;
$url     = $product["url"] ?? "";
$badge   = $product["badge"] ?? "";
$rating  = $product["rating"] ?? 0;
$reviews = $product["reviews"] ?? 0;
$swatches = $product["swatches"] ?? [];
$is_simple = ( $product["product_type"] ?? "simple" ) === "simple";
$add_to_cart_url = $product["add_to_cart_url"] ?? "";
$variant_id = $product["variant_id"] ?? $id;
?>
<div class="product-card group relative" data-product-id="<?php echo esc_attr( $id ); ?>" data-component="productCard">
  <a href="<?php echo esc_url( $url ); ?>" class="product-card__image absolute inset-0 block no-underline z-10" aria-label="<?php echo esc_attr( $name ); ?>"></a>
  <div class="relative aspect-square overflow-hidden">
    <?php if ( $image ) : ?>
    <img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $alt ); ?>" loading="lazy" class="absolute left-0 top-0 h-full w-full object-cover transition-transform duration-500 ease-out group-hover:scale-105">
    <?php endif; ?>
    <?php if ( $badge ) : ?>
    <div class="absolute left-2 top-2 z-10"><?php echo ferm_badge_html( $badge ); ?></div>
    <?php endif; ?>
    <?php if ( $swatches ) : ?>
    <div class="absolute left-2 bottom-2 z-10 flex gap-1" data-product-swatches>
      <?php foreach ( $swatches as $swatch ) : $color = $swatch["color"] ?? ""; $label = $swatch["label"] ?? ""; ?>
      <button type="button" class="h-5 w-5 rounded-full border border-black/10 transition-transform hover:scale-110" style="background-color: <?php echo esc_attr( $color ); ?>" aria-label="<?php echo esc_attr( $label ); ?>" data-swatch-color="<?php echo esc_attr( $color ); ?>"></button>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
  <div class="mt-3 flex flex-col gap-1">
    <h3 class="text-sm font-medium leading-[1.4] line-clamp-1"><?php echo esc_html( $name ); ?></h3>
    <div class="flex items-center gap-2">
      <span class="text-sm font-medium" data-price><?php echo wp_kses_post( $price ); ?></span>
    </div>
  </div>
  <?php if ( $is_simple && $add_to_cart_url ) : ?>
  <form action="<?php echo esc_url( $add_to_cart_url ); ?>" method="post" class="mt-3" data-component="addToCart" data-cart-template="false" data-loading-text="Adding ..." data-product-title="<?php echo esc_attr( $name ); ?>" data-product-price="<?php echo esc_attr( $price ); ?>" data-shop-currency="EUR" data-variant-id="<?php echo esc_attr( $variant_id ); ?>" data-is-mto-product="false" data-has-mto-tag="false" data-do-not-open-drawer="true">
    <button type="submit" class="font-secondary box-border flex h-12 w-full max-w-full cursor-pointer items-center justify-center border border-solid border-black px-[14px] py-0 text-sm font-medium no-underline transition-all duration-300 ease-in-out hover:text-cream bg-transparent text-black hover:bg-black disabled:cursor-not-allowed disabled:opacity-40 !justify-start" data-button-add-to-cart>+ Add to Cart</button>
  </form>
  <?php else : ?>
  <a href="<?php echo esc_url( $url ); ?>" class="font-secondary box-border flex h-12 w-full max-w-full cursor-pointer items-center justify-center border border-solid border-black px-[14px] py-0 text-sm font-medium no-underline transition-all duration-300 ease-in-out hover:text-cream bg-transparent text-black hover:bg-black disabled:cursor-not-allowed disabled:opacity-40 !justify-start" data-button-select-size>Select size</a>
  <?php endif; ?>
</div>
<?php
function ferm_badge_html( $badge ) { $map = [ "Sale" => [ "text" => "Sale", "class" => "bg-black text-cream" ], "New" => [ "text" => "New", "class" => "bg-black text-cream" ], "Certified" => [ "text" => "Certified", "class" => "bg-green-700 text-cream" ] ]; $b = $map[ $badge ] ?? [ "text" => $badge, "class" => "bg-black text-cream" ]; return "<span class=\"inline-flex items-center px-1.5 py-0.5 text-[10px] font-medium uppercase " . $b["class"] . "\">" . esc_html( $b["text"] ) . "</span>"; }
