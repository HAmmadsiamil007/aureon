# Phase 17.1 — Frontend Integration Architecture

**Date:** 2026-08-05
**Status:** Design Document — DECISIONS LOCKED
**Scope:** AETHER Frontend Template → Aureon Core Integration
**Principle:** Server-side rendering primary. REST API only for AJAX interactions.

---

## Locked Decisions

| Decision | Locked Choice | Rationale |
|----------|--------------|-----------|
| **Theme architecture** | Standalone Aureon theme (no child theme) | Independent product, original branding, not a GP derivative |
| **WooCommerce integration** | Hybrid (80-90% hooks, 10-20% minimal template overrides) | Minimizes maintenance, avoids WC update conflicts |
| **Authentication** | Bridge WordPress + Firebase (+ future providers) | Maximum flexibility, abstraction layer, future-proof |
| **`phantom-data.js`** | Reduce to AJAX-only enhancement layer | Server-side rendering for pages, JS only for interactions |
| **Asset delivery** | Bundle locally (GSAP, Lenis, Swiper, FA, etc.) | Reliability, performance, privacy, version control |
| **Rendering strategy** | Server-side primary, REST only for AJAX | SEO, performance, caching, simplicity |
| **Data flow** | WordPress → WC → Plugin Modules → Adapters → ViewModels → Renderer → Components → Frontend | Components never call WP functions directly |
| **CSS strategy** | Keep frontend CSS intact, replace hardcoded values with Aureon design tokens | Preserve UI/UX, enable Customizer control |
| **JS strategy** | Preserve working scripts (GSAP timelines, Lenis, Swiper), no rewrites without clear technical reason | Stability, proven animations |
| **Component strategy** | Every HTML section = reusable Aureon component receiving data from ViewModels | Decoupled, testable, replaceable |

---

## Table of Contents

