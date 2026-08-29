# PHASE 2 — COMPONENT MAPPING MATRIX

**Date:** 2026-08-21
**Status:** Complete — every Ferm UI component classified per §18 of Master Prompt

---

## 1. Classification Legend

| Class | Meaning | Action |
|-------|---------|--------|
| **A** | Existing AETHER component renders it directly | No change needed — CSS-only restyling via tokens |
| **B** | Existing component renders with client styling | Token overrides + design pack CSS |
| **C** | Existing component needs client-specific variant | Create override in `designs/fermliving/components/` |
| **D** | New client component required | Create new in `designs/fermliving/components/` |
| **E** | Platform capability gap | Document — requires core change (rare) |

---

## 2. Shell Components

| Ferm Component | AETHER Component | Class | Action | Notes |
|---------------|-----------------|-------|--------|-------|
| Announcement bar (rotating USP) | `shell/announcement` | **C** | Override | Ferm uses rotating carousel (4 items) with auto-advance; AETHER uses static text. Create override with Embla-like carousel. |
| Header (logo left, nav center, icons right) | `shell/header` | **C** | Override | Ferm has different layout (logo left, nav center, icons right) vs AETHER (logo left, nav right). Different nav structure, mega menu trigger. |
| Mega menu (3-column dropdown) | `shell/header` (mega section) | **C** | Override | Ferm mega menu is a separate component within header. AETHER header has simpler dropdown. Build as part of header override. |
| Mobile menu (nested slide-out panels) | `shell/mobile-chrome` | **C** | Override | Ferm uses 3-level deep slide-out (menu → subcategory → tertiary). AETHER has simpler 2-level. Tertiary navigation is unique to Ferm. |
| Search overlay | `nav/search` | **B** | Style only | Ferm search is a full overlay. AETHER has search modal. CSS token overrides should handle visual difference. |
| Cart drawer | `cart/items` + `cart/summary` | **B** | Style only | Ferm cart drawer is a slide-out panel with upsell. AETHER has cart drawer architecture. Style differences via CSS. |
| Footer (USP row + newsletter + columns + legal) | `shell/footer` | **C** | Override | Ferm footer has unique USP row at top, Klaviyo newsletter, 3 link columns, payment icons. AETHER footer has social links + newsletter. Create override. |
| Back to top | `shell/back-to-top` | **A** | Direct | No structural change needed |

---

## 3. Hero Components

| Ferm Component | AETHER Component | Class | Action | Notes |
|---------------|-----------------|-------|--------|-------|
| Homepage hero (split-panel images) | `hero/slider` + `hero/slide` | **C** | Override | Ferm homepage uses a category image grid (2×2 tiles) NOT a traditional hero slider. This is a different visual pattern. Create override as category grid section. |
| Page title band | `hero/page-title` | **B** | Style only | Ferm page titles use CanelaText serif. Token overrides handle typography. |
| Page banner | `hero/page-banner` | **A** | Direct | No change needed |

---

## 4. Section Components

| Ferm Component | AETHER Component | Class | Action | Notes |
|---------------|-----------------|-------|--------|-------|
| Section header (label + title) | `section/header` | **B** | Style only | Ferm uses CanelaText for section titles. Token overrides. |
| Filter bar (sort + filters) | `section/filter-bar` | **B** | Style only | Ferm filter is minimal (sort dropdown + filter button). AETHER filter bar is similar. CSS overrides. |
| Product grid (4-col) | `section/shop-grid` | **B** | Style only | Ferm product grid uses 12-col grid with `col-span-6 tab_l:col-span-3`. AETHER uses similar grid. Token overrides for gaps and spacing. |
| Pagination | `section/pagination` | **A** | Direct | Standard pagination |
| Newsletter section | `section/newsletter` | **C** | Override | Ferm uses Klaviyo embed. AETHER has newsletter form. Create override with Klaviyo-style form or use Aureon newsletter adapter. |
| Accordion (FAQ/tabs) | `section/accordion` | **B** | Style only | Ferm uses native details/summary or custom accordion. Token overrides. |
| CTA block | `section/cta` | **B** | Style only | Token overrides for button styling |

---

## 5. Card Components

| Ferm Component | AETHER Component | Class | Action | Notes |
|---------------|-----------------|-------|--------|-------|
| Product card (image carousel + info + CTA) | `card/product` | **C** | Override | Ferm product card has: Embla image carousel, color swatches, "New"/"Certified" badges, wishlist heart, "+ Add to Cart" CTA. AETHER product card has different layout. Create override. |
| Category card (full-bleed image + title) | `card/category` | **C** | Override | Ferm category cards are full-bleed images with title overlay at bottom-left. AETHER has count + CTA arrow. Create override. |
| Blog card | `card/blog` | **B** | Style only | Ferm blog cards show image + title + date. AETHER has similar structure. Token overrides. |
| Review card | `card/review` | **A** | Direct | Review card structure is similar |
| Team card | `card/team` | **A** | Direct | Team card structure is similar |
| Wishlist card | `card/wishlist` | **B** | Style only | Token overrides |

---

## 6. Product Components

