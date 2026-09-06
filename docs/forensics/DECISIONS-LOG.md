# DECISIONS-LOG — Q3/Q4/Q7 Resolutions (recorded 2026-09-06)

**Context:** code-level release complete; instruction: close decision items only — no new features, no Core change, Golden Copy immutable, RC reproducible, production-gated tests never marked PASS, then STOP until production/staging access.

---

## Q3 — Default design / Core decoupling → **DECIDED: keep `vineta` as the default**

- **Decision:** `DEFAULT DESIGN = vineta` — the hardcoded fallback in `frontend/views/design.php` **stays as-is**. **No Core change is made.**
- **Rationale (owner's call):** this deployment is a single-client store; the fallback guarantees the client design always resolves, even on a fresh install where the `aether_active_design` option has not been set. The architectural cost (Core↔client coupling, dead luxury branch) is accepted and documented rather than paid down now.
- **Multi-client escape hatch (recorded for the future, not implemented):** the platform already supports per-install override via the `AETHER_DESIGN` constant or the `aether_active_design` option — a future client install simply sets one of those in wp-config/DB; the fallback only matters when neither is present. When a genuinely multi-client platform build is needed, Q3 reopens as a CORE_REVIEW with this log as the starting evidence.
- **Effect on acceptance matrix:** none (no code change; the matrix already records A-03 as a documented accepted coupling, not a FAIL).

## Q4 — Old design trees / luxury engine → **DECIDED: archive, do not delete**

- **Decision:** archive stale trees outside the active runtime with a documented rollback path; delete nothing yet.
- **Rationale:** deletion is irreversible operational risk for zero runtime benefit; git history alone does not give fast rollback for non-git users. Concretely (to be executed when packaging allows — **not now**, to keep the tested RC byte-identical):
  1. Tag the current commit as the rollback point.
  2. Move `aureon/`, `theme/`, root `frontend/` into `archive/` (or an external archive location), with an `archive/README.md` stating: contents are frozen legacy designs (fermliving/lumen/luxury-era), never deployed, kept for rollback/reference.
  3. Golden Copy stays exactly where it is — immutable baseline (already verified: 5+3 diff baseline, untouched).
- **Layer model now explicit:** Golden Copy = immutable baseline · current canonical = active development · deployment package = release artifact.
- **Effect on acceptance matrix:** B-01 moves from "RISK open" to "resolution planned — pending packaging window". The RC manifest is not touched (any tree move would change hashes and invalidate the tested RC).

## Q7 — Account endpoint hook zones → **DECIDED: document existing zones; do not invent hooks**

- **Decision:** no new hook zones are added to the custom account dashboard. Instead, the exact WC endpoint surfaces the template already implements are documented as the supported insertion points, and the Future Design Edit Contract binds future frontend edits to preserve them.
- **Documented supported surfaces in `themes/aureon/myaccount/my-account.php`:**
  - Endpoint routing via `WC()->query->get_current_endpoint()` — orders / downloads / edit-address / edit-account / customer-logout all resolve through the dashboard template.
  - Account data via WC functions: `wc_get_account_endpoint_url()`, `wc_get_customer_order_count()`, user meta address counts, `get_avatar_url()`.
  - Navigation built from WC's account menu contract (`wc_get_account_endpoint_url('')` per endpoint).
- **Plugin-compatibility note (honest limitation, already in the matrix as B-07):** account plugins that inject via `woocommerce_account_*_endpoint` content hooks will not render inside the custom dashboard. If such a plugin is adopted later, that is a BRIDGE_UPDATE task against this documented surface — not a Core change.
- **Effect on acceptance matrix:** B-07 closed as "decision recorded — documented limitation, revisit only if an account plugin is adopted".

## Standing rules reaffirmed

- Golden Copy: **immutable** (verified again this session).
- Tested RC (`1289995`, 1,972-file SHA-256 manifest): **reproducible and untouched** by this decisions pass.
- Production-gated tests (24 BLOCKED): **stay BLOCKED** until a real WordPress/WooCommerce runtime exists.
- Verdict remains **AUREON_CLIENT_PRODUCTION_READY_BLOCKED**. **STOP** — awaiting production/staging access.
