# Frozen Ferm Source Analysis

## Status: Reference validated. Immutable FERM_REFERENCE_V1.

## Source Location
`C:\Users\hamma\Downloads\SiteOne-Crawler\fermliving.com`

## Frozen Source Structure
- `index.html` — Homepage, 10,756 lines
- `collections/*.html` — Collection/archive pages
- `products/*.html` — Product detail pages
- `blogs/*.html` — Blog listing pages
- `blogs/*/articles/*.html` — Article pages
- `pages/*.html` — Static pages (about, contact, etc.)
- `cart.html` — Cart page
- `account/*.html` — Account pages
- `cdn/shop/t/164/assets/` — All static assets (CSS, JS, images, fonts)

## Homepage Structure (index.html)
- `<html lang='en' data-country='DK' data-shop='fermliving.com'>`
- Head: fonts, preconnects, manifest, OG meta, Shopify scripts (TO BE REMOVED)
- Body:
  - Announcement/USP bar
  - Header (data-component='header', data-section-type='header')
  - Main content sections (hero, categories, editorial, products, rooms)
  - Footer (newsletter, columns, legal, payments)
  - Shopify-specific scripts (TO BE REMOVED)

## Key DOM Patterns
- `data-section-id="header"`, `data-section-type="header"`, `data-component="header"`
- `data-header-nav`, `data-header-inner`, `data-header-logo`, `data-header-box`
- `data-header-link` for nav items
- `data-search`, `data-wishlist`, `data-main-cart-button`, `data-cart-count`
- `data-mobile-menu-link`, `data-mobile-cart-button`
- `data-component="megaMenu"`, `data-megamenu`, `data-megamenu-menu-point`
- `data-dynamic-menu-wrapper`, `data-dynamic-menu-parent`, `data-dynamic-menu-child`
- `data-megamenu-overlay`

## CSS Classes Used
- Utilities: `fixed`, `z-[12]`, `w-full`, `flex`, `items-center`, `gap-10`, `px-0`, `py-3`
- Components: `header`, `header--transparent`, `header--solid`, `header--scrolled`
- Layout: `limit`, `grid-12`, `max-w-[var(--site-max-width)]`
- Typography: `font-secondary`, `text-sm`, `font-normal`, `not-italic`
- Colors: `bg-cream`, `text-black`, `bg-canvas`
- Transitions: `transition-transform`, `duration-200`, `ease-in-out`
- Responsive: `tab_l:` (tablet landscape), `tab_p:` (tablet portrait), `mobile:`

## Assets in Frozen Source
- Fonts: `fonts.fd2d67c5ce.css` + WOFF/WOFF2 files
- JS: `customer.5de68fbefc.js` (and others)
- Images: various product/editorial images
- Total clone: 38,485 files, 7.38 GB
- Only extract assets required by Ferm pack routes

## Shopify Dependencies TO REMOVE
- Shopify Liquid syntax
- Shopify cart API (`/cart.js`, `/cart/add.js`, `/cart/update.js`)
- Shopify customer/account endpoints
- Shopify predictive search
- Liquid-generated JSON objects
- Clerk.io recommendation engine
- Shopify section rendering
- Shopify-specific scripts

## Shopify Dependencies TO REPLACE with AUREON/WooCommerce
- Product data → WooCommerce product post
- Cart operations → WooCommerce cart AJAX
- Checkout → WooCommerce checkout
- Customer auth → WordPress user auth
- Search → WordPress/Woo search
- Recommendations → AUREON or reference data
- Menu/navigation → wp_nav_menu or AUREON menu adapter
