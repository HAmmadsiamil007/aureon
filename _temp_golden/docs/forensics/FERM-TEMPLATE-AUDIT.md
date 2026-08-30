# Ferm Living Source — Forensic Audit

**Date:** 2026-08-26
**Reference:** `C:\Users\hamma\Downloads\SiteOne-Crawler\fermliving.com`
**Status:** IMMUTABLE — FERM_REFERENCE_V1

---

## 1. Page Families

980 crawled pages collapse into ~10 distinct template families:

| Family | Routes | WordPress Equivalent |
|--------|--------|---------------------|
| **Homepage** | `/` | `front-page.php` |
| **Product** | `/products/*` | `single-product.php` |
| **Collection** | `/collections/*` | `archive-product.php` |
| **Blog Listing** | `/journal/*`, `/news/*` | `archive.php`, `home.php` |
| **Blog Article** | `/journal/*-slug`, `/news/*-slug` | `single.php` |
| **About** | `/about`, `/about/*` | `page-about.php` |
| **Contact** | `/contact` | `page-contact.php` |
| **Cart** | `/cart` | `cart.php` |
| **Checkout** | `/checkout` | `checkout/form-checkout.php` |
| **Account** | `/account/*` | `myaccount/my-account.php` |
| **Search** | `/search` | `search.php` |
| **404** | `/*` (fallback) | `404.php` |

---

## 2. HTML DOM Structure

### 2.1 Global Shell

Every page shares this skeleton:

```html
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>{{ page_title }}</title>
  {{ content_for_header }}
  <link rel="stylesheet" href="{{ 'app.css' | asset_url }}">
</head>
<body class="template-{{ template_name }}">
  <!-- Announcement Bar -->
  <div class="announcement-bar" data-component="announcement">
    <div class="announcement-bar__inner">
      <span class="announcement-bar__text">Free shipping on orders over €50</span>
    </div>
  </div>

  <!-- Header -->
  <header class="site-header" data-component="header">
    <div class="site-header__inner">
      <a class="site-header__logo" href="/">
        <img src="{{ 'logo.svg' | asset_url }}" alt="Ferm Living">
      </a>
      <nav class="site-header__nav" data-component="mega-menu">
        <!-- Desktop mega menu -->
      </nav>
      <div class="site-header__actions">
        <button class="site-header__search" data-action="search">Search</button>
        <a class="site-header__account" href="/account">Account</a>
        <a class="site-header__cart" href="/cart" data-component="cart-count">
          Cart (<span class="cart-count">0</span>)
        </a>
      </div>
    </div>
  </header>

  <!-- Mobile Chrome -->
  <div class="mobile-chrome" data-component="mobile-chrome">
    <!-- Mobile navigation drawer -->
  </div>

  <!-- Main Content -->
  <main id="MainContent" role="main">
    {{ content_for_layout }}
  </main>

  <!-- Footer -->
  <footer class="site-footer" data-component="footer">
    <!-- USP row, newsletter, link columns, legal -->
  </footer>

  <script src="{{ 'app.js' | asset_url }}"></script>
  {{ content_for_footer }}
</body>
</html>
```

### 2.2 Homepage Sections

