# AETHER Frontend — Architecture & Integration Guide

> **Product:** AETHER — premium dark-mode e-commerce frontend
> **Stack:** WordPress 6.x + WooCommerce 11 + Aureon theme + AETHER engine (PHP 8.3)
> **Status:** Live on Docker `aureon_wp` (localhost:8080) — all 17 routes verified, 0 console errors
> **Doc version:** 2026-08-07

---

## 1. What is AETHER?

AETHER is a **premium, dark-mode, motion-rich storefront frontend** built as a self-contained PHP engine inside WordPress. It takes a hand-crafted static design (`frontend/source/`) and turns every element into a **live, data-driven, themeable** component — powered by WordPress content and WooCommerce commerce data.

The entire frontend follows one strict pipeline:

```
WordPress / WooCommerce
        │
        ▼
  ADAPTERS (only layer that touches WP/WC APIs)
        │  normalize → plain arrays
        ▼
  VIEWMODEL (sanitize, resolve images, apply motion toggles)
        │
        ▼
  RENDERER (section registry → merge adapter data → include template)
        │
        ▼
  COMPONENTS (pure markup, escaped output, zero WP calls)
        │
        ▼
  BROWSER (GSAP / Lenis / Swiper / Bootstrap / custom JS)
```

**The golden rule:** components never call WordPress or WooCommerce. Adapters are the only layer allowed to. This makes the design layer pure, portable, and testable — and the data layer isolated.

---

## 2. How the frontend is connected to WordPress

### 2.1 Theme glue — `aureon/theme/inc/frontend.php`

The theme is the entry point. One file boots everything:

| Step | What happens | Hook |
|---|---|---|
| 1 | `require loader.php` (defines `AETHER_FRONTEND_DIR`, engine files) | file load |
| 2 | Register nav locations `primary` + `footer` | `after_setup_theme` @20 |
| 3 | `aether_frontend_boot()` — loads tokens, registry, renderer, viewmodel, composer, all 21 adapters, all 24 sections | `after_setup_theme` @30 |
| 4 | **Suppress legacy theme output** — dequeues 12 theme styles + 5 scripts + 3 wp_footer hookups so nothing duplicates or fights AETHER | `wp_enqueue_scripts` @1000 |
| 5 | Enqueue AETHER assets (CDN + local, source-contract order) | `wp_enqueue_scripts` @20 |
| 6 | Route WC pages to AETHER templates (`cart.php`, `checkout/`, `my-account/`) | `template_include` @99 |
| 7 | Favicons, theme-color, msapplication metadata | `wp_head` @1 |

### 2.2 Path resolution — dev vs production

- **Dev:** `frontend/` lives at the repo root; `inc/frontend.php` requires `get_template_directory() . '/../../frontend/views/loader.php'`.
- **Production:** the whole `frontend/` tree ships to `wp-content/frontend/`; `AETHER_FRONTEND_DIR` is computed as `trailingslashit(dirname(__DIR__))` from `loader.php` itself, so the engine is **path-safe and self-contained** regardless of where it's mounted.

### 2.3 Template delegation

Theme templates are now **pure composition stubs** — they call `get_header()` (which runs `aether_compose_header()`), render sections via `aether_render_section()`, then `get_footer()` (which runs `aether_compose_footer()`):

```php
// front-page.php — home
if ( aureon_get_option( 'aether_section_hero', true ) ) {
    aether_render_section( 'hero' );
}
aether_render_section( 'categories' );  // adapter: aether_adapter_wc_categories()
aether_render_section( 'bestsellers' ); // adapter: aether_adapter_wc_products()
```

**Templates and their compositions:**

| Route | Template | Sections / Components |
|---|---|---|
| `/` | `front-page.php` | hero slider → categories → bestsellers → reviews → faq → newsletter |
| `/shop/`, `/product-category/*/` | `archive-product.php` | shop-hero → shop-filter → shop-grid → newsletter |
| `/product/*/` | `single-product.php` | product (breadcrumb/gallery/info/sticky/specs/reviews/size-guide) → related → newsletter |
| `/cart/` | `cart.php` | cart (WC session) → newsletter |
| `/checkout/` | `inc/frontend.php` routing | WC form-checkout inside AETHER shell |
| `/my-account/` | `inc/frontend.php` routing | WC account inside AETHER shell |
| `/blog/` (posts page) | `home.php` | page-title hero → blog-grid → newsletter |
| `/post-slug/` | `single.php` | blog-single → related (3) → newsletter |
| `/about/` | `page-about.php` | mission → features → story → stats → team → newsletter |
| `/contact/` | `page-contact.php` | contact → newsletter |
| `/team/` | `page-team.php` | page-title hero → team → newsletter |
| `/faq/` | `page-faq.php` | page-title hero → faq → newsletter |
| `/wishlist/` | `page-wishlist.php` | page-title hero → wishlist → newsletter |
| `/login/` | `page-login.php` | auth section (mode=login) |
| `/register/` | `page-register.php` | auth section (mode=register) |
| `/coming-soon/` | `page-coming-soon.php` | coming-soon (live countdown) |
| 404 | `404.php` | `error/404` component — "Lost in the Void" |

