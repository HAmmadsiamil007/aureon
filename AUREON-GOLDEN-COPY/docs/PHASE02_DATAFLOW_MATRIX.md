# Phase 2 — Static Audit: Data-Flow Matrix

> **Date:** 2026-08-14. Master verification task Phase 2 of 16.
> **Method:** static read of `aureon/theme/*.php` (24 templates), `frontend/sections/` (26), `frontend/adapters/` (23), `frontend/components/` (53 manifest entries), `frontend/views/{composer,renderer,registry}.php`.
> **Contract under test:** `WP/WC → Adapters (only WP/WC-touching layer) → ViewModel → Renderer (section registry, per-call $data wins) → Components (zero WP/WC calls) → CSS/JS`.

---

## 1. Surface inventory (24 = 23 live + 1 dev)

| # | Route / template | Sections (in order) | Direct components | Adapter(s) |
|---|---|---|---|---|
| S1 | **Shell (every route)** — `header.php`/`footer.php` → `composer.php` | — | preloader, fog, skip-link, announcement, header, mobile-chrome, footer, quick-view (footer.php) | `aether_adapter_site` (preloader), `aether_adapter_mobile`, `aether_adapter_announcement`, `aether_adapter_header`, `aether_adapter_footer` |
| S2 | `/` — `front-page.php` | hero, categories, bestsellers, reviews, faq, newsletter | — | hero / wc-categories / wc-products / testimonials / faq / options |
| S3 | `/shop/`, `/product-category/*` — `archive-product.php` | shop-hero, shop-filter, shop-grid, newsletter | — | shop-hero / wc-filter / wc-products (per-call: paged, tax_query, on_sale, orderby_shop) / options |
| S4 | `/product/*` — `single-product.php` | product, related, newsletter | — | product / wc-products (related_to, posts_per_page=4) / options |
| S5 | `/cart/` — `cart.php` | cart, newsletter | — | cart / options |
| S6 | `/checkout/` — `checkout/form-checkout.php` | checkout | — | cart |
| S7 | `/checkout/order-received/*` — `woocommerce/checkout/thankyou.php` | order-confirmation, newsletter | — | order / options |
| S8 | `/my-account/*` — `myaccount/my-account.php` | — | page-banner; then per branch: account/profile (dashboard), account/orders + stock nav (orders), stock `woocommerce_account_content` (other endpoints), stock WC login/register forms (logged out) | `aether_adapter_account`, `aether_adapter_account_orders` |
| S9 | `/blog/` — `home.php` | blog-grid, newsletter | hero/page-title (inline) | blog / options |
| S10 | single post — `single.php` | blog-single, blog-grid (related: posts_per_page=3, post__not_in), newsletter | — | article / blog / options |
| S11 | archives — `archive.php` | blog-grid (category_name), newsletter | hero/page-title (inline) | blog / options |
| S12 | search — `search.php` | blog-grid (s=), newsletter | hero/page-title (inline) | blog / options |
| S13 | index fallback — `index.php` | blog-grid, newsletter | hero/page-title (inline) | blog / options |
| S14 | 404 — `404.php` | newsletter | error/404 | — / options |
| S15 | generic page — `page.php` | newsletter | hero/page-title, content/page | — / options |
| S16 | `/about/` — `page-about.php` | mission, features, story, stats, team, newsletter | — | about (x5) / team / options |
| S17 | `/contact/` — `page-contact.php` | contact, newsletter | hero/page-title (inline) | contact / options |
| S18 | `/team/` — `page-team.php` | team, values, newsletter | hero/page-title (inline) | team / about / options |
| S19 | `/faq/` — `page-faq.php` | faq (first-item-open), newsletter | hero/page-title (inline) | faq / options |
| S20 | `/wishlist/` — `page-wishlist.php` | wishlist, newsletter | hero/page-title (inline) | wishlist / options |
| S21 | `/login/` — `page-login.php` | auth (mode=login) | — | auth |
| S22 | `/register/` — `page-register.php` | auth (mode=register) | — | auth |
| S23 | `/coming-soon/` — `page-coming-soon.php` | coming-soon | — | coming-soon |
| S24 | `/styleguide/` (dev) — `page-styleguide.php` | — | ~30 direct component calls with sample data | none (pure) |

**Note:** "27-surface" in the task refers to the E2E route matrix (16 desktop + mobile splits + error/edge surfaces). The 24 template surfaces above are the complete static inventory.

## 2. Section → adapter → component map (26 sections)

