# Design: Remove License Key System from Aureon Studio

**Date:** 2026-08-05
**Status:** Approved
**Scope:** `aureon/plugin` (Aureon Studio v3.0.0)
**Out of scope:** `aureon/theme` (verified: contains **zero** license code), feature modules, import/export/reset functionality.

---

## 1. Background & motivation

Aureon Studio is the rebranded premium companion plugin for the Aureon theme. It currently ships an
Easy Digital Downloads Software Licensing (EDD SL) setup that phones home to `aureonstudio.com` /
`https://example.com` with `item_name = 'Aureon Studio'`. This system was never functional for this
product (placeholder endpoints) and the user does not want any license key system: the product must
work fully with **no activation required**.

A prior session already removed the license key UI and the PHP container div for the React license
root, but left the minified dashboard bundle mounting into `#aureon-license-key`. Because the
container no longer exists, React 18.2's `createRoot(null)` throws production error **#299**
("Target container is not a DOM element"), which crashes that React root and logs a console error on
every dashboard load. Removal of the license component + mount fixes this crash as a side effect.

The user additionally wants a **clean, extensible seam** for a future commercial licensing/update
system: interface-based providers, with no-op ("null") implementations shipped today.

## 2. Verified current footprint

All license references are confined to (confirmed via grep across plugin + theme):

| File | License content |
|---|---|
| `aureon-studio.php:147-193` | `aureon_premium_updater()` — instantiates `Aureon_Premium_Plugin_Updater` (EDD phone-home), + `aureon_premium_set_updater_api_params` filter. |
| `library/class-plugin-updater.php` | Full EDD SL updater class (~680 lines), `Aureon_Premium_Plugin_Updater`. |
| `inc/class-rest.php:74-82, 171-306` | `/license/` route + `update_licensing()` (phone-home, key/status options); `/beta/` route + `update_beta_testing()` (beta option, only used by the license UI + updater). |
| `inc/class-dashboard.php:61, 289-304, 354-356, 363-375, 384-389` | `set_beta_tester` filter hook; `get_license_key()` (masked); localize keys `licenseKey`, `licenseKeyStatus`, `betaTester`; empty `license_key()` stub. |
| `inc/legacy/activation.php` | `aureon_license_errors()` (admin notices, 50-82); dead `$key` read (125); empty `aureon_activation_area()` (360-366); `aureon_premium_process_license_key()` (368-527, full phone-home handler incl. `wp_remote_post`); `aureon_license_missing()` update message (529-541); beta filter (543-555). |
| `inc/deprecated.php:578-600` | Four empty license wrapper stubs: `aureon_add_license_key_field`, `aureon_premium_license_key`, `aureon_save_premium_license_key`, `aureon_process_license_key`. |
| `dist/dashboard.js` | Minified license component (`g`, ~10154-13790) + `#aureon-license-key` mount — the #299 crash source. Also contains a **dead** "Site Library" tab branch referencing `aureonProDashboard.siteLibraryUrl` (never executed — no such module; left in place per minimal-change). |
| `dist/style-dashboard.css` | `.aureon-license-key-area` rules (dead after UI removal). |

**High confidence in isolation:** no feature loading is gated by license status. Modules load via
`aureon_package_*` options; the `$key = get_option('aureon_studio_license_key_status')` read in the
legacy dashboard is never used afterward (verified: dead variable). `inc/legacy/dashboard.php` has no
license references.

## 3. Decisions

- **Full removal** of the current licensing + updater implementation (option 1/3), **not** dormant
  retention.
- Replace with **interface-based provider seams** (option 4): `UpdateProviderInterface` +
  `NullUpdateProvider`, `LicenseProviderInterface` + `NullLicenseProvider`, wired through filters so a
  future commercial provider can be swapped in without restructuring.
- The beta-testing toggle is removed too — it only existed inside the license React component and fed
  the updater's `beta` param; it has no lifecycle apart from licensing.
- Keep the legacy dashboard's non-license features (module bulk activate/deactivate handlers,
  admin notices) intact.
- Delete `library/class-plugin-updater.php` entirely. Verify no `require`/hook/filter references it
  (only `aureon-studio.php:154` — being removed).
- Existing `aureon_studio_license_key` / `aureon_studio_license_key_status` /
  `aureon_studio_beta_testing` options are left in the DB (harmless — no code reads them after this
  change); not deleted at runtime.

## 4. Changes

### 4.1 `aureon-studio.php`

- Replace `aureon_premium_updater()` (147-172) and remove `aureon_premium_set_updater_api_params`
  (174-193). The updater function keeps its name, hook, and priority (stable public seam) but its
  body becomes a single call to the update provider (see below).
- Add:
  ```php
  require_once AUREON_STUDIO_DIR_PATH . 'inc/licensing/class-license-provider.php';
  require_once AUREON_STUDIO_DIR_PATH . 'inc/update/class-update-provider.php';
  ```
- Add seam hook (same timing the updater used):
  ```php
  add_action( 'admin_init', 'aureon_premium_updater', 0 );
  function aureon_premium_updater() {
      aureon_premium_get_update_provider()->init();
  }
  ```

### 4.2 Delete `library/class-plugin-updater.php`

### 4.3 `inc/licensing/class-license-provider.php` (new)

```php
interface Aureon_Pro_License_Provider {
    public function is_active(): bool;   // whether the product is licensed/unlocked
    public function get_status(): string; // 'valid' | 'none' ...
}
```

- `Aureon_Pro_Null_License_Provider implements Aureon_Pro_License_Provider` —
  `is_active() = true`, `get_status() = 'valid'` (null provider = everything unlocked).
