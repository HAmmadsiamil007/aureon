# VINETA ROUTE MAP

**Date:** 2026-09-01
**Source:** Vineta HTML Package

---

## Route Classification

| Source Route | Page Family | Future WP Route | Future WC Route | Data Required | Bridge | Fallback |
|-------------|-------------|-----------------|-----------------|---------------|--------|----------|
| `index.html` | HOME | `/` | — | Products, categories, Customizer | Customizer + WC | Static homepage |
| `home-*.html` (29) | HOME ALT | `/` (alternate designs) | — | Products, categories, Customizer | Customizer + WC | Static alternate homepages |
| `product-detail.html` | PRODUCT | `/product/{slug}` | `/product/{slug}` | Product data, variations, gallery | WC product | Static product page |
| `product-*.html` (33) | PRODUCT ALT | `/product/{slug}` (alternate layouts) | `/product/{slug}` | Product data, variations | WC product | Static product layouts |
| `shop-default.html` | SHOP | `/shop` | `/shop` | Products, filters, sorting | WC product loop | Static shop page |
| `shop-*.html` (13) | SHOP ALT | `/shop` (alternate layouts) | `/shop` | Products, filters | WC product loop | Static shop layouts |
| `shop-collection-list.html` | CATEGORY | `/product-category/{slug}` | `/product-category/{slug}` | Categories, products | WC category | Static collection list |
| `shop-sub-collection*.html` | SUB-CATEGORY | `/product-category/{parent}/{child}` | `/product-category/{parent}/{child}` | Subcategories, products | WC category | Static sub-collection |
| `blog-grid-01.html` | BLOG | `/blog` | — | Posts | WP_Query | Static blog grid |
| `blog-grid-02.html` | BLOG ALT | `/blog` (alternate) | — | Posts | WP_Query | Static blog grid |
| `blog-list-01.html` | BLOG ALT | `/blog` (list view) | — | Posts | WP_Query | Static blog list |
| `blog-list-02.html` | BLOG ALT | `/blog` (list view alt) | — | Posts | WP_Query | Static blog list |
| `blog-single.html` | ARTICLE | `/blog/{slug}` | — | Post content, meta | WP post | Static blog post |
| `account-page.html` | ACCOUNT | `/my-account` | `/my-account` | Customer data | WC customer | Static account page |
| `account-addresses.html` | ACCOUNT | `/my-account/edit-address` | `/my-account/edit-address` | Addresses | WC customer | Static addresses |
| `account-details.html` | ACCOUNT | `/my-account/edit-account` | `/my-account/edit-account` | Account details | WC customer | Static account details |
| `account-orders.html` | ACCOUNT | `/my-account/orders` | `/my-account/orders` | Orders | WC orders | Static orders |
| `cart-drawer-v2.html` | CART DRAWER | (overlay on any page) | (overlay on any page) | Cart contents | WC cart AJAX | Static cart drawer |
| `cart-empty.html` | CART | `/cart` (empty state) | `/cart` | Empty cart | WC cart | Static empty cart |
| `view-cart.html` | CART | `/cart` | `/cart` | Cart contents | WC cart | Static cart page |
| `checkout.html` | CHECKOUT | `/checkout` | `/checkout` | Cart, customer, payment | WC checkout | Static checkout |
| `thank-you.html` | ORDER SUCCESS | `/order-received/{id}` | `/order-received/{id}` | Order data | WC order | Static thank you |
| `wish-list.html` | WISHLIST | `/wishlist` | — | Wishlist items | Bridge required | Static wishlist |
| `compare.html` | COMPARE | `/compare` | — | Compare items | Bridge required | Static compare |
| `about-us.html` | STATIC | `/about-us` | — | Page content | WP page | Static about page |
| `contact-us.html` | STATIC | `/contact-us` | — | Page content, form | WP page + form | Static contact page |
| `faq.html` | STATIC | `/faq` | — | Page content | WP page | Static FAQ |
| `shipping.html` | STATIC | `/shipping` | — | Page content | WP page | Static shipping info |
| `return-and-refund.html` | STATIC | `/returns` | — | Page content | WP page | Static returns |
| `privacy-policy.html` | STATIC | `/privacy-policy` | — | Page content | WP page | Static privacy |
| `term-and-condition.html` | STATIC | `/terms` | — | Page content | WP page | Static terms |
| `store-location.html` | STATIC | `/store-location` | — | Page content, map | WP page | Static store location |
| `cookies.html` | STATIC | `/cookie-policy` | — | Page content | WP page | Static cookies |
| `404.html` | ERROR | WordPress 404 template | — | None | WP template | Static 404 |
| `coming-soon.html` | SPECIAL | WordPress maintenance | — | None | WP maintenance | Static coming soon |
| `before-you-leave.html` | POPUP | (JS trigger on any page) | — | None | JS trigger | Static popup |
| `newsletter-popup-02.html` | POPUP | (JS trigger on any page) | — | Newsletter form | WC newsletter | Static popup |
| `newsletter-popup-03.html` | POPUP | (JS trigger on any page) | — | Newsletter form | WC newsletter | Static popup |

---

## ROUTE SUMMARY

| Route Type | Count | WP Integration |
|------------|-------|----------------|
| Homepage variants | 30 | Customizer + WC products |
| Product pages | 34 | WC product data |
| Shop pages | 14 | WC product query |
| Blog pages | 5 | WP posts |
| Account pages | 4 | WC customer |
| Cart pages | 3 | WC cart |
| Checkout/thank you | 2 | WC checkout/orders |
| Wishlist/compare | 2 | Bridge required (plugin) |
| Static pages | 9 | WP pages |
| Special/utility | 5 | WP templates |
| **TOTAL** | **108** | |

---

## PERMALINK STRUCTURE

```
/                                    → index.html (homepage)
/product/{slug}/                     → product-detail.html
/product-category/{slug}/            → shop-collection-list.html
/product-category/{parent}/{child}/  → shop-sub-collection.html
/shop/                               → shop-default.html
/blog/                               → blog-grid-01.html
/blog/{slug}/                        → blog-single.html
/my-account/                         → account-page.html
/my-account/edit-address/            → account-addresses.html
/my-account/edit-account/            → account-details.html
/my-account/orders/                  → account-orders.html
/cart/                               → view-cart.html
/checkout/                           → checkout.html
/order-received/{id}/                → thank-you.html
/wishlist/                           → wish-list.html
/compare/                            → compare.html
/about-us/                           → about-us.html
/contact-us/                         → contact-us.html
/faq/                                → faq.html
/shipping/                           → shipping.html
/returns/                            → return-and-refund.html
/privacy-policy/                     → privacy-policy.html
/terms/                              → term-and-condition.html
/store-location/                     → store-location.html
/cookie-policy/                      → cookies.html
```
