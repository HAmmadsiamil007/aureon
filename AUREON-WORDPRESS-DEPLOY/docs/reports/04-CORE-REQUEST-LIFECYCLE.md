# 04 — CORE REQUEST LIFECYCLE

## General Request Flow

```
HTTP Request
    ↓
WordPress Bootstrap (wp-config, wp-settings)
    ↓
Theme Detection → aureon/theme/
    ↓
functions.php → require inc/frontend.php
    ↓
frontend.php → require frontend/views/loader.php
    ↓
aether_frontend_boot() [after_setup_theme, priority 30]
    ↓
  ├── Load tokens/tokens.php
  ├── Load views/design.php
  ├── Load views/registry.php
  ├── Load views/renderer.php
  ├── Load views/viewmodel.php
  ├── Load views/assets.php
  ├── Load views/composer.php
  ├── Glob adapters/*.php → require each
  ├── Glob sections/*.php → require each
  └── Glob active_design/sections/*.php → require each
    ↓
Design Resolution: aether_active_design()
    ↓
Route Detection (is_product(), is_cart(), etc.)
    ↓
template_include filter [priority 99-998]
    ↓
Template Selection
    ↓
Data Loading (Adapters)
    ↓
Asset Loading (wp_enqueue_scripts)
    ↓
Rendering
    ↓
Browser
```

## Route-Specific Flows

### Homepage (/)
```
is_front_page() → true
    ↓
template_include [priority 998]:
  aether_is_complete_page_design()?
    YES → ferm-page.php
    NO  → front-page.php
    ↓
Complete-page: aureon_ferm_resolve_page() → 'index.html'
Component: aether_frontpage_sections() → render sections
```

### Single Product (/product/*)
```
is_product() → true
    ↓
template_include [priority 99]:
  is_cart()? NO
  is_checkout()? NO
    ↓
template_include [priority 998]:
  aether_is_complete_page_design()?
    YES → ferm-page.php → aureon_ferm_resolve_page()
    NO  → single-product.php
```

### Cart (/cart/)
```
is_cart() → true
    ↓
template_include [priority 99]:
  aureon_aether_wc_page_templates() → cart.php
    ↓
cart.php → aether_render_section('cart')
```

### Checkout (/checkout/)
```
is_checkout() → true
    ↓
template_include [priority 99]:
  aureon_aether_wc_page_templates() → checkout/form-checkout.php
    ↓
Complete-page: WC native template (not frozen HTML)
```

### Account (/my-account/)
```
is_account_page() → true
    ↓
template_include [priority 99]:
  is_user_logged_in()?
    YES → myaccount/my-account.php (WC native)
    NO  → ferm-page.php (frozen login.html)
```

### Search (/?s=)
```
is_search() → true
    ↓
template_include [priority 998]:
  aether_is_complete_page_design()?
    YES → ferm-page.php → 'blogs/stories.html' (fallback)
    NO  → index.php
```

### 404
```
is_404() → true
    ↓
template_include [priority 998]:
  aether_is_complete_page_design()?
    YES → ferm-page.php → 'pages/contact.html' (fallback)
    NO  → 404.php
```

## Template Priority Chain

| Priority | Filter | Purpose |
|----------|--------|---------|
| 99 | `aureon_aether_wc_page_templates` | WC pages (cart/checkout/account) |
| 998 | `aureon_ferm_template_include` | Complete-page routing |

**Key:** WC templates (priority 99) run BEFORE complete-page routing (priority 998), so cart/checkout/account always use WC native templates even when complete-page mode is active.
