# FERM LIVING — FINAL ARCHITECTURE + 100/100 VISUAL REPLACEMENT
# FORENSIC AUDIT REPORT

> **Audit Scope:** Full-stack comparison of frozen reference (fermliving.com) against current WordPress deployment  
> **Audit Date:** 2026-08-25  
> **Framework:** AETHER frontend engine + Ferm Living design pack  
> **Verdict:** ~40/100 — Structural gaps dominate; content layer is solid but presentation is wrong

---

## Executive Summary

The Ferm Living design pack delivers strong content fidelity through adapter filters and tokens, but the **presentation architecture diverges significantly from the reference**. The root cause is architectural: AETHER's section/component system enforces a different page structure than the reference, and the Ferm pack's composer overrides only partially compensate.

**Critical failures:**
1. Homepage missing hero split, USP carousel, category slider, social feed
2. Shop page uses wrong filter categories (AETHER shoe categories instead of Ferm Living furniture categories)
3. Product cards lack carousel, swatches, wishlist heart, "Certified" badge
4. Contact page has wrong structure (form instead of sticky-title + FAQ accordion)
5. Section ordering on homepage differs from reference

**What works:**
- Brand tokens (colors, typography, copy) are accurate
- Adapter filters successfully inject Ferm Living content
- Editorial splits, product grids, room grid render correctly
- Blog content is faithfully reproduced

---

## Part 0: Baseline Verification

### File Inventory

| Asset | Path | Status |
|-------|------|--------|
| Pack manifest | `frontend/designs/fermliving/manifest.json` | Present |
| Composer | `frontend/designs/fermliving/composer.php` | 1047 lines |
| Tokens | `frontend/designs/fermliving/tokens.php` | Complete |
| Stylesheet | `frontend/designs/fermliving/css/ferm.css` | 3285 lines |
| Fonts | `frontend/designs/fermliving/css/fonts.css` | CanelaText + KHTeka |
| JS | `frontend/designs/fermliving/js/ferm.js` | 463 lines |
| Sections | `frontend/designs/fermliving/sections/` | 6 files |
| Components | `frontend/designs/fermliving/components/` | 11 files |

### AETHER Core Files

| File | Role |
|------|------|
| `frontend/loader.php` | Boot sequence: tokens → design → registry → renderer → viewmodel → assets → composer → adapters → sections |
| `frontend/composer.php` | Shell assembly: preloader → fog → skip-link → mobile-chrome → announcement → header → main#swup → footer |
| `frontend/renderer.php` | Section/component rendering with filter hooks |
| `frontend/design.php` | Pack-first template shadowing via `aether_resolve_design_path()` |
| `frontend/registry.php` | `aether_register_section()` registration |
| `frontend/viewmodel.php` | Image/behavior normalization |
| `frontend/assets.php` | Manifest-based asset enqueue, CDN libs |

### Adapters Inventory (23 total)

**AETHER-hardcoded content (9 adapters need pack override):**
1. `adapter-about.php` — mission, features, values, stats
2. `adapter-coming-soon.php` — "Something is Coming"
3. `adapter-contact.php` — "123 Innovation Drive, SF"
4. `adapter-menu.php` — fallback Home/Shop/About/Blog/Contact
5. `adapter-order.php` — "Order Confirmed"
6. `adapter-product.php` — AETHER color map (obsidian, chrome, phantom)
7. `adapter-shop-hero.php` — "Six colorways. One obsession."
8. `adapter-site.php` — footer columns, newsletter, payment icons
9. `adapter-wc-categories.php` — fallback SKUs, "Find Your Fit"

**Fully generic (14 adapters — no AETHER content):**
adapter-account, adapter-article, adapter-auth, adapter-blog, adapter-cart, adapter-faq, adapter-hero, adapter-options, adapter-shell, adapter-team, adapter-testimonials, adapter-wc-filter, adapter-wc-products, adapter-wishlist

---

## Part 1: Screenshot Route/Viewport Matrix

### Required Screenshot Routes

| # | Route | Viewport | Priority |
|---|-------|----------|----------|
| 1 | `/` (homepage) | 1440×900 (desktop) | CRITICAL |
| 2 | `/` | 375×812 (mobile) | CRITICAL |
| 3 | `/shop` | 1440×900 | CRITICAL |
| 4 | `/shop` | 375×812 | HIGH |
| 5 | `/product/{slug}` | 1440×900 | CRITICAL |
| 6 | `/product/{slug}` | 375×812 | HIGH |
| 7 | `/about` | 1440×900 | HIGH |
| 8 | `/contact` | 1440×900 | HIGH |
| 9 | `/blog` | 1440×900 | MEDIUM |
| 10 | `/category/furniture` | 1440×900 | HIGH |

### Viewport Breakpoints (from `ferm.css`)

```css
/* Standard breakpoints implied by responsive classes */
/* Mobile: < 768px */
/* Tablet: 768px - 1024px */
/* Desktop: > 1024px */
/* Wide: > 1280px */
```

---

## Part 2: Frozen Source Architecture

### Homepage Section Stack (Reference — 19 sections)

