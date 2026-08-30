# TEMPLATE REQUIREMENTS FOR CORE THEME

> **Purpose:** Complete guide for creating, cloning, and building any template frontend that connects seamlessly with the AUREON/AETHER core theme and uses ALL available features.
> **Version:** 2.0.0 · **Date:** 2026-08-29

---

# CRITICAL FRONTEND RULE

AUREON supports **TWO frontend modes**. Determine the mode BEFORE doing any implementation.

## MODE A — COMPONENT MODE

**Use when:**
- No complete premium frontend exists, OR
- The client explicitly wants AUREON to compose the UI from sections/components.

**Architecture:**
```
AUREON
→ adapters
→ ViewModels/data
→ sections/components
→ rendered page
```

**In this mode:**
- AUREON controls presentation composition.
- Existing section/component requirements apply.
- tokens.php, composer.php, component/section conventions are used.
- Platform CDN dependencies (Bootstrap, Swiper, GSAP) may be loaded.
- Design packs shadow base templates via filesystem.

**File structure:**
```
frontend/designs/{your-pack}/
  manifest.json
  tokens.php
  composer.php
  css/{pack}.css
  js/{pack}.js
  components/**/*.php
  sections/**/*.php
  assets/**
```

## MODE B — COMPLETE-PAGE MODE

**Use when:**
- The client provides a complete HTML/CSS/JS premium frontend.
- The client wants that frontend preserved as-is.

**Architecture:**
```
COMPLETE CLIENT FRONTEND
→ generic complete-page host
→ thin integration bridge
→ AUREON/WP/WooCommerce
```

**In this mode:**
- Client HTML remains presentation source of truth.
- Client CSS remains presentation source of truth.
- Client presentation JS remains presentation source of truth.
- Client assets/libraries remain presentation source of truth.
- AUREON provides routing, canonical data, business logic, security, endpoints, and WooCommerce integration.
- Bridge translates between the two systems.

**DO NOT:**
- Split complete pages into AUREON sections.
- Reconstruct the frontend with AUREON components.
- Recreate the client DOM.
- Rewrite the client CSS.
- Rewrite presentation JS unnecessarily.
- Replace the client's visual system with AUREON's visual system.

**File structure:**
```
frontend/designs/{your-pack}/
  manifest.json              ← complete_page=true
  bridge.php                 ← thin data/business bridge
  js/bridge.js               ← cart/wishlist sync (optional)
  assets/                    ← client assets (if self-hosted)
```

**Core changes permitted:**
- `ferm-page.php` or equivalent complete-page host
- `frontend/views/assets.php` — asset isolation for complete-page mode
- `aureon/theme/inc/frontend.php` — routing and boot
- `aureon/theme/inc/aether-tokens.php` — token suppression when complete_page=true

---

## Decision Flowchart

```
Does the client provide a complete HTML/CSS/JS frontend?
    │
    ├── YES → Use MODE B (Complete-Page Mode)
    │         Preserve the client frontend.
    │         Build only the data/business bridge.
    │
    └── NO  → Use MODE A (Component Mode)
              Build sections/components.
              Use design packs.
```

**NEVER convert a COMPLETE-PAGE frontend into COMPONENT MODE merely because AUREON's component architecture already exists.**

---

## Table of Contents

