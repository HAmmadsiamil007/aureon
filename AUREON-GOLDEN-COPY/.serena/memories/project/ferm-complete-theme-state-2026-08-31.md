# Ferm Living Theme — Complete State Report (2026-08-31)

## Architecture Overview

### Engine Kernel (FROZEN — Golden AUREON)
- **Loader:** `frontend/views/loader.php` → boots engine, loads design resolver, registry, renderer, adapters, sections
- **Design Resolver:** `frontend/views/design.php` → `aether_active_design()` resolves slug, `aether_resolve_design_path()` does pack-first file shadowing
- **Composer:** `frontend/views/composer.php` → `aether_compose_header()` / `aether_compose_footer()` for luxury design only
- **Renderer:** `frontend/views/renderer.php` → `aether_render_component()` + `aether_render_section()` with adapter data pipeline
- **Registry:** `frontend/views/registry.php` → `aether_register_section()` / `aether_section_registry()`
- **Assets:** `frontend/views/assets.php` → `aether_design_enqueue_assets()` handles pack CSS/JS + platform CDNs
- **Adapters:** `frontend/adapters/*.php` — only layer allowed to touch WP/WC APIs

### Theme (FROZEN — Golden AUREON)
- `aureon/inc/frontend.php:16` → loads `../../frontend/views/loader.php`
- `aureon/front-page.php` → renders sections via `aether_render_section()`
- `aureon/header.php` → calls `aether_compose_header()`
- `aureon/footer.php` → calls `aether_compose_footer()`
- `aureon/inc/aether-tokens.php` — design token system
- `aureon/inc/aether-cart.php` — WC cart integration

### Design Pack: `fermliving/` (COMPLETE-PAGE MODE)
- **Manifest:** `manifest.json` → `"complete_page": true`, version 5.0.0
- **Frozen HTML:** `index.html` (homepage), `cart.html`, `account.html`, `checkout.html` (redirect)
- **Templates:** `products/*.html` (3 product pages), `collections/*.html` (3 collections), `pages/*.html` (about, contact, store-locator), `blogs/stories.html`, `account/login.html`
- **CDN Assets:** `cdn/shop/t/164/assets/` — app.css, app.js, ferm-data-shims.js, product.js, customer.js, speedblitz.min.js, fonts CSS, favicons
- **Composer:** `composer.php` — 997 lines, data bridge for WP→Ferm mapping
- **Demo Data:** `demo/demo-products.json` (66 products), `demo/demo-categories.json` (9 categories), `demo/demo-collections.json`, `demo/demo-navigation.json`, `demo/demo-homepage.json`, `demo/demo-assets.json`, `demo/demo-manifest.json`, `demo/demo-image-url-inventory.json` (510 entries)

## Key Architecture Decisions

### Complete-Page Isolation
- When `manifest.json` has `"complete_page": true`, the pack:
  - Serves frozen HTML via `aureon/ferm-page.php` (generic template)
  - Skips ALL platform CDNs (Bootstrap, Swiper, GSAP, etc.)
  - Only enqueues pack CSS/JS from manifest
  - AETHER shell (header.php/footer.php) is NOT used
  - Body content extracted from frozen HTML, paths rewritten server-side

### Data Bridge (FermPageData)
- `composer.php` builds `FermPageData` object injected via `wp_localize_script()` or inline `<script>`
- Contains: cart state, customer state, shop config, navigation, product data, collection data
- `ferm-data-shims.js` reads `FermPageData` and merges into Ferm globals (Shopify shim, FermCart, FermCustomer, FermNavigation)
- Product DOM bridge updates frozen product pages with real WC data
- Variant selection bridge handles color swatch clicks
- Collection bridge replaces hardcoded thumbs with real WC products
- Customizer bridge updates frozen DOM with Customizer values

### Cart System
- `ferm-data-shims.js` intercepts Shopify cart endpoints (`/cart/add.js`, `/cart/change.js`, etc.)
- Routes to `FermCart` methods which call WordPress AJAX (`ferm_cart_add`, `ferm_cart_update`, `ferm_cart_get`)
- `composer.php` registers WP AJAX handlers that wrap WC cart API
- Returns Shopify-shaped JSON responses so app.js stays untouched

### Path Rewriting
- Server-side: `aureon_ferm_rewrite_paths()` in `ferm-page.php` rewrites `cdn/...` to absolute pack URLs
- Client-side: MutationObserver in `ferm-page.php` catches dynamically created images
- Link rewriting: Shopify paths → WordPress routes (collections → product-category, products → product, etc.)

### Demo System
- 66 curated products across 9 categories with verified CDN images
- 510 image references, all pointing to canonical CDN domain
- Zero local image downloads — lightweight remote-reference approach
- Demo↔Real transition: 0 real WC products → 66 demo visible; 1+ real → ALL demo hidden
- Demo products are non-purchasable (`purchasable: false`)
- Automatic transition on page reload

### WC Session Fix (mu-plugin)
- `mu-plugins/aureon-fix-wc-session.php` — initializes WC session early on init, rest_api_init, customize_preview_init
- Guards `wc_clear_cart_after_payment()` to prevent null session warnings

## File Inventory

