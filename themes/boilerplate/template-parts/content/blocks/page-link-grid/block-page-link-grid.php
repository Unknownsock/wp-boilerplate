<?php
/**
 * Content block: page link grid.
 *
 * @package Boilerplate
 */

	defined( 'ABSPATH' ) || exit;

	// Content block.
	$content = get_row( true );
	$block   = 'b-' . str_replace( '_', '-', $content['acf_fc_layout'] ?? '' );

	// Singles.
	$rows = $content['pages'] ?? null;
?>
<section class="<?php echo esc_attr( $block ); ?>">
	<div class="<?php echo esc_attr( $block ); ?>__grid">
		<?php if ( $rows ) : ?>
			<?php foreach ( $rows as $index => $row ) : ?>
				<?php
				$link  = get_permalink( $row );
				$title = get_the_title( $row );
				$image = null;
				if ( has_post_thumbnail( $row ) ) {
					$thumbnail_id = get_post_thumbnail_id( $row );
					$image_data   = wp_get_attachment_image_src( $thumbnail_id, 'gallery' );
					$alt          = get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true );

					$image = array(
						'url'    => $image_data[0],
						'width'  => $image_data[1],
						'height' => $image_data[2],
						'alt'    => $alt,
					);
				}
				$hero_options = get_field( 'hero_options', $row );
				$text         = $hero_options['text'] ?? null;


				// Alternate item colour variants: a / b / c, repeating every 3.
				$variant_index = $index % 3;
				$variant       = array( 'a', 'b', 'c' )[ $variant_index ];
				?>
				<article class="<?php echo esc_attr( $block ); ?>__item <?php echo esc_attr( $block ); ?>__item--<?php echo esc_attr( $variant ); ?>">
					<div class="<?php echo esc_attr( $block ); ?>__media">
						<a href="<?php echo esc_url( $link ); ?>" aria-label="<?php echo esc_attr( $title . ' - Find out more' ); ?>">
							<?php if ( $image ) : ?>
								<img src="<?php echo esc_url( $image['url'] ); ?>"
									width="<?php echo esc_attr( (string) $image['width'] ); ?>"
									height="<?php echo esc_attr( (string) $image['height'] ); ?>"
									alt="<?php echo esc_attr( $image['alt'] ); ?>"
									loading="lazy">
							<?php endif; ?>
						</a>
					</div>
					<div class="<?php echo esc_attr( $block ); ?>__body">
						<?php if ( $title ) : ?>
							<h3 class="<?php echo esc_attr( $block ); ?>__title"><?php echo esc_html( $title ); ?></h3>
						<?php endif; ?>

						<?php if ( $text ) : ?>
							<?php echo $text; ?>
						<?php endif; ?>

						<?php if ( $link ) : ?>
							<?php
							// Button colour follows the same a/b/c rotation as
							// the item background (0-indexed: a => white,
							// b => blue, c => navy - was grey-dark,
							// removed from the palette).
							$button_colour = array(
								'a' => 'white',
								'b' => 'blue',
								'c' => 'navy',
							)[ $variant ];
							echo add_button( $link, 'Find Out More', 'solid ' . $button_colour, '', '', '', $title . ' - Find out more' );
							?>
						<?php endif; ?>
					</div>
				</article>
			<?php endforeach; ?>
			<?php wp_reset_postdata(); ?>
		<?php endif; ?>
	</div>
</section>
