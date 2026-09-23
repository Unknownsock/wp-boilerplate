<?php
/**
 * Seed starter pages, home content, and the primary navigation.
 *
 * Run with: npm run wp:seed
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$pages = array(
	'home'    => array(
		'post_title' => 'Home',
		'post_name'  => 'home',
	),
	'about'   => array(
		'post_title' => 'About',
		'post_name'  => 'about',
	),
	'contact' => array(
		'post_title' => 'Contact',
		'post_name'  => 'contact',
	),
	'services' => array(
		'post_title' => 'Services',
		'post_name'  => 'services',
	),
	'approach' => array(
		'post_title' => 'Our Approach',
		'post_name'  => 'approach',
	),
);

$page_ids = array();
$created_pages = array();

foreach ( $pages as $slug => $page ) {
	$existing = get_page_by_path( $page['post_name'], OBJECT, 'page' );

	if ( $existing ) {
		$page_ids[ $slug ] = (int) $existing->ID;
		$created_pages[ $slug ] = false;
		WP_CLI::log( sprintf( 'Skipped existing page: %s (%d)', $page['post_title'], $existing->ID ) );
		continue;
	}

	$page_ids[ $slug ] = wp_insert_post(
		array(
		'post_title'  => $page['post_title'],
		'post_name'   => $page['post_name'],
		'post_type'   => 'page',
		'post_status' => 'publish',
		'post_content'=> '',
		),
		true
	);

	if ( is_wp_error( $page_ids[ $slug ] ) ) {
		WP_CLI::error( $page_ids[ $slug ]->get_error_message() );
	}

	$created_pages[ $slug ] = true;
	WP_CLI::success( sprintf( 'Created page: %s (%d)', $page['post_title'], $page_ids[ $slug ] ) );
}

$home_id = $page_ids['home'];

if ( $created_pages['home'] && function_exists( 'update_field' ) ) {
	update_field(
		'field_68b0ffb0hero1_home',
		array(
			'eyebrow'      => 'A flexible starting point',
			'text'         => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Build something thoughtful from here.</p>',
			'button_group' => array(),
			'media_type'   => 'video',
			'video'        => array(),
		),
		$home_id
	);

	update_field(
		'field_5b9931063bbb3',
		array(
			array(
				'acf_fc_layout' => 'wysiwyg',
				'text_area'     => '<h2>Start with a strong foundation</h2><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. This placeholder content is here to make the layout visible while the real content takes shape.</p>',
			),
			array(
				'acf_fc_layout' => 'wysiwyg',
				'text_area'     => '<h2>Make it your own</h2><p>Replace these words, add more flexible blocks, and shape the page around the project. The content system is ready for you.</p>',
			),
			array(
				'acf_fc_layout'     => 'stats',
				'background_colour' => 'white',
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
					array(
						'text_position' => 'below',
						'value'         => 98,
						'suffix'        => '%',
						'use_commas'    => 0,
						'label'         => 'Client satisfaction',
					),
				),
			),
			array(
				'acf_fc_layout'     => 'cards',
				'background_colour' => 'white',
				'items'             => array(
					array(
						'background_colour' => 'none',
						'title'             => '<p>Card one</p>',
						'text'              => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>',
					),
					array(
						'background_colour' => 'none',
						'title'             => '<p>Card two</p>',
						'text'              => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>',
					),
					array(
						'background_colour' => 'none',
						'title'             => '<p>Card three</p>',
						'text'              => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>',
					),
				),
			),
		),
		$home_id
	);

	WP_CLI::success( 'Seeded home hero and flexible content blocks (wysiwyg, stats, cards).' );
} elseif ( ! $created_pages['home'] ) {
	WP_CLI::log( 'Skipped home content because the Home page already exists.' );
} else {
	WP_CLI::warning( 'ACF is not active; pages and menu were created, but flexible content was skipped.' );
}

if ( 'posts' === get_option( 'show_on_front' ) ) {
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $home_id );
	WP_CLI::success( 'Set Home as the static front page.' );
} else {
	WP_CLI::log( 'Skipped front-page settings because WordPress is already configured.' );
}

/**
 * Creates (or reuses) a nav menu, seeds it with the given pages, and assigns
 * it to a theme menu location if that location is still unassigned.
 *
 * @param string $menu_name    Nav menu name, e.g. 'Main Menu'.
 * @param string $location_key Theme menu location key, e.g. 'menu-1'.
 * @param array  $slugs        Page slugs (keys into $page_ids/$pages) to add, in order.
 * @param array  $page_ids     Map of slug => page ID.
 * @param array  $pages        Map of slug => page data (for post_title in log messages).
 */
