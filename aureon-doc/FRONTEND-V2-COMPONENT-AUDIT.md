# Aureon Frontend v2 — M2 Component Library Audit (PHASE B)

> **Status:** COMPLETE (inventory + gap analysis) — component build-out queues the gaps below
> **Doc version:** 2026-08-07
> **Scope:** catalog all 48 component templates + 24 sections, diff against the 454-class source reference, flag contract compliance, list missing components.
> **Contract governing this work:** `FRONTEND-V2-ARCHITECTURE.md` (data shapes, behavior whitelist, token audit gates)

---

## 1. Inventory snapshot (verified vs disk)

### 1.1 Component manifest → templates (48/48 resolve, 1:1)

| Group | Count | Missing from manifest? |
|---|---|---|
| shell | 7 (preloader, fog, skip-link, announcement, header, mobile-chrome, footer) | — |
| hero | 4 (slider, slide, page-title, page-banner) | — |
| section | 6 (header, filter-bar, accordion, cta, newsletter, pagination) | — |
| cards | 6 (product, category, blog, review, team, wishlist) | — |
| cart/checkout/account | 4 (cart items, cart summary, checkout order-items, account profile) | — |
| commerce/product | 9 (rating + breadcrumb, gallery, info, sticky-bar, specs, reviews, related, size-guide) | — |
| content | 6 (page, article-hero, article-meta, article-body, author-bio, story) | — |
| forms | 4 (contact, login, register, newsletter) | — |
| utility | 2 (error-404, countdown) | — |
| **total** | **48** | **0** |

### 1.2 Section registry (24 sections)

hero · categories · bestsellers · reviews · faq · newsletter · mission · features · story · stats · team · contact · auth · wishlist · coming-soon · shop-hero · shop-filter · shop-grid · product · related · cart · checkout · blog-grid · blog-single

---

## 2. Contract compliance audit (grep gates)

### 2.1 ⛔ Violations — components calling WordPress directly (must fix)

Grep gate should be **0**. Current hits **7**:

| File | Line | Call | Fix (M2.3) |
|---|---|---|---|
| `forms/login.php` | 25 | `get_bloginfo('name')` fallback | adapter-site passes `brand`; remove fallback |
| `forms/register.php` | 22 | `get_bloginfo('name')` fallback | same |
| `utility/countdown.php` | 17 | `get_bloginfo('name')` fallback | same |
| `utility/error-404.php` | 19-20 | `home_url('/')` fallback | adapter-options/options passes home/shop url |
| `cart/items.php` | 18 | `home_url('/')` fallback | adapter-cart passes shop_url |
| `cart/summary.php` | 18 | `home_url('/')` fallback | same |
| `content/article-hero.php` | 17 | `get_the_title()` fallback | adapter-article passes title |

**Fix rule:** components receive `brand`/`home_url`/`shop_url`/`title` in `$componentData` (already true at call sites). Fallbacks must be removed — a component is data-dumb or it is defective.

### 2.2 `do_action()` in components — 2 hits (attention)

`forms/register.php:41 do_action('woocommerce_register_form')`, `forms/login.php:53 do_action('woocommerce_login_form')`. **Decision:** forms are the sanctioned exception (WC needs its form hooks for honeypots/extensions). Document as **explicitly allowed** in the contract (update M1 §6) so the grep gate isn't a lie.

### 2.3 Token audit gate (hardcoded presentation values in components)

Ran the gate against the gates spec. **Clean** apart from sanctioned constants: `aether_grid_gap`/`container` values come from tokens; the 2 accent `style="color:var(--gold)"` inline styles in login.php/register.php are token-drawn (`--gold`) — acceptable, but better as `.form-label .required` token class in M3.

---

## 3. Coverage diff — source reference modules WITHOUT a component

22 source pages audited; 454 distinct CSS classes extracted; mapped module-by-module.

### 3.1 ❌ MISSING components (build in M2.3)

