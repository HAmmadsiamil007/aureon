# Ferm Living Demo ↔ Real Client State-Transition Acceptance Test — Execution Plan

## Architecture Understanding

The demo↔real switching is governed by **three mechanisms** in `composer.php`:

1. **`aether_demo_mode`** option — `auto` / `force_demo` / `disabled`
2. **`ferm_has_real_products()`** — checks for published products where `aureon_demo` meta ≠ `1`
3. **`ferm_has_real_categories()`** — checks for published categories where `aureon_demo_category` meta ≠ `1` AND `hide_empty=true` (must have products)

When real products exist → `ferm_filter_demo_products()` injects meta_query to exclude `aureon_demo=1` products from all front-end WC queries.

When real categories exist → `ferm_filter_demo_categories()` filters out `aureon_demo_category=1` from `get_terms()` results.

**Current state:** `auto` mode, 3 real WC products (Uncategorized), 15 demo categories (all empty, all `aureon_demo_category=1`), no custom logo/hero/heading.

---

## Phase Grouping

### Group A: Baseline & Clean State (Phases 0-4)
**Goal:** Establish clean starting point, verify demo state works.

- **Phase 0:** Record git state, current option values, create QA checkpoint
- **Phase 1:** Remove all 3 existing WC products (restore clean new-client state). Verify zero real products → demo products visible
- **Phase 2:** Verify demo products have title, image, price, category, URL
- **Phase 3:** Verify demo categories visible (all 15)
- **Phase 4:** Verify demo products cannot be purchased (cart safety)

### Group B: Real Product Transition (Phases 5-8)
**Goal:** Prove one real product hides ALL demo products.

- **Phase 5:** Create "AUREON QA Real Product" via WP Admin (published, price, SKU, stock, featured image uploaded through WP Media)
- **Phase 6:** Reload site → verify ALL demo products disappear globally (homepage, shop, search, categories)
- **Phase 7:** Verify real product detail (title, image, price, SKU, stock, URL, add-to-cart works)
- **Phase 8:** Verify real product image appears on product card, detail, related, search

### Group C: Product Restoration (Phase 9)
**Goal:** Prove removing last real product restores demo.

- **Phase 9:** Delete the QA product → verify demo products automatically return

### Group D: Real Category Transition (Phases 10-12)
**Goal:** Prove one real category hides ALL demo categories.

