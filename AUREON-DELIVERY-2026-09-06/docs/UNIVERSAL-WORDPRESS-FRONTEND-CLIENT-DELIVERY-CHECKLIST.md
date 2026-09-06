# GOLDEN AUREON — UNIVERSAL WORDPRESS / PREMIUM FRONTEND / CLIENT DELIVERY MASTER CHECKLIST

Version: 2.0.0

Purpose: End-to-end checklist for taking any premium HTML/CSS/JS frontend, connecting it to the Golden AUREON WordPress/WooCommerce platform, safely editing it for a client, testing every important capability, and delivering the finished client theme without breaking the platform.

## 0. OPERATING MODEL

```text
GOLDEN AUREON CORE
        │
        │ protected
        ▼
CLIENT FRONTEND / CLIENT PACK
        │
        │ editable
        ▼
BRIDGE / DATA CONNECTION
        │
        ▼
WORDPRESS + WOOCOMMERCE + PLUGINS
```

### Ownership

Golden Core provides: WordPress integration, WooCommerce integration, routing, security, menus, search, authentication, account, cart, checkout, Customizer, demo/fallback system, active-pack isolation, adapters, contracts.

Client Frontend provides: HTML, CSS, JavaScript, visual design, UX, layouts, animations, responsive presentation, component presentation, page composition.

Bridge provides: data translation, dynamic slots, DOM hooks, event integration, WordPress/WooCommerce mapping.

### Golden rule

```text
VISUAL / PRESENTATION → CLIENT PACK
DATA / BUSINESS → BRIDGE
GENERIC PLATFORM DEFECT → GOLDEN CORE ONLY AFTER PROOF
```

## 1. MASTER STATUS

- [ ] PROJECT_INTAKE_COMPLETE
- [ ] SOURCE_PROTECTED
- [ ] FORENSIC_AUDIT_PASS
- [ ] DESIGN_SELECTION_PASS
- [ ] ARCHITECTURE_MAPPING_PASS
- [ ] TEMPLATE_CLEANUP_PASS
- [ ] TEMPLATE_FEATURE_COMPLETION_PASS
- [ ] TEMPLATE_AUREON_READY_PASS
- [ ] TEMPLATE_STANDALONE_QA_PASS
- [ ] TEMPLATE_RELEASE_CANDIDATE_PASS
- [ ] PACK_INSTALLED_PASS
- [ ] ACTIVATION_PASS
- [ ] ROUTING_PASS
- [ ] WOOCOMMERCE_FUNCTIONAL_PASS
- [ ] AUTH_FUNCTIONAL_PASS
- [ ] ACCOUNT_FUNCTIONAL_PASS
- [ ] CART_FUNCTIONAL_PASS
- [ ] CHECKOUT_FUNCTIONAL_PASS
- [ ] SEARCH_PASS
- [ ] MENUS_PASS
- [ ] CUSTOMIZER_PASS
- [ ] PLUGIN_COMPATIBILITY_PASS
- [ ] SECURITY_PASS
- [ ] IMAGE_ASSET_PASS
- [ ] RESPONSIVE_PASS
- [ ] ACCESSIBILITY_PASS
- [ ] CONSOLE_PASS
- [ ] NETWORK_PASS
- [ ] CACHE_PASS
- [ ] CLIENT_ISOLATION_PASS
- [ ] FEATURE_RETENTION_PASS
- [ ] PRODUCTION_PACKAGE_PASS
- [ ] CLIENT_DELIVERY_PASS

## 2. CLIENT INTAKE

- [ ] Client/project identified
- [ ] Template/source location identified
- [ ] Target WordPress site identified
- [ ] Target WooCommerce site identified
- [ ] Hosting/environment identified
- [ ] WordPress version recorded
- [ ] WooCommerce version recorded
- [ ] PHP version recorded
- [ ] Active plugins inventoried
- [ ] Required plugins identified
- [ ] Existing menus recorded
- [ ] Existing Customizer state recorded
- [ ] Existing client data recorded
- [ ] Existing theme/client pack recorded
- [ ] Visual requirements recorded
- [ ] Functional requirements recorded
- [ ] Explicit exclusions recorded
- [ ] Required routes recorded
- [ ] Required commerce features recorded
- [ ] Third-party integrations recorded

Create: `docs/forensics/CLIENT-INTAKE.md`

## 3. SCOPE LOCK

Translate the request into:

