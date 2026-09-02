# VINETA → AUREON CONNECTION CONTRACT

**Date:** 2026-09-01
**Source:** Vineta HTML Package
**Platform:** Golden AUREON (WordPress + WooCommerce)
**Status:** Bridge contract definition — ready for implementation

---

## Purpose

This document defines every dynamic slot in the Vineta frontend that must connect to Golden AUREON data/actions. Each slot specifies:
- **DOM TARGET** — The HTML element/class to populate
- **DATA** — What data flows through
- **AUREON SOURCE** — Where the data comes from
- **BRIDGE** — How it connects
- **FALLBACK** — What shows when data is unavailable
- **TEST** — How to verify it works

---

## GLOBAL SLOTS

### Logo
| Property | Value |
|----------|-------|
| DOM TARGET | `.logo img` (header), `.footer-logo img` (footer) |
| DATA | Logo image URL, alt text |
| AUREON SOURCE | WordPress Customizer → Site Identity → Logo |
| BRIDGE | `aether_customizer_value('bloglogo')` |
| FALLBACK | Default Vineta logo (`images/logo/logo.svg`) |
| TEST | Customizer logo change reflects on all pages |

### Site Name
| Property | Value |
|----------|-------|
| DOM TARGET | `.logo` text fallback, `<title>`, meta tags |
| DATA | Site name string |
| AUREON SOURCE | WordPress Customizer → Site Identity → Site Title |
| BRIDGE | `aether_customizer_value('blogname')` |
| FALLBACK | "Vineta" (default) |
| TEST | Customizer site title change reflects |

### Announcement Bar
| Property | Value |
|----------|-------|
| DOM TARGET | `.tf-topbar .marquee-child-item p` |
| DATA | Announcement text lines |
| AUREON SOURCE | WordPress Customizer → Announcement section |
| BRIDGE | `aether_customizer_value('announcement_text')` |
| FALLBACK | "Return extended to 60 days • Life-time Guarantees" |
| TEST | Customizer announcement change reflects |

### Navigation Menu
| Property | Value |
|----------|-------|
| DOM TARGET | `#menu-main-menu`, `.box-nav-menu` |
| DATA | Menu items with children |
| AUREON SOURCE | WordPress → Appearance → Menus → Primary menu |
| BRIDGE | `wp_nav_menu(['theme_location' => 'primary'])` |
| FALLBACK | Default Vineta menu structure |
| TEST | WordPress menu changes reflect in header |

### Mega Menu
| Property | Value |
|----------|-------|
| DOM TARGET | `.sub-menu.mega-menu.mega-home`, `.mega-menu.mega-shop` |
| DATA | Mega menu content with images, categories |
| AUREON SOURCE | WordPress mega menu plugin or custom walker |
| BRIDGE | Custom walker class for mega menu |
| FALLBACK | Default Vineta mega menu structure |
| TEST | Mega menu items display correctly |

---

## PRODUCT SLOTS

### Product Card (Shop/Grid)
| Property | Value |
|----------|-------|
| DOM TARGET | `.card-product` |
| DATA | Product image, title, price, sale price, badge, URL |
| AUREON SOURCE | WooCommerce → WC_Product object |
| BRIDGE | `aether_product_card_data($product)` |
| FALLBACK | Static product cards from Vineta source |
| TEST | Real WC products display in shop grid |

### Product Image
| Property | Value |
|----------|-------|
| DOM TARGET | `.card-product-wrapper .product-img img`, `.product-gallery img` |
| DATA | Image URL, srcset, alt text |
| AUREON SOURCE | WooCommerce → `wp_get_attachment_image_url()` |
| BRIDGE | `aether_product_image($product, $size)` |
| FALLBACK | Default Vineta product images |
| TEST | Product images load correctly |

### Product Title
| Property | Value |
|----------|-------|
| DOM TARGET | `.card-product-info a.product-title`, `.product-title` |
| DATA | Product name, permalink |
| AUREON SOURCE | WooCommerce → `get_the_title()`, `get_permalink()` |
| BRIDGE | `aether_product_title($product)` |
| FALLBACK | Static product titles |
| TEST | Product titles display and link correctly |

### Product Price
| Property | Value |
|----------|-------|
| DOM TARGET | `.card-product-info .price`, `.product-price` |
| DATA | Regular price, sale price, price range |
| AUREON SOURCE | WooCommerce → `wc_get_price_html()` |
| BRIDGE | `aether_product_price($product)` |
| FALLBACK | Static prices |
| TEST | Prices display correctly for simple/variable/sale products |

