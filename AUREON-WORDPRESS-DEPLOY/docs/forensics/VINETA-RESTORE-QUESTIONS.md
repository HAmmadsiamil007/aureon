# VINETA FRONTEND RESTORATION — Deep Analysis & Blocking Questions

**Date:** 2026-09-04
**Analyst:** Buffy (Freebuff AI Agent)
**Status:** PRE-IMPLEMENTATION — ALL QUESTIONS IN THIS FILE ARE ASKED AT ONCE

---

## EXECUTIVE SUMMARY

I have completed a full, pro-level analysis of the entire Vineta + Golden AUREON
integration: the approved pack (`vineta-primary-only/`), the deployed pack
(`aureon/frontend/designs/vineta/`), the dynamic bridge (`composer.php`,
`vineta-data-shims.js`, `vineta-path-bridge.js`, `manifest.json`), the Golden Core
(`aureon/theme/`, `aureon/ferm-page.php`, `aureon/frontend/views/design.php`), the
live Docker environment (WordPress at `localhost:8080`, MySQL at 3306), the database
state (30 products, 8 categories, 2 menus, 21 pages), and all prior forensic reports.

### The situation

1. **Approved visual source = `vineta-primary-only/`** — the clean Vineta HTML pack
   (46 HTML files + css/js/fonts/images), validated by
   `PREMIUM-VINETA-FRONTEND-ACCEPTANCE-MATRIX.json` = `VINETA_PREMIUM_TEMPLATE_100_PASS`.
   Its `index.html` (4458 lines) still contains the original Vineta presentation:
   the `SUMMER SALE` hero slide, the countdown block, the **Categories tab section
   (`flat-animate-tab` with `id="women"` / `id="men"` tab panes)** and the testimonials.

2. **The deployed pack has drifted from the approved source.** The working copy of
   `aureon/frontend/designs/vineta/` was converted into a "Sole Origine" store:
   - `index.html`: hero slide text changed to `SOLE ORIGINE / Crafted in Leather`,
     the SUMMER SALE **countdown block removed**, the whole **Categories tab section
     (women/men) removed** (763 approved-only lines), CTA rewritten to `/shop/`.
   - `monochrome-black.css` (SO black/white override) added to the manifest.
   - `product-detail.html`, `shop-default.html`, etc. differ mostly by
     H2→H1 / H4→H1 accessibility fixes (small, likely desirable).
   - The **committed HEAD** version is much closer to the approved pack
     (42-line diff on `index.html`, mostly the same H1 a11y fixes).

3. **The dynamic bridge is intact and working.** `composer.php` (2177 lines) provides
   the full AUREON bridge: cart AJAX (`vineta_cart_add/update/get`), product/collection/
   search/blog data, auth bridge, customizer CSS emission, demo product fallback.
   `vineta-data-shims.js` (1776 lines) provides `VinetaCartUI`, `VinetaCustomizer`,
   `VinetaShop`, `VinetaNav`, blog/article consumers, `VinetaForms`, `VinetaA11y`.
   `vineta-path-bridge.js` rewrites frozen HTML links to WP permalinks.
   The manifest routes home/shop/product/category/blog/search/cart/checkout/account/
   static pages and loads the pack assets.

4. **Live site currently serves the Sole Origine conversion** (site name "Sole Origine",
   black/white palette, SO hero, 30 SO products, SO menus). Console is mostly clean
   (only pre-existing `Unexpected token 'export'` from WP's emoji loader, accepted on
   record). All 4 tested image 404s from an old log now return 200; only
   `images/cursor-close.svg` is genuinely missing (404).

---

## THE TASK (per the user's master prompt)

Restore the **approved Vineta UI/UX 100/100** on all pages — presentation/structure/
DOM/CSS/interactions identical to the approved pack — **while preserving every dynamic
feature**: WooCommerce (products, cart, checkout, variable products), WordPress menus,
search, auth/account, Customizer, plugins, the AUREON bridge, security, responsive and
accessibility behavior.

Formula:
```
APPROVED VINETA UI/UX (vineta-primary-only/)
        +
CURRENT WORKING AUREON BRIDGE (composer.php + data-shims + path-bridge)
        +
CURRENT WORDPRESS/WOO DATA
        =
RESTORED VINETA
```

---

## WHAT I VERIFIED (green)

| Area | State |
|---|---|
| Docker / WP / WC | ✅ Running, HTTP 200 on `/` |
| Active design | ✅ `vineta` (wp_options `aether_active_design`) |
| Bridge (composer + shims) | ✅ Present, wired to all slots |
| Cart / checkout / auth / search / menus / Customizer consumers | ✅ Present in data-shims |
| Product pages, shop grid, category archives | ✅ Working (per prior reports) |
| Responsive / a11y / console | ✅ Prior PASS with accepted pre-existing artifacts |
| Approved pack integrity | ✅ 46 HTML pages + assets, 100-PASS matrix |

---

## BLOCKING QUESTIONS (answered all at once)

