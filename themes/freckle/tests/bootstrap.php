<?php
/**
 * PHPUnit bootstrap for the theme's plain PHP unit tests.
 *
 * These tests exercise pure helper functions in isolation, without a full
 * WordPress environment. ABSPATH is stubbed only so includes/functions.php
 * doesn't exit on load.
 *
 * @package Boilerplate
 */

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

require_once dirname( __DIR__ ) . '/vendor/autoload.php';

if ( ! function_exists( 'add_action' ) ) {
	/**
	 * No-op stub so functions.php can register its wp_head hook during tests.
	 *
	 * @param mixed ...$args Unused.
	 */
	function add_action( ...$args ) {}
}

require_once dirname( __DIR__ ) . '/includes/functions.php';
