# QUESTIONS.md — Decisions & Blockers Requiring User Input

> **STATUS UPDATE 2026-09-06:** Q2 resolved (T-01, commit `2d8f4e0`). Q3/Q4/Q7 decided — see `DECISIONS-LOG.md`. Remaining open: **Q1 (runtime access — the sole release blocker)** and Q5/Q6/Q8/Q9 (packaging-time decisions, not code work). Only questions **not answerable from code** are listed.

## P0 BLOCKERS

### Q1. Where does production actually run, and may this audit's plan reference it?
- **Why:** the repo has no wp-config, no Docker, no deploy manifests; two root scripts hardcode `/var/www/html/wp-load.php`. Nothing about runtime (PHP version, active plugins, page cache, gateways, menus, catalog) can be proven from here.
- **Current evidence:** `enable_cod.php`, `update-contact.php`, absence of any deploy/IaC files.
- **Options:** (a) Docker/staging WP instance for testing; (b) SSH/export access to production config snapshots; (c) neither — audit stays code-level.
- **Recommendation:** (a) — cheapest way to convert UNPROVEN → tested.
- **Consequence:** without it, every runtime claim in the matrix stays UNPROVEN and "production-ready" cannot be certified.

### Q2. Is the untracked `vineta/components/shell/header.php` the intended version to ship?
- **Why:** commit 39e8215 ships auth pages using it, but the file isn't in git. Either it must be committed, or the feature must not depend on it.
- **Current evidence:** `git status` untracked; header.php calls `vineta_render_standalone_header()`.
- **Options:** (a) commit as-is; (b) review+commit; (c) remove feature dependency.
- **Recommendation:** (b) quick review then commit (T-01).
- **Consequence:** fresh deploys silently lose the auth header.

## ARCHITECTURE

### Q3. ~~Restore Core default design to `luxury` and select vineta via option/constant?~~ **DECIDED 2026-09-06: keep `vineta` default; no Core change.** See DECISIONS-LOG.md. Multi-client escape hatch documented (AETHER_DESIGN constant / aether_active_design option).
- **Why:** `views/design.php` hardcodes `'vineta'` (Core↔client coupling, unreachable luxury engine). The fix is one line + a runtime setting — but the runtime change needs the live DB/wp-config (ties to Q1).
- **Options:** (a) restore default + set option `aether_active_design=vineta`; (b) keep hardcode and accept coupling.
- **Recommendation:** (a).
- **Consequence:** (b) blocks every future client pack and makes the luxury engine permanently dead.

### Q4. ~~Keep the `luxury` AETHER engine at all?~~ **DECIDED 2026-09-06: archive stale trees, delete nothing; Golden Copy immutable.** Execution deferred to the packaging window so the tested RC stays byte-identical. See DECISIONS-LOG.md.
- **Why:** ~598-file root `frontend/` + engine sections/assets are dead under vineta. Deleting shrinks and de-confuses the repo; keeping preserves a Core regression harness and the multi-client story.
- **Options:** (a) keep + maintain; (b) archive (move out of deploy tree, keep in git history); (c) delete.
- **Recommendation:** (b).
- **Consequence:** (c) removes the only non-client design path; (a) costs maintenance.

## FRONTEND

### Q5. What happens to dead pack templates (checkout.html, view-cart.html, cart-empty.html, shop variants, product-3d, 3 demo popups)?
- **Why:** unreachable at runtime; they confuse future edits and bloat deploys.
- **Options:** (a) delete; (b) archive in pack `/archive`; (c) keep.
- **Recommendation:** (b).
- **Consequence:** keeping them risks a future editor "restoring" a broken checkout.html path.

## WOOCOMMERCE

### Q6. Is the real catalog expected to have variable products at launch?
- **Why:** variation support is implemented in code but untestable without catalog data (client test N/A today).
- **Options:** (a) yes → variation E2E is a launch gate; (b) no → variations stay implemented/unproven, documented.
- **Recommendation:** (a) if uncertain — one demo variable product makes the matrix testable.
- **Consequence:** shipping with variables untested repeats the "implemented ≠ tested" failure mode.

## ACCOUNT / PLUGINS

### Q7. ~~Should the custom account dashboard gain WC endpoint-hook zones for plugin compatibility?~~ **DECIDED 2026-09-06: option (b) refined — do not invent hooks; document the existing endpoint surfaces as the supported insertion points and bind future edits to preserve them.** Account-plugin injection via `woocommerce_account_*` hooks is a documented limitation; adopting such a plugin later = BRIDGE_UPDATE. See DECISIONS-LOG.md.
- **Why:** current template bypasses `woocommerce_account_*` hooks — account plugins (subscriptions, rewards) would not render.
- **Options:** (a) add hook zones (small template change); (b) accept limitation, document "no account plugins".
- **Recommendation:** (a) — cheap insurance.
- **Consequence:** (b) silently breaks any future account plugin.

## SECURITY

### Q8. Which payment/SMTP/SEO plugins are actually installed on production?
- **Why:** repo ships none; suppression lists and gateway scripts imply several exist server-side only.
- **Options:** provide plugin list / admin export (ties to Q1).
- **Recommendation:** export `wp plugin list` + active theme mods snapshot.
- **Consequence:** unknown plugins = unknown CSS/JS/hook conflicts with the frozen frontend.

## DEPLOYMENT / PRODUCTION

### Q9. What is the deployment procedure — manual copy, git pull, or host tooling?
- **Why:** the six-tree drift pattern strongly suggests manual copying; deploy correctness (hash match) cannot be asserted without knowing the pipeline.
- **Options:** (a) manual copy → adopt git-based deploy; (b) existing tooling → document it.
- **Recommendation:** git-based deploy from the canonical tree only.
- **Consequence:** without a deterministic pipeline, the audit's fixes may never reach production intact.

---

## Consolidated chat question (per audit instructions)

> **SUPERSEDED 2026-09-06:** the consolidated ask is unchanged in substance and now the ONLY release blocker: provide a testable WordPress runtime (staging/Docker instance or production config export). All code work and all decidable questions (Q2–Q4, Q7) are closed — see DECISIONS-LOG.md. Verdict remains AUREON_CLIENT_PRODUCTION_READY_BLOCKED until Q1 is answered with a real environment.
