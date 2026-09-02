# Ferm Living WordPress Theme — Full Page Audit Report
**Date:** 2026-09-01  
**Environment:** Docker (WordPress:latest + MySQL 8.0 + phpMyAdmin)  
**Port:** http://localhost:8080  
**Theme:** Aureon v1.2.0 (Ferm Living design pack v5.0.0)  
**Engine:** AETHER complete-page mode

---

## Executive Summary

| Metric | Count |
|--------|-------|
| Pages Tested | 28 |
| HTTP 200 (Pass) | 15 |
| HTTP 404 (Fail) | 13 |
| Pages with Broken Images | 11 |
| Total Broken Images | 55 |
| Console Errors (homepage) | 16 |

### Overall Health: 53% Pass Rate (15/28 pages loading)

---

## SECTION 1: Pages Loading Successfully (15/28)

| # | Page | URL | HTTP | Content Chars | Images | Status |
|---|------|-----|------|--------------|--------|--------|
| 1 | Homepage | `/` | 200 | 2,301 | 0 broken | PASS |
| 2 | Shop | `/shop` | 200 | 7,450 | 0 broken | PASS |
| 3 | Collection: Furniture | `/product-category/furniture` | 200 | 7,450 | 0 broken | PASS |
| 4 | Collection: Lighting | `/product-category/lighting` | 200 | 6,964 | 0 broken | PASS |
| 5 | Collection: Accessories | `/product-category/accessories` | 200 | 7,472 | **3 broken** | PARTIAL |
| 6 | Collection: Kids | `/product-category/kids` | 200 | 7,450 | **3 broken** | PARTIAL |
| 7 | Collection: Kitchen | `/product-category/kitchen` | 200 | 7,450 | **4 broken** | PARTIAL |
| 8 | Collection: Textiles | `/product-category/textiles` | 200 | 7,450 | **2 broken** | PARTIAL |
| 9 | Blog | `/blog` | 200 | 3,199 | **11 broken** | PARTIAL |
| 10 | Contact | `/contact` | 200 | 4,782 | 0 broken | PASS |
| 11 | Store Locator | `/store-locator` | 200 | 1,110 | 0 broken | PASS |
| 12 | Cart | `/cart` | 200 | 991 | 0 broken | PASS |
| 13 | Checkout | `/checkout` | 200 | 991 | 0 broken | PASS |
| 14 | Account Login | `/my-account` | 200 | 1,167 | **1 broken** | PARTIAL |
| 15 | Search | `/?s=test` | 200 | 3,199 | **11 broken** | PARTIAL |

---

## SECTION 2: Pages Failing (13/28) — HTTP 404

### 2A. Missing WooCommerce Product Pages (4 pages)

| # | Page | URL | Root Cause |
|---|------|-----|-----------|
| 1 | Product: Rico Lounge Chair | `/product/rico-lounge-chair-raw-boucle-natural` | No WooCommerce product exists |
| 2 | Product: Meridian Lamp | `/product/meridian-lamp-black` | No WooCommerce product exists |
| 3 | Product: Rico Sofa | `/product/rico-sofa-2-boucle-off-white` | No WooCommerce product exists |
| 4 | Product: Generic Fallback | `/product/generic-test-product` | No WooCommerce product exists |

**Root Cause:** Zero WooCommerce products exist in the database. The `is_product()` check in `ferm-page.php` returns false, causing WordPress to serve a 404. The 404 template falls back to `pages/contact.html` (4,782 chars of content — the contact page frozen HTML, which is the configured 404 fallback).

**Fix Required:** Import/create WooCommerce products via WP Admin or WP-CLI. The frozen product HTML templates exist (`products/*.html`) but have no WordPress products to route to.

### 2B. Missing WooCommerce Category Pages (7 pages)

| # | Page | URL | Root Cause |
|---|------|-----|-----------|
| 5 | Collection: Rugs | `/product-category/rugs` | No "rugs" category in WooCommerce |
| 6 | Collection: Outdoor | `/product-category/outdoor` | Slug mismatch: WP has "outdoor-living", not "outdoor" |
| 7 | Collection: Sofas | `/product-category/sofas` | No "sofas" category in WooCommerce |
| 8 | Collection: New Arrivals | `/product-category/new-arrivals` | No category exists |
| 9 | Collection: Bestsellers | `/product-category/bestsellers` | No category exists |
| 10 | Collection: Certified | `/product-category/certified-products` | No category exists |
| 11 | Collection: Sale | `/product-category/sale` | No category exists |

