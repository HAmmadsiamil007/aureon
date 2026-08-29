# Current State Audit — Ferm Premium Frontend Port

## Status: Design complete. Ready for implementation.

## Git Status
- Last recorded HEAD: 79773fcdafc4201bb4f7b3f6198d02b903745ef9
- Phase 0 will create a new baseline checkpoint before any implementation

## Existing Ferm Pack: frontend/designs/fermliving/

### Components (PHP templates)
- `components/shell/announcement.php` — USP bar
- `components/shell/header.php` — Logo left, nav center, icons right. 290 lines. Has mega menu container, search overlay, mobile nav.
- `components/shell/footer.php` — USP row, newsletter, 3 link columns, legal. 160 lines.
- `components/shell/mobile-chrome.php` — Mobile navigation drawer
- `components/shell/preloader.php` — Page preloader
- `components/cards/product.php` — Product card with carousel, badges, wishlist, swatches, ATC. 177 lines.
- `components/cards/category.php` — Category card
- `components/product/gallery.php` — Product image gallery
- `components/product/info.php` — Product info/details
- `components/content/author-bio.php` — Blog author bio

### Sections
- `sections/section-hero.php` — Homepage hero
- `sections/section-bestsellers.php` — Product grid
- `sections/section-categories.php` — Category grid
- `sections/section-editorial-split.php` — Editorial content
- `sections/section-room-grid.php` — Room/inspiration grid
- `sections/section-secondary-products.php` — Secondary product grid

### CSS/JS
- `css/ferm.css` — 3285 lines, scoped under `.design-fermliving` (WILL BE REPLACED by frozen compiled CSS)
- `css/fonts.css` — Font declarations
- `js/ferm.js` — Client-side interactions (WILL BE REPLACED by ported frozen JS)

### Data
- `data/products.json` — Reference products
- `data/categories.json` — Reference categories
- `data/navigation.json` — Navigation structure

### Other
- `composer.php` — 434 lines, hooks into adapter data filters, section sequence
- `tokens.php` — Design token defaults
- `manifest.json` — Pack descriptor (v2.0.0)
- `assets/` — Images organized by category
- `fonts/` — Font files
- `mapper/` — Ferm mapper layer

## Frozen Source: SiteOne-Crawler/fermliving.com/
- `index.html` — Homepage, 10,756 lines
- `collections/` — Collection pages (archive/PLP)
- `products/` — Product pages (PDP)
- `blogs/` — Blog pages
- `pages/` — Static pages (about, contact, etc.)
- `cart.html` — Cart page
- `account/` — Account pages
- `cdn/` — Static assets (CSS, JS, images, fonts)
- IMMUTABLE — treated as FERM_REFERENCE_V1

## 23 Adapters (data flow)
adapter-about, adapter-account, adapter-article, adapter-auth, adapter-blog, adapter-cart, adapter-coming-soon, adapter-contact, adapter-faq, adapter-hero, adapter-menu, adapter-options, adapter-order, adapter-product, adapter-shell, adapter-shop-hero, adapter-site, adapter-team, adapter-testimonials, adapter-wc-categories, adapter-wc-filter, adapter-wc-products, adapter-wishlist

## Design Pack Resolver
- `frontend/views/design.php` — Resolves pack files pack-first, falls back to engine defaults
- `aether_active_design()` — Returns active design slug
- `aether_resolve_design_path()` — Resolves pack-first file paths
- `aether_pack_url()` — Returns pack URL for assets

## Legacy Pack
- `frontend/designs/fermliving-legacy/` — PRESERVED, NOT ACTIVE, NOT RESOLVABLE
- Must remain untouched until new frontend passes all acceptance tests
