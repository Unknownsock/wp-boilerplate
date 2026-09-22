<?php
/**
 * WordPress admin customisations for the theme.
 *
 * @package Boilerplate
 */

defined( 'ABSPATH' ) || exit;

/**
 * Reorder and filter the admin menu items shown in the dashboard.
 *
 * @param array $__return_true Unused. Menu order filters are passed a boolean, not an array.
 * @return array The admin menu order.
 */
function reorder_admin_menu( $__return_true ) {
	return array(
		'index.php',
		'edit.php?post_type=portfolio',
		'edit.php?post_type=page',

		'separator1',

		'edit.php?post_type=testimonials',

		'separator2',

		'edit.php?post_type=footer-layouts',

		'separator-last',

		'wpcf7',
		'acf-options',
		'upload.php',
		'themes.php',

		// 'separator-last',

		'users.php',
		'plugins.php',
		'tools.php',
		'options-general.php',
	);
}
add_filter( 'custom_menu_order', 'reorder_admin_menu' );
add_filter( 'menu_order', 'reorder_admin_menu' );

add_filter( 'show_admin_bar', '__return_false' ); // Remove Admin bar.

add_action(
	'admin_init',
	function () {
		// Redirect any user trying to access comments page.
		global $pagenow;

		if ( 'edit-comments.php' === $pagenow ) {
			wp_redirect( admin_url() );
			exit;
		}

		// Remove comments metabox from dashboard.
		remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );

		// Disable support for comments and trackbacks in post types.
		foreach ( get_post_types() as $post_type ) {
			if ( post_type_supports( $post_type, 'comments' ) ) {
				remove_post_type_support( $post_type, 'comments' );
				remove_post_type_support( $post_type, 'trackbacks' );
			}
		}
	}
);

// Close comments on the front-end.
add_filter( 'comments_open', '__return_false', 20, 2 );
add_filter( 'pings_open', '__return_false', 20, 2 );

// Hide existing comments.
add_filter( 'comments_array', '__return_empty_array', 10, 2 );

// Remove comments page in menu.
add_action(
	'admin_menu',
	function () {
		remove_menu_page( 'edit-comments.php' );
	}
);

// Disable the comments REST API endpoint entirely - the above already
// closes/hides comments everywhere in wp-admin and the front-end, this
// closes the last remaining door (a POST to /wp-json/wp/v2/comments would
// otherwise still succeed).
add_filter(
	'rest_endpoints',
	function ( $endpoints ) {
		unset( $endpoints['/wp/v2/comments'] );
		unset( $endpoints['/wp/v2/comments/(?P<id>[\d]+)'] );
		return $endpoints;
	}
);

// Posts isn't used on this site (Testimonials/Our Work/etc. are their own
// CPTs) - remove the menu item entirely rather than leaving an unused,
// empty "Posts" section for editors to stumble into.
add_action(
	'admin_menu',
	function () {
		remove_menu_page( 'edit.php' );
	},
	999
);

// Remove comments links from admin bar.
add_action(
	'init',
	function () {
		if ( is_admin_bar_showing() ) {
			remove_action( 'admin_bar_menu', 'wp_admin_bar_comments_menu', 60 );
		}
	}
);

/**
 * Collapse all ACF flexible content layouts on page load.
 */
function my_acf_admin_head() {
	?>
	<script type="text/javascript">
	(function($){
		$(document).ready(function(){
			$( ".layout" ).each(function( index ) {
					$( this ).addClass('-collapsed');
			});
		});
	})(jQuery);
	</script>
	<?php
}

add_action( 'acf/input/admin_head', 'my_acf_admin_head' );
