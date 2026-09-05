# NEW CLIENT — TEMPLATE CREATION PLAN

**Purpose:** Prepare a completely independent premium frontend so it can later be connected to Golden AUREON with minimal integration work.

**THIS PHASE DOES NOT REPLACE THE ACTIVE CLIENT.**

**THIS PHASE DOES NOT MODIFY GOLDEN AUREON.**

---

## Core Architecture

```
GOLDEN AUREON = PLATFORM
CLIENT FRONTEND = PRESENTATION
BRIDGE = CONNECTOR
```

If the client provides a complete HTML/CSS/JS frontend:

**USE COMPLETE-PAGE MODE.**

Preserve:
- HTML
- CSS
- JavaScript
- animations
- responsive behavior
- required frontend libraries
- required local assets

Do NOT split the frontend into AUREON sections/components.

---

## Inputs

```
GOLDEN CORE:           aureon/
ORIGINAL CLIENT:       [CLIENT_SOURCE_PATH]
TEMPLATE-READY OUTPUT: aureon/frontend/designs/[NEW_SLUG]/
```

---

## Phase 0 — Freeze Everything

Before touching anything:
- Record git commit
- Record branch
- Record git status
- Create immutable source copy
- Calculate asset/source hashes

**NEVER modify the original client source.**

---

## Phase 1 — Understand Golden AUREON

Read enough of the Golden Core to understand:
- routing
- complete-page host
- design resolver
- manifest contract
- asset loading
- Customizer
- WooCommerce
- menus
- search
- account
- cart
- checkout
- security
- client isolation

Do not modify core during analysis.

---

## Phase 2 — Determine Frontend Mode

**Does the client provide a complete HTML/CSS/JS frontend?**

YES → COMPLETE-PAGE MODE
NO → COMPONENT MODE

Never convert complete-page mode into component mode for convenience.

---

## Phase 3 — Forensic Client Audit

Analyze the COMPLETE source before cleanup.

**Page Families:**
- homepage
- shop/archive
- category/collection
- product
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

**Analyze:**
- HTML DOM
- CSS
- responsive CSS
- JavaScript
- vendor libraries
- fonts
- images
- SVG
- video
- forms
- data attributes
- IDs
- JS hooks
- head requirements
- body/html attributes

---

## Phase 4 — Classify All Client Dependencies

Every significant dependency must be classified:

| Classification | Action |
|----------------|--------|
| PRESENTATION | Preserve |
| BUSINESS/API | Replace through bridge |
| DATA | Replace through bridge |
| TRACKING | Remove after tracing |
| THIRD_PARTY | Audit and classify |
| DEAD | Remove after tracing |

---

## Phase 5 — Third-Party Audit

Trace and classify:
- Shopify
- Shopify checkout
- Shopify customer API
- Clerk
- analytics
- tracking
- A/B testing
- external CDNs

Do not delete something merely because it is named after a platform.

Prove whether it is presentation or business code first.

---

## Phase 6 — Font Audit

For every font:
- filename
- family
- weight
- style
- source
- license
- redistribution status

If commercial redistribution rights are not confirmed:

**DO NOT copy the font.**

Choose an approved alternative and document:
- original
- replacement
- reason
- license

---

## Phase 7 — Asset Normalization

Create a complete local asset inventory:

- CSS
- JS
- fonts
- images
- SVG
- videos
- PDF
- JSON/config

Resolve all required local references.

No required production asset may depend on an unavailable remote source.

---

## Phase 8 — Page Family Extraction

Do NOT copy hundreds of duplicate pages when they represent one template.

Identify:
- presentation template
- + data variations

Use one representative page per genuine page family.

---

## Phase 9 — Template Contract

Create `TEMPLATE-CONTRACT.md` documenting:

- routes
- page families
- dynamic slots
- DOM hooks
- forms
- product data
- collection data
- navigation
- cart
- customer
- search
- checkout
- assets
- fonts
- JS entrypoints
- CSS entrypoints
- head requirements
- body attributes

---

## Phase 10 — Data Contract

Define exactly what AUREON must provide:

**Product:**
- id, variant_id, title, slug, url, SKU
- price, formatted price, currency
- availability, gallery, attributes
- variants, description, badge

**Collection:**
- id, name, description, products, count

**Navigation:**
- menu items, URLs, active state, account, cart, search

**Cart:**
- items, item keys, quantities, totals, count

**Customer:**
- logged-in state, safe display name

Do not expose raw WordPress/Woo objects.

---

## Phase 11 — Business Action Contract

Document:
- add to cart
- update cart
- remove cart item
- clear cart
- variation selection
- search
- login
- logout
- checkout
- forms

The client frontend must never directly depend on Shopify business APIs.

---

## Phase 12 — Build Template-Ready Copy

Create `[CLIENT]-template-ready/` with:

- manifest.json
- TEMPLATE-CONTRACT.md
- assets-manifest.json
- JS-COMPATIBILITY-MAP.md
- complete page templates
- assets
- approved fonts
- bridge entrypoints/placeholders

---

## Phase 13 — Complete-Page Manifest

Define:
```json
{
  "complete_page": true,
  "pages": {...},
  "assets": {
    "css": [...],
    "js": [...]
  }
}
```

---

## Phase 14 — Bridge Design

Design the THIN bridge:

**PHP:**
- resolve page
- inject page-scoped data/config
- provide safe URLs
- provide nonce/config

**JS:**
- translate client UI actions
- consume page data
- connect business actions

**DO NOT:**
- rebuild DOM
- recreate components
- redesign UI

---

## Phase 15 — Standalone Proof

Before WordPress integration, verify the cleaned template works standalone:

- assets
- JS
- interactions
- responsive behavior
- console

Viewports: 1440, 1024, 768, 390

---

## Phase 16 — Quality Gate

**REQUIRE:**
- 0 required asset 404
- 0 unexpected JS errors
- 0 unresolved required dependencies
- 0 prohibited business APIs
- 0 broken page families

---

## Phase 17 — Do Not Connect Yet

Stop after template preparation.

Do not:
- activate client
- replace Ferm
- modify Golden Core
- modify WooCommerce

---

## Required Output

Return:

**CLIENT_TEMPLATE_READY**

or:

**CLIENT_TEMPLATE_BLOCKED**

Include:
- source path
- template-ready path
- page families
- assets
- fonts
- third-party removals
- data contract
- action contract
- manifest
- JS compatibility
- remaining risks

---

## Pre-Flight Checklist

- [ ] Source frozen
- [ ] Golden Core understood
- [ ] Mode determined (complete-page vs component)
- [ ] All dependencies classified
- [ ] Third-party audited
- [ ] Fonts licensed
- [ ] Assets normalized
- [ ] Page families extracted
- [ ] Template contract created
- [ ] Data contract created
- [ ] Action contract created

---

## Execution Checklist

- [ ] Template-ready copy created
- [ ] Manifest created
- [ ] Bridge designed
- [ ] Standalone proof passed
- [ ] Quality gate passed

---

## Final Acceptance

- [ ] CLIENT_TEMPLATE_READY confirmed
- [ ] No Golden Core modifications
- [ ] No active client replacement
- [ ] All documentation complete
