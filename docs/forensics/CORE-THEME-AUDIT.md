# AUREON Core Theme — Forensic Audit

**Date:** 2026-08-26
**Commit:** `1d8051ea6c67e2c622fc6e89f93b4279238bbd70`
**Branch:** `main`
**Location:** `aureon/theme/`

---

## 1. Request Lifecycle

```
WordPress Core Request
    │
    ▼
functions.php (bootstrap)
    ├─ AUREON_VERSION = '3.6.1'
    ├─ aureon_setup() @ after_setup_theme
    │   └─ register_nav_menus, add_theme_support (woocommerce, title-tag, html5, post-thumbnails)
    └─ requires 15+ inc/ files in sequence:
         theme-functions → defaults → class-css → css-output
         general → customizer → markup → typography
         plugin-compat → block-editor → class-typography
         class-typography-migration → class-html-attributes
         class-theme-update → class-rest → deprecated
         frontend.php  ← THE CRITICAL AETHER INTEGRATION POINT
         (admin-only: meta-box → class-dashboard)
    └─ requires inc/structure/*.php (9 files)
         │
         ▼
inc/frontend.php (AETHER boot sequence)
    ├─ require_once /../../frontend/views/loader.php   ← ENGINE KERNEL
    ├─ require_once inc/aether-tokens.php
    ├─ require_once inc/aether-security.php
    ├─ require_once inc/aether-seo.php
    ├─ require_once inc/aether-newsletter.php
    ├─ require_once inc/aether-ajax.php
    ├─ require_once inc/aether-cart.php
    ├─ require_once inc/aether-analytics.php
    ├─ require_once inc/aether-performance.php
    ├─ Register nav menus: 'primary' + 'footer' (priority 20)
    ├─ aureon_aether_frontend_boot() @ priority 30 → calls aether_frontend_boot()
    ├─ aureon_aether_suppress_theme_output() @ priority 1000
    │   └─ Dequeues ALL legacy theme CSS/JS
    ├─ aureon_aether_enqueue_assets() @ priority 20
    │   └─ IF design === 'luxury': loads CDN stack + local CSS/JS
    │   └─ ELSE: returns (pack owns assets)
    └─ aureon_aether_wc_page_templates() @ template_include priority 99
         │
         ▼
header.php
    ├─ <!DOCTYPE html>, <html>, <head>, <body>
    ├─ wp_head()
    └─ aether_compose_header()  ← ENGINE RENDERS FULL SHELL
         │
         ▼
Page Template (front-page.php / single.php / archive-product.php / etc.)
    ├─ get_header() + get_footer()
    ├─ Content = aether_render_section() / aether_render_component() calls
    └─ Data comes from adapters + Customizer options + design pack tokens
         │
         ▼
footer.php
    ├─ aether_compose_footer()
    ├─ aether_render_component('commerce/quick-view')
    └─ wp_footer()
```

---

## 2. Engine Kernel (External to Theme)

Located at `frontend/views/loader.php`, loaded via `inc/frontend.php:16`.

### Kernel Files

| File | Lines | Role | Classification |
|------|-------|------|----------------|
| `views/loader.php` | 63 | Entry point, wiring | CORE INFRASTRUCTURE |
| `views/design.php` | 197 | Pack resolution, manifest | CORE INFRASTRUCTURE |
| `views/registry.php` | 50 | Section registration | CORE INFRASTRUCTURE |
| `views/renderer.php` | 178 | Component + section rendering | CORE INFRASTRUCTURE |
| `views/viewmodel.php` | 134 | Data normalization | CORE INFRASTRUCTURE |
| `views/assets.php` | 140 | Asset pipeline | CORE INFRASTRUCTURE |
| `views/composer.php` | 72 | Shell composition | CORE INFRASTRUCTURE |
| `tokens/tokens.php` | 607 | Default option values | CORE INFRASTRUCTURE |
| `manifest/components.php` | 78 | Component template map | CORE INFRASTRUCTURE |

### Design Resolution Chain

1. `aether_active_design()` → resolves active slug: `AETHER_DESIGN` constant > `aether_active_design` option > `'luxury'`
2. `aether_active_design_dir()` → returns pack directory path or `''` for luxury
3. `aether_resolve_design_path($relative)` → checks pack directory first, falls back to engine tree

### Rendering Flow

```
aether_render_section($id, $data)
  → registry lookup → adapter invocation → data normalization
  → aether_resolve_design_path() → template include

aether_render_component($id, $data)
  → manifest lookup → aether_resolve_design_path() → template include
```

---

