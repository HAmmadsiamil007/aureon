# FRONTEND_ARCHITECTURE_REPORT

**Phase:** 17 — Frontend Integration Framework (Step 2: Audit)
**Date:** 2026-08-06
**Status:** Complete — architecture approved for framework build
**Scope:** AETHER static frontend template → Aureon WordPress theme integration framework

---

## 1. Executive Summary

The source frontend is the **AETHER** premium sneaker-commerce template: a dark "void" (#09090B) + gold (#C8956C) aesthetic built on Bootstrap 5.3.3, Swiper 11, GSAP 3.12.5 + ScrollTrigger, and Lenis 1.1.18. It ships 22 HTML pages covering the full storefront surface (home, shop, product, cart, checkout, wishlist, auth, account, blog, content, legal).

The previous attempt at direct template conversion was **intentionally rolled back** in the working tree (AETHER asset/inc files deleted from `aureon/theme`). Phase 17 rebuilds this properly as a **frontend integration framework**:

```
Aureon Core  →  Adapters  →  ViewModels  →  Renderer  →  Components
```

Components NEVER call WordPress functions. They receive `$componentData` from adapters.

---

## 2. Frontend Inventory

### 2.1 Pages (22 HTML + 1 PHP support)

| # | File | Title | Body class | Role | Size |
|---|---|---|---|---|---|
| 1 | `index.html` | AETHER — Step Into The Void | `home-page` | Home: hero slider, categories, bestsellers, reviews, FAQ, newsletter | 51.4KB |
| 2 | `shop.html` | Collection — AETHER | `shop-page` | Product archive + category filter | 23.6KB |
| 3 | `product-detail.html` | AETHER Void Runner — Obsidian | `product-page` | Single product: gallery, swatches, size, quantity, reviews | 43.6KB |
| 4 | `cart.html` | Your Cart — AETHER | `cart-page` | Cart table + summary | 28.6KB |
| 5 | `checkout.html` | Checkout — AETHER | `checkout-page` | Checkout form layout | 26.5KB |
| 6 | `wishlist.html` | Wishlist — AETHER | `wishlist-page` | Saved items grid | 17.5KB |
| 7 | `login.html` | Sign In - AETHER | `login-page` | Auth: login + Google | 19.4KB |
| 8 | `join-now.html` | Join the Void - AETHER | `register-page` | Auth: register + strength bar | 17.1KB |
| 9 | `account.html` | My Account - AETHER | `account-page` | Account dashboard | 11.1KB |
| 10 | `thank-you.html` | Thank You — AETHER | `thankyou-page` | Order confirmation | 18.2KB |
| 11 | `404.html` | 404 — AETHER | `error-page` | Not found | 17.7KB |
| 12 | `coming-soon.html` | Coming Soon — AETHER | `coming-soon-page` | Countdown + notify form | 20KB |
| 13 | `blog.html` | Journal — AETHER | `blog-page` | Post archive | 23.7KB |
| 14 | `single-blog.html` | The Science Behind Carbon Fiber | `blog-page` (dup) | Post single | 24.7KB |
| 15 | `faq.html` | FAQ — AETHER | `faq-page` | Bootstrap accordion FAQ | 23.2KB |
| 16 | `testimonials.html` | Testimonials — AETHER | `testimonials-page` | Reviews grid | 25.6KB |
| 17 | `team.html` | Our Team — AETHER | `team-page` | Team grid | 23.1KB |
| 18 | `contact.html` | Contact — AETHER | `contact-page` | Contact form | 27.8KB |
| 19 | `cookie-policy.html` | Cookie Policy — AETHER | `legal-page` | Legal | 18.5KB |
| 20 | `privacy-policy.html` | Privacy Policy — AETHER | `legal-page` | Legal | 18.7KB |
| 21 | `term-of-use.html` | Terms of Use — AETHER | `legal-page` | Legal | 18.7KB |
| 22 | `404.html` (extra) | — | — | — | — |
| 23 | `contact-form.php` | — | — | Legacy server form (reference) | — |

Plus support: `robots.txt`, `sitemap.xml`.

### 2.2 Asset Inventory

| Category | Files | Notes |
|---|---|---|
| CSS local | 5 | `style.css` 96.7KB / `responsive.css` 32KB / `motion.css` 4.8KB / `a11y.css` 3KB + vendor |
| CSS vendor | 6 | `blog.css` 80KB, `shop.css` 93KB, `animate.css`, `owl.*` |
| Bootstrap | 1 | `bootstrap.min.css` |
| JS local | 9 | `main.js` 25KB, `animations.js` 39KB, `effects.js` 6KB, `lenis-scroll.js`, `phantom-data.js` 8.6KB, `phantom-bridge.js`, `phantom-dark-mode.js`, `three-scenes.js`, `firebase-auth.js` 15KB |
| JS vendor | 19 | jQuery 3.7.1, Bootstrap4, Popper, Owl, Wow, jQuery-validate + 13 feature snippets (ALL unreferenced) |
| Images | 171 | products, banners, blog, team, favicons, fog, vectors |
| Docs | 4 | ARCHITECTURE.md, COMPREHENSIVE-ANALYSIS.md, FRONTEND-CONTRACT.md, RULES.md |

---

## 3. Three Data Channels (existing design → framework mapping)

| Channel | Direction | Mechanism in Static Demo | Framework Replacement |
|---|---|---|---|
| **1. Server injection** | PHP → HTML | (absent — static) | Aureon templates render `$componentData` server-side |
| **2. REST/meta** | PHP → JSON → JS | `phantom-data.js` fetches `/phantom/v1/page-data` (broken: `init()` undefined) | Aureon `class-rest.php` endpoints or server-rendered `data-phantom` values |
| **3. CSS variables** | Settings → CSS | `:root` 6 palette tokens + 472 `var()` usages | Aureon Customizer dynamic CSS + design tokens |

**Architecture decision (locked in Phase 17.1):** Server-side rendering primary. `phantom-data.js` degrades to an AJAX-only enhancement layer. The `data-phantom-*` attribute system remains as the **contract**, but PHP populates the content.

---

## 4. Page Template Contract

Every static page satisfies:

```html
<!DOCTYPE html>
<html lang="en">
<head>
  <!-- CDN CSS: bootstrap, font-awesome, swiper + 4 local CSS -->
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="home-page">
  <!-- #preloader
       #fog-system (3 layers)
       .skip-to-content visually-hidden
       .page-content
         .mobile-header + .mobile-menu-overlay
         .announcement-bar
         .header
         <main id="swup"> … </main>
         .footer -->
  <!-- bootstrap bundle, swiper, gsap+ST, lenis (CDN) -->
  <script src="assets/js/lenis-scroll.js"></script>
  <script src="assets/js/animations.js"></script>
  <script src="assets/js/main.js"></script>
  <script src="assets/js/phantom-data.js"></script>
  <script src="assets/js/phantom-bridge.js"></script>
</body>
</html>
```

---

## 5. Dependency Graph (final)

```
vendor/jquery 3.7.1 (CDN if enqueued; local vendor/ unused)
  └── bootstrap 5.3.3 (CDN, active)
  └── swiper 11 (CDN, active)
  └── gsap 3.12.5 + ScrollTrigger (CDN, active)
  └── lenis 1.1.18 (CDN, active)
      └── lenis-scroll.js (active)
      └── animations.js (active, self-contained; optional GSAP path)
      └── main.js (DOMContentLoaded: header, swiper, preloader, cart)
      └── phantom-bridge.js (window.PhantomBridge util, active)
      └── phantom-data.js (BROKEN — missing init(); target for WP REST refactor)
      └── firebase-auth.js (ES module; NOT page-loaded; login/register target fix)
  └── vendor/ (19 files) — ALL UNREFERENCED (dead weight; exclude from bundle)
```

---

## 6. Data Flow Contract (locked)

```
WordPress → WC → Aureon Modules → Adapters → ViewModels → Renderer → Components
```

**Component anatomy:** Input (`$componentData`) → Render (partial/viewModel) → Animation hooks (`data-*`) → Output HTML.

---

## 7. Key Integration Caveats

1. **`a11y.css` link broken on ALL 22 pages** — `href="assets/css/a11y.css"">` stray quote → a11y stylesheet never loads. Fix at import.
2. **`phantom-data.js:212`** calls undefined `init()` → ReferenceError. The file is the REST/AJAX refactor target.
3. **`checkout.html` footer newsletter** has `id="contactpage"` collision.
4. **Two independent breakpoint systems** (local rem tiers vs vendor px tiers) — reconcile at integration.
5. **Two independent font systems** (Satoshi/Cabinet Grotesk local vs Archivo/Nunito/Playfair vendor) — unify via Customizer typography.
6. **`vendor/` + `effects.js` + `phantom-dark-mode.js` + `three-scenes.js` + `*.reference`** are dead — excluded from WP bundle.
7. **Firebase config placeholders** in `firebase-auth.js`; module path wrong (`../assets/js/…` → should be `./assets/js/…`).

---

## 8. Frontend Integration Folder Structure (target)

```
frontend/
├── source/                 ← imported static demo (read-only reference, 240 files)
├── assets/                 ← curated WP-bundled assets (css, js, img, fonts, icons)
├── components/             ← frontend component templates (PHP partials, data-driven)
├── sections/               ← section library (composition of components)
├── layouts/                ← page shell layouts (header/footer wrappers)
├── adapters/               ← PHP data adapters (WC, menus, posts, options…)
├── tokens/                 ← design token definitions (mapped to Customizer)
├── views/                  ← renderers / ViewModels
├── manifest/               ← component + asset manifests
├── docs/                   ← this architecture + all Phase 17 reports
└── demo/                   ← (future) additional demo packs: Luxury, Minimal, Dark…
```

---

## 9. Recommendation Gates

- [ ] Build integration layer (Step 4) before templates (Step 7).
- [ ] Tokenize (Step 5) before animation bridge (Step 6).
- [ ] WooCommerce adapter (Step 8) before quality verification (Step 9).
- [ ] Approval gate: architecture documents must be reviewed before production templates are rewritten.