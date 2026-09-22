<?php
/**
 * Content block: feature grid (icon, wysiwyg text and buttons per item).
 *
 * @package Boilerplate
 */

	defined( 'ABSPATH' ) || exit;

	// Content block.
	$content = get_row( true );
	$block   = 'b-' . str_replace( '_', '-', $content['acf_fc_layout'] ?? '' );

	// Singles.
	$items              = $content['items'] ?? null;
	$text               = $content['text'] ?? null;
	$background_colour  = $content['background_colour'] ?? 'white';
	$layout             = $content['layout'] ?? 'layout-1';
?>
<section class="<?php echo esc_attr( $block . ' ' . $background_colour ); ?>">
	<?php // Layout 1 ("Our Advantages") puts its title inside the panel, with a full-width rule under it - see #{$block}__panel below. Every other layout keeps the intro above the grid. ?>
	<?php if ( $text && 'layout-1' !== $layout ) : ?>
		<div class="<?php echo $block; ?>__intro u-wysiwyg"><?php echo $text; ?></div>
	<?php endif; ?>
	<?php if ( $items ) { ?>
		<div class="<?php echo $block; ?>__grid <?php echo $block; ?>__grid--<?php echo esc_attr( $layout ); ?>">
			<?php // Layout 1's card is two layers - a light grey frame (this outer __grid) with a white inner panel the items actually sit on, not flush against the outer border. ?>
			<?php if ( 'layout-1' === $layout ) : ?>
				<div class="<?php echo $block; ?>__panel">
					<?php if ( $text ) : ?>
						<div class="<?php echo $block; ?>__title u-wysiwyg"><?php echo $text; ?></div>
						<div class="<?php echo $block; ?>__divider"></div>
					<?php endif; ?>
					<div class="<?php echo $block; ?>__items">
			<?php endif; ?>
			<?php
			$total = count( $items );
			foreach ( $items as $i => $item ) {
				$item_text_top     = $item['text_top'] ?? null;
				$item_text         = $item['text'] ?? null;
				$item_button_group = $item['button_group'] ?? null;
				$icon              = boilerplate_image( $item['icon'] ?? null, 88, 84, wp_strip_all_tags( $item_text ?: 'Icon' ) );
				?>
				<div class="<?php echo $block; ?>__item">
					<?php if ( $item_text_top ) { ?>
						<div class="<?php echo $block; ?>__item-text-top u-wysiwyg"><?php echo $item_text_top; ?></div>
					<?php } ?>
					<?php if ( 'layout-2' === $layout ) { ?>
						<div class="<?php echo $block; ?>__icon">
							<img class="<?php echo $block; ?>__icon-el" src="<?php echo esc_url( $icon['url'] ); ?>" alt="<?php echo esc_attr( $icon['alt'] ); ?>" loading="lazy">
						</div>
					<?php } else { ?>
						<img class="<?php echo $block; ?>__icon" src="<?php echo esc_url( $icon['url'] ); ?>" alt="<?php echo esc_attr( $icon['alt'] ); ?>" loading="lazy">
					<?php } ?>
					<?php if ( $item_text ) { ?>
						<div class="<?php echo $block; ?>__item-text u-wysiwyg"><?php echo $item_text; ?></div>
					<?php } ?>
					<?php if ( $item_button_group ) { ?>
						<div class="<?php echo $block; ?>__item-actions">
							<?php
							get_template_part(
								'template-parts/components/component',
								'button-group',
								array(
									'button_group' => $item_button_group,
								)
							);
							?>
						</div>
					<?php } ?>
				</div>
				<?php // One full-width rule after every row of 3, not per-item borders - skipped after the very last item. ?>
				<?php if ( 'layout-1' === $layout && 0 === ( $i + 1 ) % 3 && $i + 1 < $total ) : ?>
					<div class="<?php echo $block; ?>__divider"></div>
				<?php endif; ?>
				<?php
			}
			?>
			<?php if ( 'layout-1' === $layout ) : ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
	<?php } ?>
</section>
