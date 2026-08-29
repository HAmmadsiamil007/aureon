# PHASE 3 — TEMPLATE MAPPING MATRIX

**Date:** 2026-08-21
**Status:** Complete — all 980 source pages mapped to 12 template families

---

## 1. Master Template Matrix

| # | Template Family | Source Pages | WordPress Template | Sections | Data Source |
|---|----------------|-------------|-------------------|----------|-------------|
| 1 | Homepage | 1 | `front-page.php` | category-grid, editorial-split, product-grid, room-grid, newsletter | Customizer repeater + WC |
| 2 | Collection Archive | ~80 unique | `woocommerce/archive-product.php` | page-hero, filter-bar, shop-grid, newsletter | WC taxonomy query |
| 3 | Product Detail | 784 | `woocommerce/single-product.php` | product-gallery, product-info, product-accordion, related-products | WC product data |
| 4 | Editorial/Info Page | ~30 | `page.php` + custom templates | page-hero, content-body | WordPress page content |
| 5 | Room/Landing Page | ~8 | `page.php` + room template | page-hero, room-grid, editorial-split | WordPress pages + WC |
| 6 | Blog Index | 3 | `home.php` | page-hero, blog-grid, pagination | WP_Query posts |
| 7 | Blog Article | 17 | `single.php` | article-hero, article-body, author-bio, related-posts | WP post data |
| 8 | Cart | 1 | `woocommerce/cart.php` | cart-items, cart-summary | WC cart session |
| 9 | Checkout | 1 | `woocommerce/checkout.php` | checkout-form, checkout-summary | WC checkout |
| 10 | Account | 2 | `woocommerce/myaccount.php` | login-form / dashboard | WC customer |
| 11 | Configurator | 13 | **Documented as unsupported** | — | Struct.com (third-party) |
| 12 | Legal/Utility | ~5 | `page.php` | page-hero, content-body | WordPress pages |

---

## 2. Homepage Section Composition

```
front-page.php
├── section: category-grid (D: new homepage category grid)
│   └── components: card/category × 4 (Kitchen, Outdoor Living, Kids, The Living Room)
├── section: editorial-split
│   └── components: content/story (text + image)
├── section: product-grid (bestsellers)
│   └── components: card/product × 4 (with carousel, swatches, badges)
├── section: room-grid
│   └── components: card/category × 5 (The Bedroom, The Office, Green Space, Kids' Room, Classics)
├── section: editorial-split-2
│   └── components: content/story (text + image)
├── section: product-grid-2
│   └── components: card/product × 4
├── section: room-grid-with-links
│   └── components: card/category + links
└── section: newsletter
    └── components: form/newsletter
```

---

## 3. Collection Page Section Composition

```
woocommerce/archive-product.php
├── section: page-hero
│   └── components: hero/page-title (collection name + description)
├── section: filter-bar
│   └── components: section/filter-bar (sort + filter controls)
├── section: shop-grid
│   └── components: card/product × N (4-col grid)
│   └── components: section/pagination
└── section: newsletter
    └── components: form/newsletter
```

---

## 4. Product Page Section Composition

```
woocommerce/single-product.php
├── section: product-gallery
│   └── components: product/gallery (Embla carousel + dots)
├── section: product-info
│   └── components: product/info (title, price, swatches, size, qty, CTA)
│   └── components: product/swatches (color variant links)
├── section: product-accordion
│   └── components: section/accordion (Description, Shipping, Care)
├── section: related-products
│   └── components: card/product × 4 (carousel)
└── (no newsletter on product page per source)
```

---

## 5. Blog Index Section Composition

```
home.php (or archive.php)
├── section: page-hero
│   └── components: hero/page-title ("Stories" / "Suppliers" / "Professionals")
├── section: blog-grid
│   └── components: card/blog × N (3-col grid)
│   └── components: section/pagination
└── section: newsletter
    └── components: form/newsletter
```

---

## 6. Blog Article Section Composition

```
single.php
├── section: article-hero
│   └── components: content/article-hero (featured image)
│   └── components: content/article-meta (author, date, category)
├── section: article-body
│   └── components: content/article-body (post content)
├── section: author-bio
│   └── components: content/author-bio
├── section: related-posts
│   └── components: card/blog × 3
└── section: newsletter
    └── components: form/newsletter
```

---

## 7. Cart Page Section Composition

```
woocommerce/cart.php
├── section: cart-items
│   └── components: cart/items (line items with image, title, variant, qty, price)
├── section: cart-summary
│   └── components: cart/summary (subtotal, checkout CTA)
└── (no newsletter on cart page)
```

