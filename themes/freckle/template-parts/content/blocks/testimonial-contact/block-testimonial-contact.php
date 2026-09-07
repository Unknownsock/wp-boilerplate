<?php
/**
 * Content block: testimonial with contact call to action.
 *
 * @package Boilerplate
 */

	defined( 'ABSPATH' ) || exit;

	// Content block.
	$content = get_row( true );
	$block   = 'b-' . str_replace( '_', '-', $content['acf_fc_layout'] ?? '' );

	// Singles.
	$testimonials      = $content['testimonials'] ?? null;
	$testimonial_title = $content['testimonial_title'] ?? null;
	$contact_title     = $content['contact_title'] ?? null;
	$contact_text      = $content['contact_text'] ?? null;
?>
<section class="<?php echo $block; ?>">
	<div class="<?php echo $block; ?>__testimonials">
		<div class="swiper swiper-slider swiper-testimonial js-swiper-testimonial">
			<?php if ( $testimonial_title ) { ?>
				<h3><?php echo $testimonial_title; ?></h3>
			<?php } ?>
			<div class="swiper-wrapper">
				<?php
					$rows = $testimonials ? get_field( 'testimonial', $testimonials[0] ) : null;
				if ( $rows ) {
					shuffle( $rows );
					foreach ( $rows as $row ) {
						$testimonial_text = $row['testimonial_text'] ?? null;
						$client_details   = $row['client_details'] ?? null;
						?>
							<div class="swiper-slide">
								<p>
								<?php echo $testimonial_text; ?>
								</p>
								<p class="details">
								<?php echo $client_details; ?>
								</p>
							</div>
						<?php
					}
				}
				?>
			</div>
			<div class="swiper-pagination"></div>
		</div>
	</div>
	<div class="<?php echo $block; ?>__contact">
		<div class="<?php echo $block; ?>__contact-content">

			<?php if ( $contact_title ) { ?>
				<h3><?php echo $contact_title; ?></h3>
			<?php } ?>

			<?php if ( $contact_text ) { ?>
				<p><?php echo $contact_text; ?></p>
			<?php } ?>

			<?php echo add_button( '#contact-popup', 'Contact Us', 'solid blue js-modal-trigger', 'contact', '', '', 'Modal contact form' ); ?>

		</div>
	</div>
</section>
