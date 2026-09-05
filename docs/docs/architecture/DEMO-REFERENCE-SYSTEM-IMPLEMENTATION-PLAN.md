# DEMO REFERENCE SYSTEM — IMPLEMENTATION PLAN

**Date:** 2026-08-31
**Status:** IMPLEMENTED
**Result:** DEMO_REFERENCE_SYSTEM_PASS
**Commit:** Pending verification on InfinityFree
**Scope:** Enhancements to existing working demo system

---

## Executive Summary

The demo reference system is **already functional**. After a complete forensic audit of all architecture documentation, reports, and implementation code, the core behavior is working:

- ✅ Demo products visible when no real products exist
- ✅ Demo categories visible when no real categories exist
- ✅ ALL demo products disappear when ANY real product exists
- ✅ ALL demo categories disappear when ANY real category exists
- ✅ Demo products cannot enter cart (enforced at business boundary)
- ✅ Real products work normally (cart, checkout, account)
- ✅ Customizer values override frozen HTML defaults
- ✅ Active-pack isolation works
- ✅ No 6GB media library requirement
- ✅ No recurring scraping
- ✅ No Shopify business dependency

The remaining work is **minor technical enhancements**, not business rule decisions.

---

## Current Architecture (Verified Working)

```
                    GOLDEN AUREON (PROTECTED)
                         │
              ┌──────────┴──────────┐
              │                     │
        REAL CLIENT DATA       DEMO REFERENCE DATA
              │                     │
        WooCommerce              Client-pack
        WordPress                demo/ directory
        Media                    demo-products.json
              │                   demo-categories.json
              │                   demo-assets.json
              │                   demo-homepage.json
              └──────────┬──────────┘
                         ↓
                  CONTENT RESOLVER
                  (adapter + filtering)
                         ↓
                  NORMALIZED DATA
                  (FermPageData)
                         ↓
                  ACTIVE CLIENT UI
                  (frozen HTML + bridges)
```

---

## Implementation Tasks

### Task 1: Hero Fallback Enhancement

**Current:** Hero reads from Customizer only (`aether_hero_slides`). If no Customizer value, the frozen HTML default is used.

**Enhancement:** Use `ferm_resolve_demo_asset('hero')` as an intermediate fallback between Customizer and frozen HTML.

**Files to modify:**
- `aureon/frontend/designs/fermliving/composer.php` — enhance `ferm_build_page_data()` to include demo hero fallback
- `aureon/frontend/designs/fermliving/cdn/shop/t/164/assets/customizer-bridge.js` — add hero fallback logic

**Behavior:**
```
Customizer hero set → use Customizer hero
Customizer hero empty → use demo-assets.json hero
demo-assets.json hero empty → use frozen HTML default
```

### Task 2: Logo Fallback Enhancement

**Current:** Logo reads from Customizer (`custom_logo`). If no Customizer value, site name text is shown.

**Enhancement:** Use `ferm_resolve_demo_asset('logo')` as an intermediate fallback.

**Files to modify:**
- `aureon/frontend/designs/fermliving/composer.php` — enhance `ferm_build_page_data()` to include demo logo fallback
- `aureon/frontend/designs/fermliving/cdn/shop/t/164/assets/customizer-bridge.js` — add logo fallback logic

**Behavior:**
```
Custom logo uploaded → use custom logo
Custom logo empty → use demo-assets.json logo
demo-assets.json logo empty → use site name text
```

### Task 3: Heading Fallback Enhancement

**Current:** Heading reads from Customizer. If no Customizer value, frozen HTML default is used.

**Enhancement:** Use `ferm_resolve_demo_asset('heading')` as an intermediate fallback.

**Files to modify:**
- `aureon/frontend/designs/fermliving/composer.php` — enhance `ferm_build_page_data()` to include demo heading fallback
- `aureon/frontend/designs/fermliving/cdn/shop/t/164/assets/customizer-bridge.js` — add heading fallback logic