## 3. File Classification

### Theme Root — Template Files

| File | Classification | Purpose | Verdict |
|------|---------------|---------|---------|
| `functions.php` | CORE INFRASTRUCTURE | Bootstrap | KEEP |
| `style.css` | CORE INFRASTRUCTURE | Theme metadata | KEEP |
| `header.php` | DATA CONTRACT | Shell header | KEEP |
| `footer.php` | DATA CONTRACT | Shell footer | KEEP |
| `front-page.php` | DATA CONTRACT | Homepage sections | KEEP |
| `cart.php` | DATA CONTRACT | Cart section | KEEP |
| `single-product.php` | DATA CONTRACT | Product section | KEEP |
| `archive-product.php` | DATA CONTRACT | Shop sections | KEEP |
| `checkout/form-checkout.php` | DATA CONTRACT | Checkout section | KEEP |
| `myaccount/my-account.php` | DATA CONTRACT | Account routing | KEEP |
| `woocommerce/checkout/thankyou.php` | DATA CONTRACT | Order confirmation | KEEP |
| `index.php` | DATA CONTRACT | Fallback template | KEEP |
| `single.php` | DATA CONTRACT | Blog single | KEEP |
| `page.php` | DATA CONTRACT | Generic page | KEEP |
| `archive.php` | DATA CONTRACT | Archive listing | KEEP |
| `home.php` | DATA CONTRACT | Blog listing | KEEP |
| `search.php` | DATA CONTRACT | Search results | KEEP |
| `404.php` | DATA CONTRACT | Error page | KEEP |
| `page-about.php` | DATA CONTACT | About page | KEEP |
| `page-contact.php` | DATA CONTRACT | Contact page | KEEP |
| `page-faq.php` | DATA CONTRACT | FAQ page | KEEP |
| `page-team.php` | DATA CONTRACT | Team page | KEEP |
| `page-login.php` | DATA CONTRACT | Login page | KEEP |
| `page-register.php` | DATA CONTRACT | Register page | KEEP |
| `page-wishlist.php` | DATA CONTRACT | Wishlist page | KEEP |
| `page-coming-soon.php` | DATA CONTRACT | Coming soon | KEEP |
| `page-styleguide.php` | DATA CONTRACT | Styleguide | KEEP |
| `sidebar.php` | PRESENTATION | Legacy sidebar | ARCHIVE |
| `sidebar-left.php` | PRESENTATION | Legacy left sidebar | ARCHIVE |
| `searchform.php` | PRESENTATION | Legacy search form | ARCHIVE |
| `comments.php` | PRESENTATION | Legacy comments | ARCHIVE |
| `header-min.php` | PRESENTATION | Minimal header | ARCHIVE |
| `footer-min.php` | PRESENTATION | Minimal footer | ARCHIVE |
| `content.php` | PRESENTATION | Legacy loop template | ARCHIVE |
| `content-page.php` | PRESENTATION | Legacy page content | ARCHIVE |
| `content-single.php` | PRESENTATION | Legacy single content | ARCHIVE |
| `content-404.php` | PRESENTATION | Legacy 404 content | ARCHIVE |
| `content-link.php` | PRESENTATION | Legacy link format | ARCHIVE |

### inc/ — Core Infrastructure & Business Logic

