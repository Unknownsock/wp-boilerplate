<?php
/**
 * Script and stylesheet enqueueing for the theme (Vite).
 *
 * @package Boilerplate
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether the Vite dev server is running.
 *
 * @return bool
 */
function boilerplate_vite_is_dev() {
	return file_exists( get_stylesheet_directory() . '/hot' );
}

/**
 * Get the Vite dev server URL.
 *
 * @return string
 */
function boilerplate_vite_dev_url() {
	return trim( file_get_contents( get_stylesheet_directory() . '/hot' ) );
}

/**
 * Get the parsed Vite build manifest.
 *
 * @return array
 */
function boilerplate_vite_manifest() {
	static $manifest = null;
	if ( null === $manifest ) {
		$path     = get_stylesheet_directory() . '/assets/.vite/manifest.json';
		$manifest = file_exists( $path ) ? json_decode( file_get_contents( $path ), true ) : array();
	}
	return $manifest;
}

/**
 * Enqueue the theme's main script and styles.
 */
function enqueue_theme_assets() {
	if ( boilerplate_vite_is_dev() ) {
		$dev_url = boilerplate_vite_dev_url();

		wp_enqueue_script( 'vite-client', $dev_url . '/@vite/client', array(), null, false );
		wp_enqueue_script( 'theme-js', $dev_url . '/src/js/app.js', array(), null, true );
		return;
	}

	$manifest = boilerplate_vite_manifest();
	$entry    = $manifest['src/js/app.js'] ?? null;

	if ( ! $entry ) {
		return;
	}

	if ( ! empty( $entry['css'] ) ) {
		foreach ( $entry['css'] as $i => $css_file ) {
			wp_enqueue_style(
				'theme-css' . ( $i ? "-$i" : '' ),
				get_stylesheet_directory_uri() . '/assets/' . $css_file,
				array(),
				filemtime( get_stylesheet_directory() . '/assets/' . $css_file )
			);
		}
	}

	wp_enqueue_script(
		'theme-js',
		get_stylesheet_directory_uri() . '/assets/' . $entry['file'],
		array(),
		filemtime( get_stylesheet_directory() . '/assets/' . $entry['file'] ),
		true
	);
}
add_action( 'wp_enqueue_scripts', 'enqueue_theme_assets' );

/**
 * The current page's content_blocks post ID plus, if a shared footer is
 * enabled, the footer page's ID too - both `enqueue_block_assets()` and
 * `boilerplate_get_page_faq_data()` need to scan the same set of rows, so this is
 * shared between them rather than duplicated.
 *
 * @return int[]
 */
function boilerplate_get_content_block_post_ids() {
	$post_id  = get_queried_object_id();
	$post_ids = array( $post_id );

	if ( 'yes' === get_field( 'enable_footer', $post_id ) ) {
		$footer_type = get_field( 'footer_type', $post_id );
		if ( $footer_type ) {
			$post_ids[] = is_object( $footer_type ) ? $footer_type->ID : $footer_type;
		}
	}

	return $post_ids;
}

/**
 * Build FAQPage schema.org data (question/answer pairs) from any
 * `accordion` content block rows on the current page, for
 * boilerplate_add_faq_schema() below to output as server-rendered JSON-LD.
 *
 * Reads directly from ACF at request time instead of a block template
 * writing to wp_options as a side effect of rendering (the previous
 * approach: update_option( 'faq_data_option', ... ) inside
 * block-accordion.php) - that pattern churned an autoloaded option on
 * every page view and last-block-wins if a page had more than one
 * accordion block.
 *
 * @return array<int, array{question: string, answer: string}>
 */
function boilerplate_get_page_faq_data() {
	$faq_data = array();

	foreach ( boilerplate_get_content_block_post_ids() as $id ) {
		$rows = get_field( 'content_blocks', $id );
		if ( ! $rows ) {
			continue;
		}
		foreach ( $rows as $row ) {
			if ( 'accordion' !== ( $row['acf_fc_layout'] ?? null ) ) {
				continue;
			}
			foreach ( $row['accordion'] ?? array() as $item ) {
				if ( empty( $item['title'] ) ) {
					continue;
				}
				$faq_data[] = array(
					'question' => $item['title'],
					'answer'   => $item['text'] ?? '',
				);
			}
		}
	}

	return $faq_data;
}

/**
 * Output FAQPage schema.org JSON-LD for any accordion blocks on the page.
 *
 * Used to be built client-side (src/js/modules/seo.js), appended to
 * <head> via JS after the page had already loaded - invisible to any
 * crawler/tool that doesn't execute JS, and silently dropped entirely if
 * that script failed to load. boilerplate_get_page_faq_data() already resolves
 * this from ACF at request time, so there was no reason for it to
 * round-trip through JS at all.
 */
function boilerplate_add_faq_schema() {
	$faq_data = boilerplate_get_page_faq_data();
	if ( empty( $faq_data ) ) {
		return;
	}

	$schema = array(
		'@context'   => 'https://schema.org',
		'@type'      => 'FAQPage',
		'mainEntity' => array_map(
			function ( $item ) {
				return array(
					'@type'          => 'Question',
					'name'           => wp_strip_all_tags( $item['question'] ),
					'acceptedAnswer' => array(
						'@type' => 'Answer',
						'text'  => wp_strip_all_tags( $item['answer'] ),
					),
				);
			},
			$faq_data
		),
	);

	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>';
}
add_action( 'wp_head', 'boilerplate_add_faq_schema' );