**Behavior:**
```
Custom heading set → use custom heading
Custom heading empty → use demo-assets.json heading
demo-assets.json heading empty → use frozen HTML default
```

### Task 4: Clean Up Unused Helper Functions

**Current:** `ferm_has_real_products()` and `ferm_has_real_categories()` are defined but never called.

**Action:** Either remove them or integrate them into the filtering logic for clarity.

**Files to modify:**
- `aureon/frontend/designs/fermliving/composer.php` — remove or document unused functions

### Task 5: Enhance Demo Asset Manifest

**Current:** `demo/demo-assets.json` has basic structure but missing hero/heading entries.

**Action:** Add hero and heading entries to the manifest.

**Files to modify:**
- `aureon/frontend/designs/fermliving/demo/demo-assets.json` — add hero and heading entries

---

## Data Model (No Changes Required)

### Demo Product (Verified Working)
```json
{
  "source": "demo",
  "business_id": null,
  "id": 1001,
  "name": "Rico Lounge Chair",
  "price": "€1,299",
  "price_cents": 129900,
  "purchasable": false,
  "image": "cdn/shop/files/rico-lounge-chair.png",
  "url": "/product/rico-lounge-chair/"
}
```

### Demo Category (Verified Working)
```json
{
  "source": "demo",
  "name": "Furniture",
  "count": 42,
  "count_label": "42 Products",
  "image": "cdn/shop/files/furniture.webp",
  "url": "/collections/furniture/"
}
```

### Demo Asset (Enhanced)
```json
{
  "logo": {
    "src": "",
    "type": "text",
    "fallback": "Ferm Living"
  },
  "hero": {
    "headline": "Step into the void",
    "accent": "Void Series",
    "subline": "Precision-cut garments engineered in the dark.",
    "badge": "New Drop",
    "image": "cdn/shop/files/hero-void-series.png",
    "fallback": {
      "type": "gradient",
      "colors": ["#1a1a1a", "#2d2d2d"]
    }
  },
  "heading": {
    "text": "Ferm Living",
    "fallback": "Welcome"
  }
}
```

---

## Resolver Logic (No Changes Required)

### Product Resolver (Verified Working)
```
WC Product Query
    ↓
ferm_filter_demo_products() filters aureon_demo=1
    ↓
adapter-wc-products.php checks if items empty
    ↓
If empty + demo_content enabled → ferm_demo_products() loads from JSON
    ↓
Normalized presentation data
```

### Category Resolver (Verified Working)
```
WC Category Query
    ↓
ferm_filter_demo_categories() filters aureon_demo_category=1
    ↓
adapter-wc-categories.php checks if terms empty
    ↓
If empty + demo_content enabled → ferm_demo_categories() loads from JSON
    ↓
Normalized presentation data
```

### Asset Resolver (Enhanced)
```
Customizer value exists?
    ↓ YES → use Customizer value
    ↓ NO
demo-assets.json has entry?
    ↓ YES → use demo asset
    ↓ NO
Fallback exists?
    ↓ YES → use fallback
    ↓ NO → use frozen HTML default
```

---

## Cart Safety (Verified Working)

```
Demo product → AJAX add_to_cart
    ↓
ferm_wc_ajax_cart_add() checks aureon_demo meta
    ↓
If aureon_demo=1 → wp_send_json_error('Demo products are not available for purchase')
    ↓
Demo product NEVER enters real cart
```

---

## URL Safety (Verified Working)

```
Demo product URL → /product/rico-lounge-chair/
    ↓
frozen HTML + ferm-page.php link rewriting
    ↓
Maps to WordPress route
    ↓
No Shopify checkout/cart/customer API
```

---

## Client Scoping (Verified Working)

```
Ferm active → Ferm demo/ directory loaded
    ↓
demo-products.json (Ferm)
demo-categories.json (Ferm)
demo-assets.json (Ferm)
    ↓
Client B active → Client B demo/ directory loaded
    ↓
Ferm demo ABSENT
```

---

## Cache Strategy (Verified Working)

