<?php
/**
 * WooCommerce My Account Template (AETHER).
 *
 * Logged in  → AETHER dashboard (account/profile component) on the
 *              dashboard endpoint; stock endpoint content (orders,
 *              addresses, account details) inside the AETHER frame on
 *              the other endpoints.
 * Logged out → AETHER-styled login / register forms posting straight to
 *              WooCommerce's form handlers.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

if ( ! function_exists( 'aether_render_component' ) ) {
	get_footer();
	return;
}

$is_logged_in = is_user_logged_in();
$endpoint     = function_exists( 'WC' ) && WC()->query ? (string) WC()->query->get_current_endpoint() : '';

aether_render_component( 'hero/page-banner', array(
	'title' => __( 'My Account', 'aureon' ),
	'crumbs' => array(
		array( 'label' => __( 'Home', 'aureon' ), 'url' => home_url( '/' ) ),
		array( 'label' => __( 'My Account', 'aureon' ), 'url' => '' ),
	),
) );

if ( $is_logged_in && '' === $endpoint ) :

	// Dashboard — the AETHER account profile.
	$account = function_exists( 'aether_adapter_account' ) ? aether_adapter_account() : array();
	if ( ! empty( $account ) ) :
		?>
		<section class="checkout-section" data-phantom-bg="hero">
			<div class="container">
				<?php aether_render_component( 'account/profile', $account ); ?>
			</div>
		</section>
		<?php
	endif;

elseif ( $is_logged_in && 'orders' === $endpoint ) :

	// Orders endpoint — the AETHER orders component fed by real WC orders.
	$orders_data = function_exists( 'aether_adapter_account_orders' ) ? aether_adapter_account_orders() : array();
	?>
	<section class="checkout-section" data-phantom-bg="hero">
		<div class="container">
			<div class="row g-5">
				<div class="col-lg-4">
					<div class="aether-wc">
						<?php
						if ( function_exists( 'woocommerce_account_navigation' ) ) {
							woocommerce_account_navigation();
						}
						?>
					</div>
				</div>
				<div class="col-lg-8">
					<?php
					if ( function_exists( 'aether_render_component' ) ) {
						aether_render_component( 'account/orders', $orders_data );
					}
					?>
				</div>
			</div>
		</div>
	</section>
	<?php

elseif ( $is_logged_in ) :

	// Orders / addresses / account details endpoints — stock WC content,
	// framed and styled by the .aether-wc rules in pages.css.
	?>
	<section class="checkout-section" data-phantom-bg="hero">
		<div class="container">
			<div class="aether-wc">
				<?php
				if ( function_exists( 'woocommerce_account_navigation' ) ) {
					woocommerce_account_navigation();
				}
				if ( function_exists( 'woocommerce_account_content' ) ) {
					woocommerce_account_content();
				}
				?>
			</div>
		</div>
	</section>
	<?php

elseif ( ! $is_logged_in && 'lost-password' === $endpoint ) :

	// Lost password endpoint — stock WC form inside the AETHER frame.
	?>
	<section class="checkout-section" data-phantom-bg="hero">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-lg-6">
					<div class="checkout-form-wrap aether-wc">
						<?php
						if ( function_exists( 'woocommerce_account_content' ) ) {
							woocommerce_account_content();
						}
						?>
					</div>
				</div>
			</div>
		</div>
	</section>
	<?php

else :

	// Logged out — login + register forms in the AETHER frame.
	$register_enabled = 'yes' === get_option( 'woocommerce_enable_myaccount_registration' );
	?>
	<section class="checkout-section" data-phantom-bg="hero">
		<div class="container">
			<div class="row g-5">
				<div class="col-lg-6">
					<div class="checkout-form-wrap aether-wc">
						<h3><?php esc_html_e( 'Login', 'aureon' ); ?></h3>
						<form class="woocommerce-form-login" method="post">
							<?php do_action( 'woocommerce_login_form_start' ); ?>
							<div class="form-group">
								<label for="username"><?php esc_html_e( 'Email', 'aureon' ); ?> <span style="color:var(--gold);">*</span></label>
								<input type="text" class="woocommerce-Input" name="username" id="username" autocomplete="username" value="<?php echo esc_attr( ! empty( $_POST['username'] ) ? wp_unslash( $_POST['username'] ) : '' ); // phpcs:ignore ?>">
							</div>
							<div class="form-group">
								<label for="password"><?php esc_html_e( 'Password', 'aureon' ); ?> <span style="color:var(--gold);">*</span></label>
								<input class="woocommerce-Input" type="password" name="password" id="password" autocomplete="current-password">
							</div>
							<?php do_action( 'woocommerce_login_form' ); ?>
							<div class="form-group">
								<label class="checkbox" style="cursor:pointer;">
									<input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" checked />
									<span style="text-transform:none;letter-spacing:0;color:var(--white);"><?php esc_html_e( 'Remember me', 'aureon' ); ?></span>
								</label>
							</div>
							<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
							<button type="submit" class="woocommerce-button button woocommerce-form-login__submit" name="login" value="Login"><?php esc_html_e( 'Login', 'aureon' ); ?></button>
							<div style="margin-top:16px;font-size:0.85rem;">
								<a href="<?php echo esc_url( wc_lostpassword_url() ); ?>" style="color:var(--chrome);text-decoration:none;"><?php esc_html_e( 'Lost your password?', 'aureon' ); ?></a>
							</div>
							<?php do_action( 'woocommerce_login_form_end' ); ?>
						</form>
					</div>
				</div>
				<?php if ( $register_enabled ) : ?>
					<div class="col-lg-6">
						<div class="checkout-form-wrap aether-wc">
							<h3><?php esc_html_e( 'Register', 'aureon' ); ?></h3>
							<form method="post" class="woocommerce-form-register">
								<?php do_action( 'woocommerce_register_form_start' ); ?>
								<div class="form-group">
									<label for="reg_email"><?php esc_html_e( 'Email', 'aureon' ); ?> <span style="color:var(--gold);">*</span></label>
									<input type="email" class="woocommerce-Input" name="email" id="reg_email" value="<?php echo esc_attr( ! empty( $_POST['email'] ) ? wp_unslash( $_POST['email'] ) : '' ); // phpcs:ignore ?>" autocomplete="email">
								</div>
								<div class="form-group">
									<label for="reg_password"><?php esc_html_e( 'Password', 'aureon' ); ?> <span style="color:var(--gold);">*</span></label>
									<input type="password" class="woocommerce-Input" name="password" id="reg_password" autocomplete="new-password">
								</div>
								<?php do_action( 'woocommerce_register_form' ); ?>
								<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
								<button type="submit" class="woocommerce-Button button" name="register" value="Register"><?php esc_html_e( 'Register', 'aureon' ); ?></button>
								<?php do_action( 'woocommerce_register_form_end' ); ?>
							</form>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</section>
	<?php
endif;

get_footer();
