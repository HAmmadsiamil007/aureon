<?php
/**
 * Ferm Living mobile chrome — 3-level deep slide-out submenu panels.
 *
 * Key:    'shell/mobile-chrome' (override)
 * Source: fermliving.com mobile menu structure
 * Props:  brand, brand_url, menu (with children + grandchildren), cta, socials.
 * Contract: keeps #mobileHeader, #mobileHamburger, #mobileMenuOverlay,
 *           #mobileMenuClose, .mobile-search — platform drawer JS operates unchanged.
 *
 * Ferm Living mobile menu hierarchy:
 *   Level 1: Top nav (Shop, Inspiration, Rooms, Professionals)
 *   Level 2: Subcategories (Kids, Outdoor, Accessories, Furniture, etc.)
 *   Level 3: Tertiary links (All Kids, Toys, Baby, Textiles, etc.)
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$componentData = isset( $componentData ) ? (array) $componentData : array();

$brand     = isset( $componentData['brand'] ) ? $componentData['brand'] : '';
$brand_url = isset( $componentData['brand_url'] ) ? $componentData['brand_url'] : '';
$menu      = isset( $componentData['menu'] ) ? (array) $componentData['menu'] : array();
$cta       = isset( $componentData['cta'] ) ? (array) $componentData['cta'] : array();
$socials   = isset( $componentData['socials'] ) ? (array) $componentData['socials'] : array();

$is_home = is_front_page() || ( is_home() && ! is_paged() );
?>

<?php /* Mobile Header — visible on mobile only */ ?>
<div class="mobile-header<?php echo $is_home ? ' mobile-header--transparent' : ''; ?>" id="mobileHeader" <?php echo $is_home ? 'data-mobile-header-transparent' : ''; ?>>
	<div class="mobile-header-inner">
		<?php /* Hamburger toggle — triggers mobile menu overlay */ ?>
		<button class="mobile-hamburger" id="mobileHamburger" aria-label="Open menu" aria-expanded="false">
			<span class="mobile-hamburger-line"></span>
			<span class="mobile-hamburger-line"></span>
			<span class="mobile-hamburger-line"></span>
		</button>

		<?php /* Mobile logo */ ?>
		<a href="<?php echo esc_url( $brand_url ); ?>" class="mobile-logo" aria-label="<?php echo esc_attr( $brand ); ?> - Home">
			<?php echo wp_kses_post( $brand ); ?>
		</a>

		<?php /* Mobile right actions */ ?>
		<div class="mobile-actions">
			<button type="button" class="mobile-action-btn" data-search aria-label="Search">
				<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<circle cx="11.1589" cy="11.1589" r="6.40893" stroke="currentColor" stroke-width="1.25"/>
					<path d="M19.2508 19.2498L17.3281 17.3271" stroke="currentColor" stroke-width="1.25" stroke-linecap="square" stroke-linejoin="round"/>
				</svg>
			</button>
			<button type="button" class="mobile-action-btn" data-mobile-cart-button aria-label="Cart">
				<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M6.46397 8.20717L4.55093 19.2072C4.52434 19.3601 4.64203 19.5 4.79723 19.5H19.2028C19.358 19.5 19.4757 19.3601 19.4491 19.2072L17.536 8.20716C17.5152 8.08742 17.4113 8 17.2897 8H6.71027C6.58873 8 6.4848 8.08742 6.46397 8.20717Z" stroke="currentColor" stroke-width="1.25" stroke-linecap="round"/>
					<path d="M9.5 11C9.5 8.68633 9.5 5.63294 9.5 4.74931C9.5 4.61124 9.61193 4.5 9.75 4.5H14.25C14.3881 4.5 14.5 4.61193 14.5 4.75V11" stroke="currentColor" stroke-width="1.25"/>
				</svg>
				<span class="mobile-cart-count<?php echo 0 === (int) aureon_get_option( 'aether_cart_count', 0 ) ? ' is-hidden' : ''; ?>"
					  data-cart-count
					  aria-hidden="true">0</span>
			</button>
		</div>
	</div>
