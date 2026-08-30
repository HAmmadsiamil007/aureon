# Ferm Living — Thin Integration Plan

**Date:** 2026-08-26
**Status:** COMPLETE — All audits synthesized. Ready for execution.
**Depends on:** CORE-THEME-AUDIT.md, FERM-TEMPLATE-AUDIT.md, CORE-TO-FERM-INTEGRATION-MAP.md, design spec (approved)
**Implementation plan:** `2026-08-26-ferm-premium-frontend-implementation.md`

---

## 1. Purpose

This document synthesizes the three forensic audit reports into a concrete, actionable integration strategy. It answers: **How do we minimally bridge frozen Ferm Living presentation into WordPress/WooCommerce without breaking anything?**

The implementation plan (`2026-08-26-ferm-premium-frontend-implementation.md`) contains the task-level execution. This document contains the architectural decisions, patterns, and constraints that inform those tasks.

---

## 2. Architecture Summary

### 2.1 The Thin Bridge

```
┌──────────────────────────────────────────────────────┐
│                 AUREON CANONICAL LAYER                │
│  WordPress core + WooCommerce + Customizer            │
│  23 adapters → normalized data arrays                 │
│  Security, SEO, analytics, performance                │
└───────────────────────┬──────────────────────────────┘
                        │
               ┌────────┴────────┐
               │   FERM MAPPER   │  ← NEW: pack-specific composer.php
               │   (data bridge) │     Converts adapter output → Ferm schema
               └────────┬────────┘
                        │
┌───────────────────────┴──────────────────────────────┐
│               FERM PRESENTATION LAYER                 │
│  Frozen Ferm HTML/CSS/JS (verbatim from source)      │
│  Pack templates shadow engine components              │
│  CSS animations (no GSAP/Lenis in source)            │
│  Embla carousel, PhotoSwipe lightbox                  │
│  window.FermPageData (page-scoped JSON)              │
└──────────────────────────────────────────────────────┘
```

### 2.2 Key Insight from Core Audit

The AETHER engine already supports this architecture:

- **Adapters** are the ONLY WP/WC touchpoint (23 adapters, all KEEP)
- **Components** receive normalized data arrays — never call WP/WC functions directly
- **Design packs** shadow files via `aether_resolve_design_path()` — purely filesystem-based
- **Asset isolation** is built-in: luxury assets suppressed for non-luxury designs

**The Ferm pack needs to:**
1. Override component/section templates with frozen Ferm DOM
2. Add a mapper layer that converts adapter output → FermPageData schema
3. Self-host Ferm CSS/JS/assets
4. Bridge Shopify API calls to WooCommerce endpoints

---

## 3. Integration Boundary — What Changes vs. What Stays

### 3.1 From Core Audit (KEEP unchanged)

| Layer | Files | Rule |
|-------|-------|------|
| Engine kernel | `frontend/views/*.php` (8 files) | No changes |
| Adapters | 23 adapter files | No changes |
| Tokens | `inc/aether-tokens.php` | No changes |
| Security | `inc/aether-security.php` | No changes |
| AJAX handlers | `inc/aether-ajax.php` | No changes |
| Cart fragment | `inc/aether-cart.php` | No changes |
| SEO | `inc/aether-seo.php` | No changes |
| Newsletter | `inc/aether-newsletter.php` | No changes |
| Analytics | `inc/aether-analytics.php` | No changes |
| Customizer | `inc/customizer.php` + fields | No changes |
| CSS output | `inc/css-output.php` | No changes |
| Theme templates | All `*.php` in theme root | No changes (they call `aether_render_section/component`) |

### 3.2 From Ferm Audit (FROZEN — copy verbatim)

| Aspect | Source | Rule |
|--------|--------|------|
| HTML DOM structure | Frozen `index.html`, `collections/*.html`, `products/*.html` | Copy exact classes, data-*, structure |
| CSS compiled output | Frozen `cdn/shop/t/164/assets/*.css` | Copy verbatim, no Tailwind rebuild |
| JS behavior | Frozen `cdn/shop/t/164/assets/*.js` | Copy behavior, remove Shopify API calls |
| Fonts | CanelaText-Regular, KHTeka-Regular/Medium | Self-host from frozen source |
| Animations | CSS-only (transitions + keyframes) | Copy verbatim |
| Carousels | Embla Carousel | Copy verbatim |
| Lightbox | PhotoSwipe | Copy verbatim |

