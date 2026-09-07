<?php
/**
 * Theme bootstrap: loads the Composer autoloader and theme includes.
 *
 * @package Boilerplate
 */

defined( 'ABSPATH' ) || exit;

// require_once __DIR__ . '/vendor/autoload.php';

// Functions.
foreach ( glob( get_template_directory() . '/includes/functions/*.php' ) as $file ) {
	require_once $file;
}

// // Custom Post Types
foreach ( glob( get_template_directory() . '/includes/post_types/*.php' ) as $file ) {
	require_once $file;
}

// // Custom Categories & Tags
foreach ( glob( get_template_directory() . '/includes/taxonomies/*.php' ) as $file ) {
	require_once $file;
}

// Menu Walkers.
foreach ( glob( get_template_directory() . '/includes/menu_walkers/*.php' ) as $file ) {
	require_once $file;
}

require 'includes/admin-settings.php';
require 'includes/functions.php';
require 'includes/image-management.php';
require 'includes/asset-management.php';
require 'includes/theme-settings.php';
