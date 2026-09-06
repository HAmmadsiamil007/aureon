# GOLDEN AUREON

# UNIVERSAL FRONTEND EDIT + SAFE INTEGRATION + REGRESSION PLAN

**Document:** Universal Frontend Edit Workflow
**Purpose:** Safely modify any premium client frontend while preserving Golden AUREON, WordPress, WooCommerce, Customizer, menus, search, authentication, cart, checkout, plugins, security, routing, and all existing platform capabilities.
**Version:** 1.1.0
**Created:** 2026-09-02
**Upgraded:** 2026-09-02 (v1.1 — scope lock, blast-radius, rollback verification, visual regression, schema validation, release audit, core allowlist, plugin levels, API contracts, 4-level testing)
**Proven on:** Vineta (Ferm Living frozen frontend), 15 gates PASS, GOLDEN_CORE PASS

---

# TABLE OF CONTENTS

```
1.   PURPOSE
2.   CORE ARCHITECTURE
3.   GOLDEN RULE
4.   ABSOLUTE PROTECTION RULES
5.   SCOPE LOCK                              <- NEW v1.1
6.   INPUTS
7.   PHASE 0 - BASELINE + ROLLBACK VERIFY   <- UPGRADED v1.1
8.   PHASE 1 - READ THE TEMPLATE
9.   PHASE 2 - READ AUREON CONTRACTS
10.  PHASE 3 - UNDERSTAND THE CLIENT REQUEST
11.  PHASE 4 - IMPACT ANALYSIS + BLAST RADIUS <- UPGRADED v1.1
12.  PHASE 5 - DETERMINE THE SMALLEST SAFE CHANGE
13.  PHASE 6 - PRESERVE UI/UX
14.  PHASE 7 - FRONTEND IMPLEMENTATION
15.  PHASE 8 - DYNAMIC SLOT + DATA SCHEMA    <- UPGRADED v1.1
16.  PHASE 9 - WORDPRESS COMPATIBILITY
17.  PHASE 10 - WOOCOMMERCE COMPATIBILITY
18.  PHASE 11 - CART REGRESSION
19.  PHASE 12 - CHECKOUT REGRESSION
20.  PHASE 13 - AUTHENTICATION REGRESSION
21.  PHASE 14 - ACCOUNT REGRESSION
22.  PHASE 15 - SEARCH REGRESSION
23.  PHASE 16 - MENU REGRESSION
24.  PHASE 17 - CUSTOMIZER REGRESSION
25.  PHASE 18 - PLUGIN COMPATIBILITY          <- UPGRADED v1.1
26.  PHASE 19 - IMAGE / MEDIA REGRESSION
27.  PHASE 20 - RESPONSIVE REGRESSION
28.  PHASE 21 - ACCESSIBILITY REGRESSION
29.  PHASE 22 - JAVASCRIPT REGRESSION
30.  PHASE 23 - NETWORK REGRESSION
31.  PHASE 24 - ROUTE REGRESSION
32.  PHASE 25 - CACHE REGRESSION
33.  PHASE 26 - DEMO COMPATIBILITY
34.  PHASE 27 - CLIENT ISOLATION
35.  PHASE 28 - SECURITY REGRESSION
36.  PHASE 29 - FEATURE LOSS AUDIT
37.  PHASE 30 - GOLDEN CORE INTEGRITY          <- UPGRADED v1.1
38.  PHASE 31 - FAILURE LOOP
39.  PHASE 32 - 4-LEVEL REGRESSION MODEL       <- UPGRADED v1.1
40.  PHASE 33 - VISUAL REGRESSION              <- NEW v1.1
41.  PHASE 34 - API CONTRACT COMPATIBILITY      <- NEW v1.1
42.  PHASE 35 - RELEASE PACKAGE AUDIT           <- NEW v1.1
43.  PHASE 36 - EDIT ACCEPTANCE MATRIX
44.  PHASE 37 - EDIT REPORT
45.  FINAL ACCEPTANCE
46.  BLOCKED ACCEPTANCE
47.  WHEN TO MODIFY GOLDEN CORE
48.  UNIVERSAL CLIENT EDIT WORKFLOW (MASTER)
49.  IMPORTANT PRINCIPLE
50.  FINAL OPERATING RULE
51.  USAGE
APPENDIX A - PROVEN ARCHITECTURE PATTERN
APPENDIX B - VERIFICATION COMMANDS
APPENDIX C - REQUIRED TOOL CALLS
APPENDIX D - BLAST RADIUS EXAMPLES           <- NEW v1.1
APPENDIX E - DATA SCHEMA CATALOG             <- NEW v1.1
APPENDIX F - CORE INTEGRITY ALLOWLIST        <- NEW v1.1
APPENDIX G - API CONTRACT TEMPLATES          <- NEW v1.1
```

---

# 1. PURPOSE

This document is the permanent workflow for editing an existing premium client frontend.

It applies to:

* HTML templates
* HTML/CSS/JS themes
* complete-page client packs
* premium ecommerce templates
* restaurant templates
* fashion templates
* furniture templates
* portfolio templates
* service templates
* any future client frontend

The workflow is intentionally design-independent.

The active client frontend remains the visual source of truth.

---

# 2. CORE ARCHITECTURE

The permanent separation is:

```
GOLDEN AUREON CORE
        |
        | protected
        v
CLIENT FRONTEND / CLIENT PACK
        |
        | editable
        v
BRIDGE / DATA CONNECTION
        |
        v
WORDPRESS + WOOCOMMERCE + PLUGINS
```

Responsibilities:

## Golden Core

Provides:

* WordPress integration
* WooCommerce integration
* routing
* security
* menus
* search
* authentication
* account
* cart
* checkout
* Customizer
* demo system
* active-pack isolation
* platform adapters
* platform data contracts

## Client Frontend

Provides:

* HTML
* CSS
* JavaScript
* visual design
* UX
* layouts
* animations
* responsive presentation
* component presentation
* page composition

## Bridge

Provides:

* data translation
* DOM hooks
* dynamic slots
* event integration
* WordPress/WooCommerce to frontend mapping

---

# 3. GOLDEN RULE

Always classify the problem before changing code:

```
VISUAL / PRESENTATION
-> CLIENT PACK

DATA / BUSINESS INTEGRATION
-> BRIDGE

GENERIC PLATFORM DEFECT
-> GOLDEN CORE ONLY AFTER PROOF
```