### 3.3 What Gets Replaced (Integration Points)

| Frozen Shopify Behavior | WordPress/WooCommerce Replacement | Bridge Mechanism |
|------------------------|-----------------------------------|------------------|
| Liquid templates | WordPress PHP templates | Pack component overrides |
| `{{ content_for_header }}` | `wp_head()` | Standard WP |
| `{{ content_for_layout }}` | `aether_render_section()` calls | Existing engine |
| `{{ content_for_footer }}` | `wp_footer()` | Standard WP |
| Shopify cart API (`/cart.js`, `/cart/add.js`, `/cart/change.js`) | WooCommerce AJAX cart | **Cart bridge shims** |
| Shopify predictive search (`/search/suggest.json`) | WordPress `WP_Query` + WC search | **Search bridge** |
| Shopify customer API | WordPress auth + WC customer | **Account bridge** |
| Liquid JSON (`{{ product | json }}`) | `window.FermPageData` | **FermPageData mapper** |
| Clerk.io recommendations | AUREON adapter or reference data | **Recommendation bridge** |
| Klaviyo email | AUREON newsletter handler | Use `aether-newsletter.php` |
| Swym wishlist | AUREON built-in wishlist | Use `adapter-wishlist.php` |
| Trusted Shops reviews | AUREON testimonials CPT | Use `adapter-testimonials.php` |
| Shopify checkout | WooCommerce checkout | Standard WC |

---

## 4. FermPageData Mapper Architecture

### 4.1 Where It Lives

```
frontend/designs/fermliving/
  composer.php     — existing: filter hooks for composition (904 lines)
  mapper.php       — NEW: converts adapter output → FermPageData schema
```

The mapper is called by `composer.php` hooks. It receives normalized adapter data and reshapes it into the FermPageData JSON schema.

### 4.2 Mapper Pattern

```php
// mapper.php
function ferm_build_page_data() {
    $data = [
        'version'  => 1,
        'schema'   => 'fermliving-page',
        'design'   => 'fermliving',
        'page'     => ferm_get_page_info(),
        'settings' => ferm_get_settings(),
        'navigation' => ferm_get_navigation(),
        'cart'     => ferm_get_cart_data(),
        'customer' => ferm_get_customer_data(),
    ];

    // Page-type-specific data
    switch (ferm_get_page_type()) {
        case 'homepage':
            $data['hero'] = ferm_get_hero_data();
            $data['categories'] = ferm_get_categories_data();
            $data['products'] = ferm_get_products_data();
            $data['editorial'] = ferm_get_editorial_data();
            $data['rooms'] = ferm_get_rooms_data();
            break;
        case 'product':
            $data['product'] = ferm_get_product_data();
            $data['recommendations'] = ferm_get_recommendations_data();
            $data['breadcrumbs'] = ferm_get_breadcrumbs();
            break;
        case 'collection':
            $data['collection'] = ferm_get_collection_data();
            $data['products'] = ferm_get_collection_products();
            $data['filters'] = ferm_get_filters_data();
            $data['pagination'] = ferm_get_pagination_data();
            $data['sort'] = ferm_get_sort_data();
            break;
        // ... other page types
    }

    return $data;
}
```

### 4.3 Data Source Mapping

| FermPageData Field | Adapter Source | Adapter File |
|-------------------|---------------|--------------|
| `navigation.menu` | `wp_get_nav_menu_items('primary')` | `adapter-menu.php` |
| `cart.itemCount` | `WC()->cart->get_cart_contents_count()` | `adapter-cart.php` |
| `cart.total` | `WC()->cart->get_total('edit')` | `adapter-cart.php` |
| `cart.items` | `WC()->cart->get_cart()` | `adapter-cart.php` |
| `customer.isLoggedIn` | `is_user_logged_in()` | `adapter-auth.php` |
| `customer.displayName` | `wp_get_current_user()->display_name` | `adapter-account.php` |
| `hero.*` | Customizer `aether_hero_slides` | `adapter-hero.php` |
| `categories.*` | `get_terms('product_cat')` | `adapter-wc-categories.php` |
| `products.bestsellers.*` | `WP_Query` + `wc_get_product()` | `adapter-wc-products.php` |
| `product.*` | `wc_get_product()` + reviews | `adapter-product.php` |
| `collection.*` | `get_queried_object()` | WooCommerce query context |
| `filters.*` | `get_terms('product_cat')` + sale IDs | `adapter-wc-filter.php` |
| `search.*` | `WP_Query('s')` | WordPress search |
| `editorial.*` | Customizer repeater | Custom options |
| `rooms.*` | Customizer repeater | Custom options |