---

## 8. Checkout Page Section Composition

```
woocommerce/checkout.php
├── section: checkout-form
│   └── components: checkout/form (billing, shipping fields)
├── section: checkout-summary
│   └── components: checkout/order-items + order-totals
└── (no newsletter on checkout page)
```

---

## 9. Account Page Section Composition

```
woocommerce/myaccount.php
├── Login state:
│   └── components: form/login (email + password + forgot link)
└── Dashboard state:
    └── components: account/profile + account/orders
```

---

## 10. 404 Page Section Composition

```
404.php
├── section: error-hero
│   └── components: hero/page-title ("404")
├── section: error-content
│   └── components: error/404
└── section: newsletter
    └── components: form/newsletter
```

---

## 11. Section → Adapter → ViewModel Flow

| Section | Adapter Function | ViewModel Keys |
|---------|-----------------|----------------|
| `category-grid` | `aether_adapter_wc_categories()` | `items[]` → `{name, url, image, count}` |
| `product-grid` | `aether_adapter_wc_products()` | `items[]` → `{id, name, price, image, url, badge, swatches}` |
| `editorial-split` | `aether_adapter_page_content()` | `{title, text, image, cta_url, cta_label}` |
| `room-grid` | `aether_adapter_wc_categories()` | `items[]` → `{name, url, image}` |
| `filter-bar` | `aether_adapter_wc_filter()` | `{sort_options[], filters[]}` |
| `shop-grid` | `aether_adapter_wc_products()` | `items[]` + `pagination` |
| `product-gallery` | `aether_adapter_product_gallery()` | `images[]`, `thumbnails[]` |
| `product-info` | `aether_adapter_product_single()` | `{title, price, variations[], stock, sku}` |
| `product-accordion` | `aether_adapter_product_single()` | `{description, shipping_info, care_info}` |
| `related-products` | `aether_adapter_wc_products()` | `items[]` (related query) |
| `blog-grid` | `aether_adapter_blog_posts()` | `items[]` → `{title, excerpt, image, date, url}` |
| `article-hero` | `aether_adapter_article()` | `{featured_image, author, date, category, read_time}` |
| `article-body` | `aether_adapter_article()` | `{content}` |
| `cart-items` | `aether_adapter_cart()` | `items[]` → `{image, title, variant, qty, price, total}` |
| `cart-summary` | `aether_adapter_cart()` | `{subtotal, total, checkout_url}` |
| `newsletter` | `aether_adapter_options()` | `{heading, subtitle, placeholder}` |
| `page-hero` | `aether_adapter_hero_page_title()` | `{title, description}` |
| `announcement` | `aether_adapter_announcement()` | `items[]` → `{icon, text, url}` |
| `header` | `aether_adapter_header()` | `{brand, brand_url, menu[], icons}` |
| `footer` | `aether_adapter_footer()` | `{columns[], newsletter, legal[], payments[]}` |

---

## 12. Template Override Strategy

**No new WordPress templates are needed.** All Ferm pages map to existing AETHER/WC templates:

| Template | File | Override Strategy |
|----------|------|------------------|
| Homepage | `front-page.php` | Reuse existing — compose from sections |
| Collection | WC `archive-product.php` | Design pack CSS restyling |
| Product | WC `single-product.php` | Component overrides (gallery, info) |
| Blog | `home.php` / `single.php` | Design pack CSS restyling |
| Cart | WC `cart.php` | Design pack CSS restyling |
| Checkout | WC `checkout.php` | Design pack CSS restyling (minimal) |
| Account | WC `myaccount.php` | Design pack CSS restyling |
| 404 | `404.php` | Design pack CSS restyling |
| Pages | `page.php` | Design pack CSS restyling |
| Search | `search.php` | Design pack CSS restyling |

---

## 13. Page Count Verification

| Source Count | Template Family | WordPress Equivalent |
|-------------|----------------|---------------------|
| 1 | Homepage | 1 front-page.php |
| 113 | Collections | ~80 unique archive-product.php views |
| 784 | Products | 784 single-product.php views |
| 58 | Pages | ~30 unique page.php templates |
| 17 | Blog articles | 17 single.php views |
| 3 | Blog indexes | 3 archive.php views |
| 1 | Cart | 1 cart.php |
| 1 | Checkout | 1 checkout.php |
| 2 | Account | 1 myaccount.php (2 states) |
| 13 | Configurators | **Unsupported** |
| **980** | **Total** | **12 template families** |

---

## 14. Next Phase

→ [PHASE4_DATA_MAPPING.md](./PHASE4_DATA_MAPPING.md)
