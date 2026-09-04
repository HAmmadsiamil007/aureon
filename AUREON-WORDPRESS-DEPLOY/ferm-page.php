<?php
/**
 * Generic Complete-Page Template
 *
 * Serves a complete standalone HTML page from the active design pack.
 * Opens the HTML document, calls wp_head() for WordPress essentials
 * (admin bar, WooCommerce scripts, enqueued pack CSS/JS), extracts and
 * outputs the <body> content from the design pack's HTML file, then
 * closes with wp_footer().
 *
 * The AETHER shell (header.php → aether_compose_header / footer.php →
 * aether_compose_footer) is NOT used for complete-page designs.
 *
 * Controlled by the "complete_page": true flag in the design pack's
 * manifest.json. This template is generic and works for any design pack
 * that sets that flag.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Safety: only load when a complete-page design is active.
if ( ! function_exists( 'aether_is_complete_page_design' ) || ! aether_is_complete_page_design() ) {
	return;
}

$pack_dir = aether_active_design_dir();
if ( ! $pack_dir ) {
	return;
}

// Map the current WordPress route to an HTML file.
$file = aureon_ferm_resolve_page();

if ( ! $file || ! file_exists( $pack_dir . $file ) ) {
	// Fallback: serve homepage.
	$file = 'index.html';
	if ( ! file_exists( $pack_dir . $file ) ) {
		status_header( 404 );
		nocache_headers();
		echo '<!DOCTYPE html><html><head><title>Not Found</title></head><body><h1>Page not found</h1></body></html>';
		exit;
	}
}

$html = file_get_contents( $pack_dir . $file );
if ( false === $html ) {
	status_header( 500 );
	nocache_headers();
	echo '<!DOCTYPE html><html><head><title>Server Error</title></head><body><h1>Could not load page</h1></body></html>';
	exit;
}

// --- Extract body attributes from source document ---
$body_attrs = aureon_ferm_extract_body_attrs( $html );

// --- Open document ---
echo "<!DOCTYPE html>\n";
echo '<html lang="' . esc_attr( get_locale() ) . '"' . aureon_ferm_render_attrs( $body_attrs['html'] ) . ">\n";

// --- Head: WordPress essentials (admin bar, enqueued pack CSS/JS, WC scripts) ---
echo "<head>\n";
echo "<meta charset='" . get_bloginfo( 'charset' ) . "'>\n";
// Favicon: use WordPress site_icon if set, else default to pack favicon.
$site_icon = get_option( 'site_icon' );
if ( $site_icon ) {
	$icon_url = wp_get_attachment_image_url( $site_icon, '32' );
	if ( $icon_url ) {
		echo '<link rel="icon" href="' . esc_url( $icon_url ) . '" sizes="32x32">' . "\n";
		echo '<link rel="apple-touch-icon" href="' . esc_url( wp_get_attachment_image_url( $site_icon, '180' ) ) . '">' . "\n";
	}
} else {
	$default_favicon = $pack_url . 'favicon.svg';
	echo '<link rel="icon" href="' . esc_url( $default_favicon ) . '" type="image/svg+xml">' . "\n";
}
wp_head();

// --- Dynamic CSS from Customizer ---
$aether_css = array();
$color_map  = array(
	'aether_color_bg'          => '--bg',
	'aether_color_surface'     => '--surface',
	'aether_color_text'        => '--text',
	'aether_color_muted'       => '--muted',
	'aether_color_accent'      => '--accent',
	'aether_color_accent_hover' => '--accent-hover',
	'aether_color_border'      => '--border',
);
foreach ( $color_map as $opt => $var ) {
	$val = get_option( $opt, '' );
	if ( $val ) {
		$aether_css[] = $var . ':' . $val;
	}
}
$font_heading = get_option( 'aether_font_heading', '' );
$font_body    = get_option( 'aether_font_body', '' );
if ( $font_heading ) {
	$aether_css[] = '--font-heading:' . $font_heading;
}
if ( $font_body ) {
	$aether_css[] = '--font-body:' . $font_body;
}
if ( ! empty( $aether_css ) ) {
	echo '<style>:root{' . implode( ';', $aether_css ) . '}</style>' . "\n";
}
echo "</head>\n";

// --- Body: extract and output from the source HTML ---
$body_content = aureon_ferm_extract_body( $html );
if ( false !== $body_content ) {
	// Server-side path rewrite: convert relative CDN paths to absolute before output.
	$pack_url = function_exists( 'aether_pack_url' ) ? aether_pack_url() : '';
	if ( $pack_url ) {
		$body_content = aureon_ferm_rewrite_paths( $body_content, $pack_url );
	}
	echo '<body' . aureon_ferm_render_attrs( $body_attrs['body'] ) . ">\n";
	echo $body_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- client presentation HTML, already escaped at source.
} else {
	// Fallback: output entire HTML (already a complete document).
	echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

// --- Footer: WooCommerce cart fragments, analytics, admin bar ---
// Fix relative paths in frozen HTML: rewrite cdn/... and nav links to absolute URLs.
$pack_url = function_exists( 'aether_pack_url' ) ? aether_pack_url() : '';
$site_url = home_url();
if ( $pack_url ) {
	echo "<script>\n";
	echo "(function(){\n";
	echo "var p='" . esc_js( $pack_url ) . "';\n";
	echo "var s='" . esc_js( $site_url ) . "';\n";
	// Rewrite <img src="cdn/..."> and <img src="../cdn/...">
	echo "document.querySelectorAll('img[src]').forEach(function(i){\n";
	echo "  var src=i.getAttribute('src');\n";
	echo "  if(src&&(src.indexOf('cdn/')===0||src.indexOf('../cdn/')===0)){\n";
	echo "    i.src=p+src.replace(/^\\.\\.\\//,'');\n";
	echo "  }\n";
	echo "});\n";
	// Rewrite srcset attributes
	echo "document.querySelectorAll('img[srcset]').forEach(function(i){\n";
	echo "  var srcset=i.getAttribute('srcset');\n";
	echo "  if(srcset){\n";
	echo "    i.srcset=srcset.split(',').map(function(p2){\n";
	echo "      var parts=p2.trim().split(/\\s+/);\n";
	echo "      var url=parts[0];\n";
	echo "      if(url.indexOf('cdn/')===0||url.indexOf('../cdn/')===0){\n";
	echo "        parts[0]=p+url.replace(/^\\.\\.\\//,'');\n";
	echo "      }\n";
	echo "      return parts.join(' ');\n";
	echo "    }).join(', ');\n";
	echo "  }\n";
	echo "});\n";
	// Rewrite nav/content links: all HTML files -> clean WordPress URLs
	echo "var _vm={'index.html':'/','404.html':'/','home-fashion-02.html':'/','before-you-leave.html':'/','newsletter-popup-02.html':'/','newsletter-popup-03.html':'/','shop-default.html':'/shop/','shop-left-sidebar.html':'/shop/','shop-right-sidebar.html':'/shop/','shop-filter-drawer.html':'/shop/','shop-filter-sidebar.html':'/shop/','shop-filter-hidden.html':'/shop/','shop-fullwidth.html':'/shop/','shop-grid-3-columns.html':'/shop/','shop-horizontal-filter.html':'/shop/','shop-infinity-scroll.html':'/shop/','shop-load-more-button.html':'/shop/','shop-sub-collection.html':'/shop/','shop-sub-collection-02.html':'/shop/','shop-collection-list.html':'/shop/','blog-grid-01.html':'/blog/','blog-grid-02.html':'/blog/','blog-list-01.html':'/blog/','blog-list-02.html':'/blog/','blog-single.html':'/blog/','product-detail.html':'/shop/','product-3d.html':'/shop/','product-countdown-timer.html':'/shop/','product-description-accordions.html':'/shop/','product-description-tab.html':'/shop/','product-group.html':'/shop/','product-out-of-stock.html':'/shop/','product-pickup-available.html':'/shop/','product-swatch-dropdown.html':'/shop/','product-together.html':'/shop/','product-video.html':'/shop/','product-volume-discount.html':'/shop/','product-affiliate.html':'/shop/','product-bottom-thumbnail.html':'/shop/','product-buyX-getY.html':'/shop/','product-description-side-accordions.html':'/shop/','product-description-vertical.html':'/shop/','product-drawer-sidebar.html':'/shop/','product-external-zoom.html':'/shop/','product-grid.html':'/shop/','product-grid-02.html':'/shop/','product-inner-circle-zoom.html':'/shop/','product-inner-zoom.html':'/shop/','product-no-zoom.html':'/shop/','product-open-lightbox.html':'/shop/','product-right-thumbnail.html':'/shop/','product-stacked.html':'/shop/','product-style-01.html':'/shop/','product-style-02.html':'/shop/','product-style-03.html':'/shop/','product-swatch-dropdown-color.html':'/shop/','product-swatch-image.html':'/shop/','product-swatch-image-square.html':'/shop/','product-volume-discount-thumbnail.html':'/shop/','about-us.html':'/about-us/','contact-us.html':'/contact-us/','faq.html':'/faq/','cookies.html':'/cookies/','privacy-policy.html':'/privacy-policy/','term-and-condition.html':'/term-and-condition/','return-and-refund.html':'/return-and-refund/','shipping.html':'/shipping/','store-location.html':'/store-location/','coming-soon.html':'/coming-soon/','view-cart.html':'/cart/','cart-empty.html':'/cart/','cart-drawer-v2.html':'/cart/','checkout.html':'/checkout/','thank-you.html':'/checkout/','account-page.html':'/my-account/','account-addresses.html':'/my-account/','account-details.html':'/my-account/','account-orders.html':'/my-account/','wish-list.html':'/my-account/','compare.html':'/shop/'};\n";
	echo "document.querySelectorAll('a[href]').forEach(function(a){\n";
	echo "  var h=a.getAttribute('href');\n";
	echo "  if(!h||h.charAt(0)==='#'||h.indexOf('http')===0||h.indexOf('mailto:')===0||h.indexOf('tel:')===0||h.indexOf('javascript:')===0) return;\n";
	echo "  if(h.indexOf('/wp-')===0||h.indexOf('/cart')===0||h.indexOf('/checkout')===0||h.indexOf('/my-account')===0||h.indexOf('/product/')===0||h.indexOf('/shop')===0||h.indexOf('/blog')===0||h.indexOf('/about-us')===0||h.indexOf('/contact-us')===0||h.indexOf('/faq')===0||h.indexOf('/privacy-policy')===0||h.indexOf('/shipping')===0||h.indexOf('/term-and-condition')===0||h.indexOf('/return-and-refund')===0||h.indexOf('/store-location')===0||h.indexOf('/cookies')===0||h.indexOf('/coming-soon')===0) return;\n";
	echo "  var r=h.replace(/^\\.\\.\\//,'').replace(/^\\.\\//,'');\n";
	echo "  if(_vm[r]){a.href=s+_vm[r];return;}\n";
	echo "  var b=r.replace(/\\.html$/,'');\n";
	echo "  if(b.indexOf('collections/')===0) a.href=s+'/product-category/'+b.replace('collections/','');\n";
	echo "  else if(b.indexOf('products/')===0) a.href=s+'/product/'+b.replace('products/','');\n";
	echo "  else if(b.indexOf('account/')===0) a.href=s+'/my-account/';\n";
	echo "  else if(b.indexOf('blogs/')===0) a.href=s+'/blog/';\n";
	echo "  else if(b.indexOf('pages/')===0) a.href=s+'/'+b.replace('pages/','');\n";
	echo "  else if(b.indexOf('blog')===0) a.href=s+'/blog/';\n";
	echo "  else if(b.indexOf('product')===0) a.href=s+'/shop/';\n";
	echo "  else if(b.indexOf('shop')===0) a.href=s+'/shop/';\n";
	echo "  else if(b.indexOf('account')===0||b.indexOf('order')===0||b.indexOf('wishlist')===0) a.href=s+'/my-account/';\n";
	echo "  else if(b.indexOf('compare')===0) a.href=s+'/shop/';\n";
	echo "  else if(b.indexOf('home')===0||b.indexOf('newsletter')===0) a.href=s+'/';\n";
	echo "  else a.href=s+'/'+b;\n";
	echo "});\n";
	// Rewrite external _cdn.assets.struct.com URLs to live struct.com CDN
	echo "document.querySelectorAll('[src],[href],[poster]').forEach(function(el){\n";
	echo "  ['src','href','poster'].forEach(function(attr){\n";
	echo "    var v=el.getAttribute(attr);\n";
	echo "    if(v&&v.indexOf('../_cdn.assets.struct.com/')!==-1){\n";
	echo "      el.setAttribute(attr,'https://cdn.assets.struct.com/'+v.replace('..\\/',''));\n";
	echo "    }\n";
	echo "  });\n";
	echo "});\n";
	echo "})()\n";
	echo "</script>\n";

	// MutationObserver: catch any dynamically created images with relative cdn/ paths
	echo "<script>\n";
	echo "(function(){\n";
	echo "var p='" . esc_js( $pack_url ) . "';\n";
	echo "var obs=new MutationObserver(function(muts){\n";
	echo "  muts.forEach(function(m){\n";
	echo "    m.addedNodes.forEach(function(n){\n";
	echo "      if(!n.querySelectorAll) return;\n";
	echo "      n.querySelectorAll('img[src]').forEach(function(i){\n";
	echo "        var src=i.getAttribute('src');\n";
	echo "        if(src&&(src.indexOf('cdn/')===0||src.indexOf('../cdn/')===0)){\n";
	echo "          i.src=p+src.replace(/^\\.\\.\\//,'');\n";
	echo "        }\n";
	echo "      });\n";
	echo "      n.querySelectorAll('img[srcset]').forEach(function(i){\n";
	echo "        var s=i.getAttribute('srcset');\n";
	echo "        if(s&&(s.indexOf('cdn/')!==-1||s.indexOf('../cdn/')!==-1)){\n";
	echo "          i.srcset=s.split(',').map(function(p2){\n";
	echo "            var parts=p2.trim().split(/\\s+/);\n";
	echo "            var url=parts[0];\n";
	echo "            if(url.indexOf('cdn/')===0||url.indexOf('../cdn/')===0){\n";
	echo "              parts[0]=p+url.replace(/^\\.\\.\\//,'');\n";
	echo "            }\n";
	echo "            return parts.join(' ');\n";
	echo "          }).join(', ');\n";
	echo "        }\n";
	echo "      });\n";
	// Rewrite _cdn.assets.struct.com in dynamically added elements
	echo "      n.querySelectorAll('[src],[href],[poster]').forEach(function(el){\n";
	echo "        ['src','href','poster'].forEach(function(attr){\n";
	echo "          var v=el.getAttribute(attr);\n";
	echo "          if(v&&v.indexOf('../_cdn.assets.struct.com/')!==-1){\n";
	echo "            el.setAttribute(attr,'https://cdn.assets.struct.com/'+v.replace('..\\/',''));\n";
	echo "          }\n";
	echo "        });\n";
	echo "      });\n";
	echo "    });\n";
	echo "  });\n";
	echo "});\n";
	echo "obs.observe(document.documentElement,{childList:true,subtree:true});\n";
echo "})()\n";
echo "</script>\n";
}

// Account page: enable the Ferm login submit button and bridge form to WP
if ( function_exists( 'is_account_page' ) && is_account_page() ) {
	echo "<script>\n";
	echo "(function(){\n";
	// Enable submit button immediately and on any input
	echo "var f=document.getElementById('customer_login');\n";
	echo "if(f){\n";
	echo "  var b=f.querySelector('input[type=submit],button[type=submit]');\n";
	echo "  if(b){b.disabled=false;}\n";
	echo "  f.querySelectorAll('input').forEach(function(i){\n";
	echo "    i.addEventListener('input',function(){if(b)b.disabled=false;});\n";
	echo "  });\n";
	// Fix lost-password link
	echo "  var lp=f.querySelector('a[href*=\"#recover\"]');\n";
	echo "    if(lp){lp.href='" . esc_js( wp_lostpassword_url() ) . "';}\n";
	echo "}\n";
	echo "})()\n";
	echo "</script>\n";
}

// --- Logo bridge: replace frozen SVG with WordPress custom_logo when set ---
$custom_logo_id = get_theme_mod( 'custom_logo', '' );
if ( $custom_logo_id ) {
	$logo_url = wp_get_attachment_image_url( $custom_logo_id, 'full' );
	if ( $logo_url ) {
		echo "<script>\n";
		echo "(function(){\n";
		echo "var url='" . esc_js( $logo_url ) . "';\n";
		echo "var logos=document.querySelectorAll('.header__logo,[data-header-logo]');\n";
		echo "logos.forEach(function(el){\n";
		echo "  var a=el.querySelector('a.logo,a[class*=logo]');\n";
		echo "  if(!a)return;\n";
		echo "  // Replace SVG with img. Keep original SVG hidden for reset.\n";
		echo "  var svg=a.querySelector('svg');\n";
		echo "  if(svg){svg.style.display='none';}\n";
		echo "  // Remove any existing custom logo img first.\n";
		echo "  var old=a.querySelector('img.aureon-custom-logo');\n";
		echo "  if(old)old.remove();\n";
		echo "  var img=document.createElement('img');\n";
		echo "  img.src=url;\n";
		echo "  img.alt='" . esc_js( get_bloginfo( 'name' ) ) . "';\n";
		echo "  img.className='aureon-custom-logo';\n";
		echo "  img.style.height='100%';\n";
		echo "  img.style.width='auto';\n";
		echo "  img.style.objectFit='contain';\n";
		echo "  a.insertBefore(img,a.firstChild);\n";
		echo "});\n";
		echo "})()\n";
		echo "</script>\n";
	}
}

wp_footer();

echo "\n</body>\n</html>\n";
exit;


/**
 * Map WordPress route to a complete-page HTML file path (relative to pack dir).
 *
 * Uses the manifest.json "pages" mapping when available, falls back to
 * the hardcoded Ferm route map for backward compatibility.
 *
 * @return string|false File path or false if no match.
 */