---

## 3. Engine architecture (the 5 view files)

### 3.1 `loader.php` — bootstrapper
Defines `AETHER_FRONTEND_DIR`, then on `after_setup_theme`:
1. Loads tokens, registry, renderer, viewmodel, composer.
2. Globs + requires **all** `adapters/*.php` and **all** `sections/*.php` — new files are auto-discovered, zero registration boilerplate.

### 3.2 `registry.php` — section registry
Sections self-register with `aether_register_section( $id, $args )` where args = `template`, `adapter`, `adapter_args`, `behavior`. Registration is guarded by the render-check pattern inside every section file:

```php
aether_register_section( 'team', array(
    'template' => 'sections/section-team.php',
    'adapter'  => 'adapter-team.php',
    'behavior' => array( 'reveal-group' => true ),
) );
if ( ! isset( $sectionData ) || ! is_array( $sectionData ) ) {
    return; // registration-only pass; rendering happens via aether_render_section()
}
```

### 3.3 `renderer.php` — the single escape boundary
- `aether_render_component( $id, $data )` — looks up the **manifest** (`manifest/components.php`, 45 entries), includes the template with `$componentData` filtered through `aether_component_data`.
- `aether_render_section( $id, $data )`:
  1. Resolves the adapter fn (`adapter-wc-products` → `aether_adapter_wc_products()` — hyphen-safe conversion).
  2. Merges **per-call `$data` wins over registered `adapter_args`** (`wp_parse_args( $data, $registered_args )`).
  3. Flat arrays are auto-wrapped as `items` (cards, posts, terms).
  4. `aether_section_data` filter, then `include` the template.
- `aether_behavior_attrs( $behavior )` — **whitelist-only** attribute emitter: `data-reveal-item`, `data-reveal-group`, `data-tilt`, `data-parallax`, `data-parallax-section`, `data-image-zoom`, `data-motion-text`. Attribute-safe by construction.

### 3.4 `viewmodel.php` — normalization layer
- `aether_viewmodel_image()` — attachment ID/array → `{id, url, alt, sizes}`.
- `aether_viewmodel_resolve_image()` — resolves `frontend/…` relative paths against `content_url()` **before** escaping (fixes the classic `esc_url_raw` → `http://frontend/…` bug).
- `aether_viewmodel_behavior()` — honors the Customizer **motion toggles**: `motion_enabled`, `reveal`, `tilt`, `parallax`, `text` gate which `data-*` attributes survive to the DOM. **This is how the site owner kills animations site-wide from one control.**

### 3.5 `composer.php` — page assembly
`aether_compose_header()` renders, in order: preloader → fog → skip-link → `.page-content` → mobile-chrome → announcement → desktop header → `#main` skip target → `<main id="swup">`. `aether_compose_footer()` closes `</main>`, renders footer, closes `.page-content`.

---

## 4. Adapters — the data layer (21 files)

| Adapter | Feeds | Data source |
|---|---|---|
| `adapter-site` | preloader, footer | site identity, options, menus |
| `adapter-shell` | shell toggles | Customizer options |
| `adapter-menu` | header + footer nav | `wp_nav_menu` / fallback |
| `adapter-hero` | hero slides | `aether_hero_slides` tokens (default 3 slides) |
| `adapter-options` | misc option-driven data | `aureon_get_option()` |
| `adapter-wc-categories` | category cards | WC product categories (fallback tokens) |
| `adapter-wc-products` | bestsellers/shop grid/related | WC `WP_Query` — pagination, on_sale, orderby, related_to |
| `adapter-wc-filter` | shop filter bar | WC terms + on-sale flag |
| `adapter-shop-hero` | shop page hero | `woocommerce_page_title` + taxonomy detection |
| `adapter-product` | product page | WC product + gallery + attributes + reviews |
| `adapter-cart` | cart section | WC cart session |
| `adapter-account` | account data | WC customer |
| `adapter-article` | single post | `the_post()` data |
| `adapter-blog` | blog grid | `WP_Query` posts + pagination |
| `adapter-about` | mission/features/story/stats | tokens (copy) |
| `adapter-faq` | FAQ accordion | `aether_faq` CPT → `aether_faq_items` tokens |
| `adapter-testimonials` | review cards | `aether_testimonial` CPT → tokens |
| `adapter-team` | team cards | `aether_team` CPT → `aether_team_items` tokens |
| `adapter-contact` | contact form | options + wp_mailer contract |
| `adapter-auth` | login/register forms | options, nonces, URLs |
| `adapter-coming-soon` | countdown | `aether_coming_soon_date` token |

