<?php
/**
 * AETHER REST API Endpoints.
 *
 * Registers REST API routes under the aether/v1 namespace for AJAX interactions.
 * Endpoints return HTML fragments for direct DOM injection per the integration spec.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register AETHER REST API routes.
 */
add_action( 'rest_api_init', 'aether_register_rest_routes' );

function aether_register_rest_routes() {

	// ─── Search ──────────────────────────────────────────────────
	register_rest_route(
		'aether/v1',
		'/search',
		array(
			'methods'             => 'GET',
			'callback'            => 'aether_rest_search',
			'permission_callback' => '__return_true',
			'args'                => array(
				'q'     => array(
					'required'          => true,
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_text_field',
					'validate_callback' => function ( $param ) {
						return is_string( $param ) && strlen( $param ) >= 2;
					},
				),
				'limit' => array(
					'default'           => 5,
					'type'              => 'integer',
					'sanitize_callback' => 'absint',
				),
				'type'  => array(
					'default'           => 'all',
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_text_field',
					'validate_callback' => function ( $param ) {
						return in_array( $param, array( 'product', 'post', 'all' ), true );
					},
				),
			),
		)
	);

	// ─── Product Filter ──────────────────────────────────────────
	register_rest_route(
		'aether/v1',
		'/products/filter',
		array(
			'methods'             => 'GET',
			'callback'            => 'aether_rest_product_filter',
			'permission_callback' => '__return_true',
			'args'                => array(
				'category'  => array(
					'default'           => 'all',
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_text_field',
				),
				'sort'      => array(
					'default'           => 'date',
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_text_field',
				),
				'order'     => array(
					'default'           => 'desc',
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_text_field',
				),
				'page'      => array(
					'default'           => 1,
					'type'              => 'integer',
					'sanitize_callback' => 'absint',
				),
				'per_page'  => array(
					'default'           => 12,
					'type'              => 'integer',
					'sanitize_callback' => 'absint',
				),
				'min_price' => array(
					'default'           => 0,
					'type'              => 'number',
					'sanitize_callback' => 'floatval',
				),
				'max_price' => array(
					'default'           => 9999,
					'type'              => 'number',
					'sanitize_callback' => 'floatval',
				),
				'on_sale'   => array(
					'default'           => false,
					'type'              => 'boolean',
				),
				'featured'  => array(
					'default'           => false,
					'type'              => 'boolean',
				),
			),
		)
	);

	// ─── Product Quick View ──────────────────────────────────────
	register_rest_route(
		'aether/v1',
		'/products/quick-view/(?P<id>\d+)',
		array(
			'methods'             => 'GET',
			'callback'            => 'aether_rest_quick_view',
			'permission_callback' => '__return_true',
			'args'                => array(
				'id' => array(
					'required'          => true,
					'type'              => 'integer',
					'sanitize_callback' => 'absint',
				),
			),
		)
	);

	// ─── Wishlist Toggle ─────────────────────────────────────────
	register_rest_route(
		'aether/v1',
		'/wishlist/toggle',
		array(
			'methods'             => 'POST',
			'callback'            => 'aether_rest_wishlist_toggle',
			'permission_callback' => function () {
				return is_user_logged_in();
			},
			'args'                => array(
				'product_id' => array(
					'required'          => true,
					'type'              => 'integer',
					'sanitize_callback' => 'absint',
				),
			),
		)
	);

	// ─── Wishlist Count ──────────────────────────────────────────
	register_rest_route(
		'aether/v1',
		'/wishlist/count',
		array(
			'methods'             => 'GET',
			'callback'            => 'aether_rest_wishlist_count',
			'permission_callback' => function () {
				return is_user_logged_in();
			},
		)
	);

	// ─── Mini Cart ───────────────────────────────────────────────
	register_rest_route(
		'aether/v1',
		'/cart/mini',
		array(
			'methods'             => 'GET',
			'callback'            => 'aether_rest_mini_cart',
			'permission_callback' => '__return_true',
		)
	);

	// ─── Cart Update ─────────────────────────────────────────────
	register_rest_route(
		'aether/v1',
		'/cart/update',
		array(
			'methods'             => 'POST',
			'callback'            => 'aether_rest_cart_update',
			'permission_callback' => '__return_true',
			'args'                => array(
				'cart_item_key' => array(
					'required'          => true,
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_text_field',
				),
				'quantity'      => array(
					'required'          => true,
					'type'              => 'integer',
					'sanitize_callback' => 'absint',
				),
			),
		)
	);

	// ─── Cart Remove ─────────────────────────────────────────────
	register_rest_route(
		'aether/v1',
		'/cart/remove',
		array(
			'methods'             => 'POST',
			'callback'            => 'aether_rest_cart_remove',
			'permission_callback' => '__return_true',
			'args'                => array(
				'cart_item_key' => array(
					'required'          => true,
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_text_field',
				),
			),
		)
	);

	// ─── Newsletter Subscribe ────────────────────────────────────
	register_rest_route(
		'aether/v1',
		'/newsletter/subscribe',
		array(
			'methods'             => 'POST',
			'callback'            => 'aether_rest_newsletter_subscribe',
			'permission_callback' => '__return_true',
			'args'                => array(
				'email' => array(
					'required'          => true,
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_email',
					'validate_callback' => 'is_email',
				),
			),
		)
	);

	// ─── Reviews Submit ──────────────────────────────────────────
	register_rest_route(
		'aether/v1',
		'/reviews/submit',
		array(
			'methods'             => 'POST',
			'callback'            => 'aether_rest_review_submit',
			'permission_callback' => function () {
				return is_user_logged_in();
			},
			'args'                => array(
				'product_id' => array(
					'required'          => true,
					'type'              => 'integer',
					'sanitize_callback' => 'absint',
				),
				'rating'     => array(
					'required'          => true,
					'type'              => 'integer',
					'sanitize_callback' => 'absint',
					'validate_callback' => function ( $param ) {
						return $param >= 1 && $param <= 5;
					},
				),
				'review'     => array(
					'required'          => false,
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_textarea_field',
				),
				'name'       => array(
					'required'          => false,
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_text_field',
				),
			),
		)
	);
}