Never move a client-specific problem into Golden Core.

---

# 4. ABSOLUTE PROTECTION RULES

DO NOT:

* redesign the platform
* replace the client frontend unnecessarily
* create an AI-generated replacement
* create a second UI system
* rewrite Golden Core for convenience
* duplicate WooCommerce
* duplicate authentication
* duplicate cart
* duplicate checkout
* duplicate menus
* duplicate Customizer
* create a second demo system
* remove existing functionality without analysis
* remove plugins without compatibility analysis
* remove dynamic slots silently
* declare success without regression
* use a quick fix that breaks another page

The client frontend may change.

Golden Core remains protected.

---

# 5. SCOPE LOCK

## 5.1 Approved Scope Pipeline

```
CLIENT REQUEST
    |
APPROVED SCOPE (explicit, documented)
    |
ONLY IMPLEMENT APPROVED SCOPE
    |
REPORT anything discovered outside scope
```

## 5.2 Hard Rules

OpenCode MUST NOT:

* add unrequested features
* redesign unrelated components
* refactor unrelated code
* replace working systems for stylistic preference
* optimize unrelated areas without evidence
* introduce new libraries without approval
* change Golden Core without explicit request + proof
* modify plugin code without explicit request

## 5.3 Discovered Improvements

Any improvement discovered during editing that falls outside approved scope:

```
-> REPORT_ONLY
-> document in final report under "Discovered Improvements"
-> do NOT implement
-> do NOT auto-fix
-> flag for client review in next cycle
```

## 5.4 Scope Change Protocol

If the edit naturally requires more than originally approved:

```
1. STOP implementation
2. document required additional scope
3. present to user for approval
4. resume ONLY after explicit approval
5. record scope expansion in final report
```

---

# 6. INPUTS

Before editing, identify:

```
CLIENT
TEMPLATE
CLIENT REQUEST
CURRENT ACTIVE DESIGN
GOLDEN CORE VERSION
WORDPRESS VERSION
WOOCOMMERCE VERSION
ACTIVE PLUGINS
CURRENT MENUS
CURRENT CUSTOMIZER
CURRENT ROUTES
CURRENT TEST STATE
```

If any information is unavailable:

DISCOVER IT PROGRAMMATICALLY.

Do not repeatedly ask the user for information that can be obtained from the project.

---

# 7. PHASE 0 - BASELINE + ROLLBACK VERIFICATION

Before modifying anything, create a verified rollback point.

## 7.1 Record Baseline

Record:

* Git branch
* Git commit
* working tree state
* client pack path
* active design
* manifest
* route map
* dynamic slots
* active plugins
* WooCommerce state
* Customizer state
* menu state

## 7.2 Create Rollback Checkpoint

```
docs/forensics/FRONTEND-EDIT-PRE-BASELINE.md
```

## 7.3 Verify Rollback Integrity

A backup that cannot be restored is not a real safety mechanism.

```
ROLLBACK ARTIFACT CREATED
    +
ROLLBACK ARTIFACT VERIFIED
```

Verification steps:

```
1. create checkpoint (git stash, git tag, or file snapshot)
2. verify checkpoint integrity (compare checksums / git status)
3. record exact restore procedure (command or steps)
4. if practical: perform a non-destructive restore verification
   (test that the rollback actually restores working state)
```

## 7.4 Never Continue If Ambiguous

```
If rollback state is ambiguous:
-> STOP
-> resolve ambiguity
-> re-verify rollback
-> then continue
```

---

# 8. PHASE 1 - READ THE TEMPLATE

Read the actual client frontend.

Inspect:

* HTML
* CSS
* JavaScript
* JSON
* manifest
* assets
* fonts
* existing documentation
* existing contracts

Understand the existing design before changing anything.

Do not replace unknown components simply because they look unfamiliar.

---

# 9. PHASE 2 - READ AUREON CONTRACTS

Read applicable Golden AUREON documentation.

Determine:

* platform-owned features
* frontend-owned features
* bridge-owned features
* existing dynamic slots
* route contract
* Customizer contract
* menu contract
* WooCommerce contract
* security contract

---

# 10. PHASE 3 - UNDERSTAND THE CLIENT REQUEST

Convert the client's request into an explicit change specification.

Record:

```
REQUEST
EXPECTED RESULT
AFFECTED PAGE
AFFECTED COMPONENT
RESPONSIVE REQUIREMENT
DATA REQUIREMENT
CUSTOMIZER REQUIREMENT
PLUGIN IMPACT
ROUTE IMPACT
BUSINESS-FUNCTION IMPACT
```

Separate:

```
REQUESTED
REQUIRED
OPTIONAL
OUT OF SCOPE
```

---

# 11. PHASE 4 - IMPACT ANALYSIS + BLAST RADIUS MAP

Before editing, determine full impact.

## 11.1 Standard Impact Analysis

* files affected
* pages affected
* components affected
* CSS dependencies
* JavaScript dependencies
* data dependencies
* dynamic slots
* Customizer controls
* WordPress menus
* WooCommerce features
* plugins
* routes
* responsive behavior
* accessibility
* performance

## 11.2 Blast Radius / Dependency Graph

For every edit, build the full dependency chain:

```
REQUEST
  |
COMPONENT
  |
FILES (PHP, CSS, JS, assets)
  |
DOM SLOTS (data attributes, selectors)
  |
JS EVENTS (click, scroll, submit, AJAX)
  |
DATA SOURCES (WC, WP, Customizer, demo)
  |
ROUTES (homepage, shop, category, search, account)
  |
PLUGINS (affected + critical)
  |
CUSTOMIZER CONTROLS
  |
WOOCOMMERCE FLOWS (cart, checkout, order)
```

### Example: Product Card Edit

```
Product-card edit
-> card HTML template
-> card CSS
-> card JS (hover, quick-view, wishlist)
-> homepage product grid
-> shop product grid
-> category product grid
-> search results
-> related products
-> upsell products
-> cross-sell products
-> WooCommerce product data contract
-> demo product fallback
-> Customizer card style controls
-> responsive breakpoints (1440, 1024, 768, 390)
```

### Example: Header Edit

