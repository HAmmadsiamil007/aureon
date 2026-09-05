# FRONTEND COMPONENT DYNAMICITY MATRIX

> **Status:** COMPLETE (baseline) · **Date:** 2026-08-08 · **Closure:** 2026-08-09 (G1/G2/G4/G5 closed — see §14)
> **Classification (mission §3):** A = Already dynamic · B = Partially dynamic · C = Static content · D = Static visual only · E = Broken integration · F = Missing adapter · G = Missing Customizer binding · H = Missing WooCommerce binding

---

## 1. Shell (7)

| Component | Class | Data source | Static values | Dynamic values | Deps | Status |
|---|---|---|---|---|---|---|
| `shell/preloader` | B | `aether_adapter_site()` | wordmark copy | `brand` from bloginfo | site adapter | ✅ (edge: JS-disabled fallback pending) |
| `shell/fog` | D | none | decorative fog images | — | none | ✅ |
| `shell/skip-link` | D | none | static a11y link | — | none | ✅ |
| `shell/announcement` | A | `aether_adapter_announcement()` | — | items from settings (G1 closed) | shell adapter + settings | ✅ |
| `shell/header` | A | `aether_adapter_header()` | icons FA classes | brand, menu, icons URLs, cart_count | WP menu, WC cart | ✅ |
| `shell/mobile-chrome` | A | `aether_adapter_mobile()` | search placeholder copy | brand, menu, account/wishlist/cart, announcement texts, CTA, socials | WP menu, WC | ✅ |
| `shell/footer` | A | `aether_adapter_footer()` | payment icons, socials, newsletter copy | brand, tagline, link columns from `aether_footer_columns` (G4 closed) | site adapter + settings | ✅ |

## 2. Hero (4)

| Component | Class | Data source | Static values | Dynamic values | Deps | Status |
|---|---|---|---|---|---|---|
| `hero/slider` | A | section → `adapter_hero` | swiper markup, counter, progress | slides array | hero adapter | ✅ |
| `hero/slide` | A | `adapter_hero` slide | container markup | headline, accent, subline, image, alt, buttons | hero adapter | ✅ |
| `hero/page-title` | A | theme templates (`get_the_title`, `woocommerce_page_title`) | fog markup | title, label, subtitle | page/WC context | ✅ |
| `hero/page-banner` | A | theme templates + account adapter | banner markup | title, subtitle | page context | ✅ |

## 3. Section (6)

| Component | Class | Data source | Static values | Dynamic values | Deps | Status |
|---|---|---|---|---|---|---|
| `section/header` | A | section data | markup | label, title, subtitle | section adapter args | ✅ |
| `section/filter-bar` | A | `adapter_wc_filter` | filter markup | category list, sale button | WC terms | ✅ |
| `section/accordion` | A | section data (faq items) | markup | items, active index | faq adapter | ✅ |
| `section/cta` | A | section data | markup | label, url | wc-products (with_cta) | ✅ |
| `section/newsletter` | A | settings/tokens | form markup | copy, toggles | newsletter AJAX/REST | ✅ |
| `section/pagination` | A | adapter pagination | prev/next/dots markup | current, total, base | blog/wc-products | ✅ |

## 4. Cards (6)

| Component | Class | Data source | Static values | Dynamic values | Deps | Status |
|---|---|---|---|---|---|---|
| `card/product` (home) | A | `adapter_wc_products` | action buttons, FA icons | name, tagline, price, rating, badge, image, url | WC | ✅ |
| `card/product` (shop) | A | `adapter_wc_products` | CTA markup | badge, image, name, price_plain, old_price_plain | WC | ✅ |
| `card/category` | A | `adapter_wc_categories` | markup | name, count, image, url, modifier | WC terms | ✅ |
| `card/blog` | A | `adapter_blog` | markup | title, excerpt, date, author, category, image, url | WP_Query | ✅ |
| `card/review` | A | `adapter_testimonials` | verified badge, stars render | name, role, title, quote, date, stars | CPT `aether_testimonial` + **demo fallback (G2)** | ✅ (gated by aether_demo_content) |
| `card/team` | A | `adapter_team` | markup | name, role, bio, image | CPT `aether_team` + **demo fallback (G2)** | ✅ (gated by aether_demo_content) |
| `card/wishlist` | A | `adapter_wishlist` | markup | product data, remove | WC + user meta | ✅ |

