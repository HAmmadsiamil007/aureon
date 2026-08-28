# Aureon + AETHER — Install Package

Drop-in layout for `wp-content/`. Copy these folders into your WordPress install:

```
wp-content/
├── themes/
│   └── aureon/          ← THEME  (Appearance → Themes → activate "Aureon")
├── plugins/
│   └── aureon-studio/   ← PLUGIN (Plugins → activate "Aureon Studio")
├── frontend/            ← AETHER ENGINE (required — the theme renders through it)
└── mu-plugins/
    └── aureon-fix-wc-session.php
```

## IMPORTANT — the frontend/ engine is mandatory

`theme/inc/frontend.php` boots the AETHER engine from `wp-content/frontend/views/loader.php`.
If `frontend/` is missing, every page fatals. Do not install the theme without it.

## Quick Install

1. Upload `aureon.zip` to `wp-content/` and extract
2. Activate "Aureon" theme in Appearance → Themes
3. Activate "Aureon Studio" plugin in Plugins
4. Set a static front page in Settings → Reading

## Build info

- Source of truth: https://github.com/HAmmadsiamil007/aureon (main)
- Distribution: https://github.com/HAmmadsiamil007/aureon/releases
- Gate: `frontend/tests/verify.sh` PASSED before packaging

## Integration Status (August 2026)

### ✅ Verified
- Complete-page host (Ferm Living frozen HTML)
- AUREON asset isolation
- Complete Ferm presentation (all pages)
- Homepage runtime
- Simple WC product #834 (Meridian Lamp Black)
- Variable WC product #828 (Trifolium Side Table — 3 variants)
- Variant selection (price/SKU/image/update)
- Add-to-Cart (WC AJAX, cart count)
- Collection/archive (Accessories — 48 products)
- Responsive (1440/1024/768/390)
- Shopify-free runtime
- External fonts removed (self-hosted Space Grotesk)
- Zero 404s

### Architecture
```
WC Product/Collection
    ↓
AUREON adapter (UNCHANGED)
    ↓
FermPageData (PHP bridge)
    ↓
ferm-data-shims.js (DOM bridge)
    ↓
Frozen Ferm presentation
    ↓
WooCommerce cart/checkout
```

## Requirements

- WordPress 6.0+ (tested 7.0.2)
- PHP 7.4+ (tested 8.3)
- WooCommerce 8+/11 (optional — theme falls back to demo content when absent,
  gated by the `aether_demo_content` toggle)

## What's Inside

| Package | Description | Size |
|---------|-------------|------|
| `aureon-theme.zip` | WordPress theme | ~3.7 MB |
| `aureon-studio.zip` | Plugin (15 modules) | ~1.1 MB |
| `aureon-frontend.zip` | AETHER engine + Ferm Living pack | ~180 MB |
| `aureon-mu-plugin.zip` | WC session fix | ~1 KB |
| `aureon.zip` | Full bundle (all above) | ~184 MB |

## Design Packs

The `frontend/designs/` directory contains client-specific design packs:

- **fermliving/** — Ferm Living (Danish design, complete-page mode)
- **lumen/** — Lumen (default AUREON design)

Each pack contains:
- `manifest.json` — Page mappings, assets, sections
- `composer.php` — Data bridge (FermPageData injection)
- `mapper/` — Product/category data mappers
- `cdn/` — Static assets (CSS, JS, images, fonts)
- `collections/` — Frozen collection HTML templates
- `products/` — Frozen product HTML templates
