# AUREON-GOLDEN-COPY — Complete Current State

## Purpose
Development/reference copy of the full AUREON platform with all memories, reports, test results, and documentation. This is the working copy for analysis, auditing, and development.

## Location
`C:\Users\hamma\Downloads\phantom\wordpress\AUREON-GOLDEN-COPY\`

## Version
v6.1.0 (same as DEPLOY) | Status: Development + Audit Complete

## Directory Structure

```
AUREON-GOLDEN-COPY/
├── .serena/                          ← Serena project config + memories
│   ├── project.yml / project.local.yml
│   └── memories/                     ← All project memories
│       ├── aureon-rebrand/           ← Rebrand audit/execution memories (15 files)
│       ├── ferm-living/              ← Ferm Living template status
│       ├── ferm-premium-frontend/    ← Ferm frontend analysis (6 files)
│       ├── frontend-platform/        ← Frontend platform milestones (3 files)
│       ├── gp-audit/                 ← Golden Platform audit (16 files)
│       ├── project/                  ← Project state memories (16 files)
│       └── vineta/                   ← Vineta integration memories (2 files)
├── assets/
│   └── banner.jpeg                   ← Project banner
├── aureon/                           ← Theme (reference copy)
│   ├── ferm-page.php                 ← Ferm Living page template
│   └── plugin/                       ← Plugin reference (full aureon-studio structure)
│       ├── aureon-studio.php
│       ├── backgrounds/ blog/ colors/ copyright/ disable-elements/
│       ├── dist/ elements/ font-library/ general/ hooks/ inc/
│       ├── langs/ library/ menu-plus/ page-header/ secondary-nav/
│       ├── woocommerce/ readme.txt license.txt
│       └── (full plugin tree)
├── aureon-studio/                    ← Plugin (reference copy, same as aureon/plugin/)
├── docs/                             ← Documentation
│   ├── architecture/
│   │   └── UNIVERSAL-FRONTEND-EDIT-AND-REGRESSION-PLAN.md  ← MASTER PLAN v1.1
│   ├── forensics/                    ← Forensic audit reports
│   ├── reports/                      ← Architecture reports
│   ├── superpowers/                  ← Advanced plans
│   └── ChatGPT-Generate OpenCode Prompt-20260901-1133.md
├── frontend/                         ← Frontend Engine (reference copy)
│   ├── views/                        ← Engine core (loader.php, design.php, renderer.php, etc.)
│   ├── adapters/                     ← Platform adapters
│   ├── components/                   ← UI components
│   ├── sections/                     ← Section templates (25+ sections)
│   ├── tokens/                       ← Design tokens
│   ├── manifest/                     ← Route manifests
│   ├── designs/
│   │   ├── fermliving/               ← Ferm Living design pack
│   │   └── lumen/                    ← Lumen design pack
│   ├── assets/                       ← Shared frontend assets (JS, images)
│   ├── boost-products.php            ← Product boost utility
│   ├── cleanup-demo.php              ← Demo cleanup utility
│   └── *.md                          ← Architecture docs (12 files)
├── mu-plugins/                       ← Must-use plugins
├── reports/                          ← 38 forensic audit reports (00-38)
├── test-results/                     ← Visual regression screenshots + audit JSON
│   ├── *.png                         ← Screenshots (homepage, shop, cart, checkout, etc.)
│   ├── transition/                   ← Transition test results
│   ├── audit-results.json            ← Audit data
│   ├── audit-v2-results.json         ← V2 audit data
│   └── FULL-AUDIT-REPORT.md          ← Complete audit report
├── theme/                            ← Theme reference (aureon/)
│   └── aureon/                       ← Full theme tree
├── HOW-TO-INSTALL.txt                ← Installation guide
├── README.md                         ← Project readme
├── full-audit.js / full-audit-v2.js  ← Audit scripts
└── transition-test.js                ← Transition test script
```

## Key Documentation (38 Reports)

```
00-CORE-REPORT-INDEX.md              Report index
01-CORE-EXECUTIVE-SUMMARY.md         Executive summary
02-CORE-ARCHITECTURE.md              Architecture overview
03-CORE-DIRECTORY-AND-FILE-MAP.md    File mapping
04-CORE-REQUEST-LIFECYCLE.md         Request lifecycle
05-DESIGN-ENGINE.md                  Design engine docs
06-COMPLETE-PAGE-ARCHITECTURE.md     Complete-page architecture
07-COMPONENT-MODE-ARCHITECTURE.md    Component mode
08-DESIGN-RESOLVER-AND-MANIFEST.md   Design resolver
09-ASSET-LOADING-ARCHITECTURE.md     Asset loading
10-TEMPLATE-AND-ROUTING-ARCHITECTURE.md  Template routing
11-VIEWMODEL-ADAPTER-ARCHITECTURE.md    ViewModel adapters
12-WOOCOMMERCE-ARCHITECTURE.md       WooCommerce integration
13-PRODUCT-AND-VARIATION-CONTRACT.md Product/variation contracts
14-CART-CHECKOUT-ACCOUNT-CONTRACT.md Cart/checkout/account contracts
15-MENU-ARCHITECTURE.md              Menu system
16-SEARCH-ARCHITECTURE.md            Search system
17-CUSTOMIZER-ARCHITECTURE.md        Customizer
18-CUSTOMIZER-SETTING-CATALOG.md     Customizer settings catalog
19-DEMO-CONTENT-AND-FALLBACK.md      Demo content
20-DATA-AND-FERMPAGEDATA-CONTRACT.md FermPageData contract
21-BRIDGE-API-CONTRACT.md            Bridge API contracts
22-AJAX-REST-AND-ENDPOINTS.md        AJAX/REST endpoints
23-SECURITY-ARCHITECTURE.md          Security
24-MEDIA-AND-ASSET-CONTRACT.md       Media/assets
25-PERFORMANCE-ARCHITECTURE.md       Performance
26-CLIENT-PACK-ARCHITECTURE.md       Client pack architecture
27-ACTIVE-PACK-ISOLATION.md          Active pack isolation
28-CORE-EXTENSION-CONTRACT.md        Core extension contracts
29-HOOK-FILTER-ACTION-CATALOG.md     Hook/filter/action catalog
30-DEPENDENCY-MATRIX.md              Dependency matrix
31-FEATURE-CATALOG.md                Feature catalog
32-CLIENT-INTEGRATION-REQUIREMENTS.md Client integration requirements
33-FRONTEND-COMPATIBILITY-CHECKLIST.md Frontend compatibility
34-DO-NOT-BREAK-RULES.md            Do-not-break rules
35-CORE-RISKS-AND-BOUNDARIES.md     Core risks/boundaries
36-RUNTIME-VERIFICATION-MATRIX.md    Runtime verification
37-GOLDEN-CORE-REFERENCE.md         Golden Core reference
38-DEMO-REFERENCE-SYSTEM-CONTRACT.md Demo reference system
```

## Frontend Architecture Reports

```
FRONTEND_ARCHITECTURE_REPORT.md      Frontend architecture
MASTER_FRONTEND_IMPLEMENTATION_PLAN.md  Master implementation plan
SECTION_LIBRARY.md                   Section library catalog
TEMPLATE_MAPPING.md                  Template mapping
TOKEN_MIGRATION_REPORT.md            Token migration
COMPONENT_INVENTORY.md               Component inventory
ADAPTER_SPECIFICATION.md             Adapter specification
ANIMATION_INTEGRATION_REPORT.md      Animation integration
VISUAL_REGRESSION_PLAN.md            Visual regression plan
WOO_INTEGRATION_REPORT.md            WooCommerce integration report
```

## Test Results

```
Screenshots: homepage.png, shop.png, cart.png, checkout.png, search.png,
             about.png, blog.png, contact.png, 404-page.png, account-login.png,
             product--*.png, collection--*.png, store-locator.png
