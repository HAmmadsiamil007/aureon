# GOLDEN AUREON — PRODUCTIZED CLIENT FRONTEND WORKFLOW

**Version:** 2.0
**Date:** 2026-09-01
**Status:** PERMANENT MASTER PROMPT — Use this for every new client frontend lifecycle.

---

## Purpose

This is the **complete master prompt** for the Golden AUREON client frontend productization workflow.

It covers every phase from workspace creation through final acceptance, including template creation, compatibility audit, bridge mapping, replacement, future edits, and full regression.

**Use this document when:**
- Onboarding a completely new client frontend
- Replacing an existing client pack
- Performing major edits to an active client
- Running full acceptance regression

**Do NOT use this document for:**
- Minor visual tweaks (use FRONTEND-EDIT-PLAN.md)
- Golden Core bug fixes (use core regression process)

---

## Operating Model

```
GOLDEN AUREON BACKUP
        │
        ├── NEVER EDIT
        │
        ↓
CLIENT WORKSPACE
        │
        ├── Template Creation
        │
        ├── Compatibility Contract
        │
        ├── Bridge Mapping
        │
        ├── Replacement
        │
        └── Future Edits
        │
        ↓
CLIENT ACCEPTANCE
        │
        └── Full regression
```

### Change Classification

```
PRESENTATION
→ Client Pack

DATA / BUSINESS
→ Bridge / WordPress / WooCommerce

PLATFORM CAPABILITY
→ Golden Core ONLY after generic-defect proof
```

---

## Absolute Golden Core Rule

Golden AUREON v1.0 is a protected baseline.

**NEVER modify Golden Core merely to accommodate a client frontend.**

Golden Core includes:

- WordPress integration
- WooCommerce integration
- Routing
- Data adapters
- Security
- Menus
- Search
- Account
- Cart
- Checkout
- Customizer infrastructure
- Design resolution
- Active-pack isolation
- Platform hooks/contracts

If a Core change is proposed:

**STOP.**

Prove:

1. Genuinely generic defect
2. Affects multiple clients
3. Cannot be solved by client pack
4. Cannot be solved by bridge
5. Smallest possible change
6. Full regression plan

---

## The Three Workflows

### Workflow 1 — Template Creation (Phases 0–16)

```
CLIENT SOURCE
    ↓
CLIENT WORKSPACE
    ↓
CLIENT-TEMPLATE-READY
```

**When to use:** New premium frontend from external source.

### Workflow 2 — Frontend Replacement (Phases 17–20)

```
CURRENT CLIENT ARCHIVED
    ↓
NEW CLIENT INSTALLED
    ↓
ACTIVE-PACK ISOLATION VERIFIED
```

**When to use:** Replacing the currently active client pack.

### Workflow 3 — Frontend Edit (Phases 3 + 43–45)

```
CONTRACT SNAPSHOT
    ↓
EDIT
    ↓
CONTRACT DIFF
    ↓
TARGETED TEST → FULL REGRESSION
```

**When to use:** Modifying the active client's presentation.

---

## Phase 0 — Create Client Workspace

**NEVER work directly on the Golden backup.**

Create:

```
aureon/frontend/designs/[client-slug]/
```

Or:

```
client-[id]/
```

Preserve Golden Copy untouched.

---

## Phase 1 — Create Immutable Source Snapshot

If client supplies:

- HTML
- CSS
- JS
- Assets
- ZIP
- Repository
- Website export

**Freeze it first.**

Record:

```
source hash
file count
directory tree
page count
asset count
JS files
CSS files
fonts
images
videos
```

**NEVER modify original source.**

---

## Phase 2 — Determine Frontend Mode

### Complete-Page Mode (`complete_page: true`)

Preserve:

- HTML
- CSS
- JavaScript
- Animations
- Responsive behavior

### Component Mode (`complete_page: false`)

Use:

- Adapters
- Viewmodels
- Components
- Sections

**NEVER convert complete-page HTML into components just for convenience.**

---

## Phase 3 — Read Golden AUREON Contract

Read:

