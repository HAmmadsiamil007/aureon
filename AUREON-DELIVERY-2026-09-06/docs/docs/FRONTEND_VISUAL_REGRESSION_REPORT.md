# FRONTEND VISUAL REGRESSION REPORT

> **Status:** BASELINE → **AUTOMATED SUITE COMMITTED (2026-08-09)** · **Date:** 2026-08-08 (baseline)
> ⚠ Phase E harness now exists (`frontend/tests/specs/visual.spec.js`); baselines pending a live run (Docker stack down). See `docs/PHASE_17_FRONTEND_DYNAMIC_CLOSURE_REPORT.md` §9.
> **Method (mission §9):** BEFORE (source design) vs AFTER (PHP-rendered) comparison per conversion; investigate any visual difference before accepting.

---

## 1. Current visual state

The PHP components are source-faithful conversions of `frontend/source/*.html` (the pristine 364-file mirror). Verified structurally: class names, DOM order, grid layout, hero slider markup, marquee loop (items × 2), card anatomy (image/badge/rating/name/tagline/price/CTA), footer columns, mobile chrome all match the source design.

**Known intentional deviations (none are regressions):**

| Area | Source | Rendered | Reason |
|---|---|---|---|
| Product images | static demo photos | real WC featured images (seeded) | data-driven |
| Demo prices | $449/$99/$479/$69 | real WC prices ($129.00 etc.) | data-driven |
| Menu links | `shop.html`, `product-detail.html` | WP permalinks (`/shop/`, `/product/{slug}/`) | Stage 13 routing fix |
| Search suggestions | `shop.html?q=` | `/?s=` (WP search) | Stage 13 fix |
| Categories | static 4 | real WC terms (or curated fallback) | data-driven |

## 2. Screenshot inventory (existing, repo-root + aureon-doc)

| File | Captures |
|---|---|
| `homepage-fixed-full.png`, `homepage-full.png`, `final-homepage.png`, `premium-homepage.png` | home top/full |
| `section-0-hero.png` … `section-5b-newsletter-footer.png` | home sections |
| `desktop-bento-final.png`, `desktop-bento-verified.png` | home |
| `marquee-live.png`, `marquee-fullpage.png`, `premium-marquee.png`, `premium-scrolled.png` | announcement marquee |
| `mobile-header.png`, `mobile-menu-fixed.png`, `mobile-menu-open.png` | mobile |
| `customizer-*.png`, `typography-panel.png` | Customizer |
| `announcement-fixed.png`, `announcement-top.png`, `blog-fixed.jpeg` | fixes |
| `stage3-home-top.png`, `stage4-shop-top.png`, `stage5-product-top.png`, `stage6-10-home.png`, `stage6-10-shop-grid.png` (aureon-doc) | staged verification |

All live-run evidence screenshots above now live in `aureon-doc/screenshots/` (moved from repo root on 2026-08-09 to keep the root clean).

## 3. Verified visual checks (documented stages)

| Check | Result |
|---|---|
| Hero slider 3 slides, images loaded, 700px | ✅ Stage 3 |
| Category grid 1 large + 2×2 (real terms) | ✅ Stage 3/4 |
| Shop 6 cards, 6/6 images, no broken images | ✅ Stage 4 |
| Product gallery 4+4 swiper, sticky bar, specs accordion | ✅ Stage 5 |
| Cart empty state, checkout 302, account login form | ✅ Stage 6 |
| Blog cards + pagination, single + related | ✅ Stage 7 |
| 9 static pages + 404 | ✅ Stage 8/10 |
| Countdown units live | ✅ Stage 10 |
| Full route sweep 0 console errors/warnings | ✅ Stage 13 |

## 4. Automated harness (Phase E) — COMMITTED 2026-08-09

- ✅ **Committed** Playwright `visual.spec.js` — `toHaveScreenshot` baselines for `/`, `/shop/`, `/about/` (desktop project, `animations: 'disabled'`, 1% diff budget, `?nocache` on every goto).
- ✅ Responsive coverage via the suite's desktop (1280) + mobile (390) projects; failure-injection suite covers GSAP-blocked / images / empty data.
- ⏳ **Pending:** generating the initial baselines (`npx playwright test --grep @visual --update-snapshots`) and a green full run — blocked on the Docker stack being down.
- Discipline: baselines are committed; `?nocache` defeats 301 caching (Stage 2 lesson); never commit snapshots with credentials (Stage 13 `.playwright-mcp/` lesson).

## 5. Risk to visual fidelity during remaining phases

Phase A (watchdog) is the only change that touches *how* content becomes visible — the fix must preserve the identical animation when libraries load, and only change the failure path (content visible instead of hidden). Phase B–D change data sources only; presentation markup untouched. No phase redesigns any component.
