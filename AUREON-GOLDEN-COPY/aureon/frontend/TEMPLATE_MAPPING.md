# TEMPLATE_MAPPING

**Phase:** 17 — Frontend Integration Framework (Step 4: Integration Layer)
**Date:** 2026-08-06
**Status:** Complete — static page → WP template mapping

---

## 1. Mapping Overview

Every static AETHER page maps to an existing or new Aureon template. The framework **never imports a static page wholesale** — each target template is composed of sections from the Section Library.

| Static Page | WP Template | Template File | Source of Sections |
|---|---|---|---|
| index.html | Front Page | `front-page.php` (recreate) | hero-slider, categories, bestsellers, reviews, faq, newsletter |
| shop.html | Shop Archive | WooCommerce `archive-product.php` | page-hero, filter-bar, shop-grid |
| product-detail.html | Single Product | WooCommerce `single-product.php` (or hooks) | pd-hero, pd-specs, pd-reviews, pd-related |
| cart.html | Cart | WooCommerce `cart.php` | page-hero, cart-section |
| checkout.html | Checkout | WooCommerce `checkout.php` | page-hero, checkout-section |
| wishlist.html | Wishlist | New `page-wishlist.php` (or shortcode page) | page-hero, wishlist-grid |
| login.html | My Account / Login | WooCommerce `myaccount.php` | auth-section |
| join-now.html | My Account / Register | WooCommerce `myaccount.php` | auth-section |
| account.html | My Account Dashboard | WooCommerce `myaccount.php` + dashboard partial | account-shell |
| thank-you.html | Order Received | WooCommerce `checkout/thankyou.php` | order-confirmation, order-summary |
| 404.html | 404 | `404.php` | error-hero, error-content |
| coming-soon.html | Maintenance | Maintenance mode (plugin/theme setting) | coming-soon |
| blog.html | Posts Index | `home.php` / `index.php` (new post layout) | page-hero, blog-header, blog-grid, pagination |
| single-blog.html | Single Post | `single.php` (recreate) | article-*, related-posts |
| faq.html | FAQ | New `page-faq.php` (or CPT archive) | faq-categories, faq-list, faq-cta |
| testimonials.html | Testimonials | New `page-testimonials.php` (CPT archive) | rating-overview, testimonials-grid, reviews-cta |
| team.html | Team | New `page-team.php` (CPT archive) | mission, team-grid, values, join-cta |
| contact.html | Contact | New `page-contact.php` (theme template) | contact-info, contact-form, map |
| cookie/privacy/term | Legal | `page.php` (default page layout) | page-hero, legal-content |
| about.html | About | New `page-about.php` | mission, features, story, stats |

---

## 2. Template Hierarchy Strategy

### Reuse Existing Aureon templates (no change)
- `page.php`, `single.php`, `404.php`, `archive.php`, `search.php`, `comments.php`, `header.php`, `footer.php` — the theme's GeneratePress-style structure already exists.

### Recreate (Phase 17.1 deleted these; rebuild via framework)
| File | Purpose |
|---|---|
| `front-page.php` | Composes home sections from Section Library |
| `inc/structure/` header/footer parts | Wrapped with new shell components (preloader, fog, announcement) |

### New templates (framework-generated)
| File | Purpose |
|---|---|
| `page-contact.php`, `page-faq.php`, `page-testimonials.php`, `page-team.php`, `page-about.php` | Static page layouts built from sections |
| `page-wishlist.php` | Wishlist page |
| `woocommerce/` overrides (in plugin) | Shop, product, cart, checkout, my-account, thankyou |

### Never create
- Full page imports of the 22 static HTML files into WP templates (violates component architecture).

---

## 3. Header/Footer Composition (all pages)

```
header.php (Aureon structure)
├── aether shell/preloader         (conditional: enable via Customizer)
├── aether shell/fog               (3 layers, disable on low-power)
├── aether shell/skip-link
├── aether shell/mobile-chrome
├── aether shell/announcement
├── aether shell/header            (sticky, on `aureon_menu_bar_items` for actions)
└── nav/search + nav/mini-cart    (drawers)
footer.php
├── aether shell/footer            (widgets via dynamic_sidebar, newsletter, social)
└── aether shell/back-to-top
```

---

## 4. Section → Data Source (who fills the phantom keys)

| Section | Key Data Source | Adapter |
|---|---|---|
| hero-slider | Customizer: slide repeater (3 slides) | adapter-options + fx_swiper |
| categories | WC product categories | wc_categories |
| bestsellers | WC products (popularity) | wc_products |
| reviews | `aether_testimonial` CPT | testimonials |
| faq | `aether_faq` CPT or Customizer repeater | faq_items |
| shop-grid | WC shop query | wc_products |
| filter-bar | WC layered nav | wc_filters |
| pd-hero | WC product + gallery | wc_product_single |
| cart-section | WC cart session | wc_cart + wc_cart_totals |
| checkout-section | WC checkout fields | wc_checkout_fields |
| order-confirmation | WC order by key | wc_order |
| blog-grid | WP_Query posts | blog_posts |
| article-* | the_post data | article_* |
| team-grid | `aether_team` CPT | team_members |
| contact-form | theme options + WP mailer | adapter-contact |

---

## 5. Asset Enqueue Mapping

| Static Asset | WP Enqueue | Conditional |
|---|---|---|
| `assets/css/style.css` | `aether-frontend.css` (bundled, tokenized) | all pages |
| `assets/css/responsive.css` | part of bundle (media queries) | all pages |
| `assets/css/motion.css` | separate `<link>` or bundle | enabled via Customizer |
| `assets/css/a11y.css` | separate (focus styles) | always (fixes broken link) |
| `bootstrap.min.css` | CDN or bundled — **decision: bundle locally** | all pages |
| `assets/js/main.js` | `aether-frontend.js` (bundled with phantom fixes) | all pages |
| `assets/js/animations.js` | `aether-animations.js` (gsap/lenis wrapped) | all pages |
| `assets/js/lenis-scroll.js` | `aether-lenis.js` | all pages |
| `assets/js/firebase-auth.js` | module, `type="module"` — **fix path** | login/register only |
| `vendor/*` | **excluded** (dead) | never |
| swiper/gsap/lenis CDN | local copies in `assets/` (no CDN dependency) | — |

---

## 6. Migration Order (per template)

1. **Header/Footer** — shell + navigation components (all pages change).
2. **Front page** — hero, categories, bestsellers (visual check first).
3. **Shop + Product** — WooCommerce adapters (hardest, do before cart).
4. **Cart + Checkout** — WC template hooks.
5. **Blog + single** — content adapters.
6. **Auth + account** — form adapters + firebase fix.
7. **Static pages** (faq, contact, team, testimonials, legal) — CPT + page templates.
8. **Utility** (404, thank-you, coming-soon).

---

## 7. Risks & Notes

- `single-blog.html` duplicates `blog-page` body class — WP `body_class()` will generate correct unique classes (`single-post`, `postid-*`), no action needed.
- `checkout.html` newsletter id collision (`contactpage`) — framework emits unique form ids via `uniqid`/slug.
- `contact-form.php` legacy file — replace with REST endpoint + form component.
- Blog layout in the theme today (GeneratePress-style archive) will be **replaced** by blog-grid sections — a visual regression item.