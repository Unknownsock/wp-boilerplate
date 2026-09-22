<?php
/**
 * Content block: columns.
 *
 * @package Boilerplate
 */

	defined( 'ABSPATH' ) || exit;

	// Content block.
	$content = get_row( true );
	$block   = 'b-' . str_replace( '_', '-', $content['acf_fc_layout'] ?? '' );

	// Singles.
	$columns = $content['column_content'] ?? null;
	$count   = $columns ? count( $columns ) : 0;
?>
<section class="<?php echo esc_attr( $block . ' white' ); ?>">
	<?php if ( $columns ) : ?>
		<div class="<?php echo esc_attr( $block ); ?>__grid <?php echo esc_attr( $block ); ?>__grid--<?php echo esc_attr( (string) $count ); ?>">
			<?php foreach ( $columns as $column ) : ?>
				<?php
				$image_id  = $column['image'] ?? null;
				$title     = $column['title'] ?? null;
				$body_text = $column['body_text'] ?? null;
				$link_type = $column['link_type'] ?? 'no';
				$href      = 'internal' === $link_type ? ( $column['internal_link'] ?? '' ) : ( $column['external_link'] ?? '' );
				?>
				<div class="<?php echo esc_attr( $block ); ?>__column">
					<?php if ( $image_id ) : ?>
						<div class="<?php echo esc_attr( $block ); ?>__image">
							<?php echo wp_get_attachment_image( $image_id, 'medium' ); ?>
						</div>
					<?php endif; ?>

					<?php if ( $title ) : ?>
						<h2 class="<?php echo esc_attr( $block ); ?>__title <?php echo 'no' === $link_type ? esc_attr( $block ) . '__title--plain' : ''; ?>">
							<?php if ( 'no' !== $link_type && $href ) : ?>
								<a class="<?php echo esc_attr( $block ); ?>__link" href="<?php echo esc_url( $href ); ?>" <?php echo 'external' === $link_type ? 'target="_blank" rel="noopener"' : ''; ?>>
									<?php echo esc_html( $title ); ?>
								</a>
							<?php else : ?>
								<?php echo esc_html( $title ); ?>
							<?php endif; ?>
						</h2>
					<?php endif; ?>

					<?php if ( $body_text ) : ?>
						<div class="<?php echo esc_attr( $block ); ?>__text"><?php echo wp_kses_post( $body_text ); ?></div>
					<?php endif; ?>

					<?php if ( 'no' !== $link_type && $href ) : ?>
						<div class="<?php echo esc_attr( $block ); ?>__cta">
							<a class="<?php echo esc_attr( $block ); ?>__more" href="<?php echo esc_url( $href ); ?>" <?php echo 'external' === $link_type ? 'target="_blank" rel="noopener"' : ''; ?>>
								<span>Find out more</span>
							</a>
						</div>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</section>
