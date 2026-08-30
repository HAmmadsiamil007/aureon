# Frontend Forensic Analysis — AETHER Template → Aureon Integration

**Date:** 2026-08-05
**Status:** Phase 17 — Deep Exploration Complete
**Template Location:** `C:\Users\hamma\Downloads\templete\frontend`

---

## 1. Brand Identity

| Field | Value |
|-------|-------|
| Brand | AETHER |
| Tagline | "Step Into The Void" |
| Industry | Premium athletic footwear |
| Tone | Cinematic, dark, luxury-tech |
| Primary CTA | "Shop Now — $449" |

---

## 2. Tech Stack

### CDN Libraries
| Library | Version | Purpose |
|---------|---------|---------|
| Bootstrap | 5.3.3 | Grid, components, JS utilities |
| GSAP | 3.12.5 | Core animation engine |
| ScrollTrigger | (bundled) | Scroll-driven animations |
| Swiper | 11 | Carousels, hero slider |
| Lenis | 1.1.18 | Smooth scrolling |
| Font Awesome | 6.5.1 | Icons |
| Google Fonts | — | Cabinet Grotesk + Satoshi |

### Local JS (8 files)
| File | Lines | Purpose |
|------|-------|---------|
| `main.js` | 543 | Header, mobile menu, FAQ, newsletter, product interactions |
| `animations.js` | 941 | GSAP scroll reveals, text animations, magnetic buttons |
| `effects.js` | 153 | Canvas fire sparks particle system |
| `lenis-scroll.js` | 27 | Smooth scrolling with Lenis |
| `phantom-data.js` | 217 | **WordPress REST API bridge** (key integration file) |
| `phantom-bridge.js` | 55 | Utility helpers (cookies, debounce, throttle) |
| `phantom-dark-mode.js` | 31 | Dark mode toggle with localStorage |
| `three-scenes.js` | 1 | Disabled Three.js placeholder |

### Vendor JS (19 files)
jQuery 3.7.1, Owl Carousel, WOW.js, Bootstrap/Popper, jQuery Validate, plus 12 micro-utilities (back-to-top, contact form, counter, country dropdown, filter, loadmore, product quantity, quantity, remove product, search, video popup, video section).

### CSS (4 files + vendor)
| File | Lines | Purpose |
|------|-------|---------|
| `style.css` | 4,550 | Full design system (tokens, layout, all components) |
| `motion.css` | 151 | GSAP animation helpers, GPU hints |
| `responsive.css` | 1,531 | 8 breakpoints (1920→360) |
| `a11y.css` | 142 | Accessibility: skip links, focus, reduced-motion, print |
| `vendor/` | 5 files | animate.css, blog.css, shop.css, Owl Carousel |

---

## 3. Design Token System

```css
:root {
    /* Colors */
    --void: #09090B;       /* Primary background */
    --surface: #141416;    /* Card/surface background */
    --chrome: #A8B5C0;     /* Secondary text */
    --gold: #C8956C;       /* Accent/CTA */
    --white: #FFFFFF;
    --black: #000000;

    /* Typography */
    --font-heading: 'Cabinet Grotesk', sans-serif;
    --font-body: 'Satoshi', sans-serif;

    /* Spacing */
    --section-padding: 100px 0;
    --container-max: 1200px;
    --announcement-height: 40px;
    --header-height: 80px;

    /* Transitions */
    --transition-fast: 0.2s ease;
    --transition-normal: 0.3s ease;
    --transition-slow: 0.5s ease;

    /* Z-index (10 levels) */
    --z-base: 1;
    --z-fog: 1;
    --z-footer: 3;
    --z-header: 1000;
    --z-announcement: 1001;
    --z-mobile-header: 1100;
    --z-mobile-menu: 1200;
    --z-search: 9000;
    --z-preloader: 9999;
    --z-skip-link: 10000;
}
```

---

## 4. HTML Pages Inventory

