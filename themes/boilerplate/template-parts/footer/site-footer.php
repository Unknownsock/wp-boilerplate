<?php

/**
 * Site footer template part.
 *
 * @package Boilerplate
 */

	defined( 'ABSPATH' ) || exit;

	// Groups.
	$company_details = get_field( 'company_details', 'option' );

	// Singles.
	$company_name = $company_details['company_name'] ?? null;
	$phone        = $company_details['company_telephone'] ?? null;
	$email        = $company_details['company_email'] ?? null;
	$addresses    = $company_details['addresses'] ?? null;

	// Falls back to the single legacy "company_address" repeater as one
	// address if the newer multi-address "addresses" field is empty -
	// keeps older content working without a forced re-entry.
	if ( ! $addresses ) {
		$legacy_address = $company_details['company_address'] ?? null;
		if ( $legacy_address ) {
			$addresses = array(
				array(
					'address_lines' => $legacy_address,
				),
			);
		}
	}

?>

<section class="o-site-footer js-site-footer">
	<div class="o-site-footer__main">
		<div class="o-site-footer__top">
			<div class="o-site-footer__brand-col">
				<?php
					get_template_part(
						'template-parts/components/component',
						'site-branding'
					);
					?>
				<?php if ( $addresses ) : ?>
					<?php foreach ( $addresses as $address ) : ?>
						<?php
						$address_lines = $address['address_lines'] ?? null;
						if ( ! $address_lines ) {
							continue;
						}
						?>
						<div class="o-site-footer__address-block">
							<address class="o-site-footer__address">
								<?php
								$line_index = 0;
								foreach ( $address_lines as $row ) {
									$line = $row['address_line'] ?? '';
									if ( ! $line ) {
										continue;
									}
									// First line stands in for a heading (orange) -
									// matches the rest of the footer's accent styling.
									if ( 0 === $line_index ) {
										echo '<span class="o-site-footer__address-first">' . esc_html( $line ) . '</span><br>';
									} else {
										echo esc_html( $line ) . '<br>';
									}
									++$line_index;
								}
								?>
							</address>
						</div>
					<?php endforeach; ?>
				<?php endif; ?>
				<?php if ( $phone || $email ) : ?>
					<p class="o-site-footer__contact-details">
						<?php if ( $phone ) : ?>
							<a href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a><br>
						<?php endif; ?>
						<?php if ( $email ) : ?>
							<a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a>
						<?php endif; ?>
					</p>
				<?php endif; ?>
			</div>

			<div class="o-site-footer__menu o-site-footer__menu--links">
				<nav>
					<?php
						wp_nav_menu(
							array(
								'theme_location' => 'menu-2',
								'menu_id'        => 'footer-menu',
								'container'      => false,
								'fallback_cb'    => false,
							)
						);
						?>
				</nav>
			</div>

			<div class="o-site-footer__menu o-site-footer__menu--links">
				<nav>
					<?php
						wp_nav_menu(
							array(
								'theme_location' => 'menu-3',
								'menu_id'        => 'footer-menu-2',
								'container'      => false,
								'fallback_cb'    => false,
							)
						);
						?>
				</nav>
			</div>
		</div>
	</div>
	<div class="o-site-footer__bottom">
		<div class="o-site-footer__copyright">
			<p>&copy; <?php echo gmdate( 'Y' ) . ' ' . esc_html( $company_name ?: get_bloginfo( 'name' ) ); ?>. All rights reserved.</p>
		</div>
		<div class="o-site-footer__brand">
			<?php get_template_part( 'template-parts/components/component', 'socials' ); ?>
		</div>
	</div>
</section>

<?php
	get_template_part(
		'template-parts/components/component-modal',
		'contact'
	);
	get_template_part(
		'template-parts/components/component-social',
		'sticky'
	);
	?>
