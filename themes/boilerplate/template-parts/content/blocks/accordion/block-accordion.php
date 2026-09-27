<?php
/**
 * Content block: accordion.
 *
 * @package Boilerplate
 */

	defined( 'ABSPATH' ) || exit;

	// Content block.
	$content = get_row( true );
	$block   = 'b-' . str_replace( '_', '-', $content['acf_fc_layout'] ?? '' );

	// Singles.
	$wysiwyg           = $content['wysiwyg'] ?? null;
	$accordions        = $content['accordion'] ?? null;
	$background_colour = $content['background_colour'] ?? 'white';
?>
<section class="<?php echo esc_attr( $block . ' ' . $background_colour ); ?>">
	<?php if ( $wysiwyg ) : ?>
		<div class="<?php echo esc_attr( $block ); ?>__intro"><?php echo wp_kses_post( $wysiwyg ); ?></div>
	<?php endif; ?>

	<?php if ( $accordions ) : ?>
		<ul class="<?php echo esc_attr( $block ); ?>__list">
			<?php foreach ( $accordions as $i => $accordion ) : ?>
				<?php
				$title       = $accordion['title'] ?? null;
				$text        = $accordion['text'] ?? null;
				$button_text = $accordion['button_text'] ?? null;
				$button_link = $accordion['button_link'] ?? null;
				$trigger_id  = $block . '__trigger-' . $i;
				$panel_id    = $block . '__panel-' . $i;
				?>
				<li class="<?php echo esc_attr( $block ); ?>__item">
					<h3 class="<?php echo esc_attr( $block ); ?>__heading">
						<button
							type="button"
							id="<?php echo esc_attr( $trigger_id ); ?>"
							class="<?php echo esc_attr( $block ); ?>__trigger"
							aria-expanded="<?php echo 0 === $i ? 'true' : 'false'; ?>"
							aria-controls="<?php echo esc_attr( $panel_id ); ?>"
						>
							<span class="<?php echo esc_attr( $block ); ?>__trigger-text"><?php echo esc_html( $title ); ?></span>
							<span class="<?php echo esc_attr( $block ); ?>__icon" aria-hidden="true"></span>
						</button>
					</h3>
					<div
						id="<?php echo esc_attr( $panel_id ); ?>"
						class="<?php echo esc_attr( $block ); ?>__panel"
						role="region"
						aria-labelledby="<?php echo esc_attr( $trigger_id ); ?>"
						<?php echo 0 === $i ? '' : 'hidden'; ?>
					>
						<div class="<?php echo esc_attr( $block ); ?>__panel-inner u-wysiwyg">
							<?php echo wp_kses_post( $text ); ?>
							<?php if ( $button_text && $button_link ) : ?>
								<?php echo add_button( $button_link['url'], $button_text, 'solid orange' ); ?>
							<?php endif; ?>
						</div>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</section>