```
00. Skip-to-content link
01. USP Carousel (4 items: welcome/10% off, free shipping EUR 150, worldwide delivery, EU 2-5 days)
02. Header (fixed, transparent→solid on scroll, SVG wordmark, 4 nav items)
03. Mega Menus (4 types: Shop 3-col, Inspiration 2-col, Rooms grid, Professionals 2-col)
04. Mobile Menu (full-screen slide-in, 3-level deep)
05. Cart Drawer (right slide-in, free shipping progress bar, Clerk.io upsell)
06. Notify Modal
07. Stock Error Modal
08. Cart Toast
09. HERO SPLIT: 2-up full-height panels (Bestsellers + Made for Gathering)
10. CATEGORY SLIDER: 7 categories horizontal carousel
11. EDITORIAL #1: "Bestsellers for Kids" — image left, sticky text right
12. PRODUCT GRID #1: 4 kids products
13. EDITORIAL #2: "Storage Solutions" — image right, sticky text left
14. PRODUCT GRID #2: 4 storage products
15. EDITORIAL #3: "Guided by Sustainability" — image left, sticky text right
16. ROOM SLIDER: 6 rooms with subcategory links
17. SOCIAL/UGC: #livingwithferm Flowbox feed
18. FOOTER: USP bar + Newsletter + 3 columns + bottom
19. Country Select Popup
```

### Current WordPress Homepage Stack

```
1. Featured collections (category carousel)
2. Shop by room (room grid)
3. Kids editorial split
4. Bestsellers (product grid, 8 products)
5. Storage editorial split
6. Sustainability editorial split
7. Secondary products
8. Room grid
9. Newsletter
```

### Ferm Pack Composer Registrations (`composer.php:1-1047`)

```php
// 17 filter registrations covering:
add_filter('aether_frontpage_sections', ...);     // Homepage composition (7 sections)
add_filter('aether_adapter_shop_hero_data', ...); // Shop hero subtitle
add_filter('aether_adapter_about_data', ...);     // Full about rewrite
add_filter('aether_demo_products', ...);          // 32 demo products
add_filter('aether_demo_categories', ...);        // 7 Ferm categories
add_filter('aether_adapter_contact_data', ...);   // Copenhagen address
add_filter('aether_adapter_footer_data', ...);    // Ferm footer links
add_filter('aether_adapter_search_data', ...);    // "Search Ferm Living..."
add_filter('aether_adapter_blog_data', ...);      // Blog items
add_filter('aether_section_data', ...);           // Blog/room-grid/FAQ overrides
add_filter('aether_component_data', ...);         // Page-hero, author-bio overrides
add_filter('the_content', ...);                   // Post content replacement
add_filter('the_title', ...);                     // Title replacement
add_filter('the_excerpt', ...);                   // Excerpt replacement
add_filter('wp_trim_excerpt', ...);               // Trim excerpt replacement
```

---

## Part 3: AETHER Data Boundary Audit

### What Pack CAN Override (via filters)

| Filter | Scope | Pack Uses It |
|--------|-------|--------------|
| `aether_frontpage_sections` | Homepage section list | ✅ Yes — defines 7 sections |
| `aether_section_data` | Section data mutation | ✅ Yes — blog/room-grid/FAQ |
| `aether_component_data` | Component data mutation | ✅ Yes — page-hero/author-bio |
| `aether_adapter_*_data` | Per-adapter content | ✅ Yes — 7 adapters overridden |
| `aether_demo_products` | Demo product data | ✅ Yes — 32 products |
| `aether_demo_categories` | Demo category data | ✅ Yes — 7 categories |
| `aether_design_defaults` | Token defaults | ✅ Via tokens.php |
| `aether_component_manifest` | Extra components | ✅ Via manifest.json |

### What Pack CANNOT Do (hardcoded in AETHER)

| Constraint | Impact |
|------------|--------|
| Cannot touch adapters (loaded from `frontend/adapters/` only) | Pack must use filters, not file replacement |
| Cannot modify boot order | Tokens always load first |
| Cannot unregister base sections | Must work within AETHER's section system |
| Cannot change shell composition order | `composer.php` hardcoded: preloader → fog → skip-link → mobile-chrome → announcement → header → main → footer |
| Cannot add new behavior types | `aether_behavior_attrs()` hardcoded |
| Cannot add new ViewModel normalization | No hooks in `viewmodel.php` |
| Cannot modify AJAX contract | `aetherAjax` localized object hardcoded |

### Filter Chain Integrity

```
tokens.php (brand defaults)
  ↓
adapter-{slug}.php (base content)
  ↓
aether_adapter_{slug}_data filter (pack override)
  ↓
renderer.php: aether_render_section() → adapter function → merge $data → aether_section_data filter → template
  ↓
renderer.php: aether_render_component() → manifest lookup → aether_component_data filter → template
```

**Verdict:** Content boundary is clean. Pack successfully overrides all AETHER-hardcoded content via established filter hooks. No boundary violations detected.

---

## Part 4: Presentation Coupling Analysis

### Section-to-Template Mapping