```
Header edit
-> header HTML
-> header CSS
-> header JS (scroll, sticky, mega menu)
-> mega menu (hover, keyboard)
-> mobile hamburger menu
-> search overlay
-> account link
-> cart count display
-> language selector
-> announcement bar
-> ALL routes (header is global)
-> ALL responsive breakpoints
```

## 11.3 Blast Radius Documentation

Create:

```
docs/forensics/FRONTEND-EDIT-IMPACT-ANALYSIS.md
```

Include:

* full dependency chain (per 11.2)
* risk level per affected area
* test coverage required per affected area
* rollback scope

---

# 12. PHASE 5 - DETERMINE THE SMALLEST SAFE CHANGE

Prefer:

```
minimum safe modification
```

over:

```
full rewrite
```

Before creating a new component:

1. search the current client frontend;
2. find an existing equivalent;
3. reuse it where appropriate;
4. extend it if necessary;
5. only create new code when no suitable implementation exists.

---

# 13. PHASE 6 - PRESERVE UI/UX

Unless the client explicitly requested a redesign, preserve:

* typography
* colors
* spacing
* layout
* component hierarchy
* animations
* hover states
* focus states
* responsive behavior
* image treatment
* interaction style

Only change what was requested.

---

# 14. PHASE 7 - FRONTEND IMPLEMENTATION

Allowed editing:

* client HTML
* client CSS
* client JavaScript
* client assets
* client configuration
* client bridge code when necessary

Avoid changing:

* Golden Core
* generic AUREON platform contracts

unless a proven generic defect exists.

---

# 15. PHASE 8 - DYNAMIC SLOT + DATA SCHEMA PROTECTION

## 15.1 Dynamic Slot Documentation

Whenever changing a dynamic area, document:

```
SLOT
DOM TARGET
DATA SOURCE
BRIDGE
FALLBACK
TEST
```

Never accidentally remove:

* product slots
* category slots
* menu slots
* search slots
* customer slots
* cart slots
* checkout slots
* Customizer slots
* demo slots

If a selector changes:

update the bridge and tests in the same change.

## 15.2 Data Schema / Type Validation

For every dynamic slot, validate the actual data shape:

```
FIELD NAME
TYPE (string, integer, boolean, array, null)
REQUIRED (true / false)
NULLABLE (true / false)
DEFAULT VALUE
FORMAT
ESCAPING
SANITIZATION
```

### Known Critical Data Contracts

#### WooCommerce Price

```
FIELD:      product price / variation price
TYPE:       integer (cents)
REQUIRED:   true
NULLABLE:   false
FORMAT:     cents (e.g., 2499 = $24.99)
DISPLAY:    divide by 100, format as currency
ESCAPING:   numeric only
SANITIZATION: absint()
```

#### WooCommerce Product Image

```
FIELD:      product image URL
TYPE:       string (URL)
REQUIRED:   false
NULLABLE:   true
DEFAULT:    placeholder image
FORMAT:     full URL
ESCAPING:   esc_url()
SANITIZATION: wp_kses
```

#### WooCommerce Product Title

```
FIELD:      product name
TYPE:       string
REQUIRED:   true
NULLABLE:   false
FORMAT:     plain text
ESCAPING:   esc_html()
SANITIZATION: sanitize_text_field()
```

#### WooCommerce Variation Attributes

```
FIELD:      variation attribute label
TYPE:       string
REQUIRED:   true
NULLABLE:   false
FORMAT:     plain text (e.g., "Blue", "Large")
ESCAPING:   esc_html()
SANITIZATION: sanitize_text_field()
```

#### Cart Item Key

```
FIELD:      cart item key
TYPE:       string (hash)
REQUIRED:   true (when item exists)
NULLABLE:   false
FORMAT:     alphanumeric hash
ESCAPING:   used as array key, not displayed
SANITIZATION: wc_clean()
```

#### Cart Quantity

```
FIELD:      item quantity
TYPE:       integer
REQUIRED:   true
NULLABLE:   false
DEFAULT:    1
FORMAT:     positive integer
ESCAPING:   numeric only
SANITIZATION: absint()
```

### Bridge Output Validation Rule

Every bridge function must validate:

```
1. input type matches expected schema
2. required fields are present
3. nullable fields have explicit fallback
4. output format matches frontend expectation
5. numeric values are in expected unit (cents vs dollars)
6. URLs are valid and escaped
7. HTML content is sanitized
```

### Preventing Unit Mismatch Bugs

```
WC AJAX returns prices in CENTS (e.g., 2499)
Frontend expects DOLLARS (e.g., 24.99)

BRIDGE MUST:
-> detect source unit
-> convert if needed
-> output in expected unit
-> log conversion for debugging
```

---

# 16. PHASE 9 - WORDPRESS COMPATIBILITY

Verify the change does not break:

* WordPress routing
* pages
* menus
* search
* authentication
* account
* Customizer
* plugin output

A visual change is not considered isolated until dependencies are checked.

---

# 17. PHASE 10 - WOOCOMMERCE COMPATIBILITY

When the edited area touches ecommerce, test:

* simple products
* variable products
* product images
* prices
* sale prices
* stock
* add to cart
* quantity
* cart
* checkout
* order success
* related products
* categories

For variable products verify:

* variation ID
* attributes
* price
* SKU
* image
* stock
* add-to-cart

---

# 18. PHASE 11 - CART REGRESSION

When cart-related code changes, test:

* one item
* multiple items
* quantity increase
* quantity decrease
* remove
* subtotal
* total
* cart count
* mini-cart
* drawer
* empty cart
* reload persistence

A single-item success is insufficient.

---

# 19. PHASE 12 - CHECKOUT REGRESSION

When checkout-related UI changes, verify:

* customer information
* billing
* shipping
* coupon
* payment surface
* validation
* errors
* loading
* order summary
* successful order

Real WooCommerce state must be verified.

Do not fake checkout success.

---

# 20. PHASE 13 - AUTHENTICATION REGRESSION

For authentication-related edits test:

* login
* invalid login
* signup
* duplicate email
* validation
* logout
* forgot password
* authenticated state
* unauthenticated state

Real WordPress/WooCommerce authentication must be used.

---

# 21. PHASE 14 - ACCOUNT REGRESSION

Test:

* dashboard
* orders
* order details
* addresses
* account details
* password controls
* logout

---

# 22. PHASE 15 - SEARCH REGRESSION