**WordPress Categories That DO Exist (9 categories, all with 0 products):**
| Category | Slug | ID | Product Count |
|----------|------|----|---------------|
| Accessories | accessories | 18 | 0 |
| Furniture | furniture | 16 | 0 |
| Green Space | green-space | 23 | 0 |
| Kids | kids | 19 | 0 |
| Kitchen | kitchen | 20 | 0 |
| Lighting | lighting | 17 | 0 |
| Outdoor Living | outdoor-living | 21 | 0 |
| Textiles | textites | 22 | 0 |
| Uncategorized | uncategorized | 15 | 0 |

**Slug Mismatch Detail:** The manifest.json maps `"outdoor"` → `collections/furniture.html`, but the WordPress category slug is `outdoor-living`. The `is_tax('product_cat')` check receives `outdoor-living` as the slug, which doesn't match the manifest key `outdoor`.

**Fix Required:** Create missing WooCommerce categories (rugs, sofas, new-arrivals, bestsellers, certified-products, sale) and either rename "outdoor-living" to "outdoor" or update the manifest.json to map `outdoor-living`.

### 2C. Missing WordPress Static Page (1 page)

| # | Page | URL | Root Cause |
|---|------|-----|-----------|
| 12 | About | `/about-ferm-living` | No WordPress page with this slug exists |

**Detail:** Zero WordPress pages exist in the database. The `is_page()` check in `ferm-page.php` returns false. The frozen HTML file `pages/about-ferm-living.html` exists and is valid, but there's no WordPress page entity to trigger the route.

**Fix Required:** Create a WordPress page with slug "about-ferm-living" (and optionally "contact", "store-locator" for consistency, though those appear to work via the front page fallback).

### 2D. 404 Test Page (1 page — expected)

| # | Page | URL | HTTP | Notes |
|---|------|-----|------|-------|
| 13 | 404 Page | `/this-page-does-not-exist-xyz` | 404 | Correct behavior — 404 fallback serves contact.html |

---

## SECTION 3: Broken Image Analysis (55 Total Broken Images)

### 3A. Root Cause: Missing CDN Files

The frozen HTML references images at `cdn/shop/files/...` which get server-side rewritten to the full pack URL. However, many referenced image files do NOT exist in the local CDN directory.

**CDN Directory Status:** 297 files exist in `aureon/frontend/designs/fermliving/cdn/shop/files/`

**Missing Image Categories:**

| Category | Missing Files | Examples |
|----------|--------------|---------|
| Blog thumbnails | ~11 files | `Thumbnail_Julia_Khan-1782983525023.webp`, `Thumbnail_CPH_City_Guide_Kids_2x-1780473770061.webp` |
| Product images | ~5 files | `275894_110143101_1.4a37971fcc.png`, `2300477_110143101_2.d0193556c9.jpg` |
| Account page image | 1 file | `Lounge_chairs_and_poufs.bba1f6fe03.jpg` |
| Collection subcategory images | ~6 files | Various `110427xxxx_*` files |

### 3B. Broken Images by Page

**Accessories Collection (3 broken):**
- `323906_1104270147_10.b72b1ef097.jpg`
- `1104270023_1104270022.629079fa4d.webp`
- `1104272110_1104272111.e2e568b06b.webp`

**Kids Collection (3 broken):**
- `1104273071_2.157380f78d.png`
- `1104273069_10.5bbea35373.jpg`
- `1104273231_1.334092d8f5.png`

**Kitchen Collection (4 broken):**
- `1104273069_2.2de8bcc1a5.png`
- `1104273070_2.2de8bcc1a5.png`
- `1104273187_1.961d48a310.png`
- `1104273231_1.334092d8f5.png`

**Textiles Collection (2 broken):**
- `1104273068_10.fe7ce3ac46.jpg`
- `1104273071_2.157380f78d.png`

**Blog (11 broken):**
- All blog thumbnail images (`Thumbnail_*.webp`, `Thumbnail_*.jpg`, `Spring_Summer_2026_Thumbnail*.jpg`, `Simone_Noa_Thumbnail*.jpg`)

**Account Login (1 broken):**
- `Lounge_chairs_and_poufs.bba1f6fe03.jpg`

**Search Results (11 broken):**
- Same as Blog (search results page reuses blog template)

### 3C. Homepage Console Errors (16 x 404)

The homepage reports16 console404 errors for images at `http://localhost:8080/cdn/shop/files/...`. These are caused by **srcset attribute rewriting gap**:

