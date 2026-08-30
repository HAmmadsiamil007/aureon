# COMPONENT_INVENTORY

**Phase:** 17 — Frontend Integration Framework (Step 3: Component Extraction)
**Date:** 2026-08-06
**Status:** Complete — inventory extracted from static frontend (22 pages)

---

## 1. Component Taxonomy

Every UI component extracted from the AETHER frontend is classified into one of 12 families. Each component entry records: ID, source page(s), HTML skeleton (class hooks), `data-phantom` keys it renders, behavioral attributes (`data-*` animation hooks), and CSS anchor.

**Naming convention:** `aether/{family}/{name}` — components are framework-agnostic templates that receive `$componentData`.

---

## 2. Component Register

### 2.1 Shell & Chrome (site-level)

| ID | Component | Source | CSS Anchor | Phantom Keys |
|---|---|---|---|---|
| shell/preloader | Preloader (logo pulse + bar) | all | `#preloader` | — |
| shell/fog | 3-layer animated fog system | all | `#fog-system .fog-layer` | — |
| shell/skip-link | Accessibility skip link | all | `.skip-to-content` | — |
| shell/mobile-header | Mobile top bar + hamburger | all | `.mobile-header` | — |
| shell/mobile-menu | Off-canvas menu overlay | all | `.mobile-menu-overlay` | — |
| shell/announcement | Announcement bar | all | `.announcement-bar` | — |
| shell/header | Sticky nav (logo, menu, actions) | all | `.header` | `data-phantom-menu` (36×) |
| shell/footer | Footer (widgets, newsletter, social) | all | `.footer` | — |
| shell/back-to-top | Back to top button | all | `.back-to-top` | — |

### 2.2 Navigation

| ID | Component | Source | Phantom Keys |
|---|---|---|---|
| nav/menu | Primary dropdown menu | all | `data-phantom-menu` |
| nav/search | Search modal | all | `.search-modal` |
| nav/mini-cart | Mini cart drawer | all | `.mini-cart` |

### 2.3 Hero & Intro

| ID | Component | Source | Notes |
|---|---|---|---|
| hero/slider | Full-screen slider w/ fade + ken-burns | index | Swiper; `data-mouse-parallax`, `data-parallax-section` |
| hero/slide | Single slide (kicker, h1, cta, img) | index | `hero_headline` ×3, `hero_subline` ×3 |
| hero/page-title | Page title band | 16 pages | `page_title` ×17, `page_description` ×8 |

### 2.4 Section Header

| ID | Component | Source | Notes |
|---|---|---|---|
| section/header | Label + title + subtitle + optional CTA | 8 pages | `section_label` ×31, `section_title` ×26, `section_subtitle` ×19 |

### 2.5 Product & Commerce Cards

| ID | Component | Source | Notes |
|---|---|---|---|
| card/product | Product card (img, badge, name, price, hover actions) | shop, index, wishlist | `product` ×6, `product_name` ×6, `product_price` ×7, `product_badge` |
| card/product-slider | Product carousel card | index | Swiper `data-phantom-products` ×2 |
| card/category | Category tile | index | `category_count` ×4 |
| card/cart-item | Cart line item | cart | `cart_item`, `cart_product_name`, `cart_product_price`, `cart_product_variant`, `cart_product_total` |
| card/order-item | Order summary line | checkout, thank-you | `order_item` ×2, `order_product_name` ×2, `order_product_price` ×2, `order_product_variant` ×2 |

### 2.6 Blog & Content

| ID | Component | Source | Notes |
|---|---|---|---|
| card/blog-card | Blog post card | blog | `blog_post` ×9, `blog_title` ×9, `blog_excerpt` ×9, `blog_date` ×9, `blog_category` ×10 |
| article/meta | Post meta row | single-blog | `article_meta`, `article_author`, `article_date`, `article_read_time` |
| article/quote | Blockquote pull | single-blog | `article_quote` |
| article/body | Post body container | single-blog | `article_body`, `page_content` |
| content/page | Legal/page content | 3 legal pages | `page_content` ×3, `effective_date` ×3 |

### 2.7 Testimonials & Social Proof

| ID | Component | Source | Notes |
|---|---|---|---|
| card/review | Review card (stars, text, author) | testimonials, index, product | `rating_score`, `reviews_score`, `rating_count`, `rating_overview`, `review_cta*` |
| card/team | Team member card | team | `team_member` ×6, `team_name` ×6, `team_role` ×6, `team_bio` ×6 |

### 2.8 Commerce Flows

| ID | Component | Source | Notes |
|---|---|---|---|
| cart/table | Cart table | cart | — |
| checkout/form | Checkout form layout | checkout | `.checkout-form-wrap` |
| checkout/summary | Order summary box | checkout | — |
| order/success | Thank-you confirmation | thank-you | `order_number`, `order_delivery_note`, `order_email_note` |
| product/gallery | Product image gallery + zoom | product-detail | `data-image-zoom` ×27 |
| product/meta | Variant/size/color selector | product-detail | `sticky_product_name`, `sticky_product_price` |
| product/qty | Quantity stepper | product-detail, cart | — |
| product/tabs | Description/Reviews tabs | product-detail | `product_description` |
| product/rating | Rating breakdown | product-detail | `rating_score`, `rating_count` |

