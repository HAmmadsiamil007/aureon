<?php
/**
 * WooCommerce Cart Template — Vineta Standalone
 *
 * Self-contained cart page with Vineta design. Does NOT use get_header()
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

$cart = WC()->cart;
$cart_items = $cart ? $cart->get_cart() : array();
$cart_count = $cart ? $cart->get_cart_contents_count() : 0;
$cart_is_empty = $cart ? $cart->is_empty() : true;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php wp_title( '|', true, 'right' ); ?></title>
<?php
if ( function_exists( 'WC' ) ) {
	wp_enqueue_style( 'woocommerce-general', WC()->plugin_url() . '/assets/css/woocommerce.css', array(), WC()->version );
}
?>
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

/* === CART LAYOUT === */
.vt-cart-section { padding: 48px 0 100px; }
.vt-cart-grid { display: grid; grid-template-columns: 1fr 380px; gap: 40px; max-width: 1200px; margin: 0 auto; padding: 0 24px; }

/* === CART TABLE === */
.vt-cart-table { width: 100%; border-collapse: collapse; }
.vt-cart-table thead th { font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #888; padding: 0 12px 16px; text-align: left; border-bottom: 1px solid #eee; }
.vt-cart-table thead th:nth-child(2),
.vt-cart-table thead th:nth-child(3),
.vt-cart-table thead th:nth-child(4) { text-align: center; }
.vt-cart-item { border-bottom: 1px solid #f0f0f0; }
.vt-cart-item td { padding: 20px 12px; vertical-align: middle; }
.vt-cart-item td:nth-child(2),
.vt-cart-item td:nth-child(3),
.vt-cart-item td:nth-child(4) { text-align: center; }

.vt-cart-product { display: flex; gap: 16px; align-items: center; }
.vt-cart-product-img { width: 90px; height: 110px; border-radius: 8px; overflow: hidden; flex-shrink: 0; background: #f5f5f5; }
.vt-cart-product-img img { width: 100%; height: 100%; object-fit: cover; }
.vt-cart-product-info { flex: 1; }
.vt-cart-product-name { font-size: 15px; font-weight: 500; color: #111; margin-bottom: 4px; display: block; }
.vt-cart-product-name:hover { color: #888; }
.vt-cart-product-variant { font-size: 13px; color: #888; margin-bottom: 8px; }
.vt-cart-remove { font-size: 13px; color: #cc0000; cursor: pointer; background: none; border: none; font-family: inherit; }
.vt-cart-remove:hover { text-decoration: underline; }
.vt-cart-price { font-size: 15px; font-weight: 500; color: #111; }

/* === QUANTITY === */
.vt-qty-wrap { display: inline-flex; align-items: center; border: 1px solid #ddd; border-radius: 8px; overflow: hidden; }
.vt-qty-btn { width: 36px; height: 36px; border: none; background: #fafafa; cursor: pointer; font-size: 16px; font-weight: 500; color: #333; display: flex; align-items: center; justify-content: center; transition: background 0.2s; }
.vt-qty-btn:hover { background: #eee; }
.vt-qty-input { width: 48px; height: 36px; border: none; border-left: 1px solid #ddd; border-right: 1px solid #ddd; text-align: center; font-size: 14px; font-family: inherit; outline: none; }
.vt-cart-total { font-size: 15px; font-weight: 600; color: #111; }

/* === SIDEBAR === */
.vt-cart-sidebar { position: sticky; top: 96px; }
.vt-sidebar-card { background: #fafafa; border: 1px solid #ebebeb; border-radius: 16px; padding: 24px; margin-bottom: 20px; }
.vt-sidebar-card h3 { font-size: 18px; font-weight: 600; margin-bottom: 20px; color: #111; }
.vt-total-row { display: flex; justify-content: space-between; padding: 8px 0; font-size: 14px; color: #555; }
.vt-total-row.grand { font-size: 18px; font-weight: 600; color: #111; padding-top: 12px; border-top: 1px solid #e0e0e0; margin-top: 8px; }

/* === COUPON === */
.vt-coupon-wrap { display: flex; gap: 8px; margin-bottom: 16px; }
.vt-coupon-input { flex: 1; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; font-family: inherit; outline: none; }
.vt-coupon-input:focus { border-color: #111; }
.vt-coupon-btn { padding: 10px 20px; background: #111; color: #fff; border: none; border-radius: 8px; font-size: 14px; font-weight: 500; font-family: inherit; cursor: pointer; transition: background 0.2s; white-space: nowrap; }
.vt-coupon-btn:hover { background: #333; }

/* === BUTTONS === */
.vt-btn-checkout { display: block; width: 100%; padding: 16px 32px; background: #111; color: #fff; border: none; border-radius: 99px; font-size: 16px; font-weight: 600; font-family: inherit; cursor: pointer; transition: background 0.2s; text-align: center; }
.vt-btn-checkout:hover { background: #333; }
.vt-continue-btn { display: inline-flex; align-items: center; gap: 8px; font-size: 14px; font-weight: 500; color: #111; margin-top: 24px; }
.vt-continue-btn:hover { color: #888; }
.vt-secure-note { text-align: center; font-size: 12px; color: #888; margin-top: 12px; }

/* === FEATURES === */
.vt-features { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; max-width: 1200px; margin: 48px auto 0; padding: 0 24px; }
.vt-feature { display: flex; align-items: center; gap: 12px; padding: 16px; background: #fafafa; border-radius: 12px; border: 1px solid #f0f0f0; }
.vt-feature-icon { width: 40px; height: 40px; background: #f0f0f0; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.vt-feature-icon i { font-size: 16px; color: #333; }
.vt-feature-text { font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #333; }

/* === EMPTY CART === */
.vt-empty-cart { text-align: center; padding: 80px 24px; }
.vt-empty-cart h2 { font-size: 28px; font-weight: 600; margin-bottom: 16px; }
.vt-empty-cart p { font-size: 16px; color: #888; margin-bottom: 32px; }
.vt-btn-shop { display: inline-block; padding: 14px 40px; background: #111; color: #fff; border-radius: 99px; font-size: 16px; font-weight: 600; transition: background 0.2s; }
.vt-btn-shop:hover { background: #333; }

/* === WC NOTICES === */
.vt-notices { margin-bottom: 24px; }
.vt-notice { padding: 12px 16px; border-radius: 8px; margin-bottom: 8px; font-size: 14px; }
.vt-notice-info { background: #f0f7ff; border: 1px solid #cce0ff; color: #0066cc; }
.vt-notice-error { background: #fff5f5; border: 1px solid #ffcccc; color: #cc0000; }
.vt-notice-success { background: #f0fff4; border: 1px solid #ccffe0; color: #006600; }

/* === FOOTER === */
.vt-footer { background: #111; color: #fff; padding: 64px 0 0; }
.vt-footer-grid { display: grid; grid-template-columns: 1.5fr 1fr 1fr; gap: 48px; max-width: 1200px; margin: 0 auto; padding: 0 24px; }
.vt-footer h4 { font-size: 16px; font-weight: 600; margin-bottom: 20px; }
.vt-footer p { font-size: 14px; color: #999; line-height: 1.6; margin-bottom: 16px; }
.vt-footer a { color: #999; font-size: 14px; transition: color 0.2s; }
.vt-footer a:hover { color: #fff; }
.vt-footer-links { display: flex; flex-direction: column; gap: 10px; }
.vt-footer-bottom { border-top: 1px solid rgba(255,255,255,0.1); padding: 24px 0; margin-top: 48px; text-align: center; font-size: 13px; color: #666; }

/* === RESPONSIVE === */
@media (max-width: 991px) {
	.vt-cart-grid { grid-template-columns: 1fr; }
	.vt-cart-sidebar { order: -1; }
	.vt-nav { display: none; }
	.vt-features { grid-template-columns: 1fr 1fr; }
}
@media (max-width: 575px) {
	.vt-cart-product { flex-direction: column; align-items: flex-start; gap: 8px; }
	.vt-cart-product-img { width: 72px; height: 80px; }
	.vt-header-inner { padding: 0 16px; height: 60px; }
	.vt-footer-grid { grid-template-columns: 1fr; gap: 32px; }
	.vt-features { grid-template-columns: 1fr; }
}
</style>
</head>
<body <?php body_class( 'woocommerce-cart-page' ); ?>>
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
	<h1><?php esc_html_e( 'Shopping Cart', 'woocommerce' ); ?></h1>
	<div class="vt-breadcrumb">
		<a href="<?php echo esc_url( $site_url ); ?>"><?php esc_html_e( 'Home', 'woocommerce' ); ?></a>
		<span>/</span>
		<strong><?php esc_html_e( 'Cart', 'woocommerce' ); ?></strong>
	</div>
</div>

<!-- CART -->
<section class="vt-cart-section">
	<?php if ( function_exists( 'wc_print_notices' ) ) { wc_print_notices(); } ?>

	<?php if ( $cart_is_empty || $cart_count === 0 ) : ?>

		<div class="vt-empty-cart">
			<h2><?php esc_html_e( 'Your cart is empty', 'woocommerce' ); ?></h2>
			<p><?php esc_html_e( 'Looks like you haven\'t added anything to your cart yet.', 'woocommerce' ); ?></p>
			<a href="<?php echo esc_url( $shop_url ); ?>" class="vt-btn-shop"><?php esc_html_e( 'Start Shopping', 'woocommerce' ); ?></a>
		</div>

	<?php else : ?>

		<form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
			<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
			<div class="vt-cart-grid">

				<!-- CART TABLE -->
				<div>
					<table class="vt-cart-table">
						<thead>
							<tr>
								<th><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
								<th><?php esc_html_e( 'Price', 'woocommerce' ); ?></th>
								<th><?php esc_html_e( 'Quantity', 'woocommerce' ); ?></th>
								<th><?php esc_html_e( 'Total', 'woocommerce' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $cart_items as $cart_item_key => $cart_item ) :
								$product = $cart_item['data'];
								$qty = $cart_item['quantity'];
								$image = wp_get_attachment_image_url( $product->get_image_id(), 'woocommerce_thumbnail' );
								if ( ! $image ) { $image = wc_placeholder_img_src(); }
								$line_total = $product->get_price() * $qty;
								$variation_data = isset( $cart_item['variation'] ) ? $cart_item['variation'] : array();
								?>
								<tr class="vt-cart-item" data-cart-item-key="<?php echo esc_attr( $cart_item_key ); ?>">
									<td>
										<div class="vt-cart-product">
											<a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="vt-cart-product-img">
												<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $product->get_name() ); ?>">
											</a>
											<div class="vt-cart-product-info">
												<a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="vt-cart-product-name"><?php echo esc_html( $product->get_name() ); ?></a>
												<?php if ( ! empty( $variation_data ) ) : ?>
													<div class="vt-cart-product-variant"><?php echo wp_kses_post( wc_get_formatted_cart_item_data( $cart_item ) ); ?></div>
												<?php endif; ?>
												<button type="submit" name="update_cart" value="<?php echo esc_attr( $cart_item_key ); ?>" class="vt-cart-remove"><?php esc_html_e( 'Remove', 'woocommerce' ); ?></button>
											</div>
										</div>
									</td>
									<td>
										<span class="vt-cart-price"><?php echo wp_kses_post( wc_price( $product->get_price() ) ); ?></span>
									</td>
									<td>
										<div class="vt-qty-wrap">
											<button type="button" class="vt-qty-btn vt-qty-minus" aria-label="<?php esc_attr_e( 'Decrease quantity', 'woocommerce' ); ?>">-</button>
											<input type="number" name="cart[<?php echo esc_attr( $cart_item_key ); ?>][qty]" value="<?php echo esc_attr( $qty ); ?>" min="0" class="vt-qty-input input-text qty text" aria-label="<?php esc_attr_e( 'Quantity', 'woocommerce' ); ?>">
											<button type="button" class="vt-qty-btn vt-qty-plus" aria-label="<?php esc_attr_e( 'Increase quantity', 'woocommerce' ); ?>">+</button>
										</div>
									</td>
									<td>
										<span class="vt-cart-total"><?php echo wp_kses_post( wc_price( $line_total ) ); ?></span>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>

					<div class="vt-continue-btn">
						<a href="<?php echo esc_url( $shop_url ); ?>">
							<i class="fas fa-arrow-left" style="font-size:12px;"></i>
							<?php esc_html_e( 'Continue Shopping', 'woocommerce' ); ?>
						</a>
					</div>
				</div>

				<!-- SIDEBAR -->
				<div class="vt-cart-sidebar">

					<!-- COUPON -->
					<div class="vt-sidebar-card">
						<h3><?php esc_html_e( 'Coupon Code', 'woocommerce' ); ?></h3>
						<div class="vt-coupon-wrap">
							<input type="text" name="coupon_code" class="vt-coupon-input" placeholder="<?php esc_attr_e( 'Enter coupon code', 'woocommerce' ); ?>" id="coupon_code">
							<button type="submit" name="apply_coupon" value="1" class="vt-coupon-btn"><?php esc_html_e( 'Apply', 'woocommerce' ); ?></button>
						</div>
					</div>

					<!-- CART TOTALS -->
					<div class="vt-sidebar-card">
						<h3><?php esc_html_e( 'Order Summary', 'woocommerce' ); ?></h3>
						<div class="vt-total-row">
							<span><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></span>
							<span><?php echo wp_kses_post( $cart->get_cart_subtotal() ); ?></span>
						</div>
						<?php if ( $cart->get_shipping_total() > 0 ) : ?>
							<div class="vt-total-row">
								<span><?php esc_html_e( 'Shipping', 'woocommerce' ); ?></span>
								<span><?php echo wp_kses_post( wc_price( $cart->get_shipping_total() ) ); ?></span>
							</div>
						<?php else : ?>
							<div class="vt-total-row">
								<span><?php esc_html_e( 'Shipping', 'woocommerce' ); ?></span>
								<span><?php esc_html_e( 'Free', 'woocommerce' ); ?></span>
							</div>
						<?php endif; ?>
						<?php if ( $cart->get_total_tax() > 0 ) : ?>
							<div class="vt-total-row">
								<span><?php esc_html_e( 'Tax', 'woocommerce' ); ?></span>
								<span><?php echo wp_kses_post( wc_price( $cart->get_total_tax() ) ); ?></span>
							</div>
						<?php endif; ?>
						<div class="vt-total-row grand">
							<span><?php esc_html_e( 'Total', 'woocommerce' ); ?></span>
							<span><?php echo wp_kses_post( wc_price( $cart->get_total( 'display' ) ) ); ?></span>
						</div>
						<a href="<?php echo esc_url( $checkout_url ); ?>" class="vt-btn-checkout" style="text-decoration:none;margin-top:20px;">
							<?php esc_html_e( 'Proceed to Checkout', 'woocommerce' ); ?>
						</a>
						<div class="vt-secure-note">
							<i class="fas fa-lock" style="margin-right:4px;"></i>
							<?php esc_html_e( 'Secured with 256-bit encryption', 'woocommerce' ); ?>
						</div>
					</div>

				</div>
			</div>
		</form>

	<?php endif; ?>

	<!-- FEATURES -->
	<div class="vt-features">
		<div class="vt-feature">
			<div class="vt-feature-icon"><i class="fas fa-shipping-fast"></i></div>
			<div class="vt-feature-text"><?php esc_html_e( 'Free Shipping', 'woocommerce' ); ?></div>
		</div>
		<div class="vt-feature">
			<div class="vt-feature-icon"><i class="fas fa-gift"></i></div>
			<div class="vt-feature-text"><?php esc_html_e( 'Gift Package', 'woocommerce' ); ?></div>
		</div>
		<div class="vt-feature">
			<div class="vt-feature-icon"><i class="fas fa-undo"></i></div>
			<div class="vt-feature-text"><?php esc_html_e( 'Easy Returns', 'woocommerce' ); ?></div>
		</div>
		<div class="vt-feature">
			<div class="vt-feature-icon"><i class="fas fa-shield-alt"></i></div>
			<div class="vt-feature-text"><?php esc_html_e( 'Warranty', 'woocommerce' ); ?></div>
		</div>
	</div>
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

<?php
if ( function_exists( 'WC' ) ) {
	wp_enqueue_script( 'wc-cart-fragments', WC()->plugin_url() . '/assets/js/frontend/cart-fragments' . ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min' ) . '.js', array( 'jquery' ), WC()->version, true );
	wp_print_scripts( array( 'wc-cart-fragments' ) );
}
?>
<script>
jQuery(function($){
	$('.vt-qty-minus').on('click', function(){
		var $input = $(this).siblings('.vt-qty-input');
		var val = parseInt($input.val()) || 1;
		if (val > 1) $input.val(val - 1);
	});
	$('.vt-qty-plus').on('click', function(){
		var $input = $(this).siblings('.vt-qty-input');
		var val = parseInt($input.val()) || 0;
		$input.val(val + 1);
	});
});
</script>
</body>
</html>
