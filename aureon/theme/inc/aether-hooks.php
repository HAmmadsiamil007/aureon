<?php
/**
 * AETHER Component Hooks.
 *
 * Hooks AETHER components into Aureon's action hook system.
 * Each component is attached to the appropriate Aureon hook.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ─── Disable Aureon default components on front-end ──────────
// AETHER replaces Aureon's header, footer, and accessories.
add_action( 'after_setup_theme', 'aether_remove_aureon_components' );
function aether_remove_aureon_components() {
	if ( is_admin() ) {
		return;
	}
	// Remove Aureon default header (replaced by AETHER header at priority 5).
	remove_action( 'aureon_header', 'aureon_construct_header' );
	// Remove Aureon default footer and widgets (replaced by AETHER footer).
	remove_action( 'aureon_footer', 'aureon_construct_footer' );
	remove_action( 'aureon_footer', 'aureon_construct_footer_widgets', 5 );
	// Remove Aureon back-to-top button (AETHER has its own).
	remove_action( 'aureon_after_footer', 'aureon_back_to_top' );
	// Remove Aureon skip-to-content link (AETHER has its own).
	remove_action( 'aureon_before_header', 'aureon_do_skip_to_content_link', 2 );
}

// ─── Preloader ──────────────────────────────────────────────
add_action( 'wp_body_open', 'aether_do_preloader', 1 );
/**
 * Output the AETHER preloader.
 */
function aether_do_preloader() {
	$site_name = get_bloginfo( 'name' );
	?>
	<div id="preloader" aria-hidden="true">
		<div class="preloader-inner">
			<div class="preloader-logo"><?php echo esc_html( $site_name ); ?></div>
			<div class="preloader-bar">
				<div class="preloader-progress"></div>
			</div>
		</div>
	</div>
	<?php
}

// ─── Fog System ─────────────────────────────────────────────
add_action( 'wp_body_open', 'aether_do_fog', 2 );
/**
 * Output the AETHER cinematic fog system.
 */
function aether_do_fog() {
	?>
	<div id="fog-system">
		<div id="foglayer_01" class="fog">
			<div class="image01"></div>
			<div class="image02"></div>
		</div>
		<div id="foglayer_02" class="fog">
			<div class="image01"></div>
			<div class="image02"></div>
		</div>
		<div id="foglayer_03" class="fog">
			<div class="image01"></div>
			<div class="image02"></div>
		</div>
	</div>
	<?php
}

// ─── Skip to Content ────────────────────────────────────────
add_action( 'wp_body_open', 'aether_do_skip_link', 3 );
/**
 * Output the AETHER skip-to-content link.
 */
function aether_do_skip_link() {
	?>
	<a class="skip-to-content visually-hidden" href="#main">Skip to main content</a>
	<div class="page-content">
	<?php
}

// ─── Mobile Header ──────────────────────────────────────────
add_action( 'aureon_before_header', 'aether_do_mobile_header', 5 );
/**
 * Output the AETHER mobile header (shown on <=768px).
 */
function aether_do_mobile_header() {
	$settings   = aether_get_phantom_settings();
	$site_name  = get_bloginfo( 'name' );
	$announcement = esc_html( $settings['announcement_text'] );
	?>
	<div class="mobile-header" id="mobileHeader">
		<button class="mobile-hamburger" id="mobileHamburger" aria-label="Open menu">
			<span></span>
			<span></span>
			<span></span>
		</button>
		<div class="mobile-announcement" id="mobileAnnouncement">
			<span class="mobile-announcement-text active"><?php echo esc_html( strtoupper( $announcement ) ); ?></span>
			<span class="mobile-announcement-text">FREE SHIPPING ON ALL ORDERS</span>
		</div>
	</div>
	<?php
}

// ─── Mobile Slide-Out Menu ──────────────────────────────────
add_action( 'aureon_before_header', 'aether_do_mobile_menu', 6 );
/**
 * Output the AETHER mobile slide-out menu overlay.
 */
