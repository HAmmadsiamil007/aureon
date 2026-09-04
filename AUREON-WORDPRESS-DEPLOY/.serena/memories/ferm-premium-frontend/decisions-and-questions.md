# Decisions Made — ALL 4 CONFIRMED

## Status: COMPLETE — All decisions made and approved in design spec.

## Decision 1: CSS Approach — CONFIRMED
**Choice:** A — Port compiled CSS directly from frozen source.
- No Tailwind rebuild
- No manual CSS expansion
- Copy compiled utility classes + component styles from frozen source
- Scope to .design-fermliving where needed
- Preserve exact responsive breakpoints
- Self-host in ferm pack css/ directory
- Dependency map: page family → component → required classes → required CSS
- Handle global reset/base styles carefully (classify platform-safe reset vs Ferm-specific)

## Decision 2: JS Approach — CONFIRMED
**Choice:** A — Port frozen source JS with surgical Shopify removal.
- Inventory each file → behavior mapping before porting
- Keep: animation, menu, carousel, gallery, search UI, scroll effects
- Remove: Shopify cart API, customer endpoints, Liquid JSON, Clerk.io
- Bridge: commerce/search/account → AUREON/WooCommerce contracts
- Library deduplication: check AUREON version compatibility (version, API, init, CSS)
- Server-render initial DOM; JS enhances (does not rebuild from JSON)
- Classification per file: PURE PRESENTATION / PLATFORM ADAPTER / SHOPIFY BUSINESS LOGIC / THIRD-PARTY APP

## Decision 3: Data Injection — CONFIRMED
**Choice:** A — window.FermPageData as primary data injection.
- Page-scoped, minimal, public-safe JSON payloads
- Schema identifier: { "version": 1, "schema": "fermliving-page", "design": "fermliving" }
- Structured money: { "amount": 1299, "formatted": "1,299.00 kr", "currency": "DKK" }
- Stock: { "available": true } — no raw quantity unless reference UI requires it
- Customer: minimal on non-account pages (isLoggedIn only); full data only on account page
- Search: initial results in FermPageData; predictive suggestions via async endpoint
- Security: no passwords, nonces, payment info, API secrets in FermPageData
- Mutations: existing AUREON/WooCommerce AJAX/REST contracts with proper nonces
- Mapper is the ONLY layer that shapes presentation data

## Decision 4: Asset Handling — CONFIRMED
**Choice:** A — Self-host required frozen assets in Ferm pack.
- Source: frozen SiteOne clone assets
- Target: frontend/designs/fermliving/assets/
- Do NOT copy entire 7.38 GB clone — extract only required assets by route/component
- Preserve content-hashed filenames
- Use AUREON pack URL mechanism for resolution
- No browser dependency on fermliving.com
- Verify asset identity (not just HTTP 200) for critical images
- Asset manifest required

## 7 Corrections Applied to Spec
1. Customer data: minimal fields only (isLoggedIn + displayName on account page). No full orders/addresses in public FermPageData.
2. Mutation nonces: reuse platform's existing localized nonce/endpoint bridge. Do NOT create a second auth system.
3. Routes: REFERENCE ROUTE → PAGE FAMILY → WORDPRESS TARGET ROUTE. Do NOT require WP to mimic Shopify URLs.
4. Max-width: inspect frozen compiled CSS for actual value. Do NOT hardcode 1440.
5. Critical CSS: minimal above-the-fold subset inline only. Full Ferm CSS as self-hosted pack asset.
6. Assets: generate assets-manifest.json with referencePath, localPath, hash, type, usedBy.
7. Wording: "Preserve the frozen Ferm presentation contract exactly; adapt only integration boundaries."

## What to Do After All Questions Answered
1. ✅ Design spec written → docs/superpowers/specs/2026-08-26-ferm-premium-frontend-design.md
2. ✅ Spec self-review passed
3. ✅ User approved spec with 7 corrections
4. ✅ 7 corrections applied to spec
5. NEXT: Invoke writing-plans skill → create implementation plan
6. THEN: Execute Phase 0 (Git checkpoint) → Phase 1 (Shell)