### Product Badge
| Property | Value |
|----------|-------|
| DOM TARGET | `.card-product-wrapper .badge` |
| DATA | Badge text (Sale, New, Hot, etc.) |
| AUREON SOURCE | WooCommerce → sale status, custom meta |
| BRIDGE | `aether_product_badge($product)` |
| FALLBACK | No badge |
| TEST | Sale/new badges display correctly |

### Product Actions (Add to Cart, Wishlist, Quick View, Compare)
| Property | Value |
|----------|-------|
| DOM TARGET | `.list-product-btn` |
| DATA | Add to cart URL, wishlist URL, quick view data, compare URL |
| AUREON SOURCE | WooCommerce → AJAX add-to-cart, wishlist plugin, compare plugin |
| BRIDGE | `aether_product_actions($product)` |
| FALLBACK | Static action buttons |
| TEST | Add to cart works via AJAX |

---

## VARIABLE PRODUCT SLOTS

### Variation Selector
| Property | Value |
|----------|-------|
| DOM TARGET | `.variant-picker`, `.swatch` elements |
| DATA | Attribute names, values, images, prices |
| AUREON SOURCE | WooCommerce → `WC_Product_Variable->get_children()` |
| BRIDGE | `aether_variation_data($product)` |
| FALLBACK | Static variation selectors |
| TEST | Selecting a variation updates price, image, stock |

### Variation Image
| Property | Value |
|----------|-------|
| DOM TARGET | Product gallery main image |
| DATA | Variation-specific image URL |
| AUREON SOURCE | WooCommerce → variation image |
| BRIDGE | `aether_variation_image($variation)` |
| FALLBACK | Main product image |
| TEST | Selecting a color/size updates the gallery image |

### Variation Price
| Property | Value |
|----------|-------|
| DOM TARGET | `.product-price` (dynamic update) |
| DATA | Variation-specific price |
| AUREON SOURCE | WooCommerce → variation price |
| BRIDGE | `aether_variation_price($variation)` |
| FALLBACK | Variable product price range |
| TEST | Selecting a variation updates the price |

---

## SHOP SLOTS

### Product Grid
| Property | Value |
|----------|-------|
| DOM TARGET | `.grid-product`, `.tf-product` |
| DATA | Array of product card data |
| AUREON SOURCE | WooCommerce → WC_Product_Query |
| BRIDGE | `aether_product_loop($args)` |
| FALLBACK | Static product grid |
| TEST | Real products display in shop grid |

### Filter Sidebar
| Property | Value |
|----------|-------|
| DOM TARGET | `.sidebar-filter`, `.filter-type-*` |
| DATA | Filter options (categories, prices, colors, sizes, brands) |
| AUREON SOURCE | WooCommerce → product attributes, categories |
| BRIDGE | `aether_filter_data($taxonomy)` |
| FALLBACK | Static filter options |
| TEST | Filters reflect actual product data |

### Sorting
| Property | Value |
|----------|-------|
| DOM TARGET | `.select-item select` (sort dropdown) |
| DATA | Sort options and current selection |
| AUREON SOURCE | WooCommerce → `WC_Query->get_catalog_ordering_args()` |
| BRIDGE | `aether_sorting_options()` |
| FALLBACK | Default sort options |
| TEST | Sorting works correctly |

### Pagination
| Property | Value |
|----------|-------|
| DOM TARGET | `.pagination`, infinite scroll trigger |
| DATA | Page numbers, current page, total pages |
| AUREON SOURCE | WooCommerce → `wc_get_loop_prop('total_pages')` |
| BRIDGE | `aether_pagination()` |
| FALLBACK | Static pagination |
| TEST | Pagination navigates correctly |

---

## CATEGORY / COLLECTION SLOTS

### Category Hero
| Property | Value |
|----------|-------|
| DOM TARGET | `.page-hero`, `.breadcrumb` |
| DATA | Category name, description, image |
| AUREON SOURCE | WooCommerce → `get_queried_object()` |
| BRIDGE | `aether_category_hero($category)` |
| FALLBACK | Static hero |
| TEST | Category pages show correct hero |

### Subcategory Grid
| Property | Value |
|----------|-------|
| DOM TARGET | `.collection-item`, `.category-card` |
| DATA | Subcategory name, image, product count |
| AUREON SOURCE | WooCommerce → `get_terms()` |
| BRIDGE | `aether_subcategories($parent)` |
| FALLBACK | Static subcategory cards |
| TEST | Subcategories display correctly |

---

## SEARCH SLOTS

### Search Trigger
| Property | Value |
|----------|-------|
| DOM TARGET | `.search-icon`, `.form-search` |
| DATA | Search URL |
| AUREON SOURCE | WordPress → `home_url('/?s=')` |
| BRIDGE | `aether_search_url()` |
| FALLBACK | `/?s=` |
| TEST | Search trigger opens search UI |