// ─── Callback: Search ──────────────────────────────────────────

function aether_rest_search( $request ) {
	$query      = $request->get_param( 'q' );
	$limit      = $request->get_param( 'limit' );
	$type       = $request->get_param( 'type' );
	$products   = array();
	$posts      = array();
	$total      = 0;

	if ( in_array( $type, array( 'product', 'all' ), true ) && function_exists( 'wc_get_products' ) ) {
		$wc_args    = array(
			's'        => $query,
			'limit'    => $limit,
			'orderby'  => 'relevance',
			'return'   => 'objects',
		);
		$wc_results = wc_get_products( $wc_args );

		foreach ( $wc_results as $product ) {
			$image_id  = $product->get_image_id();
			$image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'woocommerce_thumbnail' ) : wc_placeholder_img_src();

			$products[] = array(
				'id'    => $product->get_id(),
				'name'  => $product->get_name(),
				'price' => wp_strip_all_tags( $product->get_price_html() ),
				'image' => $image_url,
				'url'   => $product->get_permalink(),
				'type'  => 'product',
			);
		}
		$total += count( $products );
	}

	if ( in_array( $type, array( 'post', 'all' ), true ) ) {
		$wp_args = array(
			's'              => $query,
			'posts_per_page' => $limit,
			'post_status'    => 'publish',
			'post_type'      => 'post',
		);

		$wp_query   = new WP_Query( $wp_args );
		$post_items = array();

		foreach ( $wp_query->posts as $post ) {
			$image_url = '';
			if ( has_post_thumbnail( $post ) ) {
				$image_url = get_the_post_thumbnail_url( $post, 'thumbnail' );
			}

			$post_items[] = array(
				'id'       => $post->ID,
				'title'    => $post->post_title,
				'excerpt'  => wp_trim_words( wp_strip_all_tags( $post->post_excerpt ?: $post->post_content ), 18, '...' ),
				'image'    => $image_url,
				'url'      => get_permalink( $post ),
				'type'     => 'post',
			);
		}

		$posts = $post_items;
		$total += count( $posts );
		wp_reset_postdata();
	}

	// Build HTML fragment.
	ob_start();
	if ( ! empty( $products ) ) :
		?>
		<div class="search-section">
			<h4 class="search-section-title">Products</h4>
			<?php foreach ( $products as $item ) : ?>
				<a href="<?php echo esc_url( $item['url'] ); ?>" class="search-result-item">
					<img src="<?php echo esc_url( $item['image'] ); ?>" alt="<?php echo esc_attr( $item['name'] ); ?>" class="search-result-image" loading="lazy">
					<div class="search-result-info">
						<span class="search-result-name"><?php echo esc_html( $item['name'] ); ?></span>
						<span class="search-result-price"><?php echo esc_html( $item['price'] ); ?></span>
					</div>
				</a>
			<?php endforeach; ?>
		</div>
		<?php
	endif;

	if ( ! empty( $posts ) ) :
		?>
		<div class="search-section">
			<h4 class="search-section-title">Articles</h4>
			<?php foreach ( $posts as $item ) : ?>
				<a href="<?php echo esc_url( $item['url'] ); ?>" class="search-result-item">
					<?php if ( ! empty( $item['image'] ) ) : ?>
						<img src="<?php echo esc_url( $item['image'] ); ?>" alt="<?php echo esc_attr( $item['title'] ); ?>" class="search-result-image" loading="lazy">
					<?php endif; ?>
					<div class="search-result-info">
						<span class="search-result-name"><?php echo esc_html( $item['title'] ); ?></span>
						<span class="search-result-excerpt"><?php echo esc_html( $item['excerpt'] ); ?></span>
					</div>
				</a>
			<?php endforeach; ?>
		</div>
		<?php
	endif;

	if ( empty( $products ) && empty( $posts ) ) :
		?>
		<div class="search-no-results">
			<p>No results found for "<strong><?php echo esc_html( $query ); ?></strong>"</p>
		</div>
		<?php
	endif;

	$html = ob_get_clean();

	return new WP_REST_Response(
		array(
			'html'  => $html,
			'data'  => array(
				'products' => $products,
				'posts'    => $posts,
			),
			'meta'  => array(
				'total' => $total,
				'query' => $query,
			),
		),
		200
	);
}

