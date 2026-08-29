<?php
/**
 * Ferm Living secondary products section — reuses WC products adapter with offset.
 *
 * @package Aureon
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

aether_register_section( 'secondary_products', array(
	'template' => 'sections/section-bestsellers.php',
	'adapter'  => 'adapter-wc-products.php',
	'adapter_args' => array(
		'posts_per_page' => 4,
		'paged'          => 2,
		'with_cta'       => true,
	),
	'behavior' => array( 'reveal-group' => true ),
) );