function aether_do_mobile_menu() {
	$site_name  = get_bloginfo( 'name' );
	$wc_active  = class_exists( 'WooCommerce' );
	$shop_url   = $wc_active ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
	$cart_url   = $wc_active ? wc_get_cart_url() : home_url( '/cart/' );
	$account_url = $wc_active ? ( is_user_logged_in() ? wc_get_account_endpoint_url( 'dashboard' ) : wc_get_page_permalink( 'myaccount' ) ) : home_url( '/my-account/' );
	?>
	<div class="mobile-menu-overlay" id="mobileMenuOverlay">
		<div class="mobile-menu" id="mobileMenu">
			<div class="mobile-menu-header">
				<button class="mobile-menu-close" id="mobileMenuClose" aria-label="Close menu">
					<i class="fas fa-times"></i>
				</button>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="mobile-menu-logo"><?php echo esc_html( $site_name ); ?></a>
				<div class="mobile-menu-spacer"></div>
			</div>
			<div class="mobile-menu-body">
				<div class="mobile-search">
					<form action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get" class="mobile-search-form">
						<input type="hidden" name="post_type" value="product">
						<i class="fas fa-search"></i>
						<input type="text" name="s" placeholder="Search products...">
					</form>
				</div>
				<nav class="mobile-nav">
					<?php
					if ( has_nav_menu( 'primary' ) ) {
						wp_nav_menu(
							array(
								'theme_location' => 'primary',
								'container'      => false,
								'menu_class'     => '',
								'fallback_cb'    => false,
								'depth'          => 1,
								'items_wrap'     => '%3$s',
							)
						);
					} else {
						?>
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="mobile-nav-link">Home</a>
						<a href="<?php echo esc_url( $shop_url ); ?>" class="mobile-nav-link">Shop</a>
						<a href="<?php echo esc_url( home_url( '/about/' ) ); ?>" class="mobile-nav-link">About</a>
						<a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>" class="mobile-nav-link">Blog</a>
						<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="mobile-nav-link">Contact</a>
						<?php
					}
					?>
				</nav>
				<div class="mobile-divider"></div>
				<nav class="mobile-nav">
					<a href="<?php echo esc_url( $account_url ); ?>" class="mobile-nav-link">Account</a>
					<a href="<?php echo esc_url( $cart_url ); ?>" class="mobile-nav-link">Cart</a>
				</nav>
				<div class="mobile-divider"></div>
				<a href="<?php echo esc_url( $shop_url ); ?>" class="mobile-cta">Shop Now</a>
			</div>
		</div>
	</div>
	<?php
}

// ─── Announcement Bar ───────────────────────────────────────
add_action( 'aureon_before_header', 'aether_do_announcement', 7 );
/**
 * Output the AETHER announcement bar.
 */
function aether_do_announcement() {
	$settings = aether_get_phantom_settings();

	// Respect the Customizer enable/disable setting.
	if ( ! $settings['announcement_enable'] ) {
		return;
	}
	?>
	<div class="announcement-bar" id="announcementBar">
		<div class="announcement-content">
			<span><i class="fas fa-truck"></i> <?php echo esc_html( $settings['announcement_text'] ); ?></span>
			<span class="separator">|</span>
			<span><i class="fas fa-bolt"></i> New Collection Dropping Soon</span>
			<span class="separator">|</span>
			<span><i class="fas fa-undo"></i> 30-Day Free Returns</span>
		</div>
	</div>
	<?php
}

// ─── Desktop Header ─────────────────────────────────────────
add_action( 'aureon_header', 'aether_do_header', 5 );
/**
 * Output the AETHER desktop header.
 *
 * Priority 5 ensures this fires before Aureon's default header (priority 10),
 * effectively replacing it on front-end pages.
 */
