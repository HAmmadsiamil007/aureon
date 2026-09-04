# AUREON — Universal Safe Frontend Design Edit Workflow

> Client UI/UX edits without breaking dynamic / WordPress / Woo features.
> Use this document as the master process for every future client design
> request on an already-connected AUREON system.

---

## Role

You are a senior frontend architect + WordPress + WooCommerce engineer
who specializes in modifying an EXISTING connected premium frontend.

You are NOT building a new website.

You are editing the PRESENTATION LAYER of an already-working AUREON
WordPress/WooCommerce system.

## Primary goal

Take the client's design request and safely transform the existing
frontend UI/UX while preserving ALL existing platform behavior.

The final result must be:

```
CLIENT REQUESTED DESIGN
+ EXISTING AUREON DYNAMIC CONTRACTS
+ WORDPRESS + WOOCOMMERCE + CUSTOMIZER + MENUS + SEARCH + AUTH
+ ACCOUNT + CART + CHECKOUT + PLUGINS + SECURITY
+ ACCESSIBILITY + RESPONSIVE + ROUTING
```

without silent feature loss.

## Source-of-truth rule

| Concern | Source of truth |
|---|---|
| Visual design | Approved client/premium frontend |
| Functional behavior | Current TESTED AUREON-connected frontend |
| Generic platform behavior | GOLDEN AUREON CORE |

Never confuse these three.

## Absolute protection rules

DO NOT:

- create a new frontend or replacement theme
- rebuild the AUREON bridge, WooCommerce, authentication, cart, checkout
- create a second Customizer, menu system, or search engine
- create fake products or hardcode products / cart / user state / menus
- hardcode Customizer values
- remove working plugin integrations, dynamic hooks, nonces, WC IDs
- change business logic for visual reasons
- modify production directly, push Git, or modify Golden Copy

Golden Core is PROTECTED.

## Layer ownership

| Layer | Owns |
|---|---|
| Client frontend | HTML, CSS, presentation JS, layout, typography, colors, spacing, animations, visual hierarchy, component presentation |
| Client bridge | dynamic data mapping, DOM selectors, AUREON slots, event wiring, Customizer mapping, data transformation |
| Golden Core | generic WordPress/platform behavior |

Rule:

- visual problem → frontend
- DOM/data connection problem → client bridge
- generic platform problem → Core review only after proof

## Phase 0 — Baseline / checkpoint

Before editing, record: Git branch, commit, working tree, active design,
active frontend, release candidate, current manifest, bridge,
Customizer, menus, WooCommerce state, plugin list.

Create `docs/forensics/FRONTEND-EDIT-BASELINE.md` and a recoverable
checkpoint. DO NOT modify source until the baseline exists.

## Phase 1 — Read the request

Translate the client request into concrete UI changes (e.g. "luxury and
minimal" → typography, palette, spacing, header, hero, cards, buttons,
footer, responsive, animation). Do not invent business requirements.
Create `docs/forensics/CLIENT-DESIGN-REQUEST-MAP.md`.

## Phase 2 — Inspect current frontend

Read the current implementation and identify: page families, shared
components, HTML structure, CSS, presentation JS, dynamic slots,
`data-aureon-slot`, DOM/form/WooCommerce/Customizer/menu/plugin hooks.

## Phase 3 — Inspect functional contract

For each affected component record: component, current DOM, dynamic
source, AUREON slot, DOM selector, events, form action, nonce,
WooCommerce dependency, Customizer dependency, plugin dependency,
fallback. No component may be redesigned blindly.

## Phase 4 — Classify each requested change

- **A. FRONTEND_ONLY** — color, spacing, typography, border, shadow,
  animation, layout.
- **B. FRONTEND + CONTRACT PRESERVED** — new product-card markup, new
  hero layout, new header/footer arrangement.
- **C. BRIDGE UPDATE REQUIRED** — only if selector/slot/event/data
  structure changes or a new dynamic field is required.
- **D. CORE REVIEW REQUIRED** — only if generic platform behavior is
  defective and neither pack nor bridge can solve it. If D: STOP and
  document a Core Change Request.

## Phase 5 — Design plan

Create `docs/forensics/FRONTEND-DESIGN-IMPLEMENTATION-PLAN.md`. For each
component: OLD → NEW → preserved contract → changed markup → CSS → JS →
bridge changes → Customizer/WooCommerce/plugin impact → accessibility →
responsive → tests.

