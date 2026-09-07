<?php
/**
 * Content block: accordion, with a right-hand image that swaps to match
 * whichever item is currently open (accordion.js) - defaults to the first
 * item's image on load.
 *
 * @package Boilerplate
 */

	defined( 'ABSPATH' ) || exit;

	// Content block.
	$content = get_row( true );
	$block   = 'b-' . str_replace( '_', '-', $content['acf_fc_layout'] ?? '' );

	// Singles.
	$wysiwyg            = $content['wysiwyg'] ?? null;
	$accordions         = $content['accordion'] ?? null;
	$background_colour  = $content['background_colour'] ?? 'white';
?>
<section class="<?php echo esc_attr( $block . ' ' . $background_colour ); ?>">
	<?php if ( $wysiwyg ) : ?>
		<div class="<?php echo esc_attr( $block ); ?>__intro"><?php echo wp_kses_post( $wysiwyg ); ?></div>
	<?php endif; ?>

	<?php if ( $accordions ) : ?>
		<div class="<?php echo esc_attr( $block ); ?>__layout">
			<ul class="<?php echo esc_attr( $block ); ?>__list">
				<?php foreach ( $accordions as $i => $accordion ) : ?>
					<?php
					$title       = $accordion['title'] ?? null;
					$text        = $accordion['text'] ?? null;
					$button_text = $accordion['button_text'] ?? null;
					$button_link = $accordion['button_link'] ?? null;
					$image       = portal_image( $accordion['image'] ?? null, 800, 800, $title ?: 'Accordion image' );
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
								data-image="<?php echo esc_url( $image['url'] ); ?>"
								data-image-alt="<?php echo esc_attr( $image['alt'] ); ?>"
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
			<div class="<?php echo esc_attr( $block ); ?>__media">
				<?php
				$first_image = portal_image( $accordions[0]['image'] ?? null, 800, 800, $accordions[0]['title'] ?? 'Accordion image' );
				?>
				<img class="<?php echo esc_attr( $block ); ?>__media-image js-accordion-image" src="<?php echo esc_url( $first_image['url'] ); ?>" alt="<?php echo esc_attr( $first_image['alt'] ); ?>" loading="lazy" />
			</div>
		</div>
	<?php endif; ?>
</section>
