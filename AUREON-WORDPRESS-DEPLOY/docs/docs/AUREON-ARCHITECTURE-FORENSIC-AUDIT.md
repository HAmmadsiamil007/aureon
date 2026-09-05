# AUREON Architecture Forensic Audit

**Date:** 2026-08-28
**Status:** Complete
**Scope:** Full reverse-engineering of the AUREON frontend integration system

---

## 1. Executive Summary

The AUREON system supports **two fundamentally different frontend integration modes**:

| Mode | Template Entry | Shell Used | Data Flow | Asset Loading |
|------|---------------|------------|-----------|---------------|
| **Component Framework** | `front-page.php` / `page.php` / etc. | `header.php` → `aether_compose_header()` + `footer.php` → `aether_compose_footer()` | Adapter → ViewModel → Component → Section | `aether_design_enqueue_assets()` (priority 20) |
| **Complete Page** | `ferm-page.php` (priority 998) | NONE — entire HTML read from file | N/A — static HTML | Same CDN/pack assets via `wp_enqueue` + WooCommerce injection |

**Critical finding:** The Ferm Living design pack currently operates in **Complete Page mode**, which bypasses the entire component framework. The component overrides, sections, composer, and mapper in `designs/fermliving/` are **entirely unused** when the complete-page router is active.

---

## 2. Architecture Layers (Component Framework)

### 2.1 Boot Sequence

```
functions.php (line 108)
  → require inc/frontend.php
    → require frontend/views/loader.php
      → require tokens/tokens.php          (design token defaults)
      → require views/design.php           (pack resolution)
      → require views/registry.php         (section registry)
      → require views/renderer.php         (component/section renderer)
      → require views/viewmodel.php        (data normalization)
      → require views/assets.php           (asset pipeline)
      → require views/composer.php         (shell header/footer composition)
      → require adapters/*.php             (23 adapter files — only WP/WC layer)
      → require sections/*.php             (26 base section files)
      → require designs/<slug>/sections/*.php  (pack section overrides)
```

### 2.2 Request Lifecycle (Component Framework)

```
HTTP Request
  → WordPress Template Loader
    → template_include filter (priority 998, frontend.php)
      → Is Ferm Living active? → Yes → ferm-page.php (COMPLETE PAGE MODE)
      → Is Ferm Living active? → No → standard WP template
        → header.php
          → wp_head()
          → aether_compose_header()
            → shell/preloader (if enabled)
            → shell/fog (if enabled)
            → shell/skip-link
            → <div class="page-content">
            → shell/mobile-chrome
            → shell/announcement (if enabled)
            → shell/header
            → <main id="swup">
        → front-page.php (or page.php, single.php, etc.)
          → aether_frontpage_sections filter
          → foreach $sections: aether_render_section($id)
            → registry[$id]['adapter'] → call adapter function
            → adapter returns canonical data
            → merge with passed $data
            → aether_resolve_design_path() → pack-first template resolution
            → include $template (component renders $sectionData)
        → footer.php
          → aether_compose_footer()
            → shell/footer
          → commerce/quick-view
          → wp_footer()
```

### 2.3 Design Pack Resolution

**File:** `frontend/views/design.php`

```
aether_active_design()
  Resolution: AETHER_DESIGN constant > wp_options.aether_active_design > 'luxury'

aether_active_design_dir()
  Returns: AETHER_FRONTEND_DIR . 'designs/' . $design . '/'
  Empty when design is 'luxury' (base tree IS luxury)

aether_resolve_design_path($relative_path)
  Pack-first: if pack file exists → pack file, else → base file
```

### 2.4 Section Registry

**File:** `frontend/views/registry.php`

```php
aether_register_section($id, [
  'template'     => 'components/section-foo.php',
  'adapter'      => 'adapters/adapter-foo.php',  // optional
  'adapter_args' => [],                           // optional
  'behavior'     => ['reveal' => true],           // optional
]);
```

Sections self-register in their own files. `aether_render_section()` resolves adapter data, normalizes ViewModel keys, then includes the section template.

### 2.5 Component Renderer

**File:** `frontend/views/renderer.php`

```php
aether_render_component($id, $data)
  → Reads manifest/components.php
  → Resolves template via aether_resolve_design_path() (pack-first)
  → Applies aether_component_data filter
  → include $template (component has $componentData in scope)
```

