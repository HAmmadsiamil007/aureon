# Ferm Living — JS Compatibility Map

**Date:** 2026-08-26
**Source:** Frozen Ferm frontend JS bundles
**Status:** STANDALONE ANALYSIS

---

## 1. File Inventory

| File | Size | Classification | Standalone |
|------|------|---------------|------------|
| `app.1e7cf79a09.js` | 147.9 KB | PLATFORM_ADAPTER | NO |
| `product.fa97565a5f.js` | 52.9 KB | PURE_PRESENTATION | PARTIALLY |
| `customer.5de68fbefc.js` | 3.7 KB | PLATFORM_ADAPTER | MOSTLY |
| `speedblitz.min.95accfb9a4.js` | 5.8 KB | PURE_PRESENTATION | YES |
| `cart-page.*.js` | -- | **MISSING FROM CRAWL** | N/A |

---

## 2. `app.1e7cf79a09.js` — Main Application Bundle

### What It Does
Master shell application. Handles header, mega menu, mobile nav, cart drawer, cart page, USP bar, search, accordion, FAQ, collection browsing/filtering, video playback, gift wrapping, wishlist, newsletter/back-in-stock notify form, contact form, Roomle 3D configurator, and product card rendering.

### Component Registry (24 components)

| Component | Purpose | Shopify-Coupled |
|-----------|---------|----------------|
| `mainCartSell` | Cart page upsell (Clerk.io) | YES — Clerk API |
| `cartDrawer` | Slide-out cart drawer | YES — Section Rendering |
| `cartMain` | Cart page main content | YES — Section Rendering |
| `accordion` | Expandable accordion | NO |
| `heroWithCta` | Hero with scroll zoom | NO |
| `heroFullWidthVideo` | Autoplay video hero | NO |
| `productThumb` | Product card grid items | NO |
| `colorSelect` | Color swatch selector | NO |
| `addToCart` | Product form (add to cart) | YES — cart/add.js |
| `uspHeader` | Rotating USP bar | NO |
| `header` | Site header + mega menu | NO |
| `megaMenu` | Mega menu hover behavior | NO |
| `mobileMenu` | Mobile navigation | NO |
| `collectionAllTemplate` | Collection all layout | NO |
| `collectionTemplate` | Collection template | NO |
| `collectionFilters` | Filter sidebar | NO |
| `collectionList` | Collection list slider | NO |
| `contactForm` | Contact form routing | YES — reCAPTCHA |
| `faqAccordion` | FAQ accordion | NO |
| `price` | Price formatting | YES — money_format |
| `roomleConfigurator` | 3D configurator embed | EXTERNAL — Roomle API |
| `stockInfo` | Stock availability | NO |
| `tooltip` | Tooltip behavior | NO |

### Shopify API References

| Reference | Purpose | Bridge Required |
|-----------|---------|-----------------|
| `window.Shopify.routes.root` | Base URL for cart endpoints | YES — shim to `/` |
| `window.Shopify.money_format` | Money format template | YES — shim to WC format |
| `window.Shopify.formatMoney()` | Price formatting | YES — polyfill |
| `window.__MONEY_FORMAT__` | Custom money format | YES — shim |
| `shopify:section:load` events | Component re-init | YES — custom WP event |

### Cart API Calls

| Endpoint | Method | Body | Purpose |
|----------|--------|------|---------|
| `{root}/cart/add.js` | POST | `{items:[{id,quantity}], sections:"cart-drawer,main-cart"}` | Add to cart |
| `{root}/cart/update.js` | POST | `{updates:{key:qty}, sections:"cart-drawer,main-cart"}` | Update quantities |
| `{root}/cart/update.js` | POST | `{sections:"cart-drawer,main-cart"}` | Refetch cart state |
| `{root}/cart/update.js` | POST | `{attributes:{"Product flags":value}, sections:"..."}` | Cart attributes |

### Section Rendering Response Format

```json
{
  "sections": {
    "cart-drawer": "<div data-cart-drawer-content>...HTML...</div>",
    "main-cart": "<div data-cart-main-content>...HTML...</div>"
  }
}
```

### CustomEvent Bus

| Event | Direction | Payload |
|-------|-----------|---------|
| `cart:open` | OUT | none |
| `cart:update` | OUT | none |
| `cart:error` | OUT | `{detail: string}` |
| `cart-drawer:open-upsell-drawer` | OUT | `{detail: {open: true}}` |
| `main-cart:update` | IN | none |
| `notify:open` | OUT | `{detail: {variantId}}` |
| `stock:error` | OUT | `{detail: {variant: "mto"/"regular"}}` |
| `variant:change` | OUT | `{detail: {variant, productId}}` |
| `selectChange` | OUT | value/name/selectedIndex |
| `shopify:section:load` | IN | `{detail: {container}}` |
| `shopify:section:load:addToCart` | IN | `{detail: {container}}` |
| `shopify:section:load:emblaSlider` | IN | `{detail: {container}}` |

### Third-Party Dependencies