function aureon_ferm_resolve_page() {
	// Try manifest pages mapping first.
	$manifest = aether_design_manifest();
	if ( ! empty( $manifest['pages'] ) ) {
		$pages = $manifest['pages'];

		// Homepage.
		if ( is_front_page() || ( is_home() && ! is_paged() ) ) {
			if ( ! empty( $pages['home'] ) ) {
				return $pages['home'];
			}
		}

		// Single product.
		if ( function_exists( 'is_product' ) && is_product() ) {
			$slug = get_query_var( 'product' );
			if ( $slug && ! empty( $pages['products'][ $slug ] ) ) {
				return $pages['products'][ $slug ];
			}
			// Fallback: first available product page from manifest.
			if ( ! empty( $pages['products'] ) && is_array( $pages['products'] ) ) {
				return reset( $pages['products'] );
			}
		}

		// Product archive / shop page.
		if ( is_post_type_archive( 'product' ) || is_page( 'shop' ) ) {
			if ( ! empty( $pages['collections'] ) && is_array( $pages['collections'] ) ) {
				return reset( $pages['collections'] );
			}
		}

		// Product category.
		if ( is_tax( 'product_cat' ) ) {
			$slug = get_query_var( 'product_cat' );
			if ( $slug && ! empty( $pages['collections'][ $slug ] ) ) {
				return $pages['collections'][ $slug ];
			}
			if ( ! empty( $pages['collections'] ) && is_array( $pages['collections'] ) ) {
				return reset( $pages['collections'] );
			}
		}

		// Static pages.
		if ( is_page() ) {
			$slug = get_query_var( 'pagename' );
			if ( $slug && ! empty( $pages['pages'][ $slug ] ) ) {
				return $pages['pages'][ $slug ];
			}
			// Fallback: check the 'static' key in manifest.
			if ( $slug && ! empty( $pages['static'][ $slug ] ) ) {
				return $pages['static'][ $slug ];
			}
		}

		// Blog / posts archive.
		if ( is_home() || is_post_type_archive( 'post' ) || is_page( 'blog' ) || is_page( 'stories' ) ) {
			if ( ! empty( $pages['blog'] ) ) {
				return $pages['blog'];
			}
		}

		// Single post (blog article).
		if ( is_single() && ! empty( $pages['blog_single'] ) ) {
			return $pages['blog_single'];
		}

		// Search results.
		if ( is_search() && ! empty( $pages['search'] ) ) {
			return $pages['search'];
		}

		// Cart.
		if ( function_exists( 'is_cart' ) && is_cart() ) {
			if ( ! empty( $pages['cart'] ) ) {
				return $pages['cart'];
			}
		}

		// Checkout.
		if ( function_exists( 'is_checkout' ) && is_checkout() ) {
			if ( ! empty( $pages['checkout'] ) ) {
				return $pages['checkout'];
			}
		}

		// Account.
		if ( function_exists( 'is_account_page' ) && is_account_page() ) {
			if ( ! empty( $pages['account'] ) ) {
				return $pages['account'];
			}
		}
	}

	// --- Fallback: generic route map (backward compatibility) ---
	// Homepage.
	if ( is_front_page() || ( is_home() && ! is_paged() ) ) {
		return 'index.html';
	}

	// Single product.
	if ( function_exists( 'is_product' ) && is_product() ) {
		$slug = get_query_var( 'product' );
		if ( $slug ) {
			$file = 'products/' . $slug . '.html';
			if ( file_exists( aether_active_design_dir() . $file ) ) {
				return $file;
			}
		}
		// Fallback: first available product page.
		$products_dir = aether_active_design_dir() . 'products/';
		if ( is_dir( $products_dir ) ) {
			$files = glob( $products_dir . '*.html' );
			if ( ! empty( $files ) ) {
				return 'products/' . basename( $files[0] );
			}
		}
		return false;
	}

	// Product archive / shop page.
	if ( is_post_type_archive( 'product' ) || is_page( 'shop' ) ) {
		return 'collections/furniture.html';
	}

	// Product category.
	if ( is_tax( 'product_cat' ) ) {
		$slug = get_query_var( 'product_cat' );
		if ( $slug ) {
			$file = 'collections/' . $slug . '.html';
			if ( file_exists( aether_active_design_dir() . $file ) ) {
				return $file;
			}
		}
		return 'collections/furniture.html';
	}

	// Static pages.
	if ( is_page() ) {
		$slug = get_query_var( 'pagename' );
		$page_map = array(
			'contact'       => 'pages/contact.html',
			'about'         => 'pages/about.html',
			'store-locator' => 'pages/store-locator.html',
			'store locator' => 'pages/store-locator.html',
		);
		if ( isset( $page_map[ $slug ] ) ) {
			return $page_map[ $slug ];
		}
	}

	// Blog / posts archive.
	if ( is_home() || is_post_type_archive( 'post' ) || is_page( 'blog' ) || is_page( 'stories' ) ) {
		return 'blogs/stories.html';
	}

	// Search results — use blog page as fallback.
	if ( is_search() ) {
		return 'blogs/stories.html';
	}

	// 404 — serve the pack's designed 404 page when present.
	if ( is_404() ) {
		$not_found = '404.html';
		if ( file_exists( aether_active_design_dir() . $not_found ) ) {
			return $not_found;
		}
		return 'pages/contact.html'; // Legacy fallback.
	}

	return false;
}


