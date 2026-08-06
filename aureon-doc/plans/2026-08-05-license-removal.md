# License Key System Removal — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Remove the EDD license key system and EDD plugin updater from Aureon Studio, fix the React #299 dashboard crash, and replace them with interface-based null provider seams.

**Architecture:** Delete all licensing/updater code (REST `/license/` + `/beta/` endpoints, EDD updater class + init, legacy activation handler, license localize data, license React component + mount, license CSS). Add `Aureon_Pro_License_Provider` + `Aureon_Pro_Update_Provider` interfaces with null implementations, wired via `aureon_studio_*_provider` filters from `aureon-studio.php`.

**Tech Stack:** PHP 8.2 (local lint), WordPress plugin (no test framework — verification is `php -l`, `node --check`, grep assertions, live Docker E2E via Playwright), minified webpack bundle surgical edit.

**Spec:** `aureon-doc/specs/2026-08-05-license-removal-design.md`

## Global Constraints

- Work only inside `aureon/plugin/` — the theme has zero license code, do not touch it.
- Do not reintroduce license code, `aureon_studio_license_key*` options reads, or `edd_*` API calls (permanent removal).
- Do not touch `aureon/plugin/license.txt` (GPL attribution) or the Site Library changelog history lines in `readme.txt`.
- PHP: no scalar type hints in signatures (matches existing plugin style); docblocks per WordPress style.
- Minified `dist/dashboard.js` / `dist/style-dashboard.css`: preserve minified formatting exactly; every edit must pass `node --check` / CSS brace balance.
- Commits: conventional style (`fix(plugin): …`, `refactor(plugin): …`, `docs(…): …`), stage ONLY the task's files (repo has large unrelated uncommitted work).
- Live verification target: Docker container `phantom-wp` (`http://localhost:8080`, WP admin `admin/admin123`). Container plugin path: `/var/www/html/wp-content/plugins/aureon-studio/`.
- Deploy helper (PowerShell, use in Task 12):
  ```powershell
  function Deploy-File([string]$localPath, [string]$containerPath) {
    $b64 = [Convert]::ToBase64String([System.IO.File]::ReadAllBytes($localPath))
    $psi = New-Object System.Diagnostics.ProcessStartInfo
    $psi.FileName = 'docker'
    $psi.Arguments = 'exec -i phantom-wp sh -c ''base64 -d > "' + $containerPath + '"'''
    $psi.UseShellExecute = $false
    $psi.RedirectStandardInput = $true
    $psi.RedirectStandardOutput = $true
    $p = [System.Diagnostics.Process]::Start($psi)
    $p.StandardInput.Write($b64); $p.StandardInput.Close(); $p.WaitForExit()
    return $p.ExitCode
  }
  ```

---

### Task 1: Create licensing provider seam

**Files:**
- Create: `aureon/plugin/inc/licensing/class-license-provider.php`

**Interfaces:**
- Consumes: nothing.
- Produces: `Aureon_Pro_License_Provider` interface (`is_active(): bool`, `get_status(): string` — no PHP type hints), `Aureon_Pro_Null_License_Provider` implementing it, global function `aureon_premium_get_license_provider()` returning `apply_filters( 'aureon_studio_license_provider', new Aureon_Pro_Null_License_Provider() )`.

- [ ] **Step 1: Create the file** with this exact content:

```php
<?php
/**
 * License provider interface.
 *
 * @package Aureon Studio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Contract for license providers.
 *
 * The null provider ships by default: the product is always unlocked and
 * requires no activation. Implement this interface in a future commercial
 * provider and return it via the `aureon_studio_license_provider` filter.
 */
interface Aureon_Pro_License_Provider {
	/**
	 * Whether the product is licensed / unlocked.
	 *
	 * @return bool
	 */
	public function is_active();

	/**
	 * The current license status string.
	 *
	 * @return string
	 */
	public function get_status();
}

/**
 * Default license provider: everything unlocked, no phone-home.
 */
class Aureon_Pro_Null_License_Provider implements Aureon_Pro_License_Provider {
	/**
	 * {@inheritdoc}
	 */
	public function is_active() {
		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_status() {
		return 'valid';
	}
}

/**
 * Get the active license provider.
 *
 * Swap in a commercial provider:
 * add_filter( 'aureon_studio_license_provider', fn() => new My_Provider() );
 *
 * @return Aureon_Pro_License_Provider
 */
function aureon_premium_get_license_provider() {
	return apply_filters( 'aureon_studio_license_provider', new Aureon_Pro_Null_License_Provider() );
}
```

