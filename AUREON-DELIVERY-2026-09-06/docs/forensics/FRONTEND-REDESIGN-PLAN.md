# FRONTEND REDESIGN — PLAN

**Date:** 2026-09-04 · Stage 5 of the redesign prompt
**Direction (approved):** *keep the client's approved black/white monochrome identity; rebuild presentation/structure guided by the approved Vineta reference; preserve every dynamic contract.*
**Baseline/contracts:** `FRONTEND-REDESIGN-PRE-BASELINE.md` · `FRONTEND-REDESIGN-COMPARISON-MATRIX.md` · `FRONTEND-REDESIGN-CONTRACT-FREEZE.md`
**Every change in this plan is classified `FRONTEND_ONLY`.** No bridge update is anticipated; if one becomes necessary (a selector dies), STOP and update `vineta-data-shims.js` + re-run targeted tests before continuing.

## 1. IMPLEMENTATION VEHICLE

1. **Design layer (primary):** evolve `aureon/frontend/designs/vineta/css/monochrome-black.css` (or add `redesign-layer.css` loaded after it) into a complete premium monochrome design system — tokens, typography scale, spacing rhythm, buttons/forms, cards, header/footer, hero, drawers, focus states. CSS-only = applies to all 46 templates, zero DOM risk.
2. **Targeted markup refinement (secondary):** only where composition genuinely changes — homepage hero/section order, header polish, product card markup, cart-drawer styling hooks — preserving contract classes §4 of the CONTRACT-FREEZE doc.
3. Template edits applied to the **frozen standalone templates** directly (pack architecture: full markup per page), keeping `<base href>`, raw-body scripts, `VinetaPageData`, forms/nonces intact.

## 2. DESIGN LANGUAGE (monochrome premium — client identity preserved)

- **Canvas:** near-black `#0a0a0a` content surface, white `#fff` masthead + footer (SO logo is black), `color-scheme: dark`.
- **Type:** Playfair Display for display (hero/headings), Albert Sans for UI/body (both already injected by Customizer); refine scale/leading/letter-spacing.
- **Color accents:** one restrained accent (Customizer `--primary`, currently #3A7BFF) used only for focus/CTA/price emphasis.
- **Surfaces:** hairline `#222`/`#ececec` borders; soft layered cards on dark; generous whitespace.
- **Motion:** subtle fades/slides (existing wow/swiper), hover lifts on cards, no flashy effects.
- **A11y:** 4.5:1+ contrast, visible focus rings, aria preserved.

## 3. PER-COMPONENT PLAN

| # | Component | Old presentation → New presentation | Contract unchanged | Bridge impact | Tests |
|---|---|---|---|---|---|
| 1 | Design tokens/base | patchwork overrides → CSS-variable token block (`--aether-bg/ink/surface/line/radius/type-scale`) + base resets | all | none | visual at 4 widths |
| 2 | Announcement bar | dark strip → refined hairline topbar, consistent spacing | topbar slots | none | text swap |
| 3 | Header/masthead | current sticky white bar → cleaner height/typography/spacing, sticky, hover underline on WP menu, badge restyle | menu DOM, icons, `#search`, cart badge `.count-box`, logo | none | menu change propagation; cart badge live |
| 4 | Mobile nav | canvas → improved drawer styling, tap targets ≥44px | client-side menu rebuild | none | 390/768 |
| 5 | Homepage hero | full-bleed image + overlay → refined gradient/overlay, larger display type, clearer CTA, single visible H1 | slides/image/heading/sub/CTA/URL | none | SET→RELOAD→verify; width checks |
| 6 | Homepage sections | current SO sections → recomposed: categories, featured products, editorial band, newsletter, footer; remove nothing dynamic | product/category/newsletter slots | none | data-change reload |
| 7 | Product card | current card → premium card: type scale, price emphasis (new/old), badge chip, image zoom/hover, add-to-cart button state | `.card-product` + inner contract classes + forms/nonces | none | add-to-cart; sale badge; card data |
| 8 | Shop grid/filters/pagination | current → unified grid gutters, filter/sort/pagination styling | real catalog, context, empty state | none | route + filter + pagination |
| 9 | Product page | current → refined gallery (thumbnails), info column rhythm, qty/CTA, meta, related | title/price/SKU/stock/gallery/variation/qty/atc/related + `.info-variant` | none | product route; variation N/A (no var products) |
| 10 | Cart drawer/page | current → consistent item/totals/empty states, qty steppers | `#shoppingCart`, `.table-page-cart`, totals, forms | none | add→drawer→cart→remove; totals CHF |
| 11 | Checkout | WC-native in Vineta chrome → styling pass only (fields/buttons/summary) | WC fields/actions/nonces | none | checkout page + order create (sandbox) |
| 12 | Auth/account | popups + dashboard → styling pass, error/success notices | forms, nonces, `.s-term-user .content` | none | login/register/logout flows |
| 13 | Search | modal/results → styling pass | real `?s=` | none | search route |
| 14 | Blog/static/404 | current → consistent type/space polish | `entry_*`, page content region, 404 links | none | routes |
| 15 | Footer/newsletter/social | current white footer → refined columns, newsletter input/button, socials | WP footer menu, `.form-newsletter`, newsletter heading, social | none | menu + newsletter |
| 16 | Modals/popups/forms | current → unified control styling, focus, aria | dialog behavior, `cursor-close.svg` | none | a11y spot |

## 4. CHANGE CLASSIFICATION

All rows above: **FRONTEND_ONLY**. No `BRIDGE_UPDATE_REQUIRED`, no `CORE_REVIEW_REQUIRED` entries are planned. If any template edit removes/renames a §4 selector or an ID/form, escalation rules apply (documented in CONTRACT-FREEZE §6).

## 5. EXECUTION ORDER & VERIFICATION GATES

1. Design layer CSS (tokens → chrome → components) — verify visually on home/shop/product/cart/account/blog/404 at 1440/1024/768/390, console zero-new.
2. Header/footer/announcement refinement across templates (multi-file identical-block edits) — menus + Customizer round-trip.
3. Homepage composition (hero + sections) — hero SET/SAVE/RELOAD; featured/category data reload.
4. Product card + shop grid + product page — add-to-cart, badge, currency, gallery.
5. Cart drawer + cart page + checkout styling — full cart flow.
6. Auth/account + search + blog/static/404 styling.
7. A11y + responsive + console/network pass at 4 widths.
8. Customizer SET/RESET round-trips; dynamic-data change test (product/menu/logo/color/typography change → same UI updates).
9. Feature-loss + Core-integrity diff (vs protected fingerprint) + cleanup.
10. Stage 33 deliverables: `docs/forensics/FRONTEND-REDESIGN-FINAL-REPORT.md` + `test-results/FRONTEND-REDESIGN-ACCEPTANCE-MATRIX.json` → `VINETA_FRONTEND_REDESIGN_PASS` or `_BLOCKED`.

## 6. CONSTRAINTS REAFFIRMED

- No bridge/engine/Core/plugin changes unless a generic defect is proven (CORE-CHANGE-REQUEST.md gate).
- No restoring demo/static values; no hardcoding WC/customer/cart/menu data.
- Golden Copy frozen; deploy mirror untouched until redesign acceptance; no commits/push without approval.
