# VINETA PRODUCT VARIANT MATRIX

**Date:** 2026-09-01
**Source:** Vineta HTML Package (themesflat.com)
**Total Product Variants:** 34

---

## Classification Categories

- **CORE PRODUCT LAYOUT** — Essential product presentation pattern
- **OPTIONAL PRODUCT LAYOUT** — Genuine alternate layout worth preserving
- **BUSINESS STATE** — Out-of-stock, pickup availability, affiliate
- **MARKETING STATE** — Countdown, volume discount, buy-together
- **SPECIAL FEATURE** — 3D, video, zoom variants

---

## CORE PRODUCT LAYOUTS

### Primary Product Page
| File | Lines | Size | Classification | Key Features |
|------|-------|------|----------------|--------------|
| `product-detail.html` | 4970 | 385K | **CORE LAYOUT** | Full-featured: gallery, thumbnails, zoom, tabs, accordions, related products, wishlist, compare, add to cart, quantity, variations |

### Grid Layouts
| File | Lines | Size | Classification | Key Features |
|------|-------|------|----------------|--------------|
| `product-grid.html` | 4622 | 357K | OPTIONAL LAYOUT | Grid-style product layout |
| `product-grid-02.html` | 4556 | 352K | OPTIONAL LAYOUT | Grid-style product layout v2 |

### Thumbnail Position Variants
| File | Lines | Size | Classification | Key Features |
|------|-------|------|----------------|--------------|
| `product-bottom-thumbnail.html` | 4568 | 354K | OPTIONAL LAYOUT | Thumbnails below main image |
| `product-right-thumbnail.html` | 4544 | 351K | OPTIONAL LAYOUT | Thumbnails on right side |
| `product-stacked.html` | 4571 | 354K | OPTIONAL LAYOUT | Stacked image layout |

### Description Layout Variants
| File | Lines | Size | Classification | Key Features |
|------|-------|------|----------------|--------------|
| `product-description-accordions.html` | 4545 | 351K | OPTIONAL LAYOUT | Description in accordions |
| `product-description-side-accordions.html` | 4688 | 370K | OPTIONAL LAYOUT | Side accordion description |
| `product-description-tab.html` | 4638 | 360K | OPTIONAL LAYOUT | Tabbed description |
| `product-description-vertical.html` | 4637 | 360K | OPTIONAL LAYOUT | Vertical description layout |
| `product-drawer-sidebar.html` | 4643 | 356K | OPTIONAL LAYOUT | Drawer sidebar for info |

### Style Variants
| File | Lines | Size | Classification | Key Features |
|------|-------|------|----------------|--------------|
| `product-style-01.html` | 4826 | 369K | OPTIONAL LAYOUT | Style variant 01 |
| `product-style-02.html` | 4827 | 368K | OPTIONAL LAYOUT | Style variant 02 |
| `product-style-03.html` | 4841 | 369K | OPTIONAL LAYOUT | Style variant 03 |

---

## ZOOM VARIANTS (Special Feature)

| File | Lines | Size | Classification | Zoom Type |
|------|-------|------|----------------|-----------|
| `product-external-zoom.html` | 4546 | 351K | SPECIAL FEATURE | External zoom window |
| `product-inner-zoom.html` | 4550 | 351K | SPECIAL FEATURE | Inner zoom (lens) |
| `product-inner-circle-zoom.html` | 4546 | 351K | SPECIAL FEATURE | Circular inner zoom |
| `product-open-lightbox.html` | 4534 | 350K | SPECIAL FEATURE | Click-to-open lightbox |
| `product-no-zoom.html` | 4528 | 349K | SPECIAL FEATURE | No zoom (static only) |

**Note:** 5 zoom variants provide comprehensive zoom capability testing. All use PhotoSwipe/Drift libraries.

---

## VARIATION/SWATCH VARIANTS (Optional Layout)

| File | Lines | Size | Classification | Variation UI |
|------|-------|------|----------------|--------------|
| `product-swatch-dropdown.html` | 4672 | 362K | OPTIONAL LAYOUT | Dropdown select for variations |
| `product-swatch-dropdown-color.html` | 4674 | 362K | OPTIONAL LAYOUT | Color swatch dropdown |
| `product-swatch-image.html` | 4678 | 363K | OPTIONAL LAYOUT | Image swatch selector |
| `product-swatch-image-square.html` | 4667 | 361K | OPTIONAL LAYOUT | Square image swatch |

