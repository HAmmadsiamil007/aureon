<?php
/**
 * Premium Login Page — Vineta Standalone
 *
 * Standalone login page with premium aesthetic matching the Vineta design.
 * Uses frozen Vineta header/footer. Posts to WooCommerce login handler.
 *
 * @package Aureon
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Redirect logged-in users to my-account dashboard.
if ( is_user_logged_in() ) {
	$account_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/my-account/' );
	wp_safe_redirect( $account_url );
	exit;
}

$pack_url = function_exists( 'aether_pack_url' ) ? aether_pack_url() : '';
$site_url = home_url( '/' );
$register_url = add_query_arg( 'auth', 'register', get_permalink( wc_get_page_id( 'myaccount' ) ) );
$account_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/my-account/' );
$lost_pw_url = function_exists( 'wc_lostpassword_url' ) ? wc_lostpassword_url() : '#';

// Try to find a dedicated register page
$register_page_id = wc_get_page_id( 'myaccount' );
if ( $register_page_id > 0 ) {
	$register_url = add_query_arg( 'auth', 'register', get_permalink( $register_page_id ) );
}

// Handle WooCommerce login errors
$login_error = '';
if ( isset( $_GET['login'] ) && 'failed' === $_GET['login'] ) {
	$login_error = __( 'Invalid username or password. Please try again.', 'aureon' );
}
if ( isset( $_GET['loggedout'] ) && 'true' === $_GET['loggedout'] ) {
	$login_error = '';
}
if ( isset( $_GET['wc-error'] ) ) {
	$login_error = wp_strip_all_tags( wp_unslash( $_GET['wc-error'] ) );
}

// Handle WooCommerce login form submission.
if ( isset( $_POST['woocommerce-login-nonce'] ) && isset( $_POST['username'] ) ) {
	if ( ! function_exists( 'wc' ) || ! function_exists( 'wp_signon' ) ) {
		return;
	}
	$nonce_value = wp_unslash( $_POST['woocommerce-login-nonce'] ); // phpcs:ignore
	if ( ! wp_verify_nonce( $nonce_value, 'woocommerce-login' ) ) {
		$login_error = __( 'Security check failed. Please try again.', 'aureon' );
	} else {
		$username = sanitize_user( wp_unslash( $_POST['username'] ) ); // phpcs:ignore
		$password = wp_unslash( $_POST['password'] ); // phpcs:ignore
		$remember = isset( $_POST['rememberme'] ) && 'forever' === $_POST['rememberme'];

		$creds = array(
			'user_login'    => $username,
			'user_password' => $password,
			'remember'      => $remember,
		);

		$user = wp_signon( $creds, is_ssl() );

		if ( is_wp_error( $user ) ) {
			$login_error = __( 'Invalid username or password. Please try again.', 'aureon' );
		} else {
			wp_safe_redirect( $account_url );
			exit;
		}
	}
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php esc_html_e( 'Login', 'aureon' ); ?> &mdash; <?php bloginfo( 'name' ); ?></title>
	<?php
	// Load Vineta fonts
	if ( $pack_url ) :
	?>
	<link rel="stylesheet" href="<?php echo esc_url( $pack_url ); ?>fonts/fonts.css">
	<link rel="stylesheet" href="<?php echo esc_url( $pack_url ); ?>css/styles.css">
	<?php endif; ?>
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

		/* === HEADER === */
		.vt-auth-header {
			background: #fff;
			border-bottom: 1px solid #ebebeb;
			position: sticky;
			top: 0;
			z-index: 100;
		}
		.vt-auth-header-inner {
			display: flex;
			align-items: center;
			justify-content: space-between;
			max-width: 1400px;
			margin: 0 auto;
			padding: 14px 40px;
		}
		.vt-auth-logo { display: flex; align-items: center; gap: 10px; text-decoration: none; }
		.vt-auth-logo img { height: 36px; width: auto; }
		.vt-auth-logo-text { font-size: 22px; font-weight: 700; color: #111; letter-spacing: -0.5px; }
		.vt-auth-nav { display: flex; align-items: center; gap: 28px; }
		.vt-auth-nav a {
			text-decoration: none;
			color: #555;
			font-size: 14px;
			font-weight: 500;
			transition: color 0.2s;
			letter-spacing: 0.3px;
		}
		.vt-auth-nav a:hover { color: #111; }

		/* === MAIN === */
		.vt-auth-main {
			flex: 1;
			display: flex;
			align-items: center;
			justify-content: center;
			padding: 60px 24px 80px;
		}
		.vt-auth-card {
			background: #fff;
			border-radius: 20px;
			box-shadow: 0 4px 40px rgba(0,0,0,0.06);
			width: 100%;
			max-width: 460px;
			padding: 52px 48px;
			position: relative;
			overflow: hidden;
		}
		.vt-auth-card::before {
			content: '';
			position: absolute;
			top: 0;
			left: 0;
			right: 0;
			height: 4px;
			background: linear-gradient(90deg, #ff6f61 0%, #ff9a90 50%, #ffc5c0 100%);
		}

		/* === TYPOGRAPHY === */
		.vt-auth-title {
			font-size: 28px;
			font-weight: 600;
			color: #111;
			margin-bottom: 4px;
			letter-spacing: -0.5px;
		}
		.vt-auth-subtitle {
			font-size: 14px;
			color: #888;
			margin-bottom: 36px;
			line-height: 1.5;
		}

		/* === FORM === */
		.vt-auth-form { display: flex; flex-direction: column; gap: 0; }
		.vt-auth-field { margin-bottom: 20px; position: relative; }
		.vt-auth-field label {
			display: block;
			font-size: 13px;
			font-weight: 500;
			color: #333;
			margin-bottom: 8px;
			letter-spacing: 0.2px;
		}
		.vt-auth-field label .req { color: #ff6f61; }
		.vt-auth-field input {
			width: 100%;
			padding: 14px 18px;
			border: 1.5px solid #e8e8e8;
			border-radius: 10px;
			font-size: 14px;
			font-family: 'Poppins', sans-serif;
			color: #111;
			background: #fafafa;
			transition: all 0.3s ease;
			outline: none;
		}
		.vt-auth-field input::placeholder { color: #bbb; }
		.vt-auth-field input:focus {
			border-color: #111;
			background: #fff;
			box-shadow: 0 0 0 3px rgba(17,17,17,0.04);
		}
		.vt-auth-field input:hover { border-color: #ccc; }

		.vt-auth-options {
			display: flex;
			align-items: center;
			justify-content: space-between;
			margin-bottom: 28px;
		}
		.vt-auth-remember {
			display: flex;
			align-items: center;
			gap: 8px;
			cursor: pointer;
			font-size: 13px;
			color: #555;
		}
		.vt-auth-remember input[type="checkbox"] {
			width: 16px;
			height: 16px;
			accent-color: #111;
			cursor: pointer;
		}
		.vt-auth-forgot {
			font-size: 13px;
			color: #888;
			text-decoration: none;
			transition: color 0.2s;
		}
		.vt-auth-forgot:hover { color: #ff6f61; }

		/* === BUTTON === */
		.vt-auth-btn {
			width: 100%;
			padding: 15px 32px;
			background: #111;
			color: #fff;
			border: none;
			border-radius: 99px;
			font-size: 15px;
			font-weight: 600;
			font-family: 'Poppins', sans-serif;
			cursor: pointer;
			transition: all 0.3s ease;
			letter-spacing: 0.5px;
			text-transform: uppercase;
		}
		.vt-auth-btn:hover { background: #333; transform: translateY(-1px); box-shadow: 0 4px 16px rgba(0,0,0,0.15); }
		.vt-auth-btn:active { transform: translateY(0); }

		/* === DIVIDER === */
		.vt-auth-divider {
			display: flex;
			align-items: center;
			gap: 16px;
			margin: 28px 0;
		}
		.vt-auth-divider::before,
		.vt-auth-divider::after {
			content: '';
			flex: 1;
			height: 1px;
			background: #e8e8e8;
		}
		.vt-auth-divider span {
			font-size: 12px;
			color: #aaa;
			text-transform: uppercase;
			letter-spacing: 1px;
			font-weight: 500;
		}

		/* === REGISTER LINK === */
		.vt-auth-register {
			text-align: center;
			font-size: 14px;
			color: #888;
		}
		.vt-auth-register a {
			color: #111;
			text-decoration: none;
			font-weight: 600;
			transition: color 0.2s;
			border-bottom: 2px solid transparent;
			padding-bottom: 1px;
		}
		.vt-auth-register a:hover { color: #ff6f61; border-bottom-color: #ff6f61; }

		/* === ERROR === */
		.vt-auth-error {
			background: #fff5f5;
			border: 1px solid #ffcccc;
			color: #cc0000;
			padding: 12px 16px;
			border-radius: 10px;
			font-size: 13px;
			margin-bottom: 24px;
			display: flex;
			align-items: center;
			gap: 8px;
		}
		.vt-auth-error::before { content: '\26A0'; font-size: 16px; }

		/* === FOOTER === */
		.vt-auth-footer {
			background: #111;
			color: #999;
			padding: 32px 40px;
			text-align: center;
			font-size: 13px;
		}
		.vt-auth-footer a { color: #ccc; text-decoration: none; }
		.vt-auth-footer a:hover { color: #fff; }

		/* === RESPONSIVE === */
		@media (max-width: 576px) {
			.vt-auth-card { padding: 40px 28px; border-radius: 16px; }
			.vt-auth-title { font-size: 24px; }
			.vt-auth-header-inner { padding: 12px 20px; }
			.vt-auth-nav { gap: 16px; }
			.vt-auth-main { padding: 40px 16px 60px; }
		}
	</style>
</head>
<body>

<!-- Header -->
<header class="vt-auth-header">
	<div class="vt-auth-header-inner">
		<a href="<?php echo esc_url( $site_url ); ?>" class="vt-auth-logo">
			<?php
			// Try to load the customizer logo, else use text.
			$logo_url = '';
			if ( function_exists( 'aether_logo_url' ) ) {
				$logo_url = aether_logo_url();
			}
			if ( $logo_url ) :
			?>
				<img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php bloginfo( 'name' ); ?>">
			<?php else : ?>
				<span class="vt-auth-logo-text"><?php bloginfo( 'name' ); ?></span>
			<?php endif; ?>
		</a>
		<nav class="vt-auth-nav">
			<a href="<?php echo esc_url( $site_url ); ?>">Home</a>
			<a href="<?php echo esc_url( $account_url ); ?>">My Account</a>
		</nav>
	</div>
</header>

<!-- Main -->
<main class="vt-auth-main">
	<div class="vt-auth-card">
		<h1 class="vt-auth-title">Welcome Back</h1>
		<p class="vt-auth-subtitle">Sign in to your account to continue shopping</p>

		<?php if ( $login_error ) : ?>
			<div class="vt-auth-error"><?php echo esc_html( $login_error ); ?></div>
		<?php endif; ?>

		<!-- WooCommerce Login Form -->
		<form class="vt-auth-form" method="post" action="<?php echo esc_url( add_query_arg( 'auth', 'login', get_permalink( wc_get_page_id( 'myaccount' ) ) ) ); ?>">
			<?php do_action( 'woocommerce_login_form_start' ); ?>

			<div class="vt-auth-field">
				<label for="username">Email Address <span class="req">*</span></label>
				<input type="text" class="woocommerce-Input" name="username" id="username" autocomplete="username" placeholder="you@example.com" value="<?php echo esc_attr( ! empty( $_POST['username'] ) ? wp_unslash( $_POST['username'] ) : '' ); // phpcs:ignore ?>">
			</div>

			<div class="vt-auth-field">
				<label for="password">Password <span class="req">*</span></label>
				<input class="woocommerce-Input" type="password" name="password" id="password" autocomplete="current-password" placeholder="Enter your password">
			</div>

			<?php do_action( 'woocommerce_login_form' ); ?>

			<div class="vt-auth-options">
				<label class="vt-auth-remember">
					<input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" checked>
					<span>Remember me</span>
				</label>
				<a href="<?php echo esc_url( $lost_pw_url ); ?>" class="vt-auth-forgot">Forgot password?</a>
			</div>

			<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
			<button type="submit" class="vt-auth-btn" name="login" value="Login">Sign In</button>

			<?php do_action( 'woocommerce_login_form_end' ); ?>
		</form>

		<div class="vt-auth-divider"><span>or</span></div>

		<p class="vt-auth-register">
			Don't have an account? <a href="<?php echo esc_url( $register_url ); ?>">Create one now</a>
		</p>
	</div>
</main>

<!-- Footer -->
<footer class="vt-auth-footer">
	&copy; <?php echo esc_html( date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. All rights reserved.
	&nbsp;&middot;&nbsp;
	<a href="<?php echo esc_url( $site_url ); ?>">Privacy Policy</a>
	&nbsp;&middot;&nbsp;
	<a href="<?php echo esc_url( $site_url ); ?>">Terms of Service</a>
</footer>

</body>
</html>
