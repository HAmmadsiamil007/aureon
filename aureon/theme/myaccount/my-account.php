<?php
/**
 * WooCommerce My Account — Premium Standalone Pages
 *
 * ?auth=login    → premium login page
 * ?auth=register → premium register page
 * Logged in      → styled dashboard
 * Logged out     → redirect to ?auth=login
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$is_logged_in = is_user_logged_in();
$auth_param   = isset( $_GET['auth'] ) ? sanitize_text_field( wp_unslash( $_GET['auth'] ) ) : '';
$endpoint     = function_exists( 'WC' ) && WC()->query ? (string) WC()->query->get_current_endpoint() : '';

/*
 * Route ?auth=login → login.php (standalone)
 */
if ( ! $is_logged_in && 'login' === $auth_param ) {
	$login_file = get_stylesheet_directory() . '/myaccount/login.php';
	if ( file_exists( $login_file ) ) {
		require $login_file;
		exit;
	}
}

/*
 * Route ?auth=register → register.php (standalone)
 */
if ( ! $is_logged_in && 'register' === $auth_param ) {
	$register_file = get_stylesheet_directory() . '/myaccount/register.php';
	if ( file_exists( $register_file ) ) {
		require $register_file;
		exit;
	}
}

/*
 * Logged-in → styled My Account dashboard
 */
