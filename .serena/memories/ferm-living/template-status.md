# Ferm Living Template — Current Status

**Last Updated:** 2026-08-26
**Status:** READY_TO_CONNECT

## Location
`C:\Users\hamma\Downloads\SiteOne-Crawler\fermliving-template-ready\` (2020 files, 174.8 MB)

## What Exists
- 15 HTML templates (1 per page family, not 980 instances)
- 1963 referenced product images (165 MB)
- 33 theme assets (3 CSS, 5 JS, 13 fonts, 15 favicons)
- `ferm-data-shims.js` — standalone product/cart/navigation data
- `fonts.ferm-open-source.css` — Fraunces + Inter replacement fonts
- `assets-manifest.json` — complete file manifest
- `FERM-JS-COMPATIBILITY-MAP.md` — JS classification
- `FERM-TEMPLATE-CONTRACT.md` — dynamic data slots
- `FERM-TEMPLATE-READY-REPORT.md` — final status

## Key Facts
- CSS: Tailwind JIT (NOT Bootstrap)
- JS: Vanilla Webpack IIFE, 24 components, no GSAP/Three.js/Lenis/Swiper
- Fonts: Fraunces (replaces Canela) + Inter (replaces KHTeka) — open-source
- Cart: Shopify Section Rendering API → needs WooCommerce bridge
- Third-party: Clerk.io, Klaviyo, Swym, Roomle all stubbed
- Missing: `cart-page.js` (not in crawl), checkout (redirect only)

## Frozen Source
`C:\Users\hamma\Downloads\SiteOne-Crawler\fermliving.com\` — IMMUTABLE

## WordPress Project
`C:\Users\hamma\Downloads\phantom\wordpress\` — UNTOUCHED (verified via git status)

## Next Phase
Integration: Thin bridge (~300 lines) connecting AUREON core to Ferm presentation.
No WordPress/AUREON changes until user approves integration phase.