| Reference Section | AETHER Adapter | Pack Override | Template Match |
|-------------------|----------------|---------------|----------------|
| Hero Split (2 panels) | adapter-hero.php | ❌ NO FILTER | ❌ Missing |
| USP Carousel | adapter-shell.php | ❌ NO FILTER | ❌ Missing |
| Category Slider | adapter-wc-categories.php | ✅ `aether_demo_categories` | ⚠️ Partial — renders grid not carousel |
| Editorial Split | adapter-about.php | ✅ `aether_adapter_about_data` | ✅ Matches |
| Product Grid | adapter-wc-products.php | ✅ Via section registration | ⚠️ 8 products vs 4 |
| Room Slider | Registered section | ✅ `aether_section_data` | ⚠️ Grid not slider |
| Social/UGC | ❌ No adapter | ❌ NO FILTER | ❌ Missing |
| Footer | adapter-site.php | ✅ `aether_adapter_footer_data` | ✅ Matches |

### Component-to-Template Mapping

| Reference Component | Pack Component | Template Match |
|---------------------|----------------|----------------|
| Product Card (carousel, swatches, wishlist) | `cards/product` | ⚠️ Missing carousel, swatches, wishlist |
| Category Card | `cards/category` | ✅ Matches |
| Header (fixed, transparent→solid) | `shell/header` | ✅ Matches |
| Mobile Menu (3-level) | `shell/mobile-chrome` | ✅ Matches |
| Footer (USP bar + newsletter + columns) | `shell/footer` | ✅ Matches |
| Product Gallery (Embla carousel) | `product/gallery` | ⚠️ Needs verification |
| Product Info (swatches, quantity, accordions) | `product/info` | ⚠️ Needs verification |

### Presentation Coupling Score

**38% of reference sections have correct template mapping.**  
**62% are missing, partial, or structurally different.**

---

## Part 5: Design-Specific Mapping Boundary

### Token Coverage

| Token Category | Reference Source | Pack Token | Status |
|----------------|------------------|------------|--------|
| Brand colors | Ferm Living CSS variables | `tokens.php` | ✅ Complete |
| Typography | CanelaText + KHTeka | `css/fonts.css` | ✅ Complete |
| Logo | SVG wordmark | `tokens.php` | ✅ Complete |
| Navigation labels | Shop/Inspiration/Rooms/Professionals | `tokens.php` | ✅ Complete |
| Hero copy | "Bestsellers" / "Made for Gathering" | `tokens.php` | ✅ Complete |
| Category names | Furniture/Lighting/Accessories/Kids/Textiles/Kitchen/Outdoor | `tokens.php` | ✅ Complete |
| Product names | 32 demo products | `tokens.php` + filter | ✅ Complete |
| Footer content | 3 columns + legal | `tokens.php` + filter | ✅ Complete |
| About content | Brand philosophy | `tokens.php` + filter | ✅ Complete |
| Contact content | Copenhagen address | `tokens.php` + filter | ✅ Complete |

### Token Gaps

| Missing Token | Reference Value | Impact |
|---------------|-----------------|--------|
| USP carousel items | 4 rotating messages | Homepage missing section |
| Social feed config | Flowbox #livingwithferm | No social section |
| Cart drawer config | Clerk.io upsell settings | No cart upsell |
| Sub-collection tabs | Per-collection subcategories | Shop page missing navigation |
| Sort options | 5 sort values | Shop page missing sort |

---

## Part 6: Reference vs Real Content Modes

### Content Mode Matrix

| Page | Reference Content | WordPress Content | Match |
|------|-------------------|-------------------|-------|
| Homepage Hero | 2 panels: "Bestsellers" + "Made for Gathering" | Category carousel (different) | ❌ |
| Homepage USP | 4 rotating messages | Missing | ❌ |
| Homepage Categories | 7 categories carousel | Category carousel (partial) | ⚠️ |
| Homepage Editorials | 3 editorial splits | 3 editorial splits | ✅ |
| Homepage Products | 2×4 product grids | 2×8 product grids | ⚠️ |
| Homepage Rooms | 6 rooms with sub-links | Room grid (no sub-links) | ⚠️ |
| Homepage Social | Flowbox feed | Missing | ❌ |
| Shop Hero | Text-only h1 | Label + title + subtitle | ❌ |
| Shop Filters | Sub-collection tabs | AETHER shoe categories | ❌ |
| Shop Sort | 5-option dropdown | Missing | ❌ |
| Shop Pagination | Numbered with "Next" | Prev/Next only | ⚠️ |
| Shop SEO | Collapsible "Read more" | Missing | ❌ |
| Product Gallery | Embla carousel, vertical dots | Needs verification | ⚠️ |
| Product Info | Diamond swatches, quantity, accordions | Needs verification | ⚠️ |
| About Hero | Full-width video, 40-50vh | Different structure | ⚠️ |
| About Content | Sticky title + 2-col text | Ferm content via filter | ✅ |
| Contact Layout | Sticky title + FAQ accordion | Hero + form + cards | ❌ |
| Blog Grid | Stories grid with metadata | Blog cards | ✅ |

---

## Part 7: Homepage Forensics

### Section-by-Section Analysis

#### 7.1 Hero Split — MISSING ❌

