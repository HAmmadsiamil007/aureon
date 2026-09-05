# Vineta + Golden AUREON Integration — Deep Analysis & Questions

**Analysis Date:** 2026-09-02
**Analyst:** Buffy (Freebuff AI Agent)
**Status:** PRE-IMPLEMENTATION ANALYSIS

---

## EXECUTIVE SUMMARY OF FINDINGS

After deeply analyzing the entire codebase — including `composer.php` (1605 lines), `vineta-data-shims.js`, `vineta-path-bridge.js`, `manifest.json`, `tokens.php`, `design.php`, `ferm-page.php`, `frontend.php`, all existing test results, all docs/reports, and the complete file inventory — here is what I've found and what I need clarified before proceeding.

---

## SECTION 1: ARCHITECTURE UNDERSTANDING

### What I've Confirmed ✅

1. **Design Resolver** (`design.php:73`): `aether_active_design()` defaults to `fermliving`, but can be overridden via `AETHER_DESIGN` constant or `aether_active_design` option. Currently set to `vineta`.

2. **Complete-Page Mode**: `aether_is_complete_page_design()` reads `manifest.json['complete_page']`. Vineta has `complete_page: true`, which means:
   - `ferm-page.php` serves frozen HTML directly
   - AUREON shell (header.php/footer.php) is bypassed
   - Platform CDNs are suppressed (except on checkout/account pages)
   - Pack CSS/JS loaded via `aether_design_enqueue_assets()`

3. **Template Routing** (`frontend.php:998`): `aureon_ferm_template_include()` routes complete-page designs to `ferm-page.php`, EXCEPT:
   - Checkout → WC native template (priority 99)
   - Logged-in account → WC native template
   - Cart → WC native template (priority 99)

4. **Data Bridge** (`composer.php`): Vineta has a comprehensive data bridge that:
   - Maps WC data to Vineta format (products, categories, cart)
   - Handles demo product/category fallback
   - Injects product data on single product pages
   - Injects cart data on cart page
   - Builds `VinetaPageData` for all routes
   - Provides cart AJAX handlers (`vineta_cart_add`, `vineta_cart_update`, `vineta_cart_get`)
   - Provides `vineta_add_to_cart` handler for product pages

5. **Path Bridge** (`vineta-path-bridge.js`): Rewrites frozen HTML links to WP permalinks. Uses a MAP object with 50+ entries for flat-file → permalink mapping.

6. **jQuery Bridge**: Saves WP jQuery reference before frozen HTML scripts load, then restores it after Vineta jQuery loads. Copies Bootstrap plugins between jQuery instances.

7. **Base Tag**: Injected at priority 1 to resolve frozen HTML relative paths.

---

## SECTION 2: CRITICAL QUESTIONS

### Q1: Docker Environment State
**Question:** Is the Docker environment currently running? Can I interact with it (run `curl`, access `localhost:8080`, check MySQL)?
**Why:** I need to verify actual WC state changes (cart contents, order creation, user accounts) by querying the database or API. Without Docker access, I can only verify code correctness, not runtime behavior.
**Impact:** HIGH — runtime verification is the core requirement.

### Q2: Current WordPress/WC State
**Question:** What is the current state of the WordPress database?
- Are the test products (VTS-001 simple, VTV-001 variable) still in the database?
- Are there any existing WooCommerce orders?
- Are there any existing WordPress users (beyond admin)?
- What WordPress pages exist (Shop, Cart, Checkout, My Account, Blog, About, Contact, etc.)?
- What WordPress menus are assigned to the `primary` and `footer` locations?
- Are there any active plugins beyond WooCommerce?
**Why:** The acceptance gates require testing against real WC state. I need to know what exists before I can test or create test data.

### Q3: Active Design State
**Question:** Is `aether_active_design` currently set to `vineta` in the WordPress database? Was this persisted via `update_option()`, or is it set via a PHP constant?
**Why:** The design resolver reads from either the database option or a constant. I need to know which mechanism is active so I don't accidentally break activation.

### Q4: Frozen HTML CDN Images
**Question:** The Vineta pack's frozen HTML files reference images via `cdn/` paths (e.g., `cdn/images/products/fashion/product-1.jpg`). Are these images actually present in `aureon/frontend/designs/vineta/cdn/`? Or do they need to be downloaded/synced from somewhere?
**Why:** The existing Ferm audit shows many broken CDN images (404 errors). Vineta's frozen HTML references images that may or may not exist locally. If images are missing, the visual presentation will be broken.
**Evidence:** The `.gitignore` has `frontend/designs/*/cdn/` with an exception for `fermliving`. This suggests Vineta CDN images may not be in git.

