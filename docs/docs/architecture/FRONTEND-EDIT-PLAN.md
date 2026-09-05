# FRONTEND EDIT PLAN

**Purpose:** Safely modify an already-installed client frontend while keeping the Golden Core and ALL platform capabilities intact.

---

## Core Rule

**EDIT THE CLIENT PACK, NOT THE GOLDEN CORE.**

The active client frontend owns:
- HTML
- CSS
- JavaScript
- animation
- responsive presentation
- visual design

AUREON owns:
- data
- business logic
- routing
- commerce
- account
- menus
- search
- security
- Customizer infrastructure

---

## Phase 0 — Classify the Request

| Level | Description | Example |
|-------|-------------|---------|
| **LEVEL 1 — MINOR** | Text, image, logo, spacing, color, one component visual adjustment | "Change the hero image" |
| **LEVEL 2 — MAJOR CLIENT-PACK EDIT** | Homepage redesign, header redesign, product layout redesign, navigation redesign, animation redesign, new page family | "Redesign the entire homepage" |
| **LEVEL 3 — FULL FRONTEND REPLACEMENT** | Completely different frontend, different HTML/CSS/JS system | "I have a new website" |

**LEVEL 3 MUST use FRONTEND-REPLACEMENT-PLAN.md instead.**

---

## Phase 1 — Freeze

Record:
- current commit
- branch
- active client
- working tree

Create edit rollback checkpoint.

---

## Phase 2 — Read Client Contract

Read:
- TEMPLATE-CONTRACT.md
- assets-manifest.json
- JS-COMPATIBILITY-MAP.md
- current manifest

Determine:
- what the requested area owns
- what AUREON owns
- what bridge owns

---

## Phase 3 — Determine Safe Edit Zone

**MINOR:** Modify only the client pack.

**MAJOR:** Continue only after tracing affected dependencies.

Never modify Golden Core for convenience.

---

## Phase 4 — Preserve the Platform Contract

Regardless of visual changes, preserve:
- routes
- data hooks
- product data
- variation data
- cart actions
- account actions
- search actions
- menu data
- Customizer data
- security
- form behavior

Existing platform capabilities must survive the edit.

---

## Phase 5 — HTML Edit Rules

For complete-page clients, HTML may be edited.

However, preserve:
- dynamic hooks
- required IDs
- data attributes
- form contracts
- accessibility semantics
- page-family routing

Before removing any:
- `data-component`
- `data-*` hook
- ID
- form field
- action target

**Trace whether AUREON/bridge/JS depends on it first.**

---

## Phase 6 — CSS Edit Rules

Client CSS may be changed.

Do not accidentally:
- introduce global AUREON overrides
- depend on inactive client styles
- break responsive layout
- break Woo data states

Prefer client-scoped CSS.

---

## Phase 7 — JS Edit Rules

Client presentation JS may be edited.

Before modifying, determine whether the code is:
- presentation
- business
- bridge

Preserve bridge/business contracts.

Never replace a working commerce bridge with direct Shopify logic.

---

## Phase 8 — Customizer Compatibility

If the edited area is customizable, preserve:
- FermPageData/customizer or client equivalent
- setting names
- defaults
- fallback behavior
- preview
- reset

If adding a new configurable setting, add it to the generic client contract where appropriate.

Do not hardcode one-off settings into unrelated core logic.

---

## Phase 9 — Demo/Fallback Compatibility

Preserve:
- demo products
- demo categories
- default logo
- default hero
- fallback content

Custom value → show custom
Custom removed → default returns

---

## Phase 10 — WooCommerce Compatibility

After any product/cart/account/checkout edit, verify:
- Product #834
- Product #828
- add to cart
- variation selection
- cart
- checkout
- account

---

## Phase 11 — Menus/Search

If header/navigation/search changes, verify:
- desktop
- mobile
- mega menu
- slideout
- search
- WordPress URLs

---

## Phase 12 — Active-Pack Isolation

Verify the edit does not introduce:
- inactive client CSS
- inactive client JS
- inactive client assets
- global platform contamination

---

## Phase 13 — Visual Regression

Test affected pages and dependent pages.

Viewports: 1440, 1024, 768, 390

Compare before/after.

---

## Phase 14 — Functional Regression

Test affected functionality.

At minimum preserve:
- homepage
- product #834
- variable product #828
- cart
- account
- search

---

## Phase 15 — Network

**REQUIRE:**
- no Shopify
- no prohibited third-party business APIs
- no unexpected external calls
- no required 404

---

## Phase 16 — Console

**REQUIRE:**
- zero unexpected errors

---

## Phase 17 — Security

Verify:
- nonces
- validation
- sanitization
- authorization where applicable

---

## Phase 18 — Major Edit Safety

For a major client redesign:
- create a separate client-pack revision/branch
- do not experiment directly on the Golden release
- preserve rollback

---

## Phase 19 — Commit

Commit only the client edit and required bridge changes.

Do not mix unrelated core changes.

---

## Hard Stop

STOP if requested edit requires:
- changing Golden Core unnecessarily
- modifying WooCommerce core
- removing required data hooks
- breaking the bridge contract
- replacing the complete page with component reconstruction

Return: **CLIENT_EDIT_BLOCKED** with exact reason and safer alternative.

---

## Final Result

Return:

**CLIENT_EDIT_PASS**

or:

**CLIENT_EDIT_BLOCKED**

Include:
- request classification
- files changed
- platform features protected
- screenshots
- console
- network
- regression
- commit

---

## Pre-Flight Checklist

- [ ] Edit checkpoint created
- [ ] Client contract read
- [ ] Change classified (Level 1/2/3)
- [ ] Safe edit zone determined
- [ ] Dependencies traced

---

## Execution Checklist

- [ ] Platform contracts preserved
- [ ] HTML edits safe
- [ ] CSS edits safe
- [ ] JS/bridge edits safe
- [ ] Customizer compatible
- [ ] Demo/fallback compatible
- [ ] WooCommerce verified
- [ ] Menus/search verified
- [ ] Isolation verified
- [ ] Visual regression passed
- [ ] Functional regression passed
- [ ] Network clean
- [ ] Console clean
- [ ] Security verified

---

## Final Acceptance

- [ ] CLIENT_EDIT_PASS confirmed
- [ ] All platform features protected
- [ ] Commit created
- [ ] No Golden Core modifications
