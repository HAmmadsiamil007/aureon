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

// WooCommerce pages (checkout, cart) must use their native templates, not the
// frozen HTML. Bail out so the WC template routing handles them.
$wc_pages = array();
if ( function_exists( 'wc_get_page_id' ) ) {
	$wc_pages = array(
		'checkout' => wc_get_page_id( 'checkout' ),
		'cart'     => wc_get_page_id( 'cart' ),
	);
}
foreach ( $wc_pages as $wc_slug => $wc_id ) {
	if ( $wc_id > 0 && is_page( $wc_id ) ) {
		return;
	}
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
$pack_url = function_exists( 'aether_pack_url' ) ? aether_pack_url() : '';
$body_content = aureon_ferm_extract_body( $html );
if ( false !== $body_content ) {
	// Server-side path rewrite: convert relative CDN paths to absolute before output.
	if ( $pack_url ) {
		$body_content = aureon_ferm_rewrite_paths( $body_content, $pack_url );
		// jQuery single-source: WordPress prints jQuery in <head> (vineta-data-shims
		// declares a jquery dependency), so the pack's own js/jquery.min.js script
		// tag in the frozen body is redundant — strip it before output.
		$body_content = preg_replace(
			'/<script[^>]*src\s*=\s*["\x27][^"\x27]*js\/jquery\.min\.js["\x27][^>]*><\/script>/i',
			'',
			$body_content
		);
	}
	echo '<body' . aureon_ferm_render_attrs( $body_attrs['body'] ) . ">\n";

	// jQuery alias: WordPress prints jQuery in noConflict mode (no global `$`),
	// but the frozen design-pack scripts (carousel.js, main.js) expect `$` and
	// die with "TypeError: $ is not a function" — leaving every swiper slide
	// full-width (one giant card). Restore the alias before pack scripts run.
	echo '<script>if(window.jQuery&&!window.$){window.$=window.jQuery;}</script>' . "\n";

	echo $body_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- client presentation HTML, already escaped at source.
} else {
	// Fallback: output entire HTML (already a complete document).
	echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

// --- Footer: WooCommerce cart fragments, analytics, admin bar ---
// Fix relative paths in frozen HTML: rewrite cdn/... and nav links to absolute URLs.
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
 * Map a WordPress route class to its canonical route identifier.
 *
 * Single source of truth for route classification. The manifest "pages"
 * mapping is the ONLY template authority — there is intentionally no legacy
 * route fallback here. An unmapped route class returns '404' so a WordPress
 * route can NEVER silently render the wrong page with HTTP 200.
 *
 * @return string Route class identifier.
 */
function aureon_ferm_route_class() {
	// Single product (most specific first).
	if ( function_exists( 'is_product' ) && is_product() ) {
		return 'product';
	}

	// Cart / checkout / account (WooCommerce route classes).
	if ( function_exists( 'is_cart' ) && is_cart() ) {
		return 'cart';
	}
	if ( function_exists( 'is_checkout' ) && is_checkout() ) {
		return 'checkout';
	}
	if ( function_exists( 'is_account_page' ) && is_account_page() ) {
		return 'account';
	}

	// Product archive / shop page.
	if ( is_post_type_archive( 'product' ) || is_page( 'shop' ) ) {
		return 'shop';
	}

	// Product category taxonomy.
	if ( is_tax( 'product_cat' ) ) {
		return 'category';
	}

	// Search results (before is_page/is_home so ?s= never mismatches).
	if ( is_search() ) {
		return 'search';
	}

	// Single blog post.
	if ( is_singular( 'post' ) ) {
		return 'blog_single';
	}

	// Homepage — MUST outrank the is_home() blog check below. When WordPress
	// is set to "Your homepage displays: Your latest posts", the site root is
	// BOTH is_front_page() and is_home(); classifying it as 'blog' replaces
	// the designed homepage with the blog grid. Paged front pages (/page/2/)
	// are the posts archive, so they fall through to the blog check.
	if ( is_front_page() && ! is_paged() ) {
		return 'home';
	}

	// Blog / posts archive.
	if ( is_home() || is_post_type_archive( 'post' ) || is_page( 'blog' ) || is_page( 'stories' ) ) {
		return 'blog';
	}

	// Static pages (slug-mapped below).
	if ( is_page() ) {
		return 'static';
	}

	// Unknown route (includes WordPress 404) — must render the 404 page.
	return '404';
}

/**
 * Map a route class + context to a manifest pages key.
 *
 * @param string $class Route class from aureon_ferm_route_class().
 * @param array  $pages Manifest pages mapping.
 * @return string|false Manifest file path, or false when unmapped.
 */
function aureon_ferm_manifest_file_for( $class, $pages ) {
	switch ( $class ) {
		case 'home':
			return ! empty( $pages['home'] ) ? $pages['home'] : false;

		case 'product':
			$slug = get_query_var( 'product' );
			if ( $slug && ! empty( $pages['products'][ $slug ] ) ) {
				return $pages['products'][ $slug ];
			}
			return ! empty( $pages['product_generic'] ) ? $pages['product_generic'] : false;

		case 'shop':
			if ( ! empty( $pages['shop'] ) ) {
				return $pages['shop'];
			}
			if ( ! empty( $pages['collections'] ) && is_array( $pages['collections'] ) ) {
				return reset( $pages['collections'] );
			}
			return false;

		case 'category':
			$slug = get_query_var( 'product_cat' );
			if ( $slug && ! empty( $pages['collections'][ $slug ] ) ) {
				return $pages['collections'][ $slug ];
			}
			if ( ! empty( $pages['collections']['default'] ) ) {
				return $pages['collections']['default'];
			}
			if ( ! empty( $pages['shop'] ) ) {
				return $pages['shop'];
			}
			return false;

		case 'search':
			return ! empty( $pages['search'] ) ? $pages['search'] : false;

		case 'blog_single':
			return ! empty( $pages['blog_single'] ) ? $pages['blog_single'] : false;

		case 'blog':
			return ! empty( $pages['blog'] ) ? $pages['blog'] : false;

		case 'cart':
			return ! empty( $pages['cart'] ) ? $pages['cart'] : false;

		case 'checkout':
			return ! empty( $pages['checkout'] ) ? $pages['checkout'] : false;

		case 'account':
			return ! empty( $pages['account'] ) ? $pages['account'] : false;

		case 'static':
			$slug = get_query_var( 'pagename' );
			if ( $slug && ! empty( $pages['pages'][ $slug ] ) ) {
				return $pages['pages'][ $slug ];
			}
			if ( $slug && ! empty( $pages['static'][ $slug ] ) ) {
				return $pages['static'][ $slug ];
			}
			return false;

		case '404':
			// Prefer an explicit manifest 404 page, then a pack-root 404.html.
			if ( ! empty( $pages['404'] ) ) {
				return $pages['404'];
			}
			if ( file_exists( aether_active_design_dir() . '404.html' ) ) {
				return '404.html';
			}
			return false;
	}

	return false;
}

/**
 * Map WordPress route to a complete-page HTML file path (relative to pack dir).
 *
 * Manifest-authoritative: the design pack's manifest.json "pages" mapping is
 * the ONLY template source. There is deliberately no legacy route fallback —
 * an unmapped route must fail loudly (false => HTTP 404 in the caller), never
 * silently serve the wrong page behind HTTP 200.
 *
 * @return string|false File path or false if no match.
 */
function aureon_ferm_resolve_page() {
	$manifest = aether_design_manifest();
	if ( empty( $manifest['pages'] ) ) {
		return false;
	}

	$pages = $manifest['pages'];
	$class = aureon_ferm_route_class();

	return aureon_ferm_manifest_file_for( $class, $pages );
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

	// Rewrite relative pack-asset paths (images/) to absolute pack URLs.
	// Frozen templates ship relative paths that only resolve at the site
	// root; on nested routes (e.g. /product/{slug}, the 404 template) the
	// browser resolves them against the current path and they 404.
	if ( $pack_url ) {
		$content = preg_replace(
			'/((?:src|href|poster|data-src)\s*=\s*["\'])((?:\.\/)?(?:images|fonts)\/)/i',
			'$1' . $pack_url . '$2',
			$content
		);
		// Inline CSS url(images/...) references inside style attributes/blocks.
		// Groups: 1 = optional quote, 2 = images/ or fonts/ prefix.
		$content = preg_replace(
			'/url\((["\x27]?)((?:\.\/)?(?:images|fonts)\/)/i',
			'url($1' . $pack_url . '$2',
			$content
		);
	}

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

	// Static pack pages (manifest "pages.static" map): bare filenames like
	// about-us.html, privacy-policy.html -> real WordPress page permalinks.
	// Server-side so links work even when the JS bridge is blocked/broken.
	$manifest = function_exists( 'aether_design_manifest' ) ? aether_design_manifest() : array();
	if ( ! empty( $manifest['pages']['static'] ) && is_array( $manifest['pages']['static'] ) ) {
		foreach ( $manifest['pages']['static'] as $slug => $file ) {
			if ( ! is_string( $file ) || '' === $file || ! is_string( $slug ) || '' === $slug ) {
				continue;
			}
			$url = home_url( '/' . $slug . '/' );
			if ( function_exists( 'get_page_by_path' ) ) {
				$page = get_page_by_path( $slug );
				if ( $page instanceof WP_Post && 'publish' === $page->post_status ) {
					$permalink = get_permalink( $page );
					if ( $permalink ) {
						$url = $permalink;
					}
				}
			}
			$pattern = '/(<a\s[^>]*href\s*=\s*["\x27])((?:\.\.\/|\.\/)?' . preg_quote( $file, '/' ) . ')(["\x27])/i';
			$content = preg_replace( $pattern, '$1' . esc_url_raw( $url ) . '$3', $content );
		}
	}

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