function aether_do_header() {
	// Only render on front-end, not in admin/customizer.
	if ( is_admin() || is_customize_preview() ) {
		return;
	}

	$site_name  = get_bloginfo( 'name' );
	$wc_active  = class_exists( 'WooCommerce' );
	$cart_count = $wc_active && WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
	$shop_url   = $wc_active ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
	$cart_url   = $wc_active ? wc_get_cart_url() : home_url( '/cart/' );
	$account_url = $wc_active ? ( is_user_logged_in() ? wc_get_account_endpoint_url( 'dashboard' ) : wc_get_page_permalink( 'myaccount' ) ) : home_url( '/my-account/' );
	?>
	<header class="header" id="header">
		<div class="header-container">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="brand-logo"><?php echo esc_html( $site_name ); ?></a>

			<nav class="main-nav" id="mainNav" data-phantom-menu="primary">
				<div class="nav-mobile-logo">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="brand-logo"><?php echo esc_html( $site_name ); ?></a>
				</div>
				<ul class="nav-links">
					<?php
					if ( has_nav_menu( 'primary' ) ) {
						wp_nav_menu(
							array(
								'theme_location' => 'primary',
								'container'      => false,
								'menu_class'     => 'nav-links',
								'fallback_cb'    => false,
								'depth'          => 2,
								'items_wrap'     => '%3$s',
							)
						);
					} else {
						?>
						<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="nav-link active">Home</a></li>
						<li class="nav-dropdown">
							<a href="<?php echo esc_url( $shop_url ); ?>" class="nav-link nav-dropdown-toggle">Collection <i class="fas fa-chevron-down"></i></a>
							<ul class="nav-dropdown-menu">
								<li><a href="<?php echo esc_url( $shop_url ); ?>" class="nav-dropdown-link">Men</a></li>
								<li><a href="<?php echo esc_url( $shop_url ); ?>" class="nav-dropdown-link">Women</a></li>
								<li><a href="<?php echo esc_url( $shop_url ); ?>" class="nav-dropdown-link">New Arrivals</a></li>
							</ul>
						</li>
						<li><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>" class="nav-link">About</a></li>
						<li><a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>" class="nav-link">Blog</a></li>
						<li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="nav-link">Contact</a></li>
						<?php
					}
					?>
				</ul>
				<div class="nav-mobile-icons">
					<a href="<?php echo esc_url( $shop_url ); ?>" class="header-icon" aria-label="Search"><i class="fas fa-search"></i></a>
					<a href="<?php echo esc_url( home_url( '/wishlist/' ) ); ?>" class="header-icon" aria-label="Wishlist"><i class="fas fa-heart"></i></a>
					<a href="<?php echo esc_url( $cart_url ); ?>" class="header-icon" aria-label="Cart"><i class="fas fa-shopping-bag"></i><span class="cart-count"><?php echo esc_html( $cart_count ); ?></span></a>
					<a href="<?php echo esc_url( $account_url ); ?>" class="header-icon" aria-label="Account"><i class="fas fa-user"></i></a>
				</div>
			</nav>

			<div class="header-actions">
				<a href="<?php echo esc_url( $shop_url ); ?>" class="header-icon" aria-label="Search"><i class="fas fa-search"></i></a>
				<a href="<?php echo esc_url( home_url( '/wishlist/' ) ); ?>" class="header-icon" aria-label="Wishlist"><i class="fas fa-heart"></i></a>
				<a href="<?php echo esc_url( $cart_url ); ?>" class="header-icon" aria-label="Cart"><i class="fas fa-shopping-bag"></i><span class="cart-count"><?php echo esc_html( $cart_count ); ?></span></a>
				<a href="<?php echo esc_url( $account_url ); ?>" class="header-icon" aria-label="Account"><i class="fas fa-user"></i></a>
				<button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="Menu">
					<span></span>
					<span></span>
					<span></span>
				</button>
			</div>
		</div>
	</header>
	<div id="main" tabindex="-1" class="visually-hidden-focusable" style="position:absolute;top:0"></div>
	<?php
}

// ─── Footer Newsletter ──────────────────────────────────────
add_action( 'aureon_before_footer', 'aether_do_footer_newsletter', 5 );
/**
 * Output the AETHER newsletter section before the footer.
 */
function aether_do_footer_newsletter() {
	$site_name = get_bloginfo( 'name' );
	?>
	<section class="newsletter-section" id="newsletter">
		<div class="newsletter-glow" aria-hidden="true"></div>
		<div class="container">
			<div class="newsletter-inner">
				<span class="section-label" data-motion-text="words">Stay Connected</span>
				<h2 class="newsletter-title" data-motion-text="words">JOIN THE <?php echo esc_html( strtoupper( $site_name ) ); ?></h2>
				<p class="newsletter-text">Subscribe for exclusive drops, early access, and <?php echo esc_html( $site_name ); ?> news.</p>
				<form class="newsletter-form" id="newsletterForm">
					<div class="newsletter-input-wrap">
						<input type="email" placeholder="Enter your email" required class="newsletter-input" id="newsletterEmail" aria-label="Email address">
						<button type="submit" class="newsletter-btn">
							<span class="newsletter-btn-text">Subscribe</span>
							<i class="fas fa-arrow-right newsletter-btn-icon"></i>
						</button>
					</div>
					<p class="newsletter-note"><i class="fas fa-lock"></i> No spam. Unsubscribe anytime.</p>
					<input type="hidden" name="aether_nonce" value="<?php echo esc_attr( wp_create_nonce( 'aether_nonce' ) ); ?>">
				</form>
				<div class="newsletter-success" id="newsletterSuccess">
					<i class="fas fa-check-circle"></i>
					<p>Welcome to the void. Check your inbox.</p>
				</div>
			</div>
		</div>
	</section>
	<?php
}

// ─── Footer ─────────────────────────────────────────────────
add_action( 'aureon_footer', 'aether_do_footer', 5 );
/**
 * Output the AETHER footer.
 *
 * Priority 5 fires before Aureon's default footer (priority 10).
 */