- [ ] REQUESTED
- [ ] REQUIRED
- [ ] OPTIONAL
- [ ] OUT_OF_SCOPE

Hard rules:

- [ ] No unrelated features
- [ ] No unrelated redesigns
- [ ] No unrelated refactors
- [ ] No unrelated optimization without evidence
- [ ] No unrequested behavior changes

Out-of-scope improvements are report-only unless approved.

## 4. SOURCE PROTECTION / BASELINE

Before editing:

- [ ] Git branch recorded
- [ ] Git commit recorded
- [ ] Working tree recorded
- [ ] Client pack path recorded
- [ ] Active design recorded
- [ ] Manifest recorded
- [ ] Route map recorded
- [ ] Dynamic slots recorded
- [ ] Active plugins recorded
- [ ] WooCommerce state recorded
- [ ] Customizer state recorded
- [ ] Menu state recorded
- [ ] Source file count recorded
- [ ] Source size recorded
- [ ] HTML/CSS/JS/image/font counts recorded

Create: `docs/forensics/PRE-EDIT-BASELINE.md`

Checkpoint:

- [ ] Backup/checkpoint created
- [ ] Backup is readable
- [ ] Restore procedure documented
- [ ] Rollback artifact is usable

## 5. GOLDEN CORE PROTECTION

Identify protected paths such as:

- [ ] `aureon/frontend/views/`
- [ ] `aureon/frontend/adapters/`
- [ ] `aureon/frontend/components/`
- [ ] `aureon/frontend/sections/`
- [ ] `aureon/frontend/manifest/`
- [ ] `aureon/frontend/tokens/`
- [ ] Generic routing
- [ ] Generic WooCommerce integration
- [ ] Generic security
- [ ] Generic plugin infrastructure

Verify:

- [ ] Core diff clean
- [ ] Protected paths unchanged
- [ ] No unexplained Core modifications

If Core change appears necessary: STOP and create `CORE-CHANGE-REQUEST.md` containing defect, reproduction evidence, why it is generic, why client/bridge fixes are insufficient, proposed minimum fix, and regression impact.

## 6. FORENSIC SOURCE INVENTORY

Read the complete frontend:

- [ ] HTML
- [ ] CSS
- [ ] JavaScript
- [ ] JSON
- [ ] images
- [ ] fonts
- [ ] video
- [ ] 3D/media
- [ ] vendor libraries
- [ ] PHP/server files if present
- [ ] build files
- [ ] manifests
- [ ] source maps
- [ ] docs

Record:

- [ ] page count
- [ ] page families
- [ ] variants
- [ ] duplicate pages
- [ ] duplicate assets
- [ ] third-party dependencies
- [ ] external services
- [ ] business APIs
- [ ] tracking
- [ ] hardcoded business data
- [ ] secrets
- [ ] obsolete source logic

Create `SOURCE-INVENTORY.json`.

## 7. DESIGN SELECTION

If multiple designs exist:

- [ ] classify variants
- [ ] select ONE production design
- [ ] document selected design
- [ ] identify supporting components needed
- [ ] exclude unnecessary alternate designs

Do NOT create a multi-home production switcher or AI-generated replacement unless explicitly requested.

## 8. PAGE FAMILY INVENTORY

Verify retained page families:

- [ ] Home
- [ ] Shop
- [ ] Product
- [ ] Variable product
- [ ] Category
- [ ] Collection
- [ ] Search
- [ ] Login
- [ ] Signup/Register
- [ ] Lost password
- [ ] Account
- [ ] Orders
- [ ] Addresses
- [ ] Account details
- [ ] Cart
- [ ] Mini-cart
- [ ] Cart drawer
- [ ] Checkout
- [ ] Order success/Thank-you
- [ ] Wishlist
- [ ] Compare
- [ ] Blog
- [ ] Article
- [ ] Static pages
- [ ] FAQ
- [ ] Contact
- [ ] Policies
- [ ] 404

Update `ROUTE-MAP.md`.

## 9. HARDCODED CONTENT / SECRETS AUDIT

Search for fake or source-specific:

- [ ] addresses
- [ ] phone numbers
- [ ] emails
- [ ] company names
- [ ] author/ThemeForest attribution
- [ ] demo links
- [ ] source URLs
- [ ] old client references
- [ ] foreign commerce URLs
- [ ] test credentials
- [ ] API keys
- [ ] private tokens
- [ ] tracking IDs

Classify each occurrence and remove only inappropriate content.