/**
 * Extract <body> content from a complete HTML document.
 *
 * Finds everything between <body...> and </body>, returning the inner
 * content (without the body tags themselves).
 *
 * @param string $html Complete HTML document.
 * @return string|false Body inner content, or false if not found.
 */
function aureon_ferm_extract_body( $html ) {
	if ( preg_match( '/<body[^>]*>(.*)<\/body>/si', $html, $matches ) ) {
		return $matches[1];
	}
	return false;
}


/**
 * Extract attributes from <html> and <body> tags in the source document.
 *
 * Returns structured arrays of key=value pairs that can be re-rendered.
 * Only preserves safe, presentation-relevant attributes.
 *
 * @param string $html Complete HTML document.
 * @return array{html: array, body: array} Attributes keyed by attribute name.
 */
function aureon_ferm_extract_body_attrs( $html ) {
	$result = array(
		'html' => array(),
		'body' => array(),
	);

	// Extract <html> attributes.
	if ( preg_match( '/<html([^>]*)>/si', $html, $matches ) ) {
		$attr_string = $matches[1];
		// Preserve data-* attributes and lang.
		if ( preg_match_all( '/(data-[\w-]+)\s*=\s*["\']([^"\']*)["\']/i', $attr_string, $attr_matches, PREG_SET_ORDER ) ) {
			foreach ( $attr_matches as $attr ) {
				$result['html'][ $attr[1] ] = $attr[2];
			}
		}
	}

	// Extract <body> attributes.
	if ( preg_match( '/<body([^>]*)>/si', $html, $matches ) ) {
		$attr_string = $matches[1];
		// Preserve data-* attributes and other safe attributes.
		$safe_attrs = array( 'id', 'data-template', 'data-money-format', 'data-country', 'data-shop', 'class' );
		if ( preg_match_all( '/([\w-]+)\s*=\s*["\']([^"\']*)["\']/i', $attr_string, $attr_matches, PREG_SET_ORDER ) ) {
			foreach ( $attr_matches as $attr ) {
				$name = $attr[1];
				// Only preserve explicitly safe attributes.
				if ( in_array( $name, $safe_attrs, true ) || 0 === strpos( $name, 'data-' ) ) {
					$result['body'][ $name ] = $attr[2];
				}
			}
		}
	}

	return $result;
}