Test:

* search trigger
* search input
* query
* results
* product results
* category results
* empty state
* invalid query
* mobile search

Use the actual WordPress/AUREON search source.

---

# 23. PHASE 16 - MENU REGRESSION

If navigation is affected, test:

* primary menu
* secondary menu
* desktop
* mobile
* dropdown
* mega menu
* active state
* footer menu
* URL integrity

WordPress supplies menu data.

The client frontend controls presentation.

---

# 24. PHASE 17 - CUSTOMIZER REGRESSION

When a customizable area changes, test every affected control.

Examples:

* logo
* favicon
* site title
* heading
* hero/banner
* hero image
* announcement
* colors
* typography
* header
* footer
* social
* newsletter
* backgrounds
* supported spacing controls

For each supported control:

```
DEFAULT
-> CHANGE
-> SAVE
-> RELOAD
-> VERIFY
-> CHANGE AGAIN
-> VERIFY
-> RESET
-> VERIFY DEFAULT/FALLBACK
```

Do not hardcode values that should come from Customizer.

---

# 25. PHASE 18 - PLUGIN COMPATIBILITY

## 25.1 Two Plugin Levels

### Level A - AFFECTED PLUGINS

Plugins directly impacted by the edit.

```
For each affected plugin:
PLUGIN
FEATURE
FRONTEND SURFACE
EXPECTED RESULT
ACTUAL RESULT
STATUS
```

Full functional test required.

### Level B - CRITICAL PLATFORM PLUGINS

Plugins that are always critical regardless of edit scope:

```
SECURITY PLUGINS     -> smoke test (login, nonce, validation)
CACHE PLUGINS        -> smoke test (fresh content, no stale data)
WOO-COMMERCE CORE    -> smoke test (product, cart, checkout)
PAYMENT PLUGINS      -> smoke test (checkout surface)
FORM PLUGINS         -> smoke test (contact, newsletter)
SEO PLUGINS          -> smoke test (meta, sitemap)
PERFORMANCE PLUGINS  -> smoke test (no blocking errors)
```

Even a CSS change can unexpectedly affect a security, cache, WooCommerce, payment, or form plugin.

## 25.2 Plugin Test Protocol

```
1. list all active plugins
2. classify: affected vs critical-platform vs unrelated
3. affected -> full functional test
4. critical-platform -> smoke test
5. unrelated -> skip (but monitor console)
```

Test real plugin behavior.

Do not assume plugin activation equals compatibility.

---

# 26. PHASE 19 - IMAGE / MEDIA REGRESSION

For changed components verify:

* image src
* srcset
* lazy loading
* picture/source
* background images
* JS-generated images
* dimensions
* aspect ratio
* mobile image behavior

Target:

```
ZERO REQUIRED BROKEN ASSETS
```

---

# 27. PHASE 20 - RESPONSIVE REGRESSION

Test:

```
1440
1024
768
390
```

Check:

* layout
* navigation
* typography
* cards
* product grids
* images
* modals
* drawers
* forms
* account
* cart
* checkout

---

# 28. PHASE 21 - ACCESSIBILITY REGRESSION

Verify:

* H1/H2 hierarchy
* labels
* keyboard navigation
* focus
* buttons
* forms
* ARIA
* alt text
* modal focus
* menus

Do not accept visual correctness while introducing accessibility regressions.

---

# 29. PHASE 22 - JAVASCRIPT REGRESSION

When JavaScript changes, test affected behavior.

Target:

```
ZERO UNEXPECTED JAVASCRIPT ERRORS
```

Check:

* event handlers
* menu
* search
* gallery
* variants
* filters
* cart
* modals
* drawers
* forms

---

# 30. PHASE 23 - NETWORK REGRESSION

Check that edits do not introduce:

* obsolete APIs
* unwanted tracking
* stale client-pack assets
* foreign commerce endpoints
* unexpected network calls

Classify every unusual request.

---

# 31. PHASE 24 - ROUTE REGRESSION

At minimum test:

```
/
/shop/
/product/{slug}
/product-category/{slug}
/search/
/my-account/
/cart/
/checkout/
/blog/
/404
```

Also test every route directly affected by the edit.

For every route verify:

```
HTTP status
expected page family
actual page family
DOM identity
assets
console
```

HTTP 200 by itself is NOT sufficient.

---

# 32. PHASE 25 - CACHE REGRESSION

Test after:

* frontend changes
* Customizer changes
* login/logout
* cart changes
* product changes
* menu changes

Verify:

* fresh value
* persistence
* correct reset
* no stale client data
* no stale old frontend

---

# 33. PHASE 26 - DEMO COMPATIBILITY

Do not create another demo system.

If the edited component uses demo content:

test:

```
DEMO DATA
-> component renders
```

and:

```
REAL DATA
-> component renders
```

If demo code itself changed:

perform the relevant demo regression.

The complete demo lifecycle is a platform regression requirement where applicable, but it should not force unnecessary work when the edit has no relationship to demo behavior.

---

# 34. PHASE 27 - CLIENT ISOLATION

Verify the active client pack remains isolated.

For the active client:

* correct CSS
* correct JS
* correct DOM
* correct data
* correct assets

No stale previous-client assets.

---

# 35. PHASE 28 - SECURITY REGRESSION

Any frontend mutation must preserve:

* nonce
* authorization
* validation
* sanitization
* safe error handling

Test invalid and unauthorized operations where applicable.

Never weaken security to simplify frontend integration.

---

# 36. PHASE 29 - FEATURE LOSS AUDIT

Compare:

```
BEFORE EDIT
vs
AFTER EDIT
```

Every affected capability must be:

```
PRESERVED
MODIFIED-AS-REQUESTED
BRIDGE_REQUIRED
OPTIONAL
EXCLUDED-WITH-REASON
```

No silent feature loss.

---

# 37. PHASE 30 - GOLDEN CORE INTEGRITY

## 37.1 Standard Core Check

Inspect Git diff.

Expected:

```
CLIENT PACK
-> editable

CLIENT BRIDGE
-> editable where required

GOLDEN CORE
-> unchanged
```

If Golden Core changed unexpectedly:

STOP.

Determine whether it is:

* accidental
* client-specific
* bridge-level
* genuinely generic

Never accept an unexplained Core change.

