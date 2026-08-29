<?php
/**
 * Ferm Living Product Info - Exact DOM from frozen source
 */
if ( ! defined( "ABSPATH" ) ) { exit; }
$title       = $data["title"] ?? "";
$price_html  = $data["price_html"] ?? "";
$price_cents = $data["price_cents"] ?? 0;
$currency    = $data["currency"] ?? "EUR";
$sku         = $data["sku"] ?? "";
$description = $data["description"] ?? "";
$colors      = $data["colors"] ?? [];
$color_name  = $data["color_name"] ?? "";
$sizes       = $data["sizes"] ?? [];
$badge       = $data["badge"] ?? "";
$variant_id  = $data["variant_id"] ?? 0;
$product_id  = $data["product_id"] ?? 0;
$is_mto      = $data["is_mto"] ?? false;
$delivery    = $data["delivery_time"] ?? 56;
$usps        = $data["usps"] ?? [];
?>
<div class="product-info" data-component="productInfo" data-product-id="<?php echo esc_attr( $product_id ); ?>">
  <?php if ( $badge ) : ?>
  <div class="mb-3"><?php echo ferm_badge_html( $badge ); ?></div>
  <?php endif; ?>
  <h1 class="font-primary text-3xl tab_l:text-4xl font-medium leading-[1.15] mb-2"><?php echo esc_html( $title ); ?></h1>
  <?php if ( $color_name ) : ?>
  <p class="text-sm text-black/60 mb-4">Color: <?php echo esc_html( $color_name ); ?></p>
  <?php endif; ?>
  <div class="flex items-baseline gap-4 mb-6" data-price-container>
    <span class="font-primary text-2xl font-medium" data-variant-price="<?php echo esc_attr( $price_cents ); ?>"><?php echo wp_kses_post( $price_html ); ?></span>
  </div>
  <div class="mb-6" data-product-sku>SKU: <?php echo esc_html( $sku ); ?></div>
  <?php if ( $colors ) : ?>
  <fieldset class="mb-6" data-product-colors>
    <legend class="text-sm font-medium mb-3">Color</legend>
    <div class="flex flex-wrap gap-2">
      <?php foreach ( $colors as $color ) : $hex = $color["hex"] ?? ""; $name = $color["name"] ?? ""; $selected = ( $name === $color_name ) ? "aria-pressed=\"true\"" : ""; ?>
      <button type="button" class="h-10 w-10 rounded-full border-2 <?php echo $selected ? "border-black" : "border-black/10"; ?>" style="background-color: <?php echo esc_attr( $hex ); ?>" <?php echo $selected; ?> aria-label="<?php echo esc_attr( $name ); ?>" data-color-hex="<?php echo esc_attr( $hex ); ?>" data-color-name="<?php echo esc_attr( $name ); ?>"></button>
      <?php endforeach; ?>
    </div>
  </fieldset>
  <?php endif; ?>
  <?php if ( $sizes ) : ?>
  <fieldset class="mb-6" data-product-sizes>
    <legend class="text-sm font-medium mb-3">Size</legend>
    <div class="flex flex-wrap gap-2">
      <?php foreach ( $sizes as $size ) : $label = $size["label"] ?? ""; $value = $size["value"] ?? ""; $available = $size["available"] ?? true; ?>
      <button type="button" class="px-4 py-2 border <?php echo $available ? "border-black text-black hover:bg-black hover:text-cream" : "border-black/20 text-black/30 cursor-not-allowed"; ?>" <?php echo $available ? "" : "disabled"; ?> data-size-value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $label ); ?></button>
      <?php endforeach; ?>
    </div>
  </fieldset>
  <?php endif; ?>
  <div class="mb-6" data-component="addToCart" data-cart-template="false" data-loading-text="Adding ..." data-product-title="<?php echo esc_attr( $title ); ?>" data-product-price="<?php echo esc_attr( $price_cents ); ?>" data-shop-currency="<?php echo esc_attr( $currency ); ?>" data-variant-id="<?php echo esc_attr( $variant_id ); ?>" data-is-mto-product="<?php echo $is_mto ? "true" : "false"; ?>" data-has-mto-tag="false" data-do-not-open-drawer="">
    <button type="button" class="font-secondary box-border flex h-12 w-full max-w-full cursor-pointer items-center justify-center border border-solid border-black px-[14px] py-0 text-sm font-medium no-underline transition-all duration-300 ease-in-out hover:text-cream bg-transparent text-black hover:bg-black disabled:cursor-not-allowed disabled:opacity-40 !justify-start" data-button-add-to-cart>+ Add to Cart</button>
  </div>
  <?php if ( $description ) : ?>
  <div class="mt-8 prose prose-sm max-w-none" data-product-description><?php echo wp_kses_post( $description ); ?></div>
  <?php endif; ?>
  <?php if ( $usps ) : ?>
  <div class="mt-8 border-t border-black/10 pt-6" data-product-usps>
    <h3 class="font-medium mb-4">Why choose Ferm Living</h3>
    <ul class="space-y-2">
      <?php foreach ( $usps as $usp ) : ?>
      <li class="flex items-start gap-3"><svg class="h-5 w-5 flex-shrink-0 mt-0.5 text-black" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg><span><?php echo esc_html( $usp ); ?></span></li>
      <?php endforeach; ?>
    </ul>
  </div>
  <?php endif; ?>
  <?php if ( $is_mto ) : ?>
  <div class="mt-6 p-4 bg-black/5 rounded" data-product-mto>
    <p class="text-sm">Made to order — ships in <?php echo esc_html( $delivery ); ?> days.</p>
  </div>
  <?php endif; ?>
</div>
<?php
function ferm_badge_html( $badge ) { $map = [ "Sale" => [ "text" => "Sale", "class" => "bg-black text-cream" ], "New" => [ "text" => "New", "class" => "bg-black text-cream" ], "Certified" => [ "text" => "Certified", "class" => "bg-green-700 text-cream" ] ]; $b = $map[ $badge ] ?? [ "text" => $badge, "class" => "bg-black text-cream" ]; return "<span class=\"inline-flex items-center px-1.5 py-0.5 text-[10px] font-medium uppercase " . $b["class"] . "\">" . esc_html( $b["text"] ) . "</span>"; }