**Note:** 4 swatch variants cover all common WooCommerce variation UI patterns.

---

## BUSINESS STATE VARIANTS

| File | Lines | Size | Classification | Business Feature |
|------|-------|------|----------------|------------------|
| `product-out-of-stock.html` | 4488 | 345K | BUSINESS STATE | Out-of-stock messaging, notify me |
| `product-pickup-available.html` | 4728 | 365K | BUSINESS STATE | Local pickup availability |
| `product-affiliate.html` | 4888 | 377K | BUSINESS STATE | Affiliate external link button |

---

## MARKETING STATE VARIANTS

| File | Lines | Size | Classification | Marketing Feature |
|------|-------|------|----------------|-------------------|
| `product-countdown-timer.html` | 4595 | 358K | MARKETING STATE | Sale countdown timer |
| `product-volume-discount.html` | 4701 | 364K | MARKETING STATE | Volume/tiered pricing |
| `product-volume-discount-thumbnail.html` | 4702 | 364K | MARKETING STATE | Volume discount with thumbnails |
| `product-buyX-getY.html` | 4596 | 356K | MARKETING STATE | Buy X Get Y promotion |
| `product-together.html` | 4660 | 361K | MARKETING STATE | Buy together / bundle |
| `product-group.html` | 4991 | 386K | MARKETING STATE | Product group/bundle |

---

## MEDIA VARIANTS (Special Feature)

| File | Lines | Size | Classification | Media Feature |
|------|-------|------|----------------|---------------|
| `product-video.html` | 4556 | 352K | SPECIAL FEATURE | Video product demo |
| `product-3d.html` | 4506 | 347K | SPECIAL FEATURE | 3D model viewer (Google model-viewer) |

---

## RETAINED CAPABILITIES MATRIX

| Capability | Files | WooCommerce Slot | AUREON Bridge |
|------------|-------|------------------|---------------|
| Gallery + Thumbnails | All | Product images | Media API |
| Image Zoom | 5 variants | Product images | JS zoom library |
| Lightbox | product-open-lightbox | Product images | PhotoSwipe |
| Video | product-video | Product video URL | Video embed |
| 3D Model | product-3d | 3D model URL | model-viewer |
| Variation Dropdown | product-swatch-dropdown* | Variation attributes | WC variations |
| Color Swatch | product-swatch-dropdown-color | Variation attributes | WC variations |
| Image Swatch | product-swatch-image* | Variation attributes | WC variations |
| Out of Stock | product-out-of-stock | Stock status | WC stock |
| Pickup Available | product-pickup-available | Local pickup | WC pickup |
| Affiliate | product-affiliate | External URL | WC external product |
| Countdown | product-countdown-timer | Sale end date | WC sale price |
| Volume Discount | product-volume-discount | Tiered pricing | Custom pricing |
| Buy Together | product-together | Cross-sells | WC cross-sells |
| Product Group | product-group | Grouped product | WC grouped |
| Buy X Get Y | product-buyX-getY | Promotion | WC promotions |
| Tabs | product-detail | Product tabs | WC tabs |
| Accordions | product-description-accordions | Product description | WC description |
| Related Products | product-detail | Related products | WC related |
| Wishlist | product-detail | Wishlist | Bridge required |
| Compare | product-detail | Compare | Bridge required |
| Add to Cart | All | Cart button | WC AJAX cart |
| Quantity | All | Quantity input | WC cart |

---

## RECOMMENDATIONS

1. **Keep ALL 34 product variants.** They represent genuine presentation capability.
2. **product-detail.html is the PRIMARY** — most complete, most features.
3. **Zoom variants** (5 files) provide comprehensive zoom testing surface.
4. **Swatch variants** (4 files) cover all WooCommerce variation UI patterns.
5. **Business states** (3 files) test important ecommerce states.
6. **Marketing states** (6 files) test promotional features.
7. **Media variants** (2 files) test 3D and video capabilities.