The server-side regex catches `<img srcset="cdn/...">` but the frozen HTML has srcset patterns where only the FIRST entry is rewritten:
```
srcset=".../cdn/shop/files/Accessories.d00ebe1b2c.webp 200w, cdn/shop/files/Accessories.9cd050f696.webp 400w"
```
The first entry gets the pack URL prefix, but subsequent comma-separated entries remain bare `cdn/...` paths. The client-side JS MutationObserver attempts to fix these but the initial browser request still results in a404.

The images eventually load (Playwright confirms 0 broken `<img>` elements after 3s wait), but the initial404 burst causes layout shifts and slow LCP.

---

## SECTION 4: Page Loading Quality Assessment

### Pages That Load Correctly (Clean)
| Page | Assessment |
|------|-----------|
| Homepage (`/`) | Full content loads, hero banners, category carousel, product grid, rooms section, footer. All functional. |
| Shop (`/shop`) | Full collection page with product grid, filters, pagination. Functional. |
| Furniture (`/product-category/furniture`) | Same frozen template as shop. Functional. |
| Lighting (`/product-category/lighting`) | Lighting-specific content. Functional. |
| Contact (`/contact`) | Full contact page with form. Functional. |
| Store Locator (`/store-locator`) | Store locator page. Functional. |
| Cart (`/cart`) | WooCommerce cart (empty). Functional. |
| Checkout (`/checkout`) | WooCommerce checkout. Functional. |

### Pages That Load With Issues
| Page | Issue |
|------|-------|
| Accessories Collection | 3 broken subcategory images |
| Kids Collection | 3 broken subcategory images |
| Kitchen Collection | 4 broken subcategory images |
| Textiles Collection | 2 broken subcategory images |
| Blog | 11 broken blog thumbnails (all article cards blank) |
| Account Login | 1 broken sidebar image |
| Search Results | 11 broken thumbnails (same as blog) |

### Pages That Do NOT Load (404)
| Page | Why |
|------|-----|
| All Product Pages (4) | No WooCommerce products in database |
| 7 Collection Pages | Missing WooCommerce categories (rugs, outdoor, sofas, new-arrivals, bestsellers, certified-products, sale) |
| About Page | No WordPress page entity exists |

---

## SECTION 5: Required Fixes (Priority Order)

### CRITICAL (Pages are 404)

1. **Create WooCommerce Products** — Import or create products in WP Admin > Products. The frozen HTML templates exist but have no products to route to.

2. **Create Missing WooCommerce Categories** — Add: rugs, sofas, new-arrivals, bestsellers, certified-products, sale. Fix slug mismatch: rename "outdoor-living" → "outdoor" OR update manifest.json.

3. **Create WordPress Pages** — Create a page with slug "about-ferm-living" in WP Admin > Pages.

### HIGH (Broken Images)

4. **Download Missing CDN Images** — ~20 image files referenced in frozen HTML are missing from `cdn/shop/files/`. Download them from the original crawl or Fermliving.com.

5. **Fix srcset Rewriting** — The server-side regex in `ferm-page.php:aureon_ferm_rewrite_paths()` doesn't fully rewrite multi-entry srcset attributes. Fix the regex pattern to handle all comma-separated entries.

### MEDIUM (Quality)

6. **Blog Content** — Only 1 default "Hello world!" post exists. The blog template loads but shows static content from frozen HTML with no real posts.

7. **Product Data Bridge** — The `composer.php` data bridge is in place but needs real WooCommerce product data to populate the frozen templates.

---

## SECTION 6: Screenshots

All page screenshots saved to `test-results/` directory:
- `homepage.png`
- `shop.png`
- `collection-furniture.png`
- `collection-lighting.png`
- `collection-accessories.png`
- `collection-kids.png`
- `collection-kitchen.png`
- `collection-textiles.png`
- `collection-rugs.png`
- `collection-outdoor.png`
- `collection-sofas.png`
- `collection-new-arrivals.png`
- `collection-bestsellers.png`
- `collection-certified.png`
- `collection-sale.png`
- `product-rico-lounge-chair.png`
- `product-meridian-lamp.png`
- `product-rico-sofa.png`
- `product-generic-fallback.png`
- `blog.png`
- `contact.png`
- `about.png`
- `store-locator.png`
- `cart.png`
- `checkout.png`
- `account-login.png`
- `search.png`
- `404-page.png`

Full audit data: `test-results/audit-results.json`

---

*Report generated by automated Playwright audit — 28 pages tested, screenshots captured, broken images catalogued.*
