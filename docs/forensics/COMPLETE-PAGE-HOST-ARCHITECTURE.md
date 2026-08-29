# Complete-Page Host Architecture — Forensic Audit

**Date:** 2026-08-28
**Status:** READ-ONLY AUDIT — NO CODE CHANGES MADE
**Scope:** Minimum generic architecture for isolated complete-page hosting

---

## Executive Summary

The AUREON complete-page host mechanism (`ferm-page.php`) already provides the correct structural isolation: it opens a fresh HTML document, extracts the frozen client body, and wraps it with `wp_head()`/`wp_footer()`. The AETHER shell (header.php → aether_compose_header / footer.php → aether_compose_footer) is correctly bypassed.

**The actual failure is asset contamination, not structural contamination.**

When `ferm-page.php` calls `wp_head()`, the WordPress asset pipeline injects:

1. **Platform CDN CSS** — Bootstrap 5.3.3, Font Awesome 6.5.1, Swiper 11 (loaded by `aether_design_enqueue_assets()` at priority 20)
2. **Platform contract JS** — animations.js, main.js, countdown.js, GSAP 3.12.5, ScrollTrigger (same function)
3. **Design token CSS** — `:root` custom properties from `aether_enqueue_tokens()` at priority 98
4. **Pack CSS/JS** — Ferm's own assets from manifest.json (correct, but depends on suppressed platform handles)
5. **WooCommerce assets** — cart fragments, scripts (required for commerce)
6. **WordPress assets** — admin bar, jQuery, comment reply script (standard WordPress)

The frozen Ferm page also loads its own CSS (`app.adf0bc36b7.css`) and JS (`app.1e7cf79a09.js`, `product.fa97565a5f.js`, etc.) via `<link>` and `<script>` tags in its `<head>` and `<body>`.

**Result:** Two competing CSS/JS ecosystems render in the same browser context:
- Ferm's Tailwind CSS + Embla carousel + vanilla JS
- AETHER's Bootstrap CSS + Swiper + GSAP + jQuery + contract JS

This produces CSS specificity conflicts, JS global namespace collisions, and load-orderdependent visual breakage — even though the Ferm HTML itself is correct.

**The fix is NOT to rebuild the Ferm frontend.** The fix is to suppress platform assets in complete-page mode and let only the pack's own CSS/JS load.

---

## 1. Current Architecture — Complete-Page Router

### 1.1 Request Flow

```
WordPress request
    │
    ▼
functions.php (bootstrap)
    ├─ loads inc/frontend.php
    │   ├─ loads frontend/views/loader.php (defines AETHER_FRONTEND_DIR)
    │   ├─ loads aether-tokens.php, aether-cart.php, aether-ajax.php, etc.
    │   ├─ aureon_aether_frontend_boot() @ priority 30
    │   │   └─ aether_frontend_boot() → loads all kernel files
    │   ├─ aureon_aether_suppress_theme_output() @ priority 1000
    │   │   └─ dequeues legacy theme CSS/JS (aureon-style, aureon-fonts, etc.)
    │   ├─ aureon_aether_enqueue_assets() @ priority 20
    │   │   └─ IF luxury: loads CDN stack + local CSS/JS
    │   │   └─ ELSE: returns (pack owns assets)
    │   └─ aureon_ferm_template_include() @ template_include priority 998
    │       └─ detects 'fermliving' → returns ferm-page.php
    │
    ▼
ferm-page.php
    ├─ aureon_ferm_resolve_page() → maps WP route to frozen HTML file
    ├─ file_get_contents($pack_dir . $file) → reads complete HTML
    ├─ echo "<!DOCTYPE html>\n<html lang='en'>\n<head>"
    ├─ echo "<meta charset>..."
    ├─ wp_head()  ← AETHER ASSETS INJECTED HERE
    ├─ echo "</head>\n<body>"
    ├─ aureon_ferm_extract_body($html) → extracts <body> content
    ├─ echo $body  ← FERM CONTENT OUTPUT
    ├─ wp_footer()  ← WC FRAGMENTS, ANALYTICS INJECTED HERE
    └─ echo "</body>\n</html>\nexit"
```

### 1.2 Route Mapping

| WordPress Route | Frozen HTML File | Notes |
|----------------|-----------------|-------|
| Front page / Home | `index.html` | 9,937 lines, 475 KB |
| Single product | `products/{slug}.html` | Falls back to first available |
| Shop / Product archive | `collections/furniture.html` | Default collection |
| Product category | `collections/{slug}.html` | Falls back to furniture |
| Contact | `pages/contact.html` | Static page map |
| About | `pages/about-ferm-living.html` | Static page map |
| Store locator | `pages/store-locator.html` | Static page map |
| Blog / Stories | `blogs/stories.html` | Blog listing |
| Search results | `blogs/stories.html` | Fallback to blog |
| 404 | `pages/contact.html` | Fallback to contact |
| Cart | `cart.php` (AETHER) | Bypassed by priority 99 |
| Checkout | `checkout/form-checkout.php` (AETHER) | Bypassed by priority 99 |
| Account | `myaccount/my-account.php` (AETHER) | Bypassed by priority 99 |

### 1.3 Document Model

