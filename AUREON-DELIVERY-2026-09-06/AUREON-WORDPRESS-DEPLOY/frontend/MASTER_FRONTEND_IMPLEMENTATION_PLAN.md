# MASTER_FRONTEND_IMPLEMENTATION_PLAN

**Phase:** 17 — Frontend Integration Framework
**Date:** 2026-08-06
**Status:** Plan complete — awaiting approval before production code

---

## 1. Objective

Integrate the AETHER static frontend into the Aureon theme via a **component framework**: adapters → ViewModels → renderer → components, tokenized CSS, declarative animation bridge, and WooCommerce overrides — with visual regression verification. **No wholesale page imports.**

---

## 2. Guiding Principles (locked)

1. Components receive `$componentData`; **never call WP/WC functions**.
2. Adapters are the only layer touching WP APIs; outputs filtered via `aether_*_data`.
3. Single escape boundary in the renderer (esc_html/esc_attr/esc_url/wp_kses_post).
4. Tokenized CSS: `--aureon-frontend-*` vars driven by Customizer defaults = AETHER palette.
5. Declarative behavior: components emit `data-*` attributes; bridge JS auto-initializes.
6. Server-side rendering primary; REST/AJAX only for lazy-load & form posts.
7. Dead code excluded: `vendor/` 19 files, `effects.js`, `phantom-dark-mode.js`, `three-scenes.js`, `bootstrap.min.css` decision, `contact-form.php`.
8. Extend existing plugin WC module + hooks — never duplicate.
9. Pinned local vendor libs (GSAP 3.12.5, Lenis 1.1.18, Swiper 11, Bootstrap 5.3.3, jQuery 3.7.1).
10. Approval gate after this plan + architecture docs before production templates.

---

## 3. Implementation Order (15 steps, from approved method)

