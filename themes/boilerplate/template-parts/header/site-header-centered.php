<?php
/**
 * Site header variant: centered logo, nav split either side of it on
 * desktop. Below $grid-nav, the whole nav (both halves plus the logo)
 * becomes one off-canvas panel, same toggle pattern as the other header
 * styles - see .o-site-header--centered in _navigation.scss.
 *
 * @package Boilerplate
 */

defined( 'ABSPATH' ) || exit();

list( $left_items, $right_items ) = boilerplate_split_menu_items( 'menu-1' );

// wp_nav_menu() always resolves its own items from theme_location/menu -
// it has no "render this pre-filtered array" mode - so each half is
// rendered by calling the walker directly instead, wrapped in the same
// <ul class> markup wp_nav_menu() would otherwise produce.
$walker = new Simple_Menu_Walker();
$args   = (object) array(
	'after'       => '',
	'before'      => '',
	'link_after'  => '',
	'link_before' => '',
	'walker'      => $walker,
);
?>

<div class="o-site-header o-site-header--centered js-site-header">
	<div class="o-site-header__overlay"></div>
	<div class="o-site-header__inner">
		<nav id="primary-menu" class="o-primary-nav o-primary-nav--centered" role="navigation">
			<ul class="o-primary-nav__half o-primary-nav__half--left">
				<?php echo $walker->walk( $left_items, 0, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from esc_url()/esc_html()-escaped pieces inside Simple_Menu_Walker. ?>
			</ul>

			<?php
			get_template_part(
				'template-parts/components/component-site',
				'branding',
			);
			?>

			<ul class="o-primary-nav__half o-primary-nav__half--right">
				<?php echo $walker->walk( $right_items, 0, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from esc_url()/esc_html()-escaped pieces inside Simple_Menu_Walker. ?>
				<li class="o-primary-nav__cta"><?php echo add_button( '#contact-popup', 'Get in touch', 'solid orange js-modal-trigger', '', '' ); ?></li>
			</ul>
		</nav>

		<div class="o-site-header__actions">
			<?php
			echo add_button(
				'#contact-popup',
				'Contact us',
				'outline white js-modal-trigger',
				'',
				'',
				'',
				'',
				'before',
				false,
			);
			?>
		</div>
		<?php get_template_part( 'template-parts/components/component', 'hamburger' ); ?>
	</div>
</div>