- Accessor:
  ```php
  function aureon_premium_get_license_provider() {
      return apply_filters( 'aureon_studio_license_provider', new Aureon_Pro_Null_License_Provider() );
  }
  ```

### 4.4 `inc/update/class-update-provider.php` (new)

- `Aureon_Pro_Update_Provider` interface — `init(): void`.
- `Aureon_Pro_Null_Update_Provider implements Aureon_Pro_Update_Provider` — `init()` is a documented
  no-op (relies on standard WordPress update behavior).
- Accessor:
  ```php
  function aureon_premium_get_update_provider() {
      return apply_filters( 'aureon_studio_update_provider', new Aureon_Pro_Null_Update_Provider() );
  }
  ```

### 4.5 `inc/class-rest.php`

- Remove `/license/` route registration + `update_licensing()` (178-279).
- Remove `/beta/` route registration + `update_beta_testing()` (281-306).

### 4.6 `inc/class-dashboard.php`

- Remove `get_license_key()` (289-304).
- Remove localize keys `licenseKey`, `licenseKeyStatus`, `betaTester` (354-356).
- Remove `set_beta_tester()` (363-375) and its `aureon_premium_beta_tester` filter hook (61).
- Remove empty `license_key()` (384-389).

### 4.7 `inc/legacy/activation.php`

- Remove `aureon_license_errors()` (50-82).
- Remove dead `$key` line (125).
- Remove `aureon_activation_area()` + its hook (360-366).
- Remove `aureon_premium_process_license_key()` + its hook (368-527).
- Remove `aureon_license_missing()` + hook (529-541).
- Remove beta filter + `aureon_premium_beta_tester()` (543-555).
- **Keep:** `aureon_premium_dashboard_scripts`, `aureon_premium_notices`,
  `aureon_super_package_addons`, `aureon_multi_activate`, `aureon_activate_super_package_addons`,
  `aureon_deactivate_super_package_addons`, `aureon_premium_body_class`.

### 4.8 `inc/deprecated.php`

- Remove `aureon_add_license_key_field`, `aureon_premium_license_key`,
  `aureon_save_premium_license_key`, `aureon_process_license_key` (578-600).

### 4.9 `dist/dashboard.js` (minified surgical edit)

- Remove the license component `g` and its mount:
  - From end of module-list mount (`getElementById("aureon-module-list"))}));`) through the license
    mount (`getElementById("aureon-license-key"))}));`), inclusive.
  - Leaves `const _=()=>{` (import/export) intact.
- Validate with `node --check`. Confirm no remaining `aureon-license-key` / `licenseKey` /
  `betaTester` refs. The dead `siteLibraryUrl` tab branch is left (never executes).

### 4.10 `dist/style-dashboard.css`

- Remove the `.aureon-license-key-area` rule block (dead styles). Surgical, verify the neighboring
  rule boundaries restore correctly.

### 4.11 Docs

- `aureon-doc/STATUS.md`: mark license system ✅ REMOVED; resolve the affected open item; update
  plugin verdict.
- `aureon-doc/PLUGIN.md`: § on dashboard/license updated or removed; dist file list updated.
- `aureon-doc/CHANGELOG.md`: new "Feature removal" entry + resolved open item.
- `aureon/plugin/readme.txt`: changelog entry — "License key system removed — no activation required."
- `aureon-doc/specs/`: this design doc + implementation plan.

## 5. Error handling / edge cases

- `aureon_premium_updater` keeps its name + `admin_init` priority-0 hook so nothing else that
  references it breaks (nothing else does; kept for a stable public seam).
- `Aureon_Premium_Plugin_Updater` class no longer exists — the only reference removed with it.
- Login/E2E admin: `manage_options` cap unchanged; no permission changes.
- The #299 crash disappears because the null-container mount is removed; the other three roots
  (modules, import/export, reset) are untouched.

## 6. Verification plan

1. `php -l` on every touched PHP file.
2. `node --check` on the edited `dist/dashboard.js`; grep the bundle for `aureon-license-key`,
   `licenseKey`, `betaTester` → 0 hits.
3. Grep plugin + theme for `aureon_studio_license_key`, `Aureon_Premium_Plugin_Updater`,
   `class-plugin-updater`, `aureon_premium_beta_tester` → only `readme.txt` changelog history (kept)
   and `aureon-doc` (historical) remain.
4. Deploy changed files to `phantom-wp` container (`docker exec -i` + base64 pipe).
5. Browser E2E (admin/admin123):
   - Dashboard (`themes.php?page=aureon-options`): **0 console errors**; no License Key heading;
     Modules (10 items, no Site Library), Start Customizing, Import / Export, Reset all interactive.
   - Toggle a module activate/deactivate via the React UI.
   - Network tab: no request to `aureonstudio.com`.
   - Customizer page, Elements editor, homepage: 0 console errors.
6. Run regression suite if reproducible (543 tests / 12,140 assertions per project memory);
   otherwise clean smoke tests above suffice.
7. Update project memory (`project/site-library-removal` → also record license removal, or new
   `project/license-removal`).

## 7. Future extension points

- **Licensing:** implement `Aureon_Pro_License_Provider`, return it via the
  `aureon_studio_license_provider` filter (e.g., a `CommercialLicenseProvider` backed by the user's
  own server).
- **Updates:** implement `Aureon_Pro_Update_Provider`, return it via the
  `aureon_studio_update_provider` filter.
- Adding a future provider does not require restructuring any unrelated plugin code.