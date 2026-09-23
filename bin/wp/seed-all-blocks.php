<?php
/**
 * Overwrites the Home page's content_blocks with one example row per
 * flexible-content layout, so every block template can be eyeballed at
 * once. NOT idempotent-safe in the same way as seed-content.php - this
 * always replaces Home's content_blocks, on purpose (it's a testing tool,
 * not a one-time setup step). Also creates one example Portfolio,
 * Testimonial, and Team Member post if none exist yet, and imports the
 * theme's placeholder icon as a media attachment for image fields.
 *
 * Run with: npm run wp:seed:blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'update_field' ) ) {
	WP_CLI::error( 'ACF is not active - cannot seed content_blocks.' );
}

require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

$home = get_page_by_path( 'home', OBJECT, 'page' );
if ( ! $home ) {
	WP_CLI::error( 'Home page not found - run `npm run wp:seed` first.' );
}
$home_id = $home->ID;

// A real (if tiny) image beats an empty field for every image/icon field
// below - reuses the theme's own placeholder asset rather than fetching
// anything from the network.
$existing_attachment = get_posts(
	array(
		'post_type'      => 'attachment',
		'post_status'    => 'inherit',
		'meta_key'       => '_seed_all_blocks_placeholder',
		'posts_per_page' => 1,
		'fields'         => 'ids',
	)
);

if ( $existing_attachment ) {
	$image_id = (int) $existing_attachment[0];
	WP_CLI::log( sprintf( 'Reusing existing placeholder attachment (%d).', $image_id ) );
} else {
	$image_path = get_template_directory() . '/assets/images/placeholder_icon.png';
	$filename   = 'seed-placeholder-' . basename( $image_path );
	$upload     = wp_upload_bits( $filename, null, file_get_contents( $image_path ) );

	if ( ! empty( $upload['error'] ) ) {
		WP_CLI::warning( 'Could not import placeholder image: ' . $upload['error'] );
		$image_id = 0;
	} else {
		$image_id = wp_insert_attachment(
			array(
				'post_mime_type' => wp_check_filetype( $filename )['type'],
				'post_title'     => 'Placeholder',
				'post_content'   => '',
				'post_status'    => 'inherit',
			),
			$upload['file']
		);

		wp_update_attachment_metadata( $image_id, wp_generate_attachment_metadata( $image_id, $upload['file'] ) );
		update_post_meta( $image_id, '_seed_all_blocks_placeholder', 1 );
		WP_CLI::success( sprintf( 'Imported placeholder attachment (%d).', $image_id ) );
	}
}

/**
 * Creates a post of the given type if none exists yet, returning its ID.
 *
 * @param string $post_type   Post type slug.
 * @param string $title       Title for a newly created post.
 * @param array  $extra_field_updates Map of field name => value to apply via update_field() after creation.
 * @return int
 */
