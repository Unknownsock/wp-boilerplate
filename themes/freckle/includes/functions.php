<?php
/**
 * Shared helper functions used across the theme.
 *
 * @package Boilerplate
 */

defined( 'ABSPATH' ) || exit;

/**
 * Generate a random alphanumeric string.
 *
 * @param int $n Length of the string to generate.
 * @return string Random string.
 */
function unique_id( $n ) {
	$characters    = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$random_string = '';

	for ( $i = 0; $i < $n; $i++ ) {
		$index          = rand( 0, strlen( $characters ) - 1 );
		$random_string .= $characters[ $index ];
	}

	return $random_string;
}

/**
 * Convert an ACF image field value into inline markup - SVGs are fetched
 * and inlined (so they can inherit currentColor / be restyled with CSS,
 * e.g. as a button's $icon), anything else falls back to a plain <img>.
 *
 * @param array|string|null $image ACF image field value (array with a
 *                                  'url' key, return_format "array") or a
 *                                  raw URL string.
 * @return string Markup, or '' if nothing usable was given.
 */
function get_inline_icon_markup( $image ) {
	if ( ! $image ) {
		return '';
	}

	$url = is_array( $image ) ? ( $image['url'] ?? '' ) : $image;
	$alt = is_array( $image ) ? ( $image['alt'] ?? 'Icon' ) : 'Icon';

	if ( ! $url ) {
		return '';
	}

	if ( 'svg' !== pathinfo( $url, PATHINFO_EXTENSION ) ) {
		return '<img src="' . esc_url( $url ) . '" alt="' . esc_attr( $alt ) . '">';
	}

	$upload_dir = wp_get_upload_dir();

	if ( 0 === strpos( $url, $upload_dir['baseurl'] ) ) {
		$file_path = str_replace( $upload_dir['baseurl'], $upload_dir['basedir'], $url );
	} elseif ( 0 === strpos( $url, get_stylesheet_directory_uri() ) ) {
		$file_path = str_replace( get_stylesheet_directory_uri(), get_stylesheet_directory(), $url );
	} else {
		$file_path = $url;
	}

	$svg_content = @file_get_contents( $file_path );

	if ( false === $svg_content ) {
		error_log( "Error fetching SVG content from: $file_path" );
		return '';
	}

	return $svg_content;
}

/**
 * Build an icon or image tag, optionally wrapped in a link.
 *
 * @param string       $url    Image or SVG URL.
 * @param string|false $href   Optional link URL to wrap the icon in.
 * @param string       $css_class Optional CSS class for the wrapping link.
 * @param string       $id        Optional ID for the wrapping link.
 * @param string       $target    Optional link target.
 * @param string|false $attr      Optional raw extra attribute(s) for the wrapping link.
 * @return string The assembled markup.
 */
function add_icon( $url, $href = false, $css_class = '', $id = '', $target = '_self', $attr = false ) {
	$output = '';

	if ( $href ) {
		$output .= '<a href="' . esc_url( $href ) . '" target="' . esc_attr( $target ) . '" class="' . esc_attr( $css_class ) . '" id="' . esc_attr( $id ) . '" ' . $attr . '>';
	}

	if ( strpos( $url, '/uploads/' ) !== false ) {
		$url = esc_url( $url );
	} else {
		$theme = esc_url( get_stylesheet_directory_uri() );
		$url   = $theme . $url;
	}

	$ext = pathinfo( $url, PATHINFO_EXTENSION );

	if ( 'svg' === $ext ) {
		$upload_dir = wp_get_upload_dir();

		if ( 0 === strpos( $url, $upload_dir['baseurl'] ) ) {
			$file_path = str_replace( $upload_dir['baseurl'], $upload_dir['basedir'], $url );
		} elseif ( 0 === strpos( $url, get_stylesheet_directory_uri() ) ) {
			$file_path = str_replace( get_stylesheet_directory_uri(), get_stylesheet_directory(), $url );
		} else {
			$file_path = $url;
		}

		$svg_content = @file_get_contents( $file_path );

		if ( false !== $svg_content ) {
			$output .= $svg_content;
		} else {
			// Handle file_get_contents error.
			error_log( "Error fetching SVG content from: $file_path" );
		}
	} else {
		$output .= '<img src="' . esc_url( $url ) . '" alt="Icon">';
	}

	if ( $href ) {
		$output .= '</a>';
	}

	return $output;
}

