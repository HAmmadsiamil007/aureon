# VINETA SHOP VARIANT MATRIX

**Date:** 2026-09-01
**Source:** Vineta HTML Package (themesflat.com)
**Total Shop Variants:** 14

---

## Classification Categories

- **CORE SHOP** — Essential shop listing pattern
- **LAYOUT VARIANT** — Different page layout (sidebar, fullwidth, grid)
- **FILTER VARIANT** — Different filter UI pattern
- **PAGINATION VARIANT** — Different load/pagination behavior
- **CATEGORY VARIANT** — Category/collection listing

---

## CORE SHOP

### Primary Shop Page
| File | Lines | Size | Classification | Key Features |
|------|-------|------|----------------|--------------|
| `shop-default.html` | 4700 | 360K | **CORE SHOP** | Standard shop: product grid, sidebar filters, sorting, pagination, product cards with actions |

---

## LAYOUT VARIANTS

| File | Lines | Size | Classification | Layout Feature |
|------|-------|------|----------------|----------------|
| `shop-fullwidth.html` | 4720 | 361K | LAYOUT VARIANT | Full-width product grid, no sidebar |
| `shop-left-sidebar.html` | 4553 | 360K | LAYOUT VARIANT | Left sidebar with filters |
| `shop-right-sidebar.html` | 4555 | 360K | LAYOUT VARIANT | Right sidebar with filters |
| `shop-grid-3-columns.html` | 4640 | 356K | LAYOUT VARIANT | 3-column product grid |

---

## FILTER VARIANTS

| File | Lines | Size | Classification | Filter Feature |
|------|-------|------|----------------|----------------|
| `shop-filter-sidebar.html` | 4553 | 360K | FILTER VARIANT | Sidebar filter panel |
| `shop-filter-drawer.html` | 4708 | 364K | FILTER VARIANT | Drawer/sliding filter panel |
| `shop-filter-hidden.html` | 4700 | 360K | FILTER VARIANT | Hidden/collapsible filter |
| `shop-horizontal-filter.html` | 4592 | 355K | FILTER VARIANT | Horizontal filter bar above grid |

---

## PAGINATION VARIANTS

| File | Lines | Size | Classification | Pagination Feature |
|------|-------|------|----------------|--------------------|
| `shop-infinity-scroll.html` | 4964 | 381K | PAGINATION VARIANT | Infinite scroll loading |
| `shop-load-more-button.html` | 4963 | 381K | PAGINATION VARIANT | "Load More" button pagination |

---

## CATEGORY / COLLECTION VARIANTS

| File | Lines | Size | Classification | Category Feature |
|------|-------|------|----------------|------------------|
| `shop-collection-list.html` | 3223 | 250K | CATEGORY VARIANT | Collection/category listing grid |
| `shop-sub-collection.html` | 4870 | 372K | CATEGORY VARIANT | Sub-collection with products |
| `shop-sub-collection-02.html` | 4833 | 368K | CATEGORY VARIANT | Sub-collection variant 2 |

---

## SHARED COMPONENTS ACROSS ALL SHOP VARIANTS

All shop pages share:
- **Product Card:** `card-product` with image, title, price, sale badge, actions
- **Product Actions:** Add to cart, wishlist, quick view, compare
- **Sorting:** Sort dropdown (featured, price, name, date)
- **Product Count:** "Showing X of Y products"
- **Header/Navigation:** Same mega menu, search, cart
- **Footer:** Same multi-column footer

---

## FILTER UI CONTRACT

| Filter Type | Implementation | Files |
|-------------|----------------|-------|
| Category filter | Checkbox list | shop-filter-sidebar, shop-filter-drawer |
| Price filter | Range slider (noUiSlider) | shop-filter-sidebar, shop-filter-drawer |
| Color filter | Color swatch checkboxes | shop-filter-sidebar, shop-filter-drawer |
| Size filter | Button/checkbox list | shop-filter-sidebar, shop-filter-drawer |
| Brand filter | Checkbox list | shop-filter-sidebar |
| Rating filter | Star rating | shop-filter-sidebar |
| Availability filter | In stock / Out of stock | shop-filter-sidebar |

---

## PAGINATION CONTRACT

| Method | Implementation | Files |
|--------|----------------|-------|
| Standard pagination | Page numbers | shop-default, shop-fullwidth, shop-left-sidebar, shop-right-sidebar, shop-grid-3-columns, shop-filter-sidebar, shop-filter-drawer, shop-filter-hidden, shop-horizontal-filter |
| Infinite scroll | Auto-load on scroll | shop-infinity-scroll |
| Load more | Button click to load | shop-load-more-button |

---

## SORTING CONTRACT

All shop variants support:
- Featured (default)
- Price: Low to High
- Price: High to Low
- Name: A to Z
- Name: Z to A
- Date: Newest first
- Date: Oldest first

---

## RESPONSIVE BEHAVIOR

| Breakpoint | Layout Change |
|------------|---------------|
| Desktop (1440+) | Full grid + sidebar (where applicable) |
| Tablet (1024) | Reduced columns, collapsible sidebar |
| Mobile (768) | Single column, drawer filter, stacked layout |

---

## RECOMMENDATIONS

1. **Keep ALL 14 shop variants.** They represent genuine UI patterns.
2. **shop-default.html is the PRIMARY** — most standard layout.
3. **Filter variants** (4 files) provide comprehensive filter UI testing.
4. **Pagination variants** (2 files) test different load behaviors.
5. **Layout variants** (4 files) test different grid compositions.
6. **Category variants** (3 files) test collection/category presentation.