```html
<!-- Hero Split -->
<section class="hero-split" data-section-type="hero">
  <div class="hero-split__panel">
    <img src="..." alt="..." loading="eager">
    <a href="/collections/new" class="hero-split__link">Shop Now</a>
  </div>
  <div class="hero-split__panel">
    <img src="..." alt="..." loading="eager">
    <a href="/collections/rooms" class="hero-split__link">Explore Rooms</a>
  </div>
</section>

<!-- Category Grid -->
<section class="category-grid" data-section-type="categories">
  <div class="category-grid__item">
    <img src="..." alt="Living Room">
    <h3>Living Room</h3>
    <a href="/collections/living-room">Shop</a>
  </div>
  <!-- ... more items -->
</section>

<!-- Product Grid (Bestsellers) -->
<section class="product-grid" data-section-type="products">
  <div class="product-card" data-component="product-card">
    <div class="product-card__media">
      <img src="..." alt="..." loading="lazy">
      <button class="product-card__wishlist" data-action="wishlist">♡</button>
    </div>
    <div class="product-card__info">
      <h3 class="product-card__title">Product Name</h3>
      <span class="product-card__price">€299</span>
      <div class="product-card__swatches">
        <button class="swatch" style="background:#000" data-value="Black"></button>
        <button class="swatch" style="background:#8B4513" data-value="Brown"></button>
      </div>
      <button class="product-card__add-to-cart" data-action="add-to-cart" data-product-id="123">
        Add to Cart
      </button>
    </div>
  </div>
  <!-- ... more items -->
</section>

<!-- Editorial Split -->
<section class="editorial-split" data-section-type="editorial">
  <div class="editorial-split__media">
    <img src="..." alt="...">
  </div>
  <div class="editorial-split__content">
    <h2>Title</h2>
    <p>Description</p>
    <a href="/collections/..." class="btn">Shop Now</a>
  </div>
</section>

<!-- Room Grid -->
<section class="room-grid" data-section-type="rooms">
  <div class="room-grid__item">
    <img src="..." alt="...">
    <h3>Living Room</h3>
    <a href="/collections/living-room">Explore</a>
  </div>
  <!-- ... more items -->
</section>

<!-- Newsletter -->
<section class="newsletter" data-section-type="newsletter">
  <h2>Stay Updated</h2>
  <form class="newsletter__form" data-action="newsletter">
    <input type="email" placeholder="Your email">
    <button type="submit">Subscribe</button>
  </form>
</section>
```

### 2.3 Product Page

```html
<section class="product-page" data-section-type="product">
  <!-- Breadcrumb -->
  <nav class="breadcrumb" data-component="breadcrumb">
    <a href="/">Home</a> / <a href="/collections/...">Collection</a> / <span>Product</span>
  </nav>

  <!-- Gallery -->
  <div class="product-gallery" data-component="product-gallery">
    <div class="product-gallery__main">
      <img src="..." alt="..." id="MainProductImage">
    </div>
    <div class="product-gallery__thumbs">
      <button class="product-gallery__thumb active" data-image="...">...</button>
      <!-- ... more thumbs -->
    </div>
  </div>

  <!-- Product Info -->
  <div class="product-info" data-component="product-info">
    <h1 class="product-info__title">Product Name</h1>
    <div class="product-info__price">
      <span class="product-info__current-price">€299</span>
      <span class="product-info__compare-price" style="display:none">€399</span>
    </div>
    <div class="product-info__description">
      <p>Short description...</p>
    </div>

    <!-- Variant Selector -->
    <div class="product-info__variants" data-component="variant-selector">
      <label>Color</label>
      <div class="variant-options">
        <button class="variant-option active" data-option="1" data-value="Black">Black</button>
        <button class="variant-option" data-option="1" data-value="Brown">Brown</button>
      </div>
      <label>Size</label>
      <div class="variant-options">
        <button class="variant-option" data-option="2" data-value="Small">S</button>
        <button class="variant-option" data-option="2" data-value="Medium">M</button>
        <button class="variant-option" data-option="2" data-value="Large">L</button>
      </div>
    </div>

    <!-- Add to Cart -->
    <form class="product-info__form" data-component="add-to-cart-form" method="post" action="/cart/add">
      <input type="hidden" name="id" value="variant-id">
      <input type="hidden" name="quantity" value="1">
      <button type="submit" class="product-info__add-to-cart" data-action="add-to-cart">
        Add to Cart — €299
      </button>
    </form>

    <!-- Accordion -->
    <div class="product-info__accordion">
      <details>
        <summary>Details</summary>
        <div>Detailed description...</div>
      </details>
      <details>
        <summary>Specifications</summary>
        <table>...</table>
      </details>
      <details>
        <summary>Shipping</summary>
        <div>Shipping info...</div>
      </details>
    </div>
  </div>

  <!-- Related Products -->
  <section class="product-related" data-section-type="related">
    <h2>You May Also Like</h2>
    <div class="product-grid product-grid--related">
      <!-- ... product cards -->
    </div>
  </section>
</section>
```

