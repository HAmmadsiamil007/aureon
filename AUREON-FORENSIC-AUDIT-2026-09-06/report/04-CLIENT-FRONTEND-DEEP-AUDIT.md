# 04 — Client Frontend Deep Audit (Vineta Pack)

**Scope:** `AUREON-WORDPRESS-DEPLOY/frontend/designs/vineta/`. Read-only.

## Selected design

- Active pack: **vineta** (hardcoded fallback in `views/design.php` + `design-vineta` body class + only pack in deploy tree).
- Mode: **complete-page** (`manifest.json: "complete_page": true`) — frozen HTML served verbatim with WP `<head>`/`wp_footer()` grafted on; body extracted via regex.

## Templates present (58 HTML files at pack root)

| Family | Files |
|---|---|
| Home | index.html |
| Shop | shop-default, shop-left-sidebar, shop-infinity-scroll, shop-filter-drawer, shop-collection-list |
| Product | product-detail, product-3d, product-video, product-countdown-timer, product-description-accordions, product-description-tab, product-group, product-out-of-stock, product-pickup-available, product-swatch-dropdown, product-together, product-volume-discount |
| Cart/checkout | view-cart, cart-drawer-v2, cart-empty, checkout (bypassed at runtime), thank-you |
| Account | account-page, account-orders, account-addresses, account-details |
| Auth | (login/register handled by standalone theme templates, not pack HTML) |
| Blog | blog-grid-01, blog-list-01, blog-single |
| Static pages | about-us, contact-us, faq, shipping, return-and-refund, privacy-policy, term-and-condition, store-location, cookies |
| Utility | 404, coming-soon, wish-list, compare, newsletter-popup-02/03, before-you-leave |

## Structure

- **HTML:** full Shopify-export-style documents. Head stripped by `ferm-page.php` (only body + html/body attrs survive; safe-attribute allowlist). All links rewritten server-side (Shopify paths → WP routes) and again client-side (`vineta-path-bridge.js`).
- **CSS:** `css/styles.css` + bootstrap/swiper/animate + fonts. Enqueued via manifest `assets.css`. WC page inline CSS injected by composer (`vineta_wc_inline_css_output`).
- **JS:** 21 files in `js/` (jquery, bootstrap, swiper, photoswipe, drift, nouislider, wow, lazysize, model-viewer, main.js, shop.js, …) + 2 bridge files (`vineta-data-shims.js`, `vineta-path-bridge.js`). Manifest enqueues 8 with priority "before".
- **Dynamic hooks:** `window.VinetaPageData` (produced in composer, consumed in shims + pack JS); `data-vineta-*` conventions in pack JS; DOM class hooks (`.header__logo`, `.cart-count`, `#customer_login`, `#mobileMenuBtn`) used by bridges.
- **Hardcoded business content:** demo JSON (`demo/demo-products.json`, `demo-demo-categories.json`, `demo-assets.json`) + demo content baked in the frozen HTML (product names/prices in markup). Server-side demo/real switching exists (`vineta_show_demo_content()`, `vineta_has_real_products()`).
- **Fallbacks:** manifest `pages.products._generic` → product-detail.html for unknown products; route resolver falls back to `index.html`; missing file → hardcoded 404 HTML.

## Demo/fallback architecture (verified)

- `aether_demo_mode` option: `auto | force_demo | disabled`.
- `vineta_show_demo_content()` returns **true for both `auto` and `force_demo`** — the `auto` branch has no logic distinguishing demo from real (line ~285: `return true;`), i.e. demo suppression only works via explicit `disabled`. This looks like a stub: real-vs-demo merging exists (`vineta_has_real_products()`), but `auto` does not consult it in `show_demo_content()`.

## Component-shadow system

`frontend/designs/vineta/components/shell/header.php` shadows the AETHER shell header (rendered when a non-complete-page path renders the shell). It calls `vineta_render_standalone_header()` (composer) which extracts the frozen header from index.html and rewrites links. **File is untracked in git.**

## Frontend audit findings

| # | Finding | Evidence | Status |
|---|---|---|---|
| F1 | Frozen HTML contains Shopify-era content: placeholder products, prices, "struct.com" CDN references | index.html, rewrite rules in ferm-page.php | STATIC_BY_DESIGN / DEMO_ONLY |
| F2 | Checkout.html exists in pack but is unreachable (checkout always routed to WC native standalone template) | manifest vs `aureon_ferm_template_include()` | DEAD (manifest entry) |
| F3 | `ferm-page.php` fallback route map references `collections/furniture.html`, `pages/contact.html` — no such paths in vineta pack (files are at pack root, e.g. `contact-us.html`) | `aureon_ferm_resolve_page()` fallback section | BROKEN fallback (only reachable if manifest missing) |
| F4 | `vineta_show_demo_content()` auto-mode stub | composer line ~285 | PARTIAL |
| F5 | Pack JS is unminified in part (jquery-validate.js, carousel.js, zoom.js, infinityslide.js) and minified elsewhere; no build pipeline | js/ listing | LOW |
| F6 | Two rewrite systems for the same job: server-side regex rewrite (ferm-page.php) + client-side `vineta-path-bridge.js` + inline MutationObserver script — triple redundancy | ferm-page.php vs js | DUPLICATED |
| F7 | `components/shell/header.php` (newest feature) depends on `vineta_render_standalone_header()` defined in composer; if composer fails to load, falls back to AETHER shell — silent visual downgrade path | header.php line 16-20 | UNPROVEN |
| F8 | 0-byte `rendered-home.html` at repo root suggests a rendering test was interrupted | root listing | EVIDENCE OF UNPROVEN RUNTIME |

## What is actually dynamic vs static in the pack

- **Dynamic (proven by code):** cart count badge, cart add/update/get AJAX, VinetaPageData (site/announcement/navigation/footer/cart/customer/product/collection/search/blog), login form fields + nonce rewrite, logo replacement via `custom_logo`, menus server-spliced into frozen HTML, newsletter form (aether AJAX), search suggestions bridge, auth bridge.
- **Static by design:** all section layouts, imagery, copy in frozen HTML; demo product cards server-rendered from demo JSON when real catalog empty.
- **Unproven:** variation swatches → real WC variations; gallery → real attachments; category pages → real categories beyond demo; hero slides → Customizer repeater output; thankyou page content; wishlist/compare (endpoints exist in aether-ajax.php, pack wiring unverified).
