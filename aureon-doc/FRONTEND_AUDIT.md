# FRONTEND_AUDIT — Pristine Source (`frontend/source/`)

**Date:** 2026-08-07
**Phase:** 17.1 — STEP 4 (Audit of restored pristine source)
**Source of truth:** `C:\Users\hamma\Downloads\wordpress\frontend\source\` — 364 files, read-only, mirror-verified identical to `C:\Users\hamma\Downloads\templete\frontend`
**Status:** COMPLETE

---

## 1. Page Tree (22 HTML pages → WP template mapping)

| Source file | Body class | WP surface | Replaces |
|---|---|---|---|
| `index.html` (850 lines) | `home-page` | Front page | `front-page.php` |
| `shop.html` (445) | `shop-page` | Shop/archive | `archive-product.php` |
| `product-detail.html` | `product-page` | Single product | `single-product.php` |
| `cart.html` (663) | `cart-page` | Cart | `cart/cart.php` |
| `checkout.html` | `checkout-page` | Checkout | `checkout/form-checkout.php` |
| `thank-you.html` | — | Order received | `checkout/thankyou.php` |
| `account.html` | `account-page` | My Account | `myaccount/*` |
| `login.html` | `login-page` | Login | `myaccount/form-login.php` |
| `join-now.html` | — | Register | `myaccount/form-register.php` |
| `wishlist.html` | `wishlist-page` | Wishlist | page template |
| `blog.html` | `blog-page` | Blog archive | `index.php`/`home.php` |
| `single-blog.html` | `single-blog-page` | Single post | `single.php` |
| `about.html` | `about-page` | About | `page-about.php` |
| `team.html` | — | Team | `page-team.php` |
| `testimonials.html` | — | Testimonials | `page-testimonials.php` |
| `contact.html` | `contact-page` | Contact | `page-contact.php` |
| `faq.html` | `faq-page` | FAQ | `page-faq.php` |
| `404.html` | `error-404` | 404 | `404.php` |
| `coming-soon.html` | `coming-soon` | Coming soon | maintenance template |
| `privacy-policy.html` | — | Legal | page template |
| `term-of-use.html` | — | Legal | page template |
| `cookie-policy.html` | — | Legal | page template |

Note: `assets/js/firebase-auth.js` referenced only by account/login/join-now — auth is client-side Firebase in the template; the rebuild must map to WP auth (mission: Bridge WordPress + Firebase, authentication abstracted).

## 2. Global Components (every page)

1. **Preloader** — `#preloader` (preloader-inner/logo/bar/progress) — main.js logic
2. **Fog System** — 3-layer CSS fog, fixed viewport (hero-related, present sitewide)
3. **Mobile Header** (≤768px) — hamburger + announcement
4. **Mobile Slide-Out Menu** — `mobile-menu-overlay` → mobile-menu (header/body): mobile-search, mobile-nav (multiple link groups), mobile-divider, mobile-meta, mobile-socials
5. **Announcement Bar** — `announcement-bar` → announcement-content (scrolling text)
6. **Desktop Header** — `header` → header-container: main-nav, nav-mobile-logo, nav-mobile-icons, header-actions
7. **Skip-to-Content** — a11y
8. **Footer** — 4-column: newsletter, social, payments, legal (structure verified in source CSS)

## 3. Layout Tree — `index.html` (home)

```
#preloader (bar+progress)
#fog-system
a.skip-to-content (a11y)
.mobile-header + .mobile-menu-overlay
.announcement-bar
header#header
  .header-container: main-nav · nav-mobile-logo · nav-mobile-icons · header-actions
main
  section#hero-slider (Swiper, 3 × .swiper-slide.hero-slide)
    hero-slide-bg · hero-slide-overlay · hero-slide-text · hero-cta-group
    #hero-particles (canvas) · hero-slider-nav · hero-slide-counter · hero-slider-progress
    .scroll-indicator (mouse/wheel)
  section.categories → .section-header + .category-grid (4 × category-card: bg/overlay/content)
  section.bestsellers → .section-header + .products-grid (4 × product-card: image/actions/info/rating/price-row)
  section.reviews → .reviews-summary + reviews carousel (Swiper)
  section.faq → 2-column accordion
  section.newsletter → email form + success state
footer (4-col: widgets, newsletter, social, payments, legal)
```

## 4. Component Inventory (section library)

### 4.1 Shell
| Component | Class root | Page(s) |
|---|---|---|
| preloader | `#preloader` | all |
| fog-system | `#fog-system` | all |
| mobile-header | `.mobile-header` | all |
| mobile-menu | `.mobile-menu-overlay` | all |
| announcement-bar | `.announcement-bar` | all |
| header | `header#header` | all |
| skip-link | `.skip-to-content` | all |
| footer | `footer` | all |

### 4.2 Home sections
| Component | Class root | Data attributes |
|---|---|---|
| hero-slider | `.hero-slider` (Swiper) | `data-swiper-parallax`, `data-mouse-depth`, `data-parallax-speed`, `data-motion-text`, `data-phantom-bg` |
| categories | `.category-grid` | `data-tilt` (hover), `data-phantom-bg`, `data-phantom` |
| bestsellers | `.products-grid` | `data-phantom-products`, `data-magnetic` |
| product-card | `.product-card` | `data-phantom`, `data-phantom-alt`, `data-phantom-bg` |
| reviews | `.reviews` (Swiper) | `data-phantom` |
| faq | `.faq-section` | `data-phantom` |
| newsletter | `.newsletter-section` | `data-phantom` |

### 4.3 Commerce
| Component | Class root | Page |
|---|---|---|
| page-hero | `.page-hero` | shop/cart/checkout/account |
| filter-bar | `.filter-bar` | shop |
| product-grid | `.shop-grid` | shop |
| product-card | `.product-card` | shop/home |
| cart-table | `.cart-table` | cart |
| cart-summary | `.cart-summary` | cart |
| checkout-steps | `.checkout-section` | checkout |
| pd-gallery | `.pd-gallery` (Swiper) | product-detail |
| pd-info | `.pd-info` (price/size/color/qty) | product-detail |
| pd-tabs/accordion | `.pd-accordion` | product-detail |
| pd-related | `.pd-related` (Swiper) | product-detail |
| pd-sticky-bar | `.pd-sticky-bar` | product-detail |

### 4.4 Content/blog
| Component | Class root | Page |
|---|---|---|
| blog-grid | `.blog-grid` | blog |
| blog-card | `.blog-card` | blog/home |
| article | `.blog-article` | single-blog |
| comments | `.comments-section` | single-blog |
| team-grid | `.team-grid` | team |
| mission/stats | `.stats-section` | about |
| contact-grid | `.contact-grid` | contact |
| wishlist-grid | `.wishlist-section` | wishlist |

## 5. Dependency Graph (assets per page — verified from HTML)

### CDN (all pages)
- Bootstrap **5.3.3** `bootstrap.bundle.min.js` + `bootstrap.min.css` (jsdelivr)
- Font Awesome **6.5.1** `all.min.css` (cdnjs)
- GSAP **3.12.5** + ScrollTrigger (cdnjs) — *all pages except account/login/join-now*
- Lenis **1.1.18** (unpkg)
- Swiper **@11** `swiper-bundle.min.js` + css (jsdelivr) — *all pages except account/login/join-now*

### Local JS (order matters)
1. `assets/js/lenis-scroll.js` (780 B)
2. `assets/js/animations.js` (40,321 B — GSAP reveals, motion-text, magnetic, countup, mouse-depth)
3. `assets/js/main.js` (25,757 B — header/mobile menu/FAQ/newsletter/product interactions)
4. `assets/js/phantom-data.js` (8,788 B — data-`phantom*` injection bridge)
5. `assets/js/phantom-bridge.js` (1,842 B — helpers)
6. `assets/js/firebase-auth.js` (15,349 B) — *auth pages only*
Unused in pages: `effects.js`, `phantom-dark-mode.js`, `three-scenes.js` (present on disk; effects/dark-mode not referenced in HTML)

### Local CSS (uniform)
1. `assets/css/style.css` (99,031 B — the design system)
2. `assets/css/motion.css` (4,778 B — animation helpers)
3. `assets/css/responsive.css` (32,806 B — 8 breakpoints)
4. `assets/css/a11y.css` (3,039 B)
5. `assets/css/vendor/`: animate.css, blog.css, shop.css, owl.carousel.min.css, owl.theme.default.min.css
6. `assets/bootstrap/bootstrap.min.css` (vendor copy)

### `.reference` files (part of template itself)
`style.css.reference`, `responsive.css.reference`, `motion.css.reference`, `animations.js.reference`, `main.js.reference`, `effects.js.reference` — pristine pre-minification copies shipped inside the template; do NOT deploy; reference only.

## 6. Animation Graph (verified attribute contract)

| Attribute | Count | Purpose | Engine |
|---|---|---|---|
| `data-phantom` (+`-alt`/`-bg`/`-menu`/`-products`/`-posts`/`-account`) | 253+47+36+18+2+2+1 | Data injection (WP data → markup) | phantom-data.js / server-side |
| `data-magnetic` | 41 | Magnetic hover buttons | animations.js |
| `data-motion-text` | 35 | Word/line reveal | animations.js |
| `data-mouse-depth` | 9 | Mouse depth tracking (hero) | animations.js |
| `data-swiper-parallax` | 9 | Swiper parallax | Swiper |
| `data-countup` | 4 | Animated counters | animations.js |
| `data-color` | 4 | Color accent (titles) | animations.js |
| `data-parallax-speed` | 3 | Scroll parallax | animations.js |
| `data-bs-toggle/target/parent` | 8×3 | Bootstrap collapse/dropdown | Bootstrap |

**IMPORTANT — contract correction vs old analysis:** the source uses `data-magnetic`/`data-motion-text`/`data-mouse-depth`/`data-countup` — NOT the `data-reveal-group`/`data-reveal-item` presets described in the original FRONTEND-ANALYSIS.md (animations.js was reworked, 40 KB). The rebuild's Animation Engine must expose this *actual* attribute set, mapped through `data-aureon-animation` for mission compliance.

## 7. Asset Graph (local, sizes)

```
assets/ (4 dirs)
├── bootstrap/  bootstrap.min.css
├── css/        style.css (99 KB) · responsive.css (33 KB) · motion.css (4.8 KB) · a11y.css (3 KB)
│   └── vendor/ animate.css (57 KB) · blog.css (80 KB) · shop.css (93 KB) · owl.carousel*.css (4.4 KB)
├── images/     (~145 files — hero, products, categories, team, reviews, favicons, fog, payment cards)
└── js/         main.js (25.8 KB) · animations.js (40.3 KB) · phantom-data.js (8.8 KB) ·
                phantom-bridge.js (1.8 KB) · firebase-auth.js (15.3 KB) · effects.js (6.2 KB) ·
                lenis-scroll.js (0.8 KB) · phantom-dark-mode.js (1.1 KB) · three-scenes.js (34 B)
```

## 8. Key Audit Findings for the Rebuild

1. **CDN is the source dependency contract** (bootstrap 5.3.3, swiper@11, gsap 3.12.5, lenis 1.1.18, FA 6.5.1) — the Phase 17 vendor bundle drifted (bootstrap 4.6.2 JS inside a 5.3.3-built CSS system; swiper 11.2.10). The Asset Engine must pin EXACT versions to the source contract.
2. **phantom-data.js is the template's data contract** — `data-phantom*` attributes are the injection points. Mission requires server-side rendering; these attributes become the markup contract populated by PHP (as the restored architecture doc specifies), with `aether-ajax` for interactions only.
3. **Auth is Firebase-based in the template** (firebase-auth.js on 3 pages) — bridge to WP auth per locked decision, preserving the abstraction.
4. **`style.css` (99 KB) is the design system** — it must be deployed intact (tokenized), not hand-reduced (the failed integration shipped a reduced `frontend.css` that never matched source).
5. **8 animation engines** in source map to ONE Animation Engine + 5 vendor libs in rebuild (bootstrap bundle covers Popper; no jquery anywhere — the failed bundle added jQuery unnecessarily).
6. **`.reference` files and unused JS** (effects.js/dark-mode/three-scenes) must be excluded from deployment but preserved in source.
7. **22 page surfaces** — the rebuild template-composer must cover all of them for design consistency (Stage plan: 2 shell → 3 home → 4-6 commerce → 7 blog → 8 static).

## 9. Files
- This audit: `aureon-doc/FRONTEND_AUDIT.md`
- Source of truth: `frontend/source/` (364 files, read-only)
- Companion (restored): `aureon-doc/FRONTEND-ANALYSIS.md` (original deep exploration), `aureon-doc/PHASE-17-1-INTEGRATION-ARCHITECTURE.md` (locked decisions)
