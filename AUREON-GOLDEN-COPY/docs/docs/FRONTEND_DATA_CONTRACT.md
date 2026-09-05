# FRONTEND DATA CONTRACT

> **Status:** COMPLETE (baseline) · **Date:** 2026-08-08 · **Closure:** 2026-08-09 (G1/G2/G4/G5 resolved — see §15)
> **Source of truth:** `frontend/adapters/*.php` output shapes (verified), `frontend/views/viewmodel.php` normalizers, `frontend/components/*` prop docs, `frontend/ADAPTER_SPECIFICATION.md`.
> **Rule:** Components consume normalized ViewModels only. Adapters are the only layer that touches WP/WC. All fields optional at render; components provide safe defaults.

Every contract below is written as **INPUT → VIEWMODEL → PRESENTATION** per mission §2.

---

## 1. SiteViewModel (`aether_adapter_site` — `adapters/adapter-site.php`)

```
INPUT: bloginfo(name, description), home_url(), WC page permalinks, wishlist page
→ SITE (brand, name, tagline, url, logo, socials, footer menus, newsletter, payments, legal)
→ shell/footer, shell/preloader, shell/header, shell/mobile-chrome
```

| Field | Type | Default | Source | Consumed by |
|---|---|---|---|---|
| `name` | string | '' | `get_bloginfo('name')` | footer brand, preloader |
| `brand` | string | '' | `get_bloginfo('name')` | footer logo |
| `tagline` | string | '' | `get_bloginfo('description')` | footer tagline |
| `url` | string | '' | `home_url('/')` | footer brand link |
| `socials` | array | [] | hardcoded (IG/Twitter/TikTok/YT) | footer social row |
| `footer_links` | array[group] | [] | hardcoded columns | footer link columns |
| `newsletter` | array | [] | hardcoded copy | footer newsletter |
| `payments` | array | [] | hardcoded FA icons | footer payments |
| `legal` | array | [] | hardcoded | footer bottom |

## 2. HeaderViewModel (`aether_adapter_header` — `adapters/adapter-shell.php`)

```
INPUT: bloginfo, WC shop/cart/account permalinks, WP menu tree, WC()->cart count
→ HEADER (brand, brand_url, menu, icons{search,wishlist,cart,account}, cart_count)
→ shell/header, shell/mobile-chrome
```

| Field | Type | Default | Source |
|---|---|---|---|
| `brand` | string | '' | `get_bloginfo('name')` |
| `brand_url` | string | '' | `home_url('/')` |
| `menu` | MenuTree | [] | `aether_adapter_menu('primary')` |
| `icons.search` | string | '#' | WC shop permalink |
| `icons.wishlist` | string | '#' | wishlist page permalink (falls back to shop) |
| `icons.cart` | string | '#' | `wc_get_cart_url()` |
| `icons.account` | string | '#' | `wc_get_page_permalink('myaccount')` / `wp_login_url()` |
| `cart_count` | int | 0 | `WC()->cart->get_cart_contents_count()` |

## 3. MenuViewModel (`aether_adapter_menu` — `adapters/adapter-menu.php`)

```
INPUT: WP nav menu location 'primary' (wp_get_nav_menu_items)
→ MENU (tree of {label, url, active, children[]})
→ shell/header, shell/mobile-chrome
```

| Field | Type | Default | Notes |
|---|---|---|---|
| `label` | string | '' | wp_nav_menu item title |
| `url` | string | '#' | `esc_url` at render |
| `active` | bool | false | current-item detection |
| `children` | array | [] | nested items (dropdown) |

Fallback: assigned menu with < 4 root items → curated fallback (Shop dropdown, About, Blog, FAQ, Contact). WP menu always wins when present and sufficient.

## 4. AnnouncementViewModel (`aether_adapter_announcement` — `adapters/adapter-shell.php`)

```
INPUT: `aether_announcement_items` (repeater JSON or array) + `aether_announcement_text` single-item fallback (G1 CLOSED 2026-08-09)
→ ANNOUNCEMENT (items[{text}])
→ shell/announcement, shell/mobile-chrome
```

| Field | Type | Default | Notes |
|---|---|---|---|
| `items[].text` | string | '' | rendered twice for seamless marquee |

## 5. HeroViewModel (`aether_adapter_hero` — `adapters/adapter-hero.php`)

```
INPUT: aureon_get_option('aether_hero_slides') (repeater JSON or PHP array; legacy shape normalized)
→ HERO (slides[{headline, accent, subline, image, alt, buttons[{label,url,style}]}], behavior)
→ sections/section-hero → hero/slider + hero/slide
```

