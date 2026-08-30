# CLIENT FRONTEND FORENSIC REPORT — <Client Name>

> **Prepared:** YYYY-MM-DD · **Intake ref:** P1 · **Package version:** vX.Y.Z
> **Tier classification (A/B/C/D):** ______
> **Verdict:** ACCEPT / REBUILD-SCOPED / REJECT — reason: ______

## 1. Package summary

| Item | Value |
|---|---|
| Source | (URL / zip / repo) |
| Tech | (static HTML / templated / SPA / framework) |
| Pages | (count + list) |
| Dependencies | (libraries, CDNs, versions) |
| Fonts / Icons | (families, license) |
| Assets | (images count/size, videos, favicons) |
| Licensing | (code, assets, fonts — verified) |

## 2. Page inventory

| Page | URL/source | Layout notes | Shared components used |
|---|---|---|---|
| Home | | | |
| Shop | | | |
| Product | | | |
| Cart | | | |
| Checkout | | | |
| Account | | | |
| Blog / single | | | |
| Contact | | | |
| 404 / misc | | | |

## 3. Shared component inventory (client side)

| Client component | Occurrences | Maps to AETHER id (reuse/extend/new) | Data needed (DATA_CONTRACT key) |
|---|---|---|---|
| | | | |

## 4. Navigation & shell

- Header structure / menu depth / mobile behavior: ______
- Footer structure: ______
- Announcement/marquee/promo elements: ______

## 5. Forms & interactions

| Form / behavior | Client implementation | AETHER contract counterpart |
|---|---|---|
| Search | | aetherAjax.searchUrl |
| Add to cart | | AJAX add-to-cart |
| Cart qty | | qty stepper |
| Newsletter | | newsletter handler |
| Contact | | contact handler |
| Account/login | | account endpoints |

## 6. WooCommerce surfaces

- Product card layout: ______ (vs card/product)
- Single product layout: ______ (vs product/* components)
- Cart page: ______ (vs cart/items + cart/summary)
- Checkout/thankyou: ______ (vs order/confirmation)
- Account pages: ______ (vs account/*)

## 7. Animations & motion

| Effect | Client mechanism | AETHER behavior attr / KEEP-ADAPT-REPLACE-MOVE-REMOVE |
|---|---|---|
| | | |

## 8. Responsive behavior

- Breakpoints used: ______
- Mobile nav pattern: ______
- Known issues at 390/375: ______

## 9. Token mapping (draft)

| Contract token | Client value | Notes |
|---|---|---|
| `--gold` (accent) | | |
| `--surface` | | |
| `--font-heading` | | |
| `--font-body` | | |
| spacing/radius | | |

## 10. Risks & blockers

| # | Risk | Severity | Mitigation |
|---|---|---|---|
| | | | |

## 11. Decision & follow-ups

- Tier: ______ · Recommended milestone: ______
- Client approval required before: ______
- Follow-up tasks: ______