| File | Classification | Purpose | Verdict |
|------|---------------|---------|---------|
| `inc/frontend.php` | CORE INFRASTRUCTURE | AETHER boot | KEEP |
| `inc/aether-tokens.php` | CORE INFRASTRUCTURE | CSS custom properties | KEEP |
| `inc/aether-security.php` | CORE INFRASTRUCTURE | HTTP headers, CSP | KEEP |
| `inc/aether-ajax.php` | BUSINESS LOGIC | AJAX handlers | KEEP |
| `inc/aether-cart.php` | BUSINESS LOGIC | WC cart fragment | KEEP |
| `inc/aether-seo.php` | BUSINESS LOGIC | OG, Schema.org | KEEP |
| `inc/aether-newsletter.php` | BUSINESS LOGIC | Newsletter DB + admin | KEEP |
| `inc/aether-analytics.php` | BUSINESS LOGIC | GA4 dataLayer | KEEP |
| `inc/aether-performance.php` | CORE INFRASTRUCTURE | Resource hints, optimization | KEEP |
| `inc/theme-functions.php` | BUSINESS LOGIC | Option helpers, SVG, microdata | KEEP |
| `inc/defaults.php` | DATA CONTRACT | Default option values | KEEP |
| `inc/general.php` | BUSINESS LOGIC | Legacy enqueue, widgets | KEEP |
| `inc/customizer.php` | BUSINESS LOGIC | Full Customizer (1575 lines) | KEEP |
| `inc/css-output.php` | BUSINESS LOGIC | Dynamic CSS (1340 lines) | KEEP |
| `inc/markup.php` | DATA CONTRACT | Body/sidebar/header classes | KEEP |
| `inc/typography.php` | BUSINESS LOGIC | Google Fonts | KEEP |
| `inc/plugin-compat.php` | BUSINESS LOGIC | WC wrappers, compat | KEEP |
| `inc/deprecated.php` | BUSINESS LOGIC | Deprecated stubs | ARCHIVE |
| `inc/class-css.php` | CORE INFRASTRUCTURE | CSS builder class | KEEP |
| `inc/block-editor.php` | BUSINESS LOGIC | Block editor support | KEEP |
| `inc/class-typography.php` | BUSINESS LOGIC | Dynamic typography | KEEP |
| `inc/class-typography-migration.php` | BUSINESS LOGIC | Font migration | KEEP |
| `inc/class-html-attributes.php` | CORE INFRASTRUCTURE | HTML attribute builder | KEEP |
| `inc/class-theme-update.php` | BUSINESS LOGIC | DB migration | KEEP |
| `inc/class-rest.php` | BUSINESS LOGIC | REST API endpoint | KEEP |
| `inc/meta-box.php` | BUSINESS LOGIC | Admin layout meta box | KEEP |
| `inc/class-dashboard.php` | BUSINESS LOGIC | Admin dashboard | KEEP |
| `inc/dashboard.php` | BUSINESS LOGIC | Legacy dashboard | ARCHIVE |

### inc/structure/ — ALL ARCHIVE (dead code when AETHER active)

| File | Purpose |
|------|---------|
| `inc/structure/header.php` | Legacy header construction |
| `inc/structure/footer.php` | Legacy footer + widgets |
| `inc/structure/navigation.php` | Legacy nav + mobile toggle |
| `inc/structure/sidebars.php` | Sidebar rendering |
| `inc/structure/search-modal.php` | Legacy search modal |
| `inc/structure/post-meta.php` | Post meta display |
| `inc/structure/featured-images.php` | Featured images |
| `inc/structure/comments.php` | Comment structure |
| `inc/structure/archives.php` | Archive titles |

### inc/customizer/ — ALL KEEP

- `customizer-helpers.php`, `helpers.php` — CORE INFRASTRUCTURE
- `fields/*.php` (14 files) — DATA CONTRACT
- `controls/*.php` (8 files) — CORE INFRASTRUCTURE
- `deprecated.php` — ARCHIVE

### assets/ — ALL ARCHIVE (dead code when AETHER active)

All CSS, JS, dist, and font files in `assets/` are suppressed by `aureon_suppress_theme_output()` at priority 1000.

Exception: `assets/css/admin/meta-box.css` — KEEP (admin-only).

---

## 4. Design Pack System

### Pack Structure (fermliving example)

```
frontend/designs/fermliving/
  manifest.json          — Pack descriptor
  tokens.php             — Option defaults override (405 lines)
  composer.php           — Filter hooks for composition (904 lines)
  css/fonts.css          — Font imports
  css/ferm.css           — Pack CSS (3285 lines)
  js/ferm.js             — Pack JS (463 lines)
  components/            — Component template overrides (10 files)
  sections/              — Section template overrides (5 files)
  assets/                — Pack-specific images/fonts
```

### Shadowing Mechanism

`aether_resolve_design_path($relative)` checks pack directory first. If file exists there, it's used; otherwise falls back to engine tree. No manifest registration needed — purely filesystem-based.

### Asset Enqueue (Non-Luxury)

1. Platform CDNs: Bootstrap 5.3.3, Font Awesome 6.5.1, Swiper 11, GSAP 3.12.5, ScrollTrigger
2. Platform contract JS: animations.js, main.js, countdown.js
3. Pack assets from `manifest.json`: CSS and JS arrays
4. Pack JS can declare deps on platform handles

### Isolation Guarantee

- Luxury CSS suppressed for non-luxury designs
- Non-luxury designs never load luxury CSS
- Platform CDNs + contract JS shared across all designs
- Two enqueue paths are mutually exclusive

---

## 5. Adapter Registry (23 Adapters)

