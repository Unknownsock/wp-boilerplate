<?php
/**
 * Registers the custom Category taxonomy.
 *
 * @package Boilerplate
 */

/**
 * Registers a custom "category" taxonomy for the projects and team post types.
 *
 * @return void
 */
function taxonomy_custom_category() {
	$labels = array(
		'name'                       => _x( 'Categories', 'taxonomy general name' ),
		'singular_name'              => _x( 'Category', 'taxonomy singular name' ),
		'search_items'               => __( 'Search Categories' ),
		'popular_items'              => __( 'Popular Categories' ),
		'all_items'                  => __( 'All Category' ),
		'parent_item'                => __( 'Parent Status' ),
		'parent_item_colon'          => __( 'Parent Status:' ),
		'edit_item'                  => __( 'Edit Category' ),
		'update_item'                => __( 'Update Category' ),
		'add_new_item'               => __( 'Add New Category' ),
		'new_item_name'              => __( 'New Category Name' ),
		'separate_items_with_commas' => __( 'Separate Categories with commas' ),
		'add_or_remove_items'        => __( 'Add or remove Category' ),
		'choose_from_most_used'      => __( 'Choose from the most used Categories' ),
		'menu_name'                  => __( 'Category' ),
	);
	$args   = array(
		'labels'            => $labels,
		'hierarchical'      => true,
		'public'            => true,
		'show_ui'           => true,
		'show_admin_column' => true,
		'show_in_nav_menus' => true,
		'show_tagcloud'     => true,
	);
	register_taxonomy( 'category', array( 'projects', 'team' ), $args );
}
	add_action( 'init', 'taxonomy_custom_category', 12 );