### Q5: Checkout Template Routing
**Question:** When a user visits `/checkout/`, which template is actually served?
- `frontend.php:99` routes checkout to `aureon/theme/checkout/form-checkout.php` (WC native)
- `frontend.php:998` routes complete-page designs to `ferm-page.php`
- Priority 99 runs BEFORE priority 998
- BUT `aureon_ferm_template_include` at priority 998 has a guard: `if (is_checkout()) return $template;`

So the flow is:
1. Priority 99: `aureon_aether_wc_page_templates` → returns `checkout/form-checkout.php`
2. Priority 998: `aureon_ferm_template_include` → receives `checkout/form-checkout.php`, sees `is_checkout()`, returns it as-is

**Result:** Checkout uses WC native template, NOT frozen HTML. Is this correct?

### Q6: Cart Page Template Routing
**Question:** When a user visits `/cart/`, which template is served?
- `frontend.php:99` routes cart to `aureon/theme/cart.php`
- `frontend.php:998` routes complete-page designs to `ferm-page.php`

Flow:
1. Priority 99: returns `cart.php`
2. Priority 998: receives `cart.php`, does NOT have a cart guard (only checkout and logged-in account are guarded)

**Result:** Cart uses `cart.php` (AETHER shell), NOT `ferm-page.php`. But the manifest maps `cart` → `view-cart.html` (frozen HTML).

**Wait** — actually, looking more carefully at `aureon_ferm_template_include`:
```php
if ( function_exists( 'is_checkout' ) && is_checkout() ) {
    return $template;
}
```
It only guards checkout, not cart. So cart would go through to `ferm-page.php` if the template returned is `cart.php` and the function continues past the checkout guard.

Actually no — the function returns `$template` for checkout, meaning it returns the WC template. For cart, it falls through to the `ferm-page.php` return. So cart DOES use `ferm-page.php` (frozen HTML).

**Is this the intended behavior?** The frozen `view-cart.html` is served, and `vineta_inject_cart_data()` populates it with real WC cart data.

### Q7: Cart AJAX vs WC Native Cart
**Question:** The Vineta `composer.php` registers custom AJAX handlers (`vineta_cart_add`, `vineta_cart_update`, `vineta_cart_get`). These are SEPARATE from WooCommerce's native cart AJAX (`wc-ajax=add_to_cart`). Which cart mechanism should be used for:
- Adding items from the product page?
- Updating quantities on the cart page?
- The mini-cart/drawer?
- Cart count in the header?

Currently:
- Product page: uses `vineta_add_to_cart` (custom handler via `wp_ajax_vineta_add_to_cart`)
- Cart page: uses `vineta_cart_update` (custom handler)
- Mini-cart: opens via CSS class toggle, data from `vineta:cart-updated` event

**Is this architecture correct?** Or should we use WC native AJAX for some operations?

### Q8: Variation Attribute Handling
**Question:** In `vineta_wc_ajax_add_to_cart()`, the variation attributes are constructed like:
```php
$variation[ 'attribute_' . sanitize_title( $attr_name ) ] = $meta_val;
```
But the meta is retrieved using:
```php
$meta_val = get_post_meta( $variation_id, 'attribute_' . $attr_name, true );
```
Note: `$attr_name` here is the original attribute name (e.g., `pa_color`), but the variation array key uses `sanitize_title()` which might produce a different slug.

**Potential Bug:** If the original attribute name is `pa_color` but `sanitize_title('pa_color')` produces `pa-color` (with hyphen instead of underscore), the variation array won't match what WC expects.

**Is this a known issue?** Or does WC handle both formats?

### Q9: Price Display Consistency
**Question:** The `vineta_remap_product()` function converts prices to cents:
```php
$product['price'] = (int) round( (float) $clean * 100 );
```
But `vineta_inject_cart_data()` uses float prices:
```php
'price' => (float) $product->get_price(),
'line_total' => (float) $item['line_total'],
```
And the JavaScript displays them as:
```javascript
price.textContent='$'+it.price.toFixed(2);
```

**Is this intentional?** Product pages show prices in cents (2499 = $24.99), but cart page shows prices as floats ($24.99). This inconsistency could confuse users if both are visible simultaneously.

