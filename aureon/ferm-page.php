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

// --- Override 404 status for product URLs ---
// When WordPress routes /product/[slug] to ferm-page.php via template_include,
// the HTTP status is already 404. Override it to 200 before any output.
$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? wp_parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ) : '';
if ( preg_match( '#/product/([^/]+)/?$#', $request_uri ) ) {
	status_header( 200 );
	nocache_headers();
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

	// Image404 fallback: if a live CDN image fails, retry from local pack.
	echo "<script>\n";
	echo "(function(){\n";
	echo "var p='" . esc_js( $pack_url ) . "';\n";
	echo "document.querySelectorAll('img').forEach(function(img){\n";
	echo "  img.addEventListener('error',function(){\n";
	echo "    var src=img.getAttribute('src');\n";
	echo "    if(src&&src.indexOf('https://fermliving.com/cdn/')===0&&!img.dataset.fallback){\n";
	echo "      img.dataset.fallback='1';\n";
	echo "      var local=p+'cdn/'+src.replace('https://fermliving.com/cdn/','')+'?v='+Date.now();\n";
	echo "      img.src=local;\n";
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
		if ( function_exists( 'is_product' ) && is_product() ) {
			$slug = get_query_var( 'product' );
			if ( $slug && ! empty( $pages['products'][ $slug ] ) ) {
				return $pages['products'][ $slug ];
			}
			// Fallback: generic product template.
			if ( ! empty( $pages['product_generic'] ) ) {
				return $pages['product_generic'];
			}
			// Fallback: first available product page from manifest.
			if ( ! empty( $pages['products'] ) && is_array( $pages['products'] ) ) {
				return reset( $pages['products'] );
			}
		}

		// Product archive / shop page.
		if ( function_exists( 'is_post_type_archive' ) && is_post_type_archive( 'product' ) || is_page( 'shop' ) ) {
			if ( ! empty( $pages['collections'] ) && is_array( $pages['collections'] ) ) {
				return reset( $pages['collections'] );
			}
		}

		// Product category.
		if ( function_exists( 'is_tax' ) && is_tax( 'product_cat' ) ) {
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

	// Product URL pattern detection — catches product-like URLs that WordPress
	// doesn't recognize as WooCommerce products (demo products, missing products).
	// MUST come after is_product() and before is_home()/is_search()/is_404().
	$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? wp_parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ) : '';
	if ( preg_match( '#/product/([^/]+)/?$#', $request_uri, $m ) ) {
		// Product-like URL — use generic product template.
		if ( ! empty( $pages['product_generic'] ) ) {
			return $pages['product_generic'];
		}
		// Fallback: check for exact product HTML file.
		$slug = sanitize_title( $m[1] );
		if ( $slug && ! empty( $pages['products'][ $slug ] ) ) {
			return $pages['products'][ $slug ];
		}
		// Fallback: check filesystem for exact product HTML.
		$product_file = 'products/' . $slug . '.html';
		if ( file_exists( aether_active_design_dir() . $product_file ) ) {
			return $product_file;
		}
		// Final fallback: generic product template from filesystem.
		$generic = 'products/_generic-product.html';
		if ( file_exists( aether_active_design_dir() . $generic ) ) {
			return $generic;
		}
	}

		// Blog / posts archive.
		if ( is_home() || ( function_exists( 'is_post_type_archive' ) && is_post_type_archive( 'post' ) ) || is_page( 'blog' ) || is_page( 'stories' ) ) {
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
	if ( function_exists( 'is_product' ) && is_product() ) {
		$slug = get_query_var( 'product' );
		if ( $slug ) {
			$file = 'products/' . $slug . '.html';
			if ( file_exists( aether_active_design_dir() . $file ) ) {
				return $file;
			}
		}
		// Fallback: generic product template.
		$generic = 'products/_generic-product.html';
		if ( file_exists( aether_active_design_dir() . $generic ) ) {
			return $generic;
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
	if ( function_exists( 'is_post_type_archive' ) && is_post_type_archive( 'product' ) || is_page( 'shop' ) ) {
		return 'collections/furniture.html';
	}

	// Product category.
	if ( function_exists( 'is_tax' ) && is_tax( 'product_cat' ) ) {
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

	// Product URL pattern detection — fallback section.
	// Catches product-like URLs that WordPress doesn't recognize as products.
	$request_uri_fb = isset( $_SERVER['REQUEST_URI'] ) ? wp_parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ) : '';
	if ( preg_match( '#/product/([^/]+)/?$#', $request_uri_fb, $m_fb ) ) {
		$slug_fb = sanitize_title( $m_fb[1] );
		if ( $slug_fb ) {
			$file_fb = 'products/' . $slug_fb . '.html';
			if ( file_exists( aether_active_design_dir() . $file_fb ) ) {
				return $file_fb;
			}
		}
		// Fallback: generic product template.
		$generic_fb = 'products/_generic-product.html';
		if ( file_exists( aether_active_design_dir() . $generic_fb ) ) {
			return $generic_fb;
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
		// Check if this looks like a product URL pattern.
		$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? wp_parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ) : '';
		if ( preg_match( '#/product/([^/]+)/?$#', $request_uri, $m ) ) {
			// Product-like URL that doesn't exist — use generic product template.
			$generic = 'products/_generic-product.html';
			if ( file_exists( aether_active_design_dir() . $generic ) ) {
				return $generic;
			}
		}
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
	// Live CDN base — load images/media directly from fermliving.com Shopify CDN
	$live_cdn = 'https://fermliving.com/';

	// Rewrite <img src="cdn/..."> and <img src="../cdn/...">
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

	// Rewrite protocol-relative //fermliving.com/cdn/... — keep on live CDN
	$content = preg_replace(
		'/((?:poster|src|href|data-[a-z-]+)\s*=\s*["\'])\/\/fermliving\.com\/cdn\//i',
		'$1' . $live_cdn . 'cdn/',
		$content
	);

	// Rewrite bare <a href="cdn/..."> links — keep on live CDN
	$content = preg_replace(
		'/(<a\s[^>]*href\s*=\s*["\x27])((?:\.\.\/)?cdn\/)/i',
		'$1' . $live_cdn . '$2',
		$content
	);

	// Rewrite <link rel="preload" href="cdn/..."> — keep on live CDN
	$content = preg_replace(
		'/(<link\s[^>]*href\s*=\s*["\'])((?:\.\.\/)?cdn\/)/i',
		'$1' . $live_cdn . '$2',
		$content
	);

	// Rewrite CSS url() references: url(cdn/...) — keep on live CDN for font/image assets
	$content = preg_replace(
		'/(url\(\s*["\']?)((?:\.\.\/)?cdn\/)/i',
		'$1' . $live_cdn . '$2',
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

	// Strip Shopify content hashes from live CDN filenames
	// e.g. file.7cb49da5d1.webp -> file.webp, file.ea2092c6f9.jpg -> file.jpg
	$content = preg_replace(
		'/(https?:\/\/fermliving\.com\/cdn\/shop\/[^"\s?]+?)\.([0-9a-f]{10})\.(webp|jpg|jpeg|png|gif|avif|svg)/i',
		'$1.$3',
		$content
	);

	return $content;
}
