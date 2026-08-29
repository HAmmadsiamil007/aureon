# Shell Header Component Analysis

## Status: Analysis complete. Template ready for porting.

## Current header.php
Location: `frontend/designs/fermliving/components/shell/header.php` (290 lines)

## DOM Structure to Preserve
```
<section data-section-id="header" data-section-type="header" data-component="header">
  <header class="header header--transparent has-bar fixed left-0 top-0 z-[12]">
    <div class="absolute left-0 top-8 w-full bg-cream" data-header-nav>
      <div class="limit mx-auto my-0 flex max-w-[var(--site-max-width)] justify-between tab_l:grid-12" data-header-inner>
        
        <!-- Logo -->
        <div class="header__logo static col-span-6 flex transform-none items-center gap-10 px-0 py-3 font-secondary" data-header-logo data-header-box>
          <a class="logo" href="{brand_url}">{brand}</a>
        </div>
        
        <!-- Right side -->
        <div data-header-box-right data-header-box class="col-span-6 flex items-center justify-between font-secondary">
          
          <!-- Desktop Nav -->
          <div class="nav header__navigation hidden items-center justify-start gap-[24px] text-sm tab_l:flex">
            <a href="{url}" class="animation-underline" data-header-link>{label}</a>
          </div>
          
          <!-- Desktop Icons -->
          <div data-header-right class="hidden items-center gap-[24px] text-sm font-normal not-italic tab_l:flex">
            <button data-search>Search SVG</button>
            <a href="{wishlist}" data-wishlist>Wishlist</a>
            <button data-main-cart-button>
              Cart
              <span data-cart-count>{count}</span>
            </button>
            <a href="{account}">Login</a>
          </div>
          
          <!-- Mobile Icons -->
          <div class="tab_l:!hidden">
            <button data-search>Search SVG</button>
            <a href="{wishlist}" data-wishlist>Wishlist SVG</a>
            <button data-mobile-cart-button>
              Cart SVG
              <span data-cart-count>{count}</span>
            </button>
            <button data-mobile-menu-link>
              Hamburger SVG / Close SVG
            </button>
          </div>
        </div>
      </div>
    </div>
  </header>
  
  <!-- Mega Menu -->
  <div class="megamenu-wrapper z-1 relative hidden tab_l:block" data-component="megaMenu" data-megamenus>
    {foreach menu items with children}
      <div class="megamenu closed fixed z-[-2] w-full bg-cream" data-megamenu data-megamenu-menu-point="{label}">
        <div class="megamenu__inner limit grid grid-cols-12 gap-[24px] min-h-[350px] pb-6">
          <div class="megamenu__static-menu col-span-3">{children}</div>
          <div class="megamenu__dynamic-wrapper col-span-9">
            <div class="megamenu__dynamic-menu-left col-span-4">{children}</div>
            <div class="megamenu__dynamic-menu-right col-span-4">{grandchildren}</div>
            <div class="megamenu__single-image col-span-4">{image placeholder}</div>
          </div>
        </div>
      </div>
    {/foreach}
    <div class="megamenu-overlay" data-megamenu-overlay></div>
  </div>
</section>

<!-- Search Overlay -->
<div id="searchOverlay" class="search-overlay">
  <div class="search-overlay-header">
    <span>Search</span>
    <button class="search-overlay-close search-close">Close SVG</button>
  </div>
  <input class="search-overlay-input search-input" placeholder="Search Ferm Living...">
  <div class="search-suggestions">
    <p class="search-suggestion-label">Popular Searches</p>
    <a class="search-suggestion">Furniture</a>
    <a class="search-suggestion">Lighting</a>
    <a class="search-suggestion">Accessories</a>
  </div>
</div>

<!-- Header Spacer -->
<div class="header-spacer" aria-hidden="true"></div>
```

## Data Contract
Props from adapter: brand, brand_url, menu (with children/grandchildren), icons (search, wishlist, cart, account), cart_count

## Platform JS Hooks (MUST PRESERVE)
- `data-component="header"` — Platform header behavior
- `data-header-link` — Navigation link behavior
- `data-search` — Search overlay trigger
- `data-wishlist` — Wishlist behavior
- `data-main-cart-button` — Cart button
- `data-cart-count` — Cart count display
- `data-mobile-menu-link` — Mobile menu toggle
- `data-mobile-cart-button` — Mobile cart
- `data-component="megaMenu"` — Mega menu behavior
- `data-megamenu` — Individual mega menu
- `data-megamenu-menu-point` — Menu trigger point
- `data-dynamic-menu-wrapper` — Dynamic menu content
- `data-dynamic-menu-parent` — Parent menu item
- `data-dynamic-menu-child` — Child submenu
- `data-megamenu-overlay` — Overlay click handler

## Shopify → WordPress Mapping for Header
| Shopify | WordPress/AUREON |
|---------|-----------------|
| Liquid menu loop | wp_nav_menu or AUREON menu adapter |
| cart.item_count | FermPageData.cart.itemCount |
| customer first_name | FermPageData.customer.firstName |
| Shopify section rendering | WordPress template part |

## Shell Components to Port
1. `announcement.php` — USP bar
2. `header.php` — Main header (290 lines)
3. `mobile-chrome.php` — Mobile navigation drawer
4. Footer — `footer.php` (160 lines)
5. Search overlay — integrated in header
6. Mega menu — integrated in header
