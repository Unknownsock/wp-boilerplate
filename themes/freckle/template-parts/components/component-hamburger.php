<?php
/**
 * Component: mobile navigation toggle.
 *
 * @package Boilerplate
 */

	defined( 'ABSPATH' ) || exit;

	$hamburger = $args['hamburger'] ?? null;

?>

<button class="hamburger menu-toggle jcf-ignore js-menu-toggle" type="button" aria-label="Toggle main menu" aria-controls="main-navigation" aria-expanded="false">
	<div class="inner">
		<span class="top"></span>
		<span class="middle"></span>
		<span class="bottom"></span>
	</div>
</button>
