<?php
/**
 * WooCommerce Checkout Template — Vineta Standalone
 *
 * Self-contained checkout page with Vineta design. Does NOT use get_header()
 * / get_footer() because the AETHER shell is stripped of CSS on complete-page
 * designs. Instead, includes Vineta CSS directly and renders its own header
 * and footer markup.
 *
 * @package Aureon
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$pack_url = function_exists( 'aether_pack_url' ) ? aether_pack_url() : '';
$site_url = home_url( '/' );
$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
$cart_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'cart' ) : home_url( '/cart/' );
$account_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/my-account/' );
$checkout_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'checkout' ) : home_url( '/checkout/' );

$menu_items = wp_get_nav_menu_items( 'primary' );
if ( ! $menu_items ) {
	$menu_items = array();
}

$countries = array();
if ( function_exists( 'WC' ) && WC()->countries ) {
	$countries = WC()->countries->get_countries();
}
$checkout = WC()->checkout;
$fields   = $checkout ? $checkout->get_checkout_fields() : array();
$billing  = isset( $fields['billing'] ) ? $fields['billing'] : array();
$order_f  = isset( $fields['order'] ) ? $fields['order'] : array();
$terms    = isset( $order_f['terms'] ) ? $order_f['terms'] : array();
$gateways = function_exists( 'WC' ) && WC()->payment_gateways ? WC()->payment_gateways->get_available_payment_gateways() : array();
$cart_count = function_exists( 'WC' ) && WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php wp_title( '|', true, 'right' ); ?></title>
<?php wp_head(); ?>
<?php if ( $pack_url ) : ?>
<link rel="stylesheet" href="<?php echo esc_url( $pack_url ); ?>css/bootstrap.min.css">
<link rel="stylesheet" href="<?php echo esc_url( $pack_url ); ?>css/styles.css">
<link rel="stylesheet" href="<?php echo esc_url( $pack_url ); ?>fonts/fonts.css">
<link rel="stylesheet" href="<?php echo esc_url( $pack_url ); ?>fonts/font-icons.css">
<?php endif; ?>
<style>
/* === RESET & BASE === */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: 'Cabinet Grotesk', 'Satoshi', 'Poppins', sans-serif; color: #111; background: #fff; -webkit-font-smoothing: antialiased; }
a { text-decoration: none; color: inherit; }
ul, ol { list-style: none; }
img { max-width: 100%; height: auto; }