**Key rule:** Components NEVER call WordPress/WooCommerce functions. They receive `$componentData` from adapters.

### 2.6 Asset Pipeline

**File:** `frontend/views/assets.php`

For non-luxury designs (packs), `aether_design_enqueue_assets()` loads:
1. **Platform CDNs** (same handles as Luxury bridge):
   - Bootstrap 5.3.3 CSS + JS
   - Font Awesome 6.5.1 CSS
   - Swiper 11 CSS + JS
   - GSAP 3.12.5 + ScrollTrigger
2. **Platform contract JS** (base files):
   - `animations.js` (motion watchdog)
   - `main.js` (AJAX cart, forms, etc.)
   - `countdown.js`
3. **Pack assets** (from `manifest.json`):
   - `assets.css` array → `wp_enqueue_style()`
   - `assets.js` array → `wp_enqueue_script()` with deps

For Luxury (base tree), `aureon_aether_enqueue_assets()` in `inc/frontend.php` handles loading.

---

## 3. Ferm Living Design Pack — Hybrid Architecture

### 3.1 Component Framework Layer (UNUSED in practice)

The Ferm Living pack includes a complete component framework:

| Layer | Files | Purpose |
|-------|-------|---------|
| **Manifest** | `manifest.json` | Declares 10 component overrides, 6 custom sections, assets |
| **Composer** | `composer.php` | Hooks into adapter data filters, defines homepage section sequence |
| **Mapper** | `mapper/ferm-mapper.php` | Transforms canonical → Ferm presentation model |
| **Tokens** | `tokens.php` | Ferm-specific defaults (brand, hero, categories, products, rooms, footer) |
| **Components** | `components/shell/*.php`, `components/cards/*.php`, `components/product/*.php`, `components/content/*.php` | 10 shell/card/product/content overrides |
| **Sections** | `sections/section-*.php` | 6 Ferm-specific sections (hero, categories, editorial-split, bestsellers, room-grid, secondary-products) |
| **Assets** | `css/ferm.css`, `css/fonts.css`, `js/ferm.js` | Ferm-specific styling and JS |
| **Data** | `data/products.json`, `data/categories.json`, `data/navigation.json` | Demo/reference content |
| **Images** | `assets/` (categories, common, editorial, fonts, hero, products, rooms) | Ferm Living visual assets |

### 3.2 Complete Page Layer (ACTIVE — overrides component framework)

**File:** `aureon/theme/ferm-page.php`

When `fermliving` is the active design:
1. `aureon_ferm_template_include()` at priority 998 catches ALL non-WC requests
2. `ferm-page.php` reads a complete HTML file from the design pack
3. Opens `<!DOCTYPE html>`, adds `<head>` with `wp_head()`
4. Extracts `<body>` content from the Ferm HTML
5. Outputs the body, closes with `wp_footer()`

**Router mapping** (in `aureon_ferm_resolve_page()`):

| WordPress Route | HTML File |
|----------------|-----------|
| Homepage | `index.html` |
| Product (by slug) | `products/<slug>.html` |
| Product (fallback) | First available `products/*.html` |
| Shop / Product Archive | `collections/furniture.html` |
| Product Category | `collections/<slug>.html` |
| Contact | `pages/contact.html` |
| About / About Ferm Living | `pages/about-ferm-living.html` |
| Store Locator | `pages/store-locator.html` |
| Blog / Stories | `blogs/stories.html` |
| Search | `blogs/stories.html` |
| 404 | `pages/contact.html` (fallback) |
| Cart / Checkout / Account | PASSED THROUGH (not caught by Ferm router) |

### 3.3 The Conflict

When Ferm Living is active, both systems try to load assets:

1. **`aether_design_enqueue_assets()`** (priority 20) — loads CDN + pack assets via `wp_enqueue`
2. **The Ferm HTML file** — has its own `<link>` and `<script>` tags for CSS/JS
3. **WooCommerce** — injects its own CSS/JS via `wp_head()` / `wp_footer()`
4. **WordPress core** — admin bar, emoji scripts, etc.

**Result:** Duplicate CSS/JS, load order conflicts, meta tag overrides.

---

## 4. Template Hierarchy (Component Framework)

