<?php
/**
 * Nav menu walker for the header navigation.
 *
 * @package Boilerplate
 */

defined( 'ABSPATH' ) || exit();

/**
 * Nav menu walker that renders the header dropdown menu markup.
 *
 * Depth-1 dropdown items are grouped into flex "mega menu" columns via the
 * `mega_start_column` / `mega_column_title` / `mega_column_text` ACF fields
 * on each item, and the depth-0 (top-level) item's own `mega_cta_*` fields
 * append a CTA card after the last column. See group_68b90001megamenu.
 */
class Custom_Menu_Walker extends Walker_Nav_Menu {

	/**
	 * ID of the top-level item whose dropdown is currently being rendered.
	 *
	 * @var int
	 */
	protected $current_parent_id = 0;

	/**
	 * Whether a `.o-primary-nav__dropdown-column` is currently open and
	 * needs closing before the next one starts (or the dropdown ends).
	 *
	 * @var bool
	 */
	protected $column_open = false;

	/**
	 * Whether the next depth-1 item is the first in its dropdown - always
	 * starts column 1, regardless of its own `mega_start_column` value.
	 *
	 * @var bool
	 */
	protected $is_first_dropdown_item = true;

	/**
	 * Count of top-level (depth-0) items rendered so far - used to detect
	 * the first one, so it always gets the home icon regardless of what
	 * it links to.
	 *
	 * @var int
	 */
	protected $current_item_index = 0;

	/**
	 * Start the markup for a new submenu level.
	 *
	 * @param string   $output Passed by reference. Used to append additional content.
	 * @param int      $depth  Depth of the menu item.
	 * @param stdClass $args   Wp_nav_menu() arguments.
	 * @return void
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ) {
		if ( 0 === $depth ) {
			// Opening a top-level item's dropdown - reset the column tracking
			// state for it and open the mega menu wrappers.
			$this->column_open            = false;
			$this->is_first_dropdown_item = true;

			$output .=
				'<ul class="o-primary-nav__dropdown js-nav-dropdown" aria-sub-expanded="false"><div class="o-primary-nav__dropdown-inner"><div class="o-primary-nav__dropdown-columns">';
			return;
		}

		// Deeper levels (links nested under a column item) render as a plain
		// list - no column/CTA wrappers, styled via nesting in _navigation.scss.
		$output .= '<ul>';
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
		if ( 0 !== $depth ) {
			$output .= '</ul>';
			return;
		}

		// Close the last open column, append the CTA card (if any), then
		// close the mega menu wrappers opened in start_lvl().
		if ( $this->column_open ) {
			$output           .= '</div>';
			$this->column_open = false;
		}

		$output .= $this->get_cta_markup( $this->current_parent_id );
		$output .= '</div></div></ul>';
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
		if ( 0 === $depth ) {
			$this->current_parent_id = $item->ID;
		} elseif ( 1 === $depth ) {
			$output .= $this->maybe_start_column( $item );
		}

		$object      = $item->object;
		$type        = $item->type;
		$title       = $item->title;
		$description = $item->description;
		$permalink   = $item->url;

		// A standalone home item always leads the top level, in its own
		// <li> ahead of whatever the first real menu item is.
		if ( 0 === $depth && 0 === $this->current_item_index ) {
			$output .= '<li class="o-primary-nav__home-item"><a href="' . esc_url( home_url( '/' ) ) . '" aria-label="' . esc_attr__( 'Home', 'freckle' ) . '"><svg class="o-primary-nav__home-icon" width="13" height="11" viewBox="0 0 13 11" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M6.5 0L0 4.71429H1.625V11H4.875V7.85714H8.125V11H11.375V4.66622L13 4.71429L6.5 0Z" fill="currentColor"/></svg></a></li>';
		}

		if ( 0 === $depth ) {
			++$this->current_item_index;
		}

		$output .= '<li class="' . implode( ' ', $item->classes ) . '">';

		// Add SPAN if no Permalink.
		if ( $permalink ) {
			$output .= '<a href="' . $permalink . '">';
		} else {
			$output .= '<span>';
		}

		$output .= '<span class="o-primary-nav__label">' . $title . '</span>';

		if ( $args->walker->has_children ) {
			// Screen readers get no indication a dropdown exists otherwise -
			// this div is the actual click target menuDropdown() (navigation.js)
			// binds to (the trigger's own <a> still just navigates), so it
			// needs real disclosure-widget semantics of its own:
			// role="button"/tabindex so it's keyboard-reachable, aria-haspopup
			// so its purpose is announced, and aria-expanded kept in sync with
			// the dropdown's own open/closed state in JS. The svg arrow inside
			// it is now purely decorative given the label below.
			$arrow =
				'<svg width="12" height="5" viewBox="0 0 12 5" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M0.5 0.500023L4.76649 3.83703C5.33028 4.15702 6.0312 4.15702 6.59499 3.83703L10.8615 0.500023" stroke="#DF0E7B" stroke-linecap="round" stroke-linejoin="round"/></svg>';

			$output .=
				'<div class="o-primary-nav__arrow js-nav-arrow" role="button" tabindex="0" aria-haspopup="true" aria-expanded="false" aria-label="' .
				esc_attr( sprintf( 'Toggle submenu for %s', wp_strip_all_tags( $title ) ) ) .
				'">' .
				$arrow .
				'</div>';
		}

		if ( $permalink ) {
			$output .= '</a>';
		} else {
			$output .= '</span>';
		}
	}

	/**
	 * Work out whether a depth-1 item should start a new mega menu column
	 * and, if so, return the markup that closes the previous column (if
	 * one's open) and opens the new one, including its optional title/text.
	 *
	 * @param WP_Post $item Menu item data object.
	 * @return string
	 */
	protected function maybe_start_column( $item ) {
		$starts_column = $this->is_first_dropdown_item || (bool) get_field( 'mega_start_column', $item->ID );

		$this->is_first_dropdown_item = false;

		if ( ! $starts_column ) {
			return '';
		}

		$markup = $this->column_open ? '</div>' : '';

		$this->column_open = true;

		$markup .= '<div class="o-primary-nav__dropdown-column">';

		$column_title = get_field( 'mega_column_title', $item->ID );
		$column_text  = get_field( 'mega_column_text', $item->ID );

		if ( $column_title || $column_text ) {
			$markup .= '<div class="o-primary-nav__dropdown-column-head">';
			if ( $column_title ) {
				$markup .= '<p class="o-primary-nav__dropdown-column-title">' . esc_html( $column_title ) . '</p>';
			}
			if ( $column_text ) {
				// Same plain line as the CTA card's own divider (not an
				// svg asset) - now sits between the title and text
				// instead of as a border under the text.
				$markup .= '<div class="o-primary-nav__dropdown-column-divider" aria-hidden="true"></div>';
				$markup .= '<p class="o-primary-nav__dropdown-column-text">' . esc_html( $column_text ) . '</p>';
			}
			$markup .= '</div>';
		}

		return $markup;
	}