// ─── Callback: Product Filter ──────────────────────────────────

function aether_rest_product_filter( $request ) {
	if ( ! function_exists( 'wc_get_products' ) ) {
		return new WP_REST_Response( array( 'html' => '', 'data' => array(), 'meta' => array() ), 200 );
	}

	$wc_args = array(
		'limit'    => $request->get_param( 'per_page' ),
		'page'     => $request->get_param( 'page' ),
		'orderby'  => $request->get_param( 'sort' ),
		'order'    => strtoupper( $request->get_param( 'order' ) ),
		'return'   => 'objects',
		'status'   => 'publish',
	);

	$sort = $request->get_param( 'sort' );
	if ( 'price' === $sort ) {
		$wc_args['orderby'] = 'price';
	} elseif ( 'popularity' === $sort ) {
		$wc_args['orderby'] = 'popularity';
	} elseif ( 'rating' === $sort ) {
		$wc_args['orderby'] = 'rating';
	} else {
		$wc_args['orderby'] = 'date';
	}

	$category = $request->get_param( 'category' );
	if ( 'all' !== $category && ! empty( $category ) ) {
		$term = get_term_by( 'slug', $category, 'product_cat' );
		if ( $term ) {
			$wc_args['category'] = array( $category );
		}
	}

	$min_price = $request->get_param( 'min_price' );
	$max_price = $request->get_param( 'max_price' );
	if ( $min_price > 0 ) {
		$wc_args['min_price'] = $min_price;
	}
	if ( $max_price < 9999 ) {
		$wc_args['max_price'] = $max_price;
	}

	if ( $request->get_param( 'on_sale' ) ) {
		$wc_args['on_sale'] = true;
	}
	if ( $request->get_param( 'featured' ) ) {
		$wc_args['featured'] = true;
	}

	$wc_args['return'] = 'ids';
	$all_products      = wc_get_products( $wc_args );
	$found_posts       = count( $all_products );
	$max_num_pages     = (int) ceil( $found_posts / $request->get_param( 'per_page' ) );

	$wc_args['return'] = 'objects';
	$products          = wc_get_products( $wc_args );

	// Build HTML.
	ob_start();
	if ( ! empty( $products ) ) :
		?>
		<div class="shop-grid" data-columns="<?php echo esc_attr( get_theme_mod( 'aureon_woocommerce_shop_columns', 3 ) ); ?>">
			<?php foreach ( $products as $product ) : ?>
				<div class="product-card" data-product-id="<?php echo esc_attr( $product->get_id() ); ?>">
					<div class="product-image">
						<a href="<?php echo esc_url( $product->get_permalink() ); ?>">
							<?php echo wp_kses_post( $product->get_image( 'woocommerce_large_image' ) ); ?>
						</a>
						<?php if ( $product->is_on_sale() ) : ?>
							<span class="product-badge badge-sale">Sale</span>
						<?php endif; ?>
						<?php if ( $product->is_featured() ) : ?>
							<span class="product-badge badge-new">Featured</span>
						<?php endif; ?>
						<div class="product-quick-actions">
							<button type="button" class="quick-view-btn" data-product-id="<?php echo esc_attr( $product->get_id() ); ?>" aria-label="Quick view">
								<i class="fas fa-eye"></i>
							</button>
							<button type="button" class="wishlist-btn" data-product-id="<?php echo esc_attr( $product->get_id() ); ?>" aria-label="Add to wishlist">
								<i class="far fa-heart"></i>
							</button>
						</div>
					</div>
					<div class="product-info">
						<a href="<?php echo esc_url( $product->get_permalink() ); ?>">
							<h3 class="product-name"><?php echo esc_html( $product->get_name() ); ?></h3>
						</a>
						<span class="product-category"><?php echo esc_html( wc_get_product_category_list( $product->get_id(), ', ' ) ); ?></span>
						<div class="product-price-wrap">
							<span class="product-price"><?php echo wp_kses_post( $product->get_price_html() ); ?></span>
							<span class="product-rating">
								<i class="fas fa-star"></i>
								<?php echo esc_html( $product->get_average_rating() ); ?>
							</span>
						</div>
						<button type="button" class="btn btn-primary btn-full add-to-cart-btn" data-product-id="<?php echo esc_attr( $product->get_id() ); ?>">
							Add to Cart
						</button>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	else :
		?>
		<div class="shop-empty">
			<i class="fas fa-search"></i>
			<h3>No products found</h3>
			<p>Try adjusting your filters.</p>
		</div>
		<?php
	endif;

	// Pagination HTML.
	if ( $max_num_pages > 1 ) :
		?>
		<div class="shop-pagination" data-total-pages="<?php echo esc_attr( $max_num_pages ); ?>">
			<button type="button" class="load-more-btn btn btn-outline" data-page="1">
				Load More <i class="fas fa-arrow-down"></i>
			</button>
		</div>
		<?php
	endif;

	$html = ob_get_clean();

	return new WP_REST_Response(
		array(
			'html' => $html,
			'data' => array(
				'found_posts'   => $found_posts,
				'max_num_pages' => $max_num_pages,
			),
			'meta' => array(
				'page'       => $request->get_param( 'page' ),
				'per_page'   => $request->get_param( 'per_page' ),
				'total'      => $found_posts,
				'total_pages' => $max_num_pages,
			),
		),
		200
	);
}