/* === HEADER === */
.vt-header { background: #fff; border-bottom: 1px solid #f0f0f0; position: sticky; top: 0; z-index: 1000; }
.vt-header-inner { display: flex; align-items: center; justify-content: space-between; max-width: 1400px; margin: 0 auto; padding: 0 24px; height: 72px; }
.vt-logo { font-size: 24px; font-weight: 700; letter-spacing: -0.5px; color: #111; }
.vt-nav { display: flex; gap: 28px; align-items: center; }
.vt-nav a { font-size: 14px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px; color: #333; transition: color 0.2s; }
.vt-nav a:hover { color: #999; }
.vt-header-actions { display: flex; gap: 16px; align-items: center; }
.vt-header-actions a { color: #333; font-size: 14px; font-weight: 500; display: flex; align-items: center; gap: 4px; }
.vt-header-actions a:hover { color: #999; }
.vt-cart-badge { background: #111; color: #fff; font-size: 11px; font-weight: 600; width: 20px; height: 20px; border-radius: 50%; display: flex; align-items: center; justify-content: center; }

/* === PAGE TITLE === */
.vt-page-title { background: #f8f8f8; padding: 48px 0 32px; text-align: center; }
.vt-page-title h1 { font-size: 32px; font-weight: 600; margin-bottom: 12px; }
.vt-breadcrumb { display: flex; gap: 8px; justify-content: center; font-size: 14px; color: #888; }
.vt-breadcrumb a { color: #888; }
.vt-breadcrumb a:hover { color: #111; }
.vt-breadcrumb span { color: #ccc; }

/* === CHECKOUT LAYOUT === */
.vt-checkout-section { padding: 64px 0 100px; }
.vt-checkout-grid { display: grid; grid-template-columns: 1fr 400px; gap: 40px; max-width: 1200px; margin: 0 auto; padding: 0 24px; }

/* === FORM CARD === */
.vt-form-card { background: #fff; border: 1px solid #ebebeb; border-radius: 16px; padding: 32px; }
.vt-form-section { margin-bottom: 32px; }
.vt-form-section:last-child { margin-bottom: 0; }
.vt-form-section h3 { font-size: 18px; font-weight: 600; margin-bottom: 20px; color: #111; }

/* === FORM FIELDS === */
.vt-field-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.vt-field { margin-bottom: 16px; }
.vt-field label { display: block; font-size: 13px; font-weight: 500; color: #333; margin-bottom: 6px; }
.vt-field label .req { color: #111; }
.vt-field input, .vt-field select, .vt-field textarea {
	width: 100%; padding: 12px 16px; border: 1px solid #e0e0e0; border-radius: 8px;
	font-size: 14px; font-family: inherit; color: #111; background: #fff;
	transition: border-color 0.2s; outline: none;
}
.vt-field input:focus, .vt-field select:focus { border-color: #111; }
.vt-field input::placeholder { color: #aaa; }

/* === PAYMENT === */
.vt-payment-method { border: 1px solid #ebebeb; border-radius: 8px; margin-bottom: 12px; overflow: hidden; }
.vt-payment-header { display: flex; align-items: center; gap: 10px; padding: 14px 16px; cursor: pointer; }
.vt-payment-header input[type="radio"] { accent-color: #111; width: 16px; height: 16px; }
.vt-payment-header span { font-size: 14px; font-weight: 500; color: #333; }
.vt-payment-body { padding: 0 16px 16px; background: #fafafa; border-top: 1px solid #f0f0f0; }

/* === ORDER SUMMARY === */
.vt-order-summary { position: sticky; top: 96px; }
.vt-order-card { background: #fafafa; border: 1px solid #ebebeb; border-radius: 16px; padding: 24px; }
.vt-order-card h3 { font-size: 18px; font-weight: 600; margin-bottom: 20px; color: #111; }
.vt-order-item { display: flex; gap: 14px; align-items: center; padding: 14px 0; border-bottom: 1px solid #f0f0f0; }
.vt-order-item:last-of-type { border-bottom: none; }
.vt-order-item-img { width: 72px; height: 94px; border-radius: 8px; overflow: hidden; flex-shrink: 0; position: relative; background: #eee; }
.vt-order-item-img img { width: 100%; height: 100%; object-fit: cover; }
.vt-order-item-qty { position: absolute; top: -6px; right: -6px; width: 22px; height: 22px; background: #111; color: #fff; font-size: 11px; font-weight: 600; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
.vt-order-item-info { flex: 1; }
.vt-order-item-name { font-size: 14px; font-weight: 500; color: #111; margin-bottom: 2px; }
.vt-order-item-variant { font-size: 12px; color: #888; }
.vt-order-item-price { font-size: 14px; font-weight: 600; color: #111; white-space: nowrap; }
.vt-order-totals { padding-top: 16px; border-top: 1px solid #e0e0e0; margin-top: 8px; }
.vt-order-total-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 14px; color: #555; }
.vt-order-total-row.grand { font-size: 16px; font-weight: 600; color: #111; padding-top: 12px; border-top: 1px solid #e0e0e0; margin-top: 8px; }

/* === BUTTONS === */
.vt-btn-order { display: block; width: 100%; padding: 16px 32px; background: #111; color: #fff; border: none; border-radius: 99px; font-size: 16px; font-weight: 600; font-family: inherit; cursor: pointer; transition: background 0.2s; margin-top: 20px; text-align: center; }
.vt-btn-order:hover { background: #333; }
.vt-secure-note { text-align: center; font-size: 12px; color: #888; margin-top: 12px; }

/* === FOOTER === */
.vt-footer { background: #111; color: #fff; padding: 64px 0 0; }
.vt-footer-grid { display: grid; grid-template-columns: 1.5fr 1fr 1fr; gap: 48px; max-width: 1200px; margin: 0 auto; padding: 0 24px; }
.vt-footer h4 { font-size: 16px; font-weight: 600; margin-bottom: 20px; }
.vt-footer p { font-size: 14px; color: #999; line-height: 1.6; margin-bottom: 16px; }
.vt-footer a { color: #999; font-size: 14px; transition: color 0.2s; }
.vt-footer a:hover { color: #fff; }
.vt-footer-links { display: flex; flex-direction: column; gap: 10px; }
.vt-footer-bottom { border-top: 1px solid rgba(255,255,255,0.1); padding: 24px 0; margin-top: 48px; text-align: center; font-size: 13px; color: #666; }

/* === NOTICES === */
.vt-notices { margin-bottom: 24px; }
.vt-notice { padding: 12px 16px; border-radius: 8px; margin-bottom: 8px; font-size: 14px; }
.vt-notice-info { background: #f0f7ff; border: 1px solid #cce0ff; color: #0066cc; }
.vt-notice-error { background: #fff5f5; border: 1px solid #ffcccc; color: #cc0000; }
.vt-notice-success { background: #f0fff4; border: 1px solid #ccffe0; color: #006600; }

/* === RESPONSIVE === */
@media (max-width: 991px) {
	.vt-checkout-grid { grid-template-columns: 1fr; }
	.vt-order-summary { order: -1; }
	.vt-nav { display: none; }
}
@media (max-width: 575px) {
	.vt-field-grid { grid-template-columns: 1fr; }
	.vt-form-card { padding: 20px; }
	.vt-header-inner { padding: 0 16px; height: 60px; }
	.vt-footer-grid { grid-template-columns: 1fr; gap: 32px; }
}
</style>
</head>
<body <?php body_class( 'woocommerce-checkout-page' ); ?>>
<?php wp_body_open(); ?>

<!-- HEADER -->
<header class="vt-header">
	<div class="vt-header-inner">
		<a href="<?php echo esc_url( $site_url ); ?>" class="vt-logo">AUREON</a>
		<nav class="vt-nav">
			<?php foreach ( $menu_items as $item ) : ?>
				<a href="<?php echo esc_url( $item->url ); ?>"><?php echo esc_html( $item->title ); ?></a>
			<?php endforeach; ?>
		</nav>
		<div class="vt-header-actions">
			<a href="<?php echo esc_url( $account_url ); ?>"><i class="far fa-user"></i></a>
			<a href="<?php echo esc_url( $cart_url ); ?>">
				<i class="fas fa-shopping-bag"></i>
				<?php if ( $cart_count > 0 ) : ?>
					<span class="vt-cart-badge"><?php echo esc_html( $cart_count ); ?></span>
				<?php endif; ?>
			</a>
		</div>
	</div>
</header>

<!-- PAGE TITLE -->
<div class="vt-page-title">
	<h1><?php esc_html_e( 'Checkout', 'woocommerce' ); ?></h1>
	<div class="vt-breadcrumb">
		<a href="<?php echo esc_url( $site_url ); ?>"><?php esc_html_e( 'Home', 'woocommerce' ); ?></a>
		<span>/</span>
		<a href="<?php echo esc_url( $cart_url ); ?>"><?php esc_html_e( 'Cart', 'woocommerce' ); ?></a>
		<span>/</span>
		<strong><?php esc_html_e( 'Checkout', 'woocommerce' ); ?></strong>
	</div>
</div>

<!-- CHECKOUT -->
<section class="vt-checkout-section">
	<?php if ( function_exists( 'wc_print_notices' ) ) { wc_print_notices(); } ?>
	<form name="checkout" class="checkout woocommerce-checkout" action="<?php echo esc_url( $checkout_url ); ?>" method="post" enctype="multipart/form-data">
		<?php wp_nonce_field( 'woocommerce-process-checkout', 'woocommerce-process-checkout-nonce' ); ?>
		<input type="hidden" name="ship_to_different_address" value="0">

		<div class="vt-checkout-grid">
			<!-- LEFT: FORM -->
			<div class="vt-form-card">

				<!-- CONTACT -->
				<div class="vt-form-section">
					<h3><?php esc_html_e( 'Contact', 'woocommerce' ); ?></h3>
					<div class="vt-field-grid">
						<?php if ( isset( $billing['billing_email'] ) ) : ?>
							<?php $f = $billing['billing_email']; $v = $checkout->get_value( 'billing_email' ); ?>
							<div class="vt-field">
								<label for="billing_email"><?php esc_html_e( 'Email address', 'woocommerce' ); ?> <span class="req">*</span></label>
								<input type="email" id="billing_email" name="billing_email" value="<?php echo esc_attr( $v ); ?>" placeholder="you@example.com" autocomplete="email" required>
							</div>
						<?php endif; ?>
						<?php if ( isset( $billing['billing_phone'] ) ) : ?>
							<?php $f = $billing['billing_phone']; $v = $checkout->get_value( 'billing_phone' ); ?>
							<div class="vt-field">
								<label for="billing_phone"><?php esc_html_e( 'Phone', 'woocommerce' ); ?> <span class="req">*</span></label>
								<input type="tel" id="billing_phone" name="billing_phone" value="<?php echo esc_attr( $v ); ?>" placeholder="+92 300 1234567" autocomplete="tel" required>
							</div>
						<?php endif; ?>
					</div>
				</div>

				<!-- SHIPPING -->
				<div class="vt-form-section">
					<h3><?php esc_html_e( 'Shipping', 'woocommerce' ); ?></h3>
					<div class="vt-field-grid">
						<?php if ( isset( $billing['billing_first_name'] ) ) : ?>
							<?php $v = $checkout->get_value( 'billing_first_name' ); ?>
							<div class="vt-field">
								<label for="billing_first_name"><?php esc_html_e( 'First name', 'woocommerce' ); ?> <span class="req">*</span></label>
								<input type="text" id="billing_first_name" name="billing_first_name" value="<?php echo esc_attr( $v ); ?>" autocomplete="given-name" required>
							</div>
						<?php endif; ?>
						<?php if ( isset( $billing['billing_last_name'] ) ) : ?>
							<?php $v = $checkout->get_value( 'billing_last_name' ); ?>
							<div class="vt-field">
								<label for="billing_last_name"><?php esc_html_e( 'Last name', 'woocommerce' ); ?> <span class="req">*</span></label>
								<input type="text" id="billing_last_name" name="billing_last_name" value="<?php echo esc_attr( $v ); ?>" autocomplete="family-name" required>
							</div>
						<?php endif; ?>
					</div>
					<?php if ( isset( $billing['billing_address_1'] ) ) : ?>
						<?php $v = $checkout->get_value( 'billing_address_1' ); ?>
						<div class="vt-field">
							<label for="billing_address_1"><?php esc_html_e( 'Street address', 'woocommerce' ); ?> <span class="req">*</span></label>
							<input type="text" id="billing_address_1" name="billing_address_1" value="<?php echo esc_attr( $v ); ?>" placeholder="House number and street name" autocomplete="address-line1" required>
						</div>
					<?php endif; ?>
					<?php if ( isset( $billing['billing_address_2'] ) ) : ?>
						<?php $v = $checkout->get_value( 'billing_address_2' ); ?>
						<div class="vt-field">
							<label for="billing_address_2"><?php esc_html_e( 'Apartment, suite, unit, etc.', 'woocommerce' ); ?></label>
							<input type="text" id="billing_address_2" name="billing_address_2" value="<?php echo esc_attr( $v ); ?>" placeholder="Optional" autocomplete="address-line2">
						</div>
					<?php endif; ?>
					<div class="vt-field-grid">
						<?php if ( isset( $billing['billing_city'] ) ) : ?>
							<?php $v = $checkout->get_value( 'billing_city' ); ?>
							<div class="vt-field">
								<label for="billing_city"><?php esc_html_e( 'Town / City', 'woocommerce' ); ?> <span class="req">*</span></label>
								<input type="text" id="billing_city" name="billing_city" value="<?php echo esc_attr( $v ); ?>" autocomplete="address-level2" required>
							</div>
						<?php endif; ?>
						<?php if ( isset( $billing['billing_state'] ) ) : ?>
							<?php $v = $checkout->get_value( 'billing_state' ); ?>
							<div class="vt-field">
								<label for="billing_state"><?php esc_html_e( 'State / County', 'woocommerce' ); ?> <span class="req">*</span></label>
								<input type="text" id="billing_state" name="billing_state" value="<?php echo esc_attr( $v ); ?>" autocomplete="address-level1" required>
							</div>
						<?php endif; ?>
					</div>
					<div class="vt-field-grid">
						<?php if ( isset( $billing['billing_postcode'] ) ) : ?>
							<?php $v = $checkout->get_value( 'billing_postcode' ); ?>
							<div class="vt-field">
								<label for="billing_postcode"><?php esc_html_e( 'Postcode / ZIP', 'woocommerce' ); ?> <span class="req">*</span></label>
								<input type="text" id="billing_postcode" name="billing_postcode" value="<?php echo esc_attr( $v ); ?>" autocomplete="postal-code" required>
							</div>
						<?php endif; ?>
						<?php if ( isset( $billing['billing_country'] ) ) : ?>
							<div class="vt-field">
								<label for="billing_country"><?php esc_html_e( 'Country', 'woocommerce' ); ?> <span class="req">*</span></label>
								<select id="billing_country" name="billing_country" class="country_to_state" autocomplete="country" required>
									<option value=""><?php esc_html_e( 'Select a country', 'woocommerce' ); ?></option>
									<?php foreach ( $countries as $code => $name ) : ?>
										<option value="<?php echo esc_attr( $code ); ?>" <?php selected( $code, $checkout->get_value( 'billing_country' ) ); ?>><?php echo esc_html( $name ); ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						<?php endif; ?>
					</div>
				</div>

				<!-- PAYMENT -->
				<div class="vt-form-section">
					<h3><?php esc_html_e( 'Payment', 'woocommerce' ); ?></h3>
					<?php if ( ! empty( $gateways ) ) : ?>
						<?php $first = true; foreach ( $gateways as $gateway ) : ?>
							<div class="vt-payment-method">
								<label class="vt-payment-header">
									<input type="radio" name="payment_method" value="<?php echo esc_attr( $gateway->id ); ?>" <?php checked( $first, true ); ?>>
									<span><?php echo esc_html( $gateway->get_title() ); ?></span>
								</label>
								<?php if ( $gateway->has_fields() || $gateway->get_description() ) : ?>
									<div class="vt-payment-body payment_method_<?php echo esc_attr( $gateway->id ); ?>">
										<?php $gateway->payment_fields(); ?>
									</div>
								<?php endif; ?>
							</div>
						<?php $first = false; endforeach; ?>
					<?php else : ?>
						<p class="woocommerce-info"><?php esc_html_e( 'No payment methods available. Please contact the store owner.', 'woocommerce' ); ?></p>
					<?php endif; ?>
				</div>

			</div>

			<!-- RIGHT: ORDER SUMMARY -->
			<div class="vt-order-summary">
				<div class="vt-order-card">
					<h3><?php esc_html_e( 'Order Summary', 'woocommerce' ); ?></h3>

					<?php
					$cart_items = WC()->cart ? WC()->cart->get_cart() : array();
					foreach ( $cart_items as $cart_item_key => $cart_item ) :
						$product = $cart_item['data'];
						$qty = $cart_item['quantity'];
						$image = wp_get_attachment_image_url( $product->get_image_id(), 'woocommerce_thumbnail' );
						if ( ! $image ) {
							$image = wc_placeholder_img_src();
						}
						$price = $product->get_price() * $qty;
						?>
						<div class="vt-order-item">
							<div class="vt-order-item-img">
								<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $product->get_name() ); ?>">
								<span class="vt-order-item-qty"><?php echo esc_html( $qty ); ?></span>
							</div>
							<div class="vt-order-item-info">
								<div class="vt-order-item-name"><?php echo esc_html( $product->get_name() ); ?></div>
								<?php if ( $cart_item['variation'] ) : ?>
									<div class="vt-order-item-variant"><?php echo esc_html( wc_get_formatted_cart_item_data( $cart_item, true ) ); ?></div>
								<?php endif; ?>
							</div>
							<div class="vt-order-item-price"><?php echo wp_kses_post( wc_price( $price ) ); ?></div>
						</div>
					<?php endforeach; ?>

					<div class="vt-order-totals">
						<div class="vt-order-total-row">
							<span><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></span>
							<span><?php echo wp_kses_post( WC()->cart->get_cart_subtotal() ); ?></span>
						</div>
						<?php if ( WC()->cart->get_shipping_total() > 0 ) : ?>
							<div class="vt-order-total-row">
								<span><?php esc_html_e( 'Shipping', 'woocommerce' ); ?></span>
								<span><?php echo wp_kses_post( wc_price( WC()->cart->get_shipping_total() ) ); ?></span>
							</div>
						<?php else : ?>
							<div class="vt-order-total-row">
								<span><?php esc_html_e( 'Shipping', 'woocommerce' ); ?></span>
								<span><?php esc_html_e( 'Free', 'woocommerce' ); ?></span>
							</div>
						<?php endif; ?>
						<?php if ( WC()->cart->get_total_tax() > 0 ) : ?>
							<div class="vt-order-total-row">
								<span><?php esc_html_e( 'Tax', 'woocommerce' ); ?></span>
								<span><?php echo wp_kses_post( wc_price( WC()->cart->get_total_tax() ) ); ?></span>
							</div>
						<?php endif; ?>
						<div class="vt-order-total-row grand">
							<span><?php esc_html_e( 'Total', 'woocommerce' ); ?></span>
							<span><?php echo wp_kses_post( WC()->cart->get_total_to_display() ); ?></span>
						</div>
					</div>

					<button type="submit" class="vt-btn-order" id="place_order" name="woocommerce-process-checkout">
						<?php esc_html_e( 'Place Order', 'woocommerce' ); ?>
					</button>
					<div class="vt-secure-note">
						<i class="fas fa-lock" style="margin-right:4px;"></i>
						<?php esc_html_e( 'Secured with 256-bit encryption', 'woocommerce' ); ?>
					</div>
				</div>
			</div>
		</div>
	</form>
</section>

<!-- FOOTER -->
<footer class="vt-footer">
	<div class="vt-footer-grid">
		<div>
			<h4>AUREON</h4>
			<p><?php esc_html_e( 'Premium fashion and lifestyle store. Curated collections for the modern wardrobe.', 'woocommerce' ); ?></p>
		</div>
		<div>
			<h4><?php esc_html_e( 'Quick Links', 'woocommerce' ); ?></h4>
			<div class="vt-footer-links">
				<a href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'Shop', 'woocommerce' ); ?></a>
				<a href="<?php echo esc_url( home_url( '/about-us/' ) ); ?>"><?php esc_html_e( 'About Us', 'woocommerce' ); ?></a>
				<a href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>"><?php esc_html_e( 'Contact', 'woocommerce' ); ?></a>
				<a href="<?php echo esc_url( home_url( '/faq/' ) ); ?>"><?php esc_html_e( 'FAQ', 'woocommerce' ); ?></a>
			</div>
		</div>
		<div>
			<h4><?php esc_html_e( 'Policies', 'woocommerce' ); ?></h4>
			<div class="vt-footer-links">
				<a href="<?php echo esc_url( home_url( '/shipping/' ) ); ?>"><?php esc_html_e( 'Shipping', 'woocommerce' ); ?></a>
				<a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>"><?php esc_html_e( 'Privacy Policy', 'woocommerce' ); ?></a>
				<a href="<?php echo esc_url( home_url( '/term-and-condition/' ) ); ?>"><?php esc_html_e( 'Terms & Conditions', 'woocommerce' ); ?></a>
				<a href="<?php echo esc_url( home_url( '/return-and-refund/' ) ); ?>"><?php esc_html_e( 'Returns & Refunds', 'woocommerce' ); ?></a>
			</div>
		</div>
	</div>
	<div class="vt-footer-bottom">
		&copy; <?php echo esc_html( date( 'Y' ) ); ?> AUREON. <?php esc_html_e( 'All rights reserved.', 'woocommerce' ); ?>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
