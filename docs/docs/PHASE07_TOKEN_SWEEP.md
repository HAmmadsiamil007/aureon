# PHASE 07 — TOKEN SWEEP

> **Phase:** 7 · **Date:** 2026-08-14 · **Method:** static cross-reference of the three token surfaces + live `:root` emission (2026-08-14, already captured in Phase 4)
> **Scope:** all CSS custom properties (`--aether-*`, `--void … --radius-pill`, z/transition scale) — definition, emission, consumption
> **Surfaces:** ① static `:root` in `frontend/assets/css/style.css:2-51` · ② dynamic emission `aureon/theme/inc/aether-tokens.php:64-167` (`aether_generate_tokens_css`) · ③ option-default registrations `frontend/tokens/tokens.php` (settings bucket + color/font bridges)

---

## 1. Token census (23 dynamic + 13 static-only + 3 WC-bridge)

| Group | Vars | Defined statically | Emitted dynamically | Consumed by engine CSS/JS |
|---|---|---|---|---|
| Colors (12) | `--void --surface --surface-2 --surface-3 --text --muted --chrome --gold --gold-alt --line --error --success` | 4/12 (`void surface chrome gold`) | ✅ 12/12 | 9/12 (see F7-3) |
| Fonts (2) | `--font-heading --font-body` | ✅ | ✅ (Typography Manager bridge) | ✅ |
| Layout (9) | `--container-max --section-padding --announcement-height --header-height --grid-gap --radius-sm --radius-md --radius-lg --radius-pill` | ✅ | ✅ 9/9 | 7/9 (see F7-3) |
| WC bridge (≤4) | `--aether-wc-primary --aether-wc-highlight --aether-wc-subtext --aether-wc-price` | — | ✅ conditional | 3/4 (see F7-3) |
| Static-only (13) | `--white --black --transition-fast/normal/slow --z-base --z-fog --z-footer --z-scroll-top --z-nav-overlay --z-header --z-announcement --z-mobile-header --z-mobile-hamburger --z-mobile-menu --z-search --z-preloader --z-skip-link` | ✅ | — | ✅ (except `--black`) |
| Self-contained (3) | `--primary--color --button-text --progress` (a11y.css) | a11y.css inline fallbacks | — | ✅ (a11y.css only) |

Emission totals verified live (Phase 4): 23 dynamic vars + `--font-*` overrides — matches census.

## 2. Value-neutrality (static vs dynamic defaults)

| Var | Static `:root` | Dynamic default | Match |
|---|---|---|---|
| `--void --surface --chrome --gold` | `#09090B #141416 #A8B5C0 #C8956C` | same | ✅ |
| `--font-heading/body` | `'Cabinet Grotesk'/'Satoshi', sans-serif` | same (when typography override absent) | ✅ |
| `--section-padding --container-max --grid-gap --announcement-height --header-height --radius-sm/md/lg/pill` | `100px 0 / 1200px / 24px / 40px / 80px / 8px / 12px / 24px / 999px` | same (all 9) | ✅ |
| `--gold-alt --line --error --success --muted --surface-2 --surface-3` | **NOT DECLARED** | `#D4A574 #1A1A1A #CC4444 #4CAF50 #A8B5C0 #1a1a1d #232327` | ❌ **F7-1** |
| `--white --black --transition-* --z-*` | declared | not emitted (not customizable) | ✅ (by design) |

## 3. Findings

| ID | Sev | Finding |
|---|---|---|
| F7-1 | MED | **Value-neutrality claim is false for 7 vars.** `aether-tokens.php:58-60` states "removing this block never breaks the design" — true only for the 12 statically-declared vars. `--gold-alt` (hover states, style.css:388), `--line` (borders, :1161), `--error`/`--success` (status/badges, :1205) are consumed by engine CSS but exist **only** in the dynamic block. If `aether_generate_tokens_css()` ever returns '' (e.g. `aureon_get_option` absent) or the inline style is stripped, those declarations silently degrade to `color: initial`. READ-ONLY fix option for Phase 12-15: add the 7 vars to static `:root` with their default values (pure value-neutral addition). |
| F7-2 | MED | **Color-default bridges disagree on 3 keys.** Settings bucket + emission fallback use `aether_color_border #1A1A1A`, `error #CC4444`, `success #4CAF50` (tokens.php:464-466, aether-tokens.php:87-89) but the customizer/WC bridge (`aether_frontend_color_defaults`, tokens.php:490-492) uses `rgba(255,255,255,0.08)`, `#e5484d`, `#46a758`. Live emission proves the bucket wins at runtime (Phase 4: `--line:#1A1A1A`). Consequence: the Customizer's default preview for border/error/success differs from the actual emitted value whenever the option is unset — cosmetic but confusing round-trip (G10 regression risk). Recommend aligning the bridge to the bucket values (Phase 12-15). |
| F7-3 | LOW | **Orphan tokens.** `--radius-lg`, `--radius-pill` declared (static + dynamic) but consumed nowhere (repo-wide grep). `--surface-2`, `--surface-3`, `--muted` consumed only by theme demo page `page-styleguide.php` (not engine). `--aether-wc-subtext` emitted on explicit WC subtext set but never consumed (JS uses primary/highlight/price only). `--black` declared, never used. Harmless (no conflicts; emission is cheap) — prune only if desired in Phase 12-15. |
| F7-4 | LOW | `--chrome` is a documented alias of `--muted` (aether-tokens.php:84) — engine CSS consistently uses `--chrome`; no drift found. ✅ |

## 4. Verdict

Token set is internally consistent for 20/23 dynamic vars; no broken consumption; WC bridge only fires on explicit merchant customization (M2 intact). F7-1 + F7-2 are documentation/consistency debt that must be resolved in the Phase 12-15 change gate; F7-3/F7-4 optional cleanups. No immediate change required.