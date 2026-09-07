<?php
/**
 * Content block: contact call to action + form.
 *
 * @package Boilerplate
 */

	defined( 'ABSPATH' ) || exit;

	// Content block.
	$content = get_row( true );
	$block   = 'b-' . str_replace( '_', '-', $content['acf_fc_layout'] ?? '' );

	// Singles.
	$background_colour      = $content['background_colour'] ?? 'navy';
	$eyebrow                = $content['eyebrow'] ?? null;
	$text                   = $content['text'] ?? null;
	$button_group           = $content['button_group'] ?? null;
	$force_stack            = ! empty( $content['force_stack'] );
	$contact_form_shortcode = $content['contact_form_shortcode'] ?? null;
?>
<section class="<?php echo esc_attr( $block . ' ' . $background_colour ); ?>">
	<div class="<?php echo $block; ?>__intro">
		<?php if ( $eyebrow ) { ?>
			<p class="c-eyebrow c-eyebrow--default"><?php echo esc_html( $eyebrow ); ?></p>
		<?php } ?>
		<?php if ( $text ) { ?>
			<div class="<?php echo $block; ?>__text u-wysiwyg"><?php echo $text; ?></div>
		<?php } ?>
		<?php if ( $button_group ) { ?>
			<div class="<?php echo $block; ?>__actions">
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
	</div>
	<?php if ( $contact_form_shortcode ) { ?>
		<div class="<?php echo $block; ?>__form">
			<?php echo do_shortcode( $contact_form_shortcode ); ?>
		</div>
	<?php } ?>
</section>