## 37.2 Machine-Readable Allowlist

### ALLOWED Changed Paths

```
ALLOWED:
  frontend/designs/<client-pack>/
  frontend/bridges/<client-bridge>/
  docs/
  test-results/
  test-results/transition/
```

### FORBIDDEN Changed Paths

```
FORBIDDEN:
  aureon/theme/inc/
  aureon/theme/frontend/
  aureon/theme/aether-*
  frontend/views/
  frontend/adapters/
  frontend/components/
  frontend/sections/
  frontend/tokens/
  frontend/manifest/
  aureon-studio/
  mu-plugins/
```

### Enforcement Rule

```
If a file under FORBIDDEN path changes:
  -> HARD STOP
  -> determine if accidental or genuine platform defect
  -> if accidental: revert
  -> if genuine platform defect: follow Phase 47 (WHEN TO MODIFY GOLDEN CORE)
  -> never silently accept a Core change
```

## 37.3 Core Change Verification Command

```bash
git diff --name-only | grep -E "^(aureon/theme/inc/|aureon/theme/frontend/|aureon/theme/aether|frontend/views/|frontend/adapters/|frontend/components/|frontend/sections/|frontend/tokens/|frontend/manifest/)"
# Expected: empty output
# If anything appears: HARD STOP
```

---

# 38. PHASE 31 - FAILURE LOOP

When any test fails:

```
FAIL
  |
CLASSIFY
  |
ROOT CAUSE
  |
LOWEST-CORRECT LAYER
  |
MINIMUM SAFE FIX
  |
TARGETED TEST
  |
RELATED REGRESSION
  |
FULL REGRESSION
```

Never use uncontrolled patch chains.

---

# 39. PHASE 32 - 4-LEVEL REGRESSION MODEL

## Level 0 - STATIC

```
syntax / schema / references
PHP lint, JS syntax, CSS validity
file existence, import resolution
data schema validation
```

## Level 1 - TARGETED

```
changed component only
specific feature test
direct DOM / interaction verification
```

## Level 2 - DEPENDENCY

```
pages/features using the changed component
blast-radius affected routes
related WooCommerce flows
Customizer controls
plugin surfaces
```

## Level 3 - PLATFORM

```
full WP/WC/AUREON smoke test
full regression across all routes
full feature regression
security regression
performance regression
```

## Escalation Rules

```
small CSS change
  -> Level 0 + Level 1

cart JS change
  -> Level 0 + Level 1 + Level 2 (cart, checkout, mini-cart)

global header change
  -> Level 0 + Level 1 + Level 2 (all routes, menus, search, account)
  -> Level 3 (full platform regression)

Core change
  -> Level 0 + Level 1 + Level 2 + Level 3 (mandatory full regression)

bridge API change
  -> Level 0 + Level 1 + Level 2 (all consumers of bridge)
  -> Level 3 if bridge is global
```

---

# 40. PHASE 33 - VISUAL REGRESSION

## 40.1 Baseline Capture (BEFORE Edit)

For every affected page/route, capture:

```
ROUTE
VIEWPORT (1440, 1024, 768, 390)
SCREENSHOT (full page)
```

Store as baseline in:

```
docs/forensics/visual-baseline/
```

## 40.2 Post-Edit Comparison (AFTER Edit)

After editing:

```
1. capture same routes
2. same viewports
3. compare against baseline
4. investigate unexpected visual differences
```

## 40.3 Classification

```
Client-requested visual differences -> EXPECTED
Unrelated visual differences        -> REGRESSION CANDIDATE
```

## 40.4 What to Compare

* layout structure
* spacing / gaps
* typography (size, weight, line-height)
* image sizing / cropping / aspect ratio
* card dimensions
* alignment
* overlays / modals / drawers
* hover / focus states
* responsive breakpoints
* color application
* shadow / border
* animation presence

## 40.5 Important Distinction

This is NOT screenshot-driven redesign.

This is screenshot-driven REGRESSION DETECTION.

```
If visual change is client-requested:
  -> document as intentional
  -> update baseline

If visual change is unexpected:
  -> flag as regression
  -> investigate root cause
  -> fix before proceeding
```

---

# 41. PHASE 34 - API CONTRACT COMPATIBILITY

## 41.1 Existing Bridge APIs

Every existing bridge must maintain its input/output contract unless explicitly required to change.

```
EXISTING BRIDGE API
    |
DO NOT CHANGE INPUT/OUTPUT CONTRACT
```

## 41.2 Contract Dimensions

For each bridge API, verify:

```
ENDPOINT / FUNCTION
HTTP METHOD
REQUEST FIELDS (name, type, required)
RESPONSE SHAPE (structure, types)
ERROR FORMAT (error codes, messages)
NONCE / AUTH REQUIREMENT
HTTP STATUS CODES
CONTENT-TYPE
```

## 41.3 Critical Bridge APIs

### Cart AJAX

```
ENDPOINT:   admin-ajax.php (wc-ajax=add_to_cart, update_order_review)
METHOD:     POST
REQUEST:    product_id, variation_id, quantity, cart_item_key
RESPONSE:   fragments (HTML), cart_hash
NONCE:      required
```

### Search

```
ENDPOINT:   admin-ajax.php or REST
METHOD:     GET/POST
REQUEST:    s (query string), per_page
RESPONSE:   results array (id, title, url, image, price)
NONCE:      varies
```

### Customizer

```
ENDPOINT:   customize_save (WordPress Customizer)
METHOD:     POST
REQUEST:    customized (JSON blob of settings)
RESPONSE:   success/error
NONCE:      required (customize_save)
```

### Product/Variation Data

```
SOURCE:     WooCommerce variation data
SHAPE:      { variation_id, attributes: { pa_color: "Blue", pa_size: "S" }, display_price: 2499 }
UNIT:       price in cents
```

## 41.4 Contract Change Protocol

If a bridge API must change:

```
1. document current contract
2. document required change
3. identify all consumers
4. update contract
5. update all consumers in same change
6. verify all consumers work
7. document new contract
```

Never change a bridge API without updating all its consumers.

---

# 42. PHASE 35 - RELEASE PACKAGE AUDIT

## 42.1 Pre-Release Checklist

Before considering any edit complete:

```
ONLY INTENDED FILES SHIPPED
```

## 42.2 Audit Items

Check for and remove:

