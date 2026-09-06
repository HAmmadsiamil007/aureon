# PHASE 10 — FAILURE INJECTION

> **Phase:** 10 · **Date:** 2026-08-14 · **Method:** live fault injection on the Docker stack — every mutation executed via PHP `update_option()` with verified restore; responses probed with `Invoke-WebRequest` + Playwright DOM checks
> **Scope:** ① demo-gate flip ② hostile option values (CSS/color injection) ③ WooCommerce deactivation (worst-case integration failure)
> **Result:** 3/3 injections survived — no fatals, no XSS/CSS breakout, graceful degradation confirmed. 1 INFO finding.

---

## 1. Injection matrix

| # | Injection | Method | Result |
|---|---|---|---|
| 1 | `aether_demo_content=false` | option flip (restored after) | Home: hero slides + categories + bestsellers intact (real content); demo testimonials (F3 demo block) correctly removed; newsletter intact. Shop: **real** product grid persists (9 cards: "[W] Denim Trucker Jacket", "Bifold Wallet" ×3, $281.00…). Demo gate strips fallbacks only — real data never gated. ✅ |
| 2 | Hostile option values: `aether_color_accent = 'red; background:url(javascript:alert(1)); --pwned:'` and `aether_section_padding = '100px 0; } body { display:none } .x {'` | option write (restored after) | Token block emitted with **all payloads neutralized**: `--gold` fell back to `#C8956C` (hex/rgb/var whitelist in `aether_sanitize_color` rejected `red; url(...)`); `--section-padding: 100px 0  body  display:none  .x ` — `aether_token_css_value` char whitelist stripped `; { } < > =` so no declaration breakout and no `:root` closure. Live DOM: `body {display:block; visibility:visible}`, h1 visible — page fully functional. ✅ |
| 3 | WooCommerce **deactivated** (active_plugins flip; restored) | plugin toggle | **Zero fatals** on all surfaces: `/` 200 (hero, categories, bestsellers header, newsletter all render); `/shop/` 301 (WC page gone → graceful redirect); product URL 301; `/cart/` 200, `/checkout/` 200, `/my-account/` 200 (WC page slugs render as normal pages, no `wc_*` calls). Bestsellers grid hides gracefully (adapter early-return at `adapter-wc-products.php:16-21` — no demo products shown because product fallback lives after the WC guard; acceptable: store empty ≠ fake data). ✅ |

## 2. Findings

| ID | Sev | Finding |
|---|---|---|
| F10-1 | INFO | **Pre-reveal snapshot artifact (not a bug).** During the `demo=false` shop probe the grid first appeared empty — cards were actually present but frozen at `opacity:0; filter:blur(6px); visibility:hidden` awaiting the scroll-reveal observer. Scrolling triggered reveal (6/9 visible in viewport, rest staged). Any screenshot/DOM probe taken without scrolling misreads the grid as empty; the E2E suite already scrolls (visual.spec) — document this for future probes. |

## 3. Verdict

The engine's three defensive layers held under live failure:
1. **Demo gate** — fallbacks never mask real data; toggling it off is safe (F3-6 reconfirmed under load).
2. **CSS sanitizers** — `aether_sanitize_color` (hex/rgb/var only) + `aether_token_css_value` (character whitelist) make option→`:root` injection inert; no CSS breakout possible.
3. **WC guards** — `function_exists`/`class_exists` guards + early returns hold with the plugin fully disabled; site remains a functioning storefront skeleton with zero PHP errors (verified `Fatal error`/`Uncaught` absent in all responses).

All state restored to baseline (demo=true, options clean, WC active). No change required.