| Page | File | Body Class | Key Sections |
|------|------|------------|--------------|
| Home | `index.html` (850 lines) | `home-page` | Hero slider, Categories, Bestsellers, Reviews, FAQ, Newsletter |
| Shop | `shop.html` (445 lines) | `shop-page` | Page hero, Product grid, Sidebar filters |
| Cart | `cart.html` (663 lines) | `cart-page` | Page hero, Cart table, Order summary |
| Checkout | `checkout.html` | `checkout-page` | Multi-step checkout |
| Account | `account.html` | `account-page` | Dashboard, orders, addresses |
| Blog | `blog.html` | `blog-page` | Post grid, sidebar |
| Single Blog | `single-blog.html` | `single-blog-page` | Article, related posts, comments |
| Product Detail | `product-detail.html` | `product-page` | Gallery, options, reviews, related |
| About | `about.html` | `about-page` | Team, mission, values |
| Contact | `contact.html` | `contact-page` | Form, map, info |
| FAQ | `faq.html` | `faq-page` | Accordion FAQ |
| Wishlist | `wishlist.html` | `wishlist-page` | Saved products |
| Login | `login.html` | `login-page` | Login/register forms |
| 404 | `404.html` | `error-404` | Error page |
| Coming Soon | `coming-soon.html` | `coming-soon` | Launch countdown |
| Privacy | `privacy-policy.html` | — | Legal content |
| Terms | `term-of-use.html` | — | Legal content |
| Cookies | `cookie-policy.html` | — | Legal content |

---

## 5. Component Architecture

### 5.1 Global Components (every page)
1. **Preloader** — Animated bar with logo
2. **Fog System** — 3-layer cinematic CSS fog (fixed position, full viewport)
3. **Mobile Header** — ≤768px hamburger + announcement
4. **Mobile Slide-Out Menu** — Overlay menu with search, nav, socials
5. **Announcement Bar** — Scrolling text bar
6. **Header** — Smart sticky (hide on scroll down, show on scroll up, 80px height)
7. **Skip-to-Content** — Accessibility link
8. **Footer** — 4-column with newsletter, social, payments, legal

### 5.2 Home Page Sections
1. **Hero Slider** — Swiper, 3 slides, parallax, mouse depth tracking, fog overlay, particles canvas, progress bar
2. **Category Selector** — 4 cards (Men/Women/Kids/New), tilt effect, reveal animations
3. **Bestsellers** — 4 product cards, image zoom, badges, add-to-cart
4. **Reviews Carousel** — Swiper, 4 reviews, star ratings, verified badges
5. **FAQ Accordion** — 2-column, expandable items
6. **Newsletter** — Email form with success state

### 5.3 E-Commerce Components
- **Product Card** — Image zoom, badges (Bestseller/New/Limited), wishlist + quick view actions
- **Product Grid** — Responsive grid with reveal animations
- **Cart Table** — Line items, quantity controls, remove, totals
- **Product Detail** — Gallery, size selector, add-to-cart, related products
- **Wishlist** — Saved products grid

---

## 6. Animation System (16 Presets)

### Reveal Presets
| Preset | Direction | Effect |
|--------|-----------|--------|
| `slide-left` | ← | x: -80, opacity |
| `slide-left-soft` | ← | x: -50, opacity |
| `slide-left-far` | ← | x: -120, opacity |
| `slide-left-up` | ←↑ | x: -60, y: 30, opacity |
| `slide-left-rotate` | ←↻ | x: -70, rotation: -3, opacity |
| `slide-right` | → | x: 80, opacity |
| `slide-right-soft` | → | x: 50, opacity |
| `slide-right-far` | → | x: 120, opacity |
| `slide-right-up` | →↑ | x: 60, y: 30, opacity |
| `slide-right-rotate` | →↻ | x: 70, rotation: 3, opacity |
| `fade-up` | ↑ | y: 50, opacity |
| `fade-down` | ↓ | y: -50, opacity |
| `scale` | ⊕ | scale: 0.88, opacity |
| `scale-up` | ⊕↑ | scale: 0.85, y: 30, opacity |
| `blur-in` | ⊘ | y: 20, blur: 16px, opacity |
| `clip-reveal` | ▭ | clipPath inset(100%), opacity |

