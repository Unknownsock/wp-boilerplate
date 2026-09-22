<?php
/**
 * Registers the Team Member custom post type.
 *
 * @package Boilerplate
 */

	defined( 'ABSPATH' ) || exit;

/**
 * Registers the Team Member post type.
 *
 * Individual people (photo via featured image, role, bio via the normal
 * editor) picked into the homepage "Team" block via a relationship field -
 * see acf-json/group-5b9930d7812e5.json (team layout) and
 * template-parts/content/blocks/team/.
 *
 * @return void
 */
function create_team_member_posttype() {
	register_post_type(
		'team_member',
		array(
			'labels'             => array(
				'name'          => __( 'Team' ),
				'singular_name' => __( 'Team Member' ),
			),
			'public'             => true,
			'has_archive'        => false,
			'publicly_queryable' => true,
			'show_in_rest'       => true,
			'menu_icon'          => 'dashicons-groups',
			'rewrite'            => array(
				'slug'       => 'team',
				'with_front' => false,
			),
			'menu_position'      => 6,
			'supports'           => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
		)
	);
}
	add_action( 'init', 'create_team_member_posttype' );
