# AUREON CLIENT DESIGN LAYER

## Master UI/UX Redesign Architecture, Rules, Workflow, Contracts & Execution Plan

VERSION: 1.0
STATUS: CLIENT-DESIGN STANDARD
CORE STATUS: FROZEN — machine-enforced by `scripts/check-core-freeze.php`
EDITABLE DESIGN LAYER: `frontend/designs/vineta/` (and future `frontend/designs/<pack>/`)

Companion documents: `docs/AUREON-MASTER-BASELINE.md` (current verified architecture) ·
`docs/AUREON-DESIGN-PACK-CONTRACT.md` (technical contract shapes) ·
`docs/AUREON-PHASE-C-RELEASE-GATE.md` (core freeze evidence).

---

# 1. PURPOSE

The purpose of this document is to define exactly how a client-specific storefront redesign must be created inside AUREON without breaking:

* WordPress
* WooCommerce
* AUREON Core
* AUREON Studio plugin
* routing
* dynamic data
* product data
* pricing
* inventory
* cart
* checkout
* account
* search
* blog
* menus
* AJAX
* REST
* security
* Customizer contracts
* existing frontend engine contracts
* existing integrations
* existing business logic

The Client Design Layer exists exclusively to control:

* HTML
* CSS
* JS
* images
* fonts
* animation
* layout
* visual hierarchy
* responsive UI
* sections
* components
* visual UX
* interaction presentation

The design layer may completely change the appearance of the storefront.
It must not change the underlying commerce or business engine merely because the visual design changes.

---

# 2. FUNDAMENTAL ARCHITECTURE

AUREON consists of:

A. FROZEN CORE
B. DYNAMIC CONTRACTS
C. CLIENT DESIGN LAYER

The architecture is:

```text
FROZEN CORE
    ↓
DYNAMIC CONTRACTS
    ↓
CLIENT DESIGN LAYER
    ↓
HTML + CSS + JS + ASSETS
    ↓
CLIENT EXPERIENCE
```

The client design layer consumes the data and capabilities provided by the Core.
The client design layer does not redefine those capabilities.

---

# 3. HARD BOUNDARY

## 3.1 FROZEN

The following are Core and must not be modified for ordinary client visual requests:

* WordPress integration
* WooCommerce integration
* AUREON runtime infrastructure
* plugin registration
* security model
* routing engine
* REST architecture
* AJAX architecture
* cart logic
* checkout logic
* account logic
* product data source
* search backend
* blog backend
* menu source
* Customizer storage architecture
* dynamic-data engine
* authentication
* authorization
* database logic
* order logic
* payment logic
* stock logic
* pricing calculation
* tax logic
* shipping logic
* session logic
* nonce architecture
* capability checks
* core asset infrastructure
* design-pack resolver
* shared contract infrastructure

Frozen paths (checksum-enforced): `themes/aureon/`, `plugins/aureon-studio/`,
`frontend/views/`, `frontend/tokens/`, `frontend/adapters/`, plus `themes/aureon/ferm-page.php`.
Do not modify these simply because a client wants a different appearance.

---

# 4. EDITABLE CLIENT DESIGN LAYER

Primary editable location: `frontend/designs/vineta/`

The client design layer controls:

## HTML

* markup
* component structure
* element hierarchy
* wrapper structure
* visual containers
* product-card markup
* hero markup
* navigation markup
* footer markup
* section composition
* typography markup
* badges
* promotional blocks
* visual states
* modal presentation
* drawer presentation

## CSS

* colors
* typography
* spacing
* margins
* padding
* borders
* shadows
* backgrounds
* gradients
* widths
* heights
* max widths
* grids
* flex layouts
* positioning
* responsive behavior
* transitions
* animation styles
* hover states
* focus styles
* visual states

## JS

* visual interactions
* sliders
* tabs
* accordions
* drawers
* menus
* modal presentation
* animations
* scroll effects
* counters
* carousels
* visual product interactions
* filtering presentation
* visual search behavior
* UI state transitions
* mobile navigation
* client-specific interaction patterns