/**
 * Build a button (or button-styled span/link) with an arrow icon.
 *
 * @param string|array|false $href URL string, an array with a 'url' key, or false for no link.
 * @param string             $text   Button label.
 * @param string             $css_class Optional CSS class.
 * @param string             $id        Optional ID.
 * @param string             $target    Optional link target.
 * @param string             $icon      Optional inline SVG markup shown next to the label.
 * @param string             $aria_label Optional aria-label, for icon/short-text buttons.
 * @param string             $icon_position Where $icon sits relative to $text: 'before' (default) or 'after'.
 * @param bool               $show_arrow Whether to append the trailing chevron on solid/outline/text buttons. Default true.
 * @return string Button HTML markup.
 */
function add_button( $href, $text, $css_class = '', $id = '', $target = '', $icon = '', $aria_label = '', $icon_position = 'before', $show_arrow = false ) {
	$output = '';  // Initialize $output.

	if ( is_array( $href ) && isset( $href['url'] ) && '' !== $href['url'] ) {
		$url = $href['url'];
	} elseif ( is_string( $href ) && '' !== $href ) {
		$url = $href;
	} else {
		$url = '#';
	}

	$target = empty( $target ) ? '_self' : $target;

	// Known style/colour keywords become BEM modifiers (c-button--x);
	// anything else (js- hooks, "mail", "callus") passes through as-is.
	// Colour tokens match the shared background_colour/button_colour choice
	// list exactly (field_617ea04499fac / field_63ee393743836 in
	// group-618080750e91b.json) - a button can always be set to the same
	// colour as any section background. Legacy previous-client colours
	// (pink/pink-light/grey-dark/grey-light) removed from the palette.
	$modifiers = array( 'solid', 'outline', 'text', 'white', 'blue', 'orange', 'navy', 'eyebrow' );
	$tokens    = array_filter( explode( ' ', $css_class ) );
	$classes   = array( 'c-button' );
	foreach ( $tokens as $token ) {
		$classes[] = in_array( $token, $modifiers, true ) ? 'c-button--' . $token : $token;
	}
	$class_attr = implode( ' ', $classes );
	$aria_attr  = $aria_label ? ' aria-label="' . esc_attr( $aria_label ) . '"' : '';

	if ( $url ) {
		$output .= '<a href="' . $url . '" class="' . $class_attr . '" id="' . $id . '" target="' . $target . '"' . $aria_attr . '>';
	} else {
		$output .= '<span class="' . $class_attr . '" id="' . $id . '"' . $aria_attr . '>';
	}

	// --solid and --outline both draw their corners on ::before via a mask
	// (see _buttons.scss) and --text has no corners at all, so all three
	// hide these icons in CSS - skip emitting them there rather than
	// shipping dead, display:none markup. Only the default/plain button
	// still uses a real corner icon.
	$show_corner_icons = ! in_array( 'solid', $tokens, true ) && ! in_array( 'text', $tokens, true ) && ! in_array( 'outline', $tokens, true );
	$icon_id           = 'arrow-icon';

	if ( $show_corner_icons ) {
		$output .= '<svg class="c-button__icon c-button__icon--start" aria-hidden="true"><use href="#' . $icon_id . '"></use></svg>';
	}
	// .c-button__inner is a flex row, so the icon just needs to sit on the
	// correct side of $text in markup order - no extra CSS required.
	$icon_markup = $icon ? '<span class="c-button__icon-inline" aria-hidden="true">' . $icon . '</span>' : '';

	$output .= '<div class="c-button__inner">';
	if ( $icon_markup && 'after' !== $icon_position ) {
		$output .= $icon_markup;
	}
	$output .= $text;
	if ( $icon_markup && 'after' === $icon_position ) {
		$output .= $icon_markup;
	}
	if ( $show_arrow && ( in_array( 'solid', $tokens, true ) || in_array( 'outline', $tokens, true ) || in_array( 'text', $tokens, true ) ) ) {
		// No clip-path/mask needed - that was just how Figma exported it,
		// not anything the icon actually relies on. stroke="currentColor"
		// (not the #DF0E7B the design gave us) so the arrow still picks up
		// each button's own colour (pink/yellow/grey/white/etc.) rather
		// than always being pink.
		// $output .= '<svg class="c-button__arrow" width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M7.04004 10.9027L10.3903 6.61927C10.7116 6.05324 10.7116 5.34953 10.3903 4.7835L7.04004 0.500061" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/><path d="M6.5 5.70886H0.5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/></svg>';
	}
	$output .= '</div>';
	if ( $show_corner_icons ) {
		$output .= '<svg class="c-button__icon c-button__icon--end" aria-hidden="true"><use href="#' . $icon_id . '"></use></svg>';
	}

	if ( $url ) {
		$output .= '</a>';
	} else {
		$output .= '</span>';
	}

	return $output;
}