Audit JSON:  audit-results.json, audit-v2-results.json
Full Report: FULL-AUDIT-REPORT.md
```

## Key Memories Index

### aureon-rebrand/ (15 files)
- audit-results, current-snapshot, execution-checklist, final-audit, final-state
- final-verification, frontend-v2-roadmap, git-history, lead-architect-review
- phase17-frontend-framework, phase17-stage12-hardening, phase17-stage2-shell-done
- plan-overview, state

### ferm-premium-frontend/ (6 files)
- current-state-audit, decisions-and-questions, frozen-source-analysis
- plan-overview, resume-checklist, shell-header-analysis

### project/ (16 files)
- aether-pro-integration-plan, aether-wc-checkout-integration
- aureon-complete-record, aureon-docker-deployment, aureon-infinityfree-deploy
- ferm-complete-theme-state-2026-08-31, ferm-pack-replacement-status
- ferm-product-routing-fix-2026-08-31, ferm-theme-change-summary-2026-08-31
- final-state, frontend-complete-status, frontend-dynamic-conversion-baseline
- license-removal, site-library-removal, verification-master-task-status

### gp-audit/ (16 files)
- architecture-blueprint, engineering-review, enterprise-forensic-verification
- phase1-package through phase12-verdict, state

## Engine Kernel

- Repo-root `frontend/views/*` loaded via `inc/frontend.php:16` (`/../../frontend/views/loader.php`)
- Pack-first shadow via `aether_resolve_design_path()` (design.php:80)
- `luxury` = virtual slug (no dir)
- Design packs emit FROZEN DOM verbatim; CSS/JS byte-copied
- Cart shims (/cart.js, add/change/update .js → Woo cart, Shopify-shaped JSON) so app.js stays untouched
- Bridge.js ≤150 lines for cart-count sync + wishlist
- ~10 family templates cover all crawled pages

## Docker Environment

- Port 8080: WordPress
- Port 8081: phpMyAdmin
- Port 3306: MySQL
- Theme/frontend/plugin mounted from host
- Docker: WordPress + MySQL + phpMyAdmin
