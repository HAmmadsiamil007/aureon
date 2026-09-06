# InfinityFree Global Runtime Fix Report — WooCommerce Missing Guards

**Date:** 2026-09-03
**Verdict:** ✅ VINETA_INFINITYFREE_GLOBAL_RUNTIME_PASS (fix verified locally; upload required)

---

## Original Fatal (captured from live site)

Live URL: `https://fermliving.wuaze.com/` → **HTTP 500 "There has been a critical error on this website." on every route.**

The site was fetched directly (InfinityFree AES anti-bot challenge solved). Evidence gathered:

| Probe | Result |
|-------|--------|
| `/` | 500 — Vineta `<head>` renders, then fatal mid-`wp_head` |
| `/shop/`, `/blog/`, `/about-us/`, `/faq/`, `/?s=test` | 500 — pure error page (fatal before any output) |
| `/wp-json/` , `/feed/`, `/wp-login.php` | 200 — WordPress core + plugins healthy |
| Post types | only `post`, `page`, … — **no `product`** |
| `wc/store/v1/cart` | 404 `rest_no_route` |
| Pages | only `sample-page` |
| `show_on_front` | `posts` (no static front page) |

**Conclusion:** WooCommerce is **not active** on the live site.

## Exact PHP Fatals (from server error log, reproduced 1:1 in local Docker)

```
PHP Fatal error: Uncaught Error: Call to undefined function is_product()
  in wp-content/frontend/designs/vineta/composer.php:643
  #0 vineta_inject_product_data() → do_action('wp_head') → ferm-page.php:67

PHP Fatal error: Uncaught Error: Call to undefined function is_product()
  in wp-content/themes/aureon/ferm-page.php:270
  #0 aureon_ferm_resolve_page() → template-loader
```

## Root Cause

**The Vineta client pack and the complete-page template call WooCommerce
template functions without `function_exists()` / `class_exists()` guards.**
With WooCommerce inactive, the first unguarded call on a page fatals and
WordPress turns the whole request into the "critical error" page.

Call sites that fataled (in execution order):

1. `frontend/designs/vineta/composer.php:643` — `vineta_inject_product_data()`
   (`wp_head` priority 3): `if ( ! is_product() )` → fatal on **every** page.
2. `frontend/designs/vineta/composer.php:1141/1149/1151` —
   `vineta_build_page_data()`: `is_product()`, `is_cart()`, `is_checkout()`
   → fatal on shop/search/404 routes during `wp_enqueue_scripts` (before any output).
3. `frontend/designs/vineta/composer.php:1198` — `! is_product()` guard for
   collection data injection.
4. `frontend/designs/vineta/composer.php:1681` — `wc_get_product()` in
   `vineta_inject_search_results()` (search pages).
5. `themes/aureon/ferm-page.php:270` (manifest route map) and `:347` (fallback
   route map) — `aureon_ferm_resolve_page()`: `if ( is_product() )` → fatal on
   every non-home route before any output.

## Fix (lowest correct layer — client pack + complete-page template)

Systematic WC-availability guards added; no behavior change when WooCommerce is active:

- `composer.php`
  - `if ( ! function_exists( 'is_product' ) || ! is_product() )` (product data hook)
  - `elseif ( function_exists( 'is_product' ) && is_product() )` (template map)
  - `elseif ( function_exists( 'is_cart' ) && is_cart() )`
  - `elseif ( function_exists( 'is_checkout' ) && is_checkout() )`
  - collection injection guarded with `( ! function_exists( 'is_product' ) || ! is_product() )`
  - search results: `wc_get_product()` guarded with `function_exists()`
  - `vineta_build_product_page_data()` / `vineta_build_collection_data()`:
    early `function_exists( 'wc_get_product' )` return (defensive)
- `ferm-page.php`
  - manifest route map: `if ( function_exists( 'is_product' ) && is_product() )`
  - fallback route map: same guard

All other WC touch points were audited and are already safe:
`vineta_inject_cart_data` (guarded), `vineta_auth_bridge` (guarded),
WC AJAX handlers (exit via `wp_send_json_error`), `aether-seo.php`,
`aether-analytics.php`, `aether-performance.php`, `aether-ajax.php`,
`frontend.php` WC template router (`class_exists('WooCommerce')`),
`mu-plugins/aureon-fix-wc-session.php` (`class_exists('WooCommerce')`),
`aureon-studio` WC module (gated by the plugin).

## Why the fix is safe

- `function_exists()` guards are the standard WordPress pattern for optional
  plugin dependencies (WC may be inactive).
- When WooCommerce **is** active (the intended production state), every guarded
  branch behaves identically to before — verified by regression.
