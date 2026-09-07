<?php
/**
 * Theme setup: theme supports, nav menus and general WordPress behaviour.
 *
 * @package Boilerplate
 */

defined( 'ABSPATH' ) || exit;

add_theme_support( 'title-tag' );

/**
 * Send the clickjacking-protection header. Used to be a plain `header()`
 * call inline in header.php, after `<!doctype html>` and other output had
 * already been echoed - PHP can't send headers once body output has
 * started, so it silently never went out. send_headers fires before any
 * theme template output begins.
 *
 * @return void
 */
function freckle_send_frame_options_header() {
	header( 'X-Frame-Options: DENY' );
}
add_action( 'send_headers', 'freckle_send_frame_options_header' );

/**
 * Register the theme's navigation menu locations.
 *
 * @return void
 */
function register_menu() {
	register_nav_menus(
		array(
			'menu-1' => 'Main Menu',
			'menu-2' => 'Footer Menu 1',
			'menu-3' => 'Footer Menu 2',
			'menu-4' => 'Footer Menu 3',
		)
	);
}
add_action( 'init', 'register_menu' );

/**
 * Suppress the core update notice. Currently unused; not hooked to any filter.
 *
 * @return object Fake update-check response with no updates available.
 */
function hide_updates() {
	global $wp_version;
	return (object) array(
		'last_checked'    => time(),
		'version_checked' => $wp_version,
	);
}

// Remove p tag auto insertion.
remove_filter( 'the_excerpt', 'wpautop' );
add_filter( 'widget_text', 'shortcode_unautop' ); // Remove <p> tags in Dynamic Sidebars (better!).
add_filter( 'the_excerpt', 'shortcode_unautop' ); // Remove auto <p> tags in Excerpt (Manual Excerpts only).

// Remove annoying paragraphs from CF7.
add_filter( 'wpcf7_autop_or_not', '__return_false' );

// Remove cf7 element wrap.
add_filter(
	'wpcf7_form_elements',
	function ( $content ) {
		$content = preg_replace( '/<(span).*?class="\s*(?:.*\s)?wpcf7-form-control-wrap(?:\s[^"]+)?\s*"[^\>]*>(.*)<\/\1>/i', '\2', $content );
		return $content;
	}
);

/* ACF Options*/
if ( function_exists( 'acf_add_options_page' ) ) {
	acf_add_options_page();
}

add_action( 'wp_enqueue_scripts', 'no_more_jquery' );
/**
 * Deregister jQuery, since it slows down page load and isn't needed.
 *
 * @return void
 */
function no_more_jquery() {
	wp_deregister_script( 'jquery' );
}
/**
 * Dequeue the Gutenberg block library CSS, since it slows down page load and isn't needed.
 *
 * @return void
 */
function remove_block_css() {
	wp_dequeue_style( 'wp-block-library' );
}
add_action( 'wp_enqueue_scripts', 'remove_block_css', 100 );

// Replace all non ASCII chars from media names.
// https://wpartisan.me/tutorials/rename-clean-wordpress-media-filenames.
add_filter(
	'sanitize_file_name',
	function ( $filename ) {
		$sanitized_filename = remove_accents( $filename ); // Convert to ASCII.

		// Standard replacements.
		$invalid            = array(
			' '   => '-',
			'%20' => '-',
			'_'   => '-',
		);
		$sanitized_filename = str_replace( array_keys( $invalid ), array_values( $invalid ), $sanitized_filename );

		$sanitized_filename = preg_replace( '/[^A-Za-z0-9-\. ]/', '', $sanitized_filename ); // Remove all non-alphanumeric except .
		$sanitized_filename = preg_replace( '/\.(?=.*\.)/', '', $sanitized_filename ); // Remove all but last .
		$sanitized_filename = preg_replace( '/-+/', '-', $sanitized_filename ); // Replace any more than one - in a row.
		$sanitized_filename = str_replace( '-.', '.', $sanitized_filename ); // Remove last - if at the end.
		$sanitized_filename = strtolower( $sanitized_filename ); // Lowercase.

		return $sanitized_filename;
	},
	10,
	1
);

