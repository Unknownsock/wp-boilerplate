<?php
/**
 * Registers the News custom post type.
 *
 * @package Boilerplate
 */

	defined( 'ABSPATH' ) || exit;

/**
 * Registers the News post type.
 *
 * Same shape as the built-in "post" type (title/editor/thumbnail/excerpt)
 * so it can be queried and rendered anywhere Posts already are - the
 * `listing` block's "Post Type" field (field_6227d1fc615c5) includes it as
 * a choice for exactly that reason.
 *
 * @return void
 */
function create_news_posttype() {
	register_post_type(
		'news',
		array(
			'labels'             => array(
				'name'          => __( 'News' ),
				'singular_name' => __( 'News Article' ),
			),
			'public'             => true,
			'has_archive'        => true,
			'publicly_queryable' => true,
			'show_in_rest'       => true,
			'menu_icon'          => 'dashicons-megaphone',
			'rewrite'            => array(
				'slug'       => 'news',
				'with_front' => false,
			),
			'menu_position'      => 6,
			'supports'           => array( 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields' ),
			'taxonomies'         => array( 'post_tag' ),
		)
	);
}
	add_action( 'init', 'create_news_posttype' );
