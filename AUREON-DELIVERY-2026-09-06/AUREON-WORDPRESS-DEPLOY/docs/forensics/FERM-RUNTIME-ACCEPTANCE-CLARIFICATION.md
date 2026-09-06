# FERM LIVING — RUNTIME ACCEPTANCE CLARIFICATION

**Date:** 2026-08-31  
**Purpose:** Corrective QA document clarifying evidence granularity, test scope, and documentation consistency for the 17/17 runtime result  
**Reference:** `FERM-DEMO-THEME-FINAL-ACCEPTANCE-REPORT.md`, `FERM-RUNTIME-TESTING-CHECKLIST.md`  
**Status:** FERM_DEMO_REAL_TRANSITION_RUNTIME_PASS (17/17 AUTO mode)

---

## 1. Exact 17/17 Test Evidence

### Test Inventory

| Test | Description | Result | Evidence |
|------|-------------|--------|----------|
| TEST 1 | Real Product Transition | ✅ PASS | Create/delete real product; demo hidden/restored |
| TEST 2 | Real Category Transition | ✅ PASS | Create/delete real category; demo hidden/restored |
| TEST 3 | Logo Transition | ✅ PASS | Customizer upload/remove; demo text fallback |
| TEST 4 | Hero Transition | ✅ PASS | Customizer upload/remove; demo hero fallback |
| TEST 5 | Heading Transition | ✅ PASS | Customizer set/clear; demo heading fallback |
| TEST 6 | Combined States | ✅ PASS | 6a, 6b, 6f directly exercised |
| TEST 7 | Cache Transition | ✅ PASS | Application-level demo↔real on reload |
| TEST 8 | Route Matrix | ✅ PASS | All routes return expected HTTP status |
| TEST 9 | Search Transition | ✅ PASS | Demo excluded when real products exist |

### Total: 17/17 PASS (AUTO mode)

---

## 2. Individual Logo/Hero/Heading Transitions

### Logo

```
✅ no custom logo → demo
   Action: Ensure no logo set in Customizer
   Result: Demo text "Ferm Living" displayed in header
   Evidence: Header element contains demo text fallback

✅ custom logo → client
   Action: Upload logo via Customizer → Publish
   Result: Client logo image replaces demo text
   Evidence: Header element contains uploaded image

✅ remove custom logo → demo
   Action: Remove logo via Customizer → Publish
   Result: Demo text "Ferm Living" returns
   Evidence: Header element reverts to demo text fallback
```

### Hero

```
✅ no custom hero → demo
   Action: Ensure no hero set in Customizer
   Result: Demo hero image from demo-assets.json displayed
   Evidence: Homepage hero section contains demo image

✅ custom hero → client
   Action: Upload hero image via Customizer → Publish
   Result: Client hero image replaces demo
   Evidence: Homepage hero section contains uploaded image

✅ remove custom hero → demo
   Action: Remove hero via Customizer → Publish
   Result: Demo hero image returns
   Evidence: Homepage hero section reverts to demo image
```

### Heading

```
✅ no custom heading → demo
   Action: Ensure no custom heading set in Customizer
   Result: "Ferm Living" from demo displayed
   Evidence: Site heading contains demo text

✅ custom heading → client
   Action: Set custom heading via Customizer → Publish
   Result: Client heading displayed
   Evidence: Site heading contains custom text

✅ remove custom heading → demo
   Action: Clear custom heading via Customizer → Publish
   Result: "Ferm Living" demo heading returns
   Evidence: Site heading reverts to demo text
```

---

## 3. Combined-State Testing (States A–F)

### Directly Exercised

```
6a — Clean full demo
  Setup: 0 products, 0 categories, no logo, no hero, no custom heading
  Result: Full demo visible (66 products, 9 categories, demo hero/logo/heading)
  Status: ✅ PASS — directly exercised

6b — Product only
  Setup: 1 real product, 0 categories, no customizer changes
  Result: Real product visible, demo categories visible, demo presentation visible
  Status: ✅ PASS — directly exercised

6f — Full restoration
  Setup: Delete product, delete category, remove logo, remove hero, remove heading
  Result: Full demo returns automatically
  Status: ✅ PASS — directly exercised
```

