<?php
/**
 * Content block: client testimonials slider.
 *
 * @package Boilerplate
 */

	defined( 'ABSPATH' ) || exit;

	// Content block.
	$content = get_row( true );
	$block   = 'b-' . str_replace( '_', '-', $content['acf_fc_layout'] ?? '' );

	// Singles.
	$eyebrow      = $content['eyebrow'] ?? null;
	$testimonials = $content['testimonials'] ?? null;

	$rows = $testimonials ? get_field( 'testimonial', $testimonials[0] ) : null;
?>
<section class="<?php echo esc_attr( $block . ' white' ); ?>">
	<?php if ( $eyebrow ) { ?>
		<p class="c-eyebrow c-eyebrow--centre"><?php echo esc_html( $eyebrow ); ?></p>
	<?php } ?>
	<?php if ( $rows ) { ?>
		<div class="swiper swiper-testimonial js-swiper-testimonial">
			<div class="swiper-wrapper">
				<?php
				foreach ( $rows as $row ) {
					$testimonial_text = $row['testimonial_text'] ?? null;
					$client_logo      = $row['client_logo'] ?? null;
					$client_name      = $row['client_name'] ?? null;
					$client_details   = $row['client_details'] ?? null;
					$client_link      = $row['link_to_our_work'] ?? null;

					if ( ! $client_logo && $client_link ) {
						$client_id   = url_to_postid( $client_link );
						$client_logo = $client_id ? get_field( 'client_logo', $client_id ) : null;
					}
					$client_role = $row['client_details'] ?? null;
					?>
					<div class="swiper-slide <?php echo $block; ?>__slide">
						<?php if ( $client_logo || $client_name ) { ?>
							<div class="<?php echo $block; ?>__client">
								<?php if ( $client_logo ) { ?>
									<img class="<?php echo $block; ?>__logo" src="<?php echo $client_logo['url']; ?>" alt="<?php echo $client_logo['alt'] ?? ''; ?>" loading="lazy">
								<?php } ?>
							</div>
						<?php } ?>
						<?php if ( $testimonial_text ) { ?>
							<p class="<?php echo $block; ?>__quote"><?php echo esc_html( $testimonial_text ); ?></p>
						<?php } ?>
						<?php if ( $client_role || $client_name ) { ?>
							<div class="<?php echo $block; ?>__details">
								<?php if ( $client_role ) { ?>
									<p class="<?php echo $block; ?>__role"><?php echo esc_html( $client_role ); ?></p>
								<?php } ?>
								<?php if ( $client_name ) { ?>
									<p class="<?php echo $block; ?>__name"><?php echo esc_html( $client_name ); ?></p>
								<?php } ?>
							</div>
						<?php } ?>
					</div>
					<?php
				}
				?>
			</div>
			<div class="swiper-pagination"></div>
		</div>
	<?php } ?>
</section>
