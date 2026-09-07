<?php
/**
 * Content block: stats / counters.
 *
 * @package Boilerplate
 */

	defined( 'ABSPATH' ) || exit;

	// Content block.
	$content = get_row( true );
	$block   = 'b-' . str_replace( '_', '-', $content['acf_fc_layout'] ?? '' );

	// Singles.
	$stats             = $content['stats'] ?? null;
	$background_colour = $content['background_colour'] ?? 'gradient';
?>

<section class="<?php echo esc_attr( $block . ' ' . $background_colour ); ?>">
	<div class="<?php echo $block; ?>__grid">
		<?php
		if ( $stats ) {
			$total = count( $stats );
			foreach ( $stats as $i => $stat ) {
				$value      = $stat['value'] ?? 0;
				$prefix     = $stat['prefix'] ?? '';
				$suffix     = $stat['suffix'] ?? '';
				$use_commas = ! empty( $stat['use_commas'] );
				$label      = $stat['label'] ?? '';
				$image      = portal_image( $stat['icon'] ?? null, 120, 120, $label ?: 'Stat' );

				// Either/or - one position, not both (was two independent
				// checkboxes, which allowed duplicating or hiding the text
				// entirely; a single field can't be in two states at once).
				$text_position = $stat['text_position'] ?? 'below';

				$render_text = function () use ( $block, $value, $prefix, $suffix, $use_commas, $label ) {
					?>
					<div class="<?php echo $block; ?>__text">
						<p class="<?php echo $block; ?>__number js-stat-count" data-value="<?php echo esc_attr( $value ); ?>" data-commas="<?php echo $use_commas ? '1' : '0'; ?>">
							<?php if ( $prefix ) : ?>
								<span class="<?php echo $block; ?>__affix"><?php echo esc_html( $prefix ); ?></span>
							<?php endif; ?>
							<span class="<?php echo $block; ?>__value js-stat-value">0</span>
							<?php if ( $suffix ) : ?>
								<span class="<?php echo $block; ?>__affix"><?php echo esc_html( $suffix ); ?></span>
							<?php endif; ?>
						</p>
						<?php if ( $label ) : ?>
							<p class="<?php echo $block; ?>__label"><?php echo esc_html( $label ); ?></p>
						<?php endif; ?>
					</div>
					<?php
				};
				?>
				<div class="<?php echo $block; ?>__item">
					<?php if ( 'above' === $text_position ) { $render_text(); } ?>
					<div class="<?php echo $block; ?>__image">
						<img class="<?php echo $block; ?>__image-el" src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>" loading="lazy" width="120" height="120" />
					</div>
					<?php if ( 'below' === $text_position ) { $render_text(); } ?>
				</div>
				<?php if ( $i < $total - 1 ) : ?>
					<svg class="<?php echo $block; ?>__arrow" aria-hidden="true" width="67" height="32" viewBox="0 0 67 32" fill="none" xmlns="http://www.w3.org/2000/svg">
						<circle cx="2.5" cy="15.2678" r="2.5" fill="currentColor" />
						<circle cx="12.5" cy="15.2678" r="2.5" fill="currentColor" />
						<circle cx="21.5" cy="15.2678" r="2.5" fill="currentColor" />
						<circle cx="31.5" cy="15.2678" r="2.5" fill="currentColor" />
						<circle cx="41.5" cy="15.2678" r="2.5" fill="currentColor" />
						<circle cx="51.5" cy="15.2678" r="2.5" fill="currentColor" />
						<path d="M48.5 1.76782L62.5 15.7678L48.5 29.7678" stroke="currentColor" stroke-width="5" />
					</svg>
				<?php endif; ?>
				<?php
			}
		}
		?>
	</div>
</section>
