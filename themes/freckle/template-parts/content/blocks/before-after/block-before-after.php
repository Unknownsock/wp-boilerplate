<?php
/**
 * Content block: before and after image comparison.
 *
 * @package Boilerplate
 */

	defined( 'ABSPATH' ) || exit;

	// Content block.
	$content = get_row( true );
	$block   = 'b-' . str_replace( '_', '-', $content['acf_fc_layout'] ?? '' );

	// Singles.
	$before_image = $content['before_image'] ?? null;
	$after_image  = $content['after_image'] ?? null;
?>
<section class="<?php echo esc_attr( $block . ' white' ); ?>">
	<div class="<?php echo esc_attr( $block ); ?>__content">
		<div class="<?php echo esc_attr( $block ); ?>__before">
			<img src="<?php echo esc_url( $before_image['sizes']['gallery'] ); ?>" width="<?php echo esc_attr( $before_image['sizes']['gallery-width'] ); ?>" height="<?php echo esc_attr( $before_image['sizes']['gallery-height'] ); ?>" alt="<?php echo esc_attr( $before_image['alt'] ); ?>" loading="lazy">
		</div>
		<div class="<?php echo esc_attr( $block ); ?>__after">
			<img src="<?php echo esc_url( $after_image['sizes']['gallery'] ); ?>" width="<?php echo esc_attr( $after_image['sizes']['gallery-width'] ); ?>" height="<?php echo esc_attr( $after_image['sizes']['gallery-height'] ); ?>" alt="<?php echo esc_attr( $after_image['alt'] ); ?>" loading="lazy">
		</div>
		<div class="<?php echo esc_attr( $block ); ?>__resizer" tabindex="0" role="slider" aria-label="Drag to compare before and after" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100">
			<div class="<?php echo esc_attr( $block ); ?>__resizer-handle">
				<svg xmlns="http://www.w3.org/2000/svg" width="7.121" height="11.414" viewBox="0 0 7.121 11.414" aria-hidden="true"><path d="M20.5,9l-5,5,5,5" transform="translate(21.207 19.707) rotate(180)" fill="none" stroke="#2c363d" stroke-miterlimit="10" stroke-width="2"/></svg>
				<svg xmlns="http://www.w3.org/2000/svg" width="7.121" height="11.414" viewBox="0 0 7.121 11.414" aria-hidden="true"><path d="M20.5,9l-5,5,5,5" transform="translate(21.207 19.707) rotate(180)" fill="none" stroke="#2c363d" stroke-miterlimit="10" stroke-width="2"/></svg>
			</div>
		</div>
	</div>
</section>
