<?php
/**
 * Content block: logo carousel.
 *
 * @package Boilerplate
 */

	defined( 'ABSPATH' ) || exit;

	// Content block.
	$content = get_row( true );
	$block   = 'b-' . str_replace( '_', '-', $content['acf_fc_layout'] ?? '' );

	// Singles.
	$background_colour = $content['background_colour'] ?? null;
	$eyebrow            = $content['eyebrow'] ?? null;
	$eyebrow_style      = $content['eyebrow_style'] ?? 'centre';
	$clients            = $content['clients'] ?? null;

	$classes = implode( ' ', array( $block, $background_colour ) );

	// Renders the <li> items once; called twice below to build a
	// duplicated, seamless marquee track (see __track in _logos.scss -
	// it scrolls from 0 to -50%, so two identical copies back to back is
	// what makes the loop invisible). Unlike the services block, there's
	// enough logos here that one copy is reliably wider than any
	// realistic viewport, so a third copy's buffer isn't needed. The
	// duplicate copy is purely visual, so it's hidden from assistive
	// tech and keyboard focus.
	$render_client_items = function ( $hidden = false ) use ( $block, $clients ) {
		if ( ! $clients ) {
			return;
		}
		foreach ( $clients as $client ) {
			$client_logo = get_field( 'client_logo', $client );
			$client_name = get_the_title( $client );
			$client_link = get_permalink( $client );
			if ( ! $client_logo ) {
				continue;
			}
			?>
				<li class="<?php echo $block; ?>__item">
					<?php if ( $client_link ) : ?>
						<a href="<?php echo esc_url( $client_link ); ?>" aria-label="<?php echo esc_attr( $client_name ); ?>"<?php echo $hidden ? ' tabindex="-1"' : ''; ?>>
					<?php endif; ?>
					<img src="<?php echo $client_logo['url']; ?>" width="<?php echo $client_logo['width'] ?? ''; ?>" height="<?php echo $client_logo['height'] ?? ''; ?>" alt="<?php echo $client_logo['alt'] ? $client_logo['alt'] : $client_name; ?>" loading="lazy">
					<?php if ( $client_link ) : ?>
						</a>
					<?php endif; ?>
				</li>
			<?php
		}
	};
?>

<section class="<?php echo esc_attr( $block . ' white' ); ?>">
	<?php if ( $eyebrow ) : ?>
		<p class="c-eyebrow c-eyebrow--<?php echo $eyebrow_style; ?>"><?php echo esc_html( $eyebrow ); ?></p>
	<?php endif; ?>
	<div class="<?php echo $block; ?>__viewport">
		<div class="<?php echo $block; ?>__track">
			<ul class="<?php echo $block; ?>__list">
				<?php $render_client_items(); ?>
			</ul>
			<ul class="<?php echo $block; ?>__list" aria-hidden="true">
				<?php $render_client_items( true ); ?>
			</ul>
		</div>
	</div>
</section>
