<?php
/**
 * Content block: wysiwyg (plain, full width rich text).
 *
 * @package Boilerplate
 */

	defined( 'ABSPATH' ) || exit;

	// Content block.
	$content = get_row( true );
	$block   = 'b-' . str_replace( '_', '-', $content['acf_fc_layout'] ?? '' );

	// Singles.
	$text_area = $content['text_area'] ?? null;
?>
<section class="<?php echo esc_attr( $block . ' white' ); ?>">
	<div class="u-wysiwyg">
		<?php echo $text_area; ?>
	</div>
</section>
