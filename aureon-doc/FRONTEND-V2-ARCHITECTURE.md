# Aureon Frontend v2 — Formal Integration Architecture (PHASE A)

> **Status:** DRAFT 0.9 — for review before component-library work begins
> **Doc version:** 2026-08-07
> **Owner:** Aureon lead architect
> **Supersedes informal conventions in:** `AETHER-FRONTEND.md`, `STATUS.md` (kept as operation logs; this doc defines the contract)
> **Companion roadmap:** `mem:aureon-rebrand/frontend-v2-roadmap` (M1–M10)

---

## 1. Purpose & scope

This document is the **normative contract** between the four layers of the Aurreen frontend:
WordPress, Aureon Core, the AETHER data layer (adapters/viewmodels), and the component
library. It exists so that:

1. Any developer can build a new component without reading widget loaders or hook docs.
2. Any designer can change a section's markup without breaking data contracts.
3. WP/WC upgrades cannot silently break the presentation layer.
4. The demo-import and builder systems (M6, M9) can be built against stable boundaries.

> **Architectural principle (non-negotiable)**
> The presentation layer is entirely *replaceable*. Every pixel is produced by a
> component reading data from adapters. WordPress/WooCommerce are data sources only.
>
> `Component → ViewModel → Adapter → Core → WordPress`
>
> A component that calls WP/WC directly is a **defect** (grep gate enforces this).

---

## 2. Layered architecture (normative)

```
┌──────────────────────────────────────────────────────────────┐
│ BROWSER  (GSAP · Lenis · Swiper · Bootstrap · custom JS)     │
│              ▲  data-* attributes, DOM, CSS tokens           │
│ COMPONENTS  pure markup · escaped output · zero WP calls     │
│              ▲  $componentData (normalized array)            │
│ RENDERER     manifest lookup → adapter invoke → include      │
│              ▲  per-call $data merged, behavior whitelist    │
│ VIEWMODEL    sanitize · resolve image URLs · motion gates    │
│              ▲  normalized plain arrays                      │
│ ADAPTERS     only layer allowed to call WP/WC APIs            │
│              ▲                                                │
│ CORE         theme engine · options (aureon_get_option)      │
│              ▲                                                │
│ WORDPRESS + WOOCOMMERCE  (content, products, cart, users)    │
└──────────────────────────────────────────────────────────────┘
```

**Ownership boundaries**

| Boundary | Owner | Rule |
|---|---|---|
| WP → Core | Aureon theme/plugin | Core wraps WP APIs; data normalized before adapter |
| Core → Adapter | Adapters | Adapter returns **plain PHP array**; never echoes HTML |
| Adapter → ViewModel | ViewModels | Sanitization, URL resolution, motion gating happen here |
| ViewModel → Component | Registry/Renderer | `$componentData` is the only data shape components see |
| Component → Browser | Components | **Escaped output**. Whitelisted `data-*` only |
| Customizer → anything | Options bucket | Components read **tokens/options**, never hardcoded values |

---

## 3. The data contract

### 3.1 Component data shape (everything a component may receive)

Every component receives exactly one argument: **`$componentData`** — a plain
associative array. Allowed top-level keys:

```php
$componentData = [
    // identification
    'id'          => 'section-hero',        // unique per render
    'variant'     => '',                    // modifier key (e.g. 'dark', 'shop')
    // optional heading/intro block (used by most sections)
    'heading'     => 'Step Into the Void',
    'label'       => 'NEW COLLECTION',
    'subtitle'    => 'Six colorways. One obsession.',
    'lead'        => '',                    // paragraph under heading (rare)
    // homogeneous item lists (cards, slides, posts)
    'items'       => [ /* 0..N item arrays, schema per §3.2 */ ],
    // behavior attributes — emitted by aether_behavior_attrs()
    'behavior'    => [ 'reveal-group' => true, 'parallax' => true ],
    // layout/design knobs
    'columns'     => 4,
    'gap'         => 32,                    // px, tokenized by theme
    // any component-specific keys (docs in the component file header)
    'commerce'    => [ 'show_rating' => true, 'old_price' => '$199.00' ],
    // action/link block
    'link'        => [ 'label' => 'Shop All', 'url' => '/shop/', 'target' => '' ],
    // CTAs (hero), score (reviews), etc. — defined per component
];
```