</div>

<?php /* Mobile Menu Overlay — full-screen slide-in */ ?>
<div class="mobile-menu-overlay" id="mobileMenuOverlay" aria-hidden="true">
	<div class="mobile-menu" id="mobileMenu" role="dialog" aria-label="Mobile navigation">

		<?php /* Close button */ ?>
		<button type="button" class="mobile-menu-close" aria-label="Close menu">
			<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="1.25" stroke-linecap="square" stroke-linejoin="round"/>
			</svg>
		</button>

		<?php /* Level 1: Top navigation items */ ?>
		<div class="mobile-menu-level mobile-menu-level-1 is-active" data-mobile-level="1">
			<?php foreach ( $menu as $item ) :
				$label        = isset( $item['label'] ) ? $item['label'] : '';
				$url          = isset( $item['url'] ) ? $item['url'] : '#';
				$has_children = ! empty( $item['children'] );
				if ( empty( $label ) ) {
					continue;
				}
				?>
				<div class="mobile-menu-item">
					<?php if ( $has_children ) : ?>
						<button type="button"
								class="mobile-menu-link mobile-menu-link--has-children"
								data-mobile-submenu="<?php echo esc_attr( $label ); ?>"
								aria-haspopup="true"
								aria-expanded="false">
							<?php echo esc_html( $label); ?>
							<svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg" class="mobile-menu-chevron">
								<path d="M4.5 2L8.5 6L4.5 10" stroke="currentColor" stroke-width="1.25" stroke-linecap="square" stroke-linejoin="round"/>
							</svg>
						</button>
					<?php else : ?>
						<a href="<?php echo esc_url( $url ); ?>" class="mobile-menu-link">
							<?php echo esc_html( $label ); ?>
						</a>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>

			<?php /* Static quick links (Ferm: Gift Guides, Classics, Bestsellers, etc.) */ ?>
			<div class="mobile-menu-quick-links">
				<?php
				// These could come from Customizer or be hardcoded as design defaults.
				$quick_links = array(
					array( 'label' => 'Gift Guides', 'url' => '#' ),
					array( 'label' => 'Ferm Living Classics', 'url' => '#' ),
					array( 'label' => 'Bestsellers', 'url' => '#' ),
					array( 'label' => 'News', 'url' => '#' ),
				);
				foreach ( $quick_links as $link ) :
					?>
					<a href="<?php echo esc_url( $link['url'] ); ?>" class="mobile-menu-quick-link">
						<?php echo esc_html( $link['label'] ); ?>
					</a>
				<?php endforeach; ?>
			</div>
		</div>

		<?php /* Level 2: Subcategories (slides in from right when parent is tapped) */ ?>
		<?php foreach ( $menu as $item ) :
			$label        = isset( $item['label'] ) ? $item['label'] : '';
			$has_children = ! empty( $item['children'] );
			if ( ! $has_children || empty( $label ) ) {
				continue;
			}
			?>
			<div class="mobile-menu-level mobile-menu-level-2" data-mobile-submenu-panel="<?php echo esc_attr( $label ); ?>" aria-hidden="true">
				<?php /* Back button */ ?>
				<button type="button"
						class="mobile-menu-back"
						data-mobile-submenu-close
						aria-label="Back to menu">
					<svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M7.5 2L3.5 6L7.5 10" stroke="currentColor" stroke-width="1.25" stroke-linecap="square" stroke-linejoin="round"/>
					</svg>
					<?php echo esc_html( $label ); ?>
				</button>

				<?php /* Featured image (optional) */ ?>
				<?php if ( isset( $item['image'] ) && $item['image'] ) : ?>
					<div class="mobile-menu-feature">
						<img src="<?php echo esc_url( $item['image'] ); ?>"
							 alt="<?php echo esc_attr( $label ); ?>"
							 loading="lazy"
							 width="600"
							 height="338"
							 decoding="async">
					</div>
				<?php endif; ?>

				<?php /* Quick links for this category */ ?>
				<div class="mobile-menu-quick-links">
					<?php foreach ( $item['children'] as $child ) :
						$child_label        = isset( $child['label'] ) ? $child['label'] : '';
						$child_url          = isset( $child['url'] ) ? $child['url'] : '#';
						$child_has_children = ! empty( $child['children'] );
						if ( empty( $child_label ) ) {
							continue;
						}
						?>
						<div class="mobile-menu-item">
							<?php if ( $child_has_children ) : ?>
								<button type="button"
										class="mobile-menu-link mobile-menu-link--has-children"
										data-tertiary-menu-link="<?php echo esc_attr( $child_label ); ?>"
										aria-haspopup="true"
										aria-expanded="false">
									<?php echo esc_html( $child_label ); ?>
									<svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg" class="mobile-menu-chevron">
										<path d="M4.5 2L8.5 6L4.5 10" stroke="currentColor" stroke-width="1.25" stroke-linecap="square" stroke-linejoin="round"/>
									</svg>
								</button>
							<?php else : ?>
								<a href="<?php echo esc_url( $child_url ); ?>" class="mobile-menu-link">
									<?php echo esc_html( $child_label ); ?>
								</a>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<?php /* Level 3: Tertiary links (slides in from right when subcategory is tapped) */ ?>
			<?php foreach ( $item['children'] as $child ) :
				$child_label        = isset( $child['label'] ) ? $child['label'] : '';
				$child_has_children = ! empty( $child['children'] );
				if ( ! $child_has_children || empty( $child_label ) ) {
					continue;
				}
				?>
				<div class="mobile-menu-level mobile-menu-level-3"
					 data-tertiary-menu="<?php echo esc_attr( $child_label ); ?>"
					 aria-hidden="true">
					<?php /* Back button */ ?>
					<button type="button"
							class="mobile-menu-back"
							data-tertiary-menu-close
							aria-label="Back to <?php echo esc_attr( $child_label ); ?>">
						<svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M7.5 2L3.5 6L7.5 10" stroke="currentColor" stroke-width="1.25" stroke-linecap="square" stroke-linejoin="round"/>
						</svg>
						<?php echo esc_html( $child_label ); ?>
					</button>

					<div class="mobile-menu-tertiary-links">
						<?php foreach ( $child['children'] as $grandchild ) :
							$gc_label = isset( $grandchild['label'] ) ? $grandchild['label'] : '';
							$gc_url   = isset( $grandchild['url'] ) ? $grandchild['url'] : '#';
							if ( empty( $gc_label ) ) {
								continue;
							}
							?>
							<a href="<?php echo esc_url( $gc_url ); ?>" class="mobile-menu-link">
								<?php echo esc_html( $gc_label ); ?>
							</a>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endforeach; ?>
		<?php endforeach; ?>

		<?php /* Professionals submenu (simple list) */ ?>
		<div class="mobile-menu-level mobile-menu-level-2" data-mobile-submenu-panel="Professionals" aria-hidden="true">
			<button type="button"
					class="mobile-menu-back"
					data-mobile-submenu-close
					aria-label="Back to menu">
				<svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M7.5 2L3.5 6L7.5 10" stroke="currentColor" stroke-width="1.25" stroke-linecap="square" stroke-linejoin="round"/>
				</svg>
				Professionals
			</button>
			<div class="mobile-menu-quick-links">
				<a href="#" class="mobile-menu-link">B2B Login</a>
				<a href="#" class="mobile-menu-link">Image Bank</a>
				<a href="#" class="mobile-menu-link">Showrooms</a>
				<a href="#" class="mobile-menu-link">Catalogues</a>
				<a href="#" class="mobile-menu-link">Contract Projects</a>
				<a href="#" class="mobile-menu-link">Company Information</a>
			</div>
		</div>

	</div>
</div>