| Missing | Source ref | Region | Needed for | Commercial value | Status 2026-08-07 |
|---|---|---|---|---|---|
| **`order/confirmation`** | `thank-you.html` `confirmation-section` | WC order-received | thank-you page | **High** (commerce) | ✅ **DONE + LIVE** |
| **`form/forgot-password`** (modal) | `login.html` `forgot-modal` (15 refs) | /login/, /account/ | Password-recovery modal | Medium | ✅ **DONE + LIVE** |
| **`auth/password-strength`** | `join-now.html` `strength-segment` (10) | /register/ | Strength meter in source, absent in component | Low-Medium | ✅ **DONE + LIVE** |
| **`values` block** | `team.html` `values-section` | /about/, /team/ | "Our Values" mission block | Medium | ✅ **DONE + LIVE** |
| **`newsletter-success`** | `index.html` `newsletter-success` | all newsletters | success-state markup parity | Low | ✅ covered — `forms/newsletter.php` already renders `.newsletter-success` (false positive in first pass) |
| **`account/orders`** | `account.html` `order-item`/`order-summary` | /my-account/ | order list + order table component | **High** (commerce) | ✅ **DONE + LIVE** (source refs `order-item`/`order-summary` actually belong to checkout order summary, not account — component modeled on WC order data) |
| **`account/navigation` menu** | `account.html` `account-menu` (5 items) | /my-account/ | account nav sidebar as component | Medium | ✅ covered — `account/profile.php` + `adapter-account` render the account nav (false positive in first pass) |

### 3.2 ⚠️ PARTIAL coverage — verified present but under-differentiated

| Source class set | Live component | Note |
|---|---|---|
| `pd-accordion*`, `faq-*` | `section/accordion` | one accordion serves FAQ + product specs |
| `pd-review-*`, `reviews-*` | `product/reviews`, `cards/review` | two different source designs, one component each |
| `mobile-*` | `shell/mobile-chrome` | verified covering mobile-search, menu, socials |
| `pd-sticky-*` | `product/sticky-bar` | verified |
| `order-*`, `cart-item*` | `cart/items`, `checkout/order-items` | verified cart-table-header present |

### 3.3 ✅ VERIFIED present (no action)

hero slider+sections, category cards, product cards, blog grid/cards, team cards, wishlist, auth forms (login/register), coming-soon countdown, contact, error-404, gallery+zoom, size-guide modal, accordion, pagination, filter-bar, cta, footer links/newsletter/payments/socials, mobile-search.

---

## 4. Design-system showcase page (styleguide) — RECOMMENDED

Add `/styleguide/` (noindex) that renders every component with a **live token readout** (`--aureon-*` current value next to each example) — the pixel checklist agencies use to approve a theme.

- Route: `page-styleguide.php` template + `aether_styleguide_sections` adapter that returns demo data per component.
- Reuses all components with **tokens fixtures** (no WC dependency) so it works on a fresh install — a marketing + QA asset.
- Verify via Playwright `/styleguide/` (0 errors) + screenshot for docs.

---

## 5. M2 execution plan (atomic order)

