<?php
/**
 * Ferm Living site header — logo left, nav center, icons right.
 *
 * Key:    'shell/header' (override)
 * Source: fermliving.com header structure
 * Props:  same schema as engine shell/header:
 *         brand, brand_url, menu, icons, cart_count.
 * Contract: keeps #header, .header-icon, .cart-count aria-labels —
 *           platform JS (AJAX cart, search, drawer) operates unchanged.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
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

$is_home = is_front_page() || ( is_home() && ! is_paged() );
$header_class = 'header' . ( $is_home ? ' header--transparent' : ' header--solid' );

?>
<header class="<?php echo esc_attr( $header_class ); ?>" id="header" role="banner" <?php echo $is_home ? 'data-header-transparent' : ''; ?>>
	<div class="header-inner">
		<div class="header-logo">
			<a href="<?php echo esc_url( $brand_url ); ?>" aria-label="<?php echo esc_attr( $brand ); ?> - Home">
				<?php echo wp_kses_post( $brand ); ?>
			</a>
		</div>

		<nav class="header-nav" id="mainNav" aria-label="Primary navigation">
			<?php foreach ( $menu as $item ) :
				$label        = isset( $item['label'] ) ? $item['label'] : '';
				$url          = isset( $item['url'] ) ? $item['url'] : '#';
				$active       = ! empty( $item['active'] );
				$has_children = ! empty( $item['children'] );
				?>
				<?php if ( $has_children ) : ?>
					<div class="nav-dropdown" data-mega-trigger="<?php echo esc_attr( $label ); ?>">
						<a href="<?php echo esc_url( $url ); ?>"
						   class="nav-link"
						   aria-haspopup="true"
						   aria-expanded="false"
						   data-header-link><?php echo esc_html( $label ); ?></a>
					</div>
				<?php else : ?>
					<a href="<?php echo esc_url( $url ); ?>"
					   class="nav-link<?php echo $active ? ' is-active' : ''; ?>"
					   data-header-link><?php echo esc_html( $label ); ?></a>
				<?php endif; ?>
			<?php endforeach; ?>
		</nav>

		<div class="header-actions">
			<button type="button" class="header-action-btn header-action-search" data-search aria-label="Search" title="Search">
				<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<circle cx="11.1589" cy="11.1589" r="6.40893" stroke="currentColor" stroke-width="1.25"/>
					<path d="M19.2508 19.2498L17.3281 17.3271" stroke="currentColor" stroke-width="1.25" stroke-linecap="square" stroke-linejoin="round"/>
				</svg>
			</button>
			<a href="<?php echo esc_url( $wishlist ); ?>" class="header-action-link header-action-wishlist" aria-label="Wishlist" data-wishlist>Wishlist</a>
			<button type="button" class="header-action-btn header-action-cart" data-main-cart-button aria-label="Cart">
				<span class="header-cart-label">Cart</span>
				<span class="header-cart-count<?php echo 0 === $cart_count ? ' is-hidden' : ''; ?>"
					  data-cart-count
					  aria-hidden="true"><?php echo esc_html( $cart_count ); ?></span>
				<span class="sr-only" data-cart-count-label>Cart (<?php echo esc_html( $cart_count ); ?>)</span>
			</button>
			<a href="<?php echo esc_url( $account ); ?>" class="header-action-link header-action-login" aria-label="Login">Login</a>

			<?php /* Mobile menu toggle */ ?>
			<button type="button"
					class="header-mobile-toggle"
					data-mobile-menu-link
					aria-label="Menu"
					aria-expanded="false"
					title="Menu">
				<svg width="14" height="12" viewBox="0 0 14 12" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M0 1H14" stroke="currentColor" stroke-width="1.25" stroke-linejoin="round"/>
					<path d="M3.5 6H14" stroke="currentColor" stroke-width="1.25" stroke-linejoin="round"/>
					<path d="M2 11H14" stroke="currentColor" stroke-width="1.25" stroke-linejoin="round"/>
				</svg>
			</button>
		</div>
	</div>

	<?php /* Mega menu container — populated by JS or adapter data */ ?>
	<div class="mega-menu" data-component="megaMenu" data-megamenus aria-hidden="true">
		<?php foreach ( $menu as $item ) :
			$has_children = ! empty( $item['children'] );
			if ( ! $has_children ) {
				continue;
			}
			?>
			<div class="mega-menu-panel"
				 data-megamenu
				 data-megamenu-type="megamenu"
				 data-megamenu-menu-point="<?php echo esc_attr( $item['label'] ); ?>"
				 aria-hidden="true">
				<div class="mega-menu-inner">
					<div class="mega-menu-static">
						<?php foreach ( $item['children'] as $child ) : ?>
							<div class="mega-menu-item">
								<a href="<?php echo esc_url( isset( $child['url'] ) ? $child['url'] : '#' ); ?>" class="mega-menu-link">
									<?php echo esc_html( isset( $child['label'] ) ? $child['label'] : '' ); ?>
								</a>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</header>

<?php /* Pre-rendered search overlay — prevents main.js from injecting AETHER fallback markup. */ ?>
<div id="searchOverlay" class="search-overlay" aria-hidden="true">
	<div class="search-overlay-header">
		<span>Search</span>
		<button class="search-overlay-close search-close" aria-label="Close search">
			<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="1.5" stroke-linecap="square" stroke-linejoin="round"/>
			</svg>
		</button>
	</div>
	<input type="text"
		   class="search-overlay-input search-input"
		   placeholder="Search Ferm Living..."
		   aria-label="Search Ferm Living"
		   autofocus>
	<div class="search-suggestions">
		<p class="search-suggestion-label">Popular Searches</p>
		<a href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' ) ); ?>" class="search-suggestion">Furniture</a>
		<a href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' ) ); ?>" class="search-suggestion">Lighting</a>
		<a href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' ) ); ?>" class="search-suggestion">Accessories</a>
	</div>
</div>

<?php /* Spacer to offset fixed header */ ?>
<div class="header-spacer" aria-hidden="true"></div>
