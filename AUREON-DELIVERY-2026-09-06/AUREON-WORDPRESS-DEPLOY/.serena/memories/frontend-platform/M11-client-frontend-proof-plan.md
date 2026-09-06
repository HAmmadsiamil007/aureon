# M11: Real Client Frontend Replacement Proof — Master Plan (EXECUTE TOMORROW)

## Status: 2026-08-15 — PLAN SAVED, NOT STARTED. Wait for explicit approval before Phase 0 implementation.

## Roadmap (committed in the M6-M10 final-state memory)
v1.2.1-audit → M1-M5 foundation ✅ → M6-M10 design-pack architecture ✅ → **M11 Real Client Frontend Proof (NEXT)** →
M12 Reusable Frontend Replacement Workflow → Commercial Client Production.

## Business model (the reason M11 comes before demo importers / multiple demo packs)
ONE AUREON CORE + ONE PREMIUM STARTER FRONTEND + CLIENT-SPECIFIC FRONTEND REPLACEMENT.
Per client: take existing premium HTML/CSS/JS frontend → analyze → preserve UI/UX design language →
convert to AETHER design pack → replace static data with WP/WC/plugin data → connect Customizer + Aureon Studio +
WC + supported plugins → preserve visual fidelity → do NOT break Aureon core → deliver.
Core promise to prove: "Change the frontend completely while keeping the engine underneath."
The first client frontend is the PROOF OF THE PLATFORM — no one-off hacks; the same pipeline must be reusable
for the next client. A hack needed for one design = architectural warning, not acceptance.

## Core principle (never reversed)
Frontend = PRESENTATION. Aureon/AETHER = DATA + CONTRACT + INTEGRATION ENGINE.
Correct chain: Aureon Core → Contracts → Data → Adapters → ViewModels → Frontend.
Forbidden in presentation components: WP_Query, get_posts(), get_option(), get_post_meta(), wc_get_product(s),
direct DB calls, plugin DB calls. Required data missing → extend the correct adapter/ViewModel, never query in frontend.

## Master prompt
Full 37-section M11 master prompt (audit → plan → staged implement with AUDIT/PLAN/IMPLEMENT/VERIFY/REGRESSION/
DOCUMENT/COMMIT per stage; core-freeze rule with LEVEL 1-5 classification where ONLY LEVEL 5
(actual core architecture defect) may justify core modification; CSS/JS isolation; Customizer round-trip;
WC/WP/Studio module matrix; 8 breakpoints; visual fidelity measured; axe; performance; failure injection;
core protection via hashes; design switch A→B→A + deactivate→base fallback; no core contamination scans;
FINAL_REPORT.md with 24 sections; acceptance checklist ~30 items; STOP conditions ~13 items; git strategy
M11-01..M11-15 small commits; Serena memory update) is with the user (chat message 2026-08-15).
Resume next session: open that prompt and execute PHASE 0 (read-only repository audit, build
docs/frontend-platform/M11_FORENSIC_BASELINE.md + docs/frontend-platform/M11_CLIENT_FRONTEND_PROOF_PLAN.md),
then STOP and wait for approval before implementation.

## Phase 0 deliverables (read-only, no code changes)
- docs/frontend-platform/M11_FORENSIC_BASELINE.md — A..J: current architecture, contracts, extension points,
  risks, limitations, MUST-NOT-CHANGE files, allowed files, proven-gap-only files, regression coverage, gaps.
- docs/frontend-platform/m11-client-proof/01-source-inventory.md .. 11-final-mapping.md — client intake analysis.
- docs/frontend-platform/M11_CLIENT_FRONTEND_PROOF_PLAN.md — architecture, mapping, component/section/data/
  Customizer/WC/plugin/CSS/JS/security/responsive/a11y/performance/testing/rollback/commit strategy,
  acceptance criteria, stop conditions, risk register.
- End of Phase 0: report "PHASE 0 COMPLETE — READY FOR M11 IMPLEMENTATION" + plan path + audit path + risks +
  expected core modifications (goal: ZERO) + expected design-pack modifications + recommended next step.
  Then WAIT FOR APPROVAL.

## M11 key rules to honor (from master prompt)
- Frozen: theme core, Studio module architecture, WP/WC integration, Customizer infra, adapters, core ViewModels,
  existing component/section contracts, security behavior, AJAX contracts, plugin bridges.
- Do NOT invent a parallel pack architecture — use existing design-pack resolver (frontend/designs/<slug>/).
- Client presentation code lives ONLY in frontend/designs/<slug>/ (incl. client-specific sections added INSIDE
  the pack, never in core section set).
- CSS: namespace under the design-pack scope; audit global selectors/resets/!important; never leak into
  wp-admin/WC/checkout/plugins. JS: lifecycle-safe, idempotent, guarded init; no duplicate GSAP/Swiper/Bootstrap.
- Core protection: hash-compare core files before/after; any unexpected change = STOP.
- Regression: full existing suite (verify.sh, live-gaps, design-isolation, routes, a11y, visual, failure-injection)
  + Luxury must remain byte-identical in behavior after client pack install/switch.
- Git: M11-01..M11-15 small commits, tests before each commit.
- DO NOT implement anything until the plan is complete, internally consistent, and approved.