<?php
/**
 * Nav menu walker for the centered and minimal header styles.
 *
 * @package Boilerplate
 */

defined( 'ABSPATH' ) || exit();

/**
 * Flat dropdown menu walker - no mega-menu columns/CTA card, just plain
 * nested `<ul>`s per depth. Shared by the centered header (desktop hover
 * dropdown, same `.js-nav-dropdown` toggle markup/JS as the standard
 * header) and the minimal header (off-canvas panel, where the same nested
 * list becomes a click-to-expand accordion via CSS - see _navigation.scss).
 */
class Simple_Menu_Walker extends Walker_Nav_Menu {

	/**
	 * Start the markup for a new submenu level.
	 *
	 * @param string   $output Passed by reference. Used to append additional content.
	 * @param int      $depth  Depth of the menu item.
	 * @param stdClass $args   Wp_nav_menu() arguments.
	 * @return void
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ) {
		$output .= '<ul class="' . ( 0 === $depth ? 'o-primary-nav__dropdown js-nav-dropdown' : '' ) . '" aria-sub-expanded="false">';
	}

	/**
	 * End the markup for a submenu level.
	 *
	 * @param string   $output Passed by reference. Used to append additional content.
	 * @param int      $depth  Depth of the menu item.
	 * @param stdClass $args   Wp_nav_menu() arguments.
	 * @return void
	 */
	public function end_lvl( &$output, $depth = 0, $args = null ) {
		$output .= '</ul>';
	}

	/**
	 * Start the markup for a single menu item.
	 *
	 * @param string   $output Passed by reference. Used to append additional content.
	 * @param WP_Post  $item   Menu item data object.
	 * @param int      $depth  Depth of the menu item.
	 * @param stdClass $args   Wp_nav_menu() arguments.
	 * @param int      $id     Current item ID.
	 * @return void
	 */
	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$title     = $item->title;
		$permalink = $item->url;

		$output .= '<li class="' . implode( ' ', (array) $item->classes ) . '">';

		if ( $permalink ) {
			$output .= '<a href="' . esc_url( $permalink ) . '">';
		} else {
			$output .= '<span>';
		}

		$output .= '<span class="o-primary-nav__label">' . esc_html( $title ) . '</span>';

		if ( $args->walker->has_children ) {
			// Same disclosure-widget semantics as the standard header's
			// mega-menu walker (menuDropdown()/menuHoverDropdown() in
			// navigation.js bind to this, not the <a> itself).
			$output .= '<div class="o-primary-nav__arrow js-nav-arrow" role="button" tabindex="0" aria-haspopup="true" aria-expanded="false" aria-label="' .
				esc_attr( sprintf( 'Toggle submenu for %s', wp_strip_all_tags( $title ) ) ) .
				'"><svg width="12" height="5" viewBox="0 0 12 5" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M0.5 0.500023L4.76649 3.83703C5.33028 4.15702 6.0312 4.15702 6.59499 3.83703L10.8615 0.500023" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/></svg></div>';
		}

		if ( $permalink ) {
			$output .= '</a>';
		} else {
			$output .= '</span>';
		}
	}
}