## 10. CLEANUP

Clean only proven unnecessary:

- [ ] obsolete PHP
- [ ] demo switchers
- [ ] demo-only modals
- [ ] template attribution
- [ ] source business data
- [ ] dead scripts
- [ ] obsolete endpoints
- [ ] duplicate assets
- [ ] unused assets
- [ ] unused page variants

After bulk operations:

- [ ] HTML integrity
- [ ] CSS integrity
- [ ] JS integrity
- [ ] links
- [ ] forms
- [ ] page identity
- [ ] no accidental deletion

## 11. THIRD-PARTY LIBRARIES

For every vendor library record:

- [ ] name
- [ ] version
- [ ] purpose
- [ ] used pages
- [ ] required/optional
- [ ] license status
- [ ] removal risk

Classify:

- [ ] KEEP_REQUIRED
- [ ] KEEP_OPTIONAL
- [ ] REMOVE_UNUSED
- [ ] LICENSE_REVIEW

## 12. IMAGE / MEDIA AUDIT

Check every image/media reference:

- [ ] `src`
- [ ] `srcset`
- [ ] `data-src`
- [ ] `data-srcset`
- [ ] `picture`
- [ ] `source`
- [ ] CSS background
- [ ] JS-generated media
- [ ] lazy loading
- [ ] video
- [ ] 3D
- [ ] fonts

Classify REQUIRED / DUPLICATE / UNUSED / UNKNOWN.

Target: ZERO REQUIRED BROKEN ASSETS.

## 13. CSS AUDIT

Verify:

- [ ] variables/tokens
- [ ] reset
- [ ] typography
- [ ] layout
- [ ] grid
- [ ] cards
- [ ] buttons
- [ ] forms
- [ ] state styles
- [ ] responsive styles
- [ ] animation styles
- [ ] required vendor CSS

Remove only proven dead/duplicate CSS.

## 14. JAVASCRIPT AUDIT

Verify:

- [ ] navigation
- [ ] mobile menu
- [ ] search
- [ ] sliders
- [ ] gallery
- [ ] zoom
- [ ] variants
- [ ] filters
- [ ] sorting
- [ ] pagination
- [ ] drawers
- [ ] modals
- [ ] forms
- [ ] cart
- [ ] account
- [ ] auth
- [ ] tracking

Check syntax, dependency order, selectors, events and DOM hooks.

Target: ZERO UNEXPECTED JS ERRORS.

## 15. DYNAMIC SLOT CONTRACT

For every dynamic slot define:

- [ ] slot name
- [ ] DOM target
- [ ] type
- [ ] required/optional
- [ ] nullable
- [ ] default
- [ ] data source
- [ ] bridge
- [ ] validation
- [ ] sanitization
- [ ] escaping
- [ ] fallback
- [ ] test

Typical slots:

- [ ] logo
- [ ] favicon
- [ ] site name
- [ ] heading
- [ ] announcement
- [ ] hero
- [ ] hero image
- [ ] product
- [ ] product image
- [ ] gallery
- [ ] variation
- [ ] category
- [ ] collection
- [ ] search
- [ ] menu
- [ ] customer
- [ ] login
- [ ] signup
- [ ] account
- [ ] cart
- [ ] checkout
- [ ] order
- [ ] newsletter
- [ ] footer

## 16. DATA CONTRACT

For each bridge field document:

- [ ] field name
- [ ] type
- [ ] units
- [ ] format
- [ ] source
- [ ] nullable behavior
- [ ] default
- [ ] validation
- [ ] sanitization
- [ ] escaping

Keep business values separate from display strings.

## 17. ROUTING

Every retained route must have:

- [ ] source route
- [ ] WordPress route
- [ ] WooCommerce route if relevant
- [ ] page family
- [ ] template
- [ ] bridge
- [ ] data
- [ ] fallback
- [ ] expected identity/title

Test both HTTP status and correct rendered page identity.

## 18. WORDPRESS DATA

Verify frontend can receive:

- [ ] pages
- [ ] menus
- [ ] media
- [ ] users/customers
- [ ] search
- [ ] site settings
- [ ] theme settings
- [ ] Customizer values

## 19. WOOCOMMERCE PRODUCT

Real simple product:

- [ ] title
- [ ] description
- [ ] short description
- [ ] price
- [ ] sale price
- [ ] SKU
- [ ] stock
- [ ] image
- [ ] gallery
- [ ] categories
- [ ] tags
- [ ] URL
- [ ] add-to-cart