| Field | Type | Default | Source |
|---|---|---|---|
| `headline` | string | '' | slide `title`/`headline` (sanitized) |
| `accent` | string | '' | slide `accent` |
| `subline` | string | '' | slide `subtitle`/`subline` |
| `image` | string | '' | `aether_viewmodel_resolve_image(slide['image'])` |
| `alt` | string | '' | slide `alt`/`label` |
| `buttons` | array[{label,url,style}] | [] | slide `buttons` array or `cta`+`url` (primary) |
| `behavior` | array | `parallax-section` | via `aether_viewmodel_behavior()` |

## 6. ProductViewModel (cards) (`aether_adapter_wc_products` — `adapters/adapter-wc-products.php`)

```
INPUT: WP_Query over 'product' (+ on_sale/related_to/orderby_shop/paged) → wc_get_product per post
→ PRODUCT (id, name, tagline, price, price_plain, old_price_plain, rating, reviews, image, alt, url, badge, behavior)
→ card/product (layout home|shop), card/wishlist
```

| Field | Type | Default | Source |
|---|---|---|---|
| `id` | int | 0 | `$product->get_id()` |
| `name` | string | '' | `get_name()` |
| `tagline` | string | '' | `get_short_description()` stripped, ≤48 chars |
| `price` | string | '' | `get_price_html()` (may contain HTML — escaped at render via documented exception) |
| `price_plain` | string | '' | `wp_strip_all_tags(wc_price(get_price()))` |
| `old_price_plain` | string | '' | regular price when on sale |
| `rating` | float | 0 | `get_average_rating()` |
| `reviews` | int | '' | `get_review_count()` |
| `image` | string | '' | `get_the_post_thumbnail_url(medium_large)` |
| `alt` | string | name | attachment alt |
| `url` | string | '#' | `get_permalink()` |
| `badge` | string | '' | Sale > New(<30d) > Featured |
| `behavior` | array | `tilt` | `aether_viewmodel_behavior()` |

**Fallback:** when query empty → `aether_product_items` demo tokens, gated by `aether_demo_content` (G2 CLOSED 2026-08-09).

## 7. CategoryViewModel (`aether_adapter_wc_categories` — `adapters/adapter-wc-categories.php`)

```
INPUT: get_terms('product_cat', exclude default_cat, count desc, 5) → per-term image/url/count
→ CATEGORY (items[{name,count,image,alt,url,modifier,behavior}], has_more, all_categories_url, section copy)
→ sections/section-categories → card/category
```

| Field | Type | Default | Source |
|---|---|---|---|
| `name` | string | '' | term name |
| `count` | string | '' | `_n('%d Product(s)')` |
| `image` | string | '' | term thumbnail → first product image → `wc_placeholder_img_src` |
| `url` | string | '' | `get_term_link()` (WP_Error → shop) |
| `modifier` | string | '' | `large` / `accent` (grid placement) |
| `behavior` | array | `reveal` | — |
| `has_more` | bool | false | `wp_count_terms > 5` |
| `all_categories_url` | string | '' | WC shop permalink |
| `aether_categories_{label,title,subtitle}` | string | '' | section copy (adapter-args-bound; no Customizer control — gap G3) |

**Fallback:** curated SKU-based categories when no terms.

## 8. BlogPostViewModel (`aether_adapter_blog` — `adapters/adapter-blog.php`)

```
INPUT: WP_Query (whitelisted keys only) → posts
→ BLOG (items[{id,title,excerpt,date,author,category,url,image,alt}], pagination{current,total,base})
→ sections/section-blog-grid → card/blog; sections/section-blog-single → content/article-*
```

| Field | Type | Default | Source |
|---|---|---|---|
| `id` | int | 0 | `get_the_ID()` |
| `title` | string | '' | `get_the_title()` |
| `excerpt` | string | '' | `get_the_excerpt()` |
| `date` | string | '' | `get_the_date()` |
| `author` | string | '' | author name |
| `category` | string | '' | first category name |
| `url` | string | '#' | `get_permalink()` |
| `image` | string | '' | `get_the_post_thumbnail_url(medium_large)` |
| `pagination` | array | {current:1,total:1,base} | `paginate_links()` data |

## 9. CartViewModel (`aether_adapter_cart` — `adapters/adapter-cart.php`)

