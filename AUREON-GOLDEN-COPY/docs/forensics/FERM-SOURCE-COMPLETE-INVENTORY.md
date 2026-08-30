# Ferm Living Frozen Source — Complete Frontend Inventory

**Source:** `C:\Users\hamma\Downloads\SiteOne-Crawler\fermliving.com`
**Date:** 2026-08-26
**Version:** FERM_REFERENCE_V1 (frozen, do not modify)

---

## 1. Source Overview

| Metric | Value |
|--------|-------|
| HTML pages | 980 |
| CSS files | 3 (main compiled, prettified, fonts) |
| JS files | 5 theme + 8 Shopify core + 6 extensions + 6 third-party |
| Font files | 10 (2 families: Canela + KHTeka) |
| Image files | ~37,448 |
| SVG/icon files | 2 (favicons only) |
| Video files | 37 |
| PDF documents | 150 |

---

## 2. CSS Inventory

### Source Path
`cdn/shop/t/164/assets/`

| File | Classification | Size | Description |
|------|---------------|------|-------------|
| `app.adf0bc36b7.css` | **PRESENTATION** | ~500KB minified | Main compiled CSS (production) |
| `app.prettified.css` | BUILD/REFERENCE | ~12,879 lines | Readable version of main CSS |
| `fonts.fd2d67c5ce.css` | **PRESENTATION** | ~2KB | @font-face declarations |

### CSS Architecture Analysis

**Framework:** Compiled Tailwind CSS (JIT output)
**NOT Bootstrap, NOT custom framework.**

#### Key Structural Elements
- **Grid system:** 12-column grid (`.grid-12`) + Tailwind grid utilities
- **Container:** `.container` max-width 1596px with responsive padding (16px → 56px → 104px)
- **Breakpoints:** 480, 600, 768, 992, 1024, 1440, 1920px
- **Color system:** 22 CSS custom properties in `:root`
- **Font system:** 2 families (`canela` for headings, `teka` for body)
- **Animations:** 13 keyframe definitions

#### Color Palette (CSS Custom Properties)
| Variable | Value | Role |
|----------|-------|------|
| `--color-black` | `#383838` | Primary text (warm charcoal) |
| `--color-cream` | `#fffefa` | Primary background |
| `--color-canvas` | `#f7f5ef` | Product/image backgrounds |
| `--color-cashmere` | `#b7a990` | Accent |
| `--color-light-beige` | `#dcd3cb` | Borders |
| `--color-dark-beige` | `#785c52` | Deep accent |
| `--color-oyster-grey` | `#e3dad1` | Neutral |
| `--color-warm-grey` | `#a79e92` | Secondary text |
| `--color-parchment` | `#ded1bc` | Background accent |
| `--color-coffee` | `#655248` | Deep accent |
| `--color-orange` | `#ca8a55` | CTA accent |
| `--color-cognac` | `#61451d` | Deep accent |
| `--color-burned-yellow` | `#b08651` | Accent |
| `--color-price` | `#545454` | Price text |
| `--color-label` | `#666` | Label text |
| `--color-red` | `red` | Error/sale |
| `--color-green` | `#587664` | Success |

#### Component Classes Present
- `.header--transparent`, `.header__navigation`, `[data-header-*]`
- `.megamenu`, `.megamenu-wrapper`, `.megamenu-overlay`
- `.usp-text`, `.usp-text.animate-in/out`
- `.product`, `.product__wrapper`, `.product-thumb__top`, `.product-thumb-carousel`
- `.single-product__top`, `.single-product_configurator`
- `.add-to-cart`, `.accordions`, `.question`
- `.cart-toast`, `.cart-toast-visible`
- `.collection__row`, `.collection-list`
- `.notify-modal-*`, `.stock-error-modal-*`
- `.upsell-cta`, `.rich-text`, `.rte`
- `.hero-with-cta__overlay`, `.section-title-column-text`
- `.faq-answer`, `.category-overview`
- `.grid-height-transition`, `.media-carousel`, `.slider-with-images`

#### Keyframes
`fade-down`, `fade-out`, `fade-up`, `pulse`, `slide-left`, `slide-right`, `spin`, `slideIn`, `slideOut`, `slideInFade`, `fadeOut`, `slideOutFade`, `fadeIn`

### Classification: COPY
The complete compiled CSS must be copied as-is. Do NOT recreate in separate files.

---

## 3. JS Inventory

### Source Path
`cdn/shop/t/164/assets/`

#### 3a. Theme JS (PRESENTATION)

