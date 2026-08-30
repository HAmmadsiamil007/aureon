# Frontend Replacement Framework — M1–M5 foundation (2026-08-14)

Frozen baseline: main @ de52eaf @ tag v1.2.1-audit → plan committed at 4c13bd0, foundation at d8f70f4 (feat) + e7157cd (docs).

## Master plan
docs/frontend-platform/MASTER_FRONTEND_REPLACEMENT_PLAN.md — 35 sections; answers A–N in §34; roadmap M1–M12; adapter inventory in Appendix A (23 adapters).

## Kernel changes (d8f70f4)
- NEW frontend/views/design.php (required by loader): aether_active_design() (AETHER_DESIGN const > option aether_active_design > 'luxury'), aether_active_design_dir() (frontend/designs/<slug>/), aether_resolve_design_path($rel) (pack-first), aether_design_defaults() (pack tokens.php include + filter), aether_apply_design_defaults (aureon_option_defaults priority 20).
- loader.php: requires design.php; globs pack sections frontend/designs/<slug>/sections/*.php after base sections.
- renderer.php: aether_component_manifest now apply_filters('aether_component_manifest'); component+section templates resolve via aether_resolve_design_path; NEW aether_normalize_viewmodel($data) applied in aether_render_section: aliases pagination<->paged, breadcrumb<->crumbs, stats{items:[...]}->list.
- section-stats.php: accepts stats as list or {items:[...]} (canonical = list [{number,label}]).
- verify.sh: NEW hex gate (components must not hardcode colors; excludes &#NNNN; entities) + design.php existence check.

## Docs (e7157cd)
- docs/frontend-platform/DATA_CONTRACT.md — frozen canonical ViewModels + freeze rules + canonical/legacy alias table.
- docs/frontend-platform/COMPONENT_CONTRACT.md — 52-component matrix (purpose/input/states/tokens/js/a11y) + pack rules.
- docs/frontend-platform/CLIENT_FRONTEND_INTAKE.md — P0–P10 intake, Tier A/B/C/D, JS KEEP/ADAPT/REPLACE/MOVE/REMOVE, gate checklist.
- docs/frontend-platform/templates/CLIENT_FRONTEND_FORENSIC_REPORT.template.md.

## Pack anatomy
frontend/designs/<slug>/{tokens.php (option defaults), sections/ (self-register), components/ (shadow same rel path), assets/}. Activation: AETHER_DESIGN const or option. Fallback to base tree per file.

## Remaining roadmap
M6 mapping manifest, M7 CSS isolation/enqueue, M8 JS behavior adapter, M9 visual harness multi-viewport/multi-pack, M10 first real client pack (proof), M11 hardening, M12 remaining 11 docs. Next recommended: M6+M7 then M10.

## Verified state
verify.sh PASSED; routes desktop 16/16 after deploy; main.js MD5 unchanged 6d8f3b671333571508efcb53b1e39e60. Deploy method: tar+base64+docker cp (48MB b64 worked).