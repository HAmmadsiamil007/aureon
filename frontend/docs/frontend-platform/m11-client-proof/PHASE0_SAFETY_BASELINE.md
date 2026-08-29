# PHASE 0 — SAFETY BASELINE

**Date:** 2026-08-21
**Branch:** `main` (pre-branch state)
**Commit:** `1d8051e` — chore(build): stop tracking aureon.zip in git

---

## 1. Git State at Start

| Item | Value |
|------|-------|
| Branch | `main` (clean, up to date with `origin/main`) |
| HEAD | `1d8051e` |
| Staged changes | None |
| Unstaged changes | `.gitignore` (minor: added `theme/` entry) |
| Untracked | `frontend/designs/fermliving/` (existing skeleton: `manifest.json`, `tokens.php`, empty `css/`, `js/`, `components/shell/`) |

## 2. Active Design State

| Setting | Value |
|---------|-------|
| `aether_active_design` option | `'luxury'` (engine tree default) |
| `AETHER_DESIGN` constant | Not defined |
| Effective active design | `luxury` |
| Existing design packs | `lumen` (complete), `fermliving` (skeleton only) |

## 3. Architecture Baseline (Protected)

The following are **frozen** and must not be modified:

```
AUREON CORE (aureon/theme/, aureon/plugin/)
├── AETHER Frontend Engine (frontend/)
│   ├── Core Architecture (views/)
│   │   ├── design.php       — Design pack resolution
│   │   ├── loader.php       — Engine boot
│   │   ├── renderer.php     — Component renderer + escape boundary
│   │   ├── composer.php     — Page composition (header/footer)
│   │   ├── viewmodel.php    — ViewModel normalization
│   │   ├── registry.php     — Section registry
│   │   └── assets.php       — Asset enqueue
│   ├── Manifest (manifest/components.php) — 50+ component registrations
│   ├── Tokens (tokens/tokens.php) — Base token defaults (AETHER luxury)
│   ├── Components (components/) — 14 families, 50+ templates
│   ├── Sections (sections/) — 26 section templates
│   ├── Adapters (adapters/) — 23 adapter files
│   └── Design Packs (designs/)
│       ├── lumen/ — Complete reference pack (9 overrides)
│       └── fermliving/ — Target pack (skeleton)
```

## 4. Current Component Registry (50 components)

```
Shell:         preloader, fog, skip-link, announcement, header, mobile-chrome, footer
Hero:          slider, slide, page-title, page-banner
Section:       header, filter-bar, accordion, cta, newsletter, pagination
Cards:         product, category, blog, review, team, wishlist
Cart/Checkout: cart/items, cart/summary, checkout/order-items
Account:       profile, orders
Auth:          password-strength
Order:         confirmation
Commerce:      rating, quick-view
Product:       breadcrumb, gallery, info, sticky-bar, specs, reviews, related, size-guide
Content:       page, article-hero, article-meta, article-body, author-bio, story
Forms:         contact, login, register, newsletter, forgot-password
Utility:       error-404, countdown, empty-state
```

## 5. Current Section Library (26 sections)

```
section-auth, section-bestsellers, section-blog-grid, section-blog-single,
section-cart, section-categories, section-checkout, section-coming-soon,
section-contact, section-faq, section-features, section-hero,
section-mission, section-newsletter, section-order-confirmation,
section-product, section-related, section-reviews, section-shop-filter,
section-shop-grid, section-shop-hero, section-stats, section-story,
section-team, section-values, section-wishlist
```

## 6. Current Adapter Layer (23 adapters)

```
adapter-about, adapter-account, adapter-article, adapter-auth,
adapter-blog, adapter-cart, adapter-coming-soon, adapter-contact,
adapter-faq, adapter-hero, adapter-menu, adapter-options,
adapter-order, adapter-product, adapter-shell, adapter-shop-hero,
adapter-site, adapter-team, adapter-testimonials, adapter-wc-categories,
adapter-wc-filter, adapter-wc-products, adapter-wishlist
```

## 7. Existing Lumen Pack Reference (9 overrides)

| Component | Override Path |
|-----------|---------------|
| `shell/header` | `lumen/components/shell/header.php` |
| `shell/mobile-chrome` | `lumen/components/shell/mobile-chrome.php` |
| `shell/footer` | `lumen/components/shell/footer.php` |
| `hero/slider` | `lumen/components/hero/slider.php` |
| `hero/slide` | `lumen/components/hero/slide.php` |
| `card/product` | `lumen/components/cards/product.php` |
| `card/category` | `lumen/components/cards/category.php` |
| `card/blog` | `lumen/components/cards/blog.php` |
| `section/header` | `lumen/components/section/header.php` |

## 8. Fermliving Source State

| Metric | Value |
|--------|-------|
| Root path | `C:\Users\hamma\Downloads\SiteOne-Crawler\fermliving.com` |
| Total size | ~7.38 GB |
| HTML pages | 980 |
| Collections | 113 |
| Products | 784 |
| Pages | 58 (incl. 13 configurators) |
| Blog stories | 13 |
| Supplier stories | 4 |
| Root HTML | 4 (index, cart, checkout, account) |

## 9. Fermliving Existing Design Pack Skeleton

```
frontend/designs/fermliving/
├── manifest.json          — Complete mapping manifest ✓
├── tokens.php             — Token defaults ✓ (13 tokens)
├── assets/                — Empty
├── components/
│   └── shell/             — Empty (header/footer not yet created)
├── css/                   — Empty
└── js/                    — Empty
```

## 10. Safety Constraints

- [ ] No core files modified
- [ ] No adapter files modified
- [ ] No component registry modified
- [ ] No section registry modified
- [ ] No renderer modified
- [ ] No composer modified
- [ ] No base token defaults modified
- [ ] Lumen pack remains intact
- [ ] Luxury design remains default fallback
- [ ] WooCommerce logic untouched
- [ ] Plugin bridges untouched

## 11. Baseline Screenshots Reference

To be captured before any implementation:
- Homepage (desktop + mobile)
- Shop/Collection (desktop + mobile)
- Product detail (desktop + mobile)
- Cart
- Blog
- 404

## 12. Next Phase

→ [PHASE1_FERMLIVING_SOURCE_AUDIT.md](./PHASE1_FERMLIVING_SOURCE_AUDIT.md)
