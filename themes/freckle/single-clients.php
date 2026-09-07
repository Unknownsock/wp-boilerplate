<?php
/**
 * The template for displaying single Clients posts.
 *
 * @package Boilerplate
 */

	defined( 'ABSPATH' ) || exit;

	get_header();

	global $post;

	$enable_footer = get_field( 'enable_footer' );
	$toggle        = get_field( 'content_mode_toggle', $post->ID );

	$above_gallery_text      = get_field( 'above_gallery_text' );
	$gallery_layout          = get_field( 'gallery_layout' );
	$gallery_images          = get_field( 'gallery' );
	$before_image            = get_field( 'before_image' );
	$after_image             = get_field( 'after_image' );
	$below_before_after_text = get_field( 'below_before_after_text' );

	get_template_part(
		'template-parts/header/site',
		'header'
	);

	?>

<div class="site-content">

	<main id="main" class="site-main" tabindex="-1">
			<?php
				get_template_part(
					'template-parts/header/hero',
					'standard'
				);
				?>

		<?php if ( 'legacy' === $toggle || empty( $toggle ) ) { ?>

			<section class="full_text_area white">
				<div class="block-content">
					<div class="container">
						<div class="row">
							<div class="col-10">
								<div class="content">
									<?php echo $above_gallery_text; ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
			<section class="full_image_gallery">
				<div class="block-content">
					<div class="container">
						<div class="row">
							<div class="col">
								<div class="content">

									<?php
										$rows  = $gallery_images;
										$count = count( $rows );
									?>

									<?php if ( 'layout-1' === $gallery_layout ) { ?>

										<?php if ( $rows ) { ?>
											<?php if ( $count > 1 ) { ?>
												<div class="swiper swiper-slider swiper-gallery-legacy">
													<div class="swiper-wrapper">
											<?php } ?>

												<?php foreach ( $rows as $index => $row ) { ?>
													<div class="swiper-slide">
														<?php $image = $row['image'] ?? null; ?>
														<img src="<?php echo esc_url( $image['sizes']['gallery'] ); ?>" width="<?php echo esc_attr( $image['sizes']['gallery-width'] ); ?>" height="<?php echo esc_attr( $image['sizes']['gallery-height'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ?? '' ); ?>" <?php echo 0 === $index ? 'fetchpriority="high"' : 'loading="lazy"'; ?>>
													</div>
												<?php } ?>

											<?php if ( $count > 1 ) { ?>
													</div>
													<div class="swiper-pagination"></div>
												</div>
											<?php } ?>

										<?php } ?>

									<?php } elseif ( 'layout-2' === $gallery_layout ) { ?>

											<?php if ( $rows ) { ?>
												<?php if ( $count > 1 ) { ?>
												<div class="swiper swiper-slider swiper-gallery">
													<div class="swiper-wrapper">
											<?php } ?>

												<?php foreach ( $rows as $index => $row ) { ?>
												<div class="swiper-slide">
													<?php $image = $row['image'] ?? null; ?>
													<img src="<?php echo esc_url( $image['sizes']['gallery'] ); ?>" width="<?php echo esc_attr( $image['sizes']['gallery-width'] ); ?>" height="<?php echo esc_attr( $image['sizes']['gallery-height'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ?? '' ); ?>" <?php echo 0 === $index ? 'fetchpriority="high"' : 'loading="lazy"'; ?>>
												</div>
											<?php } ?>

												<?php if ( $count > 1 ) { ?>
													</div>
												</div>
											<?php } ?>


												<?php if ( $count > 1 ) { ?>
												<div thumbsSlider="" class="swiper swiper-slider swiper-gallery-thumbs">
													<div class="swiper-wrapper">

														<?php foreach ( $rows as $index => $row ) { ?>
															<div class="swiper-slide">
															<?php $image = $row['image'] ?? null; ?>
																<img src="<?php echo esc_url( $image['sizes']['gallery-thumb'] ); ?>" width="<?php echo esc_attr( $image['sizes']['gallery-thumb-width'] ); ?>" height="<?php echo esc_attr( $image['sizes']['gallery-thumb-height'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ?? '' ); ?>" <?php echo 0 === $index ? 'fetchpriority="high"' : 'loading="lazy"'; ?>>
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
			<section class="full_text_area white">
				<div class="block-content">
					<div class="container">
						<div class="row">
							<div class="col-10">
								<div class="content">
									<?php echo the_content(); ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>

			<?php if ( $before_image && $after_image ) { ?>
			<section class="before_after">
				<div class="block-content">
					<div class="container">
						<div class="row">
							<div class="content">
								<div class="before">
									<img src="<?php echo esc_url( $before_image['sizes']['gallery'] ); ?>" width="<?php echo esc_attr( $before_image['sizes']['gallery-width'] ); ?>" height="<?php echo esc_attr( $before_image['sizes']['gallery-height'] ); ?>" alt="<?php echo esc_attr( $before_image['alt'] ?? '' ); ?>" loading="lazy">
								</div>
								<div class="after">
									<img src="<?php echo esc_url( $after_image['sizes']['gallery'] ); ?>" width="<?php echo esc_attr( $after_image['sizes']['gallery-width'] ); ?>" height="<?php echo esc_attr( $after_image['sizes']['gallery-height'] ); ?>" alt="<?php echo esc_attr( $after_image['alt'] ?? '' ); ?>" loading="lazy">
								</div>
								<div class="resizer">
									<div>
										<svg xmlns="http://www.w3.org/2000/svg" width="7.121" height="11.414" viewBox="0 0 7.121 11.414"><path id="Path_32" data-name="Path 32" d="M20.5,9l-5,5,5,5" transform="translate(21.207 19.707) rotate(180)" fill="none" stroke="#2c363d" stroke-miterlimit="10" stroke-width="2"/></svg>
										<svg xmlns="http://www.w3.org/2000/svg" width="7.121" height="11.414" viewBox="0 0 7.121 11.414"><path id="Path_32" data-name="Path 32" d="M20.5,9l-5,5,5,5" transform="translate(21.207 19.707) rotate(180)" fill="none" stroke="#2c363d" stroke-miterlimit="10" stroke-width="2"/></svg>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
			<?php } ?>

			<?php if ( $below_before_after_text ) { ?>
			<section class="full_text_area white">
				<div class="block-content">
					<div class="container">
						<div class="row">
							<div class="col-10">
								<div class="content">
									<?php echo $below_before_after_text; ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
			<?php } ?>

			<?php
				$posts        = get_field( 'related_articles' );
				$custom_title = get_field( 'custom_title' );
			?>
			<?php if ( $posts ) { ?>
				<?php
					// Reuses the latest-blogs block's own markup/grid rather
					// than the old bespoke swiper-related layout - passing
					// $posts/$custom_title through $args instead of the
					// block's usual ACF row makes it render this field's
					// posts instead of auto-querying the 3 latest.
					get_template_part(
						'template-parts/content/blocks/latest-blogs/block-latest-blogs',
						null,
						array(
							'posts' => $posts,
							'title' => $custom_title ? $custom_title : 'You may also be <strong>interested in&hellip;</strong>',
						)
					);
				?>
			<?php } ?>
		<?php } else { ?>
			<?php
				// Main content block.
				$id = get_the_ID();
				get_template_part(
					'template-parts/content/content',
					'blocks',
					array(
						'id' => $id,
					)
				);
			?>
			<?php
				$posts        = get_field( 'related_articles' );
				$custom_title = get_field( 'custom_title' );
			?>
			<?php if ( $posts ) { ?>
				<?php
					// Reuses the latest-blogs block's own markup/grid rather
					// than the old bespoke swiper-related layout - passing
					// $posts/$custom_title through $args instead of the
					// block's usual ACF row makes it render this field's
					// posts instead of auto-querying the 3 latest.
					get_template_part(
						'template-parts/content/blocks/latest-blogs/block-latest-blogs',
						null,
						array(
							'posts' => $posts,
							'title' => $custom_title ? $custom_title : 'You may also be <strong>interested in&hellip;</strong>',
						)
					);
				?>
			<?php } ?>

		<?php } ?>

		<?php if ( 'yes' === $enable_footer ) { ?>
			<?php
				// Default Footer.
				$footer_type = get_field( 'footer_type' );
				$footer_id   = $footer_type->ID;
			if ( $footer_type ) {
				get_template_part(
					'template-parts/content/content',
					'blocks',
					array(
						'id' => $footer_id,
					)
				);
			}
			?>
		<?php } ?>
	</main>

</div>

<?php
	get_footer();
?>
