<?php
/**
 * Post meta elements.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! function_exists( 'aureon_content_nav' ) ) {
	/**
	 * Display navigation to next/previous pages when applicable.
	 *
	 * @since 0.1
	 *
	 * @param string $nav_id The id of our navigation.
	 */
	function aureon_content_nav( $nav_id ) {
		global $wp_query, $post;

		// Don't print empty markup on single pages if there's nowhere to navigate.
		if ( is_single() ) {
			$previous = ( is_attachment() ) ? get_post( $post->post_parent ) : get_adjacent_post( false, '', true );
			$next = get_adjacent_post( false, '', false );

			if ( ! $next && ! $previous ) {
				return;
			}
		}

		// Don't print empty markup in archives if there's only one page.
		if ( $wp_query->max_num_pages < 2 && ( is_home() || is_archive() || is_search() ) ) {
			return;
		}
		?>
		<nav <?php aureon_do_attr( 'post-navigation', array( 'id' => esc_attr( $nav_id ) ) ); ?>>
			<?php
			if ( is_single() ) : // navigation links for single posts.

				$post_navigation_args = apply_filters(
					'aureon_post_navigation_args',
					array(
						'previous_format' => '<div class="nav-previous">' . aureon_get_svg_icon( 'arrow-left' ) . '<span class="prev">%link</span></div>',
						'next_format' => '<div class="nav-next">' . aureon_get_svg_icon( 'arrow-right' ) . '<span class="next">%link</span></div>',
						'link' => '%title',
						'in_same_term' => apply_filters( 'aureon_category_post_navigation', false ),
						'excluded_terms' => '',
						'taxonomy' => 'category',
					)
				);

				previous_post_link(
					$post_navigation_args['previous_format'],
					$post_navigation_args['link'],
					$post_navigation_args['in_same_term'],
					$post_navigation_args['excluded_terms'],
					$post_navigation_args['taxonomy']
				);

				next_post_link(
					$post_navigation_args['next_format'],
					$post_navigation_args['link'],
					$post_navigation_args['in_same_term'],
					$post_navigation_args['excluded_terms'],
					$post_navigation_args['taxonomy']
				);

			elseif ( is_home() || is_archive() || is_search() ) : // navigation links for home, archive, and search pages.

				if ( get_next_posts_link() ) :
					?>
					<div class="nav-previous">
						<?php aureon_do_svg_icon( 'arrow' ); ?>
						<span class="prev" title="<?php esc_attr_e( 'Previous', 'aureon' ); ?>"><?php next_posts_link( __( 'Older posts', 'aureon' ) ); ?></span>
					</div>
					<?php
				endif;

				if ( get_previous_posts_link() ) :
					?>
					<div class="nav-next">
						<?php aureon_do_svg_icon( 'arrow' ); ?>
						<span class="next" title="<?php esc_attr_e( 'Next', 'aureon' ); ?>"><?php previous_posts_link( __( 'Newer posts', 'aureon' ) ); ?></span>
					</div>
					<?php
				endif;

				if ( function_exists( 'the_posts_pagination' ) ) {
					the_posts_pagination(
						array(
							'mid_size' => apply_filters( 'aureon_pagination_mid_size', 1 ),
							'prev_text' => apply_filters(
								'aureon_previous_link_text',
								sprintf(
									/* translators: left arrow */
									__( '%s Previous', 'aureon' ),
									'<span aria-hidden="true">&larr;</span>'
								)
							),
							'next_text' => apply_filters(
								'aureon_next_link_text',
								sprintf(
									/* translators: right arrow */
									__( 'Next %s', 'aureon' ),
									'<span aria-hidden="true">&rarr;</span>'
								)
							),
							'before_page_number' => sprintf(
								'<span class="screen-reader-text">%s</span>',
								_x( 'Page', 'prepends the pagination page number for screen readers', 'aureon' )
							),
						)
					);
				}

				/**
				 * aureon_paging_navigation hook.
				 *
				 * @since 0.1
				 */
				do_action( 'aureon_paging_navigation' );

			endif;
			?>
		</nav>
		<?php
	}
}

if ( ! function_exists( 'aureon_modify_posts_pagination_template' ) ) {
	add_filter( 'navigation_markup_template', 'aureon_modify_posts_pagination_template', 10, 2 );
	/**
	 * Remove the container and screen reader text from the_posts_pagination()
	 * We add this in ourselves in aureon_content_nav()
	 *
	 * @since 1.3.45
	 *
	 * @param string $template The default template.
	 * @param string $class The class passed by the calling function.
	 * @return string The HTML for the post navigation.
	 */
	function aureon_modify_posts_pagination_template( $template, $class ) {
		if ( ! empty( $class ) && false !== strpos( $class, 'pagination' ) ) {
			$template = '<div class="nav-links">%3$s</div>';
		}

		return $template;
	}
}