| Adapter | Data Source | Used By |
|---------|------------|---------|
| `adapter-site.php` | `get_bloginfo()`, `get_theme_mod()` | preloader, shell |
| `adapter-shell.php` | WP menus + WC cart count | announcement, header, mobile |
| `adapter-menu.php` | `wp_get_nav_menu_items()` | header, mobile |
| `adapter-hero.php` | Customizer `aether_hero_slides` | hero section |
| `adapter-wc-products.php` | `WP_Query` + `wc_get_product()` | bestsellers, shop-grid, related |
| `adapter-wc-categories.php` | `get_terms('product_cat')` | categories |
| `adapter-wc-filter.php` | `get_terms('product_cat')` + sale IDs | shop filter |
| `adapter-product.php` | `wc_get_product()` + reviews | single product |
| `adapter-cart.php` | `WC()->cart` | cart, checkout |
| `adapter-order.php` | `wc_get_order()` | order confirmation |
| `adapter-blog.php` | `WP_Query('post')` | blog grid |
| `adapter-article.php` | `get_post()` + `get_comments()` | blog single |
| `adapter-faq.php` | `WP_Query('aether_faq')` CPT | FAQ section |
| `adapter-contact.php` | Customizer options | contact page |
| `adapter-about.php` | Demo content tokens | about page |
| `adapter-testimonials.php` | `WP_Query('aether_testimonial')` CPT | reviews |
| `adapter-team.php` | `WP_Query('aether_team')` CPT | team section |
| `adapter-auth.php` | WC auth settings | login/register |
| `adapter-account.php` | `wp_get_current_user()` + WC orders | my account |
| `adapter-wishlist.php` | User meta `aether_wishlist` | wishlist |
| `adapter-coming-soon.php` | Customizer option | coming soon |
| `adapter-shop-hero.php` | WC archive context | shop hero |
| `adapter-options.php` | `aureon_get_option()` | newsletter |

**Key rule:** Components and section templates NEVER call WP/WC functions. They receive pre-normalized data arrays from adapters.

---

## 6. Section Registry (28 Sections)

| Section ID | Adapter | Purpose |
|------------|---------|---------|
| `hero` | adapter-hero.php | Homepage hero slider |
| `categories` | adapter-wc-categories.php | Category grid |
| `bestsellers` | adapter-wc-products.php | Top products |
| `reviews` | adapter-testimonials.php | Testimonials |
| `faq` | adapter-faq.php | FAQ accordion |
| `newsletter` | adapter-options.php | Email capture |
| `shop-hero` | adapter-shop-hero.php | Shop page hero |
| `shop-filter` | adapter-wc-filter.php | Filter bar |
| `shop-grid` | adapter-wc-products.php | Product grid |
| `product` | adapter-product.php | Single product |
| `related` | adapter-wc-products.php | Related products |
| `cart` | adapter-cart.php | Cart page |
| `checkout` | adapter-cart.php | Checkout |
| `order-confirmation` | adapter-order.php | Thank you |
| `auth` | adapter-auth.php | Login/register |
| `account` | adapter-account.php | My account |
| `blog-grid` | adapter-blog.php | Blog listing |
| `blog-single` | adapter-article.php | Single post |
| `mission` | adapter-about.php | About mission |
| `features` | adapter-about.php | About features |
| `story` | adapter-about.php | About story |
| `stats` | adapter-about.php | About stats |
| `values` | adapter-about.php | About values |
| `team` | adapter-team.php | Team cards |
| `contact` | adapter-contact.php | Contact form |
| `wishlist` | adapter-wishlist.php | Saved items |
| `coming-soon` | adapter-coming-soon.php | Countdown |

---

## 7. Security

- Nonce verification on all AJAX handlers (`check_ajax_referer`)
- CSP headers (report-only default, enforce via `AETHER_CSP_STRICT`)
- Rate limiting on contact form and newsletter (1 per IP per minute)
- Capability checks (`manage_options`, `edit_theme_options`, `edit_post`)
- Input sanitization (`sanitize_text_field`, `sanitize_email`, `absint`, `esc_url`)

---

## 8. Summary

| Category | Count | Verdict |
|----------|-------|---------|
| Core Infrastructure | ~25 | KEEP |
| Business Logic | ~20 | KEEP |
| Data Contract | ~20 | KEEP |
| Presentation (archive) | ~35 | ARCHIVE |
| Presentation (replace) | ~30 | REPLACE |
| Legacy Assets | ~20 | ARCHIVE |

**Key insight:** The AETHER engine is a clean separation of concerns. Adapters are the ONLY WP/WC touchpoint. Components receive normalized data and render escaped HTML. Design packs shadow files via filesystem. This architecture already supports the thin integration approach.
