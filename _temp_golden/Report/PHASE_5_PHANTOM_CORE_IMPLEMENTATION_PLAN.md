# PHASE 5 — PHANTOM CORE FRAMEWORK MASTER IMPLEMENTATION PLAN

> **For agentic workers:** This is the engineering specification for Phantom Core. Implementation will execute phase-by-phase per `Report/MASTER_ROADMAP.md` status gates. Each phase ends in an independently testable deliverable; no phase starts until its dependencies are `Completed`.

**Goal:** Build **Phantom Core**, a modern framework layer (service container → tokens → render engine → component registry → bridges) that delivers every custom capability for the Phantom frontend while leaving GeneratePress 3.6.1 + GP Premium 2.5.6 100% untouched.

**Architecture:** A GeneratePress **child theme** (`wp-content/themes/phantom`) acting as the presentation layer, with a PHP service-container "core" that (a) hooks only public WP/GP APIs, (b) exposes adapters/bridges for plugins, (c) renders a token-driven component system, and (d) is independently testable per subsystem.

**Tech Stack:** WordPress 6.9+, PHP 8.2+, Composer (PSR-4), Vite + SCSS + TypeScript/ESM, GSAP/Lenis/Three.js. Tests: PHPUnit + `WP_Mock` + `Brain Monkey` + Playwright.

## Global Constraints

- **Never modify GeneratePress.** **Never modify GP Premium.** Zero shipped-file changes; CI hash-gate enforces.
- Public WP APIs + documented `generate_*` hooks/filters only (`generate_get_option()`, `apply_filters('generate_*')`, `do_action('generate_*')`, `wp_nav_menu`, widgets, template hierarchy). Never call internal GP symbols.
- Namespaces `Phantom\Core`; function/option/hook prefix `phantom_`; handles `phantom-*`.
- Update-safe: child-theme-only overrides; every shipped asset that interacts with a vendor file is a wrapper.
- WooCommerce: preserve every `woocommerce_*` action/filter; full HPOS + Blocks compatibility.
- Accessibility: WCAG 2.2 AA is a requirement, not a phase; tokens carry contrast/`prefers-reduced-motion`.
- Business logic and presentation separate: data→adapter→view-model→token→component→template.
- Every subsystem independently testable via a test harness per phase.

---

# PART A — FOUNDATION

## PHASE 0 — PROJECT FOUNDATION

**Purpose:** Standing up the Phantom repo, toolchain, coding standards, and delivery pipeline so all later phases build on one consistent foundation.

**Responsibilities**
- Create top-level structure (child theme + `assets-src` + `tests` + `docs`).
- Adopt coding standards and tooling (PHPCS, PHPStan/Psalm, Prettier, ESLint).
- Lock naming & versioning policy; wire Git workflow and CI.

**Public APIs:** none exposed to runtime; this phase defines *meta-* APIs only (script targets, version string).

**Internal APIs:** none (scaffold-level constants only).

**Dependencies:** none (this phase bootstraps everything else).

**Directory structure (canonical, defined once):**

```
wp-content/themes/phantom/
├─ style.css                      # WP theme header; Version: 0.1.0
├─ functions.php                  # thin loader → require load.php
├─ theme.json                     # editor defaults (optional, vanilla)
├─ composer.json                  # PSR-4 Phantom\Core\\ —> app/
├─ package.json                   # vite, scss, ts, linters
├─ vite.config.js                 # build config (see Phase 7)
├─ .phpcs.xml / phpstan.neon / psalm.xml / .editorconfig
├─ app/                           # Phantom Core (namespaces under Phantom\Core\)
│  ├─ Boot/                       # Phase 1
│  ├─ Container/                  # Phase 1–2
│  ├─ Config/                     # Phase 1–2
│  ├─ Events/                     # Phase 2
│  ├─ Hooks/                      # Phase 2
│  ├─ Registry/                   # Phase 2
│  ├─ Factory/                    # Phase 2
│  ├─ Cache/                      # Phase 2
│  ├─ Tokens/                     # Phase 3
│  ├─ Render/                     # Phase 4
│  ├─ Components/                 # Phase 5
│  ├─ Templates/                  # Phase 6
│  ├─ Assets/                     # Phase 7
│  ├─ Bridges/                    # Phase 8
│  ├─ Woo/                        # Phase 9
│  ├─ Animation/                  # Phase 10
│  ├─ Support/                    # traits/utils
├─ assets-src/                    # SCSS/TS/JS source (Phase 7)
├─ templates/                     # Theme templates (Phase 6/12)
├─ template-parts/                # Partial components inventory
├─ inc/                           # child-theme glue (no reuse)
├─ tests/                         # PHPUnit + WP core (Phase 15)
├─ e2e/                           # Playwright (Phase 15)
├─ docs/                          # per-phase docs
└── git/                          # project-level git repo (or monorepo hook)
```

**Classes / Interfaces / Traits / Services:** only `Phantom\Core\Core\Version` (constants) in this phase.

**Data flow:** none operational.

**Event flow:** none operational.

**Hook usage:** none required for function.

**Performance considerations:** CI + pre-commit `phpcs`, `prettier --check`; keep placeholders free of runtime cost.

**Accessibility considerations:** `.editorconfig`, `aria`-related sniffs wired into PHPCS tooling now; no UI yet.

**Security considerations:** no secrets in repo; `git-secrets` / `leaktpl` add to CI; key/secret files in `.gitignore`.

**Testing strategy:** repo bootstrap CI job — `composer validate`, `php -l` on `app/`, `npm ci`.

**Risks:** tool version sprawl — throttle to documented versions; authorless dependency drift.

**Rollback strategy:** repository revert to `main` (git); releases never depend on a single developer machine.

**Acceptance criteria:** CI green on an empty `app/` tree; composer autoload dump succeeds; `php -l` passes; versions pinned in `composer.lock` and `package-lock.json`.

