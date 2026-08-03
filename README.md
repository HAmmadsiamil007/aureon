# wordpress

GeneratePress 3.6.1 + GP Premium 2.5.6 + **Phantom Core** (child-theme frontend framework).

## Contents

| Path | What |
|---|---|
| `generatepress.3.6.1/` | GeneratePress theme 3.6.1 — **untouched** (byte-verified vs official release) |
| `gp-premium_v2.5.6/` | GP Premium plugin 2.5.6 — **untouched** (audited: genuine, no tampering) |
| `wp-content/themes/phantom/` | **Phantom** — token-driven frontend framework as a GeneratePress child theme (Phases 0–17 implementation plan) |
| `Report/` | Complete forensic audit, engineering review, architecture blueprint, implementation plan, roadmap |
| `.serena/` | Project memory (AI-assisted development records) |

## Phantom Core status

**Phase 0 — Project Foundation: `Completed`** · verification report:
`wp-content/themes/phantom/docs/PHASE_0_VERIFICATION_REPORT.md` (**APPROVED FOR PHASE 1**).

Source of truth:
- `Report/MASTER_ROADMAP.md` — phase tracker + ADR table
- `Report/PHASE_5_PHANTOM_CORE_IMPLEMENTATION_PLAN.md` — engineering spec (18 phases)
- `Report/master_architecture_blueprint.md` — architecture blueprint

## Audit summary

- GeneratePress 3.6.1: **authentic** — 144/144 files SHA-256 match the official WordPress.org release.
- GP Premium 2.5.6: **genuine** — clean license flow, all CVEs patched, 0% nulled indicators.
- Composite verdict: **PASS (8.6/10)** — safe as the backend/core for a fully custom frontend.

## License notes

- GeneratePress: GPL-2.0+ · Phantom Core: GPL-2.0-or-later (child theme).
- GP Premium is a **commercial** product (EDGE22 Studios) with its own license — included here for development use by the license holder only.
