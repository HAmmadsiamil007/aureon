# Aureon Frontend v2 — CURRENT SNAPSHOT (2026-08-07 late, M2 COMPLETE + Stage 12 hardening verified)

Single authoritative "what's done / what's remaining" memory. Companion: `mem:aureon-rebrand/frontend-v2-roadmap` (full roadmap), `mem:aureon-rebrand/lead-architect-review` (release readiness), `mem:aureon-rebrand/phase17-stage12-hardening` (hardening ledger — CURRENT).

## SESSION 2 (2026-08-07) — Stage 12 hardening verified live; fonts root-cause fixed; git facts corrected
- **Fonts DONE & LIVE**: Cabinet Grotesk + Satoshi self-hosted (woff2 in `frontend/assets/fonts/`, `@font-face` in `frontend/assets/css/fonts.css`); root-cause of broken font stack was `esc_attr()` in `aether-tokens.php` printing `&#039;` entities inside inline `<style>` → fixed with CSS-safe `aether_token_css_value()`. Browser shows both families `loaded`; `--font-body: 'Satoshi', sans-serif`; h1 Cabinet Grotesk. Preload warning fixed (fonts.css `?ver=filemtime` now matches both preload + stylesheet).
- **Wishlist DONE & LIVE**: page renders real user-meta products (28 Void Jacket, 33 Midnight Sneakers); toggle AJAX writes meta; logged-out/empty states handled in `adapter-wishlist.php`.
- **Quick view DONE & LIVE**: modal opens on home cards w/ real product data via `aether_quick_view` AJAX; shop-grid cards intentionally have NO quick-view (faithful to `source/shop.html`).
- **CSP**: `font-src` gained `data:` (WC base64 fonts); still report-only (strict via `AETHER_CSP_STRICT` after monitoring); WP/WC core script violations are expected info-only.
- **Console**: fresh tab = 0 errors / 0 warnings.
- **Git reality (verified)**: branch is `main`; HEAD `3e5741a` = "feat(aureon): complete hardening…", **1 commit ahead of origin/main, NOT pushed**. 260 working-tree entries: 200 deletions (legacy `assets/aether/*` cleanup), 23 modified, 37 untracked (`frontend/`, `mu-plugins/`, docs). This session touched `aureon/theme/inc/aether-tokens.php` + `aether-security.php` (bind-mounted, live; UNCOMMITTED by user policy).
- **Files changed this session**: `aether-tokens.php`, `aether-security.php`, docs (`aureon-doc/DECISIONS.md`, `aureon-doc/STATUS.md`).

## DONE + LIVE (verified on aureon_wp @ localhost:8080, 0 console errors)
- **All Stage 2–11 frontend**: shell, home, shop, product, cart/checkout/account, blog, static, tokens/assets/animation. 17 routes verified.
- **M2 Component Library Audit COMPLETE (2026-08-07)**:
  - M2.1/M2.2 inventory + manifest consistency — 52 components, 1:1.
  - M2.3 gap components: `order/confirmation` (thank-you override via template_include), `form/forgot-password` modal (`/login/`), `auth/password-strength` meter (`/register/`), `account/orders` table (`/my-account/orders/`), `values` section (`/team/`). All DONE + LIVE. Screenshots stage-m2-*.png in repo root.
  - M2.4 purged 7 WP-danger fallbacks from components (error-404, countdown, login, register, cart/items, cart/summary, article-hero); buttons gated on empty URLs; grep gate 0 direct WP calls (sanctioned `do_action('woocommerce_*_form')` only).
  - **M2.5 API headers DONE 2026-08-07**: normative standard in `FRONTEND-V2-ARCHITECTURE.md` §4.2 (`Key`/`Source`/`Props`/`Slots`/`Variants`/`Tokens`). Applied to all 52 `frontend/components/*.php`. Verification: 52/52 carry @package+Keys+Props:+Slots:, 224/224 `$componentData[...]` keys documented vs header, `php -l` clean everywhere, no mojibake.
  - Slot graph (documented in headers): `commerce/rating` shared leaf; `hero/slider→hero/slide`; `forms/register→auth/password-strength`; `section/newsletter→form/newsletter`; `cards/product`, `cards/review`, `product/info`, `product/reviews→commerce/rating`; `product/related→cards/product`.
  - Known header-flagged caveats (M3 follow-up): `cart/summary` hardcoded #4CAF50; `checkout/order-items`, `cart/items`, `account/orders`, `product/specs` inline layout styles; `forms/login`+`forms/register` `var(--gold)` required-marks (sanctioned §5).
- Serena memories current: `frontend-v2-roadmap` (M2.5 DONE recorded), `lead-architect-review` (frontend ~88%), `phase17-frontend-framework` (overhauled to complete status).

## REMAINING (ordered — per user review 2026-08-07)
- **M3 — Token-driven design system** (PHASE C): full token map → Customizer; SCSS/CSS generated from tokens; resolve the header-flagged inline-style caveats.
- **M4 — Customizer integration** (PHASE E): every component reads every value from `aureon_get_option()` (clean remaining stragglers).
- **M5 — Frontend Integration Engine**: HTML→component mapping, data adapters, WC data binding; extend adapters, product quick-view/related/upsells.
- **M6 — Demo import system** (PHASE F — 0% today, largest commercial feature): one-click demo via JSON manifest + importer REST endpoint; ship AETHER flagship demo first.
- **M7 — Animation system** (PHASE G): per-component presets + reduced-motion extension.
- **M8 — Multi-demo library** (PHASE H): 10–15 premium demos reusing one core.
- **M9 — Builder** (PHASE D, post-RC): section pattern library, visual builder.
- **M10 — RC gates**: Playwright multi-viewport + cross-browser, performance budgets (LCP<2.5s/TBT<300ms/CLS<0.1), PHPCS/PHPStan/Psalm + php -l + node --check, package zips.

## Operation notes (CAREFUL)
- **Deploy gotcha**: live tree is `/var/www/html/wp-content/frontend/` (theme loads ../../frontend/views/loader.php → AETHER_FRONTEND_DIR). Extract tar to `wp-content/frontend/`, NOT `wp-content/` root (stale fat copy). Use tar.exe (not Compress-Archive). After deploy, verify live HTTP; screenshots/repro with Playwright.
- **DB/tokens**: Docker aureon_wp, DB wpuser/wppass/wordpress/rootpass. Admin admin123, WC 11.0.0, WP 6.9.1.
- **Engine invariants**: `aureon_do_attr` (helpers.php), `aureon_construct_sidebars`; plugin WC functions.php 1571 lines; WP drops stylesheet deps if handle registered with false src — register first.
- Components are data-dumb: must NOT call WP/WC directly; receive real values in `$componentData` from adapters.