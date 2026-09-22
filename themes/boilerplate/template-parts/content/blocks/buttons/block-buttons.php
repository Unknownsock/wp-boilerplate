<?php
/**
 * Content block: buttons - a plain, manually-added row of buttons (no
 * layout/copy of its own). Pair with a text block (full_text_area /
 * content_grid) above it for something like a "Contact us" section -
 * e.g. see README "Contact/Careers panels".
 *
 * @package Boilerplate
 */

	defined( 'ABSPATH' ) || exit;

	// Content block.
	$content = get_row( true );
	$block   = 'b-' . str_replace( '_', '-', $content['acf_fc_layout'] ?? '' );

	// Singles.
	$background_colour = $content['background_colour'] ?? 'white';
	$force_stack        = ! empty( $content['force_stack'] );
	$button_group        = $content['button_group'] ?? null;
?>
<section class="<?php echo esc_attr( $block . ' ' . $background_colour ); ?>">
	<?php if ( $button_group ) { ?>
		<div class="<?php echo $block; ?>__inner">
			<?php
			get_template_part(
				'template-parts/components/component',
				'button-group',
				array(
					'button_group' => $button_group,
					'force_stack'  => $force_stack,
				)
			);
			?>
		</div>
	<?php } ?>
</section>
