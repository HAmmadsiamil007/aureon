# AUREON Design-Pack Contract — Phase D Client Visual Workflow

**Status:** Active contract · **Core state:** FROZEN (see `AUREON-PHASE-C-RELEASE-GATE.md` §8)
**Audience:** Designers and developers building client designs on the AUREON platform.

This document is the single source of truth for what a design pack may change, what it
consumes, and how it is verified. Every field/API named here was extracted from the
current runtime code — nothing is aspirational.

---

## 1. The boundary

```text
FROZEN — change only for a proven core defect, with regression evidence:
  themes/aureon/                 WordPress + WooCommerce integration, routing, complete-page rendering
  plugins/aureon-studio/         plugin runtime, snippet registry, module system
  frontend/views/assets.php      manifest-driven asset enqueuer
  frontend/tokens/tokens.php     engine defaults + repeater schema infra
  frontend/adapters/*.php        data adapters (engine → pack-neutral shape)
  themes/aureon/ferm-page.php    complete-page template host
  Contracts: route manifest, asset manifest, aureon_settings bucket,
             aether_repeater_schemas, AJAX/REST endpoints, pageData shape

EDITABLE — the design layer (all client visual work happens here):
  frontend/designs/<pack>/       frozen HTML templates
    ├── index.html, shop-default.html, product-detail.html, ... (frozen pages)
    ├── styles.css               pack design system
    ├── js/vineta-data-shims.js  pack-level bridge (pack-owned, see §6)
    ├── js/main.js, vendor/      pack-owned frontend logic + libraries
    ├── composer.php             pack adapter: filters engine data → pack shape,
    │                            registers pack Customizer sections
    ├── tokens.php               pack Customizer defaults
    ├── manifest.json            routes + asset authority
    ├── demo/                    demo JSON (never silently overrides real data)
    └── images/, fonts/          visual assets
```

Enforcement: `php scripts/check-core-freeze.php` compares checksums of every frozen
file against `docs/core-freeze-manifest.json`. Exit 1 = the core was modified.
Run it in CI / before every release.

## 2. Route contract (manifest.json is the only route authority)

A pack declares its complete pages in `manifest.json`. The frozen resolver maps every
WordPress route class to exactly one manifest page:

| Route class | WP condition | Vineta page |
|---|---|---|
| home | `is_front_page()` | `index.html` |
| shop | `is_shop()` / product archive | `shop-default.html` |
| category | `is_product_category()` | `shop-default.html` (+collection data) |
| product | `is_singular('product')` | `product-detail.html` |
| search | `is_search()` | `shop-default.html` (+search data) |
| blog_archive | `is_home()` / archive | `blogs-default.html` |
| blog_single | `is_singular('post')` | `blog-single.html` |
| static | `is_page()` via manifest `pages.static` | e.g. `about-us.html`, `contact-us.html` |
| cart / checkout / account | WooCommerce native templates | (WC renders; pack CSS/JS still applies) |
| 404 | `is_404()` | manifest 404 entry |

Rules: every manifest entry must exist (enforced by `scripts/check-assets.php`);
no legacy fallbacks; a 200 must never mean "wrong page".

## 3. Asset contract

`manifest.json` is the single asset authority. The frozen enqueuer
(`frontend/views/assets.php`) loads manifest CSS/JS with page gates:

- Global: loads on every pack page.
- Page-gated: `"pages": ["product-detail"]` style declarations load only there
  (photoswipe/drift/zoom → product; nouislider/shop.js → shop/collection;
  jquery-validate → contact).
- jQuery: the pack does NOT ship its own copy. WordPress core jQuery is the only one.
- Never hardcode `<script>`/`<link>` for something the manifest already declares.

## 4. Data contracts (what a design may consume)

### 4.1 `window.VinetaPageData` (server-injected per page)

```js
{
  cart:       { item_count: Number },
  is_home:    Boolean,
  site:       { url },                                   // via vineta_bridge/config
  contact:    { address: String[], hours, email, phone },
  search:     { placeholder: String, suggestions: String[] },
  product:    { ...single product, see 4.3 },            // product pages only
  chrome:     { products: ProductCard[] },               // header/cart drawer cross-sell
  home:       { products: ProductCard[], categories: [...] },
  collection: { products: ProductCard[], filters... },
  blog:       { posts: BlogPost[] },
  article:    BlogPost,                                  // single post
  page:       { id, title, content },                    // static pages
  customizer: {
    site:         { name, description, logo_url },
    announcement: [{ id, visible: Bool, text }],
    hero:         [{ id, headline, accent, subline, badge, image, mobile_image, tablet_image, cta... }],
    categories:   CategoryItem[],
    footer:       [{ title, links: [{ url, title }] }],
    newsletter:   { heading, text },
    social:       [{ id, label, url }]
  }
}
```

### 4.2 ProductCard (grids, carousels, related, chrome)

Produced by the frozen WC adapter + `vineta_remap_product()` (pack composer):

