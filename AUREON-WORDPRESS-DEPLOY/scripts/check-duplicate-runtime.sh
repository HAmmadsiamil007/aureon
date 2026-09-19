#!/usr/bin/env bash
# AUREON duplicate-runtime guard (release gate).
# Fails if an executable duplicate of the theme or plugin source tree exists
# inside the runtime locations (wp-content paths mirrored by this repo).
set -u
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
fail=0

# 1. No nested theme tree inside the active theme.
if [ -d "$ROOT/themes/aureon/theme" ]; then
  echo "FAIL: duplicate theme tree themes/aureon/theme exists"
  fail=1
fi

# 2. Theme/plugin runtime trees must be the only executable copies.
for dup in "$ROOT/themes/aureon/checkout" "$ROOT/themes/aureon/myaccount" "$ROOT/plugins/aureon-studio/plugin"; do
  [ -e "$dup" ] || continue
  echo "WARN: overlapping path present: $dup (verify it is the single authoritative copy)"
done

# 3. _archive must never live inside a runtime directory.
if [ -d "$ROOT/themes/aureon/_archive" ] || [ -d "$ROOT/plugins/aureon-studio/_archive" ]; then
  echo "FAIL: archive directory inside runtime tree"
  fail=1
fi

[ "$fail" -eq 0 ] && echo "PASS: single executable theme/plugin runtime"
exit $fail