	/**
	 * Build the CTA card markup for a top-level item, using its
	 * `mega_cta_*` fields. Returns an empty string when there's nothing to
	 * show, so dropdowns without a CTA configured don't get an empty card.
	 *
	 * @param int $parent_id Top-level menu item ID.
	 * @return string
	 */
	protected function get_cta_markup( $parent_id ) {
		if ( ! $parent_id ) {
			return '';
		}

		$title = get_field( 'mega_cta_title', $parent_id );
		$image = get_field( 'mega_cta_image', $parent_id );

		if ( ! $title && ! $image ) {
			return '';
		}

		$tag  = get_field( 'mega_cta_tag', $parent_id );
		$text = get_field( 'mega_cta_text', $parent_id );
		$link = get_field( 'mega_cta_link', $parent_id );

		$markup = '<div class="o-primary-nav__dropdown-cta">';

		if ( $image ) {
			$markup .= '<div class="o-primary-nav__dropdown-cta-media">';
			$markup .= '<img src="' . esc_url( $image['url'] ) . '" alt="' . esc_attr( $image['alt'] ) . '" loading="lazy">';
			$markup .= '</div>';
		}

		$markup .= '<div class="o-primary-nav__dropdown-cta-body">';

		if ( $image ) {
			// Torn-edge overlay, painted over the seam between the media
			// and this body - same svg/technique as .c-card__image-shape,
			// using the --large card's own asset (block-cards.php), not
			// the small article card's.
			$markup .= '<svg class="o-primary-nav__dropdown-cta-image-shape" viewBox="0 0 1139 608" aria-hidden="true" focusable="false"><path fill-rule="evenodd" clip-rule="evenodd" fill="currentColor" d="M1138.03 357.291L951.9 575.169V575.342C931.526 599.222 899.78 610.595 868.554 605.169L228.678 493.236C197.479 487.81 171.599 466.303 160.772 437.01L0 0V607.996H1138.66L1138.03 357.291Z"></path></svg>';
		}

		if ( $tag ) {
			$markup .= '<p class="o-primary-nav__dropdown-cta-tag">' . $tag . '</p>';
			// Underline, directly under the tag - plain div, not an svg
			// asset, so it genuinely stretches to the card's actual width
			// (see _navigation.scss) rather than just visually scaling.
			$markup .= '<div class="o-primary-nav__dropdown-cta-divider" aria-hidden="true"></div>';
		}

		if ( $title ) {
			$markup .= '<p class="o-primary-nav__dropdown-cta-title">' . $title . '</p>';
		}

		if ( $text || ! empty( $link['url'] ) ) {
			// Wrapped so both are hidden until the card is hovered (see
			// _navigation.scss) - the tag/title above stay visible at rest.
			$markup .= '<div class="o-primary-nav__dropdown-cta-reveal">';

			if ( $text ) {
				$markup .= '<p class="o-primary-nav__dropdown-cta-text">' . esc_html( $text ) . '</p>';
			}

			if ( ! empty( $link['url'] ) ) {
				// Real site button component (functions.php add_button()),
				// styled as the underlined "text" variant - same one used
				// elsewhere for a link-style CTA - instead of a plain <a>, so
				// it gets the standard arrow icon and hover treatment for
				// free. add_button() accepts the ACF link field's own array
				// shape directly ('url'/'target' keys), so $link is passed
				// through as-is.
				$markup .= '<div class="o-primary-nav__dropdown-cta-link">' . add_button( $link, $link['title'], 'text orange', '', $link['target'] ?? '' ) . '</div>';
			}

			$markup .= '</div>';
		}

		$markup .= '</div></div>';

		return $markup;
	}
}