### Modified Tracked Files (from git status)
1. `aureon/ferm-page.php` — Generic complete-page template (521 lines)
2. `aureon/frontend/designs/fermliving/cdn/shop/t/164/assets/customizer-bridge.js` — Customizer DOM bridge (285 lines)
3. `aureon/frontend/designs/fermliving/composer.php` — Data bridge (997 lines)
4. `aureon/frontend/designs/fermliving/demo/demo-assets.json` — Demo assets
5. `aureon/frontend/designs/fermliving/demo/demo-categories.json` — 9 categories
6. `aureon/frontend/designs/fermliving/demo/demo-homepage.json` — Homepage data
7. `aureon/frontend/designs/fermliving/demo/demo-products.json` — 66 products

### Untracked Files (new)
- `aureon/frontend/designs/fermliving/cdn/shop/files/` — Remote image cache
- `aureon/frontend/designs/fermliving/cdn/shop/t/164/assets/` — All CDN assets (CSS, JS, fonts, favicons)
- `aureon/frontend/designs/fermliving/demo-backup-2026-08-31/` — Pre-change backup
- `aureon/frontend/designs/fermliving/demo/demo-collections.json` — 4 collections
- `aureon/frontend/designs/fermliving/demo/demo-image-url-inventory.json` — 510 entries
- `aureon/frontend/designs/fermliving/demo/demo-manifest.json` — Counts match data
- `aureon/frontend/designs/fermliving/demo/demo-navigation.json` — Navigation structure
- `aureon/frontend/designs/fermliving/demo/test-runtime.php` — Test script
- `docker-compose.yml`, `Dockerfile` — Docker config
- `docs/forensics/FERM-*.md` — Forensics reports (7 files)
- `docs/architecture/DEMO-REFERENCE-SYSTEM-IMPLEMENTATION-PLAN.md`
- `docs/architecture/DIRECTORY-STRUCTURE.md`
- `reports/38-DEMO-REFERENCE-SYSTEM-CONTRACT.md`
- `test-results/phase1/` — Test results

### Deleted Docs (46 files)
- All `docs/PHASE*.md` files (12 files)
- All `docs/FRONTEND_*.md` files (11 files)
- All `docs/forensics/CORE-*.md` and `FERM-*.md` files (6 old files)
- All `docs/frontend-platform/` files (7 files)
- All `docs/superpowers/plans/*.md` and `specs/*.md` files (6 files)
- All `docs/screenshots/*.png` files (15 files)
- `docs/OPERATING-MODEL.md`, `docs/TEMPLATE_REQUIREMENTS_FOR_CORE_THEME.md`

## Template Coverage

| Route | Template File | Status |
|-------|--------------|--------|
| Homepage | `index.html` | ✅ Complete |
| Single Product | `products/*.html` (3) | ✅ Complete |
| Product Archive | `collections/furniture.html` | ✅ Fallback |
| Product Category | `collections/*.html` (3) | ✅ Partial |
| Cart | `cart.html` | ✅ Complete |
| Checkout | `checkout.html` (redirect) | ⚠️ Redirects to Shopify |
| Account | `account.html` (redirect) | ⚠️ Redirects to Shopify |
| Account Login | `account/login.html` | ✅ Complete |
| Blog | `blogs/stories.html` | ✅ Complete |
| About | `pages/about-ferm-living.html` | ✅ Complete |
| Contact | `pages/contact.html` | ✅ Complete |
| Store Locator | `pages/store-locator.html` | ✅ Complete |
| Search | `blogs/stories.html` (fallback) | ⚠️ Uses blog as fallback |
| 404 | `pages/contact.html` (fallback) | ⚠️ Uses contact as fallback |

## Known Issues / Open Decisions

1. **Checkout redirect:** `checkout.html` is a Shopify redirect meta tag — WC checkout uses `aureon/checkout/form-checkout.php` instead
2. **Account redirect:** `account.html` is a Shopify redirect — WC account uses `aureon/myaccount/my-account.php`
3. **Language selector:** Frozen in HTML with hardcoded fermliving.com URLs — single-store handling needed
4. **Blogs→Posts:** Blog template is static HTML — WP posts not dynamically injected
5. **Cart page DOM:** `cart.html` frozen DOM — filled-cart state reconstructed via JS bridge
6. **Font licensing:** Canela/KHTeka fonts are self-hosted woff/woff2 — licensing status unknown
7. **Missing Tailwind utilities:** `app.prettified.css` available but shipped `app.css` may lack some mobile/desktop utilities
8. **Product page coverage:** Only 3 frozen product pages (rico-lounge-chair, meridian-lamp, rico-sofa-2) — other products use first available fallback
9. **Collection page coverage:** Only 3 frozen collection pages (lighting, accessories, furniture) — other categories use first available fallback

## Git State
- **HEAD:** `1b79e71` — "feat: implement Demo Reference Content System"
- **Modified tracked files:** 7
- **Deleted tracked files:** 46 (docs cleanup)
- **Untracked new files:** ~50+ (CDN assets, demo data, forensics reports)
- **Golden Core:** Untouched (frontend/views/*, adapters/*, sections/*, components/*, tokens/*)