- **Phase 10:** Create "AUREON QA Real Category" via WC Admin (with at least 1 product so `hide_empty` doesn't hide it)
- **Phase 11:** Reload → verify ALL demo categories disappear, real category visible
- **Phase 12:** Remove real category → verify demo categories return

### Group E: Customizer Transitions (Phases 13-15)
**Goal:** Prove logo/hero/heading can be set and removed.

- **Phase 13:** Logo: no custom → demo logo → upload custom → verify → remove → demo returns → delete media → fallback
- **Phase 14:** Hero: demo hero → set custom → verify → remove → demo returns
- **Phase 15:** Heading: demo heading → set custom via option → verify → remove → demo returns

### Group F: Combined & Full Restoration (Phases 16-17)
**Goal:** Prove all real content together, then full restoration.

- **Phase 16:** Create real product + real category + custom logo + custom hero + custom heading → verify all real content
- **Phase 17:** Remove all → verify FULL demo state returns

### Group G: Regression (Phases 18-24)
**Goal:** Verify nothing breaks across states.

- **Phase 18:** Menu regression across all states
- **Phase 19:** Search regression (demo → real → demo)
- **Phase 20:** Image integrity across states
- **Phase 21:** Cache/state consistency after each change
- **Phase 22:** Responsive at 1440/1024/768/390
- **Phase 23:** Network audit (no Shopify business calls)
- **Phase 24:** Console clean

### Group H: Admin & Safety (Phases 25-26)
**Goal:** Prove admin operations work and demo data is non-destructive.

- **Phase 25:** Admin can create/delete products, categories, upload/remove logo, update/remove hero
- **Phase 26:** Demo JSON files remain untouched after all transitions

### Group I: Final (Phases 27-35)
**Goal:** Clean up, final regression, reports.

- **Phase 27:** Remove all QA fixtures
- **Phase 28:** Final clean-client demo regression
- **Phase 29:** Final route matrix (all routes)
- **Phase 30:** Final image matrix (all images)
- **Phase 31:** Final performance check (pack size, image count)
- **Phase 32:** Golden Core integrity check
- **Phase 33:** Final report (`FERM-DEMO-REAL-CLIENT-TRANSITION-FINAL-REPORT.md`)
- **Phase 34:** Machine-readable test matrix (`FERM-DEMO-REAL-CLIENT-TRANSITION-MATRIX.json`)
- **Phase 35:** Final acceptance verdict

---

## Test Implementation

### Tools
- **Playwright** (`full-audit-v2.js` pattern) — browser automation for visual/functional verification
- **WP-CLI** via Docker — product/category/option management
- **PowerShell** — orchestration

### Test Script Architecture
Create `transition-test.js` — a Playwright script that:
1. Navigates to each page
2. Counts products (demo vs real)
3. Counts categories (demo vs real)
4. Checks logo/hero/heading state
5. Verifies no broken images
6. Captures screenshots for evidence

### WP-CLI Commands for Each Transition

**Create real product:**
```
wp wc product create --name="AUREON QA Real Product" --regular_price="199" --sku="QA-REAL-001" --status=publish --user=1
wp media import <image-path> --title="QA Product Image" --porcelain  # get attachment_id
wp wc product update <id> --images=[{id: <attachment_id>}] --user=1
```

**Delete real product:**
```
wp wc product delete <id> --force=true --user=1
```

**Create real category:**
```
wp wc product_cat create --name="AUREON QA Real Category" --slug="aureon-qa-real" --user=1
```

**Delete real category:**
```
wp wc product_cat delete <id> --force=true --user=1
```

**Set custom logo:**
```
wp media import <logo-file> --title="QA Client Logo" --porcelain
wp option update theme_mods_aureon '{"custom_logo":<attachment_id>,"custom_css_post_id":-1}'
```

**Remove custom logo:**
```
wp option update theme_mods_aureon '{"custom_css_post_id":-1}'
```

**Set custom heading:**
```
wp option update aether_site_heading "My Custom Heading"
```

**Remove custom heading:**
```
wp option delete aether_site_heading
```

**Clear static caches (critical between transitions):**
```
wp cache flush --allow-root
# Also need to reset ferm_has_real_products() static cache
# This requires a page load (static var resets per request)
```

---

## Critical Implementation Detail

The `ferm_has_real_products()` and `ferm_has_real_categories()` functions use **static variables** that persist within a single PHP request. This means:
- The result is cached for the duration of one page load
- A new page load will re-evaluate
- WP-CLI commands run in a separate PHP process, so they don't affect the static cache
- After creating/deleting products via WP-CLI, the next browser page load will re-evaluate correctly

**No special cache-busting needed** — just reload the page after each WP-CLI operation.

---

## Execution Order

1. Create `transition-test.js` (Playwright test script)
2. Execute Group A (baseline)
3. Execute Group B (real product creation)
4. Execute Group C (product restoration)
5. Execute Group D (category transitions)
6. Execute Group E (customizer transitions)
7. Execute Group F (combined state)
8. Execute Group G (regression)
9. Execute Group H (admin & safety)
10. Execute Group I (final)
11. Generate reports

---

## Risk Areas

1. **Static cache in `ferm_has_real_products()`** — could cause stale results if same request is cached. Mitigation: always do full page reload, not AJAX.

2. **`hide_empty=true` in `ferm_has_real_categories()`** — a real category with 0 products won't be detected as "real". Mitigation: assign at least 1 product to the real category.

3. **Demo categories already exist as WC categories** — the 15 demo categories are actual WC categories with `aureon_demo_category=1` meta. Creating a "real" category means creating a NEW category without that meta.

4. **`woocommerce_product_query` hook only filters front-end** — admin always sees all products. This is by design.

5. **Hero/heading Customizer controls may not exist in UI** — `aether_site_heading` has no Customizer field registered. Must use WP-CLI or direct option update.

---

## Expected Outcomes

| Phase | Expected | Pass Criteria |
|-------|----------|---------------|
| Demo products visible | 66 demo products | Homepage, shop show demo products |
| Real product hides demo | 0 demo products visible | Only real product shows |
| Demo returns after delete | 66 demo products | Automatic restoration |
| Demo categories visible | 15 demo categories | Category nav shows all |
| Real category hides demo | Only real category | Demo categories hidden |
| Demo categories return | 15 demo categories | Automatic restoration |
| Custom logo works | Custom logo displayed | Demo logo hidden |
| Logo removal restores | Demo logo returns | Automatic |
| Custom hero works | Custom hero displayed | Demo hero hidden |
| Hero removal restores | Demo hero returns | Automatic |
| Custom heading works | Custom heading displayed | Demo heading hidden |
| Heading removal restores | Demo heading returns | Automatic |
| Combined state works | All real content | No demo remnants |
| Full restoration | Complete demo | All demo content returns |
| Cart safety | Demo products not purchasable | Error/prevention |
| Real product purchasable | Add-to-cart works | Cart/checkout functional |
| Golden Core untouched | No modifications | Git diff clean |
