<?php
/**
 * Content block: services.
 *
 * @package Boilerplate
 */

	defined( 'ABSPATH' ) || exit;

	// Content block.
	$content = get_row( true );
	$block   = 'b-' . str_replace( '_', '-', $content['acf_fc_layout'] ?? '' );

	// Singles.
	$service_list = $content['service_list'] ?? null;

	// Renders the <li> items once; called three times below to build a
	// duplicated, seamless marquee track (see __track in _services.scss -
	// it scrolls from 0 to -33.33%, so three identical copies back to
	// back is what makes the loop invisible). Two copies isn't enough
	// buffer on wide viewports: the visible window can be wider than a
	// single copy, so its trailing edge runs past the end of the track
	// before the loop resets, showing as a gap right after the last <li>.
	// The duplicate copies are purely visual, so they're hidden from
	// assistive tech and keyboard focus.
	$render_service_items = function ( $hidden = false ) use ( $block, $service_list ) {
		if ( ! $service_list ) {
			return;
		}
		foreach ( $service_list as $row ) {
			$link  = $row['link'] ?? null;
			$title = $row['title'] ?? null;
			?>
				<li class="<?php echo $block; ?>__item">
					<a href="<?php echo $link['url'] ?? '#'; ?>"<?php echo $hidden ? ' tabindex="-1"' : ''; ?>><?php echo $title; ?></a>
				</li>
			<?php
		}
	};
?>
<section class="<?php echo $block; ?>">
	<div class="<?php echo $block; ?>__viewport">
		<div class="<?php echo $block; ?>__track">
			<ul class="<?php echo $block; ?>__list">
				<?php $render_service_items(); ?>
			</ul>
			<ul class="<?php echo $block; ?>__list" aria-hidden="true">
				<?php $render_service_items( true ); ?>
			</ul>
			<ul class="<?php echo $block; ?>__list" aria-hidden="true">
				<?php $render_service_items( true ); ?>
			</ul>
		</div>
	</div>
</section>