- Golden Core architecture
- Complete-page architecture
- WooCommerce contract
- Product/variation contract
- Cart/account/checkout contract
- Menus
- Search
- Customizer
- Demo system
- Active-pack isolation
- Security

The new frontend must **ADAPT TO THIS CONTRACT.**

---

## Phase 4 — Frontend Forensic Audit

Inventory every page family:

```
homepage
shop
product archive
category
collection
simple product
variable product
search
account
cart
checkout
blog
article
static pages
404
```

Inventory per page:

```
HTML structure
CSS files/selectors
JS files/globals
Assets (images, fonts, icons)
Forms and their actions
Routes and links
IDs and data attributes
JS hooks and event listeners
DOM contracts (elements that JS expects to exist)
```

---

## Phase 5 — Page Family Model

Determine:

```
exact static pages (one-off HTML)
reusable templates (data-driven, multiple records)
data-driven templates (WooCommerce loops, queries)
```

**Do NOT create one HTML file for every product/category when one template can represent many records.**

---

## Phase 6 — Dynamic Slot Inventory

For EVERY page family, identify:

```
product slots
category slots
collection slots
hero slots
logo slot
heading slot
announcement slot
footer slot
social slots
search slot
cart slot
account slot
related products
recommendations
editorial slots
```

Every dynamic slot must have:

```
DOM target (which element receives data)
data source (WooCommerce field, Customizer value, menu, etc.)
bridge mapping (how data reaches the DOM)
fallback (what shows when data is missing)
test (how to verify it works)
```

---

## Phase 7 — Business Action Inventory

Identify every business action:

```
add to cart
update cart
remove from cart
clear cart
checkout
login
logout
search
account management
variant selection
quantity selection
```

For every action:

```
presentation target (client DOM element that triggers it)
platform endpoint (WooCommerce AJAX handler)
nonce/security contract (which nonce, which action)
request format (what data is sent)
response format (what data comes back)
error state (what the user sees on failure)
```

---

## Phase 8 — Platform Capability Matrix

Create: `FEATURE-CAPABILITY-MATRIX.md`

Categories to audit:

```
WordPress
WooCommerce
Products
Variations
Categories
Menus
Search
Account
Cart
Checkout
Customizer
Demo
Security
Routing
Media
Performance
Client isolation
```

Columns:

```
capability
Golden platform support
frontend target
bridge required
fallback
test
status (SUPPORTED / PARTIAL / UNSUPPORTED / BLOCKED)
```

See [CLIENT-FRONTEND-CAPABILITY-CONTRACT.md](CLIENT-FRONTEND-CAPABILITY-CONTRACT.md) for the full capability table.

---

## Phase 9 — Customizer Compatibility

Classify settings into:

```
UNIVERSAL — all frontends must support (logo, favicon, title, tagline)
PLATFORM — all AUREON frontends support (announcement, hero, footer, social, heading, color)
CLIENT-PRESENTATION — may be unsupported (advanced layout, animation toggles, custom CSS)
```

For every supported setting:

```
DEFAULT → CHANGE → SAVE → FRONTEND UPDATES → REMOVE/RESET → DEFAULT RESTORES
```

Unsupported visual settings MUST NOT overwrite the client design.

---

## Phase 10 — Demo Contract

Define:

```
demo products
demo categories
demo images
demo hero
demo logo
demo heading
```

Rules:

```
0 real products → demo products shown
1+ real products → ALL demo products hidden

0 real categories → demo categories shown
1+ real categories → ALL demo categories hidden

custom logo → demo logo hidden
custom hero → demo hero hidden
custom heading → demo heading hidden

removing custom data → corresponding demo returns
```

Demo data is never business data. Demo products are never purchasable.

---

## Phase 11 — Security

Trace:

```
authentication flow
nonces on every AJAX action
AJAX endpoints and their permission checks
form submissions and CSRF protection
authorization (who can do what)
input validation (server-side)
output escaping (prevent XSS)
```

Never expose:

```
secrets
passwords
private customer data
API keys
```

---

## Phase 12 — Asset Audit

Every asset:

