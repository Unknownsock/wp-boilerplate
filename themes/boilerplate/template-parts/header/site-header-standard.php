<?php
/**
 * Site header variant: standard (logo left, full-width mega-menu nav,
 * actions right).
 *
 * @package Boilerplate
 */

defined( 'ABSPATH' ) || exit();
?>

<div class="o-site-header o-site-header--standard js-site-header">
	<div class="o-site-header__overlay"></div>
	<div class="o-site-header__inner">
		<?php
		get_template_part(
			'template-parts/components/component-site',
			'branding',
		);
		?>
		<nav id="site-navigation" class="o-primary-nav" role="navigation">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'menu-1',
					'menu_id'        => 'primary-menu',
					'walker'         => new Custom_Menu_Walker(),
					// Mobile-only "Get in touch" CTA, appended as a real <li>
					// so it lives inside the slide-out panel itself rather
					// than the header actions - those (.o-site-header__actions)
					// are hidden below $grid-xl. .o-primary-nav__cta hides it
					// again above $grid-nav so desktop doesn't get a
					// duplicate button (see _navigation.scss).
					'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s<li class="o-primary-nav__cta">' . add_button( '#contact-popup', 'Get in touch', 'solid orange js-modal-trigger', '', '' ) . '</li></ul>',
				)
			);
			?>
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