/**
 * Extract YouTube video ID from URL
 *
 * @param string $url YouTube URL.
 * @return string|null YouTube video ID or null.
 */
function get_youtube_id( $url ) {
	if ( ! $url ) {
		return null;
	}

	// Regular expressions for YouTube URLs including /shorts/.
	$reg_exp = '/^.*(youtu.be\/|v\/|u\/\w\/|embed\/|shorts\/|watch\?v=|\&v=)([^#\&\?]*).*/';
	$match   = array();
	preg_match( $reg_exp, $url, $match );

	return ( isset( $match[2] ) && strlen( $match[2] ) === 11 ) ? $match[2] : null;
}

/**
 * Extract Vimeo video ID from URL
 *
 * @param string $url Vimeo URL.
 * @return string|null Vimeo video ID or null.
 */
function get_vimeo_id( $url ) {
	if ( ! $url ) {
		return null;
	}

	// Regular expressions for Vimeo URLs.
	$reg_exp = '/^.*(vimeo\.com\/)((channels\/[A-z]+\/)|(groups\/[A-z]+\/videos\/))?([0-9]+)/';
	$match   = array();
	preg_match( $reg_exp, $url, $match );

	return isset( $match[5] ) ? $match[5] : null;
}

/**
 * Render a video embed (YouTube/Vimeo/HTML5) for the `video` content block
 * (see blocks/video/block-video.php). Pulled out into one helper so the
 * landscape and portrait branches share it instead of duplicating the same
 * markup/param-building twice.
 *
 * @param array $args {
 *     Video embed args.
 *
 *     @type string $video_type   'youtube' | 'vimeo' | 'html5'.
 *     @type string $video_url    YouTube/Vimeo URL (youtube/vimeo only).
 *     @type array  $video_file   ACF file field value (html5 only).
 *     @type array  $video_poster ACF image field value (html5 only).
 *     @type bool   $autoplay     Whether to autoplay.
 *     @type bool   $loop         Whether to loop.
 *     @type bool   $muted        Whether to start muted.
 *     @type bool   $controls     Whether to show player controls.
 *     @type bool   $portrait     Whether to add the portrait-ratio modifier.
 * }
 * @return string HTML markup, or '' if there's nothing playable.
 */
function freckle_render_video_embed( $args ) {
	$video_type   = $args['video_type'] ?? null;
	$video_url    = $args['video_url'] ?? null;
	$video_file   = $args['video_file'] ?? null;
	$video_poster = $args['video_poster'] ?? null;
	$autoplay     = ! empty( $args['autoplay'] );
	$loop         = ! empty( $args['loop'] );
	$muted        = ! empty( $args['muted'] );
	$controls     = ! empty( $args['controls'] );
	$portrait     = ! empty( $args['portrait'] );

	if ( ! $video_type || ( ! $video_url && ! $video_file ) ) {
		return '';
	}

	$embed_class = 'b-video__embed' . ( $portrait ? ' b-video__embed--portrait' : '' );

	ob_start();

	if ( 'youtube' === $video_type && ! empty( $video_url ) ) {
		$youtube_id = get_youtube_id( $video_url );
		$params     = array_filter(
			array(
				'rel'      => '0',
				'showinfo' => '0',
				'autoplay' => $autoplay ? '1' : null,
				'loop'     => $loop ? '1' : null,
				'playlist' => $loop ? $youtube_id : null,
				'mute'     => $muted ? '1' : null,
				'controls' => ! $controls ? '0' : null,
			)
		);
		?>
		<div class="<?php echo esc_attr( $embed_class ); ?> b-video__embed--youtube">
			<iframe
				src="https://www.youtube.com/embed/<?php echo esc_attr( $youtube_id ); ?>?<?php echo esc_attr( http_build_query( $params ) ); ?>"
				title="Video"
				frameborder="0"
				allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
				allowfullscreen
			></iframe>
		</div>
		<?php
	} elseif ( 'vimeo' === $video_type && ! empty( $video_url ) ) {
		$params = array_filter(
			array(
				'autoplay' => $autoplay ? '1' : null,
				'loop'     => $loop ? '1' : null,
				'muted'    => $muted ? '1' : null,
				'controls' => $controls ? '1' : '0',
			)
		);
		?>
		<div class="<?php echo esc_attr( $embed_class ); ?> b-video__embed--vimeo">
			<iframe
				src="https://player.vimeo.com/video/<?php echo esc_attr( get_vimeo_id( $video_url ) ); ?>?<?php echo esc_attr( http_build_query( $params ) ); ?>"
				title="Video"
				frameborder="0"
				allow="autoplay; fullscreen"
				allowfullscreen
			></iframe>
		</div>
		<?php
	} elseif ( 'html5' === $video_type && ! empty( $video_file ) ) {
		?>
		<div class="<?php echo esc_attr( $embed_class ); ?> b-video__embed--html5">
			<video
				class="b-video__video"
				<?php echo ! empty( $video_poster['url'] ) ? 'poster="' . esc_url( $video_poster['url'] ) . '"' : ''; ?>
				<?php echo $autoplay ? 'autoplay' : ''; ?>
				<?php echo $loop ? 'loop' : ''; ?>
				<?php echo $muted ? 'muted' : ''; ?>
				<?php echo $controls ? 'controls' : ''; ?>
				playsinline
			>
				<source src="<?php echo esc_url( $video_file['url'] ); ?>" type="video/mp4">
				Your browser does not support the video tag.
			</video>
		</div>
		<?php
	}

	return ob_get_clean();
}