/**
 * Output requested post meta.
 *
 * @since 2.3
 *
 * @param string $item The post meta item we're requesting.
 */
function aureon_do_post_meta_item( $item ) {
	if ( 'date' === $item ) {
		$time_string = '<time class="entry-date published" datetime="%1$s"%5$s>%2$s</time>';

		$updated_time = get_the_modified_time( 'U' );
		$published_time = get_the_time( 'U' ) + 1800;
		$schema_type = aureon_get_schema_type();

		if ( $updated_time > $published_time ) {
			if ( apply_filters( 'aureon_post_date_show_updated_only', false ) ) {
				$time_string = '<time class="entry-date updated-date" datetime="%3$s"%6$s>%4$s</time>';
			} else {
				$time_string = '<time class="updated" datetime="%3$s"%6$s>%4$s</time>' . $time_string;
			}
		}

		$time_string = sprintf(
			$time_string,
			esc_attr( get_the_date( 'c' ) ),
			esc_html( get_the_date() ),
			esc_attr( get_the_modified_date( 'c' ) ),
			esc_html( get_the_modified_date() ),
			'microdata' === $schema_type ? ' itemprop="datePublished"' : '',
			'microdata' === $schema_type ? ' itemprop="dateModified"' : ''
		);

		$posted_on = '<span class="posted-on">%1$s%4$s</span> ';

		if ( apply_filters( 'aureon_post_date_link', false ) ) {
			$posted_on = '<span class="posted-on">%1$s<a href="%2$s" title="%3$s" rel="bookmark">%4$s</a></span> ';
		}

		echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'aureon_post_date_output',
			sprintf(
				$posted_on,
				apply_filters( 'aureon_inside_post_meta_item_output', '', 'date' ),
				esc_url( get_permalink() ),
				esc_attr( get_the_time() ),
				$time_string
			),
			$time_string,
			$posted_on
		);
	}

	if ( 'author' === $item ) {
		$schema_type = aureon_get_schema_type();

		$byline = '<span class="byline">%1$s<span class="author%8$s" %5$s><a class="url fn n" href="%2$s" title="%3$s" rel="author"%6$s><span class="author-name"%7$s>%4$s</span></a></span></span> ';

		if ( ! apply_filters( 'aureon_post_author_link', true ) ) {
			$byline = '<span class="byline">%1$s<span class="author%8$s" %5$s><span class="author-name"%7$s>%4$s</span></span></span> ';
		}

		echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'aureon_post_author_output',
			sprintf(
				$byline,
				apply_filters( 'aureon_inside_post_meta_item_output', '', 'author' ),
				esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
				/* translators: 1: Author name */
				esc_attr( sprintf( __( 'View all posts by %s', 'aureon' ), get_the_author() ) ),
				esc_html( get_the_author() ),
				aureon_get_microdata( 'post-author' ),
				'microdata' === $schema_type ? ' itemprop="url"' : '',
				'microdata' === $schema_type ? ' itemprop="name"' : '',
				aureon_is_using_hatom() ? ' vcard' : ''
			)
		);
	}

	if ( 'categories' === $item ) {
		$term_separator = apply_filters( 'aureon_term_separator', _x( ', ', 'Used between list items, there is a space after the comma.', 'aureon' ), 'categories' );
		$categories_list = get_the_category_list( $term_separator );

		if ( $categories_list ) {
			echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				'aureon_category_list_output',
				sprintf(
					'<span class="cat-links">%3$s<span class="screen-reader-text">%1$s </span>%2$s</span> ',
					esc_html_x( 'Categories', 'Used before category names.', 'aureon' ),
					$categories_list,
					apply_filters( 'aureon_inside_post_meta_item_output', '', 'categories' )
				)
			);
		}
	}

	if ( 'tags' === $item ) {
		$term_separator = apply_filters( 'aureon_term_separator', _x( ', ', 'Used between list items, there is a space after the comma.', 'aureon' ), 'tags' );
		$tags_list = get_the_tag_list( '', $term_separator );

		if ( $tags_list ) {
			echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				'aureon_tag_list_output',
				sprintf(
					'<span class="tags-links">%3$s<span class="screen-reader-text">%1$s </span>%2$s</span> ',
					esc_html_x( 'Tags', 'Used before tag names.', 'aureon' ),
					$tags_list,
					apply_filters( 'aureon_inside_post_meta_item_output', '', 'tags' )
				)
			);
		}
	}

	if ( 'comments-link' === $item ) {
		if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
			echo '<span class="comments-link">';
				echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					'aureon_inside_post_meta_item_output',
					'',
					'comments-link'
				);
				comments_popup_link( __( 'Leave a comment', 'aureon' ), __( '1 Comment', 'aureon' ), __( '% Comments', 'aureon' ) );
			echo '</span> ';
		}
	}

	if ( 'post-navigation' === $item && is_single() ) {
		aureon_content_nav( 'nav-below' );
	}

	/**
	 * aureon_post_meta_items hook.
	 *
	 * @since 2.4
	 */
	do_action( 'aureon_post_meta_items', $item );
}

