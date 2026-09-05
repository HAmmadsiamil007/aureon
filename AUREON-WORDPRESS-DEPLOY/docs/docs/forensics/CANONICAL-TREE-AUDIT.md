# AUREON — CANONICAL TREE AUDIT
## AUDIT DATE: 2026-09-04
## AUDITOR: Forensic Pass 1

## Executive Summary
This document provides an overview of the AUREON WordPress/WooCommerce architecture and directory structure. The project consists of a core plugin (`aureon/`), a deployment theme (`theme/aureon/`), and specific frontend design packs (e.g. `vineta/`).

## Directory Tree

| PATH | PURPOSE | SOURCE/GENERATED | EDITABLE | RUNTIME | DEPENDS ON | DEPENDED ON BY |
|------|---------|-----------------|----------|---------|------------|----------------|
| `aureon/` | Core plugin/source of truth | SOURCE | PROTECTED | NO | WP/WC | `theme/aureon/` |
| `theme/aureon/` | Deployed WP theme | GENERATED | YES | YES | `aureon/` | WP Runtime |
| `aureon/frontend/designs/vineta/` | Client Pack | SOURCE | YES | YES | `aureon/` | Adapters |
| `aureon/frontend/adapters/` | Bridge Layer | SOURCE | PROTECTED | YES | WC | Views |
| `AUREON-GOLDEN-COPY/` | Unmodified backup | BACKUP | NO | NO | None | Restore processes |
| `AUREON-WORDPRESS-DEPLOY/` | Deployment staging | GENERATED | NO | NO | Source | Live site |

## Key Relationships
### Source vs. Deploy
`aureon/` contains the canonical plugin source code. `theme/aureon/` is the deployed theme used at runtime by WordPress. `AUREON-WORDPRESS-DEPLOY/` appears to be a staging or deployment artifact directory.

### Golden Copy
`AUREON-GOLDEN-COPY/` is a backup directory containing unmodified reference files.

### Client Pack
`aureon/frontend/designs/vineta/` contains the active frontend design templates, CSS, JS, and manifesting defining the "Vineta" complete-page design.

### Bridge Layer
`aureon/frontend/adapters/` maps WooCommerce and WordPress backend data to the frontend variables expected by the Client Pack.

## Discrepancies Found
- **CRITICAL**: `aureon/ferm-page.php` vs `theme/aureon/ferm-page.php`. Although the actual filesystem check revealed both files are currently 34987 bytes, earlier reports indicate a file size discrepancy (34987 vs 25062 bytes).

## Risk Assessment
- `aureon/`: PROTECTED core. Changes here carry high risk.
- `theme/aureon/`: GENERATED/RUNTIME. Can be overwritten during deployment.
- `aureon/frontend/designs/`: EDITABLE. Safe for frontend adjustments.
