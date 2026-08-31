# Ferm Living Theme — Change Summary (2026-08-31)

## What Changed Since Last Commit (1b79e71)

### 1. Demo Reference Content System (CORE CHANGE)
**Files:** `demo/demo-*.json` (7 files rebuilt)

- **Products:** 54 → 66 curated products across 9 categories
- **Categories:** 6 → 9 (added Kids, Kitchen, Outdoor Living)
- **Collections:** 5 → 4 (furniture, lighting, accessories, plus rebuilt)
- **Images:** ~54 → 510 verified CDN references
- **CDN domain:** Consolidated from 3 buckets → single canonical `cdn.shopify.com/s/files/1/0150/3336/8640/`
- **Backup:** `demo-backup-2026-08-31/` preserved

### 2. Composer.php Data Bridge (297 lines changed)
**File:** `aureon/frontend/designs/fermliving/composer.php`

Changes:
- JSON loader normalization (handles both wrapped and legacy formats)
- Demo product fallback system via `aether_demo_products` filter
- Demo category fallback via `aether_demo_categories` filter
- Cart AJAX handlers (ferm_cart_add, ferm_cart_update, ferm_cart_get)
- `ferm_build_page_data()` — builds FermPageData object for complete-page templates
- `ferm_build_product_page_data()` — single product page data (simple + variable products)
- `ferm_build_collection_data()` — collection/archive page data
- Navigation menu mapping (WP nav → Ferm format)
- Product remapping helpers (ferm_remap_product, ferm_format_swatches)
- Color swatch generation from WC product attributes
- Collection/archive data injection via `wp_head`

### 3. Customizer Bridge (113 lines changed)
**File:** `aureon/frontend/designs/fermliving/cdn/shop/t/164/assets/customizer-bridge.js`

Changes:
- Logo replacement from Customizer
- Announcement bar item injection
- Newsletter heading/subtitle update
- Social links mapping
- Footer column content update
- Hero slide fallback from Customizer
- Site heading update
- Color tokens → CSS custom properties injection
- Font tokens → CSS custom properties injection
- **NEW:** Remote demo image runtime fallback (MutationObserver + error handler)
- **NEW:** Demo placeholder SVG for failed image loads

### 4. Ferm Page Template (14 lines changed)
**File:** `aureon/ferm-page.php`

Changes:
- Server-side path rewriting for frozen HTML (cdn/ → absolute URLs)
- Client-side MutationObserver for dynamically created images
- Shopify → WordPress link rewriting (collections, products, account, blogs, pages)
- Body attribute extraction and re-rendering
- Fallback to homepage when page not found

### 5. Documentation Cleanup (46 files deleted, 10+ new)
**Deleted:** All PHASE*.md, FRONTEND_*.md, old forensics, frontend-platform docs, superpowers plans, screenshots
**Added:** New forensics reports (7 files), demo system architecture, directory structure, runtime testing checklist

### 6. Documentation QA Clarification (NEW)
**Files:** `docs/forensics/FERM-RUNTIME-ACCEPTANCE-CLARIFICATION.md` (created)
**Updated:** `FERM-DEMO-THEME-FINAL-ACCEPTANCE-REPORT.md`, `FERM-RUNTIME-TESTING-CHECKLIST.md`

Changes:
- Added exact evidence for individual logo/hero/heading transitions (3/3 each)
- Documented combined states A–F (6a/6b/6f directly exercised, 6c/6d/6e verified by resolver)
- Clarified checkout 302 as expected WC behavior
- Added exact cache transition sequences
- Distinguished discovered vs curated dataset (2,609 vs 66)
- Updated test scope labels for FORCE_DEMO/DISABLED modes
- Added remote image failure test scope
- Added menu regression verification
- Established report location consistency

## Golden Core Status
**ZERO modifications to:**
- `frontend/views/*` (engine kernel)
- `frontend/adapters/*` (WP/WC integration layer)
- `frontend/components/*` (component templates)
- `frontend/sections/*` (section templates)
- `frontend/tokens/*` (design tokens)
- Theme PHP files (except `front-page.php` — already tracked)

## Runtime Architecture
```
WordPress Request
  ↓
aureon/ferm-page.php (complete-page template)
  ↓
aether_is_complete_page_design() → true (manifest flag)
  ↓
aureon_ferm_resolve_page() → maps route to frozen HTML file
  ↓
file_get_contents() → loads frozen HTML
  ↓
aureon_ferm_extract_body() → extracts <body> content
  ↓
aureon_ferm_rewrite_paths() → CDN paths → absolute URLs
  ↓
wp_head() → enqueues pack CSS/JS from manifest
  ↓
echo body content
  ↓
Inline script: client-side link rewriting
  ↓
wp_footer() → WC cart fragments, admin bar
```

## Data Flow
```
WooCommerce Products
  ↓
aether_adapter_wc_products() → normalized data
  ↓
ferm_wc_products_data() filter → remapped to Ferm format
  ↓
FermPageData.cart / FermPageData.product / FermPageData.collection
  ↓
ferm-data-shims.js → merges into Ferm globals
  ↓
app.js (frozen) reads FermCart/FermProducts/FermCollections
  ↓
DOM updates via Product DOM Bridge / Collection Bridge / Variant Bridge
```

## Verified Working
- Homepage: 66 demo products visible, cart count sync
- Product pages: Real WC data injected into frozen DOM
- Variant selection: Color swatches update price/SKU/image
- Collection pages: Real WC products replace hardcoded thumbs
- Cart: Add/update/get via WordPress AJAX
- Demo↔Real: Automatic transition when WC products exist
- Responsive: 1440px, 1024px, 768px, 390px verified
- Console: Zero JS errors
- Security: Nonce verification, CSP headers active

## Current Classification
```
FERM DEMO DATASET                  ✅ COMPLETE
FERM DEMO/REAL SYSTEM              ✅ COMPLETE
FERM RUNTIME TRANSITIONS           ✅ 17/17 (AUTO mode)
CUSTOMIZER FALLBACK                ✅
MENU INTEGRATION                   ✅
SEARCH                             ✅
CART/ACCOUNT/CHECKOUT              ✅
LIGHTWEIGHT PACK                   ✅
GOLDEN CORE                        🔒 PROTECTED

DOCUMENTATION QA                   ✅ CLARIFIED
INFINITYFREE                       ⏳ NOT YET PROVEN
```

## Remaining Path to Final Release
```
17/17 runtime pass (DONE)
        ↓
documentation clarification (DONE)
        ↓
InfinityFree deployment
        ↓
remote-image/runtime-hosting proof
        ↓
FINAL RELEASE

FERM_DEMO_THEME_RUNTIME_PASS
+
FERM_INFINITYFREE_HOSTING_PASS
=
FERM_DEMO_PACK_RELEASE_READY
```
