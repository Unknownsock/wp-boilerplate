<?php
/**
 * Content block: sticky intro with boxes.
 *
 * @package Boilerplate
 */

	defined( 'ABSPATH' ) || exit;

	// Content block.
	$content = get_row( true );
	$block   = 'b-' . str_replace( '_', '-', $content['acf_fc_layout'] ?? '' );

	// Singles.
	$eyebrow        = $content['eyebrow'] ?? null;
	$text           = $content['text'] ?? null;
	$button_group   = $content['button_group'] ?? null;
	$column_content = $content['column_content'] ?? null;

	// Background colour used to be editor-selectable (background_colour
	// field, removed from this layout in the ACF JSON) - this block is
	// always white now, hardcoded in _sticky.scss.
?>
<section class="<?php echo esc_attr( $block ); ?>">
	<div class="<?php echo $block; ?>__intro">
		<?php if ( $eyebrow ) { ?>
			<p class="c-eyebrow c-eyebrow--default"><?php echo esc_html( $eyebrow ); ?></p>
		<?php } ?>
		<?php if ( $text ) { ?>
			<div class="<?php echo $block; ?>__text"><?php echo $text; ?></div>
		<?php } ?>
		<?php if ( $button_group ) { ?>
			<div class="<?php echo $block; ?>__actions">
				<?php
					get_template_part(
						'template-parts/components/component',
						'button-group',
						array(
							'button_group' => $button_group,
						)
					);
				?>
			</div>
		<?php } ?>
	</div>
	<?php if ( $column_content ) { ?>
		<div class="<?php echo $block; ?>__boxes">
			<?php
			foreach ( $column_content as $row ) {
				$icon             = $row['icon'] ?? null;
				$title            = $row['title'] ?? null;
				$body_text        = $row['body_text'] ?? null;
				$box_button_group = $row['button_group'] ?? null;
				?>
				<div class="<?php echo $block; ?>__box">
					<?php if ( $icon ) { ?>
						<img class="<?php echo $block; ?>__icon" src="<?php echo esc_url( $icon['url'] ); ?>" width="<?php echo esc_attr( $icon['width'] ?? '' ); ?>" height="<?php echo esc_attr( $icon['height'] ?? '' ); ?>" alt="<?php echo esc_attr( $icon['alt'] ?? '' ); ?>" loading="lazy">
					<?php } ?>
					<?php if ( $title ) { ?>
						<h3 class="<?php echo $block; ?>__box-title"><?php echo esc_html( $title ); ?></h3>
					<?php } ?>
					<?php if ( $body_text ) { ?>
						<p class="<?php echo $block; ?>__box-text"><?php echo esc_html( $body_text ); ?></p>
					<?php } ?>
					<?php if ( $box_button_group ) { ?>
						<div class="<?php echo $block; ?>__box-actions">
							<?php
							get_template_part(
								'template-parts/components/component',
								'button-group',
								array(
									'button_group' => $box_button_group,
								)
							);
							?>
						</div>
					<?php } ?>
				</div>
				<?php
			}
			?>
		</div>
	<?php } ?>

</section>