- [ ] **Step 2: Lint + verify**

Run: `php -l aureon/plugin/inc/licensing/class-license-provider.php`
Expected: `No syntax errors detected`

- [ ] **Step 3: Commit**

```bash
git add aureon/plugin/inc/licensing/class-license-provider.php
git commit -m "feat(plugin): add license provider seam with null implementation"
```

---

### Task 2: Create update provider seam

**Files:**
- Create: `aureon/plugin/inc/update/class-update-provider.php`

**Interfaces:**
- Consumes: nothing.
- Produces: `Aureon_Pro_Update_Provider` interface (`init()`), `Aureon_Pro_Null_Update_Provider` implementing it (no-op), global function `aureon_premium_get_update_provider()` returning `apply_filters( 'aureon_studio_update_provider', new Aureon_Pro_Null_Update_Provider() )`.

- [ ] **Step 1: Create the file** with this exact content:

```php
<?php
/**
 * Update provider interface.
 *
 * @package Aureon Studio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Contract for update providers.
 *
 * The null provider ships by default: updates rely on standard WordPress
 * behavior. Implement this interface for a future commercial update server
 * and return it via the `aureon_studio_update_provider` filter.
 */
interface Aureon_Pro_Update_Provider {
	/**
	 * Hook up update checks.
	 *
	 * @return void
	 */
	public function init();
}

/**
 * Default update provider: no custom update checks.
 */
class Aureon_Pro_Null_Update_Provider implements Aureon_Pro_Update_Provider {
	/**
	 * {@inheritdoc}
	 */
	public function init() {
		// Updates rely on the standard WordPress plugin update process.
	}
}

/**
 * Get the active update provider.
 *
 * Swap in a commercial provider:
 * add_filter( 'aureon_studio_update_provider', fn() => new My_Provider() );
 *
 * @return Aureon_Pro_Update_Provider
 */
function aureon_premium_get_update_provider() {
	return apply_filters( 'aureon_studio_update_provider', new Aureon_Pro_Null_Update_Provider() );
}
```

- [ ] **Step 2: Lint + verify**

Run: `php -l aureon/plugin/inc/update/class-update-provider.php`
Expected: `No syntax errors detected`

- [ ] **Step 3: Commit**

```bash
git add aureon/plugin/inc/update/class-update-provider.php
git commit -m "feat(plugin): add update provider seam with null implementation"
```

---

### Task 3: Replace EDD updater with provider seam in `aureon-studio.php`

**Files:**
- Modify: `aureon/plugin/aureon-studio.php` (lines 147-193 + requires near line 138)

**Interfaces:**
- Consumes: `aureon_premium_get_update_provider()` from Task 2.
- Produces: nothing new.

- [ ] **Step 1: Add provider requires** after line 138 (`require_once AUREON_STUDIO_DIR_PATH . 'inc/class-dashboard.php';`):

```php
require_once AUREON_STUDIO_DIR_PATH . 'inc/class-dashboard.php';

// Licensing and update provider seams.
require_once AUREON_STUDIO_DIR_PATH . 'inc/licensing/class-license-provider.php';
require_once AUREON_STUDIO_DIR_PATH . 'inc/update/class-update-provider.php';
```

- [ ] **Step 2: Replace the updater block** — remove lines 147-193 exactly (the `aureon_premium_updater()` function with the `new Aureon_Premium_Plugin_Updater( 'https://aureonstudio.com', ... )` call AND the entire `aureon_premium_set_updater_api_params()` function + its `edd_sl_plugin_updater_api_params` filter), replacing with:

```php
if ( ! function_exists( 'aureon_premium_updater' ) ) {
	add_action( 'admin_init', 'aureon_premium_updater', 0 );
	/**
	 * Set up update checks.
	 *
	 * The default update provider relies on standard WordPress update
	 * behavior; swap it via the `aureon_studio_update_provider` filter.
	 */
	function aureon_premium_updater() {
		aureon_premium_get_update_provider()->init();
	}
}
```

- [ ] **Step 3: Lint + verify**