### Q1 — Approved source confirmation
The pasted master prompt references
`C:\Users\hamma\Downloads\vinetahtml-10\vinetahtml-10\vineta-package\vineta-ready-for-wordpress`
which is **outside this project and not accessible from here**. The in-repo folder
`vineta-primary-only/` looks like exactly that approved pack (100-PASS matrix + docs).

**Q1:** May I treat **`vineta-primary-only/`** as the approved Vineta visual source?

### Q2 — Content vs presentation
The live DB currently holds **Sole Origine** data (30 SO products, SO categories, SO
menus, black/white Customizer palette, "Sole Origine" site name). The approved pack
shows the **original Vineta demo** (fashion products, SUMMER SALE hero, category tabs,
demo colors).

**Q2:** When restoring "100/100 same UI/UX", should I:
- (A) **Restore original Vineta demo content too** (demo products/categories/colors/
  hero copy/branding) — full visual + data reset to the approved pack;
- (B) **Keep Sole Origine business data** (products, categories, menus, branding,
  palette) and restore only the approved Vineta **presentation structure/DOM/CSS**
  (hero slider structure, Categories tab section, product card design, spacing);
- (C) Something else (specify)?

### Q3 — Page scope of "all the page 100/100"
The approved pack has 46 HTML files; the manifest currently routes the main families
(home, shop, product, category, blog, search, cart, checkout, account, static pages,
404). The pack also contains many **unrouted variant pages** (product-countdown-timer,
product-swatch-dropdown, product-group, product-video, shop-left-sidebar,
shop-infinity-scroll, shop-filter-drawer, cart-drawer-v2, cart-empty, wish-list,
compare, account-addresses/orders/details, blog-list-01, newsletter-popups, etc.).

**Q3:** Should "100/100" mean:
- (A) Only the **currently routed families** (restore each to approved presentation);
- (B) **Every HTML page in the pack** wired to the manifest/routes where a WP/WC
  counterpart exists, and variants mapped to their closest real route;
- (C) Current families + static pages only, variants documented but not routed?

### Q4 — Golden Core modifications
The Golden Core (`aureon/theme/`, `aureon/ferm-page.php`, `aureon/frontend/views/`)
currently has **uncommitted working-tree modifications** (`ferm-page.php`,
`theme/inc/frontend.php`, `theme/inc/aether-cart.php`, `frontend/views/design.php`)
from prior sessions (logged-in account routing, complete-page cart fragment, etc.).
The rules say Golden Core is frozen.

**Q4:** Keep the existing uncommitted Core changes (they power dynamic features), and
make **pack-level changes only** during this restoration? Or restore Core files to
their committed state too?

### Q5 — `monochrome-black.css` (SO override)
This pack-level override (registered last in the manifest) forces the black/white
Sole Origine chrome. It is **not** in the approved pack.

**Q5:** Keep it (if we keep SO branding per Q2-B) or remove it (if we restore the
approved Vineta look per Q2-A)?

### Q6 — Database content
If we restore the approved Vineta presentation, the demo products JSON
(`demo/demo-products.json`) exists for fallback (`aether_demo_mode=auto` — demo shows
only when no real products exist; 30 real SO products currently exist, so demo is
hidden).

**Q6:** Keep the current DB (SO products/categories/menus/Customizer values) and
restore only the frontend presentation? Or also reset Customizer/menus/pages to the
approved Vineta defaults?

### Q7 — Verification method
The user requires verification that all pages are 100/100 same UI/UX **without
breaking dynamic features**.

**Q7:** OK to run a full Playwright browser verification against `localhost:8080`
(screenshots at 1440/1024/768/390, DOM/structure compare vs approved pack, console +
network audit, and functional tests of cart/checkout/auth/search/Customizer)?

### Q8 — Deployment copies
There are also `AUREON-WORDPRESS-DEPLOY/` and `AUREON-GOLDEN-COPY/` trees with the
same pack files.

**Q8:** After restoring `aureon/frontend/designs/vineta/`, should I sync the same
changes into `AUREON-WORDPRESS-DEPLOY/` (and keep `AUREON-GOLDEN-COPY/` as the frozen
record, updating it only if you say so)?

---

## RECOMMENDED DEFAULT PLAN (if no answer given)

1. Use `vineta-primary-only/` as the approved source (Q1).
2. Restore approved presentation structure/DOM/CSS for all routed families, **keep
   current business data** (Q2-B): restore the Categories tab section, hero slider
   structure, product-card design, spacing, footer; keep real products/menus flowing
   through the bridge (Q3-A, Q6 keep DB).
3. Pack-level changes only; leave existing Core working-tree changes untouched (Q4).
4. Keep `monochrome-black.css` only if SO palette is kept (Q5 tied to Q2).
5. Full Playwright verification at 4 viewports + functional regression (Q7).
6. Sync pack into `AUREON-WORDPRESS-DEPLOY/`; leave Golden Copy frozen unless told
   otherwise (Q8).