**Demo-first philosophy:** adapters try real data first (WC/CPT), and fall back to rich demo arrays from `tokens.php` when the store is empty — so the design is pixel-visible from day one. Real data always wins.

---

## 5. Components — the design layer (45 manifest entries, 48 files)

| Group | Components |
|---|---|
| **Shell (7)** | preloader, fog, skip-link, announcement, header, mobile-chrome, footer |
| **Hero (4)** | slider, slide, page-title, page-banner |
| **Section (6)** | header, filter-bar, accordion, cta, newsletter, pagination |
| **Cards (6)** | product, category, blog, review, team, wishlist |
| **Product (8)** | breadcrumb, gallery, info, sticky-bar, specs, reviews, related, size-guide |
| **Commerce (3)** | rating, cart/items, cart/summary, checkout/order-items |
| **Content (7)** | page, article-hero, article-meta, article-body, author-bio, story |
| **Forms (4)** | contact, login, register, newsletter |
| **Account (1)** | profile |
| **Utility (2)** | error-404, countdown |

All components receive `$componentData`, render **escaped HTML** (`esc_html`, `esc_attr`, `esc_url`), and never touch WP/WC.

---

## 6. Design tokens — everything is Customizer-driven

`frontend/tokens/tokens.php` registers **all** AETHER defaults on the theme settings bucket via `add_filter( 'aureon_option_defaults', 'aether_frontend_defaults' )` — so every consumer reads through `aureon_get_option()` and every value is editable in the Customizer:

| Group | Keys (examples) |
|---|---|
| Motion | `aether_motion_enabled/reveal/tilt/parallax/text` |
| Shell | `aether_preloader_enabled`, `aether_fog_enabled`, `aether_announcement_enabled/text/url` |
| Layout | `aether_container_max` (1200px), `aether_section_padding`, `aether_header_height` (80px), `aether_grid_gap`, `aether_shop_per_page` (9) |
| Radii | `aether_radius_sm/md/lg/pill` (8/12/24/999px) |
| Section visibility (15) | `aether_section_hero/categories/bestsellers/reviews/faq/newsletter/mission/features/story/stats/team/contact/auth/wishlist/coming_soon` |
| Demo content | `aether_hero_slides` (3), `aether_category_items` (4), `aether_product_items` (4), `aether_faq_items` (6), `aether_testimonial_items` (4), `aether_team_items` (4) |
| Product UX | `aether_product_colors`, `aether_product_sizes`, `aether_size_table` (12 rows), `aether_spec_items`, `aether_product_trust`, score/bars/reviews |
| Auth | `aether_google_client_id/secret` (empty = feature hidden) |

---

## 7. Customizer panel — "AETHER Frontend" (priority 120)

`aureon/theme/inc/customizer/fields/frontend.php` adds one section with three control groups:

1. **Section Visibility** — 15 checkboxes that turn whole page sections on/off (live site toggling, no code).
2. **Shell & Motion** — preloader/fog/announcement/search toggles + the 5 motion master-switches (motion, reveal, tilt, parallax, text).
3. **Announcement & Commerce** — announcement text (active_callback-gated), announcement URL, shop products-per-page.

All controls: `transport => refresh`, sanitized (`aureon_sanitize_checkbox`, `sanitize_text_field`, `esc_url_raw`, `absint`), defaults from the shared `$defaults` bucket. **Verified live:** flipping `aether_section_mission` off removed the mission section from `/about/` instantly; flipping back restored it.

---

## 8. Assets & dependency contract

### 8.1 CDN (pinned, per source contract)
| Library | Version | Where |
|---|---|---|
| Bootstrap | 5.3.3 (CSS+JS bundle) | jsdelivr |
| Font Awesome | 6.5.1 | cdnjs |
| Swiper | 11 (CSS+JS) | jsdelivr |
| GSAP + ScrollTrigger | 3.12.5 | cdnjs |
| Lenis smooth scroll | 1.1.18 | unpkg |
| Cabinet Grotesk + Satoshi | Google Fonts | fonts.googleapis.com |

