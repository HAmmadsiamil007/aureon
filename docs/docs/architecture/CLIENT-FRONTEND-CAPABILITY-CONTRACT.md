# CLIENT-FRONTEND CAPABILITY CONTRACT

**Version:** 1.0
**Date:** 2026-09-01
**Status:** PERMANENT REFERENCE — Every new client frontend must satisfy this contract before activation.

---

## Purpose

This document answers the five questions every client onboarding must resolve:

1. What does every frontend have to support?
2. What does Golden AUREON provide?
3. What must the bridge provide?
4. What may the client design change?
5. What may never be removed?

It is the single source of truth for capability requirements. The feature-capability matrix for each client pack is derived from this document.

---

## Responsibility Boundaries

### Golden Core (PLATFORM — never modified for clients)

```
WordPress integration
WooCommerce integration
routing (permalinks, query vars, rewrite rules)
data adapters (product, category, variation, cart, account, search)
security (nonces, AJAX verification, capability checks, escaping)
menus (primary, secondary, footer, mega, mobile)
search (WP native, AJAX live)
account/authentication (login, logout, registration, dashboard)
cart (add, update, remove, count, mini-cart API)
checkout (WooCommerce checkout flow, payment gateway surface)
Customizer infrastructure (register, transport, save, reset, preview)
design resolution (aether_resolve_design_path, active-pack isolation)
platform hooks/contracts (actions, filters, data bridges)
demo system (manifests, fallback products/categories, purchase prevention)
active-pack isolation (only current client CSS/JS/fonts loaded)
client workspace separation (never mix client data into Golden Core)
```

### Client Frontend (PRESENTATION — fully replaceable)

```
complete HTML structure per page family
CSS styling, layout, typography, spacing, color
JavaScript behavior (animations, interactions, UI state)
responsive design (breakpoints, mobile/desktop behavior)
image assets, fonts, icons
visual hierarchy and information architecture
client-specific UI components (mega menus, sliders, accordions, etc.)
presentation-layer event handling (click, scroll, hover, resize)
```

### Bridge (CONNECTOR — client-specific, never Golden Core)

```
data mapping (WooCommerce data → client DOM targets)
runtime configuration (Customizer values → client presentation)
URL translation (WooCommerce routes → client link structure)
client-state translation (WooCommerce states → client display format)
business-action translation (client DOM events → WooCommerce AJAX endpoints)
nonce/security forwarding (platform nonces → client AJAX calls)
fallback logic (missing data → placeholder/default rendering)
```

### Demo System (FALLBACK CONTENT — never real business data)

```
curated demo product manifests
approved demo image URLs
demo categories and collections
default hero/logo/heading content
purchase prevention (demo products → add-to-cart blocked)
non-destructive switching (real content exists → demo hidden)
```

---

## Platform Capability Requirements

Every capability in this table MUST be present after any frontend workflow (creation, replacement, or edit). Loss of any capability is a blocking defect.

### Core Commerce

| # | Capability | Platform Owner | Frontend Must Provide | Bridge Must Map | Test |
|---|-----------|---------------|----------------------|----------------|------|
| C1 | Simple product display | WooCommerce | Product template slot | product data bridge | title, price, SKU, image, gallery, add-to-cart |
| C2 | Variable product display | WooCommerce | Variation selector UI | variation data bridge | options render, selection updates price/image/SKU |
| C3 | Product gallery | WooCommerce | Gallery component | gallery data bridge | images load, navigation works, thumbnails responsive |
| C4 | Product pricing | WooCommerce | Price display slot | price data bridge | regular/sale price, currency, variation price update |
| C5 | Stock status | WooCommerce | Stock display slot | stock data bridge | in-stock, out-of-stock, backorder states render |
| C6 | Add to cart | WooCommerce | Add-to-cart button/DOM | cart bridge (AJAX endpoint) | simple product adds, variable product adds with selection |
| C7 | Cart management | WooCommerce | Cart page/DOM | cart bridge (CRUD) | add, update quantity, remove, clear, count display |
| C8 | Cart count badge | WooCommerce | Cart count slot | cart count bridge | count updates on add/update/remove |
| C9 | Checkout | WooCommerce | Checkout form surface | checkout bridge | form renders, submission works, payment surface visible |
| C10 | Mini-cart | WooCommerce (if supported) | Mini-cart DOM | cart data bridge | opens, shows items, updates live |

