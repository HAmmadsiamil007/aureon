# 24 — MEDIA AND ASSET CONTRACT

## Media Types

| Type | Source | Handling |
|------|--------|----------|
| Logo | Customizer (custom_logo) | wp_get_attachment_image_url() |
| Favicon | frontend/assets/images/favicon/ | Static files |
| Hero images | Customizer (aether_hero_slides) | aether_viewmodel_resolve_image() |
| Product images | WooCommerce (gallery) | wp_get_attachment_image_url() |
| Category images | WooCommerce (thumbnail) | wp_get_attachment_image_url() |
| Demo images | Pack CDN | Pack URL rewriting |

## Image Resolution

```php
aether_viewmodel_resolve_image($src)
// Absolute URL → pass through
// Relative 'frontend/...' → content_url() prefix
// Other → pass through
```

## Image Sizes

| Context | Size |
|---------|------|
| Product card | medium_large |
| Product gallery | large |
| Category card | medium_large |
| Hero slide | full |

## Font Sources

### Component Mode
- Cabinet Grotesk + Satoshi (Fontshare, self-hosted)
- Loaded via `aether-fonts` handle → `assets/css/fonts.css`

### Complete-Page (Ferm)
- Space Grotesk + Ferm Open Source fonts
- Loaded via pack CSS: `cdn/shop/t/164/assets/fonts.*.css`

## CDN Assets

| CDN | Assets |
|-----|--------|
| cdn.jsdelivr.net | Bootstrap 5.3.3, Swiper 11 |
| cdnjs.cloudflare.com | Font Awesome 6.5.1, GSAP 3.12.5, ScrollTrigger |
| unpkg.com | Lenis 1.1.19 |

## Path Rewriting

### Server-Side (ferm-page.php)
- `<img src="cdn/...">` → absolute pack URL
- `<img srcset="cdn/...">` → absolute pack URL
- `<link preload href="cdn/...">` → absolute pack URL
- CSS `url(cdn/...)` → absolute pack URL

### Client-Side (inline JS)
- MutationObserver for dynamic images
- Link rewriting (Shopify → WordPress routes)

## Asset Versioning

Local assets: `filemtime()` for cache busting
CDN assets: Version numbers in handle (e.g., '5.3.3')
Query strings stripped from CDN URLs by `aether_remove_query_strings()`