**Rules**
1. Components must tolerate **missing keys** (use `empty()` guards / `?:` defaults).
2. Components must **never mutate** `$componentData` for another render (it may be re-rendered).
3. A **flat array** passed to `aether_render_section` is auto-wrapped to `['items' => …]`.
4. Every key is escaped at render time — never trust, always `esc_html/esc_attr/esc_url`.

### 3.2 Item schema (cards, slides, posts)

Standardized across all card-like components (product, category, blog, review, team, testimonial, wishlist):

```php
[
    'id'            => 12,
    'title'         => 'Midnight Sneakers',
    'url'           => 'https://…/product/…/',
    'excerpt'       => 'Short description.',
    'image'         => [ 'id'=>60, 'url'=>'…', 'alt'=>'', 'sizes' => [] ],  // via aether_viewmodel_image()
    'badge'         => 'New',              // '', 'Sale', 'New', 'Featured'
    'price'         => '$129.00',
    'old_price'     => '$199.00',           // strikethrough when present
    'rating'        => [ 'score'=>4.8, 'count'=>128 ],   // commerce/rating
    'tagline'       => 'Carbon · Midnight', // under-title meta
    'meta'          => '2026-08-01',        // date, author, etc.
    'excerpt'       => 'Longer body copy.',
    'places'        => ['One','Two'],        // spec list, gallery thumbs, etc.
    'attributes'    => [ ['label'=>'Color','value'=>'Midnight'] ],
    'cta'           => [ 'label'=>'Add to Cart', 'url'=>'…' ],
]
```

### 3.3 Adapter interface (normative)

An adapter is a function `aether_adapter_{name}($args = [])` living in one file in
`frontend/adapters/`. Contracts:

| Rule | Explanation |
|---|---|
| **Returns** | A plain PHP array (see §3.1). Never echoes, never prints. |
| **Touches WP/WC** | Allowed — it is the only layer allowed to. |
| **Real data wins** | Try real data first; fall back to demo tokens **only** when empty. |
| **No caching** | Adapters are cheap queries; caching is the Core's job, not the adapter's. |
| **No HTML** | Return data, never markup. Even escaped markup is a defect here. |
| **Naming** | `adapter-<name>` file, `aether_adapter_<name>()` fn, `adapter` key in the section registration. Hyphens → underscores resolve automatically. |

**Signature contract:** `$args` are merged per-call (caller wins) over registered
`adapter_args`. Every adapter MUST honor these reserved keys when relevant:
`posts_per_page`, `paged`, `orderby`/`orderby_shop`, `on_sale`, `related_to`,
`tax_query`, `category`, `show_pagination`, `with_cta`.

### 3.4 ViewModel rules

| Transform | Responsible fn | Notes |
|---|---|---|
| Image normalize | `aether_viewmodel_image` | id/array → `{id,url,alt,sizes}` |
| Local path resolution | `aether_viewmodel_resolve_image` | `frontend/…` → `content_url()` prefix **before** escaping (escaping a relative url otherwise treats `frontend` as a host) |
| Motion gating | `aether_viewmodel_behavior` | Honors `aether_motion_enabled/reveal/tilt/parallax/text` customizer switches — a site owner can kill 100% of animation from one toggling |
| Sanitization | per-component escape | esc_html/esc_attr/esc_url at the boundary, never inside adapters |

### 3.5 Section registration (normative)

```php
aether_register_section( 'team', [
    'template'    => 'sections/section-team.php',   // relative to AETHER_FRONTEND_DIR
    'adapter'     => 'adapter-team.php',
    'adapter_args'=> [ 'posts_per_page' => 9 ],
    'behavior'    => [ 'reveal-group' => true ],
] );
if ( ! isset( $sectionData ) || ! is_array( $sectionData ) ) {
    return; // registration-only pass
}
```