### Animation Features
- **Word-by-word text reveal** — Split text into masked spans, staggered entrance
- **Line-by-line text reveal** — Same for multi-line text
- **Magnetic hover** — Buttons follow cursor with `data-magnetic` attribute
- **Tilt effect** — Cards tilt on hover with `data-tilt` attribute
- **Image zoom** — Products zoom on hover with `data-image-zoom`
- **Fire sparks** — Canvas particle system (ember, glow, cinder types)
- **Reduced motion** — Full `prefers-reduced-motion` support

---

## 7. WordPress Integration Bridge

### `phantom-data.js` — The Key Integration File

This file is the **existing WordPress integration layer**. It:
1. Fetches data from `/wp-json/phantom/v1/page-data`
2. Injects settings, menus, products, posts, and cart data into HTML templates

### Data Attributes Used
| Attribute | Purpose | Example |
|-----------|---------|---------|
| `data-phantom` | Inject setting value (text/src/href) | `data-phantom="hero_headline"` |
| `data-phantom-bg` | Inject background image | `data-phantom-bg="hero"` |
| `data-phantom-alt` | Inject alt text | `data-phantom-alt="product_1"` |
| `data-phantom-menu` | Inject menu items | `data-phantom-menu="primary"` |
| `data-phantom-products` | Inject product grid | `data-phantom-products="featured"` |
| `data-phantom-posts` | Inject post grid | `data-phantom-posts="latest"` |

### Data Flow
```
WordPress Customizer → REST API → phantom-data.js → HTML injection
```

### Injection Functions
- `injectSettings(data)` — Maps `data-phantom` keys to API response
- `injectMenus(data)` — Builds nav HTML from menu data
- `injectProducts(data)` — Builds product cards from product data
- `injectPosts(data)` — Builds blog cards from post data
- `injectCart(data)` — Updates cart count and totals

---

## 8. Aureon Integration Architecture

### Current Aureon Stack
- **Theme:** Aureon 3.6.1 (GeneratePress fork) — `functions.php`, template files
- **Plugin:** Aureon Studio 3.0.0 — 14 active modules (backgrounds, blog, copyright, disable-elements, elements, secondary-nav, spacing, menu-plus, woocommerce, page-header, font-library, general, typography, colors)
- **Customizer:** 18 sections, 500+ settings

### Integration Points

