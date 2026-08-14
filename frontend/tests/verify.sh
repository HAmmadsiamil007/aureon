#!/bin/bash
# AETHER Frontend Verification Gate
# Run from the project root: bash frontend/tests/verify.sh

set -e

ERRORS=0

echo "=== AETHER Frontend Verification ==="

# 1. PHP syntax check on all PHP files in frontend/
echo ""
echo "--- PHP Syntax Check ---"
while IFS= read -r f; do
    if ! php -l -q "$f" >/dev/null 2>&1; then
        echo "FAIL: $f"
        ERRORS=$((ERRORS + 1))
    fi
done < <(find frontend/ -name "*.php" -not -path "*/source/*")

# 2. Node syntax check on JS files (if node available)
if command -v node &> /dev/null; then
    echo ""
    echo "--- JS Syntax Check ---"
    while IFS= read -r f; do
        if ! node --check "$f" >/dev/null 2>&1; then
            echo "FAIL: $f"
            ERRORS=$((ERRORS + 1))
        fi
    done < <(find frontend/ -name "*.js" -not -path "*/source/*" -not -path "*/vendor/*")
fi

# 3. Grep gate — components must NOT CALL WP/WC functions
#    (call-syntax only: docblock mentions and data-attr values are allowed)
echo ""
echo "--- Grep Gate (components must not call WP/WC) ---"
HITS=$(grep -rnE 'wc_[a-z_]+\(|get_option\(|get_post\(|WP_Query\(|get_bloginfo\(|get_theme_mod\(|get_permalink\(|home_url\(|admin_url\(|wp_get_attachment\(' frontend/components/ 2>/dev/null || true)
if [ -n "$HITS" ]; then
    echo "FAIL: WP/WC functions found in components:"
    echo "$HITS"
    ERRORS=$((ERRORS + 1))
else
    echo "PASS: No WP/WC functions in components/"
fi

# 3b. Hex gate — components must NOT hardcode hex colors
#     (design identity comes from tokens/CSS custom properties only)
echo ""
echo "--- Hex Gate (components must not hardcode colors) ---"
HEX=$(grep -rnE '#[0-9a-fA-F]{3,8}\b' frontend/components/ 2>/dev/null | grep -vE '&#[0-9a-fA-F]+;' || true)
if [ -n "$HEX" ]; then
    echo "FAIL: Hardcoded hex colors found in components:"
    echo "$HEX"
    ERRORS=$((ERRORS + 1))
else
    echo "PASS: No hardcoded hex colors in components/"
fi

# 4. Adapters MUST call WP functions (spot check)
echo ""
echo "--- Adapter Verification ---"
ADAPTERS=$(find frontend/adapters/ -name "*.php" 2>/dev/null)
if [ -z "$ADAPTERS" ]; then
    echo "WARN: No adapter files found"
else
    echo "PASS: $(echo "$ADAPTERS" | wc -l) adapter files present"
fi

# 5. Tokens file exists
echo ""
echo "--- Tokens Check ---"
if [ -f "frontend/tokens/tokens.php" ]; then
    echo "PASS: tokens.php exists"
else
    echo "FAIL: tokens.php missing"
    ERRORS=$((ERRORS + 1))
fi

# 6. Manifest file exists
echo ""
echo "--- Manifest Check ---"
if [ -f "frontend/manifest/components.php" ]; then
    echo "PASS: components.php manifest exists"
else
    echo "FAIL: components.php manifest missing"
    ERRORS=$((ERRORS + 1))
fi

# 7. Renderer file exists
echo ""
echo "--- Renderer Check ---"
if [ -f "frontend/views/renderer.php" ]; then
    echo "PASS: renderer.php exists"
else
    echo "FAIL: renderer.php missing"
    ERRORS=$((ERRORS + 1))
fi

# 7b. Design pack resolver exists (M3 — pack-first template resolution)
echo ""
echo "--- Design Pack Resolver Check ---"
if [ -f "frontend/views/design.php" ]; then
    echo "PASS: design.php exists"
else
    echo "FAIL: design.php missing"
    ERRORS=$((ERRORS + 1))
fi

# 7c. Design pack contract validation (M8 — manifests, assets, mappings)
if command -v node &> /dev/null; then
    echo ""
    echo "--- Manifest Contract Validation (M8) ---"
    if node frontend/tests/validate-manifest.cjs; then
        echo "PASS: design pack manifests valid"
    else
        ERRORS=$((ERRORS + 1))
    fi
fi

# Summary
echo ""
echo "=== Verification Complete ==="
if [ $ERRORS -gt 0 ]; then
    echo "RESULT: FAILED ($ERRORS errors)"
    exit 1
else
    echo "RESULT: PASSED"
    exit 0
fi