| File | Classification | Size | Description |
|------|---------------|------|-------------|
| `app.1e7cf79a09.js` | **PRESENTATION** | ~151KB | Main theme bundle (Webpack IIFE) |
| `product.fa97565a5f.js` | **PRESENTATION** | ~10KB | Product page behaviors |
| `customer.5de68fbefc.js` | **PRESENTATION** | ~8KB | Customer account JS |
| `speedblitz.min.95accfb9a4.js` | **PRESENTATION** | ~15KB | Lazy-loading/optimization |
| `cart-page.4c84950b1c.js` | **PRESENTATION** | ~8KB | Cart page behaviors |

#### 3b. Shopify Core JS (EXCLUDE)

| File | Classification |
|------|---------------|
| `cdn/shopifycloud/storefront/assets/storefront/load_feature-*.js` | THIRD-PARTY/EXCLUDE |
| `cdn/shopifycloud/storefront/assets/shopify_pay/storefront-*.js` | THIRD-PARTY/EXCLUDE |
| `cdn/shopifycloud/storefront/assets/shop_events_listener-*.js` | THIRD-PARTY/EXCLUDE |
| `cdn/shopifycloud/storefront/assets/themes_support/option_selection-*.js` | THIRD-PARTY/EXCLUDE |
| `cdn/shopifycloud/storefront/assets/storefront/autosizes-*.js` | THIRD-PARTY/EXCLUDE |
| `cdn/shopifycloud/storefront/assets/storefront/origin_trials-*.js` | THIRD-PARTY/EXCLUDE |
| `cdn/shopifycloud/perf-kit/shopify-perf-kit-*.min.js` | THIRD-PARTY/EXCLUDE |
| `cdn/shopifycloud/shop-js/modules/v2/loader.init-*.esm.js` | THIRD-PARTY/EXCLUDE |

#### 3c. Shopify Extensions (EXCLUDE — replace with WP equivalents)

| File | Classification | Replacement |
|------|---------------|-------------|
| `swym-relay-809/swym-consent-manager.js` | EXCLUDE | WP wishlist plugin |
| `bookeasy-175/bookeasy-widget.js` | EXCLUDE | Not needed |
| `stape-remix-56/widget.js` | EXCLUDE | GTM direct embed |
| `cdn.shopify.com/storefront/standard-actions.js` | EXCLUDE | Remove |
| `checkouts/internal/preloads.*.js` | EXCLUDE | Remove |

#### 3d. Third-Party Vendor (EXCLUDE — embed separately if needed)

| File | Classification | Replacement |
|------|---------------|-------------|
| `klaviyo.e5c296369f.js` | EXCLUDE | Direct Klaviyo embed |
| `ablyft.com/s/82068457.js` | EXCLUDE | Remove or direct embed |
| `506.io/eg/script.js` | EXCLUDE | Remove |
| `flowbox.js` | EXCLUDE | Remove or direct embed |
| `cookiebot.com/.../cd.js` | EXCLUDE | Direct Cookiebot embed |

### JS Architecture Analysis

**Framework:** Vanilla JS with Webpack IIFE module system
**Component mounting:** `[data-component="name"]` attribute-driven
**Event system:** CustomEvent bus on `document`

#### 36 Registered Components
`mainCartSell`, `accordion`, `heroWithCta`, `productThumb`, `colorSelect`, `addToCart`, `uspHeader`, `header`, `megaMenu`, `mobileMenu`, `tooltip`, `collectionTemplate`, `faqAccordion`, `roomleConfigurator`, `contactForm`, `heroFullWidthVideo`, `price`, `stockInfo`, `collectionList`, `collectionAllTemplate`, `collectionFilters`, `customSelect`, `hotspots`, `cartDrawer`, `lighting`, `stockErrorModal`, `comingCollections`, `notifyModal`, `swymWishlistButton`, `swymWishlistPage`, `callbackFn`, `errorFn`, `wallpaperCalculator`, `cartToast`, `productThumbCarousel`, `categoryOverview`

#### Shopify-Coupled APIs (REQUIRE BRIDGE)
| API | Purpose | Bridge Target |
|-----|---------|---------------|
| `{root}/cart/add.js` | Add to cart | WC AJAX add-to-cart |
| `{root}/cart/update.js` | Update cart | WC AJAX cart update |
| `{root}/cart/change.js` | Change quantity | WC AJAX cart change |
| `Shopify.formatMoney()` | Price formatting | `wc_price()` via JS shim |
| `Shopify.routes.root` | Base URL | Window shim |
| `Shopify.currency.active` | Currency code | WC currency setting |
| `shopify:section:load` | Section render event | Custom WP event |
| Section rendering response | `{sections: {"cart-drawer": html}}` | Custom WP bridge |