### Search Results
| Property | Value |
|----------|-------|
| DOM TARGET | `.search-results`, product cards in results |
| DATA | Search query, matching products |
| AUREON SOURCE | WordPress → `WP_Query` with `s` parameter |
| BRIDGE | `aether_search_results($query)` |
| FALLBACK | "No results found" message |
| TEST | Search returns relevant products |

---

## AUTHENTICATION SLOTS

### Login Form
| Property | Value |
|----------|-------|
| DOM TARGET | `#login-form`, `.form-login` |
| DATA | Login URL, nonce, redirect URL |
| AUREON SOURCE | WordPress → `wp_login_url()` |
| BRIDGE | `aether_login_form()` |
| FALLBACK | Static login form |
| TEST | Login works with WordPress credentials |

### Registration Form
| Property | Value |
|----------|-------|
| DOM TARGET | `#register-form`, `.form-register` |
| DATA | Registration URL, nonce |
| AUREON SOURCE | WordPress → `wp_registration_url()` |
| BRIDGE | `aether_registration_form()` |
| FALLBACK | Static registration form |
| TEST | Registration creates WordPress user |

---

## ACCOUNT SLOTS

### Account Dashboard
| Property | Value |
|----------|-------|
| DOM TARGET | `.account-content`, `.account-sidebar` |
| DATA | Customer name, email, order count |
| AUREON SOURCE | WooCommerce → `WC_Customer` object |
| BRIDGE | `aether_account_dashboard($customer)` |
| FALLBACK | Static account page |
| TEST | Dashboard shows real customer data |

### Account Orders
| Property | Value |
|----------|-------|
| DOM TARGET | `.order-table`, `.order-item` |
| DATA | Order list with status, date, total |
| AUREON SOURCE | WooCommerce → `wc_get_orders()` |
| BRIDGE | `aether_account_orders($customer_id)` |
| FALLBACK | Static order list |
| TEST | Orders display correctly |

### Account Addresses
| Property | Value |
|----------|-------|
| DOM TARGET | `.address-card`, `.address-form` |
| DATA | Billing/shipping addresses |
| AUREON SOURCE | WooCommerce → `WC_Customer->get_address()` |
| BRIDGE | `aether_account_addresses($customer_id)` |
| FALLBACK | Static address forms |
| TEST | Addresses display and save correctly |

---

## CART SLOTS

### Cart Count
| Property | Value |
|----------|-------|
| DOM TARGET | `.count-cart`, `.cart-count` |
| DATA | Number of items in cart |
| AUREON_SOURCE | WooCommerce → `WC()->cart->get_cart_contents_count()` |
| BRIDGE | `aether_cart_count()` |
| FALLBACK | "0" |
| TEST | Cart count updates after adding item |

### Cart Contents
| Property | Value |
|----------|-------|
| DOM TARGET | `.cart-item`, `.shopping-cart` |
| DATA | Cart items with image, name, price, quantity, subtotal |
| AUREON SOURCE | WooCommerce → `WC()->cart->get_cart()` |
| BRIDGE | `aether_cart_contents()` |
| FALLBACK | Empty cart message |
| TEST | Cart shows correct items and totals |

### Mini Cart / Cart Drawer
| Property | Value |
|----------|-------|
| DOM TARGET | `#shoppingCart`, `.cart-drawer` |
| DATA | Cart contents (abbreviated) |
| AUREON SOURCE | WooCommerce → AJAX cart fragments |
| BRIDGE | `aether_mini_cart()` |
| FALLBACK | Empty cart drawer |
| TEST | Cart drawer opens and shows items |

### Cart Totals
| Property | Value |
|----------|-------|
| DOM TARGET | `.cart-total`, `.order-total` |
| DATA | Subtotal, shipping, tax, discount, total |
| AUREON SOURCE | WooCommerce → `WC()->cart->get_totals()` |
| BRIDGE | `aether_cart_totals()` |
| FALLBACK | Static totals |
| TEST | Totals calculate correctly |

---

## CHECKOUT SLOTS

### Checkout Form
| Property | Value |
|----------|-------|
| DOM TARGET | `#checkout-form`, `.checkout-content` |
| DATA | Customer fields, shipping, billing |
| AUREON SOURCE | WooCommerce → `woocommerce_checkout` |
| BRIDGE | WooCommerce native checkout template |
| FALLBACK | Static checkout form |
| TEST | Checkout processes orders correctly |

### Order Summary
| Property | Value |
|----------|-------|
| DOM TARGET | `.order-review`, `.order-summary` |
| DATA | Cart items, totals |
| AUREON SOURCE | WooCommerce → checkout order review |
| BRIDGE | WooCommerce native order review |
| FALLBACK | Static order summary |
| TEST | Order summary shows correct items |