### 2.4 Collection Page

```html
<section class="collection-page" data-section-type="collection">
  <!-- Collection Hero -->
  <div class="collection-hero">
    <h1>Collection Name</h1>
    <p>Collection description...</p>
  </div>

  <!-- Filter Bar -->
  <div class="filter-bar" data-component="filter-bar">
    <button class="filter-bar__toggle" data-action="toggle-filters">Filters</button>
    <div class="filter-bar__filters">
      <div class="filter-group">
        <label>Category</label>
        <select data-filter="category">
          <option value="">All</option>
          <option value="chairs">Chairs</option>
          <option value="tables">Tables</option>
        </select>
      </div>
      <div class="filter-group">
        <label>Price</label>
        <select data-filter="price">
          <option value="">All</option>
          <option value="0-100">Under €100</option>
          <option value="100-500">€100 - €500</option>
          <option value="500+">Over €500</option>
        </select>
      </div>
      <div class="filter-group">
        <label>Sort</label>
        <select data-sort="manual">
          <option value="manual">Featured</option>
          <option value="price-ascending">Price: Low to High</option>
          <option value="price-descending">Price: High to Low</option>
          <option value="created-descending">Newest</option>
        </select>
      </div>
    </div>
  </div>

  <!-- Product Grid -->
  <div class="collection-products" data-component="collection-grid">
    <!-- ... product cards -->
  </div>

  <!-- Pagination -->
  <div class="collection-pagination" data-component="pagination">
    <a href="?page=1" class="active">1</a>
    <a href="?page=2">2</a>
    <a href="?page=3">3</a>
    <a href="?page=2" class="next">→</a>
  </div>
</section>
```

### 2.5 Cart Page

```html
<section class="cart-page" data-section-type="cart">
  <h1>Your Cart</h1>

  <div class="cart-page__items">
    <div class="cart-item" data-component="cart-item">
      <img src="..." alt="..." class="cart-item__image">
      <div class="cart-item__info">
        <h3>Product Name</h3>
        <p>Color: Black, Size: M</p>
        <div class="cart-item__quantity">
          <button data-action="decrease-qty">−</button>
          <input type="number" value="1" min="1">
          <button data-action="increase-qty">+</button>
        </div>
      </div>
      <div class="cart-item__price">€299</div>
      <button class="cart-item__remove" data-action="remove-item">✕</button>
    </div>
    <!-- ... more items -->
  </div>

  <div class="cart-page__summary">
    <div class="cart-summary">
      <div class="cart-summary__subtotal">
        <span>Subtotal</span>
        <span>€299</span>
      </div>
      <div class="cart-summary__shipping">
        <span>Shipping</span>
        <span>Calculated at checkout</span>
      </div>
      <a href="/checkout" class="cart-summary__checkout">Proceed to Checkout</a>
    </div>
  </div>
</section>
```

---

## 3. CSS Framework

### Tailwind Configuration

- **Version:** Tailwind CSS v4
- **Custom Breakpoints:**
  - `tab_p:` 768px (portrait tablet)
  - `tab_l:` 1024px (landscape tablet)
  - Standard: `sm:` 640px, `md:` 768px, `lg:` 1024px, `xl:` 1280px, `2xl:` 1536px

- **Custom Colors:**
  - `bg-canvas` — main background
  - `bg-cream` — secondary background
  - `text-primary` — primary text
  - `text-secondary` — secondary text
  - Brand colors defined via CSS variables

- **Custom Utilities:**
  - `grid-12` — 12-column grid system
  - Animation classes: `fade-in`, `slide-up`, `scale-in`
  - Transition utilities for hover states

- **Font Variables:**
  - `--font-serif`: CanelaText-Regular
  - `--font-sans`: KHTeka-Regular, KHTeka-Medium

### CSS Architecture

```
app.css
├── Tailwind base/components/utilities
├── Custom properties (CSS variables)
├── Component styles (header, footer, product-card, etc.)
├── Animation keyframes
└── Responsive overrides
```