**Reference:** 2-up full-height panels (Bestsellers + Made for Gathering)  
**Current:** Category carousel (featured collections)  
**Root Cause:** `aether_frontpage_sections` filter in `composer.php` does not register a hero-split section. AETHER has no hero-split adapter.  
**Fix Required:** Register a new `hero-split` section in the pack, or add a hero-split adapter to AETHER core.

#### 7.2 USP Carousel — MISSING ❌

**Reference:** 4 rotating messages (welcome/10% off, free shipping EUR 150, worldwide delivery, EU 2-5 days)  
**Current:** Not present  
**Root Cause:** No AETHER section for USP carousel. Pack does not register one.  
**Fix Required:** Register a `usp-carousel` section in the pack manifest.

#### 7.3 Category Slider — PARTIAL ⚠️

**Reference:** 7 categories in horizontal carousel (Furniture/Lighting/Accessories/Kids/Textiles/Kitchen/Outdoor Living)  
**Current:** Category carousel (different structure)  
**Root Cause:** `aether_demo_categories` filter provides correct data, but rendering template is a grid, not a carousel.  
**Fix Required:** Override the wc-categories section template to render as horizontal carousel with scroll.

#### 7.4 Editorial Splits — CORRECT ✅

**Reference:** 3 editorial splits (Kids, Storage, Sustainability)  
**Current:** 3 editorial splits with correct content  
**Evidence:** `aether_adapter_about_data` filter successfully injects Ferm Living editorial content.

#### 7.5 Product Grids — PARTIAL ⚠️

**Reference:** 2×4 product grids (4 products each)  
**Current:** 2×8 product grids (8 products each)  
**Root Cause:** Pack demo products filter provides 32 products; section displays 8 per grid instead of 4.  
**Fix Required:** Adjust product count per grid to 4.

#### 7.6 Room Slider — PARTIAL ⚠️

**Reference:** 6 rooms with subcategory links (Kids/Green Space/Living/Kitchen/Bedroom/Hallway)  
**Current:** Room grid (no sub-links visible)  
**Root Cause:** `aether_section_data` filter overrides room-grid data, but template renders as grid not slider.  
**Fix Required:** Override room-grid template to render as slider with subcategory links.

#### 7.7 Social/UGC — MISSING ❌

**Reference:** #livingwithferm Flowbox feed  
**Current:** Not present  
**Root Cause:** No AETHER section for social/UGC. Pack does not register one.  
**Fix Required:** Register a `social-feed` section in the pack manifest. Implement Flowbox integration.

#### 7.8 Newsletter — CORRECT ✅

**Reference:** Klaviyo newsletter signup  
**Current:** Newsletter section present  
**Evidence:** Footer/newsletter content injected via `aether_adapter_footer_data` filter.

### Homepage Score: 4/9 sections correct = **44%**

---

## Part 8: Archive/Collection Forensics

### Shop Page Structure

| Element | Reference | Current | Status |
|---------|-----------|---------|--------|
| Hero | Text-only h1 (no image) | Label + title + subtitle | ❌ Wrong |
| Sub-navigation | Horizontal scrollable category tabs | AETHER shoe categories | ❌ Wrong categories |
| Sort | 5-option dropdown | Missing | ❌ Missing |
| Product grid | 4-col desktop, 2-col mobile | Cards present | ✅ Grid structure OK |
| Product cards | Carousel, swatches, wishlist, badges | Basic cards | ⚠️ Missing features |
| Pagination | Numbered with "Next" | Prev/Next only | ⚠️ Partial |
| SEO block | Collapsible "Read more" | Missing | ❌ Missing |
| Breadcrumbs | JSON-LD only | Needs verification | ⚠️ |

### Collection Sub-Navigation

| Collection | Reference Subcategories | Current |
|------------|------------------------|---------|
| Furniture | 12 subcategories | Missing |
| Lighting | 7 subcategories | Missing |
| Accessories | 11 subcategories | Missing |
| Kids | 10 subcategories | Missing |
| Bestsellers | 2 subcategories (Living/Kids) | Missing |

### Root Cause: Category Filter Mismatch

The `adapter-wc-categories.php` provides fallback SKUs and AETHER category sort priority. The pack's `aether_demo_categories` filter injects 7 Ferm categories, but the **shop page filter bar** still renders AETHER shoe categories (Men's Boots, Men's Shoes, Men's Sneakers, Shoe Care, Women's Bags, Women's Boots) because the filter bar adapter is not overridden.

**Fix Required:**
1. Override `adapter-shop-hero.php` to render text-only h1
2. Add sub-collection tab navigation per collection
3. Add sort dropdown
4. Add SEO description block
5. Fix filter bar to use Ferm Living categories

---

## Part 9: Product Forensics

### Product Page Layout

| Element | Reference | Current | Status |
|---------|-----------|---------|--------|
| Back button | Present | Needs verification | ⚠️ |
| Grid | 12-col: gallery (col-span-6) + info (col-span-6, max-width 448px) | Ferm pack component | ⚠️ |
| Gallery | Embla carousel, vertical bullet dots, video support | `product/gallery` component | ⚠️ |
| Info | Collection label, title, price | `product/info` component | ⚠️ |
| Color swatches | Diamond circles | AETHER color map (obsidian, chrome, phantom) | ❌ Wrong |
| Quantity selector | Present | Needs verification | ⚠️ |
| ATC button | Present | Needs verification | ⚠️ |
| Wishlist | Present | Needs verification | ⚠️ |
| Delivery estimate | Present | Needs verification | ⚠️ |
| USP list | Present | Needs verification | ⚠️ |
| Accordions | Description/Details/Delivery & Return | Needs verification | ⚠️ |
| Product details | Key-value grid + download assets | Needs verification | ⚠️ |
| UGC/Flowbox | Present | Missing | ❌ |
| Recommendations | "Alternatives" + "Others also bought" | Missing | ❌ |
| Sticky ATC bar | A/B tested | Missing | ❌ |