```
INPUT: WC()->cart (empty → empty state), WC page permalinks
→ CART (items[{key,name,url,image,price,total,qty,remove_url,sku}], totals{subtotal,shipping,total}, actions{shop_url,checkout_url,cart_url})
→ sections/section-cart → cart/items + cart/summary
```

| Field | Type | Source |
|---|---|---|
| `items[].key` | string | cart item key |
| `items[].name` | string | `$item['data']->get_name()` |
| `items[].price`/`total` | string | `wc_price()` |
| `items[].qty` | int | item qty |
| `items[].remove_url` | string | `wc_get_cart_remove_url($key)` |
| `subtotal`/`total` | string | `$cart->get_totals()` |
| `empty` | bool | `$cart->is_empty()` |

## 10. OrderViewModel (`aether_adapter_order` — `adapters/adapter-order.php`)

```
INPUT: order id (URL) → wc_get_order
→ ORDER (number, date, status, items[{name,qty,price,total}], totals, actions{shop_url, track_url})
→ sections/section-order-confirmation → order/confirmation
```

## 11. AccountViewModel (`aether_adapter_account` — `adapters/adapter-account.php`)

```
INPUT: current WP/WC customer + wc_get_orders + endpoint URLs
→ ACCOUNT (user{name,email,avatar}, nav[{label,url,active}], stats{orders,addresses,downloads}, orders[{number,date,status,total,url}], dashboard_url, shop_url, logout_url)
→ account/profile, account/orders
```

## 12. SingleProductViewModel (`aether_adapter_product` — `adapters/adapter-product.php`)

```
INPUT: wc_get_product($id) → WC product + attributes + gallery + reviews
→ PRODUCT (id, name, price_html, rating, score, gallery[{src}], colors[{name,hex}], sizes[], specs[{icon,title,body}], reviews[{initials,name,meta,stars,title,text}], score_bars[{star,percent,count}], breadcrumb, add_to_cart_url, trust[{icon,label}], size_table)
→ sections/section-product → product/* components
```

**Fallbacks (documented):** colors/sizes/specs/reviews/score/bars/trust/size_table fall back to demo tokens, gated by `aether_demo_content` (G2 CLOSED 2026-08-09).

## 13. ContactViewModel (`aether_adapter_contact` — `adapters/adapter-contact.php`)

```
INPUT: admin_email, hardcoded address/phone/hours
→ CONTACT (heading, info[{icon,label,value,type,href}], form{id,fields[{name,type,label,placeholder,required}]}, map, socials)
→ sections/section-contact → form/contact
```

---

## 14. Contract invariants (enforced by architecture)

1. Components never call `get_option`, `wc_get_product`, `WP_Query`, `get_permalink`, etc. (verified via `tests/verify.sh` grep gate).
2. Adapters return normalized arrays; renderer normalizes flat lists → `items`.
3. All render-time values escaped: `esc_html` / `esc_attr` / `esc_url` / `esc_url_raw` (one documented `wc_price` HTML passthrough in `card/product.php`).
4. Per-call section `$data` wins over registered `adapter_args` (renderer contract — Stage 5 bug fix).
5. Behavior flags pass through `aether_viewmodel_behavior()` (Customizer motion toggles filter them).
6. Every contract field is optional at render — missing key → safe default, never a blank page.

---

## 15. Contract gaps (bindings needed — Phase C)

| Gap | Contract affected | Status (2026-08-09) |
|---|---|---|
| G1 | AnnouncementViewModel ignored `aether_announcement_text/items` | ✅ **CLOSED** — `adapter-shell.php` reads `aether_announcement_items` (JSON/array) with `aether_announcement_text` fallback |
| G2 | Demo fallbacks in Product/Category/Testimonial/Team/FAQ/Product-single contracts | ✅ **CLOSED** — `aether_demo_content` master toggle gates all 7 fallback loops |
| G3 | Categories section copy not Customizer-bound | ⚠️ partial — copy flows via tokens/settings (`aether_categories_*`); no dedicated controls (deferred, low priority) |
| G4 | Footer link columns hardcoded | ✅ **CLOSED** — `adapter-site.php` reads `aether_footer_columns`, empty URLs resolved against the default map |
| G5 | Contact info hardcoded | ✅ **CLOSED** — `adapter-contact.php` reads `aether_contact_address`/`aether_contact_hours` |
| G6 | Hero slides no Customizer repeater control | ⏸ **DEFERRED** — would require a theme-side control (stop-condition); slides settable via settings bucket / tokens |
