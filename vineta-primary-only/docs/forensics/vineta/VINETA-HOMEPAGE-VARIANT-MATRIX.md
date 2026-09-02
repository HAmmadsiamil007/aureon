# VINETA HOMEPAGE VARIANT MATRIX

**Date:** 2026-09-01
**Source:** Vineta HTML Package (themesflat.com)
**Total Homepage Variants:** 30

---

## Classification Strategy

Each homepage is classified into:
- **PRIMARY** — The main reference homepage for the client template
- **GENUINE ALTERNATE** — Visually distinct design family worth preserving as optional variant
- **REDUNDANT** — Nearly identical to another variant; safe to archive
- **OPTIONAL REFERENCE** — Contains unique sections worth noting but not production-critical

---

## VISUAL FAMILIES

### Family 1: Fashion/Default (`slider-fashion-1`)
Classic fashion ecommerce layout with full-width hero slideshow.

| File | Lines | Size | Status | Notes |
|------|-------|------|--------|-------|
| `index.html` | 4816 | 361K | **PRIMARY** | Default homepage, most complete sections |
| `home-fashion-women.html` | 5290 | 407K | GENUINE ALTERNATE | Fashion-3 slider, absolute header variant, women-focused |
| `home-fashion-02.html` | 5212 | 390K | GENUINE ALTERNATE | Fashion-2 slider, 3-slide preview, different composition |

### Family 2: Electronics (`slider-electronic`)
Tech-focused layout with product-heavy grid sections.

| File | Lines | Size | Status | Notes |
|------|-------|------|--------|-------|
| `home-electronic.html` | 4393 | 325K | GENUINE ALTERNATE | Standard electronic homepage |
| `home-mega-electronic.html` | 12276 | 996K | GENUINE ALTERNATE | Mega electronic — largest file, extensive mega-menu, extra product sections |
| `home-electric-accessories.html` | 6368 | 492K | GENUINE ALTERNATE | Electric accessories variant |

### Family 3: Furniture/Interior (`slider-viewport`, `slider-furniture*`)
Interior design aesthetic with viewport-based hero.

| File | Lines | Size | Status | Notes |
|------|-------|------|--------|-------|
| `home-furniture.html` | 3826 | 289K | GENUINE ALTERNATE | Furniture style-2 slider |
| `home-furniture2.html` | 3789 | 284K | REDUNDANT | Near-identical to furniture.html |
| `home-ergonic-chair.html` | 3506 | 206K | GENUINE ALTERNATE | Ergonomic chair — minimal, space1 slider |
| `home-florist.html` | 3804 | 218K | GENUINE ALTERNATE | Florist — viewport, space1 slider |

### Family 4: Jewelry/Luxury (`slider-jewelry`, `slider-viewport`)
Luxury aesthetic with viewport-based hero.

| File | Lines | Size | Status | Notes |
|------|-------|------|--------|-------|
| `home-jewelry.html` | 4237 | 322K | GENUINE ALTERNATE | Jewelry viewport slider |
| `home-jewelry2.html` | 3719 | 291K | REDUNDANT | Near-identical to jewelry.html |
| `home-watch.html` | 4048 | 235K | GENUINE ALTERNATE | Watch — space2 viewport slider |

### Family 5: Sports/Outdoor (`slider-sport*`, `slider-pickleball`)
Active/sports aesthetic.

| File | Lines | Size | Status | Notes |
|------|-------|------|--------|-------|
| `home-sportwear.html` | 6063 | 487K | GENUINE ALTERNATE | Sportwear — large, many sections |
| `home-pickleball.html` | 4108 | 316K | GENUINE ALTERNATE | Pickleball — style-2 slider |
| `home-bicycle.html` | 4268 | 321K | GENUINE ALTERNATE | Bicycle — viewport slider |

### Family 6: Beauty/Skincare (`slider-skincare*`)
Beauty-focused aesthetic.

| File | Lines | Size | Status | Notes |
|------|-------|------|--------|-------|
| `home-skincare.html` | 4538 | 343K | GENUINE ALTERNATE | Skincare viewport slider |
| `home-skincare2.html` | 3390 | 260K | REDUNDANT | Near-identical to skincare.html |
| `home-supplement.html` | 3634 | 273K | GENUINE ALTERNATE | Supplement — different slider |

### Family 7: Lifestyle/Niche
Unique niche-specific layouts.

| File | Lines | Size | Status | Notes |
|------|-------|------|--------|-------|
| `home-baby.html` | 4103 | 301K | GENUINE ALTERNATE | Baby products — baby slider |
| `home-book.html` | 4148 | 233K | GENUINE ALTERNATE | Book — space3 slider, control-img |
| `home-glasses.html` | 3371 | 254K | GENUINE ALTERNATE | Glasses — style-2 viewport |
| `home-handcraft.html` | 4362 | 332K | GENUINE ALTERNATE | Handcraft — unique sections |
| `home-pet-accessories.html` | 4492 | 332K | GENUINE ALTERNATE | Pet — pet slider |
| `home-phonecase.html` | 5790 | 439K | GENUINE ALTERNATE | Phonecase — large, many sections |
| `home-plant.html` | 3922 | 303K | GENUINE ALTERNATE | Plant — plant slider |
| `home-pod.html` | 4584 | 343K | GENUINE ALTERNATE | Pod — pod slider |
| `home-footwear.html` | 3632 | 268K | GENUINE ALTERNATE | Footwear — unique layout |
| `home-travel.html` | 3166 | 180K | GENUINE ALTERNATE | Travel — space2 viewport, smallest homepage |
| `home-vegetable.html` | 5881 | 453K | GENUINE ALTERNATE | Vegetable — large, many sections |

---

## RECOMMENDED ACTIONS

### Step 1: Choose Primary Client Homepage
**Recommendation:** `index.html` as PRIMARY (default fashion-1 layout, most complete).

### Step 2: Archive Redundant Variants
These are near-duplicates and can be archived:
- `home-furniture2.html` → duplicate of `home-furniture.html`
- `home-jewelry2.html` → duplicate of `home-jewelry.html`
- `home-skincare2.html` → duplicate of `home-skincare.html`

### Step 3: Retain as Optional Reference
All other 26 variants represent genuine visual capability. They should be:
- Preserved as reference templates
- Available for future client customization
- NOT deleted — they represent Vineta's design breadth

### Step 4: Shared Sections Across All Homepages
All 30 homepages share:
- **Header:** Top bar + main nav + mega menu + search + cart + wishlist + compare
- **Footer:** Multi-column footer + newsletter + social + payment icons
- **Mega Menu:** `mega-home` and `mega-shop` submenus
- **Product Cards:** `card-product` with add-to-cart, wishlist, quick view, compare
- **Newsletter Popup:** Triggered on exit intent
- **Back to Top:** Scroll-to-top button
- **RTL Toggle:** Right-to-left language support

### Step 5: Unique Sections Per Family
Each family has unique hero compositions, section layouts, and product grid arrangements that represent genuine design capability.

---

## SUMMARY

| Category | Count | Action |
|----------|-------|--------|
| PRIMARY | 1 | `index.html` — production default |
| GENUINE ALTERNATE | 26 | Preserve as optional variants |
| REDUNDANT | 3 | Archive (furniture2, jewelry2, skincare2) |
| **TOTAL** | **30** | |
