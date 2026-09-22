<?php
/**
 * Site header dispatcher - loads the header variant chosen by the
 * sitewide `header_style` option (Options > Header). Defaults to
 * "standard" if the option is unset.
 *
 * @package Boilerplate
 */

defined( 'ABSPATH' ) || exit();

$header_style = get_field( 'header_style', 'options' ) ?: 'standard';
$allowed      = array( 'standard', 'centered', 'minimal' );

if ( ! in_array( $header_style, $allowed, true ) ) {
	$header_style = 'standard';
}

get_template_part( 'template-parts/header/site-header-' . $header_style );