JS must consume existing dynamic/AUREON APIs and data contracts rather than replacing them.

## ASSETS

* logos
* icons
* illustrations
* images
* background imagery
* fonts
* videos
* 3D visual assets
* client-specific graphics

---

# 5. THE GOLDEN RULE

Every client request must first be classified: **VISUAL** or **FUNCTIONAL**.

VISUAL requests belong in the Client Design Layer.
FUNCTIONAL requests must first be checked against existing AUREON Core capabilities.

Examples:

| Request | Classification |
|---|---|
| "Make the header taller." | DESIGN LAYER |
| "Move the logo to the left." | DESIGN LAYER |
| "Use a black luxury style." | DESIGN LAYER |
| "Add animation to product cards." | DESIGN LAYER |
| "Change product-card layout." | DESIGN LAYER |
| "Create a horizontal product slider." | DESIGN LAYER |
| "Make the mobile menu full-screen." | DESIGN LAYER |
| "Show actual products." | EXISTING DYNAMIC DATA |
| "Change product price calculation." | CORE — NOT A DESIGN CHANGE |
| "Change checkout payment logic." | CORE — NOT A DESIGN CHANGE |
| "Change stock calculation." | CORE — NOT A DESIGN CHANGE |
| "Change cart calculation." | CORE — NOT A DESIGN CHANGE |

---

# 6. CLIENT REQUEST TRANSLATION PROCESS

Never start coding directly from a client's vague request.
Convert the client request into a visual specification.

```text
CLIENT REQUEST
    ↓
DESIGN REQUIREMENTS
    ↓
PAGE / COMPONENT INVENTORY
    ↓
DESIGN SYSTEM
    ↓
HTML PLAN
    ↓
CSS PLAN
    ↓
JS INTERACTION PLAN
    ↓
DYNAMIC DATA MAPPING
    ↓
RESPONSIVE PLAN
    ↓
IMPLEMENTATION
    ↓
VISUAL QA
    ↓
DYNAMIC REGRESSION
    ↓
RELEASE
```

---

# 7. PHASE D0 — CLIENT DESIGN DISCOVERY

Before editing code, create `CLIENT-DESIGN-BRIEF.md`.

Record:

* brand name
* industry
* audience
* style
* visual references
* colors
* typography
* imagery direction
* spacing philosophy
* border style
* button style
* product presentation
* navigation style
* header style
* footer style
* animation style
* mobile philosophy
* desktop philosophy
* page priorities
* special interactions
* required sections
* sections to remove
* sections to reorder
* client-specific assets

Separate: **MUST HAVE / NICE TO HAVE / OPTIONAL / OUT OF SCOPE**.

Do not begin implementation until the visual direction is understood.

---

# 8. PHASE D1 — CURRENT DESIGN-LAYER FORENSIC BASELINE

Before changing the client design, inventory the existing Vineta design pack.

Map:

* files
* HTML templates
* sections
* components
* CSS files
* JS files
* assets
* fonts
* route mappings
* dynamic consumers
* reusable components

Identify each component's current role.
Do not rewrite a component just because its code could be cleaner.
Preserve working contracts.

---

# 9. PHASE D2 — PAGE INVENTORY

Every client design must have a page matrix. Minimum:

HOME · SHOP · CATEGORY · PRODUCT · SEARCH · BLOG ARCHIVE · BLOG SINGLE · ABOUT · CONTACT ·
STORE LOCATOR · CART · CHECKOUT · ACCOUNT · 404

Also identify optional pages: WISHLIST · COMPARE · FAQ · LOOKBOOK · LANDING PAGES ·
CAMPAIGN PAGES · CUSTOM COLLECTIONS.

For every page define: layout, sections, components, dynamic data, responsive behavior,
interactions, assets.

---

# 10. PHASE D3 — DESIGN SYSTEM

