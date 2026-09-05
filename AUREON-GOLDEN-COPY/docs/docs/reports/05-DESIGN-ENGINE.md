# 05 — DESIGN ENGINE

## Overview

The design engine manages which client frontend is active and how it integrates with the Golden Core. It supports two modes: **complete-page** (frozen HTML) and **component-mode** (adapters + components).

## Design Resolution

### `aether_active_design()` → `design.php`

**Resolution order:**
1. `AETHER_DESIGN` constant (if defined)
2. `aether_active_design` option (from database)
3. Default: `'fermliving'`

**Static cached** per request — resolves once, then returns cached value.

### `aether_active_design_dir()`

Returns the active pack directory path, or `''` for `'luxury'` (the engine tree itself).

```php
if ('luxury' === $design) return '';
return AETHER_FRONTEND_DIR . 'designs/' . $design . '/';
```

### `aether_is_complete_page_design()`

Reads `manifest.json` → checks `complete_page` flag.

## Design Pack Structure

```
aureon/frontend/designs/<slug>/
├── manifest.json       # Machine-readable descriptor
├── tokens.php          # Pack-specific defaults
├── composer.php        # Data bridge (filters)
├── css/                # Pack stylesheets
├── js/                 # Pack scripts
├── cdn/                # Pack assets (images, fonts)
├── data/               # Demo data (JSON)
├── mapper/             # Data remapping functions
├── sections/           # Pack-specific sections
├── components/         # Pack component overrides
└── *.html              # Complete-page templates
```

## Manifest Schema

```json
{
  "id": "string",
  "label": "string",
  "version": "string",
  "complete_page": true|false,
  "assets": {
    "css": ["string|object"],
    "js": ["string|object"]
  },
  "pages": {
    "home": "string",
    "products": { "slug": "string" },
    "collections": { "slug": "string" },
    "pages": { "slug": "string" },
    "blog": "string",
    "cart": "string",
    "checkout": "string",
    "account": "string"
  },
  "data": {
    "required": ["string"],
    "optional": ["string"]
  },
  "integrations": {
    "wordpress": ["string"],
    "woocommerce": ["string"],
    "plugins": ["string"]
  },
  "components": {
    "overrides": { "id": "template-path" }
  }
}
```

## Token System

**Priority chain:**
1. Engine defaults (`tokens/tokens.php` → priority 10)
2. Pack defaults (`designs/<slug>/tokens.php` → priority 20)
3. Saved Customizer values (always win)

**Ferm Living tokens** disable AUREON motion, preloader, fog — Ferm has its own shell.

## Body Class

Every page gets `design-<slug>` class for CSS scoping.

## Pack Override Mechanism

`aether_resolve_design_path($relative_path)`:
1. Check if file exists in active pack directory
2. If yes → return pack file
3. If no → return engine default file

This allows packs to shadow any component/section template.
