<?php
/**
 * Component: block title, eyebrow and intro text.
 *
 * @package Boilerplate
 */

	defined( 'ABSPATH' ) || exit;

	$title_options = $args['title_options'] ?? null;

	$text_alignment = $title_options['text_alignment'] ?? null;
	$eyebrow        = $title_options['eyebrow'] ?? null;
	$eyebrow_style  = $title_options['eyebrow_style'] ?? 'default';
	$title          = $title_options['title'] ?? null;
	$sub_title      = $title_options['sub_title'] ?? null;
	$text           = $title_options['text'] ?? null;
	$logo           = $title_options['logo'] ?? null;

	$classes = implode( ' ', array( 'c-block-title', $text_alignment ) );

if ( $eyebrow || $title || $sub_title || $text || $logo ) { ?>
	<div class="<?php echo $classes; ?>">
		<?php if ( $eyebrow ) { ?>
			<p class="c-eyebrow c-eyebrow--<?php echo $eyebrow_style; ?>"><?php echo $eyebrow; ?></p>
		<?php } ?>
		<?php if ( $title ) { ?>
			<p class="c-block-title__title"><?php echo $title; ?></p>
		<?php } ?>
		<?php if ( $text ) { ?>
			<div class="c-block-title__text"><?php echo $text; ?></div>
		<?php } ?>
	</div>
<?php } ?>
