# 28 — CORE EXTENSION CONTRACT

## Safe Extension Points

### 1. Add New Adapter
```php
// Create: aureon/frontend/adapters/adapter-myservice.php
function aether_adapter_myservice($args = []) {
    // Touch WP/WC APIs here
    return $data;
}
```
**Risk:** LOW — adapters are isolated data access layer

### 2. Add New Section
```php
// Create: aureon/frontend/sections/section-myservice.php
aether_register_section('myservice', [
    'template' => 'sections/section-myservice.php',
    'adapter'  => 'adapter-myservice.php',
]);
```
**Risk:** LOW — sections are self-contained compositions

### 3. Add New Component
```php
// Add to: aureon/frontend/manifest/components.php
'my-component' => ['template' => 'components/my-component.php'],
```
**Risk:** LOW — components receive normalized data

### 4. Override Pack Components
```php
// Place in: aureon/frontend/designs/<slug>/components/shell/header.php
// Automatically shadows engine default
```
**Risk:** LOW — pack-first resolution

### 5. Add Customizer Setting
```php
// Register in customizer.php
// Add default in tokens.php
// Read via aureon_get_option()
```
**Risk:** MEDIUM — affects all designs

### 6. Add AJAX Endpoint
```php
add_action('wp_ajax_my_action', 'my_handler');
add_action('wp_ajax_nopriv_my_action', 'my_handler');
```
**Risk:** MEDIUM — must verify nonce

### 7. Add Design Pack Token Defaults
```php
// Create: aureon/frontend/designs/<slug>/tokens.php
return ['aether_color_accent' => '#MYCOLOR'];
```
**Risk:** LOW — defaults only, Customizer wins

## Core Change Rules

### SAFE
- Add new adapter
- Add new component
- Add new section
- Add new design pack
- Override pack templates
- Add filter on existing hook

### REVIEW REQUIRED
- Modify template routing
- Modify asset loading
- Add Customizer setting
- Modify adapter output
- Modify security logic

### HIGH RISK
- Modify design resolver
- Modify manifest schema
- Modify FermPageData structure
- Modify cart/checkout flow

### FORBIDDEN
- Modify WooCommerce core
- Modify WordPress core
- Rebuild client frontend
- Remove required hooks
- Change business logic for presentation
