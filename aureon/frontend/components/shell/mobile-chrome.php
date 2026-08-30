<?php
/**
 * Mobile chrome — mobile drawer: search, menu groups, CTA, socials.
 *
 * Key:    'shell/mobile-chrome'
 * Source: engine-native (global chrome — all 21 source pages)
 *
 * Props:  (all keys optional; escaped at render; missing key → default listed)
 * - `string $brand         Wordmark. Default ''.`
 * - `string $brand_url     Home link. Default ''.`
 * - `array $announcement  Announcement item. Default [].`
 * - `array $groups         Menu groups schema. Default [].`
 * - `array $cta            CTA link schema (label/url). Default [].`
 * - `array $socials        Social link schema (name/url/icon). Default [].`
 *
 * Slots:  none
 * Variants: none
 * Tokens:  no hardcoded presentation values — theme uses `--aureon-*` custom props only.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$componentData = isset( $componentData ) ? (array) $componentData : array();

$brand        = isset( $componentData['brand'] ) ? $componentData['brand'] : '';
$brand_url    = isset( $componentData['brand_url'] ) ? $componentData['brand_url'] : '';
$announcement = isset( $componentData['announcement'] ) ? (array) $componentData['announcement'] : array();
$groups       = isset( $componentData['groups'] ) ? (array) $componentData['groups'] : array();
$cta          = isset( $componentData['cta'] ) ? (array) $componentData['cta'] : array();
$socials      = isset( $componentData['socials'] ) ? (array) $componentData['socials'] : array();
?>
<!-- Mobile Header (=768px) -->
<div class="mobile-header" id="mobileHeader">
	<button class="mobile-hamburger" id="mobileHamburger" aria-label="Open menu">
		<span></span>
		<span></span>
		<span></span>
	</button>
	<div class="mobile-announcement" id="mobileAnnouncement">
		<?php if ( empty( $announcement ) ) : ?>
			<span class="mobile-announcement-text active">FREE SHIPPING ON ORDERS OVER $200</span>
			<span class="mobile-announcement-text">FREE SHIPPING ON ALL ORDERS</span>
		<?php else : ?>
			<?php foreach ( $announcement as $index => $text ) : ?>
				<span class="mobile-announcement-text<?php echo 0 === $index ? ' active' : ''; ?>"><?php echo esc_html( $text ); ?></span>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>
</div>

<!-- Mobile Menu Overlay -->
<div class="mobile-menu-overlay" id="mobileMenuOverlay">
	<div class="mobile-menu" id="mobileMenu">
		<div class="mobile-menu-header">
			<button class="mobile-menu-close" id="mobileMenuClose" aria-label="Close menu">
				<i class="fas fa-times"></i>
			</button>
			<a href="<?php echo esc_url( $brand_url ); ?>" class="mobile-menu-logo"><?php echo esc_html( $brand ); ?></a>
			<div class="mobile-menu-spacer"></div>
		</div>
		<div class="mobile-menu-body">
			<div class="mobile-search">
				<i class="fas fa-search"></i>
				<input type="text" placeholder="Search products...">
			</div>

			<?php foreach ( $groups as $group ) : ?>
				<?php
				$items = isset( $group['items'] ) ? (array) $group['items'] : array();
				if ( empty( $items ) ) {
					continue;
				}
				?>
				<nav class="mobile-nav">
					<?php foreach ( $items as $item ) : ?>
						<?php
						$label = isset( $item['label'] ) ? $item['label'] : '';
						$url   = isset( $item['url'] ) ? $item['url'] : '#';
						?>
						<a href="<?php echo esc_url( $url ); ?>" class="mobile-nav-link"><?php echo esc_html( $label ); ?></a>
					<?php endforeach; ?>
				</nav>
				<div class="mobile-divider"></div>
			<?php endforeach; ?>

			<div class="mobile-meta">
				<span>Language</span>
				<span>Currency</span>
			</div>
			<?php if ( ! empty( $cta['label'] ) ) : ?>
				<a href="<?php echo esc_url( isset( $cta['url'] ) ? $cta['url'] : '#' ); ?>" class="mobile-cta"><?php echo esc_html( $cta['label'] ); ?></a>
			<?php endif; ?>
			<div class="mobile-socials">
				<?php foreach ( $socials as $social ) : ?>
					<a href="<?php echo esc_url( isset( $social['url'] ) ? $social['url'] : '#' ); ?>" aria-label="<?php echo esc_attr( isset( $social['label'] ) ? $social['label'] : '' ); ?>"><i class="<?php echo esc_attr( isset( $social['icon'] ) ? $social['icon'] : 'fab fa-instagram' ); ?>"></i></a>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</div>
