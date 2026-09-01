# Ferm Living Demo Mode Documentation

## Three Modes

| Mode | Value | Behavior | Use Case | Runtime Tested |
|------|-------|----------|----------|----------------|
| AUTO | `auto` | Real content → hide demos; no real → show demos | Default client workflow | **YES — 34/34** |
| FORCE_DEMO | `force_demo` | Always show demos, ignore real content | Admin/dev/demo environment | **NO — implemented only** |
| DISABLED | `disabled` | Never show demos | Client handoff when real content complete | **NO — implemented only** |

## Setting the Mode

```php
// In WordPress Customizer or via wp_options:
// Option: aether_demo_mode
// Values: 'auto' | 'force_demo' | 'disabled'

// Via WP-CLI:
wp option update aether_demo_mode 'auto'
wp option update aether_demo_mode 'force_demo'
wp option update aether_demo_mode 'disabled'
```

## Mode Scope

### AUTO (default)
- Shows demo products when no real WooCommerce products exist
- Hides demo products when real products are published
- Shows demo categories when no real categories exist
- Hides demo categories when real categories are published
- Falls back to demo heading/logo/hero when no custom content set
- Custom content takes priority when set

### FORCE_DEMO
- Always shows demo products regardless of real products
- Always shows demo categories regardless of real categories
- Useful for:
  - Admin demo environment
  - Development/staging
  - Client presentations before real content ready
- Does NOT:
  - Delete real WooCommerce data
  - Hide real products from admin
  - Affect checkout/cart for real products
  - Modify WooCommerce business logic

### DISABLED
- Never shows demo products
- Never shows demo categories
- Useful for:
  - Client handoff when all real content is in place
  - Production environment with complete catalog
  - Preventing demo content from ever appearing

## Implementation Notes

- Mode is read via `ferm_get_demo_mode()` in composer.php
- `ferm_show_demo_content()` checks mode before showing any demo content
- FORCE_DEMO bypasses the `ferm_has_real_products()` check
- DISABLED returns false immediately, skipping all demo logic
- Mode is stored in `wp_options` as `aether_demo_mode`
- Default fallback is `auto` if option is missing or invalid

## Testing Status

- **AUTO**: ✅ Runtime tested — 34/34 assertions pass
- **FORCE_DEMO**: ⚠️ Implemented — code reviewed, not runtime exercised
- **DISABLED**: ⚠️ Implemented — code reviewed, not runtime exercised

> FORCE_DEMO and DISABLED should not be claimed as runtime-proven
> until explicitly tested in a live environment.
