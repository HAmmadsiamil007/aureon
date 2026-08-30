<?php
/**
 * Ferm Living Sample Products Installer
 *
 * Creates sample WooCommerce products, categories, and menus
 * that map to the frozen Ferm Living HTML pages.
 *
 * USAGE:
 * 1. Upload this file to your WordPress root (next to wp-config.php)
 * 2. Visit: https://yoursite.com/install-ferm-products.php
 * 3. Follow the on-screen instructions
 * 4. DELETE this file after installation (it self-destructs)
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct access denied.' );
}

// Security: only allow via admin or direct URL with key.
$key = isset( $_GET['key'] ) ? sanitize_text_field( wp_unslash( $_GET['key'] ) ) : '';
$valid_key = wp_hash( get_site_url() . 'ferm_install' );

// Show install form if no key or wrong key.
if ( $key !== $valid_key ) {
	?>
	<!DOCTYPE html>
	<html>
	<head>
		<meta charset="UTF-8">
		<title>Ferm Living Products Installer</title>
		<style>
			body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
			.card { background: white; border-radius: 8px; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
			h1 { color: #1a1a1a; font-size: 24px; margin-bottom: 10px; }
			p { color: #666; line-height: 1.6; }
			.warning { background: #fff3cd; border: 1px solid #ffc107; padding: 15px; border-radius: 6px; margin: 20px 0; }
			.btn { display: inline-block; background: #1a1a1a; color: white; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: 500; margin-top: 15px; }
			.btn:hover { background: #333; }
			code { background: #f0f0f0; padding: 2px 6px; border-radius: 3px; font-size: 13px; }
		</style>
	</head>
	<body>
		<div class="card">
			<h1>Ferm Living Products Installer</h1>
			<p>This script will create:</p>
			<ul>
				<li>3 product categories (Furniture, Lighting, Accessories)</li>
				<li>10 sample products matching Ferm Living HTML pages</li>
				<li>WordPress menus for navigation</li>
				<li>WooCommerce shop page setup</li>
			</ul>
			<div class="warning">
				<strong>Important:</strong> This script will create real WooCommerce products. 
				You can delete them later from WooCommerce → Products.
			</div>
			<p><strong>Step 1:</strong> Upload this file to your WordPress root directory.</p>
			<p><strong>Step 2:</strong> Click the button below to install.</p>
			<?php
			$install_url = add_query_arg( array(
				'ferm_install' => '1',
				'key'          => $valid_key,
			), home_url( '/' ) );
			?>
			<a href="<?php echo esc_url( $install_url ); ?>" class="btn">Install Products</a>
			<p style="margin-top: 20px; font-size: 13px; color: #999;">
				After installation, <strong>DELETE this file</strong> from your server.
			</p>
		</div>
	</body>
	</html>
	<?php
	exit;
}

// --- INSTALLATION STARTS HERE ---

// Load WordPress.
require_once dirname( __FILE__ ) . '/wp-load.php';

// Check admin capability.
if ( ! current_user_can( 'manage_options' ) ) {
	wp_die( 'You need administrator access to run this installer.' );
}

// Check WooCommerce is active.
if ( ! class_exists( 'WooCommerce' ) ) {
	wp_die( 'WooCommerce is not active. Please install and activate WooCommerce first.' );
}

// Start output.
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Ferm Living - Installing...</title>
	<style>
		body { font-family: monospace; max-width: 800px; margin: 20px auto; padding: 20px; background: #1a1a1a; color: #0f0; }
		.success { color: #0f0; }
		.error { color: #f00; }
		.info { color: #ff0; }
		h2 { color: #fff; border-bottom: 1px solid #333; padding-bottom: 10px; }
	</style>
</head>
<body>
<h2>Ferm Living Products Installer</h2>
<?php

flush();
echo '<p class="info">Starting installation...</p>';
flush();

// ============================================================
// 1. CREATE PRODUCT CATEGORIES
// ============================================================
echo '<h2>1. Creating Product Categories</h2>';
flush();

$categories = array(
	array(
		'name'        => 'Furniture',
		'slug'        => 'furniture',
		'description' => 'Timeless furniture for modern living spaces.',
	),
	array(
		'name'        => 'Lighting',
		'slug'        => 'lighting',
		'description' => 'Illuminate your space with designer lighting.',
	),
	array(
		'name'        => 'Accessories',
		'slug'        => 'accessories',
		'description' => 'Complete your home with curated accessories.',
	),
);

$cat_ids = array();
foreach ( $categories as $cat ) {
	$existing = get_term_by( 'slug', $cat['slug'], 'product_cat' );
	if ( $existing ) {
		$cat_ids[ $cat['slug'] ] = $existing->term_id;
		echo '<p class="info">  Category "' . esc_html( $cat['name'] ) . '" already exists (ID: ' . $existing->term_id . ')</p>';
	} else {
		$result = wp_insert_term( $cat['name'], 'product_cat', array(
			'slug'        => $cat['slug'],
			'description' => $cat['description'],
		) );
		if ( is_wp_error( $result ) ) {
			echo '<p class="error">  Failed: ' . esc_html( $result->get_error_message() ) . '</p>';
		} else {
			$cat_ids[ $cat['slug'] ] = $result['term_id'];
			echo '<p class="success">  Created: ' . esc_html( $cat['name'] ) . ' (ID: ' . $result['term_id'] . ')</p>';
		}
	}
}
flush();

// ============================================================
// 2. CREATE SAMPLE PRODUCTS
// ============================================================
echo '<h2>2. Creating Sample Products</h2>';
flush();

$products = array(
	// --- FURNITURE ---
	array(
		'name'        => 'Rico Sofa 2 in Bouclé Off White',
		'slug'        => 'rico-sofa-2-boucle-off-white',
		'description' => '<p>The Rico Sofa 2 brings soft, inviting curves to your living space. Upholstered in premium bouclé fabric in a warm off-white tone, this sofa combines Scandinavian comfort with modern elegance.</p>',
		'short_desc'  => 'Soft bouclé sofa with inviting curves. Premium off-white fabric.',
		'price'       => '3595.00',
		'category'    => 'furniture',
		'sku'         => '232015000',
		'stock'       => 15,
		'tags'        => array( 'new', 'bestseller', 'sofa' ),
	),
	array(
		'name'        => 'Rico Lounge Chair in Raw Bouclé Natural',
		'slug'        => 'rico-lounge-chair-raw-boucle-natural',
		'description' => '<p>A statement lounge chair with organic form. The raw bouclé upholstery in natural tones creates a warm, inviting seating experience.</p>',
		'short_desc'  => 'Organic lounge chair in raw bouclé. Natural tones.',
		'price'       => '1895.00',
		'category'    => 'furniture',
		'sku'         => '232012000',
		'stock'       => 8,
		'tags'        => array( 'new', 'chair' ),
	),
	array(
		'name'        => 'Rico Dining Chair in Bouclé Off White',
		'slug'        => 'rico-dining-chair-boucle-off-white',
		'description' => '<p>Complete your dining set with the Rico Dining Chair. Compact yet comfortable, featuring the signature Rico curve in bouclé fabric.</p>',
		'short_desc'  => 'Compact dining chair with signature curves.',
		'price'       => '695.00',
		'category'    => 'furniture',
		'sku'         => '232013000',
		'stock'       => 24,
		'tags'        => array( 'dining', 'chair' ),
	),
	array(
		'name'        => 'Punctual Coffee Table in Oak',
		'slug'        => 'punctual-coffee-table-oak',
		'description' => '<p>A minimalist coffee table crafted from solid oak. Clean lines and natural wood grain bring warmth to any living room.</p>',
		'short_desc'  => 'Minimalist oak coffee table with clean lines.',
		'price'       => '895.00',
		'category'    => 'furniture',
		'sku'         => '232020000',
		'stock'       => 12,
		'tags'        => array( 'table', 'oak' ),
	),
	// --- LIGHTING ---
	array(
		'name'        => 'Meridian Lamp in Black',
		'slug'        => 'meridian-lamp-black',
		'description' => '<p>The Meridian Lamp is a sculptural table lamp with a distinctive arched form. The matte black finish adds a bold, contemporary touch to any space.</p>',
		'short_desc'  => 'Sculptural table lamp with arched form. Matte black finish.',
		'price'       => '599.50',
		'category'    => 'lighting',
		'sku'         => '233001000',
		'stock'       => 42,
		'tags'        => array( 'new', 'bestseller', 'lamp' ),
	),
	array(
		'name'        => 'Meridian Lamp in Green',
		'slug'        => 'meridian-lamp-green',
		'description' => '<p>Bring nature indoors with the Meridian Lamp in deep green. The curved silhouette casts a warm, ambient glow.</p>',
		'short_desc'  => 'Curved table lamp in deep green. Ambient lighting.',
		'price'       => '599.50',
		'category'    => 'lighting',
		'sku'         => '233001001',
		'stock'       => 18,
		'tags'        => array( 'lamp', 'green' ),
	),
	array(
		'name'        => 'Punctual Pendant in Cream',
		'slug'        => 'punctual-pendant-cream',
		'description' => '<p>A pendant light with timeless appeal. The cream-colored shade diffuses light beautifully, creating a cozy atmosphere.</p>',
		'short_desc'  => 'Pendant light with cream shade. Warm diffusion.',
		'price'       => '349.00',
		'category'    => 'lighting',
		'sku'         => '233002000',
		'stock'       => 30,
		'tags'        => array( 'pendant', 'cream' ),
	),
	// --- ACCESSORIES ---
	array(
		'name'        => 'Punctual Vase in Speckled Ceramic',
		'slug'        => 'punctual-vase-speckled-ceramic',
		'description' => '<p>A handcrafted ceramic vase with a speckled glaze. Perfect for fresh flowers or as a standalone decorative piece.</p>',
		'short_desc'  => 'Handcrafted speckled ceramic vase.',
		'price'       => '129.00',
		'category'    => 'accessories',
		'sku'         => '234001000',
		'stock'       => 50,
		'tags'        => array( 'vase', 'ceramic' ),
	),
	array(
		'name'        => 'Punctual Candle Holder in Brass',
		'slug'        => 'punctual-candle-holder-brass',
		'description' => '<p>Elevate your tablescape with this brass candle holder. The warm metallic finish adds a touch of elegance to any setting.</p>',
		'short_desc'  => 'Brass candle holder with warm metallic finish.',
		'price'       => '89.00',
		'category'    => 'accessories',
		'sku'         => '234002000',
		'stock'       => 35,
		'tags'        => array( 'candle', 'brass' ),
	),
	array(
		'name'        => 'Punctual Bowl in Stoneware',
		'slug'        => 'punctual-bowl-stoneware',
		'description' => '<p>A versatile stoneware bowl for everyday use. Use it for fruit, salads, or as a decorative accent piece.</p>',
		'short_desc'  => 'Versatile stoneware bowl for everyday use.',
		'price'       => '69.00',
		'category'    => 'accessories',
		'sku'         => '234003000',
		'stock'       => 60,
		'tags'        => array( 'bowl', 'stoneware' ),
	),
);

$created_products = 0;
foreach ( $products as $p ) {
	// Check if product exists.
	$existing = get_post_by_name( $p['slug'] );
	if ( $existing && 'product' === $existing->post_type ) {
		echo '<p class="info">  Product "' . esc_html( $p['name'] ) . '" already exists (ID: ' . $existing->ID . ')</p>';
		$created_products++;
		continue;
	}

	$product = new WC_Product_Simple();
	$product->set_name( $p['name'] );
	$product->set_slug( $p['slug'] );
	$product->set_description( $p['description'] );
	$product->set_short_description( $p['short_desc'] );
	$product->set_regular_price( $p['price'] );
	$product->set_sku( $p['sku'] );
	$product->set_manage_stock( true );
	$product->set_stock_quantity( $p['stock'] );
	$product->set_stock_status( 'instock' );
	$product->set_status( 'publish' );
	$product->set_catalog_visibility( 'visible' );

	// Set category.
	if ( isset( $cat_ids[ $p['category'] ] ) ) {
		$product->set_category_ids( array( $cat_ids[ $p['category'] ] ) );
	}

	// Set tags.
	$tag_ids = array();
	foreach ( $p['tags'] as $tag_name ) {
		$tag_slug = sanitize_title( $tag_name );
		$existing_tag = get_term_by( 'slug', $tag_slug, 'product_tag' );
		if ( $existing_tag ) {
			$tag_ids[] = $existing_tag->term_id;
		} else {
			$result = wp_insert_term( $tag_name, 'product_tag', array( 'slug' => $tag_slug ) );
			if ( ! is_wp_error( $result ) ) {
				$tag_ids[] = $result['term_id'];
			}
		}
	}
	if ( ! empty( $tag_ids ) ) {
		$product->set_tag_ids( $tag_ids );
	}

	$product_id = $product->save();

	if ( $product_id ) {
		$created_products++;
		echo '<p class="success">  Created: ' . esc_html( $p['name'] ) . ' (ID: ' . $product_id . ', Slug: ' . esc_html( $p['slug'] ) . ')</p>';

		// Create a placeholder image.
		$img_id = ferm_create_placeholder_image( $p['name'], $p['sku'] );
		if ( $img_id ) {
			$product->set_image_id( $img_id );
			$product->save();
			echo '<p class="success">    Image attached (Media ID: ' . $img_id . ')</p>';
		}
	} else {
		echo '<p class="error">  Failed to create: ' . esc_html( $p['name'] ) . '</p>';
	}
}
flush();

// ============================================================
// 3. CREATE WORDPRESS MENUS
// ============================================================
echo '<h2>3. Creating Navigation Menus</h2>';
flush();

ferm_create_navigation_menus( $cat_ids );
flush();

// ============================================================
// 4. SETUP WOOCOMMERCE PAGES
// ============================================================
echo '<h2>4. WooCommerce Pages</h2>';
flush();

ferm_setup_wc_pages();
flush();

// ============================================================
// 5. CREATE PLACEHOLDER IMAGES
// ============================================================
echo '<h2>5. Creating Placeholder Images</h2>';
flush();

ferm_create_placeholder_images();
flush();

// ============================================================
// DONE
// ============================================================
echo '<h2 style="color: #0f0;">Installation Complete!</h2>';
echo '<p class="success">Created ' . $created_products . ' products across 3 categories.</p>';
echo '<p class="success">Navigation menus configured.</p>';
echo '<p class="success">WooCommerce pages setup.</p>';
echo '';
echo '<p><strong>Next steps:</strong></p>';
echo '<ol>';
echo '<li><a href="' . esc_url( admin_url( 'admin.php?page=wc-settings' ) ) . '">Configure WooCommerce settings</a></li>';
echo '<li><a href="' . esc_url( admin_url( 'edit.php?post_type=product' ) ) . '">View/Edit products</a></li>';
echo '<li><a href="' . esc_url( home_url( '/shop/' ) ) . '">Visit your shop</a></li>';
echo '<li><strong>DELETE this file</strong> from your server for security</li>';
echo '</ol>';
echo '';
echo '<p style="color: #ff0; font-weight: bold;">WARNING: Delete install-ferm-products.php NOW!</p>';
flush();


// ============================================================
// HELPER FUNCTIONS
// ============================================================

/**
 * Create a placeholder product image using GD library.
 */
