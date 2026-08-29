# PHASE 9 — HOMEPAGE & PRODUCT PAGE

**Date:** 2026-08-21
**Status:** Complete

---

## 1. New Sections Created

### 1.1 `ferm-editorial-split` — Text + Image Band
- **File:** `sections/section-editorial-split.php`
- **Purpose:** Homepage editorial blocks ("Bestsellers for Kids", etc.)
- **Layout:** 50/50 split — image on one side, text + CTA on the other
- **Responsive:** Stacks vertically on mobile, side-by-side on desktop
- **Reverse mode:** `reverse` flag flips image/text sides
- **Data:** title, text (HTML), image, image_alt, cta_label, cta_url, reverse

### 1.2 `ferm-room-grid` — Category Image Cards + Product Links
- **File:** `sections/section-room-grid.php`
- **Purpose:** Homepage room sections ("The Bedroom", "The Office", etc.)
- **Layout:** 2-col mobile → 5-col desktop grid of room cards
- **Each card:** Image with title overlay + optional product category links below
- **Data:** items[] → { title, image, url, links[] → { label, url } }

---

## 2. Component Overrides Created

### 2.1 `product/gallery` — Embla-style Image Carousel
- **File:** `components/product/gallery.php`
- **Purpose:** Product page image gallery
- **Features:**
  - Flex-based carousel with slide tracking
  - Desktop dots navigation (centered)
  - Mobile full-width dot indicators
  - Lazy loading (eager for first image, lazy for rest)
  - `data-image-zoom` attribute for zoom integration
  - ARIA roles for accessibility (tablist, tab, slide)
- **Contract:** Keeps `.product-gallery`, `data-image-zoom` — platform gallery JS unchanged

### 2.2 `product/info` — Title, Price, Swatches, Size, Qty, CTA
- **File:** `components/product/info.php`
- **Purpose:** Product page info/buy box
- **Features:**
  - Badge display (New, Certified)
  - Product title (h1)
  - Price with old price strike-through
  - Certified badge with reason text
  - Color swatches (rotated 45° circles, active state ring)
  - Size selector (pill buttons, unavailable state)
  - Quantity stepper (- / input / +)
  - Add to Cart button
  - Description block
  - SKU display
- **Contract:** Keeps `.pd-info`, `.pd-title`, `.pd-price`, `data-product-id`, `data-button-add-to-cart` — AJAX cart JS unchanged

---

## 3. CSS Additions (393 new lines)

| Section | Lines | Coverage |
|---------|-------|----------|
| Editorial split | 1-95 | 50/50 layout, responsive, reverse mode, sticky text |
| Room grid | 97-175 | 2-col → 5-col grid, image cards, title overlay, links |
| Product gallery | 380-470 | Carousel viewport, dots, mobile dots, active states |
| Product info | 472-620 | Title, price, certified, swatches, sizes, quantity, add-to-cart, description, SKU |

**Total CSS:** 1,476 lines (was 1,083)

---

## 4. Manifest Updates

### Component Overrides (7 total)
```json
{
  "shell/header": "components/shell/header.php",
  "shell/mobile-chrome": "components/shell/mobile-chrome.php",
  "shell/footer": "components/shell/footer.php",
  "card/product": "components/cards/product.php",
  "card/category": "components/cards/category.php",
  "product/gallery": "components/product/gallery.php",
  "product/info": "components/product/info.php"
}
```

### Pack Sections (2 total)
```json
{
  "ferm-editorial-split": "sections/section-editorial-split.php",
  "ferm-room-grid": "sections/section-room-grid.php"
}
```

---

## 5. Homepage Composition

The Ferm Living homepage uses these sections (in order):

| # | Section | Source | Engine Section | Pack Section |
|---|---------|--------|---------------|-------------|
| 1 | Announcement Bar | `shell/announcement` | ✓ | CSS restyled |
| 2 | Header | `shell/header` | — | **Override** |
| 3 | Category Grid | `categories` | ✓ | CSS restyled |
| 4 | Editorial Split | — | — | **New: ferm-editorial-split** |
| 5 | Product Grid | `bestsellers` | ✓ | CSS restyled |
| 6 | Room Grid | — | — | **New: ferm-room-grid** |
| 7 | Editorial Split 2 | — | — | **New: ferm-editorial-split** (reverse) |
| 8 | Product Grid 2 | `bestsellers` | ✓ | CSS restyled |
| 9 | Room Grid + Links | — | — | **New: ferm-room-grid** |
| 10 | Newsletter | `newsletter` | ✓ | CSS restyled |
| 11 | Footer | `shell/footer` | — | **Override** |

---

## 6. Product Page Composition

| # | Component | Engine | Pack Override |
|---|-----------|--------|--------------|
| 1 | Breadcrumb | ✓ | CSS restyled |
| 2 | Gallery | ✓ | **Override** (Embla carousel) |
| 3 | Info (buy box) | ✓ | **Override** (swatches, size, qty) |
| 4 | Accordion (specs) | ✓ | CSS restyled |
| 5 | Reviews | ✓ | CSS restyled |
| 6 | Related Products | ✓ | CSS restyled |

---

## 7. Design Pack Total: 24 files

```
frontend/designs/fermliving/
├── manifest.json          (170 lines)
├── tokens.php             (33 lines)
├── css/
│   ├── fonts.css          (55 lines)
│   └── ferm.css           (1,476 lines)
├── js/
│   └── ferm.js            (292 lines)
├── sections/
│   ├── section-editorial-split.php  (76 lines)
│   └── section-room-grid.php        (82 lines)
├── components/
│   ├── shell/
│   │   ├── header.php     (157 lines)
│   │   ├── mobile-chrome.php (246 lines)
│   │   └── footer.php     (147 lines)
│   ├── cards/
│   │   ├── product.php    (176 lines)
│   │   └── category.php   (47 lines)
│   └── product/
│       ├── gallery.php    (82 lines)
│       └── info.php       (175 lines)
└── assets/fonts/          (10 font files)
```

---

## 8. Remaining Phases

| Phase | Task | Status |
|-------|------|--------|
| 10-14 | Cart, checkout, blog, search, 404 CSS | Pending |
| 15-20 | Customizer round-trip, a11y, performance, regression, report | Pending |

---

## 9. Next Phase

→ [PHASE10_CART_BLOG_SEARCH.md](./PHASE10_CART_BLOG_SEARCH.md)