### WordPress Core

| # | Capability | Platform Owner | Frontend Must Provide | Bridge Must Map | Test |
|---|-----------|---------------|----------------------|----------------|------|
| W1 | Homepage | WordPress | Homepage template | data bridge | renders correctly, data populates |
| W2 | Shop/archive | WordPress | Shop template | product query bridge | product grid renders, pagination works |
| W3 | Category pages | WordPress | Category template | category data bridge | products filter, heading shows, breadcrumb works |
| W4 | Search results | WordPress | Search template | search bridge | query executes, results render, empty state shows |
| W5 | Blog | WordPress | Blog template | post query bridge | posts render, pagination works |
| W6 | Single post | WordPress | Article template | post data bridge | content, author, date, featured image render |
| W7 | Static pages | WordPress | Page template | content bridge | page content renders |
| W8 | 404 | WordPress | 404 template | — | 404 page renders with navigation |
| W9 | Menus | WordPress | Menu DOM slots | menu bridge | primary, secondary, footer, mobile menus render |
| W10 | Search UI | WordPress | Search input/overlay | search route | open, type, submit, results, close, Escape, mobile |
| W11 | Login | WordPress | Login form | auth bridge | login works, invalid credentials show error |
| W12 | Account | WordPress | Account pages | account bridge | dashboard, orders, addresses, logout |
| W13 | Customizer | WordPress | Customizer-compatible slots | Customizer bridge | logo, hero, heading, announcement, footer, social round-trip |
| W14 | Routing | WordPress | Correct URL structure | route bridge | all permalinks resolve correctly |
| W15 | Security | WordPress/Woo | Nonce forwarding, escaping | security bridge | no exposed secrets, nonces validated, output escaped |

### Presentation Features

| # | Capability | Platform Owner | Frontend Must Provide | Bridge Must Map | Test |
|---|-----------|---------------|----------------------|----------------|------|
| P1 | Responsive layout | Client | Breakpoints at 1440, 1024, 768, 390 | — | layout adapts, interactions work at all sizes |
| P2 | Animations | Client | Client-specific animations | — | performant, no layout shift |
| P3 | Mega menu | Client | Mega menu DOM + hover behavior | menu data bridge | opens, navigates, closes, mobile variant |
| P4 | Footer | Client | Footer layout + social slots | social data bridge | links correct, social icons render |
| P5 | Hero/banner | Client | Hero slot | Customizer hero bridge | image/text round-trip |
| P6 | Announcement bar | Client | Announcement slot | Customizer announcement bridge | content round-trip |

---

## Feature-Capability Matrix Template

Every client pack MUST include a file `FEATURE-CAPABILITY-MATRIX.md` with this structure:

```markdown
# [CLIENT NAME] — Feature-Capability Matrix

## Status Legend
- SUPPORTED: Frontend + bridge fully implement this capability
- PARTIAL: Some aspects work, others need fallback
- UNSUPPORTED: Intentionally not implemented (must be documented)
- BLOCKED: Required capability has no implementation (blocking defect)

## Matrix

| Platform capability | Frontend target | Bridge | Fallback | Test | Status |
|---------------------|----------------|--------|----------|------|--------|
| Simple product | product template | product data | generic render | product test | SUPPORTED |
| Variable product | option UI | variant bridge | simple fallback | variation test | SUPPORTED |
| Cart | cart DOM | cart bridge | redirect to /cart | cart test | SUPPORTED |
| Search | search overlay | search route | WP redirect | search test | SUPPORTED |
| Login | login form | auth contract | WP login page | login test | SUPPORTED |
| Logo | logo slot | Customizer | default logo | round-trip | SUPPORTED |
| Hero | hero slot | Customizer | no hero | round-trip | SUPPORTED |
| Mini-cart | — | — | cart page redirect | — | UNSUPPORTED |
| Account dashboard | — | — | WP account page | — | UNSUPPORTED |

## Unsupported Capabilities (Documented)

List every capability intentionally not supported and why.

## Fallback Behaviors

Document what happens when a capability is unsupported or data is missing.
```

---