| Ferm Component | AETHER Component | Class | Action | Notes |
|---------------|-----------------|-------|--------|-------|
| Product gallery (image carousel + dots) | `product/gallery` | **C** | Override | Ferm uses Embla carousel with dots navigation. AETHER uses different gallery. Create override. |
| Product info (title, price, variants, CTA) | `product/info` | **C** | Override | Ferm product info has unique layout: title, price, color swatches as links, size selector, quantity, "+ Add to Cart" button. Different from AETHER. Create override. |
| Product accordion (description, shipping, care) | `product/specs` | **B** | Style only | Ferm uses accordion tabs. AETHER has specs component. Token overrides. |
| Product reviews | `product/reviews` | **A** | Direct | Review display is similar |
| Related products carousel | `product/related` | **B** | Style only | Token overrides for carousel styling |
| Product breadcrumb | `product/breadcrumb` | **A** | Direct | Standard breadcrumb |
| Product sticky bar | `product/sticky-bar` | **B** | Style only | Ferm has sticky add-to-cart bar. AETHER has sticky bar. Token overrides. |
| Color swatches (rotated circles) | — | **D** | New | Ferm uses rotated 45° color swatch circles that link to variant products. This is a unique pattern. Create as product card sub-component. |
| Size selector (pill buttons) | — | **D** | New | Ferm size selector uses pill-shaped buttons. Create as product info sub-component. |

---

## 7. Content Components

| Ferm Component | AETHER Component | Class | Action | Notes |
|---------------|-----------------|-------|--------|-------|
| Article hero (featured image + meta) | `content/article-hero` | **B** | Style only | Token overrides |
| Article meta (author, date, read time) | `content/article-meta` | **A** | Direct | Similar structure |
| Article body | `content/article-body` | **A** | Direct | Similar structure |
| Author bio | `content/author-bio` | **A** | Direct | Similar structure |
| Story/editorial split | `content/story` | **B** | Style only | Ferm uses text + image split layout. Token overrides. |
| Page content (legal) | `content/page` | **A** | Direct | Standard page content |

---

## 8. Form Components

| Ferm Component | AETHER Component | Class | Action | Notes |
|---------------|-----------------|-------|--------|-------|
| Newsletter form | `form/newsletter` | **B** | Style only | Ferm uses Klaviyo. AETHER has newsletter form. Use Aureon adapter, style via tokens. |
| Contact form | `form/contact` | **A** | Direct | Standard form |
| Login form | `form/login` | **A** | Direct | Standard form |
| Register form | `form/register` | **A** | Direct | Standard form |
| Forgot password | `form/forgot-password` | **A** | Direct | Standard form |
| Search form | `form/search` (within nav/search) | **B** | Style only | Token overrides |

---

## 9. Commerce Components

| Ferm Component | AETHER Component | Class | Action | Notes |
|---------------|-----------------|-------|--------|-------|
| Cart items | `cart/items` | **B** | Style only | Ferm cart items show image, title, variant, quantity, price. Token overrides. |
| Cart summary | `cart/summary` | **B** | Style only | Ferm shows subtotal + checkout CTA. Token overrides. |
| Checkout form | WC checkout template | **B** | Style only | WooCommerce checkout — style via CSS only |
| Checkout order items | `checkout/order-items` | **B** | Style only | Token overrides |
| Order confirmation | `order/confirmation` | **B** | Style only | Token overrides |
| Quantity stepper | — (within product/cart) | **D** | New | Ferm has +/- quantity buttons. Create as reusable sub-component. |

---

## 10. Utility Components

| Ferm Component | AETHER Component | Class | Action | Notes |
|---------------|-----------------|-------|--------|-------|
| 404 page | `error/404` | **B** | Style only | Token overrides |
| Empty state | `utility/empty-state` | **A** | Direct | Standard empty state |
| Countdown (coming soon) | `soon/countdown` | **A** | Direct | Standard countdown |

---

## 11. Summary

| Class | Count | Components |
|-------|-------|-----------|
| **A** (direct) | 17 | preloader, fog, skip-link, back-to-top, page-title, page-banner, pagination, filter-bar, review-card, team-card, article-meta, article-body, author-bio, page-content, contact-form, login-form, register-form, forgot-password, 404, countdown, empty-state, product-breadcrumb, product-reviews, search-form, cart-items |
| **B** (style) | 18 | search-overlay, cart-drawer, section-header, product-grid, blog-card, wishlist-card, product-accordion, product-sticky-bar, newsletter-form, checkout-form, order-items, cart-summary, article-hero, story, size-selector, hero-page-title, accordion, cta |
| **C** (override) | 9 | announcement, header, mega-menu, mobile-menu, footer, product-card, category-card, product-gallery, product-info |
| **D** (new) | 3 | color-swatches, quantity-stepper, homepage-category-grid |
| **E** (gap) | 0 | — |

**Result:** 0 platform gaps. All Ferm UI can be implemented within the existing architecture using overrides (C), styling (B), or new client-specific components (D).

---

## 12. Override Files to Create

```
frontend/designs/fermliving/components/
├── shell/
│   ├── header.php          — C: Logo left, nav center, icons right + mega menu
│   ├── announcement.php    — C: Rotating USP carousel
│   └── footer.php          — C: USP row + newsletter + 3 columns + legal
├── hero/
│   └── category-grid.php   — D: Homepage category image grid (replaces hero slider)
├── cards/
│   ├── product.php         — C: Image carousel + badges + heart + CTA + swatches
│   └── category.php        — C: Full-bleed image + title overlay
├── product/
│   ├── gallery.php         — C: Embla carousel + dots
│   ├── info.php            — C: Title + price + swatches + size + qty + CTA
│   └── swatches.php        — D: Color swatch component
└── section/
    └── newsletter.php      — C: Klaviyo-style newsletter form
```

---

## 13. Next Phase

→ [PHASE3_TEMPLATE_MATRIX.md](./PHASE3_TEMPLATE_MATRIX.md)