### 4.1 Theme Templates

```
front-page.php        → Homepage (section composition via aether_frontpage_sections)
page.php              → Generic static page
page-about.php        → About page
page-contact.php      → Contact page
page-faq.php          → FAQ page
page-styleguide.php   → Style guide
page-team.php         → Team page
page-coming-soon.php  → Coming soon
page-login.php        → Login
page-register.php     → Register
page-wishlist.php     → Wishlist
single.php            → Blog post
single-product.php    → WooCommerce product
archive.php           → Blog archive
archive-product.php   → WooCommerce shop/category
home.php              → Blog home
cart.php              → WooCommerce cart
checkout/             → WooCommerce checkout
myaccount/            → WooCommerce account
search.php            → Search results
404.php               → Not found
```

### 4.2 Page Template Routing (Ferm Complete Page)

```
ferm-page.php catches ALL non-WC requests via template_include (priority 998)
  → aureon_ferm_resolve_page() maps WP route to HTML file
  → Reads complete HTML, extracts <body>, wraps with wp_head/wp_footer
```

---

## 5. Adapter Architecture

23 adapter files in `frontend/adapters/`:

| Adapter | Purpose |
|---------|---------|
| `adapter-site.php` | Site name, URL, logo |
| `adapter-shell.php` | Shell data (preloader, fog, announcement) |
| `adapter-header.php` | Header navigation |
| `adapter-menu.php` | WP nav menus |
| `adapter-hero.php` | Hero slider data |
| `adapter-wc-products.php` | WooCommerce products |
| `adapter-wc-categories.php` | WooCommerce categories |
| `adapter-wc-filter.php` | Shop filter data |
| `adapter-product.php` | Single product data |
| `adapter-cart.php` | Cart data |
| `adapter-about.php` | About page data |
| `adapter-contact.php` | Contact page data |
| `adapter-blog.php` | Blog archive data |
| `adapter-article.php` | Single article data |
| `adapter-faq.php` | FAQ data |
| `adapter-team.php` | Team data |
| `adapter-testimonials.php` | Testimonials data |
| `adapter-auth.php` | Authentication data |
| `adapter-account.php` | Account data |
| `adapter-order.php` | Order data |
| `adapter-wishlist.php` | Wishlist data |
| `adapter-shop-hero.php` | Shop hero data |
| `adapter-options.php` | Options/settings data |

---

## 6. Bugs & Issues Found

### 6.1 `aether_pack_url()` is undefined

**Severity:** Fatal
**Files:** `designs/fermliving/tokens.php:15`, `designs/fermliving/composer.php:111,176`
**Impact:** PHP fatal error when Ferm Living pack is active

The function `aether_pack_url()` is called but never defined anywhere in the codebase. This function should return the content URL for the active design pack.

### 6.2 Dual architecture conflict

**Severity:** High
**Impact:** Component framework is entirely unused when Ferm Living is active

The Ferm Living pack has a complete component framework (composer, mapper, 10 component overrides, 6 sections) that is completely bypassed by `ferm-page.php`. The component framework code is dead weight.

### 6.3 Asset duplication

**Severity:** Medium
**Impact:** Duplicate CSS/JS, slower page loads

When in complete-page mode:
- `aether_design_enqueue_assets()` loads CDN + pack assets via `wp_enqueue`
- The Ferm HTML file has its own asset links
- WooCommerce injects its own assets
- All three overlap

### 6.4 Meta tag contamination

**Severity:** Low
**Impact:** SEO/social sharing broken

The Ferm HTML has its own meta tags (og:title, og:description, etc.), but WordPress overrides them via `wp_head()`.

---

## 7. Two Integration Paths (Decision Point)