### Q10: Demo Product Lifecycle
**Question:** The `vineta_has_real_products()` function checks for products WITHOUT the `aureon_demo` meta key. But the test products (VTS-001, VTV-001) — were they created with `aureon_demo = 1`? Or are they "real" products that would hide demo content?
**Why:** If test products are marked as demo, the demo lifecycle test (0 real → demo visible, 1 real → demo hidden) would behave differently.

---

## SECTION 3: MISSING COMPONENTS ANALYSIS

### What's NOT Built Yet (Gaps)

1. **Checkout Page**: The frozen `checkout.html` exists but checkout uses WC native template. The frozen HTML is NOT used for checkout. However, `vineta_inject_cart_data()` only runs on cart page, not checkout. **Need:** Verify WC checkout form renders correctly with Vineta styling.

2. **Authentication Pages**: The frozen `account-page.html` exists for logged-out users. But:
   - Login form action is rewritten by `aureon_ferm_rewrite_paths()` (in `ferm-page.php`)
   - WooCommerce login nonce is injected
   - Lost password link is rewritten
   - **But:** No signup/registration form handling exists in Vineta's `composer.php`
   - **Need:** Registration form bridge, duplicate email handling, invalid login display

3. **Account Pages (Logged-In)**: When logged in, WC native template is used. No Vineta-specific styling. **Need:** Verify WC account pages work with Vineta CSS, or create account page bridge.

4. **WordPress Menus**: `vineta_get_nav_menu()` in `composer.php` reads WP nav menus and maps them to Vineta format. But:
   - Are the `primary` and `footer` nav menu locations actually assigned to real menus?
   - Do the menu items have correct URLs?
   - Are there dropdown/mega menu items?
   - **Need:** Verify menus exist and are correct.

5. **Search**: `vineta_search_data()` returns hardcoded suggestions. But the actual search functionality — does Vineta have a search page template? The frozen HTML has search UI, but:
   - How does the search form submit?
   - Where do results render?
   - Is there a WP search template for Vineta?
   - **Need:** Verify search routing and results display.

6. **Customizer Deep Integration**: `vineta-data-shims.js` handles:
   - Logo ✅
   - Announcement bar ✅
   - Social links ✅
   - Colors ✅
   
   But NOT:
   - Hero/banner content ❌
   - Category items ❌
   - Footer columns ❌
   - Newsletter heading/text ❌
   - USP items ❌
   - Typography/fonts ❌
   - Favicon ❌
   - Site title/description ❌
   
   **Need:** Extend `VinetaCustomizer` to handle all Customizer controls.

7. **Plugin Compatibility**: No plugin inventory exists. Need to:
   - List all active plugins
   - Test each plugin's frontend surface
   - Verify Vineta compatibility

8. **Responsive Testing**: No responsive test results exist for Vineta. Need to test at 1440, 1024, 768, 390.

9. **Accessibility**: No a11y audit exists for Vineta. Need to test headings, labels, keyboard nav, focus, ARIA.

10. **Performance**: No performance baseline exists. Need to measure page load, asset count, script count.

---

## SECTION 4: TECHNICAL CONCERNS

### TC1: jQuery Version Conflict
The frozen HTML loads its own jQuery (likely 3.x), while WordPress ships jQuery 3.7.1. The bridge saves/restores WP jQuery, but:
- Vineta's `main.js` depends on `jquery` and `aether-bootstrap-js`
- If `aether-bootstrap-js` is suppressed (complete-page mode), does `main.js` still work?
- **Manifest says:** `"deps": ["jquery", "aether-bootstrap-js"]` for main.js
- **But:** `aureon_aether_suppress_theme_output()` deregisters `aether-bootstrap-js` for complete-page designs

**This is a dependency conflict.** `main.js` declares a dependency on `aether-bootstrap-js`, but that script is suppressed. WordPress won't enqueue `main.js` if its dependency is missing.

**Wait** — looking at the manifest assets section:
```json
"js": [
    {"file": "js/main.js", "deps": ["jquery", "aether-bootstrap-js"], "priority": "after"}
]
```
These are pack assets enqueued by `aether_design_enqueue_assets()`. Does that function handle the dependency conflict?

### TC2: Bootstrap JS Suppression
`aureon_aether_suppress_theme_output()` deregisters `aether-bootstrap-js` for complete-page designs. But Vineta's frozen HTML includes its own Bootstrap JS via `<script>` tags in the HTML. So Bootstrap IS available, just loaded differently.