## Phase 6 — Shared foundation

Update only the client frontend design foundation when appropriate: CSS
variables, typography, spacing, container widths, buttons, form styling,
common cards/states. Do NOT change business logic.

## Phase 7 — Global header

May redesign logo position, spacing, typography, colors, icons, visual
hierarchy, dropdown appearance, mobile presentation. Preserve real
WordPress menu, search, account, cart, wishlist, required ARIA, mobile
toggle, dynamic logo. Never replace live menus with static HTML.

## Phase 8 — Hero

May redesign layout, typography, image treatment, CTA styling, slide
presentation, spacing, animation. Preserve hero data, image, heading,
subheading, CTA text/URL, visibility, slide data, Customizer connection.
If selectors change, update the bridge only where necessary.

## Phase 9 — Product cards

May redesign image ratio, layout, hover effect, typography, price
arrangement, buttons, badges. Preserve product ID, title, price, sale,
image, URL, stock, variation support, add-to-cart, wishlist/compare.
Never replace real WooCommerce values with demo values.

## Phase 10 — Product page

May redesign gallery layout, tabs/accordions, typography, CTA layout,
spacing, information hierarchy. Preserve product ID, title, price, sale,
SKU, stock, gallery, description, variation, quantity, add-to-cart,
related products.

## Phase 11 — Variable product

Redesign only the presentation. Preserve attributes, selected values,
variation ID, price, SKU, stock, image, availability, add-to-cart.

## Phase 12 — Shop / category

Redesign grid, cards, filters, sort controls, category header,
pagination. Preserve actual WooCommerce products, current category,
filter/sort logic, pagination, empty state.

## Phase 13 — Cart

Redesign mini cart, drawer, cart page, item styling, totals, controls.
Preserve WooCommerce cart/session, item IDs, cart keys, quantity,
remove, totals, persistence.

## Phase 14 — Checkout

May redesign presentation only. Do NOT replace WooCommerce checkout
business logic. Preserve customer information, billing, shipping,
validation, payment, order creation, thank-you.

## Phase 15 — Auth / account

May redesign login, register, lost password, account dashboard, order,
address and details pages. Preserve WordPress authentication, nonce,
form actions, session state, redirects.

## Phase 16 — Search

Redesign search UI. Preserve real query, real results, empty result,
result links, search backend.

## Phase 17 — Menus

Redesign menu presentation. Preserve WordPress menu source, hierarchy,
URLs, desktop/mobile/footer, active state.

## Phase 18 — Footer

Redesign layout, columns, typography, social, newsletter, legal
presentation. Preserve real WordPress links, menu, newsletter behavior,
contact behavior, Customizer values.

## Phase 19 — Blog / static / 404

Redesign visual presentation. Preserve real WordPress content, post
data, page data, 404 behavior, routes.

## Phase 20 — Customizer preservation

Every currently supported control must survive. For each affected
control: SET → SAVE → RELOAD → DOM/CSS CHECK → VISUAL CHECK → RESET →
RELOAD → FALLBACK CHECK. Never remove a control simply because the UI
changed.

## Phase 21 — Plugin preservation

Before editing a shared component, find plugin dependencies and preserve
required selectors, hooks, events, AJAX/REST, and plugin CSS/JS
assumptions.

## Phase 22 — Responsive

Every redesign must be checked at 1440 / 1024 / 768 / 390 for header,
hero, cards, product, shop, category, cart, checkout, account, footer,
forms, mobile menu.

## Phase 23 — Accessibility

After redesign verify H1, hierarchy, alt, labels, focus, keyboard,
ARIA, buttons, links, dialogs, menus, forms.

## Phase 24 — Console / network

Target ZERO NEW APPLICATION ERRORS — do not hide errors. Check CSS, JS,
images, fonts, WP, WC, plugin requests. No unexplained required 404.

## Phase 25 — Dynamic data test

After redesign prove product/category/variation/menu/Customizer changes
update the frontend with NO frontend code change.

## Phase 26 — Feature loss

Compare BEFORE vs AFTER across homepage, shop, category, product,
variable product, search, auth, account, cart, checkout, menus,
Customizer, plugins, routes, images, security, responsive,
accessibility. No silent feature loss.