```
ferm-page.php opens:
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        wp_head()
            ├─ WordPress: admin bar CSS, jQuery, comment-reply
            ├─ AETHER platform: Bootstrap CSS, FA CSS, Swiper CSS ← CONFLICT
            ├─ AETHER platform: Bootstrap JS, Swiper JS, GSAP JS ← CONFLICT
            ├─ AETHER contract: animations.js, main.js, countdown.js ← CONFLICT
            ├─ AETHER tokens: :root CSS custom properties ← CONFLICT
            ├─ Pack CSS: fonts.ferm-open-source.css, fonts.fd2d67c5ce.css, app.adf0bc36b7.css ← CORRECT
            ├─ Pack JS: speedblitz.js, ferm-data-shims.js, app.js ← CORRECT
            └─ WC: cart fragments, scripts ← REQUIRED
        </head>
        <body>
            [Ferm frozen body content — header, hero, products, footer, etc.]
            wp_footer()
                ├─ WC cart fragments (fragments Script)
                ├─ WC add-to-cart handler
                ├─ WP comment reply script
                ├─ WP admin bar
                └─ Analytics (if active)
        </body>
    </html>
```

**Who owns what:**

| Document Element | Owner | Notes |
|-----------------|-------|-------|
| `<!DOCTYPE html>` | ferm-page.php | WordPress opens fresh doc |
| `<html lang>` | ferm-page.php | Hardcoded `'en'` |
| `<meta charset>` | ferm-page.php | From `get_bloginfo('charset')` |
| `<meta viewport>` | ferm-page.php | Hardcoded |
| `<head>` content | wp_head() | Mixed: WordPress + AETHER + Pack + WC |
| `<body>` content | Frozen Ferm HTML | Extracted via regex |
| `<body>` attributes | NONE | Lost — `body_class()` not called |
| `<script>` at body end | wp_footer() | WC + WP + Analytics |

---

## 2. Asset Isolation Audit

### 2.1 Assets Loaded in Complete-Page Mode

#### CSS (loaded via wp_head → aether_design_enqueue_assets)

| Handle | Source | Type | Required for Ferm? | Conflict Risk |
|--------|--------|------|-------------------|---------------|
| `aether-bootstrap` | CDN: Bootstrap 5.3.3 | Platform CSS | **NO** | **HIGH** — Tailwind vs Bootstrap |
| `aether-fontawesome` | CDN: FA 6.5.1 | Platform CSS | **NO** | **MEDIUM** — Ferm has own icons |
| `aether-swiper` | CDN: Swiper 11 | Platform CSS | **NO** | **HIGH** — Ferm uses Embla |
| `aether-pack-css-fonts-ferm-open-source` | Pack: fonts.ferm-open-source.css | Pack CSS | **YES** | NONE |
| `aether-pack-css-fonts-fd2d67c5ce` | Pack: fonts.fd2d67c5ce.css | Pack CSS | **YES** | NONE |
| `aether-pack-css-app-adf0bc36b7` | Pack: app.adf0bc36b7.css | Pack CSS | **YES** | NONE |
| `aether-tokens` | Inline: :root vars | Platform CSS | **NO** | **MEDIUM** — may override Ferm vars |
| WooCommerce styles | Plugin CSS | WC CSS | **PARTIAL** | LOW — WC pages only |
| WordPress core | WP CSS | WP CSS | **MINIMAL** | LOW |

#### JS (loaded via wp_head → aether_design_enqueue_assets)

| Handle | Source | Type | Required for Ferm? | Conflict Risk |
|--------|--------|------|-------------------|---------------|
| `aether-bootstrap-js` | CDN: Bootstrap 5.3.3 | Platform JS | **NO** | **LOW** — Ferm doesn't use Bootstrap JS |
| `aether-swiper-js` | CDN: Swiper 11 | Platform JS | **NO** | **MEDIUM** — global Swiper obj |
| `aether-gsap` | CDN: GSAP 3.12.5 | Platform JS | **NO** | **LOW** — Ferm uses CSS animations |
| `aether-scrolltrigger` | CDN: GSAP ScrollTrigger | Platform JS | **NO** | **LOW** |
| `aether-animations` | Engine: animations.js | Platform JS | **NO** | **HIGH** — may init Bootstrap components |
| `aether-main` | Engine: main.js | Platform JS | **NO** | **HIGH** — global init, AJAX setup |
| `aether-countdown` | Engine: countdown.js | Platform JS | **NO** | **LOW** |
| `aether-pack-js-speedblitz` | Pack: speedblitz.min.js | Pack JS | **YES** | NONE |
| `aether-pack-js-ferm-data-shims` | Pack: ferm-data-shims.js | Pack JS | **YES** | NONE |
| `aether-pack-js-app` | Pack: app.1e7cf79a09.js | Pack JS | **YES** | NONE (but deps on aether-main!) |
| `aether-pack-js-product` | Pack: product.fa97565a5f.js | Pack JS | **YES** (product pages) | NONE (but deps on aether-main!) |
| `aether-pack-js-customer` | Pack: customer.5de68fbefc.js | Pack JS | **YES** (account pages) | NONE (but deps on aether-main!) |
| jQuery | WP core | WP JS | **PARTIAL** | LOW — WC needs it |
| WC cart fragments | Plugin JS | WC JS | **YES** | NONE |
| WC add-to-cart | Plugin JS | WC JS | **YES** | NONE |

### 2.2 Critical Dependency Chain Problem

The Ferm pack JS assets declare dependencies on `aether-main`:

```json
// manifest.json
{ "file": "app.1e7cf79a09.js", "deps": ["aether-main", "ferm-data-shims"] }
{ "file": "product.fa97565a5f.js", "deps": ["aether-main", "ferm-data-shims"] }
```

