<?php
/**
 * Component: icon with optional link and text.
 *
 * @package Boilerplate
 */

	$icon      = $args['icon'] ?? null;
	$link      = $args['link'] ?? null;
	$title     = $args['title'] ?? null;
	$excerpt   = $args['excerpt'] ?? null;
	$slug      = $args['slug'] ?? null;
	$post_type = $args['post_type'] ?? null;
?>
<div class="col-fixed-2 <?php echo $post_type; ?> aos">
	<div class="content">
		<?php
			echo add_icon( $icon['url'], false );
		?>
		<h5><?php echo $title; ?></h5>
		<?php
		if ( $link ) {
			echo add_button( $link, 'Find out more', 'white small', $slug . '-modal' );
		}
		?>
	</div>
</div>