// Remove counter-productive auto dns prefetch.
add_filter(
	'wp_resource_hints',
	function ( $hints, $relation_type ) {
		if ( 'dns-prefetch' === $relation_type ) {
			return array_diff( wp_dependencies_unique_hosts(), $hints );
		}
		return $hints;
	},
	10,
	2
);

// Contact Form 7's own stylesheet used to be force-dequeued here on every
// page load - both branches of the old if/else called the same
// wp_dequeue_style('contact-form-7'), so it was unconditional regardless
// of which branch ran. It also checked has_shortcode() against
// $post->post_content, which could never find the form anyway: the
// contact modal renders the [contact-form-7] shortcode from a template
// part (component-modal-contact.php, included sitewide via the footer),
// not from any post's own content. Net effect: the form was rendering
// completely unstyled on every page. Removed entirely - CF7 already
// enqueues its own stylesheet only when the shortcode actually renders,
// which is the correct and simpler behaviour this was trying to reinvent.

// Disable FWP radio counts.
add_filter( 'facetwp_facet_dropdown_show_counts', '__return_false' );

/**
 * Add a "Menu Depth" ACF field group location rule type.
 *
 * @param array $choices Existing location rule type choices.
 * @return array Filtered location rule type choices.
 */
function acf_location_rules_types( $choices ) {
	$choices['Menu']['menu_level'] = 'Menu Depth';
	return $choices;
}
add_filter( 'acf/location/rule_types', 'acf_location_rules_types' );

add_filter( 'acf/location/rule_values/menu_level', 'acf_location_rule_values_level' );
/**
 * Provide the value choices for the "Menu Depth" location rule.
 *
 * @param array $choices Existing rule value choices.
 * @return array Filtered rule value choices.
 */
function acf_location_rule_values_level( $choices ) {
	$choices[0] = '0';
	$choices[1] = '1';
	$choices[2] = '2';
	$choices[3] = '3';

	return $choices;
}
add_filter( 'acf/location/rule_match/menu_level', 'acf_location_rule_match_level', 10, 4 );
/**
 * Determine whether the "Menu Depth" location rule matches the current screen.
 *
 * @param bool  $is_match    Whether the current rule matches.
 * @param array $rule        The location rule.
 * @param array $options     Screen options.
 * @param array $field_group The field group being edited.
 * @return bool Whether the rule matches.
 */
function acf_location_rule_match_level( $is_match, $rule, $options, $field_group ) {
	$current_screen = get_current_screen();
	if ( 'nav-menus' === $current_screen->base ) {
		if ( '==' === $rule['operator'] ) {
			$is_match = ( (int) $rule['value'] === $options['nav_menu_item_depth'] );
		}
	}
	return $is_match;
}

/**
 * Bold-only wysiwyg toolbar. Used by fields whose output gets echoed
 * straight into a heading tag (e.g. the cards block's title, see
 * template-parts/content/blocks/cards/block-cards.php) - wysiwyg
 * still lets editors alternate-colour text via <strong> (see
 * _card.scss), but the default/basic toolbars also allow headings,
 * lists, links etc. that would be invalid nested inside an <h3>.
 *
 * @param array $toolbars Existing named toolbars.
 * @return array Filtered toolbars.
 */
function acf_wysiwyg_bold_only_toolbar( $toolbars ) {
	$toolbars['Bold Only'] = array(
		1 => array( 'bold' ),
	);
	return $toolbars;
}
add_filter( 'acf/fields/wysiwyg/toolbars', 'acf_wysiwyg_bold_only_toolbar' );

// Remove annoying global gutenbourg styles.
remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
remove_action( 'wp_footer', 'wp_enqueue_global_styles', 1 );

/**
 * Removes comments page and contact form 7 (if admin) from admin menu.
 *
 * @return void
 */
function remove_items_from_admin_menu() {
	remove_menu_page( 'edit-comments.php' );

	$user = wp_get_current_user();
	if ( ! in_array( 'administrator', (array) $user->roles, true ) ) {
		remove_menu_page( 'wpcf7' );
	}
}

/**
 * Assigns a default footer type to pages/posts on creation.
 *
 * @param int     $post_id Post ID.
 * @param WP_Post $post    Post object.
 * @param bool    $update  Whether this is an existing post being updated.
 * @return void
 */