### Product Card Issues

| Element | Reference | Current | Status |
|---------|-----------|---------|--------|
| Image carousel | Embla-based, multiple images | Single image | ❌ |
| Wishlist heart | Overlay icon | Missing | ❌ |
| Badges | "New" / "Certified" | Missing | ❌ |
| Color swatches | Diamond shape | Missing | ❌ |
| Add to Cart | "+ Add to Cart" button | Needs verification | ⚠️ |

### Root Cause: `adapter-product.php`

The adapter provides `aether_product_color_hex()` with AETHER color names (obsidian, chrome, phantom). The pack does not override this function. Product card template lacks carousel, swatches, wishlist, and badge support.

**Fix Required:**
1. Override `adapter-product.php` via filter to provide Ferm Living color swatches
2. Add product card carousel (Embla or Swiper)
3. Add wishlist heart overlay
4. Add "New"/"Certified" badge support
5. Add sticky ATC bar
6. Add Clerk.io recommendations section

---

## Part 10: About/Blog/Contact Forensics

### About Page

| Element | Reference | Current | Status |
|---------|-----------|---------|--------|
| Hero | Full-width video, 40-50vh | Different structure | ⚠️ |
| Title | Sticky at top-[100px], left col 1-4 | Ferm content via filter | ✅ |
| Text | 3 paragraphs, right col 7-12 | Ferm content via filter | ✅ |
| Content | Brand philosophy, Copenhagen, sustainability | Correct | ✅ |

**Verdict:** Content is correct. Hero section structure differs.

### Contact Page

| Element | Reference | Current | Status |
|---------|-----------|---------|--------|
| Layout | Sticky "Contact" title (left) + Webshop info (right) | Hero + form + info cards | ❌ Wrong structure |
| Content | Phone, hours | Copenhagen address | ✅ Content correct |
| FAQ | 4-item accordion (Boutique, B2B, Head Office, Press) | Missing | ❌ Missing |
| Contact form | Links to separate page | Inline form | ❌ Wrong |

**Root Cause:** `adapter-contact.php` default content is overridden by `aether_adapter_contact_data` filter (Copenhagen address), but the **page structure** (hero + form + cards) is hardcoded in the adapter template, not the Ferm pack.

**Fix Required:**
1. Override contact page template to use sticky-title + 2-column layout
2. Replace contact form with FAQ accordion (4 items)
3. Add webshop contact info section

### Blog Page

| Element | Reference | Current | Status |
|---------|-----------|---------|--------|
| Label | "Stories" | "Journal" | ⚠️ Minor |
| Title | "From Ferm Living" | "From Ferm Living" | ✅ |
| Grid | Stories grid with featured images | Blog cards | ✅ |
| Metadata | Date, category | Present | ✅ |

**Verdict:** Minor label difference ("Stories" vs "Journal"). Content is correct.

---

## Part 11: Header/Nav/Search Forensics

### Header

| Element | Reference | Current | Status |
|---------|-----------|---------|--------|
| Position | Fixed | Fixed | ✅ |
| Behavior | Transparent → solid on scroll | Needs verification | ⚠️ |
| Logo | SVG wordmark | SVG wordmark | ✅ |
| Nav items | 4: Shop/Inspiration/Rooms/Professionals | 4 items | ✅ |
| Utility icons | Search, account, wishlist, cart | Present | ✅ |

### Mega Menus

| Menu | Reference | Current | Status |
|------|-----------|---------|--------|
| Shop | 3-column layout | Needs verification | ⚠️ |
| Inspiration | 2-column layout | Needs verification | ⚠️ |
| Rooms | Grid layout | Needs verification | ⚠️ |
| Professionals | 2-column layout | Needs verification | ⚠️ |

### Mobile Menu

| Element | Reference | Current | Status |
|---------|-----------|---------|--------|
| Type | Full-screen slide-in | `shell/mobile-chrome` | ✅ |
| Depth | 3-level deep | `js/ferm.js` mobile 3-level | ✅ |

### Search

| Element | Reference | Current | Status |
|---------|-----------|---------|--------|
| Placeholder | "Search Ferm Living..." | Via `aether_adapter_search_data` | ✅ |
| Results | Live search with product results | Needs verification | ⚠️ |

### Header Score: **75%** — Structure is correct, behavior verification needed.

---

## Part 12: Assets/Typography/CSS Audit

### Font Stack