/**
 * Geocode a free-text address to lat/lng via OpenStreetMap's Nominatim API,
 * for the `map` content block (see blocks/map/block-map.php). Cached as a
 * transient per address so we're not hitting Nominatim on every page load -
 * their usage policy requires a low request rate and an identifying User-Agent.
 *
 * @param string $address Free-text address.
 * @return array{lat: float, lng: float}|null Coordinates, or null if geocoding failed.
 */
function freckle_geocode_address( $address ) {
	$address = trim( (string) $address );
	if ( '' === $address ) {
		return null;
	}

	$cache_key = 'imp_geocode_' . md5( $address );
	$cached    = get_transient( $cache_key );
	if ( false !== $cached ) {
		return $cached ? $cached : null;
	}

	$response = wp_remote_get(
		'https://nominatim.openstreetmap.org/search?' . http_build_query(
			array(
				'q'      => $address,
				'format' => 'json',
				'limit'  => 1,
			)
		),
		array(
			'timeout' => 5,
			// Nominatim's usage policy requires an identifying User-Agent.
			'headers' => array(
				'User-Agent' => 'IMP-Theme/1.0 (' . home_url() . ')',
			),
		)
	);

	if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
		// Don't cache transient failures (network blip) - only cache once we
		// know whether the address actually resolves.
		return null;
	}

	$results = json_decode( wp_remote_retrieve_body( $response ), true );
	$coords  = null;
	if ( ! empty( $results[0]['lat'] ) && ! empty( $results[0]['lon'] ) ) {
		$coords = array(
			'lat' => (float) $results[0]['lat'],
			'lng' => (float) $results[0]['lon'],
		);
	}

	// Cache both hits and confirmed misses for 30 days - `false` (ACF/WP's
	// "not set" sentinel) is stored as a literal 0/1 flag below since
	// get_transient() can't distinguish "not cached" from "cached false".
	set_transient( $cache_key, $coords ? $coords : 0, 30 * DAY_IN_SECONDS );

	return $coords;
}

// A hand-rolled BreadcrumbList schema function used to live here (added by
// Freckle Creative Ltd, 15 Jul 2025, before any SEO plugin was installed).
// Removed now that Yoast is active and generating its own, accurate
// breadcrumb schema from real category taxonomy - the old version always
// hardcoded "Blog" regardless of a post's actual category, and was already
// dead code by the time it was removed (guarded to stand down whenever
// Yoast is present).

/**
 * [current_year] shortcode - drop into any WYSIWYG field (e.g. a footer
 * copyright line) instead of hardcoding the year, so it never goes stale.
 *
 * @return string Current 4-digit year.
 */
function portal_current_year_shortcode() {
	return esc_html( gmdate( 'Y' ) );
}
add_shortcode( 'current_year', 'portal_current_year_shortcode' );
