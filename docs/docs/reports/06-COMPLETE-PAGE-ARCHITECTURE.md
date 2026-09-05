# 06 — COMPLETE-PAGE ARCHITECTURE

## Overview

Complete-page mode (`complete_page: true`) serves a **frozen HTML document** directly from the client pack, bypassing the AUREON shell entirely.

## How It Works

```
WordPress Route
    ↓
template_include [priority 998]
    ↓
aether_is_complete_page_design() → true
    ↓
ferm-page.php (generic complete-page host)
    ↓
aureon_ferm_resolve_page() → maps route to HTML file
    ↓
file_get_contents($pack_dir . $file)
    ↓
aureon_ferm_extract_body($html) → extract <body> content
    ↓
aureon_ferm_rewrite_paths($content, $pack_url) → fix relative paths
    ↓
Open document → wp_head() → body content → wp_footer() → close
```

## Key File: `ferm-page.php`

**Generic template** — works for ANY complete-page design pack.

**Functions:**
- `aureon_ferm_resolve_page()` — route → HTML file mapping
- `aureon_ferm_extract_body()` — extract body content from HTML
- `aureon_ferm_extract_body_attrs()` — extract safe attributes
- `aureon_ferm_render_attrs()` — render attribute array
- `aureon_ferm_rewrite_paths()` — server-side path rewriting

## Route Mapping

**Priority 1:** Manifest `pages` mapping
**Priority 2:** Hardcoded fallback (backward compatibility)

| WordPress Route | HTML File |
|-----------------|-----------|
| Homepage | `index.html` |
| Single product | `products/{slug}.html` |
| Shop/archive | `collections/furniture.html` |
| Product category | `collections/{slug}.html` |
| Static pages | `pages/{slug}.html` |
| Blog | `blogs/stories.html` |
| Cart | `cart.html` |
| Checkout | `checkout.html` |
| Account | `account/login.html` |
| Search | `blogs/stories.html` (fallback) |
| 404 | `pages/contact.html` (fallback) |

## Path Rewriting

Server-side (`aureon_ferm_rewrite_paths`):
- `<img src="cdn/...">` → absolute pack URL
- `<img srcset="cdn/...">` → absolute pack URL
- `<link preload href="cdn/...">` → absolute pack URL
- CSS `url(cdn/...)` → absolute pack URL

Client-side (inline JS):
- Dynamic image rewriting via MutationObserver
- Link rewriting (Shopify → WordPress routes)

## Asset Suppression

When complete-page mode is active, `aureon_aether_suppress_theme_output()` dequeues:
- Platform CDNs (Bootstrap, Swiper, GSAP)
- Platform JS (animations, main, countdown)
- Platform CSS (style, motion, responsive, a11y, pages)
- WooCommerce presentation CSS/JS
- Theme layout styles/scripts

**Exception:** Checkout and account pages keep WC assets.

## What MUST NOT Happen

- ❌ Split the frozen HTML into AUREON sections
- ❌ Rebuild the frontend as components
- ❌ Replace client presentation with platform markup
- ❌ Load all client assets and hide with CSS
- ❌ Modify the frozen HTML to add AUREON classes

## Data Injection

FermPageData is injected via `wp_localize_script('ferm-data-shims', 'FermPageData', $page_data)`.

Contains: cart, customer, shop, navigation, config, customizer, product (on product pages), collection (on archive pages).