## JavaScript Compatibility Map

Every client pack MUST include `JS-COMPATIBILITY-MAP.md` describing:

```markdown
# JS Compatibility

## Platform JS (enqueued by Golden Core)
- [ ] platform.js — does client DOM clash?
- [ ] cart.js — does client have own cart handling?
- [ ] checkout.js — does client modify checkout?
- [ ] account.js — does client modify auth flows?

## Client JS
- [file.js] — purpose, scope, global pollution risk
- [file.js] — purpose, scope, global pollution risk

## Conflicts Identified
- [none / list conflicts and resolution]

## Data Attributes Contract
| Attribute | Set by | Read by | Purpose |
|-----------|--------|---------|---------|
| data-product-id | Platform | Client JS | product identification |
| data-variation-id | Platform | Client JS | variation identification |
```

---

## Route Map

Every client pack MUST include a route map:

| URL Pattern | Template | Data Source | Bridge |
|-------------|----------|-------------|--------|
| `/` | homepage | WP page + Customizer | homepage bridge |
| `/shop` | shop archive | WC product query | product grid bridge |
| `/product/[slug]` | product page | WC product data | product data bridge |
| `/product-cat/[slug]` | category archive | WC category query | category bridge |
| `/cart` | cart page | WC cart session | cart bridge |
| `/checkout` | checkout page | WC checkout | checkout bridge |
| `/account` | account page | WP auth + WC account | auth bridge |
| `/blog` | blog archive | WP post query | post bridge |
| `/blog/[slug]` | single post | WP post data | post data bridge |
| `/search` | search results | WP search query | search bridge |
| `/*` (catch-all) | 404 | — | — |

---

## Demo Contract

Every client pack MUST define:

```markdown
# Demo Contract

## Demo Products
- [list demo product identifiers]
- All demo products: non-purchasable
- Purchase prevention method: [bridge blocking / cart rejection / frontend hiding]

## Demo Categories
- [list demo category identifiers]

## Demo Assets
- Hero: [URL or local path]
- Logo: [URL or local path]
- Heading: [text]

## Transition Rules
- 0 real products → demo products shown
- 1+ real products → ALL demo products hidden
- 0 real categories → demo categories shown
- 1+ real categories → ALL demo categories hidden
- Custom logo uploaded → demo logo hidden
- Custom hero uploaded → demo hero hidden
- Custom heading set → demo heading hidden
- Custom content removed → corresponding demo content returns

## Non-Destructive Rules
- Demo records are NEVER automatically destroyed
- Removing real content brings demo back
- Demo products are never purchaseable
- No automatic scraping of demo assets
```

---

## Customizer Compatibility

Settings are classified into three tiers:

### Universal (all frontends)

| Setting | Bridge | Round-trip Test |
|---------|--------|----------------|
| Site logo | Customizer → client logo slot | upload → display → remove → default restores |
| Site favicon | Customizer → favicon tag | upload → tag present → remove → default |
| Site title | Customizer → title tag | change → reflects → reset → default |
| Tagline | Customizer → subtitle display | change → reflects → reset → default |

### Platform (all AUREON frontends)

| Setting | Bridge | Round-trip Test |
|---------|--------|----------------|
| Announcement bar | Customizer → announcement slot | enable → display → disable → hidden |
| Hero section | Customizer → hero slot | change image/text → reflects → reset → default |
| Footer content | Customizer → footer slots | change → reflects → reset → default |
| Social links | Customizer → social slots | add/change → reflects → remove → hidden |
| Heading font | Customizer → typography bridge | change → reflects → reset → default |
| Primary color | Customizer → color bridge | change → reflects → reset → default |

### Client-Presentation (may be unsupported)

| Setting | Behavior if Unsupported |
|---------|------------------------|
| Advanced layout options | Setting exists in Customizer but has no effect |
| Custom section ordering | Setting exists but client HTML structure is fixed |
| Animation toggles | Setting exists but client controls its own animations |
| Custom CSS fields | Setting exists but may conflict with client CSS |

**Rule:** Unsupported visual settings MUST NOT overwrite client design. The bridge must detect unsupported settings and silently ignore them.

---

## Audit Checklist for New Client Packs

Before a client pack is marked CLIENT-TEMPLATE-READY:

