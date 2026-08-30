# PHASE 4 — MASTER ARCHITECTURE & FRONTEND REPLACEMENT BLUEPRINT — SUMMARY

Deliverable: `Report/master_architecture_blueprint.md` (649 lines, 35.4 KB)
Date: 2026-08-03 | Status: COMPLETE (architecture-only, ZERO production code written)

## Goal
Transform GeneratePress 3.6.1 into a premium frontend framework while staying 100% update-safe.

## Executive Decision Record
- **ADOPTED: GeneratePress CHILD THEME** as the presentation layer.
- **REJECTED: Standalone theme** — GP Premium hard-checks the active template slug `generatepress`; would lose GP Premium entirely.
- **REJECTED: Plugin-only presentation** — cannot win template hierarchy; fights get_header()/get_footer(); breaks Gutenberg preview.
- Dependency direction (one-way): Core → GP (UNTOUCHED) → GP Premium (UNTOUCHED) → WC/3rd-party (UNTOUCHED) → CHILD (templates/components/tokens/assets → GSAP/Lenis/Three.js → Premium Frontend).

## Non-negotiable rules
1. Child uses ONLY GP public API (`generate_get_option()`, `apply_filters('generate_*')`, `do_action('generate_*')`, `wp_nav_menu`, widgets, template hierarchy). Never internal symbols, never shadow/fork GP/Premium files.
2. Namespace `gpv\`, prefix `gpv_`, option/hook prefix `gpv_`. No global leaks.
3. Presentation tokenized (CSS custom properties only — no hard-coded hex/px).
4. Update safety via CI job that hashes GP/Premium dirs and fails on any shipped-file change.

## Key integration point (verified from source)
Dynamic CSS: `wp_enqueue_scripts @50` → `generate_enqueue_dynamic_css()` → `generate_get_dynamic_css()` → `wp_add_inline_style('generate-style', …)`; filters `generate_base_css` / `generate_advanced_css`; GP Premium external-file override via `generatepress_dynamic_css_print_method`.

## The 13 Phases (all present in blueprint)
1. Complete System Map (bootstrap order, asset loading, customizer, runtime sequence)
2. Frontend Replacement Map (26-component catalog with decision codes)
3. Template Strategy
4. Component Architecture
5. Data Layer (Gpv\Data\* adapters)
6. Design System (CSS custom-property tokens)
7. Asset Pipeline (Vite/SCSS/ESM/GSAP/Lenis/Three.js)
8. WooCommerce (keep EVERY woo_* hook + template map)
9. Customizer bridge (tokens, not replacement)
10. Third-party plugin compat matrix
11. File Structure
12. Update Safety (file risk table)
13. Implementation Roadmap (Phase 0–10, each with deliverable/verify/rollback)

## Decision codes
RETAIN / HOOK / OVERRIDE / FILTER / EXTEND / SUPPRESS / CONTROL

## Final Compliance (WE-RUN)
GP updates (child-theme only, filters) · GP Premium updates (zero engagement) · WC re-emits every `woo_*` hook · WP standards (`gpv_`, PSR-4, escaping, nonces) · A11y WCAG 2.2 AA · Performance single CSS/JS core, lazy, tokens, font swap · Maintainability token-driven, CI, documented.

## Related artifacts
- `Report/master_architecture_blueprint.md` — the full blueprint (source of architectural truth)
- `Report/complete_engineering_review_report.md` — 10-phase engineering review (Phase 3)
- `Report/second_stage_forensic_report.md` — 10-phase forensic verification (Phase 2)
- Serena memories: `gp-audit/state`, `gp-audit/architecture-blueprint`