# Ferm Product Routing Fix — Status (2026-08-31)

## COMPLETED ✅

### Root Cause Identified
- `/product/pear-braided-storage` → WordPress 404 → `404.php` → Contact page
- `is_product()` returns false for products not in WooCommerce
- `aureon_ferm_resolve_page()` 404 handler returned `pages/contact.html`
- Only 3 frozen product templates exist; 66 demo products have no matching HTML

### Files Modified

#### 1. `aureon/ferm-page.php` — Resolver Fix
- Added `product_generic` fallback in manifest path (lines 205-208)
- Added `_generic-product.html` fallback in hardcoded path (lines 285-289)
- Added product URL detection in 404 handler (lines 344-353)
- Pattern: `#/product/([^/]+)/?$#` matches `/product/[slug]`

#### 2. `aureon/frontend/designs/fermliving/manifest.json` — Generic Template Entry
- Added `"product_generic": "products/_generic-product.html"` (line 48)

#### 3. `aureon/frontend/designs/fermliving/products/_generic-product.html` — Generic Template
- Copied from `meridian-lamp-black.html` (304,459 bytes)
- Serves as presentation shell for ANY product URL
- JS bridge (`ferm-data-shims.js`) injects real data via `FermPageData.product`

#### 4. `aureon/frontend/designs/fermliving/composer.php` — Demo Product Support
- Added `ferm_handle_missing_product()` (line 1287) — runs on `wp` hook priority 1
- Added `ferm_find_demo_product_by_slug()` (line 1326) — indexes demo-products.json by slug
- Added `ferm_build_demo_product_data()` (line 1357) — builds FermPageData.product from demo data
- Updated template detection (line 791) — sets template to 'product' for product-like 404 URLs
- Product data injection at line 840 already uses `$GLOBALS['ferm_product_page_data']`

#### 5. `aureon/theme/404.php` — 404 Template Intercept
- Added product URL pattern detection (lines 14-23)
- When product-like URL detected + complete-page design active → loads `ferm-page.php`
- Falls through to standard AETHER 404 for non-product URLs

### Copied to Deploy Packages
- `AUREON-GOLDEN-COPY/aureon/` — all 5 files updated
- `AUREON-WORDPRESS-DEPLOY/aureon/` — all 5 files updated

### Routing Flow (After Fix)
```
/product/pear-braided-storage
    ↓
WordPress: product not found → is_404() = true
    ↓
404.php: detects /product/ pattern → require_once ferm-page.php
    ↓
ferm-page.php: aureon_ferm_resolve_page()
    ↓
is_404() + product URL pattern → returns 'products/_generic-product.html'
    ↓
Generic product HTML served
    ↓
composer.php: ferm_handle_missing_product()
    ↓
ferm_find_demo_product_by_slug('pear-braided-storage')
    ↓
If found in demo-products.json → FermPageData.product = demo data
If not found → FermPageData.product = empty (JS bridge handles gracefully)
    ↓
ferm-data-shims.js: updates frozen DOM with FermPageData.product
```

## REMAINING ❌

### Phase 5: Test Regression (TODO)
Test the following routes on http://localhost:8080:

1. `/` — Homepage
2. `/shop` — Shop/archive
3. `/product-category/furniture/` — Category
4. `/product/meridian-lamp-black/` — Exact frozen product (should use exact template)
5. `/product/rico-lounge-chair-raw-boucle-natural/` — Exact frozen product
6. `/product/pear-braided-storage/` — Non-existent product (should use generic template)
7. `/product/boda-dining-chair-red-brown/` — Demo product (should use generic + demo data)
8. `/cart/` — Cart
9. `/checkout/` — Checkout
10. `/my-account/` — Account
11. `/?s=lamp` — Search

Verify:
- Product pages show product layout (NOT Contact)
- FermPageData.product populated correctly
- Console: 0 errors
- Network: 0 unwanted requests

### Phase 6: Documentation (TODO)
Create `docs/forensics/FERM-PRODUCT-ROUTING-FALLBACK-REPORT.md` with:
- Root cause analysis
- Old vs new routing behavior
- Fallback rules by route type
- Generic product template strategy
- Demo vs real product behavior
- Test results
- Responsive results (1440/1024/768/390)

### Git Commit (TODO)
```
git add -A
git commit -m "fix: product routing fallback — generic template for unmatched products

- Add _generic-product.html template (copied from meridian-lamp-black)
- Fix resolver: product URLs use generic template, not Contact page
- Add demo product data builder for FermPageData injection
- Fix 404.php: intercept product-like URLs and load ferm-page.php
- Manifest: add product_generic entry for fallback resolution
- All products now render correct Ferm product layout regardless of template match"
git push origin master
```

## KEY ARCHITECTURE DECISION
- Do NOT create 66+ HTML files per product
- ONE generic frozen product presentation + DYNAMIC FermPageData.product = ANY product page
- Same architecture proven for product #834 and variable #828

## GOLDEN CORE STATUS
- `frontend/views/*` — FROZEN/UNTOUCHED ✅
- `frontend/adapters/*` — FROZEN/UNTOUCHED ✅
- `frontend/components/*` — FROZEN/UNTOUCHED ✅
- `frontend/sections/*` — FROZEN/UNTOUCHED ✅
- Changes ONLY in client pack: `aureon/ferm-page.php`, `aureon/theme/404.php`, `aureon/frontend/designs/fermliving/`