| # | Step | Deliverables | Depends on | Docs |
|---|---|---|---|---|
| 1 | **Assets** — curate `frontend/assets/` (css/js/img/fonts/icons from source; exclude dead) | `frontend/assets/` + manifest | Step 2 audit | FRONTEND_ARCHITECTURE_REPORT §8 |
| 2 | **Tokens** — tokenize style.css → `frontend/tokens/` + Customizer "Frontend" section | `tokens/`, customizer field files | — | TOKEN_MIGRATION_REPORT |
| 3 | **Header** — shell components: preloader, fog, skip-link, announcement, mobile chrome, header, mini-cart | components + structure/header.php | 1, 2 | COMPONENT_INVENTORY §2.1 |
| 4 | **Footer** — footer shell + newsletter + back-to-top | components + structure/footer.php | 1, 2 | COMPONENT_INVENTORY §2.1 |
| 5 | **Hero** — hero slider, page-title band | components + front-page hero | 3 | SECTION_LIBRARY §2.2 |
| 6 | **Buttons** — button/toggle/stepper primitives (tokenized) | ui primitives | 2 | COMPONENT_INVENTORY §2.10 |
| 7 | **Cards** — product, category, blog, review, team, cart/order-item | card components | 6 | COMPONENT_INVENTORY §2.5-2.7 |
| 8 | **Sections** — section library + engine (`aether_register_section`) | sections + section engine | 5-7 | SECTION_LIBRARY §5 |
| 9 | **Templates** — compose front-page, page templates, blog/single (per TEMPLATE_MAPPING order) | theme template files | 8 | TEMPLATE_MAPPING |
| 10 | **WooCommerce** — WC adapters + overrides (shop, product, cart, checkout, myaccount, thankyou, wishlist) | plugin WC extensions | 8, 9 | WOO_INTEGRATION_REPORT |
| 11 | **Customizer** — expose all tokens + section toggles + OAuth config + newsletter toggle | customizer fields | 2, 9 | TOKEN_MIGRATION_REPORT §2 |
| 12 | **Animations** — bridge JS (`aether-animations.js` etc.), pin vendor libs, init order | frontend/assets/js/* | 8 | ANIMATION_INTEGRATION_REPORT |
| 13 | **Performance** — bundle strategy, defer, font display, lazy sections, cache-busting | perf report | 12 | — |
| 14 | **Accessibility** — a11y.css always-on, reduced-motion, contrast, keyboard, ARIA | a11y pass | 12 | — |
| 15 | **Regression** — golden vs candidate screenshots, pixel diff, grade A/B | regression suite + results | 9-14 | VISUAL_REGRESSION_PLAN |

---

## 4. Architecture Map (files to create)

```
frontend/
├── assets/                  ← curated (css, js, img, fonts, icons)
│   ├── css/  frontend.css (tokenized), motion.css, a11y.css, aether-wc.css
│   ├── js/   aether-core.js, aether-animations.js, aether-lenis.js,
│   │         aether-forms.js, aether-cart.js, aether-gallery.js, aether-auth.js
│   ├── vendor/  gsap 3.12.5, ST, lenis 1.1.18, swiper 11, bootstrap 5.3.3, jquery 3.7.1
│   └── img/ fonts/
├── components/              ← component templates (data-driven PHP partials)
├── sections/                ← section templates
├── layouts/                 ← shell/page layouts
├── adapters/                ← adapter functions (core + content + blog)
├── views/                   ← ViewModels/renderer
├── tokens/                  ← token definitions
├── manifest/                ← component/asset manifests
├── regression/              ← golden/candidate/diff artifacts + runner
├── docs/                    ← this doc + all Phase 17 reports
└── source/                  ← static reference (read-only, untouched)
```

Theme wiring: `aureon/theme/inc/frontend/` bootstrap (enqueue, sections, adapters require).
Plugin wiring: `aureon/plugin/woocommerce/adapters/*` + template overrides.

---

## 5. Key Decisions (this plan)

| Decision | Choice | Rationale |
|---|---|---|
| Bootstrap CSS | bundle locally | no CDN dependency; tokenized overrides |
| GSAP/Lenis pin | 3.12.5 / 1.1.18 (template versions) | reproducible, matches animations.js API |
| Page-title hero on shop | keep `woocommerce_show_page_title` disabled; render section | avoid WC duplicate title |
| Wishlist | plugin-optional; Customizer toggle | no hard dependency |
| Google OAuth | server-side exchange; hidden unless configured | security |
| Newsletter | single instance in footer by default | 19 dupes avoided |
| Light mode | tokens light-ready, dark default, toggle off | scope control |
| Legacy gold #d4af37 | normalize to #C8956C | consistency |

---

## 6. Team Allocation (parallel tracks)

| Track | Steps | Max parallelism |
|---|---|---|
| Theme/core | 1-4, 11, 13, 14 | with Track B after step 8 |
| Commerce | 10 | after step 8 |
| Motion | 12 | after step 8 |

Subagents per step when independent (e.g., step 10 adapter batch in parallel).

---

## 7. Risk Register

| Risk | Mitigation |
|---|---|
| animations.js class-coupling | keep class names in components or update `autoAssignReveals` map in step 12 |
| WC variable JS conflicts | keep WC markup skeleton, AETHER styling on top (WOO report §7.1) |
| Token migration drift | step 2 verification checklist (0 hardcoded hex in bundle) |
| Firestore/firebase path bugs | fixed at bundle time; server-side verification only |
| Font loading mismatch (golden vs WP) | `document.fonts.ready` capture + font-library registration |
| Scope creep (new demo packs) | excluded from Phase 17; `demo/` reserved for later |

---

## 8. Acceptance Criteria (end of Phase 17)

1. All 10 Phase 17 reports delivered (DONE — this batch).
2. `frontend/` restructured per §4; dead files excluded from WP enqueue.
3. All 62 phantom keys server-rendered on their pages.
4. Customizer frontend section live: tokens, toggles, OAuth, newsletter.
5. WC flows end-to-end (shop → product → cart → checkout → order received).
6. Regression suite green: all pages grade A or B.
7. Reduced-motion + mobile emulation pass; a11y.css active.
8. Performance: bundle sizes recorded (target ≤ 250KB gz JS total incl. vendor), LCP ≤ 2.5s on dev machine.

---

## 9. Approval Gate

- [x] Phase 17 reports (10/10) authored
- [ ] Reviewer sign-off on architecture (this doc + FRONTEND_ARCHITECTURE_REPORT)
- [ ] Reviewer sign-off on token mapping (TOKEN_MIGRATION_REPORT)
- [ ] Sign-off on WC approach (WOO_INTEGRATION_REPORT)
- [ ] GO: begin Step 1 (assets curation) → Step 2 (tokens)

**Not in scope:** committing the rollback of the old AETHER attempt (working tree remains as-is until user decides); rebuilding `front-page.php` before sign-off; any template production code.