// ─── Callback: Quick View ──────────────────────────────────────

function aether_rest_quick_view( $request ) {
	$product_id = $request->get_param( 'id' );
	$product    = wc_get_product( $product_id );

	if ( ! $product ) {
		return new WP_REST_Response(
			array( 'code' => 'product_not_found', 'message' => 'Product not found.' ),
			404
		);
	}

	$image_id  = $product->get_image_id();
	$image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'woocommerce_large_image' ) : wc_placeholder_img_src();

	$gallery_ids = $product->get_gallery_image_ids();
	$gallery     = array();
	foreach ( $gallery_ids as $id ) {
		$gallery[] = wp_get_attachment_image_url( $id, 'woocommerce_large_image' );
	}

	// Get attributes.
	$attributes = array();
	foreach ( $product->get_attributes() as $attribute ) {
		$attr_name  = wc_attribute_label( $attribute->get_name() );
		$attr_terms = array();
		if ( $attribute->is_taxonomy() ) {
			$terms = wp_get_post_terms( $product->get_id(), $attribute->get_name(), array( 'fields' => 'names' ) );
			if ( ! is_wp_error( $terms ) ) {
				$attr_terms = $terms;
			}
		} else {
			$attr_terms = $attribute->get_options();
		}
		$attributes[ strtolower( str_replace( ' ', '_', $attr_name ) ) ] = $attr_terms;
	}

	$html = '<div class="pd-modal">';
	$html .= '<div class="pd-modal-content">';
	$html .= '<button class="pd-modal-close" aria-label="Close">&times;</button>';
	$html .= '<div class="pd-gallery">';
	$html .= '<div class="pd-main-image"><img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $product->get_name() ) . '"></div>';
	if ( ! empty( $gallery ) ) {
		$html .= '<div class="pd-thumbs">';
		$html .= '<img src="' . esc_url( $image_url ) . '" alt="Thumbnail" class="pd-thumb active">';
		foreach ( $gallery as $g_url ) {
			$html .= '<img src="' . esc_url( $g_url ) . '" alt="Thumbnail" class="pd-thumb">';
		}
		$html .= '</div>';
	}
	$html .= '</div>';
	$html .= '<div class="pd-info">';
	$html .= '<h2 class="pd-title">' . esc_html( $product->get_name() ) . '</h2>';
	$html .= '<div class="pd-price">' . wp_kses_post( $product->get_price_html() ) . '</div>';
	$html .= '<div class="pd-rating">';
	for ( $i = 1; $i <= 5; $i++ ) {
		$html .= '<i class="fas fa-star' . ( $i <= round( $product->get_average_rating() ) ? '' : ' far' ) . '"></i>';
	}
	$html .= ' <span>(' . esc_html( $product->get_review_count() ) . ')</span></div>';
	$html .= '<div class="pd-short-desc">' . wp_kses_post( $product->get_short_description() ) . '</div>';
	$html .= '<a href="' . esc_url( $product->get_permalink() ) . '" class="btn btn-primary btn-full">View Full Details</a>';
	$html .= '</div>';
	$html .= '</div></div>';

	return new WP_REST_Response(
		array(
			'html' => $html,
			'data' => array(
				'id'         => $product->get_id(),
				'name'       => $product->get_name(),
				'price'      => wp_strip_all_tags( $product->get_price_html() ),
				'image'      => $image_url,
				'gallery'    => $gallery,
				'attributes' => $attributes,
			),
		),
		200
	);
}

