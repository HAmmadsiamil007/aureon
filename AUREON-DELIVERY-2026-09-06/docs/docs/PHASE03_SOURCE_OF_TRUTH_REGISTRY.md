# Phase 3 — Static Audit: Source-of-Truth Registry

> **Date:** 2026-08-14. Master verification task Phase 3 of 16.
> **Method:** for every surface, trace where each piece of rendered data comes from: (a) Customizer option (`aureon_settings` bucket via `aureon_get_option`), (b) real WP/WC data, (c) adapter fallback/demo content (gated by `aether_demo_content`), (d) hardcoded adapter/template strings.
> **Master toggle:** `aether_demo_content` (default `true`, tokens.php:202) — gates ALL demo fallbacks (Phase D closure).

---

## 1. Source-of-truth classes

| Class | Definition | Gate |
|---|---|---|
| **A. Customizer option** | `aureon_get_option('aether_*', default)` — editable in Customizer, default from tokens.php | n/a |
| **B. Real WP/WC data** | posts/products/terms/cart/order/user meta | n/a |
| **C. Demo fallback** | adapter falls back to token array when real data empty | `aether_demo_content` (must gate) |
| **D. Hardcoded** | static strings in adapter/template, no option read | n/a |

## 2. Per-surface source-of-truth

### Shell (S1)

| Data | Source | Class |
|---|---|---|
| Preloader brand/logo | `aether_adapter_site` → `get_bloginfo` | B |
| Announcement text/url/height | `aether_announcement_text/url` (+ `_enabled`, `_height`) | A |
| Announcement marquee items | `aether_announcement_items` (3 items) | A |
| Header nav | `aether_adapter_header` → `aether_adapter_menu` → `wp_get_nav_menu_items` (real menu, fallback demo links) | B (+C if no menu assigned) |
| Header cart count/badge | `WC()->cart` count | B |
| Mobile chrome links | `aether_adapter_mobile` → site/account/cart URLs | B |
| Footer columns | `aether_footer_columns` (3 cols × links, token defaults) | A |
| Footer socials | `aether_adapter_socials` → `get_theme_mod` socials | A |
| Footer copyright | `get_bloginfo` + year | B |

### Home (S2)

| Section | Data | Source | Class |
|---|---|---|---|
| hero | slides | `aether_hero_slides` (Customizer repeater, JSON string or array) | A |
| hero CTA URLs | shop archive when empty | `wc_get_page_permalink('shop')` | B |
| categories | terms | `get_terms(product_cat)` real → **fallback** `aether_get_fallback_categories()` (curated 4, SKU-based images) | B → C ✅gated |
| categories header | label/title/subtitle | `aether_categories_label/title/subtitle` | A |
| bestsellers | products | `WC_Product_Query` (total_sales desc, 4) → **fallback** `aether_product_items` | B → C ✅gated |
| reviews | reviews | `aether_testimonial` CPT → **fallback** `aether_testimonial_items` | B → C ✅gated |
| reviews score/count | `aether_reviews_score` 4.9 / `aether_reviews_count` 312 | A (not real WC review data) ⚠ |
| faq | items | `aether_faq` CPT → **fallback** `aether_faq_items` | B → C ✅gated |
| newsletter | copy | `aether_newsletter_text/subtitle` | A |

### Shop (S3)

| Data | Source | Class |
|---|---|---|
| hero title | `woocommerce_page_title` / cat/tag title | B |
| hero label/subtitle | hardcoded "Collection" / "Six colorways…" | D |
| filter buttons | real `product_cat` terms + Sale (real `wc_get_product_ids_on_sale`) | B |
| grid items | `WC_Product_Query` (per-call paged/tax_query/on_sale/orderby_shop) → fallback `aether_product_items` | B → C ✅gated |
| pagination | `$query->max_num_pages` | B |
| shop per page | `aether_shop_per_page` (9) | A |

### Product (S4)

| Data | Source | Class |
|---|---|---|
| gallery | WC gallery images → featured x4 | B |
| breadcrumb | real post terms (Uncategorized) | B |
| price/rating | `$product->get_price_html()`, `get_average_rating()` | B |
| rating fallback | `aether_product_score` 4.8 / `aether_product_score_count` 128 | A |
| colors/sizes | `pa_color`/`pa_size` terms → **fallback** `aether_product_colors/sizes` tokens | B → C ✅gated |
| specs | visible attributes → **fallback** `aether_spec_items` | B → C ✅gated |
| reviews | real WC review comments (`get_rating_counts`) → **fallback** `aether_product_reviews` | B → C ✅gated |
| trust badges | `aether_product_trust` (3) | A |
| size guide | `aether_size_table` (12 rows) | A |
| related | `wc_get_related_products(pid, 4)` | B |

### Cart / Checkout / Order (S5–S7)

| Data | Source | Class |
|---|---|---|
| cart items/totals | `WC()->cart` real | B |
| empty-state copy | hardcoded "Your cart is empty" | D |
| checkout form | stock WC `form-checkout.php` output inside `.checkout-form-wrap` | B |
| order items summary | `aether_adapter_cart` (context=checkout) | B |
| thankyou order number | `get_query_var('order-received')` → `wc_get_order` | B |
| thankyou copy | hardcoded "Order Confirmed" etc. | D |