### Path A: Complete Page Mode (current)
- Serve entire HTML files from `designs/fermliving/`
- Minimal PHP — just file reading + wp_head/wp_footer wrapping
- No dynamic WordPress data in the page content
- All 11 HTML pages (index, collections/*, products/*, pages/*, blogs/*) as static files
- **Pro:** Pixel-perfect to original design, zero PHP rendering
- **Con:** No dynamic WooCommerce data, duplicate assets, meta contamination

### Path B: Component Framework Mode (designed but unused)
- Use the adapter → ViewModel → Component → Section pipeline
- All content dynamic from WordPress/WooCommerce
- Shell components (header, footer, etc.) rendered by PHP
- **Pro:** Full dynamic content, no duplication, clean architecture
- **Con:** Requires all 22+ page templates to be rebuilt as PHP component compositions

### Path C: Hybrid (recommended)
- Use component framework for shell (header, footer, nav, cart)
- Use complete pages for content-heavy pages (homepage, collections, products)
- Clean up asset loading to avoid duplication
- Fix `aether_pack_url()` bug
- **Pro:** Best of both — dynamic shell + pixel-perfect content
- **Con:** More complex, needs careful asset management

---

## 8. Known-Good Reference: Phantom Theme (Shopify)

The `C:\Users\hamma\Downloads\phantom-theme` directory contains a **Shopify theme** (PHANTOM v2.2.0), NOT a WordPress/AUREON integration. It is:

- Built on Impulse v8.2.0 by Archetype Themes
- Shopify Online Store 2.0 architecture (JSON templates, Liquid sections)
- 56+ sections, 100+ snippets, 23 JS modules
- PH MOTION animation system
- Design token system with CSS custom properties
- 5 style presets (Default, Minimal, Editorial, Bold, Luxury)

This theme is **architecturally unrelated** to the AUREON/WordPress system. It cannot serve as a reference for WordPress frontend integration.

---

## 9. File Reference

### Core Architecture Files
- `C:\Users\hamma\Downloads\wordpress\aureon\theme\functions.php` — Theme bootstrap
- `C:\Users\hamma\Downloads\wordpress\aureon\theme\inc\frontend.php` — Frontend engine wiring
- `C:\Users\hamma\Downloads\wordpress\aureon\theme\ferm-page.php` — Complete page template
- `C:\Users\hamma\Downloads\wordpress\frontend\views\loader.php` — Engine boot
- `C:\Users\hamma\Downloads\wordpress\frontend\views\design.php` — Pack resolution
- `C:\Users\hamma\Downloads\wordpress\frontend\views\composer.php` — Shell composition
- `C:\Users\hamma\Downloads\wordpress\frontend\views\renderer.php` — Component/section renderer
- `C:\Users\hamma\Downloads\wordpress\frontend\views\registry.php` — Section registry
- `C:\Users\hamma\Downloads\wordpress\frontend\views\assets.php` — Asset pipeline
- `C:\Users\hamma\Downloads\wordpress\frontend\views\viewmodel.php` — Data normalization
- `C:\Users\hamma\Downloads\wordpress\frontend\manifest\components.php` — Component manifest
- `C:\Users\hamma\Downloads\wordpress\frontend\tokens\tokens.php` — Design token defaults

### Ferm Living Pack
- `C:\Users\hamma\Downloads\wordpress\frontend\designs\fermliving\manifest.json` — Pack manifest
- `C:\Users\hamma\Downloads\wordpress\frontend\designs\fermliving\composer.php` — Pack composer
- `C:\Users\hamma\Downloads\wordpress\frontend\designs\fermliving\tokens.php` — Pack tokens
- `C:\Users\hamma\Downloads\wordpress\frontend\designs\fermliving\mapper\ferm-mapper.php` — Data mapper
- `C:\Users\hamma\Downloads\wordpress\frontend\designs\fermliving\css/ferm.css` — Pack styles
- `C:\Users\hamma\Downloads\wordpress\frontend\designs\fermliving\js/ferm.js` — Pack scripts
- `C:\Users\hamma\Downloads\wordpress\frontend\designs\fermliving\data/` — Demo data (products, categories, navigation)

### Theme Shell
- `C:\Users\hamma\Downloads\wordpress\aureon\theme\header.php` — Document open + wp_head + shell
- `C:\Users\hamma\Downloads\wordpress\aureon\theme\footer.php` — Shell close + wp_footer
- `C:\Users\hamma\Downloads\wordpress\aureon\theme\front-page.php` — Homepage section composition

### Documentation
- `C:\Users\hamma\Downloads\wordpress\frontend\FRONTEND_ARCHITECTURE_REPORT.md`
- `C:\Users\hamma\Downloads\wordpress\frontend\MASTER_FRONTEND_IMPLEMENTATION_PLAN.md`