The jQuery bridge copies Bootstrap plugins from the frozen HTML's jQuery to WP jQuery. This should work, but it's fragile.

### TC3: Cart Page Multi-Item Handling
The `vineta_inject_cart_data()` JavaScript only handles the first cart item for quantity updates:
```javascript
var itemKey=c.items[0]?c.items[0].key:'';
```
If there are multiple items in the cart, only the first item's quantity can be updated. **This is a bug for multi-item carts.**

### TC4: Checkout Form Fields
The WC native checkout template renders its own form fields. But the frozen `checkout.html` has its own checkout form (Shopify-style). Since checkout uses WC native template, the frozen checkout HTML is NOT used. **Is this acceptable?** Or should the frozen checkout be adapted?

### TC5: Order Success Page
The manifest maps `order-complete.html` → `/checkout/`. But the WC thank-you page is at `/checkout/order-received/{order_id}/`. The `aureon_aether_wc_page_templates` function routes `order-received` to `thankyou.php`. **Does the Vineta thank-you page exist and work?**

### TC6: Image CDN Strategy
Vineta's frozen HTML references images via relative paths like `cdn/images/products/fashion/product-1.jpg`. The `<base>` tag rewrites these to absolute URLs. But:
- Are these images actually in the `vineta/cdn/` directory?
- Or do they need to be served from an external CDN?
- The `.gitignore` excludes `frontend/designs/*/cdn/` (except fermliving)

**This is critical** — if CDN images aren't present, the entire visual presentation breaks.

---

## SECTION 5: SCOPE & PRIORITY QUESTIONS

### SP1: What Should I Build vs. Verify?
The prompt says "analyze everything" and "create questions." Should I:
- (A) Only analyze and report findings (no code changes)?
- (B) Analyze AND fix issues I find?
- (C) Analyze, fix issues, AND implement missing gates?

### SP2: Testing Strategy
How should I test the gates?
- (A) Code review only (verify code correctness by reading)
- (B) Browser testing via Playwright (automated browser interactions)
- (C) Database verification (query MySQL to confirm WC state changes)
- (D) All of the above

### SP3: Golden AUREON Modifications
The prompt says "Golden AUREON = FROZEN — zero modifications." But some gates may require changes to:
- `aureon/theme/ferm-page.php` (if checkout/account routing needs adjustment)
- `aureon/theme/inc/frontend.php` (if asset suppression needs tuning)
- `aureon/frontend/views/design.php` (if design resolver needs updating)

**Are ANY modifications to Golden AUREON acceptable?** Or is this an absolute rule?

### SP4: Demo Lifecycle Priority
The updated prompt says "DEMO → REAL → DEMO is NOT a mandatory release blocker." Should I:
- (A) Skip demo lifecycle testing entirely?
- (B) Test it as regression/support verification?
- (C) Make it mandatory?

### SP5: Deliverables Format
The prompt requests:
- `test-results/VINETA-WORDPRESS-FULL-ACCEPTANCE-MATRIX.json`
- `docs/forensics/VINETA-WORDPRESS-INTEGRATION-REPORT.md`

Should these be:
- (A) Created as empty templates to be filled during testing?
- (B) Created with my analysis findings?
- (C) Created only after all gates pass?

---

## SECTION 6: DETAILED GATE-BY-GATE ANALYSIS

### Gate 1: CART COMPLETION
**Current State:**
- `vineta_inject_cart_data()` injects real WC cart data into frozen `view-cart.html`
- Quantity inc/dec buttons are hooked to `vineta_cart_update` AJAX
- Remove button is hooked to `vineta_cart_update` with quantity=0
- Total/subtotal displayed from WC data

**Issues Found:**
1. ❌ Multi-item quantity update only works for first item (hardcoded `c.items[0]`)
2. ❌ No subtotal display (only total is shown)
3. ❌ Cart count badge selectors may not match Vineta's HTML structure
4. ❌ No empty cart state handling (just replaces table innerHTML)
5. ❌ No persistence after reload (cart data is re-injected from WC on each load — this IS persistent via WC session)

**Verdict:** PARTIALLY BUILT — needs fixes for multi-item support and UI completeness.

### Gate 2: CHECKOUT
**Current State:**
- Checkout uses WC native template (`checkout/form-checkout.php`)
- No Vineta-specific checkout styling
- No custom checkout bridge in `composer.php`

