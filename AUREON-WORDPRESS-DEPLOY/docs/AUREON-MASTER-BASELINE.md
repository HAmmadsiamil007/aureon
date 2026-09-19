# AUREON Master Baseline — Current Deploy Tree

**Status:** Verified live on `http://localhost:8080` (Docker WordPress).
**Date:** 2026-09-19 (Phase C + D verified)
**Supersedes:** All historical reports/audits. This file describes the CURRENT filesystem.
**Core state:** FROZEN — boundary enforced by `scripts/check-core-freeze.php` against `docs/core-freeze-manifest.json`.
**Client workflow spec:** `docs/AUREON-DESIGN-PACK-CONTRACT.md` (Phase D).

---

## 1. Authoritative Source Tree

| Layer | Path | Notes |
|---|---|---|
| Theme (WP bootstrap, resolver, WC templates) | `themes/aureon/` | Single executable tree. Nested `themes/aureon/theme/` duplicate REMOVED → `_archive/legacy-theme-mirror/` |
| Plugin (AUREON Studio) | `plugins/aureon-studio/` | Single runtime. Nested `plugins/aureon-studio/plugin/` copy REMOVED → `_archive/legacy-nested-plugin/` |
| Frontend engine | `frontend/views/`, `frontend/adapters/`, `frontend/components/`, `frontend/tokens/` | Design-agnostic engine |
| Design pack (active) | `frontend/designs/vineta/` | `manifest.json` + `composer.php` + frozen HTML/CSS/JS |
| QA tooling | `scripts/qa/*.cjs`, `scripts/check-duplicate-runtime.sh` | Reusable, exit-code based |
| Archives (never runtime) | `_archive/` | Non-executable history |

**Guard:** `bash scripts/check-duplicate-runtime.sh` must exit 0. It fails on any duplicate executable theme/plugin tree.

## 2. Route Contract (Manifest-Authoritative)

`themes/aureon/ferm-page.php` → `aureon_ferm_resolve_page()`:

- **Manifest is the ONLY template source.** No legacy Ferm fallback remains in active runtime.
- Route classes resolved from manifest `pages`: home, shop, product, collections, search, blog, **blog_single** (`is_singular('post')`), cart, checkout, account, static, 404 (prefers `404.html`).
- Unmatched route → `false` → **real HTTP 404**. A 200 never means "wrong page".

**Server-side rewriter (`aureon_ferm_rewrite_paths`)** converts frozen-HTML links to real WP URLs:
`index.html`→`/`, `collections/X`→`/product-category/X`, `products/X`→`/product/X`, `account/*`→`/my-account/`, `blogs/*`→`/blog/`, `pages/X`→`/X`, `cart.html`→`/cart/`, `checkout.html`→`/checkout/`, plus **manifest `pages.static` bare filenames** (e.g. `about-us.html`, `privacy-policy.html`) → real page permalinks. Works even when JS is blocked.

## 3. Data Contracts (Preserved, Do Not Break)

- **Commerce:** native WooCommerce. `vineta_cart_add` AJAX (nonce-gated) → `item_count`, badge `.nav-cart .count-box` updates live; cart page renders inside `form.woocommerce-cart-form` with `.cart-item` rows; empty checkout redirects to cart.
- **Dynamic data bridge:** `window.VinetaPageData` (composer) consumed by `js/vineta-data-shims.js` (products, article, menus, footer, contact, search, customizer).
- **Search suggestions:** `vineta_get_search_suggestions()` — Customizer curated list (`aether_search_suggestions`) or real product categories. Never hardcoded.
- **Contact data:** `aether_contact_*` options; email from `admin_email` with **no fake fallback**.
- **Legal links:** `vineta_get_static_page_url()` → real WP page permalink, falls back to site URL, **never `#`**.
- **Footer static links:** server-rewritten from manifest `pages.static` (see §2).

## 4. Account Templates (jQuery Contract)

`myaccount/login.php`, `register.php`, `my-account.php` are standalone (no `wp_head`). They now explicitly `wp_enqueue_script('jquery')` + print `wp_print_scripts('jquery')` in `<head>` **before** `main.js`, so no `jQuery is not defined`. `main.js` also guards `WOW` absence.

## 5. Verification Commands (full release gate)