/**
 * Render an attribute array as HTML attribute string.
 *
 * @param array $attrs Key-value pairs.
 * @return string HTML attribute string (leading space included if non-empty).
 */
function aureon_ferm_render_attrs( $attrs ) {
	if ( empty( $attrs ) ) {
		return '';
	}

	$output = '';
	foreach ( $attrs as $name => $value ) {
		$output .= ' ' . esc_attr( $name ) . '="' . esc_attr( $value ) . '"';
	}

	return $output;
}


/**
 * Server-side path rewriter for frozen HTML content.
 *
 * Converts relative CDN image paths and Shopify-style nav links to
 * absolute WordPress URLs before the browser parses them.
 *
 * @param string $content HTML body content.
 * @param string $pack_url Absolute URL to the design pack root.
 * @return string Rewritten content.
 */
function aureon_ferm_rewrite_paths( $content, $pack_url ) {
	$site_url = home_url();
	// Live CDN base — resolve from active pack or skip CDN rewriting.
	$live_cdn = '';
	if ( function_exists( 'aether_active_design' ) ) {
		$design = aether_active_design();
		// Legacy Ferm pack shipped remote CDN assets; Vineta and future packs use local assets.
		if ( 'fermliving' === $design ) {
			$live_cdn = 'https://fermliving.com/';
		}
	}

	// Rewrite <img src="cdn/..."> and <img src="../cdn/...">
	if ( $live_cdn ) {
		$content = preg_replace(
			'/(<img\s[^>]*src\s*=\s*["\'])((?:\.\.\/)?cdn\/)/i',
			'$1' . $live_cdn . '$2',
			$content
		);

		// Rewrite ALL cdn/ URLs inside srcset attributes (each srcset has multiple comma-separated entries)
		$content = preg_replace_callback(
			'/(<img\s[^>]*srcset\s*=\s*["\'])([^"\']*)["\']/i',
			function ( $m ) use ( $live_cdn ) {
				$prefix = $m[1];
				$srcset = $m[2];
				$rewritten = preg_replace(
					'/(^|,\s*)((?:\.\.\/)?cdn\/)/',
					'$1' . $live_cdn . '$2',
					$srcset
				);
				return $prefix . $rewritten . '"';
			},
			$content
		);

		// Rewrite ALL cdn/ URLs inside <source srcset="...">
		$content = preg_replace_callback(
			'/(<source\s[^>]*srcset\s*=\s*["\'])([^"\']*)["\']/i',
			function ( $m ) use ( $live_cdn ) {
				$prefix = $m[1];
				$srcset = $m[2];
				$rewritten = preg_replace(
					'/(^|,\s*)((?:\.\.\/)?cdn\/)/',
					'$1' . $live_cdn . '$2',
					$srcset
				);
				return $prefix . $rewritten . '"';
			},
			$content
		);

		// Rewrite <source src="cdn/...">
		$content = preg_replace(
			'/(<source\s[^>]*\bsrc\s*=\s*["\'])((?:\.\.\/)?cdn\/)/i',
			'$1' . $live_cdn . '$2',
			$content
		);

		// Rewrite external _cdn.assets.struct.com URLs to live struct.com CDN
		$content = preg_replace(
			'/\.\.\/_cdn\.assets\.struct\.com\//',
			'https://cdn.assets.struct.com/',
			$content
		);

		// Rewrite protocol-relative CDN URLs
		$content = preg_replace(
			'/((?:poster|src|href|data-[a-z-]+)\s*=\s*["\'])\/\/fermliving\.com\/cdn\//i',
			'$1' . $live_cdn . 'cdn/',
			$content
		);

		// Rewrite bare <a href="cdn/..."> links
		$content = preg_replace(
			'/(<a\s[^>]*href\s*=\s*["\x27])((?:\.\.\/)?cdn\/)/i',
			'$1' . $live_cdn . '$2',
			$content
		);

		// Rewrite <link rel="preload" href="cdn/...">
		$content = preg_replace(
			'/(<link\s[^>]*href\s*=\s*["\'])((?:\.\.\/)?cdn\/)/i',
			'$1' . $live_cdn . '$2',
			$content
		);

		// Rewrite CSS url() references: url(cdn/...)
		$content = preg_replace(
			'/(url\(\s*["\']?)((?:\.\.\/)?cdn\/)/i',
			'$1' . $live_cdn . '$2',
			$content
		);
	}

	// Rewrite nav/content links: Shopify paths -> WordPress routes
	// Index/home: index.html -> /
	$content = preg_replace(
		'/(<a\s[^>]*href\s*=\s*["\x27])((?:\.\.\/)?index\.html)(["\x27])/i',
		'$1' . $site_url . '/$3',
		$content
	);

	// Product collections: collections/X.html -> /product-category/X
	$content = preg_replace(
		'/(<a\s[^>]*href\s*=\s*["\x27])((?:\.\.\/)?collections\/)([^"\x27]+?)(\.html)(["\x27])/i',
		'$1' . $site_url . '/product-category/$3$5',
		$content
	);

	// Products: products/X.html -> /product/X
	$content = preg_replace(
		'/(<a\s[^>]*href\s*=\s*["\x27])((?:\.\.\/)?products\/)([^"\x27]+?)(\.html)(["\x27])/i',
		'$1' . $site_url . '/product/$3$5',
		$content
	);

	// Account: account/X.html -> /my-account/
	$content = preg_replace(
		'/(<a\s[^>]*href\s*=\s*["\x27])((?:\.\.\/)?account\/)([^"\x27]*?)(\.html)(["\x27])/i',
		'$1' . $site_url . '/my-account/$5',
		$content
	);

	// Blogs: blogs/X.html -> /blog/
	$content = preg_replace(
		'/(<a\s[^>]*href\s*=\s*["\x27])((?:\.\.\/)?blogs\/)([^"\x27]*?)(\.html)(["\x27])/i',
		'$1' . $site_url . '/blog/$5',
		$content
	);

	// Pages: pages/X.html -> /X
	$content = preg_replace(
		'/(<a\s[^>]*href\s*=\s*["\x27])((?:\.\.\/)?pages\/)([^"\x27]+?)(\.html)(["\x27])/i',
		'$1' . $site_url . '/$3$5',
		$content
	);

	// Bare Shopify filenames: account.html, cart.html, checkout.html
	$content = preg_replace(
		'/(<a\s[^>]*href\s*=\s*["\x27])((?:\.\.\/)?account\.html)(["\x27])/i',
		'$1' . $site_url . '/my-account/$3',
		$content
	);
	$content = preg_replace(
		'/(<a\s[^>]*href\s*=\s*["\x27])((?:\.\.\/)?cart\.html)(["\x27])/i',
		'$1' . $site_url . '/cart/$3',
		$content
	);
	$content = preg_replace(
		'/(<a\s[^>]*href\s*=\s*["\x27])((?:\.\.\/)?checkout\.html)(["\x27])/i',
		'$1' . $site_url . '/checkout/$3',
		$content
	);

	// === COMPREHENSIVE VINETA HTML FILE REWRITE MAP ===
	// All Vineta flat HTML files -> clean WordPress URLs
	$vineta_map = array(
		// Shop variants -> /shop/
		'shop-default.html'                 => '/shop/',
		'shop-left-sidebar.html'            => '/shop/',
		'shop-right-sidebar.html'           => '/shop/',
		'shop-filter-drawer.html'           => '/shop/',
		'shop-filter-sidebar.html'          => '/shop/',
		'shop-filter-hidden.html'           => '/shop/',
		'shop-fullwidth.html'               => '/shop/',
		'shop-grid-3-columns.html'          => '/shop/',
		'shop-horizontal-filter.html'       => '/shop/',
		'shop-infinity-scroll.html'         => '/shop/',
		'shop-load-more-button.html'        => '/shop/',
		'shop-sub-collection.html'          => '/shop/',
		'shop-sub-collection-02.html'       => '/shop/',
		'shop-collection-list.html'         => '/shop/',
		// Blog variants -> /blog/
		'blog-grid-01.html'                 => '/blog/',
		'blog-grid-02.html'                 => '/blog/',
		'blog-list-01.html'                 => '/blog/',
		'blog-list-02.html'                 => '/blog/',
		'blog-single.html'                  => '/blog/',
		// Product variants -> /shop/
		'product-detail.html'               => '/shop/',
		'product-3d.html'                   => '/shop/',
		'product-countdown-timer.html'      => '/shop/',
		'product-description-accordions.html' => '/shop/',
		'product-description-tab.html'      => '/shop/',
		'product-group.html'                => '/shop/',
		'product-out-of-stock.html'         => '/shop/',
		'product-pickup-available.html'     => '/shop/',
		'product-swatch-dropdown.html'      => '/shop/',
		'product-together.html'             => '/shop/',
		'product-video.html'                => '/shop/',
		'product-volume-discount.html'      => '/shop/',
		'product-affiliate.html'            => '/shop/',
		'product-bottom-thumbnail.html'     => '/shop/',
		'product-buyX-getY.html'            => '/shop/',
		'product-description-side-accordions.html' => '/shop/',
		'product-description-vertical.html' => '/shop/',
		'product-drawer-sidebar.html'       => '/shop/',
		'product-external-zoom.html'        => '/shop/',
		'product-grid.html'                 => '/shop/',
		'product-grid-02.html'              => '/shop/',
		'product-inner-circle-zoom.html'    => '/shop/',
		'product-inner-zoom.html'           => '/shop/',
		'product-no-zoom.html'              => '/shop/',
		'product-open-lightbox.html'        => '/shop/',
		'product-right-thumbnail.html'      => '/shop/',
		'product-stacked.html'              => '/shop/',
		'product-style-01.html'             => '/shop/',
		'product-style-02.html'             => '/shop/',
		'product-style-03.html'             => '/shop/',
		'product-swatch-dropdown-color.html' => '/shop/',
		'product-swatch-image.html'         => '/shop/',
		'product-swatch-image-square.html'  => '/shop/',
		'product-volume-discount-thumbnail.html' => '/shop/',
		// Static pages -> WordPress page slugs
		'about-us.html'                     => '/about-us/',
		'contact-us.html'                   => '/contact-us/',
		'faq.html'                          => '/faq/',
		'cookies.html'                      => '/cookies/',
		'privacy-policy.html'               => '/privacy-policy/',
		'term-and-condition.html'           => '/term-and-condition/',
		'return-and-refund.html'            => '/return-and-refund/',
		'shipping.html'                     => '/shipping/',
		'store-location.html'               => '/store-location/',
		'coming-soon.html'                  => '/coming-soon/',
		// Cart/Checkout/Account
		'view-cart.html'                    => '/cart/',
		'cart-empty.html'                   => '/cart/',
		'cart-drawer-v2.html'               => '/cart/',
		'checkout.html'                     => '/checkout/',
		'thank-you.html'                    => '/checkout/',
		'account-page.html'                 => '/my-account/',
		'account-addresses.html'            => '/my-account/',
		'account-details.html'              => '/my-account/',
		'account-orders.html'               => '/my-account/',
		// Wishlist / Compare
		'wish-list.html'                    => '/my-account/',
		'compare.html'                      => '/shop/',
		// Home / Misc
		'index.html'                        => '/',
		'404.html'                          => '/',
		'home-fashion-02.html'              => '/',
		'before-you-leave.html'             => '/',
		'newsletter-popup-02.html'          => '/',
		'newsletter-popup-03.html'          => '/',
	);

	// Apply Vineta map: rewrite href="filename.html" to clean WordPress URLs
	foreach ( $vineta_map as $html_file => $wp_url ) {
		$escaped = preg_quote( $html_file, '/' );
		$target  = $site_url . $wp_url;
		// Match href="filename.html" or href='../filename.html'
		$content = preg_replace(
			'/(<a\s[^>]*href\s*=\s*["\x27])((?:\.\.\/)?' . $escaped . ')(["\x27])/i',
			'$1' . $target . '$3',
			$content
		);
	}
	// === Account page: rewrite Shopify login form to WooCommerce ===
	// Note: Logged-in users are already routed to WooCommerce template via
	// aureon_ferm_template_include() in frontend.php. This code only runs
	// for logged-out users seeing the frozen login.html.
	if ( function_exists( 'is_account_page' ) && is_account_page() ) {

		// Rewrite form action: /account/login -> /my-account/ (WooCommerce contract)
		$content = preg_replace(
			'/(<form\s[^>]*action\s*=\s*["\x27])\/account\/login(["\x27])/i',
			'$1' . esc_url( home_url( '/my-account/' ) ) . '$2',
			$content
		);

		// Rewrite email field: customer[email] -> username (WooCommerce contract)
		$content = preg_replace(
			'/name\s*=\s*["\x27]customer\[email\]["\x27]/i',
			'name="username"',
			$content
		);

		// Rewrite password field: customer[password] -> password (WooCommerce contract)
		$content = preg_replace(
			'/name\s*=\s*["\x27]customer\[password\]["\x27]/i',
			'name="password"',
			$content
		);

		// Remove Shopify hidden inputs (form_type, utf8)
		$content = preg_replace(
			'/<input\s+type=["\x27]hidden["\x27]\s+name=["\x27]form_type["\x27]\s+value=["\x27]customer_login["\x27]\s*\/?>/i',
			'',
			$content
		);
		$content = preg_replace(
			'/<input\s+type=["\x27]hidden["\x27]\s+name=["\x27]utf8["\x27]\s+value=["\x27]\?["\x27]\s*\/?>/i',
			'',
			$content
		);

		// Inject WooCommerce login nonce (REQUIRED by WooCommerce)
		// Target: after the submit button inside #customer_login form.
		// Using submit-login id (unique to the Ferm login form) to avoid
		// injecting into other forms (notification, recovery, etc.).
		$nonce_value = wp_create_nonce( 'woocommerce-login' );
		$nonce_input = '<input type="hidden" name="woocommerce-login-nonce" value="' . esc_attr( $nonce_value ) . '" />';
		$content = preg_replace(
			"/(<input[^>]*id=['\"\\x27]submit-login['\"\\x27][^>]*>)/i",
			'$1' . "\n" . $nonce_input,
			$content,
			1
		);

		// Rewrite lost password link: /account/login#recover -> WooCommerce lost password
		$content = preg_replace(
			'/(<a\s[^>]*href\s*=\s*["\x27])((?:\.\.\/)?account\/login\.html)(#[^"\x27]*)?(["\x27])/i',
			'$1' . esc_url( wc_lostpassword_url() ) . '$4',
			$content
		);

		// Inject FermPageData for account state (logged-out only).
		// Use a DOMContentLoaded handler to set the values AFTER any other
		// FermPageData initialization (e.g. AETHER's) has completed.
		$bridge_script = '<script>document.addEventListener("DOMContentLoaded",function(){' .
			'window.FermPageData=window.FermPageData||{};' .
			'window.FermPageData.customer={isLoggedIn:false,displayName:null};' .
			'});</script>';
		$content = $bridge_script . $content;

		// WooCommerce error notices are displayed via the notice system
		// when the page reloads after form submission. No need to create
		// new DOM elements for error display.
	}

	return $content;
}
