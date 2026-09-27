<?php
/**
 * Content block: post listing.
 *
 * @package Boilerplate
 */

	defined( 'ABSPATH' ) || exit;

	// Content block.
	$content = get_row( true );
	$block   = 'b-' . str_replace( '_', '-', $content['acf_fc_layout'] ?? '' );

	// Groups.
	$title_options = $content['title_options'] ?? null;

	// Title options (formerly the shared component-block-title.php/
	// .c-block-title - inlined and reworked to the block's own BEM markup,
	// matching the __header/__intro pattern other blocks use, e.g.
	// latest-blogs/block-latest-blogs.php).
	$title_eyebrow       = $title_options['eyebrow'] ?? null;
	$title_eyebrow_style = $title_options['eyebrow_style'] ?? 'default';
	$title_title         = $title_options['title'] ?? null;
	$title_text          = $title_options['text'] ?? null;
	// ACF's raw choice value is "text-alignment-<left|center|right>" -
	// reduce that to a proper BEM modifier instead of using it verbatim.
	$title_alignment_raw = $title_options['text_alignment'] ?? null;
	$title_header_class  = $block . '__header';
if ( $title_alignment_raw ) {
	$title_header_class .= ' ' . $block . '__header--' . str_replace( 'text-alignment-', '', $title_alignment_raw );
}

	// Singles.
	$query_type        = $content['query_type'] ?? null;
	$relationship      = $content['relationship'] ?? null;
	$number_of_queries = $content['number_of_queries'] ?? null;
	$view_all_link     = $content['view_all_link'] ?? null;
	$post_type         = $content['post_type'] ?? 'post';

	$enable_carousel = $content['enable_carousel'] ?? null;
?>

<section class="<?php echo esc_attr( $block . ' white' ); ?>">
	<?php if ( $title_eyebrow || $title_title || $title_text ) : ?>
		<div class="<?php echo esc_attr( $title_header_class ); ?>">
			<?php if ( $title_eyebrow ) : ?>
				<p class="c-eyebrow c-eyebrow--<?php echo esc_attr( $title_eyebrow_style ); ?>"><?php echo esc_html( $title_eyebrow ); ?></p>
			<?php endif; ?>
			<?php if ( $title_title ) : ?>
				<h2 class="<?php echo esc_attr( $block ); ?>__title"><?php echo esc_html( $title_title ); ?></h2>
			<?php endif; ?>
			<?php if ( $title_text ) : ?>
				<div class="<?php echo esc_attr( $block ); ?>__text"><?php echo $title_text; ?></div>
			<?php endif; ?>
		</div>
	<?php endif; ?>
	<div class="facetwp-template">

			<?php if ( 'enable' === $enable_carousel ) { ?>
				<div class="swiper swiper-slider swiper-related js-swiper-related">
					<div class="swiper-wrapper">
			<?php } elseif ( 'disable' === $enable_carousel ) { ?>
				<div class="<?php echo $block; ?>__grid">
			<?php } ?>
				<?php
				if ( 'relationship' === $query_type ) {
					$posts = $relationship;
				} else {
					$args  = array(
						'post_type'      => $post_type,
						'orderby'        => array(
							'date' => 'DESC', // Primary sort: by post date.
							'ID'   => 'DESC',  // Secondary, fallback sort: by post ID.
						),
						'order'          => 'DESC',
						'posts_per_page' => $number_of_queries,
						'post_status'    => 'publish',
						'facetwp'        => true,
					);
					$posts = new WP_Query( $args );
					$posts = $posts->get_posts();
				}
				if ( $posts ) {
					foreach ( $posts as $post ) {
						get_template_part(
							'template-parts/articles/article',
							'listing'
						);
					}
				} else {
					?>
						<div class="content no-results">
							<h3>No search results...</h3>
							<p>Please try another search term or category.</p>
						</div>
					<?php
				}
					wp_reset_postdata();
				?>
			</div>
			<div class="filters--bottom"></div>

		<?php if ( 'enable' === $enable_carousel ) { ?>
			</div>
		<?php } ?>

	</div>
	<?php if ( $view_all_link ) { ?>
		<div class="block-footer">
			<a href="<?php echo $view_all_link['url']; ?>">View all...</a>
		</div>
	<?php } ?>
</section>