/**
 * Enqueue a block-scoped Vite entry - its JS, plus any CSS that entry
 * pulled in via its own `import './<name>.scss'`. Every block's own JS
 * file now imports its own stylesheet directly (style.scss no longer
 * imports block partials itself - see the comment there), so the
 * manifest's `css` array for this entry is exactly the styles this
 * specific block needs, nothing more. Blocks with
 * no JS behaviour of their own still get a minimal `<name>.js` that exists
 * solely to import `<name>.scss` and give Vite something to key the entry
 * on (see e.g. contact/contact.js).
 *
 * @param string $entry_path Vite entry path, relative to project root.
 * @param string $handle     Script handle to register.
 */
function boilerplate_enqueue_block_script( $entry_path, $handle ) {
	if ( boilerplate_vite_is_dev() ) {
		wp_enqueue_script( $handle, boilerplate_vite_dev_url() . '/' . $entry_path, array(), null, true );
		return;
	}

	$manifest = boilerplate_vite_manifest();
	$entry    = $manifest[ $entry_path ] ?? null;

	if ( ! $entry ) {
		return;
	}

	if ( ! empty( $entry['css'] ) ) {
		foreach ( $entry['css'] as $i => $css_file ) {
			wp_enqueue_style(
				"{$handle}-css" . ( $i ? "-$i" : '' ),
				get_stylesheet_directory_uri() . '/assets/' . $css_file,
				array(),
				filemtime( get_stylesheet_directory() . '/assets/' . $css_file )
			);
		}
	}

	wp_enqueue_script(
		$handle,
		get_stylesheet_directory_uri() . '/assets/' . $entry['file'],
		array(),
		filemtime( get_stylesheet_directory() . '/assets/' . $entry['file'] ),
		true
	);
}

/**
 * Enqueue JS+CSS for any block used on this page that has its own Vite
 * entry - i.e. a blocks/<name>/<name>.js file (see vite.config.js
 * findBlockEntries()). Checks both the page's own content_blocks field
 * and, if enabled, the shared footer page's - index.php (:38-45, :96-107)
 * renders content_blocks from both sources.
 *
 * `latest_blogs` is added explicitly rather than only being picked up from
 * content_blocks: index.php/single.php/single-portfolio.php also render that
 * block directly (passing the `related_articles` field through $args) for
 * the "related articles" section, entirely outside the content_blocks loop
 * - so a page can use the block's markup/styles without it ever appearing
 * as a content_blocks row.
 */
function enqueue_block_assets() {
	$layouts = array();
	foreach ( boilerplate_get_content_block_post_ids() as $id ) {
		$rows = get_field( 'content_blocks', $id );
		if ( ! $rows ) {
			continue;
		}
		foreach ( wp_list_pluck( $rows, 'acf_fc_layout' ) as $layout_name ) {
			if ( $layout_name ) {
				$layouts[ $layout_name ] = true;
			}
		}
	}

	if ( get_field( 'related_articles', get_queried_object_id() ) ) {
		$layouts['latest_blogs'] = true;
	}

	foreach ( array_keys( $layouts ) as $layout_name ) {
		$name = str_replace( '_', '-', $layout_name );
		boilerplate_enqueue_block_script(
			"themes/boilerplate/template-parts/content/blocks/{$name}/{$name}.js",
			"{$name}-js"
		);
	}
}
add_action( 'wp_enqueue_scripts', 'enqueue_block_assets' );

/**
 * Module scripts must keep type="module" — WP escapes attributes on scripts
 * registered without one, so filter the tag explicitly. Block-scoped
 * handles all end in "-js" by convention (see enqueue_block_assets()),
 * so new blocks don't need to be added here individually.
 *
 * @param string $tag    Script tag markup.
 * @param string $handle Script handle.
 * @return string
 */
function boilerplate_module_script_tag( $tag, $handle ) {
	if ( 'vite-client' === $handle || str_ends_with( $handle, '-js' ) ) {
		$tag = str_replace( ' src', ' type="module" src', $tag );
	}
	return $tag;
}
add_filter( 'script_loader_tag', 'boilerplate_module_script_tag', 10, 2 );

/**
 * Enqueue the admin stylesheet.
 */
function enqueue_admin_scripts() {
	wp_enqueue_style(
		'admin-styles',
		get_stylesheet_directory_uri() . '/assets/css/admin.css'
	);
}
add_action( 'admin_enqueue_scripts', 'enqueue_admin_scripts' );

//
// facetwp disable default styles.
//
add_filter(
	'facetwp_assets',
	function ( $assets ) {
		unset( $assets['front.css'] );
		return $assets;
	}
);

/**
 * Fetch the contents of a URL.
 *
 * Not currently called anywhere in the theme (verified at time of
 * writing), but rebuilt on the WP HTTP API rather than raw blocking cURL
 * in case that changes - a direct curl_exec() with no timeout and no
 * caching sitting in a page's render path would be a synchronous,
 * unbounded network call on every request. Cached the same way
 * boilerplate_geocode_address() already caches its own external calls.
 *
 * @param string $url URL to fetch.
 * @return string|false Response body, or false on failure.
 */
function curl_load( $url ) {
	$cache_key = 'boilerplate_curl_load_' . md5( $url );
	$cached    = get_transient( $cache_key );
	if ( false !== $cached ) {
		return $cached;
	}

	$response = wp_remote_get( $url, array( 'timeout' => 5 ) );

	if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
		return false;
	}

	$body = wp_remote_retrieve_body( $response );
	set_transient( $cache_key, $body, HOUR_IN_SECONDS );

	return $body;
}