function aether_do_footer() {
	$site_name  = get_bloginfo( 'name' );
	$year       = gmdate( 'Y' );
	$wc_active  = class_exists( 'WooCommerce' );
	$shop_url   = $wc_active ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
	$account_url = $wc_active ? wc_get_page_permalink( 'myaccount' ) : home_url( '/my-account/' );

	// Social media URLs from Customizer.
	$social_instagram = esc_url( get_theme_mod( 'aether_social_instagram', '#' ) );
	$social_twitter   = esc_url( get_theme_mod( 'aether_social_twitter', '#' ) );
	$social_tiktok    = esc_url( get_theme_mod( 'aether_social_tiktok', '#' ) );
	$social_youtube   = esc_url( get_theme_mod( 'aether_social_youtube', '#' ) );
	$social_facebook  = esc_url( get_theme_mod( 'aether_social_facebook', '#' ) );
	?>
	<footer class="footer" id="footer" role="contentinfo" aria-label="Site footer" data-phantom-menu="footer">
		<div class="container">
			<div class="footer-top">
				<div class="footer-brand">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="footer-logo"><?php echo esc_html( $site_name ); ?></a>
					<p class="footer-tagline">Step Into The Void</p>
					<div class="footer-social" aria-label="Social media links">
						<?php if ( '#' !== $social_instagram ) : ?>
							<a href="<?php echo esc_url( $social_instagram ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
						<?php endif; ?>
						<?php if ( '#' !== $social_twitter ) : ?>
							<a href="<?php echo esc_url( $social_twitter ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
						<?php endif; ?>
						<?php if ( '#' !== $social_tiktok ) : ?>
							<a href="<?php echo esc_url( $social_tiktok ); ?>" target="_blank" rel="noopener noreferrer" aria-label="TikTok"><i class="fab fa-tiktok"></i></a>
						<?php endif; ?>
						<?php if ( '#' !== $social_youtube ) : ?>
							<a href="<?php echo esc_url( $social_youtube ); ?>" target="_blank" rel="noopener noreferrer" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
						<?php endif; ?>
						<?php if ( '#' !== $social_facebook ) : ?>
							<a href="<?php echo esc_url( $social_facebook ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
						<?php endif; ?>
					</div>
				</div>

				<div class="footer-links">
					<h4 class="footer-heading">Shop</h4>
					<ul>
						<li><a href="<?php echo esc_url( $shop_url ); ?>">Men</a></li>
						<li><a href="<?php echo esc_url( $shop_url ); ?>">Women</a></li>
						<li><a href="<?php echo esc_url( $shop_url ); ?>">New Arrivals</a></li>
						<li><a href="<?php echo esc_url( $shop_url ); ?>">Bestsellers</a></li>
					</ul>
				</div>

				<div class="footer-links">
					<h4 class="footer-heading">Support</h4>
					<ul>
						<li><a href="<?php echo esc_url( home_url( '/faq/' ) ); ?>">FAQ</a></li>
						<li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">Contact Us</a></li>
						<li><a href="<?php echo esc_url( $account_url ); ?>">Shipping Info</a></li>
						<li><a href="<?php echo esc_url( $account_url ); ?>">Returns & Exchanges</a></li>
					</ul>
				</div>

				<div class="footer-links">
					<h4 class="footer-heading">Company</h4>
					<ul>
						<li><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>">About Us</a></li>
						<li><a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>">Blog</a></li>
						<li><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>">Careers</a></li>
					</ul>
				</div>

				<div class="footer-newsletter">
					<h4 class="footer-heading">Stay in the Loop</h4>
					<p>Get exclusive drops, early access, and <?php echo esc_html( $site_name ); ?> news.</p>
					<form class="footer-newsletter-form" aria-label="Newsletter subscription" id="footerNewsletterForm">
						<input type="email" placeholder="Your email" required aria-label="Email address">
						<button type="submit" aria-label="Subscribe"><i class="fas fa-arrow-right"></i></button>
					</form>
				</div>
			</div>

			<div class="footer-bottom">
				<div class="footer-legal">
					<span>&copy; <?php echo esc_html( $year ); ?> <?php echo esc_html( $site_name ); ?>. All Rights Reserved.</span>
					<a href="<?php echo esc_url( get_privacy_policy_url() ); ?>">Privacy</a>
					<a href="#">Terms</a>
					<a href="#">Cookies</a>
				</div>
				<div class="footer-payments">
					<i class="fab fa-cc-visa"></i>
					<i class="fab fa-cc-mastercard"></i>
					<i class="fab fa-cc-amex"></i>
					<i class="fab fa-cc-paypal"></i>
					<i class="fab fa-apple-pay"></i>
				</div>
			</div>
		</div>
	</footer>
	<?php
}

// ─── Close Page Content Wrapper ─────────────────────────────
add_action( 'aureon_after_footer', 'aether_close_page_content', 5 );
/**
 * Close the .page-content wrapper opened by the skip link.
 */
function aether_close_page_content() {
	echo '</div><!-- .page-content -->';
}
