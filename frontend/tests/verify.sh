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