Before building dozens of individual styles, establish:

## Color system

background · surface · text · muted text · heading · border · primary · secondary · accent ·
success · warning · error

## Typography

body font · heading font · display font · button font · navigation font · price font ·
metadata font

Define per role: size, weight, line-height, letter-spacing, casing.

## Spacing

Define a coherent spacing scale. Avoid random values throughout the design.

## Containers

max width · wide width · narrow width · page padding · mobile padding · grid gaps

## Radius

none · small · medium · large · pill

## Shadows

Define a small reusable scale.

## Motion

instant · fast · standard · slow · reveal · hover · modal · page transition

---

# 11. PHASE D4 — COMPONENT INVENTORY

Create a reusable client component map. Minimum:

ANNOUNCEMENT BAR · HEADER · DESKTOP NAVIGATION · MOBILE NAVIGATION · SEARCH · HERO · BANNER ·
CATEGORY SELECTOR · PRODUCT CARD · PRODUCT GRID · PRODUCT SLIDER · PRODUCT BADGE · PRICE ·
RATING · QUICK ADD · QUICK VIEW · PRODUCT GALLERY · PRODUCT INFORMATION · VARIANTS · QUANTITY ·
ADD TO CART · RELATED PRODUCTS · REVIEWS · FAQ · TESTIMONIALS · BLOG CARD · BLOG GRID ·
NEWSLETTER · CONTACT · FOOTER · CART DRAWER · CART PAGE · CHECKOUT PRESENTATION ·
ACCOUNT PRESENTATION · 404

Each component must have: visual responsibility, HTML structure, CSS scope, JS scope,
dynamic fields, states, responsive rules, accessibility rules.

---

# 12. DYNAMIC DATA CONTRACT

The design must treat dynamic data as INPUT.

Example — PRODUCT CARD INPUT (actual runtime field names, see
`docs/AUREON-DESIGN-PACK-CONTRACT.md` §4.2):

```text
product.id
product.name
product.url
product.price          (WC html)
product.price_plain
product.price_cents
product.old_price_plain   (compare-at price)
product.image
product.gallery
product.rating
product.reviews        (review_count)
product.badge
product.tagline
product.add_to_cart_url
product.product_type
```

The designer can transform INPUT → HTML → CSS → JS, but cannot change where the product
data originates without a Core-level architectural reason.

---

# 13. DYNAMIC CONTRACT RULE

Never replace `{{ dynamic value }}` with a hardcoded value unless it is intentionally
static presentation text.

Never convert real product data into fake demo data just to match a screenshot.

Never hardcode, when a dynamic source already exists:

* prices
* stock
* product names
* SKU
* cart count
* account information
* orders
* search results
* product URLs
* real legal links
* menus

---

# 14. HTML REDESIGN RULES

HTML can be completely redesigned. However:

* Do not remove required dynamic data hooks.
* Do not remove required data attributes (`data-aureon-slot`, `data-vineta-add`,
  `data-product-id`).
* Do not rename required IDs/classes used by Core JS unless the consumer is updated
  safely inside the design layer.
* Do not remove: nonce values, data endpoints, product identifiers, variation
  identifiers, cart hooks, form names, action URLs, dynamic placeholders, ARIA state
  contracts, required accessibility attributes.

When a hook must change: SEARCH ALL CONSUMERS FIRST.
Only modify the design-layer consumer if possible.

---

# 15. CSS RULES

All client visual CSS must be isolated to the design pack.

Prefer component-scoped selectors over global destructive selectors.
Avoid `* { ... }` unless deliberately controlled.
Do not globally override WordPress/WooCommerce internals unless necessary.
Avoid `!important` as a structural solution — use it only where a documented
compatibility reason exists.
Do not change browser behavior globally.
Do not introduce CSS that breaks: WooCommerce forms, checkout, account, cart,
admin integrations, plugin widgets.

---

# 16. JS RULES

Client JS is for visual interaction.