// ─── Callback: Wishlist Toggle ─────────────────────────────────

function aether_rest_wishlist_toggle( $request ) {
	$product_id = $request->get_param( 'product_id' );
	$user_id    = get_current_user_id();
	$wishlist   = get_user_meta( $user_id, 'aether_wishlist', true );
	$wishlist   = is_array( $wishlist ) ? $wishlist : array();

	if ( in_array( $product_id, $wishlist, true ) ) {
		$wishlist = array_values( array_diff( $wishlist, array( $product_id ) ) );
		$action   = 'removed';
	} else {
		$wishlist[] = $product_id;
		$action     = 'added';
	}

	update_user_meta( $user_id, 'aether_wishlist', $wishlist );

	$product = wc_get_product( $product_id );
	$html    = '<i class="' . ( 'added' === $action ? 'fas' : 'far' ) . ' fa-heart"></i>';

	return new WP_REST_Response(
		array(
			'html' => $html,
			'data' => array(
				'action' => $action,
				'count'  => count( $wishlist ),
			),
		),
		200
	);
}

// ─── Callback: Wishlist Count ──────────────────────────────────

function aether_rest_wishlist_count() {
	$user_id  = get_current_user_id();
	$wishlist = get_user_meta( $user_id, 'aether_wishlist', true );
	$count    = is_array( $wishlist ) ? count( $wishlist ) : 0;

	return new WP_REST_Response(
		array(
			'data' => array(
				'count' => $count,
			),
		),
		200
	);
}

// ─── Callback: Mini Cart ───────────────────────────────────────