#### Pure DOM Behaviors (PRESERVE AS-IS)
All non-Shopify behaviors work on DOM structure alone:
- Mega menu hover/focus
- Mobile menu navigation
- Header scroll transparency
- USP bar rotation
- Product card carousel (Embla)
- Image carousel
- Accordion expand/collapse
- Body scroll lock
- Focus trap
- Video play/pause
- Custom select dropdowns
- Tooltips

### Classification: COPY + BRIDGE
Copy all 5 theme JS files. Create a Shopify-to-WooCommerce bridge layer (~150 lines) for cart APIs.

---

## 4. Font Inventory

### Source Path
`cdn/shop/t/164/assets/`

| File | Family | Weight | Style | Format | Classification |
|------|--------|--------|-------|--------|---------------|
| `CanelaText-Regular.51f9fbf2eb.woff2` | Canela | 400 | Normal | WOFF2 | **COPY** |
| `CanelaText-Regular.0352a82bfb.woff` | Canela | 400 | Normal | WOFF | **COPY** |
| `KHTeka-Regular.7cac1a3fff.woff2` | KHTeka | 400 | Normal | WOFF2 | **COPY** |
| `KHTeka-Regular.1d5fa758ef.woff` | KHTeka | 400 | Normal | WOFF | **COPY** |
| `KHTeka-RegularItalic.a9ed0806f7.woff2` | KHTeka | 400 | Italic | WOFF2 | **COPY** |
| `KHTeka-RegularItalic.504103a712.woff` | KHTeka | 400 | Italic | WOFF | **COPY** |
| `KHTeka-Medium.fd755d7762.woff2` | KHTeka | 500 | Normal | WOFF2 | **COPY** |
| `KHTeka-Medium.e6be47c07e.woff` | KHTeka | 500 | Normal | WOFF | **COPY** |
| `KHTeka-MediumItalic.a0ddfe003a.woff2` | KHTeka | 500 | Italic | WOFF2 | **COPY** |
| `KHTeka-MediumItalic.b27637b10e.woff` | KHTeka | 500 | Italic | WOFF | **COPY** |

### Font Licensing Note
Canela and KHTeka are commercial fonts. Use the approved open-source alternatives (Fraunces + Inter) if licensing is not secured. The CSS references internal names `canela` and `teka` — font-face mapping must match.

### Classification: COPY (or substitute with Fraunces/Inter)

---

## 5. Image Asset Inventory

### 5a. Product Images — `cdn/shop/files/`

| Extension | Count | Classification |
|-----------|-------|---------------|
| `.png` | 22,058 | COPY (required subset) |
| `.jpg` | 14,247 | COPY (required subset) |
| `.webp` | 614 | COPY (required subset) |
| **Total** | **36,919** | Do NOT copy all — copy referenced subset |

### 5b. Article/Blog Images — `cdn/shop/articles/`

| Extension | Count | Classification |
|-----------|-------|---------------|
| `.jpg` | 155 | COPY (required subset) |

### 5c. Shopify CDN Files — `_cdn.shopify.com/s/files/`

| Extension | Count | Classification |
|-----------|-------|---------------|
| `.jpg` | 117 | COPY (logos, certifications, blog images) |
| `.pdf` | 18 | COPY (product documents) |
| `.png` | 18 | COPY (logos, certifications) |
| `.webp` | 7 | COPY |
| `.mp4` | 1 | COPY |
| `.gif` | 1 | COPY |
| **Total** | **161** | |

### 5d. Struct.com PIM Assets — `_cdn.assets.struct.com/`

| Extension | Count | Classification |
|-----------|-------|---------------|
| `.pdf` | 132 | COPY (assembly manuals) |
| `.png` | 39 | COPY (CAD drawings) |
| `.mp4` | 32 | COPY (product videos) |
| `.mov` | 1 | COPY |
| **Total** | **204** | |

### 5e. Theme Assets — `cdn/shop/t/164/assets/`

| Type | Files | Classification |
|------|-------|---------------|
| Favicons | 8 files | COPY |
| Certification logos | 5 files | COPY |
| Placeholder | 2 files | COPY |

### Image Strategy
Do NOT copy all 37,448 images. Copy the **referenced subset** based on the 24 demo products + 7 categories + editorial content in the reference data. Product images can be served from WordPress media library or CDN.

### Classification: COPY (referenced subset only)