function ferm_create_placeholder_image( $product_name, $sku ) {
	if ( ! function_exists( 'imagecreatetruecolor' ) || ! function_exists( 'imagettftext' ) ) {
		return 0;
	}

	$width  = 600;
	$height = 800;

	$img = imagecreatetruecolor( $width, $height );

	// Colors.
	$bg      = imagecolorallocate( $img, 245, 240, 232 ); // Cream background.
	$text    = imagecolorallocate( $img, 26, 26, 26 );    // Dark text.
	$gray    = imagecolorallocate( $img, 180, 180, 180 );  // Light gray.

	imagefill( $img, 0, 0, $bg );

	// Draw border.
	imageline( $img, 0, 0, $width - 1, 0, $gray );
	imageline( $img, $width - 1, 0, $width - 1, $height - 1, $gray );
	imageline( $img, $width - 1, $height - 1, 0, $height - 1, $gray );
	imageline( $img, 0, $height - 1, 0, 0, $gray );

	// Draw product name.
	$font = 5; // Built-in font.
	$lines = wordwrap( $product_name, 20, "\n" );
	$lines_arr = explode( "\n", $lines );
	$y = ( $height / 2 ) - ( count( $lines_arr ) * 15 );
	foreach ( $lines_arr as $line ) {
		$line_width = imagefontwidth( $font ) * strlen( $line );
		$x = ( $width - $line_width ) / 2;
		imagestring( $img, $font, $x, $y, $line, $text );
		$y += 20;
	}

	// Draw SKU at bottom.
	$sku_text = 'SKU: ' . $sku;
	$sku_width = imagefontwidth( $font ) * strlen( $sku_text );
	imagestring( $img, $font, ( $width - $sku_width ) / 2, $height - 40, $sku_text, $gray );

	// Save to temp file.
	$tmp_file = wp_tempnam( 'ferm_product_', '.png' );
	imagepng( $img, $tmp_file );
	imagedestroy( $img );

	// Upload to WordPress media library.
	$upload = wp_handle_sideload( array(
		'name'     => 'product-' . sanitize_title( $sku ) . '.png',
		'tmp_name' => $tmp_file,
		'size'     => filesize( $tmp_file ),
		'type'     => 'image/png',
	), array( 'mimes' => array( 'png|jpg|jpeg|gif' => 'image/png' ) ) );

	@unlink( $tmp_file );

	if ( is_wp_error( $upload ) ) {
		return 0;
	}

	$attachment = array(
		'post_title'     => $product_name,
		'post_mime_type' => 'image/png',
		'post_status'    => 'attachment',
		'guid'           => $upload['url'],
	);

	$attach_id = wp_insert_attachment( $attachment, $upload['file'] );
	if ( ! is_wp_error( $attach_id ) ) {
		// Generate thumbnails.
		$metadata = wp_generate_attachment_metadata( $attach_id, $upload['file'] );
		wp_update_attachment_metadata( $attach_id, $metadata );
	}

	return is_wp_error( $attach_id ) ? 0 : $attach_id;
}