**Verification checklist:**
- [ ] `composer install` succeeds with PSR-4 `Autoload\`
- [ ] `npx vite --version` prints pinned Vite version
- [ ] `.editorconfig`, `phpcs`, `phpstan` invest each load without failure
- [ ] `git pull --rebase` conflict-free on fresh clone
- [ ] Semver policy `0.x` documented in `docs/versions.md`

---

## PHASE 1 — BOOTSTRAP

**Purpose:** Load Phantom Core from the theme without touching GeneratePress. Establish deterministic boot sequence, a minimal service container, config loader, env detection, feature flags, debug mode, logging, and error handling.

**Design:**
- `app/load.php` — earliest require, guarded by a `defined('ABSPATH')` + once check. It registers Composer autoload (PSR-4) and instantiates `Boot\Kernel`.
- `Boot\Kernel` runs the ordered bootstrap: `environment() → registerContainer() → loadConfig() → registerProviders() → bootProviders() → raise('core.ready') → ]`.
- It binds to `'plugins_loaded'` priority 5 (before GP Premium future) and never earlier/later than that; the Kernel is the single entry.

**Public APIs:**
- `Phantom\Core\Core\App::instance(): App`
- `App::make(string $abstract): object|mixed` (resolves from container)
- `App::get(string $id, mixed $default=null): mixed` (config shorthand)
- `App::env(): string`
- `App::isDebug(): bool`
- `App::log(string $level, string $message, array $context=[])`

**Internal APIs:**
- `Boot\Kernel::bootstrap()`, `Boot\Kernel::runLifecycle()`
- `Container\Container` (PSR-11 like, defined Phase 2)
- `Config\ConfigLoader::load()` returns immutable config array.
- `Support\Env::detect()` wraps `wp_get_environment_type()` and merges `.env`-style env overrides from `phantom.env.json` (no dotenv dep).

**Dependencies:** Phase 0; WP `plugins_loaded`; Composer autoload.

**Directory structure:** `app/Boot/` (Kernel, Sequence), `app/Config/`, `app/Support/`.

**Classes:**
- `Phantom\Core\Boot\Kernel`
- `Phantom\Core\Boot\Sequencer` (ordered steps w/ filters)
- `Phantom\Core\Config\ConfigLoader`
- `Phantom\Core\Support\Env`
- `Phantom\Core\Support\FeatureFlags`
- `Phantom\Core\Support\Debug\Log` (facade)
- `Phantom\Core\Support\Debug\Loggers` (WCAAJ-what, date)
- `Phantom\Core\Support\ErrorHandler` (wraps `WP_Error`, never throws on WP surface)

**Interfaces:** `Boot\HasStorageContract`? No — keep single `BootableInterface` with `boot()` + `register()`.

**Services:** `cfg`, `env`, `logger`, `flags`, `errorHandler`.

**Data flow (boot):**
```
wp → plugins_loaded(5)
   → load.php → composer autoload → new Kernel → Sequencer
   → [config → env → flags → logger → errorHandler] → Providers (Phase 2) → kernel boot(self)
   → do_action('phantom_core:ready')
```

**Event flow:** emits `phantom_core:booting`, `phantom_core:booted`, `phantom_core:ready` (documented). Errors raise `phantom_core:boot_error`.

**Hook usage:** `document` this: `do_action('generate_settings_show_name', ...)`; we only tap generic `generate_settings_display` robustness. Use `generate_` filters only where documented (`generate_get_option`).

**Performance:** one-time boot ≤ 3ms self-time; no `file_get_contents` per request beyond immutable config cache; eager config cache via `wp_cache`.

**Accessibility:** no UI in this phase.

**Security:** env secrets kept out of logs (DebugLogger redacts `ph_pass`, `sku_key`); config loader rejects `../` traversal; error handler never outputs stack to non-debug.

**Testing strategy:** unit test `Sequencer` order; config loader mock via env override; flags toggle across env types; error handler logs to `debug.log` in `WP_DEBUG`.

**Risks:** boot-order races with other plugins — pin to `priority 5` and expose `boot` window; mitigation: composable through `Sequencer::add_step`.

**Rollback:** disable flag `phantom_feature_*`; fallback to parent theme (never disable theme entirely).

**Acceptance criteria:** on `plugins_loaded` (priority 5) the Kernel holds the container, config is loaded from disk/env, and `App::debug(false)` is resolvable; `phantom_core:ready` fires.

**Verification checklist:**
- [ ] Fresh install: `App::get('env')` resolves as `production`
- [ ] `WP_ENVIRONMENT_TYPE=staging` → `App::get('env')`='staging'
- [ ] `phantom.env.json` secret value never appears in log
- [ ] Error handler inside WP error surface only emits once
- [ ] Container resolves `App` without side effects at class-load
- [ ] CI-visible smoke: `wp eval 'e(Phantom\Core.php)'` prints `'Phantom\Core'`

*(End Part A)*
# PART B — PHASE 2: FRAMEWORK INFRASTRUCTURE

**Purpose:** Install the structural primitives every later phase depends on: Service Providers, Event Dispatcher, Hook Manager, Dependency Injection container, Registry, Factory, Config Manager, Cache.

**Principles applied:** composition over inheritance; auto-wiring where safe; then explicit bindings; separation: WP actions = events forwarded to `phantom_` domain events.

---

## Phase 2 — Framework Infrastructure

**Responsibilities:** container resolution, event bus (domain), hook manager (WP boundary), registry/factory for singletons, cached read path, immutable config.

**Public APIs:**

- `Phantom\Core\Container\Container` (PSR-11 + builder):
  - `register($key, callable|class, array $options = [])`
  - `get(string $id): mixed`
  - `has(string $id): bool`
  - `set(string $id, $value)`
  - `singleton(string $id, callable $factory)`
- `Phantom\Core\Events\Dispatcher::dispatch(object $event)`, `Dispatcher::listen(string $event, $listener)`, `Dispatcher::map(string $wpHook)`.
- `Phantom\Core\Hooks\HookManager`:
  - `addAction(string $wpHook, callable $handler, int $priority, int $args=1)`
  - `addFilter(string $wpHook, callable $handler, int $priority)`
  - `apply(string $value, array $args)` (proxy for idempotent calls)
- `Phantom\Core\Registry\RegistryInterface`, `Phantom\Core\Registry\ArrayRegistry`, `Phantom\Core\Registry\DynamicRegistry`.
- `Phantom\Core\Factory\SimpleFactory` (wraps container factory methods).
- `Phantom\Core\Config\Repository`: `get(string $key, $default=null)`, `set(string $key, $value)` (immutable except flagged), `all(): array`.
- `Phantom\Core\Cache\CacheInterface`, `Phantom\Core\Cache\TransientCache`, `Phantom\Core\Cache\ObjectCache` (wraps `wp_cache_*`).

**Internal APIs:**
- `Container\Definition` (abstract binding with build + scope).
- `Events\EventInterface` (marker + `name(): string`).
- `Hooks\HookBinding` (action,priority,filters).
- `Cache\CacheKey` (namespaced with source + version).

**Dependencies:** Phase 1 (`App`, `ConfigLoader`, `Env`, `FeatureFlags`, `DebugLogger`). Third-party: none (rely on PSR-11 style, WP object cache).

**Classes / Interfaces / Traits / Services:**

```
Phantom\Core\Container\
    Container.php            (PSR-11 + fluent insert)
    Definition.php           (immutable binding)
Phantom\Core\Events\
    Dispatcher.php
    EventInterface.php
    StoppableEventInterface.php
Phantom\Core\Hooks\
    HookManager.php
    WpBridge.php             (thin adapter to add_action/add_filter)
Phantom\Core\Registry\
    RegistryInterface.php
    ArrayRegistry.php
    DynamicRegistry.php      (Setools: measurer to lazy factory)
Phantom\Core\Factory\
    SimpleFactory.php
    FactoryInterface.php
