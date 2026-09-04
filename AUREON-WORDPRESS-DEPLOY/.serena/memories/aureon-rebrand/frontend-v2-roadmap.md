# Aureon Frontend v2 — Commercial Release Plan (2026-08-07)

## Goal
Turn "stable WordPress framework + verified AETHER frontend" into a **commercial premium theme platform** (update-safe backend, replaceable tokenized frontend, one-click demos, builder-ready).

## Principle (non-negotiable)
Frontend = presentation layer only. WordPress/WC = data source only.
`Component → ViewModel → Adapter → Core → WordPress`. Never patch HTML into WP; always build components consuming adapter data. This is what Phase 17.1 proved and Phase 17 failed at.

## Milestone roadmap (ordered)

### M1. Frontend Integration Architecture doc (PHASE A) ✅ DONE 2026-08-07
`aureon-doc/FRONTEND-V2-ARCHITECTURE.md` — normative contract: data shapes §3, item schema §3.2, adapter interface §3.3, viewmodel rules §3.4, section registration §3.5, behavior whitelist §3.6, lifecycle §4, tokens/options audit gate §5, extension points + sanctioned do_action form exceptions §6.1, security gates §7, open questions §7, sign-off checklist §8.

### M2. Component library audit + gaps ✅ COMPLETE 2026-08-07
`aureon-doc/FRONTEND-V2-COMPONENT-AUDIT.md` — 454-class source diff vs 48 components/24 sections.
- M2.1/2.2 DONE (inventory + manifest 48:48).
- M2.3 order/confirmation DONE+LIVE: component order/confirmation, adapter-order, section-order-confirmation, `aureon/theme/woocommerce/checkout/thankyou.php` override, routing via template_include is_wc_endpoint_url('order-received')→dedicated template. Verified /checkout/order-received/72/ 200, 0 console errors.
- M2.4 DONE: purged 7 WP fallbacks from components (error-404, countdown, login, register, cart/items, cart/summary, article-hero); gated empty-URL buttons; grep gate 0 WP calls in components (sanctioned do_action form hooks documented).
- values section DONE+LIVE 2026-08-07: section-values.php (features-grid), `values` data on adapter-about, rendered on page-team.php via aether_render_section, token aether_section_values + Customizer "Values (Team)" toggle. Verified /team/: raw HTML has values-grid/Our Values/What Drives Us, probe values_render_len=1343, 0 console errors. GSAP data-reveal-group sets opacity:0 until scroll → Playwright a11y only shows the section after scrolling bottom (screenshot stage-m2-values-section.png).
- newsletter-success + account/nav = FALSE POSITIVES in first audit pass (newsletter.php renders .newsletter-success; account/profile.php+adapter-account covers nav). Removed from missing list.
- REMAINING M2.3: forgot-password modal, password-strength, account/orders — **ALL DONE + LIVE 2026-08-07.** M2.3 COMPLETE.
  - `form/forgot-password` modal (`/login/`): component + manifest entry + adapter-auth `forgot` subarray (action = wc_lostpassword_url(), _wpnonce `lost_password` + hidden `wc_reset_password=true`) + login.php `data-forgot-toggle` + section-auth render + JS open/close. Verified: opens, prefill from `#username`, POST → `/my-account/lost-password/?reset-link-sent=true`, 0 errors.
  - `auth/password-strength` (`/register/`): component + manifest entry + register.php `show_strength` flag + **JS bug fix** — old handler bound dead source IDs `#password`/`#seg1`, WC uses `#reg_password`; now generic via `data-strength-target` (active/weak/medium/strong + text colors). Verified typing `StrongPass1!` → 4/4 strong "Very strong password" #2ECC71; `abc` → 1/4 weak.
  - `account/orders` (`/my-account/orders/`): component + `aether_adapter_account_orders()` (real wc_get_orders, limit 20) + my-account.php orders-endpoint branch (WC nav sidebar + component; addresses/edit-account keep stock). Verified empty state + populated row (#73 test order, processing). Screenshot stage-m2-account-orders.png.
  - Manifest: +3 entries (form/forgot-password, auth/password-strength, account/orders) → ~52 components.
  - ⚠️ DEPLOY GOTCHA: live tree is `/var/www/html/wp-content/frontend/` (theme loads ../../frontend/views/loader.php → AETHER_FRONTEND_DIR). Extract tar to that dir; extracting to `wp-content/` root leaves a stale fat copy — verify live HTTP after deploy.
  - M2.5 API headers DONE 2026-08-07: standardized docblock (Key/Source/Props/Slots/Variants/Tokens, normative §4.2) applied to all 52 components. Verified: 52/52 carry @package+Props:+Slots:+Key:, every `$componentData[...]` read documented (224 keys cross-checked, 0 missing), php -l clean, no mojibake. Hardcoded-value caveats flagged in headers (cart/summary #4CAF50, order-items/cart/items/account-orders/product-specs inline styles; forms login/register var(--gold) token-drawn allowed). Slot graph documented: rating/hero-slide/form-newsletter/cards-product shared leaves.

### M2. Premium component library expansion (PHASE B)
- Audit 48 existing components vs source reference design — catalog missing pieces (drawers, tabs, alerts, badges, tables, mega-menu, quick-view modal, breadcrumb patterns, sliders, galleries).
- Standardize component API + variants across the whole library.
- Ship a living "design system showcase" page (/styleguide/) rendering every component with tokens.

### M3. Token-driven design system (PHASE C)
- Full token map: typography scale, spacing scale, radius, elevation/shadows, buttons, forms, cards, badges, alerts, tables, accordions, tabs, drawers, nav, hero, footer, product/category cards. All in `tokens/tokens.php` → Customizer.
- SCSS/CSS generated from tokens (build step or customizer-generated CSS).

### M4. Customizer integration (PHASE E)
- Every component reads every value from `aureon_get_option()` (audit for hardcoded values — current grep gate).
- Live preview controls for typography/spacing/colors/radius/buttons/container/header/footer/announcement/slider.

### M5. WooCommerce data binding (already strong — extend)
- Verify cart/checkout/account/order complete adapter coverage (checkout + order confirmed; add order-received component if missing).
- Product Quick View + related + upsells adapters.

### M6. Demo import system (PHASE F) — 0% today, largest commercial feature
- One-click demo: pages, menus, widgets, Customizer settings, product catalog, theme+plugin settings, media import.
- Design: XWidget/Pro-style export manifest (JSON) + importer REST endpoint importing XML→posts, terms, options, nav, customizer mods; sanitize + role-gated.
- Ship 1 flagship demo (AETHER) first.

### M7. Animation system (PHASE G)
- Every component gets animation presets (delay/duration/ease/scroll/parallax/3D) + reduced-motion — partially done via `aether_viewmodel_behavior()` motion toggles; extend to per-component presets in registry.

### M8. Multi-demo library (PHASE H)
- 10–15 premium demos (fashion, sneakers, furniture, jewelry, electronics, watches, gym, supplements, cosmetics, perfume, marketplace, corporate, agency, SaaS, portfolio) all reusing one core. Each = demo import manifest, not a forked theme.

### M9. Builder (PHASE D) — post-RC
- Section pattern library, visual layout builder, import/export, global styles.

### M10. RC gates
- Playwright multi-viewport (375/768/1440) + cross-browser (Chromium/Firefox/WebKit) full route sweep.
- Performance budget with real frontend (LCP<2.5s, TBT<300ms, CLS<0.1) with GSAP/Lenis/Swiper enabled.
- PHPCS/PHPStan/Psalm, php -l sweep, node --check, brand scan, integrity gate.
- Package: theme zip + plugin zip + demo import + docs + CHANGELOG bump.

## Sequencing decision (per user review 2026-08-07 — REVISED)
M2 → M3 → M4 → M5 (Frontend Integration Engine — renamed, was "WC binding") → M6 → M7 → M8 → M9 → M10.
Rationale (user): component contracts MUST exist before any HTML import — the original Phase 17 broke exactly because contracts were undefined. M2 first = frontend stability. M6 demo import only after frontend is finished (imports of an unfinished frontend are pointless).
M1 (FRONTEND-V2-ARCHITECTURE.md) is DONE 2026-08-07.

### M5 = Frontend Integration Engine (user-added, critical)
HTML import pipeline, HTML→component mapping, component contracts, view models, data adapters, template binding, CSS/JS isolation, asset manifest integration, WC/plugin data injection, customizer token injection, animation presets, responsive rules, per-page regression testing. Directly solves the Phase 17 failure mode.

### Revised sprints
Sprint 1 M2 · Sprint 2 M3 · Sprint 3 M4 · Sprint 4 Frontend Connector · Sprint 5 Premium Demo Templates (home/shop/collection/product/cart/checkout/account/blog/landing) · Sprint 6 Demo Import · Sprint 7 Commercial Release.

## Reference: status ledger
- `mem:aureon-rebrand/lead-architect-review` — "what's done / do not touch" truth
- `aureon-doc/STATUS.md` — live stage states