function seed_example_post( $post_type, $title, $extra_field_updates = array() ) {
	$existing = get_posts(
		array(
			'post_type'      => $post_type,
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	);

	if ( $existing ) {
		WP_CLI::log( sprintf( 'Reusing existing %s post (%d).', $post_type, $existing[0] ) );
		return (int) $existing[0];
	}

	$post_id = wp_insert_post(
		array(
			'post_title'  => $title,
			'post_type'   => $post_type,
			'post_status' => 'publish',
		),
		true
	);

	if ( is_wp_error( $post_id ) ) {
		WP_CLI::error( $post_id->get_error_message() );
	}

	foreach ( $extra_field_updates as $field_name => $value ) {
		update_field( $field_name, $value, $post_id );
	}

	WP_CLI::success( sprintf( 'Created example %s post: %s (%d).', $post_type, $title, $post_id ) );
	return (int) $post_id;
}

/**
 * Creates several posts of the given type, one per $items entry, reusing
 * any that already exist with the same title (so reruns don't pile up
 * duplicates). Returns the IDs in the same order as $items.
 *
 * @param string $post_type Post type slug.
 * @param array  $items     List of ['title' => ..., 'content' => '', 'excerpt' => '', 'fields' => []].
 * @return int[]
 */
function seed_example_posts( $post_type, $items ) {
	$ids = array();

	foreach ( $items as $item ) {
		$existing = get_posts(
			array(
				'post_type'      => $post_type,
				'post_status'    => 'publish',
				'title'          => $item['title'],
				'posts_per_page' => 1,
				'fields'         => 'ids',
			)
		);

		if ( $existing ) {
			WP_CLI::log( sprintf( 'Reusing existing %s post: %s (%d).', $post_type, $item['title'], $existing[0] ) );
			$ids[] = (int) $existing[0];
			continue;
		}

		$post_id = wp_insert_post(
			array(
				'post_title'   => $item['title'],
				'post_content' => $item['content'] ?? '',
				'post_excerpt' => $item['excerpt'] ?? '',
				'post_type'    => $post_type,
				'post_status'  => 'publish',
			),
			true
		);

		if ( is_wp_error( $post_id ) ) {
			WP_CLI::error( $post_id->get_error_message() );
		}

		foreach ( $item['fields'] ?? array() as $field_name => $value ) {
			update_field( $field_name, $value, $post_id );
		}

		WP_CLI::success( sprintf( 'Created example %s post: %s (%d).', $post_type, $item['title'], $post_id ) );
		$ids[] = (int) $post_id;
	}

	return $ids;
}

$portfolio_id = seed_example_post( 'portfolio', 'Example Portfolio Item' );

$testimonial_id = seed_example_post(
	'testimonials',
	'Example Testimonial',
	array(
		'testimonial' => array(
			array(
				'testimonial_text' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. This is placeholder testimonial copy.',
				'client_details'   => 'Managing Director',
				'client_name'      => 'Jane Doe',
			),
		),
	)
);

$team_member_ids = seed_example_posts(
	'team_member',
	array(
		array(
			'title'  => 'Example Team Member One',
			'fields' => array(
				'role'          => 'Founder',
				'read_bio_mode' => 'none',
			),
		),
		array(
			'title'  => 'Example Team Member Two',
			'fields' => array(
				'role'          => 'Creative Director',
				'read_bio_mode' => 'modal',
			),
		),
		array(
			'title'  => 'Example Team Member Three',
			'fields' => array(
				'role'          => 'Head of Delivery',
				'read_bio_mode' => 'modal',
			),
		),
	)
);

$blog_post_ids = seed_example_posts(
	'post',
	array(
		array(
			'title'   => 'Example Blog Post One',
			'content' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. This is placeholder blog content.</p>',
			'excerpt' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
		),
		array(
			'title'   => 'Example Blog Post Two',
			'content' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. This is placeholder blog content.</p>',
			'excerpt' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
		),
		array(
			'title'   => 'Example Blog Post Three',
			'content' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. This is placeholder blog content.</p>',
			'excerpt' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
		),
		array(
			'title'   => 'Example Blog Post Four',
			'content' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. This is placeholder blog content.</p>',
			'excerpt' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
		),
		array(
			'title'   => 'Example Blog Post Five',
			'content' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. This is placeholder blog content.</p>',
			'excerpt' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
		),
		array(
			'title'   => 'Example Blog Post Six',
			'content' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. This is placeholder blog content.</p>',
			'excerpt' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
		),
	)
);

$button_row = array(
	'link'          => array(
		'title'  => 'Learn more',
		'url'    => '#',
		'target' => '',
	),
	'button_type'   => 'solid',
	'button_colour' => 'orange',
);

$title_options = array(
	'text_alignment' => 'text-alignment-left',
	'eyebrow'        => 'Eyebrow text',
	'eyebrow_style'  => 'default',
	'title'          => 'Section title',
	'text'           => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>',
);

$content_blocks = array(
	array(
		'acf_fc_layout'     => 'wysiwyg',
		'text_area'         => '<h2>Wysiwyg block</h2><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>',
	),
	array(
		'acf_fc_layout'     => 'separator',
		'background_colour' => 'white',
	),
	array(
		'acf_fc_layout'     => 'stats',
		'background_colour' => 'orange',
		'stats'             => array(
			array(
				'text_position' => 'below',
				'value'         => 10,
				'suffix'        => '+',
				'use_commas'    => 0,
				'label'         => 'Years of experience',
			),
			array(
				'text_position' => 'below',
				'value'         => 250,
				'suffix'        => '+',
				'use_commas'    => 1,
				'label'         => 'Projects delivered',
			),
		),
	),
	array(
		'acf_fc_layout'     => 'cards',
		'background_colour' => 'navy',
		'items'             => array(
			array(
				'background_colour' => 'none',
				'image'             => $image_id ?: null,
				'title'             => '<p>Card one</p>',
				'text'              => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>',
				'button_group'      => array( $button_row ),
			),
			array(
				'background_colour' => 'none',
				'image'             => $image_id ?: null,
				'title'             => '<p>Card two</p>',
				'text'              => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>',
				'button_group'      => array( $button_row ),
			),
		),
	),
	array(
		'acf_fc_layout'     => 'feature_grid',
		'background_colour' => 'white',
		'text'              => '<h2>Our Advantages</h2>',
		'layout'            => 'layout-1',
		'items'             => array(
			array(
				'icon'         => $image_id ?: null,
				'text'         => '<p>Feature one description.</p>',
				'button_group' => array(),
			),
			array(
				'icon'         => $image_id ?: null,
				'text'         => '<p>Feature two description.</p>',
				'button_group' => array(),
			),
		),
	),
	array(
		'acf_fc_layout'     => 'content_grid',
		'background_colour' => 'orange',
		'eyebrow'           => 'Content grid',
		'rows'              => array(
			array(
				'columns' => array(
					array(
						'width'             => '5',
						'background_colour' => 'none',
						'content'           => '<p>First column content.</p>',
						'border'            => 0,
						'force_stack'       => 0,
						'button_group'      => array(),
					),
					array(
						'width'             => '5',
						'background_colour' => 'none',
						'content'           => '<p>Second column content.</p>',
						'border'            => 0,
						'force_stack'       => 0,
						'button_group'      => array(),
					),
				),
			),
		),
	),
	array(
		'acf_fc_layout' => 'sticky',
		'eyebrow'       => 'Sticky block',
		'text'          => '<p>This text sticks to the top while the boxes on the right scroll past.</p>',
		'button_group'  => array( $button_row ),
		'column_content' => array(
			array(
				'icon'         => $image_id ?: null,
				'title'        => 'Box one',
				'body_text'    => 'Lorem ipsum dolor sit amet.',
				'button_group' => array(),
			),
			array(
				'icon'         => $image_id ?: null,
				'title'        => 'Box two',
				'body_text'    => 'Lorem ipsum dolor sit amet.',
				'button_group' => array(),
			),
		),
	),
	array(
		'acf_fc_layout' => 'accordion',
		'background_colour' => 'navy',
		'wysiwyg'       => '<h2>Accordion block</h2>',
		'accordion'     => array(
			array(
				'title'        => 'Accordion item one',
				'text'         => '<p>Answer to the first question.</p>',
				'button_text'  => '',
			),
			array(
				'title'        => 'Accordion item two',
				'text'         => '<p>Answer to the second question.</p>',
				'button_text'  => '',
			),
		),
	),
	array(
		'acf_fc_layout' => 'buttons',
		'background_colour' => 'white',
		'force_stack'   => 0,
		'button_group'  => array( $button_row, $button_row ),
	),
	array(
		'acf_fc_layout' => 'logos',
		'navigation'    => 'enable',
		'eyebrow'       => 'Our Clients',
		'eyebrow_style' => 'centre',
		'clients'       => $portfolio_id ? array( $portfolio_id ) : array(),
	),
	array(
		'acf_fc_layout' => 'team',
		'text'          => '<h2>The Team</h2>',
		'team_members'  => $team_member_ids,
	),
	array(
		'acf_fc_layout' => 'testimonials',
		'eyebrow'       => 'What people say',
		'testimonials'  => $testimonial_id ? array( $testimonial_id ) : array(),
	),
	array(
		'acf_fc_layout' => 'listing',
		'title_options' => $title_options,
		'query_type'    => 'query',
		'post_type'     => 'post',
		'number_of_queries' => 3,
	),
	array(
		'acf_fc_layout' => 'latest_blogs',
		'eyebrow'       => 'From the blog',
		'title'         => 'Our <strong>latest updates</strong>',
		'button_group'  => array( $button_row ),
	),
	array(
		'acf_fc_layout'     => 'contact',
		'background_colour' => 'orange',
		'eyebrow'           => 'Contact',
		'text'              => '<h2>Get in touch</h2>',
		'force_stack'       => 0,
		'button_group'      => array(),
		'contact_form_shortcode' => '[contact-form-7 id="1" title="Contact form"]',
	),
	array(
		'acf_fc_layout' => 'map',
		'address'       => 'Crown House, 94 Armley Rd, Armley, Leeds LS12 2EJ',
	),
	array(
		'acf_fc_layout' => 'video',
		'video_type'    => 'youtube',
		'video_ratio'   => 'landscape',
		'video_url'     => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
		'autoplay'      => 'false',
		'loop'          => 'false',
		'muted'         => 'false',
		'controls'      => 'true',
	),
	array(
		'acf_fc_layout' => 'before_after',
		'before_image'  => $image_id ?: null,
		'after_image'   => $image_id ?: null,
	),
);

update_field( 'field_5b9931063bbb3', $content_blocks, $home_id );

WP_CLI::success( sprintf( 'Seeded all %d flexible-content layouts onto Home (%d).', count( $content_blocks ), $home_id ) );
WP_CLI::success( sprintf( 'Seeded %d example blog posts and %d example team members.', count( $blog_post_ids ), count( $team_member_ids ) ) );

WP_CLI::log( '' );
WP_CLI::log( 'Notes:' );
WP_CLI::log( '- client_logo is not a registered ACF field on the portfolio CPT (pre-existing gap) - the logos block may show no logo for the seeded portfolio item.' );
WP_CLI::log( '- The "Block Width" clone field on map/accordion points at an ACF field with no local JSON definition (field_617e9fb9b4fbc) - left unset here.' );
WP_CLI::log( '- The placeholder image is a small icon, not a photo - before_after will look stretched/low-res; swap for a real photo to check that block properly.' );
