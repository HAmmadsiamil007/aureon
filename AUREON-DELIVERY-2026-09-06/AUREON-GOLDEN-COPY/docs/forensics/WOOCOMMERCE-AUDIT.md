# WooCommerce Integration Audit

## Product Data Pipeline
The product data pipeline in Aureon translates standard WooCommerce objects into a clean, framework-agnostic data structure suitable for UI rendering.

1. **WooCommerce Product:** Native data exists in WooCommerce (e.g. `wc_get_product()`).
2. **Adapter (`adapter-product.php`):** The `aether_adapter_product()` function fetches the WC product data (title, price, gallery, color/size attributes, reviews, etc.) and transforms it into an array of UI-ready tokens. It gracefully falls back to curated demo data if WC is missing or data is incomplete (gated by `aether_demo_content`).
3. **Section Registration:** The mapped array is registered to sections like `section-product.php`. For example, `aether_register_section('product', ['adapter' => 'adapter-product.php', ...])` ties the adapter to the template.
4. **HTML Template Rendering:** Components like `product/gallery`, `product/info`, and `product/sticky-bar` receive this clean tokenized array and output the HTML, completely decoupled from WooCommerce APIs at the template level.

## Analysis of Key WooCommerce Features

### Variable Products
- The current implementation handles attributes like `pa_color` and `pa_size` in `adapter-product.php`, mapping them to UI representations (e.g., hex codes for colors and arrays for sizes).
- **Cart Interactions:** The adapter creates a basic classic add-to-cart URL (`?add-to-cart=ID`). For full variable product support where users select attributes before adding to cart, standard WC form handling or an AJAX variation handler is required on the frontend.
- **Cart Display:** The cart item parsing in `adapter-cart.php` correctly identifies variations and formats them as a human-readable string (e.g. "Size 10 / Obsidian").

### Shop/Archive
- Managed by `adapter-wc-products.php`, which maps WP_Query arguments to WooCommerce product queries.
- Incorporates features like sales filtering (`on_sale`), related products (`related_to`), and pagination.
- Renders via `section-shop-grid.php`, which loops over items and renders `card/product` components.
- The shop filter (`adapter-wc-filter.php`) provides categorical filtering, linking to category archives, and includes a "Sale" toggle.

### Categories
- Driven by `adapter-wc-categories.php` which fetches terms for the `product_cat` taxonomy.
- Handles custom thumbnail retrieval with a robust fallback pipeline: *WC term thumbnail -> first product featured image -> WC placeholder -> theme demo image*.
- Formats categories for the curated 2x2 grid in the UI and includes custom sorting to ensure hero categories appear at the top.

### Cart
- `adapter-cart.php` interacts directly with `WC()->cart`.
- Extracts line items, quantities, variation details, and totals (subtotal, shipping, tax, total).
- The cart section (`section-cart.php`) displays empty states or item lists based on this data. It includes a frontend script that intercepts quantity `+/-` clicks, sends an AJAX POST to update the cart, and dynamically replaces the cart DOM using `DOMParser`.

### Checkout
- Uses `adapter-cart.php` with the `checkout` context to fetch order summaries.
- Renders native WooCommerce checkout fields using a custom wrapper (`aether_checkout_render_field`) to ensure they match the theme's source-style form groups (floating labels, dark mode inputs).
- Includes all standard billing fields, country select dropdowns, and dynamic payment gateway rendering (`payment_methods`). Form submissions post back directly through native WooCommerce handlers.

## Session Fix Analysis (`aureon-fix-wc-session.php`)
The mu-plugin `aureon-fix-wc-session.php` resolves critical issues related to WooCommerce session initialization:

- **Early Initialization:** WooCommerce lazily loads sessions. On REST API, Customizer, or CLI requests, `WC()->session` might be null, causing PHP warnings when checkout code or UI code tries to read/write to it (e.g., `order_awaiting_payment on null`). The plugin hooks into `init`, `rest_api_init`, and AJAX hooks to forcefully call `$woocommerce->initialize_session()` if the session is null.
- **After Payment Guard:** It hooks into `woocommerce_checkout_order_processed` to re-initialize the session before `wc_clear_cart_after_payment()` empties the cart. This prevents fatal errors if the session was destroyed prematurely during order processing.

*(Note: `WOO_INTEGRATION_REPORT.md` and `console-errors.txt` were not found in the immediate directories for review at this time.)*
