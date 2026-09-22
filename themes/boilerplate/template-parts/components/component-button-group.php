<?php
/**
 * Component: button group.
 *
 * @package Boilerplate
 */

	defined( 'ABSPATH' ) || exit;

	$rows        = $args['button_group'] ?? null;
	$force_stack = ! empty( $args['force_stack'] );
if ( $rows ) {
	echo '<div class="c-button-group' . ( $force_stack ? ' c-button-group--stack' : '' ) . '">';
	foreach ( $rows as $row ) {
		$href = $row['link'] ?? null;

		// If no link from CMS, prevents php errors.
		if ( ! $href || empty( $href['url'] ) ) {
			continue;
		}

		$text          = $href['title'] ?? '';
		$target        = $href['target'] ?? '';
		$button_type   = $row['button_type'] ?? 'solid';
		$button_colour = $row['button_colour'] ?? null;
		$custom_class  = $row['custom_class'] ?? null;
		$icon          = get_inline_icon_markup( $row['icon'] ?? null );
		$icon_position = $row['icon_position'] ?? 'before';

		$classes = implode( ' ', array( $button_type, $button_colour, $custom_class ) );

		// Always show the trailing arrow - the per-button "Hide Arrow"
		// toggle was removed as an editor option.
		echo add_button( $href['url'], $text, $classes, '', $target, $icon, '', $icon_position, true );
	}
	echo '</div>';
}