### M2.1 (DONE) — inventory + gap diff — this doc
### M2.2 (DONE) — manifest consistency — 48/48 resolve, 1:1
### M2.3 (IN PROGRESS) — build missing components
**DONE 2026-08-07:**
- **`order/confirmation`** component + manifest entry + `adapter-order` + `section-order-confirmation` + `aureon/theme/woocommerce/checkout/thankyou.php` override, routed via `template_include` (`is_wc_endpoint_url('order-received')` → dedicated template, replacing the previous stock-flow guard).
- Verified live on `/checkout/order-received/72/?key=…` (test order #72, processing): 200, real order number `#72`, subtitle/email/delivery notes, Continue Shopping → shop; 0 console errors/warnings. Screenshots `stage-m2-thankyou.png`/`-final.png` in repo root.
- **`values`** component: `section-values.php` (features-grid pattern) + `values` data on `adapter-about`, rendered on `page-team.php` (toggle `aether_section_values`, tokens.php default true, Customizer field auto-registered). Verified live on `/team/`: raw HTML contains `values-section`/Our Values/What Drives Us; in-container probe `values_render_len=1343`; 0 console errors. **Note:** `data-reveal-group` sets opacity:0 until scroll, so Playwright a11y snapshot only exposes the section after scrolling (verified: scrolled bottom → "What Drives Us" h2 in tree). Screenshot `stage-m2-values-section.png` in repo root.
**DONE 2026-08-07 (cont.):**
- **`form/forgot-password`** modal: `frontend/components/forms/forgot-password.php` + manifest entry + `adapter-auth` `forgot` subarray (`action` = `wc_lostpassword_url()`, `_wpnonce` = `lost_password` nonce, hidden `wc_reset_password=true`) + `login.php` `data-forgot-toggle` button (flag `login.forgot_modal`; legacy link fallback) + section-auth render + modal open/close JS (prefill from `#username`, ESC/overlay close, body scroll-lock). Verified live on `/login/`: modal opens, email pre-fills, submits to WC handler (`?reset-link-sent=true`); 0 console errors.
- **`auth/password-strength`** meter: `frontend/components/auth/password-strength.php` (4 `.strength-bar` + `.strength-text`, bound via `data-strength-target="reg_password"`) + manifest entry + `register.php` `show_strength` flag (adapter `register.show_strength=true`) + JS rewrite (old handler bound dead source IDs `#password`/`#seg1`; now scope-generic via `data-strength-target`) + `.strength-bar.active.weak/medium/strong` colors in style.css. Verified live on `/register/`: typing `StrongPass1!` → 4/4 bars strong, "Very strong password" (#2ECC71); `abc` → 1/4 weak "Weak password"; 0 console errors.
- **`account/orders`** component: `frontend/components/account/orders.php` (role-based table: Order/Date/Status/Total/Actions, `.order-status status-{slug}` pills, empty state with shop CTA, account-branded Sign Out) + manifest entry + `aether_adapter_account_orders()` (real `wc_get_orders` for current user, limit 20, newest first) + `my-account.php` orders-endpoint branch (WC nav sidebar + component; stock content retained for addresses/details). Verified live on `/my-account/orders/`: empty state → "No orders yet" + Start Shopping CTA; after creating test order #73 (admin, processing) → row `#73 | Aug 7, 2026 | Processing | $129.00 | View`; test order removed afterwards. 0 console errors. Screenshot `stage-m2-account-orders.png` in repo root.
- **M2.3 COMPLETE** — all §3.1 rows now DONE/LIVE or verified-covered.
### M2.4 (DONE 2026-08-07) — purge WP calls from component fallbacks
- Purged 7 files: `error-404` (home/shop_url), `countdown`/`login`/`register` (brand), `cart/items`/`cart/summary` (shop_url), `article-hero` (title). All fallbacks now empty-string data-dumb; call sites already supply real values (verified: 404.php, section-auth, section-coming-soon, adapter-cart).
- Gated buttons on empty URLs (error-404, cart items, cart summary) so no dead links render.
- Sweep: **0 direct WP/WC calls in `frontend/components/`** (grep verified). `do_action('woocommerce_*_form')` in login/register documented as sanctioned exception in FRONTEND-V2-ARCHITECTURE.md §6.1.
### M2.5 — component API standardization headers (each component file documents props/slots/variants/tokens — see `FRONTEND-V2-ARCHITECTURE.md` lifecycle) — DONE 2026-08-07
- **Standard defined in `FRONTEND-V2-ARCHITECTURE.md` §4.2** (normative): every component docblock carries `Key`, `Source`, `Props` (all keys escaped, missing→default), `Slots`, `Variants`, `Tokens`.
- **Applied to all 52 component files** (`frontend/components/**`), incl. the 3 M2.3 additions. Data schemas extracted from each file's actual `$componentData[...]` reads (props parity gate).
- **Verified:** 52/52 files carry `@package Aureon` + `Props:` + `Slots:` + `Key:`; **0** `$componentData[...]` keys undocumented vs header (224 keys cross-checked); `php -l` clean on every file; no mojibake.
- **Known caveats flagged in headers** (M3 follow-ups): `cart/summary` hardcoded `#4CAF50`; `checkout/order-items`, `cart/items`, `account/orders`, `product/specs` inline layout styles; `forms/login` + `forms/register` token-drawn `var(--gold)` (sanctioned §5 accent).
- Slot map: `hero/slider→hero/slide`, `cards/product→commerce/rating`, `cards/review→commerce/rating`, `forms/register→auth/password-strength`, `section/newsletter→form/newsletter`, `product/info→commerce/rating`, `product/reviews→commerce/rating`, `product/related→cards/product`. `commerce/rating` is the shared leaf; `hero/slide` + `form/newsletter` + `cards/product` are shared slots.

---

## 6. Call-site verification

Every `aether_render_component()` call in `theme/` + `frontend/` resolves in the manifest — already proven 1:1. Repeat this claim **after** M2.3/2.5 adds components.**(from STATUS Stage 11)**