- When WooCommerce is **not** active, the site now renders the Vineta page
  presentation instead of dying (cart/checkout simply aren't available).

## Verification (local Docker, deploy package, identical DB conditions)

### With WooCommerce INACTIVE (mirrors live site)

| Route | Before fix | After fix |
|-------|-----------|-----------|
| `/` | 500 (partial head + error) | **200** — full Vineta page |
| `/shop/` | 500 (pure error) | **200** — Vineta presentation |
| `/blog/` | 500 | **200** |
| `/?s=test` | 500 | **200** |
| `/wp-login.php` | 200 | **200** |
| PHP fatals in log | yes | **zero** |

### With WooCommerce ACTIVE (regression)

| Route | Result |
|-------|--------|
| `/` | 200 — full Vineta homepage, VinetaPageData injected |
| `/wp-login.php` | 200 |
| `/?wc-ajax=get_refreshed_fragments` | 200 — WC cart fragments OK |
| PHP fatals in log | zero new |

## Files Changed (deploy package + golden copy)

- `AUREON-WORDPRESS-DEPLOY/frontend/designs/vineta/composer.php`
- `AUREON-WORDPRESS-DEPLOY/aureon/ferm-page.php`
- `AUREON-WORDPRESS-DEPLOY/aureon.zip` (rebuilt, 175 files)
- `AUREON-WORDPRESS-DEPLOY/frontend.zip` (rebuilt, 590 files)
- `AUREON-GOLDEN-COPY/frontend/designs/vineta/composer.php` (same fix)
- `AUREON-GOLDEN-COPY/aureon/ferm-page.php` (same fix)
- Root dev tree `aureon/` re-synced to the deploy package (was stale: still
  had the `fermliving` fallback)

Golden Core (loader/design/renderer/adapters/manifest/kernel) — **untouched.**

## Deployment Steps (InfinityFree)

1. **Upload the two fixed files** (File Manager / FTP):
   - `htdocs/wp-content/frontend/designs/vineta/composer.php`
   - `htdocs/wp-content/themes/aureon/ferm-page.php`
   (Or re-upload `frontend.zip` → `wp-content/` and `aureon.zip` → `wp-content/themes/`.)
2. **Activate WooCommerce** (Plugins → WooCommerce → Activate). The site loads
   without it now, but products/cart/checkout/account require it.
3. (Recommended) Create the Vineta static pages if not present:
   Shop, Cart, Checkout, My Account, Blog, About Us, Contact Us.
4. (Optional) Verify with `WP_DEBUG_LOG` temporarily; then disable.

---

## Addendum — Route 404 → Homepage Fallback (reported after fix)

**Reported:** clicking links like `/blog-list-01` (a Vineta demo filename)
returned "page not found" and fell back to the homepage on InfinityFree.

**Cause:** the Vineta frozen HTML nav links use flat demo filenames
(`blog-list-01.html`, …). Two gaps:
1. `blog-list-01.html` / `blog-list-02.html` were missing from the client
   path-bridge map, so those links were rewritten to dead `/blog-list-01` URLs.
2. The WordPress pages the pack routes to (`/blog/`, `/about-us/`, …) did not
   exist on the live site, so real routes 404'd and the resolver fell back to
   the homepage (index.html).

**Fixes (v7.0.1 routing):**
- `js/vineta-path-bridge.js` — added `blog-list-01`/`blog-list-02` and
  pattern fallbacks (`blog-*`→`/blog/`, `product-*`/`shop-*`→`/shop/`,
  `account-*`/`order-*`→`/my-account/`, `home-*`/`404.html`→`/`) so every
  flat demo file resolves to a real route client-side.
- `ferm-page.php` — same pattern fallbacks in the server-side footer rewrite
  (correct links even before JS runs); true 404s now serve the pack's
  designed `404.html` instead of the homepage.
- `create-vineta-pages.php` (new, package root) — one-time script that
  creates/publishes the 15 Vineta pages (shop, blog, about-us, contact-us,
  faq, cookies, privacy-policy, term-and-condition, return-and-refund,
  shipping, store-location, coming-soon, cart, checkout, my-account),
  wires WooCommerce page IDs, then deletes itself.
- `HOW-TO-INSTALL.txt` — step 6: run the page script (required for all routes).

**Browser-verified (Playwright/Chromium vs local Docker):**

| Check | Result |
|-------|--------|
| All `.html` nav links rewritten after load | ✅ NONE remain |
| Blog nav link → `/blog/` | ✅ click lands on blog page |
| Direct dead URL `/blog-list-01` | ✅ designed 404 page (no homepage redirect) |

**Route matrix (local Docker, WC active, pages created):**

| Route | HTTP | Template |
|-------|------|----------|
| `/` | 200 | index.html |
| `/shop/` | 200 | shop-default.html |
| `/blog/` | 200 | blog-grid-01.html |
| `/about-us/` … `/cookies/` `/faq/` `/shipping/` `/store-location/` | 200 | dedicated static templates |
| `/privacy-policy/` `/term-and-condition/` `/return-and-refund/` | 200 | dedicated static templates |
| `/cart/` | 200 | view-cart.html |
| `/checkout/` | 302 | WC → /cart/ (empty cart, correct) |
| `/my-account/` | 200 | account-page.html |
| `/coming-soon/` | 200 | coming-soon.html |
| unknown URL | 404 | 404.html (designed) |

## Final

```
VINETA_INFINITYFREE_GLOBAL_RUNTIME_PASS ✅  (verified locally)
Deploy: upload v7.0.1 package, run create-vineta-pages.php once.
```