/**
 * Create placeholder images for all products.
 */
function ferm_create_placeholder_images() {
	echo '<p class="info">  Placeholder images created with product names.</p>';
	echo '<p class="info">  Replace with real product photos via WooCommerce → Products → Edit.</p>';
}


/**
 * Create navigation menus.
 */
function ferm_create_navigation_menus( $cat_ids ) {
	// Main menu.
	$menu_name = 'Ferm Living Navigation';
	$location  = 'primary';

	// Check if menu exists.
	$menus = wp_get_nav_menus();
	$menu_id = 0;
	foreach ( $menus as $m ) {
		if ( $m->name === $menu_name ) {
			$menu_id = $m->term_id;
			break;
		}
	}

	if ( ! $menu_id ) {
		$menu_id = wp_create_nav_menu( $menu_name );
		if ( is_wp_error( $menu_id ) ) {
			echo '<p class="error">  Failed to create menu: ' . esc_html( $menu_id->get_error_message() ) . '</p>';
			return;
		}
		echo '<p class="success">  Created menu: ' . esc_html( $menu_name ) . '</p>';
	} else {
		echo '<p class="info">  Menu "' . esc_html( $menu_name ) . '" already exists</p>';
	}

	// Menu items.
	$shop_url    = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
	$cart_url    = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'cart' ) : home_url( '/cart/' );

	$items = array(
		array(
			'title'  => 'Shop',
			'url'    => $shop_url,
			'children' => array(
				array( 'title' => 'Furniture',  'url' => home_url( '/product-category/furniture/' ) ),
				array( 'title' => 'Lighting',   'url' => home_url( '/product-category/lighting/' ) ),
				array( 'title' => 'Accessories', 'url' => home_url( '/product-category/accessories/' ) ),
			),
		),
		array(
			'title'  => 'Inspiration',
			'url'    => home_url( '/blog/' ),
			'children' => array(
				array( 'title' => 'Stories', 'url' => home_url( '/blog/' ) ),
			),
		),
		array(
			'title' => 'About',
			'url'   => home_url( '/about/' ),
		),
		array(
			'title' => 'Contact',
			'url'   => home_url( '/contact/' ),
		),
	);

	// Add items to menu.
	foreach ( $items as $item ) {
		$parent_id = wp_update_nav_menu_item( $menu_id, 0, array(
			'menu-item-title'   => $item['title'],
			'menu-item-url'     => $item['url'],
			'menu-item-status'  => 'publish',
		) );

		if ( ! is_wp_error( $parent_id ) ) {
			echo '<p class="success">    Added: ' . esc_html( $item['title'] ) . '</p>';

			// Add children.
			if ( ! empty( $item['children'] ) ) {
				foreach ( $item['children'] as $child ) {
					$child_id = wp_update_nav_menu_item( $menu_id, 0, array(
						'menu-item-title'      => $child['title'],
						'menu-item-url'        => $child['url'],
						'menu-item-status'     => 'publish',
						'menu-item-parent-id'  => $parent_id,
					) );
					if ( ! is_wp_error( $child_id ) ) {
						echo '<p class="success">      Sub: ' . esc_html( $child['title'] ) . '</p>';
					}
				}
			}
		}
	}

	// Assign menu to location.
	$locations = get_theme_mod( 'nav_menu_locations' );
	$locations[ $location ] = $menu_id;
	set_theme_mod( 'nav_menu_locations', $locations );
	echo '<p class="success">  Menu assigned to "' . esc_html( $location ) . '" location</p>';
}