Run: `php -l aureon/plugin/aureon-studio.php`
Expected: `No syntax errors detected`
Then: grep `aureon/plugin/aureon-studio.php` for `Aureon_Premium_Plugin_Updater` and `aureonstudio.com` — Expected: 0 hits.

- [ ] **Step 4: Commit**

```bash
git add aureon/plugin/aureon-studio.php
git commit -m "refactor(plugin): replace EDD updater with update provider seam"
```

---

### Task 4: Delete the EDD plugin updater class

**Files:**
- Delete: `aureon/plugin/library/class-plugin-updater.php`

- [ ] **Step 1: Verify no remaining references** (grep `Aureon_Premium_Plugin_Updater` and `class-plugin-updater` across `aureon/plugin` excluding the file itself):
  Expected: 0 hits in PHP (Task 3 removed the only reference).

- [ ] **Step 2: Delete the file**

Run: `Remove-Item -LiteralPath "C:\Users\hamma\Downloads\wordpress\aureon\plugin\library\class-plugin-updater.php"`

- [ ] **Step 3: Commit**

```bash
git add -u aureon/plugin/library/class-plugin-updater.php
git commit -m "chore(plugin): remove EDD plugin updater class"
```

---

### Task 5: Remove `/license/` and `/beta/` REST routes + handlers

**Files:**
- Modify: `aureon/plugin/inc/class-rest.php` (routes 74-92, methods 171-306)

**Interfaces:**
- Consumes: nothing new.
- Produces: nothing new.

- [ ] **Step 1: Remove the two route registrations** — delete exactly:

```php
		register_rest_route(
			$namespace,
			'/license/',
			array(
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => array( $this, 'update_licensing' ),
				'permission_callback' => array( $this, 'update_settings_permission' ),
			)
		);

		register_rest_route(
			$namespace,
			'/beta/',
			array(
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => array( $this, 'update_beta_testing' ),
				'permission_callback' => array( $this, 'update_settings_permission' ),
			)
		);

```

- [ ] **Step 2: Remove `update_licensing()`** — delete from the docblock line `/**\n\t * Update licensing.` (line 171) through the closing `}` of the method (line 279), inclusive.

- [ ] **Step 3: Remove `update_beta_testing()`** — delete from the docblock line `/**\n\t * Update licensing.` (line 281) through the closing `}` of the method (line 306), inclusive.

- [ ] **Step 4: Lint + verify**

Run: `php -l aureon/plugin/inc/class-rest.php`
Expected: `No syntax errors detected`
Then: grep the file for `update_licensing`, `update_beta_testing`, `'/license/'`, `'/beta/'`, `edd_action` — Expected: 0 hits.

- [ ] **Step 5: Commit**

```bash
git add aureon/plugin/inc/class-rest.php
git commit -m "refactor(plugin): remove license and beta REST endpoints"
```

---

### Task 6: Remove license bits from `inc/class-dashboard.php`

**Files:**
- Modify: `aureon/plugin/inc/class-dashboard.php` (lines 61, 289-304, 354-356, 363-389)

**Interfaces:**
- Consumes: nothing new.
- Produces: localize data without `licenseKey` / `licenseKeyStatus` / `betaTester`.

- [ ] **Step 1: Remove the beta filter hook** (line 61):

```php
		add_filter( 'aureon_premium_beta_tester', array( $this, 'set_beta_tester' ) );
```

- [ ] **Step 2: Remove `get_license_key()`** (lines 289-304 — the entire method including its docblock).

- [ ] **Step 3: Remove the three localize keys** (lines 354-356):

```php
						'licenseKey' => self::get_license_key(),
						'licenseKeyStatus' => get_option( 'aureon_studio_license_key_status', 'deactivated' ),
						'betaTester' => get_option( 'aureon_studio_beta_testing', false ),
```

The localize array after the edit must be:

```php
					array(
						'modules' => self::get_modules(),
						'exportableModules' => self::get_exportable_modules(),
						'fontLibraryUrl' => admin_url( 'themes.php?page=aureon-font-library' ),
						'elementsUrl' => admin_url( 'edit.php?post_type=aureon_elements' ),
						'hasWooCommerce' => class_exists( 'WooCommerce' ),
					)
```

- [ ] **Step 4: Remove `set_beta_tester()`** (lines 363-375 — entire method + docblock).