### 2.9 Forms

| ID | Component | Source | IDs (unique) |
|---|---|---|---|
| form/newsletter | Email capture | footer × all + home | `newsletterForm`, `footerNewsletterForm` |
| form/contact | Contact form | contact | `contactForm` |
| form/notify | Coming-soon notify | coming-soon | `notifyForm` |
| form/login | Login form | login | — |
| form/register | Register + strength bar | join-now | `forgotForm` |
| form/fields | Field primitives (`.form-input`, `.form-label`, `.form-group`) | all | — |

### 2.10 Content Blocks

| ID | Component | Source | Notes |
|---|---|---|---|
| block/accordion | FAQ accordion | faq, index | Bootstrap accordion |
| block/story | Brand story split | about | `story_quote` |
| block/brand-logos | Logo strip | index, about | `brand_logo` |
| block/cta | CTA panel | index | `faq_cta*`, `review_cta*` |
| block/breadcrumb | Breadcrumb trail | 12 pages | — |

### 2.11 Errors & Utility

| ID | Component | Source | Notes |
|---|---|---|---|
| error/404 | Error display | 404 | `error_code` |
| soon/countdown | Coming-soon countdown | coming-soon | — |

### 2.12 State & Decoration (behavior-only)

| ID | Component | Count | Source |
|---|---|---|---|
| fx/tilt | `data-tilt` hover tilt | 38 | all |
| fx/reveal | `data-reveal-item` scroll reveal | 20 | all |
| fx/parallax | `data-parallax-section` / `data-parallax` | 16 + 3 | home, about |
| fx/motion-text | `data-motion-text` text animation | 35 | all |
| fx/zoom | `data-image-zoom` product zoom | 27 | product |
| fx/group | `data-reveal-group` grouped reveal | 8 | all |

---

## 3. Data Contract Census (full `data-phantom` key list)

| Key | Count | Family |
|---|---|---|
| section_label | 31 | section/header |
| section_title | 26 | section/header |
| section_subtitle | 19 | section/header |
| page_title | 17 | hero/page-title |
| blog_category | 10 | card/blog-card |
| blog_post / blog_title / blog_excerpt / blog_date | 9 each | card/blog-card |
| page_description | 8 | hero/page-title |
| product_name / product_price | 6 / 7 | card/product |
| product / team_member / team_name / team_role / team_bio | 6 each | card/product, card/team |
| category_count | 4 | card/category |
| hero_headline / hero_subline | 3 each | hero/slide |
| page_content / effective_date | 3 each | content/page |
| order_item / order_product_* | 2 each | card/order-item |
| blog_* (meta) | misc | article/meta |
| article_* | 1 each | article/* |
| cart_* | 1 each | card/cart-item |
| order_number, order_delivery_note, order_email_note | 1 each | order/success |
| error_code | 1 | error/404 |
| login_form / register_form / notify_form / notify_button | 1 each | form/* |
| faq_cta / faq_cta_button / faq_cta_text / review_cta* | 1-2 each | block/cta |
| rating_score / rating_count / rating_overview / reviews_score | 1 each | card/review |
| brand_logo / story_quote | 1 each | block/* |
| sticky_product_name / sticky_product_price | 1 each | product/meta |
| author_* | 3 | article/* |
| email_placeholder | 1 | form/newsletter |
| **`data-phantom` (attributeless hooks)** | ~8 | fx/* (behavior) |

**Total unique keys: 62** (server-injectable contract points).

---

## 4. Formatter Requirements (Renderer Contract)

Each component accepts a normalized array:

```php
// Component data contract (all keys optional)
$componentData = array(
    'id'       => 'card/product',
    'data'     => array( /* phantom keys from register */ ),
    'attrs'    => array( 'class' => 'product-card', 'data-phantom-products' => 'products' ),
    'behavior' => array( 'tilt' => true, 'reveal' => true, 'motion-text' => 'words' ),
);
```

- Components output **escaped HTML** (esc_html/esc_attr/esc_url).
- Components do NOT call `get_post_meta`, `wc_get_product`, `get_option` — adapters provide data.
- Components DO accept `wp_nonce_field`/`wp_nonce_url` fragments via data array when used in forms.

---

## 5. Extraction Priority (build order)

| Order | Family | Rationale |
|---|---|---|
| 1 | shell/* | Site chrome on all 22 pages |
| 2 | section/header, hero/* | Homepage above-the-fold |
| 3 | card/* | Reused on shop/index/wishlist/blog |
| 4 | form/* | Auth + newsletter + checkout hooks |
| 5 | commerce flows | WooCommerce adapter surface |
| 6 | fx/* | Animation bridge layer |
| 7 | article/content | Blog + legal |

---

## 6. Dead / Excluded Code (do NOT extract)

- `vendor/` 19 files (jQuery plugins never referenced)
- `effects.js`, `phantom-dark-mode.js`, `three-scenes.js`
- `owl.carousel.*`, `animate.css`, `blog.css`, `shop.css` (vendor styles)
- `bootstrap.min.css` (WP enqueue replaces with core CSS or bundle decision)
- `contact-form.php` (legacy, not WP-compatible)
- `*.reference` files (in source import, excluded from WP)