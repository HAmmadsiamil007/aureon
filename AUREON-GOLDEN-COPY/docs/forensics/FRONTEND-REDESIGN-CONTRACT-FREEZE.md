# FRONTEND REDESIGN — DYNAMIC CONTRACT FREEZE

**Date:** 2026-09-04 · Stage 4 of the redesign prompt
**Rule:** *Do not redesign a component until its contract is documented.* This document is that contract. Sources: `composer.php` (emit), `js/vineta-data-shims.js` + `js/vineta-path-bridge.js` (consumers), `manifest.json` (assets), current templates.

## 1. DATA PIPELINE (unchanged by redesign)

```
WP/WC/Customizer ── composer.php ──> VinetaPageData (window/pageData) ── vineta-data-shims.js ──> frozen Vineta DOM
manifest.json ──> page-gated CSS/JS (styles.css then monochrome-black.css LAST)
```

The redesign edits the **frozen Vineta DOM + CSS** and must leave the pipeline and every selector below valid.

## 2. GLOBAL RULES

1. Keep every class listed in §4 present on its element (renaming = bridge update).
2. Keep IDs: `#shoppingCart`, `#search`, `#login`, `#register`, `#resetPass`, offcanvas ids used by popups.
3. Keep form `name`/`action`/method + hidden nonce fields byte-meaningful (cart, login, register, newsletter, search).
4. Keep WooCommerce data attributes (`data-product_id`, variation attrs) and links to WP/WC endpoints.
5. Keep `VinetaPageData` slot markers and `<base href>` + raw body script architecture (asset/script hardening).
6. New markup may use new classes freely; old classes must survive wherever the bridge still reads them.

## 3. COMPONENT CONTRACTS (frozen)

| Component | Frozen contract |
|---|---|
| Header / masthead | WP `primary` menu items + hierarchy + URLs; utility icons (search `#search`, account, cart badge `.nav-cart .count-box` / `.count-box`); logo (Customizer `custom_logo`); ARIA on nav toggles |
| Mobile menu | client-side rebuild from desktop WP menu DOM; children + URLs preserved |
| Announcement bar | text from Customizer `aether_announcement*`; 2 items rendered into topbar |
| Hero (home) | slides from Customizer `aether_hero_slides`: image(s) `.image img`, single visible `<h1>` heading, `.sub`/`.subheading`, CTA + URL; `.swiper-wrapper` slides |
| Product card (shop/home/blog fallback) | `.card-product`; title, `.new-price`/`.price-new`, `.old-price`/`.price-old`, `.badge-sale`/`.on-sale` sale state, image, product URL, add-to-cart form/nonce; real WC data only |
| Category card | image + name from `product_cat` terms; `.on-sale-wrap`/`.on-sale-item` slots where used |
| Product page | `.tf-product-info-*` info slots: title, price (CHF via WC symbol), sale badge, SKU, stock, description, gallery, `.info-variant` (variation: `.list-color-product`, `.size-box` etc.), qty + add-to-cart form + nonce, related products |
| Cart drawer | `#shoppingCart`; `.tf-mini-cart-items .tf-mini-cart-item`; `.tf-totals-total-value`; qty `.plus-btn/.minus-btn`; remove; empty state |
| Cart page | `.table-page-cart tbody .tf-cart-item`; `.cart-head .subtotal`; totals `.checkout-cart-box`/`.tf-page-cart-total`; update/remove forms + nonces; coupon if present |
| Checkout | WC-native fields/actions/nonces; billing/shipping/payment markup; order/thank-you flow untouched (server-rendered) |
| Auth | login/register/lost-password forms in `#login`/`#register`/`#resetPass` popups + account-page clones; real WC endpoints + nonces; error/success notices container |
| Account dashboard | `.s-term-user .content` region fed real WP page/WC content by `VinetaPage` |
| Search | `#search` modal form → real WP search; results page from `?s=` |
| Blog | `.entry_image/.entry_name/.entry_date/.entry_comment` etc.; real WP post data; single post content |
| Static pages | generic `.s-term-user .content` region for content-bearing WP pages |
| Footer | WP `footer` menu; newsletter `.form-newsletter` (heading from Customizer bucket, real submit); social links; `.footer-col-block` fallback chain |
| 404 | genuine HTTP 404 page with search + home links ("Whoops!") |
| Modals/popups | `.modal`/`.offcanvas-*` behavior incl. `cursor-close.svg`; ARIA dialog labelling |

## 4. PROTECTED SELECTOR VOCABULARY (extracted from bridge consumers)

```
#shoppingCart  #shoppingCart .tf-mini-cart-items  .tf-mini-cart-item  .tf-totals-total-value
.table-page-cart tbody .tf-cart-item  .cart-head .subtotal
.card-product  .badge-sale .on-sale  .new-price .old-price .price-new .price-old
.on-sale-wrap .on-sale-item  .slider-badge .badge .box-badge .chip-badge .eyebrow .box-overline
.image img .img-style img  .slider-image img .img-slider img
.name  .sub .subheading .subtitle .description .text-sub
.s-term-user .content  .info-variant  .list-color-product .list-color  .size-box .list-size-product
.plus-btn .minus-btn .btn-increase .btn-decrease .remove .remove-cart .icon-close
.footer-col-block .footer-col .footer-column .footer-heading .form-newsletter
.entry_tag .entry_image .entry_name .entry_date .entry_comment .entry_sub .entry_excerpt .entry_author
.closest() fallback chains: (.card-product, .tf-mini-cart-item, article, .swiper-slide, .tf-product-media)
```

## 5. CUSTOMIZER MAPPINGS (must keep resolving)

| Control | Reader → frontend target |
|---|---|
| `custom_logo` / `site_icon` | header logo / favicon |
| `aether_color_*` | CSS vars `--primary` etc. → computed styles |
| typography options | Playfair Display / Albert Sans font families |
| `aether_search_placeholder` | search field placeholder |
| `aether_hero_slides` | hero slides (heading/sub/CTA/image) |
| announcement / newsletter / social / footer | topbar, newsletter heading, footer blocks |

## 6. VERDICT

All planned redesign changes are classified **FRONTEND_ONLY** (see PLAN doc). Any DOM removal/rename must first be checked against this vocabulary; if a selector dies, the mapping must be updated in `vineta-data-shims.js` (bridge update) and re-tested — the default is to keep the vocabulary intact.
