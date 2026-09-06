<?php
/**
 * Premium Register Page — Vineta Standalone
 *
 * Standalone registration page with premium aesthetic matching the Vineta design.
 * Uses frozen Vineta header/footer. Posts to WooCommerce register handler.
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

$account_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/my-account/' );

$register_enabled = 'yes' === get_option( 'woocommerce_enable_myaccount_registration' );
if ( ! $register_enabled ) {
	wp_safe_redirect( $account_url );
	exit;
}

// Handle WooCommerce register form submission.
if ( isset( $_POST['woocommerce-register-nonce'] ) && isset( $_POST['email'] ) ) {
	if ( ! function_exists( 'wc' ) ) {
		return;
	}
	$nonce_value = wp_unslash( $_POST['woocommerce-register-nonce'] ); // phpcs:ignore
	if ( ! wp_verify_nonce( $nonce_value, 'woocommerce-register' ) ) {
		$reg_error = __( 'Security check failed. Please try again.', 'aureon' );
	} else {
		$email    = sanitize_email( wp_unslash( $_POST['email'] ) ); // phpcs:ignore
		$password = isset( $_POST['password'] ) ? wp_unslash( $_POST['password'] ) : ''; // phpcs:ignore
		$first    = isset( $_POST['first_name'] ) ? sanitize_text_field( wp_unslash( $_POST['first_name'] ) ) : ''; // phpcs:ignore
		$last     = isset( $_POST['last_name'] ) ? sanitize_text_field( wp_unslash( $_POST['last_name'] ) ) : ''; // phpcs:ignore

		$reg_error = '';
		if ( empty( $email ) || ! is_email( $email ) ) {
			$reg_error = __( 'Please enter a valid email address.', 'aureon' );
		} elseif ( empty( $password ) ) {
			$reg_error = __( 'Please enter a password.', 'aureon' );
		} elseif ( username_exists( $email ) || email_exists( $email ) ) {
			$reg_error = __( 'An account already exists with that email address.', 'aureon' );
		} else {
			$new_user_id = wp_create_user( $email, $password, $email );
			if ( is_wp_error( $new_user_id ) ) {
				$reg_error = $new_user_id->get_error_message();
			} else {
				// Set display name from first/last.
				$display_name = trim( $first . ' ' . $last );
				if ( $display_name ) {
					wp_update_user( array(
						'ID'           => $new_user_id,
						'display_name' => $display_name,
						'first_name'   => $first,
						'last_name'    => $last,
					) );
				}

				// Log in the new user.
				$creds = array(
					'user_login'    => $email,
					'user_password' => $password,
					'remember'      => true,
				);
				$user = wp_signon( $creds, is_ssl() );
				if ( ! is_wp_error( $user ) ) {
					wp_safe_redirect( $account_url );
					exit;
				}
			}
		}
	}
}

$pack_url = function_exists( 'aether_pack_url' ) ? aether_pack_url() : '';
$site_url = home_url( '/' );
$login_url = add_query_arg( 'auth', 'login', get_permalink( wc_get_page_id( 'myaccount' ) ) );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php esc_html_e( 'Create Account', 'aureon' ); ?> &mdash; <?php bloginfo( 'name' ); ?></title>
	<?php if ( $pack_url ) : ?>
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

		.vt-auth-field-row {
			display: grid;
			grid-template-columns: 1fr 1fr;
			gap: 16px;
		}
		.vt-auth-field-row .vt-auth-field { margin-bottom: 20px; }

		.vt-auth-privacy {
			font-size: 12px;
			color: #999;
			line-height: 1.6;
			margin-bottom: 28px;
		}
		.vt-auth-privacy a { color: #555; text-decoration: underline; }

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

		/* === LOGIN LINK === */
		.vt-auth-login {
			text-align: center;
			font-size: 14px;
			color: #888;
		}
		.vt-auth-login a {
			color: #111;
			text-decoration: none;
			font-weight: 600;
			transition: color 0.2s;
			border-bottom: 2px solid transparent;
			padding-bottom: 1px;
		}
		.vt-auth-login a:hover { color: #ff6f61; border-bottom-color: #ff6f61; }

		/* === BENEFITS === */
		.vt-auth-benefits {
			margin-top: 32px;
			padding-top: 28px;
			border-top: 1px solid #f0f0f0;
		}
		.vt-auth-benefits-title {
			font-size: 12px;
			color: #aaa;
			text-transform: uppercase;
			letter-spacing: 1px;
			font-weight: 500;
			margin-bottom: 16px;
		}
		.vt-auth-benefits ul {
			list-style: none;
			padding: 0;
		}
		.vt-auth-benefits li {
			display: flex;
			align-items: center;
			gap: 10px;
			font-size: 13px;
			color: #555;
			padding: 6px 0;
		}
		.vt-auth-benefits li::before {
			content: '\2713';
			width: 20px;
			height: 20px;
			background: #ff6f61;
			color: #fff;
			border-radius: 50%;
			display: flex;
			align-items: center;
			justify-content: center;
			font-size: 11px;
			flex-shrink: 0;
		}

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
			.vt-auth-field-row { grid-template-columns: 1fr; gap: 0; }
		}
	</style>