1. [Critical Frontend Rule](#critical-frontend-rule)
2. [Complete-Page Mode Requirements](#complete-page-mode-requirements)
3. [Component Mode Reference](#component-mode-reference)
4. [Core Theme Feature Map](#core-theme-feature-map)
5. [Template Architecture Requirements](#template-architecture-requirements)
6. [How Templates Connect to the Core](#how-templates-connect-to-the-core)
7. [Template Types and Their Requirements](#template-types-and-their-requirements)
8. [How to Clone an Existing Template](#how-to-clone-an-existing-template)
9. [How to Create a New Template from Scratch](#how-to-create-a-new-template-from-scratch)
10. [Data Flow Requirements](#data-flow-requirements)
11. [Component Requirements](#component-requirements)
12. [Section Requirements](#section-requirements)
13. [Asset Requirements](#asset-requirements)
14. [Token Requirements](#token-requirements)
15. [Adapter Requirements](#adapter-requirements)
16. [Security Requirements](#security-requirements)
17. [Performance Requirements](#performance-requirements)
18. [Accessibility Requirements](#accessibility-requirements)
19. [Testing Requirements](#testing-requirements)
20. [Deployment Requirements](#deployment-requirements)
21. [Feature Integration Checklist](#feature-integration-checklist)

---

## Complete-Page Mode Requirements

> **For clients who provide a complete HTML/CSS/JS premium frontend.**
> **This is the preferred mode for premium client frontends (e.g., Ferm Living).**

### B.1 Generic Complete-Page Host

AUREON provides a generic complete-page host mechanism:

```
WordPress request
    → complete-page host (ferm-page.php or equivalent)
    → reads frozen HTML file
    → extracts body content
    → wraps with wp_head() / wp_footer()
    → outputs complete page
```

**Requirements for the host:**
- Route mapping (WP route → frozen HTML file)
- Body content extraction from complete HTML
- `wp_head()` / `wp_footer()` wrapping for WP/WC integration
- Asset isolation (suppress AUREON presentation assets)
- Generic, reusable, not client-specific

### B.2 Asset Isolation

When `complete_page=true` in `manifest.json`:

```
SUPPRESSED:
├── AUREON presentation CSS
├── AUREON presentation JS
├── Platform CDN dependencies (Bootstrap, Swiper, GSAP)
├── Design token CSS custom properties
└── All AUREON visual system assets

KEPT:
├── WordPress core assets (admin bar, jQuery)
├── WooCommerce assets (cart fragments, scripts)
├── Client's own CSS/JS
├── Client's own vendor libraries
└── Business-required WP/WC scripts only
```

### B.3 Thin Data/Business Bridge

The bridge translates between AUREON/WP/WooCommerce and the client frontend:

```
AUREON BRIDGE RESPONSIBILITIES:
├── Product data mapping (WC → client format)
├── Collection/category data mapping
├── Navigation data (WP menus → client format)
├── Cart bridge (Shopify API → WC endpoints)
├── Customer state (WC session → client format)
├── Search bridge (WC search → client format)
├── Form handling (contact, newsletter)
├── Route mapping (WP routes → client pages)
└── Runtime configuration

AUREON BRIDGE DOES NOT:
├── Rebuild the client DOM
├── Recreate the client CSS
├── Rewrite presentation JS
├── Split pages into sections
└── Compose the visual presentation
```

### B.4 Client Template Contract

The client provides:

```
CLIENT TEMPLATE
├── Complete HTML pages
├── CSS (design system, responsive, animations)
├── JS (presentation logic, interactions)
├── Vendor libraries (if any)
├── Images, SVG, fonts
├── manifest.json (with complete_page=true)
├── TEMPLATE-CONTRACT.md (data field documentation)
├── assets-manifest.json (asset inventory)
└── JS-COMPATIBILITY-MAP.md (runtime dependencies)
```

### B.5 Complete-Page Testing Requirements

```
COMMON TESTS:
├── Source standalone vs WordPress connected
├── No AUREON presentation contamination
├── No duplicate libraries
├── No Shopify/runtime dependency (unless bridged)
├── No asset 404s
├── Real dynamic data displayed
├── Real commerce actions working
└── Isolation proven (no visual interference)

VIEWPORT TESTS:
├── 1440px — desktop
├── 1024px — tablet landscape
├── 768px — tablet portrait
├── 390px — mobile

ACCEPTANCE GATE:
├── Standalone client frontend renders correctly
├── WordPress-connected frontend renders identically
├── Zero prohibited third-party runtime errors
├── Zero presentation asset contamination
├── Dynamic data updates on page load
├── Commerce actions (add-to-cart, checkout) work
└── No console errors
```

### B.6 Complete-Page Acceptance Checklist

- [ ] Client HTML preserved exactly
- [ ] Client CSS loaded correctly
- [ ] Client JS loaded correctly
- [ ] Client vendor libraries loaded
- [ ] No AUREON presentation CSS loaded
- [ ] No AUREON presentation JS loaded
- [ ] No platform CDN contamination
- [ ] No duplicate libraries
- [ ] Product data displays correctly
- [ ] Cart add/update/remove works
- [ ] Checkout flow completes
- [ ] Navigation works
- [ ] Search works
- [ ] Customer account works
- [ ] All viewports render correctly
- [ ] Zero console errors
- [ ] Zero visual regressions

---

## Component Mode Reference

> **For clients who want AUREON to compose the UI from sections/components.**
> **Use when no complete premium frontend exists.**

### A.1 Architecture

```
AUREON
→ 23 Adapters (ONLY WP/WC touchpoint)
→ Normalized data arrays
→ ViewModels (data normalization)
→ Renderer → Composer → 53 Components + 26 Sections
→ Design Pack (presentation: HTML/CSS/JS)
```

### A.2 Design Pack Structure

```
frontend/designs/{your-pack}/
  manifest.json              ← Pack descriptor (name, version, CSS/JS assets)
  tokens.php                 ← Design token overrides (colors, typography, spacing)
  composer.php               ← Section ordering + adapter filters
  css/
    fonts.css                ← Font imports (@font-face)
    {pack}.css               ← Your main stylesheet
  js/
    {pack}.js                ← Your main JavaScript
    bridge.js                ← Cart/wishlist bridge (optional, <=150 lines)
  components/                ← Component template overrides (shadow base)
    shell/
      header.php
      footer.php
      mobile-chrome.php
      announcement.php
      preloader.php
    cards/
      product.php
      category.php
    product/
      info.php
      gallery.php
      related.php
  sections/                  ← Section template overrides (shadow base)
    hero.php
    categories.php
    bestsellers.php
    newsletter.php
    shop-hero.php
    shop-filter.php
    shop-grid.php
    product.php
    section-cart.php
    checkout.php
    order-confirmation.php
    blog-grid.php
    blog-single.php
    wishlist.php
    auth.php
    account.php
  assets/                    ← Your images, fonts, icons
    fonts/
    images/
```

### A.3 Shadowing Mechanism

When the engine renders a component or section, it calls `aether_resolve_design_path($relative)`:

1. Checks `frontend/designs/{active-pack}/{$relative}` first
2. If file exists there → uses the pack version
3. If not → falls back to `frontend/{$relative}` (base)

You don't need to register overrides. Just place a file with the same relative path in your pack directory.

### A.4 Component Rules

1. Receive data from adapters via `$data` array
2. Never call WP/WC functions
3. Escape all output with `esc_html()`, `esc_url()`, `esc_attr()`
4. Use null coalescence for optional fields: `$data['field'] ?? 'default'`
5. Provide fallbacks for missing data
6. Use semantic HTML
7. Include ARIA labels for interactive elements
8. Use lazy loading for images
9. Follow BEM naming for CSS classes

### A.5 Section Rules

1. Receive data from adapters via `$data` array
2. Render components using `aether_render_component('component-id', $data)`
3. Use semantic HTML for section containers
4. Handle empty states gracefully
5. Use BEM naming for section classes

### A.6 tokens.php (Component Mode)

Tokens generate CSS custom properties on `:root`:

```php
<?php
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

### A.7 composer.php (Component Mode)

Controls section ordering and adapter filters:

```php
<?php
add_filter('aether_frontpage_sections', function($sections) {
    return ['hero', 'categories', 'bestsellers', 'newsletter'];
});
```

### A.8 Asset Policy (Component Mode)

Platform CDN dependencies may be loaded:
- Bootstrap 5.3.3
- Font Awesome 6.5.1
- Swiper 11
- GSAP 3.12.5 + ScrollTrigger

Pack assets load on top of platform assets.

---

## 1. Core Theme Feature Map

### 1.1 Available Features

The AUREON/AETHER core theme provides these features that your template MUST use:

| Feature | Provided By | How Templates Use It |
|---------|------------|---------------------|
| **WooCommerce Commerce** | `aureon/plugin` + adapters | Product cards, cart, checkout, orders, account |
| **Customizer Options** | `aureon/theme/inc/customizer.php` | All colors, fonts, layouts, content via `aureon_get_option()` |
| **Design Tokens** | `frontend/tokens/tokens.php` | CSS custom properties on `:root` |
| **Menu System** | WP `register_nav_menus` | Header, footer, mobile navigation |
| **AJAX Handlers** | `aureon/theme/inc/aether-ajax.php` | Wishlist, contact form, newsletter, quick-view |
| **Cart Fragments** | `aureon/theme/inc/aether-cart.php` | Live cart count update, cart sidebar |
| **SEO** | `aureon/theme/inc/aether-seo.php` | OG tags, Twitter cards, JSON-LD, canonical |
| **Security** | `aureon/theme/inc/aether-security.php` | CSP headers, nonce verification, rate limiting |
| **Analytics** | `aureon/theme/inc/aether-analytics.php` | GA4 dataLayer events |
| **Performance** | `aureon/theme/inc/aether-performance.php` | Resource hints, preloads, HTML compression |
| **Newsletter** | `aureon/theme/inc/aether-newsletter.php` | Email capture, subscriber DB, admin panel |
| **Auth System** | `adapter-auth.php` | Login, register, password reset |
| **Account System** | `adapter-account.php` | Dashboard, orders, addresses, profile |
| **Wishlist** | `adapter-wishlist.php` | Add/remove items, wishlist page |
| **Blog System** | `adapter-blog.php` + `adapter-article.php` | Blog grid, single post, comments |
| **FAQ System** | `adapter-faq.php` | FAQ accordion from CPT |
| **Testimonials** | `adapter-testimonials.php` | Reviews from CPT |
| **Team System** | `adapter-team.php` | Team cards from CPT |
| **Contact System** | `adapter-contact.php` | Contact form, business info |
| **Coming Soon** | `adapter-coming-soon.php` | Countdown timer |

### 1.2 Feature Dependencies

```
Commerce Features (require WooCommerce)
├── Product display (cards, grid, single)
├── Cart (add, update, remove)
├── Checkout (payment, shipping)
├── Orders (history, tracking)
├── Account (profile, addresses)
├── Wishlist (save items)
├── Search (product search)
└── Categories (product taxonomy)

Content Features (require WordPress)
├── Blog (posts, categories, tags)
├── Pages (about, contact, FAQ)
├── Menus (primary, footer)
├── Media (images, videos)
└── Custom Post Types (FAQ, testimonials, team)

System Features (always available)
├── Customizer (all design options)
├── Tokens (CSS custom properties)
├── Security (CSP, nonces)
├── SEO (OG, JSON-LD)
├── Analytics (dataLayer)
├── Performance (hints, preloads)
└── Newsletter (email capture)
```

---

## 2. Template Architecture Requirements

### 2.1 The Template Hierarchy

Every page in WordPress follows this hierarchy:

```
WordPress Template Hierarchy
    │
    ▼
Theme Template (front-page.php, single.php, archive.php, etc.)
    │
    ▼
AETHER Engine (loader.php → design.php → renderer.php)
    │
    ▼
Design Pack (your templates override base templates)
    │
    ▼
Browser (HTML/CSS/JS)
```

### 2.2 Template File Locations

| Location | Purpose | Editable? |
|----------|---------|-----------|
| `aureon/theme/*.php` | WordPress template hierarchy | NO |
| `aureon/theme/inc/*.php` | Theme infrastructure | NO |
| `frontend/views/*.php` | Engine kernel | NO |
| `frontend/components/*.php` | Base component templates | Pack shadows |
| `frontend/sections/*.php` | Base section templates | Pack shadows |
| `frontend/designs/{pack}/*.php` | YOUR templates | YES |

### 2.3 Template Resolution Order

When the engine renders a component or section:

1. Check `frontend/designs/{active-pack}/{relative-path}`
2. If found → use pack version
3. If not → use `frontend/{relative-path}` (base)
4. NEVER falls back to `aureon/theme/`

### 2.4 Required Template Files

Your pack MUST provide these templates:

```
frontend/designs/{your-pack}/
  manifest.json                    ← REQUIRED
  tokens.php                       ← REQUIRED
  composer.php                     ← REQUIRED
  css/{pack}.css                   ← REQUIRED
  js/{pack}.js                     ← REQUIRED
  components/shell/header.php      ← REQUIRED (shell)
  components/shell/footer.php      ← REQUIRED (shell)
  components/shell/mobile-chrome.php ← REQUIRED (shell)
  components/cards/product.php     ← REQUIRED (commerce)
  sections/hero.php                ← REQUIRED (homepage)
  sections/newsletter.php          ← REQUIRED (all pages)
```

### 2.5 Optional Template Files

These are provided by the base engine if not shadowed:

```
components/shell/announcement.php   ← Optional (defaults provided)
components/shell/preloader.php      ← Optional (defaults provided)
components/cards/category.php       ← Optional (defaults provided)
components/product/info.php         ← Optional (defaults provided)
components/product/gallery.php      ← Optional (defaults provided)
components/product/related.php      ← Optional (defaults provided)
sections/categories.php             ← Optional (defaults provided)
sections/bestsellers.php            ← Optional (defaults provided)
sections/shop-hero.php              ← Optional (defaults provided)
sections/shop-filter.php            ← Optional (defaults provided)
sections/shop-grid.php              ← Optional (defaults provided)
sections/product.php                ← Optional (defaults provided)
sections/section-cart.php           ← Optional (defaults provided)
sections/checkout.php               ← Optional (defaults provided)
sections/order-confirmation.php     ← Optional (defaults provided)
sections/blog-grid.php              ← Optional (defaults provided)
sections/blog-single.php            ← Optional (defaults provided)
sections/wishlist.php               ← Optional (defaults provided)
sections/auth.php                   ← Optional (defaults provided)
sections/account.php                ← Optional (defaults provided)
```

---

## 3. How Templates Connect to the Core

### 3.1 The Connection Point: Composer

The `composer.php` file is the PRIMARY connection point between your template and the core theme. It hooks into the engine's filter system.

```php
<?php
// frontend/designs/{your-pack}/composer.php

// 1. CONTROL SECTION ORDERING
add_filter('aether_frontpage_sections', function($sections) {
    return [
        'hero',           // → sections/hero.php
        'categories',     // → sections/categories.php
        'bestsellers',    // → sections/bestsellers.php
        'newsletter',     // → sections/newsletter.php
    ];
});

// 2. ADD CUSTOM SECTIONS
add_action('aether_register_sections', function() {
    aether_register_section('editorial', 'adapter-options.php');
    aether_register_section('rooms', 'adapter-options.php');
});

// 3. OVERRIDE COMPOSITION BEHAVIOR
add_filter('aether_compose_header', function($html) {
    // Modify header output
    return $html;
});

add_filter('aether_compose_footer', function($html) {
    // Modify footer output
    return $html;
});

// 4. HOOK INTO ASSET LOADING
add_action('aether_enqueue_pack_assets', function() {
    // Enqueue additional assets
    wp_enqueue_style('your-custom-font', get_template_directory_uri() . '/assets/fonts/custom.css');
});

// 5. MODIFY ADAPTER DATA
add_filter('aether_adapter_product_data', function($data) {
    // Add custom fields to product data
    $data['custom_field'] = get_post_meta($data['id'], 'custom_field', true);
    return $data;
});
```

### 3.2 The Connection Point: Tokens

The `tokens.php` file connects your design to the core's token system.

```php
<?php
// frontend/designs/{your-pack}/tokens.php

return [
    // These generate CSS custom properties on :root
    'colors' => [
        'primary'    => '#000000',  // → --color-primary
        'secondary'  => '#ffffff',  // → --color-secondary
        'accent'     => '#c8a97e',  // → --color-accent
        'background' => '#f8f6f3',  // → --color-background
        'text'       => '#1a1a1a',  // → --color-text
        'muted'      => '#6b7280',  // → --color-muted
    ],
    'typography' => [
        'heading_font' => '"Your Font", serif',  // → --font-heading
        'body_font'    => '"Your Font", sans-serif',  // → --font-body
        'base_size'    => '16px',  // → --font-base
        'scale'        => '1.25',  // → --font-scale
    ],
    'spacing' => [
        'section_gap' => '6rem',  // → --spacing-section
        'container_width' => '1280px',  // → --container-width
    ],
];
```

### 3.3 The Connection Point: Manifest

The `manifest.json` file connects your assets to the engine's asset pipeline.

```json
{
    "name": "{your-pack}",
    "version": "1.0.0",
    "assets": {
        "css": ["css/{pack}.css"],
        "js": ["js/{pack}.js"]
    },
    "dependencies": {
        "platform": ["bootstrap", "font-awesome", "swiper"],
        "pack": ["embla-carousel", "photoswipe"]
    }
}
```

### 3.4 The Connection Point: Component Templates

Component templates connect to the core via the `$data` array from adapters.

```php
<?php
// frontend/designs/{your-pack}/components/cards/product.php

// $data comes from adapter-wc-products.php
// This is the CONNECTION between WP/WC data and your HTML

// Available $data fields:
// - $data['id']           → Product ID
// - $data['name']         → Product name
// - $data['price']        → Formatted price HTML
// - $data['image']        → Product image URL
// - $data['url']          → Product permalink
// - $data['badge']        → 'Sale', 'New', 'Featured', or ''
// - $data['variants']     → Array of variations
// - $data['rating']       → Average rating (0-5)
// - $data['review_count'] → Number of reviews
?>

<div class="product-card" data-product-id="<?php echo esc_attr($data['id']); ?>">
    <a href="<?php echo esc_url($data['url']); ?>" class="product-card__link">
        <img src="<?php echo esc_url($data['image']); ?>"
             alt="<?php echo esc_attr($data['name']); ?>"
             loading="lazy"
             class="product-card__image">
        <?php if ($data['badge']): ?>
            <span class="product-card__badge"><?php echo esc_html($data['badge']); ?></span>
        <?php endif; ?>
    </a>
    <h3 class="product-card__title">
        <a href="<?php echo esc_url($data['url']); ?>"><?php echo esc_html($data['name']); ?></a>
    </h3>
    <div class="product-card__price"><?php echo $data['price']; ?></div>
    <button class="product-card__add-to-cart"
            data-product-id="<?php echo esc_attr($data['id']); ?>">
        Add to Cart
    </button>
</div>
```

### 3.5 The Connection Point: Section Templates

Section templates connect to the core via the `$data` array from adapters and use `aether_render_component()`.

```php
<?php
// frontend/designs/{your-pack}/sections/bestsellers.php

// $data comes from adapter-wc-products.php
// $data['products'] is an array of product data arrays

// This section uses the core's component rendering system
?>

<section class="bestsellers">
    <div class="container">
        <h2 class="bestsellers__title">Best Sellers</h2>
        <div class="bestsellers__grid">
            <?php foreach ($data['products'] as $product): ?>
                <?php aether_render_component('cards/product', $product); ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
```

---

## 4. Template Types and Their Requirements

### 4.1 Shell Templates

Shell templates provide the global page structure.

#### Header (`components/shell/header.php`)

**Requirements:**
- Logo with link to homepage
- Primary navigation (from WP menus)
- Search button/link
- Wishlist button/link
- Cart button/link with count
- Account button/link
- Mobile menu toggle

**Data source:** `adapter-shell.php`

```php
<?php
// $data fields:
// - $data['logo']        → Logo image URL
// - $data['brand']       → Site name
// - $data['brand_url']   → Homepage URL
// - $data['nav_items']   → Array of menu items [{label, url, children}]
// - $data['icons']       → {search, wishlist, cart, account} URLs
// - $data['cart_count']  → Current cart item count
?>
```

#### Footer (`components/shell/footer.php`)

**Requirements:**
- Brand logo/name
- Footer navigation columns
- Newsletter signup form
- Social media links
- Payment method icons
- Legal links (privacy, terms)
- Copyright notice

**Data source:** `adapter-site.php`

```php
<?php
// $data fields:
// - $data['name']         → Site name
// - $data['brand']        → Brand name
// - $data['tagline']      → Site tagline
// - $data['url']          → Homepage URL
// - $data['socials']      → [{platform, url, icon}]
// - $data['footer_links'] → [{title, links: [{label, url}]}]
// - $data['newsletter']   → {title, description, placeholder, button}
// - $data['payments']     → [{name, icon}]
// - $data['legal']        → [{label, url}]
?>
```

#### Mobile Chrome (`components/shell/mobile-chrome.php`)

**Requirements:**
- Mobile navigation drawer
- Close button
- Navigation links
- Account link
- Cart link with count

**Data source:** `adapter-shell.php`

### 4.2 Card Templates

Card templates display individual items in grids.

#### Product Card (`components/cards/product.php`)

**Requirements:**
- Product image with lazy loading
- Product name (linked to product page)
- Price (with sale price handling)
- Badge (Sale/New/Featured)
- Add to cart button
- Wishlist button

**Data source:** `adapter-wc-products.php`

#### Category Card (`components/cards/category.php`)

**Requirements:**
- Category image
- Category name (linked to category page)
- Product count (optional)

**Data source:** `adapter-wc-categories.php`

### 4.3 Product Templates

Product templates display single product details.

#### Product Info (`components/product/info.php`)

**Requirements:**
- Product name
- Price (current + compare)
- Short description
- Variant selector
- Add to cart button
- Quantity selector
- Product meta (SKU, categories, tags)
- Accordion (description, reviews, shipping)

**Data source:** `adapter-product.php`

#### Product Gallery (`components/product/gallery.php`)

**Requirements:**
- Main product image
- Thumbnail gallery
- Image zoom/lightbox
- Gallery navigation

**Data source:** `adapter-product.php`

### 4.4 Section Templates

Section templates compose pages from components.

#### Hero Section (`sections/hero.php`)

**Requirements:**
- Hero slider/carousel
- Slide images
- Slide titles and descriptions
- CTA buttons
- Navigation dots/arrows

**Data source:** `adapter-hero.php`

#### Shop Grid (`sections/shop-grid.php`)

**Requirements:**
- Product grid
- Pagination
- Product count
- Sorting options

**Data source:** `adapter-wc-products.php`

#### Cart Section (`sections/section-cart.php`)

**Requirements:**
- Cart items list
- Item images, names, prices
- Quantity controls
- Remove buttons
- Cart summary (subtotal, shipping, total)
- Proceed to checkout button
- Empty cart state

**Data source:** `adapter-cart.php`

---

## 5. How to Clone an Existing Template

### 5.1 Cloning a Component

To clone and modify an existing component:

```bash
# 1. Find the base component
ls frontend/components/cards/product.php

# 2. Create your pack directory
mkdir -p frontend/designs/{your-pack}/components/cards

# 3. Copy the base template
cp frontend/components/cards/product.php frontend/designs/{your-pack}/components/cards/product.php

# 4. Edit your copy
# The base remains untouched — your pack shadows it
```

### 5.2 Cloning a Section

To clone and modify an existing section:

```bash
# 1. Find the base section
ls frontend/sections/bestsellers.php

# 2. Create your pack directory
mkdir -p frontend/designs/{your-pack}/sections

# 3. Copy the base template
cp frontend/sections/bestsellers.php frontend/designs/{your-pack}/sections/bestsellers.php

# 4. Edit your copy
```

### 5.3 Cloning an Entire Pack

To clone an existing pack as a starting point:

```bash
# 1. Find the existing pack
ls frontend/designs/fermliving/

# 2. Copy the entire pack
cp -r frontend/designs/fermliving/ frontend/designs/{your-pack}/

# 3. Rename files
mv frontend/designs/{your-pack}/css/ferm.css frontend/designs/{your-pack}/css/{pack}.css
mv frontend/designs/{your-pack}/js/ferm.js frontend/designs/{your-pack}/js/{pack}.js

# 4. Update manifest.json
# Edit frontend/designs/{your-pack}/manifest.json

# 5. Update tokens.php
# Edit frontend/designs/{your-pack}/tokens.php

# 6. Update composer.php
# Edit frontend/designs/{your-pack}/composer.php

# 7. Update CSS/JS references
# Edit all files that reference the old pack name
```

### 5.4 Cloning a Page Template

To clone a WordPress page template:

```bash
# 1. Find the existing template
ls aureon/theme/page-about.php

# 2. DO NOT copy it to aureon/theme/
# Instead, create a section in your pack:

# 3. Create the section template
mkdir -p frontend/designs/{your-pack}/sections
# Create sections/your-page.php with the content

# 4. Create the adapter (if needed)
# Create adapters/adapter-your-page.php

# 5. Register in composer.php
# Add to section ordering or create new route
```

---

## 6. How to Create a New Template from Scratch

### 6.1 Step-by-Step Process

**Step 1: Plan Your Template**

Before writing any code, plan:

1. What page type? (homepage, product, blog, custom)
2. What sections does it need?
3. What data does each section need?
4. What components does each section use?
5. What's the visual layout?

**Step 2: Create the Pack Structure**

```bash
mkdir -p frontend/designs/{your-pack}/{css,js,components/shell,components/cards,components/product,sections,assets/fonts,assets/images}
```

**Step 3: Create manifest.json**

```json
{
    "name": "{your-pack}",
    "version": "1.0.0",
    "description": "Your custom design pack",
    "author": "Your Name",
    "assets": {
        "css": ["css/{pack}.css"],
        "js": ["js/{pack}.js"]
    }
}
```

**Step 4: Create tokens.php**

Define your design tokens (colors, typography, spacing).

**Step 5: Create composer.php**

Define section ordering and any custom hooks.

**Step 6: Create Shell Components**

Start with header, footer, mobile-chrome — these wrap ALL pages.

**Step 7: Create Card Components**

Create product-card, category-card — these are used in grids.

**Step 8: Create Section Templates**

Create sections for each page type your template supports.

**Step 9: Create CSS**

Write your styles using CSS custom properties from tokens.

**Step 10: Create JS**

Write your JavaScript for interactions (carousels, modals, etc.).

**Step 11: Test**

Test at all breakpoints, verify all WooCommerce flows.

**Step 12: Deploy**

Activate your pack via Customizer or option.

---

## 7. Data Flow Requirements

### 7.1 Data Contract Rule

**Components NEVER call WP/WC functions.** They receive pre-normalized data from adapters.

```
WRONG (forbidden):
<?php
$products = wc_get_products(['limit' => 8]);
foreach ($products as $product) {
    echo $product->get_name();
}
?>

CORRECT (required):
<?php
// $data['products'] comes from adapter-wc-products.php
foreach ($data['products'] as $product) {
    echo esc_html($product['name']);
}
?>
```

### 7.2 Adapter Data Shapes

Every adapter returns a specific data shape. Your templates MUST use these exact shapes.

**adapter-wc-products.php returns:**
```php
[
    'products' => [
        [
            'id'          => int,
            'name'        => string,
            'price'       => string,  // formatted HTML
            'image'       => string,  // URL
            'url'         => string,  // permalink
            'badge'       => string,  // 'Sale'|'New'|'Featured'|''
            'variants'    => array,
            'rating'      => float,
            'review_count'=> int,
        ],
    ],
    'pagination' => [
        'current' => int,
        'total'   => int,
        'links'   => string,  // HTML
    ],
]
```

**adapter-product.php returns:**
```php
[
    'id'                => int,
    'name'              => string,
    'price'             => string,
    'description'       => string,  // HTML
    'short_description' => string,
    'images'            => array,  // [{url, alt, id}]
    'variants'          => array,  // [{id, name, options, available}]
    'attributes'        => array,  // [{name, options}]
    'add_to_cart_url'   => string,
    'gallery'           => array,
    'reviews'           => array,
    'rating'            => float,
    'review_count'      => int,
    'related'           => array,
]
```

**adapter-cart.php returns:**
```php
[
    'items'    => array,
    'subtotal' => string,
    'shipping' => string,
    'total'    => string,
    'count'    => int,
    'is_empty' => bool,
]
```

### 7.3 Data Access Pattern

```php
<?php
// Access nested data safely with null coalescence
$name  = esc_html($data['product']['name'] ?? 'Unknown');
$price = $data['product']['price'] ?? '';
$image = esc_url($data['product']['image'] ?? '');

// Access arrays with foreach
foreach ($data['products'] as $product) {
    echo esc_html($product['name']);
}

// Access boolean flags
if ($data['is_empty'] ?? true) {
    echo 'Cart is empty';
}
?>
```

---

## 8. Component Requirements

### 8.1 Component Rules

1. **Receive data** from adapters via `$data` array
2. **Never call** WP/WC functions
3. **Escape all output** with `esc_html()`, `esc_url()`, `esc_attr()`
4. **Use null coalescence** for optional fields: `$data['field'] ?? 'default'`
5. **Provide fallbacks** for missing data
6. **Use semantic HTML** (`<article>`, `<section>`, `<nav>`, etc.)
7. **Include ARIA labels** for interactive elements
8. **Use lazy loading** for images: `loading="lazy"`
9. **Follow BEM naming** for CSS classes: `.block__element--modifier`

### 8.2 Component Template Structure

```php
<?php
// 1. Extract and escape data
$name  = esc_html($data['name'] ?? '');
$price = $data['price'] ?? '';
$image = esc_url($data['image'] ?? '');
$url   = esc_url($data['url'] ?? '');
$id    = esc_attr($data['id'] ?? '');
?>

<!-- 2. Semantic HTML with BEM classes -->
<article class="product-card" data-product-id="<?php echo $id; ?>">
    <!-- 3. Linked image with lazy loading -->
    <a href="<?php echo $url; ?>" class="product-card__link">
        <?php if ($image): ?>
            <img src="<?php echo $image; ?>"
                 alt="<?php echo $name; ?>"
                 loading="lazy"
                 class="product-card__image">
        <?php endif; ?>
    </a>

    <!-- 4. Title with link -->
    <h3 class="product-card__title">
        <a href="<?php echo $url; ?>"><?php echo $name; ?></a>
    </h3>

    <!-- 5. Price -->
    <div class="product-card__price"><?php echo $price; ?></div>

    <!-- 6. Action buttons -->
    <div class="product-card__actions">
        <button class="product-card__add-to-cart"
                data-product-id="<?php echo $id; ?>"
                aria-label="Add <?php echo $name; ?> to cart">
            Add to Cart
        </button>
    </div>
</article>
```

### 8.3 Required Components

| Component | Path | Data Source | Used By |
|-----------|------|-------------|---------|
| Product Card | `components/cards/product.php` | `adapter-wc-products.php` | shop-grid, bestsellers, related |
| Category Card | `components/cards/category.php` | `adapter-wc-categories.php` | categories |
| Product Info | `components/product/info.php` | `adapter-product.php` | product |
| Product Gallery | `components/product/gallery.php` | `adapter-product.php` | product |
| Header | `components/shell/header.php` | `adapter-shell.php` | all pages |
| Footer | `components/shell/footer.php` | `adapter-site.php` | all pages |
| Mobile Chrome | `components/shell/mobile-chrome.php` | `adapter-shell.php` | all pages |

---

## 9. Section Requirements

### 9.1 Section Rules

1. **Receive data** from adapters via `$data` array
2. **Render components** using `aether_render_component('component-id', $data)`
3. **Use semantic HTML** for section containers
4. **Include section titles** where appropriate
5. **Handle empty states** gracefully
6. **Use BEM naming** for section classes

### 9.2 Section Template Structure

```php
<?php
// 1. Extract section data
$products = $data['products'] ?? [];
$title    = esc_html($data['title'] ?? 'Best Sellers');
?>

<!-- 2. Semantic section with BEM class -->
<section class="bestsellers">
    <div class="container">
        <!-- 3. Section header -->
        <h2 class="bestsellers__title"><?php echo $title; ?></h2>

        <!-- 4. Component grid -->
        <div class="bestsellers__grid">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <?php aether_render_component('cards/product', $product); ?>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- 5. Empty state -->
                <p class="bestsellers__empty">No products found.</p>
            <?php endif; ?>
        </div>
    </div>
</section>
```

### 9.3 Required Sections

| Section | Path | Data Source | Page |
|---------|------|-------------|------|
| Hero | `sections/hero.php` | `adapter-hero.php` | Homepage |
| Categories | `sections/categories.php` | `adapter-wc-categories.php` | Homepage |
| Bestsellers | `sections/bestsellers.php` | `adapter-wc-products.php` | Homepage |
| Newsletter | `sections/newsletter.php` | `adapter-options.php` | All pages |
| Shop Hero | `sections/shop-hero.php` | `adapter-shop-hero.php` | Shop |
| Shop Filter | `sections/shop-filter.php` | `adapter-wc-filter.php` | Shop |
| Shop Grid | `sections/shop-grid.php` | `adapter-wc-products.php` | Shop |
| Product | `sections/product.php` | `adapter-product.php` | Product |
| Cart | `sections/section-cart.php` | `adapter-cart.php` | Cart |
| Checkout | `sections/checkout.php` | `adapter-cart.php` | Checkout |
| Order Confirmation | `sections/order-confirmation.php` | `adapter-order.php` | Order |
| Blog Grid | `sections/blog-grid.php` | `adapter-blog.php` | Blog |
| Blog Single | `sections/blog-single.php` | `adapter-article.php` | Single Post |
| Wishlist | `sections/wishlist.php` | `adapter-wishlist.php` | Wishlist |
| Auth | `sections/auth.php` | `adapter-auth.php` | Login |
| Account | `sections/account.php` | `adapter-account.php` | My Account |

---

## 10. Asset Requirements

### 10.1 CSS Requirements

```css
/* frontend/designs/{your-pack}/css/{pack}.css */

/* 1. Import fonts */
@import url('fonts.css');

/* 2. Use CSS custom properties from tokens */
:root {
    --color-primary: #000000;
    --color-secondary: #ffffff;
    /* These are set by tokens.php, but you can override */
}

/* 3. Follow BEM naming */
.product-card { }
.product-card__title { }
.product-card__title--sale { }

/* 4. Use container queries or media queries */
.container { max-width: var(--container-width); margin: 0 auto; }

/* 5. Responsive breakpoints */
@media (max-width: 768px) { }
@media (max-width: 480px) { }

/* 6. Print styles */
@media print { }
```

### 10.2 JS Requirements

```javascript
// frontend/designs/{your-pack}/js/{pack}.js

(function() {
    'use strict';

    // 1. DOMContentLoaded wrapper
    document.addEventListener('DOMContentLoaded', function() {

        // 2. Initialize components
        initCarousels();
        initModals();
        initForms();

    });

    // 3. Component functions
    function initCarousels() {
        // Your carousel initialization
    }

    function initModals() {
        // Your modal initialization
    }

    function initForms() {
        // Your form handling
    }

    // 4. Safe AJAX calls (to your own endpoints)
    function fetchData(url, callback) {
        fetch(url)
            .then(function(response) { return response.json(); })
            .then(function(data) { callback(null, data); })
            .catch(function(err) { callback(err); });
    }

})();
```

### 10.3 Font Requirements

```css
/* frontend/designs/{your-pack}/css/fonts.css */

/* 1. Define @font-face for each font weight/style */
@font-face {
    font-family: 'YourFont';
    src: url('../assets/fonts/YourFont-Regular.woff2') format('woff2');
    font-weight: 400;
    font-style: normal;
    font-display: swap;  /* 2. Always use font-display: swap */
}

@font-face {
    font-family: 'YourFont';
    src: url('../assets/fonts/YourFont-Bold.woff2') format('woff2');
    font-weight: 700;
    font-style: normal;
    font-display: swap;
}

/* 3. Provide fallback stack */
body {
    font-family: 'YourFont', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
}
```

---

## 11. Token Requirements

### 11.1 Token Categories

| Category | Token Prefix | CSS Variable | Purpose |
|----------|-------------|--------------|---------|
| Colors | `colors.*` | `--color-*` | Brand, UI, state colors |
| Typography | `typography.*` | `--font-*`, `--font-size-*` | Font families, sizes |
| Spacing | `spacing.*` | `--spacing-*` | Margins, padding, gaps |
| Layout | `layout.*` | `--max-width-*`, `--grid-*` | Container, grid systems |
| Borders | `borders.*` | `--border-*`, `--radius-*` | Border styles, radii |
| Shadows | `shadows.*` | `--shadow-*` | Box shadows |
| Transitions | `transitions.*` | `--transition-*` | Animation timings |

### 11.2 Token Usage in Templates

```php
<?php
// Tokens are available as CSS custom properties
// Use them in your templates via inline styles or CSS classes
?>

<div style="color: var(--color-primary); font-family: var(--font-heading);">
    <?php echo esc_html($data['title']); ?>
</div>

<!-- Better: use CSS classes -->
<h2 class="section-title"><?php echo esc_html($data['title']); ?></h2>
```

### 11.3 Token Usage in CSS

```css
/* Use tokens throughout your CSS */
.section-title {
    color: var(--color-primary);
    font-family: var(--font-heading);
    font-size: var(--font-size-h2);
    margin-bottom: var(--spacing-lg);
}

.container {
    max-width: var(--container-width);
    padding: 0 var(--spacing-md);
}
```

---

## 12. Adapter Requirements

### 12.1 When to Create a New Adapter

Create a new adapter when:
- You need data that no existing adapter provides
- You're adding a completely new section type
- You need to transform data in a new way

### 12.2 Adapter Template

```php
<?php
// frontend/adapters/adapter-your-section.php

function aether_adapter_your_section() {
    // 1. Check dependencies
    if (!function_exists('wc_get_products')) {
        return ['items' => []];
    }

    // 2. Fetch data from WP/WC
    $items = get_posts([
        'post_type'      => 'your_post_type',
        'posts_per_page' => 10,
        'post_status'    => 'publish',
    ]);

    // 3. Normalize data
    $normalized = [];
    foreach ($items as $item) {
        $normalized[] = [
            'id'    => $item->ID,
            'title' => esc_html($item->post_title),
            'url'   => get_permalink($item->ID),
            'image' => get_the_post_thumbnail_url($item->ID, 'medium'),
        ];
    }

    // 4. Return normalized data
    return [
        'items' => $normalized,
        'title' => 'Your Section Title',
    ];
}
```

### 12.3 Registering a New Adapter

```php
// In your pack's composer.php
add_action('aether_register_sections', function() {
    aether_register_section('your-section', 'adapter-your-section.php');
});
```

---

## 13. Security Requirements

### 13.1 Output Escaping

```php
<?php
// ALWAYS escape output
echo esc_html($data['name']);          // Text
echo esc_url($data['url']);            // URLs
echo esc_attr($data['id']);            // HTML attributes
echo wp_kses_post($data['content']);   // HTML content (safe tags only)
echo wp_kses($data['raw'], [          // Custom allowed tags
    'br' => [],
    'strong' => [],
    'em' => [],
]);
?>
```

### 13.2 Input Sanitization

```php
<?php
// Sanitize user input
$name  = sanitize_text_field($_POST['name']);
$email = sanitize_email($_POST['email']);
$url   = esc_url_raw($_POST['url']);
$int   = absint($_POST['id']);
?>
```

### 13.3 Nonce Verification

```php
<?php
// Verify nonces for form submissions
if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'your_action')) {
    wp_die('Security check failed');
}
?>
```

### 13.4 Capability Checks

```php
<?php
// Check user capabilities
if (!current_user_can('edit_posts')) {
    wp_die('Unauthorized');
}
?>
```

---

## 14. Performance Requirements

### 14.1 Image Optimization

```html
<!-- Use lazy loading -->
<img src="image.jpg" loading="lazy" alt="description">

<!-- Use responsive images -->
<img srcset="image-300.jpg 300w, image-600.jpg 600w"
     sizes="(max-width: 600px) 300px, 600px"
     src="image-600.jpg"
     alt="description">

<!-- Use WebP format -->
<picture>
    <source srcset="image.webp" type="image/webp">
    <img src="image.jpg" alt="description">
</picture>
```

### 14.2 CSS Optimization

```css
/* Use efficient selectors */
.product-card { }           /* Good */
.product-card__title { }    /* Good */
.product .card .title { }   /* Bad - too deep */

/* Use CSS containment */
.product-card { contain: layout style; }

/* Use will-change for animations */
.carousel { will-change: transform; }
```

### 14.3 JS Optimization

```javascript
// Use event delegation
document.addEventListener('click', function(e) {
    if (e.target.matches('.product-card__add-to-cart')) {
        // Handle click
    }
});

// Use requestAnimationFrame for animations
function animate() {
    requestAnimationFrame(function() {
        // Animation code
    });
}

// Use IntersectionObserver for lazy loading
const observer = new IntersectionObserver(function(entries) {
    entries.forEach(function(entry) {
        if (entry.isIntersecting) {
            // Load content
            observer.unobserve(entry.target);
        }
    });
});
```

---

## 15. Accessibility Requirements

### 15.1 Semantic HTML

```html
<!-- Use semantic elements -->
<header role="banner">...</header>
<nav role="navigation">...</nav>
<main role="main">...</main>
<footer role="contentinfo">...</footer>

<!-- Use headings in order -->
<h1>Page Title</h1>
<h2>Section Title</h2>
<h3>Subsection Title</h3>

<!-- Use lists for navigation -->
<nav>
    <ul>
        <li><a href="/page">Page</a></li>
    </ul>
</nav>
```

### 15.2 ARIA Labels

```html
<!-- Label interactive elements -->
<button aria-label="Close menu">×</button>
<button aria-label="Add to cart">Add to Cart</button>
<input aria-label="Search" type="search">

<!-- Label sections -->
<section aria-labelledby="section-title">
    <h2 id="section-title">Section Title</h2>
</section>

<!-- Indicate current page -->
<nav aria-label="Main navigation">
    <ul>
        <li><a href="/current" aria-current="page">Current Page</a></li>
    </ul>
</nav>
```

### 15.3 Keyboard Navigation

```html
<!-- Make interactive elements focusable -->
<button tabindex="0">Click me</button>
<a href="/link" tabindex="0">Link</a>

<!-- Skip navigation link -->
<a href="#main-content" class="skip-link">Skip to main content</a>

<!-- Focus styles -->
<style>
:focus { outline: 2px solid var(--color-primary); }
:focus:not(:focus-visible) { outline: none; }
:focus-visible { outline: 2px solid var(--color-primary); }
</style>
```

### 15.4 Color Contrast

```css
/* Ensure sufficient contrast (WCAG AA: 4.5:1 for normal text) */
.text-primary { color: #1a1a1a; }  /* On white background */
.text-secondary { color: #6b7280; }  /* On white background */

/* Test with tools */
/* https://webaim.org/resources/contrastchecker/ */
```

---

## 16. Testing Requirements

### 16.1 Visual Testing

```bash
# Test at all breakpoints
npx playwright test specs/visual.spec.js --project=desktop
npx playwright test specs/visual.spec.js --project=tablet
npx playwright test specs/visual.spec.js --project=mobile

# Take screenshots
npx playwright test specs/screenshots.spec.js
```

### 16.2 Functional Testing

```bash
# Test WooCommerce flows
npx playwright test specs/commerce.spec.js

# Test form submissions
npx playwright test specs/forms.spec.js

# Test navigation
npx playwright test specs/navigation.spec.js
```

### 16.3 Accessibility Testing

```bash
# Run axe-core
npx playwright test specs/accessibility.spec.js

# Manual testing
# - Keyboard navigation
# - Screen reader testing
# - Color contrast verification
```

### 16.4 Performance Testing

```bash
# Lighthouse audit
npx lighthouse http://localhost:8080 --output html

# Check bundle size
npx webpack-bundle-analyzer stats.json
```

---

## 17. Deployment Requirements

### 17.1 Pre-Deployment Checklist

- [ ] All templates tested at 1440px, 1024px, 768px, 390px
- [ ] All WooCommerce flows working
- [ ] No JS errors in console
- [ ] No CSS conflicts
- [ ] All images optimized
- [ ] All fonts loading correctly
- [ ] Accessibility verified
- [ ] Performance acceptable (Lighthouse > 90)
- [ ] Git committed and pushed

### 17.2 Deployment Steps

```bash
# 1. Commit changes
git add frontend/designs/{your-pack}/
git commit -m "feat(pack): your changes"

# 2. Push to remote
git push origin main

# 3. Activate pack
# Option A: WordPress Customizer
# Option B: wp-cli
wp option update aether_active_design '{your-pack}'

# 4. Clear cache
wp cache flush

# 5. Verify
# Check site at all breakpoints
# Test all WooCommerce flows
```

### 17.3 Rollback Plan

```bash
# If something breaks
git revert HEAD

# Or switch packs
wp option update aether_active_design 'previous-pack'
wp cache flush
```

---

## 18. Feature Integration Checklist

Use this checklist to ensure your template uses ALL core theme features:

### Commerce Features
- [ ] Product cards display correctly
- [ ] Product page shows all data
- [ ] Add to cart works (simple products)
- [ ] Add to cart works (variable products)
- [ ] Cart page displays items
- [ ] Cart update quantity works
- [ ] Cart remove item works
- [ ] Checkout flow completes
- [ ] Order confirmation displays
- [ ] Product search returns results
- [ ] Category filtering works
- [ ] Sale badges display
- [ ] New badges display
- [ ] Featured badges display
- [ ] Product ratings display
- [ ] Related products display

### Account Features
- [ ] Login form works
- [ ] Registration form works
- [ ] Password reset works
- [ ] Account dashboard displays
- [ ] Order history displays
- [ ] Order details display
- [ ] Address management works
- [ ] Profile editing works

### Wishlist Features
- [ ] Add to wishlist works
- [ ] Remove from wishlist works
- [ ] Wishlist page displays
- [ ] Wishlist count updates

### Content Features
- [ ] Blog grid displays
- [ ] Single post displays
- [ ] Post pagination works
- [ ] Category pages display
- [ ] Tag pages display
- [ ] Search results display
- [ ] 404 page displays

### System Features
- [ ] Customizer options applied
- [ ] Design tokens generate CSS variables
- [ ] SEO meta tags present
- [ ] OG tags present
- [ ] JSON-LD present
- [ ] CSP headers present
- [ ] Nonce verification working
- [ ] Rate limiting working
- [ ] Analytics events firing
- [ ] Performance hints present
- [ ] Newsletter form works
- [ ] Contact form works
- [ ] FAQ accordion works
- [ ] Testimonials display
- [ ] Team cards display

### Shell Features
- [ ] Header displays correctly
- [ ] Logo links to homepage
- [ ] Navigation menu works
- [ ] Mega menu works (if used)
- [ ] Search opens correctly
- [ ] Cart count updates live
- [ ] Footer displays correctly
- [ ] Footer links work
- [ ] Newsletter signup works
- [ ] Social links work
- [ ] Mobile menu opens/closes
- [ ] Mobile navigation works
- [ ] Mobile cart count updates

### Asset Features
- [ ] CSS loads correctly
- [ ] JS loads correctly
- [ ] Fonts load correctly
- [ ] Images load correctly
- [ ] CDN resources load
- [ ] No mixed content warnings
- [ ] No CORS errors

---

*This document is the authoritative reference for creating templates that connect with the AUREON/AETHER core theme. Read it before building any template.*
