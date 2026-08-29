<?php
/**
 * Ferm Living Cart Page - Exact DOM from frozen source
 */
if ( ! defined( "ABSPATH" ) ) { exit; }
get_header();
?>
<main class="content" id="main-content">
  <div class="limit mx-auto px-4 tab_l:px-6 py-8">
    <h1 class="font-primary text-3xl tab_l:text-4xl font-medium leading-[1.15] mb-8">My Cart</h1>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8" data-component="cartPage">
      <div class="lg:col-span-2" data-cart-items>
        <table class="w-full border-collapse">
          <thead>
            <tr class="border-b border-black/10">
              <th class="text-left py-4 font-medium">Product</th>
              <th class="text-left py-4 font-medium">Price</th>
              <th class="text-left py-4 font-medium">Quantity</th>
              <th class="text-left py-4 font-medium">Total</th>
            </tr>
          </thead>
          <tbody class="cart-items-body">
            <?php foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) : $product = $cart_item["data"]; if ( ! $product ) continue; ?>
            <tr class="border-b border-black/10" data-cart-item-key="<?php echo esc_attr( $cart_item_key ); ?>">
              <td class="py-6 flex items-center gap-4">
                <?php echo wp_get_attachment_image( $product->get_image_id(), "thumbnail", false, [ "class" => "h-16 w-16 object-cover", "loading" => "lazy" ] ); ?>
                <div>
                  <a href="<?php echo esc_url( get_permalink( $cart_item["product_id"] ) ); ?>" class="font-medium"><?php echo esc_html( $product->get_name() ); ?></a>
                  <?php if ( $cart_item["variation_id"] ) : $variation = wc_get_product( $cart_item["variation_id"] ); if ( $variation ) { $attrs = $variation->get_variation_attributes(); foreach ( $attrs as $attr => $val ) { echo "<div class=\"text-sm text-black/60\">" . esc_html( $val ) . "</div>"; } } ?>
                  <?php endif; ?>
                </div>
              </td>
              <td class="py-6"><?php echo wc_price( $product->get_price() ); ?></td>
              <td class="py-6"><input type="number" min="1" value="<?php echo esc_attr( $cart_item["quantity"] ); ?>" class="w-16 h-10 text-center border border-black/20" data-cart-quantity data-cart-item-key="<?php echo esc_attr( $cart_item_key ); ?>"></td>
              <td class="py-6 font-medium" data-cart-line-total><?php echo wc_price( $cart_item["line_total"] ); ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <div class="lg:col-span-1" data-cart-summary>
        <div class="sticky top-24 bg-cream p-6">
          <h2 class="font-primary text-xl font-medium mb-6">Order Summary</h2>
          <div class="space-y-3 text-sm">
            <div class="flex justify-between"><span>Subtotal</span><span><?php echo WC()->cart->get_cart_subtotal(); ?></span></div>
            <div class="flex justify-between"><span>Shipping</span><span><?php echo WC()->cart->get_shipping_total() ? wc_price( WC()->cart->get_shipping_total() ) : "Calculated at checkout"; ?></span></div>
            <div class="flex justify-between border-t border-black/10 pt-3 font-medium"><span>Total</span><span><?php echo WC()->cart->get_total(); ?></span></div>
          </div>
          <div class="mt-6" data-component="shippingText" data-free-shipping-amount="EUR 150" data-shipping-state="1">Free EU delivery on orders over <strong>EUR 150</strong>.</div>
          <a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="font-secondary box-border flex h-12 w-full mt-6 cursor-pointer items-center justify-center border border-solid border-black px-[14px] py-0 text-sm font-medium no-underline transition-all duration-300 ease-in-out hover:text-cream bg-transparent text-black hover:bg-black">Proceed to Checkout</a>
        </div>
      </div>
    </div>
  </div>
</main>
<?php get_footer(); ?>