---

## BLOG SLOTS

### Blog Grid/List
| Property | Value |
|----------|-------|
| DOM TARGET | `.blog-card`, `.post-item` |
| DATA | Post title, excerpt, image, date, author, category |
| AUREON SOURCE | WordPress → `WP_Query` (post type: post) |
| BRIDGE | `aether_blog_loop($args)` |
| FALLBACK | Static blog posts |
| TEST | Blog posts display correctly |

### Single Post
| Property | Value |
|----------|-------|
| DOM TARGET | `.blog-detail`, `.post-content` |
| DATA | Post title, content, featured image, meta |
| AUREON SOURCE | WordPress → `the_post()` |
| BRIDGE | WordPress template hierarchy |
| FALLBACK | Static blog post |
| TEST | Single post displays correctly |

---

## FOOTER SLOTS

### Footer Content
| Property | Value |
|----------|-------|
| DOM TARGET | `.footer-content`, `.footer-col` |
| DATA | Footer columns, links, text |
| AUREON SOURCE | WordPress → `wp_nav_menu('footer')` + Customizer |
| BRIDGE | `aether_footer_content()` |
| FALLBACK | Default Vineta footer |
| TEST | Footer reflects WordPress menu and Customizer |

### Newsletter
| Property | Value |
|----------|-------|
| DOM TARGET | `.newsletter-form`, `.subscribe-form` |
| DATA | Newsletter form action URL |
| AUREON SOURCE | WooCommerce → newsletter plugin or Customizer |
| BRIDGE | `aether_newsletter_form()` |
| FALLBACK | Static newsletter form |
| TEST | Newsletter form submits correctly |

### Payment Icons
| Property | Value |
|----------|-------|
| DOM TARGET | `.payment-icon img` |
| DATA | Payment method icons |
| AUREON SOURCE | WooCommerce → enabled payment gateways |
| BRIDGE | `aether_payment_icons()` |
| FALLBACK | Default Vineta payment icons |
| TEST | Payment icons match enabled gateways |

### Social Links
| Property | Value |
|----------|-------|
| DOM TARGET | `.tf-social-icon a`, `.footer-social a` |
| DATA | Social media URLs |
| AUREON SOURCE | WordPress Customizer → Social links section |
| BRIDGE | `aether_customizer_value('social_links')` |
| FALLBACK | Default social URLs |
| TEST | Social links reflect Customizer values |

---

## WISHLIST / COMPARE (BRIDGE REQUIRED)

### Wishlist
| Property | Value |
|----------|-------|
| DOM TARGET | `.wishlist`, `#wishlist-content` |
| DATA | Wishlist items |
| AUREON SOURCE | WooCommerce → Wishlist plugin (YITH or similar) |
| BRIDGE | **REQUIRES PLUGIN INTEGRATION** |
| FALLBACK | Empty wishlist |
| TEST | Add to wishlist works |

### Compare
| Property | Value |
|----------|-------|
| DOM TARGET | `.compare`, `#compare-content` |
| DATA | Compare items |
| AUREON SOURCE | WooCommerce → Compare plugin (YITH or similar) |
| BRIDGE | **REQUIRES PLUGIN INTEGRATION** |
| FALLBACK | Empty compare |
| TEST | Add to compare works |

---

## DEMO SLOTS

### Demo Products
| Property | Value |
|----------|-------|
| DOM TARGET | All product slots |
| DATA | Demo product data |
| AUREON SOURCE | WordPress → Demo content system |
| BRIDGE | `aether_demo_products()` |
| FALLBACK | Static Vineta product data |
| TEST | Demo products display when no real products exist |

### Demo Categories
| Property | Value |
|----------|-------|
| DOM TARGET | Category slots |
| DATA | Demo category data |
| AUREON SOURCE | WordPress → Demo content system |
| BRIDGE | `aether_demo_categories()` |
| FALLBACK | Static Vineta category data |
| TEST | Demo categories display when no real categories exist |

---

## SUMMARY

| Slot Category | Total Slots | Bridge Status |
|---------------|-------------|---------------|
| Global | 4 | Ready |
| Product | 5 | Ready |
| Variable Product | 3 | Ready |
| Shop | 4 | Ready |
| Category | 2 | Ready |
| Search | 2 | Ready |
| Authentication | 2 | Ready |
| Account | 3 | Ready |
| Cart | 4 | Ready |
| Checkout | 2 | Ready |
| Blog | 2 | Ready |
| Footer | 4 | Ready |
| Wishlist/Compare | 2 | BRIDGE REQUIRED (plugin) |
| Demo | 2 | Ready |
| **TOTAL** | **41** | **39 ready, 2 require plugin** |