## 20. VARIABLE PRODUCT

Verify:

- [ ] attributes
- [ ] variation ID
- [ ] selector
- [ ] price change
- [ ] SKU change
- [ ] image change
- [ ] stock
- [ ] availability
- [ ] add-to-cart
- [ ] correct variation in cart
- [ ] unavailable combinations

## 21. PRODUCT DISCOVERY

Verify real products appear correctly in:

- [ ] homepage
- [ ] shop
- [ ] category
- [ ] collection
- [ ] search
- [ ] related
- [ ] recommendations

## 22. CART

Test:

- [ ] single item
- [ ] multiple items
- [ ] simple product
- [ ] variable product
- [ ] quantity increase
- [ ] quantity decrease
- [ ] remove
- [ ] subtotal
- [ ] total
- [ ] cart count
- [ ] mini-cart
- [ ] drawer
- [ ] cart page
- [ ] empty cart
- [ ] error state
- [ ] reload persistence

Verify actual WooCommerce cart state.

## 23. CHECKOUT

Test:

- [ ] customer information
- [ ] billing
- [ ] shipping
- [ ] coupon
- [ ] payment method
- [ ] order summary
- [ ] validation
- [ ] invalid field
- [ ] loading
- [ ] error
- [ ] place order
- [ ] successful order
- [ ] thank-you page

PASS means a real WooCommerce order is created where the environment supports real orders.

## 24. ORDER SUCCESS

Verify:

- [ ] order number
- [ ] customer
- [ ] items
- [ ] totals
- [ ] status
- [ ] next actions
- [ ] account link

## 25. AUTHENTICATION

Test:

- [ ] login
- [ ] invalid login
- [ ] signup
- [ ] duplicate email
- [ ] invalid email
- [ ] password validation
- [ ] logout
- [ ] forgot password
- [ ] authenticated state
- [ ] unauthenticated state
- [ ] session persistence

Use real WordPress/WooCommerce authentication.

## 26. ACCOUNT

Test:

- [ ] dashboard
- [ ] orders
- [ ] order detail
- [ ] addresses
- [ ] account details
- [ ] password controls
- [ ] logout
- [ ] empty states
- [ ] error states

## 27. SEARCH

Test:

- [ ] trigger
- [ ] input
- [ ] query
- [ ] product results
- [ ] category results
- [ ] empty result
- [ ] invalid query
- [ ] result links
- [ ] mobile

No hardcoded fake results.

## 28. WORDPRESS MENUS

Verify real WP menu data populates:

- [ ] primary
- [ ] secondary where used
- [ ] desktop
- [ ] mobile
- [ ] dropdown
- [ ] mega menu
- [ ] active state
- [ ] footer
- [ ] hierarchy
- [ ] URLs

## 29. CUSTOMIZER

Inventory all actual supported controls.

At minimum, where supported:

- [ ] logo
- [ ] favicon
- [ ] site title
- [ ] tagline/heading
- [ ] announcement
- [ ] hero/banner
- [ ] hero image
- [ ] colors
- [ ] typography
- [ ] header
- [ ] footer
- [ ] social
- [ ] newsletter
- [ ] backgrounds
- [ ] spacing/layout controls
- [ ] WooCommerce presentation controls

For each supported control:

```text
DEFAULT
→ CHANGE
→ SAVE
→ RELOAD
→ VERIFY
→ CHANGE AGAIN
→ VERIFY
→ RESET
→ RELOAD
→ VERIFY FALLBACK
```

No control should be presented as working if it has no actual effect, unless documented.

## 30. PLUGIN COMPATIBILITY

Inventory and test every production plugin.

For each record:

- [ ] plugin
- [ ] version
- [ ] capability
- [ ] frontend surface
- [ ] expected result
- [ ] actual result
- [ ] status

Critical plugin smoke tests include, when installed:

- [ ] WooCommerce
- [ ] AUREON
- [ ] session fixes
- [ ] payment
- [ ] shipping
- [ ] forms
- [ ] search
- [ ] wishlist
- [ ] compare
- [ ] security
- [ ] cache
- [ ] SEO
- [ ] analytics

## 31. API / BRIDGE CONTRACT

For AJAX/API interactions verify:

- [ ] endpoint
- [ ] HTTP method
- [ ] request fields
- [ ] response fields
- [ ] response types
- [ ] status codes
- [ ] error shape
- [ ] nonce
- [ ] authorization
- [ ] validation
- [ ] sanitization