add_filter( 'aureon_inside_post_meta_item_output', 'aureon_do_post_meta_prefix', 10, 2 );
/**
 * Add svg icons or text to our post meta output.
 *
 * @since 2.4
 * @param string $output The existing output.
 * @param string $item The item to target.
 */
function aureon_do_post_meta_prefix( $output, $item ) {
	if ( 'author' === $item ) {
		$output = __( 'by', 'aureon' ) . ' ';
	}

	if ( 'categories' === $item ) {
		$output = aureon_get_svg_icon( 'categories' );
	}

	if ( 'tags' === $item ) {
		$output = aureon_get_svg_icon( 'tags' );
	}

	if ( 'comments-link' === $item ) {
		$output = aureon_get_svg_icon( 'comments' );
	}

	return $output;
}

/**
 * Remove post meta items from display if their individual filters are set.
 *
 * @since 3.0.0
 * @param array $items The post meta items.
 */
function aureon_disable_post_meta_items( $items ) {
	$disable_filter_names = apply_filters(
		'aureon_disable_post_meta_filter_names',
		array(
			'date' => 'aureon_post_date',
			'author' => 'aureon_post_author',
			'categories' => 'aureon_show_categories',
			'tags' => 'aureon_show_tags',
			'comments-link' => 'aureon_show_comments',
			'post-navigation' => 'aureon_show_post_navigation',
		)
	);

	foreach ( $items as $item ) {
		$default_display = true;

		if ( 'comments-link' === $item && is_singular() ) {
			$default_display = false;
		}

		// phpcs:ignore -- Hook name is coming from a variable.
		if ( isset( $disable_filter_names[ $item ] ) && ! apply_filters( $disable_filter_names[ $item ], $default_display ) ) {
			$items = array_diff( $items, array( $item ) );
		}
	}

	return $items;
}

/**
 * Get the post meta items in the header entry meta.
 *
 * @since 3.0.0
 */
function aureon_get_header_entry_meta_items() {
	$items = apply_filters(
		'aureon_header_entry_meta_items',
		array(
			'date',
			'author',
		)
	);

	// Disable post meta items based on their individual filters.
	$items = aureon_disable_post_meta_items( $items );

	return $items;
}

/**
 * Get the post meta items in the footer entry meta.
 *
 * @since 3.0.0
 */
function aureon_get_footer_entry_meta_items() {
	$items = apply_filters(
		'aureon_footer_entry_meta_items',
		array(
			'categories',
			'tags',
			'comments-link',
			'post-navigation',
		)
	);

	/**
	 * This wasn't a "meta item" prior to 3.0.0 and some users may be using the filter above
	 * without specifying that they want to include post-navigation. The below forces it to display
	 * for users using the old float system to prevent it from disappearing on update.
	 */
	if ( ! aureon_is_using_flexbox() && ! in_array( 'post-navigation', (array) $items ) ) {
		$items[] = 'post-navigation';
	}

	if ( ! is_singular() ) {
		$items = array_diff( (array) $items, array( 'post-navigation' ) );
	}

	// Disable post meta items based on their individual filters.
	$items = aureon_disable_post_meta_items( $items );

	return $items;
}

if ( ! function_exists( 'aureon_posted_on' ) ) {
	/**
	 * Prints HTML with meta information for the current post-date/time and author.
	 *
	 * @since 0.1
	 */
	function aureon_posted_on() {
		$items = aureon_get_header_entry_meta_items();

		foreach ( $items as $item ) {
			aureon_do_post_meta_item( $item );
		}
	}
}

if ( ! function_exists( 'aureon_entry_meta' ) ) {
	/**
	 * Prints HTML with meta information for the categories, tags.
	 *
	 * @since 1.2.5
	 */
	function aureon_entry_meta() {
		$items = aureon_get_footer_entry_meta_items();

		foreach ( $items as $item ) {
			aureon_do_post_meta_item( $item );
		}
	}
}

