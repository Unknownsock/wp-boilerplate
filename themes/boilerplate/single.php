<?php
/**
 * The template for displaying single posts.
 *
 * @package Boilerplate
 */

	defined( 'ABSPATH' ) || exit;

	get_header();

	$enable_footer = get_field( 'enable_footer' );

	get_template_part(
		'template-parts/header/site',
		'header'
	);

	?>

<div class="site-content">

	<main id="main" class="site-main" tabindex="-1">

		<?php
		if ( is_front_page() ) {
			get_template_part(
				'template-parts/header/hero',
				'home'
			);
		} else {
			get_template_part(
				'template-parts/header/hero',
				'standard'
			);
		}
			// Main content block.
			$id = get_the_ID();
			get_template_part(
				'template-parts/content/content',
				'blocks',
				array(
					'id' => $id,
				)
			);
			?>

		<?php
			$posts        = get_field( 'related_articles' );
			$custom_title = get_field( 'custom_title' );
		?>
		<?php if ( $posts ) { ?>
			<?php
				// Reuses the latest-blogs block's own markup/grid rather
				// than the old bespoke swiper-related layout - passing
				// $posts/$custom_title through $args instead of the
				// block's usual ACF row makes it render this field's
				// posts instead of auto-querying the 3 latest.
				get_template_part(
					'template-parts/content/blocks/latest-blogs/block-latest-blogs',
					null,
					array(
						'posts' => $posts,
						'title' => $custom_title ? $custom_title : 'You may also be <strong>interested in&hellip;</strong>',
					)
				);
			?>
		<?php } ?>

		<?php if ( 'yes' === $enable_footer ) { ?>
			<?php
				// Default Footer.
				$footer_type = get_field( 'footer_type' );
				$footer_id   = $footer_type->ID;
			if ( $footer_type ) {
				get_template_part(
					'template-parts/content/content',
					'blocks',
					array(
						'id' => $footer_id,
					)
				);
			}
			?>
		<?php } ?>

	</main>

</div>

<?php
	get_footer();
?>
