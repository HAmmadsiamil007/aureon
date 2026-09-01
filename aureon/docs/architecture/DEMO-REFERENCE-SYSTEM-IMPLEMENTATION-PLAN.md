# Ferm Living Demo/Reference System — Implementation Plan

## Status: COMPLETE ✅

---

## Summary

The Ferm Living demo frontend has been enhanced from a **homepage-only demo** to a **complete demo storefront** covering all important page families. The implementation stays entirely within the client pack boundary and does not modify the frozen Golden AUREON Core.

---

## What Changed

### 1. Data Bridge Enhancement (composer.php)

**Before:** `ferm_build_collection_data()` only returned real WC products. No demo fallback for collection/archive pages.

**After:** `ferm_build_collection_data()` now includes:
- `ferm_get_demo_products_for_collection()` — loads demo products from JSON, filters by category slug
- `ferm_resolve_demo_category_image()` — resolves category images from demo-assets.json
- Demo products are returned in the same format expected by the frontend collection bridge

### 2. Manifest Update (manifest.json)

**Before:** Only 3 collection routes mapped (furniture, lighting, accessories).

**After:** All 9 demo categories + 4 demo collections mapped:
- kids, kitchen, textiles, rugs, outdoor, sofas → furniture.html (fallback)
- new-arrivals, bestsellers, certified-products, sale → furniture.html (fallback)
- Shop route added

### 3. Collection Bridge Enhancement (ferm-data-shims.js)

**Before:** Collection bridge replaced hardcoded product thumbs with WC data.

**After:** Collection bridge now:
- Handles both real WC products and demo products
- Updates product count display
- Correctly renders demo product URLs, images, and prices

### 4. Search Bridge Enhancement (search-bridge.js)

**Before:** Search overlay submitted to WordPress `?s=` parameter only.

**After:** Search bridge now:
- Provides instant client-side search for demo products
- Live search with debounced results
- Renders search result cards with product images, titles, and prices
- Falls back to WordPress search when real WC products exist

### 5. Demo Manifest Update (demo-manifest.json)

Updated with:
- Page family coverage list (25 page families)
- Route coverage statistics
- Complete page family enumeration

---

## Files Changed

| File | Change Type | Description |
|------|------------|-------------|
| `composer.php` | Modified | Added demo product fallback in collection data |
| `manifest.json` | Modified | Added shop route + all collection mappings |
| `demo-manifest.json` | Modified | Updated counts and coverage |
| `ferm-data-shims.js` | Modified | Enhanced collection bridge |
| `search-bridge.js` | Modified | Added demo product search |
| `DEMO-REFERENCE-CONTENT-SYSTEM.md` | Created | System documentation |
| `FERM-COMPLETE-PAGE-DEMO-COVERAGE-REPORT.md` | Created | Coverage report |
| `FERM-COMPLETE-PAGE-DEMO-COVERAGE-MATRIX.json` | Created | Acceptance matrix |
| `DEMO-REFERENCE-SYSTEM-IMPLEMENTATION-PLAN.md` | Created | This file |

---

## How It Works

### Demo Product Flow
```
1. User visits /product-category/furniture/
2. ferm-page.php resolves to collections/furniture.html (frozen HTML)
3. Frozen HTML renders with hardcoded product thumbnails
4. FermPageData.collection is injected via wp_head
5. ferm-data-shims.js collection bridge fires:
   a. Finds [data-component="collectionTemplate"]
   b. Gets product grid parent
   c. Clears hardcoded thumbs
   d. Rebuilds with demo products from FermPageData.collection
6. Page displays correctly with demo products + images
```

### Search Flow
```
1. User clicks search button
2. search-bridge.js creates overlay
3. If demoProducts exist (no real WC products):
   a. Form submit prevented
   b. Client-side search filters demo products
   c. Results rendered with images + prices
4. If no demoProducts (real WC products exist):
   a. Form submits to WordPress ?s= parameter
   b. Standard WordPress search results
```

### Real/Demo Transition
```
1. 0 real WC products → demo products visible
2. Admin creates real WC product → ferm_has_real_products() returns true
3. ferm_filter_demo_products() filters aureon_demo=1 from queries
4. All demo products hidden, real products shown
5. Admin deletes all real products → ferm_has_real_products() returns false
6. Demo products visible again
```

---

## Verification Checklist

- [x] Homepage displays complete demo content
- [x] Shop/all-products displays all 66 demo products
- [x] Each category page shows filtered products
- [x] Each collection page shows filtered products
- [x] Product detail pages have gallery + metadata
- [x] Search finds demo products
- [x] Menu links resolve correctly
- [x] Demo→Real transition works
- [x] Real→Demo restoration works
- [x] Customizer fallbacks work
- [x] Image failure fallback works
- [x] No Golden Core modifications
- [x] Pack size remains lightweight
