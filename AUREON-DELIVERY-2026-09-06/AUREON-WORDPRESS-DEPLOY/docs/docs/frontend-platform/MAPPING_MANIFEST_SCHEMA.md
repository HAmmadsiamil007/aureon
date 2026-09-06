# DESIGN PACK MAPPING MANIFEST — Schema (M6)

> **Status:** LIVE · **Date:** 2026-08-14
> **Location:** `frontend/designs/<slug>/manifest.json` (machine-readable; loaded by `aether_design_manifest()` in `frontend/views/design.php`).
> **Validator:** `node frontend/tests/validate-manifest.cjs` (wired into `verify.sh`).

## 1. Purpose

One JSON document answers, per client frontend: **which AETHER components/sections/data/integrations this design maps to**, which **assets** it ships, how its **JS behaviors** are classified, and how it binds to **Customizer/WooCommerce/plugins**. It is the intake artifact (P3–P7 of `CLIENT_FRONTEND_INTAKE.md`) made executable.

Malformed or missing manifests degrade gracefully (empty array, base tree renders) — a manifest can never fatal the site.

## 2. Top-level keys (whitelisted)

| Key | Type | Required | Meaning |
|---|---|---|---|
| `id` | string | ✅ | must equal the pack directory name |
| `label` | string | ✅ | human name |
| `version` | string | ✅ | pack version |
| `assets` | object | — | `css[]`, `js[]` (see §3) |
| `components` | object | — | `overrides` map id → template path |
| `sections` | object | — | `overrides` map id → template path (pack sections self-register) |
| `mappings` | object | — | `ui[]`, `components[]` (see §4) |
| `data` | object | — | `required[]`, `optional[]` ViewModel ids from DATA_CONTRACT |
| `integrations` | object | — | `wordpress`, `woocommerce`, `plugins` string arrays |
| `js` | object | — | `platform[]`, `design{}` with `classification` |
| `customizer` | object | — | `tokens[]` (contract names), `controls[]` |

## 3. Assets

```json
"assets": {
  "css": ["css/lumen.css"],
  "js": [
    { "file": "js/lumen.js", "deps": ["aether-main"], "base": false }
  ]
}
```

- Entry = string (path relative to pack dir) or `{file, deps[], base}`.
- `base: true` → resolves against `frontend/assets/` (platform/base files, e.g. reusing a contract script).
- `deps` reference enqueue handles (platform handles: `aether-bootstrap`, `aether-fontawesome`, `aether-swiper(-js)`, `aether-gsap`, `aether-scrolltrigger`, `aether-animations`, `aether-main`, `aether-countdown`; pack handles: `aether-pack-css-<file>`, `aether-pack-js-<file>`).
- Versioning: `filemtime` automatically.

## 4. Mappings

```json
"mappings": {
  "ui": [
    { "client": "top navigation bar", "aether": "shell/header" },
    { "client": "product tiles", "aether": "card/product" }
  ],
  "components": ["shell/header", "card/product", "hero/slide"]
}
```

- `ui[]` = the intake's client→AETHER translation (documentation).
- `components[]` = every AETHER component id the pack consumes — validator checks each exists in the base manifest or pack overrides.

## 5. Data

```json
"data": {
  "required": ["site", "header", "menu", "announcement", "hero", "wc-products", "wc-categories", "blog", "newsletter", "footer"],
  "optional": ["product", "cart", "account", "order", "contact", "faq", "team", "testimonials", "wishlist", "auth", "coming-soon", "shop-hero", "wc-filter", "article"]
}
```

Required/optional ViewModel ids per `DATA_CONTRACT.md`. A required id missing from the contract is a data-gap case (adapter extension = platform change, never a pack hack).

## 6. Integrations

```json
"integrations": {
  "wordpress": ["nav menus", "pages", "blog"],
  "woocommerce": ["shop grid", "product cards", "categories", "cart", "checkout", "account"],
  "plugins": ["aureon newsletter", "aureon ajax", "aureon analytics"]
}
```

Declares which WP/WC/plugin surfaces the pack exercises — the regression scope per pack.

## 7. JS classification

```json
"js": {
  "platform": ["animations.js (motion watchdog)", "main.js (AJAX cart, contact, newsletter, cart-count, search, drawer)"],
  "design": {
    "lumen.js": "delegated header scroll state + reveal fallback",
    "classification": "KEEP platform js; ADAPT none; REPLACE none; MOVE INTO AETHER none; REMOVE lenis/phantom (luxury design choices)"
  }
}
```

Classification per `CLIENT_FRONTEND_INTAKE.md` §5 (KEEP / ADAPT / REPLACE / MOVE INTO AETHER / REMOVE).

## 8. Customizer binding

```json
"customizer": {
  "tokens": ["--gold", "--surface", "--font-heading", "--font-body"],
  "controls": ["colors", "typography", "announcement", "hero repeater", "section toggles"]
}
```

Tokens = generic contract names the pack maps its identity onto (`TOKEN_MAPPING.md`). Controls = platform Customizer controls that drive pack presentation — packs never add controls.

## 9. Example

See `frontend/designs/lumen/manifest.json` (the M10 proof pack).