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
		$name     = str_replace( '_', '-', get_row_layout() );
		$block_id = get_sub_field( 'block_id' );

		// Blocks converted to their own folder keep everything - php,
		// scss - together; anything not yet converted still lives as a
		// flat file directly in template-parts/content/blocks/.
		$slug = 'template-parts/content/blocks/' . $name . '/block-' . $name;
		if ( ! locate_template( $slug . '.php' ) ) {
			$slug = 'template-parts/content/blocks/block-' . $name;
		}

		if ( $block_id ) {
			// Every block template renders its own <section class="..."> as
			// the first thing it outputs (see CLAUDE.md's BEM naming
			// convention) - buffering lets the editor-set "Block ID" field
			// become a real id="" attribute on that section, so pages can
			// link to it (#block-id), without every block template having
			// to read/render the field itself.
			ob_start();
			get_template_part( $slug );
			$html = ob_get_clean();
			echo preg_replace( '/<section\b/', '<section id="' . esc_attr( $block_id ) . '"', $html, 1 );
		} else {
			get_template_part( $slug );
		}

	endwhile;
	endif;
