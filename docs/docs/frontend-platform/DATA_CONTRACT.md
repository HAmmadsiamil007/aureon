# AETHER DATA CONTRACT (Frozen)

> **Status:** FROZEN · **Freeze date:** 2026-08-14 · **Baseline:** `v1.2.1-audit`
> **Scope:** canonical ViewModel shapes consumed by section/component templates, plus the freeze rules that protect them.
> **Supersets:** `docs/FRONTEND_DATA_CONTRACT.md` (baseline with per-field tables) — this doc freezes the *canonical key names* and the *change process*.
> **Related:** `docs/frontend-platform/COMPONENT_CONTRACT.md`, `docs/frontend-platform/MASTER_FRONTEND_REPLACEMENT_PLAN.md`.

## 1. Freeze rules (M1)

1. **Adapters are the contract's implementation.** A design pack never changes what an adapter returns. Changing an adapter = platform change (all clients).
2. **Canonical key names are frozen.** Legacy aliases resolve via `aether_normalize_viewmodel()` in the renderer — new templates/packs MUST use the canonical name, never the alias.
3. **Adding fields is allowed** (additive, with safe defaults). Removing/renaming a field = platform change.
4. **Every field is optional at render** — missing key → safe default, never a blank page.
5. **Components never call WP/WC** (verify.sh grep gate) and never hardcode colors (hex gate, M4).
6. **Behavior flags** pass through `aether_viewmodel_behavior()`; Customizer motion toggles filter them.
7. **Per-call section `$data` wins** over registered `adapter_args`.

## 2. Canonical key names + legacy aliases

| Canonical | Legacy alias | Origin of legacy | Normalized by |
|---|---|---|---|
| `pagination{current,total,base}` | `paged` | `adapter-blog` | `aether_normalize_viewmodel()` |
| `breadcrumb` | `crumbs` | `adapter-cart` | `aether_normalize_viewmodel()` |
| `stats` = list of `{number,label}` | `stats.items` wrapper | `adapter-about` | normalizer + `section-stats` compat line |

## 3. ViewModels (canonical shapes)

### 3.1 SiteViewModel — `adapter-site` (+ `aether_adapter_footer`)
`name, brand, tagline, url, logo, socials[], footer_links[group{title,links[]}], newsletter{...}, payments[], legal[]`
→ `shell/footer`, `shell/preloader`

### 3.2 HeaderViewModel — `adapter-shell` (`aether_adapter_header`)
`brand, brand_url, menu[], icons{search,wishlist,cart,account}, cart_count`
→ `shell/header`, `shell/mobile-chrome`

### 3.3 MenuViewModel — `adapter-menu` (signature: `$location`)
`[{label, url, active, children[]}]` — `wp_get_nav_menu_items('primary')`; <4 root items → curated fallback.

### 3.4 AnnouncementViewModel — `adapter-shell` (`aether_adapter_announcement`)
`items[{text}]` — from `aether_announcement_items` (repeater JSON/array) or `aether_announcement_text` fallback. Rendered twice for the marquee loop.
→ `shell/announcement`, `shell/mobile-chrome`

### 3.5 HeroViewModel — `adapter-hero`
`slides[{id, visible, headline, accent, subline, badge, image, mobile_image, image_alt, overlay, primary_cta{label,url}, secondary_cta{label,url}}], behavior`
→ `hero/slider`, `hero/slide`. G6 repeater schema (`aether_register_hero_repeater_schema`) IS this contract; sanitizer whitelists from the same source.

### 3.6 ProductViewModel (cards) — `adapter-wc-products`
`items[{id, name, tagline, price, price_plain, old_price_plain, rating, reviews, image, alt, url, badge, behavior}], pagination{current,total}, cta_label, cta_url`
→ `card/product` (layout home|shop), `card/wishlist`. Demo fallback gated by `aether_demo_content`.

### 3.7 CategoryViewModel — `adapter-wc-categories`
`items[{name, count, image, alt, url, modifier, behavior}], has_more, all_categories_url, label, title, subtitle`
→ `card/category`. Fallback: curated SKU categories when no terms.