#### A. CSS/Design Token Mapping
| AETHER Token | Aureon Customizer Setting | Path |
|--------------|--------------------------|------|
| `--void` (#09090B) | Body Background Color | `colors` module |
| `--surface` (#141416) | Content Background | `colors` module |
| `--gold` (#C8956C) | Link/Accent Color | `colors` module |
| `--chrome` (#A8B5C0) | Text Color | `colors` module |
| `--font-heading` | Heading Font | `typography` module |
| `--font-body` | Body Font | `typography` module |
| `--container-max` | Container Width | `spacing` module |

#### B. Template Integration
| AETHER Page | Aureon Equivalent | Approach |
|-------------|-------------------|----------|
| `index.html` | Front Page / Home | Custom template or block template |
| `shop.html` | WooCommerce Shop | WC template override |
| `cart.html` | WooCommerce Cart | WC template override |
| `product-detail.html` | WooCommerce Single Product | WC template override |
| `blog.html` | Blog Archive | `archive.php` override |
| `single-blog.html` | Single Post | `single.php` override |
| `about.html` | Page Template | Custom page template |
| `contact.html` | Page Template | Custom page template |

#### C. JS Integration
| AETHER JS | Aureon Integration |
|-----------|-------------------|
| `phantom-data.js` | **Already bridges WP REST API** — wire to Aureon Customizer settings |
| `animations.js` | Enqueue in `functions.php`, apply to theme templates |
| `effects.js` | Enqueue as optional enhancement |
| `lenis-scroll.js` | Enqueue, disable when `prefers-reduced-motion` |
| `phantom-dark-mode.js` | Wire to Customizer dark mode setting |
| `phantom-bridge.js` | Utility library — enqueue globally |

#### D. REST API Endpoints
The template expects `/wp-json/phantom/v1/page-data`. Aureon Studio has existing REST infrastructure (`inc/class-rest.php`). Need to:
1. Register `phantom/v1/page-data` endpoint in Aureon
2. Map Customizer settings to the API response
3. Return products, menus, posts, and settings in the expected format

---

## 9. Integration Approach Options

### Option A: Template Overrides (Recommended)
- Override WooCommerce templates (shop, cart, checkout, product)
- Create custom page templates for static pages (about, contact, etc.)
- Enqueue AETHER CSS/JS in `functions.php`
- Map Customizer settings to `data-phantom-*` attributes
- **Pros:** Standard WP approach, maintainable, updatable
- **Cons:** Requires careful template selection

### Option B: Block Theme Migration
- Convert Aureon to a block theme
- Use `theme.json` for design tokens
- Create block patterns for each component
- **Pros:** Modern WP approach, visual editor
- **Cons:** Major rewrite, loses Customizer, complex

### Option C: Hybrid (Aureon Core + AETHER Overlay)
- Keep Aureon as classic theme
- Create child theme with AETHER templates
- Register custom REST endpoints in plugin
- **Pros:** Best of both worlds
- **Cons:** More files to maintain

---

## 10. Key Findings & Recommendations

### Critical Discovery
**`phantom-data.js` is already a WordPress integration bridge.** The template was designed to work with a WP REST API backend. This means:
- No need to build a data bridge from scratch
- The `data-phantom-*` attribute system is already the integration contract
- Aureon just needs to populate the API response with Customizer settings

### Recommended Approach: **Option A (Template Overrides) + REST API Wiring**

### Files to Create/Modify
1. **Plugin: `inc/class-rest-phantom.php`** — Register `/phantom/v1/page-data` endpoint
2. **Plugin: `inc/class-phantom-data.php`** — Map Customizer settings to API response
3. **Theme: `functions.php`** — Enqueue AETHER CSS/JS
4. **Theme: Page templates** — About, Contact, Coming Soon, 404
5. **WooCommerce: Template overrides** — Shop, Cart, Checkout, Product Detail
6. **Theme: `style-aether.css`** — Design token overrides mapping to Customizer

### Next Steps
1. Write integration design doc
2. Implement REST API endpoint
3. Wire Customizer settings to API response
4. Enqueue CSS/JS
5. Create page templates
6. Override WooCommerce templates
7. Test end-to-end

---

## Appendix A: File Inventory

### Template Files (43 entries)
```
frontend/
├── index.html (850 lines)
├── shop.html (445 lines)
├── cart.html (663 lines)
├── checkout.html
├── account.html
├── blog.html
├── single-blog.html
├── product-detail.html
├── about.html
├── contact.html
├── faq.html
├── wishlist.html
├── login.html
├── 404.html
├── coming-soon.html
├── privacy-policy.html
├── term-of-use.html
├── cookie-policy.html
├── assets/
│   ├── bootstrap/bootstrap.min.css
│   ├── css/
│   │   ├── style.css (4,550 lines)
│   │   ├── motion.css (151 lines)
│   │   ├── responsive.css (1,531 lines)
│   │   ├── a11y.css (142 lines)
│   │   └── vendor/ (5 files)
│   ├── images/ (145 files)
│   └── js/
│       ├── main.js (543 lines)
│       ├── animations.js (941 lines)
│       ├── effects.js (153 lines)
│       ├── lenis-scroll.js (27 lines)
│       ├── phantom-data.js (217 lines)
│       ├── phantom-bridge.js (55 lines)
│       ├── phantom-dark-mode.js (31 lines)
│       ├── three-scenes.js (1 line)
│       └── vendor/ (19 files)
├── scripts/ (empty)
└── frontend/ (empty)
```
