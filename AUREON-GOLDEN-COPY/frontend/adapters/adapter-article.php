<?php
/**
 * Article adapter — single post data for content/article-* components.
 *
 * @package Aureon
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function aether_adapter_article( $args = array() ) {
	$post_id = isset( $args['post_id'] ) ? absint( $args['post_id'] ) : 0;
	if ( ! $post_id ) {
		$post_id = get_queried_object_id();
	}

	$post = get_post( $post_id );
	if ( ! $post || 'publish' !== $post->post_status ) {
		return array();
	}

	$categories = get_the_category( $post_id );
	$category   = ! empty( $categories ) ? $categories[0]->name : '';

	$content   = apply_filters( 'the_content', $post->post_content );
	$word_count = str_word_count( wp_strip_all_tags( $content ) );
	$read_time  = (int) max( 1, ceil( $word_count / 200 ) );

	$author_id  = $post->post_author;
	$author     = get_the_author_meta( 'display_name', $author_id );

	return array(
		'category'    => $category,
		'title'       => get_the_title( $post_id ),
		'image'       => get_the_post_thumbnail_url( $post_id, 'full' ),
		'alt'         => get_the_title( $post_id ),
		'author'      => $author,
		'author_bio'  => get_the_author_meta( 'description', $author_id ),
		'date'        => get_the_date( '', $post_id ),
		'read_time'   => $read_time,
		'content'     => $content,
		'avatar'      => get_avatar_url( $author_id, array( 'size' => 96 ) ),
		'behavior'    => array( 'reveal' => true ),
	);
}