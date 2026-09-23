<?php
/**
 * Registers the Portfolio custom post type.
 *
 * @package Boilerplate
 */

/**
 * Registers the Portfolio post type.
 *
 * @return void
 */
function create_portfolio_posttype() {
	register_post_type(
		'portfolio',
		array(
			'labels'             => array(
				'name'          => __( 'Portfolio' ),
				'singular_name' => __( 'Portfolio Item' ),
			),
			'public'             => true,
			'has_archive'        => false,
			'publicly_queryable' => true,
			'rewrite'            => array(
				'slug'       => 'portfolio',
				'with_front' => false,
			),
			'taxonomies'         => array( 'portfolio-cat' ),
			'menu_position'      => 6,
			'supports'           => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
		)
	);
}
	add_action( 'init', 'create_portfolio_posttype' );