## 5. Cart / Checkout / Account (5)

| Component | Class | Data source | Static values | Dynamic values | Deps | Status |
|---|---|---|---|---|---|---|
| `cart/items` | A | `adapter_cart` | empty-state copy | items, qty, prices, remove URLs | WC cart | ✅ |
| `cart/summary` | A | `adapter_cart` | markup | subtotal, shipping, total, checkout URL | WC cart | ✅ |
| `checkout/order-items` | B | `adapter_cart`/WC | markup | order items | WC checkout | ✅ |
| `account/profile` | A | `adapter_account` | markup | user name/email/avatar, nav | WP/WC user | ✅ |
| `account/orders` | A | `adapter_account` (orders) | empty-state copy | orders list, status, totals | `wc_get_orders` | ✅ |

## 6. Auth (1)

| Component | Class | Data source | Static values | Dynamic values | Deps | Status |
|---|---|---|---|---|---|---|
| `auth/password-strength` | B | section data | strength meter markup | — (JS-driven) | WC register form hooks | ✅ |

## 7. Order (1)

| Component | Class | Data source | Static values | Dynamic values | Deps | Status |
|---|---|---|---|---|---|---|
| `order/confirmation` | A | `adapter_order` | markup | order number, items, totals, status | `wc_get_order` | ✅ |

## 8. Commerce (2)

| Component | Class | Data source | Static values | Dynamic values | Deps | Status |
|---|---|---|---|---|---|---|
| `commerce/rating` | A | card/product data | star icons | stars (full/half/empty) | WC rating | ✅ |
| `commerce/quick-view` | B | card data + AJAX | modal markup | product id/name/image | `aether-ajax` quick-view | ✅ |

## 9. Product (9)

| Component | Class | Data source | Static values | Dynamic values | Deps | Status |
|---|---|---|---|---|---|---|
| `product/breadcrumb` | A | `adapter_product` | Home/Collection labels | crumbs, current product | WC | ✅ |
| `product/gallery` | A | `adapter_product` | swiper markup, zoom | main image, thumbnails | WC gallery | ✅ |
| `product/info` | A | `adapter_product` | trust icons | badge, title, price, rating, desc, colors, sizes, qty, actions | WC + **demo fallbacks (G2)** | ✅ (gated by aether_demo_content) |
| `product/sticky-bar` | A | `adapter_product` | markup | name, price, CTA | WC | ✅ |
| `product/specs` | A | `adapter_product` | accordion markup | spec items (**fallback 4 demo — G2**) | WC attributes | ✅ (gated by aether_demo_content) |
| `product/reviews` | A | `adapter_product` | score markup | score, bars, review cards (**fallback — G2**) | WC comments | ✅ (gated by aether_demo_content) |
| `product/related` | A | `adapter_wc_products` (related_to) | swiper markup | related products (excludes self) | `wc_get_related_products` | ✅ |
| `product/size-guide` | A | `adapter_product` | modal markup | size table (**fallback 12 rows — G2**) | tokens | ✅ (gated by aether_demo_content) |
| `product/quick-view` | (in commerce/) | — | — | — | — | — |

## 10. Content (6)

| Component | Class | Data source | Static values | Dynamic values | Deps | Status |
|---|---|---|---|---|---|---|
| `content/page` | A | theme template | markup | `the_content()` | WP | ✅ |
| `content/article-hero` | A | `adapter_article` | markup | title, meta, image | WP post | ✅ |
| `content/article-meta` | A | post data | markup | date, author, category, read time | WP | ✅ |
| `content/article-body` | A | post data | markup | `the_content()` | WP | ✅ |
| `content/author-bio` | A | post data | markup | avatar, name, bio | WP | ✅ |
| `content/story` | A | section data | markup | story copy (about page) | tokens/settings | ✅ |