Phantom\Core\Config\
    Repository.php
Phantom\Core\Cache\
    CacheInterface.php
    TransientCache.php
    ObjectCache.php
    CacheKey.php
```

**Data flow:**

```
[Provider bootstrap]
  └─ providers register bindings → Container
        └─ config Plugin → Repository → CacheTagged
            └─ Dispatcher binds events → HookManager
                └─ plugins/libraries consume via App::get()
```

**Event flow:** Domain events (`phantom_core:*`), WP actions bridged: `HookManager` sniffs `admin_init`, `wp_enqueue_scripts`, `wp_body_open` and dispatches `phantom_core:wp_{event}`.

**Hook usage:** documented only on the WPC boundary: connect `wp_enqueue_scripts` → `phantom_assets` (Ph 7); template sections on `wp_body_open`/`wp_footer`. No deep WP private hooks.

**Performance:** lazy service instantiation (only on resolve); container does zero reflection in prod when possible (explicit `register` closure). Cache tag names tiny.

**Accessibility:** none (infrastructure).

**Security:** container `get` never executes unvalidated dynamic classes from external input; `__CLASS__` resolution uses allowlist; no `eval`.

**Testing strategy:** PHPUnit container resolution (binding, singleton, factory override), Dispatcher dispatch/listener ordering, HookManager does not double fire, Cache with `wp_cache_*` mocked (WP Mock).

**Risks:** circular dependencies; mitigation `Container` detects cycle and throws informative `Container\CircularDependencyException`; failure self-tests.

**Rollback:** whole subsystem `Phantom\Core` disabled by `phantom_feature_phantom_core`; container has `has()` gate.

**Acceptance criteria:** Unit suite 100% green; container resolves an arbitrary service both `singleton` and `transient`; dispatcher re-invokes in registration order; App->get() will return cached config with `all()`.

**Verification checklist:**
- [ ] `Container::get('logger')` same instance on 2nd call (singleton)
- [ ] `Dispatcher` event with two listeners fires in order
- [ ] DynamicRegistry lazy-build invokes factory exactly once
- [ ] Config contains `team` env arrays (no stray)
- [ ] PHPStan level 4 passes on this directory

---

## Phase 3 — DESIGN TOKEN ENGINE

**Purpose:** Provide a single, typed system of design tokens (colors, type, space, radius, shadow, motion, size, grid, breakpoints, z-index) with presets, inheritance, JSON/SCSS/TS parity, and per-scope CSS variable generation — the CSS-variable foundation for every component.

**Public APIs:**

- `Phantom\Core\Tokens\TokenRepository`
  - `tokens(string $context = 'all'): array`
  - `token(string $name): mixed` (throws `UnknownToken` in debug)
  - `css(string $scope='phantom'): string` (rendered `:root`/`[data-phantom-theme="x"]` styles)
  - `resolve(string $name)` — returns resolved value (inheritance-aware)
- `Phantom\Core\Tokens\TokenSource`
  - `parse(array $definition)`, `preset(string $slug): array`
- `Phantom\Core\Tokens\Invariant` (compute/validate band: requires every fallback exists)
- `Phantom\Core\Tokens\TokenFactory` converts defined tokens to CSS var map.

**Internal APIs:**
- `Tokens\Preced` — precedence collector (default → preset → override).
- `Tokens\Renderer\CssRenderer` — emits CSS custom props.
- `Tokens\Loader\DataProvider` — reads `assets-src/tokens/*.json`, `presets/*.json`.
- `Tokens\Resolver` (walk inheritance graph `extends`).

**Dependencies:** Phase 1/2 container + config; access PHP `wp_json_encode` only for serializer.

**Token groups** (canonical §):
- **Color**: `color.bg`, `color.fg`, `color.accent`, `color.border`, `color.muted`, semantic memory variants.
- **Typography**: `font.sans`, `font.serif`, `font.mono`, `type.size.X`, `type.line.X`, `type.weight.*`.
- **Spacing**: `space.*` (4pt scale), `space.section`, `space.gutter`.
- **Radius**: `radius.sm`, `radius.md`, `radius.lg`, `radius.pill`.
- **Shadow**: `shadow.card`, `shadow.pop`, `shadow.focus-ring`.
- **Motion**: `motion.duration.fast/slow`, `motion.ease.*`, reduced-motion flag.
- **Layout**: `layout.max`, `layout.gutter`, `layout.stack`.
- **Grid**: `grid.cols`, `grid.gap`.
- **Breakpoints**: `bp.sm/md/lg/xl` (1: 640/768/1024/1280).
- **Z-index**: `z.header/z.modal/z.tooltip`.
- **Component tokens**: `component.button.*`, `component.card.*`, alias to above.

**Inheritance:** preset base → semantic layer → component layer; each layer can override any key; CSS var naming `--phantom-color-bg`.

**CSS variable generation:** one file `tokens.php` builds `:root` plus `[data-phantom-theme="dark"]` variant; theme presets (default, ocean, noir, ivory…) select presets; no runtime CSS generation per request (cache tokens once via `TransientCache`).

**Performance:** token resolution cached in Opcode/Transients; `tokens.css` served from `assets-src/dist/tokens.css` when Vite build present (Phase 7), PHP fallback only during phase dev; production = static.

**Accessibility:** chosen presets hard-gate contrast; `Resolve contrast(2)` warn in CI; reduced-motion token maps to `prefers-reduced-motion` media.

**Security:** only static JSON read (no evaled CSS); validate `token` names against `/^[a-z][a-z0-9\-]{0,63}$/` to prevent injection into selector.

**Testing:** unit tests resolve inheritance, change via preset, token default overrides, CSS var output; `Invariant` test ensures dark preset AA.

**Risks:** token explosion → governance: `tokens/<group>.schema.json` limits additions; only documented tokens enter public.

**Acceptance:** `tokens('color' )` → validated; `CssRenderer` emits valid `:root` block for default + dark; unit test: spacing `space.4` = `0.25rem`.

**Verification checklist:**
- [ ] `TokenRepository::token('color.accent')` returns hex
- [ ] CSS cascade check: custom property resolves in browser
- [ ] Contrast pair (fg/bg) computed 4.5:1 for default preset
- [ ] No token name with invalid chars in any value
- [ ] Preset switch flips `:root` block to full alternate palette

---

## PHASE 4 — RENDER ENGINE

**Purpose:** Convert data → view models → raw HTML string (renderless) via renderers. Component engine consumes templates; data adapters deliver data in normalized DTO shape; template resolvers announce chosen template; layout composition via hooks; final string cached safely.

**Public APIs:**

- `Phantom\Core\Render\Renderer::render(string $view, array $data = []) : string`
- `Phantom\Core\Render\TemplateResolver::resolve(string $slug, array $context): string`
- `Phantom\Core\Render\Layout::push(string $region, string $block, array $args = [])`
- `Phantom\Core\Render\Layout::flush(string $region): string`
- **ViewModels:** `Phantom\Core\Render\ViewModel` (plain data bags), generated `Map`.
  - Some optional lazy DTO decorators.
- **Adapters (Data):** `Phantom\Data\PostAdapter`, `TermAdapter`, `UserAdapter`, `MenuAdapter`, `SiteAdapter`, `SettingsAdapter`, `TaxAdapter`, `Wp_Query_Adapter`.

**Public contract:** any component receives a `Phantom\Core\Render\ViewContext` (dest size-safe data properties; escaping helpers `esc_html` designated per field).

**Internal runtime:**

```
render → resolve template (Twig/Latte-like? → decision) → run view-model transformation
      → Buffered output → finalize (enclose by WP API: get_header/footer)
```

**Template engine choice:** Recommend **Twig** (high adoption, strict sandbox, escaping built-in) with a thin `TemplateRenderer` adaption, and touch extended GP templates via PHP (GP templates remain PHP). Alternatively native PHP + helper; tradeoffs arc.

- Trade-off table:
| Option | Pros | Cons | Verdict |
|---|---|---|---|
| Native PHP templates | no dep, small | manual escaping, less DX | prefer 2nd |
| Twig (latasing Twig\\Loader\\FilesystemLoader) | escaping, auto-sandbox, strict | 3rd-party dep | **adopt** |
| Latte | PHP-like, lighter | ecosystem smaller | acceptable alt |

=> **Prefd Twig with `TemplateRenderer`**, kept revertable behind a config flag `render.engine`.

**Data flow:**

```
DB/WP → Adapter → ViewModel (DTO) → Renderer (Twig) → string
     → Layout (region→buffer) → full page
```

**Caching:** Render cache by (view, hash(viewmodel)) with `TransientCache`; disabled for `is_user_logged_in()` to avoid stale content.

**Error handling:** Renderer max-10% error → generated content with visible `exceptions` collected by `ErrorHandler`; in prod replace with fallback block (never `die`).

**Performance:** Twig compiled/cached ``cache`` metrics; null collection `is_ajax` skip; DTO small.

**A11y:** ViewModel passes `aria` fields; renderer emits standard landmark wrapper params.

**Security:** no `eval`, no magic `render` on user input; `Twig sandbox` restrict include; escaping policy.

**Testing:** YoView: `render('button', ...)` yields expected; template resolver fallback to child (child → phantom → default); layout stack output order; cache cacheSample on user-logged false.

**Risks:** WP template not matching render engine → mitigation map (`TemplateResolver` maps to WP template tags).

**Acceptance:** `Renderer::render('card.twig', $data)` produces valid HTML, escaping applied, and `<encapsulate>` works with a plain `generate_` hook.

---

## PHASE 5 — COMPONENT REGISTRY

**Purpose:** Central registry for UI components: registration, discovery, versioning, dependencies, slots, variants, composition rules. Components are pure presentational with data passed in.

**Public APIs:**

- `Phantom\Core\Components\Registry::register(string $name, string $renderer, array $meta = [])`
- `Registry::get(string $name): ?ComponentDefinition`
- `Registry::versions(string $name): array`
- `Registry::resolveDependencies(): void`
- `Registry::render(string $name, array $props = []): string`
- `Registry::providesSlot(string $name): bool`

**ComponentDefinition** = name, file, slug, `data` schema, `variants` map, `slots` list, `deps` (other component names), version.

**Discovery/config:** JSON `components.json` per instance + PHP `register` APIs in app code; merged once by `Registry`.

**Versioning:** components instance-versioned; tokens `@` stable; `Registry::versions` returns published int.

**Dependencies:** resolve graph on `Registers`, detect cycles → throw.

**Slots:** each component declares slots that accepting a hierarchy (headers, actions, footer of card); slot children registered via `<block>` in Twig.

**Variants:** component `render(name, ['variant' => 'primary'])` swaps internally to variant definition export.

**Composition rules:** components compose only via props/slots; never inspect WP globals; data must come via Data adapters; components never call `wp` functions in render scope (they return strings).

**Public DSL:** `<phantom.card>`, `<phantom.button>`, … registered as shortcodes so authors can write `[phantom:button text="go"]` in backend content; each shortcode is a discoverable component.

**Internal APIs:**
- `Registry\Loader`, `Registry\DefinitionCompiler` (schema validation), `Registry\CycleDetector`, `Registry\Resolver` (find best variant slot).

**Acceptance:** register a dummy component and render via registry; shortcode `[phantom:button]` renders same as direct call; dependency cycle throws message.

**Verification:** inventory check `Registry::all()` includes `Ph 11` full list later; each has `version >= 1`; variants validated against tokens groups.

---

## PHASE 6 — TEMPLATE SYSTEM

**Purpose:** A first-class template file hierarchy + layout system + override resolution + partial loader + dynamic sections — while still bending to the WP template rules (child theme templates in `templates/`).

**Design:**

- WP native template resolution (parent `template`) delegates to child: returned to `templates/`.
- `Template Resolver` enhances: if `templates/header.php` + child `content-single.php` exist, respect both implicitly. `ThemeTemplatesBridge` prepares default; extreme override via register.
- **Layout composition:** templates call `Layout::RFence` that calls sections returned by partial `header.php`, `content-single`, `sidebar`, `footer`. Sections render via component names (e.g., `yield('header')` = `[phantom:header]`).
- **Override table** (priority: most-specific wins):
  1. `templates/{$override}/{$slug}.php`
  2. `templates/{$slug}.php`
  3. `wp-{name}.php`
  4. fallback: parent theme `template.php`.
- **Child overrides** stay physical: copy respected `content*, index`, `header` then `generic comments/404` patterns, keeping `comments_template()` intact for comment plugins.
- **Partial loading:** `partial('card', $args, $name)` in Twig; PHP  twin `get_partial( $name, $args )` local to `templates/partials`.
- **Dynamic sections:** `Adding_content('loop')` filters; section registry `Phantom\Content\Sections::register($region, $renderable)`.

**Public API:**
- `Phantom\Core\Templates\Resolver::path(string $name): string|null`
- `Renderer::render('single')` auto-composes via section.
- `Layout::render_sections(string|\ArrayAccess $regions)`.

**Acceptance:** `single.php` template chosen by `Resolver` is the child file; `content-single` partial loaded; a registered section appended to `loop` emerges; missing partial thrown-aware fallback to `index`.

**Verification:**
- [ ] `single` resolves to `phantom/templates/single.php`
- [ ] comment template still `generate`-touches via `comments_template()`
- [ ] partial "card" engine found
- [ ] WP-hierarchy matches table in Probe 3

*(Parts 1–7 continue in Part C)*
# PART C — PHASE 7: ASSET PIPELINE
**Note: Phase X numbering in this document follows the user's canonical phase order. The phases in Part B were condensed for readability; Part C expands.**

## PHASE 7 — ASSET PIPELINE

**Purpose:** Modern build pipeline (Vite) that compiles SCSS → CSS and TS/ESM → JS, bundles vendor assets, loads fonts/images, emits a manifest for the runtime asset loader, and wires cache busting + lazy loading — without ever altering GP/Premium files.

**Responsibilities:**
- SCSS organization: `assets-src/scss/{foundations(tokens), base, components, layout, pages, vendor-overrides}.scss`.
- JS modules: `assets-src/ts/{core, registry, components, animation, bridges, main}.ts`; ES module output, deferred, split-by-entry.
- Vendor assets: import via npm (`gsap`, `lenis`, `three`), bundled, no CDN.
- Fonts: woff2 local + `font-display: swap`; tokenized font list in tokens.
- Images: `respons-awards` via Vite copy + `srcset` helpers (`Phantom\Core\Assets\Image::srcset()`).
- **Vite manifest** read by `Phantom\Core\Assets\AssetLoader` (dev → Vite server URL, prod → manifest map).
- Cache busting: hashed filenames (`assets/index-abc123.css`) naturally; `AssetLoader::enqueue()` uses manifest hash.
- Lazy loading: `loading="lazy"` default for `img`/`iframe`, `defer` + `modulepreload` for JS; intersection-observer driven reveal.

**Public APIs:**
- `Phantom\Core\Assets\AssetLoader::css(string $handle): void`
- `AssetLoader::js(string $handle, array $deps = [], bool $inFooter = true): void`
- `AssetLoader::assetUrl(string $src): string` (reads Vite manifest or dev URL)
- `AssetLoader::fontFace(array $f)`: emits `@font-face` via `wp_add_inline_style`.
- `Phantom\Core\Assets\Image::srcset(int $id, array $size): array`
- `Phantom\Core\Assets\Markup::deferAll()` / `preloadCriticalCss()`.

**Internal APIs:**
- `Assets\ManifestReader`, `Assets\DevServer` (detects `vite dev` port via env `PHANTOM_VITE_PORT`), `Assets\BuildFingerprint`.
- `Assets\Pipeline\Entries` (maps handle → output), `Assets\DepsResolver`.

**Dependencies:** Phase 0 (package.json/vite config) + Phase 3 (tokens emitted into `tokens.css`), Phase 1 (App config w/ asset paths).

**Directory:**
```
assets-src/
  scss/
    foundations/_tokens.scss        (imports the PHP generated tokens)
    base/_reset.scss, _typography.scss, _forms.scss
    components/{button,card,header,nav,footer,...}.scss
    layout/{_grid,_regions}.scss
    pages/*.scss
  ts/core/registry, tslib
  ts/components/*.ts
  ts/animation/*.ts        (see Phase 10)
  fonts/ woff2
  images/ (logo, textures)
  vite.config.ts
```

**Data flow:**

```
npm run build → Vite → dist/manifest.json + hashed files on disk
PHP asset loader reads manifest → enqueues matching handles
```

**Font loading:** `@font-display: swap`; per `docs/fonts.md` define preload main typefaces but never block first paint.

**Performance:** tree-shaken entry points; code-split `animation`, `commerce`, `editor` only when feature flag on; MSR; `preload critical CSS` inline `~2KB` once critical tokens loaded.

**Accessibility:** `prefers-reduced-motion` respected at CSS level (transformations gated); images always `alt`; no motion in `@media (prefers-reduced-motion: reduce)`.

**Security:** no remote font/JS fetch (self-hosted); SRI optional; CSP-friendly (no `eval` in prod bundles).

**Testing:** `assertCount` per entry bundle; `rimraf dist` clean; manifest hashes match files; JS runs under unit test (Playwright smoke includes loaded CSS var tokens).

**Risks:** Vite dev/prod mismatch → manifest fixture tests; node version pin.

**Acceptance criteria:** `vite build` produces `dist/manifest.json`; `AssetLoader::asset('scss/main.scss')` returns hashed prod URL; CSS loads in browser with tokens present; JS entry interactive.

**Verification:**
- [ ] `npm run build` succeeds with `vite --version` pinned
- [ ] output CSS contains `--phantom-*` vars from tokens
- [ ] `AssetLoader` uses `manifest` in prod and `localhost:5173` in dev
- [ ] `font-display: swap` in compiled font CSS
- [ ] no render-blocking third-party; lazyload verified by screenshot

---

## PHASE 8 — PLUGIN BRIDGES

**Purpose:** Provide thin, isolated adapters so Phantom Core never references vendor-specific classes directly; every vendor plugin (WooCommerce is Phase 9) is accessed through a `Phantom\Core\Bridges\<Plugin>` facade. The bridge is the single choke point: version floors, feature detection, sanitized data shaping.

**Design:** each bridge = `BridgeInterface` with `init(): bool` (active?), `isActive()`, `getVersion()`, and capability methods. Bridges registered anonymously in `Bridges/Registry` bound on `plugins_loaded`.

**Support list (target Matrix):**

| Plugin | Bridge class | Capability surface |
|---|---|---|
| ACF | `Bridges\Acf\AcfBridge` | fields(), subFields(), image, group, repeater via adapter |
| Rank Math | `Bridges\RankMath\RankMathBridge` | get meta title/desc/robots |
| Yoast   | `Bridges\Yoast\YoastBridge` | same canonical |
| WPML | `Bridges\Wpml\WpmlBridge` | `getLocale`, `getLangs`, `isTranslated` |
| Polylang | `Bridges\Polylang\PolylangBridge` | same |
| Fluent Forms | `Bridges\FluentForms` | embed shortcode/ATOM |
| Gravity Forms | `Bridges\Gravity\GravityBridge` | embed by ID, enqueue assets |
| WPForms | `Bridges\Wpforms\WpformsBridge` | embed |
| BuddyPress | `Bridges\Buddypress\BuddyBridge` | avatar/url |
| bbPress | `Bridges\Bbpress\BbpressBridge` | topic/reply def |
| LearnDash | `Bridges\Learndash\LearndashBridge` | course/enroll status |
| The Events Calendar | `Bridges\Tec\TecBridge` | events list/tickets |

**Public API (generic):**
- `BridgeManager::get(string $slug): ?BridgeInterface`
- `BridgeManager::all(): list<BridgeInterface>`
- each custom `request` method documented per phase.

**Internal APIs:**
- `Bridges\HealthCheck` (checks plugin version floor, saved deps, optional-only), `Bridges\Registry` (lazy load), `Bridges\FeatureMatrix` (read `docs/plugins.md`).

**Acceptance:**
- For every supported plugin that is installed+active, bridge loads; if not, `supports` returns false and Phantom never throws.
- Bridge feature never calls vendor method unguarded (`function_exists`, `class_exists`).

---

## PHASE 9 — WOOCOMMERCE BRIDGE

**Purpose:** Full WooCommerce isolation: product/cart/checkout/account adapters, hook preservation, HPOS, Blocks compatibility.

**Design:**
- `Phantom\Woo\WooBridge` extends `BridgeInterface`, enabled when `class_exists('WooCommerce')`.
- Preserve ALL official WooCommerce hooks: we never remove; we re-emit where our template replaces output (guarded by `has_action` checks).
- **Adapters:**
  - `Woo\Data\ProductAdapter` (id, name, price, images, gallery, rating, stock, meta query),
  - `Woo\Data\CartAdapter` (items, totals, count),
  - `Woo\Data\CheckoutAdapter` (fields schema, order id),
  - `Woo\Data\AccountAdapter` (nav, pages, current user).
- **HPOS:** hooks into `woocommerce_` order data; use `wc_get_order` / `Orders` CRUD unofficial; reading through `OrderAdapter::byId()`.
- **Blocks compatibility:** we do not replace WooCommerce Blocks; ghost template overrides apply legacy templates only when `use_blocks` off; registry stays.

**Public API:**
- `WooBridge::isActive()`, `WooBridge::product(int $id): ProductDTO`, `WooBridge::cart(): CartDTO`, `WooBridge::checkout(): array`, `WooBridge::account(): array`.

**Hook preservation table (must equal all of the standard Woo commerce template hooks):**
- `woocommerce_before_main_content`, `woocommerce_after_main_content`, `woocommerce_before_shop_loop`, `woocommerce_shop_loop_item_title`, `woocommerce_after_shop_loop_item`, `woocommerce_after_shop_loop_item_title`, `woocommerce_after_shop_loop`, `woocommerce_pagination`, `woocommerce_before_single_product_summary`, `woocommerce_single_product_summary`, `woocommerce_after_single_product_summary`, `woocommerce_before_add_to_cart_form`, `woocommerce_before_add_to_cart_button`, `woocommerce_after_add_to_cart_button`, `woocommerce_after_add_to_cart_form`, `woocommerce_after_add_to_cart_quantity`, `woocommerce_before_quantity_input_field`, `woocommerce_after_quantity_input_field`, `woocommerce_meta`, `woocommerce_share`, `woocommerce_single_product_image_thumbnail_html`, `woocommerce_after_single_product`, `woocommerce_before_checkout_form`, `woocommerce_checkout_before_customer_details`, `woocommerce_checkout_after_customer_details`, `woocommerce_checkout_billing`, `woocommerce_checkout_shipping`, `woocommerce_checkout_order_review`, `woocommerce_after_checkout_form`, `woocommerce_account_*`, `woocommerce_before_shop_loop_item`, `woocommerce_shop_loop_item_title`.

**Configuration:** `phantom.config.php` `woo.enable` toggles legacy template override; default `false` on Blocks dist.

**Acceptance:** with WC active, hooks, add-to-cart notify on both existing templates (auto feel), bridge returns DTOs w/o stale values; Blocks shop renders native block markup (mine untouched).

**Verification:**
- [ ] No emitted markup misses WC hooks (script test compares hook registry)
- [ ] HPOS switch flip produces same bridge output
- [ ] ACF/WP other 8 bridges loop-functional when plugins toggled

*(Parts 8–9 continue to Part D)*
# PART D — PHASE 10: ANIMATION ENGINE

**Purpose:** A declarative animation system (GSAP + Lenis smooth-scroll + Three.js) driven by a registry, gated by performance budgets and `prefers-reduced-motion`.

**Responsibilities:**
- `Phantom\Core\Animation\AnimationRegistry` — register/render named animation behaviors.
- GSAP: entrance, reveal, counter, stagger, timeline presets via `gsap.to/from` data attributes.
- Lenis smooth scroll (op-in, feature flag), harmonized with GP sticky nav/back-to-top.
- Three.js scoped to hero/canvas sections only; loaded as a dynamic import (code-split) — never global.
- Scroll triggers: IntersectionObserver -> GsapScrollTrigger (`ScrollTrigger.create`) for scroll-driven timelines; all guarded.
- Reduced-motion: `@media (prefers-reduced-motion: reduce)` sets duration 0 + Lenis disabled; `motion.reduced` token consumed.
- Performance budgets: JS load ≤ 120KB (uncompressed 3p libs lazy); 60fps base; no layout thrash (`will-change` only when animating).

**Public APIs:**
- `Phantom\Core\Animation\AnimationRegistry::register(string $name, Animation\Preset $preset)`
- `Registry::get(string $name)`
- `Phantom\Core\Animation\Lenis::enable()` / `disable()`
- `Phantom\Core\Animation\Three::withCanvas(string $mount, config)`
- `Phantom\Core\Animation\Scroll\Trigger::on(element, callback, opts)`

**Internal APIs:** `Animation\Engine` (controller runtime), `Animation\Preset`, `Animation\ReducedMotion`, `Animation\Breaking` (perf gates).

**Dependencies:** Phase 3 (motion tokens), Phase 4 (components render data-attrs), Phase 7 (asset pipeline bundles `gsap`, `lenis`, `three`).

**Data flow:**

```
render html data-anim="fade-up" + data-split
tokens motion.* → CSS transition class
animation module starts when element enters viewport
```

**Event flow:** `phantom_core:animation:ready`; scroll trigger binds on `document.load` readiness.

**Hook usage:** enqueue `phantom-animation` only when `registry` non-empty; `prefers-reduced-motion` via inline CSS guard.

**Performance:** tree-shaking; reduced-motion early exit (no listeners); RAF-tick budget; IntersectionObserver count cap (e.g., 40).

**Accessibility:** motion reducer gated; no content dependency on animation; `aria-live="off` on non-essential animated nodes.

**Security:** no `eval`; animations confined to registered preset names (allowlist patterns; no user-string to function).

**Testing:** Playwright verifies reduced-motion gating; three loads asynchronously with no `f12` leak; AnimationRegistry renders `data-*`; budget counts pass.

**Risks:** heavy Three on low end → feature-detect (GPU) and disable on `(prefers-reduced-motion: reduce)` + low-power mobile heuristics.

**Acceptance:** a preconfigured `reveal` preset animates on scroll; reduced-motion run leaves element static; Three canvas module-split alone > 8KB gated.

**Verification:**
- [ ] `data-phantom-anim="reveal"` lifts into GSAP on load
- [ ] prefers-reduced-motion gated (RR test) — no transforms
- [ ] three entry lazy-load only when `.phantom-canvas` exists
- [ ] perf bundle check passes

---

## PHASE 11 — FRONTEND COMPONENTS (INVENTORY)

**Purpose:** The exhaustive, dependency-ordered inventory of every UI component with props, variants, slots, tokens used, JS behavior, a11y requirements, and WCAG notes. (Implemented across Phases 3–5; Phase 11 produced the *complete defined list*. Registration happens as components are built.)

**Component catalog (grouped):**

| ID | Name | Tokens used | Props | Variants | Dependencies | a11y |
|---|---|---|---|---|---|---|
| `button` | Button | color.accent, space, radius, motion | label, href, variant, size, icon, aria-label | primary/ghost/outline/link | icon | contrast AA |
| `card` | Card | color, shadow, radius, spacing | title, media, meta, body, actions (slot) | default, media-left, accent | icon, image | heading order |
| `header` | Site header | color, z.header | sticky, logo, nav, cta | transparent, solid | nav, button, search-modal | skip-link, landmark |
| `nav` | Primary nav | fonts, space, motion | items, current, position | horizontal, vertical, inline | menu adapter, submenu | keyboard, focus trap |
| `search-modal` | Search modal | color, z.modal, motion | placeholder, url | | icon | focus trap, esc-close |
| `mobile-menu` | Off-canvas nav | motion, z | items | | nav, modal | focus trap |
| `card` (grid) | Post grid | spacing, grid | query, columns | grid, list | card, link | semantic list |
| `feature` | Feature block | space, accent | icon, index | | icon | |
| `hero` | Hero section | color, motion, type | title, eyebrow, media, heights | media-left, stacked | reveal | structured heading |
| `testimonial` | Testimonial | type, space | quote, author | carousel/static | | |
| `cta` | CTA band | accent, spacing | title, action | / | button | |
| `section` | Section layout | space, layout | id, label | | panel | landmarks |
| `footer-widgets` | Footer widgets | color, space | cols, widgets | | widget-areas | list |
| `crumb` | Breadcrumbs | type, accent | trail, markup | static | link | accessible |
| `toc` | Table of contents | type, space | headings | nested | link, scroll | skip |
| `image` | Responsive img | — | id, sizes, alt, loading | srcset | none | alt |
| `icon` | Icon | color | name (Sprite) | | svg use | aria-hidden |
| `media` | Slider | motion | images | galley | image, arrows | keyboard |
| `lightbox` | Lightbox | z.modal, motion | id | base | modal | focus trap/esc |
| `form` (fields) | Form fields | color, space | label, name, type, required | | | aria-describedby |
| `select` | Native-compat select | | options | styled | | label |
| `rating` | Product rating | color | post | stars | icon | text alternative |
| `price` | Price box | color, type | price, sale | | | |
| `add-to-cart` | Cart cta | color | id, quantity | | form | |
| `shop-card` | Product card | color, space | query per product | | image | focus |
| `account-menu` | WC account nav | | items | | nav | current-pg |

**Per-component spec template** (apply to every inventory row above; recorded in `docs/components/<name>.md`):
```
Params: id(required), title, status
Props: [name:type (default)]
Variants: {v1: {props}}
Slots: {content, actions}
Dependencies: [componentNames]
JS: module entry (file), event: onMount/reveal
CSS: token scope, class root (e.g., .phantom-card)
a11y: role/ARIA, keyboard keys, focus, contrast pair, reduced-motion behavior
Tests: unit (scope) + Playwright (interaction) link
```

**Dependencies:** Phases 3,4,5,7,10; each entry already tested in its own phase.

**Acceptance:** each component in catalog renders via `[phantom:<name>]` shortcode or `Renderer::render(name, props)`, passes ax counts, respects reduced motion, in tokens.

**Verification:** inventory `Registry::all()` equals catalog rows; per-component docs exist w/ a11y contrast pair recorded; e2e specs pass.

---

## PHASE 12 — FRONTEND TEMPLATES (MAP)

**Purpose:** Map every WP & WooCommerce template to its composing components + layout regions. This is the *composition contract* between template files and the registry.

**Mapping tables (WP):**

| Template (slug) | File in phantom/templates | Layout regions | Components compose |
|---|---|---|---|
| home/index | home.php | header,yields,foot | hero?, section, card grid, cta, footer |
| archive (generic) | archive.php | header, | section, grid | 
  ...
(belated continuation below)

**Mapping (Woo):**
| Template | regions / components |
|---|---|---|
| archive-product | shop loop `product cards` |
| single-product | gallery, price, add-to-cart, tabs, rating, related |
| cart | cart summary, cart item, totals |
| checkout | form steps, order review |
| my-account | menu, dashboard, orders, addresses |

**Design:** `Templates\Composer` automaps a slug → region → component sequence; override per template via `Config`. Templates execute `Layout::render_regions()` and pull components from registry; they may conditionally chain using actual cascading.

**Acceptance:** every WP template copies `templates/{slug}.php` with proper `Layout` call; each references only registry components; Woo pages use `WooBridge` only (never direct `wc_` in template).

**Verification:** map-check script asserts no leftover `get_template_part` that bypasses registry; every `woocommerce_*` preserved; post-level `comments_template()` intact; no plugin-hacks file copied.

*(Parts 10–12 continue to Part E)*
# PART E — PHASE 13-17: CROSSCUTTING + RELEASE

## PHASE 13 — PERFORMANCE

**Purpose:** enforceable budgets for Core Web Vitals and asset delivery, query efficiency, caching, lazy loading. The performance plan is a *spec* + audit harness, applied retroactively to all phases.

**Performance budgets (defaults, override in `phantom.config.php`):**
- LCP < 2.0 s, CLS < 0.05, INP < 150 ms (mobile mid).
- JS per-route ≤ 120 KB core (production), ≤ 60 KB non-default vendors shipped to route.
- CSS ≤ 50 KB (core), inline critical ≤ 3 KB.
- Full-page server time ≤ 300 ms p75 (no cache), ≤ 100 ms p75 cached.

**Strategy:**
- СР-empty query budgets: `# queries ≤ 8` p75: db queries per request cap via `WP_QUERY_TEST`+ limit in CI.
- DB: cache across `TransientCache`; term/post caches; no N+1: `prime_post_caches`, `update_post_caches` usage; dedicated `queryCache` keyed by `(type, params)`.
- Image: `Image::srcset` deterministic; `loading=lazy` (below fold); WebP/AVIF via core; plugin always runs from `wp_generate_attachment_metadata`.
- JS: code split via Vite entry per context (front, shop, admin + vendor chunks); `defer` all; script load late (ph content).
- CSS: aggregated final; critical via `critical.css` plugin at build time; `font-display: swap`; only load font weights present on page.
- Caching: page cache-agnostic but WP Transients + objects for tokens, nav fragments, and render caches; `CacheKey` namespace = version; flush on `save_post`, `menu`, `sideowcache`.

**Public APIs:**
- `Phantom\Core\Performance\BudgetLogger::check()`
- `Phantom\Core\Performance\QueryGuard::limit(int $n)` (debug-only introspection)
- `Phantom\Core\Performance\Lazy`
- `Phantom\Core\Performance\CachePurger::purge($domain)`

**Testing:** CI Lighthouse on a staging page (tables: LCP/CLS/INP budgets) + `perf` unit asserts count of queries via a WP_Mock DB spy.

**Acceptance:** budgets meet; query cap triggers warning in debug only; purge works after `save_post`.

---

## PHASE 14 — ACCESSIBILITY (WCAG 2.2 AA)

**Purpose:** WCAG 2.2 AA is a code requirement with an a11y spec per component and an enforcement gate in CI.

**Requirements by area:**
- **Color:** every paired color has AA contrast (token invariants); focus ring ≥ 3px visible (uses `--shadow.focus-ring`).
- **Keyboard:** all interactive components keyboard-operable; tab order logical; visible focus; no keyboard trap; skip-link (link / `#main`).
- **Focus:** `:focus-visible` micro only; `aria-hidden` for decorative; reduced motion honored.
- **Forms:** `<label>` each; `aria-invalid` + error linked `aria-describedby`; error summaries user-announced.
- **Images/media:** alt required; functional images text; media captioning.
- **Structure:** heading levels sequential (single H1); landmarks (`header`, `nav`, `main`, `footer`, `aside`); `aria-current` on nav.
- **Dialog/modal:** `role="dialog"` + `aria-modal`; focus trap; `Esc` closes; restores focus.
- **Motion:** `prefers-reduced-motion: reduce` disables reveal/scrolled/animate effects (CSS + JS fallback).
- **WCAG 2.2 adds:** Dragging alternative, Target Size (AAA opt; `min 24px` + 44 target space), focus not obscured.

**Public API:**
- `Phantom\Core\A11y\Checker::run(string $html): array`
- `Phantom\Core\A11y\SkipLink`
- `Phantom\Core\A11y\DialogManager` (focus trap impl)

**Interfaces:** all a11y at component DOM contract; registry applies defaults.

**Testing:** `axe-core` in Playwright on key templates + token contrast CI matrix (JS script computes ratio).

**Acceptance:** axe full pass (WCAG A/AA) on home, single, archive, shop, product, cart, checkout, my-account; focus flow passes; reduced-motion test passes.

**Verification:**
- [ ] axe 0 critical/0 serious
- [ ] contrast pairs AA
- [ ] keyboard walkthrough recorded

---

## PHASE 15 — TESTING

**Purpose:** layered suite: unit → integration → E2E (Playwright) → visual regression (backstop-like) → perf (Artifact/WebHint) → compat (matrix) → update regression (local WP env).

**Stack** (Phase 0 installed): PHPUnit + Brain Monkey; Playwright TS; `visual diff` runs on 5 canonical pages; Lighthouse CI for perf; matrix tests against WP 6.9 / PHP 8.1–8.3 + Woo active/block/gateway toggles.

**Test inventories per layer:**
- **Unit:** components (render), tokens (resolve/css), config, hooks, cache, container, renderer, template res.v., WOOshop DTOs.
- **Integration:** boot-to-render on `WP_SITEURL`-stubbed env; Gatling on templates via `wp` API test client.
- **E2E:** click-throughs: nav, search, cart, checkout, account; play on Blocks + legacy path.
- **Visual regression:** baseline gold files in `e2e/gold/*.png`, `--update-gold` only on announcement.
- **Perf:** Lighthouse budgets in CI.
- **Compat:** tests matrix across `PHANTOM_FEATURES` toggles.
- **Update regression:** upgrade parent theme (3.6.1→3.6.2) then full suite — prevents GP upgrade breakage.

**Public API:** CI targets `npm test`, `composer test`, `composer test:addon`.

**Acceptance:** unit+integration 100% green, e2e green on 3 browsers (chromium/firefox/webkit) core flow, axe pass, perf budget pass, visual-diff no unexpected change on release.

**Risks:** flaky visual → pixel-threshold config + flake retry CI.

**Verification:** `composer test` + `npm run test:e2e` green in CI before any stage ticket.

---

## PHASE 16 — REBRANDING (PLANNING ONLY)

**Purpose / boundaries:** Do NOT implement. Deliver a safe rebrand plan: what stays, what can change now, what is deferred.

**Can change immediately (UI/UX only):** brand name, colors, fonts, hero copy, footer text, site icon, logo assets, localized `aria`/title, menu labels. These are content-level — safe on all systems.

**Must remain for compatibility:**
- PHP namespaces `Phantom\Core`, prefix `phantom_`, CSS classes `phantom-*`, `--phantom-*` tokens, hooks `phantom_*`. (Renaming is a breaking API → defer.)

**Optional later (after extensive test + data map):**
- `wp_options` keys (from `phantom_`), DB rows, GA4/GTM identity, cookie names for consent; run `grep` audit = data-map sprint before touching.
- Rename theme slug `phantom` (impacts `get_template()`, GP templates, child pattern metadata).

**Plan doc:** `docs/rebranding.md` checklist with step-by-step; every change → a feature-flagged release.

**Verification:** after-brand stage: docs consistent; no leftover original brand in `assets-src` copy + meta.

---

## PHASE 17 — RELEASE

**Purpose:** automated, repeatable, gated release pipeline for the Phantom child theme + assets.

**Process:**
- **Build automation:** Git tag → `npm ci`, `vite build`, `composer dumpautoload -o`, `ph_cs`/`phpstan`/`phu`, unit+integration+E2E, Lighthouse, visual diff, WP update test, package zip (contents: `phantom/{app,assets-src/dist,templates,inc,style.css,theme.json,icu}`, no `.git`, no `node_modules`, no `*.md` dev-gated).
- **Quality gates (fail-stop):** CI test matrix zero fail, axe zero issues, Lighthouse budgets pass, PHPCS 0 errors, no secrets.
- **Semantic versioning** (`0.x`), `changelog` generated per commit (conventional commits); release note.
- **Rollback:** space: tag impossible BLOCKER; previous release kept on server; `phantom_feature_*` fallback; DB rollback migration never within `0.x` — migrations only in `1.x`+.
- **Distribution:** zip via `npx release` + straight `.zip` on GitHub Releases; theme extends GP (or plugin zip).

**Verification checklist (release):**
- [ ] Matrix tests green across WP 6.x/PHP versions
- [ ] axe 0, Lighthouse green
- [ ] CHANGELOG complete, version bumped in 4 files (style.css, package.json, state)
- [ ] zip signed/hash-verified
- [ ] Release notes link Report/features + ADRs

---

# FINAL DELIVERABLE NOTE

This engineering specification is the complete implementation plan for Phantom Core. Development executes **phase-by-phase** using `Report/MASTER_ROADMAP.md` as the tracker — no phase begins before its dependencies close; each phase ships independently testable work behind feature flags; all naming/tokens/hooks are locked in ADR-001..012; every WooCommerce hook is preserved; accessibility and performance are *enforced*, not appended.

*Authored from the source-of-truth audit + architecture deliverables listed in `Report/MASTER_ROADMAP.md` §Completed Work. Read-only planning; no code produced in this document.*