```
source (where it came from)
type (image, font, JS, CSS, video)
license (free, commercial, unknown)
local/remote (served from pack or CDN)
required? (does page break without it?)
fallback? (what replaces it if missing?)
```

Resolve:

```
src attributes
srcset / responsive images
data-src / lazy-loaded images
background-image in CSS
JS-generated image URLs
```

---

## Phase 13 — Font / License Audit

Record:

```
font family
weight
source (Google Fonts, local, CDN)
license (OFL, Apache, commercial, unknown)
redistribution permission
```

Commercial or unverified fonts: **flag before release.**

---

## Phase 14 — Third-Party / Business API Audit

Find every external dependency:

```
Shopify CDN
Stripe
Clerk (auth)
Analytics (GA, GTM, Segment)
Tracking pixels
External search APIs
External customer APIs
```

Classify each as:

```
PRESENTATION — visual asset, no business logic (permitted)
INTEGRATION — requires bridge mapping (document)
BUSINESS — replaces WooCommerce functionality (replace with platform contract)
UNNECESSARY — remove entirely
```

Replace business dependencies with AUREON/WooCommerce contracts.

---

## Phase 15 — Build Client Template-Ready Package

Required files:

```
manifest.json
TEMPLATE-CONTRACT.md
FEATURE-CAPABILITY-MATRIX.md
ASSET-MANIFEST.json
JS-COMPATIBILITY-MAP.md
route map
page-family map
demo contract
```

Output: **CLIENT-TEMPLATE-READY**

---

## Phase 16 — Pre-Replacement Gate

Before activation, verify:

```
every required platform feature has:
    presentation target
    OR explicit supported behavior
    OR documented unsupported status
```

Missing target for required capability: **STOP.**

---

## Phase 17 — Archive Current Client

Create immutable archive:

```
old client pack files
manifest
hashes
active settings at time of archive
```

**Never delete first. Always archive.**

---

## Phase 18 — Install New Client Pack

Install into:

```
aureon/frontend/designs/[new-client]/
```

Do not modify source snapshot.

---

## Phase 19 — Activate Client

Change only:

```
active design selection (manifest/config)
```

**No client hardcoding inside generic Core.**

---

## Phase 20 — Active-Pack Isolation

Verify NEW client loads:

```
new CSS loaded (correct path, correct file)
new JS loaded (correct path, correct file)
new fonts loaded
new images loaded
```

Verify OLD client is GONE:

```
old CSS = 0 requests
old JS = 0 requests
old DOM elements = 0
old fonts = 0 requests
old client URLs = 0 requests
```

---

## Phase 21 — Page Routing

Test every route:

```
homepage
shop
category
collection
simple product (use #834)
variable product (use #828)
search
account
cart
checkout
blog
article
static pages
404
```

**No route may render an unrelated page.**

---

## Phase 22 — Real Data Integration

Use permanent Golden references:

- **#834** — simple product
- **#828** — variable product

Verify for each:

```
title
price (regular + sale)
SKU
image (featured)
gallery (additional images)
stock status
variations (for variable products)
add-to-cart works
```

---

## Phase 23 — Demo Data Integration

Verify:

```
demo products render
demo categories render
fallback assets load
demo routes resolve
demo records are non-purchasable
```

---

## Phase 24 — Customizer Round-Trip

For every supported client setting:

```
DEFAULT
    ↓
CHANGE
    ↓
SAVE
    ↓
FRONTEND UPDATES
    ↓
REMOVE/RESET
    ↓
DEFAULT RESTORES
```

Test settings:

```
logo
hero
heading
announcement
footer
social links
colors
fonts
```

---

## Phase 25 — Menus

Test:

```
primary menu
secondary menu
mega menu (if supported)
mobile menu
footer menu
```

Verify:

```
all links resolve correctly
hierarchy is correct
active state shows
no stale client-specific links
```

---

## Phase 26 — Search

Test:

```
open search
type query
submit search
results display
product result renders
empty state displays
close search
Escape key closes
mobile search works
```

---

## Phase 27 — Account

Test:

```
logged-out state
login with valid credentials
login with invalid credentials (error message)
logged-in dashboard
logout
```