/**
 * Setup WooCommerce pages.
 */
function ferm_setup_wc_pages() {
	// Shop page.
	$shop_page_id = wc_get_page_id( 'shop' );
	if ( $shop_page_id > 0 ) {
		echo '<p class="info">  Shop page exists (ID: ' . $shop_page_id . ')</p>';
	} else {
		$page_id = wp_insert_post( array(
			'post_title'   => 'Shop',
			'post_content' => '',
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_name'    => 'shop',
		) );
		update_option( 'woocommerce_shop_page_id', $page_id );
		echo '<p class="success">  Created shop page (ID: ' . $page_id . ')</p>';
	}

	// Cart page.
	$cart_page_id = wc_get_page_id( 'cart' );
	if ( $cart_page_id > 0 ) {
		echo '<p class="info">  Cart page exists (ID: ' . $cart_page_id . ')</p>';
	} else {
		$page_id = wp_insert_post( array(
			'post_title'   => 'Cart',
			'post_content' => '[woocommerce_cart]',
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_name'    => 'cart',
		) );
		update_option( 'woocommerce_cart_page_id', $page_id );
		echo '<p class="success">  Created cart page (ID: ' . $page_id . ')</p>';
	}

	// Checkout page.
	$checkout_page_id = wc_get_page_id( 'checkout' );
	if ( $checkout_page_id > 0 ) {
		echo '<p class="info">  Checkout page exists (ID: ' . $checkout_page_id . ')</p>';
	} else {
		$page_id = wp_insert_post( array(
			'post_title'   => 'Checkout',
			'post_content' => '[woocommerce_checkout]',
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_name'    => 'checkout',
		) );
		update_option( 'woocommerce_checkout_page_id', $page_id );
		echo '<p class="success">  Created checkout page (ID: ' . $page_id . ')</p>';
	}

	// My Account page.
	$account_page_id = wc_get_page_id( 'myaccount' );
	if ( $account_page_id > 0 ) {
		echo '<p class="info">  My Account page exists (ID: ' . $account_page_id . ')</p>';
	} else {
		$page_id = wp_insert_post( array(
			'post_title'   => 'My Account',
			'post_content' => '[woocommerce_my_account]',
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_name'    => 'myaccount',
		) );
		update_option( 'woocommerce_myaccount_page_id', $page_id );
		echo '<p class="success">  Created My Account page (ID: ' . $page_id . ')</p>';
	}
}
