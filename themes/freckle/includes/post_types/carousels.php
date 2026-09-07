<?php
/**
 * Registers the Carousels custom post type.
 *
 * @package Boilerplate
 */

	defined( 'ABSPATH' ) || exit;

/**
 * Registers the Carousels post type.
 *
 * @return void
 */
function create_logos_posttype() {
	register_post_type(
		'carousels',
		array(
			'labels'        => array(
				'name'          => __( 'Carousels' ),
				'singular_name' => __( 'Carousel' ),
			),
			'public'        => true,
			'has_archive'   => false,
			'rewrite'       => array(
				'slug'       => 'carousel',
				'with_front' => false,
			),
			'menu_position' => 7,
		)
	);
}
	add_action( 'init', 'create_logos_posttype' );