### 8.2 Local (tokenized, versioned by `filemtime`)
| Asset | Size | Purpose |
|---|---|---|
| `css/style.css` | 97 KB | full AETHER design system (tokenized `--aureon-*` vars) |
| `css/motion.css` | 5 KB | animation/transition layer |
| `css/responsive.css` | 32 KB | breakpoint system |
| `css/pages.css` | 16 KB | page-specific compositions |
| `css/a11y.css` | 3 KB | focus + reduced-motion accessibility |
| `js/main.js` | 25 KB | core interactions (swiper init, countup, accordion, cart) |
| `js/animations.js` | 39 KB | GSAP ScrollTrigger animations (reveal, parallax, tilt) |
| `js/lenis-scroll.js` | 1 KB | Lenis smooth-scroll bootstrap |
| `js/phantom-bridge.js` | 2 KB | phantom.js data-attribute bindings |

Enqueue order mirrors the static source contract (bootstrap → FA → swiper → style → motion → responsive → a11y → pages; lenis → animations → main → phantom-bridge). `filemtime` cache-busting means deploys never ship stale CSS/JS.

---

## 9. Premium UI/UX features

### 9.1 Motion & interaction
- **Lenis smooth scroll** — buttery inertia scrolling across the whole site.
- **GSAP ScrollTrigger reveals** — `data-reveal-item` / `data-reveal-group` staged entrances for cards and grids.
- **Hero slider** — Swiper 11 with fog layers, slide counter, progress, particles, scroll indicator, slide transitions.
- **`data-motion-text="words"`** — word-by-word headline animation (35+ usages).
- **Magnetic + tilt + parallax** — `data-tilt` on product cards, `data-parallax-section` on page heroes, `data-image-zoom` gallery zoom.
- **Countup counters** — `data-countup` stats (280g / 40mm / 40% / 10K+).
- **Cinematic fog** — animated fog layers behind heroes (3 `hf-fog` layers per hero).
- **Preloader** — branded load screen that removes itself on completion (source contract).
- **Announcement bar** — rotating marquee messages, height tokenized.

### 9.2 Commerce UX
- Product cards: badges (Sale/New/Featured), ratings, strikethrough old prices, quick Add-to-Cart.
- Product page: gallery with thumbnails + zoom, color/size selectors, sticky purchase bar, accordion specs, review score bars, size-guide modal, related products slider (self-excluded).
- Shop: filter bar with real category terms + on-sale toggle, numbered pagination with window dots, taxonomy-aware heroes.
- Cart: live items + totals from WC session; checkout keeps WC's full flow inside the AETHER shell.

### 9.3 UX / accessibility
- Skip-link → `#main` landmark; visible focus rings (`a11y.css`).
- `visually-hidden-focusable` target, semantic landmarks (banner/main/contentinfo/nav).
- Escaped output everywhere; whitelisted behavior attributes.
- Reduced-motion respected via Customizer master switch + CSS `prefers-reduced-motion`.
- 0 console errors across every route (verified with Playwright).

---

## 10. Security & engineering guardrails

1. **Single escape boundary** — components escape; adapters never echo.
2. **Whitelist attribute emission** — `aether_behavior_attrs()` can only emit known `data-*` names.
3. **No WP calls in components** — enforced by architecture + grep gate (0 hits).
4. **Sanitized Customizer values** — checkbox/text/URL/int sanitizers on every control.
5. **Path-safe engine** — `AETHER_FRONTEND_DIR` derived from file location, immune to mounting changes.
6. **Adapter name hygiene** — hyphen→underscore conversion so multi-word adapters resolve (`adapter-wc-products` → `aether_adapter_wc_products`).
7. **Demo data isolation** — fallbacks only fire when real content is absent; real data wins.

---

## 11. Content provisioning (how pages were wired live)

Static AETHER template pages were created via `wp_insert_post` with `_wp_page_template` meta:

| Page | ID | Template |
|---|---|---|
| about | 62 | `page-about.php` |
| contact | 63 | `page-contact.php` |
| team | 64 | `page-team.php` |
| faq | 65 | `page-faq.php` |
| wishlist | 66 | `page-wishlist.php` |
| login | 67 | `page-login.php` |
| register | 68 | `page-register.php` |
| coming-soon | 69 | `page-coming-soon.php` |
| blog (posts page) | 70 | `home.php` via `page_for_posts` |
| sample-post | 71 | `single.php` |