```
[ ] manifest.json present and complete
[ ] FEATURE-CAPABILITY-MATRIX.md present and complete
[ ] JS-COMPATIBILITY-MAP.md present and complete
[ ] TEMPLATE-CONTRACT.md present and complete
[ ] route map present
[ ] demo contract present
[ ] every REQUIRED platform capability has entry in matrix
[ ] every UNSUPPORTED capability is documented with reason
[ ] every SUPPORTABLE capability has fallback defined
[ ] no Golden Core modifications required
[ ] no third-party business API dependencies
[ ] all fonts licensed for redistribution
[ ] all assets have source + license documented
[ ] bridge covers all business actions
[ ] security review passed
[ ] test plan covers all 16 capability categories
```

---

## Test Categories

Every client pack must pass all 16 categories:

| # | Category | What It Proves |
|---|----------|---------------|
| 1 | WordPress integration | Core WP functions work |
| 2 | WooCommerce integration | Commerce functions work |
| 3 | Products | Product data displays correctly |
| 4 | Variations | Variable products work end-to-end |
| 5 | Categories | Category filtering and display work |
| 6 | Menus | All menu positions render correctly |
| 7 | Search | Search works across all surfaces |
| 8 | Account | Auth flow works completely |
| 9 | Cart | Cart CRUD works end-to-end |
| 10 | Checkout | Checkout flow completes |
| 11 | Customizer | Settings round-trip correctly |
| 12 | Demo | Demo content follows all rules |
| 13 | Security | No exposed secrets, nonces validated |
| 14 | Routing | All URLs resolve correctly |
| 15 | Responsive | Works at 1440, 1024, 768, 390 |
| 16 | Active-pack isolation | Only current client assets load |

---

## Acceptance Gate

A client frontend is accepted ONLY WHEN:

```
COMPLETE PRESENTATION
✅

ALL REQUIRED PLATFORM CAPABILITIES
✅

REAL WC DATA
✅

DEMO FALLBACK
✅

CUSTOMIZER
✅

MENUS
✅

SEARCH
✅

ACCOUNT
✅

CART
✅

CHECKOUT
✅

SECURITY
✅

RESPONSIVE
✅

NETWORK (no prohibited external calls)
✅

CONSOLE (no fatal/errors)
✅

ACTIVE-PACK ISOLATION
✅

ROLLBACK (old client can be restored)
✅

GOLDEN CORE UNTOUCHED
✅

CLEAN CLIENT STATE
✅
```

---

## Hard Stop Conditions

STOP if any of the following occur:

```
- Golden Core must be changed without generic defect proof
- WooCommerce core must be modified
- Required platform capability disappears after replacement
- Business logic moves into client presentation layer
- Demo data becomes real business data
- Old client cannot be restored
- New client leaks old client assets (CSS/JS/DOM)
- Routes become invalid
- Required image is broken
- Required platform capability has no presentation target
- Bridge must handle business logic instead of presentation mapping
```

---

## Document Links

| Document | Purpose |
|----------|---------|
| [GOLDEN-AUREON-FRONTEND-WORKFLOWS.md](GOLDEN-AUREON-FRONTEND-WORKFLOWS.md) | Master workflow index |
| [GOLDEN-AUREON-PRODUCTIZED-WORKFLOW.md](GOLDEN-AUREON-PRODUCTIZED-WORKFLOW.md) | Complete 42-phase master prompt |
| [NEW-CLIENT-TEMPLATE-CREATION-PLAN.md](NEW-CLIENT-TEMPLATE-CREATION-PLAN.md) | Phase 1-16: Template creation |
| [FRONTEND-REPLACEMENT-PLAN.md](FRONTEND-REPLACEMENT-PLAN.md) | Phase 17-20: Pack replacement |
| [FRONTEND-EDIT-PLAN.md](FRONTEND-EDIT-PLAN.md) | Phase 3: Safe edits |
| [DEMO-REFERENCE-CONTENT-SYSTEM.md](DEMO-REFERENCE-CONTENT-SYSTEM.md) | Demo content architecture |

---

## Version History

| Version | Date | Change |
|---------|------|--------|
| 1.0 | 2026-09-01 | Initial contract — derived from Golden AUREON architecture audit |