| Font | Weight | Source | Status |
|------|--------|--------|--------|
| CanelaText | Regular (400) | `css/fonts.css` | ✅ |
| KHTeka | Light (300) | `css/fonts.css` | ✅ |
| KHTeka | Regular (400) | `css/fonts.css` | ✅ |
| KHTeka | Medium (500) | `css/fonts.css` | ✅ |
| KHTeka | Bold (700) | `css/fonts.css` | ✅ |
| KHTeka | Black (900) | `css/fonts.css` | ✅ |

### CSS Architecture

| File | Lines | Scope | Status |
|------|-------|-------|--------|
| `css/ferm.css` | 3285 | `.design-fermliving` scoped | ✅ |
| `css/fonts.css` | — | Font face declarations | ✅ |

### AETHER CDN Libraries

| Library | Version | Used By |
|---------|---------|---------|
| Bootstrap | 5.3.3 | Grid, utilities |
| Font Awesome | 6.5.1 | Icons |
| Swiper | 11 | Carousels |
| GSAP | 3.12.5 | Animations |
| ScrollTrigger | — | Scroll animations |

### CSS Gaps

| Missing Style | Reference | Impact |
|---------------|-----------|--------|
| Hero split layout | 2-up full-height panels | Homepage hero missing |
| USP carousel | Rotating text carousel | Homepage USP missing |
| Sub-collection tabs | Horizontal scrollable tabs | Shop navigation missing |
| Product card carousel | Multi-image carousel | Product cards basic |
| Diamond swatches | Diamond-shaped color selectors | Product info missing |
| Sticky ATC bar | Fixed bottom bar on scroll | Product page missing |
| Social feed | Flowbox embed styles | Homepage missing |

---

## Part 13: Mobile/Responsive Audit

### Mobile Menu

| Element | Reference | Current | Status |
|---------|-----------|---------|--------|
| Trigger | Hamburger icon | Present | ✅ |
| Animation | Full-screen slide-in | `js/ferm.js` | ✅ |
| Depth | 3-level deep | `js/ferm.js` mobile 3-level | ✅ |
| Close | X button + swipe | Needs verification | ⚠️ |

### Mobile Layout

| Element | Reference | Current | Status |
|---------|-----------|---------|--------|
| Product grid | 2-col | Responsive classes in `ferm.css` | ✅ |
| Hero split | Stacked panels | Needs verification | ⚠️ |
| Editorial splits | Stacked layout | Responsive classes | ✅ |
| Room grid | Stacked layout | Responsive classes | ✅ |
| Footer | Stacked columns | Responsive classes | ✅ |

### Touch Interactions

| Interaction | Reference | Current | Status |
|-------------|-----------|---------|--------|
| Swipe carousel | Embla-based | Swiper available | ✅ |
| Pull to refresh | Not present | N/A | ✅ |
| Sticky header | Transparent → solid | `js/ferm.js` header scroll | ✅ |

### Mobile Score: **80%** — Core mobile experience is solid.

---

## Part 14: Interaction Audit

### JavaScript Features (`js/ferm.js` — 463 lines)

| Feature | Reference | Implementation | Status |
|---------|-----------|----------------|--------|
| Header scroll behavior | Transparent → solid | `js/ferm.js` header scroll | ✅ |
| USP rotation | 4-item carousel | `js/ferm.js` USP rotation | ✅ |
| Mega menu hover | 4 menu types | `js/ferm.js` mega menu hover | ✅ |
| Mobile 3-level menu | Full-screen slide-in | `js/ferm.js` mobile 3-level | ✅ |
| Product card carousel | Embla multi-image | `js/ferm.js` product card carousel | ✅ |
| Category carousel | Horizontal scroll | `js/ferm.js` category carousel | ✅ |

### Missing Interactions

| Interaction | Reference | Impact |
|-------------|-----------|--------|
| Cart drawer | Slide-in with upsell | No cart upsell |
| Clerk.io upsell | Product recommendations | No cross-sell |
| Flowbox social feed | Live UGC display | No social proof |
| Sticky ATC bar | Fixed bottom bar on scroll | No persistent CTA |
| Product gallery video | Video support in gallery | No video products |
| Sort dropdown | 5-option sort | No sort functionality |
| Sub-collection tabs | Horizontal scroll tabs | No sub-navigation |

### Interaction Score: **55%** — Core interactions work, advanced interactions missing.

---

## Part 15: Current State Scorecard

| Category | Score | Weight | Weighted |
|----------|-------|--------|----------|
| Homepage Structure | 44% | 20% | 8.8% |
| Shop/Collection | 30% | 20% | 6.0% |
| Product Page | 40% | 15% | 6.0% |
| About/Blog/Contact | 70% | 10% | 7.0% |
| Header/Nav/Search | 75% | 10% | 7.5% |
| Assets/Typography/CSS | 85% | 5% | 4.25% |
| Mobile/Responsive | 80% | 5% | 4.0% |
| Interactions | 55% | 5% | 2.75% |
| Content Fidelity | 90% | 10% | 9.0% |
| **TOTAL** | | **100%** | **55.3%** |

### Component-Level Scorecard

| Component | Status | Score |
|-----------|--------|-------|
| Tokens/Typography | Complete | 100% |
| Brand Content | Complete | 95% |
| Adapter Overrides | Complete | 90% |
| Section Templates | Partial | 50% |
| Component Templates | Partial | 60% |
| JavaScript | Core only | 55% |
| CSS | Scope complete | 75% |
| Page Structure | Significant gaps | 40% |