Allowed: animation, sliders, menus, tabs, accordions, visual filtering, drawers, modals,
transitions, client-side state presentation.

Existing Core functionality must remain authoritative for: add to cart, checkout, orders,
login, authentication, pricing, inventory, payment, shipping, REST operations, security.

Do not create a second commerce engine in design JS.

---

# 17. CART RULE

A redesigned cart may look completely different.

Allowed: drawer, sidebar, full page, modal, floating cart, custom item cards,
custom quantity controls.

But the underlying cart contract must remain AUREON/WooCommerce controlled.

Design JS may trigger: existing add-to-cart (`data-vineta-add` / `VinetaCart.add()`),
existing cart refresh, existing cart endpoints.
It must not independently calculate authoritative cart totals.

---

# 18. PRODUCT PAGE RULE

The client may completely redesign: gallery, thumbnails, layout, product information,
price display, badges, tabs, accordions, sticky purchase panel, variant selector styling,
related-product layout.

But preserve real: product ID, variation IDs, price, stock, attributes, availability,
add-to-cart, quantity, reviews, product URL.

---

# 19. CHECKOUT RULE

Checkout visual design may change.
Do not rewrite: payment, order creation, tax, shipping, WooCommerce validation,
customer data processing.
Use WooCommerce's existing checkout functionality.
The design layer may style and arrange the presentation.

---

# 20. ACCOUNT RULE

The account page can be completely visually redesigned.

Allowed: dashboard cards, sidebar, tabs, mobile drawer, order cards, account navigation,
custom typography, custom forms.

But preserve WooCommerce endpoint behavior and authentication.

---

# 21. SEARCH RULE

Search visual UI may be redesigned completely.

Allowed: search overlay, predictive search presentation, dropdown, command-bar style,
full-page search, filters, result cards.

But search data must remain authoritative.
Do not replace real search with hardcoded suggestions/results.

---

# 22. HEADER RULE

Header can be redesigned from scratch visually.

Possible structures: transparent, floating, centered logo, split navigation, mega menu,
minimal, luxury, editorial, sticky, multi-row, mobile fullscreen.

But preserve: real menu data, search, account, cart, logo/branding sources,
dynamic cart count, authentication state.

---

# 23. FOOTER RULE

Footer visual structure is fully editable.

However preserve: real menus, real legal pages, real contact information, real social
links, configured newsletter behavior, dynamic site information.

Never reintroduce placeholder `#` or fake contact information into production.

---

# 24. RESPONSIVE SYSTEM

Do not treat mobile as an afterthought.
Every component must define: DESKTOP / TABLET / MOBILE.

At minimum test: **1440 · 1280 · 1024 · 768 · 390 · 360**
(automated: `node scripts/qa/shots-6vp.cjs`).

For each breakpoint verify: overflow, spacing, typography, image ratio, grid, navigation,
touch targets, buttons, drawers, sticky elements, modal behavior, text wrapping.

---

# 25. MOBILE-FIRST VALIDATION

A design is not approved because desktop looks correct.
Mobile must be intentionally designed. Do not simply shrink desktop.

Define: mobile hierarchy, mobile navigation, mobile product cards, mobile hero,
mobile typography, mobile spacing, mobile checkout presentation, mobile account layout.

---

# 26. IMAGE RULES

Use: correct aspect ratio, responsive images, optimized assets, WebP/AVIF where
appropriate, lazy loading when appropriate, intentional quality settings.

Do not alter image dimensions in ways that cause layout shift.
Do not load unnecessarily huge images on mobile.

---

# 27. FONT RULES

Use only required fonts. Avoid loading unnecessary font families.
Define: family, weight, fallback, loading strategy.
Avoid excessive font variants.

---

# 28. ANIMATION RULES

Animation must improve UX. Avoid excessive animation.

Every major animation should consider: performance, reduced motion, mobile behavior,
interaction timing.

Do not create animation loops that consume CPU continuously without purpose.
Respect `prefers-reduced-motion`.

