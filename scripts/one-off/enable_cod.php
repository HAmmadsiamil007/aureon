<?php
require_once "/var/www/html/wp-load.php";
$settings = get_option("woocommerce_cod_settings", array());
if (is_array($settings)) {
    $settings["enabled"] = "yes";
} else {
    $settings = array("enabled"=>"yes","title"=>"Cash on delivery","description"=>"Pay with cash upon delivery.","instructions"=>"","enable_for_methods"=>array(),"enable_for_virtual"=>"yes");
}
update_option("woocommerce_cod_settings", $settings);
echo "Done. Enabled: " . var_export(get_option("woocommerce_cod_settings")["enabled"], true) . "\n";