## 32. SECURITY

Verify:

- [ ] nonce checks
- [ ] authorization
- [ ] input validation
- [ ] sanitization
- [ ] contextual escaping
- [ ] safe error output
- [ ] no credentials in frontend
- [ ] no private keys/tokens
- [ ] no privileged operation exposed incorrectly

Typical output rules:

```text
text → esc_html()
attribute → esc_attr()
URL → esc_url()
trusted HTML fragment → wp_kses_post()
```

## 33. ACCESSIBILITY

Verify:

- [ ] H1/H2 hierarchy
- [ ] labels
- [ ] button names
- [ ] link names
- [ ] keyboard navigation
- [ ] focus states
- [ ] visible focus
- [ ] ARIA where appropriate
- [ ] alt text
- [ ] modal focus
- [ ] menu keyboard operation
- [ ] validation announcements

## 34. RESPONSIVE

Test:

- [ ] 1440px
- [ ] 1024px
- [ ] 768px
- [ ] 390px

Check:

- [ ] header
- [ ] navigation
- [ ] hero
- [ ] cards
- [ ] product grid
- [ ] product gallery
- [ ] filters
- [ ] forms
- [ ] modals
- [ ] drawers
- [ ] account
- [ ] cart
- [ ] checkout
- [ ] no horizontal overflow
- [ ] no clipped content
- [ ] usable controls

## 35. VISUAL REGRESSION

Before editing:

- [ ] capture screenshots of affected routes
- [ ] record viewport
- [ ] record relevant state

After editing:

- [ ] capture same routes
- [ ] compare layout
- [ ] compare typography
- [ ] compare spacing
- [ ] compare image sizing/cropping
- [ ] compare cards
- [ ] compare overlays
- [ ] compare responsive behavior
- [ ] compare hover/focus where relevant

Classify differences as REQUESTED, EXPECTED, or UNEXPECTED REGRESSION.

Screenshots are for regression detection, not screenshot-driven redesign.

## 36. IMAGE / ASSET QA

For every retained page verify:

- [ ] images load
- [ ] src correct
- [ ] srcset correct
- [ ] lazy loading correct
- [ ] picture/source correct
- [ ] CSS background correct
- [ ] JS-loaded images correct
- [ ] fonts load
- [ ] video/3D loads where required

Target: ZERO REQUIRED BROKEN ASSETS.

## 37. CONSOLE QA

Target:

- [ ] zero unexpected JS errors
- [ ] no uncaught exceptions
- [ ] no failed required initialization
- [ ] no repeated runtime errors

Classify remaining warnings.

## 38. NETWORK QA

Classify every request:

- [ ] local asset
- [ ] allowed presentation dependency
- [ ] WordPress
- [ ] WooCommerce
- [ ] plugin
- [ ] analytics
- [ ] unnecessary
- [ ] foreign business API
- [ ] stale previous-client dependency

## 39. ROUTE QA

For every retained route:

- [ ] HTTP status
- [ ] expected page family
- [ ] actual page family
- [ ] DOM identity
- [ ] CSS
- [ ] JS
- [ ] images
- [ ] console
- [ ] network

No unrelated page fallback.

## 40. CACHE QA

Test after:

- [ ] frontend edit
- [ ] Customizer update
- [ ] product update
- [ ] menu update
- [ ] login/logout
- [ ] cart update

Verify:

- [ ] fresh values
- [ ] persistence
- [ ] reset
- [ ] no stale client data
- [ ] no stale previous frontend
- [ ] no stale assets

## 41. DEMO / FALLBACK COMPATIBILITY

Use the existing AUREON demo/fallback system.

Do NOT create another demo engine.

Where relevant verify:

- [ ] demo product renders
- [ ] real product renders
- [ ] demo category renders
- [ ] real category renders
- [ ] fallback logo works
- [ ] fallback hero works
- [ ] fallback heading works

If demo code itself changed, run the applicable demo regression.

## 42. CLIENT ISOLATION

When the client pack is active:

- [ ] correct CSS
- [ ] correct JS
- [ ] correct DOM
- [ ] correct data
- [ ] correct assets
- [ ] no previous client CSS
- [ ] no previous client JS
- [ ] no previous client DOM
- [ ] no previous client data
- [ ] no previous client URLs

## 43. FEATURE LOSS AUDIT

Compare BEFORE vs AFTER.