---

## 6. SVG/Icon Inventory

| File | Classification |
|------|---------------|
| `favicon.svg` | COPY |
| `favicon.d61e8fe0db.svg` | COPY |

No icon sprite sheets. Icons are inline in HTML or CSS.

### Classification: COPY

---

## 7. Video Inventory

| Source | Count | Classification |
|--------|-------|---------------|
| Struct.com PIM | 32 `.mp4` + 1 `.mov` | COPY (product videos) |
| Shopify videos | 4 `.mp4` (HD/SD) | COPY |
| Other | 1 `.mp4` | COPY |
| **Total** | **38** | |

### Classification: COPY (referenced subset)

---

## 8. HTML Page Family Inventory

### Page Families and Counts

| Family | Count | Template Pattern |
|--------|-------|-----------------|
| Homepage | 1 | `index.html` |
| Product (PDP) | 784 | `products/*.html` |
| Collection (CLP) | 113 | `collections/*.html` |
| Blog/Article | 20 | `blogs/*.html` |
| Static pages | 58 | `pages/*.html` |
| Cart | 1 | `cart.html` |
| Checkout | 1 | `checkout.html` |
| Account | 2 | `account.html`, `account/*.html` |
| **Total** | **980** | |

### DOM Structure Pattern
```
<html>
  <head> (CSS, fonts, preloads, Shopify globals)
  <body>
    <div class="shopify-section" id="shopify-section-*-popup"> (popups/modals)
    <div class="shopify-section" id="shopify-section-country-select-pop-up">
    <header data-component="header" data-template="*">
      <div data-header-bar> (USP announcements)
      <div data-header-inner> (logo, nav, icons)
      <div data-mobile-menu> (mobile nav)
    </header>
    <main class="content" id="main-content">
      [PAGE-SPECIFIC CONTENT]
    </main>
    <footer> (footer content)
    <div class="shopify-section" id="shopify-section-cart-drawer"> (cart drawer)
    <script> (Shopify globals, analytics)
  </body>
</html>
```

### Classification: COPY (shell) + BRIDGE (dynamic data injection)

---

## 9. Classification Summary

### COPY (Presentation — preserve as-is)

| Asset Type | Files | Notes |
|-----------|-------|-------|
| Compiled CSS | 1 | `app.prettified.css` (or minified) |
| Fonts CSS | 1 | `fonts.css` |
| Theme JS | 5 | app.js, product.js, customer.js, speedblitz.js, cart-page.js |
| Font files | 10 | Canela + KHTeka (or Fraunces/Inter) |
| Favicons | 8 | All favicon variants |
| Certification logos | 5 | FSC, GOTS, GRS, OCS |
| Placeholder images | 2 | product-placeholder, pixel |
| Product images | ~100 | Referenced subset of 24 demo products |
| Category images | 7 | Category hero images |
| Editorial images | 3 | Blog/sustainability images |
| Hero images | 2 | Homepage hero |
| Room images | 6 | Room category images |
| **Total** | **~145** | |

### BRIDGE (Business/API — replace with WooCommerce)

| Dependency | Bridge Target |
|-----------|---------------|
| `cart/add.js` | WC AJAX add-to-cart |
| `cart/update.js` | WC AJAX cart update |
| `cart/change.js` | WC AJAX cart change |
| `Shopify.formatMoney()` | JS shim using `wc_price()` |
| `Shopify.routes.root` | JS shim (empty string) |
| `Shopify.currency` | JS shim from WC settings |
| `shopify:section:load` | Custom WP event dispatch |
| Section rendering response | Custom WP bridge (return rendered HTML) |
| `data-template` attribute | PHP injection from WordPress |
| Customer accounts | WooCommerce My Account |
| Checkout | WooCommerce checkout |
| Search | WordPress/WooCommerce search |
| Wishlist | WP plugin or custom |
| Newsletter | Direct Klaviyo embed |
| A/B testing | Remove or direct embed |
| Analytics | GA/GTM direct |

### EXCLUDE (Third-party Shopify-only)

| Asset | Reason |
|-------|--------|
| Shopify CDN scripts (8 files) | Shopify-only runtime |
| Shopify extensions (6 files) | Shopify-only |
| Third-party vendor JS (6 files) | Not presentation-critical |
| Shopify preconnect/prefetch hints | CDN-specific |
| Apple Pay/Shop Pay metadata | WC payment gateways |
| PayPal metadata | WC PayPal |
| Trekkie analytics | Replace with GA/GTM |
| `__st` tracking object | Replace with GA/GTM |
| `utf8`/`form_type` hidden fields | Shopify form convention |
| Hreflang multi-store links | WPML/Polylang (later) |
| Checkout preloads | WC handles checkout |

