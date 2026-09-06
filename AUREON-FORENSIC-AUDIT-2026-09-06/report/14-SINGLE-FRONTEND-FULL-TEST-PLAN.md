# 14 — Single-Frontend Full Test Plan (Phase 28)

**Environment gate:** tests require a WP runtime with vineta active + seeded demo catalog. None exists in this repo — P0 prerequisite: stand up runtime (Docker or staging) and record it in `docs/forensics/runtime/`.

## 1. Static validation (no runtime)
- PHP lint all theme/plugin/pack PHP (`php -l`).
- `php -r` manifest JSON decode check.
- Node syntax check pack JS.
- Grep gates: no `enable_cod.php`/`update-contact.php` in deploy package; untracked-file check passes.
- Selector-contract suite (T-15): every frozen template used by the route map contains every selector the bridge depends on.

## 2. Route matrix (Playwright; 15 routes × 4 viewports 1440/1024/768/390)
Routes: `/`, `/shop/`, `/product/{demo-slug}`, `/product-category/{slug}`, `/search/?s=shirt`, empty search, `/cart/`, `/checkout/`, `/my-account/` (logged out + in), `/my-account/orders|edit-address|edit-account|downloads`, `/blog/`, `/blog/{post}`, `/about-us/`, `/contact-us/`, `/faq/`, `/404`, order-received (after test order).
Per route assert: HTTP 200, no PHP errors in log, page identity marker (not just 200 — e.g. route-specific title/VinetaPageData.page key), console error-free, net error-free.

## 3. Dynamic slots
- VinetaPageData present exactly once per route; schema keys per contract.
- Cart badge count consistency across header/drawer/mobile after each op.
- Menu splice renders WP menu (assert link hrefs equal WP menu items), desktop + mobile + footer.
- Logo bridge replaces SVG when custom_logo set; fallback SVG otherwise.
- Announcement bar reflects option; hero slides reflect repeater.

## 4. WooCommerce core flows
- Add simple product (grid + product page) → drawer + badge + cart page totals.
- Add variable product: select each attribute combo; unavailable combos disabled; wrong/missing variation rejected by AJAX (assert error JSON); cart line shows variation text.
- Quantity update / remove / subtotal recalculation; empty-cart redirect from checkout.
- Coupon apply/reject (if coupons enabled).
- Checkout: guest + logged-in; billing/shipping validation errors render; order creation succeeds; order-received shows correct totals; admin order exists.
- Currency formatting consistent across grid/drawer/cart/checkout.

## 5. Auth/account
- Register (new user), login (valid/invalid), logout, lost-password request + reset link flow.
- Endpoints render inside standalone account template; dashboard order counts correct.
- Logged-in flag absent from any cached HTML (see §9).

## 6. Search
- Exact match, partial match, sku, no-results empty state, suggestions dropdown, mobile search UI.

## 7. Menus
- Hierarchy/depth, active state, dropdown hover+keyboard, mobile toggle, footer menu, menu-not-assigned fallback.

## 8. Plugins/integrations
- aureon-studio active and non-fatal with pack.
- Newsletter: subscribe (AJAX + REST), duplicate email, unsubscribe, admin export authorized-only.
- Contact form submit → admin receives (mailpit/smtp stub), validation errors.
- Analytics events on view_item/add_to_cart/purchase hit dataLayer (tag-assistant or console assert).

## 9. Security
- Nonce failures: AJAX without/expired nonce → 403 JSON.
- Unauthenticated newsletter export → 403.
- REST endpoint permission checks; XSS probes in search/review/contact inputs rendered escaped.
- CSP: zero console violations on complete-page routes.
- No PHP notices/warnings on any route (WP_DEBUG log empty).

## 10. Cache/state
- Logged-in vs guest page differentiation under any page cache (config-dependent; document exclusions).
- bfcache: back-navigation keeps correct cart badge; no stale auth UI.
- Session cart persists across reload/login/logout boundary per WC rules.

## 11. Responsive/visual
- Per route × 4 viewports: no horizontal scroll ≥320px, mobile menu/drawer/popups usable, tap targets.
- Visual regression baseline (screenshot diff) after T-10 refactor.

## 12. Accessibility
- axe-core: zero critical violations per route.
- Keyboard: skip link present or equivalent, focus trap in drawer/modals, visible focus.
- Headings: exactly one H1 per page; logical order.
- Alt text on all content images; labels on all form fields.

## 13. Assets/performance
- No 404 assets across matrix; no duplicate jQuery; fonts self-hosted load; hero LCP image preloaded.
- Budgets: LCP < 2.5s, CLS < 0.1 on demo catalog (informational, not a release blocker).

## 14. Deployment/mail/payments
- Deployed tree hash = canonical tree git hash (no drift).
- wp-mail test (order admin notification, newsletter confirmation) via SMTP stub.
- Payment gateway smoke (COD first; others per server config) end-to-end order.

## Execution order
§1 → runtime gate → §2/§3 → §4–§8 → §9–§10 → §11–§12 → §13 → §14.

**Exit criteria for "production-ready":** all §2–§5 green twice (staging + production smoke), §9 fully green, security checks green, deployment hash check green. Anything else stays classified UNPROVEN — never converted to PASS without evidence.
