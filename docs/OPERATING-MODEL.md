# GOLDEN AUREON — OPERATING MODEL

**Version:** 1.0
**Date:** 2026-08-31
**Status:** RELEASE READY

---

## Executive Summary

Golden AUREON is a **reusable multi-client premium frontend platform**. The Golden Core is immutable infrastructure. Client frontend work happens inside a controlled client-pack boundary.

---

## Architecture

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

## Core Rules

```
GOLDEN CORE = PLATFORM
CLIENT PACK = PRESENTATION
BRIDGE = CONNECTOR

Never solve a client presentation problem by modifying the Golden Core.

Never solve a business/data problem by rebuilding the client frontend.

Never convert a complete HTML/CSS/JS frontend into AUREON sections/components
unless the project explicitly chooses Component Mode.

Never modify a known-good feature merely because the new frontend doesn't
understand it. Build the smallest bridge necessary.

Golden AUREON is a protected release baseline.
```

---

## Three Workflows

### A. Template Creation

```
CLIENT SOURCE
    ↓
MASTER PROMPT 1
    ↓
CLIENT TEMPLATE-READY
```

**When to use:** You receive a new premium frontend.

**What it does:** Prepares a completely independent premium frontend so it can later be connected to Golden AUREON with minimal integration work.

**Key rules:**
- Does NOT replace the active client
- Does NOT modify Golden AUREON
- Preserves complete HTML/CSS/JS
- Creates template-ready copy with manifest

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

**What it does:** Replaces the current client frontend with a NEW COMPLETE frontend while preserving ALL Golden AUREON platform capabilities.

**Key rules:**
- Archives current client first
- Proves compatibility before activation
- Full regression after replacement
- Rollback capability preserved

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

**What it does:** Modifies the currently active client frontend without breaking Golden AUREON, WordPress, WooCommerce, Customizer, menus, search, account, cart, checkout, security, or routing.

**Key rules:**
- Edits the client pack, NOT the Golden Core
- Preserves platform contract
- Regression testing required
- Major edits use separate branch

---

## Decision Matrix

### "Change the logo / hero / color / text"

```
USE: MASTER PROMPT 3 (EDIT)
```

### "Change the product page / homepage / header design significantly"

```
USE: MASTER PROMPT 3 (MAJOR EDIT)
```

### "I have a completely new premium HTML/CSS/JS website"

```
USE: MASTER PROMPT 1 (TEMPLATE CREATION)
    → then MASTER PROMPT 2 (REPLACEMENT)
```

### "I want a totally different frontend"

```
USE: MASTER PROMPT 1 (NEW TEMPLATE)
    → complete audit
    → ready
    → MASTER PROMPT 2 (REPLACEMENT)
```

---

## Phase Results

```
Phase 1  Account              ✅ 59/59
Phase 2  Cart/Checkout        ✅ 31/31
Phase 3  Menus               ✅ 26/27* (headless hover limitation)
Phase 4  Search              ✅ 26/26
Phase 5  Demo Content        ✅ 9/9
Phase 6  Customizer          ✅ 39/39
Phase 7  Active-Pack Loading ✅ 15/15
Phase 8  Core Cleanup        ✅ 13/13
Phase 9  Full Regression     ✅ 22/22
Phase 10 Client Isolation    ✅ 18/18
Phase 11 Final Acceptance    ✅ 23/23

TOTAL:                        ✅ 281/282 (99.6%)
```

---

## Key Files

| File | Purpose |
|------|---------|
| `aureon/ferm-page.php` | Complete-page template host |
| `aureon/frontend/views/design.php` | Design resolver |
| `aureon/frontend/views/assets.php` | Asset pipeline |
| `aureon/frontend/views/composer.php` | Component composer |
| `aureon/frontend/designs/fermliving/` | Ferm Living client pack |
| `aureon/frontend/designs/testclient/` | Test client pack (isolation verification) |
| `theme/aureon/inc/frontend.php` | Theme integration |

---

## Git Tags

```
v1.0.0-golden-aureon-release — Final 100/100 acceptance
```

---

## What This Means

Future client replacement becomes:

```
Install pack → Activate → Complete page works → Real data works → Done
```

rather than rebuilding the core.

**The Golden AUREON platform is ready for release as a reusable multi-client premium frontend platform.**
