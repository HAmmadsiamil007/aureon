# PHASE 05 — Aureon Studio Plugin Module Audit

**Status:** DONE
**Date:** 2026-08-14
**Scope:** Static + live audit of the companion plugin (16 module folders, 10 active) against the AETHER frontend integration. Live evidence from `aureon_wp` (localhost:8080).

---

## 1. Module Load State (live-verified)

Plugin header `aureon-studio.php` v1.1.0 (internal `AUREON_STUDIO_VERSION` 3.0.0). Module gating = option `aureon_package_*` === 'activated' OR constant defined.

| Module | Option live | Loaded? | Reason |
|---|---|---|---|
| backgrounds | activated | ✅ | — |
| blog | activated | ✅ | — |
| copyright | activated | ✅ | — |
| disable-elements | activated | ✅ | — |
| elements | activated | ✅ (+ dynamic-tags + adjacent-posts) | — |
| secondary-nav | activated | ✅ | — |
| spacing | activated | ✅ | — |
| menu-plus | activated | ✅ | — |
| woocommerce | activated | ✅ (only when WC active — it is, 11.0.0) | — |
| font-library | activated | ✅ | — |
| hooks | false | ❌ | dormant legacy |
| page-header | false | ❌ | dormant legacy |
| sections | false | ❌ | dormant legacy |
| typography | false | ❌ | superseded — theme-native dynamic Typography Manager (`aureon_is_using_dynamic_typography()` = true forces skip) |
| colors | false | ❌ | superseded — theme-native Color Manager (theme version 3.6.1 ≥ 3.1.0-alpha.1 gate skips) |

## 2. Frontend Asset Presence (live homepage scan)

Only **2 plugin handles** reach the AETHER frontend:

```
aureon-woocommerce-inline-css   (plugin woocommerce/css/woocommerce.min.css + inline)
aureon-woocommerce-js (+ extra) (plugin woocommerce/functions/js/woocommerce.min.js)
```

Everything else is neutralized by three independent mechanisms:

