# SINGLE-FRONTEND-IMPLEMENTATION-PLAN

## PHASE P0 — TASK: Resolve ferm-page.php duplication
- FILE(S): `aureon/ferm-page.php`, `theme/aureon/ferm-page.php`
- LAYER: Core
- WHY: Critical divergence in core engine.
- DEPENDENCIES: None
- RISK: HIGH
- EXPECTED RESULT: Single source of truth.
- TEST: Site loads.
- REGRESSION: Check all routes.
- ROLLBACK: Restore `theme/aureon/ferm-page.php`.

## PHASE P1 — TASK: Fix Product Gallery JS
- FILE(S): `vineta/js/gallery.js`
- LAYER: Frontend
- WHY: console-errors.txt shows crash on product page.
- DEPENDENCIES: None
- RISK: LOW
- EXPECTED RESULT: Gallery works.
- TEST: View variable product.
- REGRESSION: None.
- ROLLBACK: Revert JS.