if ( ! function_exists( 'aureon_excerpt_more' ) ) {
	add_filter( 'excerpt_more', 'aureon_excerpt_more' );
	/**
	 * Prints the read more HTML to post excerpts.
	 *
	 * @since 0.1
	 *
	 * @param string $more The string shown within the more link.
	 * @return string The HTML for the more link.
	 */
	function aureon_excerpt_more( $more ) {
		return apply_filters(
			'aureon_excerpt_more_output',
			sprintf(
				' ... <a title="%1$s" class="read-more" href="%2$s" aria-label="%4$s">%3$s</a>',
				the_title_attribute( 'echo=0' ),
				esc_url( get_permalink( get_the_ID() ) ),
				aureon_get_read_more_text(),
				aureon_get_read_more_aria_label()
			)
		);
	}
}

if ( ! function_exists( 'aureon_content_more' ) ) {
	add_filter( 'the_content_more_link', 'aureon_content_more' );
	/**
	 * Prints the read more HTML to post content using the more tag.
	 *
	 * @since 0.1
	 *
	 * @param string $more The string shown within the more link.
	 * @return string The HTML for the more link
	 */
	function aureon_content_more( $more ) {
		return apply_filters(
			'aureon_content_more_link_output',
			sprintf(
				'<p class="read-more-container"><a title="%1$s" class="read-more content-read-more" href="%2$s" aria-label="%4$s">%3$s</a></p>',
				the_title_attribute( 'echo=0' ),
				esc_url( get_permalink( get_the_ID() ) . apply_filters( 'aureon_more_jump', '#more-' . get_the_ID() ) ),
				aureon_get_read_more_text(),
				aureon_get_read_more_aria_label()
			)
		);
	}
}

add_action( 'wp', 'aureon_add_post_meta', 5 );
/**
 * Add our post meta items to the page.
 *
 * @since 3.0.0
 */
function aureon_add_post_meta() {
	$header_items = aureon_get_header_entry_meta_items();

	$header_post_types = apply_filters(
		'aureon_entry_meta_post_types',
		array(
			'post',
		)
	);

	if ( in_array( get_post_type(), $header_post_types ) && ! empty( $header_items ) ) {
		add_action( 'aureon_after_entry_title', 'aureon_post_meta' );
	}

	$footer_items = aureon_get_footer_entry_meta_items();

	$footer_post_types = apply_filters(
		'aureon_footer_meta_post_types',
		array(
			'post',
		)
	);

	if ( in_array( get_post_type(), $footer_post_types ) && ! empty( $footer_items ) ) {
		add_action( 'aureon_after_entry_content', 'aureon_footer_meta' );
	}
}

if ( ! function_exists( 'aureon_post_meta' ) ) {
	/**
	 * Build the post meta.
	 *
	 * @since 1.3.29
	 */
	function aureon_post_meta() {
		?>
		<div class="entry-meta">
			<?php aureon_posted_on(); ?>
		</div>
		<?php
	}
}

if ( ! function_exists( 'aureon_footer_meta' ) ) {
	/**
	 * Build the footer post meta.
	 *
	 * @since 1.3.30
	 */
	function aureon_footer_meta() {
		?>
		<footer <?php aureon_do_attr( 'footer-entry-meta' ); ?>>
			<?php aureon_entry_meta(); ?>
		</footer>
		<?php
	}
}

add_action( 'aureon_after_loop', 'aureon_do_post_navigation' );
/**
 * Add our post navigation after post loops.
 *
 * @since 3.0.0
 * @param string $template The template of the current action.
 */
function aureon_do_post_navigation( $template ) {
	$templates = apply_filters(
		'aureon_post_navigation_templates',
		array(
			'index',
			'archive',
			'search',
		)
	);

	if ( in_array( $template, $templates ) && apply_filters( 'aureon_show_post_navigation', true ) ) {
		aureon_content_nav( 'nav-below' );
	}
}

/**
 * Returns the read more text for our posts.
 *
 * @since 3.4.0
 */
function aureon_get_read_more_text() {
	return apply_filters(
		'aureon_excerpt_more_text',
		__( 'Read more', 'aureon' )
	);
}

/**
 * Returns the read more `aria-label` for our posts.
 *
 * @since 3.4.0
 */
function aureon_get_read_more_aria_label() {
	return apply_filters(
		'aureon_excerpt_more_aria_label',
		sprintf(
			/* translators: Aria-label describing the read more button */
			_x( 'Read more about %s', 'read more about post title', 'aureon' ),
			the_title_attribute( 'echo=0' )
		)
	);
}
