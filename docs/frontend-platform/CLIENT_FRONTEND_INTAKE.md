# CLIENT FRONTEND INTAKE (M5)

> **Status:** LIVE · **Date:** 2026-08-14
> **Purpose:** the repeatable process for receiving an external HTML/CSS/JS frontend and turning it into an AETHER design pack — without touching the frozen core (see `MASTER_FRONTEND_REPLACEMENT_PLAN.md` §22).

## 1. Deliverables per client

1. `docs/frontend-platform/reports/CLIENT_FRONTEND_FORENSIC_REPORT_<client>.md` (template: `docs/frontend-platform/templates/CLIENT_FRONTEND_FORENSIC_REPORT.template.md`)
2. A design pack: `frontend/designs/<slug>/` (assets/, components/, sections/, tokens.php)
3. Branch `client/<slug>` off the latest release tag
4. Mapping docs: CUSTOMIZER_MAPPING / WOO_MAPPING / TOKEN_MAPPING per client (files 5–7 of §28)

## 2. Intake steps

**P0 · Intake interview** — goal, pages, WC scope, assets, dependencies, licensing, timeline.
**P1 · Forensic audit** — fill the report template from the delivered package (pages, shared components, layouts, nav, forms, product UI, cart/checkout/account, blog, animations, responsive, assets, fonts, icons, dependencies).
**P2 · Tier classification**

| Tier | Meaning | Action |
|---|---|---|
| A | Directly compatible | Map onto existing ids/tokens; enqueue pack |
| B | Adaptable | Component/token/JS adaptation within pack |
| C | Architectural mismatch | Reconstruction required — client-scoped plan + approval |
| D | Unsafe (licensing/security/perf) | **Reject** with documented reason |

**P3 · Component mapping** — each client UI unit → manifest id (reuse > extend > new).
**P4 · Data contract check** — every component's inputs must exist in `DATA_CONTRACT.md`. Missing data = adapter extension case (platform change, not pack hack).
**P5 · Token mapping** — client palette/typography/spacing → generic contract names (`--gold`, `--surface`, `--font-heading`, ...) via pack `tokens.php` defaults.
**P6 · Template mapping** — client pages → section ids; pack sections/ only for genuinely new sections.
**P7 · JS behavior mapping** — classify every client behavior KEEP / ADAPT / REPLACE / MOVE INTO AETHER / REMOVE (see §5).
**P8 · Build the pack** — assets, shadowed templates, pack sections, tokens.php, delegated JS.
**P9 · Regression** — pack gates (§6) + platform regression.
**P10 · Acceptance + release** — checklist signed, pack activated, branch merged per `CLIENT_BRANCH_STRATEGY.md`.

## 3. What never happens during intake

- No editing `frontend/views/*`, `adapters/*`, `manifest/components.php`, `tokens/tokens.php` (mechanism), `theme/inc/*`, Customizer fields
- No regex HTML replacement / str_replace / output-buffer surgery
- No copying static HTML into PHP templates
- No direct WP/WC calls in pack components (verify.sh gates run against `frontend/` including `designs/`)
- No client forks of WC templates; WC business logic stays WC-native

## 4. Pack anatomy

```
frontend/designs/<slug>/
├── tokens.php                 # option defaults (aureon_option_defaults, priority 20)
├── sections/                  # self-registering pack sections (aether_register_section)
├── components/                # shadowed component templates (same relative path wins)
└── assets/                    # css/, js/, images/, fonts/, icons/ (enqueued by bridge, M7)
```

Activation: `AETHER_DESIGN` constant or `aether_active_design` option = `<slug>`. Fallback: any missing file resolves to the base engine tree — a pack can be as thin as one overridden section.

## 5. JS behavior classification

| Class | Meaning | Where it lives |
|---|---|---|
| KEEP | platform contract (AJAX cart, cart-count, contact, newsletter, search, preloader, motion watchdog) | base JS, untouched |
| ADAPT | design-specific but delegatable (marquee, accordion, tabs, gallery, counters, sticky) | pack JS, delegated hooks |
| REPLACE | same role as a platform behavior but client implementation | pack JS shadows only its own hooks |
| MOVE INTO AETHER | useful to all clients → propose platform improvement (kernel/JS, gated) | platform tree |
| REMOVE | duplicated, conflicting, or dead | dropped at intake |

## 6. Gate checklist per pack (from plan §33)

- [ ] `verify.sh` PASSED (includes php -l + grep + hex gates over `frontend/` incl. pack)
- [ ] Pack routes 16/16 × desktop + mobile
- [ ] Pack live-gaps 6/6 (AJAX cart, qty stepper, contact, newsletter, no-JS, cart page)
- [ ] Pack a11y 14/14 ×2 (scroll-then-axe, 11 surfaces)
- [ ] Pack visual zero-delta vs committed baselines + fidelity comparison vs client reference
- [ ] Failure-injection 4/4
- [ ] Coexistence test: inactive pack CSS/JS has zero effect
- [ ] Hostile-input injection re-run
- [ ] Customizer round-trip spot check
- [ ] WC flow spot check (AJAX add-to-cart + no-JS + cart + checkout redirect)
- [ ] Performance: no new blocking requests, asset cap, same Lighthouse class
- [ ] Rollback verified (deactivate pack → base tree renders unchanged)