- [ ] **Step 5: Remove `license_key()`** (lines 377-389 — the docblock `Add the container for our start customizing app.` + the empty method body).

- [ ] **Step 6: Lint + verify**

Run: `php -l aureon/plugin/inc/class-dashboard.php`
Expected: `No syntax errors detected`
Then: grep the file for `license` (case-insensitive), `beta` — Expected: 0 hits (excluding the `@package` header line).

- [ ] **Step 7: Commit**

```bash
git add aureon/plugin/inc/class-dashboard.php
git commit -m "refactor(plugin): remove license and beta data from dashboard"
```

---

### Task 7: Remove license bits from `inc/legacy/activation.php`

**Files:**
- Modify: `aureon/plugin/inc/legacy/activation.php`

**Interfaces:**
- Consumes: nothing.
- Produces: legacy dashboard without license handling. **Keep** `aureon_premium_dashboard_scripts`, `aureon_premium_notices`, `aureon_super_package_addons`, `aureon_multi_activate`, `aureon_activate_super_package_addons`, `aureon_deactivate_super_package_addons`, `aureon_premium_body_class`.

- [ ] **Step 1: Remove `aureon_license_errors()`** (lines 50-82 — from `if ( ! function_exists( 'aureon_license_errors' ) ) {` through its closing `}` + the trailing blank line).

- [ ] **Step 2: Remove the dead `$key` read** (line 125):

```php
		$key = get_option( 'aureon_studio_license_key_status', 'deactivated' );
```

(Note: `$key` at line 159 is a different, used variable — leave it.)

- [ ] **Step 3: Remove `aureon_activation_area()`** (lines 360-366 — the whole `if ( ! function_exists( ... ) )` block, which is already an empty stub).