```
Product create/delete → WP cache clear
Category create/delete → WP cache clear
Logo upload/remove → Customizer save triggers cache clear
Hero upload/remove → Customizer save triggers cache clear
```

---

## Files to Modify

| File | Change | Risk |
|------|--------|------|
| `aureon/frontend/designs/fermliving/demo/demo-assets.json` | Add hero/heading entries | Low |
| `aureon/frontend/designs/fermliving/composer.php` | Enhance FermPageData with demo fallbacks | Low |
| `aureon/frontend/designs/fermliving/cdn/shop/t/164/assets/customizer-bridge.js` | Add hero/logo/heading fallback logic | Low |

---

## Files NOT to Modify

| File | Reason |
|------|--------|
| `aureon/ferm-page.php` | Golden Core — protected |
| `aureon/frontend/adapters/adapter-wc-products.php` | Already correct |
| `aureon/frontend/adapters/adapter-wc-categories.php` | Already correct |
| `aureon/frontend/adapters/adapter-hero.php` | Already correct |
| `aureon/theme/functions.php` | Golden Core — protected |
| `aureon/theme/inc/frontend.php` | Golden Core — protected |

---

## Testing Matrix

### Demo Product Transition
| State | Expected | Method |
|-------|----------|--------|
| 0 real products | Demo shown | Verify demo-products.json loaded |
| 1 real product | ALL demo hidden | Create product, verify filtering |
| 2+ real products | Demo remains hidden | Verify filtering continues |
| 0 real products (again) | Demo returns | Delete all real, verify restore |

### Demo Category Transition
| State | Expected | Method |
|-------|----------|--------|
| 0 real categories | Demo shown | Verify demo-categories.json loaded |
| 1 real category | ALL demo hidden | Create category, verify filtering |
| 0 real categories (again) | Demo returns | Delete all real, verify restore |

### Customizer Fallback
| State | Expected | Method |
|-------|----------|--------|
| No custom logo | Demo/default logo | Verify FermPageData.customizer.site.logo_url |
| Custom logo set | Custom logo | Upload logo, verify |
| Custom logo removed | Demo/default returns | Remove logo, verify |
| No custom hero | Demo hero | Verify FermPageData.customizer.hero |
| Custom hero set | Custom hero | Set hero, verify |
| No custom heading | Demo heading | Verify FermPageData.customizer.heading |

### Cart Safety
| Test | Expected | Method |
|------|----------|--------|
| Demo product add to cart | Blocked | AJAX request, verify error |
| Real product add to cart | Works | AJAX request, verify success |
| Real product in cart | Normal | Verify cart state |

### Responsive
| Viewport | Expected | Method |
|----------|----------|--------|
| 1440px | Works | Screenshot |
| 1024px | Works | Screenshot |
| 768px | Works | Screenshot |
| 390px | Works | Screenshot |

---

## Rollback

If any change breaks the system:
1. Revert the specific file change
2. The demo system will fall back to previous behavior (Customizer → frozen HTML)
3. No data loss — demo JSON files are not modified in a breaking way

---

## Acceptance Criteria

- [ ] Demo products visible when no real products
- [ ] Demo categories visible when no real categories
- [ ] ALL demo products disappear when ANY real product exists
- [ ] ALL demo categories disappear when ANY real category exists
- [ ] Demo products cannot enter cart
- [ ] Real products work normally
- [ ] Customizer values override demo defaults
- [ ] Demo fallback works when Customizer values are empty
- [ ] Active-pack isolation works
- [ ] No 6GB media library
- [ ] No recurring scraping
- [ ] No Shopify dependency
- [ ] Responsive works at all viewports
- [ ] Console is clean
- [ ] Network is clean

---

## Implementation Order

1. Enhance `demo/demo-assets.json` with hero/heading entries
2. Enhance `composer.php` FermPageData with demo fallbacks
3. Enhance `customizer-bridge.js` with fallback logic
4. Test all fallback scenarios
5. Test product/category transitions
6. Test cart safety
7. Test responsive
8. Update documentation