### 3.8 BlogPostViewModel — `adapter-blog`
`items[{id, title, excerpt, date, author, category, url, image, alt}], pagination{current,total,base}` (alias `paged`)
→ `card/blog`; `content/article-*` on single.

### 3.9 CartViewModel — `adapter-cart` (signature: `context`)
`context, cart_url, shop_url, checkout_url, is_empty, items[{key,name,url,image,price,total,qty,remove_url,sku}], subtotal, shipping, tax, total, title, crumbs(=breadcrumb)`
→ `cart/items`, `cart/summary`

### 3.10 OrderViewModel — `adapter-order`
`title, subtitle, order_number, email_note, delivery_note, shop_url, track_url`
→ `order/confirmation`

### 3.11 AccountViewModel — `adapter-account` (+ `aether_adapter_account_orders`)
`name, email, initial, stats[{number,label}], menu[{label,url,icon}], logout_label, dashboard_url, shop_url` / `orders[{number,date,status,total,url}], empty_text, shop_url, logout_url`
→ `account/profile`, `account/orders`

### 3.12 SingleProductViewModel — `adapter-product`
`id, name, price, price_plain, old_price_plain, rating, rating_text, description, colors[{name,hex}], sizes[], quantity, add_to_cart_url, add_to_cart_label, trust[{icon,label}], specs[{icon,title,body}], size_table[{us,eu,uk,cm}], reviews_score, reviews_count, reviews_bars[{star,percent,count}], reviews_items[{initials,name,meta,stars,title,text}], breadcrumb(=crumbs), gallery[{src}]`
→ `product/*` components. Fallbacks gated by `aether_demo_content`.

### 3.13 ContactViewModel — `adapter-contact`
`fields[{name,type,label,placeholder,required}], action, nonce, info[{icon,label,value,type,href}], socials`
→ `form/contact`. Reads `aether_contact_address`/`aether_contact_hours`.

### 3.14 Supporting ViewModels

| Adapter | Keys |
|---|---|
| `adapter-auth` | `brand, forgot, login, register, redirect, show_register` |
| `adapter-coming-soon` | `brand, title, subtitle, target, socials` |
| `adapter-faq` | `items[{question,answer}]` (demo-gated) |
| `adapter-options` (signature: `$keys`) | passthrough of requested option values |
| `adapter-shop-hero` | `label, title, subtitle` |
| `adapter-team` | `items[{name,role,bio,image}]` (demo-gated) |
| `adapter-testimonials` | `items[{name,role,verified,stars,title,quote,date}], score, count` (real WC aggregate wins; only adapter with raw `$wpdb`) |
| `adapter-wc-filter` | `buttons[]` |
| `adapter-wishlist` | `items[], status, count, shop_url, account_url` |
| `adapter-article` (signature: `post_id`) | `category, title, image, alt, author, author_bio, date, read_time, content, avatar, behavior` |

### 3.15 Shared shapes
- `items` (8 adapters) · `brand` (6) · `title` (6) · `subtitle` (4) · `shop_url` (4) · `socials` (4) · `behavior` (hero, product, category, article — via `aether_viewmodel_behavior()`)

## 4. Contract invariants

1. Components never call `get_option`, `wc_get_product`, `WP_Query`, `get_permalink`, etc. (verify.sh gate).
2. Adapters return normalized arrays; renderer wraps flat lists → `items`.
3. All render-time values escaped (`esc_html`/`esc_attr`/`esc_url`/`esc_url_raw`); one documented `wc_price` HTML passthrough in `card/product.php`.
4. Behavior flags whitelisted via `aether_behavior_attrs()` (reveal, reveal-group, tilt, parallax, parallax-section, zoom, motion-text).
5. Missing keys render safe defaults; empty sections return early (graceful empty state).

## 5. Change process (any future contract change)

1. Open a case in the master plan risk register (docs/frontend-platform/MASTER_FRONTEND_REPLACEMENT_PLAN.md §31).
2. Adapter change + normalizer alias + template compat in one commit, gated by full suite.
3. Update this doc and `docs/FRONTEND_DATA_CONTRACT.md` in the same commit.
4. Never change a canonical name without adding a normalizer alias for the old one.