### 4.4 Security Constraints

**In FermPageData (public, page-load safe):**
- Product data (title, price, images, description, variants, stock)
- Navigation structure
- Cart state (item count, totals, line items)
- Page settings (currency, locale)
- Search results
- Customer: `isLoggedIn` + `displayName` (account page only)

**NOT in FermPageData:**
- Passwords, nonces, payment info, API secrets
- Full order history (use authenticated WC endpoint)
- Saved addresses (use authenticated WC endpoint)
- Server credentials

**For mutations (cart add/update/remove, search, account):**
Use existing AUREON/WooCommerce AJAX/REST contracts with proper nonces. The platform already localizes endpoint URLs and nonces via `wp_localize_script`. Do NOT create a second auth system.

---

## 5. Cart Bridge (Shopify → WooCommerce)

### 5.1 Problem

Ferm JS calls Shopify cart API:
```javascript
POST /cart/add.js     // Add item
POST /cart/change.js  // Change quantity
POST /cart/update.js  // Update attributes
GET  /cart.js         // Get cart state
```

WooCommerce uses different endpoints:
```javascript
POST /?add-to-cart={id}           // Add item
POST /?update-cart=true           // Update quantities
GET  /cart/                       // Cart page (HTML)
WC REST API: /wp-json/wc/v3/cart  // Cart data (auth required)
```

### 5.2 Solution: Cart Endpoint Shims

Create WordPress rewrite rules or AJAX handlers that intercept Ferm JS cart calls and route them to WooCommerce:

```
/cart.js          → WC AJAX get cart fragments (JSON response)
/cart/add.js      → WC ?add-to-cart= or WC AJAX add to cart
/cart/change.js   → WC update-cart AJAX
/cart/update.js   → WC update-cart AJAX
```

### 5.3 Implementation Location

```
frontend/designs/fermliving/
  cart-bridge.php    — NEW: rewrite rules + AJAX handlers
```

Registered via `composer.php` hooks in the Ferm pack.

### 5.4 Response Format

The shims must return Shopify-shaped JSON so existing Ferm JS stays untouched:

```php
// /cart.js shim
function ferm_cart_js_handler() {
    $cart = WC()->cart;
    $response = [
        'token'         => wp_create_nonce('ferm_cart'),
        'item_count'    => $cart->get_cart_contents_count(),
        'total_price'   => (int) ($cart->get_total('edit') * 100), // cents
        'total_discount' => 0,
        'currency'      => get_woocommerce_currency(),
        'items'         => array_map(function($item) {
            $product = $item['data'];
            return [
                'id'          => $item['variation_id'] ?: $item['product_id'],
                'product_id'  => $item['product_id'],
                'variant_id'  => $item['variation_id'],
                'quantity'    => $item['quantity'],
                'title'       => $product->get_name(),
                'price'       => (int) ($item['line_total'] * 100),
                'line_price'  => (int) ($item['line_total'] * 100),
                'sku'         => $product->get_sku(),
                'image'       => wp_get_attachment_url($product->get_image_id()),
            ];
        }, $cart->get_cart()),
    ];
    header('Content-Type: application/json');
    echo json_encode($response);
    wp_die();
}
```

---

## 6. CSS Strategy — Detailed

### 6.1 Extraction from Frozen Source

