<?php
/**
 * Registers the Testimonials custom post type.
 *
 * @package Boilerplate
 */

/**
 * Registers the Testimonials post type.
 *
 * @return void
 */
function create_testimonial_posttype() {
	register_post_type(
		'testimonials',
		array(
			'labels'      => array(
				'name'          => __( 'Testimonials' ),
				'singular_name' => __( 'Testimonial' ),
			),
			'public'      => true,
			'has_archive' => false,
			'show_ui'     => true,
			'rewrite'     => array( 'slug' => 'testimonials' ),
			// 'supports' => array('title', 'editor', 'thumbnail', 'comments', 'author'),
		)
	);
}
	add_action( 'init', 'create_testimonial_posttype' );
