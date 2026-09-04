# 07 — COMPONENT-MODE ARCHITECTURE

## Overview

Component mode (`complete_page: false`) uses the AUREON shell with adapters, viewmodels, and components to render the frontend. The client pack provides visual overrides, not complete HTML.

## How It Works

```
WordPress Route
    ↓
template_include → standard WP template (front-page.php, single-product.php, etc.)
    ↓
get_header() → header.php → aether_compose_header()
    ↓
Template renders sections via aether_render_section()
    ↓
Section Registry → Adapter → ViewModel → Component Template
    ↓
get_footer() → footer.php → aether_compose_footer()
```

## Rendering Pipeline

### Section Registration
```php
aether_register_section('hero', [
    'template' => 'sections/section-hero.php',
    'adapter'  => 'adapter-hero.php',
    'adapter_args' => [],
    'behavior' => ['reveal' => true],
]);
```

### Section Rendering
```php
aether_render_section('hero', $data)
    ↓
1. Get section from registry
2. Resolve adapter function
3. Call adapter with args
4. Merge adapter data with passed $data
5. Normalize ViewModel keys
6. Resolve template (pack-first)
7. Apply filter: aether_component_data
8. Include template
```

### Component Rendering
```php
aether_render_component('card/product', $data)
    ↓
1. Get component from manifest
2. Resolve template (pack-first)
3. Apply filter: aether_component_data
4. Include template with $componentData
```

## Platform Assets (Component Mode)

Component mode loads platform CDNs + contract JS:
- Bootstrap 5.3.3 (CSS + JS)
- Font Awesome 6.5.1
- Swiper 11
- GSAP 3.12.5 + ScrollTrigger
- Platform animations.js, main.js, countdown.js

Plus pack assets from manifest.json.

## Comparison: Complete-Page vs Component Mode

| Aspect | Complete-Page | Component Mode |
|--------|---------------|----------------|
| HTML source | Frozen HTML file | PHP templates |
| Shell | Bypassed | header.php → footer.php |
| CSS | Pack only | Platform CDNs + pack |
| JS | Pack only | Platform + pack |
| Data flow | FermPageData globals | Adapters → ViewModels |
| Customizer | Bridge JS | Tokens + PHP |
| Product data | JS reads globals | Adapter → template |
| Cart | AJAX handlers | WC native + AJAX |

## Pack Overrides

Component-mode packs can override:
- Shell components (header, footer, mobile-chrome)
- Card components (product, category, blog)
- Hero components (slider, slide)
- Section components

Pack overrides are resolved via `aether_resolve_design_path()`.