---

## 10. Asset Manifest Template

```json
{
  "source": "FERM_REFERENCE_V1",
  "inventoryDate": "2026-08-26",
  "css": {
    "copy": [
      { "ref": "cdn/shop/t/164/assets/app.prettified.css", "local": "css/ferm-compiled.css", "type": "text/css" },
      { "ref": "cdn/shop/t/164/assets/fonts.fd2d67c5ce.css", "local": "fonts/fonts.css", "type": "text/css" }
    ]
  },
  "js": {
    "copy": [
      { "ref": "cdn/shop/t/164/assets/app.1e7cf79a09.js", "local": "js/app.js", "type": "application/javascript" },
      { "ref": "cdn/shop/t/164/assets/product.fa97565a5f.js", "local": "js/product.js", "type": "application/javascript" },
      { "ref": "cdn/shop/t/164/assets/customer.5de68fbefc.js", "local": "js/customer.js", "type": "application/javascript" },
      { "ref": "cdn/shop/t/164/assets/speedblitz.min.95accfb9a4.js", "local": "js/speedblitz.js", "type": "application/javascript" },
      { "ref": "cdn/shop/t/164/assets/cart-page.4c84950b1c.js", "local": "js/cart-page.js", "type": "application/javascript" }
    ],
    "bridge": [
      { "local": "js/bridge.js", "type": "application/javascript", "description": "Shopify-to-WooCommerce API bridge" }
    ]
  },
  "fonts": {
    "copy": [
      { "ref": "cdn/shop/t/164/assets/CanelaText-Regular.51f9fbf2eb.woff2", "local": "fonts/CanelaText-Regular.woff2" },
      { "ref": "cdn/shop/t/164/assets/CanelaText-Regular.0352a82bfb.woff", "local": "fonts/CanelaText-Regular.woff" },
      { "ref": "cdn/shop/t/164/assets/KHTeka-Regular.7cac1a3fff.woff2", "local": "fonts/KHTeka-Regular.woff2" },
      { "ref": "cdn/shop/t/164/assets/KHTeka-Regular.1d5fa758ef.woff", "local": "fonts/KHTeka-Regular.woff" },
      { "ref": "cdn/shop/t/164/assets/KHTeka-RegularItalic.a9ed0806f7.woff2", "local": "fonts/KHTeka-RegularItalic.woff2" },
      { "ref": "cdn/shop/t/164/assets/KHTeka-RegularItalic.504103a712.woff", "local": "fonts/KHTeka-RegularItalic.woff" },
      { "ref": "cdn/shop/t/164/assets/KHTeka-Medium.fd755d7762.woff2", "local": "fonts/KHTeka-Medium.woff2" },
      { "ref": "cdn/shop/t/164/assets/KHTeka-Medium.e6be47c07e.woff", "local": "fonts/KHTeka-Medium.woff" },
      { "ref": "cdn/shop/t/164/assets/KHTeka-MediumItalic.a0ddfe003a.woff2", "local": "fonts/KHTeka-MediumItalic.woff2" },
      { "ref": "cdn/shop/t/164/assets/KHTeka-MediumItalic.b27637b10e.woff", "local": "fonts/KHTeka-MediumItalic.woff" }
    ]
  },
  "images": {
    "copy": "Referenced subset based on demo data (products.json, categories.json, tokens.php)"
  }
}
```

---

## 11. Key Architectural Facts

1. **CSS IS Tailwind JIT** — The compiled CSS is the complete design system. No separate Tailwind config needed.
2. **JS IS vanilla** — No React, Alpine, or framework. Pure DOM manipulation via `data-component` attributes.
3. **No GSAP/Three.js/Lenis/Swiper** — All motion is CSS keyframes + vanilla JS transitions.
4. **Embla carousel** — Product image carousels use Embla (likely bundled in app.js or loaded separately).
5. **12-column grid** — `.grid-12` with Tailwind utilities for responsive layout.
6. **Warm color palette** — All colors derived from earthy/Scandinavian tones.
7. **Two font families** — Canela (serif/display) + KHTeka (sans/body).
8. **CustomEvent bus** — All JS communication via `document.dispatchEvent(new CustomEvent(...))`.
9. **Section rendering pattern** — Cart operations request rendered HTML back from server.
10. **`data-component` mounting** — JS scans DOM for `[data-component]` and mounts handlers.