function update_footer_type( $post_id, $post, $update ) {
	if ( wp_is_post_revision( $post_id ) ) {
		return;
	}
	if ( ! $update ) {
		if ( 'page' === $post->post_type ) {
			$value = 3006;
			update_field( 'footer_type', $value, $post_id );
		} elseif ( 'post' === $post->post_type ) {
			$value = 3257;
			update_field( 'footer_type', $value, $post_id );
		} elseif ( 'clients' === $post->post_type ) {
			$value = 3006;
			update_field( 'footer_type', $value, $post_id );
		}
	}
}
add_action( 'wp_insert_post', 'update_footer_type', 10, 3 );

add_theme_support( 'post-thumbnails' );

/**
 * Customise ACF WYSIWYG output to render headings as p.className instead of real heading tags.
 *
 * @param mixed $value   The field value.
 * @param int   $post_id Post ID.
 * @param array $field   The field array.
 * @return mixed Filtered field value.
 */
function modify_acf_wysiwyg_output( $value, $post_id, $field ) {
	if ( 'wysiwyg' === $field['type'] ) {

		// Set the array of allowed heading levels.
		$allowed_heading_levels = array( 1, 4, 5, 6 );

		// Convert the array to a string for regex.
		$allowed_heading_levels_str = implode( ',', $allowed_heading_levels );

		// Change heading 1 to paragraph and add custom class.
		$value = preg_replace_callback(
			'/<h([' . $allowed_heading_levels_str . '])\b[^>]*>(.*?)<\/h[' . $allowed_heading_levels_str . ']>/s',
			function ( $matches ) {
				$heading_level = $matches[1];
				$content       = $matches[2];
				$style         = '';

				// Extract the inline style from the heading tag, if present.
				preg_match( '/style="(.*?)"/', $matches[0], $style_matches );
				if ( ! empty( $style_matches ) ) {
					$style = ' style="' . $style_matches[1] . '"';
				}

				return '<p class="h' . $heading_level . '"' . $style . '>' . $content . '</p>';
			},
			$value
		);
	}

	return $value;
}
// add_filter( 'acf/format_value/type=wysiwyg', 'modify_acf_wysiwyg_output', 10, 3 ).

// Remove classic editor if toggle = 'new' or if toggle doesn't exist.
add_action(
	'admin_init',
	function () {
		global $pagenow, $post;

		// Only run on post editing screens.
		if ( 'post.php' !== $pagenow && 'post-new.php' !== $pagenow ) {
			return;
		}

		// Ensure $post is set.
		if ( ! $post && isset( $_GET['post'] ) ) {
			$post = get_post( $_GET['post'] );
		}

		if ( ! $post ) {
			return;
		}

		$toggle = get_field( 'content_mode_toggle', $post->ID );

		// Remove editor if toggle is 'new' OR if toggle field doesn't exist (null/empty).
		if ( 'new' === $toggle || empty( $toggle ) ) {
			// Remove the default editor.
			remove_post_type_support( $post->post_type, 'editor' );
		}
	}
);

// Hide flexible content field group when toggle = 'legacy'.
add_filter(
	'acf/prepare_field/name=content_blocks',
	function ( $field ) {
		global $post;

		// Get current post ID from different sources.
		$post_id = null;
		if ( isset( $_GET['post'] ) ) {
			$post_id = intval( $_GET['post'] );
		} elseif ( isset( $_POST['post_ID'] ) ) {
			$post_id = intval( $_POST['post_ID'] );
		} elseif ( $post && isset( $post->ID ) ) {
			$post_id = $post->ID;
		}

		if ( ! $post_id ) {
			return $field;
		}

		$toggle = get_field( 'content_mode_toggle', $post_id );

		if ( 'legacy' === $toggle ) {
			return false; // hide the field.
		}

		return $field;
	}
);