`aether-main` is the platform contract JS (`frontend/assets/js/main.js`). If we suppress platform assets, `aether-main` never loads, and WordPress will NOT enqueue the pack assets that depend on it.

**This is the core coupling problem:** The pack assets were designed to coexist with platform assets. Removing platform assets breaks the dependency chain.

### 2.3 Asset Categorization

```
REQUIRED FERM:
    app.adf0bc36b7.css (main CSS)
    fonts.ferm-open-source.css (font substitution)
    fonts.fd2d67c5ce.css (original fonts)
    app.1e7cf79a09.js (main app bundle)
    speedblitz.min.95accfb9a4.js (PJAX)
    ferm-data-shims.js (data shims)
    product.fa97565a5f.js (PDP bundle, product pages only)
    customer.5de68fbefc.js (account pages only)

REQUIRED WORDPRESS:
    admin bar CSS/JS (when logged in)
    jQuery (WC dependency)
    comment-reply script (when comments active)

REQUIRED WOOCOMMERCE:
    wc-cart-fragments (cart count updates)
    wc-add-to-cart-variation (variable products)
    woocommerce.css (minimal, WC pages only)

AUREON PLATFORM (CONFLICTING):
    Bootstrap 5.3.3 CSS — CONFLICTS with Tailwind
    Font Awesome 6.5.1 CSS — redundant
    Swiper 11 CSS — CONFLICTS with Embla
    Bootstrap 5.3.3 JS — unnecessary
    Swiper 11 JS — unnecessary
    GSAP 3.12.5 — unnecessary
    ScrollTrigger — unnecessary
    animations.js — may reinit components
    main.js — global init, AJAX setup
    countdown.js — unnecessary

AUREON TOKENS (CONFLICTING):
    :root CSS custom properties — may override Ferm vars

UNNECESSARY:
    aether-fonts (Cabinet Grotesk — Ferm uses Canela/KHTeka)
    aether-style (Luxury engine CSS)
    aether-motion (Luxury animations)
    aether-responsive (Luxury responsive)
    aether-a11y (Luxury a11y)
    aether-pages (Luxury page styles)
    aether-lenis (Luxury smooth scroll)
    aether-lenis-scroll (Luxury scroll handler)
    aether-phantom-bridge (Luxury bridge)
```

---

## 3. Document Environment Audit

### 3.1 Who Controls Each Document Region

| Region | Current Owner | Ideal Owner | Gap |
|--------|--------------|-------------|-----|
| `<!DOCTYPE html>` | ferm-page.php | ferm-page.php | NONE |
| `<html>` attributes | ferm-page.php (hardcoded `lang='en'`) | ferm-page.php | Missing `data-country`, `data-shop` |
| `<head>` — meta charset | ferm-page.php | ferm-page.php | NONE |
| `<head>` — meta viewport | ferm-page.php | ferm-page.php | NONE |
| `<head>` — wp_head() | WordPress + AETHER + Pack + WC | Pack ONLY (+ WC) | **PLATFORM ASSETS LEAK** |
| `<head>` — Ferm internal `<link>`/`<script>` | Frozen HTML body content | N/A | Ferm's own head elements are in body (extracted) |
| `<body>` attributes | MISSING | Should have `data-template`, `data-money-format` | **BODY ATTRS LOST** |
| `<body>` content | Frozen Ferm HTML | Frozen Ferm HTML | NONE (correct) |
| `<body>` — wp_footer() | WordPress + WC + Analytics | WC + Analytics only | **MINIMAL LEAK** |

### 3.2 Document Model Issues

**Issue 1: `<body>` attributes lost**

The frozen Ferm HTML has:
```html
<body data-template='index' data-money-format='EUR {{amount_with_comma_separator}}'>
```

`ferm-page.php` extracts inner body content via regex, discarding the `<body>` tag attributes. Ferm's JS may read `data-template` and `data-money-format`.

**Issue 2: Ferm's `<head>` content in body**

The frozen HTML contains `<head>` elements (font links, preloaded scripts). When `aureon_ferm_extract_body()` extracts between `<body>` and `</body>`, these head elements (if any are inside body) come through correctly. But the frozen HTML's own `<head>` is discarded — only the body content is preserved.

**Issue 3: Relative asset paths**

The frozen Ferm HTML uses relative paths like `../cdn/shop/t/164/assets/app.css`. When served from WordPress (e.g., `/collections/furniture/`), these resolve correctly if the pack directory is properly URL-mapped. Currently the pack assets are enqueued via WordPress `wp_enqueue_style/script` with absolute URLs from `aether_pack_url()`, so relative paths in the HTML body may point to non-existent URLs.

**Issue 4: Meta contamination**

WordPress may inject OpenGraph tags, RSS links, etc. via `wp_head()`. The frozen Ferm HTML has its own OpenGraph tags. These may conflict.

---

## 4. Complete Page Preservation

### 4.1 What Can Be Preserved

| Element | Preserved? | Notes |
|---------|-----------|-------|
| Full HTML DOM structure | **YES** | Extracted verbatim from frozen HTML |
| All CSS classes | **YES** | Body content is verbatim |
| All IDs | **YES** | Body content is verbatim |
| All data attributes | **YES** | Body content is verbatim |
| CSS relationships | **PARTIAL** | Pack CSS loaded via wp_enqueue, not inline in `<head>` |
| JS hooks (data-component) | **YES** | Ferm's JS reads data-component attributes |
| Source asset order | **NO** | WordPress injects AETHER assets BEFORE pack assets |
| `<head>` content from frozen source | **NO** | Discarded by body extraction |
| `<body>` tag attributes | **NO** | Discarded by body extraction |

