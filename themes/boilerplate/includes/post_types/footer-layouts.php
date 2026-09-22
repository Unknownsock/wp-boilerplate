<?php
/**
 * Registers the Footer Layouts custom post type.
 *
 * @package Boilerplate
 */

	defined( 'ABSPATH' ) || exit;

/**
 * Registers the Footer Layouts post type.
 *
 * @return void
 */
function create_footer_posttype() {
	register_post_type(
		'footer-layouts',
		array(
			'labels'             => array(
				'name'          => __( 'Footer Layouts' ),
				'singular_name' => __( 'Footer Layout' ),
			),
			'public'             => true,
			'has_archive'        => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'menu_icon'          => 'dashicons-layout',
		)
	);
}
	add_action( 'init', 'create_footer_posttype' );