function seed_nav_menu( $menu_name, $location_key, $slugs, $page_ids, $pages ) {
	$menu = wp_get_nav_menu_object( $menu_name );

	if ( ! $menu ) {
		$menu_id = wp_create_nav_menu( $menu_name );

		if ( is_wp_error( $menu_id ) ) {
			WP_CLI::error( $menu_id->get_error_message() );
		}

		WP_CLI::success( sprintf( 'Created menu: %s (%d)', $menu_name, $menu_id ) );
	} else {
		$menu_id = (int) $menu->term_id;
		WP_CLI::log( sprintf( 'Using existing menu: %s (%d)', $menu_name, $menu_id ) );
	}

	$wanted_page_ids = array_map(
		function ( $slug ) use ( $page_ids ) {
			return $page_ids[ $slug ];
		},
		$slugs
	);

	$menu_items = wp_get_nav_menu_items( $menu_id );
	$menu_slugs = array();
	$seen_pages = array();

	foreach ( (array) $menu_items as $menu_item ) {
		if ( 'page' === $menu_item->object && in_array( (int) $menu_item->object_id, $wanted_page_ids, true ) ) {
			$page_id = (int) $menu_item->object_id;

			if ( isset( $seen_pages[ $page_id ] ) ) {
				wp_delete_post( $menu_item->ID, true );
				WP_CLI::log( sprintf( 'Removed duplicate menu item: %s', $menu_item->title ) );
				continue;
			}

			$seen_pages[ $page_id ] = true;
		}

		$menu_slugs[ $menu_item->object ][] = (int) $menu_item->object_id;
	}

	foreach ( $slugs as $slug ) {
		$page_id = $page_ids[ $slug ];

		if ( in_array( $page_id, $menu_slugs['page'] ?? array(), true ) ) {
			WP_CLI::log( sprintf( 'Skipped existing menu item: %s', $pages[ $slug ]['post_title'] ) );
			continue;
		}

		$item_id = wp_update_nav_menu_item(
			$menu_id,
			0,
			array(
				'menu-item-object'    => 'page',
				'menu-item-object-id' => $page_id,
				'menu-item-type'      => 'post_type',
				'menu-item-status'    => 'publish',
			)
		);

		if ( is_wp_error( $item_id ) ) {
			WP_CLI::error( $item_id->get_error_message() );
		}

		WP_CLI::success( sprintf( 'Added menu item: %s', $pages[ $slug ]['post_title'] ) );
	}

	$locations = get_theme_mod( 'nav_menu_locations', array() );
	if ( empty( $locations[ $location_key ] ) ) {
		$locations[ $location_key ] = $menu_id;
		set_theme_mod( 'nav_menu_locations', $locations );
		WP_CLI::success( sprintf( 'Assigned %s to the %s menu location.', $menu_name, $location_key ) );
	} else {
		WP_CLI::log( sprintf( 'Skipped menu location %s because it is already assigned.', $location_key ) );
	}
}

/**
 * Nests pages as dropdown children under an existing top-level menu item -
 * so there's at least one real dropdown to see when checking out the
 * standard header's mega-menu or the centered/minimal headers' simple
 * dropdown (both otherwise have nothing to expand).
 *
 * @param string $menu_name   Nav menu name, e.g. 'Main Menu'.
 * @param string $parent_slug Page slug (key into $page_ids/$pages) of the top-level item to nest under.
 * @param array  $child_slugs Page slugs to add as children, in order.
 * @param array  $page_ids    Map of slug => page ID.
 * @param array  $pages       Map of slug => page data (for post_title in log messages).
 */
function seed_menu_item_children( $menu_name, $parent_slug, $child_slugs, $page_ids, $pages ) {
	$menu = wp_get_nav_menu_object( $menu_name );

	if ( ! $menu ) {
		WP_CLI::warning( sprintf( 'Menu "%s" not found - skipped nesting children.', $menu_name ) );
		return;
	}

	$menu_id    = (int) $menu->term_id;
	$menu_items = wp_get_nav_menu_items( $menu_id );
	$parent_id  = $page_ids[ $parent_slug ];

	$parent_item_id  = 0;
	$existing_object_ids = array();

	foreach ( (array) $menu_items as $menu_item ) {
		if ( 0 === (int) $menu_item->menu_item_parent && 'page' === $menu_item->object && (int) $menu_item->object_id === $parent_id ) {
			$parent_item_id = $menu_item->ID;
		}
	}

	if ( ! $parent_item_id ) {
		WP_CLI::warning( sprintf( 'Parent menu item for "%s" not found in "%s" - skipped nesting children.', $pages[ $parent_slug ]['post_title'], $menu_name ) );
		return;
	}

	foreach ( (array) $menu_items as $menu_item ) {
		if ( (int) $menu_item->menu_item_parent === $parent_item_id ) {
			$existing_object_ids[] = (int) $menu_item->object_id;
		}
	}

	foreach ( $child_slugs as $child_slug ) {
		$child_page_id = $page_ids[ $child_slug ];

		if ( in_array( $child_page_id, $existing_object_ids, true ) ) {
			WP_CLI::log( sprintf( 'Skipped existing child menu item: %s', $pages[ $child_slug ]['post_title'] ) );
			continue;
		}

		$item_id = wp_update_nav_menu_item(
			$menu_id,
			0,
			array(
				'menu-item-object'    => 'page',
				'menu-item-object-id' => $child_page_id,
				'menu-item-type'      => 'post_type',
				'menu-item-parent-id' => $parent_item_id,
				'menu-item-status'    => 'publish',
			)
		);

		if ( is_wp_error( $item_id ) ) {
			WP_CLI::error( $item_id->get_error_message() );
		}

		WP_CLI::success( sprintf( 'Added child menu item: %s (under %s)', $pages[ $child_slug ]['post_title'], $pages[ $parent_slug ]['post_title'] ) );
	}
}