Every important capability must be:

- [ ] PRESERVED
- [ ] MODIFIED_AS_REQUESTED
- [ ] BRIDGE_REQUIRED
- [ ] OPTIONAL
- [ ] EXCLUDED_WITH_REASON

No silent feature loss.

## 44. PLATFORM SMOKE TEST

Even for a small frontend edit, run the appropriate platform smoke test:

- [ ] homepage
- [ ] shop
- [ ] product
- [ ] variable product
- [ ] category
- [ ] search
- [ ] login
- [ ] account
- [ ] cart
- [ ] checkout
- [ ] menus
- [ ] Customizer

Escalate to full regression when risk is high.

## 45. TEST ESCALATION

### Level 0 — Static

- [ ] syntax
- [ ] schema
- [ ] references
- [ ] manifest

### Level 1 — Targeted

- [ ] changed component

### Level 2 — Dependency

- [ ] every page/feature using the component

### Level 3 — Platform

- [ ] WordPress/WooCommerce/AUREON smoke or full regression

## 46. FAILURE LOOP

```text
FAIL
 ↓
CLASSIFY
 ↓
ROOT CAUSE
 ↓
LOWEST CORRECT LAYER
 ↓
MINIMUM SAFE FIX
 ↓
TARGETED TEST
 ↓
DEPENDENCY REGRESSION
 ↓
FULL REGRESSION WHEN REQUIRED
```

Never use uncontrolled patch chains.

## 47. ROLLBACK

- [ ] Previous release available
- [ ] Current release available
- [ ] Restore instructions available
- [ ] Rollback tested or otherwise verified
- [ ] Rollback cannot damage Core
- [ ] Rollback restores client pack/routing as required

## 48. PRODUCTION PACKAGE AUDIT

Before deployment/delivery:

- [ ] no API keys
- [ ] no secrets
- [ ] no debug credentials
- [ ] no temporary activation scripts
- [ ] no temporary test scripts
- [ ] no unwanted screenshots
- [ ] no logs
- [ ] no QA users
- [ ] no QA products
- [ ] no QA orders
- [ ] no development backups in production package
- [ ] no unintended alternate templates
- [ ] no unused assets
- [ ] no stale client files
- [ ] manifest finalized
- [ ] version finalized
- [ ] package size recorded
- [ ] file count recorded

## 49. PRODUCTION DEPLOYMENT

- [ ] production backup created
- [ ] deployment plan confirmed
- [ ] active design confirmed
- [ ] plugin versions confirmed
- [ ] PHP compatibility confirmed
- [ ] required filesystem structure confirmed
- [ ] permissions confirmed
- [ ] client pack deployed
- [ ] required bridge deployed
- [ ] required configuration deployed
- [ ] required assets deployed

## 50. PRODUCTION ACTIVATION

- [ ] active design points to correct pack
- [ ] homepage renders
- [ ] required routes render correctly
- [ ] no old client is active
- [ ] no stale assets
- [ ] no temporary migration scripts remain

## 51. REAL CLIENT DATA

### Branding

- [ ] logo
- [ ] favicon
- [ ] site title
- [ ] heading
- [ ] hero/banner
- [ ] announcement
- [ ] footer
- [ ] social
- [ ] newsletter

### Commerce

- [ ] products
- [ ] categories
- [ ] variations
- [ ] prices
- [ ] stock
- [ ] product media

### Content

- [ ] pages
- [ ] blog
- [ ] articles
- [ ] menus
- [ ] footer content

## 52. REAL CLIENT CUSTOMIZER

For each client-controlled setting:

- [ ] set
- [ ] save
- [ ] reload
- [ ] verify
- [ ] change
- [ ] save
- [ ] reload
- [ ] verify
- [ ] reset
- [ ] verify fallback

## 53. REAL CLIENT MENU

- [ ] primary
- [ ] mobile
- [ ] footer
- [ ] dropdown
- [ ] mega menu
- [ ] active state
- [ ] correct URLs
- [ ] hierarchy
- [ ] no source-template links

## 54. REAL CLIENT COMMERCE

- [ ] simple product
- [ ] variable product
- [ ] category
- [ ] search
- [ ] product page
- [ ] add-to-cart
- [ ] multi-item cart
- [ ] checkout
- [ ] order
- [ ] account order

## 55. REAL CLIENT AUTH

- [ ] login
- [ ] signup
- [ ] logout
- [ ] lost password
- [ ] customer state
- [ ] account
- [ ] orders
- [ ] addresses

