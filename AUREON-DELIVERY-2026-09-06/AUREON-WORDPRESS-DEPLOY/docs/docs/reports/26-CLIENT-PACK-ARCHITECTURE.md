# 26 — CLIENT PACK ARCHITECTURE

## Pack Structure

```
aureon/frontend/designs/<slug>/
├── manifest.json          # Required: pack descriptor
├── tokens.php             # Optional: pack defaults
├── composer.php           # Optional: data bridge
├── index.html             # Complete-page: homepage
├── product.html           # Complete-page: product
├── cart.html              # Complete-page: cart
├── checkout.html          # Complete-page: checkout
├── account.html           # Complete-page: account
├── blog.html              # Complete-page: blog
├── collection.html        # Complete-page: collection
├── page.html              # Complete-page: static page
├── css/                   # Pack stylesheets
├── js/                    # Pack scripts
├── cdn/                   # Pack assets (images, fonts)
├── data/                  # Demo data (JSON)
├── mapper/                # Data remapping
├── sections/              # Pack-specific sections
└── components/            # Pack component overrides
```

## Manifest Required Fields

```json
{
  "id": "string (slug)",
  "label": "string (display name)",
  "version": "string"
}
```

## Manifest Optional Fields

```json
{
  "complete_page": true|false,
  "assets": {"css": [...], "js": [...]},
  "pages": {"home": "index.html", ...},
  "data": {"required": [...], "optional": [...]},
  "integrations": {"wordpress": [...], "woocommerce": [...], "plugins": [...]},
  "components": {"overrides": {"id": "template-path"}},
  "sections": {},
  "mappings": {},
  "customizer": {}
}
```

## Pack Modes

### Complete-Page Pack
- `complete_page: true`
- Ships complete HTML files
- Has its own CSS/JS/assets
- Uses thin bridge for data
- AUREON shell bypassed

### Component-Mode Pack
- `complete_page: false` (or absent)
- Ships component overrides
- Uses platform CDNs + pack CSS/JS
- AUREON shell renders
- Adapters provide data

## Pack Installation

1. Place pack directory in `aureon/frontend/designs/<slug>/`
2. Set `aether_active_design` option to slug
3. Pack assets auto-enqueued via manifest
4. Pack templates auto-resolved via design resolver

## Pack Activation

```php
// Set active design
update_option('aether_active_design', 'newpack');

// Or via constant
define('AETHER_DESIGN', 'newpack');
```

## Current Packs

| Pack | Mode | Status |
|------|------|--------|
| fermliving | Complete-page | ✅ Active |
| lumen | Component-mode | Available |
| testclient | Complete-page | Test only |
