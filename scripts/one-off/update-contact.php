<?php
require_once "/var/www/html/wp-load.php";
$settings = get_option("aureon_settings", array());
$settings["aether_contact_phone"] = "+92 300 1234567";
$settings["aether_contact_address"] = array("123 Innovation Drive", "San Francisco, CA 94102");
$settings["aether_contact_hours"] = "Mon-Fri 9am-6pm PST";
update_option("aureon_settings", $settings);
echo "Updated aureon_settings contact info\n";
echo "Phone: " . $settings["aether_contact_phone"] . "\n";
echo "Address: " . implode(", ", $settings["aether_contact_address"]) . "\n";
echo "Hours: " . $settings["aether_contact_hours"] . "\n";