### 4.2 What Cannot Be Preserved Without Changes

| Element | Issue | Fix Required |
|---------|-------|-------------|
| Body `data-template` attribute | Lost by regex extraction | Modify `ferm-page.php` to preserve body tag attrs |
| Body `data-money-format` attribute | Lost by regex extraction | Same |
| Frozen source `<head>` elements | Discarded | Not needed — pack CSS/JS loaded via enqueue |
| Asset load order | AETHER assets load before pack assets | Suppress AETHER assets in complete-page mode |

---

## 5. Thin Data Bridge

### 5.1 Current Bridge Architecture

The existing Ferm data bridge (`frontend/designs/fermliving/composer.php`) provides:

| Bridge | Status | Mechanism |
|--------|--------|-----------|
| Cart AJAX | **IMPLEMENTED** | `ferm_wc_ajax_cart_add/update/get` via `wp_ajax_*` |
| Cart bridge JS | **IMPLEMENTED** | `ferm-data-shims.js` intercepts fetch to `/cart/*.js` |
| Product remapping | **IMPLEMENTED** | `ferm_remap_product()` via adapter filters |
| Site data | **IMPLEMENTED** | `ferm_site_data()` filter |
| Header data | **IMPLEMENTED** | `ferm_header_data()` (cart_count, is_home) |
| Footer data | **IMPLEMENTED** | `ferm_footer_data()` filter |
| Blog data | **IMPLEMENTED** | `ferm_blog_data()` filter |
| Search data | **IMPLEMENTED** | `ferm_search_data()` filter |
| Newsletter data | **IMPLEMENTED** | `ferm_newsletter_data()` filter |
| Demo products | **IMPLEMENTED** | `ferm_demo_products()` fallback from JSON |
| Demo categories | **IMPLEMENTED** | `ferm_demo_categories()` fallback from JSON |

### 5.2 What the Bridge Does NOT Provide

| Gap | Issue | Impact |
|-----|-------|--------|
| `window.FermPageData` | No server-side data injection into complete Ferm page | Ferm JS relies on shims, not real data |
| Navigation URLs | Ferm HTML has hardcoded Shopify URLs | Links point to `/collections/...` which may not match WP permalinks |
| Product data in HTML | Frozen HTML has demo products | Real WC products not injected into body |
| Collection data in HTML | Frozen HTML has demo categories | Real WC categories not injected into body |
| Customer state | `ferm-data-shims.js` has stub `FermCustomer` | Real WC customer data not provided |
| Money formatting | `Shopify.formatMoney` shim exists | Works but may not match WC price format |
| Search endpoint | `ferm-data-shims.js` has no search bridge | Ferm's search overlay calls Shopify predictive search |
| Language selector | Not implemented | Frozen source has language selector, bridge ignores it |

### 5.3 Minimum Generic Bridge Design

For a complete page to be dynamic without DOM rebuild, the bridge must:

```
1. INJECT DATA into the page
   └─ window.FermPageData = { ... } via wp_localize_script or inline script
       ├─ cart: { items, count, total }
       ├─ customer: { logged_in, name, email }
       ├─ shop: { currency, money_format, url }
       ├─ navigation: { main: [...], footer: [...] }
       ├─ products: { /* current page products if applicable */ }
       └─ config: { ajax_url, nonce, wc_ajax_url }

2. BRIDGE APIS
   └─ Cart: intercept fetch(/cart/*.js) → WC AJAX
       ├─ /cart/add.js → ferm_wc_ajax_cart_add
       ├─ /cart/change.js → ferm_wc_ajax_cart_update
       ├─ /cart/update.js → ferm_wc_ajax_cart_update
       ├─ /cart.js → ferm_wc_ajax_cart_get
       └─ /cart/clear.js → WC cart empty
   └─ Search: intercept search API → WP_Query
   └─ Customer: read from FermPageData (server-rendered)

3. PRESERVE JS HOOKS
   └─ Ferm's app.js reads:
       ├─ window.Shopify (shimmed)
       ├─ window.FermCart (shimmed)
       ├─ window.FermProducts (populated from real data)
       ├─ window.FermNavigation (populated from real data)
       ├─ data-component attributes (preserved in body)
       └─ data-template, data-money-format (preserved on body tag)
```

---

## 6. WooCommerce Asset Isolation

### 6.1 WC Assets Required for Complete-Page Mode

| Asset | Required? | Pages | Notes |
|-------|----------|-------|-------|
| `wc-cart-fragments` | **YES** | All | Updates cart count in header |
| `wc-add-to-cart-variation` | **YES** | Product | Variable product support |
| `wc-checkout` | **YES** | Checkout | Checkout form handling |
| `wc-account` | **YES** | Account | Account pages |
| `woocommerce` (base CSS) | **MINIMAL** | WC pages | Only on cart/checkout/account |
| `wc-quantity-input` | **YES** | Product | Quantity selector |

### 6.2 WC Assets That Can Be Suppressed on Ferm Pages

| Asset | Suppress? | Reason |
|-------|----------|--------|
| `woocommerce-general` | **YES** on non-WC pages | Ferm has own styling |
| `woocommerce-layout` | **YES** on non-WC pages | Ferm has own layout |
| `woocommerce-smallscreen` | **YES** on non-WC pages | Ferm has own responsive |
| `wc-price-format` | **KEEP** | Price display |

