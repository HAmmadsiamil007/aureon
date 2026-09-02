# 25 — PERFORMANCE ARCHITECTURE

## Active-Pack-Only Loading

**Key principle:** Only the active design pack's assets load at runtime.

```
Installed packs: fermliving, lumen, testclient
Active pack: fermliving
    ↓
Runtime loaded: fermliving CSS/JS only
lumen CSS/JS: NOT loaded
testclient CSS/JS: NOT loaded
```

## Asset Suppression

### Complete-Page Mode
All platform CDNs + contract JS suppressed:
- Bootstrap, Swiper, GSAP, ScrollTrigger
- animations.js, main.js, countdown.js
- WooCommerce presentation CSS/JS

### Component Mode
Theme layout styles suppressed:
- aureon-style, aureon-style-grid, aureon-mobile-style
- aureon-font-icons, font-awesome (legacy)
- aureon-menu, aureon-dropdown-click, aureon-modal

## Performance Optimizations

### Resource Hints
```php
aether_resource_hints() [wp_head, priority 1]
// DNS prefetch: cdn.jsdelivr.net, cdnjs.cloudflare.com, unpkg.com
// Preconnect: cdn.jsdelivr.net, cdncdnjs.cloudflare.com
```

### Asset Preloading
```php
aether_preload_assets() [wp_head, priority 2]
// Preload: fonts.css
// Preload: first hero slide image (component mode only)
```

### Query String Cleanup
```php
aether_remove_query_strings()
// Strip ?ver= from CDN URLs (keep for local assets)
```

### WooCommerce Optimization
```php
aether_optimize_woocommerce()
// Disable WC frontend scripts on non-WC pages
```

### HTML Compression
```php
aether_compress_html()
// Strip HTML comments (preserve conditionals + JSON-LD)
// Collapse inter-tag whitespace
// Skip for AJAX/REST responses
```

## Performance Metrics

| Metric | Component Mode | Complete-Page |
|--------|----------------|---------------|
| CSS requests | 8+ (CDNs + platform + pack) | 4 (pack only) |
| JS requests | 10+ (CDNs + platform + pack) | 3-8 (pack only) |
| Font requests | 2-3 (self-hosted) | 2-3 (pack fonts) |
| Platform CDNs | 6 | 0 |
| Inactive pack assets | 0 | 0 |

## Cache Strategy

- Local assets: `filemtime()` version strings
- CDN assets: Version numbers, query strings stripped
- HTML: Output compression (ob_start)
- Static cached: design resolution, manifest
