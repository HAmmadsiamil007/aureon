# FRONTEND REPLACEMENT PLAN

**Purpose:** Replace the current client frontend with a NEW COMPLETE frontend while preserving ALL Golden AUREON platform capabilities.

**ASSUMPTION:** The new frontend has already passed the Template Creation workflow.

---

## Golden Rule

**GOLDEN AUREON IS PROTECTED.**

DO NOT alter the Golden Core merely to make the new frontend easier to use.

The new frontend must adapt to the existing AUREON platform contract.

---

## Inputs

```
GOLDEN CORE:           aureon/
CURRENT ACTIVE PACK:   aureon/frontend/designs/[CURRENT_SLUG]/
NEW TEMPLATE-READY:    aureon/frontend/designs/[NEW_SLUG]/
```

---

## Phase 0 — Release Checkpoint

Verify:
- current Golden release commit
- branch
- working tree
- active design

Create a rollback point.

Archive the CURRENT ACTIVE CLIENT PACK before replacement.

**NEVER delete it first.**

---

## Phase 1 — Pre-Replacement Audit

Read:
- Golden Core architecture
- current client contract
- new client TEMPLATE-CONTRACT.md
- new assets-manifest
- new JS compatibility map

Compare:
- routes
- page families
- dynamic slots
- business actions
- assets
- fonts
- client libraries

---

## Phase 2 — Compatibility Gate

The new client must prove:
- complete page structure
- asset completeness
- page-family coverage
- dynamic data slots
- business action slots
- navigation slots
- account slots
- cart slots
- search slots
- checkout handling

**BEFORE activation.**

If required platform functionality has no presentation target:

**STOP.**

Do not invent a new visual UI inside the replacement operation.

---

## Phase 3 — Archive Current Client

Create a verified archive:
- old pack path
- file count
- checksums where practical
- version
- active manifest

Preserve rollback ability.

---

## Phase 4 — Install New Pack

Copy/install the new template-ready pack into:

```
aureon/frontend/designs/[NEW_SLUG]/
```

Do not modify the original template-ready source.

---

## Phase 5 — Active Design Switch

Change only the design selection/configuration required to activate the new client.

Do not hardcode the new client into generic core logic.

---

## Phase 6 — Presentation Isolation

Verify:
- new client CSS loads
- new client JS loads
- new client assets load
- old client CSS absent
- old client JS absent
- old client DOM absent
- old client fonts absent

Only active client presentation assets may load.

---

## Phase 7 — Platform Feature Retention

Verify ALL existing Golden AUREON capabilities remain available:

### WordPress
- routing
- pages
- media
- editor
- menus

### Customizer
- site identity
- logo
- favicon
- hero where supported
- announcement
- footer
- social
- colors/fonts where supported
- preview
- reset
- fallback

### Menus
- primary
- secondary
- footer
- slideout/mobile
- mega menu where supported

### Search
- open
- search
- results
- empty state
- close
- Escape
- mobile

### WooCommerce
- simple product
- variable product
- variants
- pricing
- stock
- add to cart
- quantity
- remove
- clear
- cart count
- mini cart where supported
- checkout
- account

### Content
- blog
- article
- static pages
- 404

---

## Phase 8 — Real Data Proof

Use permanent regression references:
- simple product #834
- variable product #828

Verify:
- title
- price
- SKU
- images
- stock
- variations
- add to cart
- cart count

---

## Phase 9 — Data Bridge

Verify:
- page-scoped data is safe
- no raw WP objects
- no private customer data
- URLs are WordPress/WooCommerce
- currencies are runtime-configured
- no Shopify URLs

---

## Phase 10 — Business API Replacement

Confirm:
- no Shopify API
- no Shopify checkout
- no Shopify customer system
- no prohibited business APIs

Client presentation actions route through AUREON/WooCommerce.

---

## Phase 11 — Demo Content

Verify:
- demo products
- demo categories
- real content hides demo
- demo records remain stored

---

## Phase 12 — Route Regression

Verify:
- `/`
- `/shop/`
- category
- simple product
- variable product
- blog
- article
- about
- contact
- cart
- checkout
- account
- search
- 404

---

## Phase 13 — Responsive

Test: 1440, 1024, 768, 390

Verify no horizontal overflow and all required interactions work.

---

## Phase 14 — Network

**REQUIRE:**
- 0 Shopify
- 0 Clerk
- 0 prohibited external business API
- 0 inactive client assets
- 0 required 404

---

## Phase 15 — Console

**REQUIRE:**
- zero unexpected runtime errors

---

## Phase 16 — Performance

Verify:
- only active client CSS
- only active client JS
- only active client fonts
- no duplicate libraries
- no unnecessary preload

---

## Phase 17 — Client Switching

If previous client remains archived:
- verify old client can be restored

If a second safe client exists:
- New Client → Old Client → New Client
- Verify isolation on every switch

---

## Phase 18 — Rollback Test

Prove the old client can be restored from the archive/Git checkpoint.

Do not destroy rollback capability.

---

## Phase 19 — Final Acceptance

New client is accepted only if:

| Capability | Status |
|------------|--------|
| COMPLETE PRESENTATION | PASS |
| DATA BRIDGE | PASS |
| WOO | PASS |
| CUSTOMIZER | PASS |
| MENUS | PASS |
| SEARCH | PASS |
| ACCOUNT | PASS |
| CART | PASS |
| CHECKOUT | PASS |
| DEMO | PASS |
| RESPONSIVE | PASS |
| NETWORK | PASS |
| CONSOLE | PASS |
| ISOLATION | PASS |
| ROLLBACK | PASS |

---

## Hard Stop Conditions

STOP if:
- Golden Core must be rewritten substantially
- WooCommerce core must be modified
- client must be split into sections
- client DOM must be recreated
- required platform feature has no safe presentation contract
- old client cannot be rolled back

Return: **FRONTEND_REPLACEMENT_BLOCKED** with exact reason.

---

## Required Result

Return:

**FRONTEND_REPLACEMENT_PASS**

or:

**FRONTEND_REPLACEMENT_BLOCKED**

Include:
- old client archive
- new client
- active design
- files changed
- platform features verified
- screenshots
- network
- console
- rollback proof
- Git commit

---

## Pre-Flight Checklist

- [ ] New client passed Template Creation
- [ ] Release checkpoint created
- [ ] Current client archived
- [ ] Compatibility gate passed
- [ ] Rollback verified

---

## Execution Checklist

- [ ] New pack installed
- [ ] Design activated
- [ ] Presentation isolation verified
- [ ] Platform features retained
- [ ] Real data proof passed
- [ ] Data bridge verified
- [ ] Business APIs replaced
- [ ] Demo content verified
- [ ] Routes verified
- [ ] Responsive verified
- [ ] Network clean
- [ ] Console clean
- [ ] Performance clean
- [ ] Client switching works
- [ ] Rollback works

---

## Final Acceptance

- [ ] FRONTEND_REPLACEMENT_PASS confirmed
- [ ] All 15 capabilities verified
- [ ] Rollback preserved
- [ ] Git commit created