---

# 29. ACCESSIBILITY RULES

Visual redesign must preserve accessibility.

Every component must maintain: semantic HTML, heading hierarchy, button labels, link
purpose, form labels, keyboard operation, focus visibility, focus order, ARIA where
necessary, image alt behavior.

Do not remove semantic markup simply to obtain visual similarity.

---

# 30. SEO RULE

Do not damage SEO while changing presentation.

Preserve: headings, content, canonical behavior, meaningful links, structured product
information, metadata architecture.

Visual design should not replace meaningful content with images.

---

# 31. PERFORMANCE RULE

Never optimize by deleting functionality blindly.

The design layer should minimize: unused CSS, unused JS, duplicate libraries, huge
images, unnecessary fonts, unnecessary video, unnecessary 3D assets.

Load expensive visual features only where needed (manifest page gates).

---

# 32. CLIENT PAGE-BUILD ORDER

Build client designs in this order:

**PHASE 1 — Global foundation**: colors, typography, spacing, containers, buttons, forms,
global responsive rules.

**PHASE 2 — Global shell**: announcement, header, navigation, search, footer, mobile
navigation.

**PHASE 3 — Homepage**: hero, categories, featured products, promotional sections,
testimonials, FAQ, newsletter.

**PHASE 4 — Commerce listing**: shop, category, product cards, filters, sorting,
pagination/load-more.

**PHASE 5 — Product**: gallery, information, purchase area, variants, related products,
reviews.

**PHASE 6 — Content**: blog, article, about, contact, store locator.

**PHASE 7 — Commerce utility**: cart drawer, cart page, checkout presentation, account.

**PHASE 8 — Edge cases**: search empty, product unavailable, out of stock, empty cart,
empty results, 404, validation errors, mobile edge cases.

---

# 33. DO NOT REDESIGN EVERYTHING AT ONCE

Use controlled batches.

BATCH 1: Foundation + header + footer
BATCH 2: Homepage
BATCH 3: Product cards + collection
BATCH 4: Product page
BATCH 5: Cart + search + account
BATCH 6: Blog + content
BATCH 7: Responsive + animation polish
BATCH 8: Performance + accessibility + final QA

After each batch: test → screenshot → compare → fix → commit.

---

# 34. CLIENT REVIEW CYCLE

For each major design milestone:

1. Generate screenshots.
2. Compare against approved design.
3. Verify desktop.
4. Verify mobile.
5. Verify dynamic values.
6. Verify console.
7. Verify links.
8. Verify interactions.
9. Record deviations.
10. Obtain design approval.
11. Freeze that batch.

---

# 35. DESIGN FREEZE

When a client approves a design, CREATE `DESIGN-FREEZE.md`.

Record: approved screenshots, approved routes, approved breakpoints, approved colors,
approved typography, approved components, approved interactions, approved assets.

After freeze: do not change visual behavior casually.
Additional client changes become a new revision.

---

# 36. CLIENT REVISION RULE

Every client request must be classified:

**VISUAL BUG / DESIGN CHANGE / CONTENT CHANGE / DYNAMIC DATA CHANGE / CORE FEATURE REQUEST**

* VISUAL BUG → fix design layer.
* DESIGN CHANGE → fix design layer.
* CONTENT CHANGE → use existing dynamic/content system where possible.
* DYNAMIC DATA CHANGE → do not fake it in HTML; use the existing contract.
* CORE FEATURE REQUEST → stop and evaluate separately before modifying Core.

---

# 37. CORE CHANGE FIREWALL

If a client visual request appears to require Core changes: STOP.
Do not modify Core immediately.

Produce `CORE-CHANGE-REQUEST.md` including: client request, why the design layer cannot
currently solve it, current contract, requested change, affected Core files, regression
risk, alternative design-only solutions, recommended solution, tests required.

Only after explicit architectural approval may Core be changed
(and then: reviewed re-freeze via `php scripts/check-core-freeze.php --generate`).

