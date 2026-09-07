<?php
/**
 * Registers the Clients custom post type.
 *
 * @package Boilerplate
 */

/**
 * Registers the Clients post type.
 *
 * @return void
 */
function create_team_posttype() {
	register_post_type(
		'clients',
		array(
			'labels'             => array(
				'name'          => __( 'Our work' ),
				'singular_name' => __( 'Client' ),
			),
			'public'             => true,
			'has_archive'        => false,
			'publicly_queryable' => true,
			'rewrite'            => array(
				'slug'       => 'clients',
				'with_front' => false,
			),
			'taxonomies'         => array( 'clients-cat' ),
			'menu_position'      => 6,
			'supports'           => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
		)
	);
}
	add_action( 'init', 'create_team_posttype' );
