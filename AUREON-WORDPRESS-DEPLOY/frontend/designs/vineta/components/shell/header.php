<?php
/**
 * Vineta Shell Header — shadows the base AETHER shell/header component.
 *
 * Extracts the full Vineta mega-menu header from the frozen index.html
 * and rewrites Shopify paths to WordPress URLs.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// The Vineta helper function is defined in this pack's composer.php.
if ( function_exists( 'vineta_render_standalone_header' ) ) {
	vineta_render_standalone_header();
	return;
}

// Fallback: render the base AETHER shell header if the helper is unavailable.
$componentData = isset( $componentData ) ? (array) $componentData : array();
$brand      = isset( $componentData['brand'] ) ? $componentData['brand'] : '';
$brand_url  = isset( $componentData['brand_url'] ) ? $componentData['brand_url'] : '';
$menu       = isset( $componentData['menu'] ) ? (array) $componentData['menu'] : array();
$icons      = isset( $componentData['icons'] ) ? (array) $componentData['icons'] : array();
$cart_count = isset( $componentData['cart_count'] ) ? (int) $componentData['cart_count'] : 0;

$search   = isset( $icons['search'] ) ? $icons['search'] : '#';
$wishlist = isset( $icons['wishlist'] ) ? $icons['wishlist'] : '#';
$cart     = isset( $icons['cart'] ) ? $icons['cart'] : '#';
$account  = isset( $icons['account'] ) ? $icons['account'] : '#';
?>
<header class="header" id="header">
	<div class="header-container">
		<a href="<?php echo esc_url( $brand_url ); ?>" class="brand-logo"><?php echo esc_html( $brand ); ?></a>

		<nav class="main-nav" id="mainNav" data-phantom-menu="primary">
			<div class="nav-mobile-logo">
				<a href="<?php echo esc_url( $brand_url ); ?>" class="brand-logo"><?php echo esc_html( $brand ); ?></a>
			</div>
			<ul class="nav-links">
				<?php foreach ( $menu as $item ) : ?>
					<?php
					$label   = isset( $item['label'] ) ? $item['label'] : '';
					$url     = isset( $item['url'] ) ? $item['url'] : '#';
					$active  = ! empty( $item['active'] );
					$has_children = ! empty( $item['children'] );
					?>
					<?php if ( $has_children ) : ?>
						<li class="nav-dropdown">
							<a href="<?php echo esc_url( $url ); ?>" class="nav-link nav-dropdown-toggle"><?php echo esc_html( $label ); ?> <i class="fas fa-chevron-down"></i></a>
							<ul class="nav-dropdown-menu">
								<?php foreach ( $item['children'] as $child ) : ?>
									<li><a href="<?php echo esc_url( isset( $child['url'] ) ? $child['url'] : '#' ); ?>" class="nav-dropdown-link"><?php echo esc_html( isset( $child['label'] ) ? $child['label'] : '' ); ?></a></li>
								<?php endforeach; ?>
							</ul>
						</li>
					<?php else : ?>
						<li><a href="<?php echo esc_url( $url ); ?>" class="nav-link<?php echo $active ? ' active' : ''; ?>"><?php echo esc_html( $label ); ?></a></li>
					<?php endif; ?>
				<?php endforeach; ?>
			</ul>
			<div class="nav-mobile-icons">
				<a href="<?php echo esc_url( $search ); ?>" class="header-icon" aria-label="Search"><i class="fas fa-search"></i></a>
				<a href="<?php echo esc_url( $wishlist ); ?>" class="header-icon" aria-label="Wishlist"><i class="fas fa-heart"></i></a>
				<a href="<?php echo esc_url( $cart ); ?>" class="header-icon" aria-label="Cart"><i class="fas fa-shopping-bag"></i><span class="cart-count"><?php echo esc_html( $cart_count ); ?></span></a>
				<a href="<?php echo esc_url( $account ); ?>" class="header-icon" aria-label="Account"><i class="fas fa-user"></i></a>
			</div>
		</nav>

		<div class="header-actions">
			<a href="<?php echo esc_url( $search ); ?>" class="header-icon" aria-label="Search"><i class="fas fa-search"></i></a>
			<a href="<?php echo esc_url( $wishlist ); ?>" class="header-icon" aria-label="Wishlist"><i class="fas fa-heart"></i></a>
			<a href="<?php echo esc_url( $cart ); ?>" class="header-icon" aria-label="Cart"><i class="fas fa-shopping-bag"></i><span class="cart-count"><?php echo esc_html( $cart_count ); ?></span></a>
			<a href="<?php echo esc_url( $account ); ?>" class="header-icon" aria-label="Account"><i class="fas fa-user"></i></a>
			<button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="Menu">
				<span></span>
				<span></span>
				<span></span>
			</button>
		</div>
	</div>
</header>
