# 09 — ASSET LOADING ARCHITECTURE

## Asset Loading Flow

```
wp_enqueue_scripts
    ↓
aether_design_enqueue_assets() [priority 20]
    ↓
Is active design 'luxury'?
  YES → return (handled by aureon_aether_enqueue_assets)
  NO  → continue
    ↓
Is complete_page design?
  YES → enqueue ONLY pack assets from manifest
  NO  → enqueue platform CDNs + platform JS + pack assets
    ↓
aether_enqueue_pack_asset() for each manifest entry
```

## Complete-Page Asset Loading

Only pack assets from `manifest.json`:
- CSS entries from `assets.css`
- JS entries from `assets.js`
- No platform CDNs
- No platform contract JS
- Page-specific gating via `page` field

## Component-Mode Asset Loading

### Platform CDNs
- Bootstrap 5.3.3 CSS
- Font Awesome 6.5.1
- Swiper 11 CSS
- Bootstrap 5.3.3 JS
- Swiper 11 JS
- GSAP 3.12.5
- ScrollTrigger 3.12.5

### Platform Contract JS
- animations.js (motion watchdog)
- main.js (AJAX cart, contact, newsletter, search, drawer)
- countdown.js

### Pack Assets
From `manifest.json`:
- CSS files
- JS files with dependencies

## Asset Suppression

`aureon_aether_suppress_theme_output()` [priority 1000]:

### Theme Assets Removed
- aureon-style, aureon-style-grid, aureon-mobile-style
- aureon-comments, aureon-widget-areas
- aureon-font-icons, font-awesome
- aureon-rtl, aureon-fonts, aureon-child
- aureon-menu, aureon-dropdown-click, aureon-modal
- aureon-navigation-search, aureon-back-to-top

### Complete-Page Platform Assets Removed
- aether-bootstrap, aether-fontawesome, aether-swiper
- aether-style, aether-motion, aether-responsive
- aether-a11y, aether-pages, aether-fonts, aether-tokens
- woocommerce-general, woocommerce-layout, woocommerce-smallscreen
- aureon-woocommerce, aureon-woocommerce-mobile
- wc-blocks-style, select2, wc-blocks-packages-style
- aether-bootstrap-js, aether-swiper-js, aether-gsap
- aether-scrolltrigger, aether-lenis, aether-lenis-scroll
- aether-animations, aether-main, aether-countdown
- aether-phantom-bridge
- wc-country-select, wc-address-i18n, wc-checkout
- wc-customer-input, wc-geolocation

**Exception:** Checkout and account pages keep WC assets.

## Page-Specific Gating

Manifest entries can specify `"page": "product"` to only load on product pages.

Detection: `aether_is_pack_asset_page_match($page_key)` checks:
- WordPress query state
- Request URI fallback

## Asset Entry Format

String: `"css/style.css"`
Object: `{"file": "js/app.js", "deps": ["ferm-data-shims"], "page": "product", "base": false}`

## Cache/Versioning

Local assets use `filemtime()` for version strings.
CDN assets have query strings stripped by `aether_remove_query_strings()`.

## Active-Pack-Only Proof

When Ferm is active:
- 4 CSS files loaded (fonts × 3 + app)
- 3-8 JS files loaded (speedblitz + shims + app + page-specific)
- ZERO lumen/other pack assets
- ZERO platform CDNs (suppressed)
- ZERO platform contract JS (suppressed)