```
SECRETS
  -> API keys
  -> passwords
  -> tokens
  -> credentials

DEBUG ARTIFACTS
  -> var_dump() / print_r()
  -> console.log() debug statements
  -> test scripts
  -> debug pages

TEST FIXTURES
  -> test products (unless intentional)
  -> test orders
  -> test users
  -> QA data

TEMPORARY FILES
  -> .tmp files
  -> .bak files
  -> .old files
  -> scratch files
  -> temporary scripts

DEV ARTIFACTS
  -> source maps (unless intended)
  -> .map files
  -> node_modules
  -> package-lock.json (if not needed)
  -> .env files

SCREENSHOTS
  -> dev screenshots
  -> debug screenshots
  -> not for production

BACKUPS
  -> accidental backup copies in production paths
  -> old versions left in production directory
```

## 42.3 Package Verification

```
1. compare package against source
2. verify only approved files are included
3. remove QA fixtures
4. remove temporary scripts
5. remove secrets
6. remove dev-only artifacts
7. record final package size
8. record final file manifest
```

## 42.4 Manifest Output

```
docs/forensics/RELEASE-PACKAGE-MANIFEST.md
```

Include:

```
TOTAL FILES
TOTAL SIZE
FILE LIST (relative paths)
EXCLUDED FILES (and reason)
VERIFICATION STATUS
```

---

# 43. PHASE 36 - EDIT ACCEPTANCE MATRIX

Create/update:

```
test-results/FRONTEND-EDIT-ACCEPTANCE-MATRIX.json
```

Each test requires:

```
id
feature
page
setup
expected
actual
status
evidence
```

Allowed:

```
PASS
FAIL
OPTIONAL
NOT_APPLICABLE
BRIDGE_REQUIRED
```

Do not hide failures.

---

# 44. PHASE 37 - EDIT REPORT

Create:

```
docs/forensics/FRONTEND-EDIT-FINAL-REPORT.md
```

Include:

* client request
* approved scope
* scope changes (if any)
* files changed
* HTML changes
* CSS changes
* JS changes
* assets changed
* bridge changes
* affected slots
* data schema validation results
* Customizer impact
* menu impact
* plugin impact (affected + critical levels)
* WooCommerce impact
* route impact
* responsive impact
* accessibility impact
* network impact
* security impact
* cache behavior
* visual regression results
* API contract verification results
* regression (4-level model results)
* Golden Core verification
* feature-loss audit
* release package audit
* discovered improvements (scope lock reporting)
* remaining limitations

---

# 45. FINAL ACCEPTANCE

Return:

```
CLIENT_FRONTEND_EDIT_PASS
```

ONLY when:

- requested change complete
- approved scope only (no scope creep)
- existing UI/UX preserved except intentional changes
- Golden Core protected
- WordPress working
- WooCommerce working
- products working
- variations working where applicable
- add-to-cart working
- cart working
- checkout working
- real order flow working where affected
- authentication working where affected
- account working where affected
- menus working
- search working
- Customizer working
- relevant plugins working (affected + critical)
- routes working
- images working
- responsive working
- accessibility preserved
- console clean
- network clean
- cache correct
- client isolation preserved
- security preserved
- no silent feature loss
- data schema validated
- API contracts verified
- visual regression checked
- 4-level regression passed
- rollback verified
- release package audited
- final report generated

---

# 46. BLOCKED ACCEPTANCE

Return:

```
CLIENT_FRONTEND_EDIT_BLOCKED
```

if any required behavior fails.

Include:

```
problem
affected file
layer
root cause
safe fix
targeted test
regression test
```

Do not hide unresolved failures.

---

# 47. WHEN TO MODIFY GOLDEN CORE

Golden Core may only be considered when:

1. the problem reproduces across multiple client packs;
2. the behavior is clearly generic;
3. the defect belongs to the platform layer;
4. client-pack/bridge solutions are insufficient;
5. the smallest generic fix is identified;
6. complete Core regression is planned.

Otherwise:

```
DO NOT MODIFY CORE.
```

---

# 48. UNIVERSAL CLIENT EDIT WORKFLOW (MASTER)

Every client edit must follow:

```
CLIENT REQUEST
      |
SCOPE LOCK (approve scope, no creep)
      |
BASELINE + ROLLBACK VERIFY
      |
READ CURRENT FRONTEND
      |
READ PLATFORM CONTRACT
      |
IMPACT ANALYSIS + BLAST RADIUS
      |
CLASSIFY CLIENT / BRIDGE / CORE
      |
MINIMUM SAFE FRONTEND EDIT
      |
DATA SCHEMA VALIDATION
      |
API CONTRACT VERIFICATION
      |
LEVEL 0 - STATIC REGRESSION
      |
LEVEL 1 - TARGETED REGRESSION
      |
LEVEL 2 - DEPENDENCY REGRESSION
      |
VISUAL REGRESSION
      |
LEVEL 3 - FULL PLATFORM REGRESSION
      |
CORE INTEGRITY CHECK
      |
RELEASE PACKAGE AUDIT
      |
FINAL REPORT
      |
CLIENT_FRONTEND_EDIT_PASS
```

---

# 49. IMPORTANT PRINCIPLE

The frontend is editable.

The platform is protected.

The workflow must allow the client frontend to evolve without forcing changes into Golden AUREON.

The desired relationship is:

```
PREMIUM CLIENT FRONTEND
        +
GOLDEN AUREON
        =
CUSTOM CLIENT EXPERIENCE
WITHOUT PLATFORM BREAKAGE
```

---

# 50. FINAL OPERATING RULE

## EDIT THE CLIENT FRONTEND.
## PRESERVE THE UI/UX.
## PROTECT GOLDEN AUREON.
## PRESERVE WORDPRESS / WOOCOMMERCE.
## PRESERVE CUSTOMIZER.
## PRESERVE MENUS.
## PRESERVE SEARCH.
## PRESERVE AUTHENTICATION.
## PRESERVE CART.
## PRESERVE CHECKOUT.
## PRESERVE PLUGINS.
## PRESERVE SECURITY.
## LOCK THE SCOPE.
## VALIDATE DATA SCHEMAS.
## VERIFY API CONTRACTS.
## CHECK VISUAL REGRESSION.
## TEST EVERY DEPENDENCY (4 LEVELS).
## FIX FAILURES AT THE LOWEST CORRECT LAYER.
## NEVER ACCEPT SILENT FEATURE LOSS.
## AUDIT THE RELEASE PACKAGE.
## PROVE THE ROLLBACK WORKS.

