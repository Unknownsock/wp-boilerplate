<?php
/**
 * Content block: cards (hand-entered card grid, not tied to a CPT).
 *
 * Static/simple - no hover lift, no stretched link, no cropped image
 * shape. Everything (image, heading, text, buttons) is always visible;
 * a plain `<a>` wraps the whole card only when a link is set.
 *
 * @package Boilerplate
 */

	defined( 'ABSPATH' ) || exit;

	// Content block.
	$content = get_row( true );
	$block   = 'b-' . str_replace( '_', '-', $content['acf_fc_layout'] ?? '' );

	// Singles.
	$items              = $content['items'] ?? null;
	$background_colour  = $content['background_colour'] ?? 'grey-light';
?>
<section class="<?php echo esc_attr( $block . ' ' . $background_colour ); ?>">
	<?php if ( $items ) { ?>
		<div class="<?php echo $block; ?>__grid">
			<?php
			foreach ( $items as $item ) {
				$image        = boilerplate_image( $item['image'] ?? null, 600, 400, wp_strip_all_tags( $item['title'] ?? 'Card image' ) );
				$title        = $item['title'] ?? null;
				$eyebrow      = $item['eyebrow'] ?? null;
				$text         = $item['text'] ?? null;
				$button_group = $item['button_group'] ?? null;
				$link         = $item['link'] ?? null;
				$link_url     = $link['url'] ?? null;
				$link_target  = $link['target'] ?? '';
				$tag          = $link_url ? 'a' : 'div';
				$card_colour  = $item['background_colour'] ?? 'none';
				$card_classes = 'c-card c-card--large' . ( 'none' !== $card_colour ? ' ' . esc_attr( $card_colour ) : '' );
				?>
				<<?php echo tag_escape( $tag ); ?>
					class="<?php echo esc_attr( $card_classes ); ?>"
					<?php if ( $link_url ) : ?>
						href="<?php echo esc_url( $link_url ); ?>"
						<?php echo $link_target ? ' target="' . esc_attr( $link_target ) . '" rel="noopener"' : ''; ?>
					<?php endif; ?>
				>
					<div class="c-card__inner">
						<div class="c-card__image">
							<img src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>" loading="lazy">
						</div>
						<div class="c-card__content">
							<?php if ( $eyebrow || $title ) { ?>
								<div class="c-card__heading">
									<?php if ( $eyebrow ) { ?>
										<p class="c-card__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
									<?php } ?>
									<?php if ( $title ) { ?>
										<?php
											// wysiwyg wraps its output in <p> tags - strip them so
											// they don't nest invalidly inside the <h3> below.
											$title_html = str_replace( array( '<p>', '</p>' ), '', $title );
										?>
										<h3 class="c-card__title"><?php echo $title_html; ?></h3>
									<?php } ?>
								</div>
							<?php } ?>
							<?php if ( $text || $button_group ) { ?>
								<div class="c-card__body">
									<?php if ( $text ) { ?>
										<div class="c-card__text u-wysiwyg"><?php echo $text; ?></div>
									<?php } ?>
									<?php if ( $button_group ) { ?>
										<?php
										get_template_part(
											'template-parts/components/component',
											'button-group',
											array(
												'button_group' => $button_group,
											)
										);
										?>
									<?php } ?>
								</div>
							<?php } ?>
						</div>
					</div>
				</<?php echo tag_escape( $tag ); ?>>
				<?php
			}
			?>
		</div>
	<?php } ?>
</section>
