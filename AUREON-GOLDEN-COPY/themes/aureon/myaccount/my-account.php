<?php
/**
 * WooCommerce My Account Template — Vineta Standalone
 *
 * Self-contained my-account page with Vineta design. Does NOT use get_header()
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

$menu_items = wp_get_nav_menu_items( 'primary' );
if ( ! $menu_items ) {
	$menu_items = array();
}

$is_logged_in = is_user_logged_in();
$current_user = wp_get_current_user();
$endpoint = function_exists( 'WC' ) && WC()->query ? (string) WC()->query->get_current_endpoint() : '';
$register_enabled = 'yes' === get_option( 'woocommerce_enable_myaccount_registration' );
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

/* === ACCOUNT LAYOUT === */
.vt-account-section { padding: 64px 0 100px; }
.vt-account-grid { display: grid; grid-template-columns: 240px 1fr; gap: 40px; max-width: 1100px; margin: 0 auto; padding: 0 24px; }

/* === SIDEBAR NAV === */
.vt-account-nav { background: #fafafa; border: 1px solid #ebebeb; border-radius: 16px; padding: 8px; height: fit-content; position: sticky; top: 96px; }
.vt-account-nav a {
	display: flex; align-items: center; gap: 10px; padding: 12px 16px; border-radius: 10px;
	font-size: 14px; font-weight: 500; color: #555; transition: all 0.2s;
}
.vt-account-nav a:hover { background: #f0f0f0; color: #111; }
.vt-account-nav a.active { background: #111; color: #fff; }
.vt-account-nav a i { width: 18px; text-align: center; }

/* === FORM CARD === */
.vt-form-card { background: #fff; border: 1px solid #ebebeb; border-radius: 16px; padding: 32px; }
.vt-form-card h2 { font-size: 22px; font-weight: 600; margin-bottom: 24px; }

/* === FORM FIELDS === */
.vt-field { margin-bottom: 16px; }
.vt-field label { display: block; font-size: 13px; font-weight: 500; color: #333; margin-bottom: 6px; }
.vt-field input, .vt-field select, .vt-field textarea {
	width: 100%; padding: 12px 16px; border: 1px solid #e0e0e0; border-radius: 8px;
	font-size: 14px; font-family: inherit; color: #111; background: #fff;
	transition: border-color 0.2s; outline: none;
}
.vt-field input:focus, .vt-field select:focus { border-color: #111; }
.vt-field-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

/* === BUTTONS === */
.vt-btn-primary { display: inline-flex; align-items: center; gap: 8px; padding: 12px 28px; background: #111; color: #fff; border: none; border-radius: 99px; font-size: 14px; font-weight: 600; font-family: inherit; cursor: pointer; transition: background 0.2s; text-decoration: none; }
.vt-btn-primary:hover { background: #333; }
.vt-btn-outline { display: inline-flex; align-items: center; gap: 8px; padding: 12px 28px; background: transparent; color: #111; border: 1px solid #ddd; border-radius: 99px; font-size: 14px; font-weight: 600; font-family: inherit; cursor: pointer; transition: all 0.2s; text-decoration: none; }
.vt-btn-outline:hover { border-color: #111; background: #f8f8f8; }

/* === DASHBOARD === */
.vt-dashboard-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 32px; }
.vt-dashboard-card { background: #fafafa; border: 1px solid #ebebeb; border-radius: 12px; padding: 24px; }
.vt-dashboard-card h3 { font-size: 14px; font-weight: 500; color: #888; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
.vt-dashboard-card .vt-value { font-size: 28px; font-weight: 700; color: #111; }
.vt-dashboard-card .vt-sub { font-size: 13px; color: #888; margin-top: 4px; }

/* === ORDERS TABLE === */
.vt-orders-table { width: 100%; border-collapse: collapse; }
.vt-orders-table thead th { font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #888; padding: 0 12px 12px; text-align: left; border-bottom: 1px solid #eee; }
.vt-orders-table tbody td { padding: 16px 12px; font-size: 14px; color: #333; border-bottom: 1px solid #f5f5f5; }
.vt-order-status { display: inline-block; padding: 4px 12px; border-radius: 99px; font-size: 12px; font-weight: 600; }
.vt-order-status.processing { background: #fff3cd; color: #856404; }
.vt-order-status.completed { background: #d4edda; color: #155724; }
.vt-order-status.on-hold { background: #fff3cd; color: #856404; }
.vt-order-status.cancelled { background: #f8d7da; color: #721c24; }
.vt-order-status.refunded { background: #e2e3e5; color: #383d41; }
.vt-order-status.pending { background: #fff3cd; color: #856404; }

/* === LOGIN / REGISTER === */
.vt-auth-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; max-width: 900px; margin: 0 auto; padding: 0 24px; }
.vt-auth-divider { display: flex; align-items: center; gap: 16px; margin: 24px 0; }
.vt-auth-divider::before, .vt-auth-divider::after { content: ''; flex: 1; height: 1px; background: #eee; }
.vt-auth-divider span { font-size: 13px; color: #888; white-space: nowrap; }
.vt-form-check { display: flex; align-items: center; gap: 8px; margin: 12px 0; }
.vt-form-check input[type="checkbox"] { accent-color: #111; width: 16px; height: 16px; }
.vt-form-check label { font-size: 14px; color: #555; }
.vt-form-links { margin-top: 16px; font-size: 14px; }
.vt-form-links a { color: #555; }
.vt-form-links a:hover { color: #111; text-decoration: underline; }

/* === EMPTY STATE === */
.vt-empty-state { text-align: center; padding: 48px 24px; color: #888; }
.vt-empty-state i { font-size: 48px; margin-bottom: 16px; color: #ddd; }
.vt-empty-state p { font-size: 16px; margin-bottom: 24px; }

/* === WC NOTICES === */
.woocommerce-notices-wrapper { margin-bottom: 24px; }
.woocommerce-notices-wrapper .woocommerce-message,
.woocommerce-notices-wrapper .woocommerce-error,
.woocommerce-notices-wrapper .woocommerce-info { padding: 12px 16px; border-radius: 8px; margin-bottom: 8px; font-size: 14px; }
.woocommerce-notices-wrapper .woocommerce-message { background: #f0fff4; border: 1px solid #ccffe0; color: #006600; }
.woocommerce-notices-wrapper .woocommerce-error { background: #fff5f5; border: 1px solid #ffcccc; color: #cc0000; list-style: none; }
.woocommerce-notices-wrapper .woocommerce-info { background: #f0f7ff; border: 1px solid #cce0ff; color: #0066cc; }

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
	.vt-account-grid { grid-template-columns: 1fr; }
	.vt-account-nav { position: static; }
	.vt-dashboard-grid { grid-template-columns: 1fr 1fr; }
	.vt-nav { display: none; }
	.vt-auth-grid { grid-template-columns: 1fr; }
}
@media (max-width: 575px) {
	.vt-dashboard-grid { grid-template-columns: 1fr; }
	.vt-header-inner { padding: 0 16px; height: 60px; }
	.vt-footer-grid { grid-template-columns: 1fr; gap: 32px; }
}
</style>
</head>
<body <?php body_class( 'woocommerce-account woocommerce-myaccount' ); ?>>
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
	<h1><?php esc_html_e( 'My Account', 'woocommerce' ); ?></h1>
	<div class="vt-breadcrumb">
		<a href="<?php echo esc_url( $site_url ); ?>"><?php esc_html_e( 'Home', 'woocommerce' ); ?></a>
		<span>/</span>
		<strong><?php esc_html_e( 'My Account', 'woocommerce' ); ?></strong>
	</div>
</div>

<?php do_action( 'woocommerce_before_my_account' ); ?>
<?php wc_print_notices(); ?>

<?php if ( $is_logged_in ) : ?>

	<!-- LOGGED IN -->
	<section class="vt-account-section">
		<div class="vt-account-grid">
			<!-- SIDEBAR -->
			<nav class="vt-account-nav">
				<a href="<?php echo esc_url( wc_get_account_endpoint_url( 'dashboard' ) ); ?>" class="<?php echo ( '' === $endpoint || 'dashboard' === $endpoint ) ? 'active' : ''; ?>">
					<i class="fas fa-th-large"></i> <?php esc_html_e( 'Dashboard', 'woocommerce' ); ?>
				</a>
				<a href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>" class="<?php echo ( 'orders' === $endpoint ) ? 'active' : ''; ?>">
					<i class="fas fa-box"></i> <?php esc_html_e( 'Orders', 'woocommerce' ); ?>
				</a>
				<a href="<?php echo esc_url( wc_get_account_endpoint_url( 'downloads' ) ); ?>" class="<?php echo ( 'downloads' === $endpoint ) ? 'active' : ''; ?>">
					<i class="fas fa-download"></i> <?php esc_html_e( 'Downloads', 'woocommerce' ); ?>
				</a>
				<a href="<?php echo esc_url( wc_get_account_endpoint_url( 'edit-address' ) ); ?>" class="<?php echo ( 'edit-address' === $endpoint || 'edit-account' === $endpoint ) ? 'active' : ''; ?>">
					<i class="fas fa-map-marker-alt"></i> <?php esc_html_e( 'Addresses', 'woocommerce' ); ?>
				</a>
				<a href="<?php echo esc_url( wc_get_account_endpoint_url( 'edit-account' ) ); ?>" class="<?php echo ( 'edit-account' === $endpoint ) ? 'active' : ''; ?>">
					<i class="fas fa-user-cog"></i> <?php esc_html_e( 'Account Details', 'woocommerce' ); ?>
				</a>
				<a href="<?php echo esc_url( wc_get_account_endpoint_url( 'customer-logout' ) ); ?>">
					<i class="fas fa-sign-out-alt"></i> <?php esc_html_e( 'Logout', 'woocommerce' ); ?>
				</a>
			</nav>

			<!-- CONTENT -->
			<div>
				<?php wc_account_content(); ?>
			</div>
		</div>
	</section>

<?php else : ?>

	<!-- LOGGED OUT -->
	<section class="vt-account-section">
		<div class="vt-auth-grid">
			<!-- LOGIN -->
			<div class="vt-form-card">
				<h2><?php esc_html_e( 'Login', 'woocommerce' ); ?></h2>
				<form class="woocommerce-form-login" method="post">
					<?php do_action( 'woocommerce_login_form_start' ); ?>
					<div class="vt-field">
						<label for="username"><?php esc_html_e( 'Email address', 'woocommerce' ); ?> <span style="color:#cc0000;">*</span></label>
						<input type="text" class="woocommerce-Input" name="username" id="username" autocomplete="username" value="<?php echo esc_attr( ! empty( $_POST['username'] ) ? wp_unslash( $_POST['username'] ) : '' ); // phpcs:ignore ?>">
					</div>
					<div class="vt-field">
						<label for="password"><?php esc_html_e( 'Password', 'woocommerce' ); ?> <span style="color:#cc0000;">*</span></label>
						<input class="woocommerce-Input" type="password" name="password" id="password" autocomplete="current-password">
					</div>
					<?php do_action( 'woocommerce_login_form' ); ?>
					<div class="vt-form-check">
						<input type="checkbox" name="rememberme" id="rememberme" value="forever" checked>
						<label for="rememberme"><?php esc_html_e( 'Remember me', 'woocommerce' ); ?></label>
					</div>
					<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
					<button type="submit" class="vt-btn-primary" name="login" value="<?php esc_attr_e( 'Login', 'woocommerce' ); ?>"><?php esc_html_e( 'Login', 'woocommerce' ); ?></button>
					<div class="vt-form-links">
						<a href="<?php echo esc_url( wc_lostpassword_url() ); ?>"><?php esc_html_e( 'Lost your password?', 'woocommerce' ); ?></a>
					</div>
					<?php do_action( 'woocommerce_login_form_end' ); ?>
				</form>
			</div>

			<!-- REGISTER -->
			<?php if ( $register_enabled ) : ?>
				<div class="vt-form-card">
					<h2><?php esc_html_e( 'Register', 'woocommerce' ); ?></h2>
					<form method="post" class="woocommerce-form-register">
						<?php do_action( 'woocommerce_register_form_start' ); ?>
						<div class="vt-field">
							<label for="reg_email"><?php esc_html_e( 'Email address', 'woocommerce' ); ?> <span style="color:#cc0000;">*</span></label>
							<input type="email" class="woocommerce-Input" name="email" id="reg_email" value="<?php echo esc_attr( ! empty( $_POST['email'] ) ? wp_unslash( $_POST['email'] ) : '' ); // phpcs:ignore ?>" autocomplete="email">
						</div>
						<div class="vt-field">
							<label for="reg_password"><?php esc_html_e( 'Password', 'woocommerce' ); ?> <span style="color:#cc0000;">*</span></label>
							<input type="password" class="woocommerce-Input" name="password" id="reg_password" autocomplete="new-password">
						</div>
						<?php do_action( 'woocommerce_register_form' ); ?>
						<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
						<button type="submit" class="vt-btn-primary" name="register" value="<?php esc_attr_e( 'Register', 'woocommerce' ); ?>"><?php esc_html_e( 'Register', 'woocommerce' ); ?></button>
						<?php do_action( 'woocommerce_register_form_end' ); ?>
					</form>
				</div>
			<?php else : ?>
				<div class="vt-form-card">
					<h2><?php esc_html_e( 'Register', 'woocommerce' ); ?></h2>
					<div class="vt-empty-state">
						<p><?php esc_html_e( 'Registration is currently disabled.', 'woocommerce' ); ?></p>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</section>

<?php endif; ?>

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