**Issues Found:**
1. ❌ WC checkout form may not match Vineta's visual design
2. ❌ No checkout field customization
3. ❌ No payment method testing
4. ❌ No order creation verification
5. ❌ Thank-you page routing unclear

**Verdict:** NOT BUILT — relies entirely on WC native templates.

### Gate 3: AUTHENTICATION
**Current State:**
- Frozen `account-page.html` serves login form for logged-out users
- `aureon_ferm_rewrite_paths()` rewrites Shopify form fields to WC format
- WC login nonce is injected
- Lost password link is rewritten

**Issues Found:**
1. ❌ No registration/signup form in frozen HTML (only login)
2. ❌ No duplicate email handling
3. ❌ No invalid login error display
4. ❌ No logout mechanism
5. ❌ No forgot password workflow
6. ❌ No authenticated state detection in frozen HTML

**Verdict:** PARTIALLY BUILT — login works, everything else missing.

### Gate 4: ACCOUNT
**Current State:**
- Logged-in users use WC native template
- No Vineta-specific account styling

**Issues Found:**
1. ❌ No account dashboard customization
2. ❌ No orders display customization
3. ❌ No addresses management customization
4. ❌ No account details customization
5. ❌ No password change support

**Verdict:** NOT BUILT — relies entirely on WC native templates.

### Gate 5: WORDPRESS MENUS
**Current State:**
- `vineta_get_nav_menu()` reads WP menus and maps to Vineta format
- `VinetaPageData.navigation` includes main and footer menus
- `vineta-path-bridge.js` rewrites links

**Issues Found:**
1. ❓ Are WP menus actually assigned to `primary` and `footer` locations?
2. ❓ Do menu items have correct URLs?
3. ❓ Is there dropdown/mega menu support?
4. ❓ Are active states applied?
5. ❓ Are there stale Ferm links?

**Verdict:** CODE EXISTS — needs runtime verification.

### Gate 6: SEARCH
**Current State:**
- `vineta_search_data()` returns placeholder and suggestions
- No search results page template
- No search form handling

**Issues Found:**
1. ❌ No search results page in Vineta
2. ❌ Search form action not configured
3. ❌ No product search results display
4. ❌ No empty results handling

**Verdict:** NOT BUILT — only placeholder data exists.

### Gate 7: CUSTOMIZER
**Current State:**
- `vineta-data-shims.js` handles logo, announcement, social, colors
- `VinetaPageData.customizer` includes all Customizer values

**Issues Found:**
1. ❌ Hero/banner content not applied to DOM
2. ❌ Category items not applied to DOM
3. ❌ Footer columns not applied to DOM
4. ❌ Newsletter heading/text not applied to DOM
5. ❌ USP items not applied to DOM
6. ❌ Typography/fonts not applied
7. ❌ Favicon not handled
8. ❌ Site title/description not applied

**Verdict:** PARTIALLY BUILT — 4 of ~15 controls implemented.

### Gate 8: DEMO/REAL LIFECYCLE
**Current State:**
- `vineta_has_real_products()` checks for non-demo products
- `vineta_filter_demo_products()` filters WC queries
- `vineta_filter_demo_categories()` filters category terms
- Demo products/categories loaded from JSON files

**Issues Found:**
1. ❓ Are test products marked as demo?
2. ❓ Is the demo mode set to `auto`?
3. ❓ Does the lifecycle work correctly?

**Verdict:** CODE EXISTS — needs runtime verification.

### Gate 9: ISOLATION
**Current State:**
- `aureon_aether_suppress_theme_output()` deregisters platform assets for complete-page designs
- Pack assets loaded via manifest
- jQuery bridge manages version conflicts

**Issues Found:**
1. ❓ Are any Ferm CSS/JS/DOM remnants present?
2. ❓ Are only Vineta assets loaded?
3. ❓ Is the jQuery bridge working correctly?

**Verdict:** CODE EXISTS — needs runtime verification.

### Gate 10: SECURITY
**Current State:**
- `check_ajax_referer()` on all cart handlers
- Nonce verification on add-to-cart
- Product status/stock validation
- Demo product purchase prevention

**Issues Found:**
1. ❓ Are nonces being verified on all frontend mutations?
2. ❓ Is authorization checked for account operations?
3. ❓ Is input sanitization adequate?
4. ❓ Are invalid IDs/operations handled?