Rendering is **per-call**: `aether_render_section( 'team', [ 'posts_per_page' => 4 ] )`
overrides the registered `adapter_args`. Identical IDs render independently.

### 3.6 Behavior attributes (animation whitelist — normative)

Only these `data-*` attributes may ever be emitted by the engine:

| Attribute | Meaning | Customizer gate |
|---|---|---|
| `data-reveal-item` | staged entrance per element | `aether_motion_reveal` |
| `data-reveal-group` | staggers group children | `aether_motion_reveal` |
| `data-tilt` | 3D tilt card | `aether_motion_tilt` |
| `data-parallax` | scroll parallax speed | `aether_motion_parallax` |
| `data-image-zoom` | hover zoom in gallery/cards | `aether_motion_zoom` |
| `data-motion-text` | word/char headline reveal | `aether_motion_text` |

Everything emitted via `aether_behavior_attrs()`. Anything else → **defect**.

---

## 4. Component lifecycle (normative)

```
register          sections/*, components/* self-register at boot
        ↓
data              adapters normalize WP/WC/tokens → plain arrays
        ↓
viewmodel         sanitize, resolve images, apply motion gates
        ↓
render            composer emits ONLY the requested section; single include
        ↓
browser           JS binds to data-* contract (GSAP/Lenis/Swiper/main.js)
```

No render callback. No "template parts". No hook patching. A section renders **once**, in
the template that called `aether_render_section()`.

### 4.2 Standard component header (M2.5 — NORMATIVE)

Every component file in `frontend/components/` must open its docblock with the
following API header. It is the contract implementers and reviewers read first —
data schemas here MUST match the `isset( $componentData[ X ] )` reads in the body.

```php
/**
 * Component name — one line "what it renders" summary.
 *
 * Key:    '<manifest key>'                     // e.g. 'card/product'
 * Source: '<source.html> <source class(es)>'   // reference design, or "engine-native (no source)"
 *
 * Props:  (all keys optional; escaped at render; missing key → default listed)
 * - `string $title`    Display title. Default `__('…','aureon')`.
 * - `array  $items`    §3.2 item schema list. Default `[]`.
 * - …every key the body reads…
 *
 * Slots:  nested components rendered (e.g. `'commerce/rating'`, `'hero/slide'`), or `none`.
 * Variants: modifier keys consumed (e.g. `layout = home|shop`), or `none`.
 * Tokens:  no hardcoded presentation values — theme uses `--aureon-*` custom props only.
 *
 * @package Aureon
 */
```

Header fields:

| Field | Rule |
|---|---|
| `Contract Key` | The manifest path (must resolve in `manifest/components.php`). |
| `Props` | Every `$componentData[<key>]` the body reads, with type hint + default. |
| `Slots` | Every nested `aether_render_component()` call inside the body. |
| `Variants` | Keys that switch layout/marking (e.g. `cards/product` `layout=home|shop`). `none` if absent. |
| `Tokens` | Components may NOT hardcode `px`, hex colors, or font sizes; state "no token consumption" unless a sanctioned §5 layout constant is documented. |

A grep gate protects the contract: every file must contain `@package Aureon`, a
`Props:` line, and a `Slots:` line.

---

## 5. Tokens + options — the design contract

**Rule:** a component contains **zero hardcoded presentation values**. Every color,
spacing, radius, breakpoint, font, and animation fact is:

```php
add_filter( 'aureon_option_defaults', function ( $d ) {
    $d['aether_container_max']   = 1200;
    $d['aether_radius_sm']       = 8;
    $d['aether_radius_pill']     = 999;
    $d['aether_section_padding'] = 6;   // rem
    return $d;
} );
```

Consumed via `aureon_get_option('aether_…')`, surfaced as CSS custom properties
`--aureon-*`, editable through the Customizer panel “AETHER Frontend” (priority 120).

