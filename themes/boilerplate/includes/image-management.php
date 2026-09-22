<?php
/**
 * Custom image sizes and media handling.
 *
 * @package Boilerplate
 */

defined( 'ABSPATH' ) || exit;

add_image_size( 'gallery', 1920, 1080, true );
add_image_size( 'gallery-mobile', 768, 576, true );
add_image_size( 'gallery-thumb', 276, 176, true );
add_image_size( 'listing', 766, 520, true );

add_filter( 'intermediate_image_sizes', 'remove_default_img_sizes', 10, 1 );
/**
 * Remove image sizes that won't be used, to save server space.
 *
 * @param array $sizes Registered image size names.
 * @return array Filtered image size names.
 */
function remove_default_img_sizes( $sizes ) {
	$targets = array( 'medium', 'medium_large', 'large', '1536x1536', '2048x2048', 'full', 'thumbnail' );
	foreach ( $sizes as $size_index => $size ) {
		if ( in_array( $size, $targets, true ) ) {
			unset( $sizes[ $size_index ] );
		}
	}
	return $sizes;
}

/**
 * Allow thumbnails to be upscaled when cropping. Currently unused; not hooked to image_resize_dimensions.
 *
 * @param bool|array $default_value Default resize dimensions.
 * @param int        $orig_w  Original width.
 * @param int        $orig_h  Original height.
 * @param int        $new_w   New width.
 * @param int        $new_h   New height.
 * @param bool       $crop    Whether to crop.
 * @return array|null Resize dimensions, or null to defer to WordPress' default.
 */
function alx_thumbnail_upscale( $default_value, $orig_w, $orig_h, $new_w, $new_h, $crop ) {
	if ( ! $crop ) {
		return null; // let the WordPress default function handle this.
	}

	$aspect_ratio = $orig_w / $orig_h;
	$size_ratio   = max( $new_w / $orig_w, $new_h / $orig_h );

	$crop_w = round( $new_w / $size_ratio );
	$crop_h = round( $new_h / $size_ratio );

	$s_x = floor( ( $orig_w - $crop_w ) / 2 );
	$s_y = floor( ( $orig_h - $crop_h ) / 2 );

	return array( 0, 0, (int) $s_x, (int) $s_y, (int) $new_w, (int) $new_h, (int) $crop_w, (int) $crop_h );
}