if ( $is_logged_in ) {
	$user           = wp_get_current_user();
	$display_name   = $user->display_name ?: $user->user_login;
	$email          = $user->user_email;
	$member_since   = date( 'Y', strtotime( $user->user_registered ) );
	$avatar_url     = get_avatar_url( $user->ID, array( 'size' => 96 ) );

	$dashboard_url = wc_get_account_endpoint_url( '' );
	$orders_url    = wc_get_account_endpoint_url( 'orders' );
	$downloads_url = wc_get_account_endpoint_url( 'downloads' );
	$address_url   = wc_get_account_endpoint_url( 'edit-address' );
	$details_url   = wc_get_account_endpoint_url( 'edit-account' );
	$logout_url    = wc_get_account_endpoint_url( 'customer-logout' );

	// Stats
	$orders_count  = wc_get_customer_order_count( $user->ID );
	$address_count = 0;
	$types         = array( 'billing', 'shipping' );
	foreach ( $types as $type ) {
		$val = get_user_meta( $user->ID, $type . '_address_1', true );
		if ( $val ) {
			$address_count++;
		}
	}

	$first_name = explode( ' ', $display_name )[0] ?: $display_name;
	?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php esc_html_e( 'My Account', 'aureon' ); ?> &mdash; <?php bloginfo( 'name' ); ?></title>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
	<style>
		*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
		body {
			font-family: 'Poppins', sans-serif;
			background: #f5f0eb;
			color: #111;
			min-height: 100vh;
			display: flex;
			flex-direction: column;
			-webkit-font-smoothing: antialiased;
		}
		a { text-decoration: none; color: inherit; }

		/* === HEADER === */
		.vt-hdr { background: #fff; border-bottom: 1px solid #ebebeb; position: sticky; top: 0; z-index: 100; }
		.vt-hdr-in { display: flex; align-items: center; justify-content: space-between; max-width: 1400px; margin: 0 auto; padding: 14px 40px; }
		.vt-logo { display: flex; align-items: center; gap: 10px; text-decoration: none; }
		.vt-logo img { height: 36px; width: auto; }
		.vt-logo-t { font-size: 22px; font-weight: 700; color: #111; letter-spacing: -0.5px; }
		.vt-nav { display: flex; align-items: center; gap: 28px; }
		.vt-nav a { text-decoration: none; color: #555; font-size: 14px; font-weight: 500; transition: color 0.2s; }
		.vt-nav a:hover { color: #111; }

		/* === BANNER === */
		.vt-ban { background: #fff; border-bottom: 1px solid #ebebeb; padding: 24px 40px; }
		.vt-ban-in { max-width: 1200px; margin: 0 auto; }
		.vt-ban h1 { font-size: 24px; font-weight: 600; color: #111; }
		.vt-crumb { font-size: 13px; color: #888; margin-top: 4px; }
		.vt-crumb a { color: #888; }
		.vt-crumb a:hover { color: #ff6f61; }

		/* === DASHBOARD LAYOUT === */
		.vt-wrap { max-width: 1200px; margin: 0 auto; padding: 40px 40px 80px; display: grid; grid-template-columns: 280px 1fr; gap: 32px; }

		/* === SIDEBAR === */
		.vt-side { display: flex; flex-direction: column; }
		.vt-prof {
			background: #fff;
			border-radius: 16px;
			padding: 32px 28px;
			text-align: center;
			box-shadow: 0 2px 20px rgba(0,0,0,0.04);
			margin-bottom: 16px;
		}
		.vt-avatar {
			width: 80px; height: 80px; border-radius: 50%; object-fit: cover;
			margin-bottom: 16px; border: 3px solid #f0f0f0;
		}
		.vt-avatar-fb {
			width: 80px; height: 80px; border-radius: 50%;
			background: linear-gradient(135deg, #ff6f61, #ff9a90);
			color: #fff; display: flex; align-items: center; justify-content: center;
			font-size: 28px; font-weight: 600; margin: 0 auto 16px;
		}
		.vt-uname { font-size: 18px; font-weight: 600; color: #111; margin-bottom: 4px; }
		.vt-uemail { font-size: 13px; color: #888; margin-bottom: 20px; word-break: break-all; }
		.vt-stats { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
		.vt-stat { background: #fafafa; border-radius: 10px; padding: 14px 12px; text-align: center; }
		.vt-stat-n { font-size: 22px; font-weight: 600; color: #111; line-height: 1; }
		.vt-stat-l { font-size: 11px; color: #888; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 4px; }

		/* === NAV === */
		.vt-nav-box { background: #fff; border-radius: 16px; padding: 8px; box-shadow: 0 2px 20px rgba(0,0,0,0.04); }
		.vt-nav-i {
			display: flex; align-items: center; gap: 12px;
			padding: 13px 20px; border-radius: 10px;
			font-size: 14px; font-weight: 500; color: #555;
			transition: all 0.2s; cursor: pointer;
		}
		.vt-nav-i:hover { background: #fafafa; color: #111; }
		.vt-nav-i.on { background: #111; color: #fff; }
		.vt-nav-i svg { width: 18px; height: 18px; flex-shrink: 0; }
		.vt-nav-i.on svg { opacity: 0.8; }

		/* === MAIN === */
		.vt-main { min-height: 400px; }

		.vt-wel {
			background: #fff; border-radius: 16px; padding: 36px 40px;
			box-shadow: 0 2px 20px rgba(0,0,0,0.04); margin-bottom: 24px;
		}
		.vt-wel h2 { font-size: 22px; font-weight: 600; color: #111; margin-bottom: 8px; }
		.vt-wel p { font-size: 14px; color: #888; line-height: 1.6; }

		.vt-acts { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
		.vt-act {
			background: #fff; border-radius: 16px; padding: 28px 24px;
			box-shadow: 0 2px 20px rgba(0,0,0,0.04); transition: all 0.3s;
			cursor: pointer; display: block;
		}
		.vt-act:hover { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(0,0,0,0.08); }
		.vt-act-ic {
			width: 44px; height: 44px; border-radius: 12px;
			display: flex; align-items: center; justify-content: center; margin-bottom: 16px;
		}
		.vt-act-ic svg { width: 22px; height: 22px; }
		.vt-act-ic.crl { background: #fff0ee; color: #ff6f61; }
		.vt-act-ic.drk { background: #f0f0f0; color: #111; }
		.vt-act-ic.mtd { background: #f5f5f5; color: #888; }
		.vt-act h3 { font-size: 15px; font-weight: 600; color: #111; margin-bottom: 6px; }
		.vt-act p { font-size: 13px; color: #888; line-height: 1.5; }

		/* === SECTION === */
		.vt-sec { background: #fff; border-radius: 16px; padding: 36px 40px; box-shadow: 0 2px 20px rgba(0,0,0,0.04); }
		.vt-sec h2 { font-size: 20px; font-weight: 600; color: #111; margin-bottom: 24px; }

		/* === TABLE === */
		.vt-tbl { width: 100%; border-collapse: collapse; }
		.vt-tbl th {
			text-align: left; font-size: 11px; font-weight: 600; color: #888;
			text-transform: uppercase; letter-spacing: 0.8px;
			padding: 0 16px 16px; border-bottom: 1px solid #f0f0f0;
		}
		.vt-tbl td { padding: 16px; font-size: 14px; color: #333; border-bottom: 1px solid #f8f8f8; vertical-align: middle; }
		.vt-tbl tr:last-child td { border-bottom: none; }
		.vt-tbl tr:hover td { background: #fafafa; }
		.vt-badge { display: inline-block; padding: 4px 12px; border-radius: 99px; font-size: 12px; font-weight: 500; }
		.vt-badge.pending { background: #fff8e1; color: #c78b00; }
		.vt-badge.completed { background: #e8f5e9; color: #2e7d32; }
		.vt-badge.processing { background: #e3f2fd; color: #1565c0; }
		.vt-badge.cancelled { background: #fbe9e7; color: #c62828; }
		.vt-badge.on-hold { background: #fff3e0; color: #e65100; }
		.vt-badge.refunded { background: #f3e5f5; color: #7b1fa2; }

		.vt-empty { text-align: center; padding: 60px 20px; }
		.vt-empty svg { width: 48px; height: 48px; color: #ccc; margin-bottom: 16px; }
		.vt-empty h3 { font-size: 16px; font-weight: 500; color: #555; margin-bottom: 8px; }
		.vt-empty p { font-size: 13px; color: #999; margin-bottom: 24px; }
		.vt-empty a { display: inline-block; padding: 12px 32px; background: #111; color: #fff; border-radius: 99px; font-size: 14px; font-weight: 500; transition: all 0.3s; }
		.vt-empty a:hover { background: #333; }

		/* === WC FORMS === */
		.vt-fm .form-row { margin-bottom: 20px; }
		.vt-fm label { display: block; font-size: 13px; font-weight: 500; color: #333; margin-bottom: 6px; }
		.vt-fm input, .vt-fm select, .vt-fm textarea {
			width: 100%; padding: 12px 16px; border: 1.5px solid #e8e8e8;
			border-radius: 10px; font-size: 14px; font-family: 'Poppins', sans-serif;
			color: #111; background: #fafafa; transition: all 0.3s; outline: none;
		}
		.vt-fm input:focus, .vt-fm select:focus, .vt-fm textarea:focus {
			border-color: #111; background: #fff; box-shadow: 0 0 0 3px rgba(17,17,17,0.04);
		}
		.vt-fm .form-row-half { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
		.vt-fm .button {
			display: inline-block; padding: 13px 36px; background: #111; color: #fff;
			border: none; border-radius: 99px; font-size: 14px; font-weight: 600;
			font-family: 'Poppins', sans-serif; cursor: pointer; transition: all 0.3s;
			text-transform: uppercase; letter-spacing: 0.5px;
		}
		.vt-fm .button:hover { background: #333; transform: translateY(-1px); }
		.vt-fm .woocommerce-message { background: #e8f5e9; border: 1px solid #c8e6c9; color: #2e7d32; padding: 12px 16px; border-radius: 10px; font-size: 13px; margin-bottom: 20px; }
		.vt-fm .woocommerce-error { background: #fff5f5; border: 1px solid #ffcccc; color: #cc0000; padding: 12px 16px; border-radius: 10px; font-size: 13px; margin-bottom: 20px; }
		.vt-fm .woocommerce-info { background: #e3f2fd; border: 1px solid #bbdefb; color: #1565c0; padding: 12px 16px; border-radius: 10px; font-size: 13px; margin-bottom: 20px; }
		.vt-fm ul { list-style: none; padding: 0; }
		.vt-fm ul li { padding: 6px 0; font-size: 14px; }

		/* === FOOTER === */
		.vt-ft { background: #111; color: #999; padding: 32px 40px; text-align: center; font-size: 13px; margin-top: auto; }
		.vt-ft a { color: #ccc; text-decoration: none; }
		.vt-ft a:hover { color: #fff; }

		/* === RESPONSIVE === */
		@media (max-width: 900px) {
			.vt-wrap { grid-template-columns: 1fr; padding: 24px 20px 60px; }
			.vt-acts { grid-template-columns: 1fr; }
			.vt-fm .form-row-half { grid-template-columns: 1fr; }
		}
		@media (max-width: 576px) {
			.vt-hdr-in { padding: 12px 20px; }
			.vt-ban { padding: 16px 20px; }
			.vt-wel, .vt-sec, .vt-prof { padding: 24px 20px; }
			.vt-nav-i { padding: 12px 16px; }
		}
	</style>
</head>
<body>

<header class="vt-hdr">
	<div class="vt-hdr-in">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="vt-logo">
			<?php $logo_url = function_exists( 'aether_logo_url' ) ? aether_logo_url() : '';
			if ( $logo_url ) : ?>
				<img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php bloginfo( 'name' ); ?>">
			<?php else : ?>
				<span class="vt-logo-t"><?php bloginfo( 'name' ); ?></span>
			<?php endif; ?>
		</a>
		<nav class="vt-nav">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a>
			<a href="<?php echo esc_url( $dashboard_url ); ?>" style="color:#111;font-weight:600;">My Account</a>
			<a href="<?php echo esc_url( $logout_url ); ?>">Log Out</a>
		</nav>
	</div>
</header>

<div class="vt-ban">
	<div class="vt-ban-in">
		<h1>My Account</h1>
		<div class="vt-crumb"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> &nbsp;/&nbsp; My Account</div>
	</div>
</div>

<div class="vt-wrap">
	<aside class="vt-side">
		<div class="vt-prof">
			<?php if ( $avatar_url ) : ?>
				<img src="<?php echo esc_url( $avatar_url ); ?>" alt="<?php echo esc_attr( $display_name ); ?>" class="vt-avatar">
			<?php else : ?>
				<div class="vt-avatar-fb"><?php echo esc_html( strtoupper( mb_substr( $display_name, 0, 1 ) ) ); ?></div>
			<?php endif; ?>
			<div class="vt-uname"><?php echo esc_html( $display_name ); ?></div>
			<div class="vt-uemail"><?php echo esc_html( $email ); ?></div>
			<div class="vt-stats">
				<div class="vt-stat">
					<div class="vt-stat-n"><?php echo esc_html( $orders_count ); ?></div>
					<div class="vt-stat-l">Orders</div>
				</div>
				<div class="vt-stat">
					<div class="vt-stat-n"><?php echo esc_html( $address_count ); ?></div>
					<div class="vt-stat-l">Addresses</div>
				</div>
			</div>
		</div>

		<nav class="vt-nav-box">
			<a href="<?php echo esc_url( $dashboard_url ); ?>" class="vt-nav-i <?php echo empty( $endpoint ) ? 'on' : ''; ?>">
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
				Dashboard
			</a>
			<a href="<?php echo esc_url( $orders_url ); ?>" class="vt-nav-i <?php echo 'orders' === $endpoint ? 'on' : ''; ?>">
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
				Orders
			</a>
			<a href="<?php echo esc_url( $address_url ); ?>" class="vt-nav-i <?php echo 'edit-address' === $endpoint ? 'on' : ''; ?>">
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
				Addresses
			</a>
			<a href="<?php echo esc_url( $details_url ); ?>" class="vt-nav-i <?php echo 'edit-account' === $endpoint ? 'on' : ''; ?>">
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
				Account Details
			</a>
			<a href="<?php echo esc_url( $logout_url ); ?>" class="vt-nav-i">
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
				Log Out
			</a>
		</nav>
	</aside>

	<main class="vt-main">
		<?php if ( empty( $endpoint ) ) : ?>
			<div class="vt-wel">
				<h2>Welcome back, <?php echo esc_html( $first_name ); ?>!</h2>
				<p>From your account dashboard you can view your recent orders, manage your shipping addresses, and update your account details.</p>
			</div>
			<div class="vt-acts">
				<a href="<?php echo esc_url( $orders_url ); ?>" class="vt-act">
					<div class="vt-act-ic crl">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
					</div>
					<h3>Orders</h3>
					<p>View and track your recent orders</p>
				</a>
				<a href="<?php echo esc_url( $address_url ); ?>" class="vt-act">
					<div class="vt-act-ic drk">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
					</div>
					<h3>Addresses</h3>
					<p>Manage your shipping & billing</p>
				</a>
				<a href="<?php echo esc_url( $details_url ); ?>" class="vt-act">
					<div class="vt-act-ic mtd">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
					</div>
					<h3>Account Details</h3>
					<p>Update your personal information</p>
				</a>
			</div>

		<?php elseif ( 'view-order' === $endpoint ) : ?>
			<div class="vt-sec">
				<h2>Order Details</h2>
				<?php if ( function_exists( 'woocommerce_account_content' ) ) { woocommerce_account_content(); } ?>
			</div>

		<?php elseif ( 'edit-address' === $endpoint || 'edit-account' === $endpoint || 'customer-logout' === $endpoint || 'lost-password' === $endpoint ) : ?>
			<div class="vt-sec vt-fm">
				<?php if ( function_exists( 'woocommerce_account_content' ) ) { woocommerce_account_content(); } ?>
			</div>

		<?php elseif ( 'orders' === $endpoint ) : ?>
			<div class="vt-sec">
				<h2>Your Orders</h2>
				<?php if ( function_exists( 'woocommerce_account_content' ) ) { woocommerce_account_content(); } ?>
			</div>

		<?php elseif ( 'downloads' === $endpoint ) : ?>
			<div class="vt-sec">
				<h2>Your Downloads</h2>
				<?php if ( function_exists( 'woocommerce_account_content' ) ) { woocommerce_account_content(); } ?>
			</div>

		<?php else : ?>
			<div class="vt-sec vt-fm">
				<?php if ( function_exists( 'woocommerce_account_content' ) ) { woocommerce_account_content(); } ?>
			</div>
		<?php endif; ?>
	</main>
</div>

<footer class="vt-ft">
	&copy; <?php echo esc_html( date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. All rights reserved.
	&nbsp;&middot;&nbsp;
	<a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>">Privacy Policy</a>
	&nbsp;&middot;&nbsp;
	<a href="<?php echo esc_url( home_url( '/terms-and-conditions/' ) ); ?>">Terms of Service</a>
</footer>

</body>
</html>
<?php
	return;
}

/*
 * Logged out, no ?auth param → redirect to ?auth=login
 */
wp_safe_redirect( add_query_arg( 'auth', 'login', get_permalink( wc_get_page_id( 'myaccount' ) ) ) );
exit;
