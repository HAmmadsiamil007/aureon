# VINETA → GOLDEN AUREON REPLACEMENT — PRE-BASELINE

**Date:** 2026-09-02
**Status:** BASELINE RECORDED — READY FOR REPLACEMENT

---

## Git State

| Property | Value |
|----------|-------|
| Branch | `master` |
| HEAD | `c9c688d` |
| Last commit | `docs: productized client frontend workflow — capability contract + 42-phase master prompt` |
| Working tree | Clean (only untracked: `vineta-primary-only/`, `docs/Master file map...`) |
| Tracked file modified | `aureon/theme/front-page.php` (from prior Ferm work) |

## Active Design

| Property | Value |
|----------|-------|
| Active design slug | `fermliving` |
| Design directory | `aureon/frontend/designs/fermliving/` |
| Manifest version | 5.0.0 |
| Complete-page mode | YES |
| Design resolver default | `fermliving` (hardcoded fallback in `design.php:73`) |

## Current Ferm Pack Structure

| Component | Path | Status |
|-----------|------|--------|
| Manifest | `fermliving/manifest.json` | ✅ v5.0.0 |
| Composer | `fermliving/composer.php` | ✅ Data bridge |
| Tokens | `fermliving/tokens.php` | ✅ Token overrides |
| Frozen HTML pages | `fermliving/*.html` | ✅ 108 pages |
| Frozen CSS | `fermliving/cdn/shop/t/164/assets/` | ✅ app.css |
| Frozen JS | `fermliving/cdn/shop/t/164/assets/` | ✅ app.js + page JS |
| Bridge JS | `ferm-data-shims.js`, `cart-page.ferm.js`, `search-bridge.js`, `customizer-bridge.js` | ✅ |
| Demo data | `fermliving/demo/` | ✅ products, categories, assets |
| CDN assets | `fermliving/cdn/shop/files/` | ✅ images |

## Current Design Resolver Architecture

- `aether_active_design()` → reads `AETHER_DESIGN` constant or `aether_active_design` option → defaults to `fermliving`
- `aether_active_design_dir()` → `AETHER_FRONTEND_DIR . 'designs/' . $design . '/'`
- `aether_pack_url()` → `content_url() . 'frontend/designs/' . $design . '/'`
- `aether_resolve_design_path()` → pack-first shadow: file in pack shadows base file
- Complete-page flag: `aether_is_complete_page_design()` reads `manifest.json['complete_page']`
- Body class: `design-{slug}` added automatically

## Current Route Mapping (Ferm)

| Route | Template | Source |
|-------|----------|--------|
| `/` | `index.html` | Frozen homepage |
| `/shop/` | `collections/furniture.html` | Frozen collection |
| `/product/{slug}/` | Frozen product HTML or `_generic-product.html` | Per-product or generic |
| `/product-category/{slug}/` | `collections/{slug}.html` or fallback | Frozen collection |
| `/cart/` | `cart.html` | Frozen cart |
| `/checkout/` | `checkout.html` | Frozen checkout |
| `/my-account/` | `account/login.html` | Frozen account |
| `/blog/` | `blogs/stories.html` | Frozen blog |
| `/{page}/` | `pages/{slug}.html` or frozen page | Frozen page |
| `/search/` | Search bridge JS | Dynamic |
| 404 | WordPress 404 template | WP native |

## Vineta Source Inventory

| Property | Value |
|----------|-------|
| Source path | `vineta-primary-only/` |
| Total HTML pages | 108 |
| CSS files | 10 (bootstrap, animate, photoswipe, swiper, drift, fancybox, nouislider, styles) |
| JS files | 19 (jQuery, Bootstrap, Swiper, Photoswipe, Drift, Lazysize, carousel, shop, main, etc.) |
| Images | 1369 |
| Fonts | Icomoon icon font |
| Aureon slots | 122 (90 hooked, 2 bridge-required) |
| Page families | 10 (home, product, shop, blog, account, cart, checkout, wishlist/compare, static, utility) |

## Dependencies Required (Vineta)

### CSS
- `bootstrap.min.css` (3rd party)
- `bootstrap-select.min.css` (3rd party)
- `animate.css` (3rd party)
- `swiper-bundle.min.css` (3rd party)
- `photoswipe.css` (3rd party)
- `drift-basic.min.css` (3rd party)
- `jquery.fancybox.min.css` (3rd party)
- `nouislider.min.css` (embedded in styles.css)
- `font-icons.css` + `fonts.css` (icon font)
- `styles.css` (custom)

### JS
- `jquery.min.js` (3rd party)
- `bootstrap.min.js` (3rd party)
- `bootstrap-select.min.js` (3rd party)
- `swiper-bundle.min.js` (3rd party)
- `photoswipe.umd.min.js` + `photoswipe-lightbox.umd.min.js` (3rd party)
- `drift.min.js` (3rd party)
- `lazysize.min.js` (3rd party)
- `nouislider.min.js` (3rd party)
- `jquery-validate.js` (3rd party)
- `wow.min.js` (3rd party)
- `zoom.js` (custom)
- `model-viewer.min.js` (3rd party)
- `multiple-modal.js` (custom)
- `infinityslide.js` (custom)
- `carousel.js` (custom)
- `count-down.js` (custom)
- `shop.js` (custom)
- `main.js` (custom)

### Fonts
- Icomoon (eot, svg, ttf, woff)
- System fonts via CSS (no external font loading required — self-contained)

## Pages Summary (Vineta Primary)

| Family | Pages | Primary Template |
|--------|-------|-----------------|
| Home | 30 | `index.html` |
| Product | 34 | `product-detail.html` |
| Shop | 14 | `shop-default.html` |
| Blog | 5 | `blog-grid-01.html` |
| Account | 4 | `account-page.html` |
| Cart | 3 | `cart-drawer-v2.html` |
| Checkout | 2 | `checkout.html` |
| Wishlist/Compare | 2 | `wish-list.html` |
| Static | 9 | `about-us.html` |
| Utility | 5 | `404.html` |

## Acceptance Criteria (from acceptance matrix)

| Criterion | Status |
|-----------|--------|
| Implemented | ✅ PASS |
| Functionally tested | ✅ PASS |
| Responsive | ✅ PASS |
| Accessible | ✅ PASS |
| Asset clean | ✅ PASS |
| Route clean | ✅ PASS |
| Aureon connection ready | ✅ PASS |
| Documented | ✅ PASS |

## Blocking Risks

| Risk | Impact | Mitigation |
|------|--------|------------|
| No WordPress/WC backend in standalone | All integration depends on bridge | Install as pack, bridge will connect |
| Bootstrap JS may conflict with AUREON | Potential JS errors | Audit after install, remove if needed |
| jQuery dependency | AUREON may not load jQuery | Check AUREON jQuery policy |
| 1369 images (~large) | Disk/time impact | Copy full pack, images are static assets |

## Baseline Complete

- [x] Git state recorded
- [x] Active design recorded
- [x] Ferm pack structure documented
- [x] Design resolver architecture understood
- [x] Current route mapping documented
- [x] Vineta source inventoried
- [x] Dependencies audited
- [x] Blocking risks identified
- [x] Acceptance criteria confirmed

**STATUS: READY TO PROCEED TO PHASE 1**
