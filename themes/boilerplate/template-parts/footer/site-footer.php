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
	$offices      = $company_details['offices'] ?? null;

	// Falls back to the single legacy "company_address" repeater as one
	// unlabelled office if the newer multi-office "offices" field is
	// empty - keeps older content working without a forced re-entry.
	if ( ! $offices ) {
		$legacy_address = $company_details['company_address'] ?? null;
		if ( $legacy_address ) {
			$offices = array(
				array(
					'country'       => '',
					'flag'          => null,
					'legal_name'    => '',
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
				<?php if ( $offices ) : ?>
					<?php foreach ( $offices as $office ) : ?>
						<?php
						$country       = $office['country'] ?? '';
						$flag          = $office['flag'] ?? null;
						$legal_name    = $office['legal_name'] ?? '';
						$address_lines = $office['address_lines'] ?? null;
						if ( ! $address_lines ) {
							continue;
						}
						?>
						<div class="o-site-footer__address-block">
							<?php if ( ! empty( $flag['url'] ) || $country ) : ?>
								<div class="o-site-footer__address-block-head">
									<?php if ( ! empty( $flag['url'] ) ) : ?>
										<img class="o-site-footer__flag" src="<?php echo esc_url( $flag['url'] ); ?>" alt="" loading="lazy">
									<?php endif; ?>
									<?php if ( $country ) : ?>
										<p class="o-site-footer__menu-title">In the <?php echo esc_html( $country ); ?></p>
									<?php endif; ?>
								</div>
							<?php endif; ?>
							<?php if ( $legal_name ) : ?>
								<p class="o-site-footer__legal-name"><?php echo esc_html( $legal_name ); ?></p>
							<?php endif; ?>
							<address class="o-site-footer__address">
								<?php
								$line_index = 0;
								foreach ( $address_lines as $row ) {
									$line = $row['address_line'] ?? '';
									if ( ! $line ) {
										continue;
									}
									// The first address line stands in for the legal
									// entity name (orange) when that field's left
									// empty - avoids two orange lines when it's set.
									if ( 0 === $line_index && ! $legal_name ) {
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