### 6.3 WC Script Isolation Strategy

WooCommerce scripts (`wc-cart-fragments`, `wc-add-to-cart-variation`) are enqueued by the WooCommerce plugin and are required for cart functionality. They should NOT be suppressed. However, their CSS can be suppressed on complete-page routes to avoid conflicts with Ferm's styling.

---

## 7. AUREON Asset Isolation

### 7.1 Platform Assets That Must NOT Load in Complete-Page Mode

These assets are loaded by `aether_design_enqueue_assets()` for non-luxury designs and conflict with the frozen Ferm frontend:

| Handle | Type | Conflict | Suppression Method |
|--------|------|----------|-------------------|
| `aether-bootstrap` | CSS | Bootstrap grid/utilities vs Tailwind | Skip in `aether_design_enqueue_assets()` |
| `aether-fontawesome` | CSS | FA icons vs Ferm icons | Skip in `aether_design_enqueue_assets()` |
| `aether-swiper` | CSS | Swiper styles vs Embla | Skip in `aether_design_enqueue_assets()` |
| `aether-bootstrap-js` | JS | Bootstrap JS init | Skip in `aether_design_enqueue_assets()` |
| `aether-swiper-js` | JS | Swiper global | Skip in `aether_design_enqueue_assets()` |
| `aether-gsap` | JS | GSAP global | Skip in `aether_design_enqueue_assets()` |
| `aether-scrolltrigger` | JS | ScrollTrigger | Skip in `aether_design_enqueue_assets()` |
| `aether-animations` | JS | Platform init | Skip in `aether_design_enqueue_assets()` |
| `aether-main` | JS | Platform init, AJAX | Skip in `aether_design_enqueue_assets()` |
| `aether-countdown` | JS | Countdown init | Skip in `aether_design_enqueue_assets()` |
| `aether-tokens` | CSS | :root vars | Skip in `aether_enqueue_tokens()` |

### 7.2 Suppression Mechanism

**Option A: Check design slug in asset functions**

Add a complete-page check to `aether_design_enqueue_assets()`:

```php
function aether_design_enqueue_assets() {
    $design = aether_active_design();
    $dir    = aether_active_design_dir();
    if ( ! $dir ) { return; }

    // COMPLETE-PAGE ISOLATION: skip platform CDNs and contract JS
    if ( aether_is_complete_page_design( $design ) ) {
        // Only enqueue pack assets from manifest
        $manifest = aether_design_manifest();
        foreach ( (array) ( $manifest['assets']['css'] ?? array() ) as $entry ) {
            aether_enqueue_pack_asset( $entry, 'css', ... );
        }
        foreach ( (array) ( $manifest['assets']['js'] ?? array() ) as $entry ) {
            aether_enqueue_pack_asset( $entry, 'js', ... );
        }
        return;
    }

    // ... existing platform CDN + pack asset loading
}
```

**Option B: Manifest-driven suppression**

Add a `"complete_page": true` flag to `manifest.json` and check it in asset functions. This is more generic.

**Recommended: Option B** — more generic, works for any future client.

### 7.3 Dependency Chain Fix

The Ferm pack JS declares deps on `aether-main` and `ferm-data-shims`. If `aether-main` is suppressed, pack JS won't load.

**Fix:** Remove `aether-main` from pack JS deps in `manifest.json`:

```json
// BEFORE
{ "file": "app.1e7cf79a09.js", "deps": ["aether-main", "ferm-data-shims"] }

// AFTER
{ "file": "app.1e7cf79a09.js", "deps": ["ferm-data-shims"] }
```

The `ferm-data-shims.js` already provides all the Shopify globals that `app.js` needs. The `aether-main` dep was only needed for the platform's AJAX/localize setup, which the shims now provide.

---

## 8. Dynamic Data Without DOM Rebuild

### 8.1 Current Data Flow (Broken)

```
WordPress/WooCommerce
    ↓
Adapters (23 adapter files)
    ↓
Normalized data arrays
    ↓
AETHER renderer → Pack templates → PHP-rendered HTML
    ↓
[FERM PAGE DOES NOT USE THIS PATH]
```

The Ferm complete page bypasses the entire adapter → renderer → template pipeline. The adapters and composer.php filters run, but their output is never rendered because `ferm-page.php` serves the frozen HTML directly.

### 8.2 Required Data Flow (Proposed)

```
WordPress/WooCommerce
    ↓
Adapters (unchanged)
    ↓
ferm-page.php → reads frozen HTML
    ↓
Injects window.FermPageData via wp_localize_script
    ↓
Ferm's frozen HTML + Ferm's app.js
    ↓
app.js reads FermPageData + FermCart + FermProducts + etc.
    ↓
Browser renders dynamic content
```

### 8.3 Data Injection Points

| Data | Source | Injection Method | Priority |
|------|--------|-----------------|----------|
| Cart state | `WC()->cart` | `ferm_bridge` localized script (already exists) | HIGH |
| Customer state | `wp_get_current_user()` | New `FermPageData` localized script | HIGH |
| Navigation | `wp_get_nav_menu_items()` | Inline `<script>` in `ferm-page.php` | HIGH |
| Shop config | `wc_get_permalink()`, currency | `ferm_bridge` localized script (extend) | HIGH |
| Product data | `WC_Product` | Only on PDP — inject via inline script | MEDIUM |
| Collection data | `get_terms('product_cat')` | Only on CLP — inject via inline script | MEDIUM |
| Money format | WooCommerce settings | Extend `ferm_bridge` | MEDIUM |
| Search endpoint | WordPress search | Replace Shopify predictive search | LOW |

