# Ferm Premium Frontend Port — Master Plan

## Status: COMPLETE — Design spec approved with 7 corrections. Ready for writing-plans → implementation.

## Spec Location
`C:\Users\hamma\Downloads\phantom\wordpress\docs\superpowers\specs\2026-08-26-ferm-premium-frontend-design.md`

## Objective
Port the frozen Ferm Living frontend into the Ferm design pack as a PREMIUM FRONTEND with a thin dynamic bridge to WordPress/WooCommerce/AUREON.

## Core Principle
```
FERM SOURCE FRONTEND → PRESERVE FERM PRESENTATION CONTRACT → REMOVE SHOPIFY BACKEND → ADD THIN DATA BRIDGE → AUREON/WORDPRESS/WOO
```
PHP = server-side rendering + canonical→Ferm data bridge. HTML/CSS/JS = Ferm presentation (preserve the frozen Ferm presentation contract exactly; adapt only integration boundaries). PHP is NOT the visual design authoring mechanism.

## Source
- Frozen Ferm source: `C:\Users\hamma\Downloads\SiteOne-Crawler\fermliving.com` (immutable FERM_REFERENCE_V1)
- Live reference: `https://fermliving.com/`
- Project: `C:\Users\hamma\Downloads\phantom\wordpress`
- Design spec: `C:\Users\hamma\Downloads\phantom\wordpress\docs\superpowers\specs\2026-08-26-ferm-premium-frontend-design.md`

## All 4 Decisions CONFIRMED
1. **CSS:** Port compiled CSS directly from frozen source (no Tailwind rebuild)
2. **JS:** Port frozen JS behavior, surgically remove Shopify code, bridge to AUREON/WooCommerce
3. **Data:** window.FermPageData — page-scoped JSON payloads, data-* for small local metadata, REST/AJAX for mutations only
4. **Assets:** Self-host required frozen assets in Ferm pack (no external CDN dependency)

## Architecture
```
WordPress / WooCommerce
        ↓
AUREON canonical data (adapters)
        ↓
Ferm presentation mapper
        ↓
┌───────┴───────┐
↓               ↓
Server-rendered    window.FermPageData
Ferm HTML          (page-scoped JSON)
↓               ↓
└───────┬───────┘
        ↓
Ferm JS (enhances/interacts)
        ↓
AUREON/WooCommerce mutations
        ↓
CLIENT UI/UX
```

## Integration Boundary
```
Shopify cart API         → WooCommerce/AUREON cart contract
Shopify search           → WordPress/Woo/AUREON search contract
Shopify customer/account → WordPress/Woo customer contract
Clerk.io recommendations → Reference/demo provider or AUREON recommendation contract
Liquid JSON              → window.FermPageData
```

## What Stays from Ferm Source
HTML structure, CSS classes, layout, grid system, spacing, typography, responsive structure, images, fonts, animations, GSAP/Lenis/Swiper behavior, interaction states, mobile behavior

## What Gets Replaced
Shopify Liquid, Shopify product queries, Shopify cart, Shopify checkout, Shopify customer account API, Shopify URLs, Shopify-specific data state, Clerk.io runtime

## What Does NOT Change
- AUREON core (no Ferm-specific changes; generic extensions allowed)
- WooCommerce business logic
- Luxury design pack (zero changes)
- Existing adapter architecture
- Security behavior
- Design pack resolver
- WooCommerce database (never replaced with Ferm reference data)

## Page Families (10 + Global Shell)
**Global Shell:** announcement, header, mega menu, search overlay, mobile nav, footer
**Page Families:** Homepage, Archive/PLP, Product/PDP, Blog, Article, About, Contact, Cart/Checkout, Account, Search/404

## Implementation Order
```
Phase 0: Git checkpoint (baseline commit)
Phase 1: Global shell → 16-point gate → 1440 + 390 screenshots
Phase 2: Homepage → 16-point gate → 1440 + 390 screenshots
Phase 3: Archive/PLP → 16-point gate → 1440 + 390 screenshots
Phase 4: Product/PDP → 16-point gate → 1440 + 390 screenshots
Phase 5: Content → 16-point gate → 1440 + 390 screenshots
Phase 6: Commerce → 16-point gate → functional test
Phase 7: Full visual regression
Phase 8: Isolation testing (Ferm ↔ Luxury)
Phase 9: Final 100/100 acceptance
```

## 16-Point Per-Phase Gate (ALL REQUIRED)
1. Correct route loads
2. Correct page family/template renders
3. Correct Ferm DOM/visual structure
4. Required Ferm CSS applied
5. Ferm JS initialized (no console errors)
6. Correct FermPageData schema/content
7. 1440px visual comparison — PASS
8. 390px visual comparison — PASS
9. No AETHER class leakage
10. No Shopify runtime/API/markup dependency
11. No legacy Ferm pack assets/scripts
12. No duplicate JS library initialization
13. Server-rendered HTML works before JS enhancement
14. Fonts/CSS/assets load correctly
15. Critical assets are correct reference assets (identity verified)
16. No unexpected console/network errors

## Three-Layer Proof
Every page family requires:
1. Route + page family correctness
2. DOM + data + asset correctness
3. Screenshot visual parity

## Key Rules (from corrections)
- Customer data: all pages = isLoggedIn only; account page = minimum fields rendered only. No full orders/addresses in public FermPageData.
- Nonces: FermPageData = content/state. AUREON/WP runtime = endpoint URLs + mutation nonce/config. Do NOT create a second auth system.
- Routes: REFERENCE ROUTE → PAGE FAMILY → WORDPRESS TARGET ROUTE. Do NOT require WP to mimic Shopify URLs.
- Max-width: Inspect frozen compiled CSS for actual value. Do NOT hardcode 1440 (viewport ≠ content max-width).
- Critical CSS: minimal above-the-fold subset inline. Full Ferm CSS as self-hosted pack asset.
- Assets: generate assets-manifest.json with referencePath, localPath, hash, type, usedBy.
- Wording: "Preserve the frozen Ferm presentation contract exactly; adapt only integration boundaries."

## Risk: Biggest Avoidance
> Do not allow the AUREON DOM/component system to rewrite the copied Ferm frontend. Data should flow INTO the premium frontend.

## Release Gate
Tests pass + Screenshots pass + Route pass + Content pass + Asset pass = RELEASE
No "green tests, visually wrong" acceptance.
