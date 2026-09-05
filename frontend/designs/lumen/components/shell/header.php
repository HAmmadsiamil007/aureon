<?php
/**
 * Lumen site header — light editorial shell (M10 proof pack).
 *
 * Key:    'shell/header' (override)
 * Source: client frontend "top navigation bar"
 * Props:  same schema as engine shell/header:
 *         brand, brand_url, menu, icons, cart_count.
 * Contract: keeps #header, #mainNav, .nav-link, .nav-dropdown-toggle,
 *           .header-icon aria-labels, .cart-count — platform JS operates
 *           unchanged. Presentation lives in css/lumen.css.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

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
<header class="lumen-header header" id="header">
	<div class="container lumen-header-inner">
		<a href="<?php echo esc_url( $brand_url ); ?>" class="lumen-logo"><?php echo esc_html( $brand ); ?></a>

		<nav class="lumen-nav" id="mainNav">
			<ul>
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
		</nav>

		<div class="lumen-actions">
			<a href="<?php echo esc_url( $search ); ?>" class="header-icon" aria-label="Search"><i class="fas fa-search"></i></a>
			<a href="<?php echo esc_url( $wishlist ); ?>" class="header-icon" aria-label="Wishlist"><i class="fas fa-heart"></i></a>
			<a href="<?php echo esc_url( $cart ); ?>" class="header-icon" aria-label="Cart"><i class="fas fa-shopping-bag"></i><span class="cart-count"><?php echo esc_html( $cart_count ); ?></span></a>
			<a href="<?php echo esc_url( $account ); ?>" class="header-icon" aria-label="Account"><i class="fas fa-user"></i></a>
			<button class="lumen-hamburger" id="mobileMenuBtn" aria-label="Menu">
				<span></span>
				<span></span>
				<span></span>
			</button>
		</div>
	</div>
</header>