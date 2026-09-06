# GP 3.6.1 → Premium Frontend — Master Architecture Blueprint (PHASE 4)

Deliverable: Report/master_architecture_blueprint.md (649 lines, 35.4KB). Architecture-only; ZERO production code. Grounded in direct source reads of theme + premium (bootstrap, inc/*, assets/*, gp-premium.php, module dirs).

## Executive Decision
- **ADOPT: GeneratePress CHILD THEME** (presentation layer). Optional companion mu-plugin for asset/token concerns.
- REJECT standalone theme: GP Premium hard-checks active template slug `generatepress` (parent/theme check) — would lose GP Premium entirely.
- REJECT plugin-only presentation: cannot win template hierarchy; fights get_header()/get_footer(); breaks Gutenberg preview.
- Dependency direction ONE-WAY: Core → GP(parent, UNTOUCHED) → GP Premium(UNTOUCHED) → WC + 3rd-party(UNTOUCHED) → CHILD THEME (templates/components/tokens/assets → GSAP/Lenis/Three.js → Premium Frontend).

## Non-negotiable rules
1. Child uses ONLY GP public API: generate_get_option(), apply_filters('generate_*'), do_action('generate_*'), wp_nav_menu, widgets, template hierarchy. NEVER internal symbols, never shadow/fork GP/Premium files.
2. Namespace gpv\, prefix gpv_, option prefix gpv_, hook prefix gpv_. No global leaks.
3. Presentation tokenized (CSS custom properties, never hard-coded hex/px).
4. Update safety: CI job hashes GP/Premium dirs, fails if any shipped file changes.

## Decision codes (Table at line 236)
RETAIN / HOOK / OVERRIDE / FILTER / EXTEND / SUPPRESS / CONTROL.

## Key integration points (from source)
- Dynamic CSS: wp_enqueue_scripts @50 → generate_enqueue_dynamic_css() → generate_get_dynamic_css() → wp_add_inline_style('generate-style', ...). init → generate_set_dynamic_css_cache() (option); customize_save_after → update cache. Filters generate_base_css / generate_advanced_css append modules. GP Premium external-file override: filter generatepress_dynamic_css_print_method → 'external', writes uploads/generatepress/dynamic-css-{hash}.css via WP_Filesystem (class-external-file-css.php).
- Theme style handles: generate-style (main.min.css flexbox / all.min.css), generate-style-grid (unsemantic-grid, non-flexbox), generate-mobile-style (mobile.min.css), font-awesome 4.7, generate-rtl, generate-comments, generate-widget-areas, generate-child (child stylesheet get_stylesheet_uri).
- JS handles: generate-menu, dropdown-click, navigation-search, back-to-top, modal.js (dist.197).
- Components: Header (RETAIN/HOOK/restyle via tokens), Navigation (OVERRIDE), Footer (SUPPRESS+ship own), Loop grid (OVERRIDE), Single post (HOOK/OVERRIDE), Sidebar (HOOK), Search modal (EXTEND gp-modal primitive, keep focus mgmt), Comments (EXTEND keep comments_template()), Breadcrumbs (RETAIN/native), Back-to-top (RETAIN), Widget areas (RETAIN/restyle), Page builders (EXTEND never override templates — plugin-printed content, only CSS guards), GitHub REST (RETAIN), Tables (tokens), Forms (tokens), 404 (OVERRIDE), Blocks (RETAIN/align tokens), Standard sections etc.
- Template strategy: header.php, footer.php, index.php, single.php, archive.php, search.php, 404.php, page.php, wp-comments.php? comments.php child overrides; comments EXTEND feed comments_template().

## 13 Phases
1 System Map (bootstrap order theme + Premium + assets) 2 Frontend Replacement Map (26-component catalog w/ decision codes) 3 Template Strategy 4 Component Architecture (26-comp catalog) 5 Data Layer (adapter)

## Data Layer adapters (Gpv\Data\*)
Wp_Post_Adapter, Acf_Adapter, Wc_Product_Adapter, Menu_Adapter, Settings_Adapter, Widget_Adapter, Tax_Adapter. Components isolated from GP templates; data via adapters.

## Design System (Phase 6)
CSS custom-property tokens: color/typography/spacing/layout/radius/shadow/motion/breakpoints/z-index/accessibility. Customizer bridge (Phase 9) = mapping generate options → tokens, not a replacement.

## Asset Pipeline (Phase 7)
Vite → SCSS→CSS, ESM→JS (GSAP/Lenis/Three.js), image optimization, font loading, cache-busting/versioning. WP enqueue via handle + inline CSS.

## WooCommerce (Phase 8)
Keep EVERY woocommerce_* hook. Template map + compat protection: override templates via theme WC folder; scripts; do not fight direct loop.

## 3rd-party compat matrix (Phase 10)
Elementor/Bricks/Beaver/Blocksy etc → EXTEND, CSS guards only.

## Update Safety (Phase 12)
File risk table; every file classed as SAFE/OVERRIDE/EXTEND; CI hash gate.

## Implementation Roadmap (Phase 13)
Phase 0 env+baseline, 1 skeleton boot+tokens, 2 layout system, 3 component library, 4 WooCommerce layer, 5 design tokens+theming, 6 motion (GSAP/Lenis/Three), 7 performance, 8 accessibility, 9 testing+hardening, 10 release. Each phase: deliverable / verify / rollback.

## Final Compliance (WE-RUN)
GP updates (child-theme only/filters); GP Premium updates (zero engagement); WooCommerce (re-emit every woo_* hook); WP standards (gpv_, PSR-4, escaping, nonces, PHPCS); A11y WCAG 2.2 AA; Performance single CSS/JS core, lazy, tokens, fonts swap; Maintainability token-driven, documented, CI.

## Deliverable
Blueprint = single source of architectural truth. Next step: per-phase task-track build sessions using Phase 13 as sequence.

## PHASE 5 — PLAN DOCUMENTS (COMPLETE 2026-08-03)
Project now branded "Phantom Theme / Phantom Core".
- Report/MASTER_ROADMAP.md — SINGLE SOURCE OF TRUTH tracker: phase status table (Phases 0-17, all Not Started), dependency graph, ADR table (ADR-001..012), completed-work index (A0-A4), release/versioning policy. Update this file when a phase starts/finishes.
- Report/PHASE_5_PHANTOM_CORE_IMPLEMENTATION_PLAN.md — 902-line engineering spec: Part A (Phases 0-1), Part B (2-6), Part C (7-9), Part D (10-12), Part E (13-17). Ends with FINAL DELIVERABLE NOTE.

KEY CONVENTIONS (locked): namespace Phantom\Core, prefix phantom_, hooks phantom_*, options phantom_*, handles phantom-*, tokens as CSS custom props --phantom-*, ADR-001..012. Feature flags phantom_feature_*.

PHASE MAP (user's authoritative numbering):
0 Foundation | 1 Bootstrap | 2 Framework Infra | 3 Design Token Engine | 4 Render Engine | 5 Component Registry | 6 Template Engine | 7 Asset Pipeline | 8 Plugin Bridges | 9 SEO? NO — note: user's canonical is 8 Plugin Bridges 9 WooCommerce Bridge 10 Animation | 11 Frontend Components | 12 Frontend Templates | 13 Performance | 14 Accessibility | 15 Testing | 16 Rebranding(plan only) | 17 Release.

NOTE: The master phase numbering table shown in this memory's earlier Phase 4 summary used the 13-phase blueprint numbering — the Phase 5 plan supersedes with 18 phases. ADR numbering is NOT in the plan; ADRs live in MASTER_ROADMAP.md.