</head>
<body>

<!-- Header -->
<header class="vt-auth-header">
	<div class="vt-auth-header-inner">
		<a href="<?php echo esc_url( $site_url ); ?>" class="vt-auth-logo">
			<?php
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
			<a href="<?php echo esc_url( $login_url ); ?>">Sign In</a>
		</nav>
	</div>
</header>

<!-- Main -->
<main class="vt-auth-main">
	<div class="vt-auth-card">
		<h1 class="vt-auth-title">Create Account</h1>
		<p class="vt-auth-subtitle">Join us and discover a curated shopping experience</p>

		<?php if ( ! empty( $reg_error ) ) : ?>
			<div class="vt-auth-error"><?php echo esc_html( $reg_error ); ?></div>
		<?php endif; ?>

		<!-- WooCommerce Register Form -->
		<form class="vt-auth-form" method="post" action="<?php echo esc_url( add_query_arg( 'auth', 'register', get_permalink( wc_get_page_id( 'myaccount' ) ) ) ); ?>">
			<?php do_action( 'woocommerce_register_form_start' ); ?>

			<div class="vt-auth-field-row">
				<div class="vt-auth-field">
					<label for="reg_first_name">First Name</label>
					<input type="text" name="first_name" id="reg_first_name" autocomplete="given-name" placeholder="John">
				</div>
				<div class="vt-auth-field">
					<label for="reg_last_name">Last Name</label>
					<input type="text" name="last_name" id="reg_last_name" autocomplete="family-name" placeholder="Doe">
				</div>
			</div>

			<div class="vt-auth-field">
				<label for="reg_email">Email Address <span class="req">*</span></label>
				<input type="email" class="woocommerce-Input" name="email" id="reg_email" autocomplete="email" placeholder="you@example.com" value="<?php echo esc_attr( ! empty( $_POST['email'] ) ? wp_unslash( $_POST['email'] ) : '' ); // phpcs:ignore ?>">
			</div>

			<div class="vt-auth-field">
				<label for="reg_password">Password <span class="req">*</span></label>
				<input type="password" class="woocommerce-Input" name="password" id="reg_password" autocomplete="new-password" placeholder="Create a strong password">
			</div>

			<?php do_action( 'woocommerce_register_form' ); ?>

			<p class="vt-auth-privacy">
				By creating an account, you agree to our <a href="<?php echo esc_url( $site_url ); ?>">Privacy Policy</a> and <a href="<?php echo esc_url( $site_url ); ?>">Terms of Service</a>. Your personal data will be used to support your experience throughout this website.
			</p>

			<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
			<button type="submit" class="vt-auth-btn" name="register" value="Register">Create My Account</button>

			<?php do_action( 'woocommerce_register_form_end' ); ?>
		</form>

		<div class="vt-auth-benefits">
			<div class="vt-auth-benefits-title">Member Benefits</div>
			<ul>
				<li>Track your orders in real-time</li>
				<li>Save items to your wishlist</li>
				<li>Get exclusive access to new collections</li>
				<li>Enjoy member-only offers and early sales</li>
			</ul>
		</div>

		<div class="vt-auth-divider"><span>or</span></div>

		<p class="vt-auth-login">
			Already have an account? <a href="<?php echo esc_url( $login_url ); ?>">Sign in here</a>
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