### 8.4 FermPageData Schema

```javascript
window.FermPageData = {
    // Cart
    cart: {
        items: [{ id, variant_id, quantity, title, price, image, url }],
        item_count: 0,
        total_price: 0,
        currency: 'EUR'
    },
    // Customer
    customer: {
        logged_in: false,
        id: null,
        email: null,
        first_name: null,
        last_name: null,
        addresses: []
    },
    // Shop
    shop: {
        name: 'Ferm Living',
        url: '/',
        currency: 'EUR',
        money_format: 'EUR {{amount_with_comma_separator}}',
        money_format_decimals: 'EUR {{amount_with_comma_separator}}'
    },
    // Navigation (for URL mapping)
    navigation: {
        main: [{ title, url, children: [{ title, url }] }],
        footer: [{ title, url }]
    },
    // Config
    config: {
        ajax_url: '/wp-admin/admin-ajax.php',
        nonce: '...',
        wc_ajax_url: '/?wc-ajax=...',
        is_logged_in: false,
        template: 'index',
        money_format: 'EUR {{amount_with_comma_separator}}'
    }
};
```

---

## 9. Business Actions Bridge

### 9.1 Cart Operations

| Action | Shopify API | WC AJAX Endpoint | Bridge Status |
|--------|-------------|------------------|---------------|
| Add to cart | `POST /cart/add.js` | `ferm_cart_add` (wp_ajax) | **IMPLEMENTED** |
| Update quantity | `POST /cart/change.js` | `ferm_cart_update` (wp_ajax) | **IMPLEMENTED** |
| Get cart | `GET /cart.js` | `ferm_cart_get` (wp_ajax) | **IMPLEMENTED** |
| Clear cart | `POST /cart/clear.js` | Not implemented | **MISSING** |
| Cart count | `GET /cart.json` | WC fragments | **PARTIAL** (via WC fragments) |

### 9.2 Search

| Action | Shopify API | WC/WP Endpoint | Bridge Status |
|--------|-------------|----------------|---------------|
| Predictive search | `GET /search/suggest.json` | WordPress `/?s=` | **MISSING** |
| Search results | `GET /search?q=` | WordPress `/?s=` | **MISSING** |

Ferm's search overlay uses Shopify predictive search. Bridge needs to intercept and route to WordPress search.

### 9.3 Account

| Action | Shopify API | WC Endpoint | Bridge Status |
|--------|-------------|-------------|---------------|
| Login | `POST /account/login` | `wp_login` | **MISSING** |
| Register | `POST /account` | `wp_create_user` | **MISSING** |
| Orders | `GET /account/orders` | WC account | **MISSING** |
| Addresses | `GET /account/addresses` | WC account | **MISSING** |

### 9.4 Checkout

Checkout is already routed to AETHER templates (cart.php, checkout/form-checkout.php, myaccount/my-account.php) via the priority 99 template_include filter. No Ferm bridge needed for checkout.

---

## 10. Future-Client Compatibility

### 10.1 Test: Could Client B Use This Architecture?

**Client B scenario:**
- HTML + Bootstrap + custom CSS + GSAP + Lenis + Swiper
- Complete frontend from frozen source
- Needs WooCommerce integration

**Compatibility assessment:**

| Requirement | Supported? | Notes |
|-------------|-----------|-------|
| Complete HTML preservation | **YES** | `ferm-page.php` model works for any client |
| CSS isolation | **YES** | With `complete_page` flag in manifest |
| JS isolation | **YES** | With dependency chain fix |
| Dynamic data injection | **YES** | `FermPageData` schema is generic |
| Cart bridge | **YES** | Shopify API interception is generic |
| WooCommerce integration | **YES** | WC templates bypassed for cart/checkout/account |
| No AETHER shell | **YES** | `ferm-page.php` already bypasses shell |
| No component rebuild | **YES** | Complete HTML served directly |

**Verdict: YES — the architecture is future-client compatible.**

The key is the `complete_page` flag in `manifest.json`. When set, the engine suppresses platform assets and only loads pack assets. The bridge provides data injection via localized scripts. No section/component rendering is involved.

### 10.2 Generic Complete-Page Host Parameters

For any future client, the design pack needs:

```json
{
    "id": "client-b",
    "complete_page": true,
    "assets": {
        "css": ["client-b.css"],
        "js": [
            { "file": "client-b-app.js", "deps": [] },
            { "file": "client-b-bridge.js", "deps": [] }
        ]
    },
    "pages": {
        "home": "index.html",
        "cart": "cart.html",
        ...
    }
}
```

And `ferm-page.php` (renamed to `complete-page.php` or made generic) handles the rest.

---

## 11. Core Change Boundary

### 11.1 Minimum Files for Generic Change