1. [Architecture Decision](#1-architecture-decision)
2. [Frontend → Core Mapping Matrix](#2-frontend--core-mapping-matrix)
3. [Data Flow Specification](#3-data-flow-specification)
4. [REST API Design](#4-rest-api-design)
5. [Customizer → Token → CSS Mapping](#5-customizer--token--css-mapping)
6. [Component Contracts](#6-component-contracts)
7. [Regression Strategy](#7-regression-strategy)

---

## 1. Architecture Decision

### 1.1 Why Server-Side Primary

The AETHER template was designed with `phantom-data.js` as a client-side WordPress bridge. However, for an integrated WordPress theme, server-side rendering is the stronger choice:

| Concern | Server-Side | Client-Side (REST) |
|---------|-------------|---------------------|
| **SEO** | HTML in initial response | Requires JavaScript execution |
| **Performance** | Single request | Additional API round-trip |
| **Caching** | Full page cache works | Partial cache only |
| **Simplicity** | Standard WP architecture | Duplicate data layer |
| **Accessibility** | Content available immediately | Progressive enhancement needed |
| **Analytics** | Page views tracked accurately | May miss if JS fails |

### 1.2 The Hybrid Approach

```
┌─────────────────────────────────────────────────────────┐
│                    RENDERING LAYER                       │
├─────────────────────────────────────────────────────────┤
│                                                          │
│   SERVER-SIDE (Primary)          CLIENT-SIDE (AJAX)     │
│   ─────────────────────          ────────────────────   │
│   • Page templates               • Live search           │
│   • WooCommerce templates        • AJAX filters          │
│   • Header/Footer                • Infinite scroll       │
│   • Content rendering            • Quick view modal      │
│   • SEO metadata                 • Wishlist toggle       │
│   • Structured data              • Mini cart updates     │
│   • Design tokens (CSS)          • Account dashboard     │
│   • Animation init               • Newsletter submit     │
│                                                          │
└─────────────────────────────────────────────────────────┘
```

### 1.3 What `phantom-data.js` Becomes

The existing `phantom-data.js` client-side bridge is **replaced** by server-side PHP renderers that output the same HTML structure. The `data-phantom-*` attribute system remains as the **contract** — but PHP populates the content instead of JavaScript.

For AJAX interactions, a lightweight `phantom-ajax.js` handles only:
- Search suggestions
- Filter/sort without page reload
- Infinite scroll pagination
- Quick view modals
- Wishlist add/remove
- Mini cart count/total updates
- Newsletter form submission

---

## 2. Frontend → Core Mapping Matrix

### 2.1 Global Components (Every Page)

```
GLOBAL COMPONENTS
├── Preloader
│   ├── Render: PHP (server-side)
│   ├── Data: Static HTML + Customizer toggle
│   ├── CSS: style.css (#preloader)
│   ├── JS: main.js (preloader logic)
│   └── Aureon Hook: wp_body_open
│
├── Fog System
│   ├── Render: PHP (server-side)
│   ├── Data: Static HTML (3 layers)
│   ├── CSS: style.css (#fog-system)
│   ├── JS: None (CSS animation only)
│   └── Aureon Hook: wp_body_open (after preloader)
│
├── Mobile Header (≤768px)
│   ├── Render: PHP (server-side)
│   ├── Data: Customizer settings (logo, announcement)
│   ├── CSS: responsive.css (.mobile-header)
│   ├── JS: main.js (hamburger toggle)
│   └── Aureon Hook: aureon_before_header
│
├── Mobile Slide-Out Menu
│   ├── Render: PHP (server-side)
│   ├── Data: WP_nav_menu (primary location)
│   ├── CSS: responsive.css (.mobile-menu-overlay)
│   ├── JS: main.js (open/close/overlay)
│   └── Aureon Hook: aureon_before_header
│
├── Announcement Bar
│   ├── Render: PHP (server-side)
│   ├── Data: Customizer (text, toggle, rotation)
│   ├── CSS: style.css (.announcement-bar)
│   ├── JS: main.js (rotation timer)
│   └── Aureon Hook: aureon_before_header
│
├── Desktop Header
│   ├── Render: PHP (server-side)
│   ├── Data: WP_nav_menu (primary), Customizer (logo, sticky toggle)
│   ├── CSS: style.css (.header, .header--scrolled, .header--hidden)
│   ├── JS: main.js (smart sticky, dropdown toggle)
│   ├── Aureon Hook: aureon_header
│   └── Aureon Element: Can be replaced by Block Element
│
├── Skip-to-Content
│   ├── Render: PHP (server-side)
│   ├── Data: Static HTML
│   ├── CSS: a11y.css (.skip-to-content)
│   └── JS: None
│
└── Footer
    ├── Render: PHP (server-side)
    ├── Data: WP_nav_menu (footer), Widget areas, Customizer (social, payments)
    ├── CSS: style.css (.footer)
    ├── JS: None
    ├── Aureon Hook: aureon_footer
    └── Aureon Element: Can be replaced by Block Element
```

### 2.2 Homepage

```
HOMEPAGE SECTIONS
├── Hero Slider
│   ├── Render: PHP (server-side)
│   ├── Data Source: Customizer (slides 1-3)
│   │   ├── slide_1_headline → data-phantom="hero_headline"
│   │   ├── slide_1_subline → data-phantom="hero_subline"
│   │   ├── slide_1_image → <img src>
│   │   ├── slide_1_cta_text → .btn text
│   │   └── slide_1_cta_url → .btn href
│   ├── CSS: style.css (.hero-slider, .hero-swiper, .hero-slide)
│   ├── JS: main.js (Swiper init, parallax, progress bar)
│   ├── Animation: animations.js (data-motion-text="words")
│   └── Aureon Hook: aureon_do_homepage_hero
│
├── Category Selector
│   ├── Render: PHP (server-side)
│   ├── Data Source: WooCommerce product categories
│   │   ├── cat_men_name → .category-name
│   │   ├── cat_men_count → .category-count
│   │   ├── cat_men_image → .category-card-bg img
│   │   └── cat_men_url → <a href>
│   ├── CSS: style.css (.categories, .category-grid, .category-card)
│   ├── JS: main.js (tilt via data-tilt)
│   ├── Animation: animations.js (data-reveal-group, data-reveal-item)
│   └── Aureon Hook: aureon_do_homepage_categories
│
├── Bestsellers
│   ├── Render: PHP (server-side)
│   ├── Data Source: WooCommerce (featured products / query)
│   │   ├── product.name → .product-name
│   │   ├── product.price → .product-price
│   │   ├── product.image → .product-image img
│   │   ├── product.rating → .product-rating
│   │   ├── product.badge → .product-badge
│   │   └── product.url → <a href>
│   ├── CSS: style.css (.products-grid, .product-card)
│   ├── JS: main.js (card click navigation)
│   ├── Animation: animations.js (data-reveal-group)
│   └── Aureon Hook: aureon_do_homepage_products
│
├── Reviews Carousel
│   ├── Render: PHP (server-side)
│   ├── Data Source: Customizer (reviews 1-4)
│   │   ├── review_1_author → .review-author
│   │   ├── review_1_role → .review-role
│   │   ├── review_1_text → .review-text
│   │   ├── review_1_rating → .review-stars count
│   │   └── review_1_avatar → .review-avatar text
│   ├── CSS: style.css (.reviews, .review-card)
│   ├── JS: main.js (Swiper init)
│   └── Aureon Hook: aureon_do_homepage_reviews
│
├── FAQ Accordion
│   ├── Render: PHP (server-side)
│   ├── Data Source: Customizer (FAQ items 1-6)
│   │   ├── faq_1_question → .faq-question span
│   │   └── faq_1_answer → .faq-answer p
│   ├── CSS: style.css (.faq-section, .faq-item)
│   ├── JS: main.js (accordion toggle, aria-expanded)
│   └── Aureon Hook: aureon_do_homepage_faq
│
├── Newsletter
│   ├── Render: PHP (server-side)
│   ├── Data Source: Customizer (toggle, text)
│   ├── CSS: style.css (.newsletter-section)
│   ├── JS: main.js (form submit, success state)
│   └── Aureon Hook: aureon_do_newsletter
│
└── All Homepage Hooks
    └── Registered in: aureon-studio.php (new module or functions.php)
```

### 2.3 Shop / Archive Pages

```
SHOP / ARCHIVE
├── Page Hero
│   ├── Render: PHP (server-side)
│   ├── Data Source: Customizer (hero image, title)
│   ├── CSS: style.css (.page-hero)
│   └── Aureon Hook: aureon_before_content
│
├── Filter Bar
│   ├── Render: PHP (server-side) + JS enhancement
│   ├── Data Source: WooCommerce product categories/tags
│   ├── CSS: style.css (.filter-bar, .filter-btn)
│   ├── JS: main.js (active state) + phantom-ajax.js (AJAX filter)
│   └── Aureon Hook: aureon_before_shop_loop
│
├── Product Grid
│   ├── Render: PHP (WooCommerce template override)
│   ├── Data Source: WC_Product_Query
│   │   ├── Uses wc_get_template_part('content-product')
│   │   └── Custom card template: template-parts/aether/product-card.php
│   ├── CSS: style.css (.shop-grid, .product-card)
│   ├── JS: main.js (tilt, zoom)
│   └── Aureon Hook: aureon_wc_before_shop_loop_item_title, aureon_wc_after_shop_loop_item_title
│
├── Pagination
│   ├── Render: PHP (server-side) + JS enhancement
│   ├── Data Source: WC_Product_Query pagination
│   ├── CSS: style.css (.shop-pagination)
│   ├── JS: phantom-ajax.js (AJAX pagination / infinite scroll)
│   └── Aureon Hook: aureon_after_shop_loop
│
└── WooCommerce Template Overrides
    └── woocommerce/
        ├── archive-product.php (shop page layout)
        ├── content-product.php (product card)
        ├── single-product.php (product detail)
        ├── cart/cart.php (cart page)
        ├── cart/cart-empty.php (empty cart)
        ├── checkout/form-checkout.php (checkout layout)
        └── myaccount/dashboard.php (account page)
```

### 2.4 Product Detail Page

```
PRODUCT DETAIL
├── Breadcrumb
│   ├── Render: PHP (WooCommerce breadcrumb)
│   ├── CSS: style.css (.pd-breadcrumb)
│   └── Aureon Hook: aureon_before_content
│
├── Product Hero (Gallery + Info)
│   ├── Render: PHP (WC template override)
│   ├── Data Source: WC_Product object
│   │   ├── product.name → .pd-title
│   │   ├── product.price → .pd-price
│   │   ├── product.images → .pd-gallery-swiper
│   │   ├── product.description → .pd-description
│   │   ├── product.categories → .pd-breadcrumb
│   │   ├── product.reviews → .pd-rating
│   │   ├── product.attributes (color) → .pd-color-options
│   │   ├── product.attributes (size) → .pd-size-grid
│   │   └── product.stock → .pd-add-to-cart
│   ├── CSS: style.css (.pd-hero, .pd-grid, .pd-gallery, .pd-info)
│   ├── JS: main.js (gallery swiper, color/size select, qty, sticky bar, zoom)
│   └── Aureon Hook: aureon_single_product_before_summary
│
├── Tech Specs Accordion
│   ├── Render: PHP (server-side)
│   ├── Data Source: Customizer or ACF fields
│   ├── CSS: style.css (.pd-specs, .pd-accordion)
│   ├── JS: main.js (accordion toggle)
│   └── Aureon Hook: aureon_single_product_after_summary
│
├── Customer Reviews
│   ├── Render: PHP (WooCommerce reviews)
│   ├── Data Source: WC_Comment_Query
│   ├── CSS: style.css (.pd-reviews, .pd-review-card)
│   └── Aureon Hook: aureon_single_product_after_tabs
│
├── Related Products
│   ├── Render: PHP (WC related products)
│   ├── Data Source: WC_Product::get_related()
│   ├── CSS: style.css (.pd-related)
│   ├── JS: main.js (Swiper init)
│   └── Aureon Hook: aureon_after_single_product_summary
│
└── Sticky Add-to-Cart Bar
    ├── Render: PHP (server-side)
    ├── Data Source: Same as product hero
    ├── CSS: style.css (.pd-sticky-bar)
    ├── JS: main.js (scroll trigger)
    └── Aureon Hook: wp_footer
```

### 2.5 Cart Page

```
CART
├── Cart Table
│   ├── Render: PHP (WC template override)
│   ├── Data Source: WC()->cart->get_cart()
│   │   ├── cart_item.product → .cart-item-name
│   │   ├── cart_item.quantity → .cart-item-qty
│   │   ├── cart_item.price → .cart-item-price
│   │   ├── cart_item.total → .cart-item-total
│   │   └── cart_item.image → .cart-item-img
│   ├── CSS: style.css (.cart-section, .cart-table) + inline in cart.html
│   ├── JS: main.js (qty controls) + WC AJAX (update/remove)
│   └── Template: woocommerce/cart/cart.php override
│
├── Cart Summary
│   ├── Render: PHP (WC cart totals)
│   ├── Data Source: WC()->cart
│   │   ├── subtotal → .summary-row
│   │   ├── total → .cart-total
│   │   └── shipping → .summary-row
│   └── Template: woocommerce/cart/cart.php (totals section)
│
└── Empty Cart State
    ├── Render: PHP (WC template)
    ├── CSS: style.css (.empty-cart)
    └── Template: woocommerce/cart/cart-empty.php
```

### 2.6 Checkout Page

```
CHECKOUT
├── Checkout Form
│   ├── Render: PHP (WC template override)
│   ├── Data Source: WC()->checkout
│   ├── CSS: style.css (.checkout-section) + inline in checkout.html
│   └── Template: woocommerce/checkout/form-checkout.php
│
├── Order Summary Sidebar
│   ├── Render: PHP (WC order review)
│   ├── Data Source: WC()->cart
│   └── Template: woocommerce/checkout/review-order.php
│
└── Place Order
    ├── Render: PHP (WC place order button)
    ├── JS: WC checkout validation + submission
    └── Template: woocommerce/checkout/place-order.php
```

### 2.7 Blog Pages

```
BLOG
├── Blog Listing
│   ├── Render: PHP (archive.php override)
│   ├── Data Source: WP_Query (posts)
│   │   ├── post.title → .blog-card-title
│   │   ├── post.excerpt → .blog-card-excerpt
│   │   ├── post.image → .blog-card-image img
│   │   ├── post.date → .blog-date
│   │   └── post.category → .blog-category
│   ├── CSS: style.css (.blog-grid, .blog-card)
│   └── Template: archive.php or template-parts/aether/blog-card.php
│
├── Single Blog Article
│   ├── Render: PHP (single.php override)
│   ├── Data Source: WP_Post
│   │   ├── post.title → .blog-hero-title
│   │   ├── post.content → .article-body
│   │   ├── post.author → .article-author
│   │   ├── post.date → .article-date
│   │   └── post.image → .blog-hero-image
│   ├── CSS: style.css (.blog-article, .article-body)
│   └── Template: single.php or template-parts/aether/blog-article.php
│
└── Related Posts
    ├── Render: PHP (server-side)
    ├── Data Source: WP_Query (same category/tags)
    └── Template: template-parts/aether/related-posts.php
```

### 2.8 Static Pages

```
STATIC PAGES
├── About
│   ├── Render: PHP (page template)
│   ├── Data Source: Customizer (team, mission, stats)
│   ├── CSS: style.css (.mission-grid, .team-grid, .stats-section)
│   └── Template: templates/page-about.php
│
├── Contact
│   ├── Render: PHP (page template)
│   ├── Data Source: Customizer (contact info) + CF7 or custom form
│   ├── CSS: style.css (.contact-section, .contact-grid)
│   └── Template: templates/page-contact.php
│
├── FAQ
│   ├── Render: PHP (page template)
│   ├── Data Source: Customizer (FAQ items)
│   ├── CSS: style.css (.faq-section)
│   └── Template: templates/page-faq.php
│
├── Wishlist
│   ├── Render: PHP (page template) + JS
│   ├── Data Source: YITH Wishlist or custom DB
│   ├── CSS: style.css (.wishlist-section)
│   └── Template: templates/page-wishlist.php
│
├── Login / My Account
│   ├── Render: PHP (WC template override)
│   ├── Data Source: WC()->session, wp_get_current_user()
│   ├── CSS: inline + style.css
│   └── Template: woocommerce/myaccount/form-login.php
│
├── 404
│   ├── Render: PHP (404.php override)
│   ├── Data Source: Customizer (error text)
│   ├── CSS: style.css (.error-page)
│   └── Template: 404.php
│
├── Coming Soon
│   ├── Render: PHP (page template)
│   ├── Data Source: Customizer (countdown date, email)
│   ├── CSS: inline + style.css
│   └── Template: templates/page-coming-soon.php
│
└── Legal Pages (Privacy, Terms, Cookies)
    ├── Render: PHP (page template)
    ├── Data Source: WP page content (Gutenberg blocks)
    ├── CSS: style.css (.content-page)
    └── Template: templates/page-legal.php
```

---

## 3. Data Flow Specification

### 3.1 Server-Side Rendering Flow (Primary)

```
┌─────────────────────────────────────────────────────────────┐
│                    SERVER-SIDE FLOW                          │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  1. WordPress Query                                         │
│     └── WP_Query determines template                        │
│                                                              │
│  2. Template Selection                                      │
│     ├── Aureon theme template hierarchy                     │
│     ├── WooCommerce template overrides                      │
│     └── Custom page templates (about, contact, etc.)        │
│                                                              │
│  3. Data Assembly (PHP)                                     │
│     ├── aureon_get_option($key) → Customizer values         │
│     ├── WC_Product / WC_Cart / WP_Post → entity data        │
│     ├── WP_nav_menu() → menu items                          │
│     └── get_sidebar() / widget areas → widgets              │
│                                                              │
│  4. Template Rendering (PHP)                                │
│     ├── header.php → Aureon hooks → AETHER header HTML      │
│     ├── content template → AETHER section HTML              │
│     └── footer.php → Aureon hooks → AETHER footer HTML      │
│                                                              │
│  5. CSS Generation                                          │
│     ├── aureon_base_css() → inline <style>                  │
│     ├── aureon_typography_css() → font imports + inline     │
│     ├── aureon_color_css() → color variables                │
│     ├── aureon_spacing_css() → responsive spacing           │
│     └── aether.css → static AETHER component styles         │
│                                                              │
│  6. JS Enqueue                                              │
│     ├── GSAP + ScrollTrigger (CDN)                          │
│     ├── Swiper (CDN)                                        │
│     ├── Lenis (CDN)                                         │
│     ├── aether-main.js (bundled)                            │
│     ├── aether-animations.js (bundled)                      │
│     └── aether-ajax.js (bundled, AJAX interactions only)    │
│                                                              │
│  7. HTML Response                                           │
│     └── Complete HTML with data-phantom-* attributes        │
│         populated server-side                               │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

### 3.2 AJAX Interaction Flow (Secondary)

```
┌─────────────────────────────────────────────────────────────┐
│                    AJAX FLOW                                 │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  1. User Interaction                                        │
│     └── Click/scroll/input triggers AJAX handler            │
│                                                              │
│  2. aether-ajax.js                                          │
│     ├── Validates input                                     │
│     ├── Shows loading state                                 │
│     └── Sends fetch() to /wp-json/aether/v1/...             │
│                                                              │
│  3. REST Endpoint (PHP)                                     │
│     ├── Permission check (nonce verification)               │
│     ├── Data query (WP_Query / WC_Product_Query)            │
│     ├── HTML fragment generation                            │
│     └── JSON response { html, data, meta }                  │
│                                                              │
│  4. DOM Update                                              │
│     ├── Parse JSON response                                 │
│     ├── Replace/update target element innerHTML             │
│     ├── Re-init GSAP ScrollTrigger for new elements         │
│     └── Update URL state (pushState for filters)            │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

### 3.3 Design Token Flow

```
┌─────────────────────────────────────────────────────────────┐
│                 DESIGN TOKEN FLOW                            │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  Customizer Controls                                        │
│     │                                                       │
│     ▼                                                       │
│  aureon_settings / aureon_color_settings /                  │
│  aureon_typography_settings / aureon_spacing_settings       │
│     │                                                       │
│     ▼                                                       │
│  aureon_get_option($key)                                    │
│     │                                                       │
│     ▼                                                       │
│  CSS Output Functions                                       │
│  ├── aureon_base_css()      → body colors, fonts            │
│  ├── aureon_color_css()     → 50+ color pickers             │
│  ├── aureon_typography_css()→ 14 font groups                │
│  └── aureon_spacing_css()   → padding/margin                │
│     │                                                       │
│     ▼                                                       │
│  wp_add_inline_style() / <style> in wp_head                │
│     │                                                       │
│     ▼                                                       │
│  CSS Custom Properties (:root)                              │
│  ├── --void, --surface, --chrome, --gold                   │
│  ├── --font-heading, --font-body                           │
│  ├── --section-padding, --container-max                    │
│  └── --announcement-height, --header-height                │
│     │                                                       │
│     ▼                                                       │
│  AETHER Component Styles                                    │
│  ├── style.css (references CSS vars)                       │
│  ├── motion.css (animation system)                         │
│  ├── responsive.css (breakpoints)                          │
│  └── a11y.css (accessibility)                              │
│     │                                                       │
│     ▼                                                       │
│  Live Preview in Customizer                                │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

---

## 4. REST API Design

### 4.1 Guiding Principles

REST endpoints are **only** for AJAX interactions where a full page reload would degrade UX. Every endpoint:

- Returns **HTML fragments** (not raw data) for direct DOM injection
- Includes a **nonce** for security verification
- Has **permission callbacks** (public vs. logged-in)
- Returns **404** for invalid requests
- Supports **caching headers** where appropriate

### 4.2 Endpoint Registry

**Namespace:** `aether/v1`

| Endpoint | Method | Auth | Cache | Purpose |
|----------|--------|------|-------|---------|
| `/search` | GET | Public | No | Live search suggestions |
| `/products/filter` | GET | Public | Yes (60s) | AJAX product filtering |
| `/products/page/{n}` | GET | Public | Yes (60s) | AJAX pagination |
| `/products/quick-view/{id}` | GET | Public | Yes (300s) | Quick view modal HTML |
| `/wishlist/toggle` | POST | Logged-in | No | Add/remove from wishlist |
| `/wishlist/count` | GET | Logged-in | No | Wishlist item count |
| `/cart/mini` | GET | Public (session) | No | Mini cart HTML fragment |
| `/cart/update` | POST | Public (session) | No | Update cart quantity |
| `/cart/remove` | POST | Public (session) | No | Remove cart item |
| `/newsletter/subscribe` | POST | Public | No | Newsletter email signup |
| `/reviews/submit` | POST | Logged-in | No | Submit product review |

### 4.3 Endpoint Contracts

#### `GET /wp-json/aether/v1/search`

**Purpose:** Live search with product/post suggestions.

**Request:**
```
GET /wp-json/aether/v1/search?q=void&limit=5
```

**Query Parameters:**
| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `q` | string | (required) | Search query, min 2 chars |
| `limit` | int | 5 | Max results per type |
| `type` | string | `all` | `product`, `post`, or `all` |

**Response (200):**
```json
{
  "html": "<div class=\"search-results\">...</div>",
  "data": {
    "products": [
      {
        "id": 123,
        "name": "AETHER Void Runner",
        "price": "$449",
        "image": "https://...",
        "url": "/product/void-runner/",
        "type": "product"
      }
    ],
    "posts": [
      {
        "id": 456,
        "title": "The Science of Cushioning",
        "excerpt": "...",
        "image": "https://...",
        "url": "/blog/science-of-cushioning/",
        "type": "post"
      }
    ]
  },
  "meta": {
    "total": 8,
    "query": "void"
  }
}
```

**Permission:** `read` (public)
**Nonce:** `aether_search_nonce` (verify via `wp_verify_nonce`)
**Error (400):** `{ "code": "invalid_query", "message": "Search query must be at least 2 characters" }`

---

#### `GET /wp-json/aether/v1/products/filter`

**Purpose:** Filter products by category, price, attributes without page reload.

**Request:**
```
GET /wp-json/aether/v1/products/filter?category=men&sort=price&order=asc&page=1&per_page=12
```

**Query Parameters:**
| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `category` | string | `all` | Category slug |
| `tag` | string | `all` | Tag slug |
| `sort` | string | `date` | `date`, `price`, `rating`, `popularity` |
| `order` | string | `desc` | `asc` or `desc` |
| `page` | int | 1 | Page number |
| `per_page` | int | 12 | Items per page |
| `min_price` | float | 0 | Minimum price |
| `max_price` | float | 9999 | Maximum price |
| `on_sale` | bool | false | Only sale items |
| `featured` | bool | false | Only featured |

**Response (200):**
```json
{
  "html": "<div class=\"shop-grid\">...(product cards)...</div>",
  "data": {
    "products": [...],
    "found_posts": 24,
    "max_num_pages": 2
  },
  "meta": {
    "page": 1,
    "per_page": 12,
    "total": 24,
    "total_pages": 2
  }
}
```

**Permission:** `read` (public)
**Cache:** `Cache-Control: public, max-age=60`

---

#### `GET /wp-json/aether/v1/products/quick-view/{id}`

**Purpose:** Load product detail in a modal without navigation.

**Request:**
```
GET /wp-json/aether/v1/products/quick-view/123
```

**Response (200):**
```json
{
  "html": "<div class=\"pd-modal\">...(product detail fragment)...</div>",
  "data": {
    "id": 123,
    "name": "AETHER Void Runner",
    "price": "$449",
    "description": "...",
    "images": ["..."],
    "attributes": {
      "color": ["Obsidian", "Phantom White"],
      "size": ["7", "8", "9", "10", "11", "12"]
    }
  }
}
```

**Permission:** `read` (public)
**Cache:** `Cache-Control: public, max-age=300`

---

#### `POST /wp-json/aether/v1/wishlist/toggle`

**Purpose:** Add/remove product from wishlist.

**Request:**
```
POST /wp-json/aether/v1/wishlist/toggle
Content-Type: application/json

{
  "product_id": 123
}
```

**Response (200):**
```json
{
  "html": "<i class=\"fas fa-heart\"></i>",
  "data": {
    "added": true,
    "count": 5
  }
}
```

**Permission:** `read` (logged-in user)
**Nonce:** Required (`aether_wishlist_nonce`)

---

#### `POST /wp-json/aether/v1/cart/update`

**Purpose:** Update cart item quantity via AJAX.

**Request:**
```
POST /wp-json/aether/v1/cart/update
Content-Type: application/json

{
  "cart_item_key": "abc123",
  "quantity": 2
}
```

**Response (200):**
```json
{
  "html": "<div class=\"cart-item\">...(updated row)...</div>",
  "data": {
    "item_total": "$898.00",
    "cart_total": "$1,347.00",
    "cart_count": 3
  }
}
```

**Permission:** `read` (uses WC session)
**Nonce:** Required (`aether_cart_nonce`)

---

#### `POST /wp-json/aether/v1/newsletter/subscribe`

**Purpose:** Subscribe email to newsletter.

**Request:**
```
POST /wp-json/aether/v1/newsletter/subscribe
Content-Type: application/json

{
  "email": "user@example.com"
}
```

**Response (200):**
```json
{
  "html": "<div class=\"newsletter-success\">...</div>",
  "data": {
    "success": true
  }
}
```

**Permission:** `read` (public)
**Nonce:** Required (`aether_newsletter_nonce`)
**Validation:** `is_email()` check, rate limiting (1 per IP per minute)

---

### 4.4 Security Model

```php
// Every endpoint follows this pattern:
register_rest_route('aether/v1', '/search', array(
    'methods'             => 'GET',
    'callback'            => 'aether_rest_search',
    'permission_callback' => '__return_true', // public
    'args'                => array(
        'q' => array(
            'required'          => true,
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'validate_callback' => function($param) {
                return strlen($param) >= 2;
            },
        ),
    ),
));

// For authenticated endpoints:
register_rest_route('aether/v1', '/wishlist/toggle', array(
    'methods'             => 'POST',
    'callback'            => 'aether_rest_wishlist_toggle',
    'permission_callback' => function() {
        return is_user_logged_in();
    },
));
```

**Nonce verification:**
```php
// In callback:
if (!wp_verify_nonce($_POST['_wpnonce'], 'aether_wishlist_nonce')) {
    return new WP_Error('invalid_nonce', 'Security check failed', array('status' => 403));
}
```

---

## 5. Customizer → Token → CSS Mapping

### 5.1 Color Mapping

| AETHER CSS Var | Customizer Setting Key | Customizer Label | Default | CSS Selector |
|----------------|----------------------|------------------|---------|--------------|
| `--void` | `aureon_color_settings[background_color]` | Body Background | `#09090B` | `body`, `.page-content` |
| `--surface` | `aureon_color_settings[content_background]` | Content Background | `#141416` | `.product-card`, `.review-card`, `.faq-item` |
| `--chrome` | `aureon_color_settings[text_color]` | Text Color | `#A8B5C0` | `body`, `.footer-tagline` |
| `--gold` | `aureon_color_settings[link_color]` | Link/Accent Color | `#C8956C` | `a`, `.btn-primary`, `.hero-headline-accent` |
| `--white` | (hardcoded) | — | `#FFFFFF` | Headings, hero text |
| `--black` | (hardcoded) | — | `#000000` | — |

**Additional color mappings:**

| AETHER Usage | Customizer Key | Default |
|--------------|---------------|---------|
| Announcement bar bg | `aureon_color_settings[secondary_background]` | `#141416` |
| Announcement bar text | `aureon_color_settings[secondary_text_color]` | `#A8B5C0` |
| Header bg (scrolled) | `aureon_color_settings[secondary_background]` | `#141416` |
| Button hover | `aureon_color_settings[link_hover_color]` | `#D4A574` |
| Footer bg | `aureon_color_settings[footer_background]` | `#09090B` |
| Footer text | `aureon_color_settings[footer_text_color]` | `#A8B5C0` |
| Footer links | `aureon_color_settings[footer_link_color]` | `#C8956C` |
| Product badge bg | `aureon_color_settings[secondary_background]` | `#141416` |
| Sale badge bg | `aureon_color_settings[link_color]` | `#C8956C` |
| Input border | `aureon_color_settings[borders_color]` | `#2A2A2E` |
| Input focus | `aureon_color_settings[link_color]` | `#C8956C` |

### 5.2 Typography Mapping

| AETHER CSS Var | Customizer Setting Key | Customizer Group | Default |
|----------------|----------------------|------------------|---------|
| `--font-heading` | `aureon_typography_settings[heading_font]` | Headings | `Cabinet Grotesk` |
| `--font-body` | `aureon_typography_settings[body_font]` | Body | `Satoshi` |

**Font weight mapping:**

| AETHER Usage | Customizer Key | Default |
|--------------|---------------|---------|
| Heading weight | `aureon_typography_settings[heading_weight]` | `700` |
| Body weight | `aureon_typography_settings[body_weight]` | `400` |
| Nav weight | `aureon_typography_settings[nav_weight]` | `500` |
| Button weight | `aureon_typography_settings[button_weight]` | `700` |
| Footer weight | `aureon_typography_settings[footer_weight]` | `400` |

**Font size mapping (responsive):**

| AETHER Element | Desktop | Tablet | Mobile | Customizer Control |
|----------------|---------|--------|--------|-------------------|
| Hero headline | `clamp(2.5rem, 6vw, 5rem)` | `clamp(2rem, 5vw, 3.5rem)` | `clamp(1.6rem, 8vw, 2.4rem)` | `heading_1_size` |
| Section title | `clamp(1.8rem, 4vw, 2.5rem)` | `clamp(1.5rem, 3vw, 2rem)` | `clamp(1.3rem, 5vw, 1.6rem)` | `heading_2_size` |
| Body text | `1rem` | `1rem` | `0.95rem` | `body_size` |
| Nav links | `0.9rem` | `0.85rem` | `1rem` | `nav_size` |
| Product name | `1.05rem` | `0.95rem` | `0.9rem` | `body_size` (derived) |

### 5.3 Spacing Mapping

| AETHER CSS Var | Customizer Setting Key | Default | Breakpoints |
|----------------|----------------------|---------|-------------|
| `--section-padding` | `aureon_spacing_settings[content_padding_top]` | `100px 0` | 140/120/100/80/60/50/40/30px |
| `--container-max` | `aureon_settings[container_width]` | `1200px` | 1400/1300/1140/100%/100%/100%/100%/100% |
| `--announcement-height` | (derived from padding) | `40px` | — |
| `--header-height` | `aureon_spacing_settings[header_padding_top]` + padding | `80px` | 70/60/56px |

### 5.4 Layout Mapping

| AETHER Setting | Customizer Key | Options |
|----------------|---------------|---------|
| Container width | `aureon_settings[container_width]` | 960/1080/1200/1400px |
| Header layout | `aureon_settings[header_layout]` | fluid / contained |
| Nav alignment | `aureon_settings[nav_alignment]` | left / center / right |
| Product columns | `aureon_woocommerce_settings[shop_columns]` | 2/3/4 |
| Products per page | `aureon_woocommerce_settings[shop_posts_per_page]` | 12/24/36/48 |
| Blog layout | `aureon_settings[content_layout]` | right-sidebar / left-sidebar / no-sidebar |

### 5.5 CSS Output Strategy

**New file: `assets/css/aether-tokens.css`**

This file bridges Customizer settings to AETHER CSS variables:

```css
/* Generated by aureon_base_css() + aureon_color_css() */
:root {
    /* Colors — populated from Customizer */
    --void: {{ aureon_get_option('background_color', '#09090B') }};
    --surface: {{ aureon_get_option('content_background', '#141416') }};
    --chrome: {{ aureon_get_option('text_color', '#A8B5C0') }};
    --gold: {{ aureon_get_option('link_color', '#C8956C') }};

    /* Typography — populated from Customizer */
    --font-heading: '{{ aureon_get_option('heading_font', 'Cabinet Grotesk') }}', sans-serif;
    --font-body: '{{ aureon_get_option('body_font', 'Satoshi') }}', sans-serif;

    /* Spacing — populated from Customizer */
    --container-max: {{ aureon_get_option('container_width', '1200') }}px;
    --section-padding: {{ aureon_get_option('content_padding_top', '100') }}px 0;
}
```

This is output via `wp_add_inline_style('aether-style', $css)` in `aureon_scripts()`.

---

## 6. Component Contracts

### 6.1 Hero Slider Component

```
COMPONENT: Hero Slider
FILE: template-parts/aether/hero-slider.php

INPUTS:
├── slides (array, required)
│   └── [0..2]
│       ├── headline (string, required) — max 60 chars
│       ├── subline (string, required) — max 200 chars
│       ├── image (URL, required) — desktop background
│       ├── mobile_image (URL, optional) — mobile background
│       ├── cta_text (string, required) — max 30 chars
│       ├── cta_url (URL, required)
│       └── overlay_opacity (float, 0-1, default: 0.4)
├── autoplay (bool, default: true)
├── interval (int, ms, default: 6000)
├── show_navigation (bool, default: true)
├── show_progress (bool, default: true)
├── show_particles (bool, default: true)
├── show_fog (bool, default: true)
└── animation_preset (string, default: 'words')

DEFAULTS:
{
    "autoplay": true,
    "interval": 6000,
    "show_navigation": true,
    "show_progress": true,
    "show_particles": true,
    "show_fog": true,
    "animation_preset": "words"
}

ERROR BEHAVIOR:
- Missing slide image → fallback to Customizer hero_image
- Missing headline → "Welcome" fallback text
- Missing CTA → button hidden (display:none)
- No slides → component not rendered

OUTPUT: HTML matching AETHER .hero-slider structure exactly
```

### 6.2 Product Card Component

```
COMPONENT: Product Card
FILE: template-parts/aether/product-card.php

INPUTS:
├── product_id (int, required) — WC product ID
├── show_badge (bool, default: true)
├── show_rating (bool, default: true)
├── show_actions (bool, default: true) — wishlist + quick view
├── badge_override (string, optional) — "New", "Sale", "Limited"
└── animation (string, default: 'reveal-item')

DERIVED FROM WC_Product:
├── name → .product-name
├── get_price_html() → .product-price
├── get_image() → .product-image img
├── get_rating_html() → .product-rating
├── get_permalink() → <a href>
├── is_on_sale() → .badge-sale
├── get_attribute('pa_color') → color options
└── get_stock_status() → .pd-add-to-cart state

DEFAULTS:
{
    "show_badge": true,
    "show_rating": true,
    "show_actions": true,
    "animation": "reveal-item"
}

ERROR BEHAVIOR:
- Product not found → silent skip (don't render)
- No image → placeholder image
- No price → "Price on request"
- Out of stock → "Out of Stock" badge + disabled CTA

OUTPUT: HTML matching AETHER .product-card structure exactly
```

### 6.3 Cart Item Component

```
COMPONENT: Cart Item Row
FILE: template-parts/aether/cart-item.php

INPUTS:
├── cart_item_key (string, required) — WC cart item key
├── cart_item (array, required) — from WC()->cart->get_cart()
└── show_remove (bool, default: true)

DERIVED FROM cart_item:
├── data['product_id'] → product link
├── data['quantity'] → .qty-value
├── data['variation_id'] → variant info
├── $product->get_name() → .cart-item-name
├── $product->get_image() → .cart-item-img
├── WC_Cart::get_product_price() → .cart-item-price
├── WC_Cart::get_product_subtotal() → .cart-item-total
└── WC_Product_variation::get_attributes() → .cart-item-variant

DEFAULTS:
{
    "show_remove": true
}

ERROR BEHAVIOR:
- Product deleted → "Product no longer available" message
- Zero quantity → auto-remove from cart
- Price calculation error → show last known price

OUTPUT: HTML matching AETHER .cart-item structure exactly
```

### 6.4 Blog Card Component

```
COMPONENT: Blog Card
FILE: template-parts/aether/blog-card.php

INPUTS:
├── post_id (int, required) — WP post ID
├── show_category (bool, default: true)
├── show_excerpt (bool, default: true)
├── show_date (bool, default: true)
├── excerpt_length (int, default: 20) — word count
└── animation (string, default: 'reveal-item')

DERIVED FROM WP_Post:
├── get_the_title() → .blog-card-title
├── get_the_excerpt() → .blog-card-excerpt
├── get_the_post_thumbnail() → .blog-card-image img
├── get_the_date() → .blog-date
├── get_the_category() → .blog-category
└── get_permalink() → <a href>

DEFAULTS:
{
    "show_category": true,
    "show_excerpt": true,
    "show_date": true,
    "excerpt_length": 20,
    "animation": "reveal-item"
}

ERROR BEHAVIOR:
- Post not found → silent skip
- No featured image → placeholder
- No excerpt → auto-generated from content

OUTPUT: HTML matching AETHER .blog-card structure exactly
```

### 6.5 Newsletter Form Component

```
COMPONENT: Newsletter Form
FILE: template-parts/aether/newsletter.php

INPUTS:
├── title (string, default: "JOIN THE VOID")
├── text (string, default: "Subscribe for exclusive drops...")
├── button_text (string, default: "Subscribe")
├── privacy_note (string, default: "No spam. Unsubscribe anytime.")
├── success_message (string, default: "Welcome to the void. Check your inbox.")
├── show_glow (bool, default: true)
└── ajax (bool, default: true) — use AJAX or page reload

DEFAULTS:
{
    "title": "JOIN THE VOID",
    "text": "Subscribe for exclusive drops, early access, and AETHER news.",
    "button_text": "Subscribe",
    "privacy_note": "No spam. Unsubscribe anytime.",
    "success_message": "Welcome to the void. Check your inbox.",
    "show_glow": true,
    "ajax": true
}

ERROR BEHAVIOR:
- Invalid email → "Please enter a valid email" (client-side)
- Duplicate email → "You're already subscribed" (server-side)
- API failure → "Something went wrong. Try again." (client-side)
- Rate limit → "Please wait before subscribing again."

OUTPUT: HTML matching AETHER .newsletter-section structure exactly
```

### 6.6 Review Card Component

```
COMPONENT: Review Card
FILE: template-parts/aether/review-card.php

INPUTS:
├── author (string, required) — max 40 chars
├── role (string, required) — max 40 chars
├── rating (int, 1-5, required)
├── text (string, required) — max 500 chars
├── verified (bool, default: true)
├── date (string, optional) — relative date
└── avatar_type (string, default: 'initials') — 'initials' or 'image'

DEFAULTS:
{
    "verified": true,
    "avatar_type": "initials"
}

ERROR BEHAVIOR:
- Missing author → "Anonymous"
- Missing text → component hidden
- Rating out of range → clamped to 1-5

OUTPUT: HTML matching AETHER .review-card structure exactly
```

---

## 7. Regression Strategy

### 7.1 Verification Matrix

For every component integrated, verify:

| Check | Method | Tool | Pass Criteria |
|-------|--------|------|---------------|
| **HTML Structure** | Compare DOM | Playwright `page.content()` | Identical class names, hierarchy, attributes |
| **CSS Classes** | Compare rendered styles | Playwright `getComputedStyle()` | Same visual properties at same breakpoints |
| **JS Behavior** | Functional tests | Playwright interactions | Same click/hover/scroll responses |
| **Animations** | Visual comparison | Playwright screenshots | Same entrance animations, timing |
| **Responsive** | Multi-viewport | Playwright `page.setViewportSize()` | Same layout at 1920/1440/1200/1024/768/576/480/360 |
| **Accessibility** | Automated audit | axe-core | 0 violations |
| **SEO** | HTML validation | Schema.org validator | Same structured data, meta tags |
| **Performance** | Lighthouse | Lighthouse CI | Same or better scores |

### 7.2 Page-by-Page Regression Checklist

```
HOMEPAGE
├── [ ] Preloader animates and disappears
├── [ ] Fog system renders (3 layers)
├── [ ] Announcement bar visible with correct text
├── [ ] Header sticks on scroll, hides/shows correctly
├── [ ] Mobile menu opens/closes (≤768px)
├── [ ] Hero slider cycles through 3 slides
├── [ ] Hero parallax works on mouse move
├── [ ] Hero progress bar animates
├── [ ] Category cards reveal on scroll
├── [ ] Category cards tilt on hover
├── [ ] Product cards reveal on scroll
├── [ ] Product cards tilt on hover
├── [ ] Product image zoom on hover
├── [ ] Reviews carousel scrolls
├── [ ] FAQ accordion expands/collapses
├── [ ] Newsletter form submits + shows success
├── [ ] Footer links correct
├── [ ] Social icons link correctly
├── [ ] Skip-to-content link works

SHOP PAGE
├── [ ] Page hero renders with correct background
├── [ ] Filter buttons toggle active state
├── [ ] Product grid renders correct number of columns
├── [ ] Product badges display correctly
├── [ ] Pagination works (click + AJAX)
├── [ ] Product card click navigates to detail
├── [ ] Newsletter works

PRODUCT DETAIL
├── [ ] Breadcrumb correct
├── [ ] Gallery swiper works (main + thumbs)
├── [ ] Color selection updates display
├── [ ] Size selection updates display
├── [ ] Quantity +/- works (1-10 range)
├── [ ] Add to cart button functional
├── [ ] Sticky bar appears on scroll
├── [ ] Tech specs accordion works
├── [ ] Size guide modal opens/closes
├── [ ] Reviews section renders
├── [ ] Related products carousel works
├── [ ] Magnifying glass zoom works on gallery

CART
├── [ ] Cart items render correctly
├── [ ] Quantity update works (AJAX)
├── [ ] Remove item works (AJAX)
├── [ ] Cart totals update correctly
├── [ ] Empty cart state shows
├── [ ] Continue shopping link works
├── [ ] Checkout button navigates

CHECKOUT
├── [ ] Checkout form renders
├── [ ] Order summary shows correct items/totals
├── [ ] Form validation works
├── [ ] Place order button submits
├── [ ] Secure badge visible

BLOG
├── [ ] Blog grid renders
├── [ ] Blog cards reveal on scroll
├── [ ] Category badges display
├── [ ] Pagination works
├── [ ] Single article renders correctly
├── [ ] Article meta (author, date, read time) correct
├── [ ] Related posts render

STATIC PAGES
├── [ ] About page renders (mission, team, stats)
├── [ ] Contact page renders (form, info cards)
├── [ ] FAQ page renders (accordion)
├── [ ] Wishlist page renders
├── [ ] Login page renders (form, social buttons)
├── [ ] 404 page renders
├── [ ] Coming Soon renders (countdown)
├── [ ] Legal pages render (content)
```

### 7.3 Animation Regression

| Animation | Trigger | Expected Behavior | Verification |
|-----------|---------|-------------------|--------------|
| Text word reveal | Scroll into view | Words slide up with blur, staggered 55ms | Screenshot comparison |
| Card reveal | Scroll into view | Cards slide from left/right, 80px offset | Screenshot comparison |
| Hero slide transition | Autoplay / click | Fade + parallax shift | Video capture |
| Magnetic hover | Mouse near button | Button follows cursor 12% offset | Interaction test |
| Card tilt | Mouse over card | Subtle 3D tilt effect | Interaction test |
| Image zoom | Mouse over product | 2.5x magnifying lens | Interaction test |
| Fog animation | Page load | Continuous CSS drift, 3 layers | Visual inspection |
| Fire sparks | Page load | Canvas particles (ember/glow/cinder) | Visual inspection |
| Preloader | Page load | Progress bar fills, fades out | Timing test |
| Smooth scroll | Click anchor | Lenis smooth scroll to target | Scroll position test |

### 7.4 Accessibility Regression

| Feature | Test | Tool | Pass |
|---------|------|------|------|
| Skip link | Tab to first element | axe-core | Visible, focuses #main |
| Focus visible | Tab through all interactive elements | Manual + axe | 2px outline on all |
| Reduced motion | `prefers-reduced-motion: reduce` | Emulate in DevTools | All animations disabled |
| High contrast | `forced-colors: active` | Windows High Contrast | Borders visible |
| Touch targets | All interactive elements | axe-core | min 44x44px on touch |
| ARIA labels | All icons/buttons | axe-core | aria-label present |
| ARIA expanded | FAQ accordion | axe-core | Toggles correctly |
| Screen reader | Content announced | NVDA / VoiceOver | All content accessible |
| Print | Print stylesheet | Print preview | Clean layout |

### 7.5 SEO Regression

| Element | Check | Expected |
|---------|-------|----------|
| Title tag | `<title>` | Page-specific, includes brand |
| Meta description | `<meta name="description">` | Unique per page |
| Canonical URL | `<link rel="canonical">` | Correct URL |
| Open Graph | `og:title`, `og:description`, `og:image` | Present on all pages |
| Twitter Card | `twitter:card`, `twitter:title` | Present on all pages |
| Structured data | JSON-LD | Organization on all, Product on product pages |
| H1 tag | Single `<h1>` per page | Correct content |
| Image alt | All `<img>` | Descriptive alt text |
| Semantic HTML | `<main>`, `<header>`, `<footer>`, `<nav>` | Correct usage |
| Internal linking | All `<a href>` | No broken links |

### 7.6 Performance Regression

| Metric | Target | Tool |
|--------|--------|------|
| LCP | < 2.5s | Lighthouse |
| FID | < 100ms | Lighthouse |
| CLS | < 0.1 | Lighthouse |
| Total bundle size | < 150KB (excl. CDN) | Bundle analyzer |
| CSS size | < 50KB (excl. CDN) | Stylelint |
| JS size | < 80KB (excl. CDN) | Bundle analyzer |
| Image total | < 2MB per page | Network tab |
| Time to Interactive | < 3s | Lighthouse |

---

## Appendix A: File Structure After Integration

```
aureon/theme/
├── functions.php                    (MODIFIED — enqueue AETHER assets)
├── header.php                       (MODIFIED — AETHER header markup)
├── footer.php                       (MODIFIED — AETHER footer markup)
├── index.php                        (MODIFIED — AETHER homepage layout)
├── page.php                         (MODIFIED — AETHER page template)
├── single.php                       (MODIFIED — AETHER single post)
├── archive.php                      (MODIFIED — AETHER blog listing)
├── search.php                       (MODIFIED — AETHER search results)
├── 404.php                          (MODIFIED — AETHER 404 page)
├── style-aether.css                 (NEW — AETHER base styles, tokens)
├── assets/
│   └── css/
│       └── aether-tokens.css        (NEW — Customizer → CSS vars)
├── template-parts/
│   └── aether/
│       ├── hero-slider.php          (NEW)
│       ├── product-card.php         (NEW)
│       ├── cart-item.php            (NEW)
│       ├── blog-card.php            (NEW)
│       ├── review-card.php          (NEW)
│       ├── newsletter.php           (NEW)
│       ├── faq-accordion.php        (NEW)
│       ├── category-card.php        (NEW)
│       └── page-hero.php            (NEW)
├── templates/
│   ├── page-about.php               (NEW)
│   ├── page-contact.php             (NEW)
│   ├── page-faq.php                 (NEW)
│   ├── page-wishlist.php            (NEW)
│   ├── page-coming-soon.php         (NEW)
│   └── page-legal.php               (NEW)
└── woocommerce/
    ├── archive-product.php          (NEW — shop layout)
    ├── content-product.php          (NEW — product card)
    ├── single-product.php           (NEW — product detail)
    ├── cart/
    │   ├── cart.php                 (NEW — cart layout)
    │   └── cart-empty.php           (NEW — empty state)
    ├── checkout/
    │   ├── form-checkout.php        (NEW — checkout layout)
    │   └── review-order.php         (NEW — order summary)
    └── myaccount/
        ├── dashboard.php            (NEW — account dashboard)
        └── form-login.php           (NEW — login/register)

aureon/plugin/
├── aureon-studio.php                (MODIFIED — register REST routes)
├── inc/
│   ├── class-rest-aether.php        (NEW — AJAX endpoint handlers)
│   └── class-aether-renderer.php    (NEW — server-side HTML renderers)
└── assets/
    └── js/
        └── aether-ajax.js           (NEW — AJAX interaction handlers)

aureon-child/                        (NEW — child theme for overrides)
├── style.css
├── functions.php
└── ...
```

---

## Appendix B: Implementation Phases (Post-Approval)

| Phase | Description | Est. Time |
|-------|-------------|-----------|
| 18.1 | Token bridge (Customizer → CSS vars) | 2h |
| 18.2 | Global components (header, footer, preloader, fog) | 4h |
| 18.3 | Homepage sections (hero, categories, products, reviews, FAQ, newsletter) | 6h |
| 18.4 | WooCommerce templates (shop, product detail, cart, checkout) | 8h |
| 18.5 | Blog templates (archive, single, related) | 3h |
| 18.6 | Static page templates (about, contact, FAQ, legal, 404, coming soon) | 4h |
| 18.7 | REST API endpoints (search, filters, AJAX interactions) | 4h |
| 18.8 | JS integration (animations, smooth scroll, interactions) | 3h |
| 18.9 | Regression testing (all pages, all breakpoints) | 4h |
| 18.10 | Accessibility audit + fixes | 2h |
| 18.11 | Performance optimization | 2h |
| **Total** | | **~42h** |

---

## Appendix C: Decisions — LOCKED

All open questions have been resolved. See "Locked Decisions" at top of document.

| Question | Decision |
|----------|----------|
| Child theme or direct edits? | ✅ Standalone Aureon theme |
| WooCommerce integration | ✅ Hybrid (hooks + minimal overrides) |
| Firebase auth | ✅ Bridge WordPress + Firebase |
| `phantom-data.js` fate | ✅ Reduce to AJAX-only |
| CDN or bundled assets | ✅ Bundle locally |

---

## Appendix D: Phase 18.1 — Frontend Foundation (VERIFIED)

**Date:** 2026-08-05
**Status:** ✅ VERIFIED — 0 console errors, all sections rendering

### Files Created/Modified

| File | Purpose |
|------|---------|
| `inc/aether-enqueue.php` | Enqueues all AETHER CSS/JS + CDN libs + localizes phantomData |
| `inc/aether-hooks.php` | Hooks AETHER components into Aureon action system |
| `functions.php` | Added includes for aether-enqueue.php and aether-hooks.php |
| `front-page.php` | AETHER homepage template (hero, categories, bestsellers, reviews, FAQ) |
| `assets/aether/` | All AETHER assets (CSS, JS, images, vendor libs — 171 images) |

### Verification Results

| Check | Result |
|-------|--------|
| CSS loading | ✅ All 8 stylesheets loaded (bootstrap, swiper, FA, style, motion, responsive, a11y, animate, owl) |
| JS loading | ✅ All scripts loaded (GSAP, ScrollTrigger, Swiper, Lenis, Bootstrap, phantom-bridge, phantom-data, animations, effects, main + 15 vendor) |
| Console errors | ✅ 0 errors |
| Header | ✅ AETHER nav renders, Aureon default removed |
| Hero slider | ✅ Swiper with 3 slides |
| Categories | ✅ "Find Your Fit" section |
| Bestsellers | ✅ "Most Loved" with 4 product cards |
| Reviews | ✅ "What Athletes Say" with 4.9 rating |
| FAQ | ✅ "Got Questions?" accordion |
| Footer | ✅ Brand, links, social, payments |
| Fog effects | ✅ Particle system active |
| Dark theme | ✅ Void aesthetic with gold accents |

### Bug Fixes Applied

1. `front-page.php`: Added `get_header()`/`get_footer()` calls (was missing `<head>` and `wp_head()`)
2. `front-page.php`: Restored `$aether` variable after header require removal
3. `aether-hooks.php`: Added `remove_action` to disable Aureon's default header
4. `phantom-data.js`: Added missing `init()` function definition
5. `counter.js`: Added WOW.js typeof guard
6. `filter-button.js`: Added null guard for shop-only elements
7. `loadmore.js`: Added null guard for blog-only elements
8. `product-quantity.js`: Added null guard for product-page-only elements
9. `aether-enqueue.php`: Added `filemtime()` cache-busting to version strings