function aether_rest_mini_cart() {
	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		return new WP_REST_Response(
			array( 'html' => '', 'data' => array() ),
			200
		);
	}

	ob_start();
	?>
	<div class="mini-cart-content">
		<?php if ( WC()->cart->is_empty() ) : ?>
			<div class="mini-cart-empty">
				<i class="fas fa-shopping-bag"></i>
				<p>Your cart is empty</p>
				<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="btn btn-outline">Start Shopping</a>
			</div>
		<?php else : ?>
			<div class="mini-cart-items">
				<?php foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) : ?>
					<?php
					$product = $cart_item['data'];
					$image   = $product->get_image( 'woocommerce_thumbnail' );
					?>
					<div class="mini-cart-item" data-key="<?php echo esc_attr( $cart_item_key ); ?>">
						<div class="mini-cart-item-image"><?php echo wp_kses_post( $image ); ?></div>
						<div class="mini-cart-item-info">
							<a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="mini-cart-item-name"><?php echo esc_html( $product->get_name() ); ?></a>
							<span class="mini-cart-item-qty">x<?php echo esc_html( $cart_item['quantity'] ); ?></span>
							<span class="mini-cart-item-price"><?php echo wp_kses_post( WC()->cart->get_product_subtotal( $product, $cart_item['quantity'] ) ); ?></span>
						</div>
						<button class="mini-cart-item-remove" data-key="<?php echo esc_attr( $cart_item_key ); ?>" aria-label="Remove item">
							<i class="fas fa-times"></i>
						</button>
					</div>
				<?php endforeach; ?>
			</div>
			<div class="mini-cart-footer">
				<div class="mini-cart-subtotal">
					<span>Subtotal</span>
					<span class="mini-cart-total-amount"><?php echo wp_kses_post( WC()->cart->get_cart_subtotal() ); ?></span>
				</div>
				<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="btn btn-outline btn-full">View Cart</a>
				<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="btn btn-primary btn-full">Checkout</a>
			</div>
		<?php endif; ?>
	</div>
	<?php
	$html = ob_get_clean();

	return new WP_REST_Response(
		array(
			'html' => $html,
			'data' => array(
				'count'  => WC()->cart->get_cart_contents_count(),
				'total'  => wp_strip_all_tags( WC()->cart->get_cart_subtotal() ),
				'is_empty' => WC()->cart->is_empty(),
			),
		),
		200
	);
}

// ─── Callback: Cart Update ─────────────────────────────────────

function aether_rest_cart_update( $request ) {
	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		return new WP_REST_Response(
			array( 'code' => 'wc_not_available', 'message' => 'WooCommerce not available.' ),
			500
		);
	}

	$cart_item_key = $request->get_param( 'cart_item_key' );
	$quantity      = $request->get_param( 'quantity' );

	// Validate item exists in cart.
	if ( ! isset( WC()->cart->cart_contents[ $cart_item_key ] ) ) {
		return new WP_REST_Response(
			array( 'code' => 'invalid_item', 'message' => 'Cart item not found.' ),
			404
		);
	}

	$product = WC()->cart->cart_contents[ $cart_item_key ]['data'];

	if ( $quantity <= 0 ) {
		WC()->cart->remove_cart_item( $cart_item_key );
	} else {
		WC()->cart->set_quantity( $cart_item_key, $quantity );
	}

	WC()->cart->calculate_totals();

	return new WP_REST_Response(
		array(
			'html' => '',
			'data' => array(
				'item_total' => wp_strip_all_tags( WC()->cart->get_product_subtotal( $product, $quantity ) ),
				'cart_total' => wp_strip_all_tags( WC()->cart->get_cart_subtotal() ),
				'cart_count' => WC()->cart->get_cart_contents_count(),
			),
		),
		200
	);
}

// ─── Callback: Cart Remove ─────────────────────────────────────

function aether_rest_cart_remove( $request ) {
	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		return new WP_REST_Response(
			array( 'code' => 'wc_not_available', 'message' => 'WooCommerce not available.' ),
			500
		);
	}

	$cart_item_key = $request->get_param( 'cart_item_key' );

	if ( ! isset( WC()->cart->cart_contents[ $cart_item_key ] ) ) {
		return new WP_REST_Response(
			array( 'code' => 'invalid_item', 'message' => 'Cart item not found.' ),
			404
		);
	}

	WC()->cart->remove_cart_item( $cart_item_key );
	WC()->cart->calculate_totals();

	return new WP_REST_Response(
		array(
			'html' => '',
			'data' => array(
				'cart_total' => wp_strip_all_tags( WC()->cart->get_cart_subtotal() ),
				'cart_count' => WC()->cart->get_cart_contents_count(),
				'is_empty'   => WC()->cart->is_empty(),
			),
		),
		200
	);
}

// ─── Callback: Newsletter Subscribe ────────────────────────────