---

# 38. DESIGN-LAYER FILE OWNERSHIP

Every file must have an obvious owner.

CORE: WordPress, WooCommerce, AUREON runtime, shared infrastructure, security, routing,
dynamic contracts.

DESIGN: HTML, CSS, JS, assets, visual configuration, design-specific components.

Do not mix responsibilities.

---

# 39. NAMING RULES

Use clear design-specific names. Examples: `client-header`, `client-hero`,
`client-product-card`, `client-footer`.

Avoid vague names such as: `test`, `new`, `new2`, `final`, `final2`, `old`, `old-new`, `temp`.

Do not create disposable architecture inside production code.

---

# 40. NO DUPLICATE SYSTEMS

Never create a second: routing system, cart system, product engine, search engine,
Customizer engine, dynamic data engine, AJAX architecture — because the design layer
needs different UI.

Change presentation, not the business engine.

---

# 41. DESIGN JS CONTRACT

Before calling any existing AUREON/WooCommerce behavior from client JS, document:
endpoint, event, required data, response, error behavior, refresh behavior.

Do not guess existing APIs. Search the codebase first.

---

# 42. EVENT RULE

Client JS may listen to existing events. It may emit design-specific events.
Do not rename or break Core events. Existing Core event contracts must remain unchanged.

---

# 43. AJAX RULE

Client JS may trigger existing AJAX requests.
Do not recreate server-side AJAX logic inside the design layer.
Do not bypass nonces. Do not hardcode authentication assumptions.

---

# 44. REST RULE

Client design may consume existing safe REST data where permitted.
Do not create new privileged REST endpoints for cosmetic behavior.
Do not expose private data to the browser.

---

# 45. SECURITY RULE

No design request justifies: eval, arbitrary PHP, bypassing nonce, bypassing capability
checks, exposing private data, unsafe URL injection, unsafe HTML injection,
arbitrary file loading.

---

# 46. VISUAL QUALITY GATE

Before a design is approved, check: spacing consistency, alignment, typography,
hierarchy, image quality, visual rhythm, button consistency, card consistency,
navigation consistency, responsive behavior, animation quality, whitespace,
content density, mobile usability.

---

# 47. FUNCTIONAL SAFETY GATE

Before approval, check: product links, add-to-cart, quantity, cart, search, account,
checkout, menu, footer links, forms, blog, 404, dynamic prices, dynamic product
information, responsive behavior.

A design is rejected if visual quality improves while functionality regresses.

---

# 48. CONSOLE GATE

Final browser console: **0 unintended errors**.
Warnings must be understood. Third-party warnings must be identified.
Do not hide errors just to make the console appear clean.

---

# 49. NETWORK GATE

Final browser network inspection — no unexplained: 404, 500, 403, failed JavaScript,
failed CSS, missing font, missing image, missing video, missing 3D asset.

Optional demo assets may use documented graceful fallback behavior.

---

# 50. SCREENSHOT GATE

For every approved design, at minimum: **1440 desktop + 390 mobile**.
Recommended: 1280, 1024, 768, 360 (automated: `node scripts/qa/shots-6vp.cjs`).

Capture the full page set. Compare against the client-approved design and the previous
approved AUREON baseline.

---

# 51. DYNAMIC DATA SCREENSHOT RULE

A screenshot must contain real dynamic data where production data is available.

Do not approve screenshots containing: fake prices, fake cart count, fake product names,
fake stock, fake search results, fake links — unless explicitly marked as a design
mockup before dynamic integration.

---

# 52. FINAL DESIGN RELEASE MATRIX

Every client project must finish with this matrix (PASS required in every column):

