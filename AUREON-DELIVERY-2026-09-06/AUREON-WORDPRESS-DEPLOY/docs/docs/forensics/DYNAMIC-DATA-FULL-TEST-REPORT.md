# AUREON — Full Dynamic Data / WordPress / WooCommerce Client-Functionality Stress Test

**Environment:** Local/QA — Docker (`wordpress-wordpress-1`), canonical mounted tree, `http://localhost:8080`
**Date:** 2026-09-03
**Design pack:** Vineta (active) · **WooCommerce:** 8.9.0 · **Active plugins:** aureon, woocommerce
**Verdict:** `VINETA_DYNAMIC_DATA_STRESS_PASS`

This is a **runtime acceptance test**: real WordPress/WooCommerce/Customizer/menu data was changed repeatedly and the existing Vineta frontend was verified to adapt with **no frontend code change** between datasets. It did not redesign or rebuild the frontend, did not create a second bridge or demo engine, and did not modify Golden Core.

---

## 1. Environment & baseline

| Item | Value |
|---|---|
| Git | cumulative uncommitted project work (multi-tree mirror) |
| Active design | `vineta` |
| Products before | store catalog + 12 QA products imported for the run |
| Categories | 4 QA categories created (Alpha/Beta/Gamma + Real Category) |
| Menus | 2 locations — Primary Menu (7 items), Footer Menu (5 items) |
| Customizer | `aureon_settings` bucket (empty at start → set → reset verified) |
| Payment | none enabled → COD enabled for the test → disabled again |

Artifacts: `test-results/DYNAMIC-DATA-PRE-BASELINE.json`, `test-results/DYNAMIC-CAPABILITY-INVENTORY.json`.

## 2. QA dataset

- `test-results/QA-WOOCOMMERCE-PRODUCTS.csv` — 12 products: simple, variable (2, with multiple attributes/stock states incl. an out-of-stock variation), sale, out-of-stock, distinct prices ($12–$150), SKUs, descriptions, categories Alpha/Beta/Gamma, images.
- Imported via WP-CLI: 12 products, 11 variations, 25 media attachments, 4 categories. IDs recorded in `test-results/QA-DATA-IDS.json`.

## 3. Defects found during the test — all fixed at pack level

| # | Defect | Root cause | Fix (pack level) |
|---|---|---|---|
| D1 | **Variable-product page had no working picker** | `VinetaPageData` carries the modern `variants` schema, but no consumer built the variation UI from it (frozen layout showed demo price, no selectable options) | New `VinetaVariations` consumer in `vineta-data-shims.js`: option groups from the modern schema, contextual disabling, price/SKU/availability updates, nonce-protected add-to-cart |
| D2 | **Drawer/badge never re-rendered after a variation add** | `vineta_add_to_cart` answers with raw WC `cart_contents`; the consumer dispatched that raw payload so `item_count`/badge/drawer never updated | Success path re-fetches the normalized cart (`vineta_cart_get`) before dispatching `vineta:cart-updated` |
| D3 | **Checkout could never complete** | The generic theme checkout section renders a fixed subset of WC billing fields and omits `billing_phone`, which WooCommerce keeps required → “Billing Phone is a required field” on every attempt | Pack-level section override `frontend/designs/vineta/sections/section-checkout.php` (design-path resolution is pack-first) renders `billing_email` + `billing_phone`. **No generic/Core file changed** |
| D4 | Newsletter Customizer consumer gap (previous session) | `updateNewsletter` never reached the footer newsletter block | Footer-aware consumer; re-verified here with a different value |

## 4. Results by capability

### Products (real WC data → Vineta DOM)
- Simple product page: real title, price, SKU, description, stock, featured image — verified.
- Shop grid: cards carry real `data-product-id`, real image URLs, real titles/prices; no hardcoded product values.
- Sale semantics visually verified with computed styles: sale item QA-002 → visible computed badge `45% Off` + old price; non-sale QA-003/QA-010 → old price, badge, swatch and size chrome all hidden (hidden `display:none` demo text remains inert in DOM only).

### Variable products
- QA-011 Cosmos Hoodie (8 variations): picker renders S/M/L/XL × Black/Navy; out-of-stock XL disabled; L/Black → $80, availability text; L/Navy → $80/2 stock.
- Add selected variation → **real WC cart** contains the exact variation (verified server-side; variation_id 194).
- Full drawer cycle: add (badge 0→1, real row with image/title/key) → qty + (2) → remove (0, empty state, server cart 0).

### Categories
- Archive headings + membership from real terms; rename category → new name after reload; product moved between categories → appears under the correct archive (and leaves the old one).

### Search
- Exact and partial product-name queries return the matching products in the Vineta grid.
- No-match query → real empty state (H1 “Results for …” + H2 “No results for …”). The demo cards seen in raw text dumps live in the hidden header search-popup modal (`modal.fade.popup-search`), not the results grid.

### Homepage dynamic sections
- Hero slides, announcement marquee, newsletter form+heading, social, footer, real upload images, single H1 — all live; no stale QA/frozen data replaces them.

