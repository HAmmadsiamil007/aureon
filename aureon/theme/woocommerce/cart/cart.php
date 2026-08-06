<?php
/**
 * Cart page template.
 *
 * AETHER-styled WooCommerce cart.
 *
 * @package Aureon
 * @version 8.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header( 'shop' );

$aether   = get_template_directory_uri() . '/assets/aether';
$shop_url = wc_get_page_permalink( 'shop' );
?>

<section class="cart-page" id="cartPage">
	<div class="container">
		<div class="section-header">
			<span class="section-label" data-motion-text="words">Your Cart</span>
			<h1 class="section-title" data-motion-text="words">Shopping Bag</h1>
		</div>

		<?php wc_print_notices(); ?>

		<?php if ( WC()->cart->is_empty() ) : ?>
			<div class="cart-empty">
				<i class="fas fa-shopping-bag"></i>
				<h3>Your bag is empty</h3>
				<p>Looks like you haven't added anything yet.</p>
				<a href="<?php echo esc_url( $shop_url ); ?>" class="btn btn-primary">Start Shopping</a>
			</div>
		<?php else : ?>
			<form action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post" class="woocommerce-cart-form">
				<div class="cart-layout">
					<div class="cart-items">
						<table class="woocommerce-cart-form__table" cellspacing="0">
							<thead>
								<tr>
									<th class="product-name">Product</th>
									<th class="product-price">Price</th>
									<th class="product-quantity">Quantity</th>
									<th class="product-subtotal">Total</th>
									<th class="product-remove"><span class="screen-reader-text">Remove</span></th>
								</tr>
							</thead>
							<tbody>
								<?php
								foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
									$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
									$quantity = $cart_item['quantity'];

									if ( ! $_product || ! $_product->is_visible() ) {
										continue;
									}
									?>
									<tr class="woocommerce-cart-form__cart-item cart-item">
										<td class="product-name">
											<div class="cart-item-info">
												<div class="cart-item-image">
													<?php echo wp_kses_post( $_product->get_image( 'woocommerce_thumbnail' ) ); ?>
												</div>
												<div class="cart-item-details">
													<a href="<?php echo esc_url( wc_get_cart_item_data_remove_url( $cart_item_key ) ); ?>" class="cart-item-remove" aria-label="Remove <?php echo esc_attr( $_product->get_name() ); ?>">
														<i class="fas fa-times"></i>
													</a>
													<h3 class="cart-item-name">
														<?php echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) ); ?>
													</h3>
													<?php
													$variations = $cart_item['variation'];
													if ( is_array( $variations ) ) {
														foreach ( $variations as $attr => $val ) {
															echo '<span class="cart-item-attribute">' . esc_html( wc_attribute_label( str_replace( 'attribute_', '', $attr ) ) ) . ': ' . esc_html( $val ) . '</span>';
														}
													}
													?>
												</div>
											</div>
										</td>
										<td class="product-price">
											<?php echo wp_kses_post( wc_get_cart_item_data_display_price( $cart_item ) ); ?>
										</td>
										<td class="product-quantity">
											<?php
											woocommerce_quantity_input(
												array(
													'input_name'  => "cart[{$cart_item_key}][qty]",
													'input_value' => $quantity,
													'min_value'   => 1,
													'max_value'   => $_product->get_max_purchase_quantity(),
													'product_id'  => $_product->get_id(),
												)
											);
											?>
										</td>
										<td class="product-subtotal">
											<?php echo wp_kses_post( wc_get_cart_item_data_display_subtotal( $cart_item ) ); ?>
										</td>
										<td class="product-remove">
											<a href="<?php echo esc_url( wc_get_cart_item_data_remove_url( $cart_item_key ) ); ?>" class="remove" aria-label="Remove this item">
												<i class="fas fa-trash-alt"></i>
											</a>
										</td>
									</tr>
									<?php
								}
								?>
							</tbody>
						</table>

						<div class="cart-actions">
							<div class="cart-coupon">
								<?php if ( wc_coupons_enabled() ) : ?>
									<form class="woocommerce-form-coupon" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
										<input type="text" name="coupon_code" class="input-text" placeholder="Coupon code" id="coupon_code" aria-label="Coupon code">
										<button type="submit" class="btn btn-outline btn-sm" name="apply_coupon" value="Apply">Apply</button>
									</form>
								<?php endif; ?>
							</div>
							<button type="submit" class="btn btn-outline btn-sm" name="update_cart" value="Update Cart">Update Cart</button>
						</div>
					</div>

					<!-- Cart Summary -->
					<div class="cart-summary">
						<h3 class="cart-summary-title">Order Summary</h3>
						<div class="cart-summary-row">
							<span>Subtotal</span>
							<span><?php echo wp_kses_post( WC()->cart->get_cart_subtotal() ); ?></span>
						</div>
						<?php
						foreach ( WC()->cart->get_shipping_methods() as $shipping_method ) {
							?>
							<div class="cart-summary-row">
								<span><?php echo esc_html( $shipping_method->get_label() ); ?></span>
								<span><?php echo wp_kses_post( wc_price( $shipping_method->get_cost() ) ); ?></span>
							</div>
							<?php
						}
						?>
						<?php
						foreach ( WC()->cart->get_fees() as $fee ) {
							?>
							<div class="cart-summary-row">
								<span><?php echo esc_html( $fee->name ); ?></span>
								<span><?php echo wp_kses_post( wc_price( $fee->total ) ); ?></span>
							</div>
							<?php
						}
						?>
						<?php if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) : ?>
							<div class="cart-summary-row">
								<span>Tax</span>
								<span><?php echo wp_kses_post( WC()->cart->get_tax_total() ); ?></span>
							</div>
						<?php endif; ?>
						<div class="cart-summary-row cart-total">
							<span>Total</span>
							<span><?php echo wp_kses_post( WC()->cart->get_total_display_price() ); ?></span>
						</div>
						<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="btn btn-primary btn-lg btn-full">
							Proceed to Checkout <i class="fas fa-arrow-right"></i>
						</a>
						<a href="<?php echo esc_url( $shop_url ); ?>" class="btn btn-outline btn-full">Continue Shopping</a>
					</div>
				</div>
			</form>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer( 'shop' );