| Section | Adapter | Components rendered | Per-call data |
|---|---|---|---|
| `hero` | `aether_adapter_hero` | hero/slider → hero/slide (inner loop) | — |
| `categories` | `aether_adapter_wc_categories` | section/header, card/category | — |
| `bestsellers` | `aether_adapter_wc_products` | section/header, card/product, section/cta | with_cta, posts_per_page=4 |
| `reviews` | `aether_adapter_testimonials` | section/header, commerce/rating, card/review | — |
| `faq` | `aether_adapter_faq` | section/header, section/accordion | open flag |
| `newsletter` | `aether_adapter_options` | section/newsletter → form/newsletter (inner) | — |
| `shop-hero` | `aether_adapter_shop_hero` | hero/page-title | — |
| `shop-filter` | `aether_adapter_wc_filter` | **inline markup — NOT `section/filter-bar`** ⚠ | — |
| `shop-grid` | `aether_adapter_wc_products` | card/product (layout=shop), section/pagination | paged, tax_query, on_sale, orderby_shop |
| `product` | `aether_adapter_product` | product/breadcrumb, gallery, info, sticky-bar, specs, reviews, size-guide | — |
| `related` | `aether_adapter_wc_products` | product/related | related_to, posts_per_page=4 |
| `cart` | `aether_adapter_cart` | hero/page-banner, cart/items, cart/summary | — |
| `checkout` | `aether_adapter_cart` | hero/page-banner, checkout/order-items + inline WC form | — |
| `order-confirmation` | `aether_adapter_order` | order/confirmation | — |
| `auth` | `aether_adapter_auth` | form/login OR form/register + form/forgot-password (mode-gated) | mode |
| `mission` | `aether_adapter_about` | **inline** (image + text grid) | — |
| `features` | `aether_adapter_about` | section/header + inline feature cards | — |
| `story` | `aether_adapter_about` | content/story | — |
| `stats` | `aether_adapter_about` | **inline** stat items (data-countup) | — |
| `values` | `aether_adapter_about` | section/header + inline value cards | — |
| `team` | `aether_adapter_team` | section/header, card/team | — |
| `contact` | `aether_adapter_contact` | form/contact | — |
| `wishlist` | `aether_adapter_wishlist` | card/wishlist | — |
| `coming-soon` | `aether_adapter_coming_soon` | soon/countdown | — |
| `blog-grid` | `aether_adapter_blog` | section/header, card/blog, section/pagination | s, category_name, post__not_in, show_pagination, posts_per_page |
| `blog-single` | `aether_adapter_article` | content/article-hero, article-meta, article-body, author-bio | post_id |

## 3. Adapter inventory (23 files, 28 functions)

| Adapter | Functions | Serves | Primary option/query reads |
|---|---|---|---|
| `adapter-about.php` | `aether_adapter_about` | mission, features, story, stats, values | `aether_mission/features/story/stats/values` tokens |
| `adapter-account.php` | `aether_adapter_account`, `aether_adapter_account_orders` | my-account dashboard + orders | WC customer, `wc_get_orders` |
| `adapter-article.php` | `aether_adapter_article` | blog-single | `get_post($post_id)`, `get_the_terms`, `wp_get_attachment_image` |
| `adapter-auth.php` | `aether_adapter_auth` | auth | `get_option('users_can_register')`, Google OAuth tokens |
| `adapter-blog.php` | `aether_adapter_blog` | blog-grid (4 surfaces) | `WP_Query` (s/category_name/post__not_in/orderby), pagination |
| `adapter-cart.php` | `aether_adapter_cart` | cart, checkout | `WC()->cart`, `wc_get_page_permalink` |
| `adapter-coming-soon.php` | `aether_adapter_coming_soon` | coming-soon | `aether_coming_soon_*` tokens |
| `adapter-contact.php` | `aether_adapter_contact` | contact | `aether_contact_*` tokens |
| `adapter-faq.php` | `aether_adapter_faq` | faq | FAQ CPT → `aether_faq_items` fallback |
| `adapter-hero.php` | `aether_adapter_hero` | hero | `aether_hero_slides` (JSON string or array; legacy shape normalize) |
| `adapter-menu.php` | `aether_adapter_menu`, `aether_adapter_socials` | header nav, footer socials | `wp_get_nav_menu_items`, `get_theme_mod` socials |
| `adapter-options.php` | `aether_adapter_options` | newsletter | `aether_newsletter_*` tokens |
| `adapter-order.php` | `aether_adapter_order` | order-confirmation | `WC()->session->order_awaiting_payment`, `wc_get_order` |
| `adapter-product.php` | `aether_adapter_product` | product | `wc_get_product`, gallery, attributes, reviews, `wc_get_related_products` |
| `adapter-shell.php` | `aether_adapter_announcement`, `aether_adapter_header`, `aether_adapter_mobile` | shell | `aether_announcement_*`, `aether_header_*`, cart count |
| `adapter-shop-hero.php` | `aether_adapter_shop_hero` | shop-hero | `woocommerce_page_title`, is_product_category/tag |
| `adapter-site.php` | `aether_adapter_site`, `aether_adapter_footer` | preloader, footer | `get_bloginfo`, `aether_footer_columns`, menus |
| `adapter-team.php` | `aether_adapter_team` | team | team CPT → `aether_team_items` fallback |
| `adapter-testimonials.php` | `aether_adapter_testimonials` | reviews | testimonials CPT → `aether_testimonial_items` fallback, score/count |
| `adapter-wc-categories.php` | `aether_adapter_wc_categories` | categories | `get_terms(product_cat)`, images |
| `adapter-wc-filter.php` | `aether_adapter_wc_filter` | shop-filter | `get_terms(product_cat)`, `wc_get_product_ids_on_sale` |
| `adapter-wc-products.php` | `aether_adapter_wc_products` | bestsellers, shop-grid, related | `WC_Product_Query`/`wc_get_related_products`, sale/featured flags, menu_order |
| `adapter-wishlist.php` | `aether_adapter_wishlist` | wishlist | user meta `aether_wishlist`, `WC_Product_Query` |

