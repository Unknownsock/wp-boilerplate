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
		'edit.php?post_type=page',

		'separator1',

		'edit.php?post_type=portfolio',
		'edit.php?post_type=news',
		'edit.php',
		'edit.php?post_type=testimonials',
		'edit.php?post_type=team_member',

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

/**
 * Live-replaces a flexible content layout's title in the layout list with
 * its "Admin Label" field (field_68e00adm0001, cloned into every layout -
 * see group-5b9930d7812e5.json), so long pages full of same-type blocks
 * (e.g. several "Content Grid" rows) are easier to scan/reorder in
 * wp-admin. Falls back to the normal title when the label is cleared.
 * Editor-only - never touches the front end.
 */
function boilerplate_acf_admin_label_js() {
	?>
	<script type="text/javascript">
	(function($){
		function updateLayoutTitle($input) {
			var $handle = $input.closest('.layout').children('.acf-fc-layout-handle');
			if (!$handle.length) {
				return;
			}

			// Cache the layout's real title (e.g. "1. Content Grid") the
			// first time we touch it, so clearing the label can restore it -
			// otherwise the original text is lost after the first override.
			if ($handle.data('boilerplateOriginalTitle') === undefined) {
				$handle.data('boilerplateOriginalTitle', $handle.text().trim());
			}

			var label = $.trim($input.val());
			// The handle's own title lives in a bare text node alongside the
			// row-order/collapse controls (which are real elements) - only
			// that text node should change, so the controls stay intact.
			var textNode = $handle.contents().filter(function () {
				return this.nodeType === 3 && $.trim(this.nodeValue) !== '';
			}).first();

			if (textNode.length) {
				textNode[0].nodeValue = label || $handle.data('boilerplateOriginalTitle');
			}
		}

		function initLayoutTitles($scope) {
			$scope.find('.acf-flexible-content [data-name="admin_label"] input[type="text"]').each(function () {
				updateLayoutTitle($(this));
			});
		}

		$(document).on('input', '.acf-flexible-content [data-name="admin_label"] input[type="text"]', function () {
			updateLayoutTitle($(this));
		});

		if (window.acf && acf.addAction) {
			// Covers rows already on the page (ready) and rows added via
			// "Add Layout"/duplicate afterwards (append).
			acf.addAction('ready append', function ($el) {
				initLayoutTitles($el);
			});
		} else {
			$(document).ready(function () {
				initLayoutTitles($('body'));
			});
		}
	})(jQuery);
	</script>
	<?php
}
add_action( 'acf/input/admin_head', 'boilerplate_acf_admin_label_js' );