**Audit gate:** `rg -n "px|#[0-9a-fA-F]{6}|1[0-9]rem" frontend/components/` must return
**0 hits** except in `tokens/` and sanctioned layout constants (section padding), each
documented in the owning component header.

---

## 6. Frontend library & extension points

| Concern | Mechanism | Public |
|---|---|---|---|
| Rendering hook | `aether_section_data`, `aether_component_data` filters | ✓ |
| Query vars | `aether_shop_query_args` filter on `adapter-wc-products` | ✓ (read docs in file) |
| New section | copy a `section-*.php`, register, adapter | ✓ |
| New component | copy a manifest entry + template | ✓ |
| Data source | implement `adapter-<name>.php` | ✓ |
| Full layout swap | replace the whole `frontend/` dir (engine re-derives paths) | ✓ path-safe |

### 6.1 Sanctioned exceptions to "no WP in components" (M2.4 audit)

| File | Call | Why it is allowed |
|---|---|---|
| `forms/login.php` `do_action('woocommerce_login_form')` | WC extension hook | honeypots, social login, anti-spam plugins inject here; the form is unusable without it |
| `forms/register.php` `do_action('woocommerce_register_form')` | same | same |
| sections (`section-checkout.php` etc.) | `WC()`, `wc_*()`, `home_url()` fallbacks | sections are the **composition boundary** — they may invoke WC form subsystems but never business logic; components remain zero-WP |

Grep gate therefore checks **components only**; section-level calls are allowed but must stay in `frontend/sections/`.

---

## 7. Security & quality gates (must stay green)

| Check | How |
|---|---|
| Components never call WP/WC | grep gate eviction: `rg 'wp_|wc_|get_option|query_posts' frontend/components/` → 0 (allow-list: the two `do_action` form hooks + errors via components.php `error/404` fallback-free purity; see §6.1) |
| Escape at boundary only | `esc_html/esc_attr/esc_url` in templates not adapters |
| Whitelisted attributes only | `aether_behavior_attrs` is the only emitter |
| Sanitized Customizer | every control has a sanitizer (`_sanitize_checkbox`, `sanitize_text_field`, `esc_url_raw`, `absint`) |
| Path-safe | `AETHER_FRONTEND_DIR` derived from file location, mounted anywhere |
| Lint | `php -l` over all frontend files; `node --check` over all non-vendor JS |
| No regressions | STATUS.md Stage 11 gates: manifest 1:1, no brand strings, single-load assets, no duplicate shell |

---

## 7. Open questions (resolve before M2/M4/M5)

1. **Container naming:** current source uses `container` width tokens; decide final
   scale (`.hf-container` vs `--aureon-container`) — token map will drive M6.
2. **Fonts:** Cabinet Grotesk / Satoshi are not bundled (`frontend/assets/fonts/` empty).
   Decision needed: (a) ship @font-face local (pro, fully offline) vs (b) keep Google
   Fonts CDN (network needed). Recommend (a) for a commercial product.
3. **Checkout/account:** AETHER-styled WC checkout + account templates are still WC-defaults
   inside the shell (cosmetic). Decision needed whether M5 rebuilds them or Phase M2 library
   portion first.
4. **Order-received:** no `thankyou` AETHER component yet — the closest loop for M5.
5. **Demo import format** (M6): manifest JSON + WordPress XML vs custom importer. To be
   decided before M6 kickoff (roadmap M6).

---

## 8. Sign-off checklist

- [ ] Component authors read §3.1–§3.6 (data contract + behavior whitelist)
- [ ] Adapter authors read §3.3 + §5
- [ ] 0 direct WP/WC calls in `frontend/components/` (grep gate)
- [ ] 0 hardcoded presentation values in `frontend/components/` (audit gate §5)
- [ ] Every component annotated with its data schema + token keys in a header comment
- [ ] `php -l` + `node --check` green
- [ ] Live Playwright sweep green after related change (roadmap M10 preflight)

___

*Definition of authority: `AETHER-FRONTEND.md` (how it works today), `STATUS.md` (stage logs)
and this document (the design contract). Where they disagree, this document wins — update
the others.*