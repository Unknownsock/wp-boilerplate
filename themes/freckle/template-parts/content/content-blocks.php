<?php
/**
 * Loops the ACF content blocks field and loads each block template.
 *
 * @package Boilerplate
 */

	$id = $args['id'] ?? null;

if ( have_rows( 'content_blocks', $id ) ) :
	while ( have_rows( 'content_blocks', $id ) ) :
		the_row();
		$name = str_replace( '_', '-', get_row_layout() );

		// Blocks converted to their own folder keep everything - php,
		// scss - together; anything not yet converted still lives as a
		// flat file directly in template-parts/content/blocks/.
		$slug = 'template-parts/content/blocks/' . $name . '/block-' . $name;
		if ( ! locate_template( $slug . '.php' ) ) {
			$slug = 'template-parts/content/blocks/block-' . $name;
		}

		get_template_part( $slug );

	endwhile;
	endif;