| Library | Purpose | Bundled | Action |
|---------|---------|---------|--------|
| Clerk.io | Product recommendations | External | STUB/DISABLE |
| Klaviyo | Back-in-stock email | External | STUB/DISABLE |
| Swym/Wishlist | Wishlist | External | STUB/DISABLE |
| Embla Carousel | Image sliders | Bundled | KEEP |
| Roomle | 3D configurator | External | STUB/DISABLE |
| Ferm Variant Fetcher | Vercel microservice | External | STUB/DISABLE |
| reCAPTCHA | Form protection | External | STUB/DISABLE |

### Critical Data Attributes

```
data-component, data-cart-item, data-cart-count, data-cart-drawer-content,
data-cart-drawer-footer, data-cart-drawer-overlay, data-quantity,
data-decrease-quantity, data-increase-quantity, data-variant-id,
data-variant-select, data-button-add-to-cart, data-button-notify-me,
data-button-sold-out, data-megamenu, data-megamenus, data-mobile-menu,
data-mobile-menu-link, data-header-bar, data-header-inner, data-header-nav,
data-usp-item, data-usp-current-index, data-shipping-text,
data-wishlist-button, data-accordion-item, data-faq-question,
data-faq-answer, data-notify-form, data-notify-submit, data-form-state,
data-success-state, data-gift-wrapping-button, data-remove-item,
data-quantity-button, data-item-key, data-customer-id
```

### Window Globals Consumed

```
window.Shopify, window.__MONEY_FORMAT__, window.shop,
window._swat, window.SwymCallbacks, window.dataLayer, window.sessionStorage
```

---

## 3. `product.fa97565a5f.js` — Product Page Bundle

### What It Does
PDP interactivity: image gallery (Embla carousel), variant info updating, sticky add-to-cart bar, back button, product rating (Judge.me), store inventory check (Vercel API), delivery estimate, Roomle configurator.

### Shopify API References
**NONE.** No direct Shopify API calls.

### Internal Components

| Function | Purpose | Standalone |
|----------|---------|------------|
| `product images` | Embla carousel | YES |
| `variant info updater` | Updates price/stock on variant change | YES (needs DOM data) |
| `back button` | `window.history.back()` | YES |
| `product rating` | Judge.me widget | CONDITIONAL (hostname check) |
| `store inventory button` | Vercel API check | NO — external API |
| `delivery estimate` | Delivery date display | YES |
| `sticky add to cart` | IntersectionObserver sticky bar | YES |

### Classification: PURE_PRESENTATION
All behavior is DOM manipulation. External deps are optional/non-critical.

---

## 4. `customer.5de68fbefc.js` — Account Pages

### What It Does
Login form with password recovery toggle, address book management (create/edit/delete with form validation).

### Shopify API References
- `Shopify.theme.addresses.AddressForm` — Country/province dropdown handler

### Functions

| Function | Purpose | Standalone |
|----------|---------|------------|
| `initLoginForm` | Login form, password recovery toggle | YES |
| `checkUrlHash` | Handle `#recover` hash | YES |
| `resetPasswordSuccess` | Show success state | YES |
| `initializeAddressForm` | Address CRUD with overlays | MOSTLY (needs AddressForm shim) |

### Classification: PLATFORM_ADAPTER
Predominantly vanilla DOM. Shopify dep is address form handler only.

---

## 5. `speedblitz.min.95accfb9a4.js` — PJAX Library

### What It Does
InstantClick — PJAX library for link prefetching and instant page transitions. Intercepts link clicks, prefetches on hover, swaps `<body>` without full reload.

### Shopify API References: NONE

### Classification: PURE_PRESENTATION
Fully standalone. No platform coupling.

---

## 6. Missing: `cart-page.*.js`

**Not present in crawl.** This file handles cart page specific behaviors (quantity update, remove, coupon apply). Needs to be reconstructed or shimmed.

---

## 7. Bridge Requirements Summary

### For Standalone Mode

| Requirement | Implementation |
|-------------|---------------|
| `window.Shopify` shim | Provide `routes.root`, `money_format`, `formatMoney()` |
| Cart API endpoints | Intercept fetch to `/cart/*.js` and return mock responses |
| Section rendering | Return pre-rendered HTML fragments in Shopify format |
| `shopify:section:load` events | Dispatch after AJAX updates |
| Money format | `data-money-format` attribute on body |
| Third-party stubs | Empty/no-op functions for Clerk, Klaviyo, Swym, Roomle |
| `data-template` attribute | Set on body for template-specific behavior |
| Cart page script | Reconstruct missing `cart-page.js` |

### For WordPress Integration

| Requirement | AUREON Source |
|-------------|--------------|
| Product data | `WC_Product` → adapter |
| Category data | `WP_Term` → adapter |
| Cart operations | WooCommerce AJAX → bridge |
| Customer data | `WP_User` / WC customer → adapter |
| Search | `WP_Query` → bridge |
| Money format | `wc_price()` → `wp_localize_script` |
| Currency | `get_woocommerce_currency()` → `wp_localize_script` |
