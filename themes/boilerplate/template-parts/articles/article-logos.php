<?php
/**
 * Article partial: logo card layout.
 *
 * @package Boilerplate
 */

	defined( 'ABSPATH' ) || exit;

	global $post;

	$logos_options = get_field( 'logos_options', $post );
	$title         = get_the_title();
	$icon          = $logos_options['featured_icon'] ?? null;
	$image         = $logos_options['image'] ?? null;
	$link          = $logos_options['link'] ?? null;

?>
<article class="swiper-slide">
</article>
