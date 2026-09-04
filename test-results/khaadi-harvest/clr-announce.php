<?php
/* QA cleanup: remove stale Sole Origine announcement items from the bucket. */
if (!defined('ABSPATH')) { define('ABSPATH', dirname(__FILE__) . '/'); }
require_once ABSPATH . 'wp-load.php';

$s = get_option('aureon_settings', array());
if (!is_array($s)) { $s = array(); }
$before = isset($s['aether_announcement_items']) ? count($s['aether_announcement_items']) : 0;
$s['aether_announcement_items'] = array();
update_option('aureon_settings', $s);
echo "announcement items cleared: $before\n";
$s2 = get_option('aureon_settings', array());
echo 'hero slides: ' . count($s2['aether_hero_slides'] ?? array()) . "\n";
echo 'accent: ' . ($s2['aether_color_accent'] ?? '(empty)') . "\n";
echo "DONE\n";