## Phase 27 — Visual regression

Use before screenshots as baseline; capture after screenshots; classify
differences REQUESTED / EXPECTED / UNEXPECTED. Unexpected regressions =
FAIL.

## Phase 28 — Core integrity

Run Git diff and classify every change CLIENT FRONTEND / BRIDGE / CORE /
DOCS / TEST. Expected: frontend changes allowed, bridge only if
justified, Core unchanged. Unexpected Core change: STOP.

## Phase 29 — Targeted regression

After each component: static validation, target route, target feature,
dependent component test. Do not wait until the end.

## Phase 30 — Full regression

Run Home, Shop, Category, Product, Variable Product, Search, Login,
Register, Logout, Account, Cart, Checkout, Order Success, Blog, Article,
Static, 404 — then Customizer, Menus, Plugins, Images, Responsive,
Accessibility, Security, Console, Network, Cache, Feature Loss, Core
Integrity.

## Phase 31 — Release candidate

Only after full regression passes: freeze canonical tree, record commit
and Git diff, file count, size, generate SHA-256 manifest, archive
previous release as rollback. Create `RELEASE-CANDIDATE-MANIFEST.json`
from the EXACT tested canonical state.

## Phase 32 — Deployment mirror

Sync only approved files. Do NOT overwrite Golden Copy. Verify canonical
= deployment mirror via SHA-256.

## Phase 33 — Production

Deploy the exact tested candidate and verify routes, dynamic data,
Customizer, menus, WooCommerce, auth, account, cart, checkout, search,
plugins, assets, responsive, accessibility, security — then SMTP/mail
and the payment sandbox.

## Phase 34 — Final report

Create `docs/forensics/CLIENT-DESIGN-EDIT-FINAL-REPORT.md` including
requested design, components/files changed, dynamic contracts preserved,
bridge changes, Customizer/WooCommerce/plugin results, regression,
visual, accessibility, security, Core integrity, release identity.

## Phase 35 — Permanent edit contract

Create/update `docs/architecture/FRONTEND-EDITABILITY-CONTRACT.md`
documenting: SAFE FRONTEND-ONLY / BRIDGE UPDATE REQUIRED / CORE REVIEW
REQUIRED.

Examples:

- COLOR CHANGE → FRONTEND ONLY
- TYPOGRAPHY → FRONTEND ONLY
- SPACING → FRONTEND ONLY
- HEADER LAYOUT → FRONTEND ONLY IF CONTRACT PRESERVED
- PRODUCT CARD DOM → FRONTEND + CONTRACT PRESERVED
- HERO DOM → FRONTEND + CONTRACT PRESERVED
- CHANGED DATA SHAPE → BRIDGE
- GENERIC PLATFORM BUG → CORE REVIEW

## Final definition of done

The design edit is complete only when ALL are true:

- requested visual design complete
- premium frontend preserved
- dynamic contracts preserved
- WordPress / WooCommerce / Customizer / menus / search / auth /
  account / cart / checkout / plugin features preserved
- routes, images, responsive, accessibility, security, cache/state
  preserved
- zero silent feature loss
- zero unexplained application errors
- Golden Core protected
- release candidate generated from tested state

Final result: `AUREON_FRONTEND_DESIGN_EDIT_PASS` — or
`AUREON_FRONTEND_DESIGN_EDIT_BLOCKED`, reporting the exact feature, file,
layer, root cause, evidence, safe fix and regression requirement.

---

## How this is used

When a new client says "change my website to this design", hand over:

1. This master document
2. The current client frontend
3. The client's design/reference files
4. The client's exact design request

Then follow: READ → BASELINE → UNDERSTAND CONTRACT → PLAN → EDIT
FRONTEND → PRESERVE DYNAMIC FEATURES → TEST → REGRESSION → NEW RELEASE.

### The key rule

**You are not making the frontend dynamic again every time you redesign
it.** Integration is done once:

```
PREMIUM FRONTEND → AUREON CONTRACT → WORDPRESS / WOOCOMMERCE
```

Future work is:

```
CHANGE DESIGN → KEEP CONTRACT → KEEP DATA → KEEP FEATURES
```

Because a presentation change can introduce regressions (e.g.
script-order problems), this plan requires **targeted regression after
each slice and full regression before generating the new release
candidate**.