### Verified by Resolver/Combined-State Evidence

```
6c — Category only
  Setup: 0 products, 1 real category, no customizer changes
  Expected: Demo products visible, real category visible, demo presentation visible
  Basis: Category resolver logic identical to product resolver; demo product
         visibility independent of category state
  Status: ⚠️ verified by resolver evidence — not directly exercised

6d — Product + Category
  Setup: 1 real product, 1 real category, no customizer changes
  Expected: Real products + real categories, 0 demo
  Basis: Product and category resolver logic both trigger demo hiding;
         combined state is intersection of individual rules
  Status: ⚠️ verified by resolver evidence — not directly exercised

6e — Full client state
  Setup: 1 real product, 1 real category, custom logo, custom hero, custom heading
  Expected: Fully client-controlled, 0 demo
  Basis: All demo hiding rules active simultaneously;
         resolver evidence confirms each independent rule
  Status: ⚠️ verified by resolver evidence — not directly exercised
```

---

## 4. Checkout 302 — Expected Behavior

```
/checkout/
→ 302 under empty-cart condition
→ expected WooCommerce behavior
→ final destination verified

Explanation:
  WooCommerce redirects /checkout/ to /cart/ when cart is empty.
  This is native WC behavior, not a bug or routing failure.

  When cart contains items:
  → /checkout/ renders WC checkout form
  → "Test Product Alpha" visible in order summary
  → WC native templates used

Route test result: ✅ PASS (302 is expected redirect, not failure)
```

---

## 5. Demo Mode Scope

```
AUTO            ✅ runtime tested (17/17)
                Behavior: 0 real → demo visible; 1+ real → demo hidden
                Tested in: TEST 1, 2, 6, 7, 9

FORCE_DEMO      ⚠️ documented / not part of 17 tests
                Behavior: Demo always visible regardless of real content
                Code location: composer.php → ferm_get_demo_mode()
                Runtime test: not exercised

DISABLED        ⚠️ documented / not part of 17 tests
                Behavior: Demo never visible regardless of content
                Code location: composer.php → ferm_get_demo_mode()
                Runtime test: not exercised
```

---

## 6. Remote Image Failure Test Scope

```
REMOTE_DEMO_FAILURE_RECOVERY = ⚠️ fallback code implemented, not deliberately broken

Implementation verified:
  → customizer-bridge.js: MutationObserver watches for image load failures
  → Demo placeholder SVG (gradient) applied on error
  → No fatal console error on failure

Not exercised:
  → No test broke a remote image URL intentionally
  → No test confirmed fallback rendering visually

Recommended future test:
  → Take one demo image URL
  → Temporarily make it invalid
  → Reload
  → Expected:
      - fallback image/background displayed
      - no broken-image layout
      - no fatal console error
  → Test for: hero image, product image, category image
```

---

## 7. Cache Transition Evidence

### Application-Level Cache

```
demo → create real product → reload → demo hidden
  Start: 0 products (demo visible)
  Action: Create 1 real WooCommerce product
  Reload: Hard refresh (Ctrl+Shift+R)
  Result: ALL 66 demo products disappear; real product visible
  Status: ✅ PASS

real product → delete last real product → reload → demo restored
  Start: 1 product (real visible)
  Action: Delete last real product
  Reload: Hard refresh (Ctrl+Shift+R)
  Result: Real product gone; ALL 66 demo products return
  Status: ✅ PASS
```

### Application-Level Cache (Categories)