Frozen CSS source: `C:\Users\hamma\Downloads\SiteOne-Crawler\fermliving.com\cdn\shop\t\164\assets\`

**Process:**
1. Identify all CSS files loaded by frozen Ferm pages
2. Extract compiled Tailwind utilities + component styles
3. Do NOT rebuild Tailwind — copy compiled output verbatim
4. Scope to `.design-fermliving` where DOM is inside that scope
5. Self-host in `frontend/designs/fermliving/css/`

### 6.2 CSS Classification

| Category | Action | Notes |
|----------|--------|-------|
| Tailwind utilities (`fixed`, `z-[12]`, `w-full`, `flex`, `tab_l:grid-12`) | Copy verbatim | Include all responsive variants |
| Component styles (header, mega menu, product card, gallery, footer) | Copy verbatim | Preserve exact selectors |
| `@font-face` declarations | Copy, self-host fonts | CanelaText + KHTeka |
| `@media` queries | Copy verbatim | Preserve exact breakpoint values |
| CSS custom properties (`--site-max-width`, tokens) | Copy verbatim | Inspect for actual max-width value |
| Animation keyframes (`fade-in`, `slide-up`, `scale-in`) | Copy verbatim | CSS-only, no GSAP |
| Shopify-specific selectors | Exclude | `.shopify-section`, etc. |
| Clerk.io embedded CSS | Exclude | |
| Third-party analytics CSS | Exclude | |

### 6.3 Critical CSS

- Inline minimal above-the-fold subset only (announcement + header styles)
- Full Ferm CSS as self-hosted pack asset loaded via `manifest.json`
- Do NOT inline full Ferm CSS

### 6.4 Isolation from AETHER

- Platform Bootstrap 5.3.3 stays loaded (shared CDN handle)
- Platform Font Awesome stays loaded (shared CDN handle)
- Platform contract JS stays loaded (animations.js, main.js — verify no clash)
- Ferm CSS scoped where possible to avoid specificity wars
- Test isolation after each phase

### 6.5 Max-Width Discovery

**DO NOT hardcode 1440.** Inspect frozen compiled CSS for actual `--site-max-width` or equivalent value. The viewport width (1440px screenshot) does NOT equal the content max-width.

---

## 7. JS Strategy — Detailed

### 7.1 JS File Classification (from Ferm Audit)

| File | Classification | Shopify Dependency | Action |
|------|---------------|-------------------|--------|
| `app.js` | PLATFORM ADAPTER | Liquid menu data, cart count | Adapt: replace data source → FermPageData |
| `product.js` | PURE PRESENTATION | None | Copy verbatim |
| `cart-page.js` | SHOPIFY BUSINESS | Shopify cart API | Replace with WooCommerce AJAX |
| `collection.js` | PURE PRESENTATION | None | Copy verbatim |
| `animations.js` | PURE PRESENTATION | None | Copy verbatim |
| `mega-menu.js` | PURE PRESENTATION | Liquid menu data | Adapt: read from DOM (server-rendered) |
| `mobile-nav.js` | PURE PRESENTATION | None | Copy verbatim |
| `search.js` | SHOPIFY BUSINESS | Shopify predictive search | Replace with WordPress/Woo search |
| `newsletter.js` | SHOPIFY BUSINESS | Klaviyo | Replace with AUREON newsletter |
| `wishlist.js` | SHOPIFY BUSINESS | Swym | Replace with AUREON wishlist |

### 7.2 Third-Party Library Decision

| Library | Ferm Source | AUREON Has | Decision |
|---------|------------|------------|----------|
| Embla Carousel | Yes | No (has Swiper) | Load from Ferm pack (isolated) |
| PhotoSwipe | Yes | No | Load from Ferm pack |
| Fancybox | Yes | No | Load from Ferm pack |
| InstantClick | Yes | No | Evaluate: skip if not critical |
| GSAP | No (CSS-only animations) | Yes (GSAP 3.12.5) | Not needed for Ferm |
| Lenis | No (native scroll) | Yes | Not needed for Ferm |
| Three.js | No | Yes | Not needed for Ferm |

**Key insight:** Ferm uses CSS-only animations (transitions + keyframes), NOT GSAP/Lenis. This simplifies library deduplication — no conflict.

### 7.3 JS Loading Order

```
1. Platform CDN: Bootstrap 5.3.3 (if needed by Ferm CSS)
2. Platform contract JS: animations.js, main.js (shared)
3. Ferm pack JS: ferm-shell.js, ferm-homepage.js, etc. (from manifest.json)
4. Ferm third-party: Embla, PhotoSwipe (from pack assets)
```

### 7.4 Cart Bridge in JS

The cart bridge shims (section 5) intercept Ferm JS calls at the HTTP level. Ferm JS continues calling `/cart.js`, `/cart/add.js`, etc. — the shims translate to WooCommerce internally. **No Ferm JS changes needed for cart operations.**

---

## 8. Asset Strategy — Detailed

### 8.1 Asset Categories

| Category | Source | Target | Rules |
|----------|--------|--------|-------|
| Fonts | `cdn/shop/t/164/assets/fonts.*` | `assets/fonts/` | Self-host, preserve woff2+woff |
| Logos | `cdn/shop/t/164/assets/logo.*` | `assets/` | Verify identity (not just HTTP 200) |
| Icons (SVG) | `cdn/shop/t/164/assets/*.svg` | `assets/icons/` | Copy all UI icons |
| Payment icons | `cdn/shop/t/164/assets/payments/` | `assets/payments/` | All payment method icons |
| Hero images | `cdn/shop/t/164/assets/hero/` | `assets/hero/` | Reference/demo content |
| Category images | `cdn/shop/t/164/assets/categories/` | `assets/categories/` | Reference/demo content |
| Editorial images | `cdn/shop/t/164/assets/editorial/` | `assets/editorial/` | Reference/demo content |
| Room images | `cdn/shop/t/164/assets/rooms/` | `assets/rooms/` | Reference/demo content |
| Product images | `cdn/shop/t/164/assets/products/` | `assets/products/` | Reference/demo content |
| Product JS | `cdn/shop/t/164/assets/product.js` | `js/` | Adapted |
| Shell JS | `cdn/shop/t/164/assets/app.js` | `js/ferm-shell.js` | Adapted |
| CSS | `cdn/shop/t/164/assets/app.css` | `css/ferm.css` | Verbatim |

### 8.2 Asset Manifest

Generate `assets-manifest.json`:

```json
{
  "version": "2026-08-26",
  "assets": [
    {
      "referencePath": "cdn/shop/t/164/assets/CanelaText-Regular.woff2",
      "localPath": "assets/fonts/CanelaText-Regular.woff2",
      "hash": "sha256:...",
      "type": "font",
      "usedBy": ["css:fonts.css"]
    },
    {
      "referencePath": "cdn/shop/t/164/assets/logo.svg",
      "localPath": "assets/logo.svg",
      "hash": "sha256:...",
      "type": "image",
      "usedBy": ["shell:header"]
    }
  ]
}
```

### 8.3 Asset Verification

For each critical asset:
1. HTTP 200 from frozen source
2. SHA-256 hash computed
3. Same hash from local copy
4. Identity verified (not just same length — actual content match)

---

## 9. PHP Template Architecture

### 9.1 Template Override Map

The Ferm pack shadows AETHER engine templates via `aether_resolve_design_path()`:

| Engine Component | Pack Override | Purpose |
|-----------------|---------------|---------|
| `components/shell/announcement.php` | `fermliving/components/shell/announcement.php` | USP bar |
| `components/shell/header.php` | `fermliving/components/shell/header.php` | Site header |
| `components/shell/footer.php` | `fermliving/components/shell/footer.php` | Site footer |
| `components/shell/mobile-chrome.php` | `fermliving/components/shell/mobile-chrome.php` | Mobile nav |
| `components/cards/product.php` | `fermliving/components/cards/product.php` | Product card |
| `components/product/gallery.php` | `fermliving/components/product/gallery.php` | Product gallery |
| `components/product/info.php` | `fermliving/components/product/info.php` | Product info |
| `sections/section-hero.php` | `fermliving/sections/section-hero.php` | Homepage hero |
| `sections/section-categories.php` | `fermliving/sections/section-categories.php` | Category grid |
| `sections/section-bestsellers.php` | `fermliving/sections/section-bestsellers.php` | Product grid |
| `sections/section-cart.php` | `fermliving/sections/section-cart.php` | Cart page |

### 9.2 Template Pattern

Every pack template follows this pattern:

```php
<?php
// Data comes from adapter — NEVER call WP/WC functions here
// $data is the normalized adapter output
?>
<!-- Frozen Ferm DOM — copied verbatim from source -->
<div class="frozen-classname" data-component="frozen-name">
    <?php foreach ($data['items'] as $item): ?>
        <div class="frozen-card" data-id="<?= esc_attr($item['id']) ?>">
            <img src="<?= esc_url($item['image']) ?>" alt="<?= esc_attr($item['title']) ?>">
            <h3><?= esc_html($item['title']) ?></h3>
            <span><?= esc_html($item['price']['formatted']) ?></span>
        </div>
    <?php endforeach; ?>
</div>
```

### 9.3 FermPageData Injection

Injected via `wp_footer` hook in `composer.php`:

```php
function ferm_inject_page_data() {
    $data = ferm_build_page_data();
    ?>
    <script>
    window.FermPageData = <?= wp_json_encode($data, JSON_UNESCAPED_SLASHES) ?>;
    </script>
    <?php
}
add_action('wp_footer', 'ferm_inject_page_data');
```

---

## 10. Known Integration Gaps — Resolution

| # | Gap | Issue | Resolution | Phase |
|---|-----|-------|------------|-------|
| 1 | `aether_pack_url()` | Undefined but called by old packs | Add to `design.php` — generic fix, not Ferm-specific | Pre-phase (bugfix) |
| 2 | Cart page DOM | Missing from crawl | Reconstruct from `cart-page.js` behavior + frozen `cart.html` structure | Phase 6 |
| 3 | Language selector | Single-store handling | Add to header adapter or pack-level customizer option | Phase 1 |
| 4 | Tailwind utilities | Missing from shipped CSS | Copy prettified superset from frozen source | Phase 1 |
| 5 | Font licensing | CanelaText/KHTeka commercial | Confirm licensing before self-hosting — BLOCKER for production | Phase 0 |
| 6 | Platform contract JS | animations.js, main.js | Verify no clash with Ferm CSS animations — test in Phase 1 | Phase 1 |
| 7 | Cart bridge | Shopify cart API shims | Create `/cart.js`, `/cart/add.js`, `/cart/change.js` → WC endpoints | Phase 6 |
| 8 | Swym wishlist | Not compatible with AUREON | Use AUREON built-in wishlist (`adapter-wishlist.php`) — ignore Swym | Phase 6 |
| 9 | Klaviyo email | Not compatible with AUREON | Use AUREON newsletter handler (`aether-newsletter.php`) | Phase 5 |
| 10 | InstantClick | PWA page transitions | Evaluate in Phase 1 — likely skip (adds complexity, low value) | Phase 1 |
| 11 | Embla vs Swiper | Different carousel libs | Load Embla from Ferm pack (isolated), don't conflict with platform Swiper | Phase 1 |

### 10.1 Pre-Phase Bugfix: `aether_pack_url()`

This is a generic bugfix, not Ferm-specific. Old packs call `aether_pack_url()` but it's not defined in `design.php`. Add:

```php
// In frontend/views/design.php — generic addition
if (!function_exists('aether_pack_url')) {
    function aether_pack_url($path = '') {
        $design = aether_active_design();
        $dir = aether_active_design_dir();
        if (!$dir) return '';
        $url = content_url('designs/' . $design . '/' . ltrim($path, '/'));
        return apply_filters('aether_pack_url', $url, $path, $design);
    }
}
```

---

## 11. Route Mapping

### 11.1 Reference → Family → WordPress Target

| Reference Route (Frozen Shopify) | Page Family | WordPress Target Route |
|----------------------------------|-------------|----------------------|
| `/` | Homepage | `/` (front-page.php) |
| `/collections/all` | Archive/PLP | `/product-category/all/` |
| `/collections/furniture` | Archive/PLP | `/product-category/furniture/` |
| `/collections/rooms` | Archive/PLP | `/product-category/rooms/` |
| `/products/[slug]` | Product/PDP | `/product/[slug]/` |
| `/blogs/journal` | Blog | `/blog/` |
| `/blogs/journal/[slug]` | Article | `/blog/[slug]/` |
| `/pages/about` | About | `/about/` |
| `/pages/contact` | Contact | `/contact/` |
| `/cart` | Cart | `/cart/` |
| `/checkout` | Checkout | `/checkout/` |
| `/account` | Account | `/my-account/` |
| `/search?q=...` | Search | `/?s=...` |
| `/*` (fallback) | 404 | `/404` |

**Rule:** Do NOT require WordPress to mimic Shopify URLs. The page family determines the template; WordPress permalink configuration determines the URL.

---

## 12. Section Composition

### 12.1 Homepage Section Order

| Order | Section ID | Ferm Section | AUREON Section | Adapter |
|-------|-----------|--------------|----------------|---------|
| 1 | `hero` | Hero Split | hero-slider | adapter-hero.php |
| 2 | `categories` | Category Grid | categories | adapter-wc-categories.php |
| 3 | `bestsellers` | Product Grid | bestsellers | adapter-wc-products.php |
| 4 | `editorial` | Editorial Split | — (pack custom) | — |
| 5 | `rooms` | Room Grid | — (pack custom) | — |
| 6 | `newsletter` | Newsletter | newsletter | adapter-options.php |

Controlled by `composer.php` hooks on `aether_frontpage_sections`.

### 12.2 Ferm-specific Sections (not in AETHER engine)

Two sections have no AETHER equivalent — they're purely Ferm pack additions:

- **Editorial Split** — text/image split section (customizer-driven content)
- **Room Grid** — room-based navigation (customizer-driven content)

These need:
1. New section registration in pack `composer.php`
2. New adapter (or use customizer options directly)
3. New section template

---

## 13. Implementation Sequence Summary

```
Phase 0: Git checkpoint + bugfix (aether_pack_url) + font licensing check
Phase 1: Global shell (announcement, header, mega menu, search, mobile nav, footer)
         → Shell FermPageData → Shell CSS → Shell JS → Shell assets
Phase 2: Homepage (hero, categories, editorial, products, rooms, newsletter)
         → Homepage FermPageData → Homepage CSS → Homepage JS → Homepage assets
Phase 3: Archive/PLP (grid, filters, sorting, pagination)
         → Collection FermPageData → Archive CSS → Archive JS → Archive assets
Phase 4: Product/PDP (gallery, info, variants, ATC, accordion, recommendations)
         → Product FermPageData → Product CSS → Product JS → Product assets
Phase 5: Content (about, blog, article, contact)
         → Content FermPageData → Content CSS → Content JS → Content assets
Phase 6: Commerce (cart, checkout, account, search, 404)
         → Cart bridge shims → Commerce FermPageData → Commerce CSS/JS
Phase 7: Full visual regression (all families, all widths)
Phase 8: Isolation testing (Ferm ↔ Luxury)
Phase 9: Final 100/100 acceptance
```

Each phase follows:
```
Extract DOM → Map to PHP template → Identify dynamic fields → 
Map to FermPageData → Connect adapter data → CSS extraction → 
JS extraction/adaptation → Asset extraction → Visual validation → 16-point gate
```

---

## 14. 16-Point Per-Phase Gate

Every phase must pass ALL 16 checks before proceeding:

| # | Check | Pass Criteria |
|---|-------|--------------|
| 1 | Correct route loads | URL resolves to correct page family |
| 2 | Correct template renders | Page family template loaded |
| 3 | Correct Ferm DOM | Frozen source classes, data-*, structure present |
| 4 | Ferm CSS applied | No missing classes, no style breaks |
| 5 | Ferm JS initialized | No console errors, behaviors work |
| 6 | FermPageData correct | Schema matches spec, data populated |
| 7 | 1440px visual | Matches frozen source reference |
| 8 | 390px visual | Matches frozen source reference |
| 9 | No AETHER leakage | No AETHER component classes in DOM |
| 10 | No Shopify dependency | No Shopify API calls, markup, or runtime |
| 11 | No legacy Ferm | No fermliving-legacy assets/scripts |
| 12 | No duplicate libraries | No double-init of shared libraries |
| 13 | Server-rendered HTML | Works before JS enhancement |
| 14 | Fonts/assets load | All resources return 200 |
| 15 | Asset identity correct | Critical assets verified by hash |
| 16 | No unexpected errors | Clean console and network tab |

---

## 15. Release Gate

```
TESTS PASS
  + SCREENSHOTS PASS (all families, all widths)
  + ROUTE PASS (all routes resolve correctly)
  + CONTENT PASS (all dynamic data populated)
  + ASSET PASS (all assets resolve + identity verified)
  + ISOLATION PASS (Ferm ↔ Luxury clean switch)
  + CORE INTEGRITY PASS (AUREON untouched)
= RELEASE
```

**No "green tests, visually wrong" acceptance.**

---

## 16. Risk Register

| Risk | Impact | Likelihood | Mitigation |
|------|--------|------------|------------|
| Wrong asset paths | Visual breakage | Medium | Asset manifest + hash verification |
| Wrong FermPageData schema | JS hydration failure | Medium | Schema validation per page type |
| CSS specificity wars | Layout breakage | Low | Scope to `.design-fermliving`, test isolation |
| Embla vs Swiper conflict | Carousel breakage | Low | Load Embla in isolated scope, test |
| Platform contract JS clash | Animation issues | Low | Test in Phase 1, remove if needed |
| Cart bridge incorrect | Commerce failure | Medium | Test all cart operations in Phase 6 |
| Font licensing block | Cannot ship | Low | Confirm before Phase 0 |
| Missing from crawl (cart page) | Visual gap | Medium | Reconstruct from JS behavior + reference |
| AETHER core change needed | Architecture risk | Low | Core modification protocol (STOP + approve) |
| Performance regression | UX degradation | Medium | Monitor critical resources per phase |

---

## END OF PLAN