### 2.1 Handle dequeue (theme bridge)
`inc/frontend.php:54-101` dequeues `aureon-style` (the plugin's inline-CSS host). Consequently these module outputs are **dropped silently**:
- blog module: `wp_add_inline_style('aureon-style', aureon_blog_css()/columns_css())` (blog/functions/aureon-blog.php:24-25)
- spacing module: `aureon_spacing_premium_css` at 105 (spacing/functions/functions.php:342)
- disable-elements: `aureon_de_scripts` at 50 (disable-elements/functions/functions.php:80)
- elements Hero: `wp_add_inline_style('aureon-style', self::build_css())` (elements/class-hero.php:138)
- external CSS file mode: `aureon-dynamic` depends on `aureon-style` (general/class-external-file-css.php:191) — `css_print_method` default 'inline' anyway

No AETHER CSS is harmed; the plugin CSS simply never applies (its selectors target legacy theme markup — `.site-header`, `#site-navigation`, `.entry-header`, `.inside-article` — none of which exist in AETHER markup).

### 2.2 Settings off (defaults resolve to disabled)
Live option buckets: `aureon_menu_plus_settings`, `aureon_secondary_nav_settings`, `aureon_background_settings`, `aureon_spacing_settings`, `aureon_disable_elements_settings`, `smooth_scroll` → all **false** (whole-bucket false → `wp_parse_args` with defaults → features off). Confirms: no sticky menu, no slideout nav (`aureon_slideout_navigation` wp_footer:1231 dead), no secondary nav, no background images, no plugin smooth-scroll (AETHER Lenis owns this).

### 2.3 Empty CPTs
- `aureon_elements`: 0 published (10 auto-drafts from testing) → `aureon_premium_do_elements()` iterates nothing.
- `aureon_font`: 0 published → font-library `enqueue_font_css()` (class-font-library.php:447-456) no-ops (uploads/aureon/fonts/fonts.css absent).

## 3. Live-Visible Module: WooCommerce

- `aureon_wc_scripts` (functions.php:215-271) runs whenever `is_checkout` exists **on every page** (weak gate — function-existence check, not page-state). Enqueues `aureon-woocommerce` CSS (+mobile) always; JS only when `cart_menu_item` || off_canvas_panel || sticky_add_to_cart || quantity_buttons (defaults: `cart_menu_item`=true → JS loads).
- Inline CSS (`aureon_wc_css`, :1215) targets `.woocommerce` selectors with theme button palette (#55555e / #3f4047) — **inert on AETHER surfaces** (no `.woocommerce` classes in engine markup) but a small payload on every page. No visual conflict; correctness unaffected.
- `loop_shop_columns` (999) → `columns` default 4; `loop_shop_per_page` (20) → `products_per_page` default 9 — **these filter the engine's WC queries** (shop adapter). This is the intended bridge: plugin WC settings drive AETHER shop grid (see Phase 6).
- WC color bridge (M2) lives in theme, not plugin: `aether-tokens.php:102-129` streams `woocommerce_{primary,highlight,subtext,price}_color` into `--aether-wc-*` tokens **only when explicitly set**. Live: all 4 options unset (false) → engine stays on AETHER gold defaults — verified correct.

## 4. Module-by-Module Verdicts (frontend impact)

| Module | Frontend hooks | Verdict under AETHER |
|---|---|---|
| blog | `aureon_blog_scripts`(50), `post_class`, `body_class`, `excerpt_length/more`, `aureon_after_main_content/footer` | ✅ Inert (inline CSS dropped; masonry/infinite-scroll defaults off; theme hooks never fire). **G12 input**: `aureon_blog_settings` bucket intact (excerpt_length, read_more, post_image, date/author/cats/tags toggles) — the blog adapter can bridge these in G12. |
| elements | `wp` → CPT scan (elements.php:25-78); Hero hooks `aureon_after_header` — **removed by theme bridge** (frontend.php:99) | ⚠️ **G11 gap confirmed**: even a published Hero element would NOT render on AETHER surfaces (hook target deleted; AETHER templates don't fire it). 0 published elements today → zero impact now. G11 must re-target. |
| woocommerce | `aureon_wc_scripts`(100), `aureon_wc_enqueue_css`(100), `woocommerce_*` template hooks, loop filters | ✅ Live-visible but inert CSS; loop filters are the intended engine bridge. |
| menu-plus | `aureon_menu_plus_enqueue_css/js`(0/100), `aureon_slideout_navigation` wp_footer:0 | ✅ Inert (settings false). |
| secondary-nav | enqueue 100/110, `#secondary-navigation` markup | ✅ Inert (settings false). |
| spacing | `aureon_spacing_premium_css`(105) → aureon-style | ✅ Inert (dropped handle). |
| backgrounds | `aureon_background_scripts`(70) | ✅ Inert (settings false). |
| disable-elements | `aureon_de_scripts`(50) → aureon-style; post metabox | ✅ Inert (dropped handle); metabox admin-only. |
| copyright | admin + `wp_enqueue_script`(185, admin-guarded) | ✅ Admin-only. |
| font-library | `enqueue_font_css`(1) — file-existence gated | ✅ No-op (empty CPT). |
| general/icons | `aureon_enqueue_premium_icons` — register-only; `aureon_svg_icon` filter | ✅ Register-only (AETHER uses FA 6.5.1, not these SVGs). |
| general/smooth-scroll | `aureon_smooth_scroll_scripts` — `smooth_scroll` default false | ✅ Inert; AETHER Lenis owns smooth scroll. |
| general/external-file-css | dynamic CSS file mode | ✅ Default inline; file mode depends on dequeued `aureon-style`. |
| hooks / page-header / sections | (inactive) | ✅ Dormant. |
| typography / colors | (inactive — theme-native supersedes) | ✅ Theme-native Typography Manager + Color Manager feed AETHER via G9 bridge (verified Phase 4: `typography` option → Space Grotesk → `--font-body`) and `global_colors` palette → `aether_resolve_color()` (F4-6). |

## 5. Findings

| ID | Sev | Finding |
|---|---|---|
| F5-1 | INFO | 10/16 modules active; 5 dormant (hooks, page-header, sections, typography, colors). Typography/colors intentionally superseded by theme-native managers — no action. |
| F5-2 | INFO | All active-module frontend output neutralized by dequeue/off-settings/empty-CPT triple guard. Live scan proves only WC module assets load. No conflicts, no double-rendering, no FOUC sources from plugin. |
| F5-3 | LOW | `aureon_wc_scripts` gate is too weak (function-existence, not page state) → WC module CSS+JS on every page (~2 KB). Inert, but candidate for `is_woocommerce()||is_cart()||is_checkout()||is_account_page()` gating in Phase 12 hardening. |
| F5-4 | INFO | G11 Elements confirmed as a real integration gap: Hero elements hook `aureon_after_header`, which the bridge removes (frontend.php:99). Any G11 work must re-target element rendering onto AETHER surfaces (composer/section-level hook). Zero impact today (0 published). |
| F5-5 | INFO | G12 Blog bridging surface identified: `aureon_blog_settings` bucket (excerpt_length, read_more, post_image, meta toggles, infinite_scroll) is intact and versioned — blog adapter can read it without new options. |
| F5-6 | INFO | M2 WC color bridge verified end-to-end: plugin WC customizer → `woocommerce_*_color` options → `--aether-wc-*` tokens (theme aether-tokens.php:102-129). All 4 unset live → AETHER gold defaults preserved. Bridge activates only on explicit merchant palette — correct by design. |
| F5-7 | INFO | Licensing/update provider seams + `inc/legacy/` (activation, dashboard, import-export, reset) + `inc/deprecated.php` are inert: no HTTP calls on frontend, admin-only, no AETHER interaction. |
| F5-8 | INFO | `aureon_elements` CPT holds 10 auto-drafts (test residue). Harmless; cleanup candidate for the M9-M12 demo-import phase. |
| F5-9 | INFO | Security spot-check passed: `aureon_regenerate_css_file` ajax has `check_ajax_referer` + `manage_options` (class-external-file-css.php:437-441); font uploads whitelist mime types + `sanitize_file_name` (class-font-library.php:399-408,483); no unescaped option echo on frontend. |
| F5-10 | INFO | `aureon_dynamic_css_data`/`css_print_method` machinery is dormant (default inline; file mode impossible under AETHER since host handle is dequeued). No cleanup needed — harmless. |

## 6. Inputs for Later Phases
- **Phase 12 (hardening):** F5-3 WC script gate; F5-4 Elements re-target design.
- **G12 (blog dynamicity):** F5-5 bucket is the settings source.
- **M9-M12 (demo import):** F5-8 auto-draft cleanup.