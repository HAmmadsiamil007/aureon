<?php
/**
 * Demo Reference System — Runtime Verification Script
 *
 * Run: docker exec wordpress-wordpress-1 php /var/www/html/wp-content/themes/aureon/frontend/designs/fermliving/demo/test-runtime.php
 */

error_reporting(0);
ini_set('display_errors', 0);

$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['SERVER_NAME'] = 'localhost';

require_once '/var/www/html/wp-load.php';

echo "========================================\n";
echo "DEMO REFERENCE SYSTEM — RUNTIME TESTS\n";
echo "========================================\n\n";

$passed = 0;
$failed = 0;
$total = 0;

function test($name, $condition, $detail = '') {
    global $passed, $failed, $total;
    $total++;
    if ($condition) {
        $passed++;
        echo "✅ PASS: {$name}\n";
    } else {
        $failed++;
        echo "❌ FAIL: {$name}\n";
        if ($detail) echo "   Detail: {$detail}\n";
    }
}

// TEST 1: Demo Products
echo "\n--- TEST 1: Demo Products ---\n";
$demo_file = get_template_directory() . '/frontend/designs/fermliving/demo/demo-products.json';
test('Demo products JSON exists', file_exists($demo_file));
$demo_products = json_decode(@file_get_contents($demo_file), true);
test('Demo products JSON valid', is_array($demo_products));
test('Demo products count > 0', count($demo_products) > 0);
if (count($demo_products) > 0) {
    $p = $demo_products[0];
    test('Product has source=demo', ($p['source'] ?? '') === 'demo');
    test('Product has purchasable=false', ($p['purchasable'] ?? true) === false);
    test('Product has image URL', !empty($p['image']));
}

// TEST 2: Demo Categories
echo "\n--- TEST 2: Demo Categories ---\n";
$cat_file = get_template_directory() . '/frontend/designs/fermliving/demo/demo-categories.json';
test('Demo categories JSON exists', file_exists($cat_file));
$demo_cats = json_decode(@file_get_contents($cat_file), true);
test('Demo categories JSON valid', is_array($demo_cats));
test('Demo categories count > 0', count($demo_cats) > 0);

// TEST 3: Demo Assets
echo "\n--- TEST 3: Demo Assets ---\n";
$assets_file = get_template_directory() . '/frontend/designs/fermliving/demo/demo-assets.json';
test('Demo assets JSON exists', file_exists($assets_file));
$assets = json_decode(@file_get_contents($assets_file), true);
test('Demo assets JSON valid', is_array($assets));
test('Logo asset exists', isset($assets['assets']['logo']));
test('Hero asset exists', isset($assets['assets']['hero']));
test('Heading asset exists', isset($assets['assets']['heading']));
test('Hero has required field', isset($assets['assets']['hero']['required']));
test('Hero has source_site field', isset($assets['assets']['hero']['source_site']));

// TEST 4: Functions Exist
echo "\n--- TEST 4: Composer Functions ---\n";
test('ferm_demo_products exists', function_exists('ferm_demo_products'));
test('ferm_demo_categories exists', function_exists('ferm_demo_categories'));
test('ferm_get_demo_mode exists', function_exists('ferm_get_demo_mode'));
test('ferm_show_demo_content exists', function_exists('ferm_show_demo_content'));
test('ferm_has_real_products exists', function_exists('ferm_has_real_products'));
test('ferm_has_real_categories exists', function_exists('ferm_has_real_categories'));
test('ferm_load_demo_assets exists', function_exists('ferm_load_demo_assets'));
test('ferm_resolve_demo_asset exists', function_exists('ferm_resolve_demo_asset'));
test('ferm_filter_demo_products exists', function_exists('ferm_filter_demo_products'));
test('ferm_filter_demo_categories exists', function_exists('ferm_filter_demo_categories'));

// TEST 5: Demo Mode
echo "\n--- TEST 5: Demo Mode ---\n";
$mode = get_option('aether_demo_mode', 'auto');
test('Demo mode is set', !empty($mode));
test('Demo mode is valid', in_array($mode, ['auto', 'force_demo', 'disabled']));
echo "   Current mode: {$mode}\n";

// TEST 6: Remote URLs
echo "\n--- TEST 6: Remote URLs ---\n";
foreach ($demo_products as $p) {
    $img = $p['image'] ?? '';
    $is_remote = (strpos($img, 'http://') === 0 || strpos($img, 'https://') === 0);
    $is_local = (strpos($img, 'cdn/') === 0 || strpos($img, '_cdn.') === 0);
    test("Product '{$p['demo_id']}' has valid URL", $is_remote || $is_local, $img);
}

// TEST 7: Fallbacks
echo "\n--- TEST 7: Fallbacks ---\n";
foreach ($assets['assets'] as $type => $asset) {
    test("Asset '{$type}' has fallback", isset($asset['fallback']) || $type === 'heading');
}

// SUMMARY
echo "\n========================================\n";
echo "RESULTS: {$passed}/{$total} passed, {$failed} failed\n";
echo "========================================\n";

if ($failed === 0) {
    echo "🎉 ALL TESTS PASSED\n";
    echo "DEMO_REFERENCE_SYSTEM_RUNTIME_PASS\n";
} else {
    echo "⚠️  {$failed} TEST(S) FAILED\n";
}
