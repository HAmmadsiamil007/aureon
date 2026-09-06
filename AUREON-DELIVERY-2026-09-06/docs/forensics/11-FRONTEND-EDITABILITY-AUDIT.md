# 11 — Frontend Editability Audit (Phase 25 companion)

Consolidated, per-area map of **who owns what** and **what a future UI edit may safely change**.

## Layer ownership

```
GOLDEN CORE (never client-edit)
  wp loading, theme bootstrap, template_include filters, suppression,
  design resolution, manifest schema, asset pipeline, WC page routing,
  security/newsletter/analytics/performance modules
BRIDGE (edit with tests)
  ferm-page.php rewrites, pack composer data builders + AJAX + splicers,
  vineta-data-shims/path-bridge, adapters, WC page standalone templates
CLIENT FRONTEND (freely editable)
  frozen HTML/CSS/JS of the pack, pack tokens.php, demo JSON
```

## Edit-safety matrix

| Area | Owner layer | Safe edit class | Hard dependencies (do not break) |
|---|---|---|---|
| Logo/favicon | CLIENT + bridge | FRONTEND_ONLY | logo bridge classes `.header__logo`, `[data-header-logo]`; favicon hardcoded path in `inc/frontend.php` |
| Announcement bar | CLIENT (VinetaPageData.announcement) | FRONTEND_ONLY | announcement key + pack JS consumer |
| Header/menus | CLIENT markup + BRIDGE splicer | BRIDGE_UPDATE | `.main-menu`-class splice target, WP menu locations, `#mobileMenuBtn` |
| Hero | CLIENT + pack Customizer repeater | FRONTEND_ONLY | repeater schema, hero slide selectors |
| Product cards / shop grid | CLIENT JS render | FRONTEND_ONLY | VinetaPageData.products schema |
| Category cards | CLIENT JS render | FRONTEND_ONLY | VinetaPageData.categories schema |
| Product page | CLIENT markup + BRIDGE hydration | BRIDGE_UPDATE | product data schema + gallery/swatch/add-to-cart selectors |
| Variations | BRIDGE (composer data) + CLIENT swatch UI | BRIDGE_UPDATE | `is_variable/variation_attributes/variations` schema |
| Cart drawer | BRIDGE | BRIDGE_UPDATE | VinetaCart AJAX response schema |
| Cart page | CORE-owned template + section | CORE_REVIEW | WC cart session, fragments |
| Checkout | CORE (WC native) + presentation shell | CORE_REVIEW | WC fields/nonce/order flow — presentation only |
| Thank-you | CORE template | CORE_REVIEW | order-received endpoint |
| Auth pages | CORE-owned standalone templates | CORE_REVIEW | WP/WC auth + nonces |
| Account dashboard | CORE-owned standalone template | CORE_REVIEW | WC endpoints, plugin hook gaps (B-07) |
| Search | BRIDGE | BRIDGE_UPDATE | search data builder + suggestions bridge |
| Blog | CLIENT + data builder | FRONTEND_ONLY | blog data schema |
| Static pages | CLIENT | FRONTEND_ONLY | manifest `static` map keys |
| Footer | CLIENT markup + splicer | BRIDGE_UPDATE | footer menu splice class |
| Colors | CLIENT (`--vineta-*`) | FRONTEND_ONLY | emitter var names |
| Typography | BRIDGE token bridge | BRIDGE_UPDATE | `--font-heading/body` bridge |
| Animations | CLIENT | FRONTEND_ONLY | wow/animate init order |
| Spacing/layout | CLIENT CSS | FRONTEND_ONLY | — |
| 404 | CLIENT (404.html) | FRONTEND_ONLY | resolver fallback fix (B-05) required first |
| Newsletter form | CLIENT + CORE backend | FRONTEND_ONLY | aether AJAX action + nonce |
| Wishlist/compare | UNWIRED | CORE_REVIEW | decide keep/kill |

## Redesign rules of engagement

1. Run selector-contract tests (test plan §3) on the new HTML **before** merging.
2. Never rename classes used by `ferm-page.php` rewrites or composer splicers without a paired bridge change.
3. Keep VinetaPageData/VinetaCart JSON schemas stable; additive changes only.
4. Colors/typography via tokens — never hardcode in templates.
5. Any new asset must be added to pack `manifest.json`, not hardcoded.
6. Demo JSON stays; demo/real switch semantics fixed per B-04 before live.

## Target architecture summary (Phase 26)

```
GOLDEN AUREON CORE
  owns: WP/WC integration, routing, security, design-pack contract,
        manifest schema, asset pipeline, adapter/viewmodel kernel
        ↓ stable contract (filters + JSON schemas + selector contracts + manifest)
CLIENT BRIDGE
  owns: pack data builders, AJAX endpoints, frozen-HTML rewrites/splices,
        Customizer bridge, auth/cart page templates (theme-level)
        ↓
ONE PREMIUM FRONTEND (pack)
  owns: HTML/CSS/JS, tokens, demo dataset
        ↓
WORDPRESS + WOOCOMMERCE + PLUGINS (data & engine, untouched by UI work)
```

Full task-level plan: `13-SINGLE-FRONTEND-IMPLEMENTATION-PLAN.md`.