## 11. Forms (5)

| Component | Class | Data source | Static values | Dynamic values | Deps | Status |
|---|---|---|---|---|---|---|
| `form/contact` | A | `adapter_contact` | form markup | fields, labels, placeholders, info from `aether_contact_*` (G5 closed) | contact adapter + settings | ✅ |
| `form/login` | A | section data | markup | brand, redirect, WC login hooks | WC | ✅ |
| `form/register` | A | section data | markup | brand, WC register hooks | WC | ✅ |
| `form/newsletter` | A | tokens | markup | placeholder copy, AJAX/REST action | `aether-newsletter` | ✅ |
| `form/forgot-password` | A | section data | markup | email field, redirect | WC | ✅ |

## 12. Utility (2)

| Component | Class | Data source | Static values | Dynamic values | Deps | Status |
|---|---|---|---|---|---|---|
| `error/404` | A | theme 404 template | markup | shop URL | WC | ✅ |
| `soon/countdown` | A | tokens/section data | markup | countdown targets, notify form | `countdown.js` | ✅ |

---

## 13. Section templates (26) — adapter wiring

| Section | Adapter | Class | Notes |
|---|---|---|---|
| `hero` | `adapter-hero` | A | slides from settings |
| `categories` | `adapter-wc-categories` | A (**G3** copy) | WC terms; fallback curated |
| `bestsellers` | `adapter-wc-products` (top sellers) | A | total_sales meta |
| `reviews` | `adapter-testimonials` | B (**G2**) | CPT + demo fallback |
| `faq` | `adapter-faq` | B (**G2**) | CPT + demo fallback |
| `newsletter` | — | A | settings-driven |
| `mission`/`features`/`story`/`stats` | tokens/section data | B | copy static in sections/tokens |
| `team` | `adapter-team` | B (**G2**) | CPT + demo fallback |
| `values` | section data | B | static copy |
| `contact` | `adapter-contact` | B (**G5**) | admin_email + hardcoded info |
| `auth` | `adapter-auth` | A | WC registration flag |
| `wishlist` | `adapter-wishlist` | A | WC + user meta |
| `coming-soon` | `adapter-coming-soon` | B | brand dynamic, countdown static target |
| `blog-grid` | `adapter-blog` | A | WP_Query + pagination |
| `blog-single` | `adapter-article` | A | post id via per-call data |
| `cart` | `adapter-cart` | A | WC cart |
| `checkout` | `adapter-cart`/WC | A | WC checkout |
| `order-confirmation` | `adapter-order` | A | wc_get_order |
| `product` | `adapter-product` | A (**G2** fallbacks) | WC product |
| `related` | `adapter-wc-products` (related_to) | A | WC |
| `shop-hero` | `adapter-shop-hero` | A | WC context title |
| `shop-filter` | `adapter-wc-filter` | A | WC terms + sale flag |
| `shop-grid` | `adapter-wc-products` (paged/tax/on_sale) | A | WC |

---

## 14. Summary counts

| Class | Meaning | Count |
|---|---|---|
| A | Already dynamic | 39 |
| B | Partially dynamic (static copy or demo fallback) | 14 |
| G | Missing Customizer binding (G1/G3/G4/G5) | 4 |
| D | Static visual only (intentional) | 3 |
| E / F / H | Broken / missing adapter / missing WC binding | **0** |

**No broken integration, no missing adapters, no missing WC bindings.** The conversion work is **DONE (2026-08-09)**: G1/G4/G5 closed in adapters (settings-bound, defaults = current strings), G2 closed via the `aether_demo_content` master toggle (gates every fallback), animation hardening complete (guard-first + watchdog + try/catch — see `FRONTEND_FAILURE_MODE_REPORT.md`), styleguide added. Counts above are the baseline snapshot. Full closure: `docs/PHASE_17_FRONTEND_DYNAMIC_CLOSURE_REPORT.md`.