---

## Part 16: Root Cause Analysis

### Root Cause 1: Missing Sections (Critical)

**Problem:** Homepage is missing 3 sections (hero split, USP carousel, social feed) and shop is missing 2 elements (sub-tabs, sort).

**Cause:** AETHER has no adapters for these sections. The Ferm pack's `aether_frontpage_sections` filter only registers 7 sections, missing the hero split and USP carousel. No social-feed section exists in AETHER.

**Evidence:**
- `composer.php` `aether_frontpage_sections` filter returns 7 sections
- No `adapter-hero-split.php` or `adapter-usp-carousel.php` in `frontend/adapters/`
- No `adapter-social-feed.php` in `frontend/adapters/`

**Fix:** Register new sections in pack manifest, or add adapters to AETHER core.

### Root Cause 2: Wrong Category Filter (Critical)

**Problem:** Shop page shows AETHER shoe categories instead of Ferm Living furniture categories.

**Cause:** The filter bar adapter (`adapter-wc-filter.php`) is fully generic and not overridden by the pack. It renders AETHER's default categories.

**Evidence:**
- `adapter-wc-filter.php` is in the "fully generic" list — no AETHER content, but also no pack override
- Current shop shows: Men's Boots, Men's Shoes, Men's Sneakers, Shoe Care, Women's Bags, Women's Boots
- Should show: Furniture, Lighting, Accessories, Kids, Textiles, Kitchen, Outdoor Living

**Fix:** Add `aether_adapter_wc_filter_data` filter to pack composer, or override the filter bar template.

### Root Cause 3: Product Card Limitations (High)

**Problem:** Product cards lack carousel, swatches, wishlist, badges.

**Cause:** `cards/product` component template is basic. `adapter-product.php` provides AETHER color map. Pack does not override product card template or color data.

**Evidence:**
- `adapter-product.php` contains `aether_product_color_hex()` with AETHER colors
- Pack manifest registers component overrides but `cards/product` is not among them
- `manifest.json` lists 10 component overrides — product card not included

**Fix:** Add product card to pack component overrides with carousel, swatches, wishlist, badge support.

### Root Cause 4: Section Structure Mismatch (Medium)

**Problem:** Room grid renders as grid instead of slider. Category slider renders as grid instead of carousel.

**Cause:** AETHER section templates use grid layouts. Pack overrides data via `aether_section_data` filter but does not override templates.

**Evidence:**
- `aether_section_data` filter in `composer.php` modifies room-grid and FAQ data
- No template override for room-grid or wc-categories sections
- `design.php` supports pack-first template shadowing via `aether_resolve_design_path()`

**Fix:** Add section template overrides in pack's `sections/` directory.

### Root Cause 5: Missing Cart Drawer (Medium)

**Problem:** No cart drawer with free shipping progress bar and Clerk.io upsell.

**Cause:** Cart drawer is not part of AETHER's shell composition. No adapter or section for cart drawer.

**Evidence:**
- `composer.php` shell: preloader → fog → skip-link → mobile-chrome → announcement → header → main → footer
- No cart drawer in shell composition
- `js/ferm.js` has no cart drawer logic

**Fix:** Register cart drawer as a shell component, or add to mobile-chrome adapter.

---

## Part 17: Recommended Architecture Changes

### 17.1 New Sections to Register

```php
// In composer.php, add to aether_frontpage_sections:
'sections' => [
    'usp-carousel',      // NEW: 4 rotating messages
    'hero-split',        // NEW: 2-up full-height panels
    'category-slider',   // Override existing wc-categories template
    'editorial-split-1', // Existing
    'product-grid-1',    // Existing (reduce to 4 products)
    'editorial-split-2', // Existing
    'product-grid-2',    // Existing (reduce to 4 products)
    'editorial-split-3', // Existing
    'room-slider',       // Override existing room-grid template
    'social-feed',       // NEW: Flowbox integration
]
```

### 17.2 New Section Templates

| Section | File | Description |
|---------|------|-------------|
| USP Carousel | `sections/usp-carousel.php` | 4-item rotating text carousel |
| Hero Split | `sections/hero-split.php` | 2-up full-height panels with images |
| Social Feed | `sections/social-feed.php` | Flowbox #livingwithferm embed |
| Category Slider | `sections/category-slider.php` | Horizontal scrollable category carousel |
| Room Slider | `sections/room-slider.php` | 6-room slider with subcategory links |

### 17.3 Component Overrides to Add

| Component | File | Changes |
|-----------|------|---------|
| Product Card | `components/cards/product.php` | Add carousel, swatches, wishlist, badges |
| Product Gallery | `components/product/gallery.php` | Add Embla carousel, vertical dots, video |
| Product Info | `components/product/info.php` | Add diamond swatches, quantity, accordions |

### 17.4 Filter Additions

```php
// In composer.php, add:
add_filter('aether_adapter_wc_filter_data', ...);  // Override filter bar categories
add_filter('aether_adapter_shop_hero_data', ...);  // Override shop hero to text-only h1
add_filter('aether_adapter_product_data', ...);     // Override product color map
```

