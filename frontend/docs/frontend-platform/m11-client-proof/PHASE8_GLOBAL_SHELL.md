# PHASE 8 — GLOBAL SHELL

**Date:** 2026-08-21
**Status:** Complete

---

## 1. Components Implemented

### 1.1 Header (`components/shell/header.php`) — **C Override**
- **Layout:** Logo left, nav center, icons right (search, wishlist, cart, login)
- **USP Bar:** Rotating announcement carousel (4 items, auto-advance)
- **Mega Menu:** Full-width dropdown with static links + dynamic categories
- **Mobile toggle:** Hamburger button triggers mobile menu overlay
- **Contract:** Keeps `#header`, `.header-icon`, `.cart-count` — platform JS unchanged

### 1.2 Mobile Chrome (`components/shell/mobile-chrome.php`) — **C Override**
- **3-level deep navigation:**
  - Level 1: Top nav items (Shop, Inspiration, Rooms, Professionals)
  - Level 2: Subcategories (Kids, Outdoor, Accessories, etc.) with optional featured image
  - Level 3: Tertiary links (All Kids, Toys, Baby, etc.)
- **Back navigation:** Each level has a back button to return to parent
- **Quick links:** Grid of popular links at level 1
- **Contract:** Keeps `#mobileHeader`, `#mobileHamburger`, `#mobileMenuOverlay` — platform drawer JS unchanged

### 1.3 Footer (`components/shell/footer.php`) — **C Override**
- **USP Row:** 4-column grid (free shipping, sign up, help, fast delivery)
- **Newsletter:** Klaviyo-style form with heading + subtitle
- **Link Columns:** 3 columns (Customer Service, Information, Professionals)
- **Bottom Bar:** Legal links + company info + payment icons
- **Contract:** Keeps `#footer`, `#footerNewsletterForm`, `.footer-legal` — platform newsletter JS unchanged

### 1.4 Product Card (`components/cards/product.php`) — **C Override**
- **Image carousel:** Embla-style carousel with dots navigation
- **Badges:** "New", "Certified" — positioned top-left
- **Wishlist heart:** Bottom-right (mobile) / top-right (desktop)
- **Color swatches:** Rotated 45° circles linking to variant products
- **CTA:** "+ Add to Cart" button
- **Contract:** Keeps `.product-card`, `data-product-id`, `data-product-type` — AJAX cart JS unchanged

### 1.5 Category Card (`components/cards/category.php`) — **C Override**
- **Full-bleed image** with title overlay at bottom-left
- **Hover effect:** Image scale on hover
- **Contract:** Keeps `.category-card`, `data-reveal-item` — tilt/reveal JS unchanged

---

## 2. CSS Architecture (`css/ferm.css`) — 1083 lines

| Section | Lines | Coverage |
|---------|-------|----------|
| 1. Token overrides | 1-50 | Colors, fonts, layout, buttons, cards, radii |
| 2. Global resets | 52-65 | Background, font, heading styles |
| 3. Header | 67-140 | Fixed header, logo, nav, actions, mobile toggle |
| 4. Announcement bar | 142-170 | Rotating USP carousel |
| 5. Buttons | 172-215 | Border style, hover fill, disabled state |
| 6. Product card | 217-320 | Image, badges, wishlist, info, CTA |
| 7. Category card | 322-370 | Full-bleed image, title overlay |
| 8. Section header | 372-390 | CanelaText headings |
| 9. Product grid | 392-410 | 2-col → 4-col responsive |
| 10. Footer | 412-530 | USPs, columns, newsletter, legal, payments |
| 11. Shop grid | 532-555 | Collection product grid |
| 12. Product page | 557-610 | Gallery, info, swatches, add-to-cart |
| 13. Forms | 612-640 | Input styles, focus states |
| 14. Container | 642-665 | Max-width, responsive padding |
| 15. Cart drawer | 667-700 | Slide-out from right, overlay |
| 16. Mega menu | 702-720 | Full-width dropdown |
| 17. Mobile chrome | 722-900 | Header, hamburger, overlay, 3-level panels, back buttons, quick links |
| 18. Search overlay | 902-950 | Full-screen overlay, input |
| 19. Reduced motion | 952-960 | `prefers-reduced-motion` support |

---

## 3. JavaScript (`js/ferm.js`) — 292 lines

| Behavior | Lines | Description |
|----------|-------|-------------|
| Header scroll | 12-45 | Hide on scroll down, show on scroll up |
| USP rotation | 48-75 | Auto-advance announcement items |
| Mega menu hover | 78-130 | Desktop hover to open/close mega panels |
| Mobile menu | 133-280 | 3-level panel navigation with back buttons |

**Degradation:** All behaviors are progressive enhancement. Content remains visible without JS.

---

## 4. Font Assets

| File | Size | Format |
|------|------|--------|
| CanelaText-Regular.woff2 | 43KB | WOFF2 |
| CanelaText-Regular.woff | 43KB | WOFF (fallback) |
| KHTeka-Regular.woff2 | 48KB | WOFF2 |
| KHTeka-Regular.woff | 47KB | WOFF (fallback) |
| KHTeka-RegularItalic.woff2 | 51KB | WOFF2 |
| KHTeka-RegularItalic.woff | 50KB | WOFF (fallback) |
| KHTeka-Medium.woff2 | 48KB | WOFF2 |
| KHTeka-Medium.woff | 47KB | WOFF (fallback) |
| KHTeka-MediumItalic.woff2 | 51KB | WOFF2 |
| KHTeka-MediumItalic.woff | 50KB | WOFF (fallback) |

**Total:** ~480KB (WOFF2 only: ~240KB)

---

## 5. Design Pack Activation

The Ferm Living design pack is activated by setting:
```php
update_option( 'aether_active_design', 'fermliving' );
```

Or via constant:
```php
define( 'AETHER_DESIGN', 'fermliving' );
```

**Resolution flow:**
1. `aether_active_design()` returns `'fermliving'`
2. `aether_active_design_dir()` returns `frontend/designs/fermliving/`
3. `aether_resolve_design_path()` checks pack first, falls back to engine
4. `aether_design_defaults()` loads `tokens.php` from pack
5. Body gets class `design-fermliving`
6. CSS scoped under `.design-fermliving` overrides base styles
7. Component overrides shadow engine templates

---

## 6. Verification Checklist

- [x] manifest.json valid JSON
- [x] tokens.php returns array with 13 keys
- [x] All component overrides follow Lumen pattern (same data contract)
- [x] CSS scoped under `.design-fermliving` (no global pollution)
- [x] JS is progressive enhancement (content visible without JS)
- [x] `prefers-reduced-motion` respected
- [x] Font loading uses `font-display: swap`
- [x] No hardcoded business data in templates
- [x] All outputs escaped (esc_html, esc_attr, esc_url)
- [x] No direct WP/WC calls in component templates

---

## 7. Remaining Overrides (Phase 9-14)

| Component | Class | Status |
|-----------|-------|--------|
| `product/gallery` | C | Pending — Embla carousel for product page |
| `product/info` | C | Pending — title, price, swatches, size, qty, CTA |
| `section/newsletter` | C | Pending — Klaviyo-style form |
| All other sections | A/B | CSS-only via ferm.css (already covered) |

---

## 8. Next Phase

→ [PHASE9_HOMEPAGE.md](./PHASE9_HOMEPAGE.md)
