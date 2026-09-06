# FRONTEND REDESIGN — COMPARISON MATRIX (CURRENT vs APPROVED VINETA REFERENCE)

**Date:** 2026-09-04 · Stage 3 of the redesign prompt
**Method:** byte/line comparison of the current canonical pack (`aureon/frontend/designs/vineta/`, 46 templates) against the approved reference (`C:\Users\hamma\Downloads\vinetahtml-10\…\vineta-ready-for-wordpress`, Vineta HTML v1.0.3, 108 templates). Baseline: `docs/forensics/FRONTEND-REDESIGN-PRE-BASELINE.md`.

## 1. HEADLINE FINDING

The current pack **is** the approved reference family: 46 of the 46 current templates exist in the reference. **31 are byte-identical**; **15 carry surgical integration edits** (same line counts). The reference's own acceptance matrix (2026-09-02) already scored the package `VINETA_PREMIUM_TEMPLATE_100_PASS`.

⇒ "Redesign" here is **not** adopting a foreign design system — it is a **visual refinement pass on the existing approved family**, executed on the client's current identity (black/white monochrome, Playfair Display / Albert Sans, white masthead). The reference remains the component/structural source for any rebuilt sections.

## 2. TEMPLATE-LEVEL DELTA (current vs reference)

| Template | Changed-line pairs | Nature of edit (bridge/integration) |
|---|---:|---|
| `index.html` | 550 | Homepage customized for the client (SO content): BOM removed, hero `<h2>`→`<h1>` (a11y), search link aria fix, SUMMER-SALE countdown banner replaced by honest brand sections; bridge slots + `VinetaPageData` wiring |
| `product-detail.html` | 42 | H1/a11y + price slot/currency hooks |
| `shop-default.html` | 34 | Grid/card + search placeholder hooks |
| `cart-drawer-v2.html` | 34 | Cart drawer contract hooks (`#shoppingCart`) |
| `blog-single.html` / `blog-grid-01.html` | 32 / 28 | Blog payload consumers (`entry_*`), H1 |
| `account-page.html` / `404.html` | 28 / 28 | Account/404 payload + H1 |
| `checkout.html` | 26 | Checkout presentation hooks |
| `view-cart.html`, `shop-collection-list.html`, `product-description-tab.html`, `faq.html`, `contact-us.html`, `about-us.html` | 1–2 each | Minor aria/encoding/format touches |
| remaining 31 templates | 0 | **Byte-identical to reference** (incl. `wish-list`, `thank-you`, `term-and-condition`, all newsletter-popups, product variants not used by current routes) |

Notes: current files show non-ASCII display loss in console diffs (Arabic/Chinese option labels + `�`) — possible encoding mojibake from an earlier pass; flagged as an open item to verify at runtime (cosmetic, header language selector only).

## 3. COMPONENT MATRIX (current ↔ reference ↔ redesign surface)

| Component | Current structure (= reference family + SO edits) | Visual delta to reference | Dynamic dependency (bridge selectors — see CONTRACT-FREEZE doc) | Customizer | WC/Menu/Plugin | Redesign risk |
|---|---|---|---|---|---|---|
| Announcement bar | dark strip (`#111`) via override | — | topbar text slots | announcement items | none | Low |
| Header / masthead | white surface, black ink (SO logo), sticky | — | `.nav-icon-item`, cart badge `.count-box`, search modal | logo/site title | WP primary menu | **Medium** (menu DOM + search + cart badge) |
| Mobile nav | canvas from desktop menu DOM | — | menu DOM rebuild client-side | — | WP primary | Medium |
| Hero (home) | full-bleed image, dynamic slides, single visible H1 | countdown banner already removed for SO | `.image img`, `.swiper-wrapper`, H1/heading, CTA, `.sub` | hero slides | none | **High visual** (composition) / low contract |
| Category cards | image + label tiles | — | `.card-product` fallback chain, `.on-sale-item` | — | real terms | Medium |
| Product cards | `.card-product` grid cards | — | `.card-product`, `.new-price/.old-price`, badge-sale, image, add-to-cart form | colors | real WC products | **High visual** / contract must hold |
| Product page | gallery + info layout | — | `.tf-product-info-*`, `.info-variant`, gallery, price/SKU/stock | — | real WC product | High visual |
| Shop grid/filters | grid + filter UI | — | cards, filters, pagination | — | real WC catalog | Medium |
| Cart drawer/page | `#shoppingCart`, `.table-page-cart` | — | items, totals, qty +/− , remove, subtotal | — | real WC session | High visual / contract must hold |
| Checkout | WC-native forms in Vineta chrome | — | form names/actions/nonces preserved | — | WC checkout | High visual |
| Auth/account | popups + dashboard | — | login/register/reset forms, `.s-term-user .content` | — | WC auth | Medium |
| Search | modal + results | — | real `?s=` results | placeholder | WP search | Low |
| Blog/static/404 | post cards, WP page content | — | `entry_*`, `.s-term-user .content`, 404 links | — | WP content | Low |
| Footer | white surface, WP footer menu, newsletter, social | — | `.footer-*` fallback, `.form-newsletter`, newsletter heading | newsletter/social | WP footer menu | Medium |
| Modals/drawers/newsletter popups | offcanvas + modals | — | aria, forms, nonces | — | — | Low |

## 4. CSS LAYER ANALYSIS (the primary redesign vehicle)

Manifest load order: `bootstrap.min.css` → `swiper-bundle.min.css` → `animate.css` → `styles.css` (23,763 lines, family base) → **`monochrome-black.css` (225 lines, SO identity override, loaded last)**.

- Current identity = thin override patching surfaces (page base, topbar, masthead, nav ink, buttons, cards, footer).
- A redesign that preserves identity + contracts should **evolve `monochrome-black.css` into a full premium design layer** (tokens, type scale, spacing, component treatments) and only touch template markup where composition genuinely changes.

## 5. ROUTE-LEVEL FACTS (runtime, verified 2026-09-04)

`/` 200 · `/shop/` 301→`/shop` (canonical no-slash; resolves) · Docker `wordpress-*` containers healthy.

## 6. RISK & PROTECTION SUMMARY

1. Bridge selector vocabulary is the real contract (see CONTRACT-FREEZE doc §4) — keep classes or extend fallback chains.
2. `index.html` homepage is the most client-customized file (550 diff lines) — treat as hand-edited, not replaceable wholesale.
3. Forms carry names/actions/nonces (login/register/cart/checkout/newsletter) — preserve verbatim.
4. Golden Core + engine + plugin: protected fingerprint in `RELEASE-CANDIDATE-MANIFEST.json` (Stage 30 compares against it).
5. Runtime verification required per stage (Docker now up).