---

## 4. JavaScript Inventory

### Theme JS Files

| File | Classification | Purpose |
|------|---------------|---------|
| `app.js` | PLATFORM ADAPTER | Main entry: initialization, routing, global behaviors |
| `product.js` | PURE PRESENTATION | Product page: gallery, variants, add-to-cart form |
| `cart-page.js` | SHOPIFY BUSINESS | Cart page: quantity updates, removal, totals |
| `collection.js` | PURE PRESENTATION | Collection page: filtering, sorting, pagination |
| `animations.js` | PURE PRESENTATION | Scroll-triggered animations, transitions |
| `mega-menu.js` | PURE PRESENTATION | Desktop mega menu hover/focus behavior |
| `mobile-nav.js` | PURE PRESENTATION | Mobile navigation drawer |
| `search.js` | SHOPIFY BUSINESS | Search overlay, predictive search API |
| `newsletter.js` | SHOPIFY BUSINESS | Newsletter form submission |
| `wishlist.js` | SHOPIFY BUSINESS | Wishlist toggle (Swym integration) |

### Third-Party Libraries

| Library | Classification | Purpose |
|---------|---------------|---------|
| Embla Carousel | PURE PRESENTATION | Product image carousel, testimonial slider |
| PhotoSwipe | PURE PRESENTATION | Image lightbox for product gallery |
| Fancybox | PURE PRESENTATION | Modal/lightbox for quick view |
| InstantClick | PURE PRESENTATION | PWA-like page transitions |
| Klaviyo | SHOPIFY BUSINESS | Email marketing, forms |
| Cookiebot | THIRD PARTY | Cookie consent |
| ABlyft | THIRD PARTY | A/B testing |
| Trusted Shops | SHOPIFY BUSINESS | Reviews |
| NiceTeam | SHOPIFY BUSINESS | Reviews |
| EasyGift | SHOPIFY BUSINESS | Auto-add gift with purchase |
| Flowbox | SHOPIFY BUSINESS | UGC shoppable gallery |
| Struct | SHOPIFY BUSINESS | Visual search |
| Bookeasy | SHOPIFY BUSINESS | Appointments |

### Shopify API Calls

```javascript
// Cart API
POST /cart/add.js        — Add item to cart
POST /cart/change.js     — Change item quantity
POST /cart/update.js     — Update cart attributes
GET  /cart.js            — Get cart state

// Storefront API
GET  /search/suggest.json — Predictive search

// Checkout
POST /checkout            — Full checkout flow (Shopify-hosted)

// Customer
POST /account/login       — Customer login
POST /account/register    — Customer registration
```

### Core Shopify Object

```javascript
Shopify.shop = "ferm-living.myshopify.com";
Shopify.currency = { active: "EUR", rate: "1.0" };
Shopify.theme = { id: 123456, name: "Ferm Living" };
```

---

## 5. Fonts

| Font | Weights | Format | Source |
|------|---------|--------|--------|
| CanelaText | Regular (400) | woff2, woff | Self-hosted |
| KHTeka | Regular (400), Medium (500) | woff2, woff | Self-hosted |

```css
@font-face {
  font-family: 'CanelaText';
  src: url('../fonts/CanelaText-Regular.woff2') format('woff2'),
       url('../fonts/CanelaText-Regular.woff') format('woff');
  font-weight: 400;
  font-style: normal;
  font-display: swap;
}

@font-face {
  font-family: 'KHTeka';
  src: url('../fonts/KHTeka-Regular.woff2') format('woff2'),
       url('../fonts/KHTeka-Regular.woff') format('woff');
  font-weight: 400;
  font-style: normal;
  font-display: swap;
}

@font-face {
  font-family: 'KHTeka';
  src: url('../fonts/KHTeka-Medium.woff2') format('woff2'),
       url('../fonts/KHTeka-Medium.woff') format('woff');
  font-weight: 500;
  font-style: normal;
  font-display: swap;
}
```

---

## 6. Navigation

### Desktop Mega Menu

Three types observed:

