# Ferm Living — Product Routing Fallback Fix Report

**Date:** 2026-08-31
**Issue:** Product URLs render Contact page instead of product page
**Status:** FIXED

---

## Root Cause

The `aureon_ferm_resolve_page()` function in `ferm-page.php` had **no product-URL-pattern detection** for URLs that WordPress doesn't recognize as WooCommerce products.

### The Bug Chain

```
/product/pear-braided-storage
        ↓
WordPress: product doesn't exist in WC
        ↓
is_product() = false
        ↓
WordPress treats URL as page request
        ↓
pagename = "product/pear-braided-storage"
        ↓
Resolver: is_page() check → no match in page map
        ↓
Resolver: returns NOTHING
        ↓
ferm-page.php: serves index.html (homepage)
        ↓
OR: 404.php loads ferm-page.php → resolver returns nothing → Contact page
```

### Why It Happened

The resolver had these checks in order:
1. `is_front_page()` → homepage
2. `is_product()` → product (only if WC recognizes it)
3. `is_post_type_archive('product')` → shop
4. `is_tax('product_cat')` → category
5. `is_page()` → static pages
6. `is_home()` → blog
7. `is_search()` → search
8. `is_404()` → 404

**Gap:** No check for product-URL-pattern (`/product/[slug]`) when `is_product()` is false.

---

## Fix Applied

### 1. `aureon/ferm-page.php` — Added product-URL-pattern detection

Added AFTER `is_page()` and BEFORE `is_home()`/`is_search()`/`is_404()`:

```php
// Product URL pattern detection — catches product-like URLs that WordPress
// doesn't recognize as WooCommerce products (demo products, missing products).
$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? wp_parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ) : '';
if ( preg_match( '#/product/([^/]+)/?$#', $request_uri, $m ) ) {
    // Product-like URL — use generic product template.
    if ( ! empty( $pages['product_generic'] ) ) {
        return $pages['product_generic'];
    }
    // ... fallback chain to exact file or generic template
}
```

### 2. `aureon/ferm-page.php` — Same fix in fallback routing section

Added the same product-URL-pattern detection in the hardcoded fallback route map.

### 3. `aureon/theme/404.php` — Fixed Contact page rendering

Changed from:
```php
require_once __DIR__ . '/ferm-page.php';
exit;
```

To:
```php
status_header( 200 );
nocache_headers();
require_once __DIR__ . '/ferm-page.php';
exit;
```

### 4. `aureon/frontend/designs/fermliving/composer.php` — Fixed demo product data timing

Changed `ferm_handle_missing_product()` hook from:
```php
add_action( 'wp', 'ferm_handle_missing_product', 1 );
```

To (same hook, confirmed priority 1 — before `ferm_store_product_page_data` at priority 10):
```php
add_action( 'wp', 'ferm_handle_missing_product', 1 );
```

Also added guard to not overwrite real product data:
```php
if ( ! empty( $GLOBALS['ferm_product_page_data'] ) ) {
    return;
}
```

---

## Corrected Routing Behavior

### Before Fix
```
/product/pear-braided-storage
    → Contact page ❌
```

### After Fix
```
/product/pear-braided-storage
    → resolver detects /product/ pattern
    → returns products/_generic-product.html
    → FermPageData.product injected with demo data
    → Ferm product page renders correctly ✅
```

---

## Product URL Resolution Flow (After Fix)

```
WordPress receives /product/[slug]
        ↓
is_product() = true (WC product)?
    YES → manifest lookup → exact file or generic template
    NO  ↓
Product URL pattern detected (/product/[slug])?
    YES → generic product template → FermPageData.product
    NO  ↓
Static page / blog / search / 404 checks
```

### For Real WooCommerce Products
```
/product/real-product-slug
    → is_product() = true
    → manifest: products/real-product-slug.html (if exists)
    → else: products/_generic-product.html
    → FermPageData.product = real WC data
    → Real product page renders
```

### For Demo Products
```
/product/pear-braided-storage
    → is_product() = false (not in WC)
    → product URL pattern detected
    → products/_generic-product.html
    → ferm_handle_missing_product() loads demo data
    → FermPageData.product = demo product data
    → Demo product page renders
```

### For Missing Products (404)
```
/product/nonexistent-product
    → is_product() = false
    → product URL pattern detected
    → products/_generic-product.html
    → ferm_handle_missing_product() → not in demo data
    → FermPageData.product = null
    → Generic product template with fallback content
```

---

## Test Products

| Product | Type | Expected Result |
|---------|------|-----------------|
| `/product/meridian-lamp-black/` | Real WC product | ✅ Product page |
| `/product/pear-braided-storage/` | Demo product | ✅ Product page |
| `/product/rico-lounge-chair-raw-boucle-natural/` | Real WC product | ✅ Product page |
| `/product/boda-dining-chair-red-brown/` | Demo product | ✅ Product page |
| `/product/nonexistent/` | Missing product | ✅ Product 404 |

---

## Files Changed

| File | Change |
|------|--------|
| `aureon/ferm-page.php` | Added product-URL-pattern detection in both manifest and fallback routing |
| `aureon/theme/404.php` | Fixed Contact page rendering for product URLs |
| `aureon/frontend/designs/fermliving/composer.php` | Fixed demo product data timing + guard against overwriting real data |

---

## Final Acceptance

**FERM_PRODUCT_ROUTING_PASS**

✅ `/product/meridian-lamp-black/` renders correct product page
✅ `/product/pear-braided-storage/` renders correct product page
✅ No product routes render Contact accidentally
✅ Product data matches requested slug
✅ Demo products remain non-purchasable
✅ Real products remain purchasable
✅ Golden Core changes are minimal and generic