- [ ] **Step 4: Remove `aureon_premium_process_license_key()`** (lines 368-527 — from the `add_action( 'admin_init', 'aureon_premium_process_license_key', 5 );` line through the function's closing `}`).

- [ ] **Step 5: Remove `aureon_license_missing()`** (lines 529-541 — the whole `if ( ! function_exists( ... ) )` block including the `in_plugin_update_message` add_action).

- [ ] **Step 6: Remove the beta filter + function** (lines 543-555 — from `add_filter( 'aureon_premium_beta_tester', 'aureon_premium_beta_tester' );` to end of file).

- [ ] **Step 7: Lint + verify**

Run: `php -l aureon/plugin/inc/legacy/activation.php`
Expected: `No syntax errors detected`
Then: grep the file for `license`, `beta`, `edd`, `wp_remote_post` (case-insensitive) — Expected: 0 hits.

- [ ] **Step 8: Commit**

```bash
git add aureon/plugin/inc/legacy/activation.php
git commit -m "refactor(plugin): remove legacy license key handling"
```

---

### Task 8: Remove deprecated license wrappers from `inc/deprecated.php`

**Files:**
- Modify: `aureon/plugin/inc/deprecated.php` (lines 578-600)

- [ ] **Step 1: Remove exactly** (including the blank line between the 3rd and 4th blocks):

```php
if ( ! function_exists( 'aureon_add_license_key_field' ) ) {
	function aureon_add_license_key_field() {
		// Replaced by aureon_premium_license_key_field()
	}
}

if ( ! function_exists( 'aureon_premium_license_key' ) ) {
	function aureon_premium_license_key() {
		// Replaced by aureon_premium_license_key_field()
	}
}

if ( ! function_exists( 'aureon_save_premium_license_key' ) ) {
	function aureon_save_premium_license_key() {
		// Replaced by aureon_premium_process_license_key()
	}
}


if ( ! function_exists( 'aureon_process_license_key' ) ) {
	function aureon_process_license_key() {
		// Replaced by aureon_premium_process_license_key()
	}
}

```

- [ ] **Step 2: Lint + verify**

Run: `php -l aureon/plugin/inc/deprecated.php`
Expected: `No syntax errors detected`
Then: grep the file for `license_key` — Expected: 0 hits.

- [ ] **Step 3: Commit**

```bash
git add aureon/plugin/inc/deprecated.php
git commit -m "refactor(plugin): remove deprecated license wrappers"
```

---

### Task 9: Remove license React component + mount from `dist/dashboard.js`

**Files:**
- Modify: `aureon/plugin/dist/dashboard.js` (minified, single line, 23670 bytes)

**Interfaces:**
- Consumes: nothing.
- Produces: bundle without the license root — fixes the React #299 crash (`createRoot(null)`).

- [ ] **Step 1: Verify boundaries** (PowerShell, in `aureon/plugin/dist/`):

```powershell
$j = Get-Content dashboard.js -Raw
"len: $($j.Length)"
"aureon-license-key count: $(([regex]::Matches($j, 'aureon-license-key')).Count)"   # expect 2
"module mount: $($j.IndexOf('getElementById("aureon-module-list")'))"                 # expect >= 0
"license def: $($j.IndexOf('const g=()=>{'))"                                          # expect >= 0
"import-export def: $($j.IndexOf('const _=()=>{'))"                                    # expect >= 0
"reset def: $($j.IndexOf('const h=()=>{'))"                                            # expect >= 0
```

- [ ] **Step 2: Apply the surgical removal:**

```powershell
$keepEnd = 'getElementById("aureon-module-list"))}));'
$start = $j.IndexOf($keepEnd) + $keepEnd.Length
$end = $j.IndexOf('const _=()=>{', $start)
$new = $j.Remove($start, $end - $start)
Set-Content -Path dashboard.js -Value $new -NoNewline -Encoding utf8
```

- [ ] **Step 3: Verify removal** (PowerShell):

```powershell
$n = Get-Content dashboard.js -Raw
"aureon-license-key: $($n.IndexOf('aureon-license-key'))"          # expect -1
"licenseKey: $($n.IndexOf('licenseKey'))"                          # expect -1
"betaTester: $($n.IndexOf('betaTester'))"                          # expect -1
"const g=()=>{: $($n.IndexOf('const g=()=>{'))"                    # expect -1
"module mount kept: $($n.IndexOf('getElementById("aureon-module-list")'))"  # expect >= 0
"import-export kept: $($n.IndexOf('const _=()=>{'))"               # expect >= 0
"reset kept: $($n.IndexOf('const h=()=>{'))"                       # expect >= 0
"siteLibraryUrl kept (dead branch): $($n.IndexOf('siteLibraryUrl'))"  # expect >= 0 (intentional)
"new len: $($n.Length)"                                            # expect 19914
```

- [ ] **Step 4: Syntax check**

Run: `node --check aureon/plugin/dist/dashboard.js`
Expected: no output, exit 0.

- [ ] **Step 5: Commit**

```bash
git add aureon/plugin/dist/dashboard.js
git commit -m "fix(plugin): remove license dashboard root (fixes React #299 crash)"
```

---

### Task 10: Remove license CSS from `dist/style-dashboard.css`

**Files:**
- Modify: `aureon/plugin/dist/style-dashboard.css` (minified, 1618 bytes; license rules at chars 566-1383)

- [ ] **Step 1: Remove the license rule block** (PowerShell, in `aureon/plugin/dist/`):

```powershell
$c = Get-Content style-dashboard.css -Raw
$pattern = '\.aureon-license-key-area[^{}]*\{[^}]*\}'
"before: $(([regex]::Matches($c, $pattern)).Count) rules"   # expect 7
$new = [regex]::Replace($c, $pattern, '')
Set-Content -Path style-dashboard.css -Value $new -NoNewline -Encoding utf8
```

- [ ] **Step 2: Verify removal** (PowerShell):

```powershell
$n = Get-Content style-dashboard.css -Raw
"aureon-license-key: $($n.IndexOf('aureon-license-key'))"     # expect -1
"brace balance: $(([regex]::Matches($n, '\{')).Count - ([regex]::Matches($n, '\}')).Count)"   # expect 0
"aureon-dashboard__section-item-action kept: $($n.IndexOf('aureon-dashboard__section-item-action'))"  # expect >= 0
"new len: $($n.Length)"                                      # expect ~800
```

- [ ] **Step 3: Commit**

```bash
git add aureon/plugin/dist/style-dashboard.css
git commit -m "chore(plugin): remove dead license key styles"
```

---

### Task 11: Update documentation

**Files:**
- Modify: `aureon-doc/STATUS.md`, `aureon-doc/PLUGIN.md`, `aureon-doc/CHANGELOG.md`, `aureon/plugin/readme.txt`

**Interfaces:**
- Consumes: nothing.
- Produces: docs consistent with the removal; STATUS.md open items fully resolved.

- [ ] **Step 1: `aureon-doc/STATUS.md`**
  - §3 table: change the `License` row to: `| License | ✅ | GPL-2.0-or-later + upstream attribution retained; **license key system removed (2026-08-05) — no activation required** |` and add a row: `| License key system | ✅ **REMOVED** | EDD license UI, REST `/license/` + `/beta/` endpoints, EDD plugin updater, legacy activation handler — all removed; replaced with `Aureon_Pro_*_Provider` null seams |`
  - §3 plugin verdict (line 54): append: `License key system also removed (2026-08-05) — no activation required; all modules work out of the box.`
  - §5: recount the working-tree diff and update the file-count sentence (`git diff --stat | tail -1` → update the numbers).
  - §6 open items: item 2 → `**RESOLVED (2026-08-05)** — legacy license activation handler (incl. `https://example.com` endpoint) removed entirely`; item 3 → `**RESOLVED (2026-08-05)** — EDD updater deleted; replaced by `Aureon_Pro_Null_Update_Provider` seam (standard WP updates)`.

- [ ] **Step 2: `aureon-doc/PLUGIN.md`**
  - §4 (line 146): replace the `class-plugin-updater.php` bullet with:
    ```
    - **Updater:** `library/class-plugin-updater.php` **REMOVED (2026-08-05)** — EDD updater deleted. Update seam: `inc/update/class-update-provider.php` (`Aureon_Pro_Update_Provider` + `Aureon_Pro_Null_Update_Provider`), swap via the `aureon_studio_update_provider` filter.
    - **Licensing:** `inc/licensing/class-license-provider.php` — `Aureon_Pro_License_Provider` + `Aureon_Pro_Null_License_Provider` (everything unlocked, no activation), swap via the `aureon_studio_license_provider` filter. License key system fully removed (2026-08-05).
    ```
  - §6 (line 155): change `Legacy license activation still contains a \`https://example.com\` endpoint (dead code in practice).` to `License key activation was removed (2026-08-05).`
  - §9 table row 3: status → `**RESOLVED (2026-08-05)** — legacy license endpoint + handler removed entirely`.
  - §10 filters: remove `aureon_premium_beta_tester`, add `aureon_studio_license_provider`, `aureon_studio_update_provider`.

- [ ] **Step 3: `aureon-doc/CHANGELOG.md`**
  - Under the `v1.0.0` section, after `### Feature removal — Site Library (starter-site importer)`, add:
    ```markdown
    ### Feature removal — License key system
    - **Removed the EDD license key system entirely** (2026-08-05): REST `/license/` + `/beta/` endpoints, `library/class-plugin-updater.php` (EDD SL updater), updater init + API-params filter in `aureon-studio.php`, license localize data (`licenseKey`, `licenseKeyStatus`, `betaTester`), the React license section + `#aureon-license-key` mount (this also **fixes the React #299 console error** — `createRoot(null)` on the removed container), legacy activation handler in `inc/legacy/activation.php`, deprecated wrappers in `inc/deprecated.php`, and the `.aureon-license-key-area` styles.
    - No activation required — all modules work out of the box.
    - Replaced by clean seams for a future commercial system: `Aureon_Pro_License_Provider` / `Aureon_Pro_Update_Provider` interfaces with null implementations, swappable via `aureon_studio_license_provider` / `aureon_studio_update_provider` filters.
    ```
  - Update `### Known open items`: mark the `Legacy activation endpoint` line as resolved and add the EDD updater line as resolved (mirror the STATUS.md §6 wording).

- [ ] **Step 4: `aureon/plugin/readme.txt`** — add at the top of `== Changelog ==`:

```
= 1.0.0 =
* Feature: Remove the license key system - no activation required; all modules work out of the box.
```

- [ ] **Step 5: Commit**

```bash
git add aureon-doc/STATUS.md aureon-doc/PLUGIN.md aureon-doc/CHANGELOG.md aureon/plugin/readme.txt
git commit -m "docs: record license key system removal"
```

---

### Task 12: Deploy + full verification (E2E)

**Files:**
- Deploy: `aureon-studio.php`, `inc/class-rest.php`, `inc/class-dashboard.php`, `inc/legacy/activation.php`, `inc/deprecated.php`, `inc/licensing/class-license-provider.php`, `inc/update/class-update-provider.php`, `dist/dashboard.js`, `dist/style-dashboard.css` → container `phantom-wp` under `/var/www/html/wp-content/plugins/aureon-studio/` (delete `library/class-plugin-updater.php` in the container too).

**Interfaces:**
- Consumes: all tasks 1-11.

- [ ] **Step 1: Local static checks (all in one pass)**

```powershell
cd C:\Users\hamma\Downloads\wordpress
Get-ChildItem aureon\plugin -Recurse -Filter *.php | ForEach-Object { php -l $_.FullName | Select-String -Pattern 'error' }   # expect no output
node --check aureon\plugin\dist\dashboard.js    # expect no output
```

- [ ] **Step 2: Repo-wide grep (final isolation proof)**

```powershell
cd C:\Users\hamma\Downloads\wordpress\aureon
# expect hits ONLY in: readme.txt changelog history, aureon-doc/ (historical), Report/ (historical), langs/ (not checked)
Select-String -Path plugin\inc\*.php,plugin\inc\legacy\*.php,plugin\aureon-studio.php,theme\inc\*.php -Pattern 'aureon_studio_license_key|Aureon_Premium_Plugin_Updater|class-plugin-updater|aureon_premium_beta_tester|edd_action' -ErrorAction SilentlyContinue
```

- [ ] **Step 3: Deploy to container** (use the `Deploy-File` helper from Global Constraints for each file; container paths mirror the repo layout under `/var/www/html/wp-content/plugins/aureon-studio/`), then:

```powershell
docker exec phantom-wp sh -c 'rm -f /var/www/html/wp-content/plugins/aureon-studio/library/class-plugin-updater.php'
docker exec phantom-wp php -l /var/www/html/wp-content/plugins/aureon-studio/inc/class-dashboard.php
```

- [ ] **Step 4: Browser E2E — Dashboard** (Playwright, login `admin/admin123` at `http://localhost:8080/wp-admin/`):
  - Navigate to `http://localhost:8080/wp-admin/themes.php?page=aureon-options` with a **hard reload** (bundle `?ver=3.0.0` unchanged — bypass cache).
  - Console messages: **0 errors** (React #299 gone).
  - Page renders: Modules (10 items — no "Site Library", no "License Key" heading anywhere), Start Customizing, Import / Export, Reset.
  - Click "Deactivate" on Blog module → button flips to "Activate" (REST `/modules/` works). Re-activate it.
  - Network requests: **no** `aureonstudio.com` or `example.com` requests.
  - REST check: `http://localhost:8080/wp-json/aureon-pro/v1/license` → Expected 404; `/beta/` → 404.

- [ ] **Step 5: Browser E2E — other surfaces**:
  - `http://localhost:8080/wp-admin/customize.php` → 0 console errors, React Typography/Colors panels render.
  - `http://localhost:8080/wp-admin/post-new.php?post_type=aureon_elements` → 0 console errors.
  - `http://localhost:8080/` homepage → 0 console errors.

- [ ] **Step 6: Update project memory**

Use Serena memory: write `project/license-removal` capturing: decision (no license system — permanent), files changed, the #299 root cause (createRoot(null) on removed container), provider seams + filter names, what's intentionally kept (readme.txt history, dead `siteLibraryUrl` JS branch), and the verification results.

- [ ] **Step 7: Final commit (if any stragglers) + summary**

```bash
git status
```

## Self-Review Notes

- **Spec coverage:** §4.1 (aureon-studio.php) → Task 3; §4.2 (delete updater) → Task 4; §4.3 (license provider) → Task 1; §4.4 (update provider) → Task 2; §4.5 (class-rest) → Task 5; §4.6 (class-dashboard) → Task 6; §4.7 (legacy activation) → Task 7; §4.8 (deprecated) → Task 8; §4.9 (dashboard.js) → Task 9; §4.10 (css) → Task 10; §4.11 (docs) → Task 11; §6 (verification) → Task 12.
- **Interfaces consistent:** `aureon_premium_get_license_provider()` / `aureon_premium_get_update_provider()` names used identically in Tasks 1-3; `init()` called on the update provider in Task 3 matches Task 2's interface.
- **No placeholders:** every task has exact code or exact anchors; verification has expected outputs.
