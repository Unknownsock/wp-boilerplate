<?php
/**
 * Article partial: listing card layout.
 *
 * @package Boilerplate
 */

	defined( 'ABSPATH' ) || exit;

	// get_post_thumbnail_id() with no argument reads the main query's
	// queried object, not the current item of a secondary loop (e.g. a
	// related-articles list rendered on a single template) - pass the
	// loop's own $post->ID explicitly here instead.
if ( is_single() ) {
	global $post;
	$featured_image = get_post_thumbnail_id( $post->ID );

	if ( ! $featured_image ) {
		// Fallback to the first attached image if no featured image is set.
		$attachments = get_children(
			array(
				'post_parent'    => $post->ID,
				'post_type'      => 'attachment',
				'post_mime_type' => 'image',
				'numberposts'    => 1,
				'order'          => 'ASC',
			)
		);

		if ( $attachments ) {
			$attachment     = array_shift( $attachments );
			$featured_image = $attachment->ID;
		}
	}
} else {
	$featured_image = get_post_thumbnail_id();
}

	$featured_image_data = wp_get_attachment_image_src( $featured_image, 'listing' );
	$featured_image_alt  = get_post_meta( $featured_image, '_wp_attachment_image_alt', true );

	$image_url = $featured_image_data[0] ?? null;
	$image_alt = $featured_image_alt ?? null;

	$excerpt = get_the_excerpt();
	$excerpt = mb_strimwidth( $excerpt, 0, 150, '...' );

	$title = get_the_title();
	$link  = get_the_permalink();

	// "portfolio" posts are tagged via their own portfolio-cat taxonomy;
	// everything else shows its actual post_tag terms (not category) -
	// same eyebrow-above-title treatment as the large cards, just a
	// different taxonomy for the label itself.
	$eyebrow_taxonomy = 'portfolio' === get_post_type() ? 'portfolio-cat' : 'post_tag';
	$eyebrow_terms    = get_the_terms( get_the_ID(), $eyebrow_taxonomy );
	$eyebrow          = ( $eyebrow_terms && ! is_wp_error( $eyebrow_terms ) ) ? $eyebrow_terms[0]->name : '';
?>

<div class="c-card c-card--small swiper-slide">
	<a href="<?php echo esc_url( $link ); ?>" class="c-card__inner" aria-label="<?php echo esc_attr( $title ); ?> - Find out more">
		<div class="c-card__image">
			<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $image_alt ?? '' ); ?>" loading="lazy">
		</div>
		<div class="c-card__content">
			<?php if ( $image_url ) { ?>
				<svg class="c-card__image-shape" viewBox="0 0 388 335" aria-hidden="true" focusable="false">
					<path fill-rule="evenodd" clip-rule="evenodd" fill="currentColor" d="M0 0H388V335H0V0ZM0 0V50L74.7011 253.079C79.7324 266.691 91.7562 276.689 106.253 279.21L388 328.503V0H0Z" />
				</svg>
			<?php } ?>
			<?php if ( $eyebrow ) : ?>
				<p class="c-card__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
			<?php endif; ?>

			<?php if ( $title ) { ?>
				<h4 class="c-card__title"><?php echo esc_html( $title ); ?></h4>
			<?php } ?>

			<?php if ( $excerpt ) { ?>
				<p class="c-card__excerpt"><?php echo esc_html( $excerpt ); ?></p>
			<?php } ?>

			<span class="c-card__cta" aria-hidden="true">
				<svg class="c-card__arrow" width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
					<g clip-path="url(#clip0_34_119)">
						<path d="M0.581299 7.41943L4.86474 10.7697C5.43077 11.091 6.13448 11.091 6.7005 10.7697L10.9839 7.41943" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"></path>
						<path d="M5.7749 6.87988L5.7749 0.879883" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"></path>
					</g>
					<defs>
						<clipPath id="clip0_34_119">
							<rect width="11.55" height="11.5959" fill="white"></rect>
						</clipPath>
					</defs>
				</svg>
			</span>
		</div>
	</a>
</div>
