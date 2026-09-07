<?php
/**
 * Content block: content grid (rows of flexible-width wysiwyg columns).
 *
 * @package Boilerplate
 */

	defined( 'ABSPATH' ) || exit;

	// Content block.
	$content = get_row( true );
	$block   = 'b-' . str_replace( '_', '-', $content['acf_fc_layout'] ?? '' );

	// Singles.
	$eyebrow            = $content['eyebrow'] ?? null;
	$rows               = $content['rows'] ?? null;
	$background_colour  = $content['background_colour'] ?? 'white';
?>

<section class="<?php echo esc_attr( $block . ' ' . $background_colour ); ?>">
	<?php if ( $eyebrow ) : ?>
		<p class="c-eyebrow c-eyebrow--default"><?php echo esc_html( $eyebrow ); ?></p>
	<?php endif; ?>
	<?php if ( $rows ) : ?>
		<?php foreach ( $rows as $row ) : ?>
			<?php $columns = $row['columns'] ?? null; ?>
			<?php if ( $columns ) : ?>
				<div class="<?php echo $block; ?>__row">
					<?php
					foreach ( $columns as $column ) {
						$span                   = (int) ( $column['width'] ?? 5 );
						$content_html           = $column['content'] ?? '';
						$column_button_group    = $column['button_group'] ?? null;
						$column_force_stack     = ! empty( $column['force_stack'] );
						$column_background      = $column['background_colour'] ?? 'none';
						$column_border          = ! empty( $column['border'] );
						$column_classes         = $block . '__column ' . $block . '__column--' . $span;
						if ( $column_border ) {
							$column_classes .= ' ' . $block . '__column--border';
						}
						$column_inner_classes    = $block . '__column-inner u-wysiwyg';
						if ( 'none' !== $column_background ) {
							$column_inner_classes .= ' ' . $block . '__column-inner--panel ' . esc_attr( $column_background );
						}
						?>
						<div class="<?php echo esc_attr( $column_classes ); ?>">
							<?php // Background colour lives on this inner wrapper, not the outer column - keeps it from touching the --border outline above when both are set. ?>
							<div class="<?php echo esc_attr( $column_inner_classes ); ?>">
								<?php echo $content_html; ?>
								<?php if ( $column_button_group ) { ?>
									<div class="<?php echo $block; ?>__column-actions">
										<?php
										get_template_part(
											'template-parts/components/component',
											'button-group',
											array(
												'button_group' => $column_button_group,
												'force_stack'  => $column_force_stack,
											)
										);
										?>
									</div>
								<?php } ?>
							</div>
						</div>
						<?php
					}
					?>
				</div>
			<?php endif; ?>
		<?php endforeach; ?>
	<?php endif; ?>
</section>