**Verdict:** PARTIALLY BUILT — cart security exists, other areas need audit.

### Gate 11: FULL REGRESSION
**Current State:** No regression test exists for Vineta.

**Verdict:** NOT BUILT — needs comprehensive testing.

---

## SECTION 7: PRIORITIZED ACTION ITEMS

Based on my analysis, here is what needs to happen in order:

### Priority 1: Environment & State Verification
1. Verify Docker is running and accessible
2. Query WordPress database for current state
3. Verify Vineta is active design
4. Verify test products exist
5. Verify WordPress pages exist
6. Verify WordPress menus are assigned

### Priority 2: Fix Critical Bugs
1. Fix multi-item cart quantity update (currently only first item)
2. Fix potential attribute slug mismatch in variation add-to-cart
3. Fix price display consistency (cents vs floats)

### Priority 3: Build Missing Gates
1. Search functionality
2. Checkout integration (verify WC native template works with Vineta)
3. Authentication (signup, error handling, logout, forgot password)
4. Account pages (verify WC native templates work)
5. Customizer deep integration (hero, categories, footer, newsletter, typography)
6. WordPress menus (verify assignment and rendering)

### Priority 4: Runtime Testing
1. Cart completion (all sub-gates)
2. Checkout (real order creation)
3. Authentication (real user creation)
4. Customizer (all controls)
5. Demo lifecycle
6. Isolation
7. Security
8. Responsive
9. Accessibility
10. Performance

### Priority 5: Documentation
1. Create acceptance matrix JSON
2. Create integration report MD

---

## SECTION 8: RISK ASSESSMENT

| Risk | Severity | Likelihood | Impact |
|------|----------|------------|--------|
| CDN images missing | HIGH | HIGH | Visual presentation completely broken |
| jQuery conflict | HIGH | MEDIUM | Bootstrap plugins fail, modals/dropdowns broken |
| Multi-item cart bug | HIGH | HIGH | Cart page unusable for multi-item orders |
| Checkout not styled | MEDIUM | HIGH | WC native checkout looks inconsistent |
| Search not built | MEDIUM | HIGH | Search functionality completely absent |
| Auth incomplete | HIGH | MEDIUM | Users can't register or recover passwords |
| Customizer partial | MEDIUM | MEDIUM | Most Customizer controls have no effect |
| Menu not assigned | HIGH | MEDIUM | Navigation completely broken |
| Price inconsistency | LOW | HIGH | Confusing price display |
| Security gaps | HIGH | LOW | Potential vulnerabilities |

---

## SECTION 9: QUESTIONS FOR YOU

Please answer these questions so I can proceed with the correct approach:

1. **Can I access the Docker environment?** (run commands, query DB, access localhost:8080)

2. **What is the current WordPress database state?** (users, products, pages, menus, plugins, options)

3. **Are Vineta CDN images present?** Or do they need to be downloaded?

4. **Should I fix bugs I find, or only report them?**

5. **Should I use Playwright for browser testing?**

6. **Is Golden AUREON truly frozen, or can I make minor adjustments?**

7. **What is the current `aether_active_design` option value?**

8. **Are the test products (VTS-001, VTV-001) in the database?**

9. **What WordPress menus exist and are they assigned?**

10. **Should I create the deliverables now or after testing?**

---

## SECTION 10: MY RECOMMENDATION

Based on my deep analysis, here is my recommended approach:

### Phase 1: Environment Discovery (30 min)
- Query Docker/MySQL for current state
- Verify what exists and what doesn't
- Create a baseline document

### Phase 2: Bug Fixes (1-2 hours)
- Fix multi-item cart quantity update
- Fix price display consistency
- Fix attribute slug handling

### Phase 3: Missing Gate Implementation (4-6 hours)
- Search functionality
- Customizer deep integration
- Auth completion (signup, error handling)
- Menu verification/fix

### Phase 4: Runtime Testing (4-6 hours)
- Playwright browser testing for all gates
- WC state verification via database
- Real order creation testing

### Phase 5: Documentation (1-2 hours)
- Acceptance matrix
- Integration report

**Total estimated effort: 10-15 hours**

---

*This analysis was generated by Buffy (Freebuff AI Agent) after reading all 2332 indexed project files, 1605 lines of composer.php, both JS bridge files, manifest.json, tokens.php, design.php, ferm-page.php, frontend.php, all existing test results, and all documentation.*
