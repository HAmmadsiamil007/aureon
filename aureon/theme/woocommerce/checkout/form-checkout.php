<?php
/**
 * Checkout page template.
 *
 * AETHER-styled WooCommerce checkout.
 *
 * @package Aureon
 * @version 8.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header( 'shop' );

$aether = get_template_directory_uri() . '/assets/aether';
?>

<section class="checkout-page" id="checkoutPage">
	<div class="container">
		<?php wc_print_notices(); ?>

		<?php if ( ! WC()->cart->is_empty() ) : ?>
			<div class="checkout-layout">
				<!-- Checkout Form -->
				<div class="checkout-main">
					<form name="checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" method="post" class="checkout_form" id="checkoutForm" enctype="multipart/form-data">

						<!-- Checkout Steps Navigation -->
						<div class="checkout-steps">
							<ol>
								<li class="active" data-step="1"><span>1</span> Information</li>
								<li data-step="2"><span>2</span> Shipping</li>
								<li data-step="3"><span>3</span> Payment</li>
							</ol>
						</div>

						<!-- Step 1: Customer Information -->
						<div class="step-content step-1">
							<h2 class="checkout-step-title">Contact & Billing</h2>

							<?php do_action( 'woocommerce_before_checkout_form' ); ?>

							<div class="form-row">
								<label class="checkout-label" for="billing_email">Email Address *</label>
								<input type="email" class="input-text" name="billing_email" id="billing_email" placeholder="your@email.com" value="<?php echo esc_attr( $checkout->get_value( 'billing_email' ) ); ?>" autocomplete="email" required>
							</div>

							<div class="form-row form-row-split">
								<div>
									<label class="checkout-label" for="billing_first_name">First Name *</label>
									<input type="text" class="input-text" name="billing_first_name" id="billing_first_name" placeholder="First name" value="<?php echo esc_attr( $checkout->get_value( 'billing_first_name' ) ); ?>" autocomplete="given-name" required>
								</div>
								<div>
									<label class="checkout-label" for="billing_last_name">Last Name *</label>
									<input type="text" class="input-text" name="billing_last_name" id="billing_last_name" placeholder="Last name" value="<?php echo esc_attr( $checkout->get_value( 'billing_last_name' ) ); ?>" autocomplete="family-name" required>
								</div>
							</div>

							<div class="form-row">
								<label class="checkout-label" for="billing_address_1">Address *</label>
								<input type="text" class="input-text" name="billing_address_1" id="billing_address_1" placeholder="Street address" value="<?php echo esc_attr( $checkout->get_value( 'billing_address_1' ) ); ?>" autocomplete="address-line1" required>
							</div>

							<div class="form-row">
								<label class="checkout-label" for="billing_address_2">Apartment, suite, etc. (optional)</label>
								<input type="text" class="input-text" name="billing_address_2" id="billing_address_2" placeholder="Apt, suite, unit" value="<?php echo esc_attr( $checkout->get_value( 'billing_address_2' ) ); ?>" autocomplete="address-line2">
							</div>

							<div class="form-row form-row-split">
								<div>
									<label class="checkout-label" for="billing_city">City *</label>
									<input type="text" class="input-text" name="billing_city" id="billing_city" placeholder="City" value="<?php echo esc_attr( $checkout->get_value( 'billing_city' ) ); ?>" autocomplete="address-level2" required>
								</div>
								<div>
									<label class="checkout-label" for="billing_postcode">ZIP / Postal Code *</label>
									<input type="text" class="input-text" name="billing_postcode" id="billing_postcode" placeholder="ZIP code" value="<?php echo esc_attr( $checkout->get_value( 'billing_postcode' ) ); ?>" autocomplete="postal-code" required>
								</div>
							</div>

							<div class="form-row">
								<label class="checkout-label" for="billing_phone">Phone *</label>
								<input type="tel" class="input-text" name="billing_phone" id="billing_phone" placeholder="Phone number" value="<?php echo esc_attr( $checkout->get_value( 'billing_phone' ) ); ?>" autocomplete="tel" required>
							</div>

							<div class="next-btn-wrap">
								<button type="button" class="btn btn-primary btn-full next-btn">Continue to Shipping</button>
							</div>
						</div>

						<!-- Step 2: Shipping -->
						<div class="step-content step-2 d-none">
							<h2 class="checkout-step-title">Shipping Method</h2>

							<?php
							if ( WC()->cart->needs_shipping() ) {
								woocommerce_checkout_shipping();
							}
							?>

							<?php if ( WC()->cart->needs_shipping_address() ) : ?>
								<h3 class="checkout-step-title">Shipping Address</h3>
								<div class="form-row form-row-split">
									<div>
										<label class="checkout-label" for="shipping_first_name">First Name *</label>
										<input type="text" class="input-text" name="shipping_first_name" id="shipping_first_name" placeholder="First name" value="<?php echo esc_attr( $checkout->get_value( 'shipping_first_name' ) ); ?>" autocomplete="given-name" required>
									</div>
									<div>
										<label class="checkout-label" for="shipping_last_name">Last Name *</label>
										<input type="text" class="input-text" name="shipping_last_name" id="shipping_last_name" placeholder="Last name" value="<?php echo esc_attr( $checkout->get_value( 'shipping_last_name' ) ); ?>" autocomplete="family-name" required>
									</div>
								</div>
								<div class="form-row">
									<label class="checkout-label" for="shipping_address_1">Address *</label>
									<input type="text" class="input-text" name="shipping_address_1" id="shipping_address_1" placeholder="Street address" value="<?php echo esc_attr( $checkout->get_value( 'shipping_address_1' ) ); ?>" autocomplete="address-line1" required>
								</div>
								<div class="form-row form-row-split">
									<div>
										<label class="checkout-label" for="shipping_city">City *</label>
										<input type="text" class="input-text" name="shipping_city" id="shipping_city" placeholder="City" value="<?php echo esc_attr( $checkout->get_value( 'shipping_city' ) ); ?>" autocomplete="address-level2" required>
									</div>
									<div>
										<label class="checkout-label" for="shipping_postcode">ZIP / Postal Code *</label>
										<input type="text" class="input-text" name="shipping_postcode" id="shipping_postcode" placeholder="ZIP code" value="<?php echo esc_attr( $checkout->get_value( 'shipping_postcode' ) ); ?>" autocomplete="postal-code" required>
									</div>
								</div>
							<?php endif; ?>

							<div class="next-btn-wrap">
								<button type="button" class="btn btn-outline btn-sm prev-btn"><i class="fas fa-arrow-left"></i> Back</button>
								<button type="button" class="btn btn-primary btn-full next-btn">Continue to Payment</button>
							</div>
						</div>

						<!-- Step 3: Payment -->
						<div class="step-content step-3 d-none">
							<h2 class="checkout-step-title">Payment</h2>

							<?php
							if ( is_user_logged_in() ) {
								$user = wp_get_current_user();
								$order_notes_text = $user->billing_first_name . ' ' . $user->billing_last_name;
							} else {
								$order_notes_text = '';
							}
							?>

							<?php wc_get_template( 'checkout/payment.php' ); ?>

							<div class="form-row" id="order_comments">
								<label class="checkout-label" for="order_comments">Order Notes (optional)</label>
								<textarea class="input-text" name="order_comments" id="order_comments" rows="3" placeholder="Notes about your order, e.g. special notes for delivery."></textarea>
							</div>

							<div class="next-btn-wrap">
								<button type="button" class="btn btn-outline btn-sm prev-btn"><i class="fas fa-arrow-left"></i> Back</button>
								<button type="submit" class="btn btn-primary btn-lg btn-full place-order-btn" id="placeOrderBtn" name="woocommerce_checkout_place_order" value="<?php echo esc_attr( $checkout->get_checkout_button_text() ); ?>">
									<?php echo esc_html( $checkout->get_checkout_button_text() ); ?> <i class="fas fa-lock"></i>
								</button>
							</div>
						</div>

						<?php do_action( 'woocommerce_after_checkout_form' ); ?>
					</form>
				</div>

				<!-- Order Summary Sidebar -->
				<div class="checkout-sidebar">
					<h3 class="checkout-summary-title">Your Order</h3>
					<div class="checkout-order-summary">
						<?php
						foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
							$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
							if ( ! $_product ) {
								continue;
							}
							?>
							<div class="checkout-item">
								<div class="checkout-item-image">
									<?php echo wp_kses_post( $_product->get_image( 'woocommerce_thumbnail' ) ); ?>
									<span class="checkout-item-count"><?php echo esc_html( $cart_item['quantity'] ); ?></span>
								</div>
								<div class="checkout-item-info">
									<h4 class="checkout-item-name"><?php echo esc_html( $_product->get_name() ); ?></h4>
									<span class="checkout-item-price"><?php echo wp_kses_post( wc_get_cart_item_data_display_subtotal( $cart_item ) ); ?></span>
								</div>
							</div>
							<?php
						}
						?>
					</div>

					<div class="checkout-totals">
						<div class="cart-summary-row">
							<span>Subtotal</span>
							<span><?php echo wp_kses_post( WC()->cart->get_cart_subtotal() ); ?></span>
						</div>
						<?php if ( WC()->cart->needs_shipping() ) : ?>
							<div class="cart-summary-row">
								<span>Shipping</span>
								<span><?php echo wp_kses_post( WC()->cart->get_shipping_to_display() ); ?></span>
							</div>
						<?php endif; ?>
						<div class="cart-summary-row cart-total">
							<span>Total</span>
							<span><?php echo wp_kses_post( WC()->cart->get_total_display_price() ); ?></span>
						</div>
					</div>

					<div class="checkout-trust">
						<p><i class="fas fa-lock"></i> Secure 256-bit SSL encryption</p>
						<p><i class="fas fa-undo"></i> 30-day free returns</p>
					</div>
				</div>
			</div>
		<?php else : ?>
			<div class="cart-empty">
				<i class="fas fa-shopping-bag"></i>
				<h3>Your bag is empty</h3>
				<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="btn btn-primary">Start Shopping</a>
			</div>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer( 'shop' );
