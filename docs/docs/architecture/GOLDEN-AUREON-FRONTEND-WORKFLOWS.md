# GOLDEN AUREON — FRONTEND WORKFLOWS

**Version:** 1.0
**Date:** 2026-08-31
**Status:** PERMANENT REFERENCE

---

## Purpose

This is the **master index** for managing premium client frontends on the Golden AUREON platform.

When a client arrives, open this document and follow the decision tree to determine which workflow to use.

---

## Golden Architecture

```
                    GOLDEN AUREON
                   v1.0 RELEASE BASE
                          │
             ┌────────────┼────────────┐
             │            │            │
          Client A     Client B     Client C
          complete     complete     complete
          frontend     frontend     frontend
             │            │            │
             └────────────┼────────────┘
                          ↓
                  thin integration
                          ↓
              WordPress / WooCommerce
                          ↓
              Customizer / Menus / Search
              Account / Cart / Checkout
```

---

## Responsibility Boundaries

### Golden Core (PLATFORM)

```
WordPress
WooCommerce
products
variations
pricing
stock
cart
checkout
account/authentication
search
menus
Customizer
security
routing
settings
platform data
```

### Client Frontend (PRESENTATION)

```
complete HTML
CSS
JavaScript
animations
responsive presentation
visual hierarchy
client-specific presentation behavior
```

### Bridge (CONNECTOR)

```
data mapping
runtime configuration
URLs
client-state translation
business-action translation
```

### Demo Reference System (FALLBACK CONTENT)

```
curated demo manifests
approved source URLs
demo products
demo categories
default assets
fallback content
```

**Purpose:** Make a newly installed client website beautiful before the client has entered their own content.

**Rules:**
- Ferm Living is DEMO ONLY, not production content
- Demo content disappears globally when ANY real content exists
- Demo records are NEVER automatically destroyed
- No automatic scraping — manual URL updates only
- Demo products MUST NOT be purchasable

---

## Golden Core Protection Rules

```
GOLDEN CORE = PLATFORM
CLIENT PACK = PRESENTATION
BRIDGE = CONNECTOR
DEMO SYSTEM = FALLBACK CONTENT PROVIDER

Never solve a client presentation problem by modifying the Golden Core.

Never solve a business/data problem by rebuilding the client frontend.

Never convert a complete HTML/CSS/JS frontend into AUREON sections/components
unless the project explicitly chooses Component Mode.

Never modify a known-good feature merely because the new frontend doesn't
understand it. Build the smallest bridge necessary.

Golden AUREON is a protected release baseline.
```

---

## Two Frontend Modes

### Complete-Page Mode (`complete_page: true`)

- Client provides complete HTML/CSS/JS
- Frozen HTML served directly via `ferm-page.php`
- Platform assets suppressed
- Client presentation assets loaded only
- Thin bridge connects data

### Component Mode (`complete_page: false`)

- Client provides component overrides
- AETHER shell components used
- Platform CDNs loaded
- Client CSS/JS overlay platform
- Adapters supply data

---

## The Three Approved Frontend Workflows

### A. Template Creation

```
CLIENT SOURCE
    ↓
MASTER PROMPT 1
    ↓
CLIENT TEMPLATE-READY
```

**When to use:** You receive a new premium frontend.

**Document:** [NEW-CLIENT-TEMPLATE-CREATION-PLAN.md](NEW-CLIENT-TEMPLATE-CREATION-PLAN.md)

---

### B. Frontend Replacement

```
CURRENT CLIENT
    ↓
ARCHIVE
    ↓
MASTER PROMPT 2
    ↓
NEW CLIENT ACTIVE
```

**When to use:** Client says "Remove the current frontend and use this new one."

**Document:** [FRONTEND-REPLACEMENT-PLAN.md](FRONTEND-REPLACEMENT-PLAN.md)

---

### C. Frontend Edit

```
ACTIVE CLIENT
    ↓
MASTER PROMPT 3
    ↓
CLIENT REVISION
```

**When to use:** Client says "Change the hero" or "Change the product page design."

**Document:** [FRONTEND-EDIT-PLAN.md](FRONTEND-EDIT-PLAN.md)

---

## Decision Tree

```
New frontend from external source?
    → TEMPLATE CREATION (Plan 1)

Existing client replaced completely?
    → REPLACEMENT (Plan 2)

Existing client visually/content edited?
    → EDIT (Plan 3)

Completely different frontend?
    → TEMPLATE CREATION (Plan 1)
    → then REPLACEMENT (Plan 2)
```

---

## Shared Non-Negotiable Rules

1. Golden Core is protected
2. Client complete frontend remains presentation source of truth
3. Thin bridge connects client and platform
4. No unnecessary section splitting
5. No unnecessary DOM reconstruction
6. WooCommerce remains business source of truth
7. Customizer remains platform configuration source
8. Only active client pack loads presentation assets
9. Demo content is non-destructive
10. Existing proven features remain regression references
11. Demo products MUST NOT be purchasable
12. Demo content disappears globally when ANY real content exists
13. Demo records are NEVER automatically destroyed
14. No automatic scraping — manual URL updates only
15. Client-scoped demo content (each client has its own demo manifest)

---

## Permanent Regression References

| Product | Type | Use |
|---------|------|-----|
| **#834** | Simple product | Permanent regression reference |
| **#828** | Variable product | Permanent regression reference |

---

## Required Platform Capabilities

These must not be lost during any frontend workflow:

| Capability | Source | Status |
|------------|--------|--------|
| WordPress | Core | ✅ |
| WooCommerce | Plugin | ✅ |
| Products | Woo | ✅ |
| Variations | Woo | ✅ |
| Cart | Woo | ✅ |
| Checkout | Woo | ✅ |
| Account | Woo | ✅ |
| Search | WP | ✅ |
| Menus | WP | ✅ |
| Customizer | WP | ✅ |
| Demo Content | AUREON | ✅ |
| Security | WP/Woo | ✅ |
| Routing | WP | ✅ |
| Active-Pack Loading | AUREON | ✅ |
| Client Isolation | AUREON | ✅ |

---

## Release/Rollback Philosophy

- Golden AUREON is a protected release baseline
- Client packs change around it
- Rollback capability must always be preserved
- Archive before replacement
- Never destroy rollback capability

---

## Document Links

| Document | Purpose |
|----------|---------|
| [NEW-CLIENT-TEMPLATE-CREATION-PLAN.md](NEW-CLIENT-TEMPLATE-CREATION-PLAN.md) | Prepare new client frontend |
| [FRONTEND-REPLACEMENT-PLAN.md](FRONTEND-REPLACEMENT-PLAN.md) | Replace current client |
| [FRONTEND-EDIT-PLAN.md](FRONTEND-EDIT-PLAN.md) | Edit existing client |
| [DEMO-REFERENCE-CONTENT-SYSTEM.md](DEMO-REFERENCE-CONTENT-SYSTEM.md) | Demo content architecture |

---

## Quick Reference

### "Change the logo / hero / color / text"

**USE:** FRONTEND-EDIT-PLAN.md

### "Change the product page / homepage / header design significantly"

**USE:** FRONTEND-EDIT-PLAN.md (Major Edit)

### "I have a completely new premium HTML/CSS/JS website"

**USE:** NEW-CLIENT-TEMPLATE-CREATION-PLAN.md → FRONTEND-REPLACEMENT-PLAN.md

### "I want a totally different frontend"

**USE:** NEW-CLIENT-TEMPLATE-CREATION-PLAN.md → FRONTEND-REPLACEMENT-PLAN.md