---

# 51. USAGE

For any future client template, copy this file into:

```
your-client-template/
  docs/
    architecture/
      UNIVERSAL-FRONTEND-EDIT-AND-REGRESSION-PLAN.md
```

Then use this OpenCode instruction:

```
Read docs/architecture/UNIVERSAL-FRONTEND-EDIT-AND-REGRESSION-PLAN.md first.

Then inspect this client frontend and my requested UI/UX changes.

Follow the document exactly.

Do not modify Golden AUREON.
Analyze first.
Edit only the correct client/bridge layer.
Validate data schemas.
Verify API contracts.
Test the change.
Run visual regression.
Run 4-level regression.
Fix any regressions.
Audit the release package.
Generate the acceptance matrix and final report.
```

This file is template-agnostic. Use it for restaurant, furniture, fashion, SaaS, portfolio, or any other premium frontend while preserving the same AUREON platform underneath.

---

# APPENDIX A - PROVEN ARCHITECTURE PATTERN

From Vineta integration (the proof of this workflow):

```
WooCommerce product
    |
aether_adapter_product()   (existing, UNCHANGED)
    |
section-*.php receives $sectionData
    |
vineta_build_page_data($sectionData)   (thin mapper in pack)
    |
window.VinetaPageData = { ... }        (JSON, public-safe)
    |
Frozen HTML DOM                         (verbatim from source)
    |
Frozen CSS                              (verbatim)
    |
vineta-*.js                             (reads VinetaPageData)
    |
vineta-bridge.js                        (intercepts app calls -> WC AJAX)
```

Key lessons:

* WC AJAX returns prices in cents (divide by 100 for display)
* Frozen HTML duplicate selectors must be hidden when dynamic alternatives exist
* Cart bridge expects `$_POST['updates']` as JSON `{"cart_item_key": quantity}`
* jQuery bridge saves/restores WP jQuery, copies Bootstrap plugins
* Path bridge rewrites flat-file links to WP permalinks
* Activation: `aether_active_design` = pack slug; static pages created; WC pages configured

---

# APPENDIX B - VERIFICATION COMMANDS

```bash
# PHP syntax
find <pack-dir> -name "*.php" -exec php -l {} \;

# JS syntax
find <pack-dir> -name "*.js" ! -name "*.min.js" -exec node --check {} \;

# Brand isolation (no previous client leakage)
grep -rni '<previous-client-slug>' <pack-dir>

# Core integrity (no unexpected changes)
git diff --stat aureon/theme/ aureon/plugin/
# Expected: empty

# Core integrity allowlist check
git diff --name-only | grep -E "^(aureon/theme/inc/|aureon/theme/frontend/|frontend/views/|frontend/adapters/|frontend/components/|frontend/sections/|frontend/tokens/|frontend/manifest/)"
# Expected: empty output

# Route verification
curl -s -o /dev/null -w "%{http_code}" http://localhost:8080/

# Console errors (Playwright)
browser_console_messages(level="error")
```

---

# APPENDIX C - REQUIRED TOOL CALLS

For every regression cycle, use:

* `serena_initial_instructions` - Read Serena manual (once)
* `serena_list_memories` - Discover available project knowledge
* `serena_read_memory` - Read specific architecture/context memories
* `serena_activate_project` - Activate project context
* `playwright-mcp_browser_navigate` - Open route for testing
* `playwright-mcp_browser_snapshot` - Capture DOM state
* `playwright-mcp_browser_console_messages` - Check for JS errors
* `playwright-mcp_browser_network_requests` - Check for network anomalies
* `playwright-mcp_browser_take_screenshot` - Visual evidence
* `playwright-mcp_browser_click` - Interaction testing
* `playwright-mcp_browser_type` - Form testing
* `playwright-mcp_browser_fill_form` - Multi-field testing
* `bash` - Git, PHP lint, file operations
* `read` - Inspect files
* `grep` - Search codebase
* `glob` - Find files by pattern
* `todowrite` - Track regression progress

---

# APPENDIX D - BLAST RADIUS EXAMPLES

## D.1 Product Card Edit

```
EDIT: product card component
FILES:
  -> components/product-card.php
  -> css/product-card.css
  -> js/product-card.js
DOM SLOTS:
  -> .product-card
  -> .product-card__image
  -> .product-card__title
  -> .product-card__price
  -> .product-card__add-to-cart
ROUTES:
  -> / (homepage product grid)
  -> /shop/ (shop grid)
  -> /product-category/{slug} (category grid)
  -> /search/ (search results)
  -> /product/{slug} (related products)
PLUGINS:
  -> WooCommerce (product data)
  -> wishlist plugin (heart icon)
  -> quick-view plugin (modal)
CUSTOMIZER:
  -> card style controls
  -> card layout controls
WOOCOMMERCE:
  -> simple product add-to-cart
  -> variable product add-to-cart
  -> price display
  -> sale price badge
```

## D.2 Cart Widget Edit

```
EDIT: mini-cart / cart drawer
FILES:
  -> components/mini-cart.php
  -> css/mini-cart.css
  -> js/mini-cart.js
  -> js/cart-bridge.js
DOM SLOTS:
  -> .mini-cart
  -> .mini-cart__item
  -> .mini-cart__quantity
  -> .mini-cart__total
  -> .cart-count
ROUTES:
  -> ALL (mini-cart is triggered from header on all pages)
  -> /cart/ (full cart page)
  -> /checkout/ (order review)
PLUGINS:
  -> WooCommerce (cart sessions)
  -> cart fragments (AJAX cart update)
  -> coupon plugins
CUSTOMIZER:
  -> cart icon style
  -> cart drawer position
WOOCOMMERCE:
  -> add to cart
  -> update quantity
  -> remove item
  -> apply coupon
  -> cart totals
  -> cart persistence
```

## D.3 Header Edit