```js
{
  id: Number, name: String,
  price: String /*WC html*/, price_plain: String, price_cents: Number,
  old_price_plain: String,            // compare-at price ('' when not on sale)
  tagline: String, badge: String,
  rating: Number /*0-5*/, reviews: Number,
  image: String /*absolute*/, alt: String, gallery: String[],
  url: String /*absolute permalink*/,
  add_to_cart_url: String,
  product_type: String,               // 'simple' | 'variable' | ...
  behavior: { tilt: Boolean }
}
```

### 4.3 Single product (`pageData.product`)

```js
{
  id, name, sku, price, regular_price, sale_price,
  description, short_description,
  image, gallery: String[], permalink, add_to_cart_url,
  in_stock: Boolean, stock_quantity: Number|null,
  weight, dimensions, is_variable: Boolean,
  variation_attributes: {...}, variations: [{ id, attributes, price, regular_price, sku, in_stock, image }],
  categories: String[], tags: String[],
  review_count: Number, average_rating: Number,
  related: ProductCard[]
}
```

### 4.4 Frozen HTML slots (`data-aureon-slot="global.*"`)

The shims replace slot innerHTML from `pageData`. A design must keep the slot
attributes on the elements it wants driven by data (server-side rendering strips
the attributes at output but the shims consume them pre-rewrite):

`global.logo, global.site_name, global.navigation, global.announcement, global.search,
global.cart, global.account, global.social, global.newsletter, global.footer,
global.featured_categories, global.featured_products, global.categories_tabs,
global.cart_recommendations, global.picks_products, global.quickadd_product,
global.quickview_product, global.compare_products, global.wishlist, global.search_products`

### 4.5 Add-to-cart binding (the commerce contract)

```html
<a href="..." data-vineta-add="1" data-product-id="643">Add</a>
```
Click → `VinetaCart.add(productId, qty)` → AJAX `vineta_cart_add` (nonce from
`window.vineta_bridge`) → badge `.nav-cart .count-box` + drawer update. A redesigned
card keeps this attribute contract; any JS-only call may use `VinetaCart.add(id, n)`.

## 5. Customizer contract (merchant-facing settings)

A pack registers merchant settings in `composer.php` via `Aureon_Customize_Field::add_field()`
into the `aureon_settings` option bucket, reading them back with `vineta_get_customizer_value()`.
Repeaters require a schema registered on the shared `aether_repeater_schemas` filter
(registered: `hero`, `social`, `announcement`, `category`) so
`aureon_sanitize_repeater()` whitelists keys at save time (types: text, textarea, url,
image, checkbox, color, cta).

Existing pack sections: **Vineta — Hero Banner**, **Vineta — Colors**, **Vineta — Contact & Social**,
**Vineta Content**, **Vineta Fonts & Demo**. A new pack defines its own equivalents.
Gate: `aether_active_design() === '<pack>'`.

Contract proof for any new setting: reader + consumer must exist —
`php scripts/check-customizer-contracts.php` must report **DEAD 0 / ORPHANED 0**
for the whole platform.

## 6. JS bridge contract (shims)

The pack owns `js/vineta-data-shims.js`. Its obligations:

- Read config from `window.vineta_bridge` (ajax_url, nonce) — never hardcode admin-ajax.
- Export `window.VinetaCart` (`add/update/get`, `updateCount`), `window.VinetaCartUI`,
  `window.VinetaNav`, `window.VinetaShop`, `window.VinetaHome`, `window.VinetaCustomizer`.
- Tolerate missing slots/selectors on every page (defensive checks; no selector may throw).
- Update badges/counters through targeted DOM updates, not page rewrites.
- jQuery is NOT bundled; WP core jQuery loads first (pack scripts run in body).

## 7. Demo-data rules

- Demo JSON (`demo/demo-products.json`) renders only on an empty store or when
  `aether_demo_mode`/`aether_demo_content` allow it (auto / force_demo / disabled).
- Real WooCommerce/WordPress data always wins. Never hardcode business content
  (emails, legal URLs, phone numbers, suggestions) into templates — use settings.

## 8. Creating a new client pack (the workflow)

1. Copy the nearest pack as a starting point: `frontend/designs/<new-pack>/`.
2. Replace the frozen HTML/CSS/JS and assets; keep slot attributes and the ATC
   attribute contract on interactive elements.
3. Update `manifest.json` (routes + assets). Every referenced file must exist.
4. Rewrite `composer.php` data mappings to the new markup (keep filter names:
   `aether_adapter_*`, readers: `vineta_get_customizer_value()`-equivalent).
5. Register the pack's Customizer sections with matching readers.
6. Verify with the full gate (§9). Zero core files change.

## 9. Visual QA & release gate (every pack, every release)

Automated: `node scripts/qa/shots-6vp.cjs` (routes × 1440/1280/1024/768/390/360),
`scripts/qa/console-check.cjs` (0 unintended errors), `scripts/qa/cart-final.cjs`
(ATC → badge → totals), then the static gates:

```bash
php scripts/check-core-freeze.php            # frozen-core checksums unchanged
php scripts/check-assets.php                 # 0 missing-required assets
php scripts/check-customizer-contracts.php   # DEAD 0 / ORPHANED 0
bash scripts/check-duplicate-runtime.sh      # one runtime tree
find themes plugins frontend -name '*.php' -print0 | xargs -0 -n1 php -l   # 0 lint errors
```

Ship only when every gate passes on the current filesystem.