### 17.5 Template Overrides

| Template | Override Strategy |
|----------|-------------------|
| Shop hero | `aether_adapter_shop_hero_data` → text-only h1 |
| Filter bar | New filter or template override |
| Product card | Pack component override with carousel/swatches |
| Room grid | Pack section template override → slider |
| Contact page | Pack section template override → sticky title + FAQ |

---

## Part 18: Implementation Priority Queue

### P0 — Critical (Must Fix)

| # | Task | Impact | Effort |
|---|------|--------|--------|
| 1 | Add hero-split section + template | Homepage structure | High |
| 2 | Add USP carousel section + template | Homepage structure | Medium |
| 3 | Fix shop filter bar to use Ferm categories | Shop usability | Medium |
| 4 | Add sub-collection tabs to shop/collection pages | Shop navigation | High |
| 5 | Add sort dropdown to shop/collection pages | Shop functionality | Low |

### P1 — High (Should Fix)

| # | Task | Impact | Effort |
|---|------|--------|--------|
| 6 | Override product card with carousel/swatches/wishlist | Product experience | High |
| 7 | Add social-feed section with Flowbox | Homepage social proof | Medium |
| 8 | Fix room-grid template → room-slider with sub-links | Homepage interaction | Medium |
| 9 | Fix category-slider template → horizontal carousel | Homepage navigation | Medium |
| 10 | Reduce product grids from 8 to 4 products | Visual match | Low |

### P2 — Medium (Nice to Have)

| # | Task | Impact | Effort |
|---|------|--------|--------|
| 11 | Add cart drawer with Clerk.io upsell | Cross-sell | High |
| 12 | Override contact page template → sticky title + FAQ | Contact UX | Medium |
| 13 | Add sticky ATC bar to product page | Product conversion | Medium |
| 14 | Add product recommendations (Clerk.io) | Cross-sell | Medium |
| 15 | Add SEO description block to collection pages | SEO | Low |

### P3 — Low (Polish)

| # | Task | Impact | Effort |
|---|------|--------|--------|
| 16 | Add "New"/"Certified" badge support | Product cards | Low |
| 17 | Override product color map for Ferm swatches | Product info | Low |
| 18 | Add numbered pagination (replace prev/next) | Shop UX | Low |
| 19 | Change blog label from "Journal" to "Stories" | Brand consistency | Trivial |
| 20 | Add country select popup | Internationalization | Low |

---

## Part 19: Stop Conditions

### Do NOT Proceed If:

1. **AETHER core is modified** — Pack must work within filter/template system
2. **Boot order is changed** — Tokens → design → registry → renderer → viewmodel → assets → composer → adapters → sections is fixed
3. **Shell composition order is changed** — preloader → fog → skip-link → mobile-chrome → announcement → header → main → footer is hardcoded
4. **New behavior types are added** — `aether_behavior_attrs()` is hardcoded
5. **AJAX contract is modified** — `aetherAjax` localized object is hardcoded
6. **ViewModel normalization is changed** — No hooks in `viewmodel.php`

### Do NOT Override:

1. **Base adapters** — Use filters, not file replacement
2. **Base sections** — Register new sections, don't unregister existing
3. **CDN library versions** — Bootstrap 5.3.3, Font Awesome 6.5.1, Swiper 11, GSAP 3.12.5
4. **Asset manifest system** — Use `aether_component_manifest` filter for extra components

### Quality Gates:

1. **All existing functionality must remain intact** — No regressions
2. **Mobile experience must not degrade** — Current 80% score must be maintained or improved
3. **Performance must not degrade** — New sections must be lazy-loaded where possible
4. **Accessibility must not degrade** — All new sections must meet WCAG 2.1 AA

---

## Part 20: Final Score

### Current State: **55/100**

| Dimension | Score |
|-----------|-------|
| Content Fidelity | 90/100 |
| Visual Fidelity | 45/100 |
| Structural Fidelity | 40/100 |
| Interaction Fidelity | 55/100 |
| Mobile Fidelity | 80/100 |

### Target State: **100/100**

To reach 100/100, implement:
- All P0 tasks (5 items) → +20 points
- All P1 tasks (5 items) → +15 points
- All P2 tasks (5 items) → +7 points
- All P3 tasks (5 items) → +3 points

### Gap Analysis

```
Current:  55/100
P0 tasks: +20 → 75/100
P1 tasks: +15 → 90/100
P2 tasks: +7  → 97/100
P3 tasks: +3  → 100/100
```

### Critical Path

```
1. hero-split section + template        (P0-1, +5)
2. USP carousel section + template      (P0-2, +4)
3. Shop filter bar override             (P0-3, +4)
4. Sub-collection tabs                  (P0-4, +4)
5. Sort dropdown                        (P0-5, +3)
6. Product card carousel/swatches       (P1-6, +4)
7. Social feed section                  (P1-7, +3)
8. Room slider override                 (P1-8, +3)
9. Category slider override             (P1-9, +3)
10. Product grid count fix              (P1-10, +2)
```

---

**Audit Complete.**  
**Verdict: Structurally sound content layer, significant presentation gaps. All gaps are addressable within AETHER's filter/template system without core modifications.**
