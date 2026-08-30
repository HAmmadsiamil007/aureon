# Site Library Removal (2026-08-05)

## Decision
Aureon Studio's **Site Library** feature (starter-site importer that fetched agency-built templates — GP Premium's flagship "sitelibrary" like GeneratePress Sites) is **permanently removed**. User builds client templates in-house instead. **Do not reintroduce.**

## Background / why
- Feature was the GP Premium `site-library/` module: React starter-site importer (Appearance → Site Library) fetching demo sites from agency CDN `https://sites.aureonstudio.com`, importing content/WXR/widgets/media. The module dir was already deleted during the rebrand; its API endpoint was a dead placeholder `https://example.com/invalid` (never functional).
- The block-elements editor also fetched template **thumbnails** from `https://sites.aureonstudio.com/files/element-library`.

## What was removed (this change)
1. `aureon/theme/inc/dashboard.php` — removed `'Site Library'` entry from the premium-modules list (was linking aureonstudio.com/site-library).
2. `aureon/plugin/dist/site-library.asset.php` — deleted (orphan; site-library.js/.css/-rtl.css were already gone; webpack has no site-library entry).
3. `aureon/plugin/elements/class-block-elements.php:158` — removed the `templateImageUrl` localize key (`https://sites.aureonstudio.com/files/element-library`). Note: the bundled local block templates in `dist/block-elements.js` (post-nav/footer/post-meta layouts) remain — they are local, no remote fetch. Only remote thumbnail previews now render empty (no crash; string concat yields "<empty>/name.jpg").
4. Docs: `aureon-doc/STATUS.md` (Site Library now ✅ REMOVED, open-item #1 resolved), `aureon-doc/PLUGIN.md` (§3.14 module removed, dist list, known-issue #2 resolved, load-order line 6), `aureon-doc/CHANGELOG.md` (new "Feature removal" section + resolved open item).

## Known remaining (intentionally left)
- `plugin/dist/dashboard.js` (minified) still contains a dead "Site Library" tab-creator referencing `aureonProDashboard.siteLibraryUrl` (undefined — PHP localize in `inc/class-dashboard.php::enqueue_scripts()` never sets it, and `get_modules()` does not include Site Library, so the branch never fires). Cannot rebuild bundle (no `src/` shipped). Harmless dead code.
- `Report/*` files are historical point-in-time audits — NOT edited.

## Verification
- `php -l` on all touched PHP files: 0 errors.
- Grep `site-library|templateImageUrl|Site Library` across `aureon/`: only the dead minified `dist/dashboard.js` ref remains (expected).
- Test suite: 543 tests / 12,140 assertions green (see memory: Customizer E2E).