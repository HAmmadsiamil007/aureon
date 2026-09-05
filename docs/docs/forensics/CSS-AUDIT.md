# Aureon CSS Audit

## 1. CSS File Inventory
- **style.css**: Core theme styles, layout, reset, and base variables.
- **responsive.css**: Responsive overrides and media queries.
- **pages.css**: Specific page templates and structural elements.
- **a11y.css**: Accessibility helpers (focus rings, screen reader text, etc).
- **fonts.css**: Typography imports and font-face declarations.
- **motion.css**: Animations, keyframes, and transition classes.

## 2. CSS Custom Properties
Found custom properties:
- `--announcement-height`
- `--black`
- `--chrome`
- `--container-max`
- `--error`
- `--font-body`
- `--font-heading`
- `--gold`
- `--gold-alt`
- `--grid-gap`
- `--header-height`
- `--line`
- `--muted`
- `--radius-lg`
- `--radius-md`
- `--radius-pill`
- `--radius-sm`
- `--section-padding`
- `--success`
- `--surface`
- `--surface-2`
- `--surface-3`
- `--transition-fast`
- `--transition-normal`
- `--transition-slow`
- `--void`
- `--white`
- `--z-announcement`
- `--z-base`
- `--z-fog`
- `--z-footer`
- `--z-header`
- `--z-mobile-hamburger`
- `--z-mobile-header`
- `--z-mobile-menu`
- `--z-nav-overlay`
- `--z-preloader`
- `--z-scroll-top`
- `--z-search`
- `--z-skip-link`

## 3. Customizer -> CSS token mapping analysis
Tokens mapped from customizer:
- `'aether_color_bg'           => '#09090B',`
- `'aether_color_surface'      => '#141416',`
- `'aether_color_surface_2'    => '#1a1a1d',`
- `'aether_color_surface_3'    => '#232327',`
- `'aether_color_text'         => '#FFFFFF',`
- `'aether_color_muted'        => '#A8B5C0',`
- `'aether_color_accent'       => '#C8956C',`
- `'aether_color_accent_hover' => '#D4A574',`
- `'aether_color_border'       => '#1A1A1A',`
- `'aether_color_error'        => '#CC4444',`
- `'aether_color_success'      => '#4CAF50',`
- `'aether_font_heading'       => 'Cabinet Grotesk',`
- `'aether_font_body'          => 'Satoshi',`
- `'aether_color_bg'      => '#09090B',`
- `'aether_color_surface' => '#141416',`
- `'aether_color_surface_2' => '#1a1a1d',`
- `'aether_color_surface_3' => '#232327',`
- `'aether_color_text'    => '#FFFFFF',`
- `'aether_color_muted'   => '#A8B5C0',`
- `'aether_color_accent'  => '#C8956C',`
- `'aether_color_accent_hover' => '#D4A574',`
- `'aether_color_border'  => '#1A1A1A',`
- `'aether_color_error'   => '#CC4444',`
- `'aether_color_success' => '#4CAF50',`
- `'aether_font_heading' => 'Cabinet Grotesk',`
- `'aether_font_body'    => 'Satoshi',`

## 4. Component Coverage
Styles are scoped to components like `.site-header`, `.site-footer`, `.woocommerce`, etc.

## 5. Responsive Breakpoints defined
- `@media (forced-colors: active)`
- `@media (max-width: 1024px)`
- `@media (max-width: 360px)`
- `@media (max-width: 480px)`
- `@media (max-width: 575.98px)`
- `@media (max-width: 576px)`
- `@media (max-width: 640px)`
- `@media (max-width: 767.98px)`
- `@media (max-width: 767px)`
- `@media (max-width: 768px)`
- `@media (max-width: 991.98px)`
- `@media (min-width: 1200px) and (max-width: 1439px)`
- `@media (min-width: 1440px) and (max-width: 1919px)`
- `@media (min-width: 1920px)`
- `@media (pointer: coarse)`
- `@media (prefers-reduced-motion: reduce)`
- `@media (scripting: none)`

## 6. WooCommerce CSS overrides
WooCommerce specific styling is largely handled within `pages.css` and overrides default plugin styling for checkout, cart, and product single pages.

## 7. Accessibility CSS (a11y.css)
Provides `.screen-reader-text` classes, keyboard focus indicators, and reduced-motion media queries.

## 8. Animation CSS (motion.css)
Contains keyframes (`@keyframes`) and animation utility classes for fade-ins, sliding elements, and interactive hovers.

## 9. Dead/duplicate CSS rules found
Minimal duplication detected. Mostly redundant media queries grouping in `responsive.css`.

## 10. Missing CSS for known components
Forms require extensive cross-browser normalisation. Checkout styling requires more robust variable usage.