## 56. REAL CLIENT PLUGINS

For each production-critical plugin:

- [ ] active
- [ ] configured
- [ ] frontend surface works
- [ ] feature works
- [ ] assets load
- [ ] security preserved

## 57. FINAL REGRESSION

### Pages

- [ ] Home
- [ ] Shop
- [ ] Product
- [ ] Variable Product
- [ ] Category
- [ ] Collection
- [ ] Search
- [ ] Login
- [ ] Signup
- [ ] Account
- [ ] Orders
- [ ] Addresses
- [ ] Cart
- [ ] Checkout
- [ ] Order Success
- [ ] Blog
- [ ] Article
- [ ] Static Pages
- [ ] 404

### Features

- [ ] Header
- [ ] Desktop menu
- [ ] Mobile menu
- [ ] Search
- [ ] Gallery
- [ ] Variants
- [ ] Filters
- [ ] Sorting
- [ ] Pagination
- [ ] Cart
- [ ] Checkout
- [ ] Authentication
- [ ] Account
- [ ] Customizer
- [ ] Plugins

### Technical

- [ ] Images
- [ ] Links
- [ ] Console
- [ ] Network
- [ ] Responsive
- [ ] Accessibility
- [ ] Security
- [ ] Cache
- [ ] Isolation
- [ ] Performance

## 58. PERFORMANCE

Measure and compare:

- [ ] page size
- [ ] request count
- [ ] HTML size
- [ ] CSS size
- [ ] JS size
- [ ] image size
- [ ] font size
- [ ] load behavior

Do not optimize by breaking design or functionality.

## 59. SEO / DISCOVERABILITY

Where applicable verify:

- [ ] page titles
- [ ] meta descriptions
- [ ] canonical URLs
- [ ] heading hierarchy
- [ ] alt text
- [ ] indexability
- [ ] product structured data if platform/plugin supplies it
- [ ] sitemap compatibility
- [ ] robots behavior

Do not duplicate platform SEO logic unnecessarily.

## 60. ANALYTICS / CONSENT

- [ ] only approved tracking
- [ ] no source-template analytics
- [ ] no old-client tracking
- [ ] no secrets in tracking
- [ ] consent behavior where applicable

## 61. BROWSER COMPATIBILITY

Where relevant:

- [ ] Chromium
- [ ] Firefox
- [ ] WebKit/Safari

Test:

- [ ] navigation
- [ ] forms
- [ ] gallery
- [ ] variants
- [ ] cart
- [ ] checkout
- [ ] responsive
- [ ] animations

## 62. HOSTING-SPECIFIC QA

For the actual production host verify:

- [ ] PHP version
- [ ] filesystem paths
- [ ] case-sensitive paths
- [ ] require/include paths
- [ ] permissions
- [ ] extensions
- [ ] memory limits
- [ ] upload limits
- [ ] HTTPS
- [ ] rewrite rules
- [ ] cron if required
- [ ] caching
- [ ] object cache
- [ ] page cache
- [ ] CDN if used

## 63. FINAL CORE INTEGRITY

Run:

- [ ] Git diff
- [ ] protected-path diff
- [ ] plugin comparison
- [ ] route comparison
- [ ] client-pack comparison

Expected:

```text
CLIENT PACK → changed
ALLOWED BRIDGE → changed where required
GOLDEN CORE → unchanged
```

## 64. FINAL DOCUMENTATION

Create/update:

- [ ] `SOURCE-INVENTORY.json`
- [ ] `ROUTE-MAP.md`
- [ ] `DYNAMIC-SLOT-CONTRACT.md`
- [ ] `FEATURE-CAPABILITY-MATRIX.md`
- [ ] `AUREON-CONNECTION-CONTRACT.md`
- [ ] `CLEANUP-REPORT.md`
- [ ] `PLUGIN-COMPATIBILITY-REPORT.md`
- [ ] `IMAGE-ASSET-REPORT.md`
- [ ] `SECURITY-REPORT.md`
- [ ] `FEATURE-LOSS-REPORT.md`
- [ ] `FINAL-ACCEPTANCE-MATRIX.json`
- [ ] `FINAL-CLIENT-DELIVERY-REPORT.md`

## 65. ACCEPTANCE MATRIX

Create:

`test-results/CLIENT-FINAL-ACCEPTANCE-MATRIX.json`

Each test should contain:

