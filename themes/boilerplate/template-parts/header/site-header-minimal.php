<?php
/**
 * Site header variant: minimal - just logo + menu toggle, full nav (and
 * the "Contact us" action) live in the off-canvas panel at every width,
 * not just on mobile like the standard/centered headers.
 *
 * @package Boilerplate
 */

defined( 'ABSPATH' ) || exit();
?>

<div class="o-site-header o-site-header--minimal js-site-header">
	<div class="o-site-header__overlay"></div>
	<div class="o-site-header__inner">
		<?php
		get_template_part(
			'template-parts/components/component-site',
			'branding',
		);
		?>

		<nav id="site-navigation" class="o-primary-nav o-primary-nav--offcanvas" role="navigation">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'menu-1',
					'menu_id'        => 'primary-menu',
					'walker'         => new Simple_Menu_Walker(),
					// No container div, and the flex-list styling
					// (_navigation.scss) targets this class directly on
					// the <ul> itself - matching how site-header-centered.
					// php's hand-written <ul class="o-primary-nav__half">
					// is structured, rather than requiring <li> to be a
					// direct child of the outer <nav>.
					'container'      => false,
					'menu_class'     => 'o-primary-nav__offcanvas-list',
					'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s<li class="o-primary-nav__cta">' . add_button( '#contact-popup', 'Get in touch', 'solid orange js-modal-trigger', '', '' ) . '</li></ul>',
				)
			);
			?>
		</nav>

		<?php get_template_part( 'template-parts/components/component', 'hamburger' ); ?>
	</div>
</div>