### My Account (S8)

| Data | Source | Class |
|---|---|---|
| dashboard profile | `aether_adapter_account` → WC customer | B |
| orders | `aether_adapter_account_orders` → `wc_get_orders` | B |
| other endpoints | stock `woocommerce_account_content` in `.aether-wc` frame | B |
| login/register forms | stock WC forms + nonces (template lines 133–180) | B |

### Blog (S9–S13)

| Data | Source | Class |
|---|---|---|
| blog-grid items | `WP_Query` (real posts, per-call s/category_name/post__not_in) | B |
| pagination | `$query->max_num_pages` | B |
| article body | `the_content` filtered, real post | B |
| page-title heroes (home/search/archive/index) | hardcoded i18n strings in templates ("Journal", "Results for…") | D |

### Static pages (S14–S23)

| Surface | Data | Source | Class |
|---|---|---|---|
| 404 | copy | hardcoded "Lost in the Void" | D |
| generic page | content | `get_the_content()` | B |
| about: mission/features/story/values/stats | **hardcoded in `adapter-about.php`** (no option read, no token) | **D ⚠** |
| contact fields | hardcoded field schema in `adapter-contact.php` | D |
| contact address/hours | `aether_contact_address` / `aether_contact_hours` | A |
| contact email | `get_option('admin_email')` | B |
| team | `aether_team` CPT → fallback `aether_team_items` | B → C ✅gated |
| values (team page) | hardcoded in adapter-about | **D ⚠** |
| faq page | same as home faq | B → C ✅gated |
| wishlist | user meta `aether_wishlist` → WC products | B |
| auth forms | WC options (`woocommerce_enable_myaccount_registration`), nonces, Google OAuth tokens (empty = hidden) | A/B |
| coming-soon | **hardcoded** title/subtitle/brand + `strtotime('+14 days')` target | **D ⚠** |

## 3. Findings

| # | Severity | Finding |
|---|---|---|
| F3-1 | **MED** | **About-page content (mission/features/story/stats/values) is hardcoded in `adapter-about.php`** — it is NOT option-driven, NOT token-driven, and NOT gated by `aether_demo_content`. It renders "demo" copy unconditionally, indistinguishable from production copy. Violates Phase-D rule ("`aether_demo_content` gates all fallbacks") and G-series settings-binding intent. **Fix direction:** move copy to tokens (`aether_about_*`) + read via `aureon_get_option`, and gate with `aether_demo_content` (or accept as static brand copy — but then it must not be labeled demo). |
| F3-2 | **MED** | **Coming-soon copy hardcoded** (title/subtitle) + target date computed server-side as `+14 days` every request — not a fixed launch date, drifts with every load. **Fix direction:** tokenize (`aether_coming_soon_*` incl. `target`), gate by `aether_demo_content`, Customizer fields. |
| F3-3 | **LOW** | `aether_reviews_score/count` are **option-driven demo values (4.9/312), not real WC aggregate review data** — home reviews section summary never reflects real reviews even when real testimonials exist (different data sources: testimonials CPT vs WC reviews). If real WC reviews should drive the score, the adapter must aggregate `get_comments(type=review)`; otherwise the token default should be flagged demo. |
| F3-4 | **LOW** | Contact form **field schema hardcoded** (name/email/subject/message) — labels/options not editable. Acceptable (form contract), noted. |
| F3-5 | **LOW** | Blog/static page heroes use **hardcoded i18n strings in templates** — consistent across surfaces (single source: the template), but not Customizer-editable. Intentional static copy; acceptable. |
| F3-6 | **INFO** | `aether_demo_content` gate verified present at **all 7 fallback sites**: wc-products (123), wc-categories (51), testimonials (37), team (35), faq (34), product (49/145/163). Zero un-gated fallbacks found — **except** F3-1/F3-2 which are hardcoded rather than fallback-shaped. |
| F3-7 | **INFO** | All A-class options live in the `aureon_settings` bucket (not standalone rows) — Customizer round-trip is via the theme settings API; verified in Phase 4. |

## 4. Coverage summary

| Class | Surfaces using it |
|---|---|
| A (Customizer) | shell, home (hero/categories/bestsellers header/reviews score/newsletter), shop (per-page), product (trust/size-guide/fallbacks), contact, footer |
| B (real data) | all commerce surfaces, blog, wishlist, auth, shell menus |
| C (gated fallback) | categories, bestsellers, reviews items, faq, team, product (rating/colors/sizes/specs/reviews) |
| D (hardcoded) | shop-hero label/subtitle, cart empty copy, thankyou copy, blog heroes, 404, **about (F3-1)**, **coming-soon (F3-2)**, contact fields |

**CSP-deferred items:** none in this phase (CSP = Phase 14).

---
**Phase 3 complete.** Next: Phase 4 — Customizer round-trip audit.