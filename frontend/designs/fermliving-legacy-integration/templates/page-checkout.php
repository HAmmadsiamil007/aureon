<?php
/**
 * Ferm Living Checkout Page Template
 *
 * Overrides WooCommerce checkout. Renders frozen source DOM structure
 * with WooCommerce checkout fields and order processing.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'WC' ) || WC()->cart->is_empty() ) {
	wp_safe_redirect( wc_get_cart_url() );
	exit;
}

get_header();

$cart = WC()->cart;
$customer = WC()->checkout()->get_checkout_fields();
?>

<main class="content" id="main-content">
	<section class="headspace">
		<div class="ferm-checkout" data-ferm-checkout>
			<div class="limit">

				<?php wc_print_notices(); ?>

				<h1 class="ferm-checkout__heading">Checkout</h1>

				<form name="checkout" class="ferm-checkout__form" method="post" action="<?php echo esc_url( WC()->checkout()->get_checkout_action() ); ?>" id="ferm-checkout-form">

					<div class="ferm-checkout__grid">

						<!-- Left: Billing & Shipping Forms -->
						<div class="ferm-checkout__form-fields">

							<!-- Billing details -->
							<div class="ferm-checkout__section">
								<h2 class="ferm-checkout__section-title"><?php esc_html_e( 'Billing details', 'aureon' ); ?></h2>

								<?php
								$fields = WC()->checkout()->get_checkout_fields( 'billing' );
								foreach ( $fields as $key => $field ) :
									$required = ! empty( $field['required'] ) ? ' required' : '';
									$type = isset( $field['type'] ) ? $field['type'] : 'text';
									$value = WC()->checkout()->get_value( $key );
									$class_row = '';
									if ( 'billing_first_name' === $key || 'billing_last_name' === $key ) {
										if ( 'billing_first_name' === $key ) {
											$class_row = ' ferm-checkout__field--row-start';
										}
										$wrapper_start = 'billing_first_name' === $key;
										$wrapper_end = 'billing_last_name' === $key;
									} else {
										$wrapper_start = false;
										$wrapper_end = false;
									}
								?>
									<?php if ( $wrapper_start ) : ?>
										<div class="ferm-checkout__field ferm-checkout__field--row">
									<?php endif; ?>

									<?php if ( 'select' === $type && ! empty( $field['options'] ) ) : ?>
										<div class="ferm-checkout__field">
											<label for="<?php echo esc_attr( $key ); ?>"><?php echo wp_kses_post( $field['label'] ); ?></label>
											<select id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>"<?php echo $required; ?>>
												<?php foreach ( $field['options'] as $opt_val => $opt_label ) : ?>
													<option value="<?php echo esc_attr( $opt_val ); ?>" <?php selected( $value, $opt_val ); ?>><?php echo esc_html( $opt_label ); ?></option>
												<?php endforeach; ?>
											</select>
										</div>
									<?php elseif ( 'textarea' === $type ) : ?>
										<div class="ferm-checkout__field">
											<label for="<?php echo esc_attr( $key ); ?>"><?php echo wp_kses_post( $field['label'] ); ?></label>
											<textarea id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>" rows="3"<?php echo $required; ?>><?php echo esc_textarea( $value ); ?></textarea>
										</div>
									<?php else : ?>
										<div class="ferm-checkout__field">
											<label for="<?php echo esc_attr( $key ); ?>"><?php echo wp_kses_post( $field['label'] ); ?></label>
											<input type="<?php echo esc_attr( $type ); ?>" id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $value ); ?>"<?php echo $required; ?>>
										</div>
									<?php endif; ?>

									<?php if ( $wrapper_end ) : ?>
										</div>
									<?php endif; ?>
								<?php endforeach; ?>
							</div>

							<!-- Shipping details -->
							<?php if ( WC()->cart->needs_shipping_address() ) : ?>
								<div class="ferm-checkout__section">
									<h2 class="ferm-checkout__section-title"><?php esc_html_e( 'Shipping details', 'aureon' ); ?></h2>

									<label class="ferm-checkout__radio" style="margin-bottom: 24px;">
										<input type="checkbox" name="ship_to_different_address" value="1" id="ship-to-different-address" <?php checked( WC()->checkout()->ship_to_different_address(), true ); ?>>
										<span class="ferm-checkout__radio-label"><?php esc_html_e( 'Ship to a different address?', 'aureon' ); ?></span>
									</label>

									<div id="ship-to-different-address-wrap" style="display: <?php echo WC()->checkout()->ship_to_different_address() ? 'block' : 'none'; ?>;">
										<?php
										$shipping_fields = WC()->checkout()->get_checkout_fields( 'shipping' );
										foreach ( $shipping_fields as $key => $field ) :
											$required = ! empty( $field['required'] ) ? ' required' : '';
											$type = isset( $field['type'] ) ? $field['type'] : 'text';
											$value = WC()->checkout()->get_value( $key );
											$wrapper_start = 'shipping_first_name' === $key;
											$wrapper_end = 'shipping_last_name' === $key;
										?>
											<?php if ( $wrapper_start ) : ?>
												<div class="ferm-checkout__field ferm-checkout__field--row">
											<?php endif; ?>

											<?php if ( 'select' === $type && ! empty( $field['options'] ) ) : ?>
												<div class="ferm-checkout__field">
													<label for="<?php echo esc_attr( $key ); ?>"><?php echo wp_kses_post( $field['label'] ); ?></label>
													<select id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>"<?php echo $required; ?>>
														<?php foreach ( $field['options'] as $opt_val => $opt_label ) : ?>
															<option value="<?php echo esc_attr( $opt_val ); ?>" <?php selected( $value, $opt_val ); ?>><?php echo esc_html( $opt_label ); ?></option>
														<?php endforeach; ?>
													</select>
												</div>
											<?php else : ?>
												<div class="ferm-checkout__field">
													<label for="<?php echo esc_attr( $key ); ?>"><?php echo wp_kses_post( $field['label'] ); ?></label>
													<input type="<?php echo esc_attr( $type ); ?>" id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $value ); ?>"<?php echo $required; ?>>
												</div>
											<?php endif; ?>

											<?php if ( $wrapper_end ) : ?>
												</div>
											<?php endif; ?>
										<?php endforeach; ?>
									</div>
								</div>
							<?php endif; ?>

							<!-- Order notes -->
							<?php if ( wc_cart_coupons_enabled() || wc_help_text_has_options( 'order_comments' ) ) : ?>
								<div class="ferm-checkout__section">
									<h2 class="ferm-checkout__section-title"><?php esc_html_e( 'Additional information', 'aureon' ); ?></h2>
									<div class="ferm-checkout__field">
										<label for="order_comments"><?php esc_html_e( 'Order notes', 'aureon' ); ?></label>
										<textarea name="order_comments" id="order_comments" rows="3" placeholder="<?php esc_attr_e( 'Notes about your order, e.g. special notes for delivery.', 'aureon' ); ?>"></textarea>
									</div>
								</div>
							<?php endif; ?>

						</div>

						<!-- Right: Order Summary -->
						<div class="ferm-checkout__sidebar">
							<div class="ferm-checkout__summary">
								<h2 class="ferm-checkout__summary-title"><?php esc_html_e( 'Your order', 'aureon' ); ?></h2>

								<?php foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) :
									$product = $cart_item['data'];
									$image_url = wp_get_attachment_image_url( $product->get_image_id(), 'woocommerce_thumbnail' );
									$permalink = get_permalink( $cart_item['product_id'] );
									$quantity = $cart_item['quantity'];
									$line_total = $cart->get_item_data( $cart_item )['line_total'] ?? $product->get_price() * $quantity;
								?>
									<div class="ferm-checkout__summary-item">
										<a href="<?php echo esc_url( $permalink ); ?>">
											<?php if ( $image_url ) : ?>
												<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $product->get_name() ); ?>" class="ferm-checkout__summary-item-image" loading="lazy">
											<?php else : ?>
												<div class="ferm-checkout__summary-item-image"></div>
											<?php endif; ?>
										</a>
										<div class="ferm-checkout__summary-item-name">
											<?php echo esc_html( $product->get_name() ); ?>
											<?php if ( $quantity > 1 ) : ?>
												<span style="color: var(--aureon-color-muted);"> x <?php echo esc_html( $quantity ); ?></span>
											<?php endif; ?>
										</div>
										<div class="ferm-checkout__summary-item-price">
											<?php echo wp_kses_post( wc_price( $line_total ) ); ?>
										</div>
									</div>
								<?php endforeach; ?>

								<div class="ferm-checkout__summary-totals">
									<div class="ferm-cart__row">
										<span><?php esc_html_e( 'Subtotal', 'aureon' ); ?></span>
										<span><?php echo wp_kses_post( $cart->get_cart_subtotal() ); ?></span>
									</div>
									<div class="ferm-cart__row">
										<span><?php esc_html_e( 'Shipping', 'aureon' ); ?></span>
										<span><?php echo wp_kses_post( $cart->get_shipping_to_display() ); ?></span>
									</div>
									<?php if ( $cart->get_total_tax() > 0 ) : ?>
										<div class="ferm-cart__row">
											<span><?php esc_html_e( 'Tax', 'aureon' ); ?></span>
											<span><?php echo wp_kses_post( wc_price( $cart->get_total_tax() ) ); ?></span>
										</div>
									<?php endif; ?>
									<div class="ferm-cart__row ferm-cart__row--total">
										<span><?php esc_html_e( 'Total', 'aureon' ); ?></span>
										<span><?php echo wp_kses_post( $cart->get_total() ); ?></span>
									</div>
								</div>

								<button type="submit" class="ferm-checkout__place-order" name="woocommerce_checkout_place_order" id="place_order" value="<?php esc_attr_e( 'Place order', 'aureon' ); ?>">
									<?php esc_html_e( 'Place order', 'aureon' ); ?>
								</button>
							</div>
						</div>

					</div>

					<?php wp_nonce_field( 'woocommerce-checkout', 'woocommerce-process-checkout-nonce' ); ?>
					<?php do_action( 'woocommerce_checkout_terms_and_conditions' ); ?>
				</form>
			</div>
		</div>
	</section>
</main>

<?php
wp_localize_script( 'ferm-commerce', 'fermCheckoutData', array(
	'ajax_url' => admin_url( 'admin-ajax.php' ),
	'nonce'    => wp_create_nonce( 'ferm_checkout_nonce' ),
) );

get_footer();
