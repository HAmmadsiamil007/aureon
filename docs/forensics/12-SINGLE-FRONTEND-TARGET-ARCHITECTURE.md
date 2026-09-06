# 12 — Single-Frontend Target Architecture (Phase 26)

## Principle

**One canonical tree, one active design pack, one data contract.** The engine (Golden Core) must boot with zero client packs present; packs attach via a declared contract.

## Layer contract (normative)

### GOLDEN AUREON CORE — owns

1. WP/WooCommerce integration: template routing, WC page templates, sessions, fragments.
2. Design system kernel: `aether_active_design()` (default **`luxury`** — the engine itself; packs chosen by option/constant only), pack dir/url resolution, manifest schema + sanitization, complete-page flag.
3. Asset pipeline: manifest-driven enqueue, suppression registry (data-driven, not hardcoded handles).
4. Adapter/viewmodel kernel: `aether_adapter_*` filters, section registry, renderer.
5. Platform services: security headers/CSP, newsletter (AJAX+REST+admin), analytics, performance.
6. Selector-contract test harness definitions (what bridge JS may hook).

### CLIENT BRIDGE — owns

1. Pack composer split into: `data/` (VinetaPageData builders, demo switching), `ajax/` (cart endpoints), `menus/` (splicers), `customizer/` (controls + CSS emitter), `compat/` (frozen-HTML rewrites).
2. Theme-level standalone templates for WC pages (cart/checkout/account/auth/thankyou).
3. Bridge JS (`vineta-data-shims.js`, `vineta-path-bridge.js`) against published JSON schemas.

### CLIENT FRONTEND — owns

1. Frozen HTML/CSS/JS, fonts, images, tokens.php, demo JSON.
2. Nothing else. No `add_action`/`add_filter` on WP/WC internals from templates.

### WORDPRESS + WOOCOMMERCE + PLUGINS

Data + engine only. UI code never reimplements commerce logic (validated: current checkout/auth are genuine WC).

## Data contracts (frozen schemas)

- `VinetaPageData`: site, announcement, navigation, footer, hero, products[], categories[], product, collection, search, blog, article, cart, customer, config.
- `VinetaCart` AJAX: `{success, data:{cart_contents, item_count, total}}` — additive-only evolution.
- Manifest: pages map (home/shop/products._generic/collections.default/blog/blog_single/search/pages/static), assets (css/js + deps/priority), complete_page, data (required/optional), integrations.
- Selector contract: per-template list of required ids/classes (`#customer_login`, `#submit-login`, `.cart-count`, `.header__logo`, `#mobileMenuBtn`, menu splice classes…) — machine-checked.

## Routing (target, unchanged behavior, clarified ownership)

```
WP → template_include:
  99  core WC page router (cart/checkout/order-received/account)
  998 complete-page router → ferm-page.php (manifest resolver only; fallback → 404.html)
account always → standalone template (single decision point, not two)
```

## Design resolution (target)

- `AETHER_DESIGN` constant (wp-config, server-owned) > option `aether_active_design` > **`luxury`**.
- Vineta selected via option/constant — never hardcoded in Core.
- Fresh boot with no packs → luxury engine renders (proves Core independence).

## Demo system (target)

`aether_demo_mode`: `auto` = real-if-exists-else-demo (implemented, tested); `force_demo`; `disabled`. Demo dataset isolated in pack `/demo`.

## Migration order (from current state — approved work only)

1. Repo hygiene (A-01, A-02, B-01).
2. Core de-coupling (A-03) with runtime option set.
3. Composer split (B-02) behind existing filters — no behavior change.
4. Consolidations (B-06) + contract tests (B-03).
5. Demo-mode fix (B-04) + 404 fix (B-05).
6. Hardening (C-01…C-08).

Full task table: `13-SINGLE-FRONTEND-IMPLEMENTATION-PLAN.md`.