Pretty permalinks `/%postname%/` + `flush_rewrite_rules()` enabled.

---

## 12. Live verification matrix (2026-08-07)

| Route | Status | AETHER assets | Notes |
|---|---|---|---|
| `/` | 200 | ✅ | all 6 home sections |
| `/shop/` | 200 | ✅ | filter + grid + pagination |
| `/product/midnight-sneakers/` | 200 | ✅ | full product stack |
| `/blog/` | 200 | ✅ | blog grid |
| `/sample-post/` | 200 | ✅ | article + related |
| `/about/ /contact/ /team/ /faq/ /wishlist/` | 200 | ✅ | gated sections |
| `/login/ /register/` | 200 | ✅ | auth cards |
| `/coming-soon/` | 200 | ✅ | live countdown |
| `/cart/` | 200 | ✅ | empty state |
| `/checkout/` | 302→/cart/ | ✅ | WC empty-cart redirect |
| `/my-account/` | 200 | ✅ | WC login in shell |
| 404 | 404 | ✅ | "Lost in the Void" |

**Playwright:** 0 console errors / 0 warnings on all routes. **PHP lint:** 0 errors (frontend 100+ files, theme 93, plugin 122).

---

## 13. Screenshots

Full-page captures live in [`aureon-doc/screenshots/`](./screenshots/):

| File | Content |
|---|---|
| `screen-01-home.png` | Homepage — hero slider + all sections |
| `screen-02-shop.png` | Shop grid |
| `screen-03-product.png` | Single product |
| `screen-04-blog.png` | Blog index |
| `screen-05-single-post.png` | Article single |
| `screen-06-about.png` | About (mission/features/story/stats/team) |
| `screen-07-contact.png` | Contact |
| `screen-08-team.png` | Team |
| `screen-09-faq.png` | FAQ |
| `screen-10-wishlist.png` | Wishlist |
| `screen-11-login.png` | Login |
| `screen-12-register.png` | Register |
| `screen-13-coming-soon.png` | Coming soon + countdown |
| `screen-14-cart.png` | Cart |
| `screen-15-checkout.png` | Checkout |
| `screen-16-account.png` | My account |
| `screen-17-404.png` | 404 "Lost in the Void" |
| `sec-01-hero.png` → `sec-12-related.png` | Individual section close-ups (home 6 + product 6) |

---

## 14. Deploying the frontend

`frontend/` is **not** bind-mounted in Docker — it ships to `wp-content/frontend/` inside the container:

```powershell
# 1. Create a POSIX-safe tarball (bsdtar — forward slashes, NOT Compress-Archive)
tar.exe -czf C:\Users\hamma\AppData\Local\Temp\opencode\frontend.tar.gz -C C:\Users\hamma\Downloads\wordpress --exclude "frontend/source" frontend

# 2. Copy into the container and extract in place
docker cp C:\Users\hamma\AppData\Local\Temp\opencode\frontend.tar.gz aureon_wp:/tmp/frontend.tar.gz
docker exec aureon_wp sh -c 'cd /var/www/html/wp-content && tar -xzf /tmp/frontend.tar.gz && rm /tmp/frontend.tar.gz'

# 3. Lint the staged copy inside the container (PHP 8.3 parity)
# 4. Swap: mv old frontend → backup, mv new → wp-content/frontend
# (SCHILY.fflags warnings from tar are harmless)
```

PowerShell notes: never `Compress-Archive` (literal `\` path separators become junk filenames), never pipe large files through stdin (truncation), and keep the tarball under ~8 MB. Theme + plugin are bind-mounted and edit live — only `frontend/` needs this dance.

---

## 15. Open items / roadmap

| # | Item | Impact |
|---|---|---|
| 1 | Fonts (Cabinet Grotesk / Satoshi) not downloaded locally — Google Fonts CDN used; `frontend/assets/fonts/` empty | Visual only, network-dependent |
| 2 | `/checkout/` + `/my-account/` use WooCommerce stock forms inside the AETHER shell (no custom checkout/account templates yet) | Cosmetic |
| 3 | `mu-plugins/aureon-fix-wc-session.php` needs recreation in container (WC session null warning on REST) | Warning only |
| 4 | Plugin `template-locator.php` targets a missing `<plugin>/templates/` dir — inert, WC falls back correctly | None |
| 5 | Images in demo tokens use one sneaker photo — site owner swaps via Customizer/`_thumbnail_id` | Content polish |

---

*Generated from the live Docker deployment — every claim in this document was verified against `http://localhost:8080` on 2026-08-07.*
