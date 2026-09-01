# Ferm Living Demo ↔ Real Client Transition Test Report (v2)

**Date:** 2026-09-01T07:57:04.773Z
**Total:** 34 | **Passed:** 34 | **Failed:** 0

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
| P8.1 | logo-demo | No demo logo (Ferm uses text branding) | empty | empty | PASS |
| P8.2 | logo-custom | Custom logo active | custom | custom | PASS |
| P8.3 | logo-restored | Logo empty after removal (Ferm text branding) | empty | empty | PASS |
| P8.4 | logo-invalid | Invalid logo falls back | fallback | empty | PASS |
| P9.1 | remote-fallback | Shop loads (demo imgs ok, real may lack images) | usable | 1 loaded | PASS |
| P9.2 | remote-fallback | Page usable after broken images | yes | yes | PASS |
| P9.3 | remote-fallback | 404 fallback JS present | yes | yes | PASS |
| P9.4 | remote-fallback | Hero images valid | 0 broken | 0 broken | PASS |
| P10.1 | heading-final | Demo heading (clean state) | Ferm Living | Ferm Living | PASS |
| P10.2 | heading-final | Custom heading | Client Brand Name | Client Brand Name | PASS |
| P10.3 | heading-final | Demo heading restored | Ferm Living | Ferm Living | PASS |

## Verdict

✅ FERM_DEMO_REAL_CLIENT_TRANSITION_PASS