1. **Standard Mega Menu** — Multi-column with images
   ```html
   <div class="mega-menu" data-type="megamenu">
     <div class="mega-menu__column">
       <h4>Category</h4>
       <ul>
         <li><a href="/collections/...">Subcategory</a></li>
       </ul>
     </div>
     <div class="mega-menu__featured">
       <img src="..." alt="...">
       <h4>Featured Collection</h4>
       <a href="/collections/...">Shop Now</a>
     </div>
   </div>
   ```

2. **Two-Column Mega Menu** — Text + image split
   ```html
   <div class="mega-menu" data-type="two_column">
     <div class="mega-menu__links">...</div>
     <div class="mega-menu__image">
       <img src="..." alt="...">
     </div>
   </div>
   ```

3. **Rooms Mega Menu** — Room-based navigation
   ```html
   <div class="mega-menu" data-type="rooms">
     <div class="mega-menu__rooms">
       <a href="/collections/living-room" class="mega-menu__room">
         <img src="..." alt="...">
         <span>Living Room</span>
       </a>
     </div>
   </div>
   ```

### Mobile Navigation

- Slide-in drawer from left
- Accordion submenus
- Image headers for categories
- Close button + overlay

### Language Selector

- Single-store language switcher
- Positioned in header actions area

---

## 7. Responsive Behavior

| Breakpoint | Width | Behavior |
|------------|-------|----------|
| Mobile | < 768px | Single column, hamburger menu, stacked layout |
| Tablet Portrait | 768px - 1023px | 2-column grids, simplified nav |
| Tablet Landscape | 1024px - 1279px | 3-column grids, full nav |
| Desktop | ≥ 1280px | Full layout, mega menus, all features |

### Key Responsive Patterns

- Product grid: 2 cols mobile → 3 cols tablet → 4 cols desktop
- Hero: stacked panels mobile → side-by-side desktop
- Navigation: hamburger mobile → horizontal desktop
- Cart: stacked mobile → side-by-side desktop
- Footer: accordion mobile → columns desktop

---

## 8. Commerce Behavior

### Product Cards

- Image with hover swap (second image)
- Wishlist heart button
- Color swatches
- Quick add-to-cart button
- Badge system (New, Sale, etc.)

### Cart

- Line items with quantity controls
- Variant display (Color, Size)
- Subtotal calculation
- Free shipping threshold message
- Checkout button → Shopify checkout

### Checkout

- Shopify-hosted checkout (not self-hosted)
- Apple Pay, Shop Pay, Google Pay
- Address autocomplete
- Shipping calculator
- Order summary

### Wishlist

- Swym-powered wishlist
- Heart toggle on product cards
- Dedicated wishlist page
- Share functionality

---

## 9. Animations

- **CSS Transitions:** Hover states, focus states, active states
- **CSS Keyframes:** fade-in, slide-up, scale-in
- **Scroll-triggered:** Elements animate in on scroll (IntersectionObserver)
- **Page transitions:** InstantClick PWA-like transitions
- **No GSAP** — CSS-only animation approach
- **No Lenis** — Native scroll behavior

---

## 10. Summary

| Aspect | Detail |
|--------|--------|
| Template families | ~10 distinct templates |
| CSS framework | Tailwind v4, custom breakpoints/colors |
| JS architecture | Theme files + third-party libs |
| Fonts | CanelaText + KHTeka (self-hosted) |
| Carousel | Embla |
| Lightbox | PhotoSwipe |
| Animation | CSS-only (transitions + keyframes) |
| Commerce | Shopify APIs (cart, checkout, customer) |
| Email | Klaviyo |
| Reviews | Trusted Shops + NiceTeam |
| UGC | Flowbox |
| Search | Shopify predictive search API |
| PWA | InstantClick |

**Key insight:** The Ferm frontend uses CSS-only animations (no GSAP/Lenis), Embla for carousels, and relies heavily on Shopify APIs for commerce. The AUREON engine already supports GSAP/Lenis/Swiper for luxury — the Ferm pack will need to use the platform contract JS for shared behaviors while the pack owns its own CSS animations.
