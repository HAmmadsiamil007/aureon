#!/usr/bin/env bash
#
# ADR-004 — Lumina self-integrity gate (Phase 16, replaces the former
# parent-package gate).
#
# Lumina is a fully standalone theme. This gate verifies that the shipped
# Lumina theme tree (app/, templates/, assets-src/, bin/, functions.php,
# style.css, theme.json, composer.json, package.json, …) is byte-identical to
# the audited SHA-256 baseline committed at the release freeze
# (bin/lumina-integrity.sha256).
#
# Usage:
#   bash bin/verify-lumina-integrity.sh            # verify (CI default)
#   bash bin/verify-lumina-integrity.sh --update   # regenerate baseline
#
# Environment override:
#   LUMINA_BASELINE = path to the sha256 baseline file
#
# When the baseline is absent the gate SKIPS with exit 0 (pre-freeze state).
# Generated/ignored dirs (vendor, node_modules, assets/dist, tools) are
# excluded — they are reproducible artifacts, not shipped source.

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
THEME_DIR="$(cd "${SCRIPT_DIR}/.." && pwd)"
BASELINE="${LUMINA_BASELINE:-${SCRIPT_DIR}/lumina-integrity.sha256}"

# Path fragments relative to THEME_DIR that are excluded from the hash.
EXCLUDES=(
	"vendor/"
	"node_modules/"
	"assets/dist/"
	"tools/"
	".git/"
	".phpstan/"
	".phpunit.cache/"
	"lumina.env.json"
	"bin/lumina-integrity.sha256"
)

build_manifest() {
	cd "${THEME_DIR}"
	find . -type f \
		-not -path './vendor/*' \
		-not -path './node_modules/*' \
		-not -path './assets/dist/*' \
		-not -path './tools/*' \
		-not -path './.git/*' \
		-not -path './.phpstan/*' \
		-not -path './.phpunit.cache/*' \
		-not -name 'lumina.env.json' \
		-not -name 'lumina-integrity.sha256' \
		-print0 \
		| sort -z \
		| xargs -0 sha256sum \
		| sed -E 's|^([0-9a-f]{64}) [*]?\./|\1 |'
	# Normalize the path prefix portably: GNU sha256sum emits "hash  ./path"
	# (text) on Linux and "hash *./path" (binary marker) on Windows/MSYS;
	# strip the optional '*' and the two spaces so the manifest is
	# byte-identical across platforms (release gate).
}

if [[ "${1:-}" == "--update" ]]; then
	build_manifest > "${BASELINE}"
	echo "[integrity] baseline regenerated: ${BASELINE} ($(wc -l < "${BASELINE}") files)"
	exit 0
fi

if [[ ! -f "${BASELINE}" ]]; then
	echo "[integrity] baseline not found (pre-freeze state?). Gate SKIPPED: ${BASELINE}"
	exit 0
fi

# Normalize the current tree to the same shape and diff.
build_manifest > "${TMPDIR:-/tmp}/lumina-integrity-current.sha256"

if ! diff -q "${BASELINE}" "${TMPDIR:-/tmp}/lumina-integrity-current.sha256" >/dev/null; then
	echo "[integrity] FAILED — a shipped Lumina file differs from the freeze baseline."
	diff "${BASELINE}" "${TMPDIR:-/tmp}/lumina-integrity-current.sha256" | head -40
	exit 1
fi

echo "[integrity] OK — Lumina theme tree matches the frozen release baseline ($(wc -l < "${BASELINE}") files)."