```bash
# --- Static gates ---
php scripts/check-core-freeze.php             # frozen-core checksums unchanged (Phase D boundary)
bash scripts/check-duplicate-runtime.sh       # one executable theme/plugin tree
php scripts/check-assets.php                  # 0 missing-required asset references
php scripts/check-customizer-contracts.php    # DEAD 0 / ORPHANED 0
find themes plugins frontend -name '*.php' -print0 | xargs -0 -n1 php -l   # 0 lint errors
find frontend/designs/vineta/js -name '*.js' ! -name '*.min.js' -print0 | xargs -0 -n1 node --check

# --- Live gates (Docker WP must be running) ---
for r in / /shop/ /blog/ /about-us/ /contact-us/ /privacy-policy/ /cart/ /my-account/ /hello-world/; do curl -s -o /dev/null -w "%{http_code} $r\n" -L "http://localhost:8080$r"; done
curl -s -o /dev/null -w "%{http_code} 404\n" "http://localhost:8080/nonexistent-xyz/"   # must be 404
node scripts/qa/console-check.cjs   # 0 unintended console errors per route
node scripts/qa/cart-final.cjs      # ATC -> badge -> cart page smoke
node scripts/qa/shots.cjs           # 15 routes x desktop(1440)/mobile(390) -> qa-new/baseline/
node scripts/qa/shots-6vp.cjs       # 15 routes x 6 viewports + overflow check -> qa-new/phase-d/
```

## 6. Last Verified Results (Evidence — Phase C/D, 2026-09-19)

- **Core-freeze gate:** PASS — 298 frozen files checksum-verified; tamper test FAILs correctly on modification.
- PHP lint: **348/348, 0 errors**. Pack JS: **0 syntax errors**.
- Duplicate-runtime guard: **PASS**. Asset gate: **173 refs, 0 missing-required** (8 demo refs, graceful).
- Customizer matrix: **DEAD 0 / ORPHANED 0** (ENGINE_DEMO 19, documented) — `docs/customizer-contract-matrix.csv`.
- `eval()`: **0 executable occurrences** (snippet registry replaces both former sites; XSS/tamper probes verified in-container).
- Route matrix: correct page identity on all routes; unknown route → **real 404**.
- Console: **0 unintended errors** across 11 routes.
- Cart smoke: ATC → AJAX success → badge 0→1 → item + Subtotal/Total ₨4,500.
- Performance (C1): CSS 1.93→0.98 MB (single manifest authority), duplicate jQuery removed, product/shop JS page-gated → **−1.2 MB and −14–17 requests per route**.
- Screenshots: **30/30** (Phase C baseline) and **90/90 across 6 viewports, 0 overflow** (Phase D) — `qa-new/baseline/`, `qa-new/phase-d/`.

## 7. Remaining Known Items (Non-Blocking, Design Layer)

1. **Image/CDN compression pass** (WebP/AVIF, lazy-loading) — optional design-layer work; the frozen core is payload-clean (no duplicates, page-aware JS).
2. **8 demo-variant assets** — intentionally absent on demo-only pages (product-3d/product-video/product-group), `onerror` graceful fallbacks; never on production routes.
3. **ENGINE_DEMO Customizer keys (19)** — luxury-engine section adapters kept for non-complete-page designs; documented in the matrix.
4. **Tracker plugins** — `reddit-for-woocommerce` / `snapchat-for-woocommerce` disabled in this environment (empty pixel IDs). Not AUREON code.

## 8. Client Workflow (Frozen Core Rule — Phase D)

AUREON core is **frozen and machine-enforced** (`php scripts/check-core-freeze.php` fails on any byte drift in `themes/aureon/`, `plugins/aureon-studio/`, `frontend/views|tokens|adapters/`).

Client work happens exclusively in `frontend/designs/<pack>/` (HTML, CSS, JS, assets, composer mappings, pack Customizer sections), consuming the contracts documented in **`docs/AUREON-DESIGN-PACK-CONTRACT.md`**:

- `window.VinetaPageData` payloads (product/card/contact/search/blog/customizer)
- `data-aureon-slot="global.*"` frozen-slot contract
- `data-vineta-add` + `VinetaCart.add()` commerce binding
- manifest.json as route + asset authority (page-gated JS)
- `aureon_settings` Customizer bucket + `aether_repeater_schemas` (hero/social/announcement/category)

Never solve a visual problem by changing the frozen core. Ship only when every gate in §5 passes.