## 4. Component usage map (53 manifest entries → live call sites)

**All 53 entries are reachable** (direct render, inner render, or manifest-only-by-design):

| Group | Entries | Rendered from |
|---|---|---|
| Shell (7) | preloader, fog, skip-link, announcement, header, mobile-chrome, footer | composer.php (header.php/footer.php) |
| Hero (4) | slider, slide, page-title, page-banner | slider←section-hero; slide←slider inner; page-title←shop-hero + 9 inline templates; page-banner←cart, checkout, my-account |
| Section (6) | header, cta, accordion, newsletter, pagination, **filter-bar ⚠** | header←8 sections; cta←bestsellers; accordion←faq; newsletter←section-newsletter; pagination←blog-grid+shop-grid; **filter-bar←styleguide only** |
| Cards (6) | product, category, blog, review, team, wishlist | product←bestsellers/shop-grid/styleguide; category←categories; blog←blog-grid; review←reviews; team←team; wishlist←wishlist |
| Cart/Checkout/Account (5) | cart/items, cart/summary, checkout/order-items, account/profile, account/orders | cart←section-cart; checkout/order-items←section-checkout; account/*←my-account template |
| Auth (1) | auth/password-strength | inner of form/register |
| Order (1) | order/confirmation | section-order-confirmation |
| Commerce (2) | rating, quick-view | rating←reviews + styleguide; quick-view←footer.php (JS-populated modal) |
| Product (8) | breadcrumb, gallery, info, sticky-bar, specs, reviews, related, size-guide | section-product (7) + section-related |
| Content (6) | page, article-hero, article-meta, article-body, author-bio, story | page←page.php; article-*←blog-single; story←section-story |
| Forms (5) | contact, login, register, newsletter, forgot-password | contact←section-contact; login/register/forgot-password←section-auth; newsletter←inner of section/newsletter |
| Utility (2) | error/404, soon/countdown | 404.php, section-coming-soon |

**Component purity gate:** `verify.sh` grep gate enforces zero WP/WC function calls in `components/` — passed at baseline (337/337 `php -l`, gate green).

## 5. Findings

| # | Severity | Finding |
|---|---|---|
| F2-1 | **INFO** | `section/filter-bar` component is **dead on live routes** — `section-shop-filter.php` renders its own inline `.filter-bar` markup (lines 24–37) instead of the component. Component exists in manifest + styleguide only. **Divergence between manifest and live call sites.** (Style-wise identical; maintenance risk: two sources of truth for the same UI.) |
| F2-2 | **INFO** | `adapter-about.php` serves 5 sections (mission/features/story/stats/values) — a shared adapter with per-section keys. Data contract is `{mission:{…}, features:{items:[…]}, …}` — adapter returns all keys; each section picks its slice. Correct but couples 5 surfaces to one adapter file. |
| F2-3 | **INFO** | `adapter-cart.php` serves both cart AND checkout sections (checkout passes through the same cart adapter + inline WC form in section-checkout.php). |
| F2-4 | **INFO** | Blog surfaces S9/S11/S12/S13 render `hero/page-title` **inline in templates** (hardcoded i18n strings), while shop S3 uses the `shop-hero` section. Two different patterns for "page hero" — acceptable (blog heroes are static copy), noted for Phase 3 source-of-truth check. |
| F2-5 | **INFO** | `my-account.php` (S8) bypasses sections entirely — direct component composition with 5 branches (dashboard / orders / other endpoints / lost-password / logged-out). Largest hand-rolled template; uses stock WC content via `woocommerce_account_content()` inside `.aether-wc` frame. |
| F2-6 | **INFO** | `commerce/quick-view` is rendered (footer.php) but its content is 100% JS-driven (main.js:524–573, `data-quickview-body`), fed by admin-ajax `aether_quick_view`. The component ships a static "Loading…" shell. |
| F2-7 | **INFO** | Per-call `$data` reaching adapters verified at all per-call sites: S3 (paged/tax_query/on_sale/orderby_shop), S4 (related_to/posts_per_page), S10 (post_id, category_name, post__not_in), S12 (s). Matches the renderer merge fix (`wp_parse_args($data, $registered_args)`). |
| F2-8 | **OBS** | `error/404` and `content/page` are rendered **directly from templates** (404.php, page.php), not via sections — consistent with S14/S15 having no dedicated section. |
| F2-9 | **OBS** | No surface calls `form/newsletter`, `auth/password-strength`, or `hero/slide` directly — all three are inner-rendered by their parent components. No orphan entries. |

## 6. Gates

- `php -l` on all audited files: pending re-run at phase end (Phase 11 reruns the full gate suite).
- No code modified by this phase (read-only audit).

---
**Phase 2 complete.** Next: Phase 3 — source-of-truth registry.