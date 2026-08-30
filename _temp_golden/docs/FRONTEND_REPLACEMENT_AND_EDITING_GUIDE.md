# HOW TO REPLACE OR EDIT THE FRONTEND WITHOUT BREAKING ANYTHING

> **Version:** 1.0.0 · **Date:** 2026-08-29 · **Status:** AUTHORITATIVE
> **Scope:** Complete guide for safely modifying the AUREON/AETHER frontend layer
> **Rule:** Read this document end-to-end before making ANY frontend change.

---

## Table of Contents

1. [Architecture Overview](#1-architecture-overview)
2. [The Golden Rule](#2-the-golden-rule)
3. [Safe Editing Zones](#3-safe-editing-zones)
4. [Forbidden Zones](#4-forbidden-zones)
5. [How to Replace the Design Pack](#5-how-to-replace-the-design-pack)
6. [How to Edit Components](#6-how-to-edit-components)
7. [How to Edit Sections](#7-how-to-edit-sections)
8. [How to Swap Fonts](#8-how-to-swap-fonts)
9. [How to Modify CSS](#9-how-to-modify-css)
10. [How to Modify JavaScript](#10-how-to-modify-javascript)
11. [Cart and Bridge Layer](#11-cart-and-bridge-layer)
12. [Complete-Page Mode (Frozen Pages)](#12-complete-page-mode-frozen-pages)
13. [Data Flow Reference](#13-data-flow-reference)
14. [Testing Checklist](#14-testing-checklist)
15. [Rollback Strategy](#15-rollback-strategy)
16. [Common Operations Cookbook](#16-common-operations-cookbook)

---

## 1. Architecture Overview

### 1.1 The Six-Layer Data Flow

```
WordPress Core + WooCommerce + Customizer
        ↓
   23 Adapters (ONLY WP/WC touchpoint)
        ↓
   Normalized data arrays
        ↓
   ViewModels (data normalization)
        ↓
   Renderer → Composer → 53 Components + 26 Sections
        ↓
   Design Pack (presentation: HTML/CSS/JS)
```

**Every page request flows through all six layers.** The adapters are the ONLY layer that touches WordPress/WooCommerce functions. Components and templates receive pre-normalized data arrays — they never call WP/WC directly.

### 1.2 Engine Kernel Files

| File | Lines | Role |
|------|-------|------|
| `frontend/views/loader.php` | 63 | Entry point, wiring |
| `frontend/views/design.php` | 197 | Pack resolution, manifest |
| `frontend/views/registry.php` | 50 | Section registration |
| `frontend/views/renderer.php` | 178 | Component + section rendering |
| `frontend/views/viewmodel.php` | 134 | Data normalization |
| `frontend/views/assets.php` | 140 | Asset pipeline |
| `frontend/views/composer.php` | 72 | Shell composition |
| `frontend/tokens/tokens.php` | 607 | Default option values |
| `frontend/manifest/components.php` | 78 | Component template map |

### 1.3 Design Resolution Chain

1. `aether_active_design()` → resolves active slug: `AETHER_DESIGN` constant > `aether_active_design` option > `'luxury'`
2. `aether_active_design_dir()` → returns pack directory path or `''` for luxury
3. `aether_resolve_design_path($relative)` → checks pack directory first, falls back to engine tree

### 1.4 Rendering Flow

```
aether_render_section($id, $data)
  → registry lookup → adapter invocation → data normalization
  → aether_resolve_design_path() → template include

aether_render_component($id, $data)
  → manifest lookup → aether_resolve_design_path() → template include
```

---

## 2. The Golden Rule

### NEVER edit these directories:

```
aureon/theme/**          ← Core theme — updates will overwrite your changes
aureon/plugin/**         ← WooCommerce extensions — updates will overwrite your changes
frontend/views/*         ← Engine kernel — shared across all packs
frontend/tokens/*        ← Default tokens — shared across all packs
frontend/manifest/*      ← Component registry — shared across all packs
frontend/adapters/*      ← WP/WC boundary — only edit if changing data contracts
```

### ONLY edit these directories:

```
frontend/designs/{your-pack}/     ← YOUR design pack (everything is safe here)
frontend/components/              ← Base component templates (pack shadows these)
frontend/sections/                ← Base section templates (pack shadows these)
```

### Why this rule exists:

The theme and plugin are versioned and updatable. The engine kernel is shared infrastructure. Your design pack is the ONLY isolated, replaceable, swappable layer. If you edit `aureon/theme/`, those changes die on the next theme update.

---

## 3. Safe Editing Zones

### 3.1 Design Pack Directory Structure

```
frontend/designs/{your-pack}/
  ├── manifest.json              ← Pack descriptor (name, version, assets)
  ├── tokens.php                 ← Option defaults override
  ├── composer.php               ← Filter hooks for composition
  ├── css/
  │   ├── fonts.css              ← Font imports
  │   └── {pack}.css             ← Pack CSS (your main stylesheet)
  ├── js/
  │   └── {pack}.js              ← Pack JS (your main script)
  ├── components/                ← Component template overrides
  │   ├── cards/
  │   │   └── product.php        ← Overrides base components/cards/product.php
  │   ├── product/
  │   │   ├── info.php
  │   │   └── gallery.php
  │   └── shell/
  │       ├── header.php
  │       ├── footer.php
  │       └── mobile-chrome.php
  ├── sections/                  ← Section template overrides
  │   ├── hero.php
  │   ├── section-cart.php
  │   └── ...
  └── assets/                    ← Pack-specific images, fonts, icons
```

### 3.2 What Each Pack File Does

| File | Purpose | Safe to Edit? |
|------|---------|---------------|
| `manifest.json` | Declares pack name, version, CSS/JS assets | YES — this is YOUR file |
| `tokens.php` | Overrides default design tokens (colors, spacing, fonts) | YES — this is YOUR file |
| `composer.php` | Hooks into section ordering, adds custom sections | YES — this is YOUR file |
| `css/{pack}.css` | All pack-specific styles | YES — this is YOUR file |
| `js/{pack}.js` | All pack-specific JavaScript | YES — this is YOUR file |
| `components/**/*.php` | Shadow base component templates | YES — these are YOUR overrides |
| `sections/**/*.php` | Shadow base section templates | YES — these are YOUR overrides |
| `assets/**` | Images, fonts, icons | YES — these are YOUR assets |

### 3.3 Shadowing Mechanism

When the engine renders a component or section, it calls `aether_resolve_design_path($relative)`. This function:

1. Checks `frontend/designs/{active-pack}/{$relative}` first
2. If file exists there → uses the pack version
3. If not → falls back to `frontend/{$relative}` (base engine)

**You don't need to register overrides.** Just place a file with the same relative path in your pack directory, and it shadows the base automatically.

Example: To override `frontend/components/cards/product.php`, create `frontend/designs/{your-pack}/components/cards/product.php`.

---

## 4. Forbidden Zones

### 4.1 Files That Will Break Everything

| File | Why Forbidden |
|------|---------------|
| `aureon/theme/functions.php` | Bootstrap — loads everything |
| `aureon/theme/inc/frontend.php` | AETHER boot — loads engine kernel |
| `aureon/theme/inc/aether-tokens.php` | CSS custom properties emission |
| `aureon/theme/inc/aether-ajax.php` | AJAX handlers (wishlist, contact, newsletter) |
| `aureon/theme/inc/aether-cart.php` | WC cart fragment |
| `aureon/theme/inc/aether-security.php` | CSP headers, security |
| `aureon/theme/inc/aether-seo.php` | OG tags, Schema.org |
| `aureon/theme/inc/aether-newsletter.php` | Newsletter DB + admin |
| `aureon/theme/inc/aether-analytics.php` | GA4 dataLayer |
| `aureon/theme/inc/aether-performance.php` | Resource hints |
| `aureon/theme/inc/customizer.php` | Full Customizer (1575 lines) |
| `aureon/theme/inc/css-output.php` | Dynamic CSS (1340 lines) |
| `aureon/theme/header.php` | Shell header — calls `aether_compose_header()` |
| `aureon/theme/footer.php` | Shell footer — calls `aether_compose_footer()` |
| `frontend/views/*.php` | Engine kernel — shared across all packs |
| `frontend/tokens/tokens.php` | Default token values |
| `frontend/manifest/components.php` | Component registry |
| `frontend/adapters/*.php` | WP/WC boundary layer |

### 4.2 Why Each Is Forbidden

- **Theme files**: Updated by theme developer. Your changes get overwritten.
- **Plugin files**: WooCommerce extensions. Updates overwrite changes.
- **Engine kernel**: Shared infrastructure. Changing it affects ALL packs.
- **Adapters**: The data contract boundary. Changing adapters changes what data components receive — breakage cascades to every component.

### 4.3 The One Exception

You CAN edit `frontend/adapters/*.php` if you are:
- Adding a new data field to an existing adapter
- Creating a brand new adapter for a new section

But this is an advanced operation. Only do it if you understand the full data flow.

---

## 5. How to Replace the Design Pack

### 5.1 Step-by-Step Pack Creation

**Step 1: Create the pack directory**

```bash
mkdir -p frontend/designs/{your-pack}/css
mkdir -p frontend/designs/{your-pack}/js
mkdir -p frontend/designs/{your-pack}/components
mkdir -p frontend/designs/{your-pack}/sections
mkdir -p frontend/designs/{your-pack}/assets
```

**Step 2: Create `manifest.json`**

```json
{
  "name": "{your-pack}",
  "version": "1.0.0",
  "description": "Your custom design pack",
  "author": "Your Name",
  "assets": {
    "css": ["css/{your-pack}.css"],
    "js": ["js/{your-pack}.js"]
  }
}
```

**Step 3: Create `tokens.php`**

```php
<?php
// Override default design tokens
// These map to CSS custom properties
return [
    'colors' => [
        'primary'    => '#000000',
        'secondary'  => '#ffffff',
        'accent'     => '#c8a97e',
        'background' => '#f8f6f3',
        'text'       => '#1a1a1a',
        'muted'      => '#6b7280',
    ],
    'typography' => [
        'heading_font' => '"Your Heading Font", serif',
        'body_font'    => '"Your Body Font", sans-serif',
        'base_size'    => '16px',
        'scale'        => '1.25',
    ],
    'spacing' => [
        'section_gap' => '6rem',
        'container_width' => '1280px',
    ],
];
```

**Step 4: Create `composer.php`**

```php
<?php
// Control section ordering on each page type
add_filter('aether_frontpage_sections', function($sections) {
    return [
        'hero',
        'categories',
        'bestsellers',
        'editorial',
        'newsletter',
    ];
});

// Add custom sections
add_action('aether_register_sections', function() {
    aether_register_section('editorial', 'adapter-options.php');
    aether_register_section('rooms', 'adapter-options.php');
});
```

**Step 5: Create your CSS**

```css
/* frontend/designs/{your-pack}/css/{your-pack}.css */

/* Import fonts */
@import url('fonts.css');

/* Use CSS custom properties from tokens */
:root {
    --color-primary: #000000;
    --color-secondary: #ffffff;
    /* These are set by tokens.php, but you can override here too */
}

/* Your styles */
.site-header { /* ... */ }
.product-card { /* ... */ }
```

**Step 6: Create your JS**

```javascript
// frontend/designs/{your-pack}/js/{your-pack}.js

// Your pack JavaScript
// DO NOT call WP/WC functions directly
// DO NOT modify global AETHER objects
// Safe: DOM manipulation, animations, UI interactions
```

**Step 7: Shadow components you want to customize**

Copy the base component template to your pack directory and modify:

```bash
# Example: override the product card
cp frontend/components/cards/product.php frontend/designs/{your-pack}/components/cards/product.php
# Edit the copy — the base remains untouched
```

**Step 8: Activate your pack**

In WordPress admin → Customizer → AETHER Design, or set the `aether_active_design` option to your pack slug.

### 5.2 Pack Swappability

Design packs are completely swappable. To switch:

1. Set `aether_active_design` option to new pack slug
2. Clear any object cache
3. The engine resolves all templates from the new pack

No database changes, no code changes, no redeployment. The pack is just a directory of files.

---

## 6. How to Edit Components

### 6.1 Component Registry

All 53 components are registered in `frontend/manifest/components.php`. Each entry maps a component ID to its base template path.

### 6.2 Component Data Contract

Components receive pre-normalized data arrays from adapters. They NEVER call WP/WC functions.

| Component | Data Source | Key Fields |
|-----------|------------|------------|
| `cards/product` | `adapter-wc-products.php` | `name`, `price`, `image`, `url`, `badge`, `variants` |
| `product/info` | `adapter-product.php` | `name`, `price`, `description`, `variants`, `add_to_cart_url` |
| `product/gallery` | `adapter-product.php` | `main_image`, `gallery_images`, `thumbnails` |
| `shell/header` | `adapter-shell.php` | `logo`, `nav_items`, `cart_count`, `search_url` |
| `shell/footer` | `adapter-shell.php` | `footer_links`, `newsletter_url`, `copyright` |
| `shell/mobile-chrome` | `adapter-shell.php` | `nav_items`, `cart_count`, `account_url` |
| `cart/items` | `adapter-cart.php` | `items`, `subtotal`, `shipping`, `total` |
| `cart/summary` | `adapter-cart.php` | `subtotal`, `shipping`, `total`, `checkout_url` |

### 6.3 How to Override a Component

1. Find the base template: `frontend/components/{path}/{name}.php`
2. Create the same path in your pack: `frontend/designs/{your-pack}/components/{path}/{name}.php`
3. Copy the base template to your pack directory
4. Edit the copy
5. The engine automatically uses your version (shadowing)

### 6.4 Component Template Pattern

Every component template follows this pattern:

```php
<?php
// $data is the normalized data array from the adapter
// NEVER call WP/WC functions here
// ONLY use $data fields and escape output

$name    = esc_html($data['name'] ?? '');
$price   = $data['price'] ?? '';
$image   = esc_url($data['image'] ?? '');
$url     = esc_url($data['url'] ?? '');
$badge   = esc_html($data['badge'] ?? '');
?>

<div class="product-card">
    <?php if ($image): ?>
        <img src="<?php echo $image; ?>" alt="<?php echo $name; ?>" loading="lazy">
    <?php endif; ?>
    <?php if ($badge): ?>
        <span class="product-card__badge"><?php echo $badge; ?></span>
    <?php endif; ?>
    <h3 class="product-card__title">
        <a href="<?php echo $url; ?>"><?php echo $name; ?></a>
    </h3>
    <div class="product-card__price"><?php echo $price; ?></div>
</div>
```

### 6.5 Adding a New Component

1. Create the template: `frontend/components/{path}/{name}.php`
2. Register in `frontend/manifest/components.php`:
   ```php
   'your-component' => 'components/{path}/{name}.php',
   ```
3. Use in a section: `aether_render_component('your-component', $data)`

---

## 7. How to Edit Sections

### 7.1 Section Registry

28 sections are registered in `frontend/views/registry.php`. Each maps a section ID to its adapter and template.

### 7.2 Section Template Pattern

```php
<?php
// $data comes from the adapter
// Render the section HTML
?>

<section class="your-section">
    <div class="container">
        <?php foreach ($data['items'] as $item): ?>
            <?php aether_render_component('cards/product', $item); ?>
        <?php endforeach; ?>
    </div>
</section>
```

### 7.3 How to Override a Section

Same as components — create the same path in your pack directory:

```
frontend/designs/{your-pack}/sections/{section-name}.php
```

### 7.4 How to Reorder Sections

In your pack's `composer.php`:

```php
add_filter('aether_frontpage_sections', function($sections) {
    return [
        'hero',           // 1st
        'bestsellers',    // 2nd
        'categories',     // 3rd
        'newsletter',     // 4th
    ];
});
```

### 7.5 How to Add a Custom Section

1. Create a section template: `frontend/sections/section-custom.php`
2. Create an adapter: `frontend/adapters/adapter-custom.php`
3. Register in `registry.php` (or via your pack's `composer.php`)
4. Add to your section ordering filter

---

## 8. How to Swap Fonts

### 8.1 Font Loading Mechanism

Fonts are loaded via CSS `@font-face` declarations in your pack's CSS file. The engine does NOT load fonts — that's entirely pack-owned.

### 8.2 Steps to Change Fonts

1. **Choose fonts** and ensure licensing (see Section 8.3)
2. **Add font files** to `frontend/designs/{your-pack}/assets/fonts/`
3. **Create `css/fonts.css`**:
   ```css
   @font-face {
       font-family: 'Your Heading Font';
       src: url('../assets/fonts/YourHeadingFont-Regular.woff2') format('woff2');
       font-weight: 400;
       font-style: normal;
       font-display: swap;
   }
   ```
4. **Import in your pack CSS**:
   ```css
   @import url('fonts.css');
   ```
5. **Update tokens.php**:
   ```php
   'typography' => [
       'heading_font' => '"Your Heading Font", serif',
       'body_font'    => '"Your Body Font", sans-serif',
   ],
   ```

### 8.3 Font Licensing

| Font Type | License Required? | Notes |
|-----------|------------------|-------|
| Google Fonts | No (open source) | Apache 2.0 or OFL |
| Self-hosted commercial | Yes | Must purchase web font license |
| System fonts | No | Platform-specific availability |
| Variable fonts | Depends on source | Check individual license |

**Current pack fonts (Ferm Living):**
- CanelaText — commercial license required
- KHTeka — commercial license required

### 8.4 Font Fallback Stack

Always provide a fallback stack in tokens.php:

```php
'heading_font' => '"Your Font", Georgia, "Times New Roman", serif',
'body_font'    => '"Your Font", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif',
```

---

## 9. How to Modify CSS

### 9.1 CSS Architecture

The CSS loading order is:

1. **Platform CDN** — Bootstrap 5.3.3, Font Awesome 6.5.1 (loaded by engine)
2. **Platform contract JS CSS** — animations, transitions (loaded by engine)
3. **Token CSS** — `:root` custom properties from `tokens.php` (loaded by engine)
4. **Pack CSS** — Your `{pack}.css` (loaded from `manifest.json`)
5. **Pack font CSS** — Your `fonts.css` (loaded from `manifest.json`)

### 9.2 CSS Custom Properties (Tokens)

Tokens generate CSS custom properties on `:root`. Override them in your pack CSS:

```css
:root {
    --color-primary: #000000;
    --color-secondary: #ffffff;
    --spacing-section: 6rem;
    --font-heading: "Your Font", serif;
    --font-body: "Your Font", sans-serif;
}
```

### 9.3 Safe CSS Modifications

| What | Where | Safe? |
|------|-------|-------|
| Override token values | Pack CSS `:root` | YES |
| Add new utility classes | Pack CSS | YES |
| Modify component styles | Pack CSS | YES |
| Add responsive breakpoints | Pack CSS | YES |
| Override platform CSS | Pack CSS (with higher specificity) | YES — but fragile |
| Modify engine CSS | `frontend/views/assets.php` | NO |
| Modify platform CDN CSS | N/A | NO |

### 9.4 Tailwind Utilities

If your pack uses Tailwind:

1. Build Tailwind CSS with your config
2. Output to `frontend/designs/{your-pack}/css/{pack}.css`
3. Include in `manifest.json`

**Note:** Platform Bootstrap CSS loads first. Your Tailwind utilities may conflict. Use higher specificity or namespace your classes.

### 9.5 CSS Specificity Strategy

```css
/* Low specificity — easily overridden */
.product-card { }

/* Medium specificity — component scope */
.product-card .product-card__title { }

/* High specificity — pack override */
:root .product-card .product-card__title { }

/* Highest — inline (avoid) */
.product-card .product-card__title { color: red !important; }
```

**Recommendation:** Use component-scoped classes. Avoid `!important`.

---

## 10. How to Modify JavaScript

### 10.1 JS Loading Order

1. **Platform CDN** — GSAP 3.12.5, ScrollTrigger (loaded by engine)
2. **Platform contract JS** — `animations.js`, `main.js`, `countdown.js` (loaded by engine)
3. **Pack JS** — Your `{pack}.js` (loaded from `manifest.json`)

### 10.2 What Pack JS Can Do

| Operation | Safe? | Notes |
|-----------|-------|-------|
| DOM manipulation | YES | Standard browser API |
| CSS animations/transitions | YES | Preferred over JS animations |
| Event listeners | YES | Standard browser API |
| Fetch API calls | YES | To your own endpoints |
| Swiper/Embla carousel init | YES | If pack uses these libs |
| PhotoSwipe lightbox init | YES | If pack uses this lib |
| Read `AETHER` global | YES | Read-only access to design data |
| Modify `AETHER` global | NO | Will break engine behavior |
| Call WP/WC AJAX | CAUTION | Use existing endpoints only |
| Modify jQuery | NO | Platform dependency |
| Override GSAP | NO | Platform dependency |
| Modify `main.js` | NO | Platform contract |

### 10.3 Safe JS Pattern

```javascript
// frontend/designs/{your-pack}/js/{pack}.js

(function() {
    'use strict';

    // Your pack JavaScript
    // Safe: DOM manipulation, animations, UI interactions

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize carousels, modals, etc.
    });
})();
```

### 10.4 Adding External Libraries

If your pack needs a library not in the platform:

1. Add to `manifest.json`:
   ```json
   {
       "assets": {
           "js": ["js/{pack}.js"],
           "dependencies": ["platform-swiper"]
       }
   }
   ```
2. Or load via CDN in your pack JS:
   ```javascript
   const script = document.createElement('script');
   script.src = 'https://cdn.example.com/lib.min.js';
   document.head.appendChild(script);
   ```

---

## 11. Cart and Bridge Layer

### 11.1 The Problem

Ferm Living's original JS calls Shopify cart APIs:
- `POST /cart/add.js`
- `POST /cart/change.js`
- `GET /cart.js`

WooCommerce uses different endpoints.

### 11.2 The Solution: 4 Endpoint Shims

These PHP files in `aureon/theme/inc/` create Shopify-compatible endpoints that bridge to WooCommerce:

| Shim | Shopify API | WooCommerce Backend |
|------|-------------|-------------------|
| `aether-cart.php` | `GET /cart.js` | `WC()->cart` → JSON |
| `aether-cart.php` | `POST /cart/add.js` | `?add-to-cart=` or AJAX |
| `aether-cart.php` | `POST /cart/change.js` | WC update-cart |
| `aether-cart.php` | `POST /cart/clear.js` | `WC()->cart->empty_cart()` |

### 11.3 Bridge.js Constraints

The bridge file (`frontend/designs/{your-pack}/js/bridge.js`) should be ≤150 lines and handle:

- Cart count sync (header badge update)
- Wishlist button state
- Any Shopify→WC API translation

**DO NOT** put business logic in bridge.js. It's a thin translation layer.

### 11.4 What NOT to Touch

| File | Why |
|------|-----|
| `aureon/theme/inc/aether-cart.php` | WC cart fragment — handles server-side cart |
| `aureon/theme/inc/aether-ajax.php` | AJAX handlers — nonce verification, rate limiting |
| WC cart JS | Plugin-owned — handles cart form submission |

---

## 12. Complete-Page Mode (Frozen Pages)

### 12.1 What It Is

Complete-page mode loads a frozen HTML file (e.g., `ferm-page.php`) that bypasses the AETHER shell entirely. Used for pages that need pixel-perfect parity with the original client site.

### 12.2 How It Works

```
WordPress request
    → ferm-page.php
    → reads frozen HTML file
    → extracts <body> content
    → wraps with wp_head() / wp_footer()
    → outputs complete page
```

### 12.3 The Asset Contamination Problem

When `ferm-page.php` calls `wp_head()`, the WordPress asset pipeline injects:
- Platform CDN CSS (Bootstrap, Font Awesome, Swiper)
- Platform contract JS (animations.js, main.js, GSAP)
- Token CSS (`:root` custom properties)
- Pack CSS/JS (correct, but depends on suppressed platform handles)
- WooCommerce assets (required for commerce)
- WordPress assets (admin bar, jQuery)

**Result:** Two competing CSS/JS ecosystems render in the same browser context.

### 12.4 The Fix

Suppress platform assets in complete-page mode. Let only the pack's own CSS/JS load. This is handled by `aureon_suppress_theme_output()` at priority 1000.

### 12.5 When to Use Complete-Page Mode

Use for:
- Pages requiring pixel-perfect parity with original client site
- Landing pages with complex custom layouts
- Pages with unique交互 patterns not supported by AETHER components

Do NOT use for:
- Standard WooCommerce pages (shop, product, cart, checkout)
- Pages that need dynamic AETHER sections
- Pages that will be edited by non-technical users

---

## 13. Data Flow Reference

### 13.1 Homepage

| Order | Section | Adapter | Component |
|-------|---------|---------|-----------|
| 1 | `hero` | `adapter-hero.php` | `hero/slider` |
| 2 | `categories` | `adapter-wc-categories.php` | `cards/category` |
| 3 | `bestsellers` | `adapter-wc-products.php` | `cards/product` |
| 4 | `newsletter` | `adapter-options.php` | `newsletter/form` |

### 13.2 Shop Page

| Order | Section | Adapter | Component |
|-------|---------|---------|-----------|
| 1 | `shop-hero` | `adapter-shop-hero.php` | `hero/page-title` |
| 2 | `shop-filter` | `adapter-wc-filter.php` | `filter/bar` |
| 3 | `shop-grid` | `adapter-wc-products.php` | `cards/product` |
| 4 | `newsletter` | `adapter-options.php` | `newsletter/form` |

### 13.3 Product Page

| Order | Section | Adapter | Component |
|-------|---------|---------|-----------|
| 1 | `product` | `adapter-product.php` | `product/info`, `product/gallery`, `product/related` |
| 2 | `newsletter` | `adapter-options.php` | `newsletter/form` |

### 13.4 Cart Page

| Order | Section | Adapter | Component |
|-------|---------|---------|-----------|
| 1 | `cart` | `adapter-cart.php` | `cart/items`, `cart/summary` |
| 2 | `newsletter` | `adapter-options.php` | `newsletter/form` |

### 13.5 Checkout

| Order | Section | Adapter | Component |
|-------|---------|---------|-----------|
| 1 | `checkout` | `adapter-cart.php` | WC native checkout (wrapped in shell) |

### 13.6 My Account

| Endpoint | Adapter | Component |
|----------|---------|-----------|
| Dashboard | `adapter-account.php` | `account/profile` |
| Orders | `adapter-account.php` | `account/orders` |
| Other | WC native | WC templates (wrapped in shell) |

### 13.7 Blog

| Order | Section | Adapter | Component |
|-------|---------|---------|-----------|
| 1 | `blog-grid` | `adapter-blog.php` | `cards/blog` |
| 2 | `newsletter` | `adapter-options.php` | `newsletter/form` |

---

## 14. Testing Checklist

### 14.1 Visual Regression

- [ ] Homepage renders correctly at 1440px, 1024px, 768px, 390px
- [ ] Shop page renders correctly at all breakpoints
- [ ] Product page renders correctly at all breakpoints
- [ ] Cart page renders correctly at all breakpoints
- [ ] All images load correctly
- [ ] All fonts load correctly
- [ ] No CSS conflicts or specificity issues
- [ ] No JS errors in console

### 14.2 WooCommerce Flows

- [ ] Add to cart works (simple product)
- [ ] Add to cart works (variable product)
- [ ] Cart update quantity works
- [ ] Cart remove item works
- [ ] Checkout flow completes
- [ ] Order confirmation displays correctly
- [ ] My Account login works
- [ ] My Account orders list displays
- [ ] Wishlist add/remove works
- [ ] Product search returns results

### 14.3 Responsive

- [ ] Mobile menu opens/closes
- [ ] Mobile cart count updates
- [ ] Mobile product gallery works
- [ ] Touch gestures work (swipe, pinch)
- [ ] No horizontal scroll on mobile
- [ ] No overlapping elements on mobile

### 14.4 Accessibility

- [ ] All images have alt text
- [ ] All interactive elements are keyboard accessible
- [ ] Focus states are visible
- [ ] Color contrast meets WCAG AA
- [ ] Screen reader tested
- [ ] ARIA labels present where needed

### 14.5 Performance

- [ ] No render-blocking resources
- [ ] Images use lazy loading
- [ ] Fonts use `font-display: swap`
- [ ] CSS is minified
- [ ] JS is minified
- [ ] No duplicate resources loaded

---

## 15. Rollback Strategy

### 15.1 Git-Based Rollback

Every change is in git. To rollback:

```bash
# See what changed
git status
git diff

# Rollback specific file
git checkout HEAD -- frontend/designs/{your-pack}/css/{pack}.css

# Rollback last commit
git revert HEAD

# Rollback to specific commit
git revert <commit-hash>
```

### 15.2 Pack Swappability

To switch packs instantly:

1. Change `aether_active_design` option to new pack slug
2. Clear object cache
3. Done — no code changes, no redeployment

### 15.3 Zero-Downtime Switch

The pack switch is atomic:
- Old pack files remain on disk
- New pack files are already in place
- Only the option value changes
- Next request uses new pack

### 15.4 Backup Strategy

Before any major change:

```bash
# Create backup branch
git checkout -b backup/pre-change-$(date +%Y%m%d)

# Make changes on main
git checkout main
# ... make changes ...

# If something breaks
git checkout backup/pre-change-$(date +%Y%m%d)
```

---

## 16. Common Operations Cookbook

### 16.1 Change the Primary Color

1. Edit `frontend/designs/{your-pack}/tokens.php`
2. Update `'primary' => '#new-color'`
3. Or override in pack CSS:
   ```css
   :root { --color-primary: #new-color; }
   ```

### 16.2 Change the Logo

1. Add new logo to `frontend/designs/{your-pack}/assets/`
2. Edit `frontend/designs/{your-pack}/components/shell/header.php`
3. Update the `<img>` src to point to your new logo

### 16.3 Add a New Homepage Section

1. Create section template: `frontend/sections/section-custom.php`
2. Create adapter: `frontend/adapters/adapter-custom.php`
3. Register in pack `composer.php`:
   ```php
   add_filter('aether_frontpage_sections', function($sections) {
       $sections[] = 'custom';
       return $sections;
   });
   ```

### 16.4 Modify the Product Card

1. Copy: `frontend/components/cards/product.php` → `frontend/designs/{your-pack}/components/cards/product.php`
2. Edit the copy
3. The engine uses your version automatically

### 16.5 Add a Custom Font

1. Add font files to `frontend/designs/{your-pack}/assets/fonts/`
2. Create `frontend/designs/{your-pack}/css/fonts.css` with `@font-face` declarations
3. Import in pack CSS: `@import url('fonts.css');`
4. Update `tokens.php` with new font family

### 16.6 Change Section Order on Homepage

Edit `frontend/designs/{your-pack}/composer.php`:

```php
add_filter('aether_frontpage_sections', function($sections) {
    return ['hero', 'bestsellers', 'categories', 'newsletter'];
});
```

### 16.7 Add a Wishlist Button to Product Cards

1. Edit `frontend/designs/{your-pack}/components/cards/product.php`
2. Add wishlist button HTML
3. Use `data-product-id="<?php echo esc_attr($data['id']); ?>"` for the product ID
4. The existing wishlist AJAX handler (`aether-ajax.php`) handles the backend

### 16.8 Modify the Cart Page

1. Copy: `frontend/sections/section-cart.php` → `frontend/designs/{your-pack}/sections/section-cart.php`
2. Edit the copy
3. Cart data comes from `adapter-cart.php` — check the data contract first

### 16.9 Add Google Analytics Events

Edit `frontend/designs/{your-pack}/js/{pack}.js`:

```javascript
// Track product card clicks
document.querySelectorAll('.product-card__title a').forEach(function(link) {
    link.addEventListener('click', function() {
        gtag('event', 'select_item', {
            items: [{ item_name: this.textContent }]
        });
    });
});
```

### 16.10 Create a Custom Page Template

1. Create page template: `aureon/theme/page-custom.php`
2. Add AETHER section calls:
   ```php
   <?php get_header(); ?>
   <?php aether_render_section('custom', []); ?>
   <?php get_footer(); ?>
   ```
3. Create the section and adapter as described in Section 7.5

---

## Appendix A: File Path Quick Reference

### Safe to Edit (Your Pack)

```
frontend/designs/{your-pack}/manifest.json
frontend/designs/{your-pack}/tokens.php
frontend/designs/{your-pack}/composer.php
frontend/designs/{your-pack}/css/*.css
frontend/designs/{your-pack}/js/*.js
frontend/designs/{your-pack}/components/**/*.php
frontend/designs/{your-pack}/sections/**/*.php
frontend/designs/{your-pack}/assets/**
```

### Edit with Caution (Advanced)

```
frontend/adapters/*.php        (data contract boundary)
frontend/components/**/*.php   (base templates — pack shadows these)
frontend/sections/**/*..php    (base templates — pack shadows these)
```

### Never Edit

```
aureon/theme/**
aureon/plugin/**
frontend/views/**
frontend/tokens/tokens.php
frontend/manifest/components.php
```

---

## Appendix B: Adapter Data Contracts

### adapter-wc-products.php

```php
// Returns array of products
[
    'id'          => int,       // Product ID
    'name'        => string,    // Product name
    'price'       => string,    // Formatted price HTML
    'image'       => string,    // Image URL
    'url'         => string,    // Product permalink
    'badge'       => string,    // 'Sale', 'New', 'Featured', or ''
    'variants'    => array,     // Available variations
    'rating'      => float,     // Average rating
    'review_count'=> int,       // Number of reviews
]
```

### adapter-product.php

```php
// Returns single product data
[
    'id'                => int,
    'name'              => string,
    'price'             => string,    // Formatted price HTML
    'description'       => string,    // Full description HTML
    'short_description' => string,
    'images'            => array,     // [{url, alt, id}]
    'variants'          => array,     // [{id, name, options, available}]
    'attributes'        => array,     // [{name, options}]
    'add_to_cart_url'   => string,
    'gallery'           => array,     // Gallery image IDs
    'reviews'           => array,     // [{author, content, rating, date}]
    'rating'            => float,
    'review_count'      => int,
    'related'           => array,     // Related product IDs
]
```

### adapter-cart.php

```php
// Returns cart data
[
    'items'     => [
        [
            'key'       => string,    // Cart item key
            'id'        => int,       // Product ID
            'name'      => string,
            'price'     => string,    // Formatted price
            'image'     => string,
            'quantity'  => int,
            'subtotal'  => string,    // Formatted subtotal
            'variants'  => string,    // Variant info text
            'remove_url'=> string,
        ],
    ],
    'subtotal'  => string,
    'shipping'  => string,
    'total'     => string,
    'count'     => int,
    'is_empty'  => bool,
]
```

### adapter-shell.php

```php
// Returns shell/header/footer data
[
    'logo'        => string,    // Logo URL
    'nav_items'   => array,     // [{label, url, children}]
    'footer_links'=> array,     // [{title, links: [{label, url}]}]
    'cart_count'  => int,
    'search_url'  => string,
    'account_url' => string,
    'announcement'=> string,    // Announcement bar text
]
```

---

## Appendix C: Section Registry Complete List

| Section ID | Adapter | Template | Page |
|------------|---------|----------|------|
| `hero` | adapter-hero.php | sections/hero.php | Homepage |
| `categories` | adapter-wc-categories.php | sections/categories.php | Homepage |
| `bestsellers` | adapter-wc-products.php | sections/bestsellers.php | Homepage |
| `reviews` | adapter-testimonials.php | sections/reviews.php | Homepage |
| `faq` | adapter-faq.php | sections/faq.php | Homepage |
| `newsletter` | adapter-options.php | sections/newsletter.php | All pages |
| `shop-hero` | adapter-shop-hero.php | sections/shop-hero.php | Shop |
| `shop-filter` | adapter-wc-filter.php | sections/shop-filter.php | Shop |
| `shop-grid` | adapter-wc-products.php | sections/shop-grid.php | Shop |
| `product` | adapter-product.php | sections/product.php | Product |
| `related` | adapter-wc-products.php | sections/related.php | Product |
| `cart` | adapter-cart.php | sections/section-cart.php | Cart |
| `checkout` | adapter-cart.php | sections/checkout.php | Checkout |
| `order-confirmation` | adapter-order.php | sections/order-confirmation.php | Order |
| `auth` | adapter-auth.php | sections/auth.php | Login |
| `account` | adapter-account.php | sections/account.php | My Account |
| `blog-grid` | adapter-blog.php | sections/blog-grid.php | Blog |
| `blog-single` | adapter-article.php | sections/blog-single.php | Single Post |
| `mission` | adapter-about.php | sections/mission.php | About |
| `features` | adapter-about.php | sections/features.php | About |
| `story` | adapter-about.php | sections/story.php | About |
| `stats` | adapter-about.php | sections/stats.php | About |
| `values` | adapter-about.php | sections/values.php | About |
| `team` | adapter-team.php | sections/team.php | About |
| `contact` | adapter-contact.php | sections/contact.php | Contact |
| `wishlist` | adapter-wishlist.php | sections/wishlist.php | Wishlist |
| `coming-soon` | adapter-coming-soon.php | sections/coming-soon.php | Coming Soon |

---

*This document is the authoritative guide for frontend modifications. Read it before making any change. When in doubt, ask.*