| PAGE | DESKTOP | MOBILE | DYNAMIC | FUNCTIONAL | CONSOLE | NETWORK | ACCESSIBILITY | STATUS |
|---|---|---|---|---|---|---|---|---|
| HOME | PASS | PASS | PASS | PASS | PASS | PASS | PASS | PASS |
| SHOP | PASS | PASS | PASS | PASS | PASS | PASS | PASS | PASS |
| PRODUCT | PASS | PASS | PASS | PASS | PASS | PASS | PASS | PASS |
| ... | ... | ... | ... | ... | ... | ... | ... | ... |

Repeat for every route.

---

# 53. CLIENT DESIGN PACK RELEASE

When complete, confirm:

* Core untouched (`php scripts/check-core-freeze.php` → PASS)
* only approved design files changed
* dynamic contracts preserved
* no duplicate files
* no stale assets
* no unused assets where avoidable
* no console errors
* no missing required assets (`php scripts/check-assets.php`)
* Customizer contracts intact (`php scripts/check-customizer-contracts.php` → DEAD 0 / ORPHANED 0)
* screenshots pass (`node scripts/qa/shots-6vp.cjs`)
* responsive pass
* commerce pass (`node scripts/qa/cart-final.cjs`)
* accessibility pass

Then create `CLIENT-DESIGN-RELEASE.md`.

---

# 54. GOLDEN RULE FOR OPENCODE / AI CODING AGENTS

Before changing a file, determine:

1. Is this Core?
2. Is this Dynamic Contract?
3. Is this Design Layer?
4. Is this shared infrastructure?

If DESIGN: continue.
If CORE: stop and justify.
If SHARED: perform dependency analysis first.
If uncertain: do not guess. Search the codebase and trace the consumer.

---

# 55. REQUIRED AI CHANGE REPORT

After each client redesign batch, report:

FILES CHANGED · DESIGN COMPONENTS CHANGED · DYNAMIC CONTRACTS USED · CORE FILES TOUCHED ·
WHY CORE WAS TOUCHED · FUNCTIONAL TESTS · VISUAL TESTS · SCREENSHOTS · CONSOLE · NETWORK ·
ACCESSIBILITY · REGRESSIONS · REMAINING ITEMS

The preferred result is: **CORE FILES TOUCHED = 0** for normal client visual redesigns.

---

# 56. FINAL CLIENT DESIGN PRINCIPLE

The client should be able to say: "Make the store completely different."

And the development process should be able to answer:
"Yes — we will change the presentation layer while preserving the AUREON engine."

The goal is:

```text
ONE CORE
MANY DESIGNS
ONE DYNAMIC CONTRACT
INFINITE VISUAL PRESENTATIONS
```

The design layer may evolve aggressively. The Core must remain stable.

---

# 57. MASTER SUCCESS CRITERIA

A client design is COMPLETE only when:

* VISUAL ✓ matches approved client design
* RESPONSIVE ✓ desktop and mobile verified
* DYNAMIC ✓ real WordPress/WooCommerce data works
* COMMERCE ✓ product/cart/checkout/account work
* ROUTING ✓ every route remains correct
* JS ✓ visual interactions work, no unintended console errors
* ASSETS ✓ required assets exist, no unexplained network failures
* ACCESSIBILITY ✓ keyboard and semantic behavior preserved
* PERFORMANCE ✓ no unnecessary payload introduced
* SECURITY ✓ no security contract bypass
* ARCHITECTURE ✓ Core remains frozen

The final goal is not merely to produce a good-looking page.
The final goal is to produce a completely different client experience while leaving the
proven AUREON business/runtime foundation intact.

---

**Recommended operating model** — use this exact progression for every client:

Client brief → visual audit → design system → component map → homepage → header/footer →
collection → product → cart/search/account → content pages → mobile → animation →
performance → accessibility → full dynamic regression → design freeze.

And the most important practical rule:

> **Normal client redesign = `CORE FILES TOUCHED: 0`.**

That one metric protects the architecture. If a client asks for something that appears to
require Core changes, it becomes a separate architectural request — not something the
coding agent is allowed to quietly modify.

---

END OF AUREON CLIENT DESIGN LAYER STANDARD
==========================================
