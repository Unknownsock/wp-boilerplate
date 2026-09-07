<?php
/**
 * Site header template part.
 *
 * @package Boilerplate
 */

defined( 'ABSPATH' ) || exit();

// Groups.
$social_media = get_field( 'social_media', 'option' );

// Singles.
$tiktok    = $social_media['tiktok'] ?? null;
$facebook  = $social_media['facebook'] ?? null;
$instagram = $social_media['instagram'] ?? null;
$linkedin  = $social_media['linkedin'] ?? null;
?>

<div class="o-site-header js-site-header">
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
				// 'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s<li class="social"><a href="' . $tiktok['url'] . '" target="_blank" aria-label="Tik Tok"><svg width="14" height="15" viewBox="0 0 14 15" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M13.081 6.17763V3.59685C13.081 3.59685 11.7168 3.60362 10.5648 2.54818C9.59673 1.66127 9.51615 0 9.51615 0H6.93538C6.93538 0 6.93538 8.80638 6.93538 10.2099C6.93538 11.6129 5.71449 12.3393 4.83865 12.3393C4.17869 12.3393 2.67734 11.822 2.67734 10.1939C2.67734 8.48532 4.40319 8.04863 4.85464 8.04863C5.30609 8.04863 5.48384 8.14519 5.48384 8.14519V5.42111C5.48384 5.42111 5.06438 5.38113 4.75808 5.37067C2.15331 5.28272 0 7.61994 0 10.1933C0 12.3676 1.70063 15 4.80667 15C8.11506 15 9.62932 12.2409 9.62932 10.2093C9.62932 8.72519 9.62932 5.14495 9.62932 5.14495C9.62932 5.14495 10.5808 5.67697 11.3552 5.9193C12.1295 6.16164 13.081 6.17763 13.081 6.17763Z" fill="#DF0E7B"/></svg></a><a href="' . $facebook['url'] . '" target="_blank" aria-label="Facebook"><svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 10.7 22" style="enable-background:new 0 0 10.7 22;" xml:space="preserve"> <path id="facebook" class="st0" d="M2.7,22V11.7H0V8h2.7V4.8C2.7,2.3,4.3,0,8.1,0c0.9,0,1.7,0,2.6,0.1l-0.1,3.5c0,0-1.1,0-2.4,0 c-1.3,0-1.6,0.6-1.6,1.6V8h4l-0.2,3.7H6.7V22H2.7"/></svg></a><a href="' . $instagram['url'] . '" target="_blank" aria-label="Instagram"><svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 16 16" enable-background="new 0 0 16 16" xml:space="preserve"> <g id="Group_62" transform="translate(0.001 -0.24)"> <g id="Group_59" transform="translate(-0.001 0.24)"> <path id="Path_37" fill="#2C363D" d="M11.4,16H4.6C2,16,0,14,0,11.4V4.6C0,2,2,0,4.6,0h6.9C14,0,16,2,16,4.6v6.9 C16,14,14,16,11.4,16 M4.6,1.4c-1.8,0-3.2,1.4-3.2,3.2v6.9c0,1.8,1.4,3.2,3.2,3.2h6.9c1.8,0,3.2-1.4,3.2-3.2V4.6 c0-1.8-1.4-3.2-3.2-3.2L4.6,1.4z"/> <path id="Path_38" fill="#2C363D" d="M8,12.1c-2.3,0-4.1-1.8-4.1-4.1c0-2.3,1.8-4.1,4.1-4.1c2.3,0,4.1,1.8,4.1,4.1 S10.3,12.1,8,12.1L8,12.1 M8,5.3C6.5,5.3,5.3,6.5,5.3,8c0,1.5,1.2,2.7,2.7,2.7c1.5,0,2.7-1.2,2.7-2.7C10.7,6.5,9.5,5.3,8,5.3"/> <path id="Path_39" fill="#2C363D" d="M13.3,3.7c0,0.5-0.4,1-1,1s-1-0.4-1-1s0.4-1,1-1l0,0C12.8,2.7,13.3,3.1,13.3,3.7"/> </g> </g> </svg></a><a href="' . $linkedin['url'] . '" target="_blank" aria-label="Linkedin"><svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 25.3 24.2" enable-background="new 0 0 25.3 24.2" xml:space="preserve"> <path id="linkedin" d="M5.7,24.2V7.9H0.3v16.3C0.3,24.2,5.7,24.2,5.7,24.2z M3,5.6c1.6,0.1,2.9-1,3.1-2.6s-1-2.9-2.6-3 C3.4,0,3.2,0,3.1,0C1.5-0.1,0.1,1,0,2.6s1,2.9,2.6,3.1C2.7,5.6,2.9,5.6,3,5.6L3,5.6L3,5.6z M8.7,24.2h5.4v-9.1c0-0.4,0-0.9,0.2-1.3 c0.4-1.2,1.5-2,2.8-2c2,0,2.7,1.5,2.7,3.7v8.7h5.4v-9.4c0-5-2.7-7.3-6.2-7.3c-2-0.1-3.9,1-4.9,2.8l0,0V7.9H8.7 C8.8,9.4,8.7,24.2,8.7,24.2L8.7,24.2z"/> </svg></a></li></ul>',
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
