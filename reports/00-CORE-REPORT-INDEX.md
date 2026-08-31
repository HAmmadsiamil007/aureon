# 00 — CORE REPORT INDEX

## Golden AUREON Core Forensic Documentation

**Release:** v1.0.0-golden-aureon-release (commit 402a29e)
**Authoritative Core:** `aureon/`
**Documentation Date:** 2026-08-31

---

## Architecture Overview

```
                         GOLDEN AUREON CORE
                                │
              ┌─────────────────┼─────────────────┐
              │                 │                 │
          PLATFORM          DATA/BUSINESS      EXTENSIBILITY
              │                 │                 │
        WordPress          WooCommerce         Client Packs
        Routing            Products            Complete Pages
        Customizer         Variations          Component Mode
        Menus              Cart
        Search             Checkout
        Account            Authentication
        Security           Orders
        Media
              │                 │                 │
              └─────────────────┼─────────────────┘
                                ↓
                        THIN CLIENT BRIDGE
                                ↓
                     ACTIVE CLIENT FRONTEND
```

**Core = Platform | Client = Presentation | Bridge = Connector**

---

## Report List

| # | Report | Purpose |
|---|--------|---------|
| 00 | [Index](00-CORE-REPORT-INDEX.md) | Master navigation |
| 01 | [Executive Summary](01-CORE-EXECUTIVE-SUMMARY.md) | What Golden AUREON is |
| 02 | [Architecture](02-CORE-ARCHITECTURE.md) | Complete architectural layers |
| 03 | [Directory Map](03-CORE-DIRECTORY-AND-FILE-MAP.md) | File inventory |
| 04 | [Request Lifecycle](04-CORE-REQUEST-LIFECYCLE.md) | HTTP → browser flow |
| 05 | [Design Engine](05-DESIGN-ENGINE.md) | Design resolution system |
| 06 | [Complete-Page Architecture](06-COMPLETE-PAGE-ARCHITECTURE.md) | complete_page=true |
| 07 | [Component Mode](07-COMPONENT-MODE-ARCHITECTURE.md) | complete_page=false |
| 08 | [Design Resolver & Manifest](08-DESIGN-RESOLVER-AND-MANIFEST.md) | Manifest system |
| 09 | [Asset Loading](09-ASSET-LOADING-ARCHITECTURE.md) | CSS/JS/font pipeline |
| 10 | [Template & Routing](10-TEMPLATE-AND-ROUTING-ARCHITECTURE.md) | Route → template map |
| 11 | [ViewModel/Adapter](11-VIEWMODEL-ADAPTER-ARCHITECTURE.md) | Data transformation |
| 12 | [WooCommerce](12-WOOCOMMERCE-ARCHITECTURE.md) | All WC integrations |
| 13 | [Product & Variation](13-PRODUCT-AND-VARIATION-CONTRACT.md) | Product data schema |
| 14 | [Cart/Checkout/Account](14-CART-CHECKOUT-ACCOUNT-CONTRACT.md) | Commerce contracts |
| 15 | [Menu Architecture](15-MENU-ARCHITECTURE.md) | Navigation system |
| 16 | [Search Architecture](16-SEARCH-ARCHITECTURE.md) | Search system |
| 17 | [Customizer Architecture](17-CUSTOMIZER-ARCHITECTURE.md) | Customizer system |
| 18 | [Customizer Setting Catalog](18-CUSTOMIZER-SETTING-CATALOG.md) | Every setting |
| 19 | [Demo Content & Fallback](19-DEMO-CONTENT-AND-FALLBACK.md) | Demo system |
| 20 | [FermPageData Contract](20-DATA-AND-FERMPAGEDATA-CONTRACT.md) | Client data model |
| 21 | [Bridge API Contract](21-BRIDGE-API-CONTRACT.md) | Bridge boundary |
| 22 | [AJAX/REST/Endpoints](22-AJAX-REST-AND-ENDPOINTS.md) | All endpoints |
| 23 | [Security Architecture](23-SECURITY-ARCHITECTURE.md) | Security system |
| 24 | [Media & Asset Contract](24-MEDIA-AND-ASSET-CONTRACT.md) | Media handling |
| 25 | [Performance Architecture](25-PERFORMANCE-ARCHITECTURE.md) | Performance system |
| 26 | [Client Pack Architecture](26-CLIENT-PACK-ARCHITECTURE.md) | Pack structure |
| 27 | [Active-Pack Isolation](27-ACTIVE-PACK-ISOLATION.md) | Isolation proof |
| 28 | [Core Extension Contract](28-CORE-EXTENSION-CONTRACT.md) | Safe extension |
| 29 | [Hook/Filter/Action Catalog](29-HOOK-FILTER-ACTION-CATALOG.md) | All hooks |
| 30 | [Dependency Matrix](30-DEPENDENCY-MATRIX.md) | File dependencies |
| 31 | [Feature Catalog](31-FEATURE-CATALOG.md) | All features |
| 32 | [Client Integration Requirements](32-CLIENT-INTEGRATION-REQUIREMENTS.md) | Integration contract |
| 33 | [Frontend Compatibility Checklist](33-FRONTEND-COMPATIBILITY-CHECKLIST.md) | Pre-flight checklist |
| 34 | [Do-Not-Break Rules](34-DO-NOT-BREAK-RULES.md) | Engineering rules |
| 35 | [Core Risks & Boundaries](35-CORE-RISKS-AND-BOUNDARIES.md) | Risk register |
| 36 | [Runtime Verification Matrix](36-RUNTIME-VERIFICATION-MATRIX.md) | Test results |
| 37 | [Golden Core Reference](37-GOLDEN-CORE-REFERENCE.md) | Quick reference |

---

## Reading Order

**New Developer:**
01 → 02 → 03 → 04 → 05 → 10 → 11 → 12 → 17 → 31 → 37

**CLI/AI Agent:**
02 → 03 → 04 → 05 → 08 → 09 → 10 → 11 → 20 → 21 → 22 → 28 → 32 → 34

**WooCommerce Developer:**
12 → 13 → 14 → 21 → 22 → 23

**Customizer Developer:**
17 → 18 → 20 → 21 → 28

**Client/Technical Stakeholder:**
01 → 12 → 15 → 16 → 17 → 19 → 26 → 31

---

## Authoritative Paths

| Component | Path |
|-----------|------|
| Golden Core | `aureon/` |
| Frontend Engine | `aureon/frontend/` |
| Theme | `aureon/theme/` |
| Plugin | `aureon/plugin/` |
| Client Packs | `aureon/frontend/designs/` |
| Deployment | `theme/` (gitignored) |
| Docs | `docs/` |
| Reports | `reports/` |