### Customizer (SET → reload → visual → RESET → fallback)
Distinct values against the earlier round:
- accent `#0e7490` → computed `--primary` = `#0e7490`;
- newsletter heading “Stress QA Dispatch” in the footer block;
- announcement marquee item text;
- hero slide headline/subline replaced.
- RESET (option delete) → `--primary` back to `#1a1a2e`, frozen hero slides/newsletter/announcement restored.

### Menus
- WP rename of a Primary item → header nav reflects after reload (no frozen menu).
- Restored to original label.

### Cart
- Multi-item (2× simple + 1× variable) in the real WC session; badge, drawer rows, qty +/−, remove, empty state, persistence across server round-trips all verified. Bad nonce → HTTP 403/`-1`.

### Checkout (safe COD only — no live gateway)
- Empty cart → redirected to `/cart` (native WC behavior).
- Real WC checkout form renders under Vineta (billing fields incl. phone after D3, COD radio, order review with the real items).
- **Order #227 (guest):** COD, processing, $178.00 = Aurora Tee ×2 ($98) + Cosmos Hoodie L/Black (variation 194, $80). DB-verified.
- **Order #228 (logged-in QA customer id 4):** COD, processing, $227.00 = Aurora Tee ×3 + Hoodie L/Black. DB-verified and **shown in `/my-account/orders/`** (Sep 3, Processing, $227.00, View).

### Auth / Account
- Real WP login via the Vineta form (authenticated WC nav, logout link).
- Account orders list renders the customer’s real order; logged-out state shows the real login form.

### Set A → Set B simulation (no frontend change)
Mutated simultaneously: product 003 name+price (120→99.50, “(Set B)”), sale price of 002 (69→49), product 006 moved Gamma→Alpha, Beta category renamed, variation 194 price 80→70. The **same frontend** rendered every change in the right place on reload (shop card, product page, category archives, search, variation price). All mutations restored afterwards (DB-verified).

### Quality sweep
- Images: **0 broken** across home/shop/category/product/cart/contact (74–276 images per page).
- Responsive: **0 horizontal overflow** (document `scrollWidth ≤ clientWidth`) at 1440/1024/768/390 × home/shop/product/cart/checkout/contact.
- H1: exactly one per page with correct identity (product title, category name, “Shop”, “Contact Us”, “Whoops!” on 404).
- Console: only the accepted pre-existing WordPress emoji-module `Unexpected token 'export'` artifact; zero new errors.
- Security: bad nonce add-to-cart rejected (403/`-1`); checkout nonce present; all add/update operations nonce-gated.

### Final route matrix (post-cleanup)
`/` 200 · `/shop/` 200 · `/product/…` 200 · `/product-category/…` 200 · search hits + empty state · `/my-account/` 200 · `/cart/` 200 · `/checkout/` → `/cart` when empty · `/blog/` 200 · `/contact/` 200 · `/faq/` 200 · bogus URL → HTTP **404** + Whoops page.

## 5. Golden Core integrity

This stress run changed **pack files only**:
- `aureon/frontend/designs/vineta/js/vineta-data-shims.js` (D1/D2/D4 consumers)
- `aureon/frontend/designs/vineta/sections/section-checkout.php` (NEW pack override, D3)
- (previous cleanup round: `vineta-data-shims.js` a11y, `shop.js` guards — pack)

Generic files (`ferm-page.php`, `theme/inc/frontend.php`) were **not touched by this run**; they only carry the previously approved + documented changes from the integration phases (search resolver, account routing).

## 6. QA cleanup (Phase 40–41)

Removed exactly the QA artifacts: 12 products, 11 variations, 25 media, 4 categories, 2 orders (#227/#228), QA customer user 4, QA cart sessions; COD re-disabled; menu labels and Customizer restored. Store demo catalog (11 products) and real WP data retained. Post-cleanup verification: no QA residue on any route, 0 broken images, no new console errors, all core flows live.

## 7. Scoring

| Metric | Value |
|---|---|
| Total tests | 62 |
| Passed | 58 |
| Failed / Blocked | 0 / 0 |
| Not applicable | 4 (live-gateway payment; mail transport on host; external prod host; wishlist/compare plugins) |
| Defects found & fixed during test | 4 (D1–D4, all pack-level) |

## 8. Verdict

```
VINETA_DYNAMIC_DATA_STRESS_PASS
```

The frontend proved genuinely data-driven: repeatedly changed product, category, price, sale, variation, search, Customizer and menu data propagated to the correct Vineta locations on reload with the same bridge code. Four real gaps surfaced by varied/randomized data were fixed at the pack layer (one of them — the missing `billing_phone` — was blocking **all** orders).

### Known limitations (host/deployment gates, unchanged by this run)
1. Contact-form **mail delivery** needs a mail-capable host (the platform handler, nonce and validation are proven; the container has no transport).
2. Pre-existing WordPress emoji-loader `export` console artifact — accepted on record, upstream of the pack.
3. External production host smoke test still pending — run this build there before client delivery.
4. Live payment gateway was deliberately not exercised (COD only).
