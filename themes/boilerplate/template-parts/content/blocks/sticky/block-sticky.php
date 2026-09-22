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
	// always light grey now, hardcoded in _sticky.scss.
?>
<section class="<?php echo esc_attr( $block . ' grey-light' ); ?>">
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
		<div class="<?php echo $block; ?>__shapes">
			<svg class="<?php echo $block; ?>__shape_1" width="877" height="885" viewBox="0 0 877 885" fill="none" xmlns="http://www.w3.org/2000/svg" data-rotate-scroll data-rotate-direction="clockwise" data-rotate-speed="40" data-rotate-opacity="1"><path d="M101.259 154.077L5.56206 539.447C1.00597 557.794 6.57777 577.183 20.1746 590.298L305.72 865.779C319.317 878.894 338.883 883.753 357.036 878.522L738.291 768.624C756.444 763.392 770.438 748.862 774.995 730.515L870.691 345.145C875.247 326.798 869.675 307.409 856.078 294.293L570.523 18.8104C556.926 5.69463 537.359 0.835858 519.206 6.06744L137.965 115.957C119.812 121.189 105.817 135.719 101.261 154.066L101.259 154.077Z" stroke="#FB62B2" stroke-width="8" stroke-miterlimit="10"/></svg>
			<svg class="<?php echo $block; ?>__shape_2" width="400" height="392" viewBox="0 0 400 392" fill="none" xmlns="http://www.w3.org/2000/svg" data-rotate-scroll data-rotate-direction="counterclockwise" data-rotate-speed="20" data-rotate-opacity="1"><path opacity="0.1" d="M5.0807 164.443L57.8985 332.831C60.4132 340.848 67.0123 346.918 75.2062 348.75L247.293 387.253C255.487 389.085 264.037 386.404 269.716 380.219L388.988 250.327C394.668 244.143 396.618 235.392 394.103 227.375L341.286 58.9867C338.771 50.9698 332.172 44.9003 323.978 43.0677L151.886 4.56695C143.692 2.73426 135.143 5.41597 129.463 11.6002L10.1942 141.486C4.51498 147.67 2.56459 156.422 5.07924 164.438L5.0807 164.443Z" stroke="#1C2728" stroke-width="8" stroke-miterlimit="10"/></svg>
		</div>

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