<?php
/**
 * Ferm Living Cart Page Template
 *
 * Overrides WooCommerce cart page. Renders frozen source DOM structure
 * with WooCommerce cart data via AJAX for real-time updates.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$cart = WC()->cart;
$cart_items = $cart->get_cart();
$cart_empty = empty( $cart_items );
?>

<main class="content" id="main-content">
	<section class="headspace">
		<div class="ferm-cart" data-ferm-cart>
			<div class="limit">
				<div class="mb-4 pt-4 md:mb-8 md:pt-8">
					<h1 class="ferm-cart__heading">My Cart</h1>
				</div>

				<?php if ( $cart_empty ) : ?>

					<p class="ferm-cart__empty" data-ferm-cart-empty>
						<?php esc_html_e( 'No items', 'aureon' ); ?>
					</p>

				<?php else : ?>

					<div class="ferm-cart__items" data-ferm-cart-items>
						<?php foreach ( $cart_items as $cart_item_key => $cart_item ) :
							$product = $cart_item['data'];
							$product_id = $cart_item['product_id'];
							$quantity = $cart_item['quantity'];
							$image_url = wp_get_attachment_image_url( $product->get_image_id(), 'woocommerce_thumbnail' );
							$permalink = get_permalink( $product_id );
							$variation_data = isset( $cart_item['variation'] ) ? $cart_item['variation'] : array();
							$line_total = $cart->get_item_data( $cart_item )['line_total'] ?? $product->get_price() * $quantity;

							/* Build variant string */
							$variant_parts = array();
							if ( ! empty( $variation_data ) ) {
								foreach ( $variation_data as $attr_key => $attr_val ) {
									$label = wc_attribute_label( str_replace( 'attribute_', '', $attr_key ) );
									$variant_parts[] = $label . ': ' . $attr_val;
								}
							}
							$variant_str = implode( ' / ', $variant_parts );
						?>
							<div class="ferm-cart__item" data-ferm-cart-item="<?php echo esc_attr( $cart_item_key ); ?>">
								<a href="<?php echo esc_url( $permalink ); ?>" class="ferm-cart__item-image-link">
									<?php if ( $image_url ) : ?>
										<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $product->get_name() ); ?>" class="ferm-cart__item-image" loading="lazy">
									<?php else : ?>
										<div class="ferm-cart__item-image"></div>
									<?php endif; ?>
								</a>
								<div class="ferm-cart__item-details">
									<p class="ferm-cart__item-name">
										<a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $product->get_name() ); ?></a>
									</p>
									<?php if ( $variant_str ) : ?>
										<p class="ferm-cart__item-variant"><?php echo esc_html( $variant_str ); ?></p>
									<?php endif; ?>
									<p class="ferm-cart__item-price"><?php echo wp_kses_post( WC()->cart->get_item_data( $cart_item )['line_subtotal'] ?? wc_price( $product->get_price() ) ); ?></p>
									<div class="ferm-cart__item-qty">
										<button type="button" class="ferm-cart__qty-minus" data-action="minus" aria-label="<?php esc_attr_e( 'Decrease quantity', 'aureon' ); ?>">-</button>
										<input type="number" class="ferm-cart__qty-input" value="<?php echo esc_attr( $quantity ); ?>" min="1" max="<?php echo esc_attr( $product->get_max_purchase_quantity() > 0 ? $product->get_max_purchase_quantity() : '' ); ?>" data-cart-item-key="<?php echo esc_attr( $cart_item_key ); ?>" aria-label="<?php esc_attr_e( 'Quantity', 'aureon' ); ?>">
										<button type="button" class="ferm-cart__qty-plus" data-action="plus" aria-label="<?php esc_attr_e( 'Increase quantity', 'aureon' ); ?>">+</button>
									</div>
									<button type="button" class="ferm-cart__item-remove" data-action="remove" data-cart-item-key="<?php echo esc_attr( $cart_item_key ); ?>" aria-label="<?php esc_attr_e( 'Remove item', 'aureon' ); ?>">
										<?php esc_html_e( 'Remove', 'aureon' ); ?>
									</button>
								</div>
							</div>
						<?php endforeach; ?>
					</div>

					<!-- Coupon -->
					<div class="ferm-cart__coupon">
						<input type="text" class="ferm-cart__coupon-input" placeholder="<?php esc_attr_e( 'Coupon code', 'aureon' ); ?>" data-ferm-coupon-input>
						<button type="button" class="ferm-cart__coupon-apply" data-ferm-coupon-apply><?php esc_html_e( 'Apply', 'aureon' ); ?></button>
					</div>

					<!-- Summary -->
					<div class="ferm-cart__summary" data-ferm-cart-summary>
						<div class="ferm-cart__row">
							<span><?php esc_html_e( 'Subtotal', 'aureon' ); ?></span>
							<span data-ferm-cart-subtotal><?php echo wp_kses_post( WC()->cart->get_cart_subtotal() ); ?></span>
						</div>
						<?php if ( WC()->cart->get_shipping_total() > 0 ) : ?>
							<div class="ferm-cart__row">
								<span><?php esc_html_e( 'Shipping', 'aureon' ); ?></span>
								<span data-ferm-cart-shipping><?php echo wp_kses_post( WC()->cart->get_shipping_to_display() ); ?></span>
							</div>
						<?php else : ?>
							<div class="ferm-cart__row">
								<span><?php esc_html_e( 'Shipping', 'aureon' ); ?></span>
								<span><?php esc_html_e( 'Calculated at checkout', 'aureon' ); ?></span>
							</div>
						<?php endif; ?>
						<div class="ferm-cart__row ferm-cart__row--total">
							<span><?php esc_html_e( 'Total', 'aureon' ); ?></span>
							<span data-ferm-cart-total><?php echo wp_kses_post( WC()->cart->get_cart_total() ); ?></span>
						</div>
						<p class="ferm-cart__note">
							<?php esc_html_e( 'Tax included. Shipping calculated at checkout.', 'aureon' ); ?>
						</p>

						<div class="ferm-cart__actions">
							<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="ferm-cart__btn-checkout">
								<?php esc_html_e( 'Checkout', 'aureon' ); ?>
							</a>
							<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="ferm-cart__btn-continue">
								<?php esc_html_e( 'Continue shopping', 'aureon' ); ?>
							</a>
						</div>
					</div>

				<?php endif; ?>

			</div>
		</div>
	</section>
</main>

<?php
/* AJAX nonce for cart operations */
wp_localize_script( 'ferm-commerce', 'fermCartData', array(
	'ajax_url' => admin_url( 'admin-ajax.php' ),
	'nonce'    => wp_create_nonce( 'ferm_cart_nonce' ),
	'cart_url' => wc_get_cart_url(),
	'i18n'     => array(
		'remove_confirm' => __( 'Are you sure you want to remove this item?', 'aureon' ),
		'updating'       => __( 'Updating...', 'aureon' ),
	),
) );

get_footer();
