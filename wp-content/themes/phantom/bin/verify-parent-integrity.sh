#!/usr/bin/env bash
#
# ADR-004 / ADR-012 — Parent-package integrity gate.
#
# Verifies that the GeneratePress + GP Premium packages on disk are
# byte-identical to the audited SHA-256 baseline (Report/gp_audit_manifest_new.txt).
# Fails the build if ANY shipped parent file differs — enforcing the
# "GeneratePress / GP Premium / WordPress Core remain untouched" rule.
#
# The manifest lives OUTSIDE the theme repo (../..%2F.. from this theme dir).
# Override paths with env vars when the packages live elsewhere:
#   PHANTOM_GP_THEME  = path to the generatepress theme dir
#   PHANTOM_GP_PLUGIN = path to the gp-premium plugin dir
#   PHANTOM_MANIFEST  = path to the sha256 manifest (hash|bytes|mtime|path)
#
# When the baseline or packages are absent (e.g. isolated child-theme clone),
# the gate SKIPS with exit 0 — it is only authoritative inside the monorepo.

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
THEME_DIR="$(cd "${SCRIPT_DIR}/.." && pwd)"
REPO_ROOT="$(cd "${THEME_DIR}/../../.." && pwd)"

GP_THEME="${PHANTOM_GP_THEME:-${REPO_ROOT}/generatepress.3.6.1/generatepress}"
GP_PLUGIN="${PHANTOM_GP_PLUGIN:-${REPO_ROOT}/gp-premium_v2.5.6/gp-premium}"
MANIFEST="${PHANTOM_MANIFEST:-${REPO_ROOT}/Report/gp_audit_manifest_new.txt}"

if [[ ! -f "${MANIFEST}" || ! -d "${GP_THEME}" || ! -d "${GP_PLUGIN}" ]]; then
	echo "[integrity] baseline/packages not found (isolated clone?). Gate SKIPPED: ${MANIFEST}"
	exit 0
fi

fail=0
check_dir() {
	local prefix="$1" dir="$2"
	echo "[integrity] verifying prefix '${prefix}' against ${dir}"
	while IFS='|' read -r hash _ _ path; do
		[[ -n "${hash}" ]] || continue
		# The baseline was generated on Windows: normalize backslashes to forward
		# slashes so both `plugin/woocommerce\\fields\\x.php` and
		# `plugin/woocommerce/fields/x.php` entries resolve identically.
		local norm="${path//\\/\/}"
		case "${norm}" in
		"${prefix}/"*)
			local rel="${norm#${prefix}/}"
			local file="${dir}/${rel}"
			if [[ ! -f "${file}" ]]; then
				echo "[integrity] MISSING ${path}"
				fail=1
				continue
			fi
			local actual
			actual="$(sha256sum "${file}" | awk '{print $1}')"
			if [[ "${actual}" != "${hash}" ]]; then
				echo "[integrity] MISMATCH ${path}"
				fail=1
			fi		;;
	esac
	# Strip the UTF-8 BOM from the first line portably (GNU + BSD sed).
	done < <(sed "1s/^$(printf '\xEF\xBB\xBF')//" "${MANIFEST}")
}

check_dir "theme" "${GP_THEME}"
check_dir "plugin" "${GP_PLUGIN}"

if [[ "${fail}" -ne 0 ]]; then
	echo "[integrity] FAILED — a GeneratePress/GP Premium file was modified. Revert it; parent packages must stay untouched."
	exit 1
fi

echo "[integrity] OK — parent packages match the audited baseline."