---

## Phase 28 — Cart

Test:

```
add simple product
add variable product (with selection)
update quantity
remove item
clear cart
cart count updates
mini-cart (if supported)
```

---

## Phase 29 — Checkout

Test:

```
checkout page renders
empty cart redirects
product in cart → checkout flow
payment form surface visible
```

---

## Phase 30 — Responsive

Test at:

```
1440px (desktop)
1024px (laptop)
768px (tablet)
390px (mobile)
```

**Test interactions at each breakpoint, not just screenshots.**

---

## Phase 31 — Image / Asset Integrity

For every route, verify:

```
featured image loads
srcset renders correctly
lazy images load on scroll
gallery images load
background images load
responsive images adapt
```

** REQUIRE:**
- 0 required broken images
- 0 avoidable srcset 404s

---

## Phase 32 — Network

**REQUIRE:**

```
0 prohibited Shopify business calls
0 Clerk authentication calls
0 inactive client requests (old pack still loading)
0 unexpected external APIs
```

Document any explicitly permitted presentation CDN URLs.

---

## Phase 33 — Console / PHP

**REQUIRE:**

```
0 fatal errors
0 unexpected JS errors
0 routing errors
```

PHP notices/warnings: classify and document.

---

## Phase 34 — Cache / State

Change:

```
product data
category data
logo
hero
heading
```

Reload / hard reload.

**Verify: no stale state persists.**

---

## Phase 35 — Demo ↔ Real Transition

```
DEMO STATE
    ↓
create one real product
    ↓
verify: all demo products hidden
    ↓
delete that real product
    ↓
verify: demo products return

DEMO CATEGORY STATE
    ↓
create one real category
    ↓
verify: all demo categories hidden
    ↓
delete that real category
    ↓
verify: demo categories return
```

---

## Phase 36 — Admin Workflow

Verify normal client operations:

```
add product
edit product
delete product
add category
delete category
upload logo
remove logo
upload hero
remove hero
change heading
reset heading
```

**Frontend state must follow automatically.**

---

## Phase 37 — Feature Retention Matrix

Compare BEFORE replacement vs AFTER replacement:

```
WordPress
WooCommerce
Products
Variations
Categories
Menus
Search
Account
Cart
Checkout
Customizer
Demo
Security
Routing
Active-pack isolation
```

**Nothing may disappear just because the frontend was replaced.**

---

## Phase 38 — Rollback Test

Prove:

```
new client active
    ↓
restore old client
    ↓
old client works correctly
    ↓
new client can be reactivated
```

---

## Phase 39 — Clean Test Data

Remove only temporary QA data:

```
test products
test categories
test media uploads
test option changes
```

**Never remove:**

```
demo data
client pack
Golden Core
```

---

## Phase 40 — Final Clean Client Test

Return to baseline:

```
zero real products
zero real categories
no custom logo
no custom hero
no custom heading
```

Then verify: **complete beautiful demo renders.**

---

## Phase 41 — Final Report

Create:

```
docs/forensics/[CLIENT]-FRONTEND-ACCEPTANCE-REPORT.md
```

Include:

```
source details
template mode
page families
capability matrix summary
bridge coverage
demo verification
real data verification
Customizer round-trip
menu verification
search verification
account verification
cart verification
checkout verification
image integrity
network audit
console audit
responsive verification
performance notes
security review
isolation verification
rollback verification
known limitations
```

---

## Phase 42 — Machine-Readable Acceptance

Create:

```
[CLIENT]-FRONTEND-ACCEPTANCE-MATRIX.json
```

Every requirement gets:

```json
{
  "id": "C1",
  "capability": "Simple product display",
  "test": "product test",
  "expected": "title, price, SKU, image, add-to-cart render correctly",
  "actual": "all elements render correctly",
  "status": "PASS",
  "evidence": "screenshot + console log"
}
```

---

## Phase 43 — Frontend Edit: Contract Snapshot (Edit Workflow)

Before any edit to an active client:

```
snapshot current contract state
    ↓
record all DOM targets
record all data attributes
record all JS event bindings
record all bridge mappings
record all Customizer connections
```

---

## Phase 44 — Frontend Edit: Edit + Contract Diff

Perform the edit.

Then diff:

```
DOM structure before vs after
data attributes before vs after
JS globals before vs after
bridge mappings before vs after
Customizer settings before vs after
```

Any unintended change: **STOP, investigate, fix.**

---

## Phase 45 — Frontend Edit: Targeted + Regression Test

For MINOR edits:

```
targeted test of changed area
visual verification
console check
```

For MAJOR edits:

```
full regression (Phases 21–36)
```

---

## Final Acceptance Gate

Return: **CLIENT_FRONTEND_ACCEPTED**

ONLY WHEN:

```
COMPLETE PRESENTATION                              ✅
ALL REQUIRED PLATFORM CAPABILITIES                 ✅
REAL WC DATA                                       ✅
DEMO FALLBACK                                      ✅
CUSTOMIZER                                         ✅
MENUS                                              ✅
SEARCH                                             ✅
ACCOUNT                                            ✅
CART                                               ✅
CHECKOUT                                           ✅
SECURITY                                           ✅
RESPONSIVE                                         ✅
NETWORK (no prohibited external calls)             ✅
CONSOLE (no fatal/errors)                          ✅
ACTIVE-PACK ISOLATION                              ✅
ROLLBACK (old client can be restored)              ✅
GOLDEN CORE UNTOUCHED                              ✅
CLEAN CLIENT STATE                                 ✅
```

---

## Hard Stop Conditions

**STOP if:**

```
- Golden Core must be changed without generic defect proof
- WooCommerce core must be modified
- Required feature disappears after replacement
- Business logic moves into client presentation layer
- Demo data becomes real business data
- Old client cannot be restored
- New client leaks old client assets
- Routes become invalid
- Required image is broken
- Required platform capability has no presentation target
```

---

## Future Client Workflow Summary

```
GOLDEN COPY
    ↓
CREATE CLIENT WORKSPACE
    ↓
TEMPLATE CREATION (Phases 0–16)
    ↓
CLIENT-TEMPLATE-READY
    ↓
CAPABILITY MATRIX
    ↓
BRIDGE MAPPING
    ↓
COMPATIBILITY GATE
    ↓
REPLACE CURRENT PACK (Phases 17–20)
    ↓
TEST EVERYTHING (Phases 21–36)
    ↓
CLIENT ACCEPTED
```

Later:

```
MINOR CHANGE → EDIT (Phases 43–45)
MAJOR VISUAL CHANGE → EDIT / NEW PACK REVISION
COMPLETELY NEW FRONTEND → TEMPLATE CREATION → REPLACEMENT
```

---

## Document Links

| Document | Purpose |
|----------|---------|
| [CLIENT-FRONTEND-CAPABILITY-CONTRACT.md](CLIENT-FRONTEND-CAPABILITY-CONTRACT.md) | What every frontend must support |
| [GOLDEN-AUREON-FRONTEND-WORKFLOWS.md](GOLDEN-AUREON-FRONTEND-WORKFLOWS.md) | Master workflow index |
| [NEW-CLIENT-TEMPLATE-CREATION-PLAN.md](NEW-CLIENT-TEMPLATE-CREATION-PLAN.md) | Original template creation plan |
| [FRONTEND-REPLACEMENT-PLAN.md](FRONTEND-REPLACEMENT-PLAN.md) | Original replacement plan |
| [FRONTEND-EDIT-PLAN.md](FRONTEND-EDIT-PLAN.md) | Original edit plan |
| [DEMO-REFERENCE-CONTENT-SYSTEM.md](DEMO-REFERENCE-CONTENT-SYSTEM.md) | Demo content architecture |

---

## Version History

| Version | Date | Change |
|---------|------|--------|
| 1.0 | 2026-08-31 | Initial three-workflow system |
| 2.0 | 2026-09-01 | Productized: contract-first, test-first, capability-complete. 42-phase master prompt. Feature-capability matrix mandatory. Acceptance gate formalized. |