```
demo categories → create real category → reload → demo categories hidden
  Start: 0 categories (demo visible)
  Action: Create 1 real WC category
  Reload: Hard refresh
  Result: ALL 9 demo categories disappear; real category visible
  Status: ✅ PASS

real category → delete last real category → reload → demo categories restored
  Start: 1 category (real visible)
  Action: Delete last real category
  Reload: Hard refresh
  Result: Real category gone; ALL 9 demo categories return
  Status: ✅ PASS
```

### Application-Level Cache (Customizer)

```
demo logo → upload custom logo → reload → custom logo visible
  Start: No logo set
  Action: Upload logo via Customizer → Publish
  Reload: Hard refresh
  Result: Client logo displayed
  Status: ✅ PASS

custom logo → remove logo → reload → demo text returns
  Start: Custom logo set
  Action: Remove logo via Customizer → Publish
  Reload: Hard refresh
  Result: Demo text "Ferm Living" returns
  Status: ✅ PASS
```

### Browser/Hosting Cache

```
⚠️ separate test required (InfinityFree)
→ Application-level cache verified above
→ Browser-level cache is hosting-dependent
→ Will be tested during InfinityFree deployment
```

---

## 8. Known Limitations (Retained)

These limitations are accurate and should remain in the record:

```
1. Category images
   → Derived from first curated product in each category
   → Ferm API doesn't expose standalone category images

2. Hero/editorial images
   → Use product images as placeholders
   → Ferm API doesn't expose homepage editorial assets

3. Logo
   → Text fallback only (no SVG/image logo in demo data)

4. Gallery images
   → All 66 products have galleries
   → Some may have fewer images than the live site

5. FORCE_DEMO/DISABLED modes
   → Architecture verified in code
   → Not runtime-tested in browser

6. Remote image failure
   → Fallback code implemented
   → Not deliberately broken in runtime test

7. Customizer two-way transitions
   → Fallback chain verified in code
   → Browser-tested via Customizer upload/remove
```

---

## 9. Menu Regression After Demo/Real Transition

```
Menu items after demo/real transition:
→ Still resolve correctly

Verification:
  TEST 1 (product transition): Navigation routes verified
  TEST 2 (category transition): Navigation routes verified
  TEST 8 (route matrix): All menu routes return 200

Menu locations verified:
  → primary (main navigation)
  → footer (footer navigation)
  → secondary (secondary navigation)
  → mega menu (desktop dropdown)
  → mobile (mobile navigation)

Shopify business destinations excluded:
  → No Shopify cart/checkout/API endpoints in menu
```

---

## 10. Report Location Consistency

```
reports/
→ Core/platform forensic authority (38 reports)
→ Frozen reference for engine kernel verification

docs/architecture/
→ Permanent operational architecture
→ Demo system implementation plan
→ Directory structure documentation

docs/forensics/
→ Project/runtime verification
→ This clarification document
→ Acceptance matrix, dataset report, image audit, menu report

docs/reports/
→ Secondary copy (transitional, may be consolidated)
```

---

## 11. Dataset Distinction: Discovered vs Curated

```
SOURCE DISCOVERY
  Total products discovered: 2,609
  Total collections discovered: 186
  Method: One-time read-only crawl via Shopify JSON API
  Date: 2026-08-31

CURATED DEMO DATASET
  Products selected: 66
  Categories curated: 9
  Collections curated: 4
  Image references: 510
  Selection criteria: Availability, gallery richness, certification, deduplication

EXCLUDED
  Products excluded: 2,543
  Reason: Curated to 66 best representatives per category
  Subcategories: Covered by parent categories
  Product-line collections: Covered by category
  Seasonal collections: Not needed for demo
```

---

## 12. Current Classification

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

DOCUMENTATION QA                   ⚠️ THIS DOCUMENT
INFINITYFREE                       ⏳ NOT YET PROVEN
```

---

## 13. Remaining Path to Final Release

```
17/17 runtime pass (DONE)
        ↓
documentation clarification (THIS DOCUMENT)
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

---

*Generated by Codebuff 🤖*  
*Co-Authored-By: Codebuff <noreply@codebuff.com>*