seed_nav_menu( 'Main Menu', 'menu-1', array( 'home', 'about', 'contact' ), $page_ids, $pages );
seed_nav_menu( 'Footer Menu 1', 'menu-2', array( 'about', 'contact' ), $page_ids, $pages );
seed_nav_menu( 'Footer Menu 2', 'menu-3', array( 'services', 'approach' ), $page_ids, $pages );

seed_menu_item_children( 'Main Menu', 'about', array( 'services', 'approach' ), $page_ids, $pages );

/**
 * Sets the mega-menu CTA card fields (group_68b9a001megacta, menu_level
 * 0 items only) on a top-level menu item - the standard header's
 * mega-menu otherwise has no seeded item with a CTA card configured, so
 * that half of the dropdown is never exercised.
 *
 * @param string $menu_name   Nav menu name, e.g. 'Main Menu'.
 * @param string $parent_slug Page slug (key into $page_ids) of the top-level item to set the CTA on.
 * @param array  $page_ids    Map of slug => page ID.
 */
function seed_mega_menu_cta( $menu_name, $parent_slug, $page_ids ) {
	if ( ! function_exists( 'update_field' ) ) {
		return;
	}

	$menu = wp_get_nav_menu_object( $menu_name );

	if ( ! $menu ) {
		WP_CLI::warning( sprintf( 'Menu "%s" not found - skipped mega-menu CTA.', $menu_name ) );
		return;
	}

	$menu_items = wp_get_nav_menu_items( (int) $menu->term_id );
	$parent_id  = $page_ids[ $parent_slug ];
	$item_id    = 0;

	foreach ( (array) $menu_items as $menu_item ) {
		if ( 0 === (int) $menu_item->menu_item_parent && 'page' === $menu_item->object && (int) $menu_item->object_id === $parent_id ) {
			$item_id = $menu_item->ID;
		}
	}

	if ( ! $item_id ) {
		WP_CLI::warning( sprintf( 'Top-level menu item for "%s" not found in "%s" - skipped mega-menu CTA.', $parent_slug, $menu_name ) );
		return;
	}

	if ( get_field( 'mega_cta_title', $item_id ) ) {
		WP_CLI::log( 'Skipped mega-menu CTA - already set on this menu item.' );
		return;
	}

	update_field( 'mega_cta_tag', 'Featured', $item_id );
	update_field( 'mega_cta_title', 'See our approach', $item_id );
	update_field( 'mega_cta_text', 'A look at how we work, from first conversation to delivery.', $item_id );
	update_field(
		'mega_cta_link',
		array(
			'title'  => 'Learn more',
			'url'    => '#',
			'target' => '',
		),
		$item_id
	);

	WP_CLI::success( sprintf( 'Set mega-menu CTA card on the "%s" menu item.', $parent_slug ) );
}

seed_mega_menu_cta( 'Main Menu', 'about', $page_ids );

/**
 * Seeds the footer's "Company Details" options group (group_5dea200a6ee51)
 * with a couple of example addresses, so the footer isn't blank below the
 * logo out of the box. Only fills in fields that are still empty.
 */
function seed_company_details() {
	if ( ! function_exists( 'update_field' ) ) {
		return;
	}

	$existing = get_field( 'company_details', 'option' );

	if ( ! empty( $existing['addresses'] ) ) {
		WP_CLI::log( 'Skipped company details - addresses already set.' );
		return;
	}

	update_field(
		'company_details',
		array(
			'company_name'      => 'Boilerplate Ltd',
			'company_telephone' => '01234 567890',
			'company_email'     => 'hello@example.com',
			'addresses'         => array(
				array(
					'address_lines' => array(
						array( 'address_line' => 'Boilerplate Ltd' ),
						array( 'address_line' => 'Crown House' ),
						array( 'address_line' => '94 Armley Rd' ),
						array( 'address_line' => 'Leeds LS12 2EJ' ),
					),
				),
				array(
					'address_lines' => array(
						array( 'address_line' => 'Boilerplate BV' ),
						array( 'address_line' => 'Herengracht 100' ),
						array( 'address_line' => '1015 BS Amsterdam' ),
					),
				),
			),
		),
		'option'
	);

	WP_CLI::success( 'Seeded company details (name, addresses, phone, email).' );
}

seed_company_details();