| # | File | Current Behavior | Required Generic Behavior | Why Pack-Only Is Insufficient | Regression Risk |
|---|------|-----------------|--------------------------|------------------------------|-----------------|
| 1 | `frontend/views/assets.php` | Loads platform CDNs + pack assets for all non-luxury designs | Check `manifest['complete_page']` flag; if true, skip platform CDNs and contract JS, load only pack assets | The platform CDN loading is hardcoded in `aether_design_enqueue_assets()`, not configurable per-pack | LOW — only affects non-luxury designs with `complete_page` flag |
| 2 | `aureon/theme/inc/frontend.php` | `aureon_aether_enqueue_assets()` returns early for non-luxury; `aureon_aether_suppress_theme_output()` dequeues legacy theme assets | Add complete-page-specific suppression: dequeue AETHER platform assets when `complete_page` is active | The suppression function only targets legacy theme assets, not AETHER platform assets | LOW — only suppresses when `complete_page` flag is set |
| 3 | `aureon/theme/inc/aether-tokens.php` | Outputs `:root` CSS custom properties for all designs | Skip token output when `complete_page` design is active | Tokens may conflict with client CSS custom properties | LOW — only skips for `complete_page` designs |
| 4 | `aureon/theme/ferm-page.php` | Hardcoded to `fermliving` design, opens fresh doc, extracts body | Make generic: read `complete_page` flag from manifest, preserve `<body>` tag attributes, support any design slug | Currently ferm-page.php is Ferm-specific; needs to work for any complete-page client | MEDIUM — changes the template routing logic |
| 5 | `frontend/designs/fermliving/manifest.json` | Declares `assets.js[].deps` including `aether-main` | Remove `aether-main` from pack JS deps (use `ferm-data-shims` as sole dep) | The dep chain breaks when platform assets are suppressed | LOW — only affects Ferm pack |
| 6 | `frontend/designs/fermliving/composer.php` | Registers cart bridge + product JS enqueues | Extend `ferm_bridge` localization to include FermPageData (cart, customer, shop, nav) | The bridge exists but doesn't inject data into the complete page | LOW — additive change to existing bridge |
| 7 | `frontend/designs/fermliving/cdn/shop/t/164/assets/ferm-data-shims.js` | Stub methods that log to console | Wire stub methods to real WC AJAX endpoints using `ferm_bridge.ajax_url` | The shims exist but are stubs; need real WC integration | LOW — only affects Ferm pack |

### 11.2 Files That Must Remain Untouched

| File | Reason |
|------|--------|
| `frontend/views/loader.php` | Engine kernel — any change affects all designs |
| `frontend/views/design.php` | Pack resolution — core infrastructure |
| `frontend/views/registry.php` | Section registry — not involved in complete-page mode |
| `frontend/views/renderer.php` | Component renderer — not involved in complete-page mode |
| `frontend/views/composer.php` | Shell composition — not used in complete-page mode |
| `frontend/views/viewmodel.php` | Data normalization — not involved in complete-page mode |
| `frontend/adapters/*.php` | All 23 adapters — may be used for data injection, but not for rendering |
| `frontend/sections/*.php` | All 26 sections — not involved in complete-page mode |
| `frontend/manifest/components.php` | Component manifest — not involved in complete-page mode |
| `aureon/theme/functions.php` | Theme bootstrap — must not change |
| `aureon/theme/header.php` | Not used in complete-page mode |
| `aureon/theme/footer.php` | Not used in complete-page mode |
| `aureon/theme/front-page.php` | Not used in complete-page mode (ferm-page.php intercepts) |

---

## 12. Final Architecture

### 12.1 Architecture Decision

```
COMPLETE_PAGE_HOST_FEASIBLE = YES
    The existing ferm-page.php model provides correct structural isolation.
    Only asset contamination needs to be fixed.

THIN_BRIDGE_FEASIBLE = YES
    The existing composer.php + ferm-data-shims.js provide the bridge skeleton.
    Needs: real WC AJAX wiring, FermPageData injection, body attribute preservation.

FRONTEND_REBUILD_REQUIRED = NO
    The frozen Ferm HTML is correct. No rebuild needed.

SECTION_SPLITTING_REQUIRED = NO
    Complete page mode bypasses the section/component system entirely.

CORE_CHANGE_REQUIRED = GENERIC (4 files)
    assets.php, frontend.php, aether-tokens.php, ferm-page.php
    All changes gated behind manifest['complete_page'] flag.
```

### 12.2 Proposed Architecture

```
┌─────────────────────────────────────────────────────┐
│                WORDPRESS / WOOCOMMERCE                │
│  Core + WooCommerce + Customizer + Admin             │
└──────────────────────────┬──────────────────────────┘
                           │
              ┌────────────┴────────────┐
              │    AUREON ENGINE KERNEL  │
              │  (loader, design, assets)│
              │  Gated by complete_page  │
              └────────────┬────────────┘
                           │
              ┌────────────┴────────────┐
              │  COMPLETE-PAGE ROUTER   │
              │  (ferm-page.php generic)│
              │  template_include 998   │
              └────────────┬────────────┘
                           │
         ┌─────────────────┼─────────────────┐
         │                 │                 │
    ┌────┴────┐     ┌─────┴─────┐     ┌────┴────┐
    │ FROZEN  │     │ ASSET     │     │ DATA    │
    │ HTML    │     │ ISOLATION │     │ BRIDGE  │
    │ (body)  │     │ (no CDNs) │     │ (FermP  │
    │         │     │ (pack     │     │ ageData)│
    │ verbatim│     │  only)    │     │         │
    └────┬────┘     └─────┬─────┘     └────┬────┘
         │                 │                 │
         └─────────────────┼─────────────────┘
                           │
                    ┌──────┴──────┐
                    │   BROWSER   │
                    │  Renders    │
                    │  Ferm HTML  │
                    │  + Pack CSS │
                    │  + Pack JS  │
                    │  + WC AJAX  │
                    └─────────────┘
```