```
EDIT: site header
FILES:
  -> components/shell/header.php
  -> css/ferm-shell.css (header section)
  -> js/ferm-shell.js (header behaviors)
DOM SLOTS:
  -> .site-header
  -> .mega-menu
  -> .mobile-nav
  -> .search-overlay
  -> .cart-count
  -> .account-link
  -> .language-selector
ROUTES:
  -> ALL (header is global)
PLUGINS:
  -> WPML (language switcher)
  -> sticky header plugin
CUSTOMIZER:
  -> logo
  -> header layout
  -> header colors
  -> announcement bar
  -> navigation style
WOOCOMMERCE:
  -> cart count display
  -> search integration
```

---

# APPENDIX E - DATA SCHEMA CATALOG

## E.1 WooCommerce Product

```
product_id          integer   required   unique
product_title       string    required   esc_html()
product_price       integer   required   cents
sale_price          integer   nullable   cents (null = no sale)
regular_price       integer   required   cents
product_image       string    nullable   esc_url()
product_gallery     array     nullable   array of image URLs
product_sku         string    nullable   sanitize_text_field()
stock_status        string    required   instock | outofstock | onbackorder
stock_quantity      integer   nullable   absint()
product_url         string    required   esc_url()
product_category    array     required   array of category objects
product_type        string    required   simple | variable | grouped | external
```

## E.2 WooCommerce Variation

```
variation_id        integer   required   unique
variation_title     string    required   esc_html()
attributes          array     required   { pa_color: "Blue", pa_size: "S" }
display_price       integer   required   cents
display_regular_price integer required   cents
is_on_sale          boolean   required   true | false
is_in_stock         boolean   required   true | false
variation_image     string    nullable   esc_url()
variation_sku       string    nullable   sanitize_text_field()
```

## E.3 Cart Item

```
cart_item_key       string    required   hash string
product_id          integer   required   absint()
variation_id        integer   nullable   absint()
quantity            integer   required   absint() (min: 1)
line_subtotal       integer   required   cents
line_total          integer   required   cents
product_name        string    required   esc_html()
product_image       string    nullable   esc_url()
```

## E.4 Cart Totals

```
subtotal            integer   required   cents
discount_total      integer   required   cents
shipping_total      integer   required   cents
tax_total           integer   required   cents
total               integer   required   cents
cart_contents_count integer   required   absint()
```

## E.5 Search Result

```
result_id           integer   required
result_title        string    required   esc_html()
result_url          string    required   esc_url()
result_image        string    nullable   esc_url()
result_price        integer   nullable   cents
result_type         string    required   product | post | page | category
```

## E.6 Customizer Setting

```
setting_id          string    required   sanitize_key()
setting_value       mixed     required   depends on control type
default_value       mixed     required   depends on control type
transport           string    required   refresh | postMessage
```

---

# APPENDIX F - CORE INTEGRITY ALLOWLIST

## F.1 Allowed Changed Paths (Client Edit)

```
frontend/designs/<client-pack>/**     Client pack files
frontend/bridges/<client-bridge>/**   Client bridge files
frontend/assets/<client-assets>/**    Client assets
docs/**                               Documentation
test-results/**                       Test results
```

## F.2 Forbidden Changed Paths (Platform Core)

```
aureon/theme/inc/**                   Theme includes (platform)
aureon/theme/aether-*.php            Theme aether files (platform)
aureon/theme/functions.php           Theme functions (platform)
aureon/theme/style.css               Theme stylesheet (platform)
frontend/views/**                     Engine views (platform)
frontend/adapters/**                  Platform adapters
frontend/components/**                Platform components
frontend/sections/**                  Platform sections
frontend/tokens/**                    Platform tokens
frontend/manifest/**                  Platform manifests
aureon-studio/**                      Plugin files
mu-plugins/**                         Must-use plugins
```

## F.3 Enforcement

```bash
# Check for forbidden changes
git diff --name-only | grep -E "^(aureon/theme/inc/|aureon/theme/aether|aureon/theme/functions|aureon/theme/style\.css|frontend/views/|frontend/adapters/|frontend/components/|frontend/sections/|frontend/tokens/|frontend/manifest/|aureon-studio/|mu-plugins/)"

# If output is non-empty: HARD STOP
# If output is empty: PASS
```

---

# APPENDIX G - API CONTRACT TEMPLATES

## G.1 Cart AJAX Contract

```
ADD TO CART:
  URL:      /?wc-ajax=add_to_cart (or admin-ajax.php)
  METHOD:   POST
  FIELDS:   product_id (int, required)
            quantity (int, required, default: 1)
            variation_id (int, optional)
            attribute_* (string, optional, for variations)
  RESPONSE: { fragments: { ".cart-fragment": "HTML" }, cart_hash: "string" }
  NONCE:    woocommerce-cart-nonce
  ERRORS:   { products: { product_id: { error: "message" } } }
```

## G.2 Cart Update Contract

```
UPDATE QUANTITY:
  URL:      /?wc-ajax=update_order_review (or admin-ajax.php)
  METHOD:   POST
  FIELDS:   updates (JSON string: {"cart_item_key": quantity})
  RESPONSE: { fragments: { ".cart-fragment": "HTML" }, cart_hash: "string" }
  NONCE:    woocommerce-cart-nonce
```

## G.3 Checkout Contract

```
PLACE ORDER:
  URL:      /?wc-ajax=checkout
  METHOD:   POST
  FIELDS:   billing_first_name, billing_last_name, billing_email, etc.
  RESPONSE: { result: "success", redirect: "/checkout/order-received/" }
            or { result: "failure", messages: "HTML" }
  NONCE:    woocommerce-checkout
```

## G.4 Search Contract

```
SEARCH:
  URL:      /?s={query}&post_type=product (or AJAX endpoint)
  METHOD:   GET
  FIELDS:   s (string, required)
  RESPONSE: HTML page or JSON array of results
  FIELDS:   { id, title, url, image, price, type }
```

## G.5 Customizer Contract

```
SAVE:
  URL:      /wp-admin/customize.php (WordPress Customizer)
  METHOD:   POST
  FIELDS:   customized (JSON blob of all settings)
  RESPONSE: 200 OK or error
  NONCE:    customize_save
  HEADERS:  X-WP-Nonce (for REST API)
```

---

*This document is the permanent agency-level frontend editing standard for Golden AUREON.*
*Version 1.1.0 - Upgraded with scope lock, blast-radius, rollback verification, visual regression, schema validation, release audit, core allowlist, plugin levels, API contracts, 4-level testing.*