// Hide fields below a specific tab when toggle = 'new'.
add_filter(
	'acf/prepare_field',
	function ( $field ) {
		global $post;

		// Get current post ID.
		$post_id = null;
		if ( isset( $_GET['post'] ) ) {
			$post_id = intval( $_GET['post'] );
		} elseif ( isset( $_POST['post_ID'] ) ) {
			$post_id = intval( $_POST['post_ID'] );
		} elseif ( $post && isset( $post->ID ) ) {
			$post_id = $post->ID;
		}

		if ( ! $post_id ) {
			return $field;
		}

		$toggle = get_field( 'content_mode_toggle', $post_id );

		if ( 'new' === $toggle ) {
			// List of field names to hide when toggle is 'new' (everything below Legacy Content tab except client_logo).
			$fields_to_hide = array(
				'above_gallery_text',
				'gallery_layout',
				'gallery',
				'below_before_after_text',
				'before_image',
				'after_image',
			);

			if ( in_array( $field['name'], $fields_to_hide, true ) ) {
				return false; // hide the field.
			}
		}

		return $field;
	}
);

add_action(
	'admin_head',
	function () {
		global $post, $pagenow;

		if ( ( 'post.php' !== $pagenow && 'post-new.php' !== $pagenow ) || ! $post ) {
			return;
		}

		$toggle = get_field( 'content_mode_toggle', $post->ID );

		if ( 'legacy' === $toggle ) {
			echo '<style type="text/css">
            #acf-group_5b9930d7812e5 { display: none !important; }
        </style>';
			echo '<script type="text/javascript">
            document.addEventListener("DOMContentLoaded", function() {
                var metabox = document.getElementById("acf-group_5b9930d7812e5");
                if (metabox) {
                    metabox.style.display = "none";
                }
            });
        </script>';
		}

		if ( 'new' === $toggle ) {
			echo '<style type="text/css">
            #acf-group_5ba4e6ecb308c .acf-field-5bd6f3f7a4393,  /* above_gallery_text */
            #acf-group_5ba4e6ecb308c .acf-field-656de91ef988d,  /* gallery_layout */
            #acf-group_5ba4e6ecb308c .acf-field-5ba4e79b17431,  /* gallery */
            #acf-group_5ba4e6ecb308c .acf-field-66c36ee8a0e56,  /* below_before_after_text */
            #acf-group_5ba4e6ecb308c .acf-field-66c36f3420d2f,  /* before_image */
            #acf-group_5ba4e6ecb308c .acf-field-66c36f4e20d30,  /* after_image */
            #acf-group_5ba4e6ecb308c .acf-tab-wrap,             /* tab wrapper - only in Our work group */
            #acf-group_5ba4e6ecb308c .acf-field-68beb01e26752   /* Legacy Content tab field */
            { display: none !important; }
        </style>';
			echo '<script type="text/javascript">
            document.addEventListener("DOMContentLoaded", function() {
                // Only target fields within the Our work metabox
                var ourWorkMetabox = document.getElementById("acf-group_5ba4e6ecb308c");
                if (ourWorkMetabox) {
                    var fieldsToHide = [
                        "acf-field-5bd6f3f7a4393",
                        "acf-field-656de91ef988d",
                        "acf-field-5ba4e79b17431",
                        "acf-field-66c36ee8a0e56",
                        "acf-field-66c36f3420d2f",
                        "acf-field-66c36f4e20d30",
                        "acf-tab-wrap",
                        "acf-field-68beb01e26752"
                    ];

                    fieldsToHide.forEach(function(className) {
                        var elements = ourWorkMetabox.getElementsByClassName(className);
                        for (var i = 0; i < elements.length; i++) {
                            elements[i].style.display = "none";
                        }
                    });
                }
            });
        </script>';
		}
	}
);

// Hide the entire field group/metabox when toggle = 'legacy'.
add_filter(
	'acf/load_field_group',
	function ( $field_group ) {
		global $post;

		// Target the specific field group by key or title.
		if ( 'group_5b9930d7812e5' !== $field_group['key'] && 'Flexible content' !== $field_group['title'] ) {
			return $field_group;
		}

		// Get current post ID.
		$post_id = null;
		if ( isset( $_GET['post'] ) ) {
			$post_id = intval( $_GET['post'] );
		} elseif ( isset( $_POST['post_ID'] ) ) {
			$post_id = intval( $_POST['post_ID'] );
		} elseif ( $post && isset( $post->ID ) ) {
			$post_id = $post->ID;
		}

		if ( ! $post_id ) {
			return $field_group;
		}

		$toggle = get_field( 'content_mode_toggle', $post_id );

		if ( 'legacy' === $toggle ) {
			return false; // hide the entire field group.
		}

		return $field_group;
	}
);