### 12.3 Asset Loading in Complete-Page Mode

```
wp_head() outputs:
    ├─ WordPress: admin bar, jQuery (when needed)
    ├─ Pack CSS: fonts.ferm-open-source.css, fonts.fd2d67c5ce.css, app.adf0bc36b7.css
    ├─ Pack JS (before): speedblitz.min.js, ferm-data-shims.js
    └─ WC: minimal cart scripts (when needed)

wp_footer() outputs:
    ├─ WC cart fragments
    ├─ WC add-to-cart handler
    └─ WP admin bar (when logged in)

NOT loaded:
    ├─ Bootstrap CSS/JS
    ├─ Font Awesome CSS
    ├─ Swiper CSS/JS
    ├─ GSAP / ScrollTrigger
    ├─ animations.js, main.js, countdown.js
    ├─ AETHER tokens CSS
    └─ Any AETHER platform asset
```

---

## 13. Implementation Sequence

### Phase 1: Asset Isolation (Core Change)

1. Add `complete_page` flag support to `manifest.json` schema
2. Modify `aether_design_enqueue_assets()` to check flag and skip platform CDNs
3. Modify `aether_enqueue_tokens()` to skip for complete-page designs
4. Modify `aureon_aether_suppress_theme_output()` to also suppress AETHER platform assets
5. Update Ferm `manifest.json` to set `complete_page: true` and remove `aether-main` deps

### Phase 2: Document Model Fix

1. Modify `ferm-page.php` to read `complete_page` flag from manifest (make generic)
2. Preserve `<body>` tag attributes from frozen HTML
3. Add `data-country`, `data-shop` attributes from config
4. Suppress meta tag duplication (OpenGraph, viewport)

### Phase 3: Data Bridge

1. Extend `ferm_bridge` localization to include full `FermPageData`
2. Wire `ferm-data-shims.js` stub methods to real WC AJAX endpoints
3. Add navigation data injection (WP nav menus → Ferm format)
4. Add customer state injection
5. Add search bridge (Shopify predictive → WP_Query)

### Phase 4: Testing

1. Visual regression: Ferm page in isolated mode vs standalone source
2. Asset isolation: no Bootstrap/Swiper/GSAP in page source
3. Cart functionality: add/update/remove via Ferm UI
4. Navigation: all links resolve correctly
5. Responsive: mobile/tablet/desktop behavior preserved
6. WooCommerce: cart/checkout/account still functional

---

## 14. Testing Sequence

### 14.1 Asset Isolation Test

```bash
# After implementation, verify no platform assets load:
curl -s http://localhost/ | grep -c "bootstrap"
# Expected: 0

curl -s http://localhost/ | grep -c "swiper"
# Expected: 0

curl -s http://localhost/ | grep -c "gsap"
# Expected: 0

curl -s http://localhost/ | grep -c "font-awesome"
# Expected: 0
```

### 14.2 Visual Regression Test

```bash
# Compare Ferm page in browser vs standalone source
# Use Playwright to screenshot both and diff
```

### 14.3 Functional Test

- Add product to cart via Ferm UI → verify WC cart updated
- Update cart quantity → verify price recalculation
- Navigate via Ferm menu → verify correct page loads
- Search via Ferm overlay → verify results display
- Login via Ferm account page → verify WC account works

---

## 15. Safety Verification

| Check | Status |
|-------|--------|
| No code changes made | **VERIFIED** — this is a READ-ONLY audit |
| No database changes | **VERIFIED** |
| No frontend changes | **VERIFIED** |
| No config changes | **VERIFIED** |
| No files modified | **VERIFIED** |
| No files created | **VERIFIED** (except this report) |
| No files deleted | **VERIFIED** |

---

## Appendix A: Key File Locations

| File | Path | Lines |
|------|------|-------|
| Complete-page router | `aureon/theme/ferm-page.php` | 177 |
| Asset pipeline | `frontend/views/assets.php` | 140 |
| AETHER boot | `aureon/theme/inc/frontend.php` | 273 |
| Token output | `aureon/theme/inc/aether-tokens.php` | 354 |
| Pack resolution | `frontend/views/design.php` | 213 |
| Ferm data bridge | `frontend/designs/fermliving/composer.php` | 407 |
| Ferm manifest | `frontend/designs/fermliving/manifest.json` | 123 |
| Ferm data shims | `frontend/designs/fermliving/cdn/shop/t/164/assets/ferm-data-shims.js` | 400 |
| Theme bootstrap | `aureon/theme/functions.php` | 126 |
| Shell composition | `frontend/views/composer.php` | 72 |

## Appendix B: Existing Forensic Reports

| Report | Path | Lines |
|--------|------|-------|
| Core Theme Audit | `docs/forensics/CORE-THEME-AUDIT.md` | 339 |
| Core-to-Ferm Integration Map | `docs/forensics/CORE-TO-FERM-INTEGRATION-MAP.md` | 320 |
| Ferm Complete Integration Map | `docs/forensics/FERM-COMPLETE-INTEGRATION-MAP.md` | 611 |
| Ferm Source Inventory | `docs/forensics/FERM-SOURCE-COMPLETE-INVENTORY.md` | 444 |
| Ferm Template Audit | `docs/forensics/FERM-TEMPLATE-AUDIT.md` | 639 |
| **This Report** | `docs/forensics/COMPLETE-PAGE-HOST-ARCHITECTURE.md` | — |
