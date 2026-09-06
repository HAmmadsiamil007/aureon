# FUTURE-DESIGN-EDIT-CONTRACT (permanent, post-release)

**Effective:** 2026-09-06 · **Applies to:** every future client design change on this platform.

## The rule

> The client may request "change the whole design" without anyone rebuilding WordPress, WooCommerce, Customizer, Menus, Search, Auth, Account, Cart, Checkout, Plugins, or the AUREON data architecture. **The frontend is replaceable; the business/data contract is stable; Golden Core is protected.**

## Mandatory sequence for every design request

```
CLIENT REQUEST
→ SNAPSHOT            (git branch/tag the current RC)
→ READ CONTRACT       (tests/contract-tests.cjs + this doc)
→ IDENTIFY COMPONENTS (which templates/sections change)
→ IDENTIFY DYNAMIC DEPENDENCIES
→ CLASSIFY CHANGE     (FRONTEND_ONLY / BRIDGE_UPDATE / CORE_REVIEW)
→ DESIGN PLAN
→ FRONTEND EDIT
→ PRESERVE CONTRACT   (do not rename/remove contracted selectors)
→ TARGETED TEST       (node tests/contract-tests.cjs)
→ DEPENDENCY REGRESSION
→ FULL REGRESSION WHERE REQUIRED
→ RELEASE CANDIDATE
```

## Classification table

| Change | Class | Rule |
|---|---|---|
| Colors | FRONTEND_ONLY | via `--vineta-*` tokens only — never hardcode |
| Typography | FRONTEND_ONLY | via token bridge; never bypass |
| Spacing | FRONTEND_ONLY | pack CSS |
| Animation | FRONTEND_ONLY | pack JS/CSS |
| Header redesign | FRONTEND_ONLY **if contract preserved** | keep `box-nav-menu`, `footer-menu-list`, `global.logo/search/newsletter/cart/account` slots, `logo-header`/`footer-logo`, cart badge classes |
| Product card redesign | FRONTEND_ONLY **if contract preserved** | keep `shop.product_grid/product_card` slots + card schema in VinetaPageData |
| Hero redesign | FRONTEND_ONLY **if contract preserved** | keep `hero` slot + repeater schema |
| DOM selector change | BRIDGE_UPDATE | any rename of a contracted class/id/slot requires paired bridge change + suite update in the same commit |
| Data schema change | BRIDGE_UPDATE | VinetaPageData/VinetaCart JSON: additive-only; removals/renames are breaking |
| New asset | FRONTEND_ONLY | add to pack `manifest.json`, never hardcode |
| Demo dataset | FRONTEND_ONLY | stays in pack `/demo`; real data always wins (post T-11) |
| Generic platform defect | CORE_REVIEW | stop, document in CORE-CHANGE-REQUEST.md, get approval |

## The enforced safety net

`tests/contract-tests.cjs` (57 checks) asserts every bridge dependency against every routed template. **It must pass (exit 0) before any frontend edit merges.** When bridge code adopts a new selector, add it to the suite in the same commit. This converts "fragile regex/DOM contracts" (audit B-03) into an executable contract.

## Frozen facts this contract rests on (verified 2026-09-06)

- Single VinetaPageData producer: `vineta_inject_page_data()` at `wp_head` 5.
- Menus: server-spliced into frozen HTML via `box-nav-menu` + footer matcher.
- Login: `#login .form-login` (standalone templates own auth; ferm-era `#customer_login` rewrite is dead for vineta).
- Variation UI: bridge-generated `.tf-product-info-variation` inside `.tf-product-info-wrap`; frozen `product.variation` slot gets hidden.
- Core unchanged this session: zero diffs under `themes/aureon/inc` + `frontend/views` (commit range 39e8215..4f48ea0).