function aether_rest_newsletter_subscribe( $request ) {
	$email = $request->get_param( 'email' );

	if ( ! is_email( $email ) ) {
		return new WP_REST_Response(
			array( 'code' => 'invalid_email', 'message' => 'Please enter a valid email address.' ),
			400
		);
	}

	// Rate limit: 1 per IP per minute.
	$ip_key    = 'aether_newsletter_rate_' . md5( $_SERVER['REMOTE_ADDR'] ?? '' );
	$last_sub  = get_transient( $ip_key );
	if ( false !== $last_sub ) {
		return new WP_REST_Response(
			array( 'code' => 'rate_limited', 'message' => 'Please wait before subscribing again.' ),
			429
		);
	}

	// Check for duplicates.
	$existing = get_transient( 'aether_newsletter_' . md5( $email ) );
	if ( $existing ) {
		return new WP_REST_Response(
			array(
				'html'  => '<div class="newsletter-success"><i class="fas fa-check-circle"></i><p>Welcome back! You\'re already subscribed.</p></div>',
				'data'  => array( 'success' => true ),
			),
			200
		);
	}

	// Store subscription.
	set_transient(
		'aether_newsletter_' . md5( $email ),
		array(
			'email'     => $email,
			'timestamp' => current_time( 'timestamp' ),
		),
		YEAR_IN_SECONDS
	);

	set_transient( $ip_key, true, MINUTE_IN_SECONDS );

	// Store in option for admin visibility.
	$subscribers = get_option( 'aether_newsletter_subscribers', array() );
	if ( ! in_array( $email, $subscribers, true ) ) {
		$subscribers[] = $email;
		update_option( 'aether_newsletter_subscribers', $subscribers );
	}

	/**
	 * Fires after a successful newsletter subscription.
	 *
	 * @param string $email The subscriber email address.
	 */
	do_action( 'aether_newsletter_subscribed', $email );

	return new WP_REST_Response(
		array(
			'html' => '<div class="newsletter-success"><i class="fas fa-check-circle"></i><p>Welcome to the void. Check your inbox.</p></div>',
			'data' => array( 'success' => true ),
		),
		200
	);
}

// ─── Callback: Review Submit ───────────────────────────────────

function aether_rest_review_submit( $request ) {
	$product_id = $request->get_param( 'product_id' );
	$rating     = $request->get_param( 'rating' );
	$review     = $request->get_param( 'review' );
	$name       = $request->get_param( 'name' );

	$product = wc_get_product( $product_id );
	if ( ! $product ) {
		return new WP_REST_Response(
			array( 'code' => 'product_not_found', 'message' => 'Product not found.' ),
			404
		);
	}

	$comment_data = array(
		'comment_post_ID' => $product_id,
		'comment_author'  => ! empty( $name ) ? $name : wp_get_current_user()->display_name,
		'comment_content' => ! empty( $review ) ? $review : '',
		'comment_type'    => 'review',
		'comment_parent'  => 0,
		'user_id'         => get_current_user_id(),
	);

	$comment_id = wp_insert_comment( $comment_data );

	if ( $comment_id ) {
		// Set the rating.
		update_comment_meta( $comment_id, 'rating', $rating );

		// Set the verified buyer status.
		$order_ids = wc_get_orders(
			array(
				'customer_id' => get_current_user_id(),
				'status'      => array( 'wc-completed', 'wc-processing' ),
				'limit'       => -1,
				'return'      => 'ids',
			)
		);
		$bought = false;
		foreach ( $order_ids as $order_id ) {
			$order = wc_get_order( $order_id );
			if ( $order && in_array( $product_id, $order->get_item_ids(), true ) ) {
				$bought = true;
				break;
			}
		}
		update_comment_meta( $comment_id, 'verified', $bought );

		return new WP_REST_Response(
			array(
				'html' => '<div class="review-card" data-reveal><div class="review-header"><div class="review-avatar"><span class="review-avatar-initial">' . esc_html( mb_substr( $comment_data['comment_author'], 0, 1 ) ) . '</span></div><div class="review-author-info"><span class="review-author">' . esc_html( $comment_data['comment_author'] ) . '</span><span class="review-meta"><div class="review-stars">' . str_repeat( '<i class="fas fa-star"></i>', $rating ) . str_repeat( '<i class="far fa-star"></i>', 5 - $rating ) . '</div></span></div></div><div class="review-text">' . esc_html( $review ) . '</div></div>',
				'data' => array(
					'success' => true,
				),
			),
			200
		);
	}

	return new WP_REST_Response(
		array( 'code' => 'comment_failed', 'message' => 'Failed to submit review.' ),
		500
	);
}
