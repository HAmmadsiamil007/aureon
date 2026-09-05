# 17 — CUSTOMIZER ARCHITECTURE

## Customizer Flow

```
WordPress Customizer
    ↓
Customizer Registration (customizer.php)
    ↓
Settings, Sections, Panels, Controls
    ↓
Save → aureon_settings option
    ↓
Frontend: aureon_get_option() reads from aureon_settings
    ↓
Token Generation (aether-tokens.php) → CSS custom properties
    ↓
OR FermPageData.customizer (bridge JS reads globals)
```

## Settings Storage

All settings stored in single `aureon_settings` option (serialized array).

## Token Resolution

```
1. Explicit aether_color_* option
2. Customized global_colors palette (React Color Manager)
3. AETHER default
```

## Font Resolution

```
1. Explicit aether_font_* override
2. Dynamic Typography Manager entries
3. Classic font options
4. AETHER defaults (Cabinet Grotesk / Satoshi)
```

## Complete-Page Customizer Bridge

```php
$page_data['customizer'] = [
    'site' => ['name', 'description', 'logo_url'],
    'announcement' => [...],
    'hero' => [...],
    'categories' => [...],
    'footer' => [...],
    'newsletter' => ['heading', 'text', 'subtitle'],
    'social' => [...],
    'usp_items' => [...],
    'colors' => ['bg', 'surface', 'text', 'muted', 'accent', 'accent_hover', 'border'],
    'fonts' => ['heading', 'body'],
];
```

Injected via `FermPageData.customizer`. Client JS reads and applies.

## Token Output

`aether_enqueue_tokens()` [priority 98]:
- Generates `:root { --var: value; }` CSS
- Includes: 12 colors, 2 font stacks, 9 layout tokens
- WC color bridge (--aether-wc-*)
- Skipped for complete-page designs

## Design Token Variables

| Variable | Option | Default |
|----------|--------|---------|
| --void | aether_color_bg | #09090B |
| --surface | aether_color_surface | #141416 |
| --surface-2 | aether_color_surface_2 | #1a1a1d |
| --surface-3 | aether_color_surface_3 | #232327 |
| --text | aether_color_text | #FFFFFF |
| --muted | aether_color_muted | #A8B5C0 |
| --gold | aether_color_accent | #C8956C |
| --gold-alt | aether_color_accent_hover | #D4A574 |
| --line | aether_color_border | #1A1A1A |
| --error | aether_color_error | #CC4444 |
| --success | aether_color_success | #4CAF50 |
| --font-heading | aether_font_heading | Cabinet Grotesk |
| --font-body | aether_font_body | Satoshi |
