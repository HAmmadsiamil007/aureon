# 08 — DESIGN RESOLVER AND MANIFEST

## Design Resolution Chain

```
aether_active_design()
    ↓
1. AETHER_DESIGN constant (if defined)
2. 'aether_active_design' option (database)
3. Default: 'fermliving'
    ↓
Static cached per request
```

## Key Functions

### `aether_active_design()` → string
Returns the active design slug. Static cached.

### `aether_active_design_dir()` → string
Returns pack directory path or `''` for luxury.

### `aether_pack_url()` → string
Returns pack URL or `''` for luxury.

### `aether_resolve_design_path($relative)` → string
Pack-first file resolution:
1. Check pack directory
2. Fall back to engine default

### `aether_design_manifest()` → array
Reads and sanitizes `manifest.json`. Static cached.

### `aether_is_complete_page_design()` → bool
Checks `manifest['complete_page']`.

## Manifest Sanitization

Whitelisted keys: `id`, `label`, `version`, `assets`, `components`, `sections`, `mappings`, `data`, `integrations`, `js`, `customizer`, `pages`, `complete_page`

Only these keys are preserved; all others are stripped.

## Manifest Examples

### Ferm Living (Complete-Page)
```json
{
  "id": "fermliving",
  "complete_page": true,
  "assets": {
    "css": ["cdn/.../fonts.css", "cdn/.../app.css"],
    "js": [
      {"file": "cdn/.../speedblitz.min.js", "deps": []},
      {"file": "cdn/.../ferm-data-shims.js", "deps": []},
      {"file": "cdn/.../app.js", "deps": ["ferm-data-shims"]},
      {"file": "cdn/.../product.js", "page": "product", "deps": ["ferm-data-shims"]},
      {"file": "cdn/.../cart-page.ferm.js", "page": "cart", "deps": ["ferm-data-shims"]}
    ]
  },
  "pages": {
    "home": "index.html",
    "products": { "rico-lounge-chair": "products/rico-lounge-chair.html" },
    "collections": { "furniture": "collections/furniture.html" },
    "cart": "cart.html",
    "checkout": "checkout.html",
    "account": "account/login.html"
  }
}
```

### Lumen (Component Mode)
```json
{
  "id": "lumen",
  "complete_page": false,
  "assets": {
    "css": ["css/lumen.css"],
    "js": [{"file": "js/lumen.js", "deps": ["aether-main"]}]
  },
  "components": {
    "overrides": {
      "shell/header": "components/shell/header.php",
      "card/product": "components/cards/product.php"
    }
  }
}
```

## Token Defaults Priority

```
1. Engine defaults (tokens/tokens.php) → priority 10
2. Pack defaults (designs/<slug>/tokens.php) → priority 20
3. Saved Customizer values → always win
```

## Body Class

Every page gets `design-<slug>` class for CSS scoping.
