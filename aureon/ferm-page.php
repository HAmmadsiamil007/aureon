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
wp_head();
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
	// Rewrite nav/content links: Shopify paths -> WordPress paths
	echo "document.querySelectorAll('a[href]').forEach(function(a){\n";
	echo "  var h=a.getAttribute('href');\n";
	echo "  if(!h||h.charAt(0)==='#'||h.indexOf('http')===0||h.indexOf('mailto:')===0||h.indexOf('tel:')===0||h.indexOf('javascript:')===0) return;\n";
	echo "  // Skip WordPress admin/API/cart/checkout/account paths\n";
	echo "  if(h.indexOf('/wp-')===0||h.indexOf('/cart')===0||h.indexOf('/checkout')===0||h.indexOf('/my-account')===0||h.indexOf('/product/')===0||h.indexOf('/shop')===0||h.indexOf('/blog')===0) return;\n";
	echo "  var rest=h.replace(/^\\.\\.\\//,'');\n";
	echo "  // Map known Shopify paths to WordPress routes\n";
	echo "  if(rest==='index.html'||rest==='./index.html'||rest==='') a.href=s+'/';\n";
	echo "  else if(rest.indexOf('collections/')===0) a.href=s+'/product-category/'+rest.replace('collections/','').replace(/\\.html$/,'');\n";
	echo "  else if(rest.indexOf('products/')===0) a.href=s+'/product/'+rest.replace('products/','').replace(/\\.html$/,'');\n";
	echo "  else if(rest.indexOf('account/')===0) a.href=s+'/my-account/';\n";
	echo "  else if(rest.indexOf('blogs/')===0) a.href=s+'/blog/';\n";
	echo "  else if(rest.indexOf('pages/')===0) a.href=s+'/'+rest.replace('pages/','').replace(/\\.html$/,'');\n";
	echo "  else if(rest.indexOf('cart')===0) a.href=s+'/cart/';\n";
	echo "  else if(rest.indexOf('checkout')===0) a.href=s+'/checkout/';\n";
	echo "  // For unknown paths, strip .html and point to site root\n";
	echo "  else a.href=s+'/'+rest.replace(/\\.html$/,'');\n";
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
	echo "    });\n";
	echo "  });\n";
	echo "});\n";
	echo "obs.observe(document.documentElement,{childList:true,subtree:true});\n";
	echo "})()\n";
	echo "</script>\n";
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
		if ( is_product() ) {
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
		}

		// Blog / posts archive.
		if ( is_home() || is_post_type_archive( 'post' ) || is_page( 'blog' ) || is_page( 'stories' ) ) {
			if ( ! empty( $pages['blog'] ) ) {
				return $pages['blog'];
			}
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

	// --- Fallback: hardcoded Ferm route map (backward compatibility) ---
	// Homepage.
	if ( is_front_page() || ( is_home() && ! is_paged() ) ) {
		return 'index.html';
	}

	// Single product.
	if ( is_product() ) {
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
			'contact'           => 'pages/contact.html',
			'about'             => 'pages/about-ferm-living.html',
			'about-ferm-living' => 'pages/about-ferm-living.html',
			'store-locator'     => 'pages/store-locator.html',
			'store locator'     => 'pages/store-locator.html',
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

	// 404.
	if ( is_404() ) {
		return 'pages/contact.html'; // Fallback to contact page.
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

	// Rewrite <img src="cdn/..."> and <img src="../cdn/...">
	$content = preg_replace(
		'/(<img\s[^>]*src\s*=\s*["\'])((?:\.\.\/)?cdn\/)/i',
		'$1' . $pack_url . '$2',
		$content
	);

	// Rewrite <img srcset="cdn/..."> and srcset="../cdn/..."
	$content = preg_replace(
		'/(<img\s[^>]*srcset\s*=\s*["\'])((?:\.\.\/)?cdn\/)/i',
		'$1' . $pack_url . '$2',
		$content
	);

	// Rewrite <source srcset="cdn/...">
	$content = preg_replace(
		'/(<source\s[^>]*srcset\s*=\s*["\'])((?:\.\.\/)?cdn\/)/i',
		'$1' . $pack_url . '$2',
		$content
	);

	// Rewrite <link rel="preload" href="cdn/...">
	$content = preg_replace(
		'/(<link\s[^>]*href\s*=\s*["\'])((?:\.\.\/)?cdn\/)/i',
		'$1' . $pack_url . '$2',
		$content
	);

	// Rewrite <link rel="stylesheet" href="cdn/...">
	// (already handled by wp_head enqueues, but safe to catch stragglers)

	// Rewrite CSS url() references: url(cdn/...) and url(../cdn/...)
	$content = preg_replace(
		'/(url\(\s*["\']?)((?:\.\.\/)?cdn\/)/i',
		'$1' . $pack_url . '$2',
		$content
	);

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

	return $content;
}