```text
id
category
route
setup
expected
actual
status
evidence
```

Allowed statuses:

- PASS
- FAIL
- OPTIONAL
- NOT_APPLICABLE
- BRIDGE_REQUIRED

## 66. CLIENT HANDOFF

Before delivery verify:

- [ ] client branding configured
- [ ] client logo configured
- [ ] client favicon configured
- [ ] client site title/heading configured
- [ ] client hero/banner configured
- [ ] client colors configured
- [ ] client typography configured
- [ ] client menus configured
- [ ] client products loaded
- [ ] client categories loaded
- [ ] payment configured
- [ ] shipping configured
- [ ] tax configuration checked where required
- [ ] account workflow verified
- [ ] cart verified
- [ ] checkout verified
- [ ] order verified
- [ ] forms verified
- [ ] plugin configuration checked
- [ ] security checked
- [ ] cache checked
- [ ] responsive checked
- [ ] accessibility checked
- [ ] final production backup created
- [ ] rollback procedure documented

## 67. CLIENT HANDOFF DOCUMENT

Provide:

- [ ] what changed
- [ ] supported features
- [ ] installed plugins
- [ ] Customizer-controlled settings
- [ ] WordPress-controlled settings
- [ ] WooCommerce-controlled settings
- [ ] client-pack-controlled settings
- [ ] known limitations
- [ ] admin instructions
- [ ] maintenance notes
- [ ] recovery notes

## 68. FINAL ACCEPTANCE

Return:

```text
CLIENT_THEME_READY_PASS
```

ONLY if all required architecture, frontend, WordPress, WooCommerce, authentication, Customizer, menu, search, plugin, security, route, asset, responsive, accessibility, cache, isolation, regression, rollback, and release gates pass.

Otherwise:

```text
CLIENT_THEME_READY_BLOCKED
```

Include:

```text
Problem:
Affected file:
Layer:
Root cause:
Evidence:
Safe fix:
Targeted test:
Regression required:
Release impact:
```

# 69. UNIVERSAL EDIT LOOP

```text
CLIENT REQUEST
      ↓
SCOPE LOCK
      ↓
BASELINE + ROLLBACK
      ↓
READ FRONTEND
      ↓
READ AUREON CONTRACTS
      ↓
IMPACT / BLAST-RADIUS MAP
      ↓
CLASSIFY CLIENT / BRIDGE / CORE
      ↓
MINIMUM SAFE CHANGE
      ↓
STATIC VALIDATION
      ↓
TARGETED TEST
      ↓
DEPENDENCY REGRESSION
      ↓
VISUAL REGRESSION
      ↓
PLATFORM REGRESSION
      ↓
SECURITY CHECK
      ↓
CORE INTEGRITY
      ↓
PRODUCTION PACKAGE AUDIT
      ↓
FINAL REPORT
      ↓
CLIENT_THEME_READY_PASS
```

# 70. REQUIRED OPENCODE INSTRUCTION

```text
Read this checklist first.

Then inspect the actual project.

Do not assume prior implementations.
Do not create a new frontend unless explicitly requested.
Do not redesign the client frontend unless explicitly requested.
Do not modify Golden AUREON without proving a generic Core defect.

Analyze first.
Classify every issue:
CLIENT PACK / BRIDGE / CORE.

Make the minimum safe change.
Run static validation.
Run targeted regression.
Run dependency regression.
Run visual regression for UI changes.
Run full regression when risk requires it.

Verify WordPress, WooCommerce, Customizer, Menus, Search, Authentication,
Account, Cart, Checkout, Plugins, Security, Routing, Assets, Responsive,
Accessibility, Console, Network, Cache and Isolation.

Generate the acceptance matrix and final report.

Never declare PASS without evidence.

If a required feature fails:
return CLIENT_THEME_READY_BLOCKED.

If everything passes:
return CLIENT_THEME_READY_PASS.
```

# 71. END STATE

```text
PREMIUM FRONTEND
        +
WORDPRESS
        +
WOOCOMMERCE
        +
CUSTOMIZER
        +
MENUS
        +
SEARCH
        +
AUTH
        +
ACCOUNT
        +
CART
        +
CHECKOUT
        +
PLUGINS
        +
SECURITY
        +
RESPONSIVE
        +
ACCESSIBILITY
        +
ASSETS
        +
ROUTING
        +
CACHE
        +
ISOLATION
        +
REGRESSION
        =
CLIENT THEME READY
```
