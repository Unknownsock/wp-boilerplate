<?php
/**
 * Content block: separator - a plain horizontal line between sections,
 * no fields at all.
 *
 * @package Boilerplate
 */

	defined( 'ABSPATH' ) || exit;

	// Content block.
	$content = get_row( true );
	$block   = 'b-' . str_replace( '_', '-', $content['acf_fc_layout'] ?? '' );

	// Singles.
	$background_colour = $content['background_colour'] ?? 'white';
?>
<section class="<?php echo esc_attr( $block . ' ' . $background_colour ); ?>">
	<span class="<?php echo esc_attr( $block ); ?>__line"></span>
</section>
