# Ferm Living Demo ↔ Real Client State-Transition Test Report

**Date:** 2026-09-01
**Verdict:** ✅ FERM_DEMO_REAL_CLIENT_TRANSITION_PASS
**Total:** 23 tests | **Passed:** 23 | **Failed:** 0

## Executive Summary

The Ferm Living design pack's demo↔real client state-transition system is **fully functional**. All 23 acceptance tests pass across 7 phases covering:

- Real product visibility and demo suppression
- Demo product restoration on real product deletion
- Category-level demo fallback (furniture category)
- Customizer heading transitions (demo ↔ custom ↔ demo)
- Add/remove product transitions
- Category creation and cleanup

## Bugs Fixed During Testing

### 1. Pack composer.php never loaded (CRITICAL)
**File:** `aureon/frontend/views/loader.php:36-40`
**Impact:** The Ferm pack's entire demo/real switching logic (1722 lines) was never executed. FermPageData was never injected. Demo products never appeared.
**Fix:** Added `require_once` for the pack's `composer.php` in `loader.php`.

### 2. `aureon_get_option()` for heading always returned null
**File:** `aureon/frontend/designs/fermliving/composer.php:898`
**Impact:** Custom heading could never be set. `aureon_get_option()` requires the key to exist in `aureon_get_defaults()`, but `aether_site_heading` wasn't there.
**Fix:** Changed to `get_option('aether_site_heading', '')` to read directly from `wp_options`.

## Test Results

| ID | State | Action | Expected | Actual | Status |
|----|-------|--------|----------|--------|--------|
| P0.1 | baseline | DB accessible | ok | 3 products | PASS |
| P1.1 | real-present | Real products on shop | ≥1 | 3 | PASS |
| P1.2 | real-present | Demo products hidden | 0 | 0 | PASS |
| P1.3 | real-present | All sources empty (real) | all empty | all empty | PASS |
| P1.4 | real-present | Furniture shows demo fallback | >0 | 12 | PASS |
| P1.5 | real-present | Homepage 200 + FermPageData | present | present | PASS |
| P2.1 | all-deleted | Zero real products | 0 | 0 | PASS |
| P2.2 | all-deleted | Demo products restored | >0 | 66 | PASS |
| P2.3 | all-deleted | Demo source=demo | demo | demo | PASS |
| P2.4 | all-deleted | Total demo count | 66 | 66 | PASS |
| P2.5 | all-deleted | Furniture category demo fallback | >0 | 12 | PASS |
| P3.1 | restored | Real products back | 3 | 3 | PASS |
| P3.2 | restored | Demo hidden again | 0 | 0 | PASS |
| P4.1 | extra-added | Extra product visible | yes | yes | PASS |
| P4.2 | extra-added | Demo still hidden | 0 | 0 | PASS |
| P5.1 | extra-deleted | Extra gone | no | gone | PASS |
| P5.2 | extra-deleted | Other reals remain, demo hidden | 0 demo | 0 demo | PASS |
| P6.1 | qa-cat | QA category has real product | ≥1 | 1 | PASS |
| P6.2 | qa-cat-empty | Empty QA cat shows 0 (no demo match) | 0 | 0 | PASS |
| P6.3 | qa-cat-deleted | Category cleanup | ok | deleted | PASS |
| P7.1 | heading-demo | Demo heading present | Ferm Living | Ferm Living | PASS |
| P7.2 | heading-custom | Custom heading active | My Custom Test Heading | My Custom Test Heading | PASS |
| P7.3 | heading-restored | Demo heading restored | Ferm Living | Ferm Living | PASS |

## State Transition Summary

```
PHASE 1: 3 real products → shop shows 3 real, 0 demo
PHASE 2: delete all → shop shows 0 real, 66 demo (demo restored)
PHASE 3: restore 3 → shop shows 3 real, 0 demo (demo hidden)
PHASE 4: add 1 extra → shop shows 4 real, 0 demo
PHASE 5: delete extra → shop shows 3 real, 0 demo
PHASE 6: create/delete QA category → correct behavior
PHASE 7: heading demo→custom→demo → transitions work
```

## Files Modified

- `aureon/frontend/views/loader.php` — Added pack composer.php loading
- `aureon/frontend/designs/fermliving/composer.php` — Fixed heading option retrieval

## Test Script

`transition-test.js` — Dynamic, resilient, no hardcoded IDs. Run with `node transition-test.js`.
