<?php
/**
 * Content block: full width image gallery.
 *
 * @package Boilerplate
 */

	defined( 'ABSPATH' ) || exit;

	// Content block.
	$content = get_row( true );
	$block   = $content['acf_fc_layout'] ?? null;

	// Singles.
	$gallery_images = $content['gallery_images'] ?? null;
	$layout_type    = $content['layout_type'] ?? null;
?>
<section class="<?php echo esc_attr( $block . ' white' ); ?>">
	<div class="block-content">
		<div class="container">
			<div class="row">
				<div class="col">
					<div class="content slider <?php echo $layout_type; ?>">
						<?php
							$rows  = $gallery_images;
							$count = count( $rows );
						?>

						<?php if ( 'layout-1' === $layout_type ) { ?>

							<?php if ( $rows ) { ?>
								<?php if ( $count > 1 ) { ?>
									<div class="swiper swiper-slider swiper-gallery-legacy">
										<div class="swiper-wrapper">
								<?php } ?>

								<?php foreach ( $rows as $row ) { ?>
									<div class="swiper-slide">
										<?php $image = $row['image'] ?? null; ?>
											<img src="<?php echo $image['sizes']['gallery']; ?>" width="<?php echo $image['sizes']['gallery-width']; ?>" height="<?php echo $image['sizes']['gallery-height']; ?>" alt="<?php echo $image['alt']; ?>" loading="lazy">
									</div>
								<?php } ?>

								<?php if ( $count > 1 ) { ?>
										</div>
										<div class="swiper-pagination"></div>
									</div>
								<?php } ?>

							<?php } ?>

						<?php } elseif ( 'layout-2' === $layout_type ) { ?>

								<?php if ( $rows ) { ?>
									<?php if ( $count > 1 ) { ?>
									<div class="swiper swiper-slider swiper-gallery">
										<div class="swiper-wrapper">
								<?php } ?>

									<?php foreach ( $rows as $row ) { ?>
									<div class="swiper-slide">
										<?php $image = $row['image'] ?? null; ?>
										<img src="<?php echo $image['sizes']['gallery']; ?>" width="<?php echo $image['sizes']['gallery-width']; ?>" height="<?php echo $image['sizes']['gallery-height']; ?>" alt="<?php echo $image['alt']; ?>" loading="lazy">
									</div>
								<?php } ?>

									<?php if ( $count > 1 ) { ?>
										</div>
									</div>
								<?php } ?>


									<?php if ( $count > 1 ) { ?>
									<div thumbsSlider="" class="swiper swiper-slider swiper-gallery-thumbs">
										<div class="swiper-wrapper">

											<?php foreach ( $rows as $row ) { ?>
												<div class="swiper-slide">
													<?php $image = $row['image'] ?? null; ?>
													<img src="<?php echo $image['sizes']['gallery-thumb']; ?>" width="<?php echo $image['sizes']['gallery-thumb-width']; ?>" height="<?php echo $image['sizes']['gallery-thumb-height']; ?>" alt="<?php echo $image['alt']; ?>" loading="lazy">
												</div>
											<?php } ?>

										</div>
									</div>
								<?php } ?>

							<?php } ?>

							<?php wp_reset